@extends('layouts.student')

@section('title', __('site.profile'))

@section('content')

<div class="page-title">
  <h1>{{ __('site.profile') }}</h1>
</div>

<section class="card profile-head">
  <span class="avatar" aria-hidden="true">{{ mb_substr($user->name, 0, 1) }}</span>
  <div>
    <h2>{{ $user->name }}</h2>
    <p>{{ $user->email }} · {{ __('site.student') }}@if (optional($user->student)->major) · {{ $user->student->major }}@endif</p>
  </div>
</section>

<form class="card form-card form" action="{{ route('student.profile.update') }}" method="POST">
  @csrf
  @method('PUT')
  <h2>{{ __('site.personal_info') }}</h2>
  <div class="field-row">
    <div class="field">
      <label for="name">{{ __('site.full_name') }}</label>
      <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autocomplete="name">
      @error('name')<span class="error">{{ $message }}</span>@enderror
    </div>
    <div class="field">
      <label for="email">{{ __('site.email') }}</label>
      <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required autocomplete="email">
      @error('email')<span class="error">{{ $message }}</span>@enderror
    </div>
    <div class="field">
      <label for="no_phone">{{ __('site.phone') }}</label>
      <input id="no_phone" name="no_phone" type="tel" value="{{ old('no_phone', optional($user->student)->no_phone) }}" autocomplete="tel">
      @error('no_phone')<span class="error">{{ $message }}</span>@enderror
    </div>
    <div class="field">
      <label for="major">{{ __('site.major') }}</label>
      <input id="major" name="major" type="text" value="{{ old('major', optional($user->student)->major) }}">
      @error('major')<span class="error">{{ $message }}</span>@enderror
    </div>
  </div>
  <div class="form-actions">
    <a href="{{ route('student.dashboard') }}" class="btn btn-outline">{{ __('site.cancel') }}</a>
    <button type="submit" class="btn btn-primary">{{ __('site.save_changes') }}</button>
  </div>
</form>

<form class="card form-card form" action="{{ route('student.password.update') }}" method="POST">
  @csrf
  @method('PUT')
  <h2>{{ __('site.password') }}</h2>
  <div class="field-row">
    <div class="field">
      <label for="current_password">{{ __('site.current_password') }}</label>
      <input id="current_password" name="current_password" type="password" required autocomplete="current-password">
      @error('current_password')<span class="error">{{ $message }}</span>@enderror
    </div>
    <div class="field">
      <label for="password">{{ __('site.new_password') }}</label>
      <input id="password" name="password" type="password" required autocomplete="new-password" placeholder="{{ __('site.min_8') }}">
      @error('password')<span class="error">{{ $message }}</span>@enderror
    </div>
    <div class="field">
      <label for="password_confirmation">{{ __('site.password_confirm') }}</label>
      <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password">
    </div>
  </div>
  <div class="form-actions">
    <button type="submit" class="btn btn-dark">{{ __('site.update_password') }}</button>
  </div>
</form>

@endsection
