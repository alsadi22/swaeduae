@php $rtl = app()->getLocale() === 'ar'; @endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $rtl ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="{{ asset('vendor/argon/assets/css/nucleo-icons.css') }}">
  <link rel="stylesheet" href="{{ asset('vendor/argon/assets/css/nucleo-svg.css') }}">
  <link rel="stylesheet" href="{{ asset('css/brand.css') }}">
  @stack('styles')
</head>
<body class="bg-gray-100 {{ $rtl ? 'rtl' : '' }}">
  <div class="container py-5">@yield('content_org_auth')</div>
  <script src="{{ asset('vendor/argon/assets/js/core/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('vendor/argon/assets/js/argon-dashboard.min.js') }}"></script>
  @stack('scripts')
</body>
</html>
