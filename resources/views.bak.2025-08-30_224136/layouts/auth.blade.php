<!doctype html>
<html lang="{{ str_replace('_','-',app()->getLocale()) }}">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <title>{{ $title ?? 'Sign in' }} — {{ config('app.name') }}</title>
  <link rel="stylesheet" href="{{ asset('vendor/travelpro/assets/css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('vendor/travelpro/assets/css/style.css') }}">
</head>
<body class="bg-light">
  <div class="container py-5">@yield('content')</div>
  <script src="{{ asset('vendor/travelpro/assets/js/jquery-3.6.0.min.js') }}"></script>
  <script src="{{ asset('vendor/travelpro/assets/js/bootstrap.min.js') }}"></script>
</body>
</html>
