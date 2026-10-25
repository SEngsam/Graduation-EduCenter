<article class="card event-card">
  <div class="thumb">
    @if ($item->image && file_exists(public_path('uploads/events/' . $item->image)))
      <img src="{{ asset('uploads/events/' . $item->image) }}" alt="">
    @else
      @include('includes.icon', ['name' => 'pin', 'size' => 44, 'stroke' => 1.6])
    @endif
  </div>
  <div class="course-body">
    <div class="course-meta">
      @include('includes.icon', ['name' => 'pin', 'size' => 16])
      {{ $item->location }}
    </div>
    <h3>{{ $item->description }}</h3>
    @if ($item->created_at)
      <div class="course-meta">{{ $item->created_at->translatedFormat('j F Y') }}</div>
    @endif
  </div>
</article>
