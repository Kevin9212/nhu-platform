{{-- resources/views/layouts/admin.blade.php --}}
<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', '管理後台') - NHU 二手交易平台</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <style>
        .admin-wrapper {
            display: flex;
        }

        .admin-sidebar {
            width: 240px;
            background: #343a40;
            color: #fff;
            min-height: 100vh;
            padding: 1rem;
        }

        .admin-sidebar h3 {
            color: #fff;
            text-align: center;
        }

        .admin-sidebar .nav-link {
            display: block;
            color: #adb5bd;
            padding: 0.75rem 1rem;
            border-radius: 4px;
            margin-bottom: 0.5rem;
        }

        .admin-sidebar .nav-link:hover,
        .admin-sidebar .nav-link.active {
            background: #495057;
            color: #fff;
        }

        .admin-main-content {
            flex-grow: 1;
            padding: 2rem;
        }
    </style>
    @stack('styles')
</head>

<body>
    <div class="admin-wrapper">
        <aside class="admin-sidebar">
            <h3>管理後台</h3>
            <nav>
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">儀表板</a>
                {{-- 未來的使用者管理、商品管理連結會放在這裡 --}}
            </nav>
        </aside>
        <main class="admin-main-content">
            @yield('content')
        </main>
    </div>
    @stack('scripts')
</body>

</html>