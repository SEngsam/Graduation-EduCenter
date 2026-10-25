@php
  $new = $new ?? null;
  $photo = $new && $new->image && file_exists(public_path('uploads/news/' . $new->image))
      ? asset('uploads/news/' . $new->image)
      : null;
@endphp
<form class="card admin-form" style="max-width:760px" action="{{ $new ? route('admin.news.update', $new->id) : route('admin.news.store') }}" method="POST" enctype="multipart/form-data">
  @csrf
  @if ($new)
    @method('PUT')
  @endif

  <div class="field">
    <label for="title">{{ __('dash.title') }}</label>
    <input type="text" id="title" name="title" value="{{ old('title', optional($new)->title) }}" required>
    @error('title')<span class="error">{{ $message }}</span>@enderror
  </div>

  <div class="field">
    <label for="description">{{ __('dash.description') }}</label>
    <textarea id="description" name="description" rows="4" required>{{ old('description', optional($new)->description) }}</textarea>
    @error('description')<span class="error">{{ $message }}</span>@enderror
  </div>

  <div class="field-row">
    <div class="field">
      <label for="auther">{{ __('dash.author') }}</label>
      <input type="text" id="auther" name="auther" value="{{ old('auther', optional($new)->auther ?? $me->name) }}" required>
      @error('auther')<span class="error">{{ $message }}</span>@enderror
    </div>
    <div class="field">
      <label for="date">{{ __('dash.date') }}</label>
      <input type="date" id="date" name="date" value="{{ old('date', optional($new)->date ?? now()->toDateString()) }}" required>
      @error('date')<span class="error">{{ $message }}</span>@enderror
    </div>
  </div>

  <div class="field-row">
    <div class="field">
      <label for="btn_text">{{ __('dash.btn_text') }}</label>
      <input type="text" id="btn_text" name="btn_text" value="{{ old('btn_text', optional($new)->btn_text ?? __('site.read_more')) }}" required>
      @error('btn_text')<span class="error">{{ $message }}</span>@enderror
    </div>
    <div class="field">
      <label for="btn_link">{{ __('dash.btn_link') }}</label>
      <input type="text" id="btn_link" name="btn_link" value="{{ old('btn_link', optional($new)->btn_link ?? '#') }}" required dir="ltr">
      @error('btn_link')<span class="error">{{ $message }}</span>@enderror
    </div>
  </div>

  @include('admin.partials.upload', ['name' => 'image', 'current' => $photo, 'required' => ! $new])

  <div class="form-actions">
    <a href="{{ route('admin.news.index') }}" class="btn btn-outline">{{ __('dash.cancel') }}</a>
    <button type="submit" class="btn btn-primary">{{ $new ? __('dash.save') : __('dash.add_news') }}</button>
  </div>
</form>
