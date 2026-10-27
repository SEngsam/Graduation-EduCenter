@php
    $tones = ['science' => ['sci', 'flask'], 'arts' => ['art', 'palette'], 'technology' => ['tech', 'screen']];
    $major = strtolower(optional(optional($item->teacher)->category)->name_major ?? '');
    [$tone, $icon] = $tones[$major] ?? ['', 'book'];
    $photo = $item->photo();
    $enrolled = $item->is_enrolled ?? false;
@endphp
<article class="card course-card {{ $enrolled ? 'is-enrolled' : '' }}">
  <div class="thumb {{ $tone ? 'tone-' . $tone : '' }}">
    <img src="{{ $photo }}" alt="">
    @if ($enrolled)
      <span class="flag">{{ __('site.enrolled') }}</span>
    @endif
  </div>
  <div class="course-body">
    @if ($tone)
      <span class="tag tone-{{ $tone }}">{{ $item->teacher->category->name_major }}</span>
    @endif
    <h3><a href="{{ route('site.course', $item->id) }}" class="stretch">{{ $item->title }}</a></h3>
    <p>{{ Str::limit($item->description, 90) }}</p>
    @isset($item->students_count)
      <div class="course-meta">{{ __('site.students_count', ['count' => $item->students_count]) }}</div>
    @endisset
    <div class="course-foot">
      @if ($item->teacher)
        <img class="avatar" src="{{ $item->teacher->photo() }}" alt="">
        <span class="teacher">{{ $item->teacher->name }}</span>
      @else
        <span class="teacher" style="color:var(--ink-subtle)">{{ __('site.no_teacher') }}</span>
      @endif

      @if (auth()->check() && (auth()->user()->hasRole(\App\Models\Role::ROLE_STUDENT) || auth()->user()->hasAbility('enroll_courses')))
        @if ($enrolled)
          <form action="{{ route('courses.removeEnrollment', $item->id) }}" method="POST" data-confirm="{{ __('site.confirm_unenroll', ['name' => $item->title]) }}" data-confirm-ok="{{ __('site.unenroll') }}">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger-soft btn-sm">{{ __('site.unenroll') }}</button>
          </form>
        @elseif (! $item->teacher)
          <span class="tag">{{ __('site.soon') }}</span>
        @else
          <form action="{{ route('courses.enroll', $item->id) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-primary btn-sm">{{ __('site.enroll') }}</button>
          </form>
        @endif
      @endif
    </div>
  </div>
</article>
