<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IdleItem;
use Illuminate\Http\Request;

class ItemController extends Controller {
    // 商品列表
    public function index(Request $request) {
        $query = IdleItem::with('seller');

        // 篩選（狀態）
        if ($request->has('status')) {
            $query->where('idle_status', $request->status);
        }

        // 搜尋
        if ($request->has('keyword')) {
            $query->where('idle_name', 'like', '%' . $request->keyword . '%');
        }

        $items = $query->latest()->paginate(10);

        return view('admin.items.index', compact('items'));
    }

    // 顯示單一商品詳情
    public function show(IdleItem $item) {
        $item->load('seller', 'images');
        return view('admin.items.show', compact('item'));
    }

    // 商品審核通過
    public function approve(IdleItem $item) {
        $item->idle_status = 1; // 假設 1 = 上架
        $item->save();

        return redirect()->back()->with('success', '商品已通過審核並上架。');
    }

    // 商品駁回（下架）
    public function reject(IdleItem $item) {
        $item->idle_status = 0; // 假設 0 = 下架
        $item->save();

        return redirect()->back()->with('warning', '商品已被下架。');
    }

    // 商品狀態切換（上架 / 下架）
    public function toggleStatus(IdleItem $item) {
        $item->idle_status = $item->idle_status ? 0 : 1;
        $item->save();

        return redirect()->back()->with('success', '商品狀態已更新。');
    }
}
