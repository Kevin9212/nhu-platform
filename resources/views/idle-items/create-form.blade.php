{{-- resources/views/idle-items/create-form.blade.php --}}

{{-- 顯示驗證失敗的錯誤訊息 --}}
@if ($errors->any())
<div class="alert alert-danger" style="background-color: #f8d7da; color: #721c24; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
    <ul style="margin: 0; padding-left: 20px;">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

{{-- enctype="multipart/form-data" 是上傳檔案的表單必須的屬性 --}}
<form action="{{ route('idle-items.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="form-group">
        <label for="idle_name" style="display: block; margin-bottom: 5px; font-weight: bold;">商品名稱 / 標題</label>
        <input id="idle_name" type="text" name="idle_name" class="form-control" value="{{ old('idle_name') }}" required>
    </div>

    <div class="form-group">
        <label for="category_id" style="display: block; margin-bottom: 5px; font-weight: bold;">分類</label>
        <select id="category_id" name="category_id" class="form-control" required>
            <option value="" disabled selected>-- 請選擇分類 --</option>
            @foreach ($categories as $category)
            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                {{ $category->name }}
            </option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label for="idle_price" style="display: block; margin-bottom: 5px; font-weight: bold;">價格</label>
        <input id="idle_price" type="number" name="idle_price" class="form-control" value="{{ old('idle_price') }}" required min="0">
    </div>

    <div class="form-group">
        <label for="idle_details" style="display: block; margin-bottom: 5px; font-weight: bold;">商品詳情</label>
        <textarea id="idle_details" name="idle_details" class="form-control" rows="5" required>{{ old('idle_details') }}</textarea>
    </div>

    <div class="form-group">
        <label for="images" style="display: block; margin-bottom: 5px; font-weight: bold;">商品圖片 (可上傳多張)</label>
        {{-- 修正：name="images[]" 並加上 multiple 屬性以支援多圖上傳 --}}
        <input id="images" type="file" name="images[]" class="form-control" required multiple>
    </div>

    <div class="form-group" style="text-align: center; margin-top: 2rem;">
        <button type="submit" class="btn btn-primary">刊登商品</button>
    </div>
</form>
