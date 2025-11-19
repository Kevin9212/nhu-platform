<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\IdleItem;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class AdminStatsController extends Controller
{
    public function index()
    {
        // ---- 1. 總商品數 / 上架中 / 非上架 ----
        $totalItems   = IdleItem::count();
        $activeItems  = IdleItem::where('idle_status', 1)->count();
        $inactiveItems = $totalItems - $activeItems;

        // ---- 2. 依分類統計 ----
        $itemsByCategory = IdleItem::query()
            ->selectRaw('categories.id as category_id, categories.name as category_name, COUNT(*) as total')
            ->join('categories', 'idle_items.category_id', '=', 'categories.id')
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total')
            ->get()
            ->map(function ($row) {
                return [
                    'category_id'   => (int) $row->category_id,
                    'category_name' => $row->category_name,
                    'total'         => (int) $row->total,
                ];
            });

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
            });

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
