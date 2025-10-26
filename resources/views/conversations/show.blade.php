{{-- resources/views/conversations/show.blade.php --}}
@extends('layouts.app')

@section('title', '對話 #'.$conversation->id)

@section('content')
<div class="container py-4">

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="m-0">對話編號：{{ $conversation->id }}</h5>
    <small class="text-muted">
      你是：{{ $role === 'buyer' ? '買家' : '賣家' }}
    </small>
  </div>

  <div class="messages" style="height:400px; overflow-y:auto; border:1px solid #eee; padding:0.5rem; margin-bottom:1rem;">
    @forelse($messages as $message)
      @php
        $decoded = null;
        if ($message->msg_type === 'order_summary') {
            $decoded = json_decode($message->content, true);
        }
        $self = ($message->sender_id == auth()->id());
      @endphp

      {{-- 訂單摘要卡片 --}}
      @if($message->msg_type === 'order_summary' && is_array($decoded))
        <div style="margin:10px 0; padding:12px; border:1px solid #ddd; border-radius:8px; background:#fafafa;">
          <p style="margin:0 0 8px 0; font-weight:bold; color:#555;">🧾 訂單摘要</p>
          <div style="display:flex; gap:12px; align-items:center;">
            @if(!empty($decoded['image']))
              {{-- 若為相對路徑放在 storage，可改成 asset('storage/'.ltrim($decoded['image'],'/')) --}}
              <img src="{{ $decoded['image'] }}" alt="商品圖片"
                   style="width:80px; height:80px; object-fit:cover; border-radius:6px;">
            @endif
            <div style="flex:1;">
              <p style="margin:0; font-weight:bold;">{{ $decoded['item_name'] ?? '' }}</p>
              <p style="margin:0; color:#888; font-size:14px;">
                原價：NT$ {{ isset($decoded['item_price']) ? number_format((float)$decoded['item_price']) : '' }}
              </p>
              <p style="margin:0; color:#28a745; font-weight:bold;">
                議價：NT$ {{ isset($decoded['offer_price']) ? number_format((float)$decoded['offer_price']) : '' }}
              </p>

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
          <div style="margin-top:6px; font-size:12px; color:#999;">
            由 {{ $message->sender->nickname ?? $message->sender->account ?? ('用戶#'.$message->sender_id) }}
            · {{ $message->created_at->format('Y/m/d H:i') }}
          </div>
        </div>

      {{-- 一般文字訊息 --}}
      @else
        <div style="margin-bottom:0.5rem; {{ $self ? 'text-align:right;' : '' }}">
          <strong>{{ $message->sender->nickname ?? $message->sender->account ?? ('用戶#'.$message->sender_id) }}</strong>:
          <span>{{ $message->content }}</span>
          <div style="font-size:12px; color:#999; margin-top:2px;">
            {{ $message->created_at->format('Y/m/d H:i') }}
          </div>
        </div>
      @endif

    @empty
      <p style="text-align:center; color:#999;">尚無訊息</p>
    @endforelse
  </div>

  {{-- 若你有即時輸入/送出訊息的功能，可在此加入表單（此處先不放，避免跟你現有路由打架） --}}

  <div class="d-flex gap-2">
    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">返回</a>
    {{-- 你也可在這裡放同意/拒絕按鈕，若頁面語義合適 --}}
    {{-- 
    @if($role === 'seller')
      <form method="POST" action="{{ route('negotiations.agree', $someNegotiationId) }}">
        @csrf
        <button class="btn btn-success">同意議價</button>
      </form>
      <form method="POST" action="{{ route('negotiations.reject', $someNegotiationId) }}">
        @csrf
        <button class="btn btn-danger">拒絕議價</button>
      </form>
    @endif
    --}}
  </div>

</div>
@endsection
