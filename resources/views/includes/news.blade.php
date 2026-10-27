@php
    $photo = $item->photo();
@endphp
<article class="card news-row">
  <div class="thumb">
    <img src="{{ $photo }}" alt="">
  </div>
  <div>
    <small>{{ $item->date }}@if ($item->auther) · {{ $item->auther }}@endif</small>
    <h3>{{ $item->title }}</h3>
    <p>{{ Str::limit($item->description, 110) }}</p>
  </div>
</article>
