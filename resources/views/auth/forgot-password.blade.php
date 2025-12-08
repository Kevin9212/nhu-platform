{{-- resources/views/auth/forgot-password.blade.php --}}
<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>忘記密碼 - NHU 二手交易平台</title>
    @vite(['resources/css/style.css', 'resources/css/auth.css'])
</head>

<body class="auth-body forgot-page">

    {{-- 引入共用的頁首 --}}
    @include('partials.header')

     <div class="auth-container forgot-layout">
        <div class="auth-hero">
            <span class="pill">Reset Password</span>
            <h2>忘記密碼</h2>
            <p class="auth-description">
                忘記密碼了嗎？沒問題。請告訴我們您的電子郵件地址，我們會寄送一封密碼重設連結給您。
            </p>
            <ul class="step-list">
                <li><span class="dot"></span>輸入註冊信箱</li>
                <li><span class="dot"></span>收信並點擊重設連結</li>
                <li><span class="dot"></span>設定全新的登入密碼</li>
            </ul>
        </div>
        <div class="auth-card">
            <div class="card-header">
                <div class="icon-circle">📮</div>
                <div>
                    <div class="card-title">驗證您的信箱</div>
                    <div class="card-subtitle">稍後將寄出安全的重設連結</div>
                </div>
            </div>

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
             <form method="POST" action="{{ route('password.email') }}" class="stacked-form">
                @csrf
                <div class="form-group">
                    <label for="account">註冊信箱</label>
                    <input id="account" class="form-control" type="email" name="account" value="{{ old('account') }}" placeholder="example@mail.com" required autofocus>
                </div>

                <button type="submit" class="btn btn-primary">
                    寄送密碼重設連結
                </button>
            </form>

            <div class="tip-box">
                <div class="tip-title">小提醒</div>
                <p>如果收不到郵件，請檢查垃圾信件夾或將「no-reply@nhu.com」加入安全名單。</p>
            </div>

            <div class="auth-link">
                <a href="{{ route('login') }}">返回登入</a>
            </div>
        </div>
    </div>
</body>

</html>