#!/usr/bin/env bash
set -euo pipefail
TS="$(date +%Y%m%d_%H%M%S)"

echo "==> Backups"
[ -f resources/views/layouts/app.blade.php ] && cp resources/views/layouts/app.blade.php resources/views/layouts/app.blade.php.$TS.bak || true
[ -f resources/views/admin/layout.blade.php ] && cp resources/views/admin/layout.blade.php resources/views/admin/layout.blade.php.$TS.bak || true
[ -f resources/views/categories/index.blade.php ] && cp resources/views/categories/index.blade.php resources/views/categories/index.blade.php.$TS.bak || true

echo "==> Write unified PUBLIC master layout (layouts/app.blade.php)"
cat > resources/views/layouts/app.blade.php <<'BLADE'
<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>@yield('title', config('app.name').' — SawaedUAE')</title>
  <meta name="description" content="Find volunteer opportunities in the UAE, track hours, and earn verified certificates.">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  @if(app()->isLocale('ar'))
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
  @endif
  <style>
    :root { --brand:#0ea5a6; --bg:#FFF7D6; }
    body { background: var(--bg); }
    .navbar-brand { color: var(--brand) !important; font-weight: 800; letter-spacing:.2px; }
    .shadow-soft { box-shadow: 0 1px 2px rgba(0,0,0,.06), 0 4px 16px rgba(0,0,0,.04); }
    .card { border-radius: 1rem; }
  </style>
  @stack('head')
</head>
<body>
  <nav class="navbar navbar-expand-lg bg-white border-bottom shadow-sm" aria-label="Main">
    <div class="container">
      <a class="navbar-brand" href="{{ url('/') }}">SawaedUAE</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div id="mainNav" class="collapse navbar-collapse">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link" href="{{ route(\Illuminate\Support\Facades\Route::has('public.opportunities') ? 'public.opportunities' : 'home') }}">{{ __('Opportunities') }}</a></li>
          @if(\Illuminate\Support\Facades\Route::has('public.events'))
          <li class="nav-item"><a class="nav-link" href="{{ route('public.events') }}">{{ __('Events') }}</a></li>
          @endif
          @if(\Illuminate\Support\Facades\Route::has('public.organizations'))
          <li class="nav-item"><a class="nav-link" href="{{ route('public.organizations') }}">{{ __('Organizations') }}</a></li>
          @endif
          @if(\Illuminate\Support\Facades\Route::has('public.gallery'))
          <li class="nav-item"><a class="nav-link" href="{{ route('public.gallery') }}">{{ __('Gallery') }}</a></li>
          @endif
        </ul>
        <ul class="navbar-nav">
          @auth
            <li class="nav-item"><a class="nav-link" href="{{ url('/volunteer/profile') }}">{{ __('My Profile') }}</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ url('/logout') }}">{{ __('Logout') }}</a></li>
          @else
            @if(\Illuminate\Support\Facades\Route::has('login'))
              <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a></li>
            @endif
            @if(\Illuminate\Support\Facades\Route::has('register'))
              <li class="nav-item"><a class="btn btn-sm btn-success ms-2" href="{{ route('register') }}">{{ __('Sign Up') }}</a></li>
            @endif
          @endauth
        </ul>
      </div>
    </div>
  </nav>

  <main class="container py-4">
    @yield('content')
  </main>

  <footer class="bg-white border-top py-4 text-center text-muted">
    <div class="container">
      © {{ date('Y') }} SawaedUAE
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  @stack('scripts')
</body>
</html>
BLADE

echo "==> Write unified ADMIN master layout (admin/layout.blade.php)"
cat > resources/views/admin/layout.blade.php <<'BLADE'
<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>@yield('title', 'Admin — SawaedUAE')</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  @if(app()->isLocale('ar'))
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
  @endif
  <style>
    :root { --brand:#0ea5a6; --bg:#FFF7D6; }
    body { background: var(--bg); }
    .admin-bar { background:#fff7; backdrop-filter:saturate(150%) blur(6px); }
    .card { border-radius: 1rem; }
  </style>
  @stack('head')
</head>
<body>
  <header class="admin-bar border-bottom shadow-sm">
    <div class="container d-flex align-items-center justify-content-between py-2">
      <div class="d-flex align-items-center gap-3">
        <a class="fw-bold text-decoration-none" style="color:var(--brand)" href="{{ route('admin.dashboard') }}">SawaedUAE Admin</a>
      </div>
      <div class="d-flex align-items-center gap-3">
        <a class="text-decoration-none" href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a>
        @if(\Illuminate\Support\Facades\Route::has('admin.users.index'))
          <a class="text-decoration-none" href="{{ route('admin.users.index') }}">{{ __('Users') }}</a>
        @endif
        @if(\Illuminate\Support\Facades\Route::has('admin.opportunities.index'))
          <a class="text-decoration-none" href="{{ route('admin.opportunities.index') }}">{{ __('Opportunities') }}</a>
        @endif
        <a class="btn btn-sm btn-outline-secondary" href="{{ url('/') }}">{{ __('View Site') }}</a>
      </div>
    </div>
  </header>

  <main class="container py-4">
    @if(session('status'))
      <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    @yield('content')
  </main>

  <footer class="border-top bg-white py-3 text-center text-muted">
    <div class="container">© {{ date('Y') }} SawaedUAE — Admin</div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  @stack('scripts')
</body>
</html>
BLADE

echo "==> Harden categories link (fallback if named route missing)"
if [ -f resources/views/categories/index.blade.php ]; then
  sed -i -E "s@href=\{\{\s*route\('public\.opportunities'\)\s*\}}\?category=\{\{\s*urlencode\(\$cat->name\)\s*\}\}@href=\"{{ \\Illuminate\\Support\\Facades\\Route::has('public.opportunities') ? route('public.opportunities') : url('/opportunities') }}?category={{ urlencode(\$cat->name) }}\"@g" resources/views/categories/index.blade.php || true
  # If link format is different, ensure at least a generic fallback exists
  if ! grep -q "Route::has('public.opportunities')" resources/views/categories/index.blade.php; then
    sed -i -E "s@route\('public\.opportunities'\)@\\Illuminate\\Support\\Facades\\Route::has('public.opportunities') ? route('public.opportunities') : url('/opportunities')@g" resources/views/categories/index.blade.php || true
  fi
fi

echo "==> Clear caches"
php artisan view:clear && php artisan route:clear && php artisan config:clear && php artisan cache:clear
php artisan config:cache && php artisan route:cache

echo "==> Done. Backups with extension .$TS.bak"
