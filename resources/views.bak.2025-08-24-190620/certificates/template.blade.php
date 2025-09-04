<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <style>
    body { font-family: DejaVu Sans, Arial, sans-serif; background: #FBF3D5; color:#333; }
    .wrap { padding: 40px; border: 10px solid #D6A99D; }
    h1 { font-size: 32px; margin: 0 0 10px; }
    .muted { color:#555; }
    .row { display:flex; justify-content:space-between; align-items:center; margin-top:25px; }
    .box { background:#fff; border:1px solid #ddd; padding:20px; border-radius:12px; }
  </style>
</head>
<body>
  <div class="wrap">
    <h1>شهادة شكر وتقدير</h1>
    <p class="muted">Certificate of Appreciation</p>
    <hr>
    <p>نُقَدِّم هذه الشهادة إلى</p>
    <h2>{{ $user->name }}</h2>
    <p>لمساهمته التطوعية في فعالية: <strong>{{ $opportunity->title }}</strong></p>
    <p>عدد الساعات: <strong>{{ number_format($hours, 2) }}</strong></p>
    <div class="row">
      <div class="box">
        <p>التحقق: {{ $verifyUrl }}</p>
        <p>الكود: <strong>{{ $code }}</strong></p>
        <p>تاريخ الإصدار: {{ $issuedAt }}</p>
      </div>
      <div>
        <img src="{{ $qrBase64 }}" alt="QR" width="180" height="180">
      </div>
    </div>
  </div>
</body>
</html>
