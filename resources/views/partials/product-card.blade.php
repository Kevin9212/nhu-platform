@php
  $showSeller   = $showSeller   ?? true;   // 要不要顯示賣家列
  $showCategory = $showCategory ?? true;   // 要不要顯示類別標籤
  $showViews    = $showViews    ?? false;  // 先關閉：畫面更乾淨
  $isNew        = $item->created_at->diffInDays() < 7;
  $isSold       = $item->idle_status == 2;
  $hasDiscount  = $item->original_price && $item->original_price > $item->idle_price;
  $offPct       = $hasDiscount ? round((($item->original_price - $item->idle_price)/$item->original_price)*100) : 0;
@endphp

<article class="product-card" itemscope itemtype="https://schema.org/Product">
  <a href="{{ route('idle-items.show', $item->id) }}" class="media-link" aria-label="{{ $item->idle_name }}">
    @if($item->images->isNotEmpty())
      <img src="{{ asset('storage/' . $item->images->first()->image_url) }}"
           alt="{{ $item->idle_name }}" class="product-image"
           {{ ($lazy ?? true) ? 'loading=lazy' : '' }} itemprop="image">
    @else
      <img src="https://placehold.co/900x600/F2F2F0/9AA0A6?text=No+Image"
           alt="{{ $item->idle_name }}" class="product-image placeholder"
           {{ ($lazy ?? true) ? 'loading=lazy' : '' }}>
    @endif

    {{-- 角標 --}}
    @if($isNew && !$isSold)
      <span class="badge badge-new">新上架</span>
    @endif
    @if($isSold)
      <span class="badge badge-sold">已售出</span>
    @endif
    @if($hasDiscount && !$isSold)
      <span class="badge badge-off">-{{ $offPct }}%</span>
    @endif
  </a>

  <div class="content">
    <h3 class="title" itemprop="name">
      <a href="{{ route('idle-items.show', $item->id) }}">{{ Str::limit($item->idle_name, 60) }}</a>
    </h3>

    @if($showCategory && $item->category)
      <span class="chip">{{ $item->category->name }}</span>
    @endif

    @if($showSeller)
      <div class="seller" itemprop="seller" itemscope itemtype="https://schema.org/Person">
        <a href="{{ route('users.show', $item->seller->id) }}" itemprop="name">{{ $item->seller->nickname }}</a>
      </div>
    @endif

    <div class="spacer"></div>

    <div class="bottom">
      <div class="price-group">
        <span class="price" itemprop="price">NT$ {{ number_format($item->idle_price) }}</span>
        @if($hasDiscount)
          <span class="orig">NT$ {{ number_format($item->original_price) }}</span>
        @endif
      </div>
      <div class="meta">
        <span class="time" title="{{ $item->created_at->format('Y-m-d H:i') }}">
          {{ $item->created_at->diffForHumans() }}
        </span>
        @if($showViews && ($item->views_count ?? false))
          <span class="views">👀 {{ $item->views_count }}</span>
        @endif
      </div>
    </div>
  </div>
</article>

<style>
/* ===== NHU Morandi Card – clean, image-first ===== */
:root{
  --card: #fff;
  --border:#E8E5E0;
  --text:#2C2F33;
  --muted:#6B6F73;
  --accent:#6FA291;
  --accent-600:#5A8C7D;
  --soft:#DCE9E4;
  --soft-fg:#2F5E54;
  --warn:#D36B6B;
  --danger:#CF6161;
  --radius:16px;
  --shadow-1:0 4px 12px rgba(34,40,42,.06);
  --shadow-2:0 10px 22px rgba(34,40,42,.1);
  --clamp: 2; /* 標題行數 */
}

.product-card{
  display:flex; flex-direction:column;
  background:var(--card); border:1px solid var(--border);
  border-radius:var(--radius); box-shadow:var(--shadow-1);
  overflow:hidden; transition:transform .25s ease, box-shadow .25s ease, background .25s ease;
  font-family:'Inter','Noto Sans TC',system-ui,-apple-system,sans-serif; color:var(--text);
}
.product-card:hover{ transform:translateY(-6px); box-shadow:var(--shadow-2); }

.media-link{ position:relative; display:block; }
.product-image{
  width:100%; aspect-ratio:3/2; object-fit:cover; display:block;
  transition:transform .35s ease;
}
.product-card:hover .product-image{ transform:scale(1.03); }

/* 角標 */
.badge{
  position:absolute; top:10px; left:10px;
  font-size:.78rem; padding:4px 10px; border-radius:999px;
  color:#fff; font-weight:600; box-shadow:0 2px 6px rgba(0,0,0,.1);
}
.badge-new{ background:var(--accent); }
.badge-sold{ background:var(--danger); }
.badge-off{ right:10px; left:auto; background:var(--warn); }

/* 內容 */
.content{ display:flex; flex-direction:column; padding:1rem 1.1rem 1.1rem; gap:.55rem; }
.title a{
  color:var(--text); text-decoration:none; font-weight:700; font-size:1.06rem; line-height:1.35;
  display:-webkit-box; -webkit-line-clamp:var(--clamp); -webkit-box-orient:vertical; overflow:hidden;
}
.title a:hover{ color:var(--accent-600); }
.chip{
  align-self:flex-start; background:var(--soft); color:var(--soft-fg);
  padding:4px 10px; border-radius:8px; font-size:.78rem; font-weight:600;
}
.seller{ font-size:.88rem; color:var(--muted); }
.seller a{ color:var(--accent-600); text-decoration:none; }
.seller a:hover{ text-decoration:underline; }

.spacer{ flex:1; } /* 把價格固定在底部 */

.bottom{ display:flex; align-items:end; justify-content:space-between; gap:.75rem; }
.price-group{ display:flex; align-items:baseline; gap:.5rem; }
.price{ font-weight:800; font-size:1.24rem; letter-spacing:.2px; }
.orig{ text-decoration:line-through; color:#9EA3A6; font-size:.9rem; }
.meta{ display:flex; gap:.6rem; color:var(--muted); font-size:.82rem; }

/* 深色模式（可留可拿掉） */
@media (prefers-color-scheme: dark) {
  :root{
    --card:#1A1D1D; --border:#2A2E2E; --text:#ECEFF1; --muted:#AAB3B6;
    --accent:#77B3A2; --accent-600:#8BC0B1; --soft:#233231; --soft-fg:#A1D5C8;
    --warn:#E07C7C; --danger:#E07C7C; --shadow-1:0 4px 12px rgba(0,0,0,.3); --shadow-2:0 10px 22px rgba(0,0,0,.45);
  }
}
</style>
