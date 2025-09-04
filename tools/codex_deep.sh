#!/usr/bin/env bash
set -euo pipefail
APP="/var/www/swaeduae"; PHP_BIN="${PHP:-php}"
LOG="$APP/storage/logs/codex_deep_$(date +%Y%m%d-%H%M%S).log"
REPORT="$APP/storage/app/codex/REMEDIATION.md"
JSON="$APP/storage/app/codex/summary.json"
SEV=0
say(){ printf "[%s] %s\n" "$(date +%H:%M:%S)" "$*" | tee -a "$LOG"; }
add_md(){ echo -e "$*" >> "$REPORT"; }
kv(){ printf '"%s":%s' "$1" "$2"; }

cd "$APP"
: > "$LOG"
echo "# Codex Deep Report ($(date '+%Y-%m-%d %H:%M:%S %z'))" > "$REPORT"
add_md ""

# 0) Composer sanity (warn-only)
if command -v composer >/dev/null 2>&1; then
  say "composer validate/audit …"
  composer validate --no-check-all >>"$LOG" 2>&1 || add_md "- WARN: composer.json validation warnings (see log)"
  composer audit --no-dev --format plain >>"$LOG" 2>&1 || add_md "- WARN: composer audit advisories (see log)"
fi

# 1) Hard parse errors (fail if any)
say "php -l (per-file)…"
PARSE="$APP/storage/app/codex/parse_errors.txt"; : > "$PARSE"
if ! find app routes database config -name '*.php' -print0 2>/dev/null | xargs -0 -n1 -P4 php -l >>"$LOG" 2>>"$PARSE"; then true; fi
if [ -s "$PARSE" ]; then
  say "ERROR: parse/fatal errors"; SEV=$((SEV+1))
  add_md "## Parse/Fatal Errors\nFix these first. Snippets:"
  while IFS= read -r line; do
    f="$(echo "$line" | sed -n 's/.* in \(.*\) on line \([0-9]\+\).*/\1/p')"
    l="$(echo "$line" | sed -n 's/.* on line \([0-9]\+\).*/\1/p')"
    [ -f "$f" ] || continue
    lo=$((l>5?l-5:1)); hi=$((l+5))
    add_md "\n**$line**\n\`\`\`php"
    nl -ba "$f" | sed -n "${lo},${hi}p" >> "$REPORT"
    add_md "\`\`\`"
  done < <(sed 's/\r$//' "$PARSE")
fi

# 2) Parallel-lint (fast coverage)
if [ -x tools/parallel-lint.phar ]; then
  say "parallel-lint …"
  php tools/parallel-lint.phar -j 4 --colors app routes database config >>"$LOG" 2>&1 || add_md "- WARN: parallel-lint issues (see log)"
fi

# 3) PHPStan (read-only)
if [ -x vendor/bin/phpstan ]; then
  say "phpstan analyse …"
  vendor/bin/phpstan analyse --no-progress --memory-limit=1G >>"$LOG" 2>&1 || { add_md "## PHPStan\n- WARN: issues found (see log)"; SEV=$((SEV+1)); }
else
  add_md "## PHPStan\n- SKIP: vendor/bin/phpstan not found"
fi

# 4) Psalm (read-only)
if [ -x tools/psalm.phar ]; then
  say "psalm …"
  php tools/psalm.phar --no-cache --config=tools/config/psalm.xml --output-format=compact >>"$LOG" 2>&1 || add_md "## Psalm\n- WARN: issues found (see log)"
else
  add_md "## Psalm\n- SKIP: psalm.phar not present"
fi

# 5) PHPMD (smells) – with our ruleset
if [ -x tools/phpmd.phar ]; then
  say "phpmd …"
  php tools/phpmd.phar app text tools/config/phpmd.xml >>"$LOG" 2>&1 || add_md "## PHPMD\n- WARN: smells found (see log)"
else
  add_md "## PHPMD\n- SKIP: phpmd.phar not present"
fi

# 6) Duplication (PHPCPD)
if [ -x vendor/bin/phpcpd ]; then
  say "phpcpd (composer) …"
  vendor/bin/phpcpd app routes database >>"$LOG" 2>&1 || true
elif [ -x tools/phpcpd.phar ]; then
  say "phpcpd (phar) …"
  php tools/phpcpd.phar app routes database >>"$LOG" 2>&1 || true
fi
# Extract quick summary
DUP_SUM=$(grep -A2 -E '^Found [0-9]+ clones' "$LOG" || true)
[ -n "$DUP_SUM" ] && add_md "## Duplication (PHPCPD)\n\`\`\`\n$DUP_SUM\n\`\`\`"

# 7) Route-name integrity (stable & sorted)
say "route-name integrity …"
R_USED="$APP/storage/app/codex/route_used.txt"
R_ACT="$APP/storage/app/codex/route_actual.txt"
R_MISS="$APP/storage/app/codex/route_missing.txt"
grep -RhoP "route\(['\"][^'\"\)]+['\"]" app resources routes 2>/dev/null | sed -E "s/.*route\(['\"]([^'\"]+)['\"].*/\1/" | sort -u > "$R_USED" || true
$PHP_BIN -r '
require "vendor/autoload.php";
$app = require "bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$names=[];
foreach (Illuminate\Support\Facades\Route::getRoutes() as $r) { $n=$r->getName(); if($n){ $names[$n]=1; } }
ksort($names); foreach(array_keys($names) as $n){ echo $n,PHP_EOL; }
' > "$R_ACT" 2>>"$LOG" || true
comm -23 "$R_USED" "$R_ACT" > "$R_MISS" || true
MISS_COUNT=$(wc -l < "$R_MISS" | tr -d ' ')
if [ "${MISS_COUNT:-0}" -gt 0 ]; then
  SEV=$((SEV+1))
  add_md "## Missing Route Names ($MISS_COUNT)\nSee: storage/app/codex/route_missing.txt\n\nTop 25:\n\`\`\`\n$(head -n 25 "$R_MISS")\n\`\`\`"
  # Create suggested name patch hints (non-destructive)
  SUG="$APP/storage/app/codex/patches/route_names_suggest.md"
  : > "$SUG"
  while IFS= read -r n; do
    echo "### $n" >> "$SUG"
    grep -RIn "Route::" routes app | grep -Ei "$n|controller|action" | head -n 8 >> "$SUG" || true
    echo >> "$SUG"
  done < "$R_MISS"
fi

# 8) View/template presence
say "view/template audit …"
V_USED="$APP/storage/app/codex/views_used.txt"
V_MISS="$APP/storage/app/codex/views_missing.txt"
{ grep -RhoP "view\(['\"][^'\"\)]+['\"]" app routes 2>/dev/null; \
  grep -RhoP "@include(?:If|When)?\(['\"][^'\"\)]+['\"]" resources/views 2>/dev/null; } \
  | sed -E "s/.*(view|@include(?:If|When)?)\(['\"]([^'\"]+)['\"].*/\2/" \
  | sed -E 's/::class$//' | sort -u > "$V_USED" || true
: > "$V_MISS"
while IFS= read -r v; do
  f="resources/views/$(echo "$v" | tr '.' '/')"
  [ -f "${f}.blade.php" ] || echo "$v" >> "$V_MISS"
done < "$V_USED"
VM_COUNT=$(wc -l < "$V_MISS" | tr -d ' ')
if [ "${VM_COUNT:-0}" -gt 0 ]; then
  SEV=$((SEV+1))
  add_md "## Missing Blade Views ($VM_COUNT)\nSee: storage/app/codex/views_missing.txt\n\nTop 25:\n\`\`\`\n$(head -n 25 "$V_MISS")\n\`\`\`"
  # Generate stub files under storage/ (not in resources/)
  while IFS= read -r v; do
    dst="$APP/storage/app/codex/stubs/views/$(echo "$v" | tr '.' '/')"
    mkdir -p "$(dirname "$dst")"
    echo "<!-- stub for $v -->" > "${dst}.blade.php"
  done < "$V_MISS"
fi

# 9) Blade variable usage (quick heuristics)
say "blade var audit …"
BVAR="$APP/storage/app/codex/blade_vars.txt"; : > "$BVAR"
grep -RhoP "\{\{\s*\$[A-Za-z_][A-Za-z0-9_]*" resources/views 2>/dev/null \
 | sed -E 's/.*\{\{\s*\$([A-Za-z_][A-Za-z0-9_]*)/\1/' \
 | sort | uniq -c | sort -nr | head -n 40 > "$BVAR" || true
[ -s "$BVAR" ] && add_md "## Blade Variables (top 40)\n\`\`\`\n$(cat "$BVAR")\n\`\`\`"

# 10) Summary JSON
ISSUE_SUM=$SEV
printf '{%s,%s,%s,%s,%s}\n' \
  "$(kv status "\"FAIL\"")" \
  "$(kv parse_errors "$(wc -l < "$PARSE" 2>/dev/null || echo 0)")" \
  "$(kv missing_routes "$(wc -l < "$R_MISS" 2>/dev/null || echo 0)")" \
  "$(kv missing_views  "$(wc -l < "$V_MISS" 2>/dev/null || echo 0)")" \
  "$(kv generated "$(date +%s)")" \
  > "$JSON"

say "=== RESULT: FAIL (read-only report) ==="
add_md "\n---\n**Status:** FAIL (read-only). See logs:\n- $LOG\n- $REPORT\n- $JSON\n"
echo FAIL > "$APP/storage/app/codex/LAST_STATUS"
