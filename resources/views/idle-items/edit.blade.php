{{-- resources/views/idle-items/edit.blade.php --}}
@extends('layouts.app')

@section('title', '編輯商品 - ' . $item->idle_name)

@section('content')
<div class="container">
    <section class="section">
        <div class="form-container" style="max-width: 700px; margin: 2rem auto; background: #fff; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
            <h2>編輯商品：{{ $item->idle_name }}</h2>
            <p style="margin-bottom: 2rem; color: #6c757d;">更新商品資訊或圖片，讓買家獲得最新內容。</p>

            @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('idle-items.update', $item->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                <div class="form-group">
                    <label for="idle_name" style="display: block; margin-bottom: 5px; font-weight: bold;">商品名稱 / 標題</label>
                    <input id="idle_name" type="text" name="idle_name" class="form-control" value="{{ old('idle_name', $item->idle_name) }}" required>
                </div>

                <div class="form-group">
                    <label for="category_id" style="display: block; margin-bottom: 5px; font-weight: bold;">分類</label>
                    <select id="category_id" name="category_id" class="form-control" required>
                        @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $item->category_id) == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="idle_price" style="display: block; margin-bottom: 5px; font-weight: bold;">價格</label>
                    <input id="idle_price" type="number" name="idle_price" class="form-control" value="{{ old('idle_price', $item->idle_price) }}" required min="0">
                </div>

                <div class="form-group">
                    <label for="idle_details" style="display: block; margin-bottom: 5px; font-weight: bold;">商品詳情</label>
                    <textarea id="idle_details" name="idle_details" class="form-control" rows="5" required>{{ old('idle_details', $item->idle_details) }}</textarea>
                </div>

                <div class="form-group">
                    <label for="images" style="display: block; margin-bottom: 5px; font-weight: bold;">商品圖片 (可上傳多張)</label>
                    <input id="images" type="file" name="images[]" class="form-control" multiple>
                </div>

                <div class="form-group">
                    <p style="font-weight: bold;">目前圖片：</p>
                    
                    <div class="current-images" style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                        @forelse($item->images as $image)
                        <div style="background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 8px; padding: 0.5rem; text-align: center;">
                            <img src="{{ asset('storage/' . $image->image_url) }}" alt="商品圖片" style="width: 100%; height: 120px; object-fit: cover; border-radius: 4px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); margin-bottom: 0.5rem;">
                            <form action="{{ route('idle-items.images.destroy', [$item->id, $image->id]) }}" method="POST" onsubmit="return confirm('確定要刪除這張圖片嗎？');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm w-100">刪除圖片</button>
                            </form>
                        </div>
                        @empty
                        <p>無</p>
                        @endforelse
                    </div>
                </div>
                <div class="form-group" style="text-align: center; margin-top: 2rem;">
                    <button type="submit" class="btn btn-primary w-100">更新商品</button>
                </div>
            </form>
        </div>    
    </section>
</div>
@endsection