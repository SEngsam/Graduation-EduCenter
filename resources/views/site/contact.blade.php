@extends('site.master')

@section('title', __('site.contact') . ' | ' . config('app.name'))

@section('content')

@include('includes.page-head', ['title' => __('site.contact'), 'intro' => __('site.contact_intro')])

<div class="container contact-grid">
  <div class="card contact-box">
    <form class="form" action="{{ route('contact_data') }}" method="POST">
      @csrf
      <h2 style="font-size:24px">{{ __('site.send_us') }}</h2>
      <p style="margin:-8px 0 0;color:var(--ink-subtle)">{{ __('site.reply_time') }}</p>
      <div class="field-row">
        <div class="field">
          <label for="name">{{ __('site.name') }}</label>
          <input type="text" id="name" name="name" value="{{ old('name', optional(auth()->user())->name) }}" required>
        </div>
        <div class="field">
          <label for="mail">{{ __('site.email') }}</label>
          <input type="email" id="mail" name="mail" value="{{ old('mail', optional(auth()->user())->email) }}" required>
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
      <button type="submit" class="btn btn-primary" style="align-self:flex-start;min-height:48px;padding:0 28px">{{ __('site.send') }}</button>
    </form>
  </div>

  <aside class="info-card">
    <h2>{{ __('site.contact_info') }}</h2>
    @php
      $info = [
          ['pin', __('site.address'), config('site.address'), ''],
          ['phone', __('site.tel'), config('site.phone'), 'ltr'],
          ['mail', __('site.email'), config('site.email') ?: config('mail.from.address'), ''],
          ['clock', __('site.hours'), config('site.hours'), ''],
      ];
    @endphp
    @foreach ($info as $row)
      @if ($row[2])
        <div class="info-row"><span class="ic">@include('includes.icon', ['name' => $row[0]])</span><div><small>{{ $row[1] }}</small><strong @if ($row[3]) dir="ltr" @endif>{{ $row[2] }}</strong></div></div>
      @endif
    @endforeach
  </aside>
</div>

@endsection
