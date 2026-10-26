@extends('site.master')

@section('title', $course->title . ' | ' . config('app.name'))

@php
    $tones = ['science' => 'sci', 'arts' => 'art', 'technology' => 'tech'];
    $category = optional(optional($course->teacher)->category)->name_major;
    $tone = $tones[strtolower($category ?? '')] ?? '';
    $cover = $course->image && file_exists(public_path('uploads/courses/' . $course->image))
        ? asset('uploads/courses/' . $course->image)
        : asset('frontend/images/courses/course-single.jpg');
    $isStudent = auth()->check() && (auth()->user()->hasRole(\App\Models\Role::ROLE_STUDENT) || auth()->user()->hasAbility('enroll_courses'));
@endphp

@section('content')

<section class="detail-head">
  <div class="container">
    <div class="crumbs"><a href="{{ route('site.index') }}">{{ __('site.home') }}</a> / <a href="{{ route('site.courses') }}">{{ __('site.courses') }}</a> / {{ $course->title }}</div>
    @if ($category)
      <span class="tag {{ $tone ? 'tone-' . $tone : '' }}">{{ $category }}</span>
    @endif
    <h1>{{ $course->title }}</h1>
    <p>{{ $course->description }}</p>
    <div class="meta-row">
      @if ($course->teacher)
        <span>@include('includes.icon', ['name' => 'user', 'size' => 18]) {{ $course->teacher->name }}</span>
      @endif
      <span>@include('includes.icon', ['name' => 'users', 'size' => 18]) {{ __('site.students_count', ['count' => $course->students_count]) }}</span>
      @if ($course->created_at)
        <span>@include('includes.icon', ['name' => 'calendar', 'size' => 18]) {{ $course->created_at->translatedFormat('j F Y') }}</span>
      @endif
    </div>
  </div>
</section>

<div class="container detail">
  <div class="detail-main">
    <img class="cover" src="{{ $cover }}" alt="">

    <section class="card panel">
      <h2>{{ __('site.about_course') }}</h2>
      <p style="margin:0;color:var(--ink-muted)">{{ $course->description }}</p>
    </section>

    @if ($course->teacher)
      @php
        $tPhoto = $course->teacher->image && file_exists(public_path('uploads/teachers/' . $course->teacher->image))
            ? asset('uploads/teachers/' . $course->teacher->image)
            : asset('frontend/images/teachers/teacher-' . ($course->teacher->id % 3 + 1) . '.jpg');
      @endphp
      <section class="card panel teacher-strip">
        <img class="avatar" src="{{ $tPhoto }}" alt="">
        <div>
          <small style="color:var(--ink-subtle)">{{ __('site.teacher') }}</small>
          <h2>{{ $course->teacher->name }}</h2>
          <span style="color:var(--ink-muted)">{{ $course->teacher->major }}</span>
        </div>
      </section>
    @endif

    @if ($related->count())
      <h2 style="font-size:24px;margin-top:8px">{{ __('site.related_courses') }}</h2>
      <div class="grid">
        @foreach ($related as $item)
          @include('includes.course')
        @endforeach
      </div>
    @endif
  </div>

  <aside class="card enroll-box" aria-label="{{ __('site.enroll') }}">
    @if ($course->is_enrolled)
      <span class="tag" style="background:var(--success-soft);color:var(--success)">{{ __('site.enrolled') }}</span>
      <form action="{{ route('courses.removeEnrollment', $course->id) }}" method="POST" data-confirm="{{ __('site.confirm_unenroll', ['name' => $course->title]) }}" data-confirm-ok="{{ __('site.unenroll') }}">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger-soft btn-block">{{ __('site.unenroll') }}</button>
      </form>
    @elseif ($isStudent && ! $course->teacher)
      <p class="note" style="font-size:15px">{{ __('site.msg_no_teacher_yet') }}</p>
    @elseif ($isStudent)
      <form action="{{ route('courses.enroll', $course->id) }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-primary btn-block" style="min-height:52px;font-size:17px">{{ $course->btn_text ?: __('site.enroll') }}</button>
      </form>
      <p class="note">{{ __('site.enroll_note') }}</p>
    @elseif (! auth()->check())
      <a href="{{ route('login') }}" class="btn btn-primary btn-block">{{ __('site.login_to_enroll') }}</a>
    @endif

    <dl class="facts">
      <div><dt>{{ __('site.teacher') }}</dt><dd>{{ optional($course->teacher)->name ?? __('site.no_teacher') }}</dd></div>
      @if ($category)
        <div><dt>{{ __('site.category') }}</dt><dd>{{ $category }}</dd></div>
      @endif
      <div><dt>{{ __('site.stat_students') }}</dt><dd>{{ $course->students_count }}</dd></div>
    </dl>
  </aside>
</div>

@endsection
