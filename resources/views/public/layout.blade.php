@extends('public.layout')

@section('content')
<!doctype html>
<html lang="{{ str_replace('_','-',app()->getLocale()) }}" dir="{{ app()->getLocale()==='ar'?'rtl':'ltr' }}">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <title>@yield('title','SwaedUAE')</title>
  <link rel="stylesheet" href="{{ asset('themes/travelpro/css/style.css') }}">
  @stack('head')
</head>
<body>
  @includeIf('public.partials.header')
  <main class="container">@yield('content')</main>
  @includeIf('public.partials.footer')
  <script src="{{ asset('themes/travelpro/js/app.js') }}"></script>
  @stack('scripts')
</body>
</html>

@endsection
