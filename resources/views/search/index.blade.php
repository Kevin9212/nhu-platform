@extends('layouts.app')

@section('title', '搜尋商品 - NHU 二手交易平台')

@section('content')
<div class="container">
    <h1>搜尋商品</h1>

    {{-- 🔍 搜尋表單 --}}
    <form method="GET" action="{{ route('search.index') }}" style="margin-bottom: 1rem;">
        <input type="text" name="query" placeholder="搜尋商品名稱或描述..." value="{{ request('query') }}">

        <select name="category_id">
            <option value="">所有分類</option>
            @foreach($categories as $category)
            <option value="{{ $category->id }}"
                {{ request('category_id') == $category->id ? 'selected' : '' }}>
                {{ $category->name }}
            </option>
            @endforeach
        </select>

        <input type="number" name="min_price" placeholder="最低價格" value="{{ request('min_price') }}">
        <input type="number" name="max_price" placeholder="最高價格" value="{{ request('max_price') }}">

        <button type="submit">搜尋</button>
    </form>

    {{-- 🔹 搜尋結果 --}}
    <div class="product-list">
        @forelse($items as $item)
        <div class="product-card">
            {{-- 圖片 --}}
            @if($item->images->isNotEmpty())
            <img src="{{ asset('storage/' . $item->images->first()->image_path) }}" alt="{{ $item->idle_name }}" width="120">
            @endif

            {{-- 商品名稱 --}}
            <h3>{{ $item->idle_name }}</h3>

            {{-- 價格 --}}
            <p>NT$ {{ number_format($item->idle_price) }}</p>

            {{-- 分類 --}}
            <p>分類：{{ $item->category->name ?? '未分類' }}</p>

            {{-- 賣家 --}}
            <p>賣家：{{ $item->seller->nickname ?? '未知' }}</p>
        </div>
        @empty
        <p>沒有找到符合的商品</p>
        @endforelse
    </div>

    {{-- 分頁 --}}
    <div class="pagination">
        {{ $items->links() }}
    </div>
</div>
@endsection