{{-- resources/views/partials/header.blade.php --}}
<header>
    <h1><a href="{{ route('home') }}" style="color: inherit; text-decoration: none;">南華二手交易平台</a></h1>
    <nav>
        {{-- 使用 @guest 和 @auth 來判斷使用者的登入狀態 --}}
        @guest
        {{-- 未登入：顯示註冊和登入按鈕 --}}
        <a href="{{ route('register') }}" class="nav-button btn-secondary">註冊</a>
        <a href="{{ route('login') }}" class="nav-button btn-primary">登入</a>
        @else
        {{-- 已登入：顯示會員中心和登出按鈕 --}}
        <a href="{{ route('member.index') }}" class="nav-button btn-secondary">會員中心</a>

        {{-- 核心修正：將登出連結改為一個表單 --}}
        <form method="POST" action="{{ route('logout') }}" style="display: inline;">
            @csrf
            <a href="{{ route('logout') }}"
                class="nav-button btn-secondary"
                onclick="event.preventDefault(); this.closest('form').submit();">
                登出
            </a>
        </form>
        @endauth
    </nav>
</header>