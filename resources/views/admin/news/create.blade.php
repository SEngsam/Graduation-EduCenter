@extends('admin.master')

@section('title', __('dash.add_news'))
@section('crumbs')
  <a href="{{ route('admin.news.index') }}">{{ __('dash.news') }}</a> / {{ __('dash.add_news') }}
@endsection

@section('content')
<div class="admin-title"><h1>{{ __('dash.add_news') }}</h1></div>
@include('admin.news.form')
@endsection
