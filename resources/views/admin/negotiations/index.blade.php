{{-- resources/views/admin/negotiations/index.blade.php --}}
@extends('layouts.admin')

@section('title', '議價管理')

@section('content')
<div class="container">
    <h1>議價管理</h1>
    <p>以下是所有買賣雙方的議價紀錄，管理員可視情況介入。</p>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>ID</th>
                <th>商品</th>
                <th>買家</th>
                <th>賣家</th>
                <th>出價金額</th>
                <th>狀態</th>
                <th>建立時間</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            @forelse($negotiations as $negotiation)
            <tr>
                <td>{{ $negotiation->id }}</td>
                <td>{{ $negotiation->item->idle_name ?? '已刪除商品' }}</td>
                <td>{{ $negotiation->buyer->nickname ?? '已刪除帳號' }}</td>
                <td>{{ $negotiation->seller->nickname ?? '已刪除帳號' }}</td>
                <td>NT$ {{ number_format($negotiation->proposed_price) }}</td>
                <td>
                    @if($negotiation->status === 'open')
                    <span class="badge bg-warning">進行中</span>
                    @elseif($negotiation->status === 'agreed')
                    <span class="badge bg-success">已同意</span>
                    @elseif($negotiation->status === 'rejected')
                    <span class="badge bg-danger">已拒絕</span>
                    @endif
                </td>
                <td>{{ $negotiation->created_at->format('Y-m-d H:i') }}</td>
                <td>
                    @if($negotiation->status === 'open')
                    <form action="{{ route('admin.negotiations.agree', $negotiation) }}" method="POST" style="display:inline-block">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-sm btn-success">同意</button>
                    </form>
                    <form action="{{ route('admin.negotiations.reject', $negotiation) }}" method="POST" style="display:inline-block">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-sm btn-danger">拒絕</button>
                    </form>
                    @else
                    <em>已處理</em>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center">目前沒有任何議價紀錄。</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div>
        {{ $negotiations->links() }}
    </div>
</div>
@endsection