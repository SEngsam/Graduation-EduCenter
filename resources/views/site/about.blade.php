@extends('site.master')

@section('title', __('site.about') . ' | ' . config('app.name'))

@section('content')

@include('includes.page-head', ['title' => __('site.about'), 'intro' => __('site.about_intro')])

@foreach ($abouts as $item)
<section class="section">
  <div class="container">
    <div class="card about-block">
      <div class="about-img">
        @if ($item->image && file_exists(public_path('uploads/abouts/' . $item->image)))
          <img src="{{ asset('uploads/abouts/' . $item->image) }}" alt="">
        @else
          <img src="{{ asset('frontend/images/about/about-page.jpg') }}" alt="">
        @endif
      </div>
      <div class="about-text">
        <h2>{{ $item->title }}</h2>
        <p>{{ $item->description }}</p>
      </div>
    </div>
  </div>
</section>
@endforeach

<section class="section">
  <div class="container values">
    <div class="card">
      <span class="icon-box">@include('includes.icon', ['name' => 'user', 'size' => 24])</span>
      <h3>{{ __('site.value_teachers') }}</h3>
      <p>{{ __('site.value_teachers_text') }}</p>
    </div>
    <div class="card">
      <span class="icon-box">@include('includes.icon', ['name' => 'check', 'size' => 24])</span>
      <h3>{{ __('site.value_enroll') }}</h3>
      <p>{{ __('site.value_enroll_text') }}</p>
    </div>
    <div class="card">
      <span class="icon-box">@include('includes.icon', ['name' => 'calendar', 'size' => 24])</span>
      <h3>{{ __('site.value_events') }}</h3>
      <p>{{ __('site.value_events_text') }}</p>
    </div>
  </div>
</section>

@if ($teachers->count())
<section class="section" style="padding-bottom:72px">
  <div class="container">
    <div class="section-head">
      <h2>{{ __('site.our_teachers') }}</h2>
      <a href="{{ route('site.teachers') }}" class="btn btn-outline">{{ __('site.teachers') }}</a>
    </div>
    <div class="grid">
      @foreach ($teachers as $item)
        @php
          $photo = $item->photo();
        @endphp
        <div class="card team-row">
          <img class="avatar" src="{{ $photo }}" alt="">
          <div><strong>{{ $item->name }}</strong><span>{{ $item->major }}</span></div>
        </div>
      @endforeach
    </div>
  </div>
</section>
@endif

@endsection
