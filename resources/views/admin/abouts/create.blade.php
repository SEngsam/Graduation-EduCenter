@extends('admin.master')

@section('title', __('dash.add_about'))
@section('crumbs')
  <a href="{{ route('admin.abouts.index') }}">{{ __('dash.abouts') }}</a> / {{ __('dash.add_about') }}
@endsection

@section('content')
<div class="admin-title"><h1>{{ __('dash.add_about') }}</h1></div>
@include('admin.abouts.form')
@endsection
