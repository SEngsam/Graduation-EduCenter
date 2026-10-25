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

      @isset($stats)
        <div class="stats">
          <div><strong>{{ $stats['students'] }}</strong><span>{{ __('site.stat_students') }}</span></div>
          <div><strong>{{ $stats['courses'] }}</strong><span>{{ __('site.stat_courses') }}</span></div>
          <div><strong>{{ $stats['teachers'] }}</strong><span>{{ __('site.stat_teachers') }}</span></div>
        </div>
      @endisset
    </div>

    <div class="hero-panel" aria-hidden="true" style="background-image: linear-gradient(rgba(20, 33, 61, .78), rgba(20, 33, 61, .9)), url({{ asset('frontend/images/banner/banner-1.jpg') }})">
      @if ($first = $courses->first())
        <div class="panel-card">
          <small>{{ __('site.today_class') }}</small>
          <strong>{{ $first->title }}</strong>
          @if ($first->teacher)
            <span class="course-meta">
              <span class="avatar" style="width:28px;height:28px;font-size:11px">{{ mb_substr($first->teacher->name, 0, 1) }}</span>
              {{ $first->teacher->name }}
            </span>
          @endif
          <div class="progress"><span style="width:60%"></span></div>
        </div>
      @endif
      @if ($events->count())
        <div class="panel-list">
          <strong>{{ __('site.this_week') }}</strong>
          @foreach ($events as $event)
            <div><span>{{ Str::limit($event->description, 28) }}</span><em>{{ $event->location }}</em></div>
          @endforeach
        </div>
      @endif
    </div>
  </div>
</section>

@isset($categories)
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
      @php($tones = ['science' => ['sci', 'flask'], 'arts' => ['art', 'palette'], 'technology' => ['tech', 'screen']])
      @foreach ($categories as $category)
        @php([$tone, $icon] = $tones[strtolower($category->name_major)] ?? ['', 'book'])
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
@endisset

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
      <div class="card empty">{{ __('site.no_courses') }}</div>
    @endif
  </div>
</section>

@foreach ($abouts as $item)
<section class="section">
  <div class="container">
    <div class="card about-block">
      <div class="about-img">
        @if ($item->image && file_exists(public_path('uploads/abouts/' . $item->image)))
          <img src="{{ asset('uploads/abouts/' . $item->image) }}" alt="">
        @else
          <img src="{{ asset('frontend/images/about/about-us.jpg') }}" alt="">
        @endif
      </div>
      <div class="about-text">
        <h2>{{ $item->title }}</h2>
        <p>{{ $item->description }}</p>
        <a href="{{ route('site.about') }}" class="btn btn-dark" style="align-self:flex-start">{{ $item->btn_text ?: __('site.read_more') }}</a>
      </div>
    </div>
  </div>
</section>
@endforeach

@if ($teachers->count())
<section class="section">
  <div class="container">
    <div class="section-head">
      <div>
        <h2>{{ __('site.our_teachers') }}</h2>
        <p>{{ __('site.our_teachers_sub') }}</p>
      </div>
      <a href="{{ route('site.teachers') }}" class="btn btn-outline">{{ __('site.teachers') }}</a>
    </div>
    <div class="grid">
      @foreach ($teachers as $item)
        @include('includes.teacher')
      @endforeach
    </div>
  </div>
</section>
@endif

<section class="section">
  <div class="container grid-2">
    <div>
      <h2 style="font-size:26px;margin-bottom:18px">{{ __('site.upcoming_events') }}</h2>
      @forelse ($events as $item)
        <div class="card event-row">
          <div class="event-date">
            @if ($item->created_at)
              <strong>{{ $item->created_at->format('d') }}</strong>
              <span>{{ $item->created_at->translatedFormat('M') }}</span>
            @else
              @include('includes.icon', ['name' => 'pin', 'size' => 26])
            @endif
          </div>
          <div>
            <h3>{{ $item->description }}</h3>
            <p>{{ $item->location }}</p>
          </div>
        </div>
      @empty
        <p class="empty">{{ __('site.no_events') }}</p>
      @endforelse
    </div>
    <div>
      <h2 style="font-size:26px;margin-bottom:18px">{{ __('site.latest_news') }}</h2>
      @foreach ($news as $item)
        @include('includes.news')
      @endforeach
    </div>
  </div>
</section>

@guest
<div class="container">
  <div class="cta">
    <div>
      <h2>{{ __('site.cta_title') }}</h2>
      <p>{{ __('site.cta_text') }}</p>
    </div>
    <a href="{{ route('register') }}" class="btn btn-light">{{ __('site.cta_button') }}</a>
  </div>
</div>
@else
<div style="height:72px"></div>
@endguest

@endsection
