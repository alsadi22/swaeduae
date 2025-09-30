#!/usr/bin/env bash
PHP_BIN=${PHP_BIN:-php}
set -euo pipefail

ROOT="$(pwd)"
OUTDIR="_reports"
TS="$(date +%Y-%m-%d_%H%M%S)"
mkdir -p "$OUTDIR"

REPORT="$OUTDIR/structure_report_$TS.txt"

# Helpers
hr(){ printf '%s\n' "------------------------------------------------------------"; }

# Header
{
  echo "Project Structure Audit - $TS"
  echo "Root: $ROOT"
  echo
} > "$REPORT"

# Git status (if repo)
{
  hr; echo "GIT STATUS"; hr
  if [ -d .git ]; then
    git rev-parse --abbrev-ref HEAD 2>/dev/null || true
    git status --porcelain || true
  else
    echo "No .git directory."
  fi
} >> "$REPORT"

# Composer info
{
  hr; echo "COMPOSER INFO"; hr
  if command -v php >/dev/null 2>&1 && [ -f composer.lock ]; then
    php -r '
      $f = "composer.lock";
      if (!file_exists($f)) { exit(0); }
      $j = json_decode(file_get_contents($f), true);
      if (!$j) { exit(0); }
      foreach (["packages","packages-dev"] as $k) {
        if (!isset($j[$k])) continue;
        foreach ($j[$k] as $p) {
          if ($p["name"] === "laravel/framework") {
            echo "laravel/framework version: ".$p["version"].PHP_EOL;
          }
        }
      }
    '
  fi
  echo "composer.json require (top lines):"
  sed -n '1,120p' composer.json 2>/dev/null | sed -n '1,120p'
} >> "$REPORT"

# Routes
{
  hr; echo "ROUTES"; hr
  if [ -d routes ]; then
    echo "routes/ files:"
    find routes -type f -maxdepth 1 -printf '%P\n' | sort
    echo
    echo "Web routes (first 200 lines):"
    [ -f routes/web.php ] && sed -n '1,200p' routes/web.php
    echo
    echo "API routes (first 200 lines):"
    [ -f routes/api.php ] && sed -n '1,200p' routes/api.php
  else
    echo "No routes/ directory found."
  fi
  # Also report unexpected root-level web.php
  [ -f web.php ] && { echo; echo "(!) Found root-level web.php (first 120 lines):"; sed -n '1,120p' web.php; }
} >> "$REPORT"

# App structure (Controllers, Models, Middleware, Jobs, Events, Listeners, Requests)
{
  hr; echo "APP STRUCTURE"; hr
  if [ -d app ]; then
    echo "Controllers:"
    find app -type f -path '*/Http/Controllers/*' -name '*.php' -printf '%P\n' | sort || true
    echo
    echo "Models:"
    find app -type f -path '*/Models/*' -name '*.php' -printf '%P\n' | sort || true
    echo
    echo "Middleware:"
    find app -type f -path '*/Http/Middleware/*' -name '*.php' -printf '%P\n' | sort || true
    echo
    echo "Form Requests:"
    find app -type f -path '*/Http/Requests/*' -name '*.php' -printf '%P\n' | sort || true
    echo
    echo "Jobs:"
    find app -type f -path '*/Jobs/*' -name '*.php' -printf '%P\n' | sort || true
    echo
    echo "Events:"
    find app -type f -path '*/Events/*' -name '*.php' -printf '%P\n' | sort || true
    echo
    echo "Listeners:"
    find app -type f -path '*/Listeners/*' -name '*.php' -printf '%P\n' | sort || true
    echo
    echo "Policies:"
    find app -type f -path '*/Policies/*' -name '*.php' -printf '%P\n' | sort || true
    echo
    echo "Services (if any):"
    find app -type d -name 'Services' -print -exec find {} -type f -name '*.php' -printf '  %P\n' \; 2>/dev/null || true
  else
    echo "No app/ directory found."
  fi
} >> "$REPORT"

# Views
{
  hr; echo "VIEWS (Blade)"; hr
  echo "resources/views/*:"
  find resources/views -type f -name '*.blade.php' -printf '%P\n' 2>/dev/null | sort || true
  echo
  echo "Root-level Blade templates:"
  find . -maxdepth 1 -type f -name '*.blade.php' -printf '%P\n' | sort
} >> "$REPORT"

# Database (migrations/seeders/factories)
{
  hr; echo "DATABASE (Migrations/Seeders/Factories)"; hr
  if [ -d database ]; then
    echo "Migrations:"
    find database/migrations -type f -name '*.php' -printf '%P\n' 2>/dev/null | sort || true
    echo
    echo "Seeders:"
    find database/seeders -type f -name '*.php' -printf '%P\n' 2>/dev/null | sort || true
    echo
    echo "Factories:"
    find database/factories -type f -name '*.php' -printf '%P\n' 2>/dev/null | sort || true
  else
    echo "No database/ directory found."
  fi
} >> "$REPORT"

# Configs
{
  hr; echo "CONFIG FILES"; hr
  find config -type f -name '*.php' -printf '%P\n' 2>/dev/null | sort || echo "No config/ directory."
} >> "$REPORT"

# Language files
{
  hr; echo "LANG FILES"; hr
  find lang -type f -printf '%P\n' 2>/dev/null | sort || echo "No lang/ directory."
} >> "$REPORT"

# Public assets high-level
{
  hr; echo "PUBLIC ASSETS (top-level)"; hr
  find public -maxdepth 2 -type d -printf '%P/\n' 2>/dev/null | sort | sed 's:^/:public/:'
} >> "$REPORT"

# Frontend (if present)
{
  hr; echo "FRONTEND DIRECTORY"; hr
  if [ -d frontend ]; then
    echo "frontend/ exists."
    [ -f frontend/package.json ] && { echo; echo "frontend/package.json (first 120 lines):"; sed -n '1,120p' frontend/package.json; }
    [ -f package.json ] && { echo; echo "root package.json (first 120 lines):"; sed -n '1,120p' package.json; }
  else
    echo "No frontend/ directory found or no package.json present."
  fi
} >> "$REPORT"

# .env keys only (no values)
{
  hr; echo ".ENV KEYS (redacted)"; hr
  if [ -f .env ]; then
    grep -v '^\s*#' .env | grep -E '^[A-Za-z0-9_]+=' | cut -d= -f1 | sort -u
  else
    echo "No .env present."
  fi
} >> "$REPORT"

# Top 50 largest files (excluding vendor/storage/logs/cache/node_modules)
{
  hr; echo "TOP 50 LARGEST FILES (excluding vendor, storage, node_modules)"; hr
  du -ah --threshold=1K \
    --exclude='vendor' --exclude='storage' --exclude='node_modules' 2>/dev/null \
    | sort -h | tail -n 200 | grep -v -E '/(vendor|storage|node_modules)/' | tail -n 50
} >> "$REPORT"

# Anomalies in project root (suspicious names)
{
  hr; echo "ANOMALIES IN ROOT (suspicious names)"; hr
  printf "%s\n" "," "-" "[" "200" "302" "500" "Allow:" "E" "EOF" "ExpiresByType" "ExpiresDefault" "Header" "Sit" "User-agent:" "id," "cp" \
  | while read -r f; do
      [ -e "$f" ] && echo "Found: $f"
    done

  # Look for files with whitespace or strange punctuation
  echo
  echo "Files with spaces/punctuation in names (root, depth 1):"
  find . -maxdepth 1 -type f | grep -E '[[:space:][:punct:]]' | sed 's:^\./::' | sort

  # Specifically flag files that look like code snippets gone wrong
  echo
  echo "Potentially leaked code snippet files:"
  find . -maxdepth 1 -type f -name '*bcrypt*' -printf '%P\n' | sort
  ls -1 "an tinker" 2>/dev/null || true
} >> "$REPORT"

echo "Report written to: $REPORT"
