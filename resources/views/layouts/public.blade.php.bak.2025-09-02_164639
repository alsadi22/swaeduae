<!doctype html>
<html lang="{{ str_replace('_','-',app()->getLocale()) }}" dir="{{ app()->getLocale()==='ar' ? 'rtl' : 'ltr' }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name','SwaedUAE') }}</title>
    @include('partials.assets-public')
    <style>
      .su-hero{padding:6rem 1rem;background:linear-gradient(180deg,#f8fafc, #eef2f7);}
      .su-hero .btn{padding:.8rem 1.2rem;border-radius:.6rem}
      .su-topbar{border-bottom:1px solid #e5e7eb;background:#fff}
    </style>
  </head>
  <body>
    @include('partials.header-public')
      <main>@yield('content')</main>
    @include('partials.footer-public')
  </body>
</html>
