{{-- resources/views/user/show.blade.php --}}
@extends('layouts.app')
@section('title',$user->nickname . ' 的個人頁面' )
@section('content')
<div class="container">
    <section class="section seller-profile-header">
        <img src="{{  asset($user->avatar ?? 'https://placehold.co/150x150/EFEFEF/AAAAAA?text=無頭像') }}"
            alt="{{ $user->nickname }} 的頭像"
            class="seller-avatar">
        ) }}"
        <div class="seller-info">
            <h1>{{ $user->nickname }}</h1>
            <p> 注冊時間:{{ $user->created_at->format('Y-m-d ') }}</p>
            {{-- 可以加評價 --}}
        </div>
    </section>

    <secton class="section">
        <h2>{{ $user->nickname }} 刊登的商品</h2>
        @include('partials.product-grid',[
            'items' => $items,
            'emptyMessage'=>'目前沒有刊登任何商品。'
        ])
        
        @if($items->hasPages())
            <div class="pagination-links">
                {{ $items->links() }}
            </div>
    </secton>
</div>
@endsection