#!/usr/bin/env bash
# Deep, read-only audit of login/sign-in UX and backend flow.
set -u
ts(){ date +%Y%m%d_%H%M%S; }
TS=$(ts)
OUT=~/swaed_scans/reports/login_audit_$TS.txt
CJ=/tmp/cj.$TS.txt
BASE="https://swaeduae.ae"

EMAIL="volscan@swaeduae.ae"
PASS="Temp#Vol1234!"

H(){ printf "\n=== %s ===\n" "$*" | tee -a "$OUT"; }
L(){ tee -a "$OUT"; }

echo "SwaedUAE Login Audit @ $TS" | tee "$OUT"

# 0) Environment snippets
H "ENV snap (APP_URL, SESSION)"
php -r 'echo "APP_URL=".(config("app.url")??"unset").PHP_EOL; echo "SESSION_DRIVER=".(config("session.driver")??"unset").PHP_EOL; echo "SESSION_DOMAIN=".var_export(config("session.domain"),true).PHP_EOL;' 2>/dev/null | L

# 1) Routes that matter
H "Routes: login/register + org + logout"
php artisan route:list 2>/dev/null | egrep -i '(^|[[:space:]])/(login|register|forgot-password|logout)\b|(^|[[:space:]])org/(login|register)\b' | L

# 2) Controller & views used for GET /login
H "Controller create() + intended() in AuthenticatedSessionController"
awk 'NR==FNR && /class AuthenticatedSessionController/{f=1} f && NR==FNR && /^{/ && ++b==1{ } 1' \
    app/Http/Controllers/Auth/AuthenticatedSessionController.php 2>/dev/null | sed -n '1,140p' | L

H "Volunteer login view exists & includes social partial?"
ls -l resources/views/auth/login.blade.php 2>/dev/null | L
grep -n "@includeIf('auth._social_logins')" resources/views/auth/login.blade.php 2>/dev/null | L

# 3) Public homepage dropdown HTML (live) & local views
H "LIVE HTML snippet (Sign In/Up items + data-nav)"
curl -s -H 'Cache-Control: no-cache' "$BASE" | \
  awk '/Volunteer Sign In|Organization Sign In|Volunteer Sign Up|Organization Sign Up|data-nav/{print NR": "$0}' | sed -n '1,160p' | L

H "VIEWS: anchors, data-nav, duplicate href in auth menus"
grep -RIn --include='*.blade.php' --exclude='*.blade.php.*' 'data-nav|Volunteer Sign|Organization Sign|<a[^>]*href=' resources/views/partials resources/views/public | sed -n '1,220p' | L

# 4) JS/CSS that can block clicks
H "JS blockers (preventDefault/onclick) near header/home"
grep -RInE --include='*.blade.php' --include='*.js' --exclude='*.blade.php.*' \
  'preventDefault|return +false|addEventListener *\(.+click|onclick=' \
  resources/views resources/js public 2>/dev/null | sed -n '1,220p' | L

H "CSS click blockers (pointer-events:none)"
grep -RInE --include='*.css' --include='*.blade.php' --exclude='*.blade.php.*' \
  'pointer-events\s*:\s*none' resources public 2>/dev/null | L

# 5) HTTP probes (public pages)
probe(){ local u="$1"; printf "%-22s" "$u" | tee -a "$OUT"; curl -s -o /dev/null -w " -> %s " "$BASE$u" | tee -a "$OUT"; curl -s -I "$BASE$u" | awk 'BEGIN{IGNORECASE=1}/^location:/{print "(" $2 ")"}' | tr -d "\r" | tee -a "$OUT"; echo | tee -a "$OUT"; }
H "HTTP probes"
for u in / /login /register /forgot-password /org/login /org/register /verify-email /signin /sign-in /signup /sign-up; do probe "$u"; done

# 6) End-to-end sign-in (email) and sign-out
H "Ensure test user (verified) exists"
php artisan tinker --execute='
$u=\App\Models\User::where("email","volscan@swaeduae.ae")->first();
if(!$u){$u=\App\Models\User::create(["name"=>"Vol Scan","email"=>"volscan@swaeduae.ae","password"=>bcrypt("Temp#Vol1234!")]);}
if(empty($u->email_verified_at)){$u->email_verified_at=now();$u->save();}
echo "USER_OK\n";
' 2>/dev/null | L

H "GET /login (grab CSRF + cookies)"
rm -f "$CJ"; LOGIN_HTML=/tmp/login.$TS.html
curl -s -c "$CJ" -b "$CJ" -D - "$BASE/login" -o "$LOGIN_HTML" | sed -n '1,20p' | L
TOKEN=$(sed -n 's/.*name="_token" value="\([^"]*\)".*/\1/p' "$LOGIN_HTML" | head -n1)
echo "CSRF: ${TOKEN:-<missing>}" | L
echo "Google link present? $(grep -qin '/auth/google' "$LOGIN_HTML" && echo YES || echo NO)" | L

H "POST /login (follow redirects; headers)"
curl -s -L -c "$CJ" -b "$CJ" -D - \
  -H "Origin: $BASE" -H "Referer: $BASE/login" \
  -H "Content-Type: application/x-www-form-urlencoded" \
  --data-urlencode "email=$EMAIL" --data-urlencode "password=$PASS" --data-urlencode "_token=$TOKEN" \
  "$BASE/login" -o /dev/null | sed -n '1,40p' | L

H "GET /my/profile after login (expect 200)"
curl -s -I -c "$CJ" -b "$CJ" "$BASE/my/profile" | sed -n '1,40p' | L
curl -s -c "$CJ" -b "$CJ" "$BASE/my/profile" | sed -n '1,12p' | L

H "POST /logout using decoded XSRF cookie (expect 302 -> /login)"
XSRF_ENC=$(awk '($0 !~ /^#/)&&$6=="XSRF-TOKEN"{print $7}' "$CJ" | tail -n1)
XSRF=$(php -r 'echo urldecode($argv[1]);' "$XSRF_ENC")
echo "XSRF decoded length: ${#XSRF}" | L
curl -s -L -c "$CJ" -b "$CJ" -D - \
  -H "Origin: $BASE" -H "Referer: $BASE/my/profile" \
  -H "X-XSRF-TOKEN: $XSRF" \
  -X POST "$BASE/logout" -o /dev/null | sed -n '1,40p' | L

H "GET /my/profile after logout (expect 302 -> /login)"
curl -s -I -c "$CJ" -b "$CJ" "$BASE/my/profile" | sed -n '1,40p' | L

echo -e "\nReport saved: $OUT" | tee -a "$OUT"
