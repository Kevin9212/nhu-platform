{{-- resources/views/partials/product-card.blade.php --}}
<div class="product-card" itemscope itemtype="https://schema.org/Product">
    <a href="{{ route('idle-items.show', $item->id) }}" class="product-image-link">
        @if($item->images->isNotEmpty())
        <img src="{{ asset('storage/' . $item->images->first()->image_url) }}"
            alt="{{ $item->idle_name }}"
            class="product-image"
            {{ ($lazy ?? true) ? 'loading=lazy' : '' }}
            itemprop="image">
        @else
        <img src="https://placehold.co/600x400/EFEFEF/AAAAAA&text=無圖片"
            alt="{{ $item->idle_name }}"
            class="product-image placeholder"
            {{ ($lazy ?? true) ? 'loading=lazy' : '' }}>
        @endif

        {{-- 商品狀態標籤 --}}
        @if($item->idle_status == 2)
        <span class="status-badge sold">已售出</span>
        @elseif($item->created_at->diffInDays() < 7)
            <span class="status-badge new">新上架</span>
            @endif
    </a>

    <div class="product-content">
        <h3 class="product-title" itemprop="name">
            <a href="{{ route('idle-items.show', $item->id) }}">{{ Str::limit($item->idle_name, 30) }}</a>
        </h3>

        <div class="seller" itemprop="seller" itemscope itemtype="https://schema.org/Person">
            賣家：<a href="{{ route('users.show', $item->seller->id) }}" itemprop="name">{{ $item->seller->nickname }}</a>
        </div>

        <div class="price-info">
            <p class="price" itemprop="price">NT$ {{ number_format($item->idle_price) }}</p>
            @if($item->original_price && $item->original_price > $item->idle_price)
            <span class="original-price">原價 NT$ {{ number_format($item->original_price) }}</span>
            <span class="discount">省 {{ round((($item->original_price - $item->idle_price) / $item->original_price) * 100) }}%</span>
            @endif
        </div>

        {{-- 分類標籤 --}}
        @if(($showCategory ?? false) && $item->category)
        <span class="category-tag" itemprop="category">{{ $item->category->name }}</span>
        @endif

        {{-- 額外資訊 --}}
        <div class="product-meta">
            <span class="post-time" title="{{ $item->created_at->format('Y-m-d H:i') }}">
                {{ $item->created_at->diffForHumans() }}
            </span>
            @if($item->views_count ?? false)
            <span class="views">👀 {{ $item->views_count }}</span>
            @endif
        </div>
    </div>
</div>