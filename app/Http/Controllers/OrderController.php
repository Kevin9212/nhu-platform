<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function create()
    {
        return view('orders.create');
    }

    public function store(Request $request)
    {
        // 1. 驗證
        $validated = $request->validate([
            'idle_item_id' => 'required|exists:idle_items,id', // 商品
            'order_price'  => 'required|integer|min:0',        // 議價後價格

            'meet_address' => 'required|string',
            'meet_lat'     => 'required|numeric',
            'meet_lng'     => 'required|numeric',
            'meet_date'    => 'required|date|after_or_equal:today',
            'meet_time'    => 'required',
        ]);

        // 2. 組成 meetup_location（你的 Model 有 cast 成 array）
        $meetup = [
            'address' => $validated['meet_address'],
            'lat'     => $validated['meet_lat'],
            'lng'     => $validated['meet_lng'],
            'date'    => $validated['meet_date'],
            'time'    => $validated['meet_time'],
        ];

        // 3. 寫進 orders 資料表
        $order = Order::create([
            'order_number'    => now()->format('YmdHis') . Str::random(4),
            'user_id'         => auth()->id(),                // 買家
            'idle_item_id'    => $validated['idle_item_id'],  // 商品
            'order_price'     => $validated['order_price'],
            'payment_status'  => 'unpaid',
            'payment_way'     => 'meet',
            'order_status'    => 'created',
            'meetup_location' => $meetup,
        ]);

        // 4. 成功後導回會員中心的「訂單管理 → 賣出的訂單」區塊
        $url = route('member.index', ['tab' => 'orders']) . '#orders-seller';

        return redirect()
            ->to($url)
            ->with('success', '訂單已成立');
    }
}
