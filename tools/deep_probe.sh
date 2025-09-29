#!/usr/bin/env bash
# READ-ONLY DEEP PROBE — no app edits, no service restarts.
set -uo pipefail
BASE="${BASE:-https://swaeduae.ae}"
TS="$(date +'%F_%H%M%S')"
OUT="public/health/deep-probe-$TS.txt"
mkdir -p public/health tools
exec > >(tee "$OUT") 2>&1

green=$'\e[32m'; red=$'\e[31m'; yellow=$'\e[33m'; reset=$'\e[0m'
HIT(){ echo "${green}[HIT]${reset} $*"; }
MISS(){ echo "${red}[MISS]${reset} $*"; }
WARN(){ echo "${yellow}[WARN]${reset} $*"; }

echo "=== DEEP PROBE ($(date -u '+%F %T') UTC) ===  BASE=$BASE"
php -v | head -n1 || true

echo; echo "-- Lint routes before anything --"
php -l routes/web.php && HIT "routes/web.php lints OK" || MISS "routes/web.php has syntax errors"

echo; echo "-- Key route presence & mappings --"
php artisan route:list > /tmp/rt.txt 2>/tmp/rt.err || true
if [ -s /tmp/rt.txt ]; then
  grep -E "opportunities\.index|opportunities\.show|opportunities\.apply|account\.applications|org\.opportunities\.index" /tmp/rt.txt | sed 's/^/[ROUTE] /' || true
  if grep -q "opportunities/{slug}/apply" /tmp/rt.txt; then
    map=$(grep "opportunities/{slug}/apply" /tmp/rt.txt || true)
    echo "$map" | grep -q "Public\\\ApplyController@store" \
      && HIT "Apply route uses Public\\ApplyController@store" \
      || WARN "Apply route mapped to: $(echo "$map" | awk '{$1=$1;print}')"
  else MISS "Apply route missing"; fi
  grep -q "ics\.show" /tmp/rt.txt && HIT "ICS route registered" || MISS "ICS route missing"
  grep -q "qr\.verify" /tmp/rt.txt && HIT "QR verify route registered" || MISS "QR verify route missing"
  grep -q "account\.certificates" /tmp/rt.txt && HIT "Certificates page route registered" || WARN "Certificates page route missing"
else
  MISS "route:list failed"; sed -n '1,120p' /tmp/rt.err
fi

echo; echo "-- HTTP probes --"
for u in / /opportunities /opportunities/demo-event /account/applications /login /qr/verify /ics/demo-event /account/certificates; do
  code=$(curl -s -o /dev/null -w "%{http_code}" -I "$BASE$u")
  printf "%-28s -> %s\n" "$u" "$code"
done

echo; echo "-- Controllers: legacy table references (org) --"
grep -RniE "registrations|events\b" app/Http/Controllers/Org || HIT "No legacy-table refs in Org controllers"

echo; echo "-- DB tables present? --"
php artisan tinker --execute='
use Illuminate\Support\Facades\Schema;
foreach (["opportunities","applications","certificates","event_registrations","events"] as $t) {
  echo $t.": ".(Schema::hasTable($t)?"yes":"no").PHP_EOL;
}'
echo; echo "-- Sample data for the known test user (read-only) --"
php artisan tinker --execute='
use Illuminate\Support\Facades\DB;
$u = DB::table("users")->where("email","volunteer@test.local")->first();
echo "user_id=".( $u->id ?? "NULL").PHP_EOL;
if ($u) {
  $apps = DB::table("applications as a")->join("opportunities as o","o.id","=","a.opportunity_id")
    ->where("a.user_id",$u->id)->select("o.slug","o.title","a.status")->get();
  foreach($apps as $r) echo "{$r->slug} | {$r->title} | {$r->status}\n";
}
' 2>/dev/null || WARN "tinker read failed"

echo; echo "-- Mail & queue (read-only) --"
grep -E '^(MAIL_MAILER|MAIL_HOST|MAIL_PORT|MAIL_FROM_ADDRESS|MAIL_FROM_NAME)=' .env || true
systemctl status swaed-queue --no-pager | sed -n '1,10p' || true

echo; echo "-- PWA & sitemap --"
for u in /manifest.json /service-worker.js /sitemap.xml; do
  code=$(curl -s -o /dev/null -w "%{http_code}" -I "$BASE$u")
  printf "%-20s -> %s\n" "$u" "$code"
done

echo; echo "Saved report: $OUT"
