{{-- resources/views/ratings/index.blade.php --}}
@extends('layouts.app')

@section('title', $user->nickname . ' 的所有評價')

@section('content')
<div class="container">
    <section class="section">
        <div class="ratings-header">
            <h1>{{ $user->nickname }} 的所有評價</h1>
            <div class="rating-summary">
                <span class="stars" title="平均 {{ number_format($averageRating, 1) }} 顆星">
                    @for ($i = 1; $i <= 5; $i++)
                        {{ $i <= round($averageRating) ? '★' : '☆' }}
                        @endfor
                        </span>
                        <span class="rating-count">共 {{ $totalRatings }} 則評價</span>
            </div>
        </div>

        <div class="ratings-list">
            @forelse($ratings as $rating)
            <div class="rating-item">
                <div class="rating-author">
                    <img src="{{ $rating->rater->avatar ? asset('storage/' . $rating->rater->avatar) : 'https://placehold.co/50x50/EFEFEF/AAAAAA&text=頭像' }}" alt="{{ $rating->rater->nickname }}">
                    <div>
                        <strong>{{ $rating->rater->nickname }}</strong>
                        <span class="stars">
                            @for ($i = 1; $i <= 5; $i++)
                                {{ $i <= $rating->score ? '★' : '☆' }}
                                @endfor
                                </span>
                    </div>
                </div>
                <p class="rating-comment">{{ $rating->comment ?? '使用者沒有留下評論。' }}</p>
                <span class="rating-time">{{ $rating->created_at->diffForHumans() }}</span>
            </div>
            @empty
            <p>這位使用者還沒有收到任何評價。</p>
            @endforelse
        </div>

        {{-- 分頁連結 --}}
        @if($ratings->hasPages())
        <div class="pagination-links" style="margin-top: 2rem;">
            {{ $ratings->links() }}
        </div>
        @endif
    </section>
</div>
@endsection

@push('styles')
<style>
    .ratings-header {
        margin-bottom: 2rem;
        text-align: center;
    }

    .rating-summary {
        margin-top: 0.5rem;
    }

    .stars {
        color: #ffc107;
        font-size: 1.2rem;
    }

    .rating-count {
        margin-left: 0.5rem;
        color: #6c757d;
    }

    .ratings-list {
        margin-top: 2rem;
    }

    .rating-item {
        background: #fff;
        padding: 1.5rem;
        border-radius: 8px;
        margin-bottom: 1rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .rating-author {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .rating-author img {
        width: 50px;
        height: 50px;
        border-radius: 50%;
    }

    .rating-comment {
        margin: 0 0 1rem;
    }

    .rating-time {
        font-size: 0.8rem;
        color: #6c757d;
    }
</style>
@endpush