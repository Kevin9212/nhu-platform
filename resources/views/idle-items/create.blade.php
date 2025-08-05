{{-- resources/views/idle-items/create.blade.php --}}
@extends('layouts.app')

@section('title', '刊登新商品')

@section('content')
<div class="container">
    <section class="section">
        <div class="form-container" style="max-width: 700px; margin: 2rem auto; background: #fff; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
            <h2>刊登您的二手商品</h2>
            <p style="margin-bottom: 2rem; color: #6c757d;">請填寫以下資訊，讓您的寶物找到新主人！</p>

            {{-- 直接引入我們已經寫好的共用表單 --}}
            @include('idle-items.create-form')
        </div>
    </section>
</div>
@endsection