@php
  $event = $event ?? null;
  $photo = $event && $event->image && file_exists(public_path('uploads/events/' . $event->image))
      ? asset('uploads/events/' . $event->image)
      : null;
@endphp
<form class="card admin-form" style="max-width:760px" action="{{ $event ? route('admin.events.update', $event->id) : route('admin.events.store') }}" method="POST" enctype="multipart/form-data">
  @csrf
  @if ($event)
    @method('PUT')
  @endif

  <div class="field">
    <label for="description">{{ __('dash.event') }}</label>
    <textarea id="description" name="description" rows="3" required>{{ old('description', optional($event)->description) }}</textarea>
    @error('description')<span class="error">{{ $message }}</span>@enderror
  </div>

  <div class="field">
    <label for="location">{{ __('dash.location') }}</label>
    <input type="text" id="location" name="location" value="{{ old('location', optional($event)->location) }}" required>
    @error('location')<span class="error">{{ $message }}</span>@enderror
  </div>

  @include('admin.partials.upload', ['name' => 'image', 'current' => $photo, 'required' => ! $event])

  <div class="form-actions">
    <a href="{{ route('admin.events.index') }}" class="btn btn-outline">{{ __('dash.cancel') }}</a>
    <button type="submit" class="btn btn-primary">{{ $event ? __('dash.save') : __('dash.add_event') }}</button>
  </div>
</form>
