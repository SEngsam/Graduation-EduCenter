@extends('layouts.student')

@section('title', __('qr.enter_title'))

@section('content')

<div class="checkin">
  <div class="card panel checkin-card">
    <span class="checkin-icon done {{ $attendance->status == 'late' ? 'late' : '' }}">@include('includes.icon', ['name' => 'check', 'size' => 44])</span>
    @if ($attendance->wasRecentlyCreated || $attendance->wasChanged())
      <h1>{{ $attendance->status == 'late' ? __('qr.done_late') : __('qr.done_present') }}</h1>
    @else
      <h1>{{ __('qr.done_other') }}</h1>
    @endif
    <p class="muted">{{ $session->course->title }}<br>{{ $session->starts_at->translatedFormat('l') }} {{ now()->format('g:i') }}@if ($session->room) · <bdi>{{ $session->room }}</bdi>@endif</p>
    @if ($rate !== null)
      <div class="checkin-rate">
        <span class="muted">{{ __('qr.your_rate') }}</span>
        <strong>{{ $rate }}٪</strong>
      </div>
    @endif
    <a href="{{ route('student.course', $session->course_id) }}" class="btn btn-primary">{{ __('qr.back') }}</a>
  </div>
</div>

@endsection
