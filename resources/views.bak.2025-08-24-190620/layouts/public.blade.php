@php $rtl = app()->getLocale()==='ar'; @endphp
<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ $rtl ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  @includeIf('components.seo')
  <link rel="stylesheet" href="{{ asset('vendor/argon/assets/css/argon-dashboard.min.css') }}">
  <link rel="stylesheet" href="{{ asset('css/brand.css') }}">
  <link rel="stylesheet" href="{{ asset('css/site.css') }}">
  @stack('head')
</head>
<body class="bg-gray-100 site">
  <!-- pub-navbar:start -->@includeIf('partials.navbar')<!-- pub-navbar:end -->
  <main class="content wrap">@yield('content')</main>
  <!-- pub-footer:start -->@includeIf('partials.footer')<!-- pub-footer:end -->

  <script src="{{ asset('vendor/argon/assets/js/core/bootstrap.min.js') }}"></script>
  <script src="{{ asset('js/public-ui.js') }}" defer></script>
  @stack('scripts')
</body>
</html>
