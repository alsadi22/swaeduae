#!/usr/bin/env bash
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
TS="$(date +%Y%m%d-%H%M%S)"
OUT="$ROOT/tmp/hostcheck-$TS"
mkdir -p "$OUT"
cd "$ROOT"

echo "== Host & App Check (read-only) =="
echo "Root: $ROOT"
echo "Out:  $OUT"
echo

# --- Host environment (no changes) ---
{
  echo "## Host"
  echo "Date: $(date -u) (UTC)"
  echo "Uname: $(uname -a)"
  echo "User: $(id -u -n)  Groups: $(id -Gn)"
  echo
  echo "## Executables"
  for bin in php composer node npm yarn pnpm supervisorctl rg jq curl crontab; do
    printf '%-14s: ' "$bin"
    if command -v "$bin" >/dev/null 2>&1; then
      printf '%s' "$(command -v "$bin")"
      [[ "$bin" =~ ^(php|node|npm|yarn|pnpm|composer)$ ]] && printf '  (%s)' "$("$bin" --version 2>/dev/null | head -n1)"
      echo
    else
      echo "NOT FOUND"
    fi
  done
  echo
  echo "## PHP settings (key)"
  php -v | head -n1
  php -i | grep -E 'memory_limit|max_execution_time|post_max_size|upload_max_filesize' || true
  php -i | grep -E '^disable_functions' || true
} | tee "$OUT/00_host.txt"

# --- App basics ---
{
  echo "## App basics"
  echo "Laravel: $(php artisan --version 2>/dev/null || echo 'artisan not found')"
  echo "APP_URL: $(grep -E '^APP_URL=' .env | cut -d= -f2)"
  echo "CACHE_DRIVER: $(grep -E '^CACHE_DRIVER=' .env | cut -d= -f2)"
  echo "SESSION_DRIVER: $(grep -E '^SESSION_DRIVER=' .env | cut -d= -f2)"
  echo "QUEUE_CONNECTION: $(grep -E '^QUEUE_CONNECTION=' .env | cut -d= -f2)"
  echo
  echo "## Writable dirs"
  for d in storage bootstrap/cache public; do
    printf '%-18s: ' "$d"
    [[ -d "$d" ]] && test -w "$d" && echo "writable" || echo "check-perms"
  done
} | tee "$OUT/01_app.txt"

# --- Routes & migrations (read-only) ---
{
  echo "## Routes (count)"
  php artisan route:list --json 2>/dev/null | jq 'length' 2>/dev/null || echo "route:list json not available"
  echo
  echo "## Pending migrations (if any)"
  php artisan migrate:status 2>/dev/null | awk 'BEGIN{p=0}/^ *Migration name/{p=1}p' | grep -E 'Pending|Not Ran|^\s*$' -n || echo "No explicit pending lines detected"
} | tee "$OUT/02_routes_migrations.txt"

# --- Assets presence (Vite/Mix/static) ---
{
  echo "## Assets presence"
  test -f public/build/manifest.json && echo "Vite manifest: present" || echo "Vite manifest: MISSING"
  test -d public/build && echo "public/build exists" || echo "public/build MISSING"
  test -f public/mix-manifest.json && echo "Mix manifest: present" || echo "Mix manifest: MISSING"
  echo
  echo "Blade @vite usage (count):"
  grep -R "@vite(" -n resources/views | wc -l || true
  echo "Blade mix() usage (count):"
  grep -R "mix\(" -n resources/views | wc -l || true
  echo "Public css/js files (top level):"
  find public -maxdepth 2 -type f \( -name '*.css' -o -name '*.js' \) | head -n 30
} | tee "$OUT/03_assets.txt"

# --- Hardcoded URLs & @csrf quick scans (no writes) ---
{
  echo "## Hardcoded URL candidates"
  grep -RnoE 'href=[^>]*"/login|href=[^>]*"/volunteer/login|https?://swaeduae\.ae' resources/views || true
} | tee "$OUT/04_hardcoded_urls.txt"

{
  echo "## Non-GET forms missing @csrf (heuristic)"
  awk '
    BEGIN{IGNORECASE=1}
    /<form[[:space:]>]/ { printing=1; buf=""; fname=FILENAME; line=FNR }
    { if (printing) buf = buf $0 "\n" }
    printing && /<\/form>/ {
      isPost = (buf ~ /method=[\"\x27]post[\"\x27]/) || (buf ~ /@method\((\"|\x27)(PUT|PATCH|DELETE)(\"|\x27)\)/)
      if (isPost && buf !~ /@csrf/) {
        printf("%s:%d: non-GET form without @csrf\n", fname, line)
      }
      printing=0; buf=""
    }
  ' $(find resources/views -name '*.blade.php')
} | tee "$OUT/05_forms_csrf.txt"

# --- HTTP HEAD for key endpoints (no writes) ---
{
  echo "## HTTP HEAD checks"
  BASE="$(grep -E '^APP_URL=' .env | cut -d= -f2 | tr -d '"')"
  [[ -z "$BASE" ]] && BASE="https://swaeduae.ae"
  for u in "$BASE/" "$BASE/faq" "$BASE/admin/login"; do
    echo "== $u =="; curl -skI "$u" | sed -n '1p;/^Content-Language:/p;/^Cache-Control:/p;/^Content-Security-Policy:/p;/^Set-Cookie:/p'; echo
  done
} | tee "$OUT/06_http_heads.txt"

echo
echo "Done. Folder: $OUT"
