@php $rtl = app()->getLocale()==='ar'; @endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $rtl ? 'rtl':'ltr' }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name'))</title>
    <link rel="stylesheet" href="{{ asset('vendor/argon/assets/css/nucleo-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/argon/assets/css/nucleo-svg.css') }}">
    <link rel="stylesheet" href="{{ asset('css/brand.css') }}">
    @stack('head')
  </head>
  <body class="bg-gray-100">
    @includeIf('argon_front._navbar')
    <main class="container py-4">
      @yield('content')
    </main>

    @stack('scripts')
    <script src="{{ asset('vendor/argon/assets/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('vendor/argon/assets/js/core/bootstrap.min.js') }}"></script>
    <script src="{{ asset('vendor/argon/assets/js/argon-dashboard.min.js') }}"></script>
    @includeIf('argon_front._footer')
  </body>
</html>
