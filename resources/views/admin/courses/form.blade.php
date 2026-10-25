@php
  $course = $course ?? null;
  $photo = $course && $course->image && file_exists(public_path('uploads/courses/' . $course->image))
      ? asset('uploads/courses/' . $course->image)
      : null;
  $canSave = auth()->user()->hasAbility($course ? 'edit_course' : 'add_course');
@endphp
<div class="form-layout">
  <form class="card admin-form" action="{{ $course ? route('admin.courses.update', $course->id) : route('admin.courses.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if ($course)
      @method('PUT')
    @endif

    <div class="field">
      <label for="title">{{ __('dash.title') }}</label>
      <input type="text" id="title" name="title" value="{{ old('title', optional($course)->title) }}" required data-preview="title">
      @error('title')<span class="error">{{ $message }}</span>@enderror
    </div>

    <div class="field">
      <label for="description">{{ __('dash.description') }}</label>
      <textarea id="description" name="description" rows="4" required data-preview="description">{{ old('description', optional($course)->description) }}</textarea>
      @error('description')<span class="error">{{ $message }}</span>@enderror
    </div>

    <div class="field-row">
      <div class="field">
        <label for="teacher_id">{{ __('dash.teacher') }}</label>
        <select id="teacher_id" name="teacher_id" data-preview="teacher">
          <option value="">{{ __('dash.no_teacher') }}</option>
          @foreach ($teachers as $teacher)
            <option value="{{ $teacher->id }}" {{ old('teacher_id', optional($course)->teacher_id) == $teacher->id ? 'selected' : '' }}>{{ $teacher->name }}</option>
          @endforeach
        </select>
        @error('teacher_id')<span class="error">{{ $message }}</span>@enderror
      </div>
      <div class="field">
        <label for="btn_text">{{ __('dash.btn_text') }}</label>
        <input type="text" id="btn_text" name="btn_text" value="{{ old('btn_text', optional($course)->btn_text ?? __('site.enroll')) }}" required data-preview="btn">
        @error('btn_text')<span class="error">{{ $message }}</span>@enderror
      </div>
    </div>

    <div class="field">
      <label for="btn_link">{{ __('dash.btn_link') }}</label>
      <input type="text" id="btn_link" name="btn_link" value="{{ old('btn_link', optional($course)->btn_link ?? '#') }}" required dir="ltr">
      @error('btn_link')<span class="error">{{ $message }}</span>@enderror
    </div>

    @include('admin.partials.upload', ['name' => 'image', 'current' => $photo, 'required' => ! $course])

    <div class="form-actions">
      <a href="{{ route('admin.courses.index') }}" class="btn btn-outline">{{ __('dash.cancel') }}</a>
      @if ($canSave)
        <button type="submit" class="btn btn-primary">{{ $course ? __('dash.save') : __('dash.add_course') }}</button>
      @endif
    </div>
  </form>

  <aside>
    <span class="hint">{{ __('dash.preview_hint') }}</span>
    <article class="card course-card">
      <div class="thumb"><img id="pv-img" src="{{ $photo ?? asset('frontend/images/courses/course-1.jpg') }}" alt=""></div>
      <div class="course-body">
        <h3 id="pv-title">{{ old('title', optional($course)->title) ?: __('dash.title') }}</h3>
        <p id="pv-description">{{ Str::limit(old('description', optional($course)->description), 90) ?: __('dash.description') }}</p>
        <div class="course-foot">
          <span class="teacher" id="pv-teacher">{{ optional(optional($course)->teacher)->name ?? __('dash.no_teacher') }}</span>
          <span class="btn btn-primary btn-sm" id="pv-btn">{{ old('btn_text', optional($course)->btn_text ?? __('site.enroll')) }}</span>
        </div>
      </div>
    </article>
  </aside>
</div>

@section('scripts')
<script>
  // live card preview
  document.querySelectorAll('[data-preview]').forEach(function (el) {
    var target = document.getElementById('pv-' + el.dataset.preview);
    var update = function () {
      var value = el.tagName === 'SELECT' ? el.options[el.selectedIndex].text : el.value;
      target.textContent = el.dataset.preview === 'description' && value.length > 90 ? value.slice(0, 90) + '…' : (value || target.textContent);
    };
    el.addEventListener('input', update);
    el.addEventListener('change', update);
  });
  document.getElementById('image').addEventListener('change', function () {
    if (this.files[0]) document.getElementById('pv-img').src = URL.createObjectURL(this.files[0]);
  });
</script>
@endsection
