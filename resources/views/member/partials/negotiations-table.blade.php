{{-- resources/views/member/partials/negotiations-table.blade.php --}}

<div class="negotiation-hint">
  <p class="negotiation-hint__title">交易模式說明</p>
  <ul>
    <li>目前為「賣家決定」模式：買家議價後，由賣家選擇要接受哪一筆（非競標、非系統自動決定）。</li>
    <li>賣家在聊天室按下「同意議價」後即鎖定該買家，請雙方盡快完成訂單以避免爭議。</li>
    <li>如需改為先搶先贏或價高者得，可在商品描述中額外註明規則讓買家知悉。</li>
  </ul>
</div>

@if($groupedNegotiations->isEmpty())
  <p class="empty-tip">目前沒有任何商品正在議價。</p>
@else
  <div class="table-responsive">
    <table class="listings-table negotiations-table">
      <thead>
        <tr>
          <th>商品</th>
          <th>買家</th>
          <th>出價</th>
          <th>狀態</th>
          <th>私訊</th>
          <th>更新時間</th>
          <th>訂單管理</th>
        </tr>
      </thead>
      <tbody>
        @foreach($groupedNegotiations as $itemNegotiations)
          @php
            $item = optional($itemNegotiations->first())->item;
            $coverPath = optional($item?->images->first())->image_url;
            $coverUrl = $coverPath
              ? asset('storage/' . ltrim($coverPath, '/'))
              : 'https://placehold.co/80x80/EFEFEF/AAAAAA&text=無圖片';
            $rowspan = $itemNegotiations->count();
          @endphp
          @foreach($itemNegotiations as $negotiation)
            @php
              $conversationKey = $negotiation->buyer_id . '-' . $negotiation->idle_item_id;
              $conversationId  = optional($conversationLookup->get($conversationKey))->id;
              $statusClass = [
                'pending'  => 'badge-pending',
                'accepted' => 'badge-accepted',
                'rejected' => 'badge-rejected',
              ][$negotiation->status] ?? 'badge-pending';
            @endphp
            <tr>
              @if($loop->first)
                <td data-label="商品" rowspan="{{ $rowspan }}" class="align-top">
                  <div class="negotiation-item">
                    <img src="{{ $coverUrl }}" alt="{{ $item?->idle_name }}">
                    <div>
                      <div class="negotiation-item__name">{{ $item?->idle_name ?? '商品已移除' }}</div>
                      @if($item)
                        <div class="negotiation-item__price">原價 NT$ {{ number_format($item->idle_price) }}</div>
                      @endif
                    </div>
                  </div>
                </td>
              @endif
              <td data-label="買家">{{ optional($negotiation->buyer)->nickname ?? optional($negotiation->buyer)->account ?? '使用者已刪除' }}</td>
              <td data-label="出價">NT$ {{ number_format($negotiation->price) }}</td>
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
              <td data-label="訂單管理" class="text-end">
                <a href="{{ route('orders.create') }}" class="btn btn-primary btn-sm">送出訂單</a>
              </td>
            </tr>
          @endforeach
        @endforeach
      </tbody>
    </table>
  </div>
@endif