@extends('admin.master')

@section('title', __('dash.abouts'))
@section('crumbs', __('dash.abouts'))

@section('content')

<div class="admin-title">
  <div>
    <h1>{{ __('dash.abouts') }}</h1>
    <p>{{ __('dash.abouts_sub') }}</p>
  </div>
  <div class="actions"><a href="{{ route('admin.abouts.create') }}" class="btn btn-primary">@include('includes.icon', ['name' => 'plus', 'size' => 18]) {{ __('dash.new_about') }}</a></div>
</div>

<section class="table-box">
  <div class="table-scroll">
    <table class="data-table">
      <thead>
        <tr>
          <th>{{ __('dash.title') }}</th>
          <th>{{ __('dash.description') }}</th>
          <th>{{ __('dash.btn_text') }}</th>
          <th>{{ __('dash.actions') }}</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($abouts as $about)
          @php
            $photo = $about->photo();
          @endphp
          <tr>
            <td><span class="record"><img src="{{ $photo }}" alt="">{{ $about->title }}</span></td>
            <td class="muted clip">{{ $about->description }}</td>
            <td>{{ $about->btn_text }}</td>
            <td>
              @include('admin.partials.row-actions', [
                'label' => $about->title,
                'edit' => route('admin.abouts.edit', $about->id),
                'destroy' => route('admin.abouts.destroy', $about->id),
              ])
            </td>
          </tr>
        @empty
          <tr><td colspan="4" class="muted" style="text-align:center">{{ __('dash.no_records') }}</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @include('admin.partials.table-foot', ['items' => $abouts])
</section>

@endsection
