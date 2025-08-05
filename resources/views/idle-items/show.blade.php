{{-- resources/views/idle-items/show.blade.php --}}
@extends('layouts.app')

@section('title', $item->idle_name . ' - NHU 二手交易平台')

@section('content')
<div class="container">
    <div class="item-detail-container">
        <div class="item-images">
            @if($item->images->isNotEmpty())
            <img src="{{ asset('storage/' . $item->images->first()->image_url) }}" alt="{{ $item->idle_name }}" class="main-image">
            @else
            <img src="https://placehold.co/600x400/EFEFEF/AAAAAA&text=無圖片" alt="{{ $item->idle_name }}" class="main-image">
            @endif
            {{-- 未來可以新增多張圖片的縮圖輪播 --}}
        </div>

        <div class="item-info">
            <h1>{{ $item->idle_name }}</h1>

            <div class="seller-info">
                <img src="{{ asset($item->seller->avatar ?? 'https://placehold.co/100x100/EFEFEF/AAAAAA&text=頭像') }}" alt="{{ $item->seller->nickname }}">
                <div>
                    <strong>{{ $item->seller->nickname }}</strong>
                    <p style="margin: 0; color: #6c757d;">賣家</p>
                </div>
            </div>

            <p class="item-price">NT$ {{ number_format($item->idle_price) }}</p>

            <a href="{{ route('conversation.start', ['user' => $item->seller->id]) }}" class="btn btn-primary" style="width: 100%;">聯絡賣家</a>
        </div>
    </div>

    <div class="item-description">
        <h3>商品詳情</h3>
        <p>{!! nl2br(e($item->idle_details)) !!}</p>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* 針對此頁面的額外樣式 */
    .item-detail-container {
        display: flex;
        gap: 2rem;
        margin-top: 2rem;
    }

    .item-images {
        flex: 1;
    }

    .main-image {
        width: 100%;
        height: auto;
        max-height: 500px;
        object-fit: cover;
        border-radius: 8px;
        margin-bottom: 1rem;
    }

    .item-info {
        flex: 1;
    }

    .item-info h1 {
        margin-top: 0;
        font-size: 2rem;
    }

    .item-price {
        font-size: 1.8rem;
        font-weight: bold;
        color: #e44d26;
        margin: 1rem 0;
    }

    .seller-info {
        display: flex;
        align-items: center;
        gap: 10px;
        background-color: #f8f9fa;
        padding: 1rem;
        border-radius: 8px;
        margin: 1.5rem 0;
    }

    .seller-info img {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        object-fit: cover;
    }

    .item-description {
        margin-top: 2rem;
        line-height: 1.8;
    }
</style>
@endpush