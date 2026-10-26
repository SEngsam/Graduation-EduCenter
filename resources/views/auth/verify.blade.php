@extends('layouts.auth')

@section('title', __('site.verify_title'))
@section('side_title', __('site.verify_title'))
@section('side_text', __('site.verify_text'))

@section('content')
<div class="form">
  <span class="icon-box" style="width:56px;height:56px;background:var(--orange-soft);color:var(--orange-text)">@include('includes.icon', ['name' => 'mail', 'size' => 28])</span>
  <h2>{{ __('site.verify_title') }}</h2>
  <p style="margin:0;color:var(--ink-muted)">{{ __('site.verify_text') }}</p>

  @if (session('resent'))
    <div class="flash" role="status" style="margin:0">{{ __('site.resent') }}</div>
  @endif

  <form method="POST" action="{{ route('verification.resend') }}" style="margin:0">
    @csrf
    <button type="submit" class="btn btn-outline btn-block">{{ __('site.resend') }}</button>
  </form>
</div>
@endsection
