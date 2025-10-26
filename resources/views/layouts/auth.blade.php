{{-- resources/views/layouts/auth.blade.php --}}
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <title>@yield('title', '南華大學二手交易平台')</title>

  {{-- Vite 資源（請確認這兩個檔案存在；沒有就改成你的檔名） --}}
  @vite(['resources/css/auth.css','resources/js/app.js'])

  {{-- 讓子頁面的 @push('styles') 生效 --}}
  @stack('styles')
</head>
<body class="bg-light">
  {{-- 為了避免 include 出錯先關掉；等畫面正常再打開 --}}
  @include('partials.header')
  
  <main class="container py-4">
    {{-- 共用訊息區 --}}
    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
      <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if($errors->any())
      <div class="alert alert-danger">
        <ul class="mb-0">
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    @yield('content')
  </main>

  {{-- 簡單頁腳（可自行美化） --}}
  <footer class="text-center text-muted py-4">
    &copy; {{ date('Y') }} 南華大學二手交易平台
  </footer>

  {{-- 讓子頁面的 @push('scripts') 生效 --}}
  @stack('scripts')

  {{-- 如果 public/js/main.js 不一定存在，就先拿掉，避免 404/誤會 --}}
  {{-- <script src="{{ asset('js/main.js') }}"></script> --}}
</body>
</html>
