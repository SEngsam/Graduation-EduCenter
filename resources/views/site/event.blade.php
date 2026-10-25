@extends('site.master')

@section('title', __('site.events') . ' | ' . config('app.name'))

@section('content')

@include('includes.page-head', ['title' => __('site.events'), 'intro' => __('site.events_intro')])

<section class="section">
  <div class="container">
    @if ($events->count())
      <div class="grid" style="margin-bottom:72px">
        @foreach ($events as $item)
          @include('includes.event')
        @endforeach
      </div>
    @else
      <div class="card empty" style="margin-bottom:72px">{{ __('site.no_events') }}</div>
    @endif
  </div>
</section>

@endsection
