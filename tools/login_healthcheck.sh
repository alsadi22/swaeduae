#!/usr/bin/env bash
PHP_BIN=${PHP_BIN:-php}
set -euo pipefail

BASE="${BASE:-http://127.0.0.1:8000}"

VOL_EMAIL="${VOL_EMAIL:-}"     # e.g. export VOL_EMAIL="vol@example.com"
VOL_PASS="${VOL_PASS:-}"       # e.g. export VOL_PASS="secret123"
ORG_EMAIL="${ORG_EMAIL:-}"     # e.g. export ORG_EMAIL="org@example.com"
ORG_PASS="${ORG_PASS:-}"       # e.g. export ORG_PASS="secret123"

TS="$(date +%Y-%m-%d_%H%M%S)"
OUTDIR="_reports"
REPORT="$OUTDIR/login_health_${TS}.txt"
COOK_VOL="$(mktemp)"
COOK_ORG="$(mktemp)"

mkdir -p "$OUTDIR"

PASS=0; FAIL=0
ok(){ printf "PASS  %s\n" "$1"; echo "PASS  $1" >> "$REPORT"; PASS=$((PASS+1)); }
ko(){ printf "FAIL  %s\n" "$1"; echo "FAIL  $1" >> "$REPORT"; FAIL=$((FAIL+1)); }
info(){ printf "INFO  %s\n" "$1"; echo "INFO  $1" >> "$REPORT"; }

# curl helper: records code+time+redirects
hit(){
  local URL="$1" JAR="$2" METHOD="${3:-GET}" DATA="${4:-}"
  if [ "$METHOD" = "GET" ]; then
    curl -sS -L -c "$JAR" -b "$JAR" -o /dev/null \
      -w "code=%{http_code} redirect=%{redirect_url} time=%{time_total}\n" \
      "$URL"
  else
    curl -sS -L -c "$JAR" -b "$JAR" -o /dev/null \
      -w "code=%{http_code} redirect=%{redirect_url} time=%{time_total}\n" \
      -H "Content-Type: application/x-www-form-urlencoded" \
      --data "$DATA" "$URL"
  fi
}

# Extract CSRF token (from meta or hidden input)
csrf(){
  local URL="$1" JAR="$2"
  local html
  html="$(curl -sS -L -c "$JAR" -b "$JAR" "$URL")" || true
  # Try meta tag first
  local t
  t="$(printf "%s" "$html" | grep -oE 'name="csrf-token" content="[^"]+"' | sed -E 's/.*content="([^"]+)".*/\1/' || true)"
  if [ -z "$t" ]; then
    t="$(printf "%s" "$html" | grep -oE 'name="_token" value="[^"]+"' | sed -E 's/.*value="([^"]+)".*/\1/' || true)"
  fi
  printf "%s" "$t"
}

# Section header
{
  echo "Login & Health Report — $TS"
  echo "Base URL: $BASE"
  echo "------------------------------------------------------------"
} > "$REPORT"

# 0) Laravel route sanity (from app)
{
  echo "ROUTES SNAPSHOT"
  echo "------------------------------------------------------------"
  php artisan route:list --path=login   || true
  php artisan route:list --path=org     || true
  php artisan route:list --path=profile || true
  php artisan route:list --path=apply   || true
} >> "$REPORT" 2>&1

# 1) Public endpoints health
for path in "/" "/healthz" "/health" "/sitemap.xml" "/login" "/register" "/contact" "/faq"; do
  res="$(hit "$BASE$path" "$COOK_VOL")"
  code="$(awk -F'[ =]' '{for(i=1;i<=NF;i++) if($i=="code") print $(i+1)}' <<<"$res")"
  [[ "$code" =~ ^2|3 ]] && ok "GET $path [$code]" || ko "GET $path [$code]"
done

# 2) Volunteer login -> profile
if [ -n "${VOL_EMAIL}" ] && [ -n "${VOL_PASS}" ]; then
  t="$(csrf "$BASE/login" "$COOK_VOL")"
  if [ -z "$t" ]; then
    ko "Volunteer login: failed to extract CSRF token"
  else
    res="$(hit "$BASE/login" "$COOK_VOL" POST "_token=$(printf %s "$t" | sed 's/+/%2B/g')&email=$(printf %s "$VOL_EMAIL" | sed 's/@/%40/')&password=$(printf %s "$VOL_PASS")")"
    code="$(awk -F'[ =]' '{for(i=1;i<=NF;i++) if($i=="code") print $(i+1)}' <<<"$res")"
    [[ "$code" =~ ^2|3 ]] && ok "Volunteer POST /login [$code]" || ko "Volunteer POST /login [$code]"
    res="$(hit "$BASE/profile" "$COOK_VOL")"
    code="$(awk -F'[ =]' '{for(i=1;i<=NF;i++) if($i=="code") print $(i+1)}' <<<"$res")"
    [[ "$code" =~ ^2|3 ]] && ok "Volunteer GET /profile [$code]" || ko "Volunteer GET /profile [$code]"
    res="$(hit "$BASE/volunteer/profile" "$COOK_VOL")"
    code="$(awk -F'[ =]' '{for(i=1;i<=NF;i++) if($i=="code") print $(i+1)}' <<<"$res")"
    [[ "$code" =~ ^2|3 ]] && ok "Volunteer GET /volunteer/profile [$code]" || ko "Volunteer GET /volunteer/profile [$code]"
  fi
else
  info "Volunteer credentials not set (export VOL_EMAIL & VOL_PASS to test login)."
fi

# 3) Organization login -> dashboard
# Org login uses /org/login (POST alias points to Laravel login), then /org/dashboard
if [ -n "${ORG_EMAIL}" ] && [ -n "${ORG_PASS}" ]; then
  t2="$(csrf "$BASE/org/login" "$COOK_ORG")"
  if [ -z "$t2" ]; then
    # Some builds render /login with ?type=organization — try main login
    t2="$(csrf "$BASE/login?type=organization" "$COOK_ORG")"
  fi
  if [ -z "$t2" ]; then
    ko "Org login: failed to extract CSRF token"
  else
    # Try org login endpoint first; fallback to /login
    res="$(hit "$BASE/org/login" "$COOK_ORG" POST "_token=$(printf %s "$t2" | sed 's/+/%2B/g')&email=$(printf %s "$ORG_EMAIL" | sed 's/@/%40/')&password=$(printf %s "$ORG_PASS")")" || true
    code="$(awk -F'[ =]' '{for(i=1;i<=NF;i++) if($i=="code") print $(i+1)}' <<<"$res")"
    if [[ ! "$code" =~ ^2|3 ]]; then
      res="$(hit "$BASE/login?type=organization" "$COOK_ORG" POST "_token=$(printf %s "$t2" | sed 's/+/%2B/g')&email=$(printf %s "$ORG_EMAIL" | sed 's/@/%40/')&password=$(printf %s "$ORG_PASS")")" || true
      code="$(awk -F'[ =]' '{for(i=1;i<=NF;i++) if($i=="code") print $(i+1)}' <<<"$res")"
    fi
    [[ "$code" =~ ^2|3 ]] && ok "Org POST login [$code]" || ko "Org POST login [$code]"
    res="$(hit "$BASE/org/dashboard" "$COOK_ORG")"
    code="$(awk -F'[ =]' '{for(i=1;i<=NF;i++) if($i=="code") print $(i+1)}' <<<"$res")"
    [[ "$code" =~ ^2|3 ]] && ok "Org GET /org/dashboard [$code]" || ko "Org GET /org/dashboard [$code]"
  fi
else
  info "Org credentials not set (export ORG_EMAIL & ORG_PASS to test org login)."
fi

# 4) Registration & KYC pages (not posting PII)
for path in "/register" "/org/register" "/volunteer/profile" "/notifications"; do
  res="$(hit "$BASE$path" "$COOK_VOL")"
  code="$(awk -F'[ =]' '{for(i=1;i<=NF;i++) if($i=="code") print $(i+1)}' <<<"$res")"
  [[ "$code" =~ ^2|3 ]] && ok "GET $path [$code]" || ko "GET $path [$code]"
done

# 5) App internals snapshot (DB/Queue/Env) via php
{
  echo
  echo "APP INTERNALS"
  echo "------------------------------------------------------------"
  php -r '
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Schema;
    require "vendor/autoload.php";
    $app=require_once "bootstrap/app.php";
    $kernel=$app->make(Illuminate\Contracts\Console\Kernel::class); $kernel->bootstrap();
    $okDb=true; try{ DB::select("select 1"); }catch(Throwable $e){ $okDb=false; }
    $queuePending= Schema::hasTable("jobs")? (int)DB::table("jobs")->count(): null;
    $queueFailed = Schema::hasTable("failed_jobs")? (int)DB::table("failed_jobs")->count(): null;
    echo "DB_OK=$okDb\n";
    echo "QUEUE_PENDING=".($queuePending===null?"n/a":$queuePending)."\n";
    echo "QUEUE_FAILED=".($queueFailed===null?"n/a":$queueFailed)."\n";
    echo "LARAVEL_VERSION=".app()->version()."\n";
    echo "PHP_VERSION=".PHP_VERSION."\n";
  ' 2>&1
} >> "$REPORT"

# 6) Summary
{
  echo "------------------------------------------------------------"
  echo "SUMMARY: PASS=$PASS FAIL=$FAIL"
} >> "$REPORT"

echo "Report written to: $REPORT"
echo "PASS=$PASS FAIL=$FAIL"
rm -f "$COOK_VOL" "$COOK_ORG"
