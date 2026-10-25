@extends('admin.master')

@section('title', __('dash.edit_category'))
@section('crumbs')
  <a href="{{ route('admin.categories.index') }}">{{ __('dash.categories') }}</a> / {{ $category->name_major }}
@endsection

@section('content')
<div class="admin-title"><h1>{{ __('dash.edit_category') }}</h1></div>
@include('admin.categories.form', ['category' => $category])
@endsection
