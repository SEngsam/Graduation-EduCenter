@extends('admin.master')

@section('title', __('dash.add_event'))
@section('crumbs')
  <a href="{{ route('admin.events.index') }}">{{ __('dash.events') }}</a> / {{ __('dash.add_event') }}
@endsection

@section('content')
<div class="admin-title"><h1>{{ __('dash.add_event') }}</h1></div>
@include('admin.events.form')
@endsection
