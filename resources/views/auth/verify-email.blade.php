{{-- resources/views/auth/verify-email.blade.php --}}
@extends('layouts.app')

@section('title', '驗證 Email')

@section('content')
<div class="container" style="max-width: 600px; margin: 50px auto;">
    <div class="card shadow-sm p-4">
        <h2 class="mb-3">📧 驗證您的學生信箱</h2>

        <p>
            感謝您註冊 <strong>NHU 二手交易平台</strong>！<br>
            我們已經寄出一封驗證信到您的信箱：
            <strong>{{ auth()->user()->email }}</strong>
        </p>

        <p>
            請前往收件匣並點擊驗證連結，才能啟用您的帳號。<br>
            如果沒收到，請檢查垃圾郵件匣，或點擊下面的按鈕重新寄送。
        </p>

        {{-- 成功訊息 --}}
        @if (session('message'))
            <div class="alert alert-success mt-3">
                {{ session('message') }}
            </div>
        @endif

        {{-- 重新寄送驗證信 --}}
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn btn-primary mt-3">
                🔄 重新寄送驗證信
            </button>
        </form>

        {{-- 登出 --}}
        <form method="POST" action="{{ route('logout') }}" style="margin-top: 15px;">
            @csrf
            <button type="submit" class="btn btn-link text-danger">登出</button>
        </form>
    </div>
</div>
@endsection


{-- 流程説明：
    註冊後 → Laravel 自動導去 /email/verify → 顯示這個頁面
             點「重新寄送」 → Laravel 幫你寄一次
             點郵件裡的連結 → email_verified_at 更新 
--}