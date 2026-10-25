@extends('admin.master')

@section('title', __('dash.add_teacher'))
@section('crumbs')
  <a href="{{ route('admin.teachers.index') }}">{{ __('dash.teachers') }}</a> / {{ __('dash.add_teacher') }}
@endsection

@section('content')
<div class="admin-title"><h1>{{ __('dash.add_teacher') }}</h1></div>
@include('admin.teachers.form')
@endsection
