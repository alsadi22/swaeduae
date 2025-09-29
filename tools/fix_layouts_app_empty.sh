#!/usr/bin/env bash
set -euo pipefail
LAY="resources/views/layouts/app.blade.php"
STAMP="$(date +%F_%H%M%S)"

# Backup what you have
[ -f "$LAY" ] && cp -a "$LAY" "$LAY.bak_$STAMP"

# Write a minimal, safe base layout with @yield('content')
cat > "$LAY" <<'BLADE'
@php $rtl = app()->getLocale()==='ar'; @endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $rtl ? 'rtl':'ltr' }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name'))</title>
    <link rel="stylesheet" href="{{ asset('vendor/argon/assets/css/nucleo-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/argon/assets/css/nucleo-svg.css') }}">
    <link id="pagestyle" rel="stylesheet" href="{{ asset('vendor/argon/assets/css/argon-dashboard.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/brand.css') }}">
    @stack('head')
  </head>
  <body class="bg-gray-100">
    <main class="container py-4">
      @yield('content')
    </main>

    @stack('scripts')
    <script src="{{ asset('vendor/argon/assets/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('vendor/argon/assets/js/core/bootstrap.min.js') }}"></script>
    <script src="{{ asset('vendor/argon/assets/js/argon-dashboard.min.js') }}"></script>
  </body>
</html>
BLADE

php artisan view:clear >/dev/null || true
php artisan view:cache >/dev/null || true

echo -n "Homepage bytes now: "
curl -sk https://swaeduae.ae/ | wc -c
echo "Backup: $LAY.bak_$STAMP"
