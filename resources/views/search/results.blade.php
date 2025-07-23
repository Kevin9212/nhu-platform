{{-- resources/views/search/results.blade.php --}}
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>搜尋結果: {{ $query }} - NHU 二手交易平台</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

    @include('partials.header')

    <div class="container">
        <section class="section">
            <h2>搜尋「<span style="color: #007bff;">{{ $query }}</span>」的結果</h2>
            <div class="products">
                @forelse ($items as $item)
                <div class="product-card">
                    <a href="{{ route('idle-items.show', $item->id) }}" class="product-image-link">
                        @if($item->images->isNotEmpty())
                            <img src="{{ asset('storage/' . $item->images->first()->image_url) }}" alt="{{ $item->idle_name }}">
                        @else
                            <img src="https://placehold.co/600x400/EFEFEF/AAAAAA&text=無圖片" alt="{{ $item->idle_name }}">
                        @endif
                    </a>
                    <div class="product-content">
                        <h3>
                            <a href="{{ route('idle-items.show', $item->id) }}">{{ $item->idle_name }}</a>
                        </h3>
                        <div class="seller">
                            賣家：<a href="#">{{ $item->seller->nickname }}</a>
                        </div>
                        <p class="price">NT$ {{ number_format($item->idle_price) }}</p>
                    </div>
                </div>
                @empty
                <p>很抱歉，找不到與「{{ $query }}」相關的商品。</p>
                @endforelse
            </div>
            
            {{-- 顯示分頁連結，並將搜尋關鍵字附加到網址上 --}}
            <div class="pagination-links" style="margin-top: 2rem;">
                {{ $items->appends(['q' => $query])->links() }}
            </div>
        </section>
    </div>

</body>
</html>
