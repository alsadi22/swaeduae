#!/usr/bin/env bash
set -euo pipefail

APP_ROOT="${APP_ROOT:-$( [ -f artisan ] && echo . || echo laravel-app )}"
BASE="${BASE:-https://swaeduae.ae}"

cd "$APP_ROOT" || { echo "!! Can't cd into $APP_ROOT"; exit 1; }

STAMP=$(date +%F-%H%M%S)
OUT="tmp/phase-check-$STAMP"
mkdir -p "$OUT"

say(){ printf "%b\n" "$*"; echo -e "$*" >> "$OUT/summary.txt"; }
tick(){ say "✓ $1"; }
cross(){ say "✗ $1"; }

# Route list snapshot (Laravel)
php artisan route:list > "$OUT/route-list.txt" 2>/dev/null || true
RL="$OUT/route-list.txt"

has_route(){ local n="$1"; grep -Fq " $n" "$RL"; }
all_uris_under_have_auth(){
  local prefix="$1"; local lines
  lines=$(awk -v p="^$prefix" 'NR>1 && $0 ~ p {print}' "$RL" || true)
  [ -z "$lines" ] && { echo "NONE"; return 0; }
  echo "$lines" | awk 'BEGIN{ok=1} { if ($0 !~ /auth/) ok=0 } END{ exit ok?0:1 }'
}
method_only_post_no_get(){
  local name="$1"; local line
  line=$(grep -F " $name" "$RL" | head -n1 || true)
  [ -z "$line" ] && { echo "MISSING"; return 1; }
  echo "$line" | grep -Eq '\bPOST\b' && ! echo "$line" | grep -Eq '\bGET\b|\bHEAD\b'
}
http_ok(){ local path="$1"; local code; code=$(curl -skI "$BASE$path" | awk 'NR==1{print $2}'); [[ "$code" =~ ^(200|301|302)$ ]]; }

say "=== Phase 0 – Stabilization & Hardening ==="

# 0.1 Named routes
MISSING=()
for n in home faq about contact partners opportunities.index opportunities.show volunteer.dashboard volunteer.profile admin.users admin.events admin.certificates admin.kyc lang.switch; do
  has_route "$n" || MISSING+=("$n")
done
[ ${#MISSING[@]} -eq 0 ] && tick "All required named routes exist" || cross "Missing named routes: ${MISSING[*]}"

# 0.2 Layouts/partials
FILES=( "resources/views/layouts/app.blade.php"
        "resources/views/layouts/guest.blade.php"
        "resources/views/partials/navbar.blade.php"
        "resources/views/components/lang-toggle.blade.php" )
MISSF=(); for f in "${FILES[@]}"; do [ -f "$f" ] || MISSF+=("$f"); done
[ ${#MISSF[@]} -eq 0 ] && tick "All required Blade layouts/partials exist" || cross "Missing Blade files: ${MISSF[*]}"

# 0.3 Middleware on /admin/* and /org/*
ADMIN_AUTH=$(all_uris_under_have_auth " *admin")
ORG_AUTH=$(all_uris_under_have_auth " *org")
[ "$ADMIN_AUTH" = "NONE" ] && cross "No /admin routes found in route:list" || { if all_uris_under_have_auth " *admin"; then tick "/admin/* guarded by auth"; else cross "Some /admin/* routes lack auth middleware"; fi; }
[ "$ORG_AUTH" = "NONE" ] && cross "No /org routes found in route:list" || { if all_uris_under_have_auth " *org"; then tick "/org/* guarded by auth"; else cross "Some /org/* routes lack auth middleware"; fi; }

# 0.4 Logout flows (POST-only, no GET/HEAD)
for name in logout admin.logout org.logout; do
  if method_only_post_no_get "$name"; then tick "Logout route '$name' is POST-only"; else cross "Logout route '$name' missing or allows GET/HEAD"; fi
done

# 0.5 /qr/verify alias/route
http_ok "/qr/verify" && tick "QR alias /qr/verify resolves (200/3xx)" || cross "/qr/verify is not reachable (expect 200/3xx)"

say ""
say "=== Phase 1 – MVP Public (quick checks) ==="
for p in /about /faq /contact /partners; do http_ok "$p" && tick "Page $p reachable" || cross "Page $p not reachable"; done
(has_route news.index || http_ok "/news") && tick "News present (route or /news URL)" || cross "News missing"
(has_route downloads || http_ok "/downloads") && tick "Downloads present (route or /downloads URL)" || cross "Downloads missing"
[ -f public/manifest.json ] && tick "manifest.json present" || cross "manifest.json missing"
[ -f public/service-worker.js ] && tick "service-worker.js present" || cross "service-worker.js missing"
has_route "lang.switch" && [ -f resources/views/components/lang-toggle.blade.php ] && tick "Language switcher route+component present" || cross "Language switcher missing"

say ""
say "=== Environment sanity ==="
grep -q '^APP_ENV=production' .env 2>/dev/null && tick "APP_ENV=production" || cross "APP_ENV not production"
grep -q '^APP_DEBUG=false' .env 2>/dev/null && tick "APP_DEBUG=false" || cross "APP_DEBUG not false"

say ""
say "Summary saved to: $OUT/summary.txt"
