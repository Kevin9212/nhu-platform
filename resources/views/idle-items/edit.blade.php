{{-- resources/views/idle-items/edit.blade.php --}}
<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>編輯商品 - {{ $item->idle_name }}</title>
    @vite(['resources/css/style.css', 'resources/css/member.css'])
    </head>

<body>
    @include('partials.header')

    <div class="container">
        <section class="section">
            <h2>編輯商品：{{ $item->idle_name }}</h2>

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
                    <label for="idle_name">商品名稱 / 標題</label>
                    <input id="idle_name" type="text" name="idle_name" class="form-control" value="{{ old('idle_name', $item->idle_name) }}" required>
                </div>

                <div class="form-group">
                    <label for="category_id">分類</label>
                    <select id="category_id" name="category_id" class="form-control" required>
                        @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $item->category_id) == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="idle_price">價格</label>
                    <input id="idle_price" type="number" name="idle_price" class="form-control" value="{{ old('idle_price', $item->idle_price) }}" required min="0">
                </div>

                <div class="form-group">
                    <label for="idle_details">商品詳情</label>
                    <textarea id="idle_details" name="idle_details" class="form-control" rows="5" required>{{ old('idle_details', $item->idle_details) }}</textarea>
                </div>

                <div class="form-group">
                    <label for="images">新增圖片 (選填，會覆蓋舊圖片)</label>
                    <input id="images" type="file" name="images[]" class="form-control" multiple>
                </div>

                <div class="form-group">
                    <p>目前圖片：</p>
                    <div class="current-images">
                        @forelse($item->images as $image)
                        <img src="{{ asset('storage/' . $image->image_url) }}" alt="商品圖片" style="width: 100px; height: 100px; object-fit: cover; margin-right: 10px;">
                        @empty
                        <p>無</p>
                        @endforelse
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">更新商品</button>
            </form>
        </section>
    </div>
</body>

</html>