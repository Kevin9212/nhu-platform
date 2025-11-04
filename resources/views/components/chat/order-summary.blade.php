@props([
  'data' => [],           // decode 後的訂單資料陣列
  'message' => null,      // 當前訊息（可拿 sender / created_at）
])

@php
  // 安全取值
  $itemName   = $data['item_name']  ?? '';
  $itemPrice  = isset($data['item_price'])  ? (float) $data['item_price']  : null;
  $offerPrice = isset($data['offer_price']) ? (float) $data['offer_price'] : null;
  $status     = $data['status'] ?? null; // accepted / rejected / pending...
  $image      = $data['image'] ?? null;

  // 若圖片是 storage 相對路徑，可視情況轉 asset('storage/...')：
  if ($image && !preg_match('/^https?:\/\//i', $image)) {
      $image = asset('storage/' . ltrim($image, '/'));
  }

  // 狀態顯示
  $statusHtml = '';
  if ($status === 'accepted') {
    $statusHtml = '<span style="color:#007bff; font-weight:bold;">✅ 賣家已接受議價</span>';
  } elseif ($status === 'rejected') {
    $statusHtml = '<span style="color:#dc3545; font-weight:bold;">❌ 賣家已拒絕議價</span>';
  } elseif (!empty($status)) {
    $statusHtml = '<span style="color:#ff9800; font-weight:bold;">⌛ 等待賣家回覆</span>';
  }
@endphp

<li class="mb-2">
  <div style="padding:12px; border:1px solid #ddd; border-radius:8px; background:#fafafa;">
    <p class="mb-2 fw-bold" style="color:#555;">🧾 訂單摘要</p>

    <div style="display:flex; gap:12px; align-items:center;">
      @if($image)
        <img src="{{ $image }}" alt="商品圖片"
             style="width:80px; height:80px; object-fit:cover; border-radius:6px;">
      @endif

      <div style="flex:1;">
        <p class="mb-0 fw-bold">{{ $itemName }}</p>

        @if(!is_null($itemPrice))
          <p class="mb-0 text-muted" style="font-size:14px;">
            原價：NT$ {{ number_format($itemPrice) }}
          </p>
        @endif

        @if(!is_null($offerPrice))
          <p class="mb-0" style="color:#28a745; font-weight:bold;">
            議價：NT$ {{ number_format($offerPrice) }}
          </p>
        @endif

        @if($statusHtml)
          <p class="mb-0">{!! $statusHtml !!}</p>
        @endif
      </div>
    </div>

    @if($message)
      <div class="mt-1" style="font-size:12px; color:#999;">
        由 {{ $message->sender->nickname ?? $message->sender->account ?? ('用戶#'.$message->sender_id) }}
        · {{ $message->created_at->format('Y/m/d H:i') }}
      </div>
    @endif
  </div>
</li>
