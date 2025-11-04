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

    @php($items = $favoriteItems->pluck('item')->filter())

    @if($items->isEmpty())
      <p class="text-gray-500">您目前沒有任何收藏的商品。</p>
    @else
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($items as $item)
          <article class="rounded-2xl border shadow-sm overflow-hidden bg-white">
            <img src="{{ $item->cover_url ?? 'https://placehold.co/640x360?text=No+Image' }}"
                 alt="{{ $item->title }}" class="h-48 w-full object-cover">
            <div class="p-4">
              <h3 class="font-semibold line-clamp-2">{{ $item->title }}</h3>
              <div class="mt-2 font-semibold">{{ number_format($item->price) }} 元</div>

              <div class="mt-4 flex gap-2">
                <a href="{{ route('items.show', $item) }}" class="px-3 py-2 rounded-xl bg-gray-900 text-white">查看</a>

                {{-- 取消收藏 --}}
                <form method="POST" action="{{ route('favorites.destroy', $item) }}">
                  @csrf @method('DELETE')
                  <button class="px-3 py-2 rounded-xl bg-gray-100 hover:bg-gray-200">取消收藏</button>
                </form>
              </div>
            </div>
          </article>
        @endforeach
      </div>
    @endif
  </section>
</div>

    </div>
</div>
@endsection
@push('scripts')
<script src="{{ asset('js/member.js') }}"></script>
@vite('resources/js/member.js')
@endpush