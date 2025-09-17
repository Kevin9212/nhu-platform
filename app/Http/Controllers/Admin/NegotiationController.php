<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Negotiation;
use Illuminate\Http\Request;

class NegotiationController extends Controller {
    /**
     * 顯示所有議價紀錄
     */
    public function index() {
        // 載入關聯 (買家、賣家、商品)
        $negotiations = Negotiation::with(['buyer', 'seller', 'item'])
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('admin.negotiations.index', compact('negotiations'));
    }

    /**
     * 管理員介入：將議價標記為同意
     */
    public function agree(Negotiation $negotiation) {
        $negotiation->update(['status' => 'agreed']);
        return back()->with('success', '已同意此議價！');
    }

    /**
     * 管理員介入：將議價標記為拒絕
     */
    public function reject(Negotiation $negotiation) {
        $negotiation->update(['status' => 'rejected']);
        return back()->with('success', '已拒絕此議價！');
    }
}
