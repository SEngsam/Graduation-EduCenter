@extends('layouts.student')

@section('title', __('qr.enter_title'))

@section('content')

<div class="checkin">
  @if ($session)
    <form method="POST" action="{{ route('attendance.checkin') }}" class="card panel checkin-card">
      @csrf
      <input type="hidden" name="code" value="{{ $code }}">
      <span class="checkin-icon">@include('includes.icon', ['name' => 'qr', 'size' => 40])</span>
      <h1>{{ __('qr.enter_title') }}</h1>
      <p class="muted">{{ __('qr.confirm_text') }}</p>
      <div class="checkin-info">
        <strong>{{ $session->course->title }}</strong>
        <span class="muted">{{ $session->starts_at->translatedFormat('l g:i a') }}@if ($session->room) · <bdi>{{ $session->room }}</bdi>@endif</span>
      </div>
      <button class="btn btn-primary">{{ __('qr.check_in') }}</button>
    </form>
  @else
    <form method="GET" action="{{ route('attendance.here') }}" class="card panel checkin-card">
      <span class="checkin-icon">@include('includes.icon', ['name' => 'qr', 'size' => 40])</span>
      <h1>{{ __('qr.enter_title') }}</h1>
      <p class="muted">{{ __('qr.enter_text') }}</p>
      @if ($code)
        <p class="error-text">{{ __('qr.wrong_code') }}</p>
      @endif
      <label class="field">
        <span class="sr-only">{{ __('qr.code') }}</span>
        <input type="text" name="code" inputmode="numeric" maxlength="7" autocomplete="off" class="code-input" dir="ltr" placeholder="000 000" required autofocus>
      </label>
      <button class="btn btn-dark">{{ __('qr.check_in') }}</button>
    </form>
  @endif
</div>

@endsection
