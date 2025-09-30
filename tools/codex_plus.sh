#!/usr/bin/env bash
PHP_BIN=${PHP_BIN:-php}
set -euo pipefail
APP="/var/www/swaeduae"; PHP_BIN="${PHP:-php}"; HOST="swaeduae.ae"
LOG="$APP/storage/logs/codex_plus_$(date +%Y%m%d-%H%M%S).log"
REPORT="$APP/storage/app/codex/REMEDIATION.md"
FAIL=0
warn(){ printf "[%s] %s\n" "$(date +%H:%M:%S)" "$*" | tee -a "$LOG"; }
info(){ printf "[%s] %s\n" "$(date +%H:%M:%S)" "$*" | tee -a "$LOG"; }

cd "$APP"
echo "# Codex Plus Report ($(date '+%Y-%m-%d %H:%M:%S %z'))" > "$REPORT"
echo >> "$REPORT"
info "=== Codex Plus (read-only) ==="

# 0) PHP parse lint
info "php -l (syntax)…"
if ! find app routes database config -name '*.php' -print0 2>/dev/null | xargs -0 -n1 -P4 php -l >/dev/null; then
  warn "ERROR: PHP syntax errors (see above file paths if any)"
  echo "## PHP Syntax Errors" >> "$REPORT"
  echo "- One or more files failed \`php -l\`. Fix parse errors first." >> "$REPORT"
  # keep going to collect other signals
else
  info "php -l: OK"
fi

# 1) Parallel Lint (faster coverage, if available)
if [ -x tools/parallel-lint.phar ]; then
  info "parallel-lint …"
  if ! php tools/parallel-lint.phar -j 4 app routes database config >>"$LOG" 2>&1; then
    warn "WARN: parallel-lint reported issues (see log)"; echo "- Parallel-Lint flagged syntax issues." >> "$REPORT"
  fi
fi

# 2) Static analysis: PHPStan (vendor) or Psalm (phar)
ran_static=0
if [ -x vendor/bin/phpstan ]; then
  info "phpstan analyse …"
  if ! vendor/bin/phpstan analyse --no-progress --memory-limit=1G >>"$LOG" 2>&1; then
    warn "ERROR: PHPStan found issues"; echo "## Static Analysis (PHPStan)\n- PHPStan found issues. See log: \`$(basename "$LOG")\`." >> "$REPORT"; FAIL=1
  else info "phpstan: OK"; fi
  ran_static=1
fi
if [ "$ran_static" -eq 0 ] && [ -x tools/psalm.phar ]; then
  info "psalm (no cache)…"
  if ! php tools/psalm.phar --config=tools/config/psalm.xml --no-cache --output-format=compact >>"$LOG" 2>&1; then
    warn "ERROR: Psalm found issues"; echo "## Static Analysis (Psalm)\n- Psalm found issues. See log: \`$(basename "$LOG")\`." >> "$REPORT"; FAIL=1
  else info "psalm: OK"; fi
fi

# 3) Pint style (warn only)
if [ -x vendor/bin/pint ]; then
  info "pint --test …"
  if ! vendor/bin/pint --test >>"$LOG" 2>&1; then
    warn "WARN: Pint style differences"; echo "## Style (Pint)\n- Formatting differences found (no auto-fix)." >> "$REPORT"
  else info "pint: OK"; fi
fi

# 4) Duplication (PHPCPD) – warn only
if [ -x tools/phpcpd.phar ]; then
  info "phpcpd (phar) …"
  if ! php tools/phpcpd.phar app routes database >>"$LOG" 2>&1; then
    warn "WARN: Duplicated code detected"; echo "## Duplication (PHPCPD)\n- Duplicate blocks detected. Consider refactoring." >> "$REPORT"
  else info "phpcpd: OK"; fi
elif [ -x vendor/bin/phpcpd ]; then
  info "phpcpd (vendor) …"
  if ! vendor/bin/phpcpd app routes database >>"$LOG" 2>&1; then
    warn "WARN: Duplicated code detected"; echo "## Duplication (PHPCPD)\n- Duplicate blocks detected." >> "$REPORT"
  fi
fi

# 5) PHPMD (code smells) – warn only
if [ -x tools/phpmd.phar ]; then
  info "phpmd …"
  if ! php tools/phpmd.phar app text tools/config/phpmd.xml >>"$LOG" 2>&1; then
    warn "WARN: PHPMD smells found"; echo "## Code Smells (PHPMD)\n- PHPMD reported complexity/clean-code issues." >> "$REPORT"
  else info "phpmd: OK"; fi
fi

# 6) Route-name integrity (robust sort)
info "route-name integrity …"
ROUTE_NAMES="$APP/storage/app/codex/route_names.txt"
USED_NAMES="$APP/storage/app/codex/route_used.txt"
MISSING="$APP/storage/app/codex/route_missing.txt"
$PHP_BIN -r '
require __DIR__."/vendor/autoload.php";
$app=require __DIR__."/bootstrap/app.php";
$kernel=$app->make(Illuminate\Contracts\Console\Kernel::class); $kernel->bootstrap();
$names=[]; foreach (app("router")->getRoutes() as $r){ if($n=$r->getName()){ $names[$n]=true; } }
ksort($names); foreach(array_keys($names) as $n){ echo $n,PHP_EOL; }' > "$ROUTE_NAMES" 2>>"$LOG" || true
tr -d "\r" < "$ROUTE_NAMES" | LC_ALL=C sort -u > "$ROUTE_NAMES.sorted"
grep -RhoP "route\(\s*['\"][A-Za-z0-9._-]+['\"]" app resources routes 2>/dev/null \
 | sed -E "s/.*route\(\s*['\"]([A-Za-z0-9._-]+)['\"].*/\1/" \
 | tr -d "\r" | LC_ALL=C sort -u > "$USED_NAMES.sorted"
comm -23 "$USED_NAMES.sorted" "$ROUTE_NAMES.sorted" > "$MISSING" || true
if [ -s "$MISSING" ]; then
  CNT=$(wc -l < "$MISSING" | tr -d ' ')
  warn "ERROR: missing route names ($CNT)"
  echo "## Missing Route Names ($CNT)" >> "$REPORT"
  sed -n '1,200p' "$MISSING" >> "$REPORT"
  echo >> "$REPORT"; echo "> **Solution idea:** add \`->name('…')\` to the corresponding routes, or stop calling \`route('…')\` until names exist." >> "$REPORT"
  FAIL=1
else info "routes: all referenced names exist"; fi

# 7) Blade compile sanity (warn only)
info "blade compile (view:cache) …"
if ! $PHP_BIN artisan view:cache >>"$LOG" 2>&1; then
  warn "WARN: Blade compile errors (see log)"; echo "## Blade Compile\n- View compile failed. Check Blade syntax or missing variables." >> "$REPORT"
fi
$PHP_BIN artisan view:clear >/dev/null 2>&1 || true

# 8) Secrets grep (warn only; does NOT print secrets)
info "secret scan …"
SECRETS="$APP/storage/app/codex/secret_hits.txt"
grep -RInE --exclude-dir=vendor --exclude-dir=storage --exclude-dir=.git \
  '(AKIA[0-9A-Z]{16})|(-----BEGIN (RSA|EC|DSA) PRIVATE KEY-----)|(xox[baprs]-[0-9A-Za-z-]{10,})' \
  app config .env* 2>/dev/null | sed 's/\(.*\):.*/\1/' | sort -u > "$SECRETS" || true
if [ -s "$SECRETS" ]; then
  warn "WARN: potential secrets in repo"; echo "## Secrets\n- Potential secrets matched in:" >> "$REPORT"; sed -n '1,50p' "$SECRETS" >> "$REPORT"
fi

# 9) Translation keys audit
info "i18n keys audit …"
I18N_DIRS=()
[ -d "$APP/lang" ] && I18N_DIRS+=("$APP/lang")
[ -d "$APP/resources/lang" ] && I18N_DIRS+=("$APP/resources/lang")
USED="$APP/storage/app/codex/i18n_used.txt"
grep -RhoP "(?:__|@lang|trans|Lang::get)\(\s*['\"][^'\"\)]+['\"]" app resources 2>/dev/null \
 | sed -E "s/.*\(\s*['\"]([^'\"\)]+)['\"].*/\1/" \
 | tr -d "\r" | LC_ALL=C sort -u > "$USED" || true
> "$APP/storage/app/codex/i18n_summary.txt"
if [ "${#I18N_DIRS[@]}" -eq 0 ]; then
  warn "WARN: no lang dirs found (lang/ or resources/lang/)"
  echo "## Translations\n- No \`lang/\` or \`resources/lang/\` directory detected." >> "$REPORT"
else
  for DIR in "${I18N_DIRS[@]}"; do
    for L in $(find "$DIR" -maxdepth 1 -type d -printf "%f\n" | grep -vE '^\.$' || true); do
      DEF="$APP/storage/app/codex/i18n_defined_$L.txt"
      # PHP array files
      find "$DIR/$L" -type f -name '*.php' -print0 2>/dev/null | xargs -0 -I{} php -r '
        $a=include "{}"; if(is_array($a)){ $rii=new RecursiveIteratorIterator(new RecursiveArrayIterator($a));
          $keys=[]; foreach($rii as $v){ $path=[]; for($d=0;$d<$rii->getDepth();$d++){$path[]=$rii->getSubIterator($d)->key();}
            $keys[]=implode(".", $path);
          } echo implode(PHP_EOL,array_filter(array_unique($keys))),PHP_EOL;
        }' 2>/dev/null | tr -d "\r" | LC_ALL=C sort -u > "$DEF" || true
      # Locale JSON (e.g., en.json)
      [ -f "$DIR/$L.json" ] && php -r '
        $j=json_decode(file_get_contents("'$DIR'/'$L'.json"),true); if(is_array($j)){ echo implode(PHP_EOL, array_keys($j)); }' \
        | tr -d "\r" | LC_ALL=C sort -u >> "$DEF" || true
      MISS="$APP/storage/app/codex/i18n_missing_$L.txt"
      if [ -s "$USED" ]; then
        LC_ALL=C comm -23 "$USED" "$DEF" > "$MISS" || true
      else
        : > "$MISS"
      fi
      CNT=$(wc -l < "$MISS" 2>/dev/null | tr -d ' ')
      echo "$L: $CNT missing" >> "$APP/storage/app/codex/i18n_summary.txt"
      if [ "$CNT" -gt 0 ]; then
        echo "## Missing i18n Keys ($L: $CNT)" >> "$REPORT"
        sed -n '1,100p' "$MISS" >> "$REPORT"
        echo >> "$REPORT"
        # Helpful stub JSON for translators
        {
          echo "{"
          awk '{printf "  \"%s\": \"\",\n", $0}' "$MISS" | sed '$ s/,\s*$//'
          echo "}"
        } > "$APP/storage/app/codex/i18n_stub_$L.json"
        echo "_Stub JSON: storage/app/codex/i18n_stub_$L.json_" >> "$REPORT"
        echo >> "$REPORT"
      fi
    done
  done
  echo "### i18n Summary" >> "$REPORT"
  cat "$APP/storage/app/codex/i18n_summary.txt" >> "$REPORT"
fi

# 10) View existence audit
info "view/template audit …"
VIEW_USED="$APP/storage/app/codex/views_used.txt"
grep -RhoP "(?:view|View::make|return\s+view)\(\s*['\"][A-Za-z0-9_.:-]+['\"]" app resources 2>/dev/null \
 | sed -E "s/.*\(\s*['\"]([A-Za-z0-9_.:-]+)['\"].*/\1/" > "$VIEW_USED" || true
grep -RhoP "@(?:include|extends|component|each|includeIf|includeWhen)\(\s*['\"][A-Za-z0-9_.:-]+['\"]" resources 2>/dev/null \
 | sed -E "s/.*\(\s*['\"]([A-Za-z0-9_.:-]+)['\"].*/\1/" >> "$VIEW_USED" || true
tr -d "\r" < "$VIEW_USED" | LC_ALL=C sort -u > "$VIEW_USED.sorted"
MISSING_VIEWS="$APP/storage/app/codex/views_missing.txt"
: > "$MISSING_VIEWS"
while IFS= read -r V; do
  P="${V//./\/}"
  FOUND=0
  for ext in blade.php php; do
    [ -f "$APP/resources/views/$P.$ext" ] && FOUND=1 && break
  done
  [ $FOUND -eq 0 ] && echo "$V" >> "$MISSING_VIEWS"
done < "$VIEW_USED.sorted"
if [ -s "$MISSING_VIEWS" ]; then
  CNT=$(wc -l < "$MISSING_VIEWS" | tr -d ' ')
  warn "ERROR: missing views ($CNT)"
  echo "## Missing Views ($CNT)" >> "$REPORT"
  sed -n '1,200p' "$MISSING_VIEWS" >> "$REPORT"
  echo >> "$REPORT"; echo "> **Solution idea:** create the view file under \`resources/views/\` (e.g., \`$(head -n1 "$MISSING_VIEWS" | sed "s/\./\//g").blade.php\`) or update the reference." >> "$REPORT"
  FAIL=1
else
  info "views: all referenced templates exist"
fi

# 11) OpenAPI contract (if any spec found)
info "openapi scan …"
SPEC=""
for c in openapi.yaml openapi.yml openapi.json docs/openapi.yaml docs/openapi.yml docs/openapi.json public/openapi.json storage/app/openapi.yaml storage/app/openapi.yml storage/app/openapi.json; do
  [ -f "$APP/$c" ] && SPEC="$APP/$c" && break
done
if [ -n "$SPEC" ]; then
  info "spec: $SPEC"
  PATHS_FILE="$APP/storage/app/codex/openapi_paths.txt"
  : > "$PATHS_FILE"
  case "$SPEC" in
    *.json)
      php -r '
        $j=json_decode(file_get_contents("'$SPEC'"), true);
        if(isset($j["paths"]) && is_array($j["paths"])){
          foreach(array_keys($j["paths"]) as $p){ echo $p.PHP_EOL; }
        }' | head -n 30 > "$PATHS_FILE" || true
      ;;
    *.yaml|*.yml)
      # naive YAML path extraction
      awk '
        BEGIN{inpaths=0}
        /^paths:/{inpaths=1; next}
        inpaths==1 && /^[[:space:]]*\/[^:]+:/{gsub(":",""); sub(/^[[:space:]]*/,""); print $0}
        inpaths==1 && /^[^[:space:]]/{inpaths=0}
      ' "$SPEC" | head -n 30 > "$PATHS_FILE" || true
      ;;
  esac
  if [ -s "$PATHS_FILE" ]; then
    echo "## OpenAPI Probes" >> "$REPORT"
    OK=0; BAD=0
    while IFS= read -r P; do
      CODE=$(curl -s -o /dev/null -w "%{http_code}" -H "Host: $HOST" "http://127.0.0.1$P" || echo 000)
      echo "- \`$P\` -> $CODE" >> "$REPORT"
      [[ "$CODE" =~ ^2|3 ]] && OK=$((OK+1)) || BAD=$((BAD+1))
    done < "$PATHS_FILE"
    echo "" >> "$REPORT"; echo "_OpenAPI summary: $OK ok, $BAD non-2xx/3xx_" >> "$REPORT"
    [ "$BAD" -gt 0 ] && FAIL=1
  else
    echo "## OpenAPI\n- Spec found but no paths extracted." >> "$REPORT"
  fi
else
  echo "## OpenAPI\n- No spec file found (looked for openapi.{yml,yaml,json})." >> "$REPORT"
fi

# 12) Performance smoke (curl metrics)
info "perf smoke …"
echo "## Performance (curl)" >> "$REPORT"
for u in / /contact /about-us /api/v1/health; do
  read -r code size ttfb total <<<"$(curl -s -H "Host: $HOST" -o /dev/null -w "%{http_code} %{size_download} %{time_starttransfer} %{time_total}" "http://127.0.0.1$u" || echo "000 0 0 0")"
  printf "- \`%s\`: code=%s size=%sB ttfb=%ss total=%ss\n" "$u" "$code" "$size" "$ttfb" "$total" >> "$REPORT"
done

# 13) Patch suggestions (NOT applied)
info "patch suggestions …"
MISS_ROUTES="$APP/storage/app/codex/route_missing.txt"
if [ -s "$MISS_ROUTES" ]; then
  PATCH="$APP/storage/app/codex/patches/routes_name_suggestions.diff"
  {
    echo "--- a/routes/web.php"
    echo "+++ b/routes/web.php"
    echo "@@ // add missing route names (examples) @@"
    sed -n '1,50p' "$MISS_ROUTES" | sed "s/^/+ \/\/ Route::get('REPLACE_PATH', [Controller::class,'action'])->name('&');/" 
  } > "$PATCH"
  echo "## Patches" >> "$REPORT"
  echo "- Suggested route-name patch (edit paths/methods before applying):" >> "$REPORT"
  echo "  \`storage/app/codex/patches/$(basename "$PATCH")\`" >> "$REPORT"
fi
# i18n stub(s) already written in step 9

# 14) HTTP smoke (token-protected)
tok="$(sed -n 's/^AGENT_TOKEN=\(.*\)$/\1/p' "$APP/.env" | tr -d '\r\n')"
echo "## HTTP Smoke" >> "$REPORT"
[ -n "$tok" ] && echo "- /healthz-agent -> $(curl -s -o /dev/null -w "%{http_code}" -H "Host: $HOST" -H "X-Agent-Token: $tok" http://127.0.0.1/healthz-agent)" >> "$REPORT"
[ -n "$tok" ] && echo "- /api/agent/ping -> $(curl -s -o /dev/null -w "%{http_code}" -H "Host: $HOST" -H "X-Agent-Token: $tok" http://127.0.0.1/api/agent/ping)" >> "$REPORT"

# 15) Summary
if [ "$FAIL" -ne 0 ]; then
  warn "=== RESULT: FAIL (see $(basename "$LOG")) ==="
  echo >> "$REPORT"; echo "**Result:** FAIL  " >> "$REPORT"
  echo "FAIL" > "$APP/storage/app/codex/LAST_STATUS"
else
  info "=== RESULT: OK ==="
  echo >> "$REPORT"; echo "**Result:** OK  " >> "$REPORT"
  echo "OK" > "$APP/storage/app/codex/LAST_STATUS"
fi

echo >> "$REPORT"
echo "_Log: storage/logs/$(basename "$LOG")_" >> "$REPORT"
