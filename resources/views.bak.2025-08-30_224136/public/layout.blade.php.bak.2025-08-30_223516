<!doctype html>
<html lang="{{ str_replace('_','-', app()->getLocale()) }}">
  @includeWhen(View::exists('partials.meta'), 'partials.meta')
  @includeWhen(View::exists('partials.assets-public'), 'partials.assets-public')
  <body class="public">
    @includeWhen(View::exists('partials.header-public'), 'partials.header-public')
    @yield('content')
    @includeWhen(View::exists('partials.footer-public'), 'partials.footer-public')
    @stack('scripts')
  </body>
</html>
