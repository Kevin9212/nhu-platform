{{-- resources/views/layouts/admin.blade.php --}}
<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title','管理後台')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #0f172a;
            --panel: #111827;
            --muted: #334155;
            --text: #e5e7eb;
            --accent: #60a5fa;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            height: 100%;
        }

        body {
            margin: 0;
            font-family: 'Noto Sans TC', system-ui, -apple-system, Segoe UI, Roboto, Arial, 'Noto Sans TC', 'Microsoft JhengHei', sans-serif;
            background: #0b1220;
            color: var(--text);
        }

        .wrap {
            display: grid;
            grid-template-columns: 240px 1fr;
            min-height: 100vh;
        }

        aside {
            background: linear-gradient(180deg, #0b1220, #0a0f1a);
            border-right: 1px solid #1f2937;
        }

        .brand {
            padding: 16px 20px;
            font-weight: 700;
            letter-spacing: .5px;
        }

        .nav a {
            display: block;
            padding: 12px 20px;
            color: #cbd5e1;
            text-decoration: none;
            border-left: 3px solid transparent;
        }

        .nav a:hover {
            background: #0f172a;
            color: #fff;
            border-left-color: var(--accent);
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 14px 20px;
            border-bottom: 1px solid #1f2937;
            background: #0b1220;
            position: sticky;
            top: 0;
        }

        main {
            padding: 20px;
        }

        .card {
            background: #0b1220;
            border: 1px solid #1f2937;
            border-radius: 12px;
            padding: 16px;
        }

        .btn {
            display: inline-block;
            padding: 8px 12px;
            border-radius: 8px;
            background: #1f2937;
            color: #e5e7eb;
            text-decoration: none;
            border: 1px solid #334155;
        }

        .btn:hover {
            background: #273449;
        }
    </style>
</head>

<body>
    <div class="wrap">
        <aside>
            <div class="brand">NHU 平台 • 後台</div>
            <nav class="nav">
                <a href="{{ route('admin.dashboard') }}">📊 儀表板</a>
                <a href="{{ route('admin.users.index') }}">👥 使用者管理</a>
                {{-- 後續會加入：商品審核 / 訂單 / 地點 / 公告 等 --}}
            </nav>
        </aside>
        <div>
            <header>
                <div>@yield('header','後台')</div>
                <div>
                    <span>{{ auth()->user()->name ?? 'Admin' }}</span>
                    <form action="{{ route('logout') }}" method="POST" style="display:inline">
                        @csrf
                        <button class="btn">登出</button>
                    </form>
                </div>
            </header>
            <main>
                @yield('content')
            </main>
        </div>
    </div>
</body>

</html>