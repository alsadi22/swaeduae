<!doctype html>
<html lang="{{ str_replace('_','-',app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>@yield('title', config('app.name','SawaedUAE'))</title>
  
  <style>
    body { background:#fff8d9; }
    .brand { color:#0aa0a0; font-weight:700; }
    .card { border-radius:1rem; box-shadow:0 4px 10px rgba(0,0,0,.06); }
    .sidebar a { display:block; padding:.6rem .9rem; border-radius:.6rem; margin:.3rem 0; background:#fff; }
    .sidebar a.active { background:#0d6efd; color:#fff; }
    footer { color:#6b7280; }
  </style>
  @stack('head')
</head>
<body>
  @hasSection('no_nav')
  @else
    @include('partials.nav')
  @endif

  <main class="py-4">@yield('content')</main>

  @hasSection('no_nav')
  @else
    @include('partials.footer')
  @endif

  
  @stack('scripts')
</body>
</html>
