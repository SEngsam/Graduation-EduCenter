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
          <span style="color:#a8e3d9">@include('includes.icon', ['name' => 'book', 'size' => 96, 'stroke' => 1.4])</span>
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

@if ($teachers->count())
<section class="section" style="padding-bottom:72px">
  <div class="container">
    <div class="section-head">
      <h2>{{ __('site.our_teachers') }}</h2>
    </div>
    <div class="grid">
      @foreach ($teachers as $item)
        @include('includes.teacher')
      @endforeach
    </div>
  </div>
</section>
@endif

@endsection
