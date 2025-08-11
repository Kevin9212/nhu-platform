{{-- resources/views/search/results.blade.php --}}
@extends('layouts.app')

@section('title')
@if(!empty($filters['query']))
搜尋結果: {{ $filters['query'] }} - NHU 二手交易平台
@else
搜尋結果 - NHU 二手交易平台
@endif
@endsection

@section('content')
<div class="container">
    {{-- 搜尋表單 --}}
    @include('partials.search-form', ['filters' => $filters, 'categories' => $categories])

    <section class="section">
        {{-- 搜尋結果標題與統計 --}}
        <div class="search-header">
            <h2>
                @if(!empty($filters['query']))
                搜尋「<span class="search-keyword">{{ $filters['query'] }}</span>」的結果
                @else
                搜尋結果
                @endif
            </h2>
            <div class="search-stats">
                <span class="result-count">
                    找到 {{ $items->total() }} 個商品
                </span>

                {{-- 排序選項 --}}
                <div class="sort-options">
                    <label for="sort">排序：</label>
                    <select name="sort" id="sort" onchange="changeSortOrder(this.value)">
                        <option value="latest" {{ request('sort', 'latest') == 'latest' ? 'selected' : '' }}>最新上架</option>
                        <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>價格由低到高</option>
                        <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>價格由高到低</option>
                    </select>
                </div>
            </div>

            {{-- 顯示當前生效的篩選條件 --}}
            @if($hasFilters)
            <div class="active-filters">
                <span class="filters-label">篩選條件：</span>
                @if(!empty($filters['category_id']) && $categories)
                @php $selectedCategory = $categories->find($filters['category_id']); @endphp
                @if($selectedCategory)
                <span class="filter-tag">
                    分類：{{ $selectedCategory->name }}
                    <a href="{{ request()->fullUrlWithQuery(['category_id' => null, 'page' => null]) }}" class="remove-filter">×</a>
                </span>
                @endif
                @endif
                @if(!empty($filters['min_price']) || !empty($filters['max_price']))
                <span class="filter-tag">
                    價格：
                    @if(!empty($filters['min_price'])) NT$ {{ number_format($filters['min_price']) }} 以上 @endif
                    @if(!empty($filters['max_price'])) NT$ {{ number_format($filters['max_price']) }} 以下 @endif
                    <a href="{{ request()->fullUrlWithQuery(['min_price' => null, 'max_price' => null, 'page' => null]) }}" class="remove-filter">×</a>
                </span>
                @endif
                <a href="{{ route('search.index') }}" class="clear-all-filters">清除所有篩選</a>
            </div>
            @endif
        </div>

        {{-- 商品網格 --}}
        @include('partials.product-grid', ['items' => $items, 'showCategory' => true])

        {{-- 分頁連結 --}}
        @if($items->hasPages())
        <div class="pagination-container">
            <div class="pagination-info">
                顯示第 {{ $items->firstItem() ?? 0 }} - {{ $items->lastItem() ?? 0 }} 項，共 {{ $items->total() }} 項
            </div>
            <div class="pagination-links">
                {{ $items->appends(request()->query())->links() }}
            </div>
        </div>
        @endif

        {{-- 搜尋建議（當沒有結果時） --}}
        @if($items->isEmpty() && !empty($filters['query']))
        <div class="search-suggestions-section">
            <h3>搜尋建議</h3>
            <ul>
                <li>檢查拼寫是否正確</li>
                <li>嘗試使用更通用的關鍵字</li>
                <li><a href="{{ route('search.index') }}">瀏覽所有商品</a></li>
            </ul>

            @if(isset($similarItems) && $similarItems->isNotEmpty())
            <div class="similar-items">
                <h4>您可能感興趣的商品</h4>
                @include('partials.product-grid', ['items' => $similarItems, 'showCategory' => true])
            </div>
            @endif
        </div>
        @endif
    </section>
</div>
@endsection

@push('scripts')
<script>
    // 排序功能
    function changeSortOrder(sortValue) {
        const url = new URL(window.location);
        url.searchParams.set('sort', sortValue);
        url.searchParams.delete('page'); // 重新排序時回到第一頁
        window.location.href = url.toString();
    }
</script>
@endpush

@push('styles')
<style>
    .search-keyword {
        color: #007bff;
        font-weight: bold;
    }

    .search-stats {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin: 1rem 0;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .sort-options {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .sort-options select {
        padding: 0.3rem 0.5rem;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 0.9rem;
    }

    .active-filters {
        margin: 1rem 0;
        padding: 1rem;
        background: #f8f9fa;
        border-radius: 8px;
    }

    .filter-tag {
        display: inline-block;
        background: #e3f2fd;
        color: #1976d2;
        padding: 0.4rem 0.8rem;
        border-radius: 20px;
        margin: 0.2rem;
        font-size: 0.85rem;
    }

    .remove-filter {
        margin-left: 0.5rem;
        color: #999;
        text-decoration: none;
        font-weight: bold;
    }

    .clear-all-filters {
        color: #dc3545;
        text-decoration: none;
        font-size: 0.85rem;
    }

    .pagination-container {
        margin-top: 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .pagination-info {
        color: #6c757d;
        font-size: 0.9rem;
    }

    .search-suggestions-section {
        margin-top: 3rem;
        padding: 2rem;
        background: #f8f9fa;
        border-radius: 8px;
        text-align: center;
    }

    .similar-items {
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 1px solid #dee2e6;
    }
</style>
@endpush