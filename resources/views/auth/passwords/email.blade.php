@extends('layouts.auth')

@section('title', __('site.reset_title'))
@section('side_title', __('site.reset_title'))
@section('side_text', __('site.reset_text'))

@section('content')
<form class="form" method="POST" action="{{ route('password.email') }}">
  @csrf

  <h2>{{ __('site.reset_title') }}</h2>
  <p style="margin:-6px 0 0;color:var(--ink-subtle)">{{ __('site.reset_text') }}</p>

  @if (session('status'))
    <div class="flash" role="status" style="margin:0">{{ session('status') }}</div>
  @endif

  <div class="field">
    <label for="email">{{ __('site.email') }}</label>
    <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
    @error('email')
      <span class="error">{{ $message }}</span>
    @enderror
  </div>

  <button type="submit" class="btn btn-primary btn-block">{{ __('site.send_link') }}</button>

  <p class="auth-foot"><a href="{{ route('login') }}">{{ __('site.back_to_login') }}</a></p>
</form>
@endsection
