PHP_BIN=${PHP_BIN:-php}
#!/usr/bin/env bash
# SwaedUAE — Login/Register links on HOME (check first, then minimal safe fix)
set -u; set -o pipefail
TS=$(date +%F_%H%M%S)
BASE_URL=${BASE_URL:-https://swaeduae.ae}
ADMIN_URL=${ADMIN_URL:-https://admin.swaeduae.ae}
LOG="/tmp/fix_login_links_$TS.log"
exec > >(tee -a "$LOG") 2>&1

c(){ curl -s -o /dev/null -w "%{http_code}" "$1"; }

echo "== A) Pre-checks =="
echo "/ -> $(c "$BASE_URL/")"
echo "/login -> $(c "$BASE_URL/login")"
echo "/register -> $(c "$BASE_URL/register")"
echo "/org/login -> $(c "$BASE_URL/org/login")"
echo "admin host /login -> $(c "$ADMIN_URL/login")"

# If home is 500, fix storage/bootstrap perms so Blade can compile
HCODE=$(c "$BASE_URL/")
if [ "$HCODE" = "500" ]; then
  echo "== Home is 500 → fixing storage/bootstrap permissions (shared deploy detected) =="
  sudo mkdir -p /var/www/swaeduae/shared/storage/framework/{cache,sessions,views}
  sudo mkdir -p /var/www/swaeduae/current/bootstrap/cache
  sudo chown -R www-data:www-data /var/www/swaeduae/shared/storage /var/www/swaeduae/current/bootstrap/cache
  sudo chmod -R ug+rwX /var/www/swaeduae/shared/storage /var/www/swaeduae/current/bootstrap/cache
  php artisan optimize:clear || true
  php artisan view:cache || true
  HCODE=$(c "$BASE_URL/")
  echo "After perms, / -> $HCODE"
fi

echo "== B) Detect the header actually used by HOME (TravelPro) =="
HDR=$(grep -RIl --include='*.blade.php' -E '<nav class="container py-3 d-flex gap-3">' resources/views | head -n1 || true)
if [ -z "${HDR:-}" ]; then
  # Fallback to known TravelPro layout
  HDR="resources/views/public/layout-travelpro.blade.php"
fi
echo "HEADER = $HDR"
if [ ! -f "$HDR" ]; then
  echo "[FAIL] Could not locate the TravelPro header file."; exit 1
fi

echo "== C) Backup header and inject plain visible links into <span class=\"ms-auto\"> =="
cp -a "$HDR" "$HDR.$TS.bak"
# Replace ONLY the contents inside the ms-auto span with simple links (no dropdown JS reliance)
perl -0777 -i -pe "s#(<span[^>]*class=\"[^\"]*ms-auto[^\"]*\"[^>]*>)(.*?)(</span>)#\${1}\n    @auth\n      <a class=\"link-secondary me-3\" href=\"{{ url('/my/profile') }}\">Account</a>\n      <form method=\"POST\" action=\"{{ route('logout') }}\" style=\"display:inline\">@csrf <button type=\"submit\" class=\"btn btn-link p-0 align-baseline\">Logout</button></form>\n    @else\n      <a class=\"link-secondary me-3\" href=\"{{ url('/login') }}\">Volunteer Sign In</a>\n      <a class=\"link-secondary me-3\" href=\"{{ url('/register') }}\">Register</a>\n      <a class=\"link-secondary me-3\" href=\"{{ url('/org/login') }}\">Org Sign In</a>\n      <a class=\"link-secondary\" href=\"https://admin.swaeduae.ae/login\">Admin</a>\n    @endauth\n\${3}#s" "$HDR"

echo "== D) Rebuild views and routes =="
php artisan optimize:clear || true
php artisan view:cache || true
php artisan route:cache || true

echo "== E) Post-checks on LIVE HTML =="
echo "/ -> $(c "$BASE_URL/")"
TOKENS=$(curl -s "$BASE_URL/" | grep -Eo 'Volunteer Sign In|Register|Org Sign In|admin\.swaeduae\.ae/login' | sort | uniq -c || true)
echo "$TOKENS"

# Optional: warn if /org/register is missing so you can decide later
ORGREG_CODE=$(c "$BASE_URL/org/register")
echo "/org/register -> $ORGREG_CODE (info only)"
echo "== DONE =="
echo "Log: $LOG"
