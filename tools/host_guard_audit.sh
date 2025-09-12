#!/usr/bin/env bash
set +e
TS=$(date +%F_%H%M%S)
OUT="audit.host_guard.$TS.txt"
exec > >(tee "$OUT") 2>&1

echo "=== Host Guard / Admin Auth Audit @ $TS ==="
MAIN_HOST=$(grep -E '^MAIN_DOMAIN=' .env | cut -d= -f2-);   [ -z "$MAIN_HOST" ]  && MAIN_HOST="swaeduae.ae"
ADMIN_HOST=$(grep -E '^ADMIN_DOMAIN=' .env | cut -d= -f2-); [ -z "$ADMIN_HOST" ] && ADMIN_HOST="admin.swaeduae.ae"
echo "MAIN_HOST=$MAIN_HOST"
echo "ADMIN_HOST=$ADMIN_HOST"
echo

echo "== A) Route cache + environment =="
php artisan route:cache >/dev/null 2>&1 || true
php artisan route:list | head -n 2
grep -E '^(APP_URL|SESSION_DOMAIN|SESSION_SECURE_COOKIE)=' .env || true
echo

echo "== B) Top guard presence & normalization =="
sed -n '1,20p' routes/web.php
echo
if grep -q "main.admin.redirect" routes/web.php && grep -q "ltrim(\$any,'/')" routes/web.php; then
  echo "PASS: top guard present and normalizing '\$any'"
else
  echo "WARN: top guard missing OR not normalizing '\$any'"
fi
echo

echo "== C) Admin routes: ensure auth + can:access-admin =="
php artisan route:list --json > /tmp/routes.json 2>/dev/null
php -r '
$j=json_decode(file_get_contents("/tmp/routes.json"),true);
$bad1=[];$bad2=[];
foreach($j as $r){
  $name=$r["name"]??""; $uri=$r["uri"]??""; $mw=$r["middleware"]??[];
  if(preg_match("#^admin(/|$)#",$uri) || preg_match("#^admin\.#",$name)){
    $mws = is_array($mw)? $mw : [];
    $hasAuth = in_array("auth",$mws);
    $hasGate = false;
    foreach($mws as $m){ if (strpos($m,"can:access-admin")===0) {$hasGate=true; break;} }
    if(!$hasAuth){ $bad1[]="$name $uri"; }
    if(!$hasGate){ $bad2[]="$name $uri"; }
  }
}
echo "Admin routes missing AUTH:\n"; echo $bad1? " - ".implode("\n - ",$bad1)."\n" : " (none)\n";
echo "Admin routes missing can:access-admin:\n"; echo $bad2? " - ".implode("\n - ",$bad2)."\n" : " (none)\n";
'
echo

echo "== D) HTTP probes (admin host) =="
for u in /admin /admin/login /admin/users /admin/organizations /admin/settings ; do
  code=$(curl -s -o /dev/null -w '%{http_code}' https://$ADMIN_HOST$u)
  printf "https://%s%-20s -> %s\n" "$ADMIN_HOST" "$u" "$code"
done
echo

echo "== E) HTTP probes (main host should redirect /admin* to admin host) =="
for u in /admin /admin/login /admin/users ; do
  code=$(curl -s -I -o /dev/null -w '%{http_code}' https://$MAIN_HOST$u)
  loc=$(curl -sI https://$MAIN_HOST$u | awk -F': ' 'tolower($1)=="location"{print $2}' | tr -d '\r')
  printf "https://%s%-20s -> %s   Location: %s\n" "$MAIN_HOST" "$u" "$code" "${loc:--}"
done
echo

echo "== F) Admin login page sanity (CSRF + form action) =="
html=$(mktemp)
curl -sL https://$ADMIN_HOST/admin/login > "$html"
grep -n 'name="_token"' "$html" | head -n 1 || echo "(no _token found)"
echo "Form actions:"
grep -oE 'action="[^"]+"' "$html" | sed 's/action=//g' | sort -u | sed -n '1,10p' || true
rm -f "$html"
echo

echo "== G) Main-site volunteer/org login pages (should be 200) =="
for u in /login /register /org/login /org/register ; do
  code=$(curl -s -o /dev/null -w '%{http_code}' https://$MAIN_HOST$u)
  printf "https://%s%-20s -> %s\n" "$MAIN_HOST" "$u" "$code"
done
echo

echo "=== SUMMARY ==="
# Quick PASS/FAIL synopsis
FAIL=0
# E) Each /admin* on main host must be 301 and point to admin host
while read -r line; do
  code=$(echo "$line" | awk '{print $NF}')
done < <(true)

echo "Report saved to: $OUT"
