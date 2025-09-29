#!/usr/bin/env bash
set -euo pipefail
APP_DIR="${APP_DIR:-/var/www/swaeduae}"
cd "$APP_DIR"

ts(){ date +%F-%H%M%S; }
say(){ echo "[$(date +%H:%M:%S)] $*"; }

# --- backups ---------------------------------------------------------------
[[ -f routes/web.php ]] && cp -an routes/web.php routes/web.php.$(ts).bak
mkdir -p resources/views/{layouts,partials,components,pages,opportunities,volunteer,admin}

# --- helpers ---------------------------------------------------------------
has_named(){ grep -R "->name(['\"]$1['\"])" -n routes/web.php >/dev/null 2>&1; }
add_route(){ local nm="$1"; shift; local code="$*";
  has_named "$nm" || { printf "\n// added by phase0_patch (%s): %s\n%s\n" "$(ts)" "$nm" "$code" >> routes/web.php; }
}
add_view(){ local path="$1"; local title="$2";
  [[ -f "$path" ]] || cat > "$path" <<BLADE
@extends('layouts.app')
@section('content')
  <div style="padding:2rem"><h1>$title</h1><p>Placeholder page.</p></div>
@endsection
BLADE
}

# --- minimal layouts/partials (only if missing) ---------------------------
[[ -f resources/views/layouts/app.blade.php ]] || cat > resources/views/layouts/app.blade.php <<'BLADE'
<!doctype html><html lang="{{ str_replace('_','-',app()->getLocale()) }}"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>{{ $title ?? config('app.name','SwaedUAE') }}</title>
</head><body class="antialiased">
@includeIf('partials.navbar')
<main>@yield('content')</main>
</body></html>
BLADE

[[ -f resources/views/partials/navbar.blade.php ]] || cat > resources/views/partials/navbar.blade.php <<'BLADE'
<nav style="padding:.5rem 1rem;border-bottom:1px solid #eee;">
  <a href="{{ route('home') }}">Home</a> |
  <a href="{{ route('opportunities.index') }}">Opportunities</a> |
  <a href="{{ route('about') }}">About</a> |
  <a href="{{ route('faq') }}">FAQ</a> |
  <a href="{{ route('contact') }}">Contact</a>
</nav>
BLADE

[[ -f resources/views/components/lang-toggle.blade.php ]] || cat > resources/views/components/lang-toggle.blade.php <<'BLADE'
@php $alt = app()->getLocale()==='ar'?'en':'ar'; @endphp
<a href="{{ route('lang.switch', $alt) }}" rel="nofollow">{{ strtoupper($alt) }}</a>
BLADE

# --- public pages (routes + views) ----------------------------------------
add_view resources/views/pages/home.blade.php "Home"
add_view resources/views/pages/about.blade.php "About"
add_view resources/views/pages/faq.blade.php "FAQ"
add_view resources/views/pages/partners.blade.php "Partners"
add_view resources/views/pages/contact.blade.php "Contact"

add_route home       "Route::view('/', 'pages.home')->name('home');"
add_route about      "Route::view('/about', 'pages.about')->name('about');"
add_route faq        "Route::view('/faq', 'pages.faq')->name('faq');"
add_route partners   "Route::view('/partners', 'pages.partners')->name('partners');"
add_route contact    "Route::view('/contact', 'pages.contact')->name('contact');"

# --- opportunities (index/show) ------------------------------------------
add_view resources/views/opportunities/index.blade.php "Opportunities"
[[ -f resources/views/opportunities/show.blade.php ]] || cat > resources/views/opportunities/show.blade.php <<'BLADE'
@extends('layouts.app')
@section('content')
  <div style="padding:2rem"><h1>Opportunity #{{ $id ?? 'X' }}</h1></div>
@endsection
BLADE

add_route opportunities.index \
"Route::get('/opportunities', function(){ return view('opportunities.index'); })->name('opportunities.index');"

add_route opportunities.show \
"Route::get('/opportunities/{id}', function(\$id){ return view('opportunities.show', ['id'=>\$id]); })->name('opportunities.show');"

# --- volunteer (dashboard/profile) ---------------------------------------
add_view resources/views/volunteer/dashboard.blade.php "Volunteer Dashboard"
add_view resources/views/volunteer/profile.blade.php "Volunteer Profile"

add_route volunteer.dashboard \
"Route::get('/volunteer/dashboard', function(){ return view('volunteer.dashboard'); })->middleware('auth')->name('volunteer.dashboard');"

add_route volunteer.profile \
"Route::get('/volunteer/profile', function(){ return view('volunteer.profile'); })->middleware('auth')->name('volunteer.profile');"

# --- admin kyc (stub) -----------------------------------------------------
add_view resources/views/admin/kyc.blade.php "Admin KYC"
add_route admin.kyc \
"Route::get('/admin/kyc', function(){ return view('admin.kyc'); })->middleware('auth')->name('admin.kyc');"

# --- language switch (safe) -----------------------------------------------
add_route lang.switch \
"Route::get('/lang/{locale}', function(\$locale){
   if (in_array(\$locale,['en','ar'])){ session(['locale'=>\$locale]); app()->setLocale(\$locale); }
   return back();
})->name('lang.switch');"

# --- logout = POST only (add if missing) ----------------------------------
add_route logout \
"Route::post('/logout', function(){
   \\Illuminate\\Support\\Facades\\Auth::logout();
   request()->session()->invalidate();
   request()->session()->regenerateToken();
   return redirect()->route('home');
})->name('logout');"

# --- QR alias (/qr/verify -> existing /verify or route 'verify') ----------
if ! has_named "qr.verify"; then
  printf "\n// QR alias added by phase0_patch (%s)\n" "$(ts)" >> routes/web.php
  cat >> routes/web.php <<'PHP'
Route::get('/qr/verify', function(){
  if (function_exists('route') && \Illuminate\Support\Facades\Route::has('verify')) {
    return redirect()->route('verify');
  }
  return redirect('/verify');
})->name('qr.verify');
PHP
fi

# --- caches ----------------------------------------------------------------
php artisan optimize:clear || true
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

say "Phase-0 patch applied."
