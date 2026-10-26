<button type="button" class="{{ $class ?? 'icon-btn' }} theme-toggle" aria-label="{{ __('site.toggle_theme') }}" title="{{ __('site.toggle_theme') }}">
  <span class="when-light">@include('includes.icon', ['name' => 'moon', 'size' => 20])</span>
  <span class="when-dark">@include('includes.icon', ['name' => 'sun', 'size' => 20])</span>
</button>
@once
<script>
  document.addEventListener('click', function (e) {
    var btn = e.target.closest('.theme-toggle');
    if (!btn) return;
    var root = document.documentElement;
    var dark = root.getAttribute('data-theme') === 'dark' ||
      (!root.hasAttribute('data-theme') && window.matchMedia('(prefers-color-scheme: dark)').matches);
    var next = dark ? 'light' : 'dark';
    root.setAttribute('data-theme', next);
    try { localStorage.setItem('theme', next); } catch (err) {}
  });
</script>
@endonce
