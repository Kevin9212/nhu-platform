{{-- resources/views/conversations/show.blade.php --}}
@extends('layouts.app')

@section('title', '對話 #'.$conversation->id)

@section('content')
<div class="container py-4">

  {{-- 標題列 --}}
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="m-0">對話編號：{{ $conversation->id }}</h5>
    <small class="text-muted">你是：{{ $role === 'buyer' ? '買家' : '賣家' }}</small>
  </div>

  {{-- 訊息列表 --}}
  <div id="messageScroller"
       class="messages"
       style="height:400px; overflow-y:auto; border:1px solid #eee; padding:0.5rem; margin-bottom:1rem;">
    <ul id="messageList" class="list-unstyled m-0">
      @forelse($messages as $message)
        @php
          $decoded = ($message->msg_type === 'order_summary') ? json_decode($message->content, true) : null;
          $self    = ($message->sender_id == auth()->id());
        @endphp

        {{-- 訂單摘要卡片 --}}
        @if($message->msg_type === 'order_summary' && is_array($decoded))
          <x-chat.order-summary :data="$decoded" :message="$message" />

        {{-- 一般文字訊息（左右氣泡） --}}
        @else
          <li class="mb-2 d-flex {{ $self ? 'justify-content-end' : 'justify-content-start' }} align-items-end">
            {{-- 左側：對方頭像（自己發就不顯示） --}}
            @unless($self)
              <img src="{{ $message->sender->avatar_url }}"
                   class="rounded-circle me-2"
                   style="width:32px;height:32px;object-fit:cover;">
            @endunless

            {{-- 中間：名稱/時間 + 訊息泡泡 + 已讀狀態 --}}
            <div style="max-width:75%;">
              <div class="text-muted" style="font-size:12px; {{ $self ? 'text-align:right;' : '' }}">
                {{ $self ? '我' : ($message->sender->nickname ?? $message->sender->account ?? '匿名') }}
                · {{ $message->created_at->diffForHumans() }}
              </div>

              {{-- 訊息泡泡 --}}
              <div class="{{ $self ? 'text-white' : '' }}"
                   style="padding:8px 12px; border-radius:16px;
                          {{ $self
                            ? 'background:#0d6efd; border-top-right-radius:4px;'
                            : 'background:#fff; border:1px solid #e5e7eb; border-top-left-radius:4px;' }}">
                <span class="d-block" style="white-space:pre-wrap; word-break:break-word;">
                  {{ $message->content }}
                </span>
              </div>

              {{-- 已送出 / 已讀（僅顯示於自己發出的訊息） --}}
              @if($message->sender_id == auth()->id())
                <div class="mt-1" style="font-size:12px; {{ $self ? 'text-align:right;' : '' }}">
                  @if($message->read_at)
                    <small class="text-primary">已讀</small>
                  @else
                    <small class="text-muted">已送出</small>
                  @endif
                </div>
              @endif
            </div>

            {{-- 右側：自己的頭像（對方發就不顯示） --}}
            @if($self)
              <img src="{{ auth()->user()->avatar_url }}"
                   class="rounded-circle ms-2"
                   style="width:32px;height:32px;object-fit:cover;">
            @endif
          </li>
        @endif
      @empty
        <li class="text-center text-muted py-3">尚無訊息</li>
      @endforelse
    </ul>
  </div>

  {{-- 送出表單 --}}
  <form id="sendForm"
        action="{{ route('conversations.message.store', $conversation->id) }}"
        method="POST"
        data-my-id="{{ auth()->id() }}"
        data-conversation-id="{{ $conversation->id }}"
        class="d-flex align-items-end gap-2 mb-2">
    @csrf
    <div class="flex-grow-1">
      <textarea id="messageInput"
                name="content"
                rows="1"
                class="form-control"
                placeholder="輸入訊息（Enter 送出，Shift+Enter 換行）"
                style="resize:none;"></textarea>
    </div>
    <button type="submit" class="btn btn-primary">送出</button>
  </form>

  <small class="text-muted d-block mb-3">Enter 送出，Shift+Enter 換行</small>

  {{-- 底部動作 --}}
  <div class="d-flex gap-2">
    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">返回</a>
    {{-- 可在此區補「同意/拒絕」等按鈕 --}}
  </div>

</div>

{{-- 提供預設頭像（給 chat.js 使用） --}}
<script>
  window.CHAT_DEFAULT_AVATAR = "{{ asset('images/avatar-default.png') }}";
</script>
@endsection
