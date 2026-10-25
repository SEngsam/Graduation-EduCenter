@extends('admin.master')

@section('title', __('dash.edit_teacher'))
@section('crumbs')
  <a href="{{ route('admin.teachers.index') }}">{{ __('dash.teachers') }}</a> / {{ $teacher->name }}
@endsection

@section('content')
<div class="admin-title"><h1>{{ __('dash.edit_teacher') }}</h1></div>
@include('admin.teachers.form', ['teacher' => $teacher])
@endsection
