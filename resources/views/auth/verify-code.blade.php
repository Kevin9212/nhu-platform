{{-- resources/views/auth/verify-code.blade.php --}}
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>驗證註冊信箱 - NHU 二手交易平台</title>
    @vite(['resources/css/style.css', 'resources/css/auth.css'])
</head>
<body class="auth-body">
    {{-- 引入共用的頁首 --}}
    @include('partials.header')

    <div class="auth-container">
        <h2>驗證註冊信箱</h2>
        <p class="auth-description">已寄出 6 位數驗證碼到您的學校信箱，請在 10 分鐘內完成驗證。</p>

        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                <ul>
                @foreach ($errors->all() as $error)
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                        <li>{{ $error }}</li>
                @endforeach
                    @endforeach
            </ul>
                </ul>
        </div>
            </div>\
        @endif

        <form method="POST" action="{{ route('register.verify.submit') }}">
            @csrf
            <div class="form-group">
                <label for="email">學校信箱</label>
                <input type="email" class="form-control" id="email" name="email" value="{{ $email ?? old('email') }}" required readonly>
            </div>
            <div class="form-group">
                <label for="code">驗證碼</label>
                <input type="text" class="form-control" id="code" name="code" maxlength="6" inputmode="numeric" pattern="[0-9]{6}" required autofocus>
            </div>
            <button type="submit" class="btn btn-primary">送出驗證</button>
        </form>

        <form method="POST" action="{{ route('register.verify.resend') }}" style="margin-top:12px;">
            @csrf
            <input type="hidden" name="email" value="{{ $email ?? old('email') }}">
            <button type="submit" class="btn btn-link">重新寄送驗證碼</button>
        </form>

        <div class="auth-link">
            <a href="{{ route('login') }}">返回登入</a>
        </div>
    </div>
</body>
</html>