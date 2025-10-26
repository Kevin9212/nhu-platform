{{-- resources/views/idle-items/show.blade.php --}}
@extends('layouts.app')

@section('title', $item->idle_name . ' - NHU 二手交易平台')

@section('content')
<div class="container">
    <div class="item-detail-container">
        <div class="item-images">
            @if($item->images->isNotEmpty())
                <img src="{{ asset('storage/' . $item->images->first()->image_url) }}"
                     alt="{{ $item->idle_name }}" class="main-image">
            @else
                <img src="https://placehold.co/600x400/EFEFEF/AAAAAA&text=無圖片"
                     alt="{{ $item->idle_name }}" class="main-image">
            @endif
        </div>

        <div class="item-info">
            <h1>{{ $item->idle_name }}</h1>

            <div class="seller-info">
                <img src="{{ asset($item->seller->avatar ?? 'https://placehold.co/100x100/EFEFEF/AAAAAA&text=頭像') }}"
                     alt="{{ $item->seller->nickname }}">
                <div>
                    <strong>{{ $item->seller->nickname }}</strong>
                    <p style="margin: 0; color: #6c757d;">賣家</p>
                </div>
            </div>

            <p class="item-price">NT$ {{ number_format($item->idle_price) }}</p>

            {{-- ✅ 議價表單 --}}
            @if(Auth::check() && Auth::id() !== $item->seller->id)
            <form method="POST" action="{{ route('negotiations.store', $item) }}" style="margin-bottom: 1rem;">
            @csrf
            <label for="price">出價：</label>
            <input type="number" name="price" id="price"
                    required min="1" style="width: 100%; padding: 8px; margin: 8px 0;">
            <button type="submit" class="btn btn-warning" style="width: 100%;">提出議價</button>
            </form>

            @endif

            {{-- 聯絡賣家（進聊天室） --}}
            <a href="{{ route('conversation.start', ['user' => $item->seller->id]) }}"
               class="btn btn-primary" style="width: 100%;">聯絡賣家</a>
            {{-- 成立訂單 --}}
            <a href="{{ route('orders.create') }}" 
                class="btn btn-success" 
                style="width: 100%; margin-top: 0.5rem;">
                成立訂單
            </a>

        </div>
    </div>

    <div class="item-description">
        <h3>商品詳情</h3>
        <p>{!! nl2br(e($item->idle_details)) !!}</p>
    </div>
</div>
@endsection
