<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\IdleItem;
use Illuminate\Http\Request;

class SearchController extends Controller {
    /**
     * 處理搜尋請求並顯示結果。
     */
    public function index(Request $request) {
        // 1. 驗證輸入資料
        $validated = $request->validate([
            'q' => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'min_price' => 'nullable|numeric|min:0',
            'max_price' => 'nullable|numeric|min:0',
            'sort' => 'nullable|string|in:latest,oldest,price_asc,name_asc,name_desc',
        ]);

        // 2. 取得所有篩選條件
        $filters =[
            'query' => $validated['q'] ?? null,
            'category_id' => $validated['category_id'] ?? null,
            'min_price' => $validated['min_price'] ?? null,
            'max_price' => $validated['max_price'] ?? null,  
        ];
        $sortOrder = $validateed['sort'] ?? 'latest';

        // 3. 處理價格邏輯錯誤
        if ($filters['min_price'] && $filters['max_price'] && $filters['min_price'] > $filters['max_price']) {
            [$filters['min_price'], $filters['max_price']] = [$filters['max_price'], $filters['min_price']];
        }
        

        // 4. 開始建立查詢，並動態加入條件
        $itemsQuery = IdleItem::with(['images', 'seller','category'])
            ->where('idle_status', 1); // 只搜尋上架中的商品

        // 5. 動態加入搜尋條件
        if (!empty($filters['query'])) {
            $searchTerm = $filters['query'];
            $itemsQuery->where(function ($q) use ($searchTerm) {
                $q->where('idle_name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('idle_details', 'LIKE', "%{$searchTerm}%");
            });
        }
        if (!empty($filters['category_id'])) {
            $itemsQuery->where('category_id', $filters['category_id']);
        }
        if (!empty($filters['min_price'])) {
            $itemsQuery->where('idle_price', '>=', $filters['min_price']);
        }
        if (!empty($filters['max_price'])) {
            $itemsQuery->where('idle_price', '<=', $filters['max_price']);
        }

        // 6. 處理排序
        $this->applySorting($itemsQuery, $sortOrder);

        // 7. 執行查詢並分頁
        $items = $itemsQuery->paginate(12);

        // 8. 取得所有分類
        $categories = Category::orderBy('name')->get();
        
        // 9. 判斷是否有篩選條件
        $hasFilters = !empty(array_filter($filters));

        // 10. 如果沒有搜尋結果，取得相似商品
        $similarItems = collect();
        if ($items->isEmpty() && !empty($filters['query'])) {
            $similarItems = $this->getSimilarItems($filters['query']);
        }

        // 11. 記錄搜尋日誌
        if (!empty($filters['query'])) {
            $this->logSearch($filters['query'], $items->total());
        }

        // 12. 回傳視圖
        return view('search.results', [
            'items' => $items,
            'categories' => $categories,
            'filters' => $filters,
            'hasFilters' => $hasFilters,
            'similarItems' => $similarItems,
        ]);
    }

    /**
     * 取得搜尋建議（用於自動完成）
     */
    public function suggestions(Request $request)
    {
        $query = $request->input('q');
        if (strlen($query) < 2) {
            return response()->json([]);
        }
        $suggestions = IdleItem::where('idle_status', 1)
            ->where('idle_name', 'LIKE', "%{$query}%")
            ->select('idle_name')
            ->distinct()
            ->limit(8)
            ->pluck('idle_name');
        return response()->json($suggestions);
    }

    /**
     * 應用排序邏輯
     */
    private function applySorting($query, string $sortOrder): void
    {
        switch ($sortOrder) {
            case 'oldest': $query->oldest('created_at'); break;
            case 'price_asc': $query->orderBy('idle_price', 'asc'); break;
            case 'price_desc': $query->orderBy('idle_price', 'desc'); break;
            case 'name_asc': $query->orderBy('idle_name', 'asc'); break;
            case 'name_desc': $query->orderBy('idle_name', 'desc'); break;
            default: $query->latest('created_at'); break;
        }
    }

    /**
     * 取得相似商品
     */
    private function getSimilarItems(string $query, int $limit = 4)
    {
        $keywords = array_filter(explode(' ', $query));
        if (empty($keywords)) {
            return collect();
        }
        $similarQuery = IdleItem::with(['images', 'seller', 'category'])->where('idle_status', 1);
        $similarQuery->where(function ($q) use ($keywords) {
            foreach ($keywords as $keyword) {
                $q->orWhere('idle_name', 'LIKE', "%{$keyword}%");
            }
        });
        return $similarQuery->inRandomOrder()->limit($limit)->get();
    }

    /**
     * 記錄搜尋日誌
     */
    private function logSearch(string $query, int $resultCount): void
    {
        Log::info('Search performed', [
            'query' => $query,
            'result_count' => $resultCount,
            'user_ip' => request()->ip(),
        ]);
    }
}

        
        /*
        if ($query) {
            $itemsQuery->where(function ($q) use ($query) {
                $q->where('idle_name', 'LIKE', "%{$query}%")
                    ->orWhere('idle_details', 'LIKE', "%{$query}%"); // 也搜尋商品描述
            });
        }

        if ($categoryId) {
            $itemsQuery->where('category_id', $categoryId);
        }

        if ($minPrice) {
            $itemsQuery->where('idle_price', '>=', $minPrice);
        }

        if ($maxPrice) {
            $itemsQuery->where('idle_price', '<=', $maxPrice);
        }

        // 6. 執行查詢並分頁
        $items = $itemsQuery->latest()->paginate(12);

        // 7. 取得所有分類，供搜尋表單使用
        $categories = Category::all();

        // 8. 回傳視圖，並將所有需要的資料傳遞過去
        return view('search.results', [
            'items' => $items,
            'categories' => $categories,
        ]);
    }

