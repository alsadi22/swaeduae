#!/usr/bin/env bash
set -Eeuo pipefail
# Mute Slack logging during healthchecks to avoid noisy webhook errors in sandbox/CI
export DISABLE_SLACK=1

# ===== Settings =====
export PATH="/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin:$PATH"
SITE="${SITE:-swaeduae.ae}"              # Host header + domain scope
ORIGIN="${ORIGIN:-http://127.0.0.1}"     # Origin (bypasses Cloudflare)
EDGE="${EDGE:-https://swaeduae.ae}"      # Edge (through Cloudflare)
MAX_PAGES="${MAX_PAGES:-300}"            # Crawl page limit
MAX_SECONDS="${MAX_SECONDS:-180}"        # Crawl time limit
UA="SwaedAgent/2.2 (+$EDGE)"
STAMP="$(command date +%F-%H%M%S || echo unknown)"
ROOT="/var/www/swaeduae"
TMP="$ROOT/tmp/agent-$STAMP"
mkdir -p "$TMP"
DEEP_CHECK="${DEEP_CHECK:-1}"            # Run _tools/deep_check.sh if present

say(){ echo -e "$*"; }
pass(){ say "PASS: $*"; }
fail(){ say "FAIL: $*"; FAILS=$((FAILS+1)); }
FAILS=0

need() { command -v "$1" >/dev/null 2>&1 || fail "missing command: $1"; }
for c in php curl grep sed awk tr head nl; do need "$c"; done

code_of(){ curl -skL -o /dev/null -w '%{http_code}' -H "Host: $SITE" -A "$UA" "$1"; }
get_html(){ curl -skL -A "$UA" -H "Host: $SITE" "$1"; }
get_head(){ curl -sI  -A "$UA" -H "Host: $SITE" "$1" | tr -d '\r'; }

# Normalize relative/root links to absolute EDGE URLs (for dedupe/enqueue)
norm(){
  local href="$1"
  # ignore empty/mailto/tel/js/fragment
  [[ -z "$href" || "$href" =~ ^(mailto:|tel:|javascript:) || "$href" =~ ^# ]] && return 0
  if [[ "$href" =~ ^https?:// ]]; then
    echo "$href"
  elif [[ "$href" =~ ^/ ]]; then
    echo "$EDGE$href"
  fi
}
is_internal(){ [[ "$1" == https://$SITE/* || "$1" == http://$SITE/* ]]; }
ok_code(){ [[ "$1" =~ ^(200|301|302)$ ]]; }

cd "$ROOT" || exit 1

# ===== Laravel/App sanity =====
php -v                         >/dev/null || fail "PHP not available"
php -l routes/web.php          >/dev/null || fail "routes/web.php syntax"
php artisan about              >/dev/null || fail "artisan about"

grep -q '^APP_ENV=production' .env && pass "APP_ENV=production" || fail "APP_ENV not production"
grep -q '^APP_DEBUG=false'    .env && pass "APP_DEBUG=false"    || fail "APP_DEBUG not false"

grep -q  '^MAIL_MAILER=smtp'     .env && pass "MAIL_MAILER=smtp" || fail "MAIL_MAILER not smtp"
grep -Eq '^MAIL_HOST=smtp\.zoho' .env && pass "MAIL_HOST Zoho"   || fail "MAIL_HOST not Zoho"
grep -q  '^MAIL_USERNAME='       .env && pass "MAIL_USERNAME set"|| fail "MAIL_USERNAME missing"
grep -q  '^MAIL_PASSWORD='       .env && pass "MAIL_PASSWORD set"|| fail "MAIL_PASSWORD missing"

# Route cache with logging (do not stop on failure)
sudo -u www-data php artisan route:clear >/dev/null || true
if ! sudo -u www-data php artisan route:cache >"$TMP/route-cache.log" 2>&1; then
  fail "artisan route:cache failed (see $TMP/route-cache.log)"
else
  pass "artisan route:cache ok"
fi

# Blade undefined route() names (avoid pipefail)
php _tools/find_bad_route_names.php > "$TMP/bad-routes.txt" 2>&1 || true
if grep -q "OK: no undefined" "$TMP/bad-routes.txt"; then
  pass "No undefined route() names in Blade"
else
  fail "Blade references undefined routes (see $TMP/bad-routes.txt)"
fi

# Key routes exist
for n in home about services contact.show contact.send; do
  if php artisan route:list | grep -q "$n"; then
    pass "route:$n"
  else
    fail "missing route:$n"
  fi
done

# ===== Sensitive file exposure =====
for u in "/.env" "/.git/HEAD" "/storage/logs/laravel.log"; do
  c=$(code_of "$EDGE$u")
  if [[ "$c" == "200" ]]; then fail "Sensitive path EXPOSED: $u (HTTP $c)"
  else pass "Sensitive path blocked: $u (HTTP $c)"; fi
done

# ===== Edge/Origin smokes =====
for p in / /about /services /contact /contact-us /robots.txt /sitemap.xml; do
  co=$(code_of "$ORIGIN$p"); if ok_code "$co"; then pass "ORIGIN $co $p"; else fail "ORIGIN $co $p"; fi
  ce=$(code_of "$EDGE$p");   if ok_code "$ce"; then pass "EDGE   $ce $p"; else fail "EDGE   $ce $p"; fi
done

# ===== SEO/Analytics on Home =====
HTML_HOME="$(get_html "$ORIGIN/")"
echo "$HTML_HOME" > "$TMP/home.html"
grep -q 'application/ld+json' "$TMP/home.html" && pass "JSON-LD present (home)" || fail "JSON-LD missing (home)"
if grep -q '^ANALYTICS_DRIVER=plausible' .env; then
  grep -q 'plausible.io/js/script.js' "$TMP/home.html" && pass "Plausible snippet present" || fail "Plausible snippet missing"
fi

# ===== CSP checks (origin+edge) =====
HDR_O="$(get_head "$ORIGIN/")"
HDR_E="$(get_head "$EDGE/")"
echo "$HDR_O" | grep -qi '^Content-Security-Policy:' && pass "CSP header (origin)" || fail "CSP header missing (origin)"
echo "$HDR_E" | grep -qi '^Content-Security-Policy:' && pass "CSP header (edge)"   || fail "CSP header missing (edge)"
echo "$HDR_O" | grep -qi 'plausible.io' && pass "CSP allows plausible.io (origin)" || say "INFO: CSP may block plausible.io (origin)"
echo "$HDR_E" | grep -qi 'plausible.io' && pass "CSP allows plausible.io (edge)"   || say "INFO: CSP may block plausible.io (edge)"

# ===== Contact form E2E =====
curl -skL -c "$TMP/ck" -A "$UA" "$EDGE/contact-us" -o "$TMP/contact.html"
TOK="$(grep -oP 'name="_token"\s+value="\K[^"]+' "$TMP/contact.html" | head -n1 || true)"
if [[ -n "$TOK" ]]; then
  pass "CSRF token extracted"
  curl -skL -b "$TMP/ck" -A "$UA" \
    -d "_token=$TOK" -d "name=Agent Probe" -d "email=agent@example.com" -d "message=Hello" \
    -D "$TMP/contact.post.headers" -o /dev/null "$EDGE/contact" || true
  head -n 1 "$TMP/contact.post.headers" | grep -qE '^HTTP/(1\.1|2) 302' && pass "Contact POST -> 302" || fail "Contact POST unexpected status"
else
  fail "CSRF token not found on /contact-us"
fi

# ===== Sitemap harvest =====
SITEMAP_URL="$EDGE/sitemap.xml"
MAP_URLS_FILE="$TMP/sitemap.urls"
curl -sk -A "$UA" "$SITEMAP_URL" | grep -oP '(?<=<loc>)[^<]+' > "$MAP_URLS_FILE" || true
[[ -s "$MAP_URLS_FILE" ]] && pass "Sitemap fetched with $(wc -l < "$MAP_URLS_FILE") URLs" || say "INFO: sitemap.xml empty or not parsable"

# ===== Crawler (BFS) =====
say "Starting crawl: limit ${MAX_PAGES} pages / ${MAX_SECONDS}s"
SECONDS=0
VISITED="$TMP/visited.txt"; : > "$VISITED"
QUEUE="$TMP/queue.txt";    : > "$QUEUE"
BROKEN="$TMP/broken-links.txt"; : > "$BROKEN"

# Seeds: home + sitemap URLs
echo "$EDGE/" > "$QUEUE"
[[ -s "$MAP_URLS_FILE" ]] && cat "$MAP_URLS_FILE" >> "$QUEUE"

pages=0

# Unique queue (BFS-ish). Use SECONDS (bash builtin) instead of `date`.
while IFS= read -r URL || [[ -n "${URL:-}" ]]; do
  (( SECONDS > MAX_SECONDS )) && { say "Crawl time limit reached"; break; }
  (( pages >= MAX_PAGES ))    && { say "Crawl page limit reached"; break; }

  grep -qxF "$URL" "$VISITED" 2>/dev/null && continue
  echo "$URL" >> "$VISITED"
  is_internal "$URL" || continue

  HTML="$(get_html "$URL" || true)"
  CODE="$(code_of "$URL" || echo 000)"
  TITLE="$(sed -n 's:.*<title>\(.*\)</title>.*:\1:p' <<<"$HTML" | head -n1 || true)"
  META_DESC="$(grep -io '<meta[^>]*name=["'\'']description["'\''][^>]*>' <<<"$HTML" | head -n1 || true)"

  if ok_code "$CODE"; then pass "Crawl $CODE $URL"; else fail "Crawl $CODE $URL"; fi
  [[ -n "$TITLE" ]]     && pass "Title ok"         || say "INFO: Missing <title> on $URL"
  [[ -n "$META_DESC" ]] && pass "Meta description" || say "INFO: Missing meta description on $URL"

  # Enqueue internal links (process substitution avoids subshell state loss)
  while IFS= read -r HREF; do
    FULL="$(norm "$HREF" || true)"; [[ -n "${FULL:-}" ]] || continue
    is_internal "$FULL" || continue
    [[ "$FULL" =~ /logout ]] && continue
    grep -qxF "$FULL" "$VISITED" 2>/dev/null || echo "$FULL" >> "$QUEUE"
  done < <(grep -Eoi '<a[^>]+href=["'\''][^"'\'']+["'\'']' <<<"$HTML" | sed -E 's/.*href=["'\'']([^"'\'']+)["'\''].*/\1/')

  # Broken-link sampling (first 100 root-abs links)
  while IFS= read -r PATHONLY; do
    LINK="$EDGE$PATHONLY"
    LC=$(code_of "$LINK")
    ok_code "$LC" || { echo "($LC) on $URL -> $LINK" >> "$BROKEN"; fail "Broken link ($LC) on $URL -> $LINK"; }
  done < <(grep -Eoi '<a[^>]+href=["'\'']/[^"'\'']+["'\'']' <<<"$HTML" | sed -E 's/.*href=["'\'']([^"'\'']+)["'\''].*/\1/' | head -n 100)

  pages=$((pages+1))
done < <(awk '!seen[$0]++' "$QUEUE")

broken_links=$(wc -l < "$BROKEN" 2>/dev/null || echo 0)
say "Crawl summary: $pages page(s), $broken_links broken link(s)"
[[ -s "$BROKEN" ]] && say "See broken links: $BROKEN"

# ===== Deep Healthcheck (optional) =====
if [[ "$DEEP_CHECK" == "1" && -x "_tools/deep_check.sh" ]]; then
  say "Running deep healthcheck script (_tools/deep_check.sh)"
  if sudo -u www-data SITE="$SITE" _tools/deep_check.sh; then
    last_log=$(ls -1t storage/logs/healthcheck-*.log 2>/dev/null | head -n1 || true)
    [[ -n "$last_log" ]] && say "Deep check log: $last_log"
  else
    fail "deep_check.sh failed"
  fi
else
  say "Deep healthcheck skipped (set DEEP_CHECK=1 and ensure script exists)"
fi

# ===== Summary & Exit =====
if [[ "$FAILS" -eq 0 ]]; then
  say "=== ADVANCED AGENT: PASS ($STAMP) ==="
  exit 0
else
  say "=== ADVANCED AGENT: FAIL ($STAMP) — $FAILS issue(s) ==="
  exit 1
fi
