@extends('site.master')

@section('title', __('site.contact') . ' | ' . config('app.name'))

@section('content')

@include('includes.page-head', ['title' => __('site.contact'), 'intro' => __('site.contact_intro')])

<div class="container">
  <div class="card contact-box">
    <form class="form" action="{{ route('contact_data') }}" method="POST">
      @csrf
      <div class="field-row">
        <div class="field">
          <label for="name">{{ __('site.name') }}</label>
          <input type="text" id="name" name="name" value="{{ old('name') }}" required>
        </div>
        <div class="field">
          <label for="mail">{{ __('site.email') }}</label>
          <input type="email" id="mail" name="mail" value="{{ old('mail') }}" required>
        </div>
      </div>
      <div class="field">
        <label for="major">{{ __('site.major') }}</label>
        <input type="text" id="major" name="major" value="{{ old('major') }}" required>
      </div>
      <div class="field">
        <label for="message">{{ __('site.message') }}</label>
        <textarea id="message" name="message" required>{{ old('message') }}</textarea>
      </div>
      <button type="submit" class="btn btn-primary" style="align-self:flex-start">{{ __('site.send') }}</button>
    </form>
  </div>
</div>

@endsection
