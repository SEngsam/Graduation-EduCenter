@php
  $maxCount = 1;
  foreach ($enrollments as $month) {
      $maxCount = max($maxCount, $month['count']);
  }
  $step = $maxCount <= 4 ? 1 : ceil($maxCount / 4);
  $top = $step * ceil($maxCount / $step);
  $chartH = 220;
  $barW = 40;
  $gap = $months > 6 ? 20 : 44;
  $chartW = 50 + count($enrollments) * ($barW + $gap);
@endphp

<div class="stat-grid">
  <div class="card stat">
    <span>{{ __('dash.new_enrollments') }}</span>
    <strong>{{ $stats['enrollments'] }}</strong>
    @if ($stats['enrollments_change'] !== null)
      <small class="{{ $stats['enrollments_change'] >= 0 ? 'up' : 'down' }}" dir="ltr">{{ __('dash.vs_before', ['value' => ($stats['enrollments_change'] >= 0 ? '+' : '') . $stats['enrollments_change'] . '%']) }}</small>
    @endif
  </div>
  <div class="card stat">
    <span>{{ __('dash.attendance_rate') }}</span>
    <strong>{{ $stats['attendance'] !== null ? $stats['attendance'] . '%' : '—' }}</strong>
    @if ($stats['attendance_change'] !== null)
      <small class="{{ $stats['attendance_change'] >= 0 ? 'up' : 'down' }}">{{ __('dash.vs_before', ['value' => ($stats['attendance_change'] >= 0 ? '+' : '') . $stats['attendance_change'] . '%']) }}</small>
    @endif
  </div>
  <div class="card stat">
    <span>{{ __('dash.submission_rate') }}</span>
    <strong>{{ $stats['submissions'] !== null ? $stats['submissions'] . '%' : '—' }}</strong>
  </div>
  <div class="card stat">
    <span>{{ __('dash.avg_grade') }}</span>
    <strong dir="ltr">{{ $stats['grade'] !== null ? $stats['grade'] . ' / 20' : '—' }}</strong>
  </div>
</div>

<div class="report-row">
  <section class="card panel report-chart">
    <h2>{{ __('dash.enrollments_chart') }}</h2>
    <svg viewBox="0 -24 {{ $chartW }} {{ $chartH + 64 }}" role="img" aria-label="{{ __('dash.enrollments_chart_label') }}">
      @for ($v = 0; $v <= $top; $v += $step)
        @php
          $y = $chartH - $v * $chartH / $top + 10;
        @endphp
        <line x1="40" x2="{{ $chartW }}" y1="{{ $y }}" y2="{{ $y }}" class="grid-line"></line>
        <text x="30" y="{{ $y + 4 }}" text-anchor="end" class="axis">{{ $v }}</text>
      @endfor
      @foreach ($enrollments as $i => $month)
        @php
          $x = 56 + $i * ($barW + $gap);
          $h = $month['count'] * $chartH / $top;
          $y = $chartH - $h + 10;
          $isLast = $loop->last;
        @endphp
        @if ($h > 0)
          <path d="M{{ $x }} {{ $chartH + 10 }} V{{ $y + 4 }} Q{{ $x }} {{ $y }} {{ $x + 4 }} {{ $y }} H{{ $x + $barW - 4 }} Q{{ $x + $barW }} {{ $y }} {{ $x + $barW }} {{ $y + 4 }} V{{ $chartH + 10 }} Z" class="{{ $isLast ? 'bar-now' : 'bar' }}">
            <title>{{ $month['label'] }}: {{ $month['count'] }}</title>
          </path>
        @endif
        @if ($isLast || $month['count'] == $maxCount)
          <text x="{{ $x + $barW / 2 }}" y="{{ $y - 8 }}" text-anchor="middle" class="value">{{ $month['count'] }}</text>
        @endif
        <text x="{{ $x + $barW / 2 }}" y="{{ $chartH + 30 }}" text-anchor="middle" class="axis">{{ $month['label'] }}</text>
      @endforeach
    </svg>
  </section>

  <section class="card panel report-bars">
    <h2>{{ __('dash.attendance_by_course') }}</h2>
    @foreach ($courses as $row)
      @if ($row['attendance'] !== null)
        <div class="hbar">
          <span class="hbar-top"><span>{{ $row['title'] }}</span><strong>{{ $row['attendance'] }}%</strong></span>
          <span class="bar"><span style="width: {{ $row['attendance'] }}%"></span></span>
        </div>
      @endif
    @endforeach
    @if (collect($courses)->whereNotNull('attendance')->isEmpty())
      <p class="muted">{{ __('dash.no_data') }}</p>
    @endif
  </section>
</div>

<section class="card">
  <h2 class="table-title">{{ __('dash.course_performance') }}</h2>
  <div class="table-scroll">
    <table class="table">
      <thead>
        <tr>
          <th>{{ __('dash.course') }}</th>
          <th>{{ __('dash.teacher') }}</th>
          <th>{{ __('dash.students') }}</th>
          <th>{{ __('dash.avg_grade') }}</th>
          <th>{{ __('dash.attendance_rate') }}</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($courses as $row)
          @php
            $weak = ($row['attendance'] !== null && $row['attendance'] < 75) || ($row['grade'] !== null && $row['grade'] < 12);
          @endphp
          <tr>
            <td><strong>{{ $row['title'] }}</strong> @if ($weak)<span class="pill warn">{{ __('dash.needs_attention') }}</span>@endif</td>
            <td>{{ $row['teacher'] }}</td>
            <td>{{ $row['students'] }}</td>
            <td dir="ltr" style="text-align:start">{{ $row['grade'] !== null ? $row['grade'] . ' / 20' : '—' }}</td>
            <td>{{ $row['attendance'] !== null ? $row['attendance'] . '%' : '—' }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</section>
