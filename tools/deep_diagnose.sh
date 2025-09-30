#!/usr/bin/env bash
PHP_BIN=${PHP_BIN:-php}
# SwaedUAE Deep Diagnose — safe, read-only checks
set +e
TS=$(date +%F_%H%M%S)
OUT="public/health/diag-$TS.txt"
mkdir -p public/health

p() { echo -e "$@" | sed 's/\t/    /g'; }
sep(){ echo "------------------------------------------------------------"; }

{
p "=== SwaedUAE Deep Diagnose $TS ==="

p "\n== ENV / ABOUT (short) =="; php artisan about | sed -n '1,40p'

p "\n== CACHES STATUS (route/config/view) ==";
php artisan config:cache >/dev/null 2>&1 && echo "config:cache OK" || echo "config:cache FAIL"
php artisan route:clear  >/dev/null 2>&1 && echo "route:clear OK"  || echo "route:clear FAIL"
php artisan view:cache   >/dev/null 2>&1 && echo "view:cache OK"   || echo "view:cache FAIL"

p "\n== PHP LINT: routes =="
php -l routes/web.php
for f in routes/*.php; do
  [ "$f" = "routes/web.php" ] && continue
  [ -f "$f" ] && php -l "$f"
done

p "\n== ROUTE CACHE COMPILE TEST =="
php artisan route:cache >/dev/null 2>&1
RC=$?
[ $RC -eq 0 ] && echo "route:cache COMPILED OK" || echo "route:cache FAILED (see below)"

p "\n== If route:cache failed, show the error =="
php artisan route:cache 2>&1 | sed -n '1,80p'

p "\n== QUICK GREPS (duplicates / problems) =="
echo "- opportunities.apply named routes:"
grep -RIn "name\('opportunities\.apply'\)" routes || echo "  none"
echo "- public._schema stray literals:"
grep -RIn "\('public\._schema'\)" resources 2>/dev/null || echo "  none"
echo "- Old static Route::view('/opportunities', 'opportunities.index'):"
grep -RIn "Route::view\('/opportunities',\s*'opportunities\.index'\)" routes 2>/dev/null || echo "  none"
echo "- Home route name usage:"
grep -RIn "route\('home'" resources 2>/dev/null || echo "  (no direct usage or none found)"

p "\n== CONTROLLER/VIEW PRESENCE =="
echo "- PublicOpportunityController:"
[ -f app/Http/Controllers/PublicOpportunityController.php ] && php -l app/Http/Controllers/PublicOpportunityController.php || echo "  MISSING"
echo "- opportunities index view:"
[ -f resources/views/opportunities/index.blade.php ] && echo "  present" || echo "  MISSING"
echo "- opportunities show view:"
[ -f resources/views/opportunities/show.blade.php ] && echo "  present" || echo "  MISSING"

p "\n== DB SNAPSHOT (tables & domain columns) =="
eval "$(awk -F= '/^DB_DATABASE=|^DB_USERNAME=|^DB_PASSWORD=|^DB_HOST=|^DB_PORT=/{gsub(/\r/,""); print "export " $1"=\"" $2"\""}' .env)"
mysql -N -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" -e "SHOW TABLES" | sed -n '1,200p'
echo "— columns —"
mysql -N -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" -e "
SELECT TABLE_NAME,COLUMN_NAME
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA='$DB_DATABASE'
  AND (TABLE_NAME RLIKE '(event|opportun|registr|volunteer|hours|audit|cert)'
       OR COLUMN_NAME RLIKE '(slug|status|city|starts_at|ends_at|skills|hours|signature|code|phone)')
ORDER BY TABLE_NAME,COLUMN_NAME LIMIT 500;" | sed -n '1,300p'

p "\n== ROUTE LIST (top) =="
php artisan route:list 2>&1 | sed -n '1,120p'

p "\n== HTTP PROBES (public) =="
for u in / /opportunities /about /contact /privacy /terms /qr/verify; do
  code=$(curl -s -o /dev/null -w "%{http_code}" "https://swaeduae.ae$u")
  echo "$u -> $code"
done
curl -s -I -L https://swaeduae.ae/admin | sed -n '1,4p'

p "\n== LOG TAIL (errors/warnings) =="
latest=$(ls -1t storage/logs/*.log 2>/dev/null | head -1); echo "latest=$latest"
[ -n "$latest" ] && { tail -n 120 "$latest" | egrep -i "ERROR|EXCEPTION|LogicException|RouteNotFound|Undefined variable|\b500\b" || true; }

p "\n== PWA / Analytics presence =="
ls -l public/manifest.json public/service-worker.js public/img/icon-192.png public/img/icon-512.png 2>/dev/null || true
curl -s https://swaeduae.ae/ | grep -iE 'manifest\.json|service-worker\.js|plausible\.io/js' | sed -n '1,8p'

p "\n== SUMMARY HINTS =="
echo "- If you see route:cache FAILED: fix syntax in routes/web.php at the line reported."
echo "- If duplicates for opportunities.apply appear: keep only one Route::post('/opportunities/{slug}/apply')->name('opportunities.apply')"
echo "- If 'Route [home] not defined' appears: either name your home route ->name('home') or update the header to use url('/') ."
echo "- If /opportunities returns 500 and route:cache compiles: ensure PublicOpportunityController@index exists and returns the view with \$q set."
} | tee "$OUT"

echo "Saved: $OUT"
