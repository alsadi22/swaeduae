#!/usr/bin/env bash
PHP_BIN=${PHP_BIN:-php}
# SwaedUAE deep audit — safe, read-only, idempotent.
set +e  # don't exit on errors
TS=$(date +%F_%H%M%S)
OUT="audit.$TS.txt"
exec > >(tee "$OUT") 2>&1

echo "=== SwaedUAE FULL AUDIT @ $TS ==="
echo "## System / Env"
date; whoami; hostname; pwd
php -v | head -n 2 || true
php artisan --version || true
php artisan about --only=app,environment 2>/dev/null || php artisan about || true

echo
echo "## App env quick read"
php -r "define('LARAVEL_START', microtime(true)); require 'vendor/autoload.php'; \$app=require 'bootstrap/app.php'; \$app->make(Illuminate\Contracts\Console\Kernel::class); echo 'env='.config('app.env').PHP_EOL; echo 'url='.config('app.url').PHP_EOL;" 2>/dev/null || echo "about-read failed (non-fatal)"

echo
echo "## Cache status (will NOT stop on failure)"
php artisan config:clear >/dev/null 2>&1 || true
php artisan route:clear  >/dev/null 2>&1 || true
php artisan view:clear   >/dev/null 2>&1 || true
php artisan route:cache || echo "route:cache FAILED above (details printed by artisan)"

echo
echo "## Route list (first 60)"
php artisan route:list | sed -n '1,60p' || true

echo
echo "## Route duplicates & dev-like routes"
php artisan route:list --json 2>/dev/null | php -r '
$d=json_decode(stream_get_contents(STDIN), true);
if(!$d){echo "route:list --json not available\n"; exit;}
$names=[]; $uris=[];
foreach($d as $r){ $n=$r["name"]??""; $u=$r["uri"]??""; $names[$n]=($names[$n]??0)+1; $uris[$u]=($uris[$u]??0)+1; }
echo "== DUP NAMES ==\n"; foreach($names as $n=>$c){ if($n!=="" && $c>1) echo "$c x $n\n"; }
echo "== DUP URIS ==\n"; foreach($uris as $u=>$c){ if($c>1) echo "$c x $u\n"; }
echo "== DEV-LIKE ==\n"; foreach($d as $r){ $u=$r["uri"]??""; if(preg_match("#^_agent|^dev/|/dev/#",$u)) echo ($r["method"]??"")."  ".$u."  ".($r["name"]??"")."\n"; }
' || true

echo
echo "## Dev route guard presence"
for f in routes/_agent_ping.php routes/_agent_diag_api.php; do
  [ -f "$f" ] && { printf "%-28s : " "$f"; grep -q "app()->environment('production')" "$f" && echo "GUARD_OK" || echo "NO_GUARD"; }
done

echo
echo "## View discovery (navbar/header candidates)"
grep -RIl --include='*.blade.php' -E '<nav|navbar-nav|class="navbar' resources/views | sed -n '1,120p' || true

echo
echo "## Public header (header-public.blade.php) — first 80 lines"
nl -ba resources/views/partials/header-public.blade.php 2>/dev/null | sed -n '1,80p' || echo "header-public not found"

echo
echo "## Occurrences of account/auth includes & common texts"
grep -RIn --include='*.blade.php' -E "partials\.account_menu|partials\.auth_dropdown|components\.auth-nav|Sign in|Organization|Account" resources/views | sed -n '1,160p' || true

echo
echo "## Home hero view(s)"
grep -RIn --include='*.blade.php' -E 'Welcome to[[:space:]]+SwaedUAE|class=\"hero' resources/views | sed -n '1,120p' || true

echo
echo "## Recently modified views (last 24h)"
find resources/views -type f -mtime -1 -printf '%TY-%Tm-%Td %TH:%TM  %p\n' | sort | sed -n '1,200p' || true

echo
echo "## Endpoint probes (HTTP codes)"
for u in / /login /register /forgot-password /org/login /org/register /my/profile /qr/verify; do
  printf "%-20s -> %s\n" "$u" "$(curl -s -o /dev/null -w '%{http_code}' "https://swaeduae.ae$u")"
done

echo
echo "## Last production.ERROR block (if any)"
LOG="storage/logs/laravel-$(date +%F).log"
if [ -f "$LOG" ]; then
  LN=$(grep -n "production.ERROR" "$LOG" | tail -n1 | cut -d: -f1)
  [ -n "$LN" ] && sed -n "$LN,$((LN+80))p" "$LOG" || echo "No production.ERROR today."
else
  echo "No log file for today."
fi

echo
echo "=== END AUDIT — saved to $OUT ==="
