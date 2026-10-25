@php
  $about = $about ?? null;
  $photo = $about && $about->image && file_exists(public_path('uploads/abouts/' . $about->image))
      ? asset('uploads/abouts/' . $about->image)
      : null;
@endphp
<form class="card admin-form" style="max-width:760px" action="{{ $about ? route('admin.abouts.update', $about->id) : route('admin.abouts.store') }}" method="POST" enctype="multipart/form-data">
  @csrf
  @if ($about)
    @method('PUT')
  @endif

  <div class="field">
    <label for="title">{{ __('dash.title') }}</label>
    <input type="text" id="title" name="title" value="{{ old('title', optional($about)->title) }}" required>
    @error('title')<span class="error">{{ $message }}</span>@enderror
  </div>

  <div class="field">
    <label for="description">{{ __('dash.description') }}</label>
    <textarea id="description" name="description" rows="5" required>{{ old('description', optional($about)->description) }}</textarea>
    @error('description')<span class="error">{{ $message }}</span>@enderror
  </div>

  <div class="field-row">
    <div class="field">
      <label for="btn_text">{{ __('dash.btn_text') }}</label>
      <input type="text" id="btn_text" name="btn_text" value="{{ old('btn_text', optional($about)->btn_text ?? __('site.read_more')) }}" required>
      @error('btn_text')<span class="error">{{ $message }}</span>@enderror
    </div>
    <div class="field">
      <label for="btn_link">{{ __('dash.btn_link') }}</label>
      <input type="text" id="btn_link" name="btn_link" value="{{ old('btn_link', optional($about)->btn_link ?? '#') }}" required dir="ltr">
      @error('btn_link')<span class="error">{{ $message }}</span>@enderror
    </div>
  </div>

  @include('admin.partials.upload', ['name' => 'image', 'current' => $photo, 'required' => ! $about])

  <div class="form-actions">
    <a href="{{ route('admin.abouts.index') }}" class="btn btn-outline">{{ __('dash.cancel') }}</a>
    <button type="submit" class="btn btn-primary">{{ $about ? __('dash.save') : __('dash.add_about') }}</button>
  </div>
</form>
