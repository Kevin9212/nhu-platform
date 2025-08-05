{{-- resources/views/member/index.blade.php --}}
@extends('layouts.app')

@section('title', '會員中心 - NHU 二手交易平台')

@section('content')
<div class="container">
    <div class="member-container">
        {{-- 左側導覽選單 --}}
        <aside class="member-nav">
            <div class="user-profile-summary">
                <img src="{{ asset($user->avatar ?? 'https://placehold.co/100x100/EFEFEF/AAAAAA&text=頭像') }}" alt="使用者頭像" class="avatar">
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
    /* --- 主要佈局 --- */
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

    /* --- 左側導覽選單 --- */
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

    /* --- 右側內容區塊 --- */
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

    /* --- 我的刊登列表樣式 --- */
    .table-responsive {
        overflow-x: auto;
    }

    .listings-table {
        width: 100%;
        border-collapse: collapse;
        background: #fff;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.08);
        min-width: 600px;
    }

    .listings-table th,
    .listings-table td {
        padding: 1rem;
        text-align: left;
        border-bottom: 1px solid #eee;
        vertical-align: middle;
    }

    .listings-table th {
        background-color: #f8f9fa;
        font-weight: 600;
    }

    .listing-thumbnail {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 6px;
    }

    .listings-table .btn {
        padding: 0.3rem 0.7rem;
        font-size: 0.8rem;
        border-radius: 4px;
    }

    .listings-table .btn-edit {
        background-color: #ffc107;
        color: #212529;
        border: none;
    }

    .listings-table .btn-delete {
        background-color: #dc3545;
        color: white;
        border: none;
    }
</style>
@endpush

@push('scripts')
<script src="{{ asset('js/member.js') }}"></script>
@endpush