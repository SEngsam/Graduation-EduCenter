<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', config('app.name'))</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600&family=Readex+Pro:wght@500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/tokens.css') }}">
  <link rel="stylesheet" href="{{ asset('css/site.css') }}">
  @yield('styles')
</head>
<body>

<header class="site-header" id="siteHeader">
  <div class="container">
    <a class="brand" href="{{ route('site.index') }}">
      <span class="brand-mark" style="color:var(--on-orange)">@include('includes.icon', ['name' => 'cap'])</span>
      <span class="brand-name">
        <strong>EduCenter</strong>
        <small>{{ __('site.brand_sub') }}</small>
      </span>
    </a>

    <button class="nav-toggle" type="button" aria-label="{{ __('site.menu') }}" aria-expanded="false" id="navToggle">
      @include('includes.icon', ['name' => 'menu'])
    </button>

    <nav class="main-nav" aria-label="{{ __('site.menu') }}">
      <a href="{{ route('site.index') }}" class="{{ Route::is('site.index') ? 'active' : '' }}">{{ __('site.home') }}</a>
      <a href="{{ route('site.courses') }}" class="{{ Route::is('site.courses') ? 'active' : '' }}">{{ __('site.courses') }}</a>
      <a href="{{ route('site.teachers') }}" class="{{ Route::is('site.teachers') ? 'active' : '' }}">{{ __('site.teachers') }}</a>
      <a href="{{ route('site.event') }}" class="{{ Route::is('site.event') ? 'active' : '' }}">{{ __('site.events') }}</a>
      <a href="{{ route('site.about') }}" class="{{ Route::is('site.about') ? 'active' : '' }}">{{ __('site.about') }}</a>
      <a href="{{ route('contact') }}" class="{{ Route::is('contact') ? 'active' : '' }}">{{ __('site.contact') }}</a>
    </nav>

    <div class="header-actions">
      <a class="lang-link" href="{{ route('lang.switch', app()->getLocale() == 'ar' ? 'en' : 'ar') }}">{{ __('site.other_lang') }}</a>
      @auth
        <span class="user-chip">{{ __('site.hi', ['name' => auth()->user()->name]) }}</span>
        @if (auth()->user()->hasRole(\App\Models\Role::ROLE_STUDENT))
          <a class="btn btn-outline btn-sm" href="{{ route('student.dashboard') }}">{{ __('site.my_dashboard') }}</a>
        @else
          <a class="btn btn-outline btn-sm" href="{{ route('admin.index') }}">{{ __('site.dashboard') }}</a>
        @endif
        <form action="{{ route('logout') }}" method="POST" style="margin:0">
          @csrf
          <button type="submit" class="btn btn-dark btn-sm">{{ __('site.logout') }}</button>
        </form>
      @else
        <a class="btn btn-outline btn-sm" href="{{ route('login') }}">{{ __('site.login') }}</a>
        <a class="btn btn-dark btn-sm" href="{{ route('register') }}">{{ __('site.register') }}</a>
      @endauth
    </div>
  </div>
</header>

<div class="container">
  @if (session('success'))
    <div class="flash">{{ session('success') }}</div>
  @endif
  @if (session('error'))
    <div class="flash error">{{ session('error') }}</div>
  @endif
</div>

<main>
  @yield('content')
</main>

<footer class="site-footer">
  <div class="container cols">
    <div>
      <h4>EduCenter</h4>
      <p style="margin:0">{{ __('site.footer_text') }}</p>
    </div>
    <div>
      <h4>{{ __('site.links') }}</h4>
      <a href="{{ route('site.courses') }}">{{ __('site.courses') }}</a>
      <a href="{{ route('site.teachers') }}">{{ __('site.teachers') }}</a>
      <a href="{{ route('site.event') }}">{{ __('site.events') }}</a>
    </div>
    <div>
      <h4>{{ __('site.contact') }}</h4>
      <a href="{{ route('site.about') }}">{{ __('site.about') }}</a>
      <a href="{{ route('contact') }}">{{ __('site.contact') }}</a>
    </div>
  </div>
  <div class="container bottom">&copy; {{ date('Y') }} EduCenter — {{ __('site.rights') }}</div>
</footer>

<script>
  document.getElementById('navToggle').addEventListener('click', function () {
    var header = document.getElementById('siteHeader');
    var open = header.classList.toggle('open');
    this.setAttribute('aria-expanded', open);
  });
</script>
@yield('scripts')
</body>
</html>
