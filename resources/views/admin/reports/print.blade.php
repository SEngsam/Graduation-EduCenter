<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}" data-theme="light">
<head>
  <meta charset="utf-8">
  <title>{{ __('dash.report_title') }}</title>
  <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600&family=Readex+Pro:wght@500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/tokens.css') }}">
  <link rel="stylesheet" href="{{ asset('css/site.css') }}">
  <style>
    body { background: #fff; padding: 32px; }
    .print-head { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 24px; }
    .print-head p { margin: 4px 0 0; color: var(--ink-subtle); }
    @media print { body { padding: 0; } .card { break-inside: avoid; } }
  </style>
</head>
<body class="report-print">
  <div class="print-head">
    <div>
      <h1>{{ __('dash.report_title') }}</h1>
      <p>{{ __('dash.reports_sub', ['from' => $from->translatedFormat('F Y')]) }}</p>
    </div>
    <small class="muted">{{ __('dash.generated_at', ['date' => now()->translatedFormat('j F Y')]) }}</small>
  </div>
  @include('admin.reports.content')
  <script>window.addEventListener('load', function () { window.print(); });</script>
</body>
</html>
