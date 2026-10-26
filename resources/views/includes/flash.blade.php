@php
  $messages = [];
  if (session('success')) $messages[] = ['ok', session('success')];
  if (session('error')) $messages[] = ['err', session('error')];
  if (session('msg')) $messages[] = [session('type') == 'danger' ? 'err' : 'ok', session('msg')];
@endphp
@foreach ($messages as [$kind, $text])
  <div class="flash {{ $kind == 'err' ? 'error' : '' }}" role="{{ $kind == 'err' ? 'alert' : 'status' }}">
    @include('includes.icon', ['name' => $kind == 'err' ? 'alert' : 'check', 'size' => 20])
    <span>{{ $text }}</span>
    <button type="button" class="flash-close" aria-label="{{ __('site.close') }}" onclick="this.parentElement.remove()">×</button>
  </div>
@endforeach
