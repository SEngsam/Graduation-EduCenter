@php
    $photo = $item->image && file_exists(public_path('uploads/teachers/' . $item->image))
        ? asset('uploads/teachers/' . $item->image)
        : asset('frontend/images/teachers/teacher-' . ($item->id % 3 + 1) . '.jpg');
@endphp
<div class="card teacher-card" data-category="{{ $item->categorie_id }}">
  <img class="avatar" src="{{ $photo }}" alt="">
  <h3>{{ $item->name }}</h3>
  <p>{{ $item->major }}</p>
</div>
