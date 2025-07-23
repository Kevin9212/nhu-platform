{{-- resources/views/home.blade.php --}}
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>南華大學二手交易平台</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

    @include('partials.header')

    <div class="search-bar">
        {{-- 修正：將搜尋表單指向一個實際的路由 (search.index 需在 web.php 中定義) --}}
        <form action="{{ route('search.index') }}" method="GET">
            <input type="text" name="q" placeholder="搜尋商品名稱或分類...">
            <button type="submit">搜尋</button>
        </form>
    </div>

    <section class="section">
        <h2>📦 最新上架商品</h2>
        <div class="products">
            @forelse ($items as $item)
            <div class="product-card">
                <a href="{{ route('idle-items.show', $item->id) }}" class="product-image-link">
                    {{-- 修正：圖片路徑應指向 storage，並處理無圖片的情況 --}}
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
                        {{-- 建議：未來可以將賣家連結指向賣家個人頁面 --}}
                        賣家：<a href="#">{{ $item->seller->nickname }}</a>
                    </div>
                    <p class="price">NT$ {{ number_format($item->idle_price) }}</p>
                </div>
            </div>
            @empty
            <p>目前沒有任何上架中的商品。</p>
            @endforelse
        </div>
        
        {{-- 新增：顯示分頁連結 --}}
        <div class="pagination-links" style="margin-top: 2rem;">
            {{ $items->links() }}
        </div>
    </section>

    <section class="section">
        <h2>🎁 隨機推薦商品</h2>
        <div class="products">
            {{-- 修正：使用從 Controller 傳來的 $randomItems 變數 --}}
            @forelse ($randomItems as $item)
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
            <p>目前沒有任何商品可供推薦。</p>
            @endforelse
        </div>
    </section>

</body>
</html>
