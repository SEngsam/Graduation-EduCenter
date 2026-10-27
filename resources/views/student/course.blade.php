@extends('layouts.student')

@section('title', $course->title)

@section('content')

@php
  $photo = $course->photo();

  $present = 0;
  $late = 0;
  $absent = 0;
  foreach ($attendance as $status) {
      if ($status == 'present') {
          $present++;
      } elseif ($status == 'late') {
          $late++;
      } elseif ($status == 'absent') {
          $absent++;
      }
  }
  $attendanceRate = count($attendance) ? round(($present + $late) * 100 / count($attendance)) : null;
@endphp

<div class="card course-hero">
  <img src="{{ $photo }}" alt="">
  <div class="course-hero-text">
    <nav class="crumbs"><a href="{{ route('student.dashboard') }}">{{ __('site.my_dashboard') }}</a> / {{ $course->title }}</nav>
    <h1>{{ $course->title }}</h1>
    <span class="muted">
      {{ $course->teacher->name ?? __('site.no_teacher') }}
      @foreach ($course->slots as $slot) · {{ $slot->dayName() }} {{ $slot->time() }}@endforeach
    </span>
    <div class="bar-row">
      <span class="bar big"><span style="width: {{ $progress }}%"></span></span>
      <strong>{{ $progress }}٪</strong>
    </div>
  </div>
</div>

<nav class="tabs">
  <a href="{{ route('student.course', $course) }}" class="active">{{ __('discussion.course') }}</a>
  <a href="{{ route('discussion.index', $course) }}">{{ __('discussion.title') }}</a>
</nav>

<div class="split">
  <div class="split-main stack">
    <h2 class="block-title">{{ __('site.lessons') }}</h2>

    @foreach ($units as $unit)
      @if ($unit->lessons->count())
        <section class="unit">
          <div class="unit-head"><h2>{{ $unit->title }}</h2></div>
          @foreach ($unit->lessons as $lesson)
            @include('student.lesson-row')
          @endforeach
        </section>
      @endif
    @endforeach

    @if ($loose->count())
      <section class="unit">
        @foreach ($loose as $lesson)
          @include('student.lesson-row')
        @endforeach
      </section>
    @endif

    @if (count($done) == 0 && !$next)
      @include('includes.empty', ['icon' => 'book', 'text' => __('site.no_lessons_yet')])
    @endif

    <h2 class="block-title">{{ __('teach.assignments') }}</h2>
    <section class="card">
      @forelse ($assignments as $assignment)
        @php
          $sub = $submissions[$assignment->id] ?? null;
        @endphp
        <div class="list-row padded">
          <span class="list-info">
            <strong>{{ $assignment->title }}</strong>
            <small>{{ __('teach.due_at') }}: {{ $assignment->due_at->translatedFormat('l j F، g:i a') }}</small>
            @if ($sub && $sub->feedback)
              <small class="feedback">{{ $sub->feedback }}</small>
            @endif
          </span>
          @if ($sub && $sub->grade !== null)
            <span class="pill ok" dir="ltr">{{ (float) $sub->grade }} / {{ $assignment->max_grade }}</span>
          @elseif ($sub)
            <span class="pill">{{ __('site.handed_in') }}</span>
          @elseif ($assignment->isOverdue())
            <span class="pill bad">{{ __('teach.missing') }}</span>
          @else
            <span class="pill warn">{{ __('site.due_in', ['time' => $assignment->due_at->diffForHumans(['parts' => 1, 'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE])]) }}</span>
          @endif
        </div>
      @empty
        <p class="muted padded">{{ __('teach.no_assignments') }}</p>
      @endforelse
    </section>
  </div>

  <div class="split-side stack">
    @if ($upcoming)
      <form action="{{ route('student.assignment.submit', $upcoming) }}" method="POST" enctype="multipart/form-data" class="card panel">
        @csrf
        <h2>{{ __('site.next_assignment') }}</h2>
        <strong>{{ $upcoming->title }}</strong>
        <small class="muted">{{ __('teach.due_at') }}: {{ $upcoming->due_at->translatedFormat('l j F، g:i a') }}</small>
        @if ($upcoming->description)
          <p class="muted">{{ $upcoming->description }}</p>
        @endif
        <label class="upload-box">
          @include('includes.icon', ['name' => 'upload', 'size' => 22])
          <span>{{ __('site.choose_file') }}</span>
          <small class="file-name">PDF, Word, ZIP</small>
          <input type="file" name="file" required>
        </label>
        @error('file')<small class="error">{{ $message }}</small>@enderror
        <button class="btn btn-dark">{{ __('site.hand_in') }}</button>
      </form>
    @endif

    @if ($certificate || $certProgress['eligible'])
      <section class="card panel cert-ready">
        <span class="icon-box">@include('includes.icon', ['name' => 'cap', 'size' => 24])</span>
        <h2>{{ __('cert.ready_title') }}</h2>
        <p>{{ __('cert.ready_text', ['course' => $course->title]) }}</p>
        <a href="{{ route('certificate.show', $course->id) }}" class="btn btn-primary">@include('includes.icon', ['name' => 'download', 'size' => 18]) {{ __('cert.download') }}</a>
      </section>
    @else
      <section class="card panel cert-card">
        <h2>{{ __('cert.card_title') }}</h2>
        <div class="rule">
          <span class="rule-mark {{ $certProgress['lessons_ok'] ? 'ok' : '' }}">@include('includes.icon', ['name' => 'check', 'size' => 14])</span>
          <span><strong>{{ __('cert.rule_lessons') }}</strong><small>{{ __('cert.rule_lessons_value', ['done' => $certProgress['lessons_done'], 'total' => $certProgress['lessons_total']]) }}</small></span>
        </div>
        <div class="rule">
          <span class="rule-mark {{ $certProgress['attendance_ok'] ? 'ok' : '' }}">@include('includes.icon', ['name' => 'check', 'size' => 14])</span>
          <span><strong>{{ __('cert.rule_attendance', ['min' => \App\Models\Certificate::MIN_ATTENDANCE]) }}</strong><small>{{ $certProgress['attendance'] !== null ? $certProgress['attendance'] . '٪' : __('cert.no_data') }}</small></span>
        </div>
        <div class="rule">
          <span class="rule-mark {{ $certProgress['grade_ok'] ? 'ok' : '' }}">@include('includes.icon', ['name' => 'check', 'size' => 14])</span>
          <span><strong>{{ __('cert.rule_grade', ['min' => \App\Models\Certificate::MIN_GRADE]) }}</strong><small dir="ltr" style="text-align:start">{{ $certProgress['grade'] !== null ? $certProgress['grade'] . ' / 20' : __('cert.no_data') }}</small></span>
        </div>
        <button type="button" class="btn btn-outline" disabled>@include('includes.icon', ['name' => 'lock', 'size' => 16]) {{ __('cert.locked') }}</button>
      </section>
    @endif

    <section class="card panel">
      <h2>{{ __('site.my_attendance') }}</h2>
      @if ($attendanceRate !== null)
        <span class="big-number">{{ $attendanceRate }}٪</span>
        <small class="muted">{{ __('site.from_classes', ['n' => count($attendance)]) }}</small>
        <div class="chips">
          <span class="pill ok">{{ __('teach.present') }} {{ $present }}</span>
          <span class="pill warn">{{ __('teach.late') }} {{ $late }}</span>
          <span class="pill bad">{{ __('teach.absent') }} {{ $absent }}</span>
        </div>
      @else
        <p class="muted">{{ __('site.no_attendance_yet') }}</p>
      @endif
    </section>
  </div>
</div>

<script>
  document.querySelectorAll('.upload-box input[type=file]').forEach(function (input) {
    input.addEventListener('change', function () {
      if (input.files.length) {
        input.parentNode.querySelector('.file-name').textContent = input.files[0].name;
      }
    });
  });
</script>

@endsection
