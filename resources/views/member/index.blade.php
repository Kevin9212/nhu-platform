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

      <ul class="nav-list" role="tablist" aria-label="會員中心選單">
        <li><a href="#profile"   data-tab="profile"   class="tab-link active" role="tab" aria-selected="true"  aria-controls="tab-profile">個人資料</a></li>
        <li><a href="#listings"  data-tab="listings"  class="tab-link"        role="tab" aria-selected="false" aria-controls="tab-listings">我的刊登</a></li>
        <li><a href="#favorites" data-tab="favorites" class="tab-link"        role="tab" aria-selected="false" aria-controls="tab-favorites">我的收藏</a></li>
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

        <hr style="margin: 2.5rem 0;">

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
            <p class="text-gray-500">您目前沒有任何收藏的商品。</p>
          @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
              @foreach($items as $it)
                @php
                  $cover = optional($it->images->first())->image_url;
                  $coverUrl = $cover ? asset('storage/' . ltrim($cover, '/')) : 'https://placehold.co/640x360?text=No+Image';
                @endphp

                <article class="rounded-2xl border shadow-sm overflow-hidden bg-white">
                  <img src="{{ $coverUrl }}" alt="{{ $it->idle_name }}" class="h-48 w-full object-cover">
                  <div class="p-4">
                    <h3 class="font-semibold line-clamp-2">{{ $it->idle_name }}</h3>
                    <div class="mt-2 font-semibold">{{ number_format($it->idle_price) }} 元</div>

                    <div class="mt-4 flex gap-2">
                      <a href="{{ route('idle-items.show', $it) }}" class="px-3 py-2 rounded-xl bg-gray-900 text-white">查看</a>
                      <form method="POST" action="{{ route('favorites.destroy', $it) }}">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="redirect_to" value="{{ route('member.index') }}#favorites">
                        <button class="px-3 py-2 rounded-xl bg-gray-100 hover:bg-gray-200">取消收藏</button>
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
    </main>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const links = document.querySelectorAll('.tab-link');
  const panes = {
    profile:   document.getElementById('tab-profile'),
    listings:  document.getElementById('tab-listings'),
    favorites: document.getElementById('tab-favorites'),
  };

  function show(tabId, pushHash = true) {
    Object.values(panes).forEach(p => p && (p.hidden = true));
    panes[tabId] && (panes[tabId].hidden = false);

    links.forEach(a => {
      const on = a.dataset.tab === tabId;
      a.classList.toggle('active', on);
      a.setAttribute('aria-selected', on ? 'true' : 'false');
    });

    localStorage.setItem('activeMemberTab', tabId);
    if (pushHash && location.hash !== '#' + tabId) {
      history.replaceState(null, '', '#' + tabId);
    }
  }

  // 點擊
  links.forEach(a => a.addEventListener('click', e => {
    e.preventDefault();
    show(a.dataset.tab);
  }));

  // 直接輸入 #hash 時
  window.addEventListener('hashchange', () => {
    const name = (location.hash || '').slice(1);
    if (name && panes[name]) show(name, false);
  });

  // 初始化
  const byHash = (location.hash || '').slice(1);
  const saved  = localStorage.getItem('activeMemberTab');
  const initial = panes[byHash] ? byHash : (panes[saved] ? saved : 'profile');
  show(initial, false);
});
</script>

@endsection
