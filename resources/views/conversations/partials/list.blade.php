<div class="conversations-list" style="width:250px; border:1px solid #ddd; border-radius:6px; padding:1rem; background:#fff; overflow-y:auto; height:500px;">
    <h4>我的對話</h4>
    <ul style="list-style:none; padding:0; margin:0;">
        @forelse($conversations as $conv)
        @php
        $other = $conv->buyer_id === auth()->id() ? $conv->seller : $conv->buyer;
        @endphp
        <li style="margin-bottom:0.8rem;">
            <a href="{{ route('conversations.show', $conv->id) }}" style="text-decoration:none; color:#333;">
                <strong>{{ $other->nickname ?? $other->account }}</strong><br>
                <small style="color:#666;">最後更新：{{ $conv->updated_at->diffForHumans() }}</small>
            </a>
        </li>
        @empty
        <li style="color:#999;">目前沒有對話</li>
        @endforelse
    </ul>
</div>