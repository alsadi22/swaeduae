#!/usr/bin/env bash
# swaeduae.ae roadmap audit — PASS/FAIL snapshot (auto-saves to a .txt file)
# Usage: tools/roadmap_audit.sh [APP_URL]
set -u

# ===== output file (auto-created in current dir) =====
OUTFILE="${ROADMAP_OUT:-"./roadmap_audit_$(date +%F_%H%M%S).txt"}"
mkdir -p "$(dirname "$OUTFILE")"
# mirror all stdout/stderr to both terminal and the file
exec > >(tee "$OUTFILE") 2>&1
echo "# Writing audit to: $OUTFILE"

APP_URL="${1:-$(grep -E '^APP_URL=' .env 2>/dev/null | cut -d= -f2)}"
bold(){ printf "\033[1m%s\033[0m\n" "$*"; }
ok(){   printf "✅ %s\n" "$*"; }
bad(){  printf "❌ %s\n" "$*"; }
warn(){ printf "⚠️  %s\n" "$*"; }

need_bin(){ command -v "$1" >/dev/null || { bad "missing binary: $1"; exit 1; }; }
need_bin php; need_bin composer

artisan(){ php artisan "$@" 2>/dev/null; }
has_cmd(){ php artisan list --raw 2>/dev/null | grep -qx "$1"; }
pkg(){ composer show -N "$1" >/dev/null 2>&1; }
env_has(){ grep -qE "^$1=" .env* 2>/dev/null; }
any_files(){ eval "ls -1 $1 >/dev/null 2>&1"; }
grep_code(){ grep -RInE "$1" app routes resources database 2>/dev/null; }

bold "=== Phase 0 — hygiene & guardrails ==="
bk_count=$(find resources app -type f \( -name "*.bak*" -o -name "*.inplace*" -o -path "*/BEFORE_*" -o -name "*.p" \) | wc -l | tr -d ' ')
if [ "$bk_count" -eq 0 ]; then ok "No backup/temporary files in app/resources"; else warn "$bk_count backup/temporary files present (exclude from deploy)"; fi

if [ -d tests ]; then
  if has_cmd test; then ok "Laravel test runner present"; else warn "Laravel test runner not found in this install"; fi
  [ -n "$(grep_code 'events\.show')" ] && ok "Feature tests mention events.show" || warn "No tests found referencing events.show"
else
  warn "tests/ directory not found"
fi

if pkg laravel/telescope && [ -f app/Providers/TelescopeServiceProvider.php ]; then ok "Telescope installed"; else warn "Telescope not detected"; fi
if pkg sentry/sentry-laravel && env_has "SENTRY_LARAVEL_DSN"; then ok "Sentry installed & DSN set"; else warn "Sentry not configured"; fi
if env_has "QUEUE_CONNECTION"; then ok "QUEUE_CONNECTION present in .env"; else warn "QUEUE_CONNECTION missing in .env"; fi
if grep -q "schedule(" app/Console/Kernel.php 2>/dev/null; then ok "Scheduler tasks defined in Console\\Kernel"; else warn "No scheduled tasks found in Console\\Kernel"; fi
if [ -f database/seeders/DatabaseSeeder.php ]; then
  grep -qEi "staging|demo|faker|seeder" database/seeders/DatabaseSeeder.php && ok "Seeders likely configured for staging/demo" || warn "DatabaseSeeder present but no obvious staging seeding"
else
  warn "DatabaseSeeder.php not found"
fi

bold "=== Phase 1 — UX parity & trust ==="
if grep_code 'subYears\(\s*14\s*\)|before_or_equal:.*14.*year|MinAge|older_(than|or_equal)'; then
  ok "Age policy logic detected (≥14 or similar)"
else
  warn "No explicit ≥14 age validation found"
fi

[ -f resources/views/organization/register.blade.php ] || any_files "resources/views/org/*register*.blade.php" \
  && ok "Organization registration views exist" || warn "Org registration views not found"
grep -RInE 'status|approved|pending' app database/migrations 2>/dev/null | grep -qi 'organiza' \
  && ok "Org approval/status logic present" || warn "Could not find org approval/status fields"

any_files "resources/views/public/opportunities/index.blade.php" && ok "Public opportunities index present" || warn "Public opportunities index missing"
any_files "resources/views/public/events/index.blade.php" && ok "Public events index present" || warn "Public events index missing"
any_files "resources/views/public/organizations/index.blade.php" && ok "Public organizations index present" || warn "Public organizations index missing"
any_files "resources/views/**/categories*.blade.php" && ok "Categories views present" || warn "Categories views missing"
any_files "resources/views/**/regions/*.blade.php" && ok "Regions pages present" || warn "Regions pages missing"

grep -RInE '<html[^>]+dir="rtl"|:dir\(rtl\)|rtl:' resources/views 2>/dev/null \
  && ok "RTL handling detected" || warn "No explicit RTL markers found"
grep -RInE 'skip[- ]?link|sr-only|aria-' resources/views 2>/dev/null \
  && ok "Basic a11y markers present" || warn "A11y markers not obvious (aria/skip links)"

grep_code 'Cache::remember|rememberForever|->remember\(' && ok "View/data caching used somewhere" || warn "No caching helpers found"
grep -RInE '->with\(|->withCount\(' app 2>/dev/null >/dev/null && ok "Eager-loading patterns present" || warn "Eager loading not obvious"

grep -RInE 'hreflang|<meta[^>]+property="og:|twitter:card' resources/views 2>/dev/null \
  && ok "OG/Twitter/hreflang present" || warn "OG/Twitter/hreflang not detected in views"
[ -f public/sitemap.xml ] || pkg spatie/laravel-sitemap \
  && ok "Sitemap present or package installed" || warn "No sitemap.xml or sitemap package detected"

bold "=== Phase 2 — certificates & hours ==="
artisan route:list --path=certif | grep -qi 'verify' && ok "Certificate verify route present" || warn "Certificate verify route not found"
any_files "resources/views/**/certificates/*.blade.php" && ok "Certificate views present" || warn "Certificate views missing"
grep_code 'checkin|check-out|attendance' >/dev/null && ok "Check-in/attendance code paths found" || warn "No check-in/attendance mentions found"

bold "=== Phase 3 — attendance & on-site ops ==="
artisan route:list --path=qr | grep -q . && ok "QR routes present" || warn "No QR routes found"
any_files "resources/views/**/attendance/*.blade.php" && ok "Attendance views present" || warn "Attendance views missing"

bold "=== Phase 4 — PWA/mobile ==="
[ -f public/manifest.json ] && ok "PWA manifest.json present" || warn "manifest.json not found"
[ -f public/service-worker.js ] && ok "service-worker.js present" || warn "service-worker.js not found"

bold "=== Phase 5 — content & learning ==="
any_files "resources/views/**/library*.blade.php" && ok "Virtual library-like pages found" || warn "No Virtual Library pages detected"

bold "=== Phase 6 — integrations & growth ==="
grep_code 'uae.?pass|uaepass' && ok "UAE PASS mentions found" || warn "No UAE PASS integration detected"
(pkg twilio/sdk || pkg vonage/client) && ok "SMS SDK present (Twilio/Vonage)" || warn "No SMS SDK detected"
grep -RIn 'gtag\(|Google Tag Manager|Matomo' resources/views public 2>/dev/null \
  && ok "Analytics snippet present" || warn "No GA/Matomo snippets found"
(pkg anhskohbo/no-captcha || env_has "NOCAPTCHA_SECRET") && ok "reCAPTCHA present/configured" || warn "No reCAPTCHA detected"

bold "=== Phase 7 — governance & safety ==="
any_files "resources/views/**/privacy*.blade.php" && ok "Privacy page present" || warn "Privacy page not found"
any_files "resources/views/**/terms*.blade.php" && ok "Terms page present" || warn "Terms page not found"
grep_code 'report abuse|abuse|flag|moderation' && ok "Moderation/reporting hooks present" || warn "No obvious moderation hooks"

bold "=== Phase 8 — analytics & impact ==="
any_files "resources/views/**/dashboard/*.blade.php" && ok "Dashboards present" || warn "Dashboards not found"
(pkg maatwebsite/excel || grep_code 'CSV|Excel|export') && ok "Export capability present/hinted" || warn "No export package or mentions"

bold "=== Phase 9 — polish & differentiation ==="
grep_code 'challenge|campaign' && ok "Challenges/Campaigns present in code/content" || warn "No challenges/campaigns detected"
grep_code 'Verified Organization|verified' && ok "Verified-org indicators present" || warn "No verified-org markers detected"

bold "=== Runtime smoke (optional) ==="
if [ -n "${APP_URL:-}" ]; then
  if command -v curl >/dev/null; then
    http="$(curl -sI -m 10 "$APP_URL/events/1" | awk 'BEGIN{c=0;l=""} /^HTTP/{c=$2} /^Location:/{l=$2} END{print c" "l}')"
    code="$(echo "$http" | awk '{print $1}')"; loc="$(echo "$http" | awk '{print $2}')"
    if [ "$code" = "301" ] && echo "$loc" | grep -q '/events/'; then ok "GET /events/1 → 301 to slug ($loc)"; else warn "GET /events/1 did not 301→slug ($http)"; fi
    slug="$(php -r 'require "vendor/autoload.php"; $app=require "bootstrap/app.php"; $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap(); if(class_exists("\\App\\Models\\Event")){ $s=\\App\\Models\\Event::query()->whereNotNull("slug")->value("slug"); if($s) echo $s; }' 2>/dev/null)"
    if [ -n "$slug" ]; then
      code2="$(curl -sIL -o /dev/null -w "%{http_code}" -m 10 "$APP_URL/events/$slug")"
      [ "$code2" = "200" ] && ok "GET /events/$slug → 200" || warn "GET /events/$slug → $code2"
    else
      warn "Could not auto-detect an event slug to test"
    fi
  else
    warn "curl not available; skipping HTTP smoke"
  fi
else
  warn "APP_URL not set; skipping HTTP smoke"
fi

bold "=== Done ==="
