{{-- resources/views/member/index.blade.php --}}
@extends('layouts.app')

@section('title', '會員中心 - NHU 二手交易平台')
@section('page','member')
@section('content')
<div class="container">
    <div class="member-container">
        {{-- 左側導覽選單 --}}
        <aside class="member-nav">
            <div class="user-profile-summary">
                <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : 'https://placehold.co/100x100/EFEFEF/AAAAAA&text=頭像' }}" alt="使用者頭像" class="avatar">
                <p class="nickname">{{ $user->nickname }}</p>
            </div>
            <ul class="nav-list">
                <li><a href="#" data-tab="profile" class="tab-link active">個人資料</a></li>
                <li><a href="#" data-tab="listings" class="tab-link">我的刊登</a></li>
                <li><a href="#" data-tab="favorites" class="tab-link">我的收藏</a></li>
            </ul>
        </aside>

        {{-- 右側主要內容 --}}
        <main class="member-content">
            {{-- 個人資料 Tab --}}
            <div id="tab-profile" class="tab-pane active">
                <section class="section">
                    <h2>個人資料</h2>
                    @include('member.partials.profile-form')
                </section>
            </div>
            {{-- 我的刊登 Tab --}}
            <div id="tab-listings" class="tab-pane">
                <section class="section">
                    <h2>我的刊登列表</h2>
                    @include('member.partials.listings-table')
                </section>
                <hr style="margin: 2.5rem 0;">
                <section class="section">
                    <h2>新增商品</h2>
                    @include('idle-items.create-form')
                </section>
            </div>
            {{-- 我的收藏 Tab --}}
            <div id="tab-favorites" class="tab-pane">
                <section class="section">
                    <h2>我的收藏</h2>
                    @include('partials.product-grid', [
                    'items' => $favoriteItems->pluck('item')->filter(),
                    'emptyMessage' => '您目前沒有任何收藏的商品。'
                    ])
                </section>
            </div>
        </main>
    </div>
</div>
@endsection
@push('scripts')
<script src="{{ asset('js/member.js') }}"></script>
@vite('resources/js/member.js')
@endpush