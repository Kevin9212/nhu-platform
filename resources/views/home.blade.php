{{-- resources/views/home.blade.php --}}
<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8">
    <title>南華大學二手交易平台</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>

<body>

    {{-- 修改重點：直接引入我們建立的共用頁首 --}}
    @include('partials.header')

    <div class="search-bar">
        <form action="#" method="GET">
            <input type="text" name="q" placeholder="搜尋商品名稱或分類...">
            <button type="submit">搜尋</button>
        </form>
    </div>

    <section class="section">
        <h2>📦 最新上架商品</h2>
        <div class="products">
            @forelse ($items as $item)
            <div class="product-card">
                {{-- 圖片連結到商品詳細頁 --}}
                <a href="{{ route('idle-items.show', $item->id) }}" class="product-image-link">
                    <img src="{{ asset($item->images->first()->image_url ?? 'https://placehold.co/600x400/EFEFEF/AAAAAA&text=無圖片') }}" alt="{{ $item->idle_name }}">
                </a>
                <div class="product-content">
                    <h3>
                        {{-- 標題也連結到商品詳細頁 --}}
                        <a href="{{ route('idle-items.show', $item->id) }}">{{ $item->idle_name }}</a>
                    </h3>
                    {{-- 新增：顯示賣家資訊 --}}
                    <div class="seller">
                        賣家：<a href="#">{{ $item->seller->nickname }}</a>
                    </div>
                    <p class="price">NT$ {{ number_format($item->idle_price) }}</p>
                </div>
            </div>
            @empty
            <p>目前沒有任何上架中的商品。</p>
            @endforelse
        </div>
    </section>

    <section class="section">
        <h2>🎁 隨機推薦商品</h2>
        <div class="products">
            @forelse ($items->shuffle()->take(4) as $item)
            <div class="product-card">
                <a href="{{ route('idle-items.show', $item->id) }}" class="product-image-link">
                    <img src="{{ asset($item->images->first()->image_url ?? 'https://placehold.co/600x400/EFEFEF/AAAAAA&text=無圖片') }}" alt="{{ $item->idle_name }}">
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
            <p>目前沒有任何商品可供推薦。</p>
            @endforelse
        </div>
    </section>

</body>

</html>