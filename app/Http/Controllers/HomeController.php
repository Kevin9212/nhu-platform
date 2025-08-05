<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\IdleItem;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller {
    /**
     * 顯示首頁
     */
    public function index(): View {
        // 取得最新上架的商品（分頁）
        $items = IdleItem::with(['images', 'seller', 'category'])
            ->where('idle_status', 1)
            ->latest('created_at')  // 明確指定排序欄位
            ->paginate(12);

        // 取得隨機推薦商品
        $randomItems = $this->getRandomItems(8);

        // 取得所有分類供搜尋表單使用
        $categories = Category::orderBy('name')->get();

        return view('home', [
            'items' => $items,
            'randomItems' => $randomItems,
            'categories' => $categories,
        ]);
    }

    /**
     * 處理非同步取得隨機推薦商品的請求
     */
    public function randomItems(Request $request) {
        // 驗證請求參數
        $validated = $request->validate([
            'limit' => 'nullable|integer|min:1|max:20',
            'exclude' => 'nullable|string', // 排除某些商品ID，用逗號分隔
        ]);

        $limit = $validated['limit'] ?? 8;
        $excludeIds = [];

        // 處理要排除的商品ID
        if (!empty($validated['exclude'])) {
            $excludeIds = array_filter(
                array_map('intval', explode(',', $validated['exclude']))
            );
        }

        $randomItems = $this->getRandomItems($limit, $excludeIds);

        // 直接回傳渲染好的局部視圖
        return view('partials.product-grid', [
            'items' => $randomItems,
            'emptyMessage' => '目前沒有任何商品可供推薦。',
            'showCategory' => true
        ]);
    }

    /**
     * 私有方法：取得隨機商品
     *
     * @param int $limit 限制數量
     * @param array $excludeIds 要排除的商品ID陣列
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getRandomItems(int $limit = 8, array $excludeIds = []) {
        $query = IdleItem::with(['images', 'seller', 'category'])
            ->where('idle_status', 1);

        // 排除指定的商品ID
        if (!empty($excludeIds)) {
            $query->whereNotIn('id', $excludeIds);
        }

        // 為了避免大資料表的效能問題，可以考慮使用更有效率的隨機查詢
        // 方法1: 簡單隨機（適合小資料表）
        // 若商品數量少於1000，會跳到方法2
        if (IdleItem::where('idle_status', 1)->count() < 1000) {
            return $query->inRandomOrder()->limit($limit)->get();
        }

        // 方法2: 更有效率的隨機查詢（適合大資料表）
        $maxId = IdleItem::where('idle_status', 1)->max('id');
        $minId = IdleItem::where('idle_status', 1)->min('id');

        if (!$maxId || !$minId) {
            return collect(); // 回傳空集合
        }

        $randomIds = [];
        $attempts = 0;

        while (count($randomIds) < $limit && $attempts < $limit * 3) {
            $randomId = rand($minId, $maxId);
            if (!in_array($randomId, $excludeIds) && !in_array($randomId, $randomIds)) {
                $item = IdleItem::where('id', $randomId)
                    ->where('idle_status', 1)
                    ->first();

                if ($item) {
                    $randomIds[] = $randomId;
                }
            }
            $attempts++;
        }

        return $query->whereIn('id', $randomIds)->get();
    }
}
