{{-- resources/views/user/auth.blade.php --}}
@extends('layouts.auth')
@section('title', '會員系統 - NHU 二手交易平台')

@section('content')
<div class="auth-container">
  <h2>會員系統</h2>

  @if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  @if ($errors->any())
    <div class="alert alert-danger">
      <ul>
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  @php
    $is_register_active = $errors->has('nickname') || $errors->has('user_phone') || $errors->has('password_confirmation');
  @endphp

  <div class="auth-tabs">
    <div id="tab-login" class="auth-tab @if(!$is_register_active) active @endif" data-tab="login">登入</div>
    <div id="tab-register" class="auth-tab @if($is_register_active) active @endif" data-tab="register">註冊</div>
  </div>

  {{-- 登入表單 --}}
  <form id="form-login" class="auth-form @if(!$is_register_active) active @endif" action="{{ route('login') }}" method="POST">
    @csrf
    <div class="form-group">
      <input type="email" name="account" class="form-control" placeholder="學校信箱" value="{{ old('account') }}" required autocomplete="email">
    </div>
    <div class="form-group">
      <input type="password" name="password" class="form-control" placeholder="密碼" required autocomplete="current-password">
    </div>
    <div class="form-group remember-me">
      <input type="checkbox" name="remember" id="remember">
      <label for="remember">記住我</label>
    </div>
    <button type="submit" class="btn btn-primary">登入</button>
  </form>

  {{-- 註冊表單 --}}
  <form id="form-register" class="auth-form @if($is_register_active) active @endif" action="{{ route('register') }}" method="POST">
    @csrf
    <div class="form-group">
      <input type="email" name="account" class="form-control" placeholder="學校信箱 (@nhu.edu.tw / @ccu.edu.com.tw)" value="{{ old('account') }}" required autocomplete="email">
    </div>
    <div class="form-group">
      <input type="text" name="nickname" class="form-control" placeholder="暱稱" value="{{ old('nickname') }}" required autocomplete="nickname">
    </div>
    <div class="form-group">
      <input type="text" name="user_phone" class="form-control" placeholder="聯絡電話" value="{{ old('user_phone') }}" required autocomplete="tel">
    </div>
    <div class="form-group">
      <input type="password" name="password" class="form-control" placeholder="密碼 (至少8位數)" required autocomplete="new-password">
    </div>
    <div class="form-group">
      <input type="password" name="password_confirmation" class="form-control" placeholder="確認密碼" required autocomplete="new-password">
    </div>
    <button type="submit" class="btn btn-success">註冊</button>
  </form>
</div>
@endsection
