{{-- resources/views/auth/reset-password.blade.php --}}
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>重設密碼 - NHU 二手交易平台</title>
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    @include('partials.header')

    <div class="auth-container">
        <h2>設定新密碼</h2>

        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            {{-- 隱藏欄位，用來傳遞 token 和 email --}}
            <input type="hidden" name="token" value="{{ $token }}">
            
            <div class="form-group">
                <label for="email">學校信箱</label>
                {{-- 讓使用者可以看到信箱，但不能修改 --}}
                <input id="email" class="form-control" type="email" name="email" value="{{ $email ?? old('email') }}" required readonly>
            </div>

            <div class="form-group">
                <label for="password">新密碼</label>
                <input id="password" class="form-control" type="password" name="password" required autocomplete="new-password" autofocus>
            </div>

            <div class="form-group">
                <label for="password_confirmation">確認新密碼</label>
                <input id="password_confirmation" class="form-control" type="password" name="password_confirmation" required autocomplete="new-password">
            </div>

            <button type="submit" class="btn btn-primary">
                重設密碼
            </button>
        </form>
    </div>
</body>
</html>
