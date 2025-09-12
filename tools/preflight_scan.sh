#!/usr/bin/env bash
set -euo pipefail

APP=/var/www/swaeduae
CUR="$(readlink -f "$APP/current")"
TS=$(date +%Y%m%d_%H%M%S)
OUT="$HOME/swaed_scans/reports/$TS"
mkdir -p "$OUT"
echo "== Preflight started @ $TS =="
echo "current -> $CUR"; echo "out -> $OUT"

has() { command -v "$1" >/dev/null 2>&1; }
has_docker() { has docker && docker info >/dev/null 2>&1; }

# 1) Internal Laravel full audit (server-side)
echo "[1/7] Internal Laravel audit..."
if [ -x "$CUR/tools/full_audit_v5.sh" ]; then
  ( cd "$CUR" && bash tools/full_audit_v5.sh ) | tee "$OUT/internal_audit.txt"
else
  echo "SKIP: tools/full_audit_v5.sh not found" | tee -a "$OUT/summary.txt"
fi

# 2) Blade Guard + caches
echo "[2/7] Blade Guard + caches..."
if [ -x "$CUR/tools/audit_blade_guard.sh" ]; then
  ( cd "$CUR" && ./tools/audit_blade_guard.sh ) > "$OUT/blade_guard.txt" 2>&1 || true
else
  echo "SKIP: tools/audit_blade_guard.sh not found" | tee -a "$OUT/summary.txt"
fi
( cd "$CUR" && php artisan view:clear >/dev/null 2>&1 && php artisan view:cache >/dev/null 2>&1 && echo "view cache OK" > "$OUT/cache_build.txt" )
( cd "$CUR" && php artisan route:clear >/dev/null 2>&1 && php artisan route:cache >/dev/null 2>&1 && echo "route cache OK" >> "$OUT/cache_build.txt" )

# 3) Gentle site crawler (wget)
echo "[3/7] wget crawl (public)..."
mkdir -p "$HOME/swaed_scans/crawl"
( cd "$HOME/swaed_scans/crawl" && \
  wget --recursive --level=2 --wait=1 --limit-rate=200k --adjust-extension \
       --span-hosts --domains=swaeduae.ae --no-parent --no-verbose \
       --reject-regex='^https://admin\.swaeduae\.ae/.*' \
       --execute robots=off --page-requisites \
       --output-file="$OUT/wget-public.log" https://swaeduae.ae/ || true )
grep -E "ERROR|failed|404|500" "$OUT/wget-public.log" | tail -n 200 > "$OUT/wget-public-errors.txt" || true

# 4) ZAP Baseline (Docker, passive)
echo "[4/7] ZAP baseline..."
if has_docker; then
  docker run --rm -t -v "$HOME/swaed_scans/reports:/zap/wrk" ghcr.io/zaproxy/zaproxy:stable \
    zap-baseline.py -t https://swaeduae.ae \
    -r "zap-baseline-public-$TS.html" -m 5 -I \
    -z '-config spider.maxDuration=5 -config spider.maxDepth=4 -config spider.maxChildren=50
        -config globalexcludeurl.url_list.url(0).regex=true
        -config globalexcludeurl.url_list.url(0).name=AdminExclude
        -config globalexcludeurl.url_list.url(0).value=https://admin\.swaeduae\.ae/.*' || true

  docker run --rm -t -v "$HOME/swaed_scans/reports:/zap/wrk" ghcr.io/zaproxy/zaproxy:stable \
    zap-baseline.py -t https://admin.swaeduae.ae \
    -r "zap-baseline-admin-$TS.html" -m 5 -I \
    -z '-config spider.maxDuration=3 -config spider.maxDepth=2 -config spider.maxChildren=20' || true
else
  echo "SKIP: Docker/ZAP not available" | tee -a "$OUT/summary.txt"
fi

# 5) Nikto (Docker, conservative)
echo "[5/7] Nikto..."
if has_docker; then
  docker run --rm -t -v "$HOME/swaed_scans/reports:/reports" sullo/nikto \
    -host https://swaeduae.ae -nointeractive -maxtime 45m -timeout 10 -pause 1 \
    -Tuning 1,2,3,4,5,6,b -Display V -output "/reports/nikto-public-$TS.txt" || true
else
  echo "SKIP: Docker/Nikto not available" | tee -a "$OUT/summary.txt"
fi

# 6) PHP Insights (Docker, read-only)
echo "[6/7] PHP Insights..."
if has_docker; then
  mkdir -p "$HOME/safe_scan_tools"
  if [ ! -f "$HOME/safe_scan_tools/phpinsights.php" ]; then
    cat > "$HOME/safe_scan_tools/phpinsights.php" <<'PHP'
<?php
return [
  'preset' => 'laravel',
  'exclude' => ['bootstrap','storage','vendor','public','_archive','_quarantine','scripts','tools','swaeduae_updates'],
  'paths' => ['/project/app','/project/routes'],
  'remove' => [
    NunoMaduro\PhpInsights\Domain\Insights\ForbiddenNormalClasses::class,
    SlevomatCodingStandard\Sniffs\Functions\FunctionLengthSniff::class,
  ],
];
PHP
  fi
  docker run --rm -t \
    -v "$CUR:/project:ro" \
    -v "$HOME/safe_scan_tools:/tools" \
    -w /tools composer:2 bash -lc '
      set -e
      [ -x ./vendor/bin/phpinsights ] || {
        echo "{\"require-dev\":{\"nunomaduro/phpinsights\":\"^2\"},
               \"config\":{\"allow-plugins\":{\"dealerdirect/phpcodesniffer-composer-installer\":true}}}" > composer.json
        composer install --no-interaction --no-scripts --prefer-dist --quiet
      }
      ./vendor/bin/phpinsights analyse -n -v --config-path=/tools/phpinsights.php /project || true
    ' | tee "$OUT/phpinsights.txt"
else
  echo "SKIP: Docker/Composer (phpinsights) not available" | tee -a "$OUT/summary.txt"
fi

# 7) PHPStan / Larastan (Docker, read-only)
echo "[7/7] PHPStan (Larastan)..."
if has_docker; then
  mkdir -p "$HOME/safe_scan_tools"
  docker run --rm -t \
    -v "$CUR:/project:ro" \
    -v "$HOME/safe_scan_tools:/tools" \
    -w /tools composer:2 bash -lc '
      set -e
      [ -x ./vendor/bin/phpstan ] || {
        echo "{\"require-dev\":{\"phpstan/phpstan\":\"^1\",\"nunomaduro/larastan\":\"^2\"},
               \"config\":{\"allow-plugins\":{\"dealerdirect/phpcodesniffer-composer-installer\":true}}}" > composer.json
        composer install --no-interaction --no-scripts --prefer-dist --quiet
      }
      cat > /tools/larastan_bootstrap.php <<PHP
<?php
require "/project/vendor/autoload.php";
if (!defined("LARAVEL_VERSION")) {
  define("LARAVEL_VERSION", class_exists(Illuminate\\Foundation\\Application::class) ? Illuminate\\Foundation\\Application::VERSION : "11");
}
PHP
      cat > /tools/phpstan.neon <<NEON
includes:
  - vendor/nunomaduro/larastan/extension.neon
parameters:
  level: 6
  tmpDir: /tools/.phpstan
  paths: [ /project/app, /project/routes, /project/database, /project/config ]
  bootstrapFiles: [ /tools/larastan_bootstrap.php ]
  checkModelProperties: true
NEON
      ./vendor/bin/phpstan analyse -c /tools/phpstan.neon --memory-limit=1G || true
    ' | tee "$OUT/phpstan.txt"
else
  echo "SKIP: Docker/Composer (phpstan) not available" | tee -a "$OUT/summary.txt"
fi

# Simple summary
echo "== SUMMARY ==" | tee "$OUT/summary.txt"
grep -E "ERROR|FAIL|CRITICAL|High|Medium|ZAP|Nikto" "$OUT"/* 2>/dev/null | tee -a "$OUT/summary.txt" || echo "No critical findings captured." | tee -a "$OUT/summary.txt"

echo "Reports saved to: $OUT"
# Do not exit the shell if sourced
if [[ "${BASH_SOURCE[0]}" == "${0}" ]]; then exit 0; else return 0; fi
