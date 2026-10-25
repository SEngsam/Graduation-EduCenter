<section class="page-head">
  <div class="container">
    <div class="crumbs"><a href="{{ route('site.index') }}">{{ __('site.home') }}</a> / {{ $title }}</div>
    <h1>{{ $title }}</h1>
    @isset($intro)
      <p>{{ $intro }}</p>
    @endisset
  </div>
</section>
