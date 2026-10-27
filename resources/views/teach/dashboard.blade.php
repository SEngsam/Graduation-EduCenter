@extends('layouts.teacher')

@section('title', __('teach.dashboard'))

@section('content')

<div class="page-title">
  <div>
    <h1>{{ __('teach.hello', ['name' => auth()->user()->name]) }}</h1>
    <p>{{ trans_choice('teach.today_summary', $today->count(), ['n' => $today->count()]) }}</p>
  </div>
  <div class="title-actions">
    @include('includes.bell')
  </div>
</div>

<div class="stat-grid">
  <div class="card stat"><span>{{ __('teach.my_courses') }}</span><strong>{{ $courses->count() }}</strong></div>
  <div class="card stat"><span>{{ __('teach.my_students') }}</span><strong>{{ $studentsCount }}</strong></div>
  <div class="card stat"><span>{{ __('teach.to_grade') }}</span><strong>{{ $pending->count() }}</strong></div>
  <div class="card stat"><span>{{ __('teach.attendance_month') }}</span><strong>{{ $attendanceRate !== null ? $attendanceRate . '٪' : '—' }}</strong></div>
</div>

<div class="split">
  <section class="card panel split-main">
    <h2>{{ __('teach.today_classes') }}</h2>
    @forelse ($today as $slot)
      @php
        $done = in_array($slot->course_id . '-' . substr($slot->starts_at, 0, 5), $taken);
      @endphp
      <div class="slot-row">
        <span class="time-box"><strong>{{ $slot->time() }}</strong><small>{{ $slot->time('ends_at') }}</small></span>
        <span class="slot-info">
          <strong>{{ $slot->course->title }}</strong>
          @if ($slot->room)<small>{{ $slot->room }}</small>@endif
        </span>
        @if ($done)
          <span class="pill ok">@include('includes.icon', ['name' => 'check', 'size' => 14]) {{ __('teach.attendance_done') }}</span>
        @endif
        <a href="{{ route('teach.attendance', [$slot->course_id, 'date' => today()->toDateString(), 'time' => substr($slot->starts_at, 0, 5)]) }}" class="btn btn-sm {{ $done ? 'btn-outline' : 'btn-primary' }}">{{ $done ? __('teach.open') : __('teach.take_attendance') }}</a>
      </div>
    @empty
      @include('includes.empty', ['icon' => 'calendar', 'text' => __('teach.no_classes_today')])
    @endforelse
  </section>

  <section class="card panel split-side">
    <h2>{{ __('teach.new_submissions') }}</h2>
    @forelse ($pending->take(5) as $submission)
      <div class="list-row">
        <span class="avatar sm">{{ mb_substr($submission->student->name, 0, 1) }}</span>
        <span class="list-info">
          <strong>{{ $submission->student->name }}</strong>
          <small>{{ $submission->assignment->title }} · {{ $submission->created_at->diffForHumans() }}</small>
        </span>
        <a href="{{ route('teach.assignments', [$submission->assignment->course_id, 'a' => $submission->assignment_id, 'show' => 'pending']) }}" class="text-link">{{ __('teach.grade') }}</a>
      </div>
    @empty
      @include('includes.empty', ['icon' => 'check', 'text' => __('teach.nothing_to_grade')])
    @endforelse
  </section>
</div>

<section>
  <h2 class="block-title">{{ __('teach.my_courses') }}</h2>
  @if ($courses->count())
    <div class="grid">
      @foreach ($courses as $course)
        @php
          $photo = $course->photo();
        @endphp
        <article class="card course-card">
          <div class="thumb"><img src="{{ $photo }}" alt=""></div>
          <div class="course-body">
            <h3>{{ $course->title }}</h3>
            <p>{{ __('teach.students_lessons', ['s' => $course->students_count, 'l' => $course->lessons_count]) }}</p>
            @if ($course->slots->count())
              <small class="meta-line">@include('includes.icon', ['name' => 'clock', 'size' => 15])
                @foreach ($course->slots as $s){{ $s->dayName() }} {{ $s->time() }}@if (!$loop->last)، @endif @endforeach
              </small>
            @endif
            <div class="card-actions">
              <a href="{{ route('teach.course', $course) }}" class="btn btn-outline btn-sm">{{ __('teach.content') }}</a>
              <a href="{{ route('teach.attendance', $course) }}" class="btn btn-outline btn-sm">{{ __('teach.attendance') }}</a>
              <a href="{{ route('teach.assignments', $course) }}" class="btn btn-outline btn-sm">{{ __('teach.assignments') }}</a>
            </div>
          </div>
        </article>
      @endforeach
    </div>
  @else
    @include('includes.empty', ['icon' => 'book', 'text' => __('teach.no_courses')])
  @endif
</section>

@endsection
