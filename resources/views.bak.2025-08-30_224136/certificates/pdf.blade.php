<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<style>
  @page { margin: 30px; }
  body { font-family: DejaVu Sans, Arial, sans-serif; }
  .wrap { position: relative; border: 4px solid #9cafaa; padding: 24px; }
  .watermark {
    position: absolute; top: 35%; left: 10%; right: 10%; text-align: center;
    font-size: 90px; color: rgba(0,0,0,0.05); transform: rotate(-15deg);
  }
  .header { display:flex; align-items:center; justify-content:space-between; margin-bottom: 16px; }
  .logo { height: 60px; }
  h1 { margin: 8px 0; font-size: 28px; }
  .grid { display: table; width: 100%; }
  .row { display: table-row; }
  .cell { display: table-cell; padding: 6px 8px; vertical-align: top; }
  .qr { text-align:right; }
  .muted { color: #666; font-size: 12px; }
  .rtl { direction: rtl; text-align: right; }
</style>
</head>
<body>
<div class="wrap">
  <div class="watermark">SawaedUAE</div>
  <div class="header">
    <div>
      <img class="logo" src="{{ public_path('logo.png') }}" onerror="this.style.display='none'">
      <h1>{{ __('Certificate of Volunteering') }} / شهادة تطوع</h1>
    </div>
    <div class="qr">
      {!! QrCode::size(90)->generate(url('/verify/'.urlencode($cert->code ?? $cert->verification_code))) !!}
    </div>
  </div>

  <p>
    {{ __('This is to certify that') }}
    <strong>{{ $user->name }}</strong>
    {{ __('has successfully contributed') }}
    <strong>{{ number_format((float)($cert->hours ?? 0),2) }}</strong>
    {{ __('volunteer hours for') }}
    <strong>{{ optional($opportunity)->title }}</strong>.
  </p>

  <p class="rtl">
    نُشْهِد بأن <strong>{{ $user->name }}</strong> قد أكمل
    <strong>{{ number_format((float)($cert->hours ?? 0),2) }}</strong> ساعة تطوع
    ضمن <strong>{{ optional($opportunity)->title }}</strong>.
  </p>

  <div class="grid" style="margin-top:10px;">
    <div class="row">
      <div class="cell"><strong>{{ __('Issued Date') }}</strong><br>{{ $cert->issued_date ?? optional($cert->issued_at)->format('Y-m-d') }}</div>
      <div class="cell"><strong>{{ __('Code') }}</strong><br><code>{{ $cert->code ?? $cert->verification_code }}</code></div>
      <div class="cell"><strong>{{ __('Certificate No.') }}</strong><br><code>{{ $cert->certificate_number }}</code></div>
    </div>
  </div>

  <p class="muted" style="margin-top:14px;">
    {{ __('Verify at') }}: {{ url('/verify/'.urlencode($cert->code ?? $cert->verification_code)) }}
    · SHA-256: <code>{{ $cert->checksum }}</code>
  </p>
</div>
</body>
</html>
