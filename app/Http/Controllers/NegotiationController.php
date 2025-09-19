<?php

namespace App\Http\Controllers;

use App\Models\Negotiation;
use App\Models\IdleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NegotiationController extends Controller {
    /**
     * 建立新的議價
     */
    public function store(Request $request, IdleItem $item) {
        $request->validate([
            'proposed_price' => 'required|integer|min:1',
        ]);

        $buyer = Auth::user();

        // 建立議價紀錄
        $negotiation = Negotiation::create([
            'idle_item_id'  => $item->id,
            'buyer_id'      => $buyer->id,
            'seller_id'     => $item->seller->id,
            'proposed_price' => $request->proposed_price,
        ]);

        // 🚀 未來可以整合進聊天室訊息
        // $conversation->messages()->create([...]);

        return redirect()->route('conversation.start', ['user' => $item->seller->id])
            ->with('success', '議價已送出，請等待賣家回覆！');
    }

    /**
     * 賣家同意議價
     */
    public function agree(Negotiation $negotiation) {
        $negotiation->update(['status' => 'agreed']);
        return back()->with('success', '已同意此議價！');
    }

    /**
     * 賣家拒絕議價
     */
    public function reject(Negotiation $negotiation) {
        $negotiation->update(['status' => 'rejected']);
        return back()->with('info', '已拒絕此議價。');
    }
}
