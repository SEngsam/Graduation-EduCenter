@extends('layouts.auth')

@section('title', __('site.confirm_title'))
@section('side_title', __('site.confirm_title'))
@section('side_text', __('site.confirm_text'))

@section('content')
<form class="form" method="POST" action="{{ route('password.confirm') }}">
  @csrf

  <h2>{{ __('site.confirm_title') }}</h2>
  <p style="margin:-6px 0 0;color:var(--ink-subtle)">{{ __('site.confirm_text') }}</p>

  <div class="field">
    <label for="password">{{ __('site.password') }}</label>
    <input id="password" type="password" name="password" required autocomplete="current-password" autofocus>
    @error('password')
      <span class="error">{{ $message }}</span>
    @enderror
  </div>

  <button type="submit" class="btn btn-dark btn-block">{{ __('site.confirm_btn') }}</button>

  @if (Route::has('password.request'))
    <p class="auth-foot"><a href="{{ route('password.request') }}">{{ __('site.forgot') }}</a></p>
  @endif
</form>
@endsection
