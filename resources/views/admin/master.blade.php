<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', __('dash.panel')) | {{ config('app.name') }}</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600&family=Readex+Pro:wght@500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/tokens.css') }}">
  <link rel="stylesheet" href="{{ asset('css/site.css') }}">
  <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
  @yield('styles')
  @include('includes.theme-head')
</head>
<body>
<a class="skip-link" href="#main">{{ __('site.skip') }}</a>

<div class="admin">
  @include('admin.sidebar')

  <div class="admin-body">
    <header class="admin-top">
      <span class="spacer"></span>
      @foreach (LaravelLocalization::getSupportedLocales() as $code => $props)
        @if (app()->getLocale() != $code)
          <a class="lang-link" rel="alternate" hreflang="{{ $code }}" href="{{ LaravelLocalization::getLocalizedURL($code, null, [], true) }}">{{ $props['native'] }}</a>
        @endif
      @endforeach
      @include('includes.theme-toggle')
      <a class="icon-btn" href="{{ route('notifications.index') }}" aria-label="{{ __('dash.notifications') }}">
        @include('includes.icon', ['name' => 'bell', 'size' => 20])
        @if ($unread = $me->unreadNotifications()->count())
          <span class="count">{{ $unread }}</span>
        @endif
      </a>
      <span class="who">
        <span class="avatar" aria-hidden="true">{{ mb_substr($me->name, 0, 1) }}</span>
        <span><strong>{{ $me->name }}</strong><small>{{ $me->role->name ?? '' }}</small></span>
      </span>
    </header>

    <main class="admin-main" id="main" tabindex="-1">
      @hasSection('crumbs')
        <nav class="admin-crumbs" aria-label="breadcrumb">
          <a href="{{ route('admin.index') }}">{{ __('dash.panel') }}</a> / @yield('crumbs')
        </nav>
      @endif

      @include('includes.flash')
      @include('admin.errors')

      @yield('content')
    </main>
  </div>
</div>

<script>
  document.getElementById('adminNavToggle').addEventListener('click', function () {
    var open = this.closest('.admin-side').classList.toggle('open');
    this.setAttribute('aria-expanded', open);
  });

  // show the chosen file name and preview in upload boxes
  document.querySelectorAll('.upload input[type=file]').forEach(function (input) {
    input.addEventListener('change', function () {
      var box = input.closest('.upload');
      var file = input.files[0];
      if (!file) return;
      box.querySelector('strong').textContent = file.name;
      if (file.type.indexOf('image/') !== 0) return;
      var img = box.querySelector('img');
      if (!img) {
        img = document.createElement('img');
        img.alt = '';
        var placeholder = box.querySelector('.icon-box');
        box.replaceChild(img, placeholder);
      }
      img.src = URL.createObjectURL(file);
    });
  });
</script>
@include('includes.confirm-dialog')
@yield('scripts')
</body>
</html>
