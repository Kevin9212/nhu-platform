@extends('layouts.admin')

@section('title', '商品管理')

@section('content')
<h1>商品管理</h1>

@if(session('success'))
    <div style="color:#22c55e;">{{ session('success') }}</div>
@endif

<form method="GET" action="{{ route('admin.items.index') }}" class="mb-3">
    <input type="text" name="keyword" placeholder="搜尋商品名稱..." value="{{ request('keyword') }}">
    <select name="status">
        <option value="">全部</option>
        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>上架中</option>
        <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>已下架</option>
    </select>
    <button type="submit">篩選</button>
</form>

<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>名稱</th>
            <th>賣家</th>
            <th>狀態</th>
            <th>操作</th>
        </tr>
    </thead>
    <tbody>
        @foreach($items as $item)
        <tr>
            <td>{{ $item->id }}</td>
            <td><a href="{{ route('admin.items.show', $item) }}">{{ $item->idle_name }}</a></td>
            <td>{{ $item->seller->nickname }}</td>
            <td>{{ $item->idle_status ? '上架中' : '已下架' }}</td>
            <td>
                @if(!$item->idle_status)
                <form action="{{ route('admin.items.approve', $item) }}" method="POST" style="display:inline;">
                    @csrf @method('PATCH')
                    <button type="submit">審核通過</button>
                </form>
                @endif
                <form action="{{ route('admin.items.reject', $item) }}" method="POST" style="display:inline;">
                    @csrf @method('PATCH')
                    <button type="submit">駁回</button>
                </form>
                <form action="{{ route('admin.items.toggle', $item) }}" method="POST" style="display:inline;">
                    @csrf @method('PATCH')
                    <button type="submit">切換狀態</button>
                </form>
                <form action="{{ route('admin.items.destroy', $item) }}" method="POST" style="display:inline;" onsubmit="return confirm('確定刪除這件商品嗎？');">
                    @csrf @method('DELETE')
                    <button type="submit" style="background:#b91c1c;color:white;padding:4px 8px;border:none;border-radius:4px;">刪除</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

{{ $items->links() }}
@endsection