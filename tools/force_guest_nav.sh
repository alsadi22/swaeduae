PHP_BIN=${PHP_BIN:-php}
#!/usr/bin/env bash
set -u; set -o pipefail
TS=$(date +%F_%H%M%S)
BASE_URL=${BASE_URL:-https://swaeduae.ae}

# 0) Find the TravelPro header used by HOME
HDR=$(grep -RIl --include='*.blade.php' -E '<nav class="container py-3 d-flex gap-3">' resources/views | head -n1 || true)
[ -z "${HDR:-}" ] && HDR="resources/views/partials/header-public.blade.php"
echo "HEADER=$HDR"
[ -f "$HDR" ] || { echo "[FAIL] Header file not found: $HDR"; exit 1; }

# 1) Backup header
cp -a "$HDR" "$HDR.$TS.bak" && echo "Backup -> $HDR.$TS.bak"

# 2) Remove any existing @auth...@endauth blocks or account/logout includes in that header
#    (This ensures nothing authenticated leaks into the guest header.)
perl -0777 -i -pe "s/@auth.*?@endauth//gs" "$HDR"
sed -i -E "/partials\.account_menu(_inline)?/d" "$HDR"
sed -i -E "/partials\.auth_menu(_inline)?/d" "$HDR"

# 3) Replace ONLY the inner contents of the <span class="ms-auto">...</span> with plain guest links
perl -0777 -i -pe 's#(<span[^>]*class="[^"]*ms-auto[^"]*"[^>]*>)(.*?)(</span>)#\${1}
      <a class="link-secondary me-3" href="{{ url('\''/login'\'') }}">Volunteer Sign In</a>
      <a class="link-secondary me-3" href="{{ url('\''/register'\'') }}">Register</a>
      <a class="link-secondary me-3" href="{{ url('\''/org/login'\'') }}">Org Sign In</a>
      <a class="link-secondary" href="https://admin.swaeduae.ae/login">Admin</a>
\${3}#s' "$HDR"

# 4) Rebuild view cache and verify live HTML
php artisan optimize:clear >/dev/null 2>&1 || true
php artisan view:cache   >/dev/null 2>&1 || true

echo "/ -> $(curl -s -o /dev/null -w '%{http_code}' "$BASE_URL/")"
echo "Tokens on live HTML (should NOT list Account/Logout):"
curl -s "$BASE_URL/" | grep -Eo 'Volunteer Sign In|Register|Org Sign In|admin\.swaeduae\.ae/login|Account|Logout' | sort | uniq -c || true
