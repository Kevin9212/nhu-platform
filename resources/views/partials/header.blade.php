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
                @php
                    $unread = Auth::user()->unreadNotifications()->count();
                    $base = rtrim(request()->getBaseUrl(), '/'); // ✅ 自動偵測 /nhu-platform/public
                @endphp

                <div class="nhu-popover" id="nhu-notify"
                    data-fetch="{{ $base . route('notifications.fetch', [], false) }}"
                    data-readall="{{ $base . route('notifications.readAll', [], false) }}">
                    <a href="#" class="nhu-popover-toggle" aria-label="切換通知" onclick="return NHU.notify.toggle(event)">
                        <i class="fa fa-bell-o"></i>
                        <span class="nhu-badge {{ $unread ? '' : 'is-hidden' }}" data-nhu="badge">{{ $unread }}</span>
                    </a>

                    <div class="nhu-popover-panel" data-nhu="panel" aria-hidden="true">
                        <div class="nhu-popover-header">
                            <span>通知</span>
                            <div class="nhu-actions">
                                <button class="icon-btn" title="全部已讀" onclick="NHU.notify.readAll(event)">
                                    <i class="fa fa-check"></i>
                                </button>
                                <a class="icon-btn" title="查看全部" href="{{ $base . route('notifications.index', [], false) }}">
                                    <i class="fa fa-cog"></i>
                                </a>
                            </div>
                        </div>

                        <div class="nhu-popover-body">
                            <div class="nhu-loading" data-nhu="loading"><i class="fa fa-circle-o-notch fa-spin"></i></div>
                            <div class="nhu-empty is-hidden" data-nhu="empty">你沒有收到通知</div>
                            <div class="nhu-list" data-nhu="list"></div>
                        </div>

                        <a class="nhu-popover-footer" href="{{ $base . route('notifications.index', [], false) }}">查看全部</a>
                    </div>
                </div>

                {{-- 🔹 登出 --}}
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
/* === Notification popover 基本樣式 === */
.nhu-popover { position: relative; display: inline-block; margin-left: 8px; }
.nhu-popover .nhu-popover-toggle { color: #fff; text-decoration: none; position: relative; padding: 6px; display: inline-block; }
.nhu-popover .fa-bell-o { font-size: 18px; }
.nhu-badge { position: absolute; top: -6px; right: -2px; background:#dc3545; color:#fff; border-radius:999px;
  padding:0 6px; font-size:12px; line-height:18px; min-width:18px; text-align:center; }
.is-hidden { display: none !important; }

.nhu-popover-panel { position: absolute; right: 0; top: calc(100% + 8px); width: 360px; max-height: 460px;
  background:#fff; border:1px solid #e5e7eb; border-radius:10px; box-shadow:0 10px 30px rgba(0,0,0,.08);
  overflow:hidden; z-index: 1000; display:none; }
.nhu-popover [data-nhu="panel"][aria-hidden="false"] { display:block; }

.nhu-popover-header { display:flex; align-items:center; justify-content:space-between; padding:10px 12px;
  background:#197e75; color:#fff; font-weight:600; }
.nhu-actions .icon-btn { background:transparent; border:0; color:#fff; cursor:pointer; margin-left:8px; }

.nhu-popover-body { position:relative; min-height:140px; }
.nhu-loading { text-align:center; padding:18px; color:#888; }
.nhu-empty { padding:18px; color:#888; }
.nhu-list { max-height:340px; overflow:auto; }
.nhu-item { display:block; padding:10px 12px; text-decoration:none; color:#333; border-bottom:1px solid #f3f4f6; }
.nhu-item:hover { background:#f8fafc; }
.nhu-item.is-unread { background:#fffef6; }
.nhu-item-title { font-weight:600; font-size:14px; margin-bottom:2px; }
.nhu-item-text { font-size:13px; color:#555; }
.nhu-item-time { font-size:12px; color:#999; margin-top:4px; }

.nhu-popover-footer { display:block; text-align:center; padding:10px 12px; color:#197e75; text-decoration:none; border-top:1px solid #eee; }
.nhu-popover-footer:hover { background:#f8fafc; }
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
