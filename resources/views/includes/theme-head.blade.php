<script>
  // apply the saved theme before the page paints; no saved choice = follow the system
  (function () {
    try {
      var t = localStorage.getItem('theme');
      if (t === 'dark' || t === 'light') document.documentElement.setAttribute('data-theme', t);
    } catch (e) {}
  })();
</script>
