@extends('layouts.teacher')

@section('title', $course->title)

@section('content')

@include('teach.tabs')

<div class="split">
  <div class="split-main stack">
    @foreach ($course->units as $unit)
      <section class="unit">
        <div class="unit-head">
          <div>
            <h2>{{ $unit->title }}</h2>
            <small>{{ trans_choice('teach.lessons_count', $unit->lessons->count(), ['n' => $unit->lessons->count()]) }}</small>
          </div>
          <form action="{{ route('teach.units.destroy', $unit) }}" method="POST" data-confirm="{{ __('teach.confirm_delete_unit', ['name' => $unit->title]) }}" data-confirm-ok="{{ __('teach.delete') }}">
            @csrf
            @method('DELETE')
            <button class="btn btn-outline btn-sm">{{ __('teach.delete') }}</button>
          </form>
        </div>
        @foreach ($unit->lessons as $lesson)
          @include('teach.lesson-row')
        @endforeach
      </section>
    @endforeach

    @if ($loose->count())
      <section class="unit">
        <div class="unit-head"><h2>{{ __('teach.no_unit') }}</h2></div>
        @foreach ($loose as $lesson)
          @include('teach.lesson-row')
        @endforeach
      </section>
    @endif

    @if ($course->lessons_count == 0 && $course->units->count() == 0)
      @include('includes.empty', ['icon' => 'book', 'text' => __('teach.no_lessons')])
    @endif

    <form action="{{ route('teach.units.store', $course) }}" method="POST" class="card inline-form">
      @csrf
      <label class="field">
        <span>{{ __('teach.new_unit') }}</span>
        <input type="text" name="title" placeholder="{{ __('teach.unit_title') }}" required>
      </label>
      <button class="btn btn-dark">{{ __('teach.add_unit') }}</button>
    </form>

    <section class="card panel">
      <h2>{{ __('teach.schedule') }}</h2>
      @forelse ($course->slots as $slot)
        <div class="list-row">
          <span class="list-info">
            <strong>{{ $slot->dayName() }} · {{ $slot->time() }} - {{ $slot->time('ends_at') }}</strong>
            @if ($slot->room)<small>{{ $slot->room }}</small>@endif
          </span>
          <form action="{{ route('teach.slots.destroy', $slot) }}" method="POST">
            @csrf
            @method('DELETE')
            <button class="btn btn-outline btn-sm">{{ __('teach.delete') }}</button>
          </form>
        </div>
      @empty
        <p class="muted">{{ __('teach.no_schedule') }}</p>
      @endforelse
      <form action="{{ route('teach.slots.store', $course) }}" method="POST" class="slot-form">
        @csrf
        <label class="field">
          <span>{{ __('teach.day') }}</span>
          <select name="day">
            @foreach (__('teach.days') as $i => $day)
              <option value="{{ $i }}">{{ $day }}</option>
            @endforeach
          </select>
        </label>
        <label class="field"><span>{{ __('teach.from') }}</span><input type="time" name="starts_at" required></label>
        <label class="field"><span>{{ __('teach.to') }}</span><input type="time" name="ends_at" required></label>
        <label class="field"><span>{{ __('teach.room') }}</span><input type="text" name="room"></label>
        <button class="btn btn-outline">{{ __('teach.add_slot') }}</button>
      </form>
    </section>
  </div>

  <form action="{{ route('teach.lessons.store', $course) }}" method="POST" enctype="multipart/form-data" class="card panel split-side lesson-form">
    @csrf
    <h2>{{ __('teach.new_lesson') }}</h2>

    <label class="field">
      <span>{{ __('teach.lesson_title') }}</span>
      <input type="text" name="title" value="{{ old('title') }}" required>
      @error('title')<small class="error">{{ $message }}</small>@enderror
    </label>

    <fieldset class="field">
      <legend>{{ __('teach.lesson_type') }}</legend>
      <div class="type-picker">
        @foreach (['video' => 'play', 'file' => 'file', 'link' => 'link', 'text' => 'book'] as $type => $icon)
          <label>
            <input type="radio" name="type" value="{{ $type }}" {{ old('type', 'video') == $type ? 'checked' : '' }}>
            <span>@include('includes.icon', ['name' => $icon, 'size' => 18]) {{ __('teach.type_' . $type) }}</span>
          </label>
        @endforeach
      </div>
    </fieldset>

    <label class="field" data-for="video link">
      <span>{{ __('teach.video_url') }}</span>
      <input type="url" name="url" value="{{ old('url') }}" dir="ltr" placeholder="https://">
      @error('url')<small class="error">{{ $message }}</small>@enderror
    </label>

    <label class="field" data-for="file">
      <span>{{ __('teach.lesson_file') }}</span>
      <input type="file" name="file">
      <small class="muted">{{ __('teach.file_hint') }}</small>
      @error('file')<small class="error">{{ $message }}</small>@enderror
    </label>

    <label class="field" data-for="text">
      <span>{{ __('teach.lesson_body') }}</span>
      <textarea name="body" rows="5">{{ old('body') }}</textarea>
      @error('body')<small class="error">{{ $message }}</small>@enderror
    </label>

    <label class="field">
      <span>{{ __('teach.unit') }}</span>
      <select name="course_unit_id">
        <option value="">—</option>
        @foreach ($course->units as $unit)
          <option value="{{ $unit->id }}" {{ old('course_unit_id') == $unit->id ? 'selected' : '' }}>{{ $unit->title }}</option>
        @endforeach
      </select>
    </label>

    <label class="check">
      <input type="checkbox" name="is_published" value="1" checked>
      {{ __('teach.publish_now') }}
    </label>

    <button class="btn btn-primary">{{ __('teach.save_lesson') }}</button>
  </form>
</div>

<script>
  var lessonForm = document.querySelector('.lesson-form');

  function showFields() {
    var type = lessonForm.querySelector('input[name=type]:checked').value;
    lessonForm.querySelectorAll('[data-for]').forEach(function (el) {
      el.hidden = el.getAttribute('data-for').split(' ').indexOf(type) == -1;
    });
  }

  lessonForm.querySelectorAll('input[name=type]').forEach(function (radio) {
    radio.addEventListener('change', showFields);
  });
  showFields();
</script>

@endsection
