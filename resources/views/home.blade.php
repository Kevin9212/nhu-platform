@extends('layouts.app')

@section('title', '南華大學二手交易平台')

@section('content')

{{-- ===== Hero / Banner ===== --}}
<section class="banner-section">
  <div class="container-xl py-3">
    <div id="homeHero" class="carousel slide" data-bs-ride="carousel" aria-label="首頁輪播">
      {{-- 指示器 --}}
      <div class="carousel-indicators">
        <button type="button" data-bs-target="#homeHero" data-bs-slide-to="0" class="active" aria-current="true" aria-label="第一張"></button>
        <button type="button" data-bs-target="#homeHero" data-bs-slide-to="1" aria-label="第二張"></button>
        <button type="button" data-bs-target="#homeHero" data-bs-slide-to="2" aria-label="第三張"></button>
      </div>

      {{-- 圖片 --}}
      <div class="carousel-inner hero-inner rounded-4 shadow-sm overflow-hidden">
        <div class="carousel-item active">
          <img src="{{ asset('images/re.png') }}" class="d-block w-100 hero-img" alt="Recycle Banner 1">
        </div>
        <div class="carousel-item">
          <img src="{{ asset('images/recycle.png') }}" class="d-block w-100 hero-img" alt="Recycle Banner 2">
        </div>
        <div class="carousel-item">
          <img src="{{ asset('images/notify.png') }}" class="d-block w-100 hero-img" alt="Notification Banner">
        </div>
      </div>

      {{-- 左右切換 --}}
      <button class="carousel-control-prev" type="button" data-bs-target="#homeHero" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">上一張</span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#homeHero" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">下一張</span>
      </button>
    </div>
  </div>
</section>

{{-- ===== 最新上架 ===== --}}
<section class="section">
  <div class="container-xl py-4">
    <div class="section-header d-flex justify-content-between align-items-end mb-3">
      <div>
        <h3 class="section-title">最新上架商品</h3>
        <p class="section-subtitle">即時更新，別錯過剛上架的好物</p>
      </div>
      <a class="btn btn-pill" href="{{ route('idle-items.index') }}">查看全部</a>
    </div>

    @php
      $latestChunks = ($items instanceof \Illuminate\Pagination\AbstractPaginator)
        ? $items->getCollection()->chunk(4)
        : collect($items)->chunk(4);
    @endphp

    @if($latestChunks->isEmpty())
      <div class="empty-state">
        <div class="empty-icon">📦</div>
        <h4>目前沒有任何上架中的商品</h4>
        <p><a href="{{ route('idle-items.create') }}" class="link-create">成為第一個上架商品的人！</a></p>
      </div>
    @else
      <div id="latestItemsCarousel" class="carousel slide multi-carousel" data-bs-interval="false">
        <div class="carousel-inner">
          @foreach($latestChunks as $chunkIndex => $chunk)
            <div class="carousel-item {{ $chunkIndex === 0 ? 'active' : '' }}">
              <div class="product-row">
                @foreach($chunk as $item)
                  @include('partials.product-card', ['item' => $item, 'showCategory' => true])
                @endforeach
              </div>
            </div>
          @endforeach
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#latestItemsCarousel" data-bs-slide="prev">
          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
          <span class="visually-hidden">上一組</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#latestItemsCarousel" data-bs-slide="next">
          <span class="carousel-control-next-icon" aria-hidden="true"></span>
          <span class="visually-hidden">下一組</span>
        </button>
      </div>

      @if($items instanceof \Illuminate\Pagination\AbstractPaginator && $items->hasPages())
        <div class="pagination-links mt-3">
          {{ $items->links() }}
        </div>
      @endif
    @endif
  </div>
</section>

{{-- ===== 隨機推薦 ===== --}}
<section class="section">
  <div class="container-xl py-4">
    <div class="section-header d-flex justify-content-between align-items-end mb-3">
      <div>
        <h3 class="section-title">隨機推薦商品</h3>
        <p class="section-subtitle">為你推薦一批也許會喜歡的清單</p>
      </div>
      <button onclick="refreshRecommendations()" class="btn btn-pill" id="refreshBtn">換一批</button>
    </div>

    {{-- 用容器包起來，AJAX 更新時整塊替換 --}}
    <div id="random-items-container">
      <div id="randomItemsCarousel" class="carousel slide multi-carousel" data-bs-interval="false">
        <div class="carousel-inner">
          @foreach($randomItems->chunk(4) as $chunkIndex => $chunk)
            <div class="carousel-item {{ $chunkIndex == 0 ? 'active' : '' }}">
              <div class="product-row">
                @foreach($chunk as $item)
                  @include('partials.product-card', ['item' => $item])
                @endforeach
              </div>
            </div>
          @endforeach
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#randomItemsCarousel" data-bs-slide="prev">
          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
          <span class="visually-hidden">上一組</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#randomItemsCarousel" data-bs-slide="next">
          <span class="carousel-control-next-icon" aria-hidden="true"></span>
          <span class="visually-hidden">下一組</span>
        </button>
      </div>
    </div>
  </div>
</section>

@endsection

@push('styles')
<style>
  :root {
    --brand: #96a49f;       /* Morandi 綠灰 */
    --brand-700: #82938d;
    --bg-soft: #edefea;     /* 背景淡米綠 */
    --card-bg: #ffffff;
    --text-weak: #6b7280;   /* gray-500 */
  }

  /* ===== 共用區塊 ===== */
  .banner-section { background: var(--bg-soft); }
  .section { background: var(--bg-soft); }
  .section-title { margin: 0; font-weight: 700; }
  .section-subtitle { margin: .25rem 0 0; color: var(--text-weak); font-size: .95rem; }

  .btn.btn-pill {
    background: var(--brand);
    color: #fff;
    border-radius: 9999px;
    padding: .6rem 1.25rem;
    font-weight: 600;
    transition: transform .15s ease, filter .15s ease;
    border: none;
  }
  .btn.btn-pill:hover { filter: brightness(0.95); transform: translateY(-1px); }
  .btn.btn-pill:active { transform: translateY(0); }

  /* ===== Hero 高度（隨視窗自適應） ===== */
  .hero-inner { height: clamp(260px, 34vw, 460px); }
  .hero-img { width: 100%; height: 100%; object-fit: cover; display: block; }

  /* ===== Multi-item carousel ===== */
  .multi-carousel .product-row {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 1rem;
  }
  @media (max-width: 1200px) {
    .multi-carousel .product-row { grid-template-columns: repeat(3, minmax(0, 1fr)); }
  }
  @media (max-width: 992px) {
    .multi-carousel .product-row { grid-template-columns: repeat(2, minmax(0, 1fr)); }
  }
  @media (max-width: 576px) {
    .multi-carousel .product-row { grid-template-columns: 1fr; }
  }

  /* 讓箭頭稍微外擴，避免遮擋卡片 */
  .carousel-control-prev, .carousel-control-next { width: 3.5rem; }
  @media (min-width: 768px) {
    #latestItemsCarousel .carousel-control-prev { transform: translateX(-.5rem); }
    #latestItemsCarousel .carousel-control-next { transform: translateX(.5rem); }
    #randomItemsCarousel .carousel-control-prev { transform: translateX(-.5rem); }
    #randomItemsCarousel .carousel-control-next { transform: translateX(.5rem); }
  }

  /* 指示器更精緻的外觀 */
  .carousel-indicators [data-bs-target] {
    width: 10px; height: 10px; border-radius: 50%;
    background-color: rgba(0,0,0,.25);
  }
  .carousel-indicators .active { background-color: var(--brand); }

  /* 空狀態 */
  .empty-state { text-align: center; padding: 3rem 1rem; background: var(--card-bg); border-radius: 1rem; }
  .empty-icon { font-size: 2rem; margin-bottom: .5rem; }
  .link-create { color: var(--brand); font-weight: 600; text-decoration: none; }
  .link-create:hover { text-decoration: underline; }

  /* 可能存在的 .product-card 調美（不破壞 partial 結構） */
  .product-card { background: var(--card-bg); border-radius: 1rem; box-shadow: 0 8px 18px rgba(0,0,0,.06); overflow: hidden; }
  .product-card .product-image { aspect-ratio: 4/3; object-fit: cover; }

  /* 讓分頁導覽置中 */
  .pagination-links { display: flex; justify-content: center; }

  /* 偏好減少動態時，停用自動輪播（守護 UX） */
  @media (prefers-reduced-motion: reduce) {
    #homeHero, #latestItemsCarousel, #randomItemsCarousel { animation: none; }
  }
</style>
@endpush

@push('scripts')
<script>
  function refreshRecommendations() {
    const container = document.getElementById('random-items-container');
    const refreshBtn = document.getElementById('refreshBtn');

    if (!container || !refreshBtn) return;

    refreshBtn.disabled = true;
    const originText = refreshBtn.textContent;
    refreshBtn.textContent = '載入中...';

    fetch('{{ route("home.random-items") }}', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(res => res.text())
      .then(html => {
        container.innerHTML = html; // 後端請回傳完整 #random-items-container 內部的 HTML
      })
      .catch(err => console.error(err))
      .finally(() => {
        refreshBtn.textContent = originText;
        refreshBtn.disabled = false;
      });
  }
</script>
@endpush
