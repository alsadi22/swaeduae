#!/usr/bin/env bash
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
REPORT_DIR="$ROOT/tmp/audit-$(date +%Y%m%d-%H%M%S)"
mkdir -p "$REPORT_DIR"
cd "$ROOT"

echo "== Laravel Pre-flight Audit =="
echo "Project root: $ROOT"
echo "Reports: $REPORT_DIR"
echo

# 0) ENV/versions
{
  echo "## ENV & Versions"
  echo "PWD=$ROOT"
  echo "PHP: $(php -v | head -n1)"
  echo "Composer: $(composer --version 2>/dev/null || echo 'composer not found')"
  echo "Laravel: $(php artisan --version 2>/dev/null || echo 'artisan not found')"
  echo
  echo "### .env snapshot (key items)"
  grep -E '^(APP_ENV|APP_DEBUG|APP_URL|APP_LOCALE|APP_FALLBACK_LOCALE|SESSION_DRIVER|CACHE_DRIVER)=' .env || true
} | tee "$REPORT_DIR/00_env_versions.txt"

# 1) Cache & permissions
{
  echo "## Cache & Permissions"
  echo "bootstrap/cache perms: $(stat -c '%A %U:%G' bootstrap/cache 2>/dev/null || echo 'missing')"
  echo "storage perms:        $(stat -c '%A %U:%G' storage 2>/dev/null || echo 'missing')"
  echo "public/storage link:  $(readlink -f public/storage 2>/dev/null || echo 'missing')"
  echo
  echo "# Clearing & caching (dry run info)"
  php artisan config:clear  >/dev/null || true
  php artisan route:clear   >/dev/null || true
  php artisan view:clear    >/dev/null || true
  php artisan optimize      >/dev/null || true
  echo "Rebuilt caches."
} | tee "$REPORT_DIR/01_cache_perms.txt"

# 2) Routes inventory
php artisan route:list --json > "$REPORT_DIR/routes.json" || true
jq -r '.[].name | select(.!=null)' "$REPORT_DIR/routes.json" 2>/dev/null | sort -u > "$REPORT_DIR/route_names.txt" || true
jq -r '.[].uri' "$REPORT_DIR/routes.json" 2>/dev/null | sort -u > "$REPORT_DIR/route_uris.txt" || true

# 3) Blade scan (grep fallback)
find resources/views -type f -name '*.blade.php' > "$REPORT_DIR/all_blades.txt"

{
  echo "## Hardcoded URL candidates"
  echo "### /login, /volunteer/login, http(s)://swaeduae.ae etc."
  grep -RnoE 'href=[^>]*"/login|href=[^>]*"/volunteer/login|https?://swaeduae\.ae' resources/views || true
} | tee "$REPORT_DIR/02_hardcoded_urls.txt"

{
  echo "## route() helpers referenced in code"
  grep -RnoE "route\(\s*'[^']+'\s*" resources app routes || true
} | tee "$REPORT_DIR/03_route_calls.txt"

grep -RnoE "route\(\s*'([^']+)'\s*" resources app routes | \
  sed -E "s/.*route\(\s*'([^']+)'.*/\1/" | sort -u > "$REPORT_DIR/referenced_route_names.txt" || true

if [[ -s "$REPORT_DIR/route_names.txt" && -s "$REPORT_DIR/referenced_route_names.txt" ]]; then
  comm -23 "$REPORT_DIR/referenced_route_names.txt" "$REPORT_DIR/route_names.txt" > "$REPORT_DIR/MISSING_route_names.txt" || true
fi

normalize() { local n="${1//./\/}"; printf "%s\n" "resources/views/${n}.blade.php" "resources/views/${n}/index.blade.php"; }

> "$REPORT_DIR/04_includes_extends_missing.txt"
grep -RnoE '@(include|extends)\(([^)]+)\)' resources/views | \
  sed -E 's/^([^:]+):.*@(include|extends)\(([^)]+)\).*/\1:\3/' | \
  while IFS= read -r line; do
    file="${line%%:*}"; target="${line#*:}"
    target="$(printf "%s" "$target" | sed -E "s/['\"\)\s]//g")"
    found="no"
    while IFS= read -r cand; do [[ -f "$cand" ]] && found="yes" && break; done < <(normalize "$target")
    [[ "$found" == "no" ]] && echo "$file -> $target (missing?)" >> "$REPORT_DIR/04_includes_extends_missing.txt"
  done

> "$REPORT_DIR/05_view_function_missing.txt"
grep -RnoE "view\(\s*'[^']+'\s*\)" routes app | \
  while IFS= read -r v; do
    name="$(printf "%s" "$v" | sed -E "s/.*view\(\s*'([^']+)'.*/\1/")"
    [[ -z "$name" ]] && continue
    ok="no"
    while IFS= read -r c; do [[ -f "$c" ]] && ok="yes" && break; done < <(normalize "$name")
    [[ "$ok" == "no" ]] && echo "$v" >> "$REPORT_DIR/05_view_function_missing.txt"
  done

# 5) Forms & CSRF (improved: only flag likely non‑GET forms)
{
  echo "## Forms missing @csrf (probable POST/PUT/PATCH/DELETE)"
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
} | tee "$REPORT_DIR/06_forms_csrf.txt"

# 6) HTTP HEAD checks
APP_URL="$(grep -E '^APP_URL=' .env | cut -d= -f2 | tr -d '"')"
BASE="${APP_URL:-https://swaeduae.ae}"
URLS=("$BASE/" "$BASE/faq" "$BASE/admin/login")
{
  echo "## HTTP HEAD checks"
  for u in "${URLS[@]}"; do
    echo "== $u =="; curl -skI "$u" | sed -n '1p;/^Content-Language:/p;/^Cache-Control:/p;/^Content-Security-Policy:/p;/^Set-Cookie:/p'; echo
  done
} | tee "$REPORT_DIR/07_http_heads.txt"

# 7) Migration & drivers (read from .env directly)
{
  echo "## DB & Queues"
  php artisan migrate:status || true
  echo
  echo "Queue:  $(grep -E '^QUEUE_CONNECTION=' .env | cut -d= -f2 | tr -d '\"')"
  echo "Cache:  $(grep -E '^CACHE_DRIVER=' .env | cut -d= -f2 | tr -d '\"')"
  echo "Session:$(grep -E '^SESSION_DRIVER=' .env | cut -d= -f2 | tr -d '\"')"
} | tee "$REPORT_DIR/08_db_queue_cache.txt"

# 8) Assets sanity
{
  echo "## Assets sanity"
  test -f public/build/manifest.json && echo "Vite manifest: present" || echo "Vite manifest: MISSING"
  test -d public/build && echo "public/build exists" || echo "public/build MISSING"
  test -f public/mix-manifest.json && echo "Mix manifest: present" || echo "Mix manifest: MISSING"
} | tee "$REPORT_DIR/09_assets.txt"

# 9) Summary
{
  echo "## Summary (files of interest)"
  for f in 02_hardcoded_urls.txt 03_route_calls.txt MISSING_route_names.txt 04_includes_extends_missing.txt 05_view_function_missing.txt 06_forms_csrf.txt 07_http_heads.txt 08_db_queue_cache.txt 09_assets.txt
  do p="$REPORT_DIR/$f"; [[ -s "$p" ]] && echo "- $f   (issues or findings present)" || echo "- $f   (OK/empty or not generated)"; done
  echo
  echo "Full route JSON: $REPORT_DIR/routes.json"
  echo "Defined route names: $REPORT_DIR/route_names.txt"
  echo "Referenced route names: $REPORT_DIR/referenced_route_names.txt"
  echo
  echo "Done."
} | tee "$REPORT_DIR/10_summary.txt"
