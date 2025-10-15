{{-- resources/views/search/index.blade.php --}}
@extends('layouts.app')

@section('title', '搜尋商品 - NHU 二手交易平台')

@section('content')
<div class="container">
    <h1 class="mb-3">搜尋商品</h1>

    {{-- 🔍 搜尋表單 --}}
    <form method="GET" action="{{ route('search.index') }}" class="mb-4 d-flex flex-wrap gap-2">
        <input
            type="text"
            name="query"
            placeholder="搜尋商品名稱或描述..."
            value="{{ old('query', request('query', request('q'))) }}"
            class="form-control"
            style="max-width: 280px;"
        >

        <select name="category_id" class="form-select" style="max-width: 200px;">
            <option value="">所有分類</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ (string)request('category_id') === (string)$category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>

        <input
            type="number" name="min_price" placeholder="最低價格"
            value="{{ request('min_price') }}" class="form-control" style="max-width: 160px;" min="0" step="1">
        <input
            type="number" name="max_price" placeholder="最高價格"
            value="{{ request('max_price') }}" class="form-control" style="max-width: 160px;" min="0" step="1">

        <button type="submit" class="btn btn-primary">搜尋</button>
        @if(request()->hasAny(['query','q','category_id','min_price','max_price']))
            <a href="{{ route('search.index') }}" class="btn btn-outline-secondary">清除篩選</a>
        @endif
    </form>

    {{-- 🔹 搜尋結果 --}}
    <div class="product-list d-grid" style="grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 16px;">
        @forelse($items as $item)
            {{-- 共用卡片（含圖片 fallback） --}}
            @include('partials.product-card', ['item' => $item, 'lazy' => true, 'showCategory' => true])
        @empty
            <div class="text-muted">沒有找到符合條件的商品。</div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $items->links() }}
    </div>
</div>
@endsection
