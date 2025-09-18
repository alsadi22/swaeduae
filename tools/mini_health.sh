#!/usr/bin/env bash
set -euo pipefail
cd /var/www/swaeduae/current

OUT="health_$(date +%F_%H%M%S).log"
exec > >(tee -a "$OUT") 2>&1

hr(){ printf "\n=== %s ===\n" "$1"; }

hr "CONTEXT"
echo "PWD: $(pwd)"; echo "USER: $(whoami)"
echo -n "release -> "; readlink -f . || true
php -v | head -n1 || true
php artisan --version || true

hr "PUBLIC HTTP"
for p in / /login /register /org/login /org/register /contact /about /privacy /terms ; do
  printf "%-18s : " "EDGE $p"
  curl -s -o /dev/null -w "HTTP %{http_code}\n" "https://swaeduae.ae$p"
done

hr "ADMIN HTTP"
for p in / /login /admin /admin/login ; do
  printf "%-18s : " "EDGE $p"
  curl -s -o /dev/null -w "HTTP %{http_code}\n" "https://admin.swaeduae.ae$p"
done

hr "ASSETS (headers)"
for u in /assets/app.css /assets/app.js ; do
  echo "$u"
  curl -sSI "https://swaeduae.ae$u" | tr -d "\r" | egrep -i "HTTP/|content-type|cache-control|etag|last-modified|cf-cache-status|x-content-type-options" || true
done

hr "PHP/OPCACHE"
php -m | grep -E "mbstring|openssl|pdo|pdo_mysql|curl|json|xml|intl|gd|imagick|redis|opcache" || true
echo "-- FPM --"; /usr/sbin/php-fpm8.3 -i 2>/dev/null | awk -F"=>" "/^opcache.enable/ {gsub(/ /,\"\"); print \$1\"=\"\$2}" | sort -u || true

hr "PERMS"
ls -ld storage bootstrap/cache || true
test -w storage && echo "storage writable" || echo "storage NOT writable"
test -w bootstrap/cache && echo "cache writable" || echo "cache NOT writable"
[ -L public/storage ] && echo "public/storage -> $(readlink -f public/storage)" || echo "public/storage symlink MISSING"

hr "CONFIG (redacted)"
php <<'PHP'
<?php
require __DIR__."/vendor/autoload.php";
$app = require __DIR__."/bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
foreach(["app.name","app.env","app.url","cache.default","queue.default","database.default","mail.default"] as $k){
  try{$v=config($k); if(is_array($v))$v="[array]"; if(is_bool($v))$v=$v?"true":"false"; printf("%-20s = %s\n",$k,$v??"null");}
  catch(Throwable $e){ printf("%-20s = ? (%s)\n",$k,get_class($e));}
}
PHP

hr "MIGRATIONS"
php artisan migrate:status | sed -n "1,80p" || true

hr "QUEUE/SCHED"
systemctl is-active swaed-queue.service >/dev/null 2>&1 && echo "queue: ACTIVE" || echo "queue: NOT active"
php artisan schedule:list || true

hr "SUMMARY"
pub_bad=$(for p in / /login /register /org/login /org/register /contact /about /privacy /terms ; do curl -s -o /dev/null -w "%{http_code} " "https://swaeduae.ae$p"; done | tr " " "\n" | grep -vcE "^(200|301|302)$" || true)
adm_bad=$(for p in / /login /admin /admin/login ; do curl -s -o /dev/null -w "%{http_code} " "https://admin.swaeduae.ae$p"; done | tr " " "\n" | grep -vcE "^(200|301|302|401)$" || true)
echo "public endpoints bad: ${pub_bad:-0}"
echo "admin endpoints bad : ${adm_bad:-0}"
echo "log: $OUT"
