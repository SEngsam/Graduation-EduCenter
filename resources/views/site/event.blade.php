@extends('site.master')

@section('title', __('site.events') . ' | ' . config('app.name'))

@section('content')

@include('includes.page-head', ['title' => __('site.events'), 'intro' => __('site.events_intro')])

<section class="section">
  <div class="container" style="padding-bottom:72px">
    @if ($first = $events->first())
      @php
        $photo = $first->image && file_exists(public_path('uploads/events/' . $first->image))
            ? asset('uploads/events/' . $first->image)
            : asset('frontend/images/events/event-' . ($first->id % 3 + 1) . '.jpg');
      @endphp
      <article class="card featured">
        <img src="{{ $photo }}" alt="">
        <div class="text">
          <span class="badge">{{ __('site.next_event') }}</span>
          <h2>{{ $first->description }}</h2>
          <div class="meta-row">
            <span>@include('includes.icon', ['name' => 'pin', 'size' => 18]) {{ $first->location }}</span>
            @if ($first->created_at)
              <span>@include('includes.icon', ['name' => 'calendar', 'size' => 18]) {{ $first->created_at->translatedFormat('j F Y') }}</span>
            @endif
          </div>
        </div>
      </article>

      @if ($events->count() > 1)
        <h2 style="font-size:26px;margin-bottom:20px">{{ __('site.all_events') }}</h2>
        <div class="grid">
          @foreach ($events->slice(1) as $item)
            @include('includes.event')
          @endforeach
        </div>
      @endif
    @else
      <div class="card empty">{{ __('site.no_events') }}</div>
    @endif
  </div>
</section>

@endsection
