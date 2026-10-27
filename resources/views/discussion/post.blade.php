@php
  $fromTeacher = $course->isTaughtBy($author);
@endphp
<div class="post {{ $fromTeacher ? 'from-teacher' : '' }}">
  <span class="post-avatar">{{ mb_substr($author->name ?? '?', 0, 1) }}</span>
  <div class="post-body">
    <span class="post-meta">
      <strong>{{ $author->name }}</strong>
      @if ($fromTeacher)
        <span class="teacher-tag">{{ __('discussion.teacher') }}</span>
      @endif
      <small>{{ $date->diffForHumans() }}</small>
      @if ($reply && ($isTeacher || $reply->user_id == auth()->id()))
        <form method="POST" action="{{ route('discussion.reply.destroy', $reply) }}" class="post-delete" data-confirm="{{ __('discussion.confirm_reply') }}" data-confirm-ok="{{ __('discussion.delete') }}">
          @csrf
          @method('DELETE')
          <button class="link-btn">{{ __('discussion.delete') }}</button>
        </form>
      @endif
    </span>
    <p>{{ $body }}</p>
  </div>
</div>
