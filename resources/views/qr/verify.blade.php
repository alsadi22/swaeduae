@extends('public.layout')
@section('title', $title ?? 'Page')
@section('content')
<!doctype html><html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Certificate Verification — SwaedUAE</title>
<style>
 body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,Arial,sans-serif;background:#faf7ef;margin:0}
 .wrap{max-width:720px;margin:4rem auto;padding:2rem;background:#fff;border-radius:16px;box-shadow:0 10px 24px rgba(0,0,0,.06)}
 .ok{background:#ecfdf5;color:#065f46;border:1px solid #a7f3d0;padding:.8rem 1rem;border-radius:12px}
 .bad{background:#fef2f2;color:#991b1b;border:1px solid #fecaca;padding:.8rem 1rem;border-radius:12px}
 .kv{display:grid;grid-template-columns:160px 1fr;gap:.4rem .8rem;margin-top:1rem}
 .key{color:#666}
 a.btn{display:inline-block;margin-top:1rem;padding:.6rem .9rem;border-radius:10px;background:#1e66f5;color:#fff;text-decoration:none}
</style></head><body>
<div class="wrap">
  @php($valid = isset($valid) ? $valid : (isset($certificate)))
  @if ($valid)
    <div class="ok"><strong>Valid certificate</strong></div>
    <div class="kv">
      <div class="key">Serial</div><div>{{ $certificate->code ?? $serial ?? '' }}</div>
      <div class="key">Volunteer</div><div>{{ $certificate->volunteer_name ?? $volunteer_name ?? '' }}</div>
      <div class="key">Event</div><div>{{ $certificate->event_title ?? $event_title ?? '' }}</div>
      <div class="key">Hours</div><div>{{ $certificate->hours ?? $hours ?? '' }}</div>
      <div class="key">Issued</div><div>{{ optional($certificate->issued_at ?? null)->format('Y-m-d') }}</div>
    </div>
  @else
    <div class="bad"><strong>Invalid or revoked</strong></div>
    <p class="key">We could not validate this code. Please contact support if you believe this is an error.</p>
  @endif
  <a class="btn" href="{{ url('/') }}">Back to Home</a>
</div>
</body></html>
@endsection
