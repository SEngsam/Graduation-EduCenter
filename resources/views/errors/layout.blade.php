<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title') | {{ config('app.name') }}</title>
  <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600&family=Readex+Pro:wght@500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/tokens.css') }}">
  <link rel="stylesheet" href="{{ asset('css/site.css') }}">
  @include('includes.theme-head')
</head>
<body>
<main class="error-page">
  <a class="brand" href="{{ url('/') }}">
    <span class="brand-mark" style="color:var(--on-orange)">@include('includes.icon', ['name' => 'cap'])</span>
    <span class="brand-name"><strong>EduCenter</strong><small>{{ __('site.brand_sub') }}</small></span>
  </a>
  <div class="error-box">
    <span class="error-code">@yield('code')</span>
    <h1>@yield('title')</h1>
    <p>@yield('message')</p>
    <div class="error-actions">
      <a href="{{ url('/') }}" class="btn btn-primary">{{ __('site.go_home') }}</a>
      <a href="{{ url()->previous() }}" class="btn btn-outline">{{ __('site.go_back') }}</a>
    </div>
  </div>
</main>
</body>
</html>
