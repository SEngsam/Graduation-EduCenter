@php
  $latest = auth()->user()->notifications()->latest()->take(5)->get();
  $unreadCount = auth()->user()->unreadNotifications()->count();
@endphp
<details class="bell">
  <summary class="icon-btn" aria-label="{{ __('notify.title') }}">
    @include('includes.icon', ['name' => 'bell', 'size' => 20])
    @if ($unreadCount)
      <span class="bell-count">{{ $unreadCount }}</span>
    @endif
  </summary>
  <div class="bell-panel card">
    <div class="bell-head">
      <strong>{{ __('notify.title') }}</strong>
      @if ($unreadCount)
        <form method="POST" action="{{ route('notifications.readAll') }}">
          @csrf
          <button class="text-link">{{ __('notify.mark_all') }}</button>
        </form>
      @endif
    </div>
    @forelse ($latest as $notification)
      @include('notifications.item')
    @empty
      <p class="muted" style="padding: 12px">{{ __('notify.empty') }}</p>
    @endforelse
    <a href="{{ route('notifications.index') }}" class="bell-all">{{ __('notify.see_all') }}</a>
  </div>
</details>
