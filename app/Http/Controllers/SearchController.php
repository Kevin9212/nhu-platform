<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\IdleItem;
use App\Models\Category;

class SearchController extends Controller {
    public function index(Request $request) {
        // 接收搜尋條件
        $query      = $request->input('query');        // 關鍵字
        $categoryId = $request->input('category_id');  // 分類
        $minPrice   = $request->input('min_price');    // 最低價
        $maxPrice   = $request->input('max_price');    // 最高價

        // 先抓分類給下拉選單用
        $categories = Category::all();

        // 查詢 IdleItem
        $itemsQuery = IdleItem::with(['seller', 'category', 'images'])
            ->where('idle_status', 1);

        // 模糊搜尋
        if ($query) {
            $itemsQuery->where(function ($q) use ($query) {
                $q->where('idle_name', 'LIKE', "%{$query}%")
                    ->orWhere('idle_details', 'LIKE', "%{$query}%");
            });
        }

        // 分類篩選
        if ($categoryId) {
            $itemsQuery->where('category_id', $categoryId);
        }

        // 價格範圍
        if ($minPrice !== null) {
            $itemsQuery->where('idle_price', '>=', $minPrice);
        }
        if ($maxPrice !== null) {
            $itemsQuery->where('idle_price', '<=', $maxPrice);
        }

        // 分頁
        $items = $itemsQuery->orderBy('created_at', 'desc')->paginate(12);

        return view('search.index', compact('items', 'categories'));
    }

    public function suggestions(Request $request) {
        $term = $request->input('term');

        $suggestions = IdleItem::where('idle_status', 1)
            ->where('idle_name', 'LIKE', "%{$term}%")
            ->limit(5)
            ->pluck('idle_name');

        return response()->json($suggestions);
    }
}
