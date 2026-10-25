@extends('admin.master')

@section('title', __('dash.users'))
@section('crumbs', __('dash.users'))

@section('content')

<div class="admin-title">
  <div>
    <h1>{{ __('dash.users') }}</h1>
    <p>{{ __('dash.users_sub') }}</p>
  </div>
  <div class="actions">
    <a href="{{ route('admin.roles.index') }}" class="btn btn-outline">{{ __('dash.manage_roles') }}</a>
    @if ($me->hasAbility('add_user'))
      <a href="{{ route('admin.users.create') }}" class="btn btn-primary">@include('includes.icon', ['name' => 'plus', 'size' => 18]) {{ __('dash.new_user') }}</a>
    @endif
  </div>
</div>

<div class="filters">
  <form action="{{ route('admin.users.index') }}" method="GET">
    <label class="sr-only" for="q">{{ __('dash.search_placeholder') }}</label>
    <input type="search" id="q" name="q" value="{{ request('q') }}" placeholder="{{ __('dash.search_placeholder') }}">
    <label for="filter_role">{{ __('dash.role') }}</label>
    <select id="filter_role" name="role_id">
      <option value="">{{ __('dash.all') }}</option>
      @foreach ($roles as $role)
        <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
      @endforeach
    </select>
    <button type="submit" class="btn btn-outline">{{ __('dash.filter') }}</button>
  </form>
</div>

<section class="table-box">
  <div class="table-scroll">
    <table class="data-table">
      <thead>
        <tr>
          <th>{{ __('dash.user') }}</th>
          <th>{{ __('dash.email') }}</th>
          <th>{{ __('dash.role') }}</th>
          <th>{{ __('dash.created_at') }}</th>
          <th>{{ __('dash.actions') }}</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($users as $user)
          <tr>
            <td><span class="record"><span class="avatar" aria-hidden="true">{{ mb_substr($user->name, 0, 1) }}</span>{{ $user->name }}</span></td>
            <td class="muted">{{ $user->email }}</td>
            <td>
              @if ($user->role && $user->role->name)
                <span class="pill {{ $user->role_id == \App\Models\Role::ROLE_ADMIN ? 'strong' : '' }}">{{ $user->role->name }}</span>
              @else
                <span class="muted">{{ __('dash.no_role') }}</span>
              @endif
            </td>
            <td class="muted">{{ optional($user->created_at)->translatedFormat('j M Y') }}</td>
            <td>
              <span class="row-actions">
                @if ($me->hasAbility('edit_user'))
                  <button type="button" class="btn btn-outline btn-sm" data-edit-user="{{ json_encode($user->only(['id', 'name', 'email', 'role_id'])) }}">{{ __('dash.edit') }}</button>
                @endif
                @if ($me->hasAbility('delete_user') && $user->id !== $me->id)
                  <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" data-confirm="{{ __('dash.confirm_delete', ['name' => $user->name]) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger-soft btn-sm">{{ __('dash.delete') }}</button>
                  </form>
                @endif
              </span>
            </td>
          </tr>
        @empty
          <tr><td colspan="5" class="muted" style="text-align:center">{{ __('dash.no_records') }}</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @include('admin.partials.table-foot', ['items' => $users])
</section>

@if ($me->hasAbility('edit_user'))
  <dialog class="sheet" id="editUser" aria-labelledby="editUserTitle">
    <form method="POST" id="editUserForm">
      @csrf
      @method('PUT')
      <h2 id="editUserTitle">{{ __('dash.edit_user') }}</h2>
      <div class="field">
        <label for="eu_name">{{ __('dash.name') }}</label>
        <input type="text" id="eu_name" name="name" required>
      </div>
      <div class="field">
        <label for="eu_email">{{ __('dash.email') }}</label>
        <input type="email" id="eu_email" name="email" required>
      </div>
      <div class="field">
        <label for="eu_role">{{ __('dash.role') }}</label>
        <select id="eu_role" name="role_id">
          <option value="">{{ __('dash.no_role') }}</option>
          @foreach ($roles as $role)
            <option value="{{ $role->id }}">{{ $role->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="form-actions">
        <button type="button" class="btn btn-outline" data-close>{{ __('dash.cancel') }}</button>
        <button type="submit" class="btn btn-primary">{{ __('dash.save') }}</button>
      </div>
    </form>
  </dialog>
@endif

@endsection

@section('scripts')
<script>
  (function () {
    var dialog = document.getElementById('editUser');
    if (!dialog) return;
    var actionTpl = @json(route('admin.users.update', '__ID__'));
    document.querySelectorAll('[data-edit-user]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var u = JSON.parse(btn.dataset.editUser);
        document.getElementById('editUserForm').action = actionTpl.replace('__ID__', u.id);
        document.getElementById('eu_name').value = u.name;
        document.getElementById('eu_email').value = u.email;
        document.getElementById('eu_role').value = u.role_id || '';
        dialog.showModal();
      });
    });
    dialog.querySelector('[data-close]').addEventListener('click', function () { dialog.close(); });
  })();
</script>
@endsection
