@php
  $role = $role ?? null;
  $checked = old('ability', $role ? $role->abilities->pluck('id')->all() : []);

  // group abilities by the resource in their code: all_courses, add_course, edit_course → courses
  $resources = [
    'user' => 'users', 'users' => 'users', 'category' => 'categories', 'categories' => 'categories',
    'course' => 'courses', 'courses' => 'courses', 'teacher' => 'teachers', 'teachers' => 'teachers',
    'about' => 'abouts', 'abouts' => 'abouts', 'event' => 'events', 'events' => 'events',
    'new' => 'news', 'news' => 'news', 'newss' => 'news', 'roles' => 'roles', 'students' => 'students',
  ];
  $groups = $abilities->groupBy(function ($ability) use ($resources) {
      $base = preg_replace('/^(all|single|add|edit|delete|assign)_/', '', $ability->code);
      return $resources[$base] ?? 'other';
  });
@endphp
<form class="card admin-form" action="{{ $role ? route('admin.roles.update', $role->id) : route('admin.roles.store') }}" method="POST">
  @csrf
  @if ($role)
    @method('PUT')
  @endif

  <div class="field" style="max-width:420px">
    <label for="name">{{ __('dash.role_name') }}</label>
    <input type="text" id="name" name="name" value="{{ old('name', optional($role)->name) }}" required>
    @error('name')<span class="error">{{ $message }}</span>@enderror
  </div>

  <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap">
    <h2 style="font-size:20px">{{ __('dash.abilities') }}</h2>
    <label class="check check-all"><input type="checkbox" id="check_all"> {{ __('dash.select_all') }}</label>
  </div>

  @foreach ($groups as $group => $items)
    <fieldset class="check-group">
      <legend>{{ __('dash.' . $group) }}</legend>
      <ul class="checks">
        @foreach ($items as $ability)
          <li>
            <label>
              <input type="checkbox" name="ability[]" value="{{ $ability->id }}" {{ in_array($ability->id, $checked) ? 'checked' : '' }}>
              {{ $ability->name }}
            </label>
          </li>
        @endforeach
      </ul>
    </fieldset>
  @endforeach

  <div class="form-actions">
    <a href="{{ route('admin.roles.index') }}" class="btn btn-outline">{{ __('dash.cancel') }}</a>
    <button type="submit" class="btn btn-primary">{{ $role ? __('dash.save') : __('dash.add_role') }}</button>
  </div>
</form>

@section('scripts')
<script>
  document.getElementById('check_all').addEventListener('change', function () {
    var on = this.checked;
    document.querySelectorAll('input[name="ability[]"]').forEach(function (box) { box.checked = on; });
  });
</script>
@endsection
