@php
    use Illuminate\Support\Str;
@endphp

<div class="chat-sidebar" id="chatSidebar" data-chat-sidebar>
    <button type="button"
            class="chat-sidebar__close"
            data-chat-close
            aria-label="關閉對話列表">
        <span aria-hidden="true">&times;</span>
        <span class="chat-sidebar__close-text">關閉</span>
    </button>
    <div class="chat-sidebar__header">
        <h2 class="chat-sidebar__title">我的對話</h2>
        <span class="chat-sidebar__count">{{ $conversations->count() }}</span>
    </div>

    <div class="chat-sidebar__search">
        <input type="search"
               class="form-control form-control-sm"
               placeholder="搜尋暱稱或商品"
               data-chat-search>
    </div>

    <ul class="chat-sidebar__list" data-chat-list>
        @forelse($conversations as $conv)
            @php
                $isBuyer = $conv->buyer_id === auth()->id();
                $other   = $isBuyer ? $conv->seller : $conv->buyer;
                $latest  = optional($conv->messages)->first();
                $snippet = '尚無訊息';

                if ($latest) {
                    if ($latest->msg_type === 'order_summary') {
                        $snippet = '🧾 訂單摘要';
                    } else {
                        $snippet = Str::limit($latest->content, 50);
                    }
                }

                $searchText = Str::lower(collect([
                    $other->nickname ?? null,
                    $other->account ?? null,
                    optional($conv->item)->idle_name,
                    $snippet,
                ])->filter()->implode(' '));
            @endphp
            <li class="chat-sidebar__item {{ ($activeConversation ?? null) === $conv->id ? 'is-active' : '' }}"
                data-chat-item
                data-search-text="{{ $searchText }}">
                <a href="{{ route('conversations.show', $conv->id) }}" class="chat-sidebar__link">
                    <div class="chat-sidebar__avatar">
                        <img src="{{ $other->avatar_url ?? asset('images/avatar-default.png') }}" alt="{{ $other->nickname ?? $other->account ?? '匿名' }}">
                    </div>
                    <div class="chat-sidebar__content">
                        <div class="chat-sidebar__row">
                            <span class="chat-sidebar__name">{{ $other->nickname ?? $other->account ?? '匿名' }}</span>
                            <time class="chat-sidebar__time" datetime="{{ optional($latest)->created_at?->toIso8601String() }}">
                                {{ optional($latest)->created_at?->diffForHumans() ?? '—' }}
                            </time>
                        </div>
                        @if($conv->item)
                            <div class="chat-sidebar__item-label" title="{{ $conv->item->idle_name }}">
                                {{ Str::limit($conv->item->idle_name, 40) }}
                            </div>
                        @endif
                        <div class="chat-sidebar__row">
                            <span class="chat-sidebar__snippet">{{ $snippet }}</span>
                            @if(($conv->unread_count ?? 0) > 0)
                                <span class="chat-sidebar__badge">{{ $conv->unread_count }}</span>
                            @endif
                        </div>
                    </div>
                </a>
            </li>
        @empty
            <li class="chat-sidebar__item chat-sidebar__item--empty">
                <p class="mb-0 text-muted">目前沒有對話</p>
            </li>
        @endforelse
    </ul>
</div>
<style>
    
.conversations-list a:hover {
    background: #f0f4f3;
    border-color: #c7d2cc;
    transform: translateY(-2px);
}
</style>
