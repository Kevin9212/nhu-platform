<?php

namespace App\Http\Controllers;

use App\Models\IdleItem;
use App\Models\Negotiation;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NegotiationController extends Controller {
    /**
     * 儲存新的議價紀錄
     */
    public function store(Request $request, IdleItem $item) {
        $request->validate([
            'proposed_price' => 'required|integer|min:1',
        ]);

        $buyer = Auth::user();
        $seller = $item->user; // IdleItem 應該有 user() 關聯

        // 找到或建立買賣雙方的對話
        $conversation = Conversation::firstOrCreate([
            'buyer_id'  => $buyer->id,
            'seller_id' => $seller->id,
        ]);

        // 1) 儲存議價紀錄
        $negotiation = Negotiation::create([
            'idle_item_id'   => $item->id,
            'buyer_id'       => $buyer->id,
            'seller_id'      => $seller->id,
            'proposed_price' => $request->proposed_price,
        ]);

        // 2) 建立一則系統訊息
        $conversation->messages()->create([
            'sender_id' => $buyer->id,
            'content'   => "💰 買家提出 NT$ " . number_format($request->proposed_price) . " 的議價。",
            'msg_type'  => 'system',
        ]);

        // 3) 回應
        return back()->with('success', '已送出議價！');
    }
}
