@extends('admin.master')

@section('title', __('dash.events'))
@section('crumbs', __('dash.events'))

@section('content')

<div class="admin-title">
  <div>
    <h1>{{ __('dash.events') }}</h1>
    <p>{{ __('dash.events_sub') }}</p>
  </div>
  <div class="actions"><a href="{{ route('admin.events.create') }}" class="btn btn-primary">@include('includes.icon', ['name' => 'plus', 'size' => 18]) {{ __('dash.new_event') }}</a></div>
</div>

<section class="table-box">
  <div class="table-scroll">
    <table class="data-table">
      <thead>
        <tr>
          <th>{{ __('dash.event') }}</th>
          <th>{{ __('dash.location') }}</th>
          <th>{{ __('dash.date') }}</th>
          <th>{{ __('dash.actions') }}</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($events as $event)
          @php
            $photo = $event->image && file_exists(public_path('uploads/events/' . $event->image))
                ? asset('uploads/events/' . $event->image)
                : asset('frontend/images/events/event-' . ($event->id % 3 + 1) . '.jpg');
          @endphp
          <tr>
            <td><span class="record"><img src="{{ $photo }}" alt=""><span class="clip">{{ $event->description }}</span></span></td>
            <td>{{ $event->location }}</td>
            <td class="muted">{{ optional($event->created_at)->translatedFormat('j M Y') }}</td>
            <td>
              @include('admin.partials.row-actions', [
                'label' => Str::limit($event->description, 40),
                'edit' => route('admin.events.edit', $event->id),
                'destroy' => route('admin.events.destroy', $event->id),
              ])
            </td>
          </tr>
        @empty
          <tr><td colspan="4" class="muted" style="text-align:center">{{ __('dash.no_records') }}</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @include('admin.partials.table-foot', ['items' => $events])
</section>

@endsection
