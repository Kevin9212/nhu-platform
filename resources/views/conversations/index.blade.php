@extends('layouts.app')

@section('title', '聊天室')

@section('content')
<div class="chat-container" style="display:flex; gap:20px;">

    {{-- 左側：對話清單 --}}
    @include('conversations.partials.list', ['conversations' => $conversations])

    {{-- 右側：提示訊息 --}}
    <div class="chat-box" style="flex:1; border:1px solid #ddd; border-radius:6px; padding:1rem; background:#fff;">
        <p>請從左側清單選擇一個對話開始聊天。</p>
    </div>
</div>
@endsection