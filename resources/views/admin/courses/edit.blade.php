@extends('admin.master')

@section('title', __('dash.edit_course'))
@section('crumbs')
  <a href="{{ route('admin.courses.index') }}">{{ __('dash.courses') }}</a> / {{ $course->title }}
@endsection

@section('content')
<div class="admin-title"><h1>{{ __('dash.edit_course') }}</h1></div>
@include('admin.courses.form', ['course' => $course])
@endsection
