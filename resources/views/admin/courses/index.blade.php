@extends('admin.master')

@section('title', __('dash.courses'))
@section('crumbs', __('dash.courses'))

@section('content')

<div class="admin-title">
  <div>
    <h1>{{ __('dash.courses') }}</h1>
    <p>{{ __('dash.courses_sub') }}</p>
  </div>
  @if ($me->hasAbility('add_course'))
    <div class="actions"><a href="{{ route('admin.courses.create') }}" class="btn btn-primary">@include('includes.icon', ['name' => 'plus', 'size' => 18]) {{ __('dash.new_course') }}</a></div>
  @endif
</div>

<div class="filters">
  <form action="{{ route('admin.courses.index') }}" method="GET">
    <label class="sr-only" for="q">{{ __('dash.search_placeholder') }}</label>
    <input type="search" id="q" name="q" value="{{ request('q') }}" placeholder="{{ __('dash.search_placeholder') }}">
    <button type="submit" class="btn btn-outline">{{ __('dash.filter') }}</button>
  </form>
</div>

<section class="table-box">
  <div class="table-scroll">
    <table class="data-table">
      <thead>
        <tr>
          <th>{{ __('dash.course') }}</th>
          <th>{{ __('dash.category') }}</th>
          <th>{{ __('dash.teacher') }}</th>
          <th>{{ __('dash.students') }}</th>
          <th>{{ __('dash.actions') }}</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($courses as $course)
          @php
            $photo = $course->photo();
          @endphp
          <tr>
            <td><span class="record"><img src="{{ $photo }}" alt="">{{ $course->title }}</span></td>
            <td class="muted">{{ optional(optional($course->teacher)->category)->name_major ?? '—' }}</td>
            <td>
              @if ($course->teacher)
                {{ $course->teacher->name }}
                @if ($me->hasAbility('assign_teacher'))
                  <button type="button" class="missing" style="border:0;padding:2px 6px" data-assign="{{ $course->id }}" data-title="{{ $course->title }}" data-teacher="{{ $course->teacher_id }}">{{ __('dash.edit') }}</button>
                @endif
              @elseif ($me->hasAbility('assign_teacher'))
                <button type="button" class="missing" data-assign="{{ $course->id }}" data-title="{{ $course->title }}">+ {{ __('dash.assign_teacher') }}</button>
              @else
                <span class="muted">{{ __('dash.no_teacher') }}</span>
              @endif
            </td>
            <td>{{ $course->students_count }}</td>
            <td>
              @include('admin.partials.row-actions', [
                'label' => $course->title,
                'edit' => $me->hasAbility('edit_course') ? route('admin.courses.edit', $course->id) : null,
                'destroy' => $me->hasAbility('delete_course') ? route('admin.courses.destroy', $course->id) : null,
              ])
            </td>
          </tr>
        @empty
          <tr><td colspan="5" class="muted" style="text-align:center">{{ __('dash.no_records') }}</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @include('admin.partials.table-foot', ['items' => $courses])
</section>

@if ($me->hasAbility('assign_teacher'))
  <dialog class="sheet" id="assignDialog" aria-labelledby="assignTitle">
    <form action="{{ route('admin.courses.assignTeacher') }}" method="POST">
      @csrf
      <h2 id="assignTitle">{{ __('dash.assign_teacher') }}</h2>
      <input type="hidden" name="course_id" id="assign_course">
      <div class="field">
        <label for="assign_teacher">{{ __('dash.teacher') }}</label>
        <select name="teacher_id" id="assign_teacher" required>
          <option value="">{{ __('dash.choose_teacher') }}</option>
          @foreach ($teachers as $teacher)
            <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="form-actions">
        <button type="button" class="btn btn-outline" data-close>{{ __('dash.cancel') }}</button>
        <button type="submit" class="btn btn-primary">{{ __('dash.assign') }}</button>
      </div>
    </form>
  </dialog>
@endif

@endsection

@section('scripts')
<script>
  (function () {
    var dialog = document.getElementById('assignDialog');
    if (!dialog) return;
    var titleTpl = @json(__('dash.assign_teacher_to', ['name' => '__NAME__']));
    document.querySelectorAll('[data-assign]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        document.getElementById('assign_course').value = btn.dataset.assign;
        document.getElementById('assign_teacher').value = btn.dataset.teacher || '';
        document.getElementById('assignTitle').textContent = titleTpl.replace('__NAME__', btn.dataset.title);
        dialog.showModal();
      });
    });
    dialog.querySelector('[data-close]').addEventListener('click', function () { dialog.close(); });
  })();
</script>
@endsection
