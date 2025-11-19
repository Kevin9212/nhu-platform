@extends('layouts.app')

@section('title', '通知中心')

@section('content')
<div class="container" data-enable-echo>
    <h1>通知中心</h1>

    @if($notifications->isEmpty())
        <p>目前沒有任何通知。</p>
    @else
        <ul class="list-group">
            @foreach($notifications as $notification)
                @php
                    $data = $notification->data ?? [];
                    $text = $data['text'] ?? $data['message'] ?? '';
                    $isRead = !is_null($notification->read_at);
                @endphp
                <li class="list-group-item d-flex justify-content-between align-items-center {{ $isRead ? '' : 'list-group-item-warning' }}">
                    <div class="me-3">
                        <strong>{{ $data['title'] ?? '通知' }}</strong><br>
                        <span class="text-muted small">{{ $notification->created_at?->diffForHumans() }}</span>
                        <p class="mb-0">{{ $text }}</p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        @if(!$isRead)
                            <span class="badge bg-danger">未讀</span>
                        @endif
                        <form action="{{ route('notifications.read', $notification->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-primary">查看</button>
                        </form>
                    </div>
                </li>
            @endforeach
        </ul>

        <div class="mt-3">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection