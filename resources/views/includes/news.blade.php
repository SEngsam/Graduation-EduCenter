<article class="card news-row">
  <div class="thumb">
    @if ($item->image && file_exists(public_path('uploads/news/' . $item->image)))
      <img src="{{ asset('uploads/news/' . $item->image) }}" alt="">
    @else
      @include('includes.icon', ['name' => 'news', 'size' => 32, 'stroke' => 1.8])
    @endif
  </div>
  <div>
    <small>{{ $item->date }}@if ($item->auther) · {{ $item->auther }}@endif</small>
    <h3>{{ $item->title }}</h3>
    <p>{{ Str::limit($item->description, 110) }}</p>
  </div>
</article>
