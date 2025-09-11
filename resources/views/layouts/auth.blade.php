{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', '南華大學二手交易平台')</title>

    {{-- 共用css/js --}}
    @vite(['resources/css/auth.css','resources/js/app.js'])
    {{--<link rel="stylesheet" href="{{ asset('css/style.css') }}">--}}
    {{-- 頁面專屬css --}}

    @stack('styles')
</head>

<body>
    {{-- 共用header --}}
    @include('partials.header')

    <main>

        @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div class="alert alert-error">
            {{ session('error') }}
        </div>
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

    <footer class="site-footer">
        <div class="container">
            <p>&copy; {{ date('Y') }} 南華大學二手交易平台. All Rights Reserved.</p>
        </div>
    </footer>
    {{-- 頁面專屬js --}}
    @stack('scripts')
    <script src="{{ asset('js/main.js') }}"></script>
</body>

</html>