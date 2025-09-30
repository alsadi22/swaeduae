PHP_BIN=${PHP_BIN:-php}
#!/usr/bin/env bash
# READ-ONLY DIAGNOSTIC — does not modify your app.
set -uo pipefail
BASE="${BASE:-https://swaeduae.ae}"
TS="$(date +'%F_%H%M%S')"
OUT="public/health/error-audit-$TS.txt"
mkdir -p public/health tools
exec > >(tee "$OUT") 2>&1

green=$'\e[32m'; red=$'\e[31m'; yellow=$'\e[33m'; reset=$'\e[0m'
HIT(){ echo "${green}[HIT]${reset} $*"; }
MISS(){ echo "${red}[MISS]${reset} $*"; }
WARN(){ echo "${yellow}[WARN]${reset} $*"; }

echo "=== FULL ERROR AUDIT ($(date -u '+%F %T') UTC) ==="
echo "BASE=$BASE"
php -v | head -n1 || true

echo; echo "-- PHP lint: routes/web.php --"
php -l routes/web.php || true

echo; echo "-- Suspicious regions in routes/web.php --"
echo "[Head 1..120]"
nl -ba routes/web.php | sed -n '1,120p' || true
echo "[Block 240..360]"
nl -ba routes/web.php | sed -n '240,360p' || true

echo; echo "-- Duplicate mid-file facade imports (should appear only once near the top) --"
grep -n '^[[:space:]]*use[[:space:]]\+Illuminate\\Support\\Facades\\Route;' routes/web.php || true
grep -n '^[[:space:]]*use[[:space:]]\+Illuminate\\Support\\Facades\\DB;' routes/web.php || true

echo; echo "-- Rogue lines that commonly break parsing (leading backslash Facades outside closures) --"
if grep -n '^[[:space:]]*\\Illuminate\\Support\\Facades\\\\' routes/web.php; then
  WARN "Found rogue leading-backslash Facade lines above."
else
  HIT "No rogue leading-backslash Facade lines detected."
fi

echo; echo "-- Account Applications blocks & duplicates --"
grep -n "account/applications" routes/web.php || true
grep -n "return view('account.applications'" routes/web.php || true

echo; echo "-- artisan route:list (may fail if routes file is broken) --"
php artisan route:list > /tmp/rl.txt 2> /tmp/rl.err || true
if [ -s /tmp/rl.txt ]; then
  HIT "route:list succeeded (showing first 80 lines)"
  sed -n '1,80p' /tmp/rl.txt
else
  MISS "route:list failed — showing error:"
  cat /tmp/rl.err
fi

echo; echo "-- Controllers presence --"
for f in \
  app/Http/Controllers/Public/OpportunityController.php \
  app/Http/Controllers/Public/ApplyController.php
do
  if [ -f "$f" ]; then HIT "$f"; else MISS "$f (missing)"; fi
done

echo; echo "-- Views presence --"
for f in \
  resources/views/public/opportunities/index.blade.php \
  resources/views/public/opportunities/show.blade.php \
  resources/views/account/applications.blade.php
do
  if [ -f "$f" ]; then HIT "$f"; else MISS "$f (missing)"; fi
done

echo; echo "-- DB tables --"
php artisan tinker --execute='
use Illuminate\Support\Facades\Schema; 
echo "opportunities: ".(Schema::hasTable("opportunities")?"yes":"no").PHP_EOL;
echo "applications: ".(Schema::hasTable("applications")?"yes":"no").PHP_EOL;
' 2>/dev/null || WARN "tinker failed (continuing)"

echo; echo "-- Recent Laravel log tail (last 40) --"
latest=$(ls -1t storage/logs/laravel-*.log 2>/dev/null | head -n1)
if [ -n "${latest:-}" ]; then
  echo "Log: $latest"; tail -n 40 "$latest"
else
  WARN "No laravel log found"
fi

echo; echo "-- HTTP probes (public path) --"
for u in / /opportunities /opportunities/demo-event /account/applications /login; do
  code=$(curl -s -o /dev/null -w "%{http_code}" -I "$BASE$u")
  printf "%-30s -> %s\n" "$u" "$code"
done

echo; echo "-- Suggested FIX commands (NOT RUN) --"
cat <<'FIX'
# 1) Remove any rogue lines that start with a leading backslash Facade (these cause parse errors):
sudo sed -i '/^\s*\\Illuminate\\Support\\Facades\\\\/d' routes/web.php

# 2) If you see a duplicated OUT-OF-CLOSURE chunk for account/apps (you will see two "return view('account.applications'...)" blocks),
#    delete the stray block by line range (adjust after reading the report):
#    Example: to delete lines 33..50:
# sudo sed -i '33,50d' routes/web.php

# 3) Force /opportunities/{slug}/apply to use the working controller:
sudo sed -i 's#PublicOpportunityController@apply#App\\Http\\Controllers\\Public\\ApplyController@store#g' routes/web.php
sudo sed -i "s#\\[\\s*PublicOpportunityController::class\\s*,\\s*'apply'\\s*\\]#[App\\\\Http\\\\Controllers\\\\Public\\\\ApplyController::class, 'store']#g" routes/web.php

# 4) Ensure list/show routes exist (append only if missing):
grep -q "name('opportunities.index')" routes/web.php || \
  echo "Route::get('/opportunities', [\\App\\Http\\Controllers\\Public\\OpportunityController::class, 'index'])->name('opportunities.index');" | sudo tee -a routes/web.php >/dev/null
grep -q "name('opportunities.show')" routes/web.php || \
  echo "Route::get('/opportunities/{slug}', [\\App\\Http\\Controllers\\Public\\OpportunityController::class, 'show'])->name('opportunities.show');" | sudo tee -a routes/web.php >/dev/null

# 5) Rebuild
php artisan optimize:clear && php artisan route:cache && php artisan view:cache
FIX

echo; echo "Saved report: $OUT"
