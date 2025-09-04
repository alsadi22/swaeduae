<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $title ?? 'Admin' }} — {{ config('app.name','SwaedUAE') }}</title>
  <link rel="stylesheet" href="{{ asset('vendor/argon/assets/css/argon-dashboard.min.css') }}">
  <link rel="stylesheet" href="{{ asset('vendor/argon/assets/css/nucleo-icons.css') }}">
  @stack('head')
</head>
<body class="{{ app()->getLocale()==='ar' ? 'rtl' : '' }} bg-default">

  @auth
    @includeWhen(View::exists('admin.argon._sidenav'),'admin.argon._sidenav')
  @endauth

  <main class="main-content position-relative border-radius-lg">
    @auth
      @includeWhen(View::exists('admin.argon._navbar'),'admin.argon._navbar')
    @endauth

    <div class="container-fluid py-4">
      @yield('content')
      @auth
        @includeWhen(View::exists('admin.argon._footer'),'admin.argon._footer')
      @endauth
    </div>
  </main>

  <script src="{{ asset('vendor/argon/assets/js/argon-dashboard.min.js') }}" defer></script>
  @stack('scripts')
</body>
</html>
