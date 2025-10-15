<div class="messages" style="height:400px; overflow-y:auto; border:1px solid #eee; padding:0.5rem; margin-bottom:1rem;">
    @forelse($conversation->messages as $message)

        @php
            $decoded = null;
            if ($message->is_system) {
                $decoded = json_decode($message->content, true);
            }
        @endphp

        {{-- 如果不是 JSON 系統訊息 --}}
        @if(!$decoded || !isset($decoded['type']))
            <div style="margin-bottom:0.5rem; {{ $message->user_id == auth()->id() ? 'text-align:right;' : '' }}">
                <strong>{{ $message->sender->nickname ?? $message->sender->account }}</strong>:
                <span>{{ $message->content }}</span>
            </div>
        @else
            {{-- 如果是訂單摘要 --}}
            @if($decoded['type'] === 'order_summary')
                <div style="margin:10px 0; padding:12px; border:1px solid #ddd; border-radius:8px; background:#fafafa;">
                    <p style="margin:0 0 8px 0; font-weight:bold; color:#555;">🧾 訂單摘要</p>
                    <div style="display:flex; gap:12px; align-items:center;">
                        @if(!empty($decoded['image']))
                            <img src="{{ asset('storage/' . $decoded['image']) }}"
                                alt="商品圖片"
                                style="width:80px; height:80px; object-fit:cover; border-radius:6px;">
                        @endif
                        <div style="flex:1;">
                            <p style="margin:0; font-weight:bold;">{{ $decoded['item_name'] }}</p>
                            <p style="margin:0; color:#888; font-size:14px;">原價：NT$ {{ number_format($decoded['item_price']) }}</p>
                            <p style="margin:0; color:#28a745; font-weight:bold;">議價：NT$ {{ number_format($decoded['offer_price']) }}</p>

                            {{-- 議價狀態 --}}
                            @if(!empty($decoded['status']))
                                @if($decoded['status'] === 'accepted')
                                    <p style="margin:0; color:#007bff; font-weight:bold;">✅ 賣家已接受議價</p>
                                @elseif($decoded['status'] === 'rejected')
                                    <p style="margin:0; color:#dc3545; font-weight:bold;">❌ 賣家已拒絕議價</p>
                                @else
                                    <p style="margin:0; color:#ff9800; font-weight:bold;">⌛ 等待賣家回覆</p>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        @endif

    @empty
        <p style="text-align:center; color:#999;">尚無訊息</p>
    @endforelse
</div>
