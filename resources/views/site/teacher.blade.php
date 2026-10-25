@extends('site.master')

@section('title', __('site.teachers') . ' | ' . config('app.name'))

@section('content')

@include('includes.page-head', ['title' => __('site.teachers'), 'intro' => __('site.teachers_intro')])

@php
  $tones = ['science' => 'sci', 'arts' => 'art', 'technology' => 'tech'];
@endphp

<section class="section">
  <div class="container">
    @if ($categories->count())
      <div class="filter-bar" id="teacherFilter" role="group" aria-label="{{ __('site.categories') }}">
        <button type="button" class="active" aria-pressed="true" data-filter="all">{{ __('site.all') }}</button>
        @foreach ($categories as $category)
          <button type="button" aria-pressed="false" data-filter="{{ $category->id }}">{{ $category->name_major }}</button>
        @endforeach
      </div>
    @endif

    <div class="grid" style="margin-bottom:72px">
      @forelse ($teachers as $item)
        @php
          $photo = $item->image && file_exists(public_path('uploads/teachers/' . $item->image))
              ? asset('uploads/teachers/' . $item->image)
              : asset('frontend/images/teachers/teacher-' . ($item->id % 3 + 1) . '.jpg');
          $catName = optional($item->category)->name_major;
          $tone = $tones[strtolower($catName ?? '')] ?? '';
        @endphp
        <article class="card portrait teacher-card" data-category="{{ $item->categorie_id }}">
          <img src="{{ $photo }}" alt="">
          <div class="course-body">
            @if ($catName)
              <span class="tag {{ $tone ? 'tone-' . $tone : '' }}">{{ $catName }}</span>
            @endif
            <h3>{{ $item->name }}</h3>
            <span style="color:var(--ink-subtle)">{{ $item->major }}</span>
            @if ($item->course->count())
              <span class="courses">{{ $item->course->pluck('title')->join('، ') }}</span>
            @endif
          </div>
        </article>
      @empty
        <div class="card empty">{{ __('site.no_teachers') }}</div>
      @endforelse
    </div>
  </div>
</section>

@endsection

@section('scripts')
<script>
  document.querySelectorAll('#teacherFilter button').forEach(function (btn) {
    btn.addEventListener('click', function () {
      document.querySelectorAll('#teacherFilter button').forEach(function (b) {
        b.classList.remove('active');
        b.setAttribute('aria-pressed', 'false');
      });
      btn.classList.add('active');
      btn.setAttribute('aria-pressed', 'true');
      var filter = btn.dataset.filter;
      document.querySelectorAll('.teacher-card').forEach(function (card) {
        card.style.display = (filter === 'all' || card.dataset.category === filter) ? '' : 'none';
      });
    });
  });
</script>
@endsection
