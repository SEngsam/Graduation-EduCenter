@extends('layouts.auth')

@section('title', __('site.register'))
@section('side_title', __('site.join_title'))
@section('side_text', __('site.join_text'))

@section('content')
<form class="form" method="POST" action="{{ route('register') }}">
  @csrf

  <div class="auth-tabs">
    <a href="{{ route('login') }}">{{ __('site.login') }}</a>
    <a href="{{ route('register') }}" class="active">{{ __('site.register') }}</a>
  </div>

  <h2>{{ __('site.register') }}</h2>

  <div class="field">
    <label for="name">{{ __('site.name') }}</label>
    <input id="name" type="text" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
    @error('name')
      <span class="error">{{ $message }}</span>
    @enderror
  </div>

  <div class="field">
    <label for="email">{{ __('site.email') }}</label>
    <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email">
    @error('email')
      <span class="error">{{ $message }}</span>
    @enderror
  </div>

  <div class="field-row">
    <div class="field">
      <label for="no_phone">{{ __('site.phone') }}</label>
      <input id="no_phone" type="tel" name="no_phone" value="{{ old('no_phone') }}" required autocomplete="tel">
      @error('no_phone')
        <span class="error">{{ $message }}</span>
      @enderror
    </div>
    <div class="field">
      <label for="major">{{ __('site.major') }}</label>
      <input id="major" type="text" name="major" value="{{ old('major') }}" required>
      @error('major')
        <span class="error">{{ $message }}</span>
      @enderror
    </div>
  </div>

  <div class="field-row">
    <div class="field">
      <label for="password">{{ __('site.password') }}</label>
      <input id="password" type="password" name="password" required autocomplete="new-password">
      @error('password')
        <span class="error">{{ $message }}</span>
      @enderror
    </div>
    <div class="field">
      <label for="password-confirm">{{ __('site.password_confirm') }}</label>
      <input id="password-confirm" type="password" name="password_confirmation" required autocomplete="new-password">
    </div>
  </div>

  <button type="submit" class="btn btn-primary btn-block">{{ __('site.register') }}</button>

  <p class="auth-foot">{{ __('site.have_account') }} <a href="{{ route('login') }}"><strong>{{ __('site.login') }}</strong></a></p>
</form>
@endsection
