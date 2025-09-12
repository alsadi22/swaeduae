<!doctype html>
<html lang="{{ str_replace('_','-',app()->getLocale()) }}" dir="{{ app()->getLocale()==='ar' ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800<meta name="viewport" content="width=device-width, initial-scale=1">family=Noto+Naskh+Arabic:wght@400;700<meta name="viewport" content="width=device-width, initial-scale=1">display=swap" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800<meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800<meta name="viewport" content="width=device-width, initial-scale=1">family=Noto+Naskh+Arabic:wght@400;700<meta name="viewport" content="width=device-width, initial-scale=1">display=swap" rel="stylesheet">family=Noto+Naskh+Arabic:wght@400;700<meta name="viewport" content="width=device-width, initial-scale=1">display=swap" rel="stylesheet">
  <title>@yield('title','SwaedUAE')</title>
  <meta name="description" content="SwaedUAE connects volunteers with verified opportunities across the UAE.">
  <meta property="og:title" content="SwaedUAE">
  <meta property="og:description" content="Volunteer opportunities, verified hours, downloadable certificates.">
  <meta property="og:type" content="website">
  <meta property="og:url" content="https://swaeduae.ae/">
  <meta name="twitter:card" content="summary_large_image">
  <meta name="description" content="SwaedUAE connects volunteers with verified opportunities across the UAE.">
  <meta property="og:title" content="SwaedUAE">
  <meta property="og:description" content="Volunteer opportunities, verified hours, downloadable certificates.">
  <meta property="og:type" content="website">
  <meta property="og:url" content="https://swaeduae.ae/">
  <meta name="twitter:card" content="summary_large_image">
  <link rel="stylesheet" href="{{ asset('assets/app.css') }}?v={{ @filemtime(public_path('assets/app.css')) }}">
</head>
<body class="public">
  @includeIf('partials.header_public')

  <main class="container py-4">
    @yield('content')
  </main>

  @includeIf('partials.footer_public')
  <script defer src="{{ asset('assets/app.js') }}?v={{ @filemtime(public_path('assets/app.js')) }}"></script>
</body>
</html>
