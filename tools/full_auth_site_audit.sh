#!/usr/bin/env bash
set -u
ts(){ date +%Y%m%d_%H%M%S; }
TS=$(ts)
OUT=~/swaed_scans/reports/full_auth_site_audit_$TS.txt
CJ=/tmp/cj.$TS.txt
BASE="https://swaeduae.ae"

EMAIL="volscan@swaeduae.ae"
PASS="Temp#Vol1234!"

say(){ printf "%s\n" "$*" | tee -a "$OUT"; }
hr(){ say "----------------------------------------------------------------------"; }

echo "SwaedUAE FULL AUTH + SITE CHECK @ $TS" | tee "$OUT"

# 0) ENV + CSS presence on homepage
hr; say "[ENV SNAP]"
php -r 'echo "APP_URL=".(config("app.url")??"unset").PHP_EOL;' 2>/dev/null | tee -a "$OUT"

hr; say "[HOMEPAGE CSS LINKS]"
curl -s "$BASE" | grep -in '<link rel="stylesheet"' | head -n 10 | tee -a "$OUT"
if curl -s "$BASE" | grep -qi 'bootstrap.min.css\|/assets/css/app.css\|/css/app.css'; then
  say "CSS: PASS (stylesheet link found)"
else
  say "CSS: FAIL (no stylesheet link found)"
fi

# 1) ROUTES sanity (volunteer + org)
hr; say "[ROUTES: login/register + org]"
php artisan route:list 2>/dev/null | egrep -i '(^|[[:space:]])/(login|register|forgot-password)\b|(^|[[:space:]])org/(login|register)\b' | tee -a "$OUT"

# 2) VIEWS sanity (auth views exist, extend public.layout)
hr; say "[VIEWS: auth files + extends public.layout]"
for f in resources/views/auth/login.blade.php resources/views/auth/register.blade.php \
         resources/views/org/auth/login.blade.php resources/views/org/auth/register.blade.php; do
  if [ -f "$f" ]; then
    echo "VIEW: $f" | tee -a "$OUT"
    awk 'NR==1,NR==8{print FNR": "$0}' "$f" | sed -n '1,8p' | tee -a "$OUT"
    if grep -q "@extends('public.layout')" "$f"; then echo "extends public.layout: OK" | tee -a "$OUT"; else echo "extends public.layout: MISSING" | tee -a "$OUT"; fi
  else
    echo "VIEW MISSING: $f" | tee -a "$OUT"
  fi
done

# 3) NAV dropdown (live) has correct anchors + data-nav
hr; say "[HOMEPAGE NAV DROPDOWN (live HTML)]"
curl -s -H 'Cache-Control: no-cache' "$BASE" | \
  awk '/Volunteer Sign In|Organization Sign In|Volunteer Sign Up|Organization Sign Up|data-nav/{print NR": "$0}' | sed -n '1,160p' | tee -a "$OUT"

# 4) AUTH PAGES 200 + Google link on /login
hr; say "[HTTP PROBES: auth pages]"
for u in /login /register /org/login /org/register /forgot-password; do
  printf "%-22s" "$u" | tee -a "$OUT"
  code=$(curl -s -o /dev/null -w "%{http_code}" "$BASE$u")
  echo "-> $code" | tee -a "$OUT"
done
hr; say "[/login Google link present?]"
curl -s "$BASE/login" | grep -qin '/auth/google' && echo "Google: PRESENT" | tee -a "$OUT" || echo "Google: MISSING" | tee -a "$OUT"

# 5) SYNONYMS (/signin, /signup)
hr; say "[SYNONYMS 302]"
for u in /signin /sign-in /signup /sign-up; do
  loc=$(curl -s -I "$BASE$u" | awk 'BEGIN{IGNORECASE=1}/^location:/{print $2}' | tr -d '\r')
  printf "%-10s -> %s\n" "$u" "${loc:-<no redirect>}" | tee -a "$OUT"
done

# 6) VOLUNTEER SIGN-IN FLOW (real CSRF + cookies) and LOGOUT
hr; say "[VOLUNTEER SIGN-IN FLOW]"
php artisan tinker --execute='
$u=\App\Models\User::where("email","volscan@swaeduae.ae")->first();
if(!$u){$u=\App\Models\User::create(["name"=>"Vol Scan","email"=>"volscan@swaeduae.ae","password"=>bcrypt("Temp#Vol1234!")]);}
if(empty($u->email_verified_at)){$u->email_verified_at=now();$u->save();}
echo "USER_OK\n";
' 2>/dev/null | tee -a "$OUT"

rm -f "$CJ"; LOGIN_HTML=/tmp/login.$TS.html
curl -s -c "$CJ" -b "$CJ" -D - "$BASE/login" -o "$LOGIN_HTML" | sed -n '1,20p' | tee -a "$OUT"
TOKEN=$(sed -n 's/.*name="_token" value="\([^"]*\)".*/\1/p' "$LOGIN_HTML" | head -n1)
echo "CSRF: ${TOKEN:-<missing>}" | tee -a "$OUT"

say "[POST /login]"
curl -s -L -c "$CJ" -b "$CJ" -D - \
  -H "Origin: $BASE" -H "Referer: $BASE/login" \
  -H "Content-Type: application/x-www-form-urlencoded" \
  --data-urlencode "email=$EMAIL" \
  --data-urlencode "password=$PASS" \
  --data-urlencode "_token=$TOKEN" \
  "$BASE/login" -o /dev/null | sed -n '1,40p' | tee -a "$OUT"

say "[GET /my/profile after login (expect 200)]"
curl -s -I -c "$CJ" -b "$CJ" "$BASE/my/profile" | sed -n '1,20p' | tee -a "$OUT"

# LOGOUT with decoded XSRF
say "[POST /logout with decoded XSRF (expect 302 -> /login)]"
XSRF_ENC=$(awk '($0 !~ /^#/)&&$6=="XSRF-TOKEN"{print $7}' "$CJ" | tail -n1)
XSRF=$(php -r 'echo urldecode($argv[1]);' "$XSRF_ENC")
curl -s -L -c "$CJ" -b "$CJ" -D - \
  -H "Origin: $BASE" -H "Referer: $BASE/my/profile" \
  -H "X-XSRF-TOKEN: $XSRF" \
  -X POST "$BASE/logout" -o /dev/null | sed -n '1,40p' | tee -a "$OUT"

say "[GET /my/profile after logout (expect 302 -> /login)]"
curl -s -I -c "$CJ" -b "$CJ" "$BASE/my/profile" | sed -n '1,20p' | tee -a "$OUT"

# 7) ORG auth pages basic form fields present
hr; say "[ORG PAGES CONTENT CHECK]"
curl -s "$BASE/org/login"    | grep -in "Organization Sign in\|Business Email" | head -n3 | tee -a "$OUT"
curl -s "$BASE/org/register" | grep -in "Organization Registration\|Organization Name" | head -n3 | tee -a "$OUT"

# 8) ERROR LOG TAIL
hr; say "[ERROR LOG TAIL]"
tail -n 120 storage/logs/laravel-$(date +%F).log | tee -a "$OUT" || true

hr; say "REPORT: $OUT"
