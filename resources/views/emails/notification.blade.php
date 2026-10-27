@php
  $safe = [];
  foreach ($params as $key => $value) {
      $safe[$key] = e((string) $value);
  }
  $rtl = app()->getLocale() == 'ar';
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $rtl ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ strip_tags(__('notify.subject_' . $type, $params)) }}</title>
</head>
<body style="margin:0;padding:24px;background:#eef0f5;font-family:Tahoma,Arial,sans-serif;color:#14213d">
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:600px;margin:0 auto;background:#ffffff;border-radius:16px;overflow:hidden;border:1px solid #e3e6ee">
    <tr>
      <td style="padding:20px 28px;background:#14213d">
        <span style="display:inline-block;padding:6px 10px;border-radius:8px;background:#fca311;color:#14213d;font-weight:bold">EC</span>
        <span style="color:#ffffff;font-size:20px;font-weight:bold;margin-inline-start:8px">EduCenter</span>
      </td>
    </tr>
    <tr>
      <td style="padding:28px;text-align:{{ $rtl ? 'right' : 'left' }}">
        <h1 style="margin:0 0 14px;font-size:22px">{{ __('notify.hello', ['name' => $name]) }}</h1>
        <p style="margin:0 0 18px;font-size:16px;line-height:1.8;color:#3d4863">{!! __('notify.' . $type, $safe) !!}</p>
        @if ($course)
          <p style="margin:0 0 18px;font-size:14px;color:#5a6478">{{ $course }}</p>
        @endif
        @if ($type == 'grade' && !empty($params['feedback']))
          <div style="margin:0 0 20px;padding:14px 16px;border-radius:10px;background:#f5f6fa;color:#3d4863;line-height:1.7">
            <strong>{{ __('notify.feedback') }}:</strong> {{ $params['feedback'] }}
          </div>
        @endif
        <a href="{{ $url }}" style="display:inline-block;padding:12px 22px;border-radius:10px;background:#fca311;color:#14213d;font-weight:bold;text-decoration:none">{{ __('notify.open') }}</a>
      </td>
    </tr>
    <tr>
      <td style="padding:16px 28px;border-top:1px solid #e3e6ee;font-size:13px;line-height:1.7;color:#5a6478;text-align:{{ $rtl ? 'right' : 'left' }}">
        {{ __('notify.email_footer') }}
      </td>
    </tr>
  </table>
</body>
</html>
