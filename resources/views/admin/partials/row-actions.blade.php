<span class="row-actions">
  @if (! empty($edit))
    <a href="{{ $edit }}" class="btn btn-outline btn-sm" aria-label="{{ __('dash.edit') }} {{ $label }}">{{ __('dash.edit') }}</a>
  @endif
  @if (! empty($destroy))
    <form action="{{ $destroy }}" method="POST" data-confirm="{{ __('dash.confirm_delete', ['name' => $label]) }}">
      @csrf
      @method('DELETE')
      <button type="submit" class="btn btn-danger-soft btn-sm" aria-label="{{ __('dash.delete') }} {{ $label }}">{{ __('dash.delete') }}</button>
    </form>
  @endif
</span>
