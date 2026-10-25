@extends('admin.master')

@section('title', __('dash.edit_event'))
@section('crumbs')
  <a href="{{ route('admin.events.index') }}">{{ __('dash.events') }}</a> / {{ Str::limit($event->description, 40) }}
@endsection

@section('content')
<div class="admin-title"><h1>{{ __('dash.edit_event') }}</h1></div>
@include('admin.events.form', ['event' => $event])
@endsection
