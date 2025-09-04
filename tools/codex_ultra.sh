#!/usr/bin/env bash
set -euo pipefail
APP="/var/www/swaeduae"; PHP_BIN="${PHP:-php}"
LOG="$APP/storage/logs/codex_ultra_$(date +%Y%m%d-%H%M%S).log"
REPORT="$APP/storage/app/codex/REMEDIATION.md"
JSON="$APP/storage/app/codex/summary.json"
STATUS_FILE_TXT="$APP/public/_codex/status.txt"
STATUS_FILE_JSON="$APP/public/_codex/status.json"

# knobs (env-driven)
CHANGED_ONLY="${CODEX_CHANGED:-0}"            # 1 to scan only changed files
BASE_REF="${CODEX_BASE_REF:-HEAD~1}"          # base for changed files (or a branch like origin/main)
BADGE="${CODEX_BADGE:-1}"                     # 1 to write public/_codex badge files
NOTIFY_URL="${CODEX_NOTIFY_URL:-}"            # optional webhook to ping with status
PING_HEALTH="${CODEX_PING_HEALTHZ:-0}"        # 1 to ping local healthz with token
HOST="${CODEX_HOST:-swaeduae.ae}"             # host header for local probes

say(){ printf "[%s] %s\n" "$(date +%H:%M:%S)" "$*" | tee -a "$LOG"; }
add_md(){ echo -e "$*" >> "$REPORT"; }
kv(){ printf '"%s":%s' "$1" "$2"; }
cjson(){ jq -Mc . 2>/dev/null || cat; }

cd "$APP"
: > "$LOG"
echo "# Codex Ultra Report ($(date '+%Y-%m-%d %H:%M:%S %z'))" > "$REPORT"
add_md ""

# ------------------------------------------------------------------------------
# 0) Build FILESET (Changed-files mode)
# ------------------------------------------------------------------------------
FILES_TXT="$APP/storage/app/codex/files.txt"; : > "$FILES_TXT"
ADD_FILE(){ f="$1"; [ -f "$f" ] && echo "$f" >> "$FILES_TXT"; }

if [ "$CHANGED_ONLY" = "1" ] && [ -d .git ]; then
  say "Changed-files mode from ${BASE_REF} …"
  # staged + unstaged + against base
  git diff --name-only --diff-filter=ACMRTUXB "${BASE_REF}"..HEAD || true
  {
    git diff --name-only --diff-filter=ACMRTUXB "${BASE_REF}"..HEAD
    git diff --name-only --diff-filter=ACMRTUXB
    git diff --name-only --cached --diff-filter=ACMRTUXB
  } | grep -E '^(app|routes|database|config|resources/views)/.*\.(php|blade\.php)$' | sort -u > "$FILES_TXT" || true
  if [ ! -s "$FILES_TXT" ]; then
    say "No changed files detected; falling back to full set."
    CHANGED_ONLY=0
  fi
fi

if [ "$CHANGED_ONLY" = "0" ]; then
  say "Full-file scan set …"
  find app routes database config -type f -name '*.php' -print | sort -u > "$FILES_TXT" || true
  find resources/views -type f -name '*.blade.php' -print | sort -u >> "$FILES_TXT" || true
fi

# ------------------------------------------------------------------------------
# 1) Parse / Syntax (fail-gating)
# ------------------------------------------------------------------------------
say "php -l (per-file)…"
PARSE="$APP/storage/app/codex/parse_errors.txt"; : > "$PARSE"
# run per-file to capture each error; xargs -n1 to isolate failing file exit codes
if ! cat "$FILES_TXT" | grep -E '\.php$' | xargs -r -n1 -P4 php -l >>"$LOG" 2>>"$PARSE"; then true; fi

PARSE_COUNT=$(wc -l < "$PARSE" 2>/dev/null | tr -d ' ' || echo 0)
if [ "${PARSE_COUNT:-0}" -gt 0 ]; then
  add_md "## Parse/Fatal Errors ($PARSE_COUNT)\nFix these first. Snippets:"
  while IFS= read -r line; do
    f="$(echo "$line" | sed -n 's/.* in \(.*\) on line \([0-9]\+\).*/\1/p')"
    l="$(echo "$line" | sed -n 's/.* on line \([0-9]\+\).*/\1/p')"
    [ -n "$f" ] && [ -f "$f" ] && [ -n "${l:-}" ] || continue
    lo=$((l>5?l-5:1)); hi=$((l+5))
    add_md "\n**$line**\n\`\`\`php"
    nl -ba "$f" | sed -n "${lo},${hi}p" >> "$REPORT"
    add_md "\`\`\`"
  done < <(sed 's/\r$//' "$PARSE")
fi

# ------------------------------------------------------------------------------
# 2) Static analyzers (warn-only for gating)
# ------------------------------------------------------------------------------
# parallel-lint
if [ -x tools/parallel-lint.phar ]; then
  say "parallel-lint …"
  if [ "$CHANGED_ONLY" = "1" ]; then
    php tools/parallel-lint.phar --colors $(grep -E '\.php$' "$FILES_TXT" | xargs) >>"$LOG" 2>&1 || add_md "- WARN: parallel-lint issues (see log)"
  else
    php tools/parallel-lint.phar -j 4 --colors app routes database config >>"$LOG" 2>&1 || add_md "- WARN: parallel-lint issues (see log)"
  fi
fi

# phpstan
if [ -x vendor/bin/phpstan ]; then
  say "phpstan analyse …"
  if [ "$CHANGED_ONLY" = "1" ]; then
    vendor/bin/phpstan analyse --no-progress --memory-limit=1G $(grep -E '\.php$' "$FILES_TXT" | xargs) >>"$LOG" 2>&1 || add_md "## PHPStan\n- WARN: issues found (see log)"
  else
    vendor/bin/phpstan analyse --no-progress --memory-limit=1G >>"$LOG" 2>&1 || add_md "## PHPStan\n- WARN: issues found (see log)"
  fi
else
  add_md "## PHPStan\n- SKIP: vendor/bin/phpstan not found"
fi

# psalm
if [ -x tools/psalm.phar ]; then
  say "psalm …"
  php tools/psalm.phar --no-cache --config=tools/config/psalm.xml --output-format=compact >>"$LOG" 2>&1 || add_md "## Psalm\n- WARN: issues found (see log)"
else
  add_md "## Psalm\n- SKIP: psalm.phar not present"
fi

# phpmd smells
if [ -x tools/phpmd.phar ]; then
  say "phpmd …"
  php tools/phpmd.phar app text tools/config/phpmd.xml >>"$LOG" 2>&1 || add_md "## PHPMD\n- WARN: smells found (see log)"
else
  add_md "## PHPMD\n- SKIP: phpmd.phar not present"
fi

# duplication
if [ -x vendor/bin/phpcpd ]; then
  say "phpcpd (composer) …"
  vendor/bin/phpcpd app routes database >>"$LOG" 2>&1 || true
elif [ -x tools/phpcpd.phar ]; then
  say "phpcpd (phar) …"
  php tools/phpcpd.phar app routes database >>"$LOG" 2>&1 || true
fi
DUP_SUM=$(grep -A2 -E '^Found [0-9]+ clones' "$LOG" || true)
[ -n "$DUP_SUM" ] && add_md "## Duplication (PHPCPD)\n\`\`\`\n$DUP_SUM\n\`\`\`"

# ------------------------------------------------------------------------------
# 3) Route-name integrity + “unused named routes”
# ------------------------------------------------------------------------------
say "route-name integrity …"
R_USED="$APP/storage/app/codex/route_used.txt"
R_ACT="$APP/storage/app/codex/route_actual.txt"
R_MISS="$APP/storage/app/codex/route_missing.txt"
R_UNUSED="$APP/storage/app/codex/route_unused.txt"

LC_ALL=C grep -RhoP "route\(['\"][^'\"\)]+['\"]" app resources routes 2>/dev/null \
 | sed -E "s/.*route\(['\"]([^'\"]+)['\"].*/\1/" \
 | LC_ALL=C sort -u > "$R_USED" || true

$PHP_BIN -r '
require "vendor/autoload.php";
$app = require "bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$names=[];
foreach (Illuminate\Support\Facades\Route::getRoutes() as $r) { $n=$r->getName(); if($n){ $names[$n]=1; } }
ksort($names,SORT_STRING); foreach(array_keys($names) as $n){ echo $n,PHP_EOL; }
' > "$R_ACT" 2>>"$LOG" || true

LC_ALL=C comm -23 "$R_USED" "$R_ACT" > "$R_MISS" || true
LC_ALL=C comm -13 "$R_USED" "$R_ACT" > "$R_UNUSED" || true

MISS_COUNT=$(wc -l < "$R_MISS" | tr -d ' ' || echo 0)
UNUSED_COUNT=$(wc -l < "$R_UNUSED" | tr -d ' ' || echo 0)

if [ "${MISS_COUNT:-0}" -gt 0 ]; then
  add_md "## Missing Route Names ($MISS_COUNT)\nSee: storage/app/codex/route_missing.txt\n\nTop 25:\n\`\`\`\n$(head -n 25 "$R_MISS")\n\`\`\`"
fi
if [ "${UNUSED_COUNT:-0}" -gt 0 ]; then
  add_md "## Unused Named Routes ($UNUSED_COUNT)\n(Defined but not referenced via route())\nTop 25:\n\`\`\`\n$(head -n 25 "$R_UNUSED")\n\`\`\`"
fi

# ------------------------------------------------------------------------------
# 4) Dead controller/action finder (missing classes/methods)
# ------------------------------------------------------------------------------
say "dead controller/action audit …"
CTRL_MISS="$APP/storage/app/codex/controllers_missing.txt"
: > "$CTRL_MISS"
$PHP_BIN <<'PHP' >>"$CTRL_MISS" 2>>"$LOG"
<?php
require "vendor/autoload.php";
$app = require "bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\Route;

foreach (Route::getRoutes() as $route) {
    $action = $route->getAction();
    if (isset($action['controller']) && is_string($action['controller'])) {
        $uses = $action['controller'];             // "Class@method"
        if (strpos($uses, '@') === false) {        // e.g., invokable "Class"
            $class = $uses; $method = '__invoke';
        } else {
            [$class, $method] = explode('@', $uses, 2);
        }
        if (!class_exists($class)) {
            echo "MISSING_CLASS|{$route->uri()}|{$route->getName()}|$class\n";
            continue;
        }
        if (!method_exists($class, $method)) {
            echo "MISSING_METHOD|{$route->uri()}|{$route->getName()}|$class@$method\n";
        }
    }
}
PHP

CTRL_MISS_COUNT=$(wc -l < "$CTRL_MISS" | tr -d ' ' || echo 0)
if [ "${CTRL_MISS_COUNT:-0}" -gt 0 ]; then
  add_md "## Missing Controllers/Actions ($CTRL_MISS_COUNT)\nSee: storage/app/codex/controllers_missing.txt\n\nTop 25:\n\`\`\`\n$(head -n 25 "$CTRL_MISS")\n\`\`\`"
fi

# ------------------------------------------------------------------------------
# 5) Blade view presence (PHP-based, robust) + stubs (storage only)
# ------------------------------------------------------------------------------
say "view/template audit …"
V_USED="$APP/storage/app/codex/views_used.txt"
V_MISS="$APP/storage/app/codex/views_missing.txt"
: > "$V_USED"; : > "$V_MISS"

$PHP_BIN <<'PHP' >"$V_USED" 2>>"$LOG"
<?php
function scan_files($dirs, $pattern) {
  $out=[];
  foreach ($dirs as $d) {
    if (!is_dir($d)) continue;
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($d, FilesystemIterator::SKIP_DOTS));
    foreach ($it as $f) {
      $path = $f->getPathname();
      if (!preg_match($pattern, $path)) continue;
      $src = @file_get_contents($path);
      if ($src === false) continue;
      // in PHP files: view('name')
      if (substr($path,-4)==='.php') {
        if (preg_match_all('/view\(\s*[\'"]([^\'"]+)[\'"]\s*[,)]/m', $src, $m)) {
          foreach ($m[1] as $v) { $out[$v]=1; }
        }
      }
      // in blade: @include, @extends, @component, @includeIf/When
      if (substr($path,-10)==='.blade.php') {
        if (preg_match_all('/@(?:include|includeIf|includeWhen|extends|component)\(\s*[\'"]([^\'"]+)[\'"]/m', $src, $m)) {
          foreach ($m[1] as $v) { $out[$v]=1; }
        }
      }
    }
  }
  ksort($out, SORT_STRING);
  foreach(array_keys($out) as $k){ echo $k,PHP_EOL; }
}
scan_files(['app','routes','resources/views'], '/\.(php|blade\.php)$/');
PHP

while IFS= read -r v; do
  [ -z "$v" ] && continue
  f="resources/views/$(echo "$v" | tr '.' '/')"
  [ -f "${f}.blade.php" ] || echo "$v" >> "$V_MISS"
done < "$V_USED"

VM_COUNT=$(wc -l < "$V_MISS" | tr -d ' ' || echo 0)
if [ "${VM_COUNT:-0}" -gt 0 ]; then
  add_md "## Missing Blade Views ($VM_COUNT)\nSee: storage/app/codex/views_missing.txt\n\nTop 25:\n\`\`\`\n$(head -n 25 "$V_MISS")\n\`\`\`"
  while IFS= read -r v; do
    dst="$APP/storage/app/codex/stubs/views/$(echo "$v" | tr '.' '/')"
    mkdir -p "$(dirname "$dst")"
    echo "<!-- stub for $v -->" > "${dst}.blade.php"
  done < "$V_MISS"
fi

# ------------------------------------------------------------------------------
# 6) Compose summary + severity gating (parse + missing routes + missing views)
# ------------------------------------------------------------------------------
MISS_COUNT=${MISS_COUNT:-0}
UNUSED_COUNT=${UNUSED_COUNT:-0}
CTRL_MISS_COUNT=${CTRL_MISS_COUNT:-0}
VM_COUNT=${VM_COUNT:-0}

STATUS="OK"
if [ "${PARSE_COUNT:-0}" -gt 0 ] || [ "${MISS_COUNT:-0}" -gt 0 ] || [ "${VM_COUNT:-0}" -gt 0 ] || [ "${CTRL_MISS_COUNT:-0}" -gt 0 ]; then
  STATUS="FAIL"
fi

printf '{%s,%s,%s,%s,%s,%s,%s,%s}\n' \
  "$(kv status "\"$STATUS\"")" \
  "$(kv parse_errors "${PARSE_COUNT:-0}")" \
  "$(kv missing_routes "${MISS_COUNT:-0}")" \
  "$(kv unused_routes "${UNUSED_COUNT:-0}")" \
  "$(kv missing_views  "${VM_COUNT:-0}")" \
  "$(kv missing_actions "${CTRL_MISS_COUNT:-0}")" \
  "$(kv changed_mode  "\"${CHANGED_ONLY}\"")" \
  "$(kv generated "$(date +%s)")" \
  | cjson > "$JSON"

add_md "\n---\n**Status:** $STATUS (read-only). See logs:\n- $LOG\n- $REPORT\n- $JSON\n"
echo "$STATUS" > "$APP/storage/app/codex/LAST_STATUS"

# ------------------------------------------------------------------------------
# 7) Optional notifications (badge + webhook + health ping)
# ------------------------------------------------------------------------------
if [ "$BADGE" = "1" ]; then
  echo "$STATUS" > "$STATUS_FILE_TXT"
  cp -a "$JSON" "$STATUS_FILE_JSON"
fi

if [ -n "$NOTIFY_URL" ]; then
  curl -fsS -m 4 -X POST -H "Content-Type: application/json" \
    -d @"$JSON" "$NOTIFY_URL" >>"$LOG" 2>&1 || say "notify: failed"
fi

if [ "$PING_HEALTH" = "1" ]; then
  TOK="$(sed -n 's/^AGENT_TOKEN=\(.*\)$/\1/p' "$APP/.env" | tr -d '\r\n')"
  curl -fsS -m 4 -H "Host: ${HOST}" -H "X-Agent-Token: ${TOK}" \
    "http://127.0.0.1/api/agent/ping" >>"$LOG" 2>&1 || say "health ping: failed"
fi

# Exit policy (gated by severity)
[ "$STATUS" = "OK" ] || exit 1
