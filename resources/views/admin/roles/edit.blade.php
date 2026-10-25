@extends('admin.master')

@section('title', __('dash.edit_role'))
@section('crumbs')
  <a href="{{ route('admin.roles.index') }}">{{ __('dash.roles') }}</a> / {{ $role->name }}
@endsection

@section('content')
<div class="admin-title"><h1>{{ __('dash.edit_role') }}</h1></div>
@include('admin.roles.form', ['role' => $role])
@endsection
