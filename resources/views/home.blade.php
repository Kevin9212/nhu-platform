{{-- resources/views/home.blade.php --}}
@extends('layouts.app')

@section('title', '南華大學二手交易平台')

@section('content')
<div class="container">
    {{-- 搜尋表單 --}}
    @include('partials.search-form', ['showAdvanced' => true])

    {{-- 最新上架商品區塊 --}}
    <section class="section">
        <div class="section-header">
            <h2>📦 最新上架商品</h2>
            <a href="{{ route('idle-items.index') }}" class="view-all-link">查看全部 →</a>
        </div>

        @include('partials.product-grid', [
        'items' => $items,
        'emptyMessage' => '目前沒有任何上架中的商品。',
        'showCategory' => true
        ])

        {{-- 只在有分頁時顯示 --}}
        @if($items->hasPages())
        <div class="pagination-links">
            {{ $items->links() }}
        </div>
        @endif
    </section>

    {{-- 隨機推薦商品區塊 --}}
    <section class="section">
        <div class="section-header">
            <h2>🎁 隨機推薦商品</h2>
            <button onclick="refreshRecommendations()" class="refresh-btn">🔄 換一批</button>
        </div>

        <div id="random-items-container">
            @include('partials.product-grid', [
            'items' => $randomItems,
            'emptyMessage' => '目前沒有任何商品可供推薦。',
            'showCategory' => true
            ])
        </div>
    </section>
</div>

{{-- 加入換一批推薦的 JavaScript --}}
@push('scripts')
<script>
    function refreshRecommendations() {
        const container = document.getElementById('random-items-container');
        const refreshBtn = document.querySelector('.refresh-btn');

        // 顯示載入狀態
        refreshBtn.textContent = '🔄 載入中...';
        refreshBtn.disabled = true;

        fetch('{{ route("home.random-items") }}')
            .then(response => response.text())
            .then(html => {
                container.innerHTML = html;
                refreshBtn.textContent = '🔄 換一批';
                refreshBtn.disabled = false;
            })
            .catch(error => {
                console.error('Error:', error);
                refreshBtn.textContent = '🔄 換一批';
                refreshBtn.disabled = false;
            });
    }
</script>
@endpush
@endsection