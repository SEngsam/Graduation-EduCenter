<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title') | {{ config('app.name') }}</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600&family=Readex+Pro:wght@500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/site.css') }}">
</head>
<body>
<div class="auth">
  <aside class="auth-side">
    <a class="brand" href="{{ route('site.index') }}">
      <span class="brand-mark" style="color:#fff">@include('includes.icon', ['name' => 'cap'])</span>
      <span class="brand-name">
        <strong>EduCenter</strong>
        <small>{{ __('site.brand_sub') }}</small>
      </span>
    </a>
    <div>
      <h1>@yield('side_title')</h1>
      <p>@yield('side_text')</p>
    </div>
    <a href="{{ route('lang.switch', app()->getLocale() == 'ar' ? 'en' : 'ar') }}" style="color:#c4d9d5">{{ __('site.other_lang') }}</a>
  </aside>
  <main class="auth-main">
    @yield('content')
  </main>
</div>
</body>
</html>
