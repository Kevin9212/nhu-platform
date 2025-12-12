<?php

namespace App\Http\Controllers;

use App\Models\IdleItem;
use App\Models\Negotiation;
use App\Models\Order as OrderModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function create(Request $request): View
    {
        $idleItem    = null;
        $orderPrice  = null;
        $idleItemId  = $request->input('idle_item_id');
        $negotiationId = $request->input('negotiation_id');
        $negotiationStatus = null;

        if ($negotiationId) {
            $negotiation = Negotiation::find($negotiationId);

            if ($negotiation && auth()->id() === $negotiation->buyer_id) {
                $idleItemId = $negotiation->idle_item_id;
                $orderPrice = (int) $negotiation->price;
            }
            $negotiationStatus = $negotiation?->status;
        }

        if ($idleItemId) {
            $idleItem = IdleItem::find($idleItemId);

            if ($idleItem) {
                $orderPrice ??= (int) $idleItem->idle_price;
            }
        }

    return view('orders.create', compact(
            'idleItem',
            'orderPrice',
            'negotiationId',
            'negotiationStatus'
        ));    
    }

    public function store(Request $request): RedirectResponse
    {
        $priceFromRequest = $request->input('order_price');

        // 若網址帶入的價格不存在或不是數字，嘗試用商品原價補齊
        if (!is_numeric($priceFromRequest) && $request->filled('idle_item_id')) {
            $priceFromRequest = optional(IdleItem::find($request->input('idle_item_id')))->idle_price;
        }

        // 強制將價格轉為整數，避免小數造成驗證失敗
        if (is_numeric($priceFromRequest)) {
            $request->merge([
                'order_price' => (int) $priceFromRequest,
            ]);
        }

        // 1. 驗證
        $validated = $request->validate([
            'idle_item_id' => 'required|exists:idle_items,id', // 商品
            'order_price'  => 'required|integer|min:0',        // 議價後價格

            'negotiation_id' => 'nullable|exists:negotiations,id',

            'meet_address' => 'required|string',
            'meet_lat'     => 'required|numeric',
            'meet_lng'     => 'required|numeric',
            'meet_date'    => 'required|date|after_or_equal:today',
            'meet_time'    => 'required',
        ]);

        $negotiation = null;
        if (!empty($validated['negotiation_id'])) {
            $negotiation = Negotiation::find($validated['negotiation_id']);

            if (!$negotiation || auth()->id() !== $negotiation->buyer_id) {
                return back()->with('error', '您沒有權限使用此議價成立訂單');
            }

            $validated['idle_item_id'] = $negotiation->idle_item_id;
            $validated['order_price']  = (int) $negotiation->price;
        }

        // 2. 組成 meetup_location（你的 Model 有 cast 成 array）
        $meetup = [
            'address' => $validated['meet_address'],
            'lat'     => $validated['meet_lat'],
            'lng'     => $validated['meet_lng'],
            'date'    => $validated['meet_date'],
            'time'    => $validated['meet_time'],
        ];

        // 3. 寫進 orders 資料表
        $order = OrderModel::create([
            'order_number'    => now()->format('YmdHis') . Str::random(4),
            'user_id'         => auth()->id(),                // 買家
            'idle_item_id'    => $validated['idle_item_id'],  // 商品
            'order_price'     => $validated['order_price'],
            'payment_status'  => false,
            'payment_way'     => '面交',
            'order_status'    => OrderModel::STATUS_PENDING,
            'meetup_location' => $meetup,
        ]);

        if ($negotiation) {
            $negotiation->delete();
        }

        if ($item = IdleItem::find($validated['idle_item_id'])) {
            $item->idle_status = 3; // 交易中
            $item->save();
        }

        // 4. 成功後導回會員中心的「訂單管理 → 購買的訂單」區塊
        $url = route('member.index', ['tab' => 'orders']) . '#orders-buyer';

        return redirect()
            ->to($url)
            ->with('success', '訂單已成立並紀錄面交資訊，請至訂單管理查看');
    }

    public function confirm(OrderModel $order): RedirectResponse
    {
        $userId   = auth()->id();
        $sellerId = optional($order->item)->user_id;

        if ($userId !== $order->user_id && $userId !== $sellerId) {
            abort(403, '您沒有權限確認這筆訂單');
        }

        if ($order->order_status === OrderModel::STATUS_CANCELLED) {
            return redirect()
                ->route('member.index', ['tab' => 'orders'])
                ->with('error', '訂單已取消，無法確認完成');
        }

        if ($order->order_status === OrderModel::STATUS_SUCCESS) {
            return redirect()
                ->route('member.index', ['tab' => 'orders'])
                ->with('success', '訂單已經為完成狀態');
        }

        $order->order_status   = OrderModel::STATUS_SUCCESS;
        $order->payment_status = true;
        $order->save();

        if ($item = $order->item) {
            if ($item->idle_status !== 4) {
                $item->idle_status = 4; // 已完成
                $item->save();
            }
        }

        return redirect()
            ->route('member.index', ['tab' => 'orders'])
            ->with('success', '訂單已確認完成');
    }
    public function update(Request $request, OrderModel $order): RedirectResponse
    {
        $userId   = auth()->id();
        $sellerId = optional($order->item)->user_id;

        if ($userId !== $order->user_id && $userId !== $sellerId) {
            abort(403, '您沒有權限編輯這筆訂單');
        }

        if ($order->order_status !== OrderModel::STATUS_PENDING) {
            return redirect()
                ->route('member.index', ['tab' => 'orders'])
                ->with('error', '只有待確認的訂單可以修改面交資訊');
        }

        $validated = $request->validate([
            'meet_address' => 'required|string|max:255',
            'meet_lat'     => 'nullable|numeric',
            'meet_lng'     => 'nullable|numeric',
            'meet_date'    => 'required|date|after_or_equal:today',
            'meet_time'    => 'required',
        ]);

        $location = $order->meetup_location ?? [];

        $order->meetup_location = [
            'address' => $validated['meet_address'],
            'lat'     => $validated['meet_lat'] ?? data_get($location, 'lat'),
            'lng'     => $validated['meet_lng'] ?? data_get($location, 'lng'),
            'date'    => $validated['meet_date'],
            'time'    => $validated['meet_time'],
        ];

        $order->save();

        $anchor = $userId === $order->user_id ? '#orders-buyer' : '#orders-seller';

        return redirect()
            ->to(route('member.index', ['tab' => 'orders']) . $anchor)
            ->with('success', '面交資訊已更新');
    }
    public function cancel(Request $request, OrderModel $order): RedirectResponse{
    $userId = auth()->id();
    $sellerId = optional($order->item)->user_id;

    if ($userId !== $order->user_id && $userId !== $sellerId) {
        abort(403, '您沒有權限取消這筆訂單');
    }

    if ($order->order_status === OrderModel::STATUS_SUCCESS) {
        return redirect()
            ->route('member.index', ['tab' => 'orders'])
            ->with('error', '訂單已完成，無法取消');
    }

    if ($order->order_status === OrderModel::STATUS_CANCELLED) {
        return redirect()
            ->route('member.index', ['tab' => 'orders'])
            ->with('success', '訂單已經處於取消狀態');
    }

    $order->order_status = OrderModel::STATUS_CANCELLED;
    $order->cancel_reason = '使用者取消訂單';
    $order->payment_status = false;
    $order->save();

    if ($item = $order->item) {
        if ($item->idle_status === 3) {
            $item->idle_status = 1; // 重新上架
            $item->save();
        }
    }

    return redirect()
        ->route('member.index', ['tab' => 'orders'])
        ->with('success', '訂單已取消，買賣家資訊已更新');
    }
}