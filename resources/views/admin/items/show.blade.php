@extends('layouts.admin')

@section('title', '商品詳情')

@section('content')
<h1>{{ $item->idle_name }}</h1>

<p><strong>賣家：</strong>{{ $item->seller->nickname }}</p>
<p><strong>狀態：</strong>{{ $item->idle_status ? '上架中' : '已下架' }}</p>
<p><strong>描述：</strong>{{ $item->idle_details }}</p>

<div>
    <h3>商品圖片</h3>
    @foreach($item->images as $image)
    <img src="{{ asset('storage/' . $image->image_url) }}" width="200" style="margin:5px;">
    @endforeach
</div>


<div style="margin-top: 12px; display:flex; gap:8px; align-items:center;">
    <a href="{{ route('admin.items.index') }}" class="btn">返回商品列表</a>
    <form action="{{ route('admin.items.destroy', $item) }}" method="POST" onsubmit="return confirm('確定刪除這件商品嗎？');">
        @csrf
        @method('DELETE')
        <button type="submit" style="background:#b91c1c;color:white;padding:6px 10px;border:none;border-radius:6px;">刪除商品</button>
    </form>
</div>
@endsection