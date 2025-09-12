#!/bin/bash
set -e

echo "=== Applying global sand/teal theme to all pages ==="

# Create CSS
mkdir -p public/css
cat > public/css/swaed.css <<'CSS'
body {
    background-color: #f9f9f7;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}
.navbar {
    background-color: #046c5c !important;
}
.navbar a, .navbar-brand {
    color: #fff !important;
}
.btn-primary {
    background-color: #046c5c;
    border-color: #046c5c;
}
.btn-primary:hover {
    background-color: #035349;
    border-color: #035349;
}
.card {
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.05);
    border: none;
}
.search-banner {
    background: linear-gradient(135deg, #046c5c, #0d9488);
    color: white;
    padding: 40px 20px;
    margin-bottom: 30px;
    border-radius: 8px;
}
.search-banner h1 {
    font-size: 2rem;
}
[dir="rtl"] {
    text-align: right;
}
CSS

# Create global layout
mkdir -p resources/views/layouts
cat > resources/views/layouts/app.blade.php <<'BLADE'
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Sawaed UAE') }}</title>
    <link href="{{ asset('css/swaed.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @stack('styles')
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark">
  <div class="container">
    <a class="navbar-brand" href="{{ url('/') }}">{{ config('app.name', 'Sawaed UAE') }}</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="{{ route('opportunities.index') }}">{{ __('Opportunities') }}</a></li>
        @guest
          <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a></li>
          <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a></li>
        @else
          <li class="nav-item"><a class="nav-link" href="{{ url('/'.auth()->user()->role.'/dashboard') }}">{{ __('Dashboard') }}</a></li>
          <li class="nav-item">
            <form method="POST" action="{{ route('logout') }}">@csrf
              <button type="submit" class="btn btn-link nav-link">{{ __('Logout') }}</button>
            </form>
          </li>
        @endguest
        <li class="nav-item">
          <a class="nav-link" href="{{ url('locale/ar') }}">AR</a> |
          <a class="nav-link" href="{{ url('locale/en') }}">EN</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="search-banner text-center">
    <h1>{{ __('messages.welcome_banner_title', [], app()->getLocale()) }}</h1>
    <p>{{ __('messages.welcome_banner_subtitle', [], app()->getLocale()) }}</p>
</div>

<main class="py-4">
    @yield('content')
</main>

<footer class="bg-dark text-white text-center py-3 mt-4">
    <div class="container">
        &copy; {{ date('Y') }} {{ config('app.name') }} - {{ __('All Rights Reserved') }}
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
BLADE

echo "=== Global theme applied successfully ==="

