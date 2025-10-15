@extends('layouts.app')

@section('title', '南華大學二手交易平台')

@section('content')

<section class="banner-section">
  <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
    {{-- 輪播指示器 --}}
    <div class="carousel-indicators">
      <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active"
        aria-current="true" aria-label="Slide 1"></button>
      <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1" aria-label="Slide 2"></button>
      <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2" aria-label="Slide 3"></button>
    </div>

    {{-- 輪播圖片 --}}
    <div class="carousel-inner">
      <div class="carousel-item active">
        <img src="{{ asset('images/re.png') }}" class="d-block w-100" alt="Recycle Banner 1">
      </div>
      <div class="carousel-item">
        <img src="{{ asset('images/recycle.png') }}" class="d-block w-100" alt="Recycle Banner 2">
      </div>
      <div class="carousel-item">
        <img src="{{ asset('images/notify.png') }}" class="d-block w-100" alt="Notification Banner">
      </div>
    </div>

    {{-- 左右切換按鈕 --}}
    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
      <span class="visually-hidden">上一張</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
      <span class="carousel-control-next-icon" aria-hidden="true"></span>
      <span class="visually-hidden">下一張</span>
    </button>
  </div>
</section>

{{-- 最新上架商品區塊（與隨機推薦相同結構） --}}
<section class="section">
  <div class="section-header d-flex justify-content-between align-items-center">
    <h3>最新上架商品</h3>
  </div>

  @php
    // 讓 paginate() 或 collection 都能 chunk
    $latestChunks = ($items instanceof \Illuminate\Pagination\AbstractPaginator)
      ? $items->getCollection()->chunk(4)
      : collect($items)->chunk(4);
  @endphp

  @if($latestChunks->isEmpty())
    <div class="empty-state">
      <div class="empty-icon">📦</div>
      <h3>目前沒有任何上架中的商品。</h3>
      <p><a href="{{ route('idle-items.create') }}">成為第一個上架商品的人！</a></p>
    </div>
  @else
    <div id="latestItemsCarousel" class="carousel slide" data-bs-ride="false">
      <div class="carousel-inner">
        @foreach($latestChunks as $chunkIndex => $chunk)
          <div class="carousel-item {{ $chunkIndex === 0 ? 'active' : '' }}">
            <div class="d-flex justify-content-center gap-3">
              @foreach($chunk as $item)
                @include('partials.product-card', ['item' => $item, 'showCategory' => true])
              @endforeach
            </div>
          </div>
        @endforeach
      </div>

      {{-- 置中「查看全部」：沿用隨機區塊的 .refresh-btndiv / .refresh-btn 樣式 --}}
      <div class="refresh-btndiv">
        <a href="{{ route('idle-items.index') }}" class="refresh-btn" role="button">查看全部</a>
      </div>

      {{-- 左右切換按鈕 --}}
      <button class="carousel-control-prev" type="button" data-bs-target="#latestItemsCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
        <span class="visually-hidden">上一組</span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#latestItemsCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
        <span class="visually-hidden">下一組</span>
      </button>
    </div>
  @endif

  {{-- 若 $items 是分頁器且你仍想顯示分頁導覽，可保留這段 --}}
  @if($items instanceof \Illuminate\Pagination\AbstractPaginator && $items->hasPages())
    <div class="pagination-links">
      {{ $items->links() }}
    </div>
  @endif
</section>
{{-- 上架表單的地點選擇區 --}}
@include('partials.map-selector')


{{-- 隨機推薦商品區塊 --}}
<section class="section">
  <div class="section-header d-flex justify-content-between align-items-center">
    <h3>隨機推薦商品</h3>
    
  </div>

  <div id="randomItemsCarousel" class="carousel slide" data-bs-ride="false">
    <div class="carousel-inner">
      @foreach($randomItems->chunk(4) as $chunkIndex => $chunk)
        <div class="carousel-item {{ $chunkIndex == 0 ? 'active' : '' }}">
          <div class="d-flex justify-content-center gap-3">
            @foreach($chunk as $item)
              @include('partials.product-card', ['item' => $item])
            @endforeach
          </div>
        </div>
      @endforeach
    </div>
    <div class="refresh-btndiv">
      <button onclick="refreshRecommendations()" class="refresh-btn">換一批</button>
    </div>
    {{-- 左右按鈕 --}}
    <button class="carousel-control-prev" type="button" data-bs-target="#randomItemsCarousel" data-bs-slide="prev">
      <span class="carousel-control-prev-icon"></span>
      <span class="visually-hidden">上一組</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#randomItemsCarousel" data-bs-slide="next">
      <span class="carousel-control-next-icon"></span>
      <span class="visually-hidden">下一組</span>
    </button>
    
  </div>
</section>


@endsection

@push('styles')
<style>
.carousel-control-prev-icon{
  margin-right:10rem;
}
.carousel-control-next-icon{
  margin-left:10rem;
}
.refresh-btndiv{
  text-align: center;
}
.refresh-btn{
  
  background-color:#96a49f;   /* 紫色 (你圖裡的顏色 #7C3AED 接近 Tailwind purple-600) */
  color: #fff;
  border: none;
  padding: 12px 36px;          /* 上下 12px 左右 36px */
  margin-bottom:1rem;
  font-size: 16px;
  font-weight: 500;
  border-radius: 9999px;       /* 超大圓角 => 膠囊效果 */
  cursor: pointer;
  transition: background-color 0.2s ease, transform 0.2s ease;
  display: inline-block;
}
/* 一行橫向滑動，只作用在 idle-one-row 這塊 */
.idle-one-row { --card-w: 300px; }

.idle-one-row .products {
  display: flex !important;
  flex-wrap: nowrap !important;
  overflow-x: auto !important;
  gap: 1rem !important;
}

.idle-one-row .products > * {
  flex: 0 0 var(--card-w) !important;
  width: var(--card-w) !important;
  max-width: var(--card-w) !important;
}

/* 如果 partial 用 Tailwind grid-cols-* */
.idle-one-row .grid {
  grid-template-columns: none !important;
  grid-auto-flow: column !important;
  grid-auto-columns: var(--card-w) !important;
}

/* 如果 partial 用 Bootstrap row/col */
.idle-one-row .row {
  flex-wrap: nowrap !important;
}

.banner-section {
  margin: 1rem;
  background-color: #edefea;
}
.section {
  max-width: 100%;
  margin: 1rem;
  background-color: #edefea;
}
.carousel-inner img {
  width: 100%;
  height: 400px;
  object-fit: cover; /* 或改 contain 看你的需求 */
}
.home-img img {
  max-width: 100%;
  height: auto;
  display: block;
}
</style>
@endpush

@push('scripts')
<script>
function refreshRecommendations() {
  const container = document.getElementById('random-items-container');
  const refreshBtn = document.querySelector('.refresh-btn');

  refreshBtn.textContent = '載入中...';
  refreshBtn.disabled = true;

  fetch('{{ route("home.random-items") }}')
    .then(response => response.text())
    .then(html => {
      container.innerHTML = html;
      refreshBtn.textContent = '換一批';
      refreshBtn.disabled = false;
    })
    .catch(error => {
      console.error('Error:', error);
      refreshBtn.textContent = '換一批';
      refreshBtn.disabled = false;
    });
}
</script>
@endpush