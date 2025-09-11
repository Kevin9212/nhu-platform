{{-- resources/views/user/register.blade.php --}}
@extends('layouts.auth')

@section('title', '註冊新帳號 - NHU 二手交易平台')

@section('content')
<div class="auth-page-wrapper">
    <div class="auth-container">
        <h2>建立新帳號</h2>

        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('register') }}" method="POST" id="registerForm">
            @csrf

            <!-- 學校信箱 -->
            <div class="form-group">
                <label for="account">學校信箱 <span class="required">*</span></label>
                <input
                    id="account"
                    type="email"
                    name="account"
                    class="form-control @error('account') is-invalid @enderror"
                    placeholder="範例: xxxxxxxx@nhu.edu.tw"
                    value="{{ old('account') }}"
                    required
                    autocomplete="email"
                    autofocus>
                @error('account')
                <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <!-- 暱稱 -->
            <div class="form-group">
                <label for="nickname">暱稱 <span class="required">*</span></label>
                <input
                    id="nickname"
                    type="text"
                    name="nickname"
                    class="form-control @error('nickname') is-invalid @enderror"
                    value="{{ old('nickname') }}"
                    required
                    autocomplete="nickname"
                    maxlength="20">
                @error('nickname')
                <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <!-- 聯絡電話 -->
            <div class="form-group">
                <label for="user_phone">聯絡電話 <span class="required">*</span></label>
                <input
                    id="user_phone"
                    type="tel"
                    name="user_phone"
                    class="form-control @error('user_phone') is-invalid @enderror"
                    placeholder="請輸入手機號碼(09xxxxxxxx)"
                    value="{{ old('user_phone') }}"
                    required
                    autocomplete="tel"
                    pattern="09[0-9]{8}">
                @error('user_phone')
                <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <!-- 密碼 -->
            <div class="form-group">
                <label for="password">密碼 <span class="required">*</span></label>
                <div class="password-input-wrapper">
                    <input
                        id="password"
                        type="password"
                        name="password"
                        class="form-control @error('password') is-invalid @enderror"
                        placeholder="至少需要 8 位數"
                        required
                        autocomplete="new-password"
                        minlength="8">
                    <button type="button" class="password-toggle" onclick="togglePassword('password')">
                        <span id="password-toggle-text">顯示</span>
                    </button>
                </div>
                <div class="password-strength" id="password-strength"></div>
                @error('password')
                <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <!-- 確認密碼 -->
            <div class="form-group">
                <label for="password_confirmation">確認密碼 <span class="required">*</span></label>
                <input
                    id="password_confirmation"
                    type="password"
                    name="password_confirmation"
                    class="form-control"
                    required
                    autocomplete="new-password">
                <div class="password-match" id="password-match"></div>
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
        </form>

        <div class="auth-link">
            已經有帳號了？ <a href="{{ route('login') }}">前往登入</a>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .captcha-label {
        font-weight: bold;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .refresh-link {
        color: #007bff;
        cursor: pointer;
        text-decoration: underline;
        font-size: 0.9em;
        transition: color 0.2s;
    }

    .refresh-link:hover {
        color: #0056b3;
    }

    .captcha-wrapper {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 15px;
    }

    .captcha-image {
        background: linear-gradient(45deg, #f8f9fa, #e9ecef);
        border: 2px solid #ddd;
        padding: 12px 18px;
        font-family: 'Courier New', monospace;
        font-size: 18px;
        font-weight: bold;
        letter-spacing: 4px;
        color: #333;
        border-radius: 6px;
        user-select: none;
        min-width: 120px;
        text-align: center;
    }

    .captcha-input {
        flex: 1;
        max-width: 150px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .required {
        color: red;
    }

    .password-input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .password-toggle {
        position: absolute;
        right: 10px;
        background: none;
        border: none;
        color: #007bff;
        cursor: pointer;
        font-size: 0.8em;
    }

    .password-strength {
        height: 4px;
        margin-top: 5px;
        border-radius: 2px;
        transition: all 0.3s;
    }

    .password-match {
        font-size: 0.85em;
        margin-top: 5px;
    }

    .match {
        color: #28a745;
    }

    .no-match {
        color: #dc3545;
    }

    .btn-spinner {
        display: inline-block;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // 優化的驗證碼刷新功能
    async function refreshCaptcha() {
        const refreshBtn = document.getElementById('refresh-btn');
        const originalText = refreshBtn.innerHTML;

        refreshBtn.innerHTML = '🔄 刷新中...';
        refreshBtn.style.pointerEvents = 'none';

        try {
            const response = await fetch('{{ route("captcha.refresh") }}', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            if (!response.ok) throw new Error('Network response was not ok');

            const data = await response.json();
            document.getElementById('captchaText').textContent = data.captcha;
            document.getElementById('captchaInput').value = '';

        } catch (error) {
            console.error('刷新驗證碼失敗:', error);
            alert('刷新驗證碼失敗，請稍後重試。');
        } finally {
            refreshBtn.innerHTML = originalText;
            refreshBtn.style.pointerEvents = 'auto';
        }
    }

    // 密碼顯示/隱藏
    function togglePassword(inputId) {
        const input = document.getElementById(inputId);
        const toggleText = document.getElementById('password-toggle-text');

        if (input.type === 'password') {
            input.type = 'text';
            toggleText.textContent = '隱藏';
        } else {
            input.type = 'password';
            toggleText.textContent = '顯示';
        }
    }

    // 密碼強度檢查
    function checkPasswordStrength(password) {
        const strengthEl = document.getElementById('password-strength');
        let strength = 0;

        if (password.length >= 8) strength++;
        if (/[A-Z]/.test(password)) strength++;
        if (/[a-z]/.test(password)) strength++;
        if (/[0-9]/.test(password)) strength++;
        if (/[^A-Za-z0-9]/.test(password)) strength++;

        const colors = ['', '#dc3545', '#fd7e14', '#ffc107', '#28a745', '#28a745'];
        const widths = ['0%', '20%', '40%', '60%', '80%', '100%'];

        strengthEl.style.backgroundColor = colors[strength] || '#e9ecef';
        strengthEl.style.width = widths[strength] || '0%';
    }

    // 密碼確認檢查
    function checkPasswordMatch() {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('password_confirmation').value;
        const matchEl = document.getElementById('password-match');

        if (confirmPassword === '') {
            matchEl.textContent = '';
            matchEl.className = 'password-match';
            return;
        }

        if (password === confirmPassword) {
            matchEl.textContent = '✓ 密碼一致';
            matchEl.className = 'password-match match';
        } else {
            matchEl.textContent = '✗ 密碼不一致';
            matchEl.className = 'password-match no-match';
        }
    }

    // 表單提交處理
    document.getElementById('registerForm').addEventListener('submit', function(e) {
        const submitBtn = document.getElementById('submit-btn');
        const btnText = submitBtn.querySelector('.btn-text');
        const btnSpinner = submitBtn.querySelector('.btn-spinner');

        btnText.style.display = 'none';
        btnSpinner.style.display = 'inline';
        submitBtn.disabled = true;
    });

    // 事件監聽器
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('password').addEventListener('input', function() {
            checkPasswordStrength(this.value);
            checkPasswordMatch();
        });

        document.getElementById('password_confirmation').addEventListener('input', checkPasswordMatch);
    });
</script>
@endpush