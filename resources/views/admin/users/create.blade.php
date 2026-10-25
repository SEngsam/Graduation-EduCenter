@extends('admin.master')

@section('title', __('dash.add_user'))
@section('crumbs')
  <a href="{{ route('admin.users.index') }}">{{ __('dash.users') }}</a> / {{ __('dash.add_user') }}
@endsection

@section('content')

<div class="admin-title"><h1>{{ __('dash.add_user') }}</h1></div>

<form class="card admin-form" style="max-width:760px" action="{{ route('admin.users.store') }}" method="POST">
  @csrf

  <div class="field-row">
    <div class="field">
      <label for="name">{{ __('dash.name') }}</label>
      <input type="text" id="name" name="name" value="{{ old('name') }}" required>
      @error('name')<span class="error">{{ $message }}</span>@enderror
    </div>
    <div class="field">
      <label for="email">{{ __('dash.email') }}</label>
      <input type="email" id="email" name="email" value="{{ old('email') }}" required autocomplete="off">
      @error('email')<span class="error">{{ $message }}</span>@enderror
    </div>
  </div>

  <div class="field">
    <label for="role_id">{{ __('dash.role') }}</label>
    <select id="role_id" name="role_id" required>
      <option value="">{{ __('dash.role') }}…</option>
      @foreach ($roles as $role)
        <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
      @endforeach
    </select>
    @error('role_id')<span class="error">{{ $message }}</span>@enderror
  </div>

  <div class="field-row">
    <div class="field">
      <label for="password">{{ __('dash.password') }}</label>
      <input type="password" id="password" name="password" required autocomplete="new-password" placeholder="{{ __('site.min_8') }}">
      @error('password')<span class="error">{{ $message }}</span>@enderror
    </div>
    <div class="field">
      <label for="password_confirmation">{{ __('dash.password_confirm') }}</label>
      <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password">
    </div>
  </div>

  <div class="form-actions">
    <a href="{{ route('admin.users.index') }}" class="btn btn-outline">{{ __('dash.cancel') }}</a>
    <button type="submit" class="btn btn-primary">{{ __('dash.add_user') }}</button>
  </div>
</form>

@endsection
