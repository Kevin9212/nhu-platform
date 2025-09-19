{{-- resources/views/member/index.blade.php --}}
@extends('layouts.app')

@section('title', '會員中心 - NHU 二手交易平台')

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

@push('styles')
<style>
    
    .member-container {
        display: flex;
        gap: 2rem;
        align-items: flex-start;
    }

    .member-nav {
        flex-basis: 220px;
        flex-shrink: 0;
        position: sticky;
        top: 100px;
    }

    .member-content {
        flex-grow: 1;
        min-width: 0;
    }

    .user-profile-summary {
        text-align: center;
        margin-bottom: 1.5rem;
    }

    .user-profile-summary .avatar {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        object-fit: cover;
        margin-bottom: 0.75rem;
    }

    .nav-list {
        list-style: none;
        padding: 0.75rem;
        margin: 0;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.08);
    }

    .nav-list li a {
        display: block;
        padding: 0.85rem 1rem;
        color: #333;
        border-radius: 6px;
        text-decoration: none;
        cursor: pointer;
        transition: background-color 0.2s, color 0.2s;
        font-weight: 500;
    }

    .nav-list li a.active,
    .nav-list li a:hover {
        background-color: #007bff;
        color: #fff;
    }

    .tab-pane {
        display: none;
    }

    .tab-pane.active {
        display: block;
        animation: fadeIn 0.5s ease-in-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* --- 新增：對話列表樣式 --- */
    .conversation-list {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .conversation-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        border-radius: 8px;
        transition: background-color 0.2s ease;
        border-bottom: 1px solid #f0f0f0;
    }

    .conversation-item:hover {
        background-color: #f8f9fa;
    }

    .conversation-item .avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        object-fit: cover;
    }

    .conversation-details {
        flex-grow: 1;
    }

    .conversation-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .conversation-header .nickname {
        font-weight: bold;
    }

    .conversation-header .time {
        font-size: 0.8rem;
        color: #6c757d;
    }

    .last-message {
        margin: 0.25rem 0 0;
        color: #6c757d;
        font-size: 0.9rem;
    }

    .last-message .message-prefix {
        color: #333;
        font-weight: 500;
    }
</style>
@endpush

@push('scripts')
<script src="{{ asset('js/member.js') }}"></script>
@vite('resources/js/member.js')
@endpush