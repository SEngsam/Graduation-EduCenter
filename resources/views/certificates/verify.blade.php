@extends('site.master')

@section('title', __('cert.verify_title') . ' | ' . config('app.name'))

@section('content')

@include('includes.page-head', ['title' => __('cert.verify_title'), 'intro' => __('cert.verify_hint')])

<section class="section">
  <div class="container verify-wrap">
    @if ($certificate)
      <div class="card verify-card">
        <span class="verify-icon ok">@include('includes.icon', ['name' => 'check', 'size' => 32])</span>
        <h2>{{ __('cert.valid') }}</h2>
        <p class="muted">{{ __('cert.valid_text') }}</p>
        <dl class="verify-list">
          <div><dt>{{ __('cert.name') }}</dt><dd>{{ $certificate->student->name }}</dd></div>
          <div><dt>{{ __('cert.course') }}</dt><dd>{{ $certificate->course->title }}</dd></div>
          <div><dt>{{ __('cert.teacher') }}</dt><dd>{{ optional($certificate->course->teacher)->name ?? '—' }}</dd></div>
          <div><dt>{{ __('cert.issued') }}</dt><dd>{{ $certificate->created_at->translatedFormat('j F Y') }}</dd></div>
          <div><dt>{{ __('cert.code') }}</dt><dd dir="ltr">{{ $certificate->code }}</dd></div>
        </dl>
      </div>
    @else
      <div class="card verify-card">
        @if ($code)
          <span class="verify-icon bad">@include('includes.icon', ['name' => 'alert', 'size' => 32])</span>
          <h2>{{ __('cert.invalid') }}</h2>
          <p class="muted">{{ __('cert.invalid_text') }}</p>
        @endif
        <form method="GET" action="{{ route('certificate.verify') }}" class="verify-form">
          <label class="sr-only" for="code">{{ __('cert.code') }}</label>
          <input type="text" id="code" name="code" value="{{ $code }}" dir="ltr" placeholder="EC-2026-XXXXXX" required>
          <button class="btn btn-dark">{{ __('cert.check') }}</button>
        </form>
      </div>
    @endif
  </div>
</section>

@endsection
