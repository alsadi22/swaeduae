#!/usr/bin/env bash
set -u
TS=$(date +%Y%m%d_%H%M%S)
OUT=~/swaed_scans/reports/deep_nav_$TS.txt

echo "=== Deep Nav Scan @ $TS ===" | tee "$OUT"

pp(){ printf "%s\n" "$*" | tee -a "$OUT" ; }

pp "\n[1] Routes for auth pages"
php artisan route:list 2>/dev/null | egrep -i '(^|[[:space:]])/(login|register|forgot-password)\b|(^|[[:space:]])org/(login|register)\b' | tee -a "$OUT"

pp "\n[2] Live HTML — homepage block (Sign In/Up & data-nav)"
curl -s -H 'Cache-Control: no-cache' https://swaeduae.ae | \
  awk '/Volunteer Sign In|Organization Sign In|Volunteer Sign Up|Organization Sign Up|data-nav/{print NR": "$0}' | sed -n '1,120p' | tee -a "$OUT"

pp "\n[3] View sources — look for anchors & duplicate href"
grep -RIn --include='*.blade.php' --exclude='*.blade.php.*' \
  'data-nav|Volunteer Sign|Organization Sign|<a[^>]*href=' resources/views | sed -n '1,200p' | tee -a "$OUT"

pp "\n[4] JS blockers — preventDefault/return false on clicks"
grep -RInE --include='*.blade.php' --include='*.js' --exclude='*.blade.php.*' \
  'preventDefault|return +false|addEventListener *\(.+click|onclick=' \
  resources/views resources/js public 2>/dev/null | sed -n '1,200p' | tee -a "$OUT"

pp "\n[5] CSS that disables clicks"
grep -RInE --include='*.css' --include='*.blade.php' --exclude='*.blade.php.*' \
  'pointer-events\s*:\s*none' resources public 2>/dev/null | tee -a "$OUT"

pp "\n[6] Session / APP URL (quick sanity via PHP)"
php -r 'echo "APP_URL=".(config("app.url")??"unset").PHP_EOL; echo "SESSION_DRIVER=".(config("session.driver")??"unset").PHP_EOL; echo "SESSION_DOMAIN=".var_export(config("session.domain"),true).PHP_EOL;' 2>/dev/null | tee -a "$OUT"

pp "\nReport saved: $OUT"
