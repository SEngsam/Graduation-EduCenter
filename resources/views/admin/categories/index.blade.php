@extends('admin.master')

@section('title', __('dash.categories'))
@section('crumbs', __('dash.categories'))

@section('content')

<div class="admin-title">
  <div>
    <h1>{{ __('dash.categories') }}</h1>
    <p>{{ __('dash.categories_sub') }}</p>
  </div>
  @if ($me->hasAbility('add_category'))
    <div class="actions"><a href="{{ route('admin.categories.create') }}" class="btn btn-primary">@include('includes.icon', ['name' => 'plus', 'size' => 18]) {{ __('dash.new_category') }}</a></div>
  @endif
</div>

<section class="table-box">
  <div class="table-scroll">
    <table class="data-table">
      <thead>
        <tr>
          <th>{{ __('dash.category') }}</th>
          <th>{{ __('dash.title') }}</th>
          <th>{{ __('dash.description') }}</th>
          <th>{{ __('dash.created_at') }}</th>
          <th>{{ __('dash.actions') }}</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($categories as $category)
          @php
            $photo = $category->image && file_exists(public_path('uploads/categories/' . $category->image))
                ? asset('uploads/categories/' . $category->image)
                : asset('frontend/images/courses/course-' . ($category->id % 6 + 1) . '.jpg');
          @endphp
          <tr>
            <td><span class="record"><img src="{{ $photo }}" alt="">{{ $category->name_major }}</span></td>
            <td>{{ $category->title }}</td>
            <td class="muted clip">{{ $category->description }}</td>
            <td class="muted">{{ optional($category->created_at)->translatedFormat('j M Y') }}</td>
            <td>
              @include('admin.partials.row-actions', [
                'label' => $category->name_major,
                'edit' => $me->hasAbility('edit_category') ? route('admin.categories.edit', $category->id) : null,
                'destroy' => $me->hasAbility('delete_category') ? route('admin.categories.destroy', $category->id) : null,
              ])
            </td>
          </tr>
        @empty
          <tr><td colspan="5" class="muted" style="text-align:center">{{ __('dash.no_records') }}</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @include('admin.partials.table-foot', ['items' => $categories])
</section>

@endsection
