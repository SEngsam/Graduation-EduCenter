@extends('admin.master')

@section('title', __('dash.reports'))
@section('crumbs', __('dash.reports'))

@section('content')

<div class="admin-title">
  <div>
    <h1>{{ __('dash.reports') }}</h1>
    <p>{{ __('dash.reports_sub', ['from' => $from->translatedFormat('F Y')]) }}</p>
  </div>
  <div class="actions">
    <form method="GET" action="{{ route('admin.reports.index') }}">
      <label class="sr-only" for="months">{{ __('dash.period') }}</label>
      <select id="months" name="months" onchange="this.form.submit()" class="select">
        @foreach ([3, 6, 12] as $n)
          <option value="{{ $n }}" {{ $months == $n ? 'selected' : '' }}>{{ $n == 12 ? __('dash.last_year') : __('dash.last_months', ['n' => $n]) }}</option>
        @endforeach
      </select>
    </form>
    <a href="{{ route('admin.reports.print', ['months' => $months]) }}" target="_blank" class="btn btn-outline">{{ __('dash.print_pdf') }}</a>
    <a href="{{ route('admin.reports.export', ['months' => $months]) }}" class="btn btn-outline">@include('includes.icon', ['name' => 'download', 'size' => 18]) {{ __('dash.export_excel') }}</a>
  </div>
</div>

@include('admin.reports.content')

@endsection
