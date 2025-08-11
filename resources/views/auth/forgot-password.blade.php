{{-- resources/views/auth/forgot-password.blade.php --}}
<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>忘記密碼 - NHU 二手交易平台</title>
    @vite(['resources/css/style.css', 'resources/css/auth.css'])
</head>

<body class="auth-body">

    {{-- 引入共用的頁首 --}}
    @include('partials.header')

    <div class="auth-container">
        <h2>忘記密碼</h2>
        <p class="auth-description">
            忘記密碼了嗎？沒問題。請告訴我們您的電子郵件地址，我們會寄送一封密碼重設連結給您。
        </p>

        {{-- 顯示成功寄送的狀態訊息 --}}
        @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
        @endif

        {{-- 顯示錯誤訊息 --}}
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="form-group">
                <label for="email">註冊信箱</label>
                {{-- 修正：欄位名稱應為 'email' 以配合 Laravel 後端 --}}
                <input id="email" class="form-control" type="email" name="email" value="{{ old('email') }}" required autofocus>
            </div>

            <button type="submit" class="btn btn-primary">
                寄送密碼重設連結
            </button>
        </form>
        <div class="auth-link">
            <a href="{{ route('login') }}">返回登入</a>
        </div>
    </div>
</body>

</html>