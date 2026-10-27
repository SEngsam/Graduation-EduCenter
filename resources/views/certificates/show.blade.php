@php
  $rtl = app()->getLocale() == 'ar';
  $verifyUrl = route('certificate.verify', $certificate->code);
  if ($certificate->grade !== null && $certificate->attendance !== null) {
      $stats = __('cert.with_stats', ['attendance' => $certificate->attendance, 'grade' => (float) $certificate->grade]);
  } elseif ($certificate->grade !== null) {
      $stats = __('cert.with_grade', ['grade' => (float) $certificate->grade]);
  } elseif ($certificate->attendance !== null) {
      $stats = __('cert.with_attendance', ['attendance' => $certificate->attendance]);
  } else {
      $stats = '';
  }
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $rtl ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ __('cert.title') }} · {{ $certificate->student->name }}</title>
  <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600&family=Readex+Pro:wght@500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/tokens.css') }}">
  <link rel="stylesheet" href="{{ asset('css/certificate.css') }}">
</head>
<body>
  <div class="cert-tools">
    <a href="{{ route('student.course', $certificate->course_id) }}">{{ __('cert.back') }}</a>
    <button type="button" onclick="window.print()">{{ __('cert.print') }}</button>
  </div>

  <main class="cert-page">
    <div class="cert">
      <div class="cert-top">
        <span class="cert-brand">
          <span class="cert-logo">@include('includes.icon', ['name' => 'cap', 'size' => 22])</span>
          <span><strong>EduCenter</strong><small>{{ __('site.brand_sub') }}</small></span>
        </span>
        <span class="cert-code" dir="ltr">{{ $certificate->code }}</span>
      </div>

      <div class="cert-body">
        <span class="cert-kicker">{{ __('cert.title') }}</span>
        <span class="cert-line">{{ __('cert.certifies') }}</span>
        <span class="cert-name">{{ $certificate->student->name }}</span>
        <span class="cert-rule"></span>
        <span class="cert-line">{!! __('cert.completed', ['course' => e($certificate->course->title)]) !!}@if ($stats)، {{ $stats }}@endif.</span>
      </div>

      <div class="cert-foot">
        <div class="cert-sign">
          <span class="sign-name">{{ optional($certificate->course->teacher)->name }}</span>
          <span class="sign-line"></span>
          <small>{{ __('cert.teacher') }}</small>
        </div>
        <div class="cert-qr">
          {!! \App\Support\Qr::svg($verifyUrl) !!}
          <small dir="ltr">{{ $verifyUrl }}</small>
        </div>
        <div class="cert-sign">
          <span class="sign-name">{{ $certificate->created_at->translatedFormat('j F Y') }}</span>
          <span class="sign-line"></span>
          <small>{{ __('cert.issued') }}</small>
        </div>
      </div>
    </div>
  </main>
</body>
</html>
