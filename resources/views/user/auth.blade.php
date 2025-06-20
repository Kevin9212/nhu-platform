{{-- resources/views/user/auth.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>會員系統 - NHU 二手交易平台</title>

    {{-- 引入 CSS 檔案 --}}
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>

<body>
    <div class="auth-container">
        <h2>會員系統</h2>

        {{-- 顯示後端傳來的成功訊息 --}}
        @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
        @endif

        {{-- 顯示後端傳來的錯誤訊息 --}}
        @if ($errors->any())
        <div class="alert alert-danger">
            @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
            @endforeach
        </div>
        @endif

        {{-- 分頁標籤 --}}
        <div class="auth-tabs">
            <div id="tab-login" class="auth-tab" onclick="showTab('login')">登入</div>
            <div id="tab-register" class="auth-tab" onclick="showTab('register')">註冊</div>
        </div>

        {{-- 登入表單 --}}
        <form id="form-login" class="auth-form" action="{{ route('user.login') }}" method="POST">
            @csrf {{-- Laravel CSRF 保護 --}}
            <div class="form-group">
                <input type="email" name="account" class="form-control" placeholder="學校信箱" value="{{ old('account') }}" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" class="form-control" placeholder="密碼" required>
            </div>
            <button type="submit" class="btn btn-primary">登入</button>
        </form>

        {{-- 註冊表單 --}}
        <form id="form-register" class="auth-form" action="{{ route('user.register') }}" method="POST">
            @csrf
            <div class="form-group">
                <input type="email" name="account" class="form-control" placeholder="學校信箱" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" class="form-control" placeholder="密碼 (至少6位數)" required>
            </div>
            <div class="form-group">
                <input type="text" name="nickname" class="form-control" placeholder="暱稱" required>
            </div>
            <div class="form-group">
                <input type="text" name="user_phone" class="form-control" placeholder="聯絡電話" required>
            </div>
            <button type="submit" class="btn btn-success">註冊</button>
        </form>
    </div>

    {{-- 從外部檔案引入 JavaScript --}}
    <script src="{{ asset('js/auth.js') }}"></script>
</body>

</html>