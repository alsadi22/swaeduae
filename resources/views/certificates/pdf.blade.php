<!doctype html><html><head><meta charset="utf-8">
<style>
  *{box-sizing:border-box} body{font-family:DejaVu Sans,Arial,Helvetica,sans-serif;margin:0;padding:28px}
  .card{border:6px solid #1e293b;padding:28px;border-radius:14px;height:520px;position:relative}
  h1{margin:0 0 8px;font-size:32px;letter-spacing:1px}
  .muted{color:#555}
  .row{display:flex;gap:24px;margin-top:16px}
  .box{flex:1;background:#f6f7fb;border:1px solid #e5e7eb;border-radius:10px;padding:12px 14px}
  .qr{position:absolute;right:28px;bottom:28px;text-align:center}
  .foot{position:absolute;left:28px;bottom:28px;color:#444}
</style>
</head><body>
<div class="card">
  <h1>Certificate of Volunteer Service</h1>
  <div class="muted">SwaedUAE certifies the following volunteer hours.</div>

  <div class="row" style="margin-top:24px">
    <div class="box"><strong>Volunteer</strong><br>{{ $user->name ?? 'Volunteer' }}</div>
    <div class="box"><strong>Event</strong><br>{{ $event->title ?? 'Event' }}</div>
    <div class="box"><strong>Hours</strong><br>{{ number_format($c->hours ?? 0,2) }}</div>
  </div>

  <div class="row">
    <div class="box"><strong>Issued</strong><br>{{ \Illuminate\Support\Carbon::parse($c->issued_at ?? now())->format('Y-m-d') }}</div>
    <div class="box"><strong>Code</strong><br>{{ $c->code }}</div>
    <div class="box"><strong>Verified at</strong><br>{{ url('/qr/verify/'.$c->code) }}</div>
  </div>

  <div class="foot">This certificate was generated electronically and is valid without signature.</div>
  <div class="qr">
    @php $verify = url('/qr/verify/'.$c->code); @endphp
    {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(110)->generate($verify) !!}
    <div style="font-size:12px;margin-top:6px">{{ $c->code }}</div>
  </div>
</div>
</body></html>
