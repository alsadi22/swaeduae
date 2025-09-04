<!doctype html>
<html lang="en"><head><meta charset="utf-8"><style>
body{font-family: DejaVu Sans, Arial, sans-serif; margin:40px;}
.border{border:6px solid #ccc; padding:40px;}
.center{text-align:center}
.h1{font-size:36px; font-weight:bold; margin:10px 0}
.h2{font-size:22px; margin:8px 0}
.meta{margin-top:22px; font-size:14px; color:#555}
.flex{display:flex; justify-content:space-between; align-items:center; margin-top:24px}
.qr{width:160px; height:160px}
</style></head>
<body>
  <div class="border">
    <div class="center">
      <div class="h1">{{ __('Certificate of Appreciation') }}</div>
      <div class="h2">{{ $orgName }}</div>
      <p>{{ __('This certifies that') }} <strong>{{ $user->name ?? ('#'.$user->id) }}</strong> {{ __('contributed') }} <strong>{{ $hours }}</strong> {{ __('hours') }} ({{ $minutes }} {{ __('minutes') }}) {{ __('to') }} <strong>{{ $opportunity->title ?? ('#'.$opportunity->id) }}</strong>.</p>
    </div>

    <div class="flex">
      <div class="meta">
        <div>{{ __('Code') }}: {{ $code }}</div>
        <div>{{ __('Verify') }}: {{ $verifyUrl }}</div>
        <div>{{ __('Issued at') }}: {{ now()->toDateString() }}</div>
      </div>
      <div class="qr">{!! $qrSvg !!}</div>
    </div>
  </div>
</body></html>
