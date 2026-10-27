@php
  $navUnread = auth()->user()->unreadNotifications()->count();
@endphp
<a href="{{ route('notifications.index') }}" class="{{ Route::is('notifications.*') ? 'active' : '' }}">
  @include('includes.icon', ['name' => 'bell', 'size' => 20]) {{ __('notify.title') }}
  @if ($navUnread)<span class="nav-count">{{ $navUnread }}</span>@endif
</a>
