@extends('layouts.auth')

@section('title', __('site.login'))
@section('side_title', __('site.welcome_back'))
@section('side_text', __('site.welcome_back_text'))

@section('content')
<form class="form" method="POST" action="{{ route('login') }}">
  @csrf

  <div class="auth-tabs">
    <a href="{{ route('login') }}" class="active">{{ __('site.login') }}</a>
    <a href="{{ route('register') }}">{{ __('site.register') }}</a>
  </div>

  <h2>{{ __('site.login') }}</h2>

  <div class="field">
    <label for="email">{{ __('site.email') }}</label>
    <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
    @error('email')
      <span class="error">{{ $message }}</span>
    @enderror
  </div>

  <div class="field">
    <label for="password">{{ __('site.password') }}</label>
    <input id="password" type="password" name="password" required autocomplete="current-password">
    @error('password')
      <span class="error">{{ $message }}</span>
    @enderror
  </div>

  <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap">
    <label class="check">
      <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
      <span>{{ __('site.remember') }}</span>
    </label>
    @if (Route::has('password.request'))
      <a href="{{ route('password.request') }}" style="font-size:15px">{{ __('site.forgot') }}</a>
    @endif
  </div>

  <button type="submit" class="btn btn-dark btn-block">{{ __('site.login') }}</button>

  <p class="auth-foot">{{ __('site.no_account') }} <a href="{{ route('register') }}"><strong>{{ __('site.register') }}</strong></a></p>
</form>
@endsection
