<div class="lesson-row">
  <span class="lesson-icon">@include('includes.icon', ['name' => ['video' => 'play', 'file' => 'file', 'link' => 'link', 'text' => 'book'][$lesson->type], 'size' => 20])</span>
  <span class="lesson-info">
    <strong>{{ $lesson->title }}</strong>
    <small>{{ __('teach.type_' . $lesson->type) }}</small>
  </span>
  @if ($lesson->is_published)
    <span class="pill ok">{{ __('teach.published') }}</span>
  @else
    <span class="pill">{{ __('teach.draft') }}</span>
  @endif
  <form action="{{ route('teach.lessons.publish', $lesson) }}" method="POST">
    @csrf
    @method('PATCH')
    <button class="btn btn-outline btn-sm">{{ $lesson->is_published ? __('teach.hide') : __('teach.publish') }}</button>
  </form>
  <form action="{{ route('teach.lessons.destroy', $lesson) }}" method="POST" data-confirm="{{ __('teach.confirm_delete_lesson', ['name' => $lesson->title]) }}" data-confirm-ok="{{ __('teach.delete') }}">
    @csrf
    @method('DELETE')
    <button class="btn btn-danger-soft btn-sm">{{ __('teach.delete') }}</button>
  </form>
</div>
