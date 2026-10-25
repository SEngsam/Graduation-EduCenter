@extends('admin.master')

@section('title', __('dash.add_category'))
@section('crumbs')
  <a href="{{ route('admin.categories.index') }}">{{ __('dash.categories') }}</a> / {{ __('dash.add_category') }}
@endsection

@section('content')
<div class="admin-title"><h1>{{ __('dash.add_category') }}</h1></div>
@include('admin.categories.form')
@endsection
