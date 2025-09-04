#!/usr/bin/env bash
set -euo pipefail
APP="/var/www/swaeduae"; PHP_BIN="${PHP:-php}"; HOST="swaeduae.ae"
LOG="$APP/storage/logs/codex_pro_$(date +%Y%m%d-%H%M%S).log"
REPORT="$APP/storage/app/codex/REMEDIATION.md"
FAIL=0
warn(){ printf "[%s] %s\n" "$(date +%H:%M:%S)" "$*" | tee -a "$LOG"; }
info(){ printf "[%s] %s\n" "$(date +%H:%M:%S)" "$*" | tee -a "$LOG"; }
section(){ echo -e "\n## $1" >> "$REPORT"; }

cd "$APP"
echo "# Codex Pro Report ($(date '+%Y-%m-%d %H:%M:%S %z'))" > "$REPORT"
echo >> "$REPORT"
info "=== Codex Pro (read-only) ==="

# 0) Composer sanity (warn-only)
if command -v composer >/dev/null 2>&1; then
  info "composer validate/audit …"
  composer validate --no-check-all >>"$LOG" 2>&1 || warn "WARN: composer.json validate warnings"
  composer audit --no-dev --format plain >>"$LOG" 2>&1 || warn "WARN: composer audit reported advisories (see log)"
fi

# 1) Parse errors with context + heuristics suggestions
section "PHP Syntax"
info "php -l (syntax) …"
ERRFILE="$APP/storage/app/codex/parse_errors.txt"; : > "$ERRFILE"
# Collect parse errors (don’t stop on first)
find app routes database config -name '*.php' -print0 2>/dev/null \
 | xargs -0 -n1 -P4 php -l 2>&1 | grep -E "Parse error|Fatal error" || true | sed 's/\r$//' >> "$ERRFILE" || true
if [ -s "$ERRFILE" ]; then
  warn "ERROR: PHP parse/fatal errors"
  echo "_Parse/fatal errors detected. Fix these first for reliable analysis._" >> "$REPORT"
  while IFS= read -r line; do
    f="$(echo "$line" | sed -n 's/.* in \(.*\) on line \([0-9]\+\).*/\1/p')"
    l="$(echo "$line" | sed -n 's/.* on line \([0-9]\+\).*/\1/p')"
    echo -e "\n**$line**" >> "$REPORT"
    if [ -n "$f" ] && [ -f "$f" ]; then
      lo=$((l>5?l-5:1)); hi=$((l+5))
      echo '```php' >> "$REPORT"
      nl -ba "$f" | sed -n "${lo},${hi}p" >> "$REPORT"
      echo '```' >> "$REPORT"
      # Heuristic fix hints (no write)
      ctx="$(sed -n "${l}p" "$f" 2>/dev/null || true)"
      echo "**Suggested fix (heuristic):**" >> "$REPORT"
      if echo "$ctx" | grep -qE '^\s*public|protected|private'; then
        if echo "$ctx" | grep -qE '=\s*;'; then
          echo "- Property default looks empty. Try removing the trailing \`=\` (e.g. \`public string \$x;\`)." >> "$REPORT"
        else
          echo "- Check property syntax. Example valid forms: \`public string \$name;\`, \`public ?int \$age = null;\`" >> "$REPORT"
        fi
      else
        echo "- Verify parentheses/braces and trailing commas around this line." >> "$REPORT"
      fi
    fi
  done < "$ERRFILE"
  FAIL=1
else
  info "php -l: OK"
fi

# 2) Parallel Lint (fast lint across tree)
[ -x tools/parallel-lint.phar ] && { info "parallel-lint …"; php tools/parallel-lint.phar -j 4 app routes database config >>"$LOG" 2>&1 || warn "WARN: parallel-lint issues (see log)"; }

# 3) Static analysis (PHPStan if present, else Psalm PHAR)
ran_static=0
if [ -x vendor/bin/phpstan ]; then
  section "Static Analysis (PHPStan)"
  info "phpstan analyse …"
  if ! vendor/bin/phpstan analyse --no-progress --memory-limit=1G >>"$LOG" 2>&1; then
    warn "ERROR: PHPStan found issues"
    echo "- PHPStan found issues. See: _$(basename "$LOG")_" >> "$REPORT"
    # Pull a readable slice
    tail -n 200 "$LOG" | sed -n '1,200p' >> "$REPORT"
    FAIL=1
  else
    echo "- PHPStan: OK" >> "$REPORT"
  fi
  ran_static=1
fi
if [ "$ran_static" -eq 0 ] && [ -x tools/psalm.phar ]; then
  section "Static Analysis (Psalm)"
  info "psalm …"
  if ! php tools/psalm.phar --no-cache --output-format=compact >>"$LOG" 2>&1; then
    warn "ERROR: Psalm found issues"
    echo "- Psalm found issues. See: _$(basename "$LOG")_" >> "$REPORT"
    tail -n 200 "$LOG" | sed -n '1,200p' >> "$REPORT"
    FAIL=1
  else
    echo "- Psalm: OK" >> "$REPORT"
  fi
fi

# 4) Pint style (warn only)
if [ -x vendor/bin/pint ]; then
  section "Style (Pint)"
  info "pint --test …"
  if ! vendor/bin/pint --test >>"$LOG" 2>&1; then
    warn "WARN: Pint style differences"
    echo "- Formatting differences detected (no auto-fix)." >> "$REPORT"
  else
    echo "- Pint: OK" >> "$REPORT"
  fi
fi

# 5) Duplication (PHPCPD) – warn only with snippet
if [ -x tools/phpcpd.phar ]; then
  section "Duplication (PHPCPD)"
  info "phpcpd (phar) …"
  if ! php tools/phpcpd.phar app routes database >>"$LOG" 2>&1; then
    warn "WARN: Duplicated code detected"
    echo "- Duplicate blocks detected (see log for locations)." >> "$REPORT"
    # Summarize first hit if available
    grep -A3 -m1 '^  - ' "$LOG" || true >> "$REPORT"
  else
    echo "- PHPCPD: OK" >> "$REPORT"
  fi
fi

# 6) PHPMD (built-in rules) – warn only
if [ -x tools/phpmd.phar ]; then
  section "Code Smells (PHPMD)"
  info "phpmd …"
  if ! php tools/phpmd.phar app text cleancode,codesize,controversial,design,naming,unusedcode >>"$LOG" 2>&1; then
    warn "WARN: PHPMD smells found"
    echo "- Complexity/clean-code issues (see log)." >> "$REPORT"
  else
    echo "- PHPMD: OK" >> "$REPORT"
  fi
fi

# 7) Route-name integrity with suggestions
section "Routes"
info "route-name integrity …"
ROUTE_NAMES="$APP/storage/app/codex/route_names.txt"
USED_NAMES="$APP/storage/app/codex/route_used.txt"
MISSING="$APP/storage/app/codex/route_missing.txt"
$PHP_BIN -r '
require __DIR__."/vendor/autoload.php";
$app=require __DIR__."/bootstrap/app.php";
$kernel=$app->make(Illuminate\Contracts\Console\Kernel::class); $kernel->bootstrap();
$names=[]; foreach(app("router")->getRoutes() as $r){ if($n=$r->getName()){ $names[$n]=true; } }
ksort($names); foreach(array_keys($names) as $n){ echo $n,PHP_EOL; }' > "$ROUTE_NAMES" 2>>"$LOG" || true
tr -d "\r" < "$ROUTE_NAMES" | LC_ALL=C sort -u > "$ROUTE_NAMES.sorted"
grep -RhoP "route\(\s*['\"][A-Za-z0-9._-]+['\"]" app resources routes 2>/dev/null \
 | sed -E "s/.*route\(\s*['\"]([A-Za-z0-9._-]+)['\"].*/\1/" \
 | tr -d "\r" | LC_ALL=C sort -u > "$USED_NAMES.sorted"
LC_ALL=C sort -u -o "$ROUTE_NAMES.sorted" "$ROUTE_NAMES.sorted"
LC_ALL=C sort -u -o "$USED_NAMES.sorted" "$USED_NAMES.sorted"
comm -23 "$USED_NAMES.sorted" "$ROUTE_NAMES.sorted" > "$MISSING" || true
CNTM="$(wc -l < "$MISSING" | tr -d ' ')"
if [ "$CNTM" -gt 0 ]; then
  warn "ERROR: missing route names ($CNTM)"
  echo "- Missing route names: **$CNTM**" >> "$REPORT"
  echo "" >> "$REPORT"
  echo "### First 50 missing & how to fix" >> "$REPORT"
  c=0
  while IFS= read -r n; do
    echo "- \`$n\` → add \`->name('$n')\` on the route that serves this action" >> "$REPORT"
    c=$((c+1)); [ "$c" -ge 50 ] && break
  done < "$MISSING"
  echo "" >> "$REPORT"
  echo "_Full list: storage/app/codex/route_missing.txt_" >> "$REPORT"
  # Prepare a generic patch template (not applied)
  PATCH="$APP/storage/app/codex/patches/route_names_suggest.diff"
  {
    echo "--- a/routes/web.php"
    echo "+++ b/routes/web.php"
    echo "@@ // Add names to your routes (examples) @@"
    head -n 50 "$MISSING" | sed "s|^|+ // Example: Route::get('PATH', [Controller::class,'action'])->name('&');|"
  } > "$PATCH"
  echo "_Suggested patch template: storage/app/codex/patches/$(basename "$PATCH")_" >> "$REPORT"
  FAIL=1
else
  echo "- All referenced route names exist." >> "$REPORT"
fi

# 8) Blade compile (sanity)
section "Blade Compile"
$PHP_BIN artisan view:cache >>"$LOG" 2>&1 && echo "- Blade cached OK." >> "$REPORT" || { warn "WARN: Blade compile failed (see log)"; echo "- Blade compile failed (see log)." >> "$REPORT"; }
$PHP_BIN artisan view:clear >/dev/null 2>&1 || true

# 9) Secrets (warn only; never prints the secret values)
section "Secrets"
SECRETS="$APP/storage/app/codex/secret_hits.txt"
grep -RInE --exclude-dir=vendor --exclude-dir=storage --exclude-dir=.git \
  '(AKIA[0-9A-Z]{16})|(-----BEGIN (RSA|EC|DSA) PRIVATE KEY-----)|(xox[baprs]-[0-9A-Za-z-]{10,})' \
  app config .env* 2>/dev/null | sed 's/\(.*\):.*/\1/' | LC_ALL=C sort -u > "$SECRETS" || true
if [ -s "$SECRETS" ]; then
  warn "WARN: potential secrets in repo"
  echo "- Potential secrets in these files (inspect & rotate if needed):" >> "$REPORT"
  sed -n '1,50p' "$SECRETS" >> "$REPORT"
else
  echo "- No obvious secrets found." >> "$REPORT"
fi

# 10) i18n keys audit (+ stub json per locale)
section "Translations"
USED="$APP/storage/app/codex/i18n_used.txt"
grep -RhoP "(?:__|@lang|trans|Lang::get)\(\s*['\"][^'\"\)]+['\"]" app resources 2>/dev/null \
 | sed -E "s/.*\(\s*['\"]([^'\"\)]+)['\"].*/\1/" \
 | tr -d "\r" | LC_ALL=C sort -u > "$USED" || true
LC_ALL=C sort -u -o "$USED" "$USED"
I18N_DIRS=(); [ -d "$APP/lang" ] && I18N_DIRS+=("$APP/lang"); [ -d "$APP/resources/lang" ] && I18N_DIRS+=("$APP/resources/lang")
if [ "${#I18N_DIRS[@]}" -eq 0 ]; then
  echo "- No lang dirs found (lang/ or resources/lang/)." >> "$REPORT"
else
  for DIR in "${I18N_DIRS[@]}"; do
    for L in $(find "$DIR" -maxdepth 1 -type d -printf "%f\n" | grep -vE '^\.$' || true); do
      DEF="$APP/storage/app/codex/i18n_defined_$L.txt"; : > "$DEF"
      # PHP arrays
      find "$DIR/$L" -type f -name '*.php' -print0 2>/dev/null \
        | xargs -0 -I{} php -r '$a=include "{}"; if(is_array($a)){ $rii=new RecursiveIteratorIterator(new RecursiveArrayIterator($a)); $keys=[]; foreach($rii as $v){ $p=[]; for($d=0;$d<$rii->getDepth();$d++){$p[]=$rii->getSubIterator($d)->key();} $keys[]=implode(".",$p);} echo implode(PHP_EOL,array_unique(array_filter($keys))),PHP_EOL; }' 2>/dev/null \
        | tr -d "\r" | LC_ALL=C sort -u >> "$DEF" || true
      # Locale JSON
      [ -f "$DIR/$L.json" ] && php -r '$j=json_decode(file_get_contents("'$DIR'/'$L'.json"),true); if(is_array($j)) echo implode(PHP_EOL, array_keys($j));' \
        | tr -d "\r" | LC_ALL=C sort -u >> "$DEF" || true
      LC_ALL=C sort -u -o "$DEF" "$DEF"
      MISS="$APP/storage/app/codex/i18n_missing_$L.txt"
      LC_ALL=C comm -23 "$USED" "$DEF" > "$MISS" || true
      CNT=$(wc -l < "$MISS" | tr -d ' ')
      if [ "$CNT" -gt 0 ]; then
        echo "- $L: **$CNT** keys missing. Stub created at _storage/app/codex/i18n_stub_$L.json_" >> "$REPORT"
        { echo "{"; awk '{printf "  \"%s\": \"\",\n", $0}' "$MISS" | sed '$ s/,\s*$//'; echo "}"; } > "$APP/storage/app/codex/i18n_stub_$L.json"
      else
        echo "- $L: OK" >> "$REPORT"
      fi
    done
  done
fi

# 11) View/template existence + stubs
section "Views"
VIEW_USED="$APP/storage/app/codex/views_used.txt"
grep -RhoP "(?:view|View::make|return\s+view)\(\s*['\"][A-Za-z0-9_.:-]+['\"]" app resources 2>/dev/null | sed -E "s/.*\(\s*['\"]([A-Za-z0-9_.:-]+)['\"].*/\1/" > "$VIEW_USED" || true
grep -RhoP "@(?:include|extends|component|each|includeIf|includeWhen)\(\s*['\"][A-Za-z0-9_.:-]+['\"]" resources 2>/dev/null | sed -E "s/.*\(\s*['\"]([A-Za-z0-9_.:-]+)['\"].*/\1/" >> "$VIEW_USED" || true
tr -d "\r" < "$VIEW_USED" | LC_ALL=C sort -u > "$VIEW_USED.sorted"
LC_ALL=C sort -u -o "$VIEW_USED.sorted" "$VIEW_USED.sorted"
MISSING_VIEWS="$APP/storage/app/codex/views_missing.txt"; : > "$MISSING_VIEWS"
while IFS= read -r V; do
  P="${V//./\/}"; FOUND=0
  for ext in blade.php php; do [ -f "$APP/resources/views/$P.$ext" ] && FOUND=1 && break; done
  [ $FOUND -eq 0 ] && echo "$V" >> "$MISSING_VIEWS"
done < "$VIEW_USED.sorted"
CNTV="$(wc -l < "$MISSING_VIEWS" | tr -d ' ')"
if [ "$CNTV" -gt 0 ]; then
  echo "- Missing views: **$CNTV** (see _storage/app/codex/views_missing.txt_)." >> "$REPORT"
  # create stubs (not applied to views/, just suggestions)
  while IFS= read -r V; do
    P="${V//./\/}"
    STUB="$APP/storage/app/codex/stubs/views/$P.blade.php"
    mkdir -p "$(dirname "$STUB")"
    cat > "$STUB" <<EOF
{{-- Stub for $V --}}
@extends('layouts.app')
@section('title', '$V')
@section('content')
  <div class="container py-4">
    <h1>$V</h1>
    <p>TODO: implement view.</p>
  </div>
@endsection
EOF
  done < "$MISSING_VIEWS"
  echo "- Blade stubs generated under _storage/app/codex/stubs/views/…_ (copy them into resources/views/ when ready)." >> "$REPORT"
  FAIL=1
else
  echo "- All referenced views exist." >> "$REPORT"
fi

# 12) OpenAPI probe (best effort)
section "OpenAPI"
SPEC=""
for c in openapi.yaml openapi.yml openapi.json docs/openapi.yaml docs/openapi.yml docs/openapi.json public/openapi.json storage/app/openapi.yaml storage/app/openapi.yml storage/app/openapi.json; do
  [ -f "$APP/$c" ] && SPEC="$APP/$c" && break
done
if [ -n "$SPEC" ]; then
  PATHS="$APP/storage/app/codex/openapi_paths.txt"; : > "$PATHS"
  case "$SPEC" in
    *.json) php -r '$j=json_decode(file_get_contents("'$SPEC'"),true); if(isset($j["paths"])) foreach(array_keys($j["paths"]) as $p) echo $p,PHP_EOL;' | head -n 30 > "$PATHS" ;;
    *.yml|*.yaml) awk 'BEGIN{inpaths=0}/^paths:/{inpaths=1;next}inpaths&&/^[[:space:]]*\/[^:]+:/{gsub(":",""); sub(/^[[:space:]]*/,""); print}inpaths&&/^[^[:space:]]/{inpaths=0}' "$SPEC" | head -n 30 > "$PATHS" ;;
  esac
  if [ -s "$PATHS" ]; then
    echo "- Probed up to 30 paths from spec \`$(basename "$SPEC")\`." >> "$REPORT"
  else
    echo "- Spec found, but no paths extracted." >> "$REPORT"
  fi
else
  echo "- No spec file detected." >> "$REPORT"
fi

# 13) HTTP smoke for key routes (codes/TTFB/total)
section "HTTP Smoke"
for u in / /contact /about-us /api/v1/health; do
  read -r code size ttfb total <<<"$(curl -s -H "Host: $HOST" -o /dev/null -w "%{http_code} %{size_download} %{time_starttransfer} %{time_total}" "http://127.0.0.1$u" || echo "000 0 0 0")"
  printf -- "- \`%s\`: code=%s size=%sB ttfb=%ss total=%ss\n" "$u" "$code" "$size" "$ttfb" "$total" >> "$REPORT"
done

# 14) Summarize health (token-protected)
tok="$(sed -n 's/^AGENT_TOKEN=\(.*\)$/\1/p' "$APP/.env" | tr -d '\r\n')"
echo "- /healthz-agent -> $(curl -s -o /dev/null -w "%{http_code}" -H "Host: $HOST" -H "X-Agent-Token: $tok" http://127.0.0.1/healthz-agent)" >> "$REPORT"
echo "- /api/agent/ping -> $(curl -s -o /dev/null -w "%{http_code}" -H "Host: $HOST" -H "X-Agent-Token: $tok" http://127.0.0.1/api/agent/ping)" >> "$REPORT"

# 15) Result
if [ "$FAIL" -ne 0 ]; then
  warn "=== RESULT: FAIL (see $(basename "$LOG")) ==="
  echo -e "\n**Result:** FAIL  " >> "$REPORT"
  echo "FAIL" > "$APP/storage/app/codex/LAST_STATUS"
else
  info "=== RESULT: OK ==="
  echo -e "\n**Result:** OK  " >> "$REPORT"
  echo "OK" > "$APP/storage/app/codex/LAST_STATUS"
fi
echo -e "\n_Log: storage/logs/$(basename "$LOG")_" >> "$REPORT"
