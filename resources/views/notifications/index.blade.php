@extends('layouts.app')

@section('title', '通知中心')

@section('content')
<div class="container">
    <h1>通知中心</h1>

    @if($notifications->isEmpty())
        <p>目前沒有任何通知。</p>
    @else
        <ul class="list-group">
            @foreach($notifications as $notification)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <strong>{{ $notification->data['title'] }}</strong><br>
                        {{ $notification->data['message'] }}
                    </div>
                    <form action="{{ route('notifications.read', $notification->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-primary">查看</button>
                    </form>
                </li>
            @endforeach
        </ul>
    @endif
</div>
@endsection
