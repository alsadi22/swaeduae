#!/usr/bin/env bash
PHP_BIN=${PHP_BIN:-php}
set -euo pipefail
STAMP="$(date +%F-%H%M%S)"
OUT="tmp/diag-$STAMP"
mkdir -p "$OUT"

base="https://swaeduae.ae"
org="$base/org/dashboard"

say(){ echo -e "$*"; echo -e "$*" >> "$OUT/summary.txt"; }

say "=== SwaedUAE DIAG $STAMP ==="

# --- PHP/Laravel basics
say "\n[PHP & Laravel]"
php -v | head -n1 | tee -a "$OUT/php.txt"
php artisan --version 2>/dev/null | tee -a "$OUT/php.txt" || true
echo "APP_ENV=$(grep -m1 '^APP_ENV=' .env | cut -d= -f2-)" | tee -a "$OUT/php.txt"
echo "APP_DEBUG=$(grep -m1 '^APP_DEBUG=' .env | cut -d= -f2-)" | tee -a "$OUT/php.txt"

# --- Homepage fetch
say "\n[Homepage HTTP]"
curl -skL -D "$OUT/home.hdr" "$base/" -o "$OUT/home.html" || true
{ head -n1 "$OUT/home.hdr"; egrep -i '^(HTTP/|content-type|content-length|set-cookie|cache-control|x-)' "$OUT/home.hdr" | sed 's/^/  /'; } | tee -a "$OUT/home.http"

bytes=$(wc -c < "$OUT/home.html" | tr -d ' ')
say "Homepage bytes: $bytes"
head -n 40 "$OUT/home.html" > "$OUT/home.head40.txt"
[ "$bytes" -lt 200 ] && say "NOTE: homepage body is very small — likely a PHP/Blade error or empty view."

# --- Laravel logs (application layer)
say "\n[Laravel log tail]"
tail -n 120 storage/logs/laravel.log 2>/dev/null | tee "$OUT/laravel.log.tail" || true
[ ! -s "$OUT/laravel.log.tail" ] && say "laravel.log is empty (errors may be going to PHP-FPM error_log)."

# --- PHP-FPM/Apache error_log (server layer)
say "\n[Find non-empty error_log files nearby]"
find "$HOME" -maxdepth 4 -type f -name 'error_log' -size +0c -printf "%p\n" 2>/dev/null | tee "$OUT/errorlogs.list" || true
while read -r f; do
  [ -n "$f" ] || continue
  echo "--- tail: $f ---" | tee -a "$OUT/errorlogs.tail"
  tail -n 60 "$f" | tee -a "$OUT/errorlogs.tail"
done < "$OUT/errorlogs.list"

# --- Route that serves "/"
say "\n[Route for /]"
php artisan route:list| tee "$OUT/routes.txt" | egrep -i '^\| *GET *\| */( |$)' || true

# --- Show the first match for Route::get('/') in routes/
say "\n[routes/web.php → lines around Route::get('/')]"
grep -nR "Route::get\('/'\|" -n routes 2>/dev/null | head -n1 | tee "$OUT/route_root_loc.txt" || true
loc="$(grep -nR "Route::get\('/'\|" -n routes 2>/dev/null | head -n1 | cut -d: -f1)"
if [ -n "${loc:-}" ]; then
  nl -ba "$loc" | sed -n '1,200p' | tee "$OUT/route_root_snippet.txt"
fi

# --- ORG layout/menu checks
say "\n[Org layout/menu checks]"
grep -RIn --include='*.blade.php' 'Organization Console' resources/views | tee "$OUT/org_console_hits.txt" || true

LAY="resources/views/org/layout.blade.php"
if [ -f "$LAY" ]; then
  say "Layout found: $LAY"
  # body classes line
  grep -n '<body' "$LAY" | head -n1 | tee "$OUT/body_line.txt"
  # does body have pinned class?
  if grep -q 'g-sidenav-pinned' "$LAY"; then say "OK: body has g-sidenav-pinned"; else say "WARN: body missing g-sidenav-pinned"; fi
  # main-content margin tweak marker (if we added)
  if grep -q 'org-sidenav-pin-fix:start' "$LAY"; then say "NOTE: org-sidenav-pin-fix CSS present"; else say "INFO: no sidenav CSS tweak marker"; fi
else
  say "WARN: $LAY not found"
fi

# --- Check if our dropdown got an id
grep -RIn --include='*.blade.php' 'id="orgConsoleMenu"' resources/views | tee "$OUT/org_console_id_hits.txt" || true

# --- Org dashboard HTTP fetch (to see rendered classes quickly)
say "\n[Org dashboard HTTP]"
curl -skL -D "$OUT/org.hdr" "$org" -o "$OUT/org.html" || true
head -n1 "$OUT/org.hdr" | tee -a "$OUT/org.http"
egrep -i '^(HTTP/|set-cookie|content-type|content-language|cache-control)' "$OUT/org.hdr" | sed 's/^/  /' | tee -a "$OUT/org.http"
# show body tag as rendered
grep -m1 -o '<body[^>]*>' "$OUT/org.html" | tee "$OUT/org.bodytag.txt" || true

say "\n[Summary]"
say "Files in $OUT/"
ls -1 "$OUT" | sed 's/^/  - /'
say "=== END ==="
