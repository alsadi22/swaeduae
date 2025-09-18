<!doctype html>
<html lang="{{ str_replace(_,-, app()->getLocale()) }}" dir="{{ app()->getLocale()===ar ? rtl : ltr }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title','SwaedUAE')</title>

  <!-- Minimal, safe assets (no Blade logic) -->
  <link rel="stylesheet" href="/assets/app.css">
  <script src="/assets/app.js" defer></script>
</head>
<body class="public-site">
  <main id="main">@yield('content')</main>
</body>
</html>
