{{-- resources/views/user/register.blade.php --}}
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>註冊新帳號 - NHU 二手交易平台</title>

    {{-- 與登入頁面共用同一個 CSS 檔案 --}}
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}"> {{-- 引入共用的樣式檔 --}}
</head>
<body>

    {{-- 引入共用的頁首 --}}
    @include('partials.header')

    <div class="auth-container">
        <h2>建立新帳號</h2>

        {{-- 顯示註冊失敗的錯誤訊息 --}}
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- 註冊表單 --}}
        <form action="{{ route('register') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="account">學校信箱</label>
                {{-- 新增 old() 和 autocomplete --}}
                <input id="account" type="email" name="account" class="form-control" placeholder="範例: 11124149@nhu.edu.tw" value="{{ old('account') }}" required autocomplete="email" autofocus>
            </div>
            <div class="form-group">
                <label for="nickname">暱稱</label>
                <input id="nickname" type="text" name="nickname" class="form-control" value="{{ old('nickname') }}" required autocomplete="nickname">
            </div>
            <div class="form-group">
                <label for="user_phone">聯絡電話</label>
                <input id="user_phone" type="text" name="user_phone" class="form-control" value="{{ old('user_phone') }}" required autocomplete="tel">
            </div>
            <div class="form-group">
                <label for="password">密碼</label>
                <input id="password" type="password" name="password" class="form-control" placeholder="至少需要 8 位數" required autocomplete="new-password">
            </div>
            <div class="form-group">
                <label for="password_confirmation">確認密碼</label>
                <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" placeholder="請再輸入一次密碼" required autocomplete="new-password">
            </div>
            
            <button type="submit" class="btn btn-success">註冊</button>
        </form>

        <div class="auth-link">
            已經有帳號了？ <a href="{{ route('login') }}">前往登入</a>
        </div>
    </div>
</body>
</html>
