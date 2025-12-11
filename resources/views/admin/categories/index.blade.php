@extends('layouts.admin')

@section('title', '分類管理')

@section('content')
<h1>商品分類管理</h1>

@if(session('success'))
    <div style="color:#22c55e;">{{ session('success') }}</div>
@endif
@if($errors->any())
    <div style="color:#ef4444;">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card" style="margin: 16px 0;">
    <h3>新增分類</h3>
    <form method="POST" action="{{ route('admin.categories.store') }}">
        @csrf
        <div style="display:flex; gap:12px; align-items:center;">
            <label>名稱：<input type="text" name="name" value="{{ old('name') }}" required></label>
            <label>排序：<input type="number" name="sort_order" value="{{ old('sort_order', 0) }}"></label>
            <button type="submit" class="btn">新增</button>
        </div>
    </form>
</div>

<table border="1" cellpadding="8" cellspacing="0" style="width:100%; border-collapse:collapse;">
    <thead>
        <tr>
            <th>ID</th>
            <th>名稱</th>
            <th>排序</th>
            <th>操作</th>
        </tr>
    </thead>
    <tbody>
        @foreach($categories as $category)
        <tr>
            <td>{{ $category->id }}</td>
            <td>{{ $category->name }}</td>
            <td>{{ $category->sort_order }}</td>
            <td>
                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('確定要刪除分類嗎？');" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="background:#b91c1c;color:white;padding:6px 10px;border:none;border-radius:6px;">刪除</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<div style="margin-top: 12px;">
    {{ $categories->links() }}
</div>
@endsection