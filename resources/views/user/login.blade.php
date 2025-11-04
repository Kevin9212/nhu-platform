{{-- resources/views/user/login.blade.php --}}
@extends('layouts.app')

@section('title', '登入 - NHU 二手交易平台')

@section('content')
<div class="auth-page-wrapper login-wrap">
  <div class="auth-card">
    <div class="auth-head">
      <h1>登入您的帳戶</h1>
      <p class="muted">使用南華大學學校信箱登入</p>
    </div>

    {{-- 成功訊息（例如註冊完成跳轉） --}}
    @if (session('success'))
      <div class="alert alert-success soft">
        {{ session('success') }}
      </div>
    @endif

    {{-- 錯誤訊息 --}}
    @if ($errors->any())
      <div class="alert alert-danger soft">
        <div class="alert-title">登入失敗，請檢查以下項目：</div>ㄎ
        <ul class="alert-list">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form action="{{ route('login') }}" method="POST" id="loginForm" novalidate>
      @csrf

      {{-- 學校信箱 --}}
      <div class="form-group">
        <label for="account" class="form-label">學校信箱 <span class="required">*</span></label>
        <div class="input-wrap">
          <input id="account" type="email" name="account"
                 class="form-control @error('account') is-invalid @enderror"
                 value="{{ old('account') }}" required autocomplete="email" autofocus
                 placeholder="例如：s11223344@nhu.edu.tw">
          <span class="input-hint">僅接受 @nhu.edu.tw 網域</span>
        </div>
        @error('account')<span class="invalid-feedback">{{ $message }}</span>@enderror
      </div>

      {{-- 密碼 --}}
      <div class="form-group">
        <label for="password" class="form-label">密碼 <span class="required">*</span></label>
        <div class="password-input-wrapper input-wrap">
          <input id="password" type="password" name="password"
                 class="form-control @error('password') is-invalid @enderror"
                 required autocomplete="current-password" placeholder="請輸入密碼">
          <button type="button" class="password-toggle" aria-controls="password" aria-label="切換密碼顯示" onclick="togglePassword('password','pwd-toggle-text')">
            <span id="pwd-toggle-text">顯示</span>
          </button>
        </div>
        @error('password')<span class="invalid-feedback">{{ $message }}</span>@enderror
      </div>

      {{-- 記住我 / 忘記密碼 --}}
      <div class="form-group-extra">
        <label class="remember-me">
          <input type="checkbox" name="remember" id="remember">
          <span>記住我</span>
        </label>
        <div class="forgot-password">
          <a href="{{ route('password.request') }}">忘記密碼？</a>
        </div>
      </div>

      <button type="submit" class="btn btn-brand w-100" id="login-submit">
        <span class="btn-text">登入</span>
        <span class="btn-spinner" style="display:none;" aria-hidden="true">⏳</span>
      </button>
    </form>

    <div class="auth-link text-center">
      還沒有帳號嗎？ <a href="{{ route('register') }}">立即註冊</a>
    </div>
  </div>
</div>
@endsection

@push('styles')
{{--@vite('resources/css/auth.css')不與首頁樣式衝突） --}}
<style>
  .login-wrap{ background: #eeefeb; display:grid; place-items:center; padding: clamp(24px, 5vw, 48px); }
  .auth-card{ width:min(520px, 92vw); background:#fff; border-radius:20px; box-shadow:0 20px 50px rgba(0,0,0,.08); padding: clamp(20px, 3.5vw, 36px); }
  .auth-head h1{ margin:0 0 .25rem; font-weight:800; }
  .auth-head .muted{ color:var(--muted); margin:0; }

  .form-group{ margin-bottom: 18px; }
  .form-label{ font-weight:700; display:flex; align-items:center; gap:.35rem; margin-bottom:.4rem; }
  .required{ color:#e11d48; }
  .input-wrap{ position:relative; display:flex; flex-direction:column; gap:.35rem; }
  .form-control{ border:1px solid #e5e7eb; border-radius:12px; padding:.7rem .9rem; font-size:1rem; outline:none; transition:border-color .15s ease, box-shadow .15s ease; }
  .form-control:focus{ border-color: var(--brand-700); box-shadow:0 0 0 4px rgba(150,164,159,.18); }
  .is-invalid{ border-color: var(--danger); box-shadow:0 0 0 4px rgba(220,53,69,.12); }
  .invalid-feedback{ color: var(--danger); font-size:.9rem; margin-top:.35rem; }
  .input-hint{ color:var(--muted); font-size:.85rem; }

  .password-toggle{ position:absolute; right:.5rem; top:50%; transform: translateY(-50%); background:transparent; border:none; color:var(--primary); font-weight:600; cursor:pointer; padding:.25rem .4rem; border-radius:8px; }
  .password-toggle:hover{ text-decoration: underline; }

  .form-group-extra{ display:flex; align-items:center; justify-content:space-between; margin: .25rem 0 1rem; }
  .remember-me{ display:flex; align-items:center; gap:.5rem; cursor:pointer; user-select:none; }
  .forgot-password a{ color:var(--primary); text-decoration:none; }
  .forgot-password a:hover{ text-decoration:underline; }

  .btn{ display:inline-flex; align-items:center; justify-content:center; gap:.5rem; cursor:pointer; border:none; border-radius:12px; padding:.8rem 1rem; font-weight:700; transition: transform .12s ease, filter .12s ease; }
  .btn:active{ transform: translateY(1px); }
  .btn-brand{ background: linear-gradient(135deg, var(--brand), var(--brand-700)); color:#fff; box-shadow: 0 12px 28px rgba(150,164,159,.35); }
  .btn-brand:hover{ filter: brightness(.98); }

  .alert.soft{ border-radius:12px; padding:.9rem 1rem; margin-bottom:1rem; }
  .alert-success.soft{ background:#effcf3; border:1px solid #c6f6d5; color:#166534; }
  .alert-danger.soft{ background:#fff4f5; border:1px solid #ffd6db; color:#b91c1c; }
  .alert-title{ font-weight:800; margin-bottom:.35rem; }
  .alert-list{ margin:0; padding-left:1.2rem; }

  .auth-link{ margin-top:1rem; }
  .w-100{ width:100%; }

  /* 防止首頁樣式汙染到 auth 頁 */
  .login-wrap .banner-section,
  .login-wrap .section { background: transparent; }
  .login-wrap img { max-width:100%; height:auto; }
</style>
@endpush

@push('scripts')
<script>
  function togglePassword(id,labelId){
    const input=document.getElementById(id);
    const label=document.getElementById(labelId);
    const isPwd=input.type==='password';
    input.type=isPwd?'text':'password';
    if(label) label.textContent=isPwd?'隱藏':'顯示';
  }

  document.addEventListener('DOMContentLoaded', () => {
    const btn=document.getElementById('login-submit');
    document.getElementById('loginForm').addEventListener('submit', () => {
      btn.querySelector('.btn-text').style.display='none';
      btn.querySelector('.btn-spinner').style.display='inline';
      btn.disabled=true;
    });
  });
</script>
@endpush
