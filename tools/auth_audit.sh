#!/usr/bin/env bash
PHP_BIN=${PHP_BIN:-php}
set -e
echo "=== AUTH ROUTES ==="
php artisan route:list | grep -E '\blogin\b|\bregister\b|forgot-password|reset-password|confirm-password|verification|logout|admin/login|org/login' | sed -n '1,200p'
echo; echo "=== AUTH PAGES (HTTP) ==="
for u in /login /register /forgot-password /reset-password /reset-password/test-token /confirm-password /email/verification-notification /org/login /org/register /admin/login ; do
  printf "%-40s -> %s\n" "$u" "$(curl -s -o /dev/null -w '%{http_code}' https://swaeduae.ae$u)"
done
echo; echo "=== Missing route() names referenced in blades ==="
php artisan route:list --json > /tmp/routes.json
grep -RIn --include='*.blade.php' -o "route\('([^']+)'\)" resources/views \
 | sed -E "s/.*route\('([^']+)'\).*/\1/" \
 | sort -u > /tmp/route_names_used.txt
php -r '
$r=json_decode(file_get_contents("/tmp/routes.json"),true);
$have=[]; foreach($r as $x){ if(!empty($x["name"])) $have[$x["name"]]=1; }
$used=file("/tmp/route_names_used.txt",FILE_IGNORE_NEW_LINES);
foreach($used as $n){ if(!isset($have[$n])) echo "MISSING: route(\"$n\")\n"; }'
