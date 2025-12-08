{{-- resources/views/member/partials/orders-panel.blade.php --}}

@php
$orderMeta = function ($order) {
    // 優先顯示訂單上紀錄的面交資訊，若沒有則退回商品預設的面交資訊
    $locationData = $order->meetup_location ?: $order->item?->meetup_location ?: [];

    $location = data_get($locationData, 'address')
        ?? data_get($locationData, 'name')
        ?? data_get($locationData, 'place')
        ?? '未填寫';

    $date = data_get($locationData, 'date');
    $time = data_get($locationData, 'time');
    $datetime = trim(($date ? $date : '') . ' ' . ($time ? $time : ''));

    return [
        'location' => $location,
        'datetime' => $datetime !== '' ? $datetime : '未填寫',
    ];
};

$statusLabel = function ($status) {
    return match ($status) {
        'pending'   => '待確認',
        'success'   => '已完成',
        'cancelled' => '已取消',
        'failed'    => '失敗',
        default     => '未知',
    };
};
@endphp

<div class="orders-panel">
  {{-- ✅ 購買的訂單：加上錨點 id="orders-buyer" --}}
  <div class="orders-section" id="orders-buyer">
    <div class="orders-section__header">
      <h3>購買的訂單</h3>
      <p class="orders-section__hint">您作為買家的訂單列表。</p>
    </div>

    @if($buyerOrders->isEmpty())
      <p class="empty-tip">目前沒有購買中的訂單。</p>
    @else
      <div class="table-responsive">
        <table class="listings-table orders-table">
          <thead>
            <tr>
              <th>商品</th>
              <th>賣家</th>
              <th>價格</th>
              <th>交易地點</th>
              <th>交易時間</th>
              <th>狀態</th>
              <th class="text-end">操作</th>
            </tr>
          </thead>
          <tbody>
            @foreach($buyerOrders as $order)
              @php
                $item = $order->item;
                $cover = optional($item?->images->first())->image_url;
                $coverUrl = $cover
                  ? asset('storage/' . ltrim($cover, '/'))
                  : 'https://placehold.co/80x80/EFEFEF/AAAAAA&text=無圖片';
                $meta = $orderMeta($order);
                $sellerName = optional($item?->user)->nickname
                  ?? optional($item?->user)->account
                  ?? '使用者已刪除';
              @endphp
              <tr>
                <td data-label="商品">
                  <div class="order-item">
                    <img src="{{ $coverUrl }}" alt="{{ $item?->idle_name }}">
                    <div>
                      <div class="order-item__name">{{ $item?->idle_name ?? '商品已移除' }}</div>
                      <div class="order-item__meta">訂單編號：{{ $order->order_number }}</div>
                    </div>
                  </div>
                </td>
                  <td data-label="賣家">{{ $sellerName }}</td>
                <td data-label="價格">NT$ {{ number_format($order->order_price ?? 0, 0) }}</td>
              <td data-label="價格">NT$ {{ number_format($order->order_price ?? 0, 0) }}</td>
                <td data-label="交易地點">{{ $meta['location'] }}</td>
              <td data-label="交易地點">{{ $meta['location'] }}</td>
                <td data-label="交易時間">{{ $meta['datetime'] }}</td>
              <td data-label="交易時間">{{ $meta['datetime'] }}</td>
              </tr>
              <td data-label="狀態">{{ $statusLabel($order->order_status) }}</td>
              <td data-label="操作" class="text-end">
                @if($order->order_status !== 'cancelled')
                  <form method="POST" action="{{ route('orders.cancel', $order) }}" onsubmit="return confirm('確定要取消這筆訂單嗎？');">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-outline-danger btn-sm">取消訂單</button>
                  </form>
                @else
                  <span class="text-muted">已取消</span>
                @endif
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>

  {{-- ✅ 賣出的訂單：加上錨點 id="orders-seller" --}}
  <div class="orders-section" id="orders-seller">
    <div class="orders-section__header">
      <h3>賣出的訂單</h3>
      <p class="orders-section__hint">您作為賣家的訂單列表。</p>
    </div>

    @if($sellerOrders->isEmpty())
      <p class="empty-tip">目前沒有賣出的訂單。</p>
    @else
      <div class="table-responsive">
        <table class="listings-table orders-table">
          <thead>
            <tr>
              <th>商品</th>
              <th>買家</th>
              <th>價格</th>
              <th>交易地點</th>
              <th>交易時間</th>
              <th>狀態</th>
              <th class="text-end">操作</th>
            </tr>
          </thead>
          <tbody>
            @foreach($sellerOrders as $order)
              @php
                $item = $order->item;
                $cover = optional($item?->images->first())->image_url;
                $coverUrl = $cover
                  ? asset('storage/' . ltrim($cover, '/'))
                  : 'https://placehold.co/80x80/EFEFEF/AAAAAA&text=無圖片';
                $meta = $orderMeta($order);
                $buyerName = optional($order->user)->nickname
                  ?? optional($order->user)->account
                  ?? '使用者已刪除';
              @endphp
              <tr>
                <td data-label="商品">
                  <div class="order-item">
                    <img src="{{ $coverUrl }}" alt="{{ $item?->idle_name }}">
                    <div>
                      <div class="order-item__name">{{ $item?->idle_name ?? '商品已移除' }}</div>
                      <div class="order-item__meta">訂單編號：{{ $order->order_number }}</div>
                    </div>
                  </div>
                </td>
                <td data-label="買家">{{ $buyerName }}</td>
                <td data-label="價格">NT$ {{ number_format($order->order_price ?? 0, 0) }}</td>
                <td data-label="交易地點">{{ $meta['location'] }}</td>
                <td data-label="交易時間">{{ $meta['datetime'] }}</td>
                <td data-label="狀態">{{ $statusLabel($order->order_status) }}</td>
              <td data-label="操作" class="text-end">
                @if($order->order_status !== 'cancelled')
                  <form method="POST" action="{{ route('orders.cancel', $order) }}" onsubmit="return confirm('確定要取消這筆訂單嗎？');">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-outline-danger btn-sm">取消訂單</button>
                  </form>
                @else
                  <span class="text-muted">已取消</span>
                @endif
              </td>
            </tr>
              
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>
</div>
