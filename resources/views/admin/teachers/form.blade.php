@php
  $teacher = $teacher ?? null;
  $photo = $teacher && $teacher->image && file_exists(public_path('uploads/teachers/' . $teacher->image))
      ? asset('uploads/teachers/' . $teacher->image)
      : null;
  $canSave = $me->hasAbility($teacher ? 'edit_teacher' : 'add_teacher');
@endphp
<form class="card admin-form" style="max-width:760px" action="{{ $teacher ? route('admin.teachers.update', $teacher->id) : route('admin.teachers.store') }}" method="POST" enctype="multipart/form-data">
  @csrf
  @if ($teacher)
    @method('PUT')
  @endif

  <div class="field-row">
    <div class="field">
      <label for="name">{{ __('dash.name') }}</label>
      <input type="text" id="name" name="name" value="{{ old('name', optional($teacher)->name) }}" required>
      @error('name')<span class="error">{{ $message }}</span>@enderror
    </div>
    <div class="field">
      <label for="major">{{ __('dash.major') }}</label>
      <input type="text" id="major" name="major" value="{{ old('major', optional($teacher)->major) }}" required>
      @error('major')<span class="error">{{ $message }}</span>@enderror
    </div>
  </div>

  <div class="field">
    <label for="category_id">{{ __('dash.category') }}</label>
    <select id="category_id" name="category_id">
      <option value="">{{ __('dash.choose_category') }}</option>
      @foreach ($categories as $category)
        <option value="{{ $category->id }}" {{ old('category_id', optional($teacher)->categorie_id) == $category->id ? 'selected' : '' }}>{{ $category->name_major }}</option>
      @endforeach
    </select>
  </div>

  @unless ($teacher)
    <div class="field">
      <label for="email">{{ __('dash.email') }}</label>
      <input type="email" id="email" name="email" value="{{ old('email') }}" required autocomplete="off">
      @error('email')<span class="error">{{ $message }}</span>@enderror
    </div>
    <div class="field-row">
      <div class="field">
        <label for="password">{{ __('dash.password') }}</label>
        <input type="password" id="password" name="password" required autocomplete="new-password" placeholder="{{ __('site.min_8') }}">
        @error('password')<span class="error">{{ $message }}</span>@enderror
      </div>
      <div class="field">
        <label for="password_confirmation">{{ __('dash.password_confirm') }}</label>
        <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password">
      </div>
    </div>
  @endunless

  @include('admin.partials.upload', ['name' => 'image', 'current' => $photo, 'required' => ! $teacher])

  <div class="form-actions">
    <a href="{{ route('admin.teachers.index') }}" class="btn btn-outline">{{ __('dash.cancel') }}</a>
    @if ($canSave)
      <button type="submit" class="btn btn-primary">{{ $teacher ? __('dash.save') : __('dash.add_teacher') }}</button>
    @endif
  </div>
</form>
