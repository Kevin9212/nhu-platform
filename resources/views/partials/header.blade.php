{{-- resources/views/partials/header.blade.php --}}
<header class="site-header">
    <div class="header-container">
        {{-- 🔹 Logo --}}
        <div class="logo">
            <a href="{{ route('home') }}">NHU 2nd</a>
        </div>

        {{-- 🔹 漢堡選單按鈕（手機用） --}}
        <button class="menu-toggle" id="menuToggle">☰</button>

        {{-- 🔹 導覽選單 --}}
        <nav class="nav-menu" id="navMenu">
            <a href="{{ route('search.index') }}" class="nav-link">
                <img class="search-icon" src="{{ asset('images/search_icon.png') }}" alt="search">
            </a>

            @auth
                <a href="{{ route('conversations.index') }}" class="nav-link">
                    <img class="search-icon" src="{{ asset('images/speach-icon.png') }}" alt="chat">
                </a>

                <a href="{{ route('member.index') }}" class="nav-link">
                    <img class="search-icon" src="{{ asset('images/member-iocn.png') }}" alt="member">
                </a>

                {{-- 🔔 通知 --}}
                <a href="{{ route('notifications.index') }}" class="notification-bell">
                    <img src="{{ asset('images/notify.png') }}" alt="notify" class="icon">
                    @if(isset($unreadNotifications) && $unreadNotifications > 0)
                        <span class="notification-count">{{ $unreadNotifications }}</span>
                    @endif
                </a>

                {{-- 登出 --}}
                <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn-logout">登出</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="nav-link">登入</a>
                <a href="{{ route('register') }}" class="nav-link">註冊</a>
            @endauth
        </nav>
    </div>
</header>

@push('styles')
<style>
/* 你的 header 相關 CSS 可放這 */
</style>
@endpush

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", () => {
    const menuToggle = document.getElementById("menuToggle");
    const navMenu = document.getElementById("navMenu");
    if (menuToggle) {
        menuToggle.addEventListener("click", () => {
            navMenu.classList.toggle("active");
        });
    }
});
</script>
@endpush
