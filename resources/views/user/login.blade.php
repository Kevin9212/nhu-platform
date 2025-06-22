{{-- resources/views/user/login.blade.php --}}
<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>登入 - NHU 二手交易平台</title>

    {{-- 與註冊頁面共用同一個 CSS 檔案 --}}
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>

<body>
    <div class="auth-container">
        <h2>登入您的帳戶</h2>

        {{-- 顯示從註冊頁面跳轉過來的成功訊息 --}}
        @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
        @endif

        {{-- 顯示登入失敗的錯誤訊息 --}}
        @if ($errors->any())
        <div class="alert alert-danger">
            @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
            @endforeach
        </div>
        @endif

        {{-- 登入表單 --}}
        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="account">學校信箱</label>
                <input id="account" type="email" name="account" class="form-control" value="{{ old('account') }}" required>
            </div>
            <div class="form-group">
                <label for="password">密碼</label>
                <input id="password" type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">登入</button>
            <div class="auth-link" style="text-align: right; margin-top: 10px;">
                <a href="{{ route('password.request') }}">忘記密碼？</a>
            </div>
        </form>

        <div class="auth-link">
            還沒有帳號嗎？ <a href="{{ route('register') }}">立即註冊</a>
        </div>
    </div>
</body>

</html>