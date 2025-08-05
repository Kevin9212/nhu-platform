{{-- resources/views/partials/product-grid.blade.php --}}
{{-- - --}}
<div class="products" data-grid="product-grid">
    @forelse ($items as $item)
    @include('partials.product-card', [
    'item' => $item,
    'showCategory' => $showCategory ?? false,
    'lazy' => $loop->index > 8 // 前8個商品不使用lazy loading
    ])
    @empty
    <div class="empty-state">
        <div class="empty-icon">📦</div>
        <h3>{{ $emptyMessage ?? '沒有找到任何商品。' }}</h3>
        @if(request('q') || request('category_id') || request('min_price') || request('max_price'))
        <p>試試調整搜尋條件或 <a href="{{ route('home') }}">瀏覽所有商品</a></p>
        @else
        <p><a href="{{ route('idle-items.create') }}">成為第一個上架商品的人！</a></p>
        @endif
    </div>
    @endforelse
</div>

