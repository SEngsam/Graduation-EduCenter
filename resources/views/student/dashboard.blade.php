@extends('layouts.student')

@section('title', __('site.my_dashboard'))

@section('content')

<div class="page-title">
  <div>
    <h1>{{ __('site.welcome_name', ['name' => auth()->user()->name]) }}</h1>
    <p>{{ __('site.dashboard_sub') }}</p>
  </div>
  <a href="{{ route('site.courses') }}" class="btn btn-primary">{{ __('site.enroll_new') }}</a>
</div>

<div class="stat-grid">
  <div class="card stat"><span>{{ __('site.my_courses') }}</span><strong>{{ $courses->count() }}</strong></div>
  <div class="card stat"><span>{{ __('site.attendance_rate') }}</span><strong>{{ $attendanceRate !== null ? $attendanceRate . '٪' : '—' }}</strong></div>
  <div class="card stat"><span>{{ __('site.upcoming_assignments') }}</span><strong>{{ $upcoming->count() }}</strong></div>
  <div class="card stat"><span>{{ __('site.avg_grade') }}</span><strong dir="ltr">{{ $average !== null ? $average . ' / 20' : '—' }}</strong></div>
</div>

<section class="card panel">
  <div class="section-head" style="margin:0">
    <h2>{{ __('site.my_courses') }}</h2>
  </div>
  @forelse ($courses as $course)
    @php
      $photo = $course->image && file_exists(public_path('uploads/courses/' . $course->image))
          ? asset('uploads/courses/' . $course->image)
          : asset('frontend/images/courses/course-' . ($course->id % 6 + 1) . '.jpg');
      $category = optional(optional($course->teacher)->category)->name_major;
      $percent = $progress[$course->id] ?? 0;
    @endphp
    <div class="my-course">
      <img src="{{ $photo }}" alt="">
      <div class="info">
        <strong>{{ $course->title }}</strong>
        <span>{{ optional($course->teacher)->name ?? __('site.no_teacher') }}@if ($category) · {{ $category }}@endif</span>
        <span class="bar-row">
          <span class="bar"><span style="width: {{ $percent }}%"></span></span>
          <small>{{ $percent }}٪</small>
        </span>
      </div>
      <div class="acts">
        <a href="{{ route('student.course', $course->id) }}" class="btn btn-primary btn-sm">{{ __('site.continue') }}</a>
        <form action="{{ route('courses.removeEnrollment', $course->id) }}" method="POST" data-confirm="{{ __('site.confirm_unenroll', ['name' => $course->title]) }}" data-confirm-ok="{{ __('site.unenroll') }}">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-danger-soft btn-sm">{{ __('site.unenroll') }}</button>
        </form>
      </div>
    </div>
  @empty
    @include('includes.empty', ['icon' => 'book', 'text' => __('site.no_my_courses'), 'action' => [route('site.courses'), __('site.browse_courses')]])
  @endforelse
</section>

<div class="grid-2">
  <section class="card panel">
    <h2>{{ __('site.today_classes') }}</h2>
    @forelse ($todayClasses as $slot)
      <a href="{{ route('student.course', $slot->course_id) }}" class="slot-row">
        <span class="time-box"><strong>{{ $slot->time() }}</strong><small>{{ $slot->time('ends_at') }}</small></span>
        <span class="slot-info">
          <strong>{{ $slot->course->title }}</strong>
          @if ($slot->room)<small>{{ $slot->room }}</small>@endif
        </span>
      </a>
    @empty
      <p class="muted">{{ __('site.no_classes_today') }}</p>
    @endforelse
  </section>

  <section class="card panel">
    <h2>{{ __('site.upcoming_assignments') }}</h2>
    @forelse ($upcoming->take(4) as $assignment)
      <a href="{{ route('student.course', $assignment->course_id) }}" class="list-row">
        <span class="icon-box sm">@include('includes.icon', ['name' => 'clip', 'size' => 20])</span>
        <span class="list-info">
          <strong>{{ $assignment->title }}</strong>
          <small>{{ $assignment->course->title }}</small>
        </span>
        <span class="pill warn">{{ __('site.due_in', ['time' => $assignment->due_at->diffForHumans(['parts' => 1, 'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE])]) }}</span>
      </a>
    @empty
      <p class="muted">{{ __('site.no_upcoming_assignments') }}</p>
    @endforelse
  </section>
</div>

@if ($events->count())
<section>
  <h2 class="block-title">{{ __('site.upcoming_events') }}</h2>
  <div class="grid-2">
    @foreach ($events as $item)
      <div class="card event-row">
        <div class="event-date">
          @if ($item->created_at)
            <strong>{{ $item->created_at->format('d') }}</strong>
            <span>{{ $item->created_at->translatedFormat('M') }}</span>
          @else
            @include('includes.icon', ['name' => 'pin', 'size' => 26])
          @endif
        </div>
        <div>
          <h3>{{ $item->description }}</h3>
          <p>{{ $item->location }}</p>
        </div>
      </div>
    @endforeach
  </div>
</section>
@endif

@endsection
