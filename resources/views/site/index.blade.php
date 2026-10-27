@extends('site.master')

@section('title', __('site.home') . ' | ' . config('app.name'))

@section('content')

<section class="hero">
  <div class="container">
    <div class="hero-text">
      <span class="badge">{{ __('site.hero_badge') }}</span>
      <h1>{{ __('site.hero_title') }}</h1>
      <p>{{ __('site.hero_text') }}</p>

      <form class="search-box" action="{{ route('site.courses') }}" method="GET">
        <label class="sr-only" for="q">{{ __('site.search_placeholder') }}</label>
        <input type="search" id="q" name="q" placeholder="{{ __('site.search_placeholder') }}">
        <button type="submit" class="btn btn-primary">{{ __('site.search') }}</button>
      </form>
    </div>

    <div class="hero-photo">
      <img src="{{ asset('frontend/images/banner/banner-1.jpg') }}" alt="{{ __('site.hero_photo') }}">
    </div>
  </div>
</section>

@if ($categories->count())
<section class="section">
  <div class="container">
    <div class="section-head">
      <div>
        <h2>{{ __('site.categories') }}</h2>
        <p>{{ __('site.categories_sub') }}</p>
      </div>
    </div>
    <div class="grid">
      @php
        $tones = ['science' => ['sci', 'flask'], 'arts' => ['art', 'palette'], 'technology' => ['tech', 'screen']];
      @endphp
      @foreach ($categories as $category)
        @php
          [$tone, $icon] = $tones[strtolower($category->name_major)] ?? ['', 'book'];
        @endphp
        <a href="{{ route('site.courses') }}" class="card cat-card {{ $tone ? 'tone-' . $tone : '' }}">
          <span class="icon-box">@include('includes.icon', ['name' => $icon, 'size' => 26])</span>
          <h3>{{ $category->name_major }}</h3>
          <p>{{ $category->description }}</p>
          <span class="more">{{ __('site.browse') }} {{ app()->getLocale() == 'ar' ? '←' : '→' }}</span>
        </a>
      @endforeach
    </div>
  </div>
</section>
@endif

<section class="section">
  <div class="container">
    <div class="section-head">
      <div>
        <h2>{{ __('site.latest_courses') }}</h2>
        <p>{{ __('site.latest_courses_sub') }}</p>
      </div>
      <a href="{{ route('site.courses') }}" class="btn btn-outline">{{ __('site.all_courses') }}</a>
    </div>
    @if ($courses->count())
      <div class="grid">
        @foreach ($courses as $item)
          @include('includes.course')
        @endforeach
      </div>
    @else
      @include('includes.empty', ['icon' => 'book', 'text' => __('site.no_courses')])
    @endif
  </div>
</section>

<section class="band band-warm">
  <div class="container">
    <div class="section-head">
      <div>
        <h2>{{ __('site.how_title') }}</h2>
        <p>{{ __('site.how_sub') }}</p>
      </div>
      <a href="{{ route('site.about') }}" class="text-link">{{ __('site.about_more') }}</a>
    </div>
    <ol class="steps">
      @foreach ([['search', 1], ['check', 2], ['grid', 3]] as [$icon, $n])
        <li class="card step">
          <span class="step-top">
            <span class="icon-box">@include('includes.icon', ['name' => $icon, 'size' => 26])</span>
            <span class="step-n">{{ $n }}</span>
          </span>
          <h3>{{ __('site.how_' . $n) }}</h3>
          <p>{{ __('site.how_' . $n . '_text') }}</p>
        </li>
      @endforeach
    </ol>
  </div>
</section>

@if ($teachers->count())
<section class="band band-card">
  <div class="container">
    <div class="section-head">
      <div>
        <h2>{{ __('site.our_teachers') }}</h2>
        <p>{{ __('site.our_teachers_sub') }}</p>
      </div>
      <a href="{{ route('site.teachers') }}" class="btn btn-outline">{{ __('site.all_teachers') }}</a>
    </div>
    <div class="grid">
      @foreach ($teachers as $item)
        @include('includes.teacher')
      @endforeach
    </div>
  </div>
</section>
@endif

@if ($events->count() || $news)
<section class="section">
  <div class="container">
    <div class="section-head">
      <h2>{{ __('site.happening') }}</h2>
      <a href="{{ route('site.event') }}" class="text-link">{{ __('site.all_events') }}</a>
    </div>
    <div class="happening">
      @foreach ($events as $item)
        <a href="{{ route('site.event') }}" class="card happening-item">
          <span class="event-date">
            @if ($item->created_at)
              <strong>{{ $item->created_at->format('d') }}</strong>
              <span>{{ $item->created_at->translatedFormat('M') }}</span>
            @else
              @include('includes.icon', ['name' => 'calendar', 'size' => 26])
            @endif
          </span>
          <span class="happening-text">
            <span class="tag">{{ __('site.tag_event') }}</span>
            <h3>{{ $item->description }}</h3>
            @if ($item->location)
              <small>@include('includes.icon', ['name' => 'pin', 'size' => 15]) {{ $item->location }}</small>
            @endif
          </span>
        </a>
      @endforeach
      @if ($news)
        @php
          $photo = $news->photo();
        @endphp
        <a href="{{ route('site.event') }}" class="card happening-item">
          <img class="happening-thumb" src="{{ $photo }}" alt="">
          <span class="happening-text">
            <span class="tag tone-neutral">{{ __('site.tag_news') }}</span>
            <h3>{{ $news->title }}</h3>
            <p>{{ Str::limit($news->description, 70) }}</p>
          </span>
        </a>
      @endif
    </div>
  </div>
</section>
@endif

@guest
<div class="container">
  <div class="cta">
    <div>
      <h2>{{ __('site.cta_title') }}</h2>
      <p>{{ __('site.cta_text') }}</p>
    </div>
    <a href="{{ route('register') }}" class="btn btn-primary">{{ __('site.cta_button') }}</a>
  </div>
</div>
@else
<div style="height:72px"></div>
@endguest

@endsection
