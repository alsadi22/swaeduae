<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'SwaedUAE')</title>
  <link rel="canonical" href="{{ url()->current() }}">
  <link rel="stylesheet" href="{{ asset('assets/app.css') }}">
  @yield('meta')
</head>
<body class="min-h-screen antialiased">
  @include('public.components.nav')
  <main id="main" class="min-h-[60vh]">@yield('content')</main>
  @include('public.components.footer')
  <script src="{{ asset('assets/app.js') }}" defer></script>
</body>
</html>
