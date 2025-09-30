#!/usr/bin/env bash
PHP_BIN=${PHP_BIN:-php}
set -Eeuo pipefail
cd /var/www/swaeduae
echo "=== VOLUNTEER FEATURE AUDIT @ $(date -Is) ==="

# — App env / versions
php -r "require 'vendor/autoload.php'; \$app=require 'bootstrap/app.php'; \$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap(); echo 'env='.config('app.env').' php='.PHP_VERSION.' laravel='.app()->version().' APP_DEBUG='.(config('app.debug')?'true':'false').PHP_EOL;"

# — Users schema (fields Volunteers.ae expects)
php -r "require 'vendor/autoload.php'; \$app=require 'bootstrap/app.php'; \$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap(); use Illuminate\Support\Facades\Schema; \$cols=['name','name_ar','email','email_verified_at','mobile','phone','mobile_verified_at','gender','dob','nationality','emirate','city','emirates_id','emirates_id_expiry','avatar_path','terms_accepted_at']; foreach(\$cols as \$c){ printf('%-22s %s'.PHP_EOL, \$c, Schema::hasColumn('users',\$c)?'YES':'no'); }"

# — Roles & counts (Spatie)
php -r "require 'vendor/autoload.php'; \$app=require 'bootstrap/app.php'; \$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap(); use Spatie\Permission\Models\Role; use App\Models\User; echo 'roles: '; foreach(Role::all() as \$r){ echo \$r->name.','; } echo PHP_EOL; echo 'volunteers: '.User::role('volunteer')->count().PHP_EOL; echo 'org users: '.User::role('org')->count().PHP_EOL;"

# — Domain tables present (opportunities, applicants, attendance, certificates, qr)
php -r "require 'vendor/autoload.php'; \$app=require 'bootstrap/app.php'; \$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap(); use Illuminate\Support\Facades\DB; \$rows=DB::select('SHOW TABLES'); \$names=array_map('current', \$rows); foreach(['opportun','applic','attend','cert','qr','profile'] as \$k){ \$hit=array_values(array_filter(\$names, fn(\$t)=>stripos(\$t,\$k)!==false)); echo \$k.': '.(empty(\$hit)?'none':implode(',',\$hit)).PHP_EOL; }"

# — Routes of interest
php artisan route:list | sed 's/[[:space:]]\{2,\}/ /g' | grep -Ei '(login|register|password|verify|my/profile|opportun|org/|qr/verify|certificate|certificates|applic|attend' || true

# — Views presence
for f in resources/views/auth/login.blade.php resources/views/auth/register.blade.php resources/views/auth/forgot-password.blade.php resources/views/my/profile.blade.php resources/views/org/register.blade.php; do
  [ -f "$f" ] && echo "OK view $f" || echo "MISSING view $f"
done

# — PWA & i18n
[ -f public/manifest.json ] && echo "OK PWA manifest" || echo "MISSING manifest"
[ -f public/service-worker.js ] && echo "OK service worker" || echo "MISSING service worker"
[ -d resources/lang/ar ] && echo "OK AR translations dir" || echo "MISSING AR translations dir"

# — Queue & mail
php -r "require 'vendor/autoload.php'; \$app=require 'bootstrap/app.php'; \$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap(); echo 'queue='.config('queue.default').PHP_EOL; echo 'mail='.config('mail.mailer').PHP_EOL;"

# — HTTP probes (public paths)
for u in / /login /register /forgot-password /my/profile /opportunities /qr/verify /org/login /org/register; do
  code=$(curl -s -o /dev/null -w "%{http_code}" "https://swaeduae.ae$u"); printf ' %s -> %s\n' "$u" "$code";
done

echo "=== END AUDIT ==="
