<!doctype html>
<html lang="{{ str_replace('_','-',app()->getLocale()) }}" @if(app()->getLocale()==='ar') dir="rtl" @endif>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ \App\Models\Setting::name() }}</title>
  
  
  <style>
    .nav-link.active{font-weight:600}
    .glass{background:rgba(255,255,255,.15);backdrop-filter:blur(12px);border-radius:1rem}
  </style>
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
</head>
<body class="bg-light">
  <header class="bg-white border-bottom sticky-top">
    <nav class="container navbar navbar-expand-lg">
      <a class="navbar-brand fw-bold" href="{{ route('home') }}">SawaedUAE</a>
      <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav"><span class="navbar-toggler-icon"></span></button>
      <div id="nav" class="collapse navbar-collapse">
        <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
          <li class="nav-item"><a class="nav-link @if(request()->routeIs('home')) active @endif" href="{{ url('/') }}">Home</a></li>
          <li class="nav-item"><a class="nav-link @if(request()->routeIs('public.opportunities*')) active @endif" href="{{ route('opportunities.index') }}">Opportunities</a></li>
          <li class="nav-item"><a class="nav-link @if(request()->routeIs('public.events*')) active @endif" href="{{ route('events.index') }}">Events</a></li>
          <li class="nav-item"><a class="nav-link @if(request()->routeIs('public.organizations*')) active @endif" href="{{ url('/partners') }}">Organizations</a></li>
          <li class="nav-item"><a class="nav-link @if(request()->routeIs('public.gallery')) active @endif" href="{{ url('/') }}">Gallery</a></li>
          @auth
            <li class="nav-item"><a class="btn btn-outline-secondary btn-sm" href="/admin/dashboard">Dashboard</a></li>
            <li class="nav-item ms-lg-2">@include</li>
          @else
            <li class="nav-item ms-lg-2"><a class="btn btn-primary btn-sm" href="{{ route('login',['type'=>'volunteer']) }}">Login</a></li>
          @endauth
        </ul>
      </div>
    </nav>
  </header>

  <main>
    @yield('content')  {{-- SINGLE yield only --}}
  </main>

  <footer class="bg-white border-top mt-5">
    <div class="container py-4 text-muted small">© {{ date('Y') }} SawaedUAE</div>
  </footer>

  
  @stack('scripts')
    @includeIf('layouts.partials.footer')
</body>
</html>
