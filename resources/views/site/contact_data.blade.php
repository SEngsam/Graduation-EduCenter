@extends('site.master')

@section('title', __('site.contact') . ' | ' . config('app.name'))

@section('content')

@include('includes.page-head', ['title' => __('site.contact')])

<div class="container">
  <div class="card contact-box">
    <h2 style="font-size:24px;margin-bottom:16px">{{ __('site.contact_thanks', ['name' => $name]) }}</h2>
    <dl style="margin:0 0 24px">
      <dt style="color:var(--muted)">{{ __('site.email') }}</dt>
      <dd style="margin:0 0 12px">{{ $mail }}</dd>
      <dt style="color:var(--muted)">{{ __('site.major') }}</dt>
      <dd style="margin:0 0 12px">{{ $major }}</dd>
      <dt style="color:var(--muted)">{{ __('site.message') }}</dt>
      <dd style="margin:0">{{ $message }}</dd>
    </dl>
    <a href="{{ route('site.index') }}" class="btn btn-outline">{{ __('site.back_home') }}</a>
  </div>
</div>

@endsection
