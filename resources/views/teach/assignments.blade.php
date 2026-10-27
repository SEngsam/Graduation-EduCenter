@extends('layouts.teacher')

@section('title', __('teach.assignments') . ' · ' . $course->title)

@section('content')

@include('teach.tabs')

<div class="split">
  <div class="split-side stack">
    @forelse ($assignments as $item)
      <a href="{{ route('teach.assignments', [$course, 'a' => $item->id]) }}" class="card assignment-item {{ $current && $current->id == $item->id ? 'active' : '' }}">
        <strong>{{ $item->title }}</strong>
        <small>{{ $item->isOverdue() ? __('teach.overdue') : __('teach.due_at') . ' ' . $item->due_at->translatedFormat('l j F') }}</small>
        @php
          $percent = $students->count() ? round($item->submissions_count * 100 / $students->count()) : 0;
        @endphp
        <span class="bar-row">
          <span class="bar"><span style="width: {{ $percent }}%"></span></span>
          <small>{{ $item->submissions_count }}/{{ $students->count() }}</small>
        </span>
      </a>
    @empty
      <p class="muted">{{ __('teach.no_assignments') }}</p>
    @endforelse

    <form method="POST" action="{{ route('teach.assignments.store', $course) }}" class="card panel">
      @csrf
      <h2>{{ __('teach.new_assignment') }}</h2>
      <label class="field">
        <span>{{ __('teach.assignment_title') }}</span>
        <input type="text" name="title" value="{{ old('title') }}" required>
        @error('title')<small class="error">{{ $message }}</small>@enderror
      </label>
      <label class="field">
        <span>{{ __('teach.description') }}</span>
        <textarea name="description" rows="3">{{ old('description') }}</textarea>
      </label>
      <label class="field">
        <span>{{ __('teach.due_at') }}</span>
        <input type="datetime-local" name="due_at" value="{{ old('due_at') }}" required>
        @error('due_at')<small class="error">{{ $message }}</small>@enderror
      </label>
      <label class="field">
        <span>{{ __('teach.max_grade') }}</span>
        <input type="number" name="max_grade" value="{{ old('max_grade', 20) }}" min="1" max="100" required>
      </label>
      <button class="btn btn-primary">{{ __('teach.create') }}</button>
    </form>
  </div>

  @if ($current)
    <section class="card split-main">
      <div class="assignment-head">
        <div class="assignment-title">
          <h2>{{ $current->title }}</h2>
          <form method="POST" action="{{ route('teach.assignments.destroy', $current) }}" data-confirm="{{ __('teach.confirm_delete_assignment', ['name' => $current->title]) }}" data-confirm-ok="{{ __('teach.delete') }}">
            @csrf
            @method('DELETE')
            <button class="btn btn-outline btn-sm">{{ __('teach.delete') }}</button>
          </form>
        </div>
        <span class="meta-line">
          @include('includes.icon', ['name' => 'clock', 'size' => 16]) {{ __('teach.due_at') }}: {{ $current->due_at->translatedFormat('l j F، g:i a') }}
          · {{ __('teach.max_grade') }} {{ $current->max_grade }}
        </span>
        @if ($current->description)
          <p>{{ $current->description }}</p>
        @endif
      </div>

      @php
        $graded = 0;
        $waiting = 0;
        foreach ($submissions as $s) {
            if ($s->grade !== null) {
                $graded++;
            } else {
                $waiting++;
            }
        }
        $missing = $students->count() - count($submissions);
      @endphp

      <div class="chips">
        @foreach (['all' => __('teach.filter_all') . ' ' . $students->count(), 'pending' => __('teach.filter_pending') . ' ' . $waiting, 'graded' => __('teach.filter_graded') . ' ' . $graded, 'missing' => __('teach.filter_missing') . ' ' . $missing] as $key => $label)
          <a href="{{ route('teach.assignments', [$course, 'a' => $current->id, 'show' => $key]) }}" class="chip {{ $filter == $key ? 'active' : '' }}">{{ $label }}</a>
        @endforeach
      </div>

      <div class="table-scroll">
        <table class="table">
          <thead>
            <tr>
              <th>{{ __('teach.student') }}</th>
              <th>{{ __('teach.submitted_at') }}</th>
              <th>{{ __('teach.file') }}</th>
              <th>{{ __('teach.status') }}</th>
              <th>{{ __('teach.grade_col') }}</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($students as $student)
              @php
                $sub = $submissions[$student->id] ?? null;
                if (!$sub) {
                    $state = 'missing';
                } elseif ($sub->grade === null) {
                    $state = 'pending';
                } else {
                    $state = 'graded';
                }
              @endphp
              @if ($filter == 'all' || $filter == $state)
                <tr>
                  <td><span class="person"><span class="avatar sm">{{ mb_substr($student->name, 0, 1) }}</span> {{ $student->name }}</span></td>
                  <td class="muted">
                    {{ $sub ? $sub->created_at->translatedFormat('j M، g:i a') : '—' }}
                    @if ($sub && $sub->isLate())<span class="pill warn">{{ __('teach.late_badge') }}</span>@endif
                  </td>
                  <td>
                    @if ($sub)
                      <a href="{{ route('teach.submissions.file', $sub) }}" class="file-chip" dir="ltr">@include('includes.icon', ['name' => 'file', 'size' => 16]) {{ Str::limit($sub->original_name, 24) }}</a>
                    @else
                      —
                    @endif
                  </td>
                  <td>
                    @if ($state == 'missing')
                      <span class="pill bad">{{ __('teach.missing') }}</span>
                    @elseif ($state == 'pending')
                      <span class="pill warn">{{ __('teach.pending') }}</span>
                    @else
                      <span class="pill ok">{{ __('teach.graded') }}</span>
                    @endif
                  </td>
                  <td>
                    @if ($sub)
                      <form method="POST" action="{{ route('teach.submissions.grade', $sub) }}" class="grade-form">
                        @csrf
                        @method('PUT')
                        <label class="sr-only" for="grade-{{ $sub->id }}">{{ __('teach.grade_col') }}</label>
                        <input type="number" id="grade-{{ $sub->id }}" name="grade" value="{{ $sub->grade !== null ? (float) $sub->grade : '' }}" min="0" max="{{ $current->max_grade }}" step="0.5" required>
                        <span class="muted">/ {{ $current->max_grade }}</span>
                        <label class="sr-only" for="fb-{{ $sub->id }}">{{ __('teach.feedback') }}</label>
                        <input type="text" id="fb-{{ $sub->id }}" name="feedback" value="{{ $sub->feedback }}" placeholder="{{ __('teach.feedback') }}">
                        <button class="btn btn-outline btn-sm">{{ __('teach.save_grade') }}</button>
                      </form>
                    @endif
                  </td>
                </tr>
              @endif
            @endforeach
          </tbody>
        </table>
      </div>
    </section>
  @endif
</div>

@endsection
