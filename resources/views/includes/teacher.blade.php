<div class="card teacher-card" data-category="{{ $item->categorie_id }}">
  @if ($item->image && file_exists(public_path('uploads/teachers/' . $item->image)))
    <img class="avatar" src="{{ asset('uploads/teachers/' . $item->image) }}" alt="">
  @else
    <span class="avatar" aria-hidden="true">{{ mb_substr($item->name, 0, 1) }}</span>
  @endif
  <h3>{{ $item->name }}</h3>
  <p>{{ $item->major }}</p>
</div>
