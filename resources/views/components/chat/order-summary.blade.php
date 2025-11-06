@props([
  'data' => [],
  'message' => null,
])

@php
  $itemName   = $data['item_name']  ?? '';
  $itemPrice  = isset($data['item_price'])  ? (float) $data['item_price']  : null;
  $offerPrice = isset($data['offer_price']) ? (float) $data['offer_price'] : null;
  $status     = $data['status'] ?? null;
  $image      = $data['image'] ?? null;

  if ($image && !preg_match('/^https?:\/\//i', $image)) {
      $image = asset('storage/' . ltrim($image, '/'));
  }
@endphp

<div class="chat-card chat-card--order">
  <div class="chat-card__header">
    <span class="chat-card__icon" aria-hidden="true">🧾</span>
    <span class="chat-card__title">訂單摘要</span>
  </div>

  <div class="chat-card__body">
    @if($image)
      <div class="chat-card__media">
        <img src="{{ $image }}" alt="商品圖片" loading="lazy">
      </div>
    @endif

    <div class="chat-card__details">
      <p class="chat-card__name">{{ $itemName }}</p>

      @if(!is_null($itemPrice))
        <p class="chat-card__price text-muted">原價：NT$ {{ number_format($itemPrice) }}</p>
      @endif

      @if(!is_null($offerPrice))
        <p class="chat-card__offer">議價：NT$ {{ number_format($offerPrice) }}</p>
      @endif

      @if($status)
        <p class="chat-card__status chat-card__status--{{ $status }}">
          @switch($status)
            @case('accepted')
              ✅ 賣家已接受議價
              @break
            @case('rejected')
              ❌ 賣家已拒絕議價
              @break
            @default
              ⌛ 等待賣家回覆
          @endswitch
        </p>
      @endif
    </div>
  </div>
</div>