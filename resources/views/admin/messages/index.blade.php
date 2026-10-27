@extends('admin.master')

@section('title', __('dash.messages'))
@section('crumbs', __('dash.messages'))

@section('content')

<div class="admin-title">
  <div>
    <h1>{{ __('dash.messages') }}</h1>
    <p>{{ __('dash.messages_sub') }}</p>
  </div>
</div>

<section class="table-box">
  <div class="table-scroll">
    <table class="data-table">
      <thead>
        <tr>
          <th>{{ __('dash.sender') }}</th>
          <th>{{ __('dash.major') }}</th>
          <th>{{ __('dash.message') }}</th>
          <th>{{ __('dash.created_at') }}</th>
          <th>{{ __('dash.actions') }}</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($messages as $item)
          <tr>
            <td>
              <strong>{{ $item->name }}</strong>
              @if (!$item->read_at)<span class="pill">{{ __('dash.new') }}</span>@endif
              <br><a href="mailto:{{ $item->email }}" class="muted">{{ $item->email }}</a>
            </td>
            <td class="muted">{{ $item->major ?: '—' }}</td>
            <td style="max-width:420px">{{ $item->message }}</td>
            <td class="muted">{{ $item->created_at->translatedFormat('j M Y، g:i a') }}</td>
            <td>
              @include('admin.partials.row-actions', [
                'label' => $item->name,
                'destroy' => route('admin.messages.destroy', $item->id),
              ])
            </td>
          </tr>
        @empty
          <tr><td colspan="5" class="muted" style="text-align:center">{{ __('dash.no_messages') }}</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @include('admin.partials.table-foot', ['items' => $messages])
</section>

@endsection
