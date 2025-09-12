<!doctype html>
<html lang="{{ str_replace('_','-',app()->getLocale()) }}">
  <head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Admin' }} — {{ config('app.name','SwaedUAE') }}</title>
    <link rel="stylesheet" href="{{ asset('vendor/argon/css/argon.css') }}">
</head>
  <body class="bg-default">
    @yield('content')
  <script src="{{ asset('vendor/argon/js/argon.js') }}"></script>
  </body>
</html>
