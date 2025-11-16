<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\IdleItem;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class AdminStatsController extends Controller
{
    /**
     * 後台儀表板統計
     * 回傳格式：
     * {
     *   "summary": {...},
     *   "items_by_category": [...],
     *   "items_per_day": [...]
     * }
     */
    public function index()
    {
        // ---- 1. 總覽統計 ----
        $totalItems   = IdleItem::count();
        $activeItems  = IdleItem::where('idle_status', 1)->count();
        $inactiveItems = $totalItems - $activeItems;

        $summary = [
            'total_items'    => $totalItems,
            'active_items'   => $activeItems,
            'inactive_items' => $inactiveItems,
        ];

        // ---- 2. 各分類商品數量 ----
        // IdleItem.category_id -> Category.name
        $rawByCategory = IdleItem::select('category_id', DB::raw('COUNT(*) as total'))
            ->groupBy('category_id')
            ->get();

        $itemsByCategory = $rawByCategory->map(function ($row) {
            /** @var \App\Models\Category|null $category */
            $category = Category::find($row->category_id);

            return [
                'category_id'   => $row->category_id,
                'category_name' => $category ? $category->name : '未分類',
                'total'         => (int) $row->total,
            ];
        })->values();

        // ---- 3. 每日新增商品數量 ----
        $itemsPerDay = IdleItem::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get()
            ->map(function ($row) {
                return [
                    'date'  => $row->date,
                    'total' => (int) $row->total,
                ];
            })
            ->values();

        // ---- 4. 最終回傳 JSON ----
        return response()->json([
            'summary'          => $summary,
            'items_by_category'=> $itemsByCategory,
            'items_per_day'    => $itemsPerDay,
        ]);
    }
}
