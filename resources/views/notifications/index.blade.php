@php
  $user = auth()->user();
  if ($user->hasRole(\App\Models\Role::ROLE_STUDENT)) {
      $layout = 'layouts.student';
  } elseif ($user->teacher) {
      $layout = 'layouts.teacher';
  } else {
      $layout = 'admin.master';
  }
  $unreadCount = $user->unreadNotifications()->count();
@endphp
@extends($layout)

@section('title', __('notify.title'))
@section('crumbs', __('notify.title'))

@section('content')

<div class="page-title">
  <div>
    <h1>{{ __('notify.title') }}</h1>
    <p>{{ trans_choice('notify.unread_count', $unreadCount, ['n' => $unreadCount]) }}</p>
  </div>
  @if ($unreadCount)
    <form method="POST" action="{{ route('notifications.readAll') }}">
      @csrf
      <button class="btn btn-outline">@include('includes.icon', ['name' => 'check', 'size' => 18]) {{ __('notify.mark_all') }}</button>
    </form>
  @endif
</div>

<nav class="chips" style="padding:0">
  @foreach (['all', 'unread', 'grades', 'assignments', 'attendance'] as $key)
    <a href="{{ route('notifications.index', ['show' => $key]) }}" class="chip {{ $filter == $key ? 'active' : '' }}">{{ __('notify.filter_' . $key) }}</a>
  @endforeach
</nav>

<div class="split">
  <section class="card split-main note-list">
    @php
      $shownToday = false;
      $shownEarlier = false;
    @endphp
    @forelse ($notifications as $notification)
      @if ($notification->created_at->isToday() && !$shownToday)
        <h2 class="note-group">{{ __('notify.today') }}</h2>
        @php
          $shownToday = true;
        @endphp
      @elseif (!$notification->created_at->isToday() && !$shownEarlier)
        <h2 class="note-group">{{ __('notify.earlier') }}</h2>
        @php
          $shownEarlier = true;
        @endphp
      @endif
      @include('notifications.item')
    @empty
      @include('includes.empty', ['icon' => 'bell', 'text' => __('notify.empty')])
    @endforelse
    <div class="pager">{{ $notifications->links() }}</div>
  </section>

  <form method="POST" action="{{ route('notifications.settings') }}" class="card panel split-side">
    @csrf
    @method('PUT')
    <h2>{{ __('notify.email_title') }}</h2>
    <p class="muted">{{ __('notify.email_hint') }}</p>
    @foreach ($settingTypes as $type => $default)
      <label class="setting-row">
        <span>{{ __('notify.setting_' . $type) }}</span>
        <input type="checkbox" name="email[{{ $type }}]" value="1" {{ $user->wantsEmail($type) ? 'checked' : '' }}>
      </label>
    @endforeach
    <button class="btn btn-outline">{{ __('notify.save') }}</button>
  </form>
</div>

@endsection
