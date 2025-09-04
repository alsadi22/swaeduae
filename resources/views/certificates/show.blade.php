@extends('layouts.app')
@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Certificate {{ $cert->code }} | SawaedUAE</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body{background:#f5f7fa}
    .cert{max-width:900px;margin:40px auto;background:#fff;border:8px solid #e4efe9;border-radius:16px;padding:40px;box-shadow:0 10px 30px rgba(0,0,0,.05)}
    .brand{color:#0b3b5a;font-weight:800}
    .accent{color:#0fb9b1}
    .code{letter-spacing:2px}
  </style>
</head>
<body>
<div class="cert text-center">
  <h1 class="brand">SawaedUAE</h1>
  <h4 class="mb-4">{{ __('Certificate of Volunteer Service') }}</h4>
  <p class="lead">{{ __('This is to certify that') }}</p>
  <h2 class="mb-1">{{ $cert->user->name }}</h2>
  <p class="mb-4">{{ __('has successfully completed volunteer service for the event') }}</p>
  <h3 class="mb-1 accent">{{ $cert->opportunity->title }}</h3>
  <p class="mb-4">{{ __('with total service hours of') }} <strong>{{ number_format($cert->hours,2) }}</strong></p>
  <p class="mb-4">{{ __('Issued on') }} {{ optional($cert->issued_at)->format('d M Y') }}</p>
  <div class="mb-3">{{ __('Verification Code') }}: <strong class="code">{{ $cert->code }}</strong></div>
  <img alt="QR" src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ urlencode(route('certificates.verify',$cert->code)) }}">
  <div class="mt-4">
    <a onclick="window.print()" class="btn btn-success">{{ __('Print') }}</a>
    <a class="btn btn-outline-secondary" href="{{ route('certificates.verify',$cert->code) }}" target="_blank">{{ __('Verify Online') }}</a>
  </div>
</div>
</body>
</html>

@endsection
