#!/usr/bin/env bash
set -u
ts(){ date +%Y%m%d_%H%M%S; }; TS=$(ts)
OUT=~/swaed_scans/reports/pages_scan_$TS.txt
app_url=$(php -r 'echo config("app.url") ?? "";' 2>/dev/null || true)
commit=$(git rev-parse --short HEAD 2>/dev/null || echo "unknown")

{
  echo "=== SwaedUAE Pages Scan @ $TS ==="
  echo "APP_URL: ${app_url:-unset}"
  echo "Commit:  $commit"
  echo

  echo "== ROUTES: key public + self-service =="
  php artisan route:list 2>/dev/null | egrep -i '(/(about|contact|privacy|terms|stories|qr/verify|certificates/verify|login|register|forgot-password|my/profile|my/settings|organizations|volunteers)\s*\|)' || true
  echo

  echo "== ROUTES: volunteers & org & admin namespaces =="
  php artisan route:list 2>/dev/null | egrep -i '(volunteer\.|org\.|admin\.)' || true
  echo

  echo "== VIEWS present =="
  for d in resources/views/public resources/views/volunteer resources/views/org resources/views/admin resources/views/partials; do
    [ -d "$d" ] || continue
    echo "# $d"
    find "$d" -type f -name '*.blade.php' -printf '%P\n' | sort
  done
  echo

  echo "== Check for stale .html links in live views =="
  grep -RInE --include='*.blade.php' --exclude='*.blade.php.*' '\.html"' resources/views || echo "OK: no .html links in live views"
  echo

  echo "== HTTP probes =="
  probe(){ local url=$1; code=$(curl -s -o /dev/null -w "%{http_code}" "$url"); loc=$(curl -s -I "$url" | awk 'BEGIN{IGNORECASE=1}/^location:/{print $2}' | tr -d "\r"); printf "%-30s -> %s %s\n" "$url" "$code" "${loc:+($loc)}"; }
  main=https://swaeduae.ae; admin=https://admin.swaeduae.ae
  for u in / /about /stories /opportunities /organizations /volunteers /privacy /terms /contact /qr/verify /certificates/verify /login /register /forgot-password /my/profile /my/settings; do
    probe "$main$u"
  done
  for u in /login /admin /; do probe "$admin$u"; done
  echo
} | tee "$OUT"

echo "Report saved: $OUT"
