<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function create()
    {
        // 顯示成立訂單頁面（對應 resources/views/orders/create.blade.php）
        return view('orders.create');
    }

    public function store(Request $request)
    {
        // 簡單驗證
        $validated = $request->validate([
            'meet_address' => 'required|string',
            'meet_lat'     => 'required|numeric',
            'meet_lng'     => 'required|numeric',
            'meet_date'    => 'required|date|after_or_equal:today',
            'meet_time'    => 'required'
        ]);

        // 這裡未來可以加資料庫寫入，例如：
        // Order::create($validated);

        // 成功之後回傳
        return redirect()->route('orders.create')->with('success', '訂單已成立');
    }
}
