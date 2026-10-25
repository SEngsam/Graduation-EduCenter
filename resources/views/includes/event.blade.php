@php
    $photo = $item->image && file_exists(public_path('uploads/events/' . $item->image))
        ? asset('uploads/events/' . $item->image)
        : asset('frontend/images/events/event-' . ($item->id % 3 + 1) . '.jpg');
@endphp
<article class="card event-card">
  <div class="thumb">
    <img src="{{ $photo }}" alt="">
    @if ($item->created_at)
      <span class="day"><strong>{{ $item->created_at->format('d') }}</strong><span>{{ $item->created_at->translatedFormat('M') }}</span></span>
    @endif
  </div>
  <div class="course-body">
    <div class="course-meta">
      @include('includes.icon', ['name' => 'pin', 'size' => 16])
      {{ $item->location }}
    </div>
    <h3>{{ $item->description }}</h3>
  </div>
</article>
