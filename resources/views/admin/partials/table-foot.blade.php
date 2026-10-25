{{-- $items: a paginator --}}
<div class="table-foot">
  <span>{{ __('dash.showing', ['count' => $items->count(), 'total' => $items->total()]) }}</span>
  <div class="pager">{{ $items->withQueryString()->links() }}</div>
</div>
