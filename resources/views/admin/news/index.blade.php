@extends('admin.master')

@section('title', __('dash.news'))
@section('crumbs', __('dash.news'))

@section('content')

<div class="admin-title">
  <div>
    <h1>{{ __('dash.news') }}</h1>
    <p>{{ __('dash.news_sub') }}</p>
  </div>
  <div class="actions"><a href="{{ route('admin.news.create') }}" class="btn btn-primary">@include('includes.icon', ['name' => 'plus', 'size' => 18]) {{ __('dash.new_news') }}</a></div>
</div>

<section class="table-box">
  <div class="table-scroll">
    <table class="data-table">
      <thead>
        <tr>
          <th>{{ __('dash.post') }}</th>
          <th>{{ __('dash.author') }}</th>
          <th>{{ __('dash.date') }}</th>
          <th>{{ __('dash.actions') }}</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($n as $post)
          @php
            $photo = $post->image && file_exists(public_path('uploads/news/' . $post->image))
                ? asset('uploads/news/' . $post->image)
                : asset('frontend/images/blog/post-' . ($post->id % 3 + 1) . '.jpg');
          @endphp
          <tr>
            <td><span class="record"><img src="{{ $photo }}" alt="">{{ $post->title }}</span></td>
            <td>{{ $post->auther }}</td>
            <td class="muted">{{ $post->date }}</td>
            <td>
              @include('admin.partials.row-actions', [
                'label' => $post->title,
                'edit' => route('admin.news.edit', $post->id),
                'destroy' => route('admin.news.destroy', $post->id),
              ])
            </td>
          </tr>
        @empty
          <tr><td colspan="4" class="muted" style="text-align:center">{{ __('dash.no_records') }}</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @include('admin.partials.table-foot', ['items' => $n])
</section>

@endsection
