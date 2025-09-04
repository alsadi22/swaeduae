#!/usr/bin/env bash
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
cd "$ROOT"

PHP_BIN="${PHP_BIN:-/opt/alt/php84/usr/bin/php}"; [ -x "$PHP_BIN" ] || PHP_BIN="php"
COMPOSER_BIN="${COMPOSER_BIN:-/opt/cpanel/composer/bin/composer}"; command -v "$COMPOSER_BIN" >/dev/null 2>&1 || COMPOSER_BIN="composer"

LOG="$ROOT/storage/logs/deploy-$(date +%F_%H%M%S).log"
touch "$LOG"

say(){ echo -e "$@" | tee -a "$LOG"; }

say "== deploy start $(date -Iseconds) =="

# 3a) Pull latest if repo/remote exists (safe if not)
if [ -d .git ] && command -v git >/dev/null 2>&1; then
  say "\n-- git status --"
  git status -sb | tee -a "$LOG" || true
  say "\n-- git pull --"
  git pull --ff-only 2>&1 | tee -a "$LOG" || say "git pull skipped (no remote?)"
else
  say "git repo not found or git not installed; skipping pull"
fi

# 3b) Enter maintenance (with secret to preview if needed)
SECRET="$(openssl rand -hex 12 2>/dev/null || date +%s)"
$PHP_BIN artisan down --secret="$SECRET" --refresh=15 --retry=60 || true
say "maintenance secret: $SECRET"

# 3c) Dependencies
say "\n-- composer install --"
$COMPOSER_BIN install --no-dev --prefer-dist --optimize-autoloader --no-interaction --no-progress 2>&1 | tee -a "$LOG"

# 3d) DB migrations
say "\n-- artisan migrate --"
$PHP_BIN artisan migrate --force 2>&1 | tee -a "$LOG"

# 3e) Warm caches
say "\n-- warm caches --"
PHP_BIN="$PHP_BIN" bash "$ROOT/tools/warm_cache.sh" 2>&1 | tee -a "$LOG"

# 3f) Restart queues (workers will gracefully reload)
say "\n-- queue:restart --"
$PHP_BIN artisan queue:restart 2>&1 | tee -a "$LOG"

# 3g) (Optional) Reset OPcache (CLI only; FPM will refresh on file change)
$PHP_BIN -r 'if(function_exists("opcache_reset")){opcache_reset(); echo "OPcache reset\n";}'

# 3h) Health check before going up
say "\n-- health check --"
if ! $PHP_BIN tools/health_check.php 2>&1 | tee -a "$LOG" ; then
  say "health check failed; leaving app in maintenance for safety"
  exit 1
fi

# 3i) Exit maintenance
$PHP_BIN artisan up
say "\n== deploy done $(date -Iseconds) =="
say "If needed during a future deploy, preview via: https://swaeduae.ae/$SECRET"
