@extends($layout)

@section('title', __('site.my_schedule'))

@section('content')

@php
  $tones = ['science' => 'sci', 'arts' => 'art', 'technology' => 'tech'];
  $rows = ($lastHour - $firstHour) * 2;
  $today = now()->dayOfWeek;
@endphp

<div class="page-title">
  <div>
    <h1>{{ __('site.my_schedule') }}</h1>
    <p>{{ __('site.schedule_sub') }}</p>
  </div>
</div>

@if ($slots->count())
  <section class="card week-wrap">
    <div class="week" style="grid-template-columns: 64px repeat({{ count($days) }}, minmax(120px, 1fr)); grid-template-rows: 52px repeat({{ $rows }}, 34px);">
      @foreach ($days as $i => $day)
        <div class="week-day {{ $day == $today ? 'today' : '' }}" style="grid-column: {{ $i + 2 }}; grid-row: 1;">
          {{ __('teach.days')[$day] }}
          @if ($day == $today)<small>{{ __('notify.today') }}</small>@endif
        </div>
      @endforeach

      @for ($h = $firstHour; $h < $lastHour; $h++)
        <div class="week-hour" style="grid-column: 1; grid-row: {{ ($h - $firstHour) * 2 + 2 }} / span 2;">{{ date('g:i', mktime($h, 0)) }}</div>
        @foreach ($days as $i => $day)
          <div class="week-cell" style="grid-column: {{ $i + 2 }}; grid-row: {{ ($h - $firstHour) * 2 + 2 }} / span 2;"></div>
        @endforeach
      @endfor

      @foreach ($slots as $slot)
        @php
          $col = array_search($slot->day, $days);
          $start = intdiv($slot->minutes() - $firstHour * 60, 30) + 2;
          $span = max(1, intdiv($slot->minutes('ends_at') - $slot->minutes(), 30));
          $category = strtolower(optional(optional($slot->course->teacher)->category)->name_major ?? '');
          $tone = $tones[$category] ?? 'sci';
          $link = auth()->user()->hasRole(\App\Models\Role::ROLE_STUDENT) ? route('student.course', $slot->course_id) : route('teach.course', $slot->course_id);
        @endphp
        @if ($col !== false)
          <a href="{{ $link }}" class="week-block tone-{{ $tone }}" style="grid-column: {{ $col + 2 }}; grid-row: {{ $start }} / span {{ $span }};">
            <strong>{{ $slot->course->title }}</strong>
            <small dir="ltr">{{ $slot->time() }} - {{ $slot->time('ends_at') }}</small>
            @if ($slot->room)<small>{{ $slot->room }}</small>@endif
          </a>
        @endif
      @endforeach
    </div>
  </section>
@else
  @include('includes.empty', ['icon' => 'calendar', 'text' => __('site.no_schedule')])
@endif

@endsection
