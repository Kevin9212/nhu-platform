{{-- resources/views/partials/header.blade.php --}}
<header>
    <h1><a href="{{ route('home') }}" style="color: inherit; text-decoration: none;">南華二手交易平台</a></h1>
    <nav>
        @guest
        {{-- Not logged in: Show Register and Login buttons --}}
        <a href="{{ route('register') }}" class="nav-button btn-secondary">註冊</a>
        <a href="{{ route('login') }}" class="nav-button btn-primary">登入</a>
        @else
        {{-- Logged in: Show notification bell, Member Center, and Logout form --}}
        <a href="#" class="nav-button notification-bell" title="通知">
            🔔
            @if(isset($unreadNotifications) && $unreadNotifications > 0)
            <span class="notification-count">{{ $unreadNotifications }}</span>
            @endif
        </a>
        <a href="{{ route('member.index') }}" class="nav-button btn-secondary">會員中心</a>

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