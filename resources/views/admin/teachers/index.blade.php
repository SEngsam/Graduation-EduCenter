@extends('admin.master')

@section('title', __('dash.teachers'))
@section('crumbs', __('dash.teachers'))

@section('content')

<div class="admin-title">
  <div>
    <h1>{{ __('dash.teachers') }}</h1>
    <p>{{ __('dash.teachers_sub') }}</p>
  </div>
  @if ($me->hasAbility('add_teacher'))
    <div class="actions"><a href="{{ route('admin.teachers.create') }}" class="btn btn-primary">@include('includes.icon', ['name' => 'plus', 'size' => 18]) {{ __('dash.new_teacher') }}</a></div>
  @endif
</div>

<div class="filters">
  <form action="{{ route('admin.teachers.index') }}" method="GET">
    <label for="category_name">{{ __('dash.category') }}</label>
    <select id="category_name" name="category_name" onchange="this.form.submit()">
      <option value="">{{ __('dash.all') }}</option>
      @foreach ($categories as $category)
        <option value="{{ $category->name_major }}" {{ request('category_name') == $category->name_major ? 'selected' : '' }}>{{ $category->name_major }}</option>
      @endforeach
    </select>
    <noscript><button type="submit" class="btn btn-outline">{{ __('dash.filter') }}</button></noscript>
  </form>
</div>

<section class="table-box">
  <div class="table-scroll">
    <table class="data-table">
      <thead>
        <tr>
          <th>{{ __('dash.teacher') }}</th>
          <th>{{ __('dash.major') }}</th>
          <th>{{ __('dash.category') }}</th>
          <th>{{ __('dash.account') }}</th>
          <th>{{ __('dash.actions') }}</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($teachers as $teacher)
          @php
            $photo = $teacher->photo();
          @endphp
          <tr>
            <td><span class="record"><img class="round" src="{{ $photo }}" alt="">{{ $teacher->name }}</span></td>
            <td>{{ $teacher->major }}</td>
            <td>
              @if ($teacher->category && $teacher->category->name_major)
                <span class="pill">{{ $teacher->category->name_major }}</span>
              @else
                <span class="muted">—</span>
              @endif
            </td>
            <td class="muted">{{ optional($teacher->user)->email ?? '—' }}</td>
            <td>
              @include('admin.partials.row-actions', [
                'label' => $teacher->name,
                'edit' => $me->hasAbility('edit_teacher') ? route('admin.teachers.edit', $teacher->id) : null,
                'destroy' => $me->hasAbility('delete_teacher') ? route('admin.teachers.destroy', $teacher->id) : null,
              ])
            </td>
          </tr>
        @empty
          <tr><td colspan="5" class="muted" style="text-align:center">{{ __('dash.no_records') }}</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @include('admin.partials.table-foot', ['items' => $teachers])
</section>

@endsection
