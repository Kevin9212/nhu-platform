@extends('layouts.app')

@section('title', '南華大學二手交易平台')

@section('content')

{{-- ===== Hero / Banner ===== --}}
<section class="hero-wrap">
  <div class="hero-bg">
    <img src="{{ asset('images/re.png') }}" alt="NHU 二手平台" class="hero-img">
    <div class="hero-overlay"></div>
  </div>

  <div class="container-xl">
    <div class="hero-center-card">
      <h1 class="hero-title">Limited time offer</h1>
      <p class="hero-subtitle">限時優惠｜快來挖寶你需要的好物</p>
      <div class="hero-cta">
        <a href="{{ route('search.index') }}" class="btn btn-cta">開始逛逛</a>
      </div>
    </div>
  </div>
</section>

{{-- ===== 分類圖片卡片 ===== --}}
<section class="quick-cats">
  <div class="container-xl">
    <div class="cats-grid">
      {{-- 電子產品 --}}
      <a href="{{ route('search.index', ['category_id' => 1]) }}" class="cat-card"
         style="--bg:url('{{ asset('images/電子產品.png') }}')">
        <div class="cat-label">
          <div class="cat-title">電子產品</div>
          <div class="cat-sub">筆電・手機・周邊設備</div>
        </div>
      </a>

      {{-- 書籍 --}}
      <a href="{{ route('search.index', ['category_id' => 7]) }}" class="cat-card"
         style="--bg:url('{{ asset('images/書籍.png') }}')">
        <div class="cat-label">
          <div class="cat-title">書籍與講義</div>
          <div class="cat-sub">課本・參考書・講義</div>
        </div>
      </a>

      {{-- 寢具 --}}
      <a href="{{ route('search.index', ['category_id' => 14]) }}" class="cat-card"
         style="--bg:url('{{ asset('images/寢具.png') }}')">
        <div class="cat-label">
          <div class="cat-title">寢具</div>
          <div class="cat-sub">床墊・枕頭・棉被</div>
        </div>
      </a>

      {{-- 漫畫 --}}
      <a href="{{ route('search.index', ['category_id' => 32]) }}" class="cat-card"
         style="--bg:url('{{ asset('images/公仔.png') }}')">
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
  :root {
    --brand: #96a49f;       /* 主色 */
    --brand-700: #82938d;
    --bg-soft: #edefea;
    --card-bg: #ffffff;
    --ink: #111827;
    --muted: #6b7280;
    --border: #e5e7eb;
  }

  /* ===== Hero ===== */
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

  .hero-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(
      to bottom,
      rgba(150,164,159,0.35),
      rgba(237,239,234,0.45)
    );
  }

  .hero-center-card {
    position: absolute;
    inset: 0;
    margin: auto;
    height: max-content;
    max-width: min(680px, 86vw);
    background: rgba(237,239,234,0.9);
    color: #243028;
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
  .btn-cta:active { transform: translateY(0); }

  /* 手機版：Hero 卡片改為正常排版，不用絕對定位 */
  @media (max-width: 576px) {
    .hero-wrap {
      padding-bottom: 1rem;
    }
    .hero-center-card {
      position: static;
      transform: none;
      margin-top: -1.2rem;
    }
  }

  /* ===== 分類圖片卡片 ===== */
  .quick-cats {
    background:#fff;
    padding-bottom: 2rem;
  }

  .cats-grid{
    margin: clamp(1rem, 3vw, 2rem) auto;
    display:grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: clamp(.75rem, 2.5vw, 1.5rem);
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

    /* 給沒設定 style 的預設背景，避免整個變黑塊 */
    --bg: url("{{ asset('images/cats/default.jpg') }}");
  }

  .cat-card::before{
    content:"";
    position:absolute;
    inset:0;
    background:
      linear-gradient(to top, rgba(0,0,0,.5), rgba(0,0,0,.1)),
      var(--bg) center/cover no-repeat;
    transition: transform .25s ease;
  }
  .cat-card:hover::before{ transform: scale(1.04); }

  .cat-label{
    position:absolute;
    left:.95rem;
    right:.95rem;
    bottom:.95rem;
    background: rgba(0,0,0,.35);
    border: 1px solid rgba(255,255,255,.25);
    backdrop-filter: blur(4px);
    border-radius:.75rem;
    padding:.6rem .75rem;
    line-height:1.15;
  }
  .cat-title{ font-weight:800; font-size:1.05rem; }
  .cat-sub{ font-size:.9rem; opacity:.9; }

  /* 手機再微調一下間距 */
  @media (max-width: 576px){
    .cats-grid{
      margin-top: .5rem;
      margin-bottom: 1.5rem;
    }
  }
</style>
@endpush
