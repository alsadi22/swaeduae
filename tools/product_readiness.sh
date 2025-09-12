#!/usr/bin/env bash
set -euo pipefail
TS=$(date +%F_%H%M%S); OUT="public/health/product-$TS.txt"; mkdir -p public/health
envfile(){ grep -E '^(APP_ENV|APP_DEBUG|APP_URL|MAIL_|SENTRY_|SESSION_|HOURS_GEOFENCE_METERS)=' .env || true; }
artisan(){ php artisan "$@" 2>&1; }
hit(){ curl -s -o /dev/null -w "%{http_code}" "$1"; }
dbq(){ eval "$(awk -F= '/^DB_DATABASE=|^DB_USERNAME=|^DB_PASSWORD=|^DB_HOST=|^DB_PORT=/{gsub(/\r/,""); print "export " $1"=\"" $2"\""}' .env)"; mysql -N -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" -e "$1" 2>/dev/null; }

{
echo "=== PRODUCT READINESS $TS ==="

echo; echo "== ENV SNAPSHOT =="; envfile

echo; echo "== ROUTES (public + admin domains) =="; artisan route:list | egrep -i '(^GET|^POST).*(opportun|registr|apply|cert|qr|verify|hours|profile|org|onboard|calendar|ics|whatsapp)' || true

echo; echo "== DB TABLES / COLUMNS (domain) ==";
dbq "SHOW TABLES" | sed -n '1,200p'
echo "— columns —"
dbq "SELECT TABLE_NAME,COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='$DB_DATABASE' AND (TABLE_NAME RLIKE '(event|opportun|registr|applic|volunteer|hours|audit|org)' OR COLUMN_NAME RLIKE '(slug|status|city|starts_at|ends_at|skills|hours|signature|code|phone)') ORDER BY TABLE_NAME,COLUMN_NAME LIMIT 500" | sed -n '1,300p'

echo; echo "== CODE PRESENCE (models/controllers/views)=="
grep -RIn "class .*Opportunity|class .*Event" app/Models 2>/dev/null || true
grep -RIn "class .*Opportunity|class .*Event" app/Http/Controllers 2>/dev/null | sed -n '1,80p'
grep -RIn "apply|registration|onboard|whatsapp|ics|text/calendar" app 2>/dev/null | sed -n '1,80p'
grep -RIn "@extends\\('public\\.layout" resources/views | sed -n '1,120p'

echo; echo "== PUBLIC PAGES PROBES ==";
for u in / /opportunities /about /contact /privacy /terms; do echo "$u -> $(hit https://swaeduae.ae$u)"; done

echo; echo "== QR & Certificates =="
echo "/qr/verify -> $(hit https://swaeduae.ae/qr/verify)"; artisan route:list | egrep -i 'qr/verify|certificates|my/certificates' || true

echo; echo "== FEATURES SCAN =="
echo "- ICS links:"; grep -RIn "text/calendar|\\.ics|BEGIN:VCALENDAR" app resources 2>/dev/null || echo "  none"
echo "- WhatsApp deep links:"; grep -RIn "wa\\.me/|api\\.whatsapp\\.com/send" app resources 2>/dev/null || echo "  none"
echo "- Map/Calendar libs:"; grep -RIn "leaflet|mapbox|google\\.maps|fullcalendar" package.json resources public 2>/dev/null || echo "  none"
echo "- RTL hooks:"; grep -RIn "dir=\"rtl\"|rtl\\.css|lang\\('ar'\\)|app\\(\\)->getLocale\\(\\)=='ar'" resources 2>/dev/null || echo "  none"

echo; echo "== ERRORS (tail) =="; latest=$(ls -1t storage/logs/*.log 2>/dev/null | head -1); echo "latest=$latest"; [ -n "$latest" ] && tail -n 80 "$latest" || true
} | tee "$OUT"

echo; echo "Saved: $OUT"
echo; echo "SUMMARY:"
echo "- Public pages: / /opportunities /about /contact /privacy /terms => SEE ABOVE"
echo "- Domain tables: events, registrations, certificates, users, audits? => SEE ABOVE"
echo "- Missing flags likely if: ICS none | WhatsApp none | Map none | RTL none"
