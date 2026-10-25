@extends('site.master')

@section('title', __('site.teachers') . ' | ' . config('app.name'))

@section('content')

@include('includes.page-head', ['title' => __('site.teachers'), 'intro' => __('site.teachers_intro')])

<section class="section">
  <div class="container">
    @if ($categories->count())
      <div class="filter-bar" id="teacherFilter">
        <button type="button" class="active" data-filter="all">{{ __('site.all') }}</button>
        @foreach ($categories as $category)
          <button type="button" data-filter="{{ $category->id }}">{{ $category->name_major }}</button>
        @endforeach
      </div>
    @endif

    <div class="grid" style="margin-bottom:72px">
      @foreach ($teachers as $item)
        @include('includes.teacher')
      @endforeach
    </div>
  </div>
</section>

@endsection

@section('scripts')
<script>
  document.querySelectorAll('#teacherFilter button').forEach(function (btn) {
    btn.addEventListener('click', function () {
      document.querySelectorAll('#teacherFilter button').forEach(function (b) { b.classList.remove('active'); });
      btn.classList.add('active');
      var filter = btn.dataset.filter;
      document.querySelectorAll('.teacher-card').forEach(function (card) {
        card.style.display = (filter === 'all' || card.dataset.category === filter) ? '' : 'none';
      });
    });
  });
</script>
@endsection
