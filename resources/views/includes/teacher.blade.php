@php
    $photo = $item->photo();
@endphp
<div class="card teacher-card" data-category="{{ $item->categorie_id }}">
  <img class="avatar" src="{{ $photo }}" alt="">
  <h3>{{ $item->name }}</h3>
  <p>{{ $item->major }}@isset($item->course_count) · {{ trans_choice('site.courses_count', $item->course_count, ['n' => $item->course_count]) }}@endisset</p>
  <a href="{{ route('site.courses', ['teacher' => $item->id]) }}" class="btn btn-outline btn-sm">{{ __('site.see_courses') }}</a>
</div>
