@extends('admin.master')

@section('title', __('dash.add_course'))
@section('crumbs')
  <a href="{{ route('admin.courses.index') }}">{{ __('dash.courses') }}</a> / {{ __('dash.add_course') }}
@endsection

@section('content')
<div class="admin-title"><h1>{{ __('dash.add_course') }}</h1></div>
@include('admin.courses.form')
@endsection
