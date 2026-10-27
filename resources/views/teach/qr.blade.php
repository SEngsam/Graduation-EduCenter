@extends('layouts.teacher')

@section('title', __('qr.title') . ' · ' . $course->title)

@section('content')

<div class="qr-top">
  <div>
    <h1>{{ __('qr.title') }}</h1>
    <p class="muted">{{ $course->title }} · {{ $session->starts_at->translatedFormat('l g:i a') }}@if ($session->room) · <bdi>{{ $session->room }}</bdi>@endif</p>
  </div>
  <div class="qr-actions">
    <button type="button" class="btn btn-outline" id="full">{{ __('qr.fullscreen') }}</button>
    <form method="POST" action="{{ route('teach.qr.close', $session) }}" data-confirm="{{ __('qr.confirm_close') }}" data-confirm-ok="{{ __('qr.close') }}">
      @csrf
      <button class="btn btn-dark">{{ __('qr.close') }}</button>
    </form>
  </div>
</div>

<div class="split">
  <section class="qr-board split-main" id="board">
    <span class="qr-scan">{{ __('qr.scan') }}</span>
    <div class="qr-box" id="qr">{!! \App\Support\Qr::svg(route('attendance.here', ['code' => $session->qrCode()])) !!}</div>
    <div class="qr-timer">
      <span class="bar"><span id="timer-bar" style="width: 100%"></span></span>
      <span id="timer-text">{{ __('qr.changes_in', ['s' => 60 - time() % 60]) }}</span>
    </div>
    <span class="qr-manual">
      {!! __('qr.or_open', ['url' => '<strong dir="ltr">' . e(preg_replace('#^https?://#', '', route('attendance.here'))) . '</strong>']) !!}
      <strong class="qr-code" dir="ltr" id="code">{{ substr($session->qrCode(), 0, 3) }} {{ substr($session->qrCode(), 3) }}</strong>
    </span>
  </section>

  <section class="card panel split-side">
    <div class="qr-count">
      <h2>{{ __('qr.arrived') }}</h2>
      <span><strong id="count">0</strong> <small>{{ __('qr.of', ['n' => $total]) }}</small></span>
    </div>
    <div id="arrived">
      <p class="muted">{{ __('qr.nobody_yet') }}</p>
    </div>
    <p class="muted small">{{ __('qr.hint') }}</p>
  </section>
</div>

<script>
  var statusUrl = '{{ route('teach.qr.status', $session) }}';
  var changesText = @json(__('qr.changes_in', ['s' => ':s']));
  var lateText = @json(__('qr.late'));
  var nobodyText = @json(__('qr.nobody_yet'));
  var lastCode = '';
  var seconds = {{ 60 - time() % 60 }};

  function escapeHtml(text) {
    var div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
  }

  function showTimer() {
    document.getElementById('timer-text').textContent = changesText.replace(':s', seconds);
    document.getElementById('timer-bar').style.width = (seconds * 100 / 60) + '%';
  }

  function refresh() {
    fetch(statusUrl, { headers: { 'Accept': 'application/json' } })
      .then(function (res) { return res.json(); })
      .then(function (data) {
        if (!data.open) {
          window.location.reload();
          return;
        }

        if (data.code != lastCode) {
          lastCode = data.code;
          document.getElementById('qr').innerHTML = data.svg;
          document.getElementById('code').textContent = data.code.substr(0, 3) + ' ' + data.code.substr(3);
        }
        seconds = data.seconds;
        showTimer();

        document.getElementById('count').textContent = data.arrived.length;
        var html = '';
        for (var i = 0; i < data.arrived.length; i++) {
          var row = data.arrived[i];
          html += '<div class="qr-row"><span class="avatar sm">' + escapeHtml(row.name.charAt(0)) + '</span>'
            + '<span class="qr-name">' + escapeHtml(row.name) + '</span>'
            + (row.late ? '<span class="pill warn">' + lateText + '</span>' : '')
            + '<small dir="ltr">' + row.time + '</small></div>';
        }
        document.getElementById('arrived').innerHTML = html || '<p class="muted">' + nobodyText + '</p>';
      });
  }

  refresh();
  setInterval(refresh, 4000);
  setInterval(function () {
    if (seconds > 1) {
      seconds--;
      showTimer();
    } else {
      refresh();
    }
  }, 1000);

  document.getElementById('full').addEventListener('click', function () {
    var board = document.getElementById('board');
    if (board.requestFullscreen) {
      board.requestFullscreen();
    }
  });
</script>

@endsection
