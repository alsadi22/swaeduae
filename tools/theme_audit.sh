#!/usr/bin/env bash
set -u
ts(){ date +%Y%m%d_%H%M%S; }
TS=$(ts)
OUT=~/swaed_scans/reports/theme_audit_$TS.txt

say(){ printf "%s\n" "$*" | tee -a "$OUT"; }

echo "=== SwaedUAE Theme Audit @ $TS ===" | tee "$OUT"

# 0) Environment quick snap
say "\n[ENV]"
php -r 'echo "APP_URL=".(config("app.url")??"unset").PHP_EOL;' 2>/dev/null | tee -a "$OUT"

# 1) Framework & legacy fingerprints in views and public assets
say "\n[FRAMEWORK FINGERPRINTS IN VIEWS/PUBLIC]"
grep -RIn --include='*.blade.php' --exclude='*.blade.php.*' \
  -E 'argon|nucleo|tailwind|alpine(js)?\.|wow\.min|animate\.css|aos(\.min)?\.css|fontawesome|bulma|materialize|bootstrap\.min\.css|travelpro|cs_' \
  resources/views | sed -n '1,240p' | tee -a "$OUT" || true

grep -RIn --include='*.{css,js,html}' \
  -E 'argon|nucleo|tailwind|alpine(js)?\.|wow\.min|animate\.css|aos(\.min)?\.css|fontawesome|bulma|materialize|travelpro|cs_' \
  public | sed -n '1,240p' | tee -a "$OUT" || true

# 2) Blade layouts that do NOT extend public.layout (auth + org + public)
say "\n[BLADE LAYOUTS NOT EXTENDING public.layout (auth/org/public folders)]"
grep -RIn --include='*.blade.php' --exclude='*.blade.php.*' -E '^@extends' resources/views/{auth,org,public} 2>/dev/null \
  | awk '$0 !~ /public\.layout/ {print}' | sed -n '1,240p' | tee -a "$OUT"

# 3) Explicit old/alternate layouts and admin Argon
say "\n[EXPLICIT OLD/ALT LAYOUTS & ADMIN ARGON]"
grep -RIn --include='*.blade.php' --exclude='*.blade.php.*' \
  -E "@extends\('layout_auth|layout\-auth|min|argon|layout-argon|layouts\.app|layouts\.org|org\.layout\)" \
  resources/views | sed -n '1,240p' | tee -a "$OUT" || true

# 4) Public assets tree for legacy bundles
say "\n[PUBLIC ASSETS TREE: vendor, css, js] (top levels)"
find public -maxdepth 2 -type d -printf "%p\n" | sort | tee -a "$OUT"
say "\n[PUBLIC ASSETS: argon vendors]"
find public -type d -path "*/vendor/argon*" -print | tee -a "$OUT"

# 5) JS/CSS that can break UI (click blockers, pointer-events)
say "\n[JS CLICK BLOCKERS]"
grep -RIn --include='*.{js,blade.php}' --exclude='*.blade.php.*' \
  -E 'preventDefault\(|return +false;|event\.preventDefault' \
  resources public | sed -n '1,240p' | tee -a "$OUT" || true

say "\n[CSS POINTER-EVENTS NONE]"
grep -RIn --include='*.{css,blade.php}' --exclude='*.blade.php.*' \
  -E 'pointer-events\s*:\s*none' resources public | sed -n '1,240p' | tee -a "$OUT" || true

# 6) Auth route views currently in use (ensure themed ones)
say "\n[ROUTES: AUTH & ORG]"
php artisan route:list 2>/dev/null | egrep -i '(^|[[:space:]])/(login|register|forgot-password)\b|(^|[[:space:]])org/(login|register)\b' | tee -a "$OUT"

# 7) Live HTML probes (ensure card + shared header appears)
say "\n[LIVE HTML PROBES FOR AUTH PAGES (card present?)]"
probe(){ u="$1"; echo -n "$u : " | tee -a "$OUT"; curl -s "$u" | grep -in '<div class="card' | head -n1 | tee -a "$OUT"; }
BASE="https://swaeduae.ae"
probe "$BASE/login"
probe "$BASE/register"
probe "$BASE/org/login"
probe "$BASE/org/register"

say "\n[REPORT SAVED] $OUT"
