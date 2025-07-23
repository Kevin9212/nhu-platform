{{-- resources/views/member/partials/profile-form.blade.php --}}

{{-- 顯示更新成功的訊息 --}}
@if(session('profile_success'))
<div class="alert alert-success" style="background-color: #d4edda; color: #155724; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
    {{ session('profile_success') }}
</div>
@endif

{{-- 顯示驗證失敗的錯誤訊息 --}}
@if($errors->any())
<div class="alert alert-danger" style="background-color: #f8d7da; color: #721c24; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
    <ul style="margin: 0; padding-left: 20px;">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form action="{{ route('member.profile.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PATCH') {{-- 使用 PATCH 方法來更新資料 --}}

    <div class="form-group">
        <label for="account">註冊信箱 (帳號)</label>
        {{-- 帳號通常不允許修改，所以設為 readonly --}}
        <input id="account" type="email" class="form-control" value="{{ Auth::user()->account }}" readonly disabled>
    </div>

    <div class="form-group">
        <label for="nickname">暱稱</label>
        <input id="nickname" type="text" name="nickname" class="form-control" value="{{ old('nickname', Auth::user()->nickname) }}" required>
    </div>

    <div class="form-group">
        <label for="user_phone">聯絡電話</label>
        <input id="user_phone" type="text" name="user_phone" class="form-control" value="{{ old('user_phone', Auth::user()->user_phone) }}" required>
    </div>

    <div class="form-group">
        <label for="avatar">更新頭像 (選填)</label>
        <input id="avatar" type="file" name="avatar" class="form-control">
        @if(Auth::user()->avatar)
        <div style="margin-top: 10px;">
            <p>目前頭像：</p>
            {{-- 修正：圖片路徑應指向 storage --}}
            <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="目前頭像" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover;">
        </div>
        @endif
    </div>

    <div class="form-group" style="text-align: center; margin-top: 2rem;">
        <button type="submit" class="btn btn-primary">更新資料</button>
    </div>
</form>
