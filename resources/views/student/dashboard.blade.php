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
  <div class="card stat"><span>{{ __('site.my_teachers') }}</span><strong>{{ $teachersCount }}</strong></div>
  <div class="card stat"><span>{{ __('site.upcoming') }}</span><strong>{{ $events->count() }}</strong></div>
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
    @endphp
    <div class="my-course">
      <img src="{{ $photo }}" alt="">
      <div class="info">
        <strong>{{ $course->title }}</strong>
        <span>{{ optional($course->teacher)->name ?? __('site.no_teacher') }}@if ($category) · {{ $category }}@endif</span>
      </div>
      <div class="acts">
        <a href="{{ route('site.course', $course->id) }}" class="btn btn-outline btn-sm">{{ __('site.open') }}</a>
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
  <section>
    <h2 style="font-size:20px;margin-bottom:14px">{{ __('site.upcoming_events') }}</h2>
    @forelse ($events as $item)
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
    @empty
      @include('includes.empty', ['icon' => 'calendar', 'text' => __('site.no_events')])
    @endforelse
  </section>
  <section>
    <h2 style="font-size:20px;margin-bottom:14px">{{ __('site.latest_news') }}</h2>
    @foreach ($news as $item)
      @include('includes.news')
    @endforeach
  </section>
</div>

@endsection
