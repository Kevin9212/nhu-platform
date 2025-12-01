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

        {{-- 收藏按鈕：登入且不是賣家本人 --}}
        @if(Auth::check() && Auth::id() !== ($item->seller->id ?? null))
          <form method="POST"
                action="{{ $isFavorited ? route('favorites.destroy', $item) : route('favorites.store', $item) }}">
            @csrf
            @if($isFavorited) @method('DELETE') @endif
            <input type="hidden" name="redirect_to" value="{{ url()->current() }}">

            <button type="submit" class="btn outline">
              {{ $isFavorited ? '取消收藏' : '加入收藏' }}
            </button>
          </form>
        @elseif(!Auth::check())
          <a href="{{ route('login') }}" class="btn outline">登入後即可收藏</a>
        @endif

        {{-- 議價表單：登入且不是賣家本人 --}}
        @if(Auth::check() && Auth::id() !== ($item->seller->id ?? null))
          <form method="POST" action="{{ route('negotiations.store', $item) }}" class="offer-form">
            @csrf
            <label for="price">出價金額</label>
            <input type="number" id="price" name="price" required min="1" placeholder="輸入您的出價">
            <button type="submit" class="btn warn">提出議價</button>
          </form>

          <a href="{{ route('conversation.start', ['user' => $item->seller->id]) }}" class="btn primary">聯絡賣家</a>
          <a
            href="{{ route('orders.create', ['idle_item_id' => $item->id, 'order_price' => (int) $item->idle_price]) }}"
            class="btn success"
          >
            成立訂單
          </a>
        @endif

        <div class="meta-row">
          <span title="{{ $item->created_at->format('Y-m-d H:i') }}">{{ $item->created_at->diffForHumans() }}</span>
          @if($item->category)
            <span class="chip">{{ $item->category->name }}</span>
          @endif
        </div>
      </aside>
    </section>

    {{-- 商品詳情 --}}
    <section class="detail-desc">
      <h3>商品詳情</h3>
      <p>{!! nl2br(e($item->idle_details)) !!}</p>
    </section>

    {{-- ============= 賣家專用：此商品的訂單小面板 ============= --}}
    @if(Auth::check() && Auth::id() === ($item->seller->id ?? null))
      <section class="seller-mini-orders">
        <div class="mini-order-header">
          <div>
            <h3>📦 此商品的訂單狀態</h3>
            <p class="text-muted small mb-0">只有賣家本人可以看到這個區塊</p>
          </div>
        </div>

        @php
          // 預防沒有關聯時出錯（還是建議在 IdleItem 建立 orders() 關聯）
          $orders = $item->orders ?? collect();
        @endphp

        {{-- 沒有任何訂單 --}}
        @if($orders->isEmpty())
          <div class="mini-order-card mini-order-empty">
            <p class="text-muted mb-0">目前尚無任何訂單。</p>
          </div>
        @else
          {{-- 每一筆訂單 --}}
          @foreach($orders as $order)
            @php
              // meetup_location 轉成陣列，假設裡面可能有 time / place key
              $meet = $order->meetup_location ?? [];
              $meetTime  = is_array($meet) ? ($meet['time']  ?? null) : null;
              $meetPlace = is_array($meet) ? ($meet['place'] ?? null) : null;

              $statusKey = $order->order_status;
              $statusLabel = [
                'pending'   => '待確認',
                'confirmed' => '已確認',
                'completed' => '已完成',
                'cancelled' => '已取消',
              ][$statusKey] ?? $statusKey;
            @endphp

            <div class="mini-order-card">
              <div class="mini-order-left">
                {{-- 買家資訊：用你的 user() 關聯 --}}
                <div class="buyer-info">
                  <img src="{{ asset($order->user->avatar ?? 'images/default-avatar.png') }}" class="buyer-avatar" alt="買家頭像">
                  <div>
                    <div class="buyer-name">{{ $order->user->nickname ?? $order->user->name }}</div>
                    <div class="buyer-email text-muted small">{{ $order->user->email }}</div>
                  </div>
                </div>

                {{-- 價格資訊 --}}
                <div class="price-info">
                  <div>原價：
                    <span class="text-muted">
                      NT$ {{ number_format($item->idle_price) }}
                    </span>
                  </div>
                  <div>訂單價格：
                    <span class="order-price">
                      NT$ {{ number_format($order->order_price) }}
                    </span>
                  </div>
                </div>

                {{-- 面交資訊 --}}
                <div class="meet-info text-muted small">
                  <div>面交時間：
                    {{ $meetTime ?? '未設定' }}
                  </div>
                  <div>面交地點：
                    {{ $meetPlace ?? '未設定' }}
                  </div>
                </div>
              </div>

              <div class="mini-order-right">
                <span class="order-status badge bg-secondary mb-2">
                  {{ $statusLabel }}
                </span>

                <a href="{{ route('seller.orders.show', $order) }}"
                   class="btn btn-sm btn-outline-primary mini-order-btn">
                  管理訂單
                </a>
              </div>
            </div>
          @endforeach

          <div class="text-end mt-2">
            <a href="{{ route('seller.orders.index') }}" class="small">
              查看所有訂單 &raquo;
            </a>
          </div>
        @endif
      </section>
    @endif
    {{-- ============= /賣家專用：此商品的訂單小面板 ============= --}}

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

@media(max-width:960px){
  .detail-card{grid-template-columns:1fr;}
}

/* ========== 賣家專用：訂單小面板樣式 ========== */
.seller-mini-orders{
  margin-top:18px;
  background:var(--card);
  border-radius:var(--radius);
  padding:18px 22px;
  box-shadow:var(--shadow1);
}

.seller-mini-orders .mini-order-header h3{
  margin:0;
  font-size:1.15rem;
  font-weight:700;
  color:var(--ink);
}

.mini-order-card{
  margin-top:12px;
  padding:14px 16px;
  border-radius:12px;
  border:1px solid var(--border);
  background:#F8F9F7;
  box-shadow:0 4px 10px rgba(0,0,0,.03);
  display:flex;
  justify-content:space-between;
  gap:14px;
}

.mini-order-empty{
  text-align:center;
  background:#F8F9F7;
}

.mini-order-left{flex:1;min-width:0;}

.buyer-info{
  display:flex;
  align-items:center;
  gap:10px;
  margin-bottom:6px;
}

.buyer-avatar{
  width:42px;
  height:42px;
  border-radius:50%;
  object-fit:cover;
}

.buyer-name{
  font-weight:600;
  color:var(--ink);
  font-size:.95rem;
}

.buyer-email{
  font-size:.8rem;
}

.price-info{
  font-size:.9rem;
  margin:4px 0 6px;
}

.order-price{
  color:var(--accent);
  font-weight:700;
}

.meet-info{
  font-size:.8rem;
  line-height:1.5;
}

.mini-order-right{
  display:flex;
  flex-direction:column;
  align-items:flex-end;
  gap:6px;
  white-space:nowrap;
}

.mini-order-btn{
  padding:6px 10px;
  font-size:.8rem;
}

/* 手機排版調整 */
@media(max-width:768px){
  .mini-order-card{
    flex-direction:column;
    align-items:flex-start;
  }
  .mini-order-right{
    align-items:flex-start;
  }
}
</style>
@endsection
