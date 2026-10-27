<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title') | {{ config('app.name') }}</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600&family=Readex+Pro:wght@500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/tokens.css') }}">
  <link rel="stylesheet" href="{{ asset('css/site.css') }}">
  @include('includes.theme-head')
</head>
<body>
<a class="skip-link" href="#main">{{ __('site.skip') }}</a>
<div class="app">
  <aside class="app-side" aria-label="{{ __('site.menu') }}">
    <a class="brand" href="{{ route('site.index') }}">
      <span class="brand-mark" style="color:var(--on-orange)">@include('includes.icon', ['name' => 'cap'])</span>
      <span class="brand-name"><strong>EduCenter</strong><small>@yield('brand_sub', __('site.brand_sub'))</small></span>
    </a>
    <nav class="side-nav">
      @hasSection('nav')
        @yield('nav')
      @else
      <a href="{{ route('student.dashboard') }}" class="{{ Route::is('student.dashboard') ? 'active' : '' }}">@include('includes.icon', ['name' => 'grid', 'size' => 20]) {{ __('site.my_dashboard') }}</a>
      <a href="{{ route('site.courses') }}">@include('includes.icon', ['name' => 'search', 'size' => 20]) {{ __('site.browse_courses') }}</a>
      <a href="{{ route('site.event') }}">@include('includes.icon', ['name' => 'news', 'size' => 20]) {{ __('site.events') }}</a>
      <a href="{{ route('schedule') }}" class="{{ Route::is('schedule') ? 'active' : '' }}">@include('includes.icon', ['name' => 'calendar', 'size' => 20]) {{ __('site.my_schedule') }}</a>
      @include('includes.nav-notifications')
      <a href="{{ route('student.profile') }}" class="{{ Route::is('student.profile') ? 'active' : '' }}">@include('includes.icon', ['name' => 'user', 'size' => 20]) {{ __('site.profile') }}</a>
      @endif
    </nav>
    <div class="side-foot">
      <div class="side-tools">
        <a class="lang-link" href="{{ route('lang.switch', app()->getLocale() == 'ar' ? 'en' : 'ar') }}">{{ __('site.other_lang') }}</a>
        @include('includes.theme-toggle')
      </div>
      <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit">@include('includes.icon', ['name' => 'logout', 'size' => 20]) {{ __('site.logout') }}</button>
      </form>
    </div>
  </aside>

  <main class="app-main" id="main" tabindex="-1">
    @include('includes.flash')
    @yield('content')
  </main>
</div>
@include('includes.confirm-dialog')
</body>
</html>
