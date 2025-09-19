{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'NHU 二手交易平台')</title>

    {{-- 共用 CSS/JS --}}
    @vite(['resources/css/style.css', 'resources/js/app.js'])

    {{-- 頁面專屬 CSS --}}
    @stack('styles')

    <style>
        /* 🔹 導覽列樣式 */
        .site-header {
            background: #f8f9fa;
            border-bottom: 1px solid #ddd;
            padding: 0.8rem 1.5rem;
            position: sticky;
            top: 0;
            z-index: 1000;
            transition: box-shadow 0.3s ease;
        }

        /* 🔹 下滑時的陰影效果 */
        .site-header.scrolled {
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
        }

        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
        }

        .logo a {
            font-weight: bold;
            font-size: 1.3rem;
            text-decoration: none;
            color: #333;
        }

        .nav-menu {
            display: flex;
            gap: 1.2rem;
            align-items: center;
        }

        .nav-link {
            text-decoration: none;
            color: #333;
            font-weight: 500;
            transition: color 0.2s;
        }

        .nav-link:hover {
            color: #007bff;
        }

        .btn-logout {
            background: #dc3545;
            color: white;
            border: none;
            padding: 0.4rem 0.8rem;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-logout:hover {
            background: #c82333;
        }

        /* 🔹 通知鈴鐺 */
        .notification-bell {
            position: relative;
            display: inline-block;
            margin-right: 1rem;
        }

        .notification-bell .icon {
            width: 24px;
            height: 24px;
            cursor: pointer;
        }

        .notification-count {
            position: absolute;
            top: -6px;
            right: -6px;
            background: #dc3545;
            color: white;
            font-size: 0.75rem;
            font-weight: bold;
            padding: 2px 6px;
            border-radius: 50%;
            line-height: 1;
            box-shadow: 0 0 4px rgba(0, 0, 0, 0.2);
        }

        /* 🔹 RWD 漢堡選單 */
        .menu-toggle {
            display: none;
            font-size: 1.5rem;
            background: none;
            border: none;
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .nav-menu {
                display: none;
                flex-direction: column;
                background: #f8f9fa;
                position: absolute;
                top: 60px;
                right: 0;
                width: 200px;
                padding: 1rem;
                border: 1px solid #ddd;
                border-radius: 6px;
            }

            .nav-menu.active {
                display: flex;
            }

            .menu-toggle {
                display: block;
            }
        }
    </style>
</head>

<body>
    {{-- 導覽列 --}}
    @include('partials.header')

    {{-- 主內容 --}}
    <main class="app-main">
        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
        @endif
        @if($errors->any())
        <div class="alert alert-error">
            <ul>
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @yield('content')
    </main>

    {{-- 頁尾 --}}
    <footer class="site-footer">
        <div class="container">
            <p>&copy; {{ date('Y') }} 南華大學二手交易平台. All Rights Reserved.</p>
        </div>
    </footer>

    {{-- JS --}}
    @stack('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const menuToggle = document.getElementById("menuToggle");
            const navMenu = document.getElementById("navMenu");
            const header = document.querySelector(".site-header");

            // 🔹 漢堡選單
            if (menuToggle && navMenu) {
                menuToggle.addEventListener("click", () => {
                    navMenu.classList.toggle("active");
                });
            }

            // 🔹 捲動陰影效果
            window.addEventListener("scroll", () => {
                if (window.scrollY > 20) {
                    header.classList.add("scrolled");
                } else {
                    header.classList.remove("scrolled");
                }
            });
        });
    </script>
</body>

</html>