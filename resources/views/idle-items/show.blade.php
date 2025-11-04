@extends('layouts.app')

@section('title', $item->idle_name . ' - NHU 二手交易平台')

@section('content')
<div class="detail-hero">
  <div class="detail-shell">
    <header class="detail-header">
      <h1 class="brand-title">NHU 2nd</h1>
      <p class="brand-sub">Your Campus Helper</p>
    </header>

    <section class="detail-card">
      <div class="detail-media">
        @if($item->images->isNotEmpty())
          <img src="{{ asset('storage/' . $item->images->first()->image_url) }}"
               alt="{{ $item->idle_name }}" class="hero-img" loading="lazy">
        @else
          <img src="https://placehold.co/1200x800/F3F4F6/91A3AD?text=No+Image"
               alt="{{ $item->idle_name }}" class="hero-img" loading="lazy">
        @endif
      </div>

      <aside class="detail-side">
        <h2 class="item-title">{{ $item->idle_name }}</h2>
        <div class="price-now">NT$ {{ number_format($item->idle_price) }}</div>

        <div class="seller-card">
          <img src="{{ $item->seller->avatar ? asset($item->seller->avatar) : 'https://placehold.co/80x80/EFEFEF/AAAAAA?text=頭像' }}"
               alt="{{ $item->seller->nickname }}">
          <div>
            <div class="seller-name">{{ $item->seller->nickname }}</div>
            <div class="seller-note">賣家</div>
          </div>
        </div>

        @php
          $isFavorited = auth()->check()
              ? auth()->user()->favorites()->where('idle_item_id', $item->id)->exists()
              : false;
        @endphp

        @if(Auth::check() && Auth::id() !== ($item->seller->id ?? null))
          <form method="POST"
                action="{{ $isFavorited ? route('favorites.destroy', $item) : route('favorites.store', $item) }}">
            @csrf
            @if($isFavorited) @method('DELETE') @endif
            <button type="submit" class="btn outline">
              {{ $isFavorited ? '取消收藏' : '加入收藏' }}
            </button>
          </form>
        @elseif(!Auth::check())
          <a href="{{ route('login') }}" class="btn outline">登入後即可收藏</a>
        @endif

        @if(Auth::check() && Auth::id() !== ($item->seller->id ?? null))
          <form method="POST" action="{{ route('negotiations.store', $item) }}" class="offer-form">
            @csrf
            <label for="price">出價金額</label>
            <input type="number" id="price" name="price" required min="1" placeholder="輸入您的出價">
            <button type="submit" class="btn warn">提出議價</button>
          </form>
        @endif

        <a href="{{ route('conversation.start', ['user' => $item->seller->id]) }}" class="btn primary">聯絡賣家</a>
        <a href="{{ route('orders.create') }}" class="btn success">成立訂單</a>

        <div class="meta-row">
          <span title="{{ $item->created_at->format('Y-m-d H:i') }}">{{ $item->created_at->diffForHumans() }}</span>
          @if($item->category)
            <span class="chip">{{ $item->category->name }}</span>
          @endif
        </div>
      </aside>
    </section>

    <section class="detail-desc">
      <h3>商品詳情</h3>
      <p>{!! nl2br(e($item->idle_details)) !!}</p>
    </section>
  </div>
</div>

<style>
:root{
  --mint:#AFC3B9;         /* NHU 莫蘭迪綠 */
  --mint-dark:#7F9C8F;    /* 深一階 hover */
  --shell:#EDEFEA;        /* 外層背景 */
  --card:#FFFFFF;         /* 白底卡片 */
  --ink:#22302C;          /* 主文字深灰 */
  --muted:#6B716C;        /* 次文字灰 */
  --border:#DADDD9;       /* 邊框灰 */
  --accent:#698E7E;       /* 主要按鈕色 */
  --accent-hover:#587C6D; /* 按鈕 hover */
  --warn:#DA946B;         /* 橘色議價 */
  --success:#7EA798;      /* 成立訂單綠 */
  --radius:14px;
  --shadow1:0 6px 18px rgba(0,0,0,.06);
  --shadow2:0 14px 28px rgba(0,0,0,.1);
  --ring:0 0 0 4px rgba(95,149,135,.15);
}

.detail-hero{background:var(--shell);padding:28px 0 48px;}
.detail-shell{max-width:1060px;margin:0 auto;padding:0 16px;}

.detail-header{text-align:center;background:var(--mint);color:#fff;border-radius:12px;padding:24px 0;box-shadow:var(--shadow1);margin-bottom:1.5rem;}
.brand-title{font-weight:800;font-size:1.8rem;margin:0;}
.brand-sub{margin-top:4px;opacity:.9;font-size:1rem;}

.detail-card{display:grid;grid-template-columns:1.3fr .9fr;gap:24px;background:var(--card);border-radius:var(--radius);box-shadow:var(--shadow1);padding:20px;}
.detail-media{background:#fff;border-radius:10px;overflow:hidden;border:1px solid var(--border);}
.hero-img{width:100%;aspect-ratio:16/10;object-fit:cover;display:block;transition:transform .4s ease;}
.detail-media:hover .hero-img{transform:scale(1.02);}

.detail-side{display:flex;flex-direction:column;gap:14px;background:#fff;border:1px solid var(--border);border-radius:10px;padding:18px;}
.item-title{margin:0;font-size:1.4rem;line-height:1.35;color:var(--ink);font-weight:800;}
.price-now{font-size:1.6rem;font-weight:900;color:var(--ink);}

.seller-card{display:flex;gap:12px;align-items:center;background:#F7F8F6;border:1px solid #E5E7E4;padding:12px;border-radius:10px;}
.seller-card img{width:48px;height:48px;border-radius:50%;object-fit:cover;}
.seller-name{font-weight:700;color:var(--ink);}
.seller-note{color:var(--muted);font-size:.88rem;margin-top:2px;}

.offer-form{display:flex;flex-direction:column;gap:8px;}
.offer-form input{border:1px solid var(--border);border-radius:10px;padding:10px 12px;outline:none;transition:.15s;}
.offer-form input:focus{box-shadow:var(--ring);border-color:var(--accent);}

.btn{display:inline-flex;align-items:center;justify-content:center;width:100%;padding:10px 14px;border-radius:12px;font-weight:700;text-decoration:none;border:1px solid transparent;transition:transform .04s ease,background .18s ease,color .18s ease;}
.btn:active{transform:translateY(1px);}
.btn.primary{background:var(--accent);color:#fff;}
.btn.primary:hover{background:var(--accent-hover);}
.btn.success{background:var(--success);color:#fff;margin-top:8px;}
.btn.success:hover{background:#6c9585;}
.btn.outline{background:#fff;color:var(--accent);border:1px solid var(--accent);}
.btn.outline:hover{background:#F4F6F5;}
.btn.warn{background:var(--warn);color:#fff;font-weight:800;}
.btn.warn:hover{background:#c98357;}

.meta-row{display:flex;justify-content:space-between;align-items:center;color:var(--muted);font-size:.9rem;margin-top:8px;}
.chip{background:#E3E8E5;color:var(--accent);padding:4px 10px;border-radius:999px;font-weight:700;font-size:.78rem;}
.detail-desc{background:var(--card);margin-top:16px;padding:18px 22px;border-radius:var(--radius);box-shadow:var(--shadow1);}
.detail-desc h3{margin:0 0 8px;color:var(--ink);}
.detail-desc p{color:var(--ink);line-height:1.85;}
@media(max-width:960px){.detail-card{grid-template-columns:1fr;}}
</style>
@endsection
