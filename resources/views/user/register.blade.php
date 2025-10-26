{{-- resources/views/user/register.blade.php （美化版） --}}
@extends('layouts.app')

@section('title', '註冊新帳號 - NHU 二手交易平台')

@section('content')
<div class="auth-page-wrapper register-wrap">
  <div class="auth-card">
    <div class="auth-head">
      <h1>建立新帳號</h1>
      <p class="muted">使用南華大學學校信箱完成註冊</p>
    </div>

    @if ($errors->any())
      <div class="alert alert-danger soft">
        <div class="alert-title">表單有一些需要修正的欄位：</div>
        <ul class="alert-list">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form action="{{ route('register.submit') }}" method="POST" id="registerForm" novalidate>
      @csrf

      {{-- 學校信箱 --}}
      <div class="form-group">
        <label for="account" class="form-label">學校信箱 <span class="required">*</span></label>
        <div class="input-wrap">
          <input id="account" type="email" name="account"
            class="form-control @error('account') is-invalid @enderror"
            placeholder="例如：s11223344@nhu.edu.tw" value="{{ old('account') }}" required autocomplete="email" autofocus>
          <span class="input-hint">僅接受 @nhu.edu.tw 網域</span>
        </div>
        @error('account')<span class="invalid-feedback">{{ $message }}</span>@enderror
      </div>

      {{-- 暱稱 --}}
      <div class="form-group">
        <label for="nickname" class="form-label">暱稱 <span class="required">*</span></label>
        <div class="input-wrap">
          <input id="nickname" type="text" name="nickname"
            class="form-control @error('nickname') is-invalid @enderror"
            value="{{ old('nickname') }}" required autocomplete="nickname" maxlength="20">
          <span class="input-hint">20 字以內，之後仍可於會員中心修改</span>
        </div>
        @error('nickname')<span class="invalid-feedback">{{ $message }}</span>@enderror
      </div>

      {{-- 聯絡電話 --}}
      <div class="form-group">
        <label for="user_phone" class="form-label">聯絡電話 <span class="required">*</span></label>
        <div class="input-wrap">
          <input id="user_phone" type="tel" name="user_phone"
            class="form-control @error('user_phone') is-invalid @enderror"
            placeholder="請輸入手機號碼（09xxxxxxxx）" value="{{ old('user_phone') }}" required
            autocomplete="tel" pattern="09[0-9]{8}">
          <span class="input-hint">僅用於交易聯絡，不會公開顯示</span>
        </div>
        @error('user_phone')<span class="invalid-feedback">{{ $message }}</span>@enderror
      </div>

      {{-- 密碼 --}}
      <div class="form-group">
        <label for="password" class="form-label">密碼 <span class="required">*</span></label>
        <div class="password-input-wrapper input-wrap">
          <input id="password" type="password" name="password"
            class="form-control @error('password') is-invalid @enderror"
            placeholder="至少 8 碼，建議包含大小寫與數字" required autocomplete="new-password" minlength="8">
          <button type="button" class="password-toggle" aria-controls="password" aria-label="切換密碼顯示" onclick="togglePassword('password','password-toggle-text')">
            <span id="password-toggle-text">顯示</span>
          </button>
          <div class="strength-bar" aria-hidden="true">
            <div id="password-strength" class="bar"></div>
          </div>
        </div>
        @error('password')<span class="invalid-feedback">{{ $message }}</span>@enderror
        <ul class="pw-rules">
          <li>至少 8 碼</li>
          <li>建議含大小寫字母與數字</li>
          <li>可加入符號提升強度</li>
        </ul>
      </div>

      {{-- 確認密碼 --}}
      <div class="form-group">
        <label for="password_confirmation" class="form-label">確認密碼 <span class="required">*</span></label>
        <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" required autocomplete="new-password">
        <div class="password-match" id="password-match" aria-live="polite"></div>
      </div>

      {{-- 驗證碼 --}}
      <div class="form-group">
        <div class="captcha-label form-label">請輸入驗證碼 <span class="required">*</span>
          <button type="button" class="link refresh-link" onclick="refreshCaptcha()" id="refresh-btn">🔄 重新產生</button>
        </div>
        <div class="captcha-group">
          <div class="captcha-image" id="captchaText">{{ $captcha ?? '' }}</div>
          <input type="text" name="captcha" class="form-control captcha-input @error('captcha') is-invalid @enderror" id="captchaInput" required maxlength="5" autocomplete="off">
        </div>
        @error('captcha')<span class="invalid-feedback">{{ $message }}</span>@enderror
      </div>

      <button type="submit" class="btn btn-brand w-100" id="submit-btn">
        <span class="btn-text">註冊</span>
        <span class="btn-spinner" style="display:none;" aria-hidden="true">⏳</span>
      </button>
    </form>

    <div class="auth-link text-center">
      已經有帳號了？ <a href="{{ route('login') }}">前往登入</a>
    </div>
  </div>
</div>
@endsection

@push('styles')
<style>
  :root{
    --brand:#96a49f; --brand-700:#82938d; --bg-soft:#edefea; --card:#fff; --ink:#111827; --muted:#6b7280; --ring:#d1d5db;
    --danger:#dc3545; --warn:#ffc107; --ok:#28a745; --primary:#007bff;
  }
  .register-wrap{ background: var(--bg-soft); display:grid; place-items:center; padding: clamp(24px, 5vw, 48px); }
  .auth-card{ width:min(640px, 92vw); background:var(--card); border-radius: 20px; box-shadow: 0 20px 50px rgba(0,0,0,.08); padding: clamp(20px,3.5vw,36px); }
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

  .password-input-wrapper{ }
  .password-toggle{ position:absolute; right:.5rem; top:50%; transform: translateY(-50%); background:transparent; border:none; color:var(--primary); font-weight:600; cursor:pointer; padding:.25rem .4rem; border-radius:8px; }
  .password-toggle:hover{ text-decoration: underline; }

  .strength-bar{ height:6px; background:#eef2f7; border-radius:9999px; margin-top:.35rem; overflow:hidden; }
  .strength-bar .bar{ height:100%; width:0%; background: var(--danger); transition: width .25s ease, background-color .25s ease; }

  .password-match{ min-height:1.1rem; font-size:.9rem; margin-top:.35rem; }
  .password-match.match{ color: var(--ok); }
  .password-match.no-match{ color: var(--danger); }

  .captcha-label{ display:flex; align-items:center; gap:.5rem; font-weight:700; }
  .captcha-group{ display:flex; align-items:center; gap:.6rem; }
  .captcha-image{ background: linear-gradient(45deg, #f8f9fa, #e9ecef); border: 2px dashed #d1d5db; padding: .6rem 1rem; font-family: 'Courier New', monospace; font-size: 18px; font-weight: 800; letter-spacing: 4px; color: #333; border-radius: 10px; min-width: 140px; text-align:center; user-select:none; }
  .captcha-input{ max-width: 160px; }
  .link.refresh-link{ color: var(--primary); background:transparent; border:none; padding:0; cursor:pointer; font-weight:700; }
  .link.refresh-link:hover{ text-decoration: underline; }

  .btn{ display:inline-flex; align-items:center; justify-content:center; gap:.5rem; cursor:pointer; border:none; border-radius:12px; padding:.8rem 1rem; font-weight:700; transition: transform .12s ease, filter .12s ease; }
  .btn:active{ transform: translateY(1px); }
  .btn-brand{ background: linear-gradient(135deg, var(--brand), var(--brand-700)); color:#fff; box-shadow: 0 12px 28px rgba(150,164,159,.35); }
  .btn-brand:hover{ filter: brightness(.98); }

  .alert.soft{ background:#fff4f5; border:1px solid #ffd6db; color:#b91c1c; border-radius:12px; padding:.9rem 1rem; margin-bottom:1rem; }
  .alert-title{ font-weight:800; margin-bottom:.35rem; }
  .alert-list{ margin:0; padding-left:1.2rem; }

  .auth-link{ margin-top: 1rem; }

  /* 可及性 */
  .register-wrap :is(a, button, input){ outline: none; }
  .register-wrap :is(a, button, input):focus-visible{ outline:3px solid var(--ring); outline-offset: 2px; border-radius: 10px; }
</style>
@endpush

@push('scripts')
<script>
  // 驗證碼刷新
  async function refreshCaptcha(){
    const refreshBtn = document.getElementById('refresh-btn');
    const original = refreshBtn.textContent;
    refreshBtn.textContent = '🔄 重新產生中…';
    refreshBtn.disabled = true;
    try {
      const res = await fetch('{{ route("captcha.refresh") }}', {
        headers: { 'X-Requested-With':'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
      });
      if(!res.ok) throw new Error('Captcha refresh failed');
      const data = await res.json();
      document.getElementById('captchaText').textContent = data.captcha;
      document.getElementById('captchaInput').value = '';
    } catch(err){
      console.error(err);
      alert('刷新驗證碼失敗，請稍後再試');
    } finally {
      refreshBtn.textContent = original;
      refreshBtn.disabled = false;
    }
  }

  // 顯示/隱藏密碼
  function togglePassword(id, labelId){
    const input = document.getElementById(id);
    const label = document.getElementById(labelId);
    const isPwd = input.type === 'password';
    input.type = isPwd ? 'text' : 'password';
    if(label) label.textContent = isPwd ? '隱藏' : '顯示';
  }

  // 強度條
  function updateStrength(pw){
    const bar = document.getElementById('password-strength');
    if(!bar) return;
    let s = 0;
    if(pw.length >= 8) s++;
    if(/[A-Z]/.test(pw)) s++;
    if(/[a-z]/.test(pw)) s++;
    if(/[0-9]/.test(pw)) s++;
    if(/[^A-Za-z0-9]/.test(pw)) s++;
    const widths = ['0%','20%','40%','60%','80%','100%'];
    const colors = ['#e5e7eb','#dc3545','#fd7e14','#ffc107','#34d399','#22c55e'];
    bar.style.width = widths[s];
    bar.style.backgroundColor = colors[s];
  }

  // 密碼一致性
  function checkMatch(){
    const pw = document.getElementById('password').value;
    const cf = document.getElementById('password_confirmation').value;
    const el = document.getElementById('password-match');
    if(!cf){ el.textContent=''; el.className='password-match'; return; }
    if(pw === cf){ el.textContent='✓ 密碼一致'; el.className='password-match match'; }
    else { el.textContent='✗ 密碼不一致'; el.className='password-match no-match'; }
  }

  // 送出時按鈕狀態
  document.addEventListener('DOMContentLoaded', () => {
    const pw = document.getElementById('password');
    const cf = document.getElementById('password_confirmation');
    pw.addEventListener('input', () => { updateStrength(pw.value); checkMatch(); });
    cf.addEventListener('input', checkMatch);

    document.getElementById('registerForm').addEventListener('submit', function(){
      const submitBtn = document.getElementById('submit-btn');
      const text = submitBtn.querySelector('.btn-text');
      const spn = submitBtn.querySelector('.btn-spinner');
      text.style.display='none'; spn.style.display='inline'; submitBtn.disabled = true;
    });
  });
</script>
@endpush
