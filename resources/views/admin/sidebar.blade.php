@php
  $links = [
    ['admin.categories.index', 'admin.categories.*', 'dash.categories', 'all_categories'],
    ['admin.courses.index', 'admin.courses.*', 'dash.courses', 'all_courses'],
    ['admin.teachers.index', 'admin.teachers.*', 'dash.teachers', 'all_teachers'],
    ['admin.events.index', 'admin.events.*', 'dash.events', 'all_events'],
    ['admin.news.index', 'admin.news.*', 'dash.news', 'all_newss'],
    ['admin.abouts.index', 'admin.abouts.*', 'dash.abouts', 'all_abouts'],
  ];
  $manage = [
    ['admin.users.index', 'admin.users.*', 'dash.users', 'all_users'],
    ['admin.roles.index', 'admin.roles.*', 'dash.roles', 'all_users'],
    ['admin.reports.index', 'admin.reports.*', 'dash.reports', 'view_reports'],
    ['admin.messages.index', 'admin.messages.*', 'dash.messages', 'all_users'],
  ];
  $visible = function ($items) use ($me) {
    return array_filter($items, function ($l) use ($me) { return $me->hasAbility($l[3]); });
  };
@endphp
<aside class="admin-side" aria-label="{{ __('dash.panel') }}">
  <div class="side-head">
    <a class="brand" href="{{ route('admin.index') }}">
      <span class="brand-mark" style="color:var(--on-orange)">@include('includes.icon', ['name' => 'cap'])</span>
      <span class="brand-name"><strong>EduCenter</strong><small>{{ __('dash.panel') }}</small></span>
    </a>
    <button class="nav-toggle" type="button" aria-label="{{ __('site.menu') }}" aria-expanded="false" aria-controls="adminNav" id="adminNavToggle">
      @include('includes.icon', ['name' => 'menu'])
    </button>
  </div>

  <nav class="admin-nav" id="adminNav">
    <a href="{{ route('admin.index') }}" class="{{ Route::is('admin.index') ? 'active' : '' }}">@include('includes.icon', ['name' => 'grid', 'size' => 20]) {{ __('dash.overview') }}</a>

    @if ($items = $visible($links))
      <span class="group">{{ __('dash.content') }}</span>
      @foreach ($items as [$route, $pattern, $label])
        <a href="{{ route($route) }}" class="{{ Route::is($pattern) ? 'active' : '' }}">{{ __($label) }}</a>
      @endforeach
    @endif

    @if ($items = $visible($manage))
      <span class="group">{{ __('dash.management') }}</span>
      @foreach ($items as [$route, $pattern, $label])
        <a href="{{ route($route) }}" class="{{ Route::is($pattern) ? 'active' : '' }}">{{ __($label) }}</a>
      @endforeach
    @endif
  </nav>

  <div class="side-bottom admin-nav">
    <a href="{{ route('site.index') }}">{{ __('dash.public_site') }}</a>
    <form action="{{ route('logout') }}" method="POST" style="margin:0">
      @csrf
      <button type="submit" class="btn btn-sm" style="width:100%;justify-content:flex-start;background:none;color:var(--ink-inverse-muted);min-height:44px">@include('includes.icon', ['name' => 'logout', 'size' => 20]) {{ __('dash.logout') }}</button>
    </form>
  </div>
</aside>
