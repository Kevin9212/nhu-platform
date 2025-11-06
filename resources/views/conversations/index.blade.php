@extends('layouts.app')

@section('title', '聊天室')

@section('content')
<div class="chat-layout">
    {{-- 對話清單 --}}
    @include('conversations.partials.list', [
        'conversations' => $conversations,
        'activeConversation' => null,
    ])

    {{-- 提示區 --}}
    <div class="chat-thread chat-thread--empty">
        <div class="chat-thread__placeholder">
            <h2 class="chat-thread__placeholder-title">選擇一個對話開始聊天</h2>
            <p class="chat-thread__placeholder-text">
                從左側清單挑選買家或賣家即可檢視訊息內容，支援即時更新與已讀狀態。
            </p>
        </div>
    </div>
</div>
@endsection