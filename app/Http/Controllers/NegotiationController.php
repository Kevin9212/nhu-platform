<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Negotiation;
use App\Models\Conversation;
use App\Models\IdleItem;
use App\Models\Message;
use App\Notifications\NewOfferNotification;

class NegotiationController extends Controller
{
    /**
     * 建立議價（買家送出出價）
     */
    public function store(Request $request, IdleItem $item)
    {
        $buyer = Auth::user();
        $seller = $item->seller;

        // 驗證價格
        $request->validate([
            'price' => 'required|numeric|min:1',
        ]);

        // 建立議價紀錄
        $negotiation = Negotiation::create([
            'idle_item_id' => $item->id,
            'buyer_id'     => $buyer->id,
            'seller_id'    => $seller->id,
            'price'        => $request->input('price'),
            'status'       => 'pending',
        ]);

        // 確保聊天室存在
        $conversation = Conversation::firstOrCreate([
            'buyer_id'     => $buyer->id,
            'seller_id'    => $seller->id,
            'idle_item_id' => $item->id,
        ]);

        // 💬 普通訊息提示
        Message::create([
            'conversation_id' => $conversation->id,
            'user_id'         => $buyer->id,
            'content'         => "💰 {$buyer->nickname} 對商品「{$item->idle_name}」提出了議價：NT$ {$negotiation->price}",
            'is_system'       => true,
        ]);

        // 🧾 建立訂單摘要卡片訊息
        Message::create([
        'conversation_id' => $conversation->id,
        'user_id'         => $buyer->id,
        'content'         => json_encode([
        'type'       => 'order_summary',
        'item_name'  => $item->idle_name,
        'item_price' => $item->idle_price,
        'offer_price'=> $negotiation->price,
        'image'      => $item->images->first()->image_url ?? null,
        'status'     => $negotiation->status, // 新增這行
    ]),
    'is_system'       => true,
]);

        // 通知賣家
        $seller->notify(new NewOfferNotification($buyer, $item));

        return redirect()->route('conversations.show', $conversation->id)
            ->with('success', '已送出議價，進入聊天');
    }

    /**
     * 賣家同意議價
     */
    public function agree(Negotiation $negotiation)
    {
        if (Auth::id() !== $negotiation->seller_id) {
            return back()->with('error', '您沒有權限同意此議價');
        }

        $negotiation->status = 'accepted';
        $negotiation->save();

        return back()->with('success', '您已同意此議價');
    }

    /**
     * 賣家拒絕議價
     */
    public function reject(Negotiation $negotiation)
    {
        if (Auth::id() !== $negotiation->seller_id) {
            return back()->with('error', '您沒有權限拒絕此議價');
        }

        $negotiation->status = 'rejected';
        $negotiation->save();

        return back()->with('success', '您已拒絕此議價');
    }
}
