<?php

// app/Http/Controllers/SearchController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\IdleItem;
use App\Models\Category;

class SearchController extends Controller {
    public function index(Request $request) {
        // ★ 支援 q 與 query 兩種名稱
        $q          = trim((string) $request->input('q', $request->input('query', '')));
        $categoryId = $request->input('category_id');
        $minPrice   = $request->input('min_price');
        $maxPrice   = $request->input('max_price');

        $categories = Category::all();

        $itemsQuery = IdleItem::query()
            ->with([
                // ★ 圖片關聯一起載，並固定第一張排序
                'images' => fn($qq) => $qq->oldest('id'),
                'seller',
                'category',
            ])
            ->whereIn('idle_status', [1, 2]);

        if ($q !== '') {
            $itemsQuery->where(function ($w) use ($q) {
                $w->where('idle_name', 'like', "%{$q}%")
                  ->orWhere('idle_details', 'like', "%{$q}%");
            });
        }

        if ($categoryId) {
            $itemsQuery->where('category_id', $categoryId);
        }
        if ($minPrice !== null && $minPrice !== '') {
            $itemsQuery->where('idle_price', '>=', (int)$minPrice);
        }
        if ($maxPrice !== null && $maxPrice !== '') {
            $itemsQuery->where('idle_price', '<=', (int)$maxPrice);
        }

        // ★ 保留查詢字串，分頁不會丟掉篩選條件
        $items = $itemsQuery->orderByDesc('created_at')->paginate(12)->withQueryString();

        return view('search.index', compact('items', 'categories'));
    }

    public function suggestions(Request $request) {
        // ★ 支援 q 與 term 兩種名稱
        $term = trim((string) $request->input('q', $request->input('term', '')));

        $suggestions = IdleItem::whereIn('idle_status', [1, 2])
            ->where('idle_name', 'LIKE', "%{$term}%")
            ->limit(5)
            ->pluck('idle_name')
            ->unique()
            ->values();

        return response()->json($suggestions);
    }
}