@extends('layouts.app')

@section('title', '南華大學二手交易平台')

@section('content')

{{-- ===== Hero / Banner：用你原本輪播其中一張 ===== --}}
<section class="hero-wrap">
  <div class="hero-bg">
    {{-- 這裡直接沿用你現有的 re.png，你也可改成 recycle.png / notify.png --}}
    <img src="{{ asset('images/re.png') }}" alt="NHU 二手平台" class="hero-img">
    <div class="hero-overlay"></div>
  </div>

  <div class="container-xl">
    <div class="hero-center-card">
      <h1 class="hero-title">Limited time offere</h1>
      <p class="hero-subtitle">限時優惠｜快來挖寶你需要的好物</p>
      <div class="hero-cta">
        <a href="{{ route('search.index') }}" class="btn btn-cta">開始逛逛</a>
      </div>
    </div>
  </div>
</section>

{{-- ===== 分類圖片卡片（4 欄），不要表情貼 ===== --}}
<section class="quick-cats">
  <div class="container-xl">
    <div class="cats-grid">
      {{-- 電子產品 --}}
      <a href="{{ route('search.index', ['category_id' => 1]) }}" class="cat-card"
         style="--bg:url('{{ asset('images/cats/electronics.jpg') }}')">
        <div class="cat-label">
          <div class="cat-title">電子產品</div>
          <div class="cat-sub">筆電・手機・周邊設備</div>
        </div>
      </a>

      {{-- 書籍 --}}
      <a href="{{ route('search.index', ['category_id' => 7]) }}" class="cat-card"
         style="--bg:url('{{ asset('images/cats/books.jpg') }}')">
        <div class="cat-label">
          <div class="cat-title">書籍與講義</div>
          <div class="cat-sub">課本・參考書・講義</div>
        </div>
      </a>

      {{-- 寢具 --}}
      <a href="{{ route('search.index', ['category_id' => 14]) }}" class="cat-card"
         style="--bg:url('{{ asset('images/cats/bedding.jpg') }}')">
        <div class="cat-label">
          <div class="cat-title">寢具</div>
          <div class="cat-sub">床墊・枕頭・棉被</div>
        </div>
      </a>

      {{-- 漫畫 --}}
      <a href="{{ route('search.index', ['category_id' => 32]) }}" class="cat-card"
         style="--bg:url('{{ asset('images/cats/manga.jpg') }}')">
        <div class="cat-label">
          <div class="cat-title">漫畫 / 動漫週邊</div>
          <div class="cat-sub">收藏・經典・週邊</div>
        </div>
      </a>
    </div>
  </div>
</section>



@endsection

@push('styles')
<style>
  /* === 延用你原本的 Morandi 配色（與你上一版一致） === */
  :root {
    --brand: #96a49f;       /* 主色 */
    --brand-700: #82938d;
    --bg-soft: #edefea;     /* 淡底色 */
    --card-bg: #ffffff;
    --ink: #111827;
    --muted: #6b7280;
    --border: #e5e7eb;
  }

  /* ===== Hero ===== */
  /* ===== Hero 區域重新配色 ===== */
.hero-wrap {
  position: relative;
  background: var(--bg-soft);
  isolation: isolate;
  overflow: hidden;
}

.hero-bg { position: relative; }

.hero-img {
  width: 100%;
  height: clamp(260px, 36vw, 520px);
  object-fit: cover;
  display: block;
  filter: saturate(.9) brightness(.95);
}

/* 改為柔和灰綠疊層（取代原本的黑灰） */
.hero-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(
    to bottom,
    rgba(150,164,159,0.35),   /* 灰綠上層 */
    rgba(237,239,234,0.45)    /* 米白綠下層 */
  );
}

/* 中央霧面卡片 */
.hero-center-card {
  position: absolute;
  inset: 0;
  margin: auto;
  height: max-content;
  max-width: min(680px, 86vw);
  background: rgba(237,239,234,0.9); /* 白帶綠霧感 */
  color: #243028; /* 深灰綠字 */
  border-radius: 1.2rem;
  padding: clamp(1rem, 3.5vw, 2.5rem);
  text-align: center;
  box-shadow: 0 15px 50px rgba(150,164,159,.25);
  backdrop-filter: blur(10px);
  transform: translateY(clamp(0px, -6vw, -40px));
  border: 1px solid rgba(150,164,159,0.25);
}

.hero-title {
  margin: 0 0 .6rem;
  font-weight: 800;
  font-size: clamp(1.4rem, 3vw, 2.25rem);
  letter-spacing: .2px;
  color: #2f3a35;
}

.hero-subtitle {
  margin: 0 0 1.2rem;
  color: #4f5c57;
  font-size: clamp(.95rem, 1.6vw, 1.05rem);
}

/* 按鈕換成品牌主色（灰綠） */
.btn-cta {
  background: var(--brand);
  color: #fff;
  font-weight: 700;
  border: none;
  border-radius: 9999px;
  padding: .7rem 1.4rem;
  box-shadow: 0 8px 18px rgba(150,164,159,.3);
  transition: transform .15s ease, filter .15s ease;
  text-decoration: none;
  display: inline-block;
}

.btn-cta:hover {
  filter: brightness(0.95);
  transform: translateY(-1px);
}

.btn-cta:active {
  transform: translateY(0);
}

/* RWD 修正 */
@media (max-width: 576px) {
  .hero-center-card {
    position: static;
    transform: none;
    margin-top: -1.2rem;
  }
}

  /* ===== 分類圖片卡片：無表情，用圖片背景 ===== */
  .quick-cats{ background:#fff; }
  .cats-grid{
    margin: clamp(.5rem, 2.5vw, 1.25rem) auto;
    display:grid;
    grid-template-columns: repeat(4, 1fr);
    gap: clamp(.5rem, 1.6vw, 1rem);
  }
  .cat-card{
    position:relative;
    display:block;
    aspect-ratio: 4/3;
    border-radius: 1rem;
    overflow:hidden;
    background: #dfe3df;
    box-shadow: 0 8px 18px rgba(0,0,0,.06);
    text-decoration:none;
    color:#fff;
    border: 1px solid var(--border);
  }
  /* 背景圖片（用 css var 傳入），加一層品牌色漸層確保可讀性 */
  .cat-card::before{
    content:"";
    position:absolute; inset:0;
    background:
      linear-gradient(to top, rgba(0,0,0,.45), rgba(0,0,0,.05)),
      var(--bg, var(--bg, url('{{ asset('images/cats/default.jpg') }}'))) center/cover no-repeat;
    transition: transform .25s ease;
  }
  .cat-card:hover::before{ transform: scale(1.04); }

  .cat-label{
    position:absolute; left:.95rem; right:.95rem; bottom:.95rem;
    background: color-mix(in srgb, var(--brand) 26%, transparent);
    border: 1px solid rgba(255,255,255,.25);
    backdrop-filter: blur(4px);
    border-radius:.75rem;
    padding:.6rem .75rem;
    line-height:1.15;
  }
  .cat-title{ font-weight:800; font-size:1.05rem; }
  .cat-sub{ font-size:.9rem; opacity:.9; }

  /* RWD */
  @media (max-width: 992px){
    .cats-grid{ grid-template-columns: repeat(2, 1fr); }
  }
  @media (max-width: 576px){
    .hero-center-card{ position:static; transform:none; margin-top:-1.2rem; }
    .cats-grid{ grid-template-columns: 1fr; }
  }
</style>
@endpush
