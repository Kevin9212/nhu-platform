<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\IdleItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class AdminStatsController extends Controller
{
    /**
     * 後台統計資料 API
     *
     * 路由：GET /api/admin/stats
     * 回傳：
     *  - summary：總數 / 上架中 / 非上架
     *  - items_by_category：各分類商品數量
     *  - items_per_day：每天新增商品數（最多 30 筆）
     */
    public function index(): JsonResponse
    {
        // 總商品數
        $totalItems = IdleItem::count();

        // idle_status = 1 視為「上架中」
        $activeItems = IdleItem::where('idle_status', 1)->count();
        $inactiveItems = $totalItems - $activeItems;

        // 各分類商品數量（JOIN categories）
        $itemsByCategory = DB::table('idle_items')
            ->join('categories', 'idle_items.category_id', '=', 'categories.id')
            ->select(
                'categories.id as category_id',
                'categories.name as category_name',
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total')
            ->get();

        // 每天新增商品數量（最多 30 天）
        $itemsPerDay = DB::table('idle_items')
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->limit(30)
            ->get();

        return response()->json([
            'summary' => [
                'total_items'   => $totalItems,
                'active_items'  => $activeItems,
                'inactive_items'=> $inactiveItems,
            ],
            'items_by_category' => $itemsByCategory,
            'items_per_day'     => $itemsPerDay,
        ]);
    }
}
