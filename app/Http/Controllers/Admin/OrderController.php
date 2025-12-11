<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(): View
    {
        $orders = Order::with(['user', 'item.seller'])
            ->latest()
            ->paginate(15);

        return view('admin.orders.index', compact('orders'));
    }

    public function destroy(Order $order): RedirectResponse
    {
        if ($item = $order->item) {
            $item->idle_status = 1;
            $item->current_buyer_id = null;
            $item->save();
        }

        $order->delete();

        return back()->with('success', '訂單已強制刪除，相關商品已重新上架');
    }
}