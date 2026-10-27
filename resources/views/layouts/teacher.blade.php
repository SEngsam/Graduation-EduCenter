@extends('layouts.student')

@section('brand_sub', __('teach.portal'))

@section('nav')
  <a href="{{ route('teach.dashboard') }}" class="{{ Route::is('teach.dashboard') ? 'active' : '' }}">@include('includes.icon', ['name' => 'grid', 'size' => 20]) {{ __('teach.dashboard') }}</a>
  @foreach ($navCourses ?? [] as $navCourse)
    <a href="{{ route('teach.course', $navCourse) }}" class="{{ optional(request()->route('course'))->id === $navCourse->id ? 'active' : '' }}">@include('includes.icon', ['name' => 'book', 'size' => 20]) {{ $navCourse->title }}</a>
  @endforeach
  @include('includes.nav-notifications')
  <a href="{{ route('site.courses') }}">@include('includes.icon', ['name' => 'search', 'size' => 20]) {{ __('site.browse_courses') }}</a>
  <a href="{{ route('student.profile') }}" class="{{ Route::is('student.profile') ? 'active' : '' }}">@include('includes.icon', ['name' => 'user', 'size' => 20]) {{ __('site.profile') }}</a>
@endsection
