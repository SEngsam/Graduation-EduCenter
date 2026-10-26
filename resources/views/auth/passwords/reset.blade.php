@extends('layouts.auth')

@section('title', __('site.set_password'))
@section('side_title', __('site.set_password'))
@section('side_text', __('site.set_password_text'))

@section('content')
<form class="form" method="POST" action="{{ route('password.update') }}">
  @csrf
  <input type="hidden" name="token" value="{{ $token }}">

  <h2>{{ __('site.set_password') }}</h2>

  <div class="field">
    <label for="email">{{ __('site.email') }}</label>
    <input id="email" type="email" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email">
    @error('email')
      <span class="error">{{ $message }}</span>
    @enderror
  </div>

  <div class="field">
    <label for="password">{{ __('site.new_password') }}</label>
    <input id="password" type="password" name="password" required autocomplete="new-password" placeholder="{{ __('site.min_8') }}" autofocus>
    @error('password')
      <span class="error">{{ $message }}</span>
    @enderror
  </div>

  <div class="field">
    <label for="password-confirm">{{ __('site.password_confirm') }}</label>
    <input id="password-confirm" type="password" name="password_confirmation" required autocomplete="new-password">
  </div>

  <button type="submit" class="btn btn-primary btn-block">{{ __('site.save_password') }}</button>
</form>
@endsection
