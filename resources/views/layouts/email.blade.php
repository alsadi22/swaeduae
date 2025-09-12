<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <style>
    body{margin:0;background:#ffffff;color:#111;font-family:Arial,Helvetica,sans-serif}
    .container{max-width:600px;margin:0 auto;padding:16px}
    .btn{display:inline-block;background:#2dce89;color:#fff!important;text-decoration:none;padding:10px 16px;border-radius:6px}
    a{color:#2dce89}
  </style>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
  <div class="container">@yield('content')</div>
    <script src="{{ asset('js/app.js') }}" defer></script>
</body>
</html>
