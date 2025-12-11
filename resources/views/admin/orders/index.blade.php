@extends('layouts.admin')

@section('title', '訂單管理')

@section('content')
<h1>訂單管理</h1>

@if(session('success'))
    <div style="color:#22c55e;">{{ session('success') }}</div>
@endif

<table border="1" cellpadding="8" cellspacing="0" style="width:100%; border-collapse:collapse;">
    <thead>
        <tr>
            <th>ID</th>
            <th>訂單編號</th>
            <th>買家</th>
            <th>商品</th>
            <th>狀態</th>
            <th>價格</th>
            <th>建立時間</th>
            <th>操作</th>
        </tr>
    </thead>
    <tbody>
        @foreach($orders as $order)
        <tr>
            <td>{{ $order->id }}</td>
            <td>{{ $order->order_number }}</td>
            <td>{{ optional($order->user)->nickname ?? '已刪除會員' }}</td>
            <td>{{ optional($order->item)->idle_name ?? '已刪除商品' }}</td>
            <td>{{ $order->order_status }}</td>
            <td>{{ number_format($order->order_price, 0) }}</td>
            <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
            <td>
                <form method="POST" action="{{ route('admin.orders.destroy', $order) }}" onsubmit="return confirm('確定要強制刪除這筆訂單嗎？');" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="background:#b91c1c;color:white;padding:6px 10px;border:none;border-radius:6px;">強制刪除</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<div style="margin-top: 12px;">
    {{ $orders->links() }}
</div>
@endsection