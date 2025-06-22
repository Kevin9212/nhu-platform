    {{-- resources/views/auth/reset-password.blade.php --}}
    <!DOCTYPE html>
    <html lang="zh-Hant">

    <head>
        <meta charset="UTF-8">
        <title>重設密碼</title>
        <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    </head>

    <body>
        <div class="auth-container">
            <h2>設定新密碼</h2>

            @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
                @endforeach
            </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                {{-- 隱藏欄位，用來傳遞 token 和 email --}}
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $email ?? old('email') }}">

                <div class="form-group">
                    <label for="password">新密碼</label>
                    <input id="password" class="form-control" type="password" name="password" required>
                </div>

                <div class="form-group">
                    <label for="password_confirmation">確認新密碼</label>
                    <input id="password_confirmation" class="form-control" type="password" name="password_confirmation" required>
                </div>

                <button type="submit" class="btn btn-primary">
                    重設密碼
                </button>
            </form>
        </div>
    </body>

    </html>