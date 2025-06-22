{{-- resources/views/home.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NHU 二手交易平台</title>
    {{-- 引入外部 CSS 檔案 --}}
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
</head>

<body>
    <header class="header">
        <h1><a href="{{ route('home') }}" style="color: inherit;">NHU 二手交易平台</a></h1>
        <nav>
            {{-- 判斷用戶是否登入 --}}
            @auth
            {{-- 已登入，顯示刊登商品、用戶暱稱和登出按鈕 --}}
            <a href="#" class="btn-post">刊登商品</a>
            <span>您好，{{ Auth::user()->nickname }}</span>
            <a href="{{ route('logout') }}">登出</a>
            @else
            {{-- 未登入，顯示登入和註冊按鈕 --}}
            <a href="{{ route('login') }}">登入</a>
            <a href="{{ route('register') }}">註冊</a>
            @endauth
        </nav>
    </header>

    <main class="container">
        {{-- 新增：顯示 Session 提示訊息 --}}
        @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
        @elseif (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
        @endif

        {{-- 搜尋列 --}}
        <section class="search-bar">
            <form action="#" method="GET">
                <input type="text" name="query" placeholder="搜尋商品名稱...">
                <button type="submit">搜尋</button>
            </form>
        </section>

        <section>
            <h2>最新商品</h2>
            <div class="item-grid">
                {{-- 用 @forelse 迴圈來顯示所有從 Controller 傳過來的商品 --}}
                @forelse ($items as $item)
                <div class="item-card">
                    {{-- 將整個卡片變成一個連結，指向尚未建立的商品詳情頁 --}}
                    <a href="#">
                        {{-- 假設第一張圖是主要圖片，如果沒有圖片就顯示預設圖 --}}
                        <img src="{{ $item->images->first()->image_url ?? 'https://placehold.co/600x400/EFEFEF/AAAAAA&text=無圖片' }}" alt="{{ $item->idle_name }}">
                        <div class="item-card-content">
                            <h3>{{ $item->idle_name }}</h3>
                            <div class="price">NT$ {{ number_format($item->idle_price) }}</div>
                            <div class="seller">賣家：{{ $item->seller->nickname }}</div>
                        </div>
                    </a>
                </div>
                @empty
                <p>目前沒有任何上架中的商品。</p>
                @endforelse
            </div>
        </section>

        {{-- 分頁連結 --}}
        <div class="pagination-container">
            {{ $items->links() }}
        </div>
    </main>
</body>

</html>