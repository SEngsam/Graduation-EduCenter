@extends($isTeacher ? 'layouts.teacher' : 'layouts.student')

@section('title', __('discussion.title') . ' · ' . $course->title)

@section('content')

@if ($isTeacher)
  @include('teach.tabs')
@else
  <div class="course-head card">
    <div>
      <nav class="crumbs"><a href="{{ route('student.dashboard') }}">{{ __('site.my_dashboard') }}</a> / {{ $course->title }}</nav>
      <h1>{{ $course->title }}</h1>
    </div>
  </div>
  <nav class="tabs">
    <a href="{{ route('student.course', $course) }}">{{ __('discussion.course') }}</a>
    <a href="{{ route('discussion.index', $course) }}" class="active">{{ __('discussion.title') }}</a>
  </nav>
@endif

<div class="split">
  <div class="split-side stack">
    @if (!$isTeacher)
      <form method="POST" action="{{ route('discussion.store', $course) }}" class="card panel">
        @csrf
        <h2>{{ __('discussion.ask') }}</h2>
        <label class="field">
          <span>{{ __('discussion.which_lesson') }}</span>
          <select name="lesson_id">
            <option value="">{{ __('discussion.general') }}</option>
            @foreach ($lessons as $lesson)
              <option value="{{ $lesson->id }}" {{ old('lesson_id') == $lesson->id ? 'selected' : '' }}>{{ $lesson->title }}</option>
            @endforeach
          </select>
        </label>
        <label class="field">
          <span>{{ __('discussion.question') }}</span>
          <input type="text" name="title" value="{{ old('title') }}" placeholder="{{ __('discussion.question_placeholder') }}" required>
          @error('title')<small class="error">{{ $message }}</small>@enderror
        </label>
        <label class="field">
          <span>{{ __('discussion.details') }}</span>
          <textarea name="body" rows="3">{{ old('body') }}</textarea>
        </label>
        <button class="btn btn-dark">{{ __('discussion.send') }}</button>
      </form>
    @endif

    @foreach ($questions as $item)
      <a href="{{ route('discussion.index', [$course, 'q' => $item->id]) }}" class="card assignment-item {{ $current && $current->id == $item->id ? 'active' : '' }}">
        <strong>{{ $item->title }}</strong>
        <small>{{ $item->user->name }} · {{ $item->lesson->title ?? __('discussion.general') }} · {{ trans_choice('discussion.replies', $item->replies->count(), ['n' => $item->replies->count()]) }}</small>
        <span>
          @if ($item->answeredByTeacher())
            <span class="pill ok">@include('includes.icon', ['name' => 'check', 'size' => 14]) {{ __('discussion.answered') }}</span>
          @else
            <span class="pill warn">{{ __('discussion.waiting') }}</span>
          @endif
        </span>
      </a>
    @endforeach
  </div>

  <div class="split-main">
    @if ($current)
      <section class="card panel thread">
        <div class="thread-head">
          <div>
            <span class="thread-lesson">{{ $current->lesson->title ?? __('discussion.general') }}</span>
            <h2>{{ $current->title }}</h2>
          </div>
          @if ($isTeacher || $current->user_id == auth()->id())
            <form method="POST" action="{{ route('discussion.destroy', $current) }}" data-confirm="{{ __('discussion.confirm_question', ['name' => $current->title]) }}" data-confirm-ok="{{ __('discussion.delete') }}">
              @csrf
              @method('DELETE')
              <button class="btn btn-outline btn-sm">{{ __('discussion.delete') }}</button>
            </form>
          @endif
        </div>

        @if ($current->body)
          @include('discussion.post', ['author' => $current->user, 'body' => $current->body, 'date' => $current->created_at, 'reply' => null])
        @endif

        @foreach ($current->replies as $reply)
          @include('discussion.post', ['author' => $reply->user, 'body' => $reply->body, 'date' => $reply->created_at, 'reply' => $reply])
        @endforeach

        <form method="POST" action="{{ route('discussion.reply', $current) }}" class="stack">
          @csrf
          <label class="field">
            <span>{{ __('discussion.your_reply') }}</span>
            <textarea name="body" rows="3" placeholder="{{ __('discussion.reply_placeholder') }}" required></textarea>
            @error('body')<small class="error">{{ $message }}</small>@enderror
          </label>
          <div><button class="btn btn-dark">{{ __('discussion.post_reply') }}</button></div>
        </form>
      </section>
    @else
      @include('includes.empty', ['icon' => 'message', 'text' => $isTeacher ? __('discussion.empty_teacher') : __('discussion.empty')])
    @endif
  </div>
</div>

@endsection
