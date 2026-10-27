@php
  $data = $notification->data;
  $type = $data['type'] ?? 'category';
  $params = [];
  foreach ($data['params'] ?? [] as $key => $value) {
      $params[$key] = e((string) $value);
  }
  $icons = [
      'grade' => ['check', 'ok'],
      'assignment' => ['clip', 'warn'],
      'reminder' => ['clock', 'warn'],
      'absent' => ['alert', 'bad'],
      'lesson' => ['book', 'info'],
      'submission' => ['upload', 'info'],
      'category' => ['grid', 'info'],
      'question' => ['message', 'info'],
      'reply' => ['message', 'ok'],
  ];
  [$icon, $tone] = $icons[$type] ?? ['bell', 'info'];
@endphp
<a href="{{ route('notifications.open', $notification->id) }}" class="note-row {{ $notification->read_at ? '' : 'unread' }}">
  <span class="note-icon {{ $tone }}">@include('includes.icon', ['name' => $icon, 'size' => 20])</span>
  <span class="note-text">
    <span>{!! __('notify.' . $type, $params) !!}</span>
    <small>@if (!empty($data['course'])){{ $data['course'] }} · @endif{{ $notification->created_at->diffForHumans() }}</small>
  </span>
  @if (!$notification->read_at)
    <span class="note-dot" title="{{ __('notify.unread') }}"></span>
  @endif
</a>
