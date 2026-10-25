@extends('admin.master')

@section('title', __('dash.edit_news'))
@section('crumbs')
  <a href="{{ route('admin.news.index') }}">{{ __('dash.news') }}</a> / {{ $new->title }}
@endsection

@section('content')
<div class="admin-title"><h1>{{ __('dash.edit_news') }}</h1></div>
@include('admin.news.form', ['new' => $new])
@endsection
