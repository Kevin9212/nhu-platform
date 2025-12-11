@extends('layouts.admin')
@section('title','使用者管理')
@section('content')
<h1>使用者管理</h1>

@if(session('success'))
    <div style="color:#22c55e;">{{ session('success') }}</div>
@endif

<table border="1" cellpadding="8" cellspacing="0" style="width:100%; border-collapse:collapse;">
    <thead>
        <tr>
            <th>ID</th>
            <th>暱稱</th>
            <th>Email</th>
            <th>角色</th>
            <th>狀態</th>
            <th>建立時間</th>
            <th>操作</th>
        </tr>
    </thead>
    <tbody>
        @foreach($users as $u)
        <tr>
            <td>{{ $u->id }}</td>
            <td>{{ $u->nickname }}</td>
            <td>{{ $u->email }}</td>
            <td>{{ $u->role }}</td>
            <td>
                @if($u->user_status === 'active')
                <span style="color:green;">正常</span>
                @else
                <span style="color:red;">已封禁</span>
                @if($u->banned_until)
                <small>(至 {{ $u->banned_until->format('Y-m-d') }})</small>
                @endif
                @endif
            </td>
            <td>{{ $u->created_at->format('Y-m-d H:i') }}</td>
            <td>
                @if($u->user_status === 'active')
                <form method="POST" action="{{ route('admin.users.ban',$u) }}" style="display:inline;">
                    @csrf @method('PATCH')
                    <button type="submit" style="background:red;color:white;padding:4px 8px;border:none;border-radius:4px;">
                        封禁 30 天
                    </button>
                </form>
                @else
                <form method="POST" action="{{ route('admin.users.unban',$u) }}" style="display:inline;">
                    @csrf @method('PATCH')
                    <button type="submit" style="background:green;color:white;padding:4px 8px;border:none;border-radius:4px;">
                        解除封禁
                    </button>
                </form>
                @endif
                <form method="POST" action="{{ route('admin.users.destroy', $u) }}" style="display:inline;" onsubmit="return confirm('確定要刪除這個帳號嗎？');">
                    @csrf @method('DELETE')
                    <button type="submit" style="background:#b91c1c;color:white;padding:4px 8px;border:none;border-radius:4px;">刪除帳號</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<div style="margin-top:15px;">
    {{ $users->links() }}
</div>
@endsection