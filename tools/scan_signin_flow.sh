#!/usr/bin/env bash
set -u
TS=$(date +%Y%m%d_%H%M%S)
OUT=~/swaed_scans/reports/signin_flow_$TS.txt
CJ=/tmp/cj.$TS.txt
LOGIN_HTML=/tmp/login.$TS.html
PROFILE_HTML=/tmp/profile.$TS.html
BASE="https://swaeduae.ae"

EMAIL="volscan@swaeduae.ae"
PASS="Temp#Vol1234!"

echo "=== Volunteer Sign-in Flow @ $TS ===" | tee "$OUT"

echo -e "\n[1] Ensure a verified test user exists: $EMAIL" | tee -a "$OUT"
php artisan tinker --execute='
$u = \App\Models\User::where("email","volscan@swaeduae.ae")->first();
if(!$u){
  $u = \App\Models\User::create(["name"=>"Vol Scan","email"=>"volscan@swaeduae.ae","password"=>bcrypt("Temp#Vol1234!")]);
}
if(empty($u->email_verified_at)){ $u->email_verified_at = now(); $u->save(); }
echo "USER_OK\n";
' 2>/dev/null | tee -a "$OUT"

echo -e "\n[2] GET /login (grab CSRF + cookies)" | tee -a "$OUT"
rm -f "$CJ" "$LOGIN_HTML"
curl -s -c "$CJ" -b "$CJ" -D - "$BASE/login" -o "$LOGIN_HTML" | sed -n '1,20p' | tee -a "$OUT"
TOKEN=$(sed -n 's/.*name="_token" value="\([^"]*\)".*/\1/p' "$LOGIN_HTML" | head -n1)
echo "CSRF token: ${TOKEN:-<missing>}" | tee -a "$OUT"

echo -e "\n[3] POST /login (email+password+csrf) – follow redirects, show headers" | tee -a "$OUT"
curl -s -L -c "$CJ" -b "$CJ" -D - \
  -H "Origin: $BASE" -H "Referer: $BASE/login" \
  -H "Content-Type: application/x-www-form-urlencoded" \
  --data-urlencode "email=$EMAIL" \
  --data-urlencode "password=$PASS" \
  --data-urlencode "_token=$TOKEN" \
  --data-urlencode "remember=on" \
  "$BASE/login" -o /dev/null \
  | sed -n '1,40p' | tee -a "$OUT"

echo -e "\n[4] GET /my/profile with session cookies (expect 200)" | tee -a "$OUT"
curl -s -I -c "$CJ" -b "$CJ" "$BASE/my/profile" | sed -n '1,40p' | tee -a "$OUT"
curl -s -c "$CJ" -b "$CJ" "$BASE/my/profile" -o "$PROFILE_HTML"
CODE=$(curl -s -o /dev/null -w "%{http_code}" -c "$CJ" -b "$CJ" "$BASE/my/profile")
echo "Body sample (/my/profile) code=$CODE" | tee -a "$OUT"
grep -in -m1 "<title" "$PROFILE_HTML" | sed -n '1p' | tee -a "$OUT"

echo -e "\n[5] Sign OUT (fetch csrf from page and POST /logout)" | tee -a "$OUT"
LGTOKEN=$(grep -o 'name="_token" value="[^"]*' "$PROFILE_HTML" | head -n1 | sed 's/.*="//')
echo "Logout CSRF: ${LGTOKEN:-<missing>}" | tee -a "$OUT"
curl -s -I -c "$CJ" -b "$CJ" "$BASE/logout" | sed -n '1,5p' | tee -a "$OUT"  # should be 405/419 for GET; just a probe
curl -s -L -c "$CJ" -b "$CJ" -D - \
  -H "Origin: $BASE" -H "Referer: $BASE/my/profile" \
  -H "Content-Type: application/x-www-form-urlencoded" \
  --data-urlencode "_token=$LGTOKEN" \
  "$BASE/logout" -o /dev/null | sed -n '1,40p' | tee -a "$OUT"

echo -e "\n[6] Verify logged-out: GET /my/profile (expect 302 -> /login)" | tee -a "$OUT"
curl -s -I -c "$CJ" -b "$CJ" "$BASE/my/profile" | sed -n '1,40p' | tee -a "$OUT"

echo -e "\n[7] Environment sanity (session + app url)" | tee -a "$OUT"
php -r 'echo "APP_URL=".config("app.url").PHP_EOL; echo "SESSION_DRIVER=".config("session.driver").PHP_EOL; echo "SESSION_DOMAIN=".var_export(config("session.domain"),true).PHP_EOL;' 2>/dev/null | tee -a "$OUT"

echo -e "\nReport saved: $OUT"
