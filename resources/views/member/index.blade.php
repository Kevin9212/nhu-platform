@extends('layouts.app')

@section('title', '會員中心 - NHU 二手交易平台')
@section('page', 'member')

@push('styles')
  @vite('resources/css/member.css')
@endpush

@php
  $hasAcceptedNegotiation = $groupedNegotiations->flatten()->contains('status', 'accepted')
    || $buyerNegotiations->contains('status', 'accepted');
@endphp

@section('content')
<div class="container">
  <div class="member-container">
    {{-- 左側導覽選單 --}}
    <aside class="member-nav" aria-label="會員中心">
      <div class="user-profile-summary">
        <img
          src="{{ $user->avatar ? asset('storage/' . $user->avatar) : 'https://placehold.co/100x100/EFEFEF/AAAAAA&text=頭像' }}"
          alt="使用者頭像" class="avatar">
        <p class="nickname">{{ $user->nickname }}</p>
      </div>

      <ul class="nav-list" role="tablist" aria-orientation="vertical" aria-label="會員中心選單">
        <li>
          <a href="#profile" data-tab="profile" class="tab-link active"
             role="tab" aria-selected="true" aria-controls="tab-profile" tabindex="0">個人資料</a>
        </li>
        <li>
          <a href="#listings" data-tab="listings" class="tab-link"
             role="tab" aria-selected="false" aria-controls="tab-listings" tabindex="-1">我的刊登</a>
        </li>
        <li>
          <a href="#favorites" data-tab="favorites" class="tab-link"
             role="tab" aria-selected="false" aria-controls="tab-favorites" tabindex="-1">我的收藏</a>
        </li>
        <li>
          <a href="#orders" data-tab="orders" class="tab-link"
             role="tab" aria-selected="false" aria-controls="tab-orders" tabindex="-1">訂單管理</a>
        </li>
        <li>
          <a href="#negotiations" data-tab="negotiations" class="tab-link"
             role="tab" aria-selected="false" aria-controls="tab-negotiations" tabindex="-1">議價總覽</a>
        </li>
      </ul>
    </aside>

    {{-- 右側主要內容 --}}
    <main class="member-content">
      {{-- 個人資料（預設顯示） --}}
      <div id="tab-profile" class="m-pane" role="tabpanel" aria-labelledby="profile">
        <section class="section">
          <h2>個人資料</h2>
          @include('member.partials.profile-form')
        </section>
      </div>

      {{-- 我的刊登（初始隱藏） --}}
      <div id="tab-listings" class="m-pane" role="tabpanel" aria-labelledby="listings" hidden>
        <section class="section">
          <h2>我的刊登列表</h2>
          @include('member.partials.listings-table')
        </section>

        <hr class="section-divider" aria-hidden="true">

        <section class="section">
          <h2>新增商品</h2>
          @include('idle-items.create-form')
        </section>
      </div>

      {{-- 我的收藏（初始隱藏） --}}
      <div id="tab-favorites" class="m-pane" role="tabpanel" aria-labelledby="favorites" hidden>
        <section class="section">
          <h2>我的收藏</h2>

          @php
            $items = ($favoriteItems ?? collect())->pluck('item')->filter();
          @endphp

          @if($items->isEmpty())
            <p class="empty-tip">您目前沒有任何收藏的商品。</p>
          @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
              @foreach($items as $it)
                @php
                  $cover = optional($it->images->first())->image_url;
                  $coverUrl = $cover ? asset('storage/' . ltrim($cover, '/')) : 'https://placehold.co/640x360?text=No+Image';
                @endphp

                <article class="favorite-card">
                  <img src="{{ $coverUrl }}" alt="{{ $it->idle_name }}" class="favorite-cover">
                  <div class="favorite-body">
                    <h3 class="favorite-title line-clamp-2">{{ $it->idle_name }}</h3>
                    <div class="favorite-price">{{ number_format($it->idle_price) }} 元</div>

                    <div class="favorite-actions">
                      <a href="{{ route('idle-items.show', $it) }}" class="btn btn-dark">查看</a>
                      <form method="POST" action="{{ route('favorites.destroy', $it) }}">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="redirect_to" value="{{ route('member.index') }}#favorites">
                        <button class="btn btn-light">取消收藏</button>
                      </form>
                    </div>
                  </div>
                </article>
              @endforeach
            </div>

            @if(method_exists($favoriteItems, 'links'))
              <div class="mt-6">
                {{ $favoriteItems->links() }}
              </div>
            @endif
          @endif
        </section>
      </div>

      {{-- 訂單管理（初始隱藏） --}}
      <div id="tab-orders" class="m-pane" role="tabpanel" aria-labelledby="orders" hidden>
        <section class="section">
          <h2>訂單管理</h2>
          @include('member.partials.orders-panel')
        </section>
      </div>

      {{-- 議價總覽（初始隱藏） --}}
      <div id="tab-negotiations" class="m-pane" role="tabpanel" aria-labelledby="negotiations" hidden>
        <section class="section">
          <h2>議價整合</h2>
          <div class="stack gap-lg">
            @include('member.partials.negotiations-table')
            @include('member.partials.buyer-negotiations-table')
          </div>
        </section>
      </div>
    </main>
  </div>
</div>

@push('scripts')
  <script>
    window.orderAccessGuard = {
      hasAcceptedNegotiation: @json($hasAcceptedNegotiation),
      redirectTab: 'negotiations',
      message: '此議價尚未由賣家接受，將為您返回議價總覽。',
    };
  </script>
@endpush