{{-- resources/views/user/register.blade.php --}}
<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>註冊新帳號 - NHU 二手交易平台</title>

    {{-- 與登入頁面共用同一個 CSS 檔案 --}}
    @vite(['resources/css/style.css', 'resources/css/auth.css'])

    {{-- 暫時不需要載入 reCAPTCHA 腳本 --}}
    {{-- <script src="https://www.google.com/recaptcha/api.js" async defer></script> --}}
</head>

<body class="auth-body">

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
                {{-- 新增 @error 判斷式加上 is-invalid class --}}
                <input id="account" type="email" name="account" class="form-control @error('account') is-invalid @enderror" placeholder="範例: xxxxxxxx@nhu.edu.tw" value="{{ old('account') }}" required autocomplete="email" autofocus>
                {{-- 新增 顯示單一欄位的錯誤訊息  --}}
                @error('account')
                <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="nickname">暱稱</label>
                <input id="nickname" type="text" name="nickname" class="form-control @error('nickname') is-invalid @enderror" value="{{ old('nickname') }}" required autocomplete="nickname">
                @error('nickname')
                <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="user_phone">聯絡電話</label>
                <input id="user_phone" type="text" name="user_phone" class="form-control @error('user_phone') is-invalid @enderror" placeholder="請輸入手機號碼(09xxxxxxxx)" value="{{ old('user_phone') }}" required autocomplete="tel">
                @error('user_phone')
                <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="password">密碼</label>
                <input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="至少需要 8 位數" required autocomplete="new-password">
                @error('password')
                <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="password_confirmation">確認密碼</label>
                <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" placeholder="請再輸入一次密碼" required autocomplete="new-password">
            </div>
            <!-- 驗證碼 -->
            <div class="form-group">
                <div class="captcha-label">
                    請輸入驗證碼 <span class="required">*</span>
                    <span class="refresh-link" onclick="refreshCaptcha()" id="refresh-btn">
                        🔄 刷新
                    </span>
                </div>
                <div class="captcha-wrapper">
                    <div class="captcha-image" id="captchaText">{{ $captcha ?? '' }}</div>
                    <input
                        type="text"
                        name="captcha"
                        class="captcha-input form-control @error('captcha') is-invalid @enderror"
                        id="captchaInput"
                        required
                        maxlength="5"
                        autocomplete="off">
                </div>
                @error('captcha')
                <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="btn btn-success" id="submit-btn">
                <span class="btn-text">註冊</span>
                <span class="btn-spinner" style="display: none;">⏳</span>
            </button>
            {{-- --- 暫時停用 reCAPTCHA 元件 --- --}}
            {{-- <div class="form-group">
                <div class="g-recaptcha" data-sitekey="{{  env('RECAPTCHA_SITE_KEY') }}">
    </div>
    @error('g-recaptcha-response')
    <span class="invalid-feedback" style="display: block;">{{ $message }}</span>
    @enderror
    </div>--}}

    </form>

    <div class="auth-link">
        已經有帳號了？ <a href="{{ route('login') }}">前往登入</a>
    </div>
    </div>

    <script>

    </script>
</body>

</html>