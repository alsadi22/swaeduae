<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale()==='ar' ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ trim($__env->yieldContent('title', __('Organization Sign In'))) }}</title>
  <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}?v={{ @filemtime(public_path('css/bootstrap.min.css')) }}">
  <link rel="stylesheet" href="{{ asset('css/brand.css') }}?v={{ @filemtime(public_path('css/brand.css')) }}">
  <link rel="stylesheet" href="{{ asset('css/a11y.css') }}?v={{ @filemtime(public_path('css/a11y.css')) }}">
  @stack('head')
</head>
<body class="bg-light">
  <main class="container py-4">
    @yield('content_org_auth')
    @yield('content') {{-- fallback --}}
  </main>
  @stack('scripts')
</body>
</html>
