@php
  $category = $category ?? null;
  $photo = $category && $category->image && file_exists(public_path('uploads/categories/' . $category->image))
      ? asset('uploads/categories/' . $category->image)
      : null;
@endphp
<form class="card admin-form" style="max-width:760px" action="{{ $category ? route('admin.categories.update', $category->id) : route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
  @csrf
  @if ($category)
    @method('PUT')
  @endif

  <div class="field-row">
    <div class="field">
      <label for="name_major">{{ __('dash.name_major') }}</label>
      <input type="text" id="name_major" name="name_major" value="{{ old('name_major', optional($category)->name_major) }}" required>
      @error('name_major')<span class="error">{{ $message }}</span>@enderror
    </div>
    <div class="field">
      <label for="title">{{ __('dash.title') }}</label>
      <input type="text" id="title" name="title" value="{{ old('title', optional($category)->title) }}" required>
      @error('title')<span class="error">{{ $message }}</span>@enderror
    </div>
  </div>

  <div class="field">
    <label for="description">{{ __('dash.description') }}</label>
    <textarea id="description" name="description" rows="3" required>{{ old('description', optional($category)->description) }}</textarea>
    @error('description')<span class="error">{{ $message }}</span>@enderror
  </div>

  @include('admin.partials.upload', ['name' => 'image', 'current' => $photo, 'required' => ! $category])

  <div class="form-actions">
    <a href="{{ route('admin.categories.index') }}" class="btn btn-outline">{{ __('dash.cancel') }}</a>
    <button type="submit" class="btn btn-primary">{{ $category ? __('dash.save') : __('dash.add_category') }}</button>
  </div>
</form>
