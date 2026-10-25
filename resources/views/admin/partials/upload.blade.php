{{-- $name, optional $current (image URL), optional $required --}}
<div class="field">
  <span style="font-weight:500">{{ __('dash.image') }}</span>
  <label class="upload" for="{{ $name }}">
    @if (! empty($current))
      <img src="{{ $current }}" alt="">
    @else
      <span class="icon-box" style="width:56px;height:56px">@include('includes.icon', ['name' => 'plus', 'size' => 24])</span>
    @endif
    <span>
      <strong>{{ __('dash.choose_image') }}</strong>
      <small>{{ empty($current) ? __('dash.image_hint') : __('dash.keep_image') }}</small>
    </span>
    <input type="file" id="{{ $name }}" name="{{ $name }}" accept="image/*" {{ ! empty($required) ? 'required' : '' }}>
  </label>
  @error($name)<span class="error">{{ $message }}</span>@enderror
</div>
