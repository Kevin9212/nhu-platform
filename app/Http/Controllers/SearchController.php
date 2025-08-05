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
        ]);

        // 2. 取得所有篩選條件
        $query = $validated['q'] ?? null;
        $categoryId = $validated['category_id'] ?? null;
        $minPrice = $validated['min_price'] ?? null;
        $maxPrice = $validated['max_price'] ?? null;

        // 3. 處理價格邏輯錯誤（最小價格不能大於最大價格）
        if ($minPrice && $maxPrice && $minPrice > $maxPrice) {
            [$minPrice, $maxPrice] = [$maxPrice, $minPrice]; // 自動交換
        }

        // 4. 開始建立查詢，並動態加入條件
        $itemsQuery = IdleItem::with(['images', 'seller'])
            ->where('idle_status', 1); // 只搜尋上架中的商品

        // 5. 動態加入搜尋條件
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

    /**
     * 取得搜尋建議（可選功能，用於自動完成）
     */
    public function suggestions(Request $request) {
        $query = $request->input('q');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $suggestions = IdleItem::where('idle_status', 1)
            ->where('idle_name', 'LIKE', "%{$query}%")
            ->select('idle_name')
            ->distinct()
            ->limit(5)
            ->pluck('idle_name');

        return response()->json($suggestions);
    }
}
