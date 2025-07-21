{{-- resources/views/partials/header.blade.php --}}
<header>
    <h1><a href="{{ route('home') }}" style="color: inherit; text-decoration: none;">南華二手交易平台</a></h1>
    <nav>
        {{-- "會員中心" 按鈕對所有人都可見 --}}
        <a href="{{ route('member.index') }}" class="nav-button btn-secondary">會員中心</a>

        @auth
        {{-- 已登入：只顯示登出按鈕，"刊登商品" 按鈕已移除 --}}
        <a href="{{ route('logout') }}" class="nav-button btn-secondary">登出</a>
        @else
        {{-- 未登入：只顯示登入按鈕 --}}
        <a href="{{ route('login') }}" class="nav-button btn-primary">登入</a>
        @endauth
    </nav>
</header>