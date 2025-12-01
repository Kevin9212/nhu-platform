<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Negotiation;
use App\Models\Conversation;
use App\Models\IdleItem;
use App\Models\Message;
use App\Notifications\NewOfferNotification;
use App\Notifications\NegotiationAcceptedNotification;
use App\Models\Order;

class NegotiationController extends Controller
{
    /**
     * 建立議價（買家送出出價）
     */
    public function store(Request $request, IdleItem $item)
    {
        $buyer  = Auth::user();
        $seller = $item->seller;

        // 驗證價格
        $validated = $request->validate([
            'price' => ['required', 'numeric', 'min:1'],
        ]);

        // 建立議價紀錄
        $negotiation = Negotiation::create([
            'idle_item_id' => $item->id,
            'buyer_id'     => $buyer->id,
            'seller_id'    => $seller->id,
            'price'        => $validated['price'],
            'status'       => 'pending',
        ]);

        // 確保聊天室存在（買家/賣家/商品 唯一組合）
        $conversation = Conversation::firstOrCreate([
            'buyer_id'     => $buyer->id,
            'seller_id'    => $seller->id,
            'idle_item_id' => $item->id,
        ]);

        // 💬 一般文字訊息（買家提出議價）
        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => $buyer->id,
            'idle_item_id'    => $item->id,
            'msg_type'        => 'text',
            'content'         => "💰 {$buyer->nickname} 對商品「{$item->idle_name}」提出了議價：NT$ {$negotiation->price}",
            'is_recalled'     => false,
        ]);

        // 🧾 訂單摘要卡片訊息
        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => $buyer->id,
            'idle_item_id'    => $item->id,
            'msg_type'        => 'order_summary',
            'content'         => json_encode([
                'type'        => 'order_summary',
                'item_name'   => $item->idle_name,
                'item_price'  => $item->idle_price,
                'offer_price' => $negotiation->price,
                'image'       => $item->images->first()->image_url ?? null,
                'status'      => $negotiation->status,
            ], JSON_UNESCAPED_UNICODE),
            'is_recalled'     => false,
        ]);

        // 通知賣家
        $seller->notify(new NewOfferNotification($buyer, $item));

        return redirect()->route('conversations.show', $conversation->id)
            ->with('success', '已送出議價，進入聊天');
    }

    /**
     * 賣家同意議價
     */
    public function agree(Negotiation $negotiation){
    if (Auth::id() !== $negotiation->seller_id) {
            return back()->with('error', '您沒有權限同意此議價');
        }

        // 更新議價狀態
        $negotiation->status = 'accepted';
        $negotiation->save();

        // 商品資料
        $item = IdleItem::with('images')->findOrFail($negotiation->idle_item_id);
        $item->idle_status = 3; // 交易中
        $item->save();

        // 確保對話存在
        $conversation = Conversation::firstOrCreate([
            'buyer_id'     => $negotiation->buyer_id,
            'seller_id'    => $negotiation->seller_id,
            'idle_item_id' => $item->id,
        ]);

        // 文字訊息：賣家已接受
        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => Auth::id(),
            'idle_item_id'    => $item->id,
            'msg_type'        => 'text',
            'content'         => "✅ 賣家已接受議價：NT$ {$negotiation->price}",
            'is_recalled'     => false,
        ]);

        // summary 卡片
        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => Auth::id(),
            'idle_item_id'    => $item->id,
            'msg_type'        => 'order_summary',
            'content'         => json_encode([
                'type'        => 'order_summary',
                'item_name'   => $item->idle_name,
                'item_price'  => $item->idle_price,
                'offer_price' => $negotiation->price,
                'image'       => optional($item->images->first())->image_url,
                'status'      => 'accepted',
            ], JSON_UNESCAPED_UNICODE),
            'is_recalled'     => false,
        ]);

        /**
         * ⭐⭐ 新增「建立訂單」功能 — 讓商品頁可以看到訂單 ⭐⭐
         */
        \App\Models\Order::firstOrCreate(
            [
                'user_id'      => $negotiation->buyer_id,
                'idle_item_id' => $item->id,
                'order_status' => 'pending',
            ],
            [
                'order_number'   => strtoupper(uniqid('ORD')),
                'order_price'    => $negotiation->price,     // 用議價後價格
                'payment_status' => false,
                'payment_way'    => '面交',
                'cancel_reason'  => null,
                'meetup_location'=> null,
            ]
        );

        // 通知買家：賣家已接受議價並建立訂單
        $buyer = $negotiation->buyer;
        if ($buyer) {
            $buyer->notify(new \App\Notifications\NegotiationAcceptedNotification(
                $item,
                $negotiation->price
            ));
        }

        return back()->with('success', '已接受議價並建立訂單');
    }


    /**
     * 賣家拒絕議價
     */
    public function reject(Negotiation $negotiation)
    {
        if (Auth::id() !== $negotiation->seller_id) {
            return back()->with('error', '您沒有權限拒絕此議價');
        }

        // 更新議價狀態
        $negotiation->status = 'rejected';
        $negotiation->save();

        // 商品資料
        $item = IdleItem::with('images')->findOrFail($negotiation->idle_item_id);

        // 確保對話存在
        $conversation = Conversation::firstOrCreate([
            'buyer_id'     => $negotiation->buyer_id,
            'seller_id'    => $negotiation->seller_id,
            'idle_item_id' => $item->id,
        ]);

        // ❌ 新增「賣家已拒絕」訊息
        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => Auth::id(),
            'idle_item_id'    => $item->id,
            'msg_type'        => 'text',
            'content'         => "❌ 賣家已拒絕議價：NT$ {$negotiation->price}",
            'is_recalled'     => false,
        ]);

        // 🧾 更新狀態卡片
        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => Auth::id(),
            'idle_item_id'    => $item->id,
            'msg_type'        => 'order_summary',
            'content'         => json_encode([
                'type'        => 'order_summary',
                'item_name'   => $item->idle_name,
                'item_price'  => $item->idle_price,
                'offer_price' => $negotiation->price,
                'image'       => optional($item->images->first())->image_url,
                'status'      => $negotiation->status,
            ], JSON_UNESCAPED_UNICODE),
            'is_recalled'     => false,
        ]);

        return back()->with('success', '您已拒絕此議價');
    }
    /** 
     * 導向訂單管理並通知對方（同時確保有訂單）
     */
    public function redirectToOrders(Negotiation $negotiation)
    {
        $userId = Auth::id();

    // 確認是買家或賣家才可以操作
        if (!in_array($userId, [$negotiation->buyer_id, $negotiation->seller_id])) {
            return back()->with('error', '您沒有權限操作此議價');
        }

        $item = IdleItem::with('images')->findOrFail($negotiation->idle_item_id);

        // 更新商品狀態為交易中
        if ($item->idle_status !== 3) {
            $item->idle_status = 3;
            $item->save();
        }

     // ⭐ 1. 確保訂單存在（沒有就建立一筆）
        $order = Order::firstOrCreate(
            [
                'user_id'      => $negotiation->buyer_id, // 買家
                'idle_item_id' => $item->id,              // 商品
                'order_status' => 'pending',              // 你目前用的狀態
            ],
            [
                'order_number'   => strtoupper(uniqid('ORD')),
                'order_price'    => $negotiation->price,     // 用議價後價格
                'payment_status' => false,
                'payment_way'    => '面交',
                'cancel_reason'  => null,
                'meetup_location'=> null,                    // 先留空，之後要再加面交地點可以再更新
            ]
        );

        // ⭐ 1.1 刪除已成立訂單的議價，避免繼續顯示在議價總覽
        $negotiation->delete();

        // ⭐ 2. 確保聊天室存在
        $conversation = Conversation::firstOrCreate([
            'buyer_id'     => $negotiation->buyer_id,
            'seller_id'    => $negotiation->seller_id,
            'idle_item_id' => $item->id,
        ]);

        // ⭐ 3. 在聊天室裡發一則訊息：提示雙方去訂單管理看
        $ordersUrl = route('member.index', ['tab' => 'orders']) . '#orders';

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => $userId,
            'idle_item_id'    => $item->id,
            'msg_type'        => 'text',
            'content'         => "📦 已成立訂單，請至訂單管理查看：{$ordersUrl}",
            'is_recalled'     => false,
        ]);

        // ⭐ 4. 直接導到會員中心的「訂單管理」分頁
        return redirect()->to($ordersUrl)->with('success', '已成立訂單並前往訂單管理');
    }
}