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

$meetupValue = function ($order, $key, $default = '') {
    return data_get($order->meetup_location, $key, $default);
};

$buyerRatings = $buyerRatings ?? collect();
@endphp

<div class="orders-panel">
  {{-- ✅ 購買的訂單：加上錨點 id="orders-buyer" --}}
  <div class="orders-section" id="orders-buyer">
    <div class="orders-section__header">
      <h3>購買的訂單</h3>
      <p class="orders-section__hint">您作為買家的訂單列表。</p>
    </div>
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
          @forelse($buyerOrders as $order)
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
              $seller = $item?->user;
              $existingRating = $buyerRatings->get($order->id);
              $isCompleted = $order->order_status === 'success';
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
              <td data-label="交易地點">{{ $meta['location'] }}</td>
              <td data-label="交易時間">{{ $meta['datetime'] }}</td>
              <td data-label="狀態">{{ $statusLabel($order->order_status) }}</td>
              <td data-label="操作" class="text-end">
                @if($order->order_status === 'pending')
                  <details class="order-edit-details">
                    <summary class="btn btn-link btn-sm p-0">修改面交資訊</summary>
                    <div class="order-edit-card">
                      <form method="POST" action="{{ route('orders.update', $order) }}" class="stack gap-2">
                        @csrf
                        @method('PATCH')
                        <div class="form-group">
                          <label class="form-label" for="buyer-address-{{ $order->id }}">交易地點</label>
                          <input id="buyer-address-{{ $order->id }}" type="text" name="meet_address" value="{{ $meetupValue($order, 'address') }}" class="form-control" required>
                        </div>
                        <div class="d-flex gap-2">
                          <div class="form-group flex-fill">
                            <label class="form-label" for="buyer-date-{{ $order->id }}">交易日期</label>
                            <input id="buyer-date-{{ $order->id }}" type="date" name="meet_date" value="{{ $meetupValue($order, 'date') }}" class="form-control" required>
                          </div>
                          <div class="form-group flex-fill">
                            <label class="form-label" for="buyer-time-{{ $order->id }}">交易時間</label>
                            <input id="buyer-time-{{ $order->id }}" type="time" name="meet_time" value="{{ $meetupValue($order, 'time') }}" class="form-control" required>
                          </div>
                        </div>
                        <input type="hidden" name="meet_lat" value="{{ $meetupValue($order, 'lat') }}">
                        <input type="hidden" name="meet_lng" value="{{ $meetupValue($order, 'lng') }}">
                        <p class="text-muted small mb-1">更新後將同步通知賣家。</p>
                        <div class="text-end">
                          <button type="submit" class="btn btn-primary btn-sm">儲存修改</button>
                        </div>
                      </form>
                    </div>
                  </details>
                  <form method="POST" action="{{ route('orders.cancel', $order) }}" onsubmit="return confirm('確定要取消這筆訂單嗎？');">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-outline-danger btn-sm">取消訂單</button>
                  </form>
                @elseif($isCompleted)
                  <div class="d-flex flex-column gap-2 align-items-end">
                    <span class="text-success">已完成</span>
                    @if(! $seller)
                      <span class="text-muted small">賣家資訊缺失，無法評價</span>
                    @elseif($existingRating)
                      <span class="text-muted small">已對 {{ $sellerName }} 留下評價</span>
                    @else
                      @php($modalId = 'rating-modal-' . $order->id)
                      <button type="button" class="btn btn-warning btn-sm text-end" data-modal-open="{{ $modalId }}">留下交易評價</button>

                      <div class="modal" id="{{ $modalId }}" role="dialog" aria-modal="true" aria-labelledby="{{ $modalId }}-title" hidden>
                        <div class="modal__backdrop" data-modal-close></div>
                        <div class="modal__dialog" role="document">
                          <div class="modal__header">
                            <div>
                              <p class="modal__eyebrow">已完成 · {{ $item?->idle_name }}</p>
                              <h3 class="modal__title" id="{{ $modalId }}-title">留下交易評價</h3>
                            </div>
                            <button type="button" class="modal__close" aria-label="關閉" data-modal-close>&times;</button>
                          </div>
                          <div class="modal__body">
                            <form method="POST" action="{{ route('ratings.store', $seller) }}" class="stack gap-3">
                              @csrf
                              <input type="hidden" name="order_id" value="{{ $order->id }}">
                              <div class="form-group">
                                <label class="form-label d-block">評分</label>
                                <div class="rating-options" role="radiogroup" aria-label="評分">
                                  @for($i = 1; $i <= 5; $i++)
                                    <label class="rating-option">
                                      <input type="radio" name="score" value="{{ $i }}" required>
                                      <span>{{ $i }} 星</span>
                                    </label>
                                  @endfor
                                </div>
                              </div>
                              <div class="form-group">
                                <label class="form-label" for="rating-comment-{{ $order->id }}">評價內容（選填）</label>
                                <textarea id="rating-comment-{{ $order->id }}" name="comment" class="form-control" rows="3" maxlength="1000" placeholder="分享這次交易的感受吧！"></textarea>
                              </div>
                              <div class="text-end d-flex gap-2 justify-content-end">
                                <button type="button" class="btn btn-light btn-sm" data-modal-close>取消</button>
                                <button type="submit" class="btn btn-primary btn-sm">送出評價</button>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                    @endif
                  </div>
                @else
                  <span class="text-muted">已取消</span>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="text-center text-muted py-4">目前沒有購買中的訂單。</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- ✅ 賣出的訂單：加上錨點 id="orders-seller" --}}
  <div class="orders-section" id="orders-seller">
    <div class="orders-section__header">
      <h3>賣出的訂單</h3>
      <p class="orders-section__hint">您作為賣家的訂單列表。</p>
    </div>
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
          @if($sellerOrders->isEmpty())
            <tr>
              <td colspan="7" class="text-center text-muted py-4">目前沒有賣出的訂單。</td>
            </tr>
          @else
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
                $isCompleted = $order->order_status === 'success';
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
                  @if($order->order_status === 'pending')
                    <div class="d-flex flex-column gap-2 align-items-end">
                      <details class="order-edit-details w-100">
                        <summary class="btn btn-link btn-sm p-0 text-end">修改面交資訊</summary>
                        <div class="order-edit-card">
                          <form method="POST" action="{{ route('orders.update', $order) }}" class="stack gap-2">
                            @csrf
                            @method('PATCH')
                            <div class="form-group">
                              <label class="form-label" for="seller-address-{{ $order->id }}">交易地點</label>
                              <input id="seller-address-{{ $order->id }}" type="text" name="meet_address" value="{{ $meetupValue($order, 'address') }}" class="form-control" required>
                            </div>
                            <div class="d-flex gap-2">
                              <div class="form-group flex-fill">
                                <label class="form-label" for="seller-date-{{ $order->id }}">交易日期</label>
                                <input id="seller-date-{{ $order->id }}" type="date" name="meet_date" value="{{ $meetupValue($order, 'date') }}" class="form-control" required>
                              </div>
                              <div class="form-group flex-fill">
                                <label class="form-label" for="seller-time-{{ $order->id }}">交易時間</label>
                                <input id="seller-time-{{ $order->id }}" type="time" name="meet_time" value="{{ $meetupValue($order, 'time') }}" class="form-control" required>
                              </div>
                            </div>
                            <input type="hidden" name="meet_lat" value="{{ $meetupValue($order, 'lat') }}">
                            <input type="hidden" name="meet_lng" value="{{ $meetupValue($order, 'lng') }}">
                            <p class="text-muted small mb-1">更新後將同步通知買家。</p>
                            <div class="text-end">
                              <button type="submit" class="btn btn-primary btn-sm">儲存修改</button>
                            </div>
                          </form>
                        </div>
                      </details>
                      <form method="POST" action="{{ route('orders.confirm', $order) }}" onsubmit="return confirm('確認這筆訂單嗎？');">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-success btn-sm">確認訂單</button>
                      </form>
                      <form method="POST" action="{{ route('orders.cancel', $order) }}" onsubmit="return confirm('確定要取消這筆訂單嗎？');">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-outline-danger btn-sm">取消訂單</button>
                      </form>
                    </div>
                  @elseif($isCompleted)
                    <span class="text-success">已完成</span>
                  @else
                    <span class="text-muted">已取消</span>
                  @endif
                </td>
              </tr>
            @endforeach
          @endif
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const openButtons = document.querySelectorAll('[data-modal-open]');
  const modals = new Map();
  let lastTrigger = null;

  function getModal(id) {
    if (!modals.has(id)) {
      const modal = document.getElementById(id);
      if (modal) modals.set(id, modal);
    }
    return modals.get(id);
  }

  function openModal(id, trigger) {
    const modal = getModal(id);
    if (!modal) return;
    lastTrigger = trigger;
    modal.hidden = false;
    document.body.classList.add('modal-open');
    const focusable = modal.querySelector('input[name="score"]');
    if (focusable) focusable.focus();
  }

  function closeModal(modal) {
    modal.hidden = true;
    document.body.classList.remove('modal-open');
    if (lastTrigger) {
      lastTrigger.focus();
      lastTrigger = null;
    }
  }

  openButtons.forEach(btn => {
    btn.addEventListener('click', () => openModal(btn.dataset.modalOpen, btn));
  });

  document.addEventListener('click', event => {
    const closeBtn = event.target.closest('[data-modal-close]');
    if (!closeBtn) return;
    const modal = closeBtn.closest('.modal');
    if (modal) closeModal(modal);
  });

  document.addEventListener('keydown', event => {
    if (event.key !== 'Escape') return;
    const visibleModal = Array.from(modals.values()).find(m => !m.hidden);
    if (visibleModal) {
      event.preventDefault();
      closeModal(visibleModal);
    }
  });
});
</script>