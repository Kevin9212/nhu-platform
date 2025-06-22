{{-- resources/views/user/register.blade.php --}}
<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>註冊新帳號 - NHU 二手交易平台</title>

    {{-- 與登入頁面共用同一個 CSS 檔案 --}}
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>

<body>
    <div class="auth-container">
        <h2>建立新帳號</h2>

        {{-- 顯示註冊失敗的錯誤訊息 --}}
        @if ($errors->any())
        <div class="alert alert-danger">
            @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
            @endforeach
        </div>
        @endif

        {{-- 註冊表單 --}}
        <form action="{{ route('register') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="account">學校信箱</label>
                <input id="account" type="email" name="account" class="form-control" placeholder="範例:11124149@nhu.edu.tw" required>
            </div>
            <div class="form-group">
                <label for="password">密碼</label>
                <input id="password" type="password" name="password" class="form-control" placeholder="至少需要 6 位數" required>
            </div>
            <div class="form-group">
                <label for="password_confirmation">確認密碼</label>
                <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" placeholder="請再輸入一次密碼" required>
            </div>
            <div class="form-group">
                <label for="nickname">暱稱</label>
                <input id="nickname" type="text" name="nickname" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="user_phone">聯絡電話</label>
                <input id="user_phone" type="text" name="user_phone" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success">註冊</button>
        </form>

        <div class="auth-link">
            已經有帳號了？ <a href="{{ route('login') }}">前往登入</a>
        </div>
    </div>
</body>

</html>