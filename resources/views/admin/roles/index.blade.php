@extends('admin.master')

@section('title', __('dash.roles'))
@section('crumbs', __('dash.roles'))

@section('content')

<div class="admin-title">
  <div>
    <h1>{{ __('dash.roles') }}</h1>
    <p>{{ __('dash.roles_sub') }}</p>
  </div>
  <div class="actions"><a href="{{ route('admin.roles.create') }}" class="btn btn-primary">@include('includes.icon', ['name' => 'plus', 'size' => 18]) {{ __('dash.new_role') }}</a></div>
</div>

<section class="table-box">
  <div class="table-scroll">
    <table class="data-table" style="min-width:520px">
      <thead>
        <tr>
          <th>{{ __('dash.role') }}</th>
          <th>{{ __('dash.abilities') }}</th>
          <th>{{ __('dash.users') }}</th>
          <th>{{ __('dash.actions') }}</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($roles as $role)
          <tr>
            <td><span class="record">{{ $role->name }}</span></td>
            <td class="muted">{{ __('dash.abilities_count', ['count' => $role->abilities->count()]) }}</td>
            <td class="muted">{{ \App\Models\User::where('role_id', $role->id)->count() }}</td>
            <td>
              @include('admin.partials.row-actions', [
                'label' => $role->name,
                'edit' => route('admin.roles.edit', $role->id),
                'destroy' => $role->id == \App\Models\Role::ROLE_ADMIN ? null : route('admin.roles.destroy', $role->id),
              ])
            </td>
          </tr>
        @empty
          <tr><td colspan="4" class="muted" style="text-align:center">{{ __('dash.no_records') }}</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @include('admin.partials.table-foot', ['items' => $roles])
</section>

@endsection
