#!/usr/bin/env bash
set -eu
ts(){ date +%Y%m%d_%H%M%S; }; TS=$(ts)
OUT=~/swaed_scans/reports/smoke_all_$TS.txt
CJ=/tmp/cj.$TS.txt
BASE="https://swaeduae.ae"
EMAIL="volscan@swaeduae.ae"
PASS="Temp#Vol1234!"

pf(){ printf "%-52s %s\n" "$1" "$2" | tee -a "$OUT"; }
line(){ printf "%s\n" "----------------------------------------------------------------"; }

echo "SwaedUAE SMOKE @ $TS" | tee "$OUT"
line

# 1) CSS present on home
csslines=$(curl -s "$BASE" | grep -in '<link rel="stylesheet"' | wc -l || true)
[ "$csslines" -ge 1 ] && pf "[HOME] stylesheet link present" "PASS" || pf "[HOME] stylesheet link present" "FAIL"

# 2) Auth pages 200
for p in /login /register /org/login /org/register; do
  c=$(curl -s -o /dev/null -w "%{http_code}" "$BASE$p")
  [ "$c" = "200" ] && pf "[AUTH] $p -> 200" "PASS" || pf "[AUTH] $p -> $c" "FAIL"
done

# 3) /login has Google link (optional)
curl -s "$BASE/login" | grep -qi '/auth/google' && pf "[VOL] /login Google link present" "PASS" || pf "[VOL] /login Google link present" "WARN"

# 4) Synonyms redirect
for p in /signin /sign-in /signup /sign-up; do
  loc=$(curl -s -I "$BASE$p" | awk 'BEGIN{IGNORECASE=1}/^location:/{print $2}' | tr -d "\r")
  case "$p" in
    /signin|/sign-in) [[ "$loc" == */login ]]    && pf "[AUTH] $p -> $loc" "PASS" || pf "[AUTH] $p -> $loc" "FAIL" ;;
    /signup|/sign-up) [[ "$loc" == */register ]] && pf "[AUTH] $p -> $loc" "PASS" || pf "[AUTH] $p -> $loc" "FAIL" ;;
  esac
done

# 5) Volunteer sign-in flow (create+verify user, CSRF login), then logout
php artisan tinker --execute='
$u=\App\Models\User::where("email","volscan@swaeduae.ae")->first();
if(!$u){$u=\App\Models\User::create(["name"=>"Vol Scan","email"=>"volscan@swaeduae.ae","password"=>bcrypt("Temp#Vol1234!")]);}
if(empty($u->email_verified_at)){$u->email_verified_at=now();$u->save();} echo "USER_OK\n";
' >/dev/null 2>&1 || true

rm -f "$CJ"; LOGIN_HTML=/tmp/login.$TS.html
curl -s -c "$CJ" -b "$CJ" "$BASE/login" -o "$LOGIN_HTML"
TOKEN=$(sed -n 's/.*name="_token" value="\([^"]*\)".*/\1/p' "$LOGIN_HTML" | head -n1)
[ -n "${TOKEN:-}" ] && pf "[VOL] CSRF token extracted" "PASS" || pf "[VOL] CSRF token extracted" "FAIL"

# POST login
curl -s -L -c "$CJ" -b "$CJ" \
  -H "Origin: $BASE" -H "Referer: $BASE/login" \
  -H "Content-Type: application/x-www-form-urlencoded" \
  --data-urlencode "email=$EMAIL" \
  --data-urlencode "password=$PASS" \
  --data-urlencode "_token=$TOKEN" \
  "$BASE/login" -o /dev/null

code_after=$(curl -s -o /dev/null -w "%{http_code}" -c "$CJ" -b "$CJ" "$BASE/my/profile")
[ "$code_after" = "200" ] && pf "[VOL] /my/profile after login -> 200" "PASS" || pf "[VOL] /my/profile after login -> $code_after" "FAIL"

# logout with decoded XSRF cookie
XSRF_ENC=$(awk '($0 !~ /^#/)&&$6=="XSRF-TOKEN"{print $7}' "$CJ" | tail -n1); XSRF=$(php -r 'echo urldecode($argv[1]);' "$XSRF_ENC")
curl -s -L -c "$CJ" -b "$CJ" -H "Origin: $BASE" -H "Referer: $BASE/my/profile" -H "X-XSRF-TOKEN: $XSRF" -X POST "$BASE/logout" -o /dev/null
code_out=$(curl -s -o /dev/null -w "%{http_code}" -c "$CJ" -b "$CJ" -I "$BASE/my/profile")
[[ "$code_out" = "302" ]] && pf "[VOL] /my/profile after logout -> 302 to /login" "PASS" || pf "[VOL] /my/profile after logout -> $code_out" "FAIL"

# 6) Org pages contain expected strings
curl -s "$BASE/org/login"    | grep -qi "Organization Sign in"      && pf "[ORG] /org/login content" "PASS" || pf "[ORG] /org/login content" "FAIL"
curl -s "$BASE/org/register" | grep -qi "Organization Registration" && pf "[ORG] /org/register content" "PASS" || pf "[ORG] /org/register content" "FAIL"

# 7) Error log tail
line; echo "[LOG TAIL]" | tee -a "$OUT"; tail -n 80 storage/logs/laravel-$(date +%F).log | tee -a "$OUT" || true
line; echo "Report: $OUT"
