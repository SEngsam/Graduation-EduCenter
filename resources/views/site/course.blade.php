@extends('site.master')

@section('title', __('site.courses') . ' | ' . config('app.name'))

@section('content')

@include('includes.page-head', ['title' => __('site.courses'), 'intro' => __('site.courses_intro')])

<section class="section">
  <div class="container">
    <form class="search-box" action="{{ route('site.courses') }}" method="GET" style="margin-bottom:28px;max-width:560px">
      <label class="sr-only" for="q">{{ __('site.search_placeholder') }}</label>
      <input type="search" id="q" name="q" value="{{ request('q') }}" placeholder="{{ __('site.search_placeholder') }}">
      <button type="submit" class="btn btn-primary">{{ __('site.search') }}</button>
    </form>

    @if ($courses->count())
      <div class="grid">
        @foreach ($courses as $item)
          @include('includes.course')
        @endforeach
      </div>
      <div class="pager">{{ $courses->links() }}</div>
    @else
      <div class="card empty" style="margin-bottom:72px">{{ __('site.no_courses') }}</div>
    @endif
  </div>
</section>

@endsection
