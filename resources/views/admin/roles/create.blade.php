@extends('admin.master')

@section('title', __('dash.add_role'))
@section('crumbs')
  <a href="{{ route('admin.roles.index') }}">{{ __('dash.roles') }}</a> / {{ __('dash.add_role') }}
@endsection

@section('content')
<div class="admin-title"><h1>{{ __('dash.add_role') }}</h1></div>
@include('admin.roles.form')
@endsection
