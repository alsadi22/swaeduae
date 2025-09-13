@extends("org.layout")
@php $rtl = app()->getLocale()==='ar'; @endphp
<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ $rtl ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="{{ asset('vendor/argon/assets/css/nucleo-icons.css') }}">
  <link rel="stylesheet" href="{{ asset('vendor/argon/assets/css/nucleo-svg.css') }}">
  <link rel="stylesheet" href="{{ asset('css/brand.css') }}">
  @stack('head')
</head>
<body class="bg-gray-100 {{ $rtl ? 'rtl' : '' }}">
  <main class="main-content">
    <div class="container py-5">
      @yield('content')
    </div>
  </main>
  <script src="{{ asset('vendor/argon/assets/js/core/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('vendor/argon/assets/js/argon-dashboard.min.js') }}"></script>
</body>
</html>
