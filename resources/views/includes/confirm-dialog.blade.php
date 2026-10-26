{{-- one styled confirm dialog for every form[data-confirm] on the page --}}
<dialog class="sheet" id="confirmDialog" aria-labelledby="confirmTitle">
  <form method="dialog">
    <h2 id="confirmTitle">{{ __('site.confirm_title_dialog') }}</h2>
    <p id="confirmText" style="margin:0;color:var(--ink-muted)"></p>
    <div class="form-actions">
      <button type="submit" value="cancel" class="btn btn-outline" autofocus>{{ __('site.cancel') }}</button>
      <button type="submit" value="ok" class="btn btn-danger-soft" id="confirmOk" data-default="{{ __('site.yes_delete') }}">{{ __('site.yes_delete') }}</button>
    </div>
  </form>
</dialog>
<script>
  (function () {
    var dialog = document.getElementById('confirmDialog');
    var pending = null;
    document.addEventListener('submit', function (e) {
      var form = e.target;
      if (!form.matches('form[data-confirm]') || form.dataset.confirmed) return;
      e.preventDefault();
      pending = form;
      document.getElementById('confirmText').textContent = form.dataset.confirm;
      var ok = document.getElementById('confirmOk');
      ok.textContent = form.dataset.confirmOk || ok.dataset.default;
      if (dialog.showModal) dialog.showModal();
      else if (window.confirm(form.dataset.confirm)) form.submit();
    });
    dialog.addEventListener('close', function () {
      if (dialog.returnValue === 'ok' && pending) {
        pending.dataset.confirmed = '1';
        pending.submit();
      }
      pending = null;
    });
  })();
</script>
