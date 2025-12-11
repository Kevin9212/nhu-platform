@extends('layouts.admin')

@section('title', '議價管理')

@section('content')
<div class="container">
    <h1>議價管理</h1>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>商品</th>
                <th>買家</th>
                <th>賣家</th>
                <th>出價金額</th>
                <th>狀態</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            @foreach($negotiations as $negotiation)
            <tr>
                <td>{{ $negotiation->id }}</td>
                <td>{{ $negotiation->item->idle_name ?? '已刪除商品' }}</td>
                <td>{{ $negotiation->buyer->nickname ?? '未知' }}</td>
                <td>{{ $negotiation->seller->nickname ?? '未知' }}</td>
                <td>NT$ {{ number_format($negotiation->price) }}</td>
                <td>
                    @switch($negotiation->status)
                        @case('pending')
                            <span class="badge bg-warning">進行中</span>
                            @break
                        @case('accepted')
                            <span class="badge bg-success">已同意</span>
                            @break
                        @case('rejected')
                            <span class="badge bg-danger">已拒絕</span>
                            @break
                        @default
                            <span class="badge bg-secondary">未知</span>
                    @endswitch
                </td>
                <td>
                    @if($negotiation->status === 'pending')
                    <form action="{{ route('admin.negotiations.agree', $negotiation) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('PATCH')
                        <button class="btn btn-sm btn-success">同意</button>
                    </form>
                    <form action="{{ route('admin.negotiations.reject', $negotiation) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('PATCH')
                        <button class="btn btn-sm btn-danger">拒絕</button>
                    </form>
                    @else
                    <em>已處理</em>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $negotiations->links() }}
</div>
@endsection