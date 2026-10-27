@extends('layouts.teacher')

@section('title', __('teach.attendance_title') . ' · ' . $course->title)

@section('content')

@include('teach.tabs')

<form method="GET" action="{{ route('teach.attendance', $course) }}" class="card inline-form">
  <label class="field"><span>{{ __('teach.date') }}</span><input type="date" name="date" value="{{ $date }}"></label>
  <label class="field"><span>{{ __('teach.time') }}</span><input type="time" name="time" value="{{ $time }}"></label>
  <button class="btn btn-outline">{{ __('teach.open') }}</button>
</form>

<div class="split">
  <form method="POST" action="{{ route('teach.attendance.store', $course) }}" class="card split-main roster">
    @csrf
    <input type="hidden" name="date" value="{{ $date }}">
    <input type="hidden" name="time" value="{{ $time }}">
    <input type="hidden" name="room" value="{{ $slot->room ?? '' }}">

    @if ($students->count())
      <div class="roster-head">
        <button type="button" class="btn btn-outline btn-sm" id="all-present">@include('includes.icon', ['name' => 'check', 'size' => 16]) {{ __('teach.all_present') }}</button>
      </div>

      <div class="table-scroll">
        <table class="table">
          <thead>
            <tr>
              <th>#</th>
              <th>{{ __('teach.student') }}</th>
              <th>{{ __('teach.status') }}</th>
              <th>{{ __('teach.note') }}</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($students as $student)
              @php
                $mark = $marks[$student->id] ?? null;
                $current = $mark ? $mark->status : 'present';
              @endphp
              <tr>
                <td class="muted">{{ $loop->iteration }}</td>
                <td>
                  <span class="person"><span class="avatar sm">{{ mb_substr($student->name, 0, 1) }}</span> {{ $student->name }}</span>
                </td>
                <td>
                  <fieldset class="seg">
                    <legend class="sr-only">{{ $student->name }}</legend>
                    @foreach (['present', 'late', 'absent', 'excused'] as $status)
                      <label class="seg-{{ $status }}">
                        <input type="radio" name="status[{{ $student->id }}]" value="{{ $status }}" {{ $current == $status ? 'checked' : '' }}>
                        <span>{{ __('teach.' . $status) }}</span>
                      </label>
                    @endforeach
                  </fieldset>
                </td>
                <td>
                  <label class="sr-only" for="note-{{ $student->id }}">{{ __('teach.note') }}</label>
                  <input type="text" id="note-{{ $student->id }}" name="note[{{ $student->id }}]" value="{{ $mark->note ?? '' }}" class="note-input">
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <div class="roster-foot">
        <span class="muted">{{ $session ? __('teach.saved_at', ['time' => $session->updated_at->format('g:i')]) : __('teach.not_saved') }}</span>
        <button class="btn btn-primary">{{ __('teach.save_attendance') }}</button>
      </div>
    @else
      @include('includes.empty', ['icon' => 'users', 'text' => __('teach.no_students')])
    @endif
  </form>

  <section class="card panel split-side">
    <h2>{{ __('teach.past_sessions') }}</h2>
    @forelse ($past as $item)
      @php
        $rate = $item->rate();
      @endphp
      <a href="{{ route('teach.attendance', [$course, 'date' => $item->starts_at->toDateString(), 'time' => $item->starts_at->format('H:i')]) }}" class="past-row">
        <span class="past-top">
          <span>{{ $item->starts_at->translatedFormat('l j F') }}</span>
          <span>{{ $rate !== null ? $rate . '٪' : '—' }}</span>
        </span>
        <span class="bar"><span style="width: {{ $rate ?? 0 }}%"></span></span>
      </a>
    @empty
      <p class="muted">{{ __('teach.no_sessions') }}</p>
    @endforelse
  </section>
</div>

<script>
  var allBtn = document.getElementById('all-present');
  if (allBtn) {
    allBtn.addEventListener('click', function () {
      document.querySelectorAll('.roster input[value=present]').forEach(function (r) {
        r.checked = true;
      });
    });
  }
</script>

@endsection
