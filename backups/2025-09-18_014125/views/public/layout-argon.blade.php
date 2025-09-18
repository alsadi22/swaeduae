@extends('public.layout')
<!doctype html>
<html lang="{{ str_replace('_','-',app()->getLocale()) }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  @includeIf('components.seo.meta')
  @includeIf('components.seo.schema')
  @includeIf('components.seo.analytics')
  <link rel="stylesheet" href="{{ asset('vendor/argon/css/argon.css') }}">
  <style>.skip-link{position:absolute;left:-9999px} .skip-link:focus{left:auto;top:auto;padding:.5rem 1rem;background:#fff;border:2px solid #000;z-index:1000} :focus-visible{outline:2px solid #000;outline-offset:2px}</style>
  @yield('head')
  @stack('head')
</head>
<body class="bg-light">
  <a class="skip-link" href="#main">Skip to content</a>

  <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
    <div class="container">
      <a class="navbar-brand fw-bold" href="{{ route('home') }}">SwaedUAE</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navMain">
        <ul class="navbar-nav ms-auto gap-2">
          <li class="nav-item"><a class="nav-link" href="{{ route('home') }}">{{ __('Home') }}</a></li>
          <li class="nav-item"><a class="nav-link" href="{{ route('about') }}">{{ __('About') }}</a></li>
          <li class="nav-item"><a class="nav-link" href="{{ url('/services') }}">{{ __('Services') }}</a></li>
          @if (Route::has('contact.get'))
          <li class="nav-item"><a class="nav-link" href="{{ route('contact.get') }}">{{ __('Contact') }}</a></li>
          @endif
        </ul>
      </div>
    </div>
  </nav>

  <main id="main" tabindex="-1" class="py-4">
    @yield('content')
  </main>

  <footer class="py-4 border-top bg-white mt-5">
    <div class="container text-muted small d-flex justify-content-between">
      <span>© {{ date('Y') }} SwaedUAE</span>
      <span><a href="/privacy" class="text-muted me-3">{{ __('Privacy') }}</a><a href="/terms" class="text-muted">{{ __('Terms') }}</a></span>
    </div>
  </footer>

  <script src="{{ asset('vendor/argon/js/argon.js') }}"></script>
  @yield('scripts')
  @stack('scripts')
  @include('public._analytics')
    <script src="/assets/nav-dropdown-fix.js"></script>
</body>
    <script src="/assets/feather.min.js"></script>
    <script>document.addEventListener("DOMContentLoaded",function(){ if(window.feather&&feather.replace) feather.replace();});</script>
</html>

