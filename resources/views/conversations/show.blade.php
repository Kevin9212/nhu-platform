{{-- resources/views/conversations/show.blade.php --}}
@extends('layouts.app')

@section('title', '對話 #'.$conversation->id)

@section('content')
<div class="chat-layout" data-enable-echo>
    {{-- 對話清單 --}}
    @include('conversations.partials.list', [
        'conversations' => $conversations,
        'activeConversation' => $conversation->id,
    ])

    <div class="chat-thread">
        {{-- 標題列 --}}
        <header class="chat-thread__header">
            <div class="chat-thread__peer">
                <img class="chat-thread__peer-avatar"
                     src="{{ $otherUser->avatar_url }}"
                     alt="{{ $otherUser->nickname ?? $otherUser->account ?? '匿名' }}">
                <div>
                    <h1 class="chat-thread__peer-name">{{ $otherUser->nickname ?? $otherUser->account ?? '匿名' }}</h1>
                    @if($conversation->item)
                        <p class="chat-thread__peer-item" title="{{ $conversation->item->idle_name }}">
                            針對商品：{{ $conversation->item->idle_name }}
                        </p>
                    @endif
                </div>
            </div>
            <div class="chat-thread__meta">
                <span class="chat-thread__meta-id">對話編號 #{{ $conversation->id }}</span>
                <span class="chat-thread__meta-role {{ $role === 'buyer' ? 'is-buyer' : 'is-seller' }}">
                    我是{{ $role === 'buyer' ? '買家' : '賣家' }}
                </span>
            </div>
        </header>

        {{-- 訊息列表 --}}
        <div id="messageScroller" class="chat-thread__body">
            <ol id="messageList" class="chat-thread__list">
                @php
                    $lastDate = null;
                @endphp

                @forelse($messages as $message)
                    @php
                        $decoded = $message->msg_type === 'order_summary' ? json_decode($message->content, true) : null;
                        $self    = $message->sender_id == auth()->id();
                        $dateKey = $message->created_at->format('Y-m-d');
                    @endphp

                    @if($lastDate !== $dateKey)
                        <li class="chat-thread__separator">
                            <span>{{ $message->created_at->format('Y/m/d') }}</span>
                        </li>
                    @endif

                    <li class="chat-message {{ $self ? 'chat-message--mine' : 'chat-message--theirs' }}" data-message-id="{{ $message->id }}">
                        @unless($self)
                            <img class="chat-message__avatar" src="{{ $message->sender->avatar_url }}" alt="{{ $message->sender->nickname ?? $message->sender->account ?? '匿名' }}">
                        @endunless

                        <div class="chat-message__body">
                            <div class="chat-message__meta {{ $self ? 'chat-message__meta--mine' : '' }}">
                                <span class="chat-message__name">{{ $self ? '我' : ($message->sender->nickname ?? $message->sender->account ?? '匿名') }}</span>
                                <time class="chat-message__time" datetime="{{ $message->created_at->toIso8601String() }}" title="{{ $message->created_at->format('Y/m/d H:i') }}">
                                    {{ $message->created_at->diffForHumans() }}
                                </time>
                            </div>

                            @if($decoded)
                                <div class="chat-bubble chat-bubble--card">
                                    <x-chat.order-summary :data="$decoded" :message="$message" />
                                </div>
                            @else
                                <div class="chat-bubble {{ $self ? 'chat-bubble--mine' : 'chat-bubble--theirs' }}">
                                    <span class="chat-bubble__text">{!! nl2br(e($message->content)) !!}</span>
                                </div>
                            @endif

                            @if($self)
                                <div class="chat-message__status {{ $message->read_at ? 'is-read' : '' }}">
                                    {{ $message->read_at ? '已讀' : '已送出' }}
                                </div>
                            @endif
                        </div>

                        @if($self)
                            <img class="chat-message__avatar" src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->nickname ?? auth()->user()->account ?? '我' }}">
                        @endif
                    </li>

                    @php
                        $lastDate = $dateKey;
                    @endphp
                @empty
                    <li class="chat-thread__empty">尚無訊息</li>
                @endforelse
            </ol>
        </div>

        {{-- 送出表單 --}}
        <form id="sendForm"
              action="{{ route('conversations.message.store', $conversation->id) }}"
              method="POST"
              data-my-id="{{ auth()->id() }}"
              data-conversation-id="{{ $conversation->id }}"
              class="chat-thread__form">
            @csrf
            <textarea id="messageInput"
                      name="content"
                      rows="1"
                      class="form-control"
                      placeholder="輸入訊息，Enter 送出，Shift + Enter 換行"
                      maxlength="5000"
                      data-chat-input></textarea>
            <div class="chat-thread__form-actions">
                <span class="chat-thread__hint">Enter 送出 · Shift + Enter 換行</span>
                <button type="submit" class="btn btn-primary">送出</button>
            </div>
        </form>

        <div class="chat-thread__footer">
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">返回上一頁</a>
        </div>
    </div>
</div>

{{-- 提供預設頭像（給 chat.js 使用） --}}
<script>
    window.CHAT_DEFAULT_AVATAR = "{{ asset('images/avatar-default.png') }}";
</script>
@endsection
