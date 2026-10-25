@extends('admin.master')

@section('title', __('dash.overview'))

@section('content')

<div class="admin-title">
  <div>
    <h1>{{ __('dash.overview') }}</h1>
    <p>{{ __('dash.overview_sub') }}</p>
  </div>
  <div class="actions">
    @if ($user->hasAbility('add_teacher'))
      <a href="{{ route('admin.teachers.create') }}" class="btn btn-outline">{{ __('dash.new_teacher') }}</a>
    @endif
    @if ($user->hasAbility('add_course'))
      <a href="{{ route('admin.courses.create') }}" class="btn btn-primary">@include('includes.icon', ['name' => 'plus', 'size' => 18]) {{ __('dash.new_course') }}</a>
    @endif
  </div>
</div>

<div class="stat-grid">
  @if ($user->hasAbility('all_students'))
    <div class="card stat"><span>{{ __('dash.stat_students') }}</span><strong>{{ $stats['students'] }}</strong></div>
  @endif
  @if ($user->hasAbility('all_courses'))
    <a href="{{ route('admin.courses.index') }}" class="card stat"><span>{{ __('dash.stat_courses') }}</span><strong>{{ $stats['courses'] }}</strong>
      @if ($unassigned->count())<small>{{ $unassigned->count() }} {{ __('dash.no_teacher') }}</small>@endif
    </a>
  @endif
  @if ($user->hasAbility('all_teachers'))
    <a href="{{ route('admin.teachers.index') }}" class="card stat"><span>{{ __('dash.stat_teachers') }}</span><strong>{{ $stats['teachers'] }}</strong></a>
  @endif
  @if ($user->hasAbility('all_categories'))
    <a href="{{ route('admin.categories.index') }}" class="card stat"><span>{{ __('dash.stat_categories') }}</span><strong>{{ $stats['categories'] }}</strong></a>
  @endif
  @if ($user->hasAbility('all_events'))
    <a href="{{ route('admin.events.index') }}" class="card stat"><span>{{ __('dash.stat_events') }}</span><strong>{{ $stats['events'] }}</strong></a>
  @endif
</div>

@if ($teachingCourses->count())
  <section class="table-box">
    <div class="table-foot" style="border-top:0"><strong style="color:var(--ink);font-size:18px">{{ __('dash.my_teaching') }}</strong></div>
    <div class="table-scroll">
      <table class="data-table">
        <thead><tr><th>{{ __('dash.course') }}</th><th>{{ __('dash.students') }}</th></tr></thead>
        <tbody>
          @foreach ($teachingCourses as $course)
            <tr><td><span class="record">{{ $course->title }}</span></td><td>{{ $course->students_count }}</td></tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </section>
@endif

@if ($user->hasAbility('all_courses'))
<div class="overview-grid">
  <section class="table-box wide">
    <div class="table-foot" style="border-top:0">
      <strong style="color:var(--ink);font-size:18px">{{ __('dash.recent_registrations') }}</strong>
      <a href="{{ route('admin.courses.index') }}">{{ __('dash.view_all') }}</a>
    </div>
    <div class="table-scroll">
      <table class="data-table">
        <thead><tr><th>{{ __('dash.student') }}</th><th>{{ __('dash.course') }}</th><th>{{ __('dash.teacher') }}</th><th>{{ __('dash.date') }}</th></tr></thead>
        <tbody>
          @forelse ($registrations as $reg)
            <tr>
              <td><span class="record">{{ optional($reg->students)->name ?? '—' }}</span></td>
              <td>{{ optional($reg->course)->title ?? '—' }}</td>
              <td>{{ optional($reg->teacher)->name ?? __('dash.no_teacher') }}</td>
              <td class="muted">{{ optional($reg->created_at)->translatedFormat('j M Y') }}</td>
            </tr>
          @empty
            <tr><td colspan="4" class="muted" style="text-align:center">{{ __('dash.no_enrollments') }}</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </section>

  <section class="card panel narrow">
    <h2 style="font-size:20px">{{ __('dash.no_teacher_courses') }}</h2>
    <p style="margin:0;color:var(--ink-subtle);font-size:14px">{{ __('dash.no_teacher_courses_sub') }}</p>
    @forelse ($unassigned as $course)
      <div class="todo">
        <strong>{{ $course->title }}</strong>
        @if ($user->hasAbility('assign_teacher'))
          <form action="{{ route('admin.courses.assignTeacher') }}" method="POST">
            @csrf
            <input type="hidden" name="course_id" value="{{ $course->id }}">
            <label class="sr-only" for="t{{ $course->id }}">{{ __('dash.teacher') }}</label>
            <select id="t{{ $course->id }}" name="teacher_id" required>
              <option value="">{{ __('dash.choose_teacher') }}</option>
              @foreach ($teachers as $t)
                <option value="{{ $t->id }}">{{ $t->name }}</option>
              @endforeach
            </select>
            <button type="submit" class="btn btn-dark btn-sm">{{ __('dash.assign') }}</button>
          </form>
        @endif
      </div>
    @empty
      <p class="pill">{{ __('dash.all_set') }}</p>
    @endforelse
  </section>
</div>
@endif

@endsection
