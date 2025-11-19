{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Google Fonts: Roboto Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:ital,wght@0,100..700;1,100..700&display=swap" rel="stylesheet">
    <title>@yield('title', 'NHU 二手交易平台')</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap JS (含 carousel 功能) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    {{-- 共用 CSS/JS --}}
    @vite(['resources/css/auth.css', 'resources/js/app.js'])

    {{-- 頁面專屬 CSS --}}
    @stack('styles')

    <style>

        :root {
            --site-header-height: 4.5rem;
            --site-header-gap-desktop: 0.5rem;
            --site-header-gap-mobile: 1.75rem;
        }

        body.has-fixed-header .app-main {
            margin-top: calc(var(--site-header-height) + var(--site-header-gap-desktop));
        }

        @media (max-width: 768px) {
            body.has-fixed-header .app-main {
                margin-top: calc(var(--site-header-height) + var(--site-header-gap-mobile));
            }
        }

        /* 🔹 導覽列樣式 */
        .site-header {
            background: #d2d7ce;
            border-bottom: 1px solid #ddd;
            padding: 0.5rem 0.5rem;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            width: 100%;
            z-index: 1000;
            transition: box-shadow 0.3s ease;
            background-color:#d2d7ce;
        }

        /* 🔹 下滑時的陰影效果 */
        .site-header.scrolled {
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
        }

        .header-container {
            position: relative;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
            width: 100%;
        }

        .logo a {
            font-weight: bold;
            font-size: 1.5rem;
            text-decoration: none;
            color: #333;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;

        }

        .nav-menu {
            display: flex;
            gap: 1.2rem;
            align-items: center;
            flex-wrap: nowrap;
        }

        .nav-icons {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.35rem 0.85rem;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.35);
            box-shadow: inset 0 0 0 1px rgba(99, 121, 115, 0.2);
        }

        .nav-icon-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 999px;
            border: 1px solid rgba(99, 121, 115, 0.35);
            background: #f8f9f5;
            color: #273636;
            transition: background 0.2s ease, color 0.2s ease, transform 0.2s ease;
            text-decoration: none;
        }

        .nav-icon-link:hover,
        .nav-icon-link:focus-visible {
            background: #ffffff;
            color: #1d2a2a;
            transform: translateY(-1px);
        }

        .nav-icon-img {
            width: 1.2rem;
            height: 1.2rem;
            display: block;
            object-fit: contain;
        }

        .nav-icons .nhu-popover {
            margin: 0;
            display: inline-flex;
        }

        .nav-icons .nhu-popover-toggle {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 999px;
            border: 1px solid rgba(99, 121, 115, 0.35);
            background: #f8f9f5;
            color: inherit;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .nav-icons .nhu-popover .fa-bell-o {
            font-size: 1.1rem;
            color: #273636;
        }

        .login{
            background-color:#637973;
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
            background: #637973;
            color: white;
            border: none;
            padding: 0.4rem 0.8rem;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-logout:hover {
            background: #5a6f69ff;
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
            font-size: 1.75rem;
            background: none;
            border: none;
            cursor: pointer;
            color: #333;
        }
        @media(max-width:992px){
            .header-container{
                gap: 0.75rem;
            }
            .nav-menu{
                gap: 0.9rem;
            }
        }

        @media (max-width: 768px) {
            .header-container{
                position: relative;
            }
            .nav-menu {
                display: none;
                flex-direction: column;
                align-items: flex-start;
                background: #f8f9fa;
                position: absolute;
                top: calc(100% + 0.5rem);
                right: 1rem;
                left: 1rem;
                padding: 1rem;
                border: 1px solid #ddd;
                border-radius: 6px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
                z-index: 1100;
            }

            .nav-menu.active {
                display: flex !important;
            }

            .menu-toggle {
                display: block;
            }
            .nav-menu > form,
            .nav-menu > a {
                width: 100%;
            }

            .nav-icons {
                width: 100%;
                justify-content: flex-start;
                flex-wrap: wrap;
                padding: 0.5rem;
                background: rgba(255, 255, 255, 0.9);
            }
        }
    </style>
</head>

<body class="@yield('body_class', 'has-fixed-header')">
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
            const docEl = document.documentElement;

            const updateHeaderHeight = () => {
                if (!header) return;
                const measured = header.getBoundingClientRect().height;
                docEl.style.setProperty("--site-header-height", `${measured}px`);
            };

            if (header) {
                updateHeaderHeight();
                if (window.ResizeObserver) {
                    const observer = new ResizeObserver(() => updateHeaderHeight());
                    observer.observe(header);
                }
            }

            // 🔹 漢堡選單
            if (menuToggle && navMenu) {
                const syncState = () => {
                    const isOpen = navMenu.classList.contains("active");
                    menuToggle.setAttribute("aria-expanded", isOpen ? "true" : "false");
                };

                menuToggle.addEventListener("click", (event) => {
                    event.preventDefault();
                    navMenu.classList.toggle("active");
                    syncState();
                });
                navMenu.querySelectorAll("a, button").forEach((node) => {
                    node.addEventListener("click", () => {
                        if (window.innerWidth <= 768) {
                            navMenu.classList.remove("active");
                            syncState();
                        }
                    });
                });

                window.addEventListener("resize", () => {
                    updateHeaderHeight();
                    if (window.innerWidth > 768) {
                        navMenu.classList.remove("active");
                        syncState();
                    }
                });

                syncState();
            }

            // 🔹 捲動陰影效果
            if (header) {
                window.addEventListener("scroll", () => {
                    if (window.scrollY > 20) {
                        header.classList.add("scrolled");
                    } else {
                        header.classList.remove("scrolled");
                    }
                });
            }
        });
    </script>
</body>

</html>
