{{-- resources/views/components/notify-bell.blade.php --}}
@props(['unread' => 0])

<a href="{{ route('notifications.index') }}" class="notification-bell">
  <img src="{{ asset('images/notify.png') }}" alt="notify" class="icon">
  @if($unread > 0)
    <span class="notification-count">{{ $unread }}</span>
  @endif
</a>
