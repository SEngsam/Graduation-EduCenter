@php
  $isDone = in_array($lesson->id, $done);
  $isNext = $next && $next->id == $lesson->id;
@endphp
<div class="lesson-row {{ $isNext ? 'is-next' : '' }}">
  <form action="{{ route('student.lesson.done', $lesson) }}" method="POST">
    @csrf
    <button class="done-mark {{ $isDone ? 'on' : '' }}" aria-label="{{ $isDone ? __('site.mark_undone') : __('site.mark_done') }}">
      @if ($isDone)@include('includes.icon', ['name' => 'check', 'size' => 15])@endif
    </button>
  </form>
  <span class="lesson-info">
    <strong>{{ $lesson->title }}</strong>
    <small>{{ __('teach.type_' . $lesson->type) }}</small>
  </span>
  @if ($lesson->type == 'file' && $lesson->file)
    <a href="{{ route('student.lesson.file', $lesson) }}" class="btn btn-outline btn-sm">@include('includes.icon', ['name' => 'download', 'size' => 16]) {{ __('site.download') }}</a>
  @elseif ($lesson->url)
    <a href="{{ $lesson->url }}" target="_blank" rel="noopener" class="btn btn-sm {{ $isNext ? 'btn-primary' : 'btn-outline' }}">{{ __('site.open') }}</a>
  @endif
</div>
@if ($lesson->type == 'text' && $lesson->body)
  <details class="lesson-body">
    <summary>{{ __('site.read_lesson') }}</summary>
    <p>{!! nl2br(e($lesson->body)) !!}</p>
  </details>
@endif
