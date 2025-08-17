{{-- resources/views/member/partials/conversations-list.blade.php --}}
<div class="conversation-list">
    @forelse($conversations as $conversation)
    @php
    // 判斷在這場對話中，誰是「對方」
    $otherUser = $conversation->buyer_id === Auth::id() ? $conversation->seller : $conversation->buyer;
    $lastMessage = $conversation->messages->first();
    @endphp
    <a href="{{ route('conversation.start', $otherUser->id) }}" class="conversation-item">
        <img src="{{ $otherUser->avatar ? asset('storage/' . $otherUser->avatar) : 'https://placehold.co/100x100/EFEFEF/AAAAAA&text=頭像' }}" alt="avatar" class="avatar">
        <div class="conversation-details">
            <div class="conversation-header">
                <span class="nickname">{{ $otherUser->nickname }}</span>
                <span class="time">{{ $lastMessage ? $lastMessage->created_at->diffForHumans() : $conversation->created_at->diffForHumans() }}</span>
            </div>
            <p class="last-message">
                @if($lastMessage)
                {{-- 如果最新訊息是自己傳的，就加上 "你：" 的前綴 --}}
                @if($lastMessage->sender_id === Auth::id())
                <span class="message-prefix">你：</span>
                @endif
                {{ Str::limit($lastMessage->content, 30) }}
                @else
                開啟對話...
                @endif
            </p>
        </div>
    </a>
    @empty
    <p>您目前沒有任何對話。</p>
    @endforelse
</div>