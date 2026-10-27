<div class="course-head card">
  <div>
    <h1>{{ $course->title }}</h1>
    <p>{{ __('teach.students_lessons', ['s' => $course->students()->count(), 'l' => $course->lessons()->count()]) }}</p>
  </div>
</div>
<nav class="tabs">
  <a href="{{ route('teach.course', $course) }}" class="{{ Route::is('teach.course') ? 'active' : '' }}">{{ __('teach.content') }}</a>
  <a href="{{ route('teach.attendance', $course) }}" class="{{ Route::is('teach.attendance') ? 'active' : '' }}">{{ __('teach.attendance') }}</a>
  <a href="{{ route('teach.assignments', $course) }}" class="{{ Route::is('teach.assignments') ? 'active' : '' }}">{{ __('teach.assignments') }}</a>
</nav>
