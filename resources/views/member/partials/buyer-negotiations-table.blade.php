{{-- resources/views/member/partials/buyer-negotiations-table.blade.php --}}

<div class="negotiation-hint">
  <p class="negotiation-hint__title">作為買家</p>
  <ul>
    <li>可查看自己對不同賣家的議價記錄與處理狀態。</li>
    <li>若狀態為「已接受」，可透過私訊與賣家協調面交並設定交易地點。</li>
    <li>若賣家已接受其他出價，系統會將此筆議價標示為「已拒絕」並在私訊中說明原因，避免資訊落差。</li>
  </ul>
</div>

@if($buyerNegotiations->isEmpty())
  <p class="empty-tip">目前沒有送出的議價。</p>
@else
  <div class="table-responsive">
    <table class="listings-table negotiations-table">
      <thead>
        <tr>
          <th>商品</th>
          <th>賣家</th>
          <th>我的出價</th>
          <th>狀態</th>
          <th>私訊</th>
          <th>更新時間</th>
        </tr>
      </thead>
      <tbody>
        @foreach($buyerNegotiations as $negotiation)
          @php
            $item = $negotiation->item;
            $coverPath = optional($item?->images->first())->image_url;
            $coverUrl = $coverPath
              ? asset('storage/' . ltrim($coverPath, '/'))
              : 'https://placehold.co/80x80/EFEFEF/AAAAAA&text=無圖片';
            $conversationKey = $negotiation->seller_id . '-' . $negotiation->idle_item_id;
            $conversationId  = optional($buyerConversationLookup->get($conversationKey))->id;
            $statusClass = [
              'pending'  => 'badge-pending',
              'accepted' => 'badge-accepted',
              'rejected' => 'badge-rejected',
            ][$negotiation->status] ?? 'badge-pending';
            $matchedOrder = ($buyerNegotiationOrders ?? collect())
              ->first(fn ($order) => $order->idle_item_id === $negotiation->idle_item_id
                && optional($order->item)->user_id === $negotiation->seller_id);

            $meetup = $matchedOrder?->meetup_location ?? $item?->meetup_location ?? [];
            $meetupPlace = is_array($meetup)
              ? ($meetup['address'] ?? $meetup['place'] ?? null)
              : null;
            $meetupDate = is_array($meetup) ? ($meetup['date'] ?? null) : null;
            $meetupTime = is_array($meetup) ? ($meetup['time'] ?? null) : null;
          @endphp
          <tr>
            <td data-label="商品">
              <div class="negotiation-item">
                <img src="{{ $coverUrl }}" alt="{{ $item?->idle_name }}">
                <div>
                  <div class="negotiation-item__name">{{ $item?->idle_name ?? '商品已移除' }}</div>
                  @if($item)
                    <div class="negotiation-item__price">原價 NT$ {{ number_format($item->idle_price) }}</div>
                    <div class="negotiation-item__location">
                      面交地點：{{ $meetupPlace ?? '未設定' }}
                    </div>
                    <div class="negotiation-item__location">
                      面交時間：{{ ($meetupDate && $meetupTime) ? ($meetupDate . ' ' . $meetupTime) : '未設定' }}
                    </div>
                  @endif
                </div>
              </div>
            </td>
            <td data-label="賣家">{{ optional($negotiation->seller)->nickname ?? optional($negotiation->seller)->account ?? '賣家已刪除' }}</td>
            <td data-label="我的出價">NT$ {{ number_format($negotiation->price) }}</td>
            <td data-label="狀態">
              <span class="negotiation-badge {{ $statusClass }}">
                @switch($negotiation->status)
                  @case('accepted') 已接受 @break
                  @case('rejected') 已拒絕 @break
                  @default 待賣家決定
                @endswitch
              </span>
            </td>
            <td data-label="私訊">
              @if($conversationId)
                <a href="{{ route('conversations.show', $conversationId) }}" class="btn btn-dark btn-sm">查看對話</a>
              @else
                <span class="text-muted">尚未開啟</span>
              @endif
            </td>
            <td data-label="更新時間">{{ $negotiation->updated_at->format('Y-m-d H:i') }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
@endif