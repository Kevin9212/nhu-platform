@extends('layouts.app')

@section('title', '聊天室')

@section('content')
<div class="chat-container" style="display:flex; gap:20px;">

    {{-- 左側：對話清單 --}}
    @include('conversations.partials.list', ['conversations' => $conversations])

    {{-- 右側：單一聊天室 --}}
    <div class="chat-box" style="flex:1; border:1px solid #ddd; border-radius:6px; padding:1rem; background:#fff;">
        <h3>與 {{ $otherUser->nickname ?? $otherUser->account }} 的對話</h3>

        <div class="messages" style="height:400px; overflow-y:auto; border:1px solid #eee; padding:0.5rem; margin-bottom:1rem;">
            @forelse($conversation->messages as $message)
            <div style="margin-bottom:0.5rem; {{ $message->sender_id == auth()->id() ? 'text-align:right;' : '' }}">
                <strong>{{ $message->sender->nickname ?? $message->sender->account }}</strong>:
                <span>{{ $message->content }}</span>
            </div>
            @empty
            <p style="text-align:center; color:#999;">尚無訊息</p>
            @endforelse
        </div>

        {{-- 發送訊息 --}}
        <form method="POST" action="{{ route('conversations.message.store', $conversation->id) }}">
            @csrf
            <input type="text" name="content" placeholder="輸入訊息..." required
                style="width:80%; padding:0.5rem; border:1px solid #ddd; border-radius:4px;">
            <button type="submit" class="btn btn-primary">送出</button>
        </form>
    </div>
</div>
@endsection