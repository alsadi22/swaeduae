<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <style>
    body { font-family: DejaVu Sans, sans-serif; }
    .wrap { padding: 36px; border: 6px solid #0d6efd; border-radius: 18px; }
    .title { text-align:center; font-size: 28px; margin-bottom: 12px; }
    .meta { text-align:center; color:#555; margin-bottom: 24px; }
    .big { font-size: 40px; text-align:center; margin: 24px 0; }
    .qr { position:absolute; right:40px; bottom:40px; }
  </style>
</head>
<body>
  <div class="wrap">
    <div class="title">{{ __('Certificate of Volunteering') }}</div>
    <div class="meta">{{ __('This certifies that') }}</div>
    <div class="big"><strong>{{ $user->name }}</strong></div>
    <div class="meta">
      {{ __('has successfully contributed') }}
      {{ $cert->hours ? number_format($cert->hours,1).' '.__('hours') : __('volunteer service') }}
      @if($cert->event_id) {{ __('for Event') }} #{{ $cert->event_id }} @endif
      @if($cert->opportunity_id) {{ __('Opportunity') }} #{{ $cert->opportunity_id }} @endif
    </div>
    <div class="meta">{{ __('Certificate Code') }}: <strong>{{ $cert->code }}</strong></div>
    <div class="meta">{{ __('Verify at') }}: {{ $verifyUrl }}</div>
    <div class="qr">
      {!! QrCode::size(120)->generate($verifyUrl) !!}
    </div>
  </div>
</body>
</html>
