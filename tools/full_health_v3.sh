PHP_BIN=${PHP_BIN:-php}
#!/usr/bin/env bash
set -Eeuo pipefail
cd /var/www/swaeduae
echo "=== SwaedUAE full health v3 :: $(date -Is) ==="

# --- Environment quick facts ---
php -r "require 'vendor/autoload.php'; \$app=require 'bootstrap/app.php'; \$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap(); echo 'env=' . config('app.env') . ' php=' . PHP_VERSION . ' laravel=' . app()->version() . ' APP_DEBUG=' . (config('app.debug') ? 'true':'false') . PHP_EOL;"

# --- Lint & route sanity ---
php -l routes/web.php || true
echo "-- z_overrides includes in routes/web.php --"; grep -n "z_overrides\.php" routes/web.php || echo "none"
echo "-- route tail grep --"; grep -nE "forgot-password|register|my/profile" routes/web.php || true

# --- Controllers present? ---
php -r "require 'vendor/autoload.php'; foreach(['App\Http\Controllers\Auth\RegisteredUserController','App\Http\Controllers\Auth\AuthenticatedSessionController','App\Http\Controllers\Auth\PasswordResetLinkController'] as \$c){echo (class_exists(\$c)?'OK ':'MISS ').\$c.PHP_EOL;}"

# --- Views present? ---
for f in resources/views/auth/{register,forgot-password,login}.blade.php; do
  [[ -f "$f" ]] && echo "OK view $f" || echo "MISS view $f"
done

# --- Route list (key paths) ---
php artisan route:list --path='(register|forgot-password|login|admin|org|qr/verify)' --columns=Method,URI,Name,Action 2>/dev/null | sed 's/[[:space:]]\{2,\}/ /g' || true

# --- Roles (Spatie) ---
php artisan tinker --execute='use Spatie\Permission\Models\Role; echo "roles=" . Role::count() . PHP_EOL; foreach(Role::all() as $r){ echo " - {$r->name} ({$r->guard_name})" . PHP_EOL; }' || true

# --- Storage symlink & permissions ---
[[ -L public/storage ]] && echo "OK public/storage symlink" || { echo "MISS public/storage symlink -> creating"; php artisan storage:link || true; }
stat -c "%A %U:%G %n" storage storage/logs bootstrap/cache | sed "s#$(pwd)/##g" || true

# --- Optimize / caches (route:cache may warn due to closure route; that's ok) ---
php artisan optimize:clear >/dev/null || true
php artisan config:cache >/dev/null || true
php artisan view:cache >/dev/null || true
php artisan route:cache >/dev/null 2>&1 || echo "note: route:cache skipped (closure routes)"

# --- HTTP probes (public + admin host) ---
probe() { local host="$1"; local path="$2"; code=$(curl -sS -o /tmp/resp -w "%{http_code}" "https://$host$path"); echo " https://$host$path -> $code"; }
for u in / /login /register /forgot-password /qr/verify /sitemap.xml /manifest.json; do probe "swaeduae.ae" "$u"; done
for u in /admin /admin/login; do probe "admin.swaeduae.ae" "$u"; done

# --- CSRF checks on auth pages ---
for u in /login /register /forgot-password; do
  if curl -sS "https://swaeduae.ae$u" | grep -q 'name="_token"'; then
    echo " CSRF token present on $u"
  else
    echo " WARN: CSRF token missing on $u"
  fi
done
curl -sSI https://swaeduae.ae/forgot-password | grep -i ^set-cookie | grep -q XSRF-TOKEN && echo " XSRF cookie set on /forgot-password" || echo " WARN: no XSRF cookie on /forgot-password"

# --- Scheduler heartbeat (last lines) ---
echo "-- recent log lines --"; tail -n 20 storage/logs/laravel-$(date +%F).log 2>/dev/null | sed 's/\x1b\[[0-9;]*m//g' || true

echo "=== Done full health v3 ==="
