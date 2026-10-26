{{-- $icon, $text, optional $action ([url, label]) --}}
<div class="card empty-state">
  <span class="icon-box">@include('includes.icon', ['name' => $icon ?? 'book', 'size' => 28])</span>
  <p>{{ $text }}</p>
  @isset($action)
    <a href="{{ $action[0] }}" class="btn btn-primary">{{ $action[1] }}</a>
  @endisset
</div>
