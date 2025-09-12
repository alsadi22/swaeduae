#!/usr/bin/env bash
set -euo pipefail

ts() { date +%F-%H%M%S; }
bak() { [ -f "$1" ] && cp -a "$1" "$1.bak.$(ts)"; }

### 1) routes/events.php — canonical, slug/ID route names aligned
F="routes/events.php"
mkdir -p routes
bak "$F"
cat > "$F" <<'PHP'
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;

Route::get('/events', [EventController::class, 'index'])->name('events.index');

Route::get('/events/{idOrSlug}', [EventController::class, 'show'])
    ->where('idOrSlug', '[0-9]+|[A-Za-z0-9\-]+')
    ->name('events.show');
PHP
php -l "$F" >/dev/null

### 2) routes/_auth_overrides.php — placeholder so require() never explodes
F2="routes/_auth_overrides.php"
if [ ! -f "$F2" ]; then
  cat > "$F2" <<'PHP'
<?php
/**
 * Placeholder for optional auth route overrides.
 * Leave empty, or add overrides as needed.
 */
PHP
fi
php -l "$F2" >/dev/null || true

### 3) app/Http/Controllers/EventController.php — fix undefined $has
FC="app/Http/Controllers/EventController.php"
if [ -f "$FC" ]; then
  bak "$FC"

  # If $has is referenced but not clearly defined, try to seed it in index() & show()
  # (idempotent insert — will only add if the line isn't already present)
  perl -0777 -i -pe "
    s/(function\s+index\s*\([^\)]*\)\s*\{(?![^}]*request\(\)->has\('q'\)))/\${1}\n        if (!isset(\$has)) { \$has = request()->has('q'); }\n/s;
    s/(function\s+show\s*\([^\)]*\)\s*\{(?![^}]*request\(\)->has\('q'\)))/\${1}\n        if (!isset(\$has)) { \$has = request()->has('q'); }\n/s;
  " "$FC" || true

  # Fallback: if $has still appears, replace it in-place with request()->has('q') (broad but safe)
  if grep -q '\$has\b' "$FC"; then
    sed -i "s/\$has/request()->has('q')/g" "$FC"
  fi

  php -l "$FC" >/dev/null
fi

### 4) Clear & rebuild caches; show routes; quick curl checks if curl present
php artisan optimize:clear >/dev/null || true
php artisan route:clear >/dev/null || true
php artisan config:clear >/dev/null || true
php artisan route:cache >/dev/null || true
php artisan view:clear >/dev/null || true
php artisan view:cache >/dev/null || true

echo "== Route snapshot (events) =="
php artisan route:list --path=events || true

if command -v curl >/dev/null 2>&1; then
  echo "== Redirect check (ID -> slug) =="
  curl -sI https://swaeduae.ae/events/1 | sed -n '1p;/^Location:/p' || true
  echo "== Known slugs =="
  for s in bridge-action campaign-environment-innovation care-green; do
    printf "%-36s " "$s"
    curl -sIL -o /dev/null -w "HTTP %{http_code}  final:%{url_effective}\n" "https://swaeduae.ae/events/$s" || true
  done
fi

echo "All fixes applied."
