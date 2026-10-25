@extends('admin.master')

@section('title', __('dash.edit_about'))
@section('crumbs')
  <a href="{{ route('admin.abouts.index') }}">{{ __('dash.abouts') }}</a> / {{ $about->title }}
@endsection

@section('content')
<div class="admin-title"><h1>{{ __('dash.edit_about') }}</h1></div>
@include('admin.abouts.form', ['about' => $about])
@endsection
