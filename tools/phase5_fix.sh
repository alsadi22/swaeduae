#!/usr/bin/env bash
set -euo pipefail
APP="/home3/vminingc/swaeduae.ae/laravel-app"
cd "$APP"
TS=$(date +%F-%H%M%S)

echo "== Phase 5 fix =="

# 1) Register RateLimitServiceProvider (safe PHP helper to avoid shell quoting)
cat > tools/_register_provider.php <<'PHP'
<?php
$f='config/app.php';
$s=file_get_contents($f);
if ($s===false) {fwrite(STDERR,"[ERR] cannot read $f\n"); exit(1);}
if (strpos($s,'RateLimitServiceProvider::class')===false) {
  $s=preg_replace("/('providers'\s*=>\s*\[)/","$1\n        \\\\App\\\\Providers\\\\RateLimitServiceProvider::class,", $s,1);
  file_put_contents($f,$s);
  echo "[OK] Provider registered\n";
} else {
  echo "[SKIP] Provider already registered\n";
}
PHP
/usr/local/bin/php tools/_register_provider.php
rm -f tools/_register_provider.php

# 2) Patch routes to add honeypot + throttle (best-effort on common files)
cat > tools/_patch_routes.php <<'PHP'
<?php
$files = array_filter(['routes/web.php','routes/auth.php'], 'file_exists');
foreach ($files as $f) {
  $s=file_get_contents($f); $orig=$s;
  // login
  $s=preg_replace(
    "/Route::post\\((\\s*['\"]\\/login['\"][^;]*?)(\\)\\s*;)/s",
    "Route::post($1->middleware(['honeypot','throttle:login'])$2",
    $s, -1, $c1
  );
  // register
  $s=preg_replace(
    "/Route::post\\((\\s*['\"]\\/register['\"][^;]*?)(\\)\\s*;)/s",
    "Route::post($1->middleware(['honeypot'])$2",
    $s, -1, $c2
  );
  // contact
  $s=preg_replace(
    "/Route::post\\((\\s*['\"]\\/contact['\"][^;]*?)(\\)\\s*;)/s",
    "Route::post($1->middleware(['honeypot'])$2",
    $s, -1, $c3
  );
  if ($s !== $orig) { copy($f,"$f.bak_".date('Ymd_His')); file_put_contents($f,$s); }
  echo "[OK] $f (login:$c1 register:$c2 contact:$c3)\n";
}
PHP
/usr/local/bin/php tools/_patch_routes.php
rm -f tools/_patch_routes.php

# 3) Wire auth event logging if audit_logs exists
if /usr/local/bin/php artisan db:table audit_logs >/dev/null 2>&1; then
  mkdir -p app/Listeners
  cat > app/Listeners/LogAuthEvents.php <<'PHP'
<?php
namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Failed;
use Illuminate\Support\Facades\DB;

class LogAuthEvents
{
    public function handle($event): void
    {
        if ($event instanceof Login) {
            DB::table('audit_logs')->insert([
                'user_id'    => $event->user->id ?? null,
                'action'     => 'login.success',
                'ip_address' => request()->ip(),
                'meta'       => json_encode(['email' => $event->user->email ?? null]),
                'created_at' => now(), 'updated_at' => now(),
            ]);
        } elseif ($event instanceof Failed) {
            DB::table('audit_logs')->insert([
                'user_id'    => null,
                'action'     => 'login.failed',
                'ip_address' => request()->ip(),
                'meta'       => json_encode(['email' => $event->credentials['email'] ?? null]),
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }
    }
}
PHP

  cp -a app/Providers/EventServiceProvider.php app/Providers/EventServiceProvider.php.bak_$TS
  /usr/local/bin/php <<'PHP'
<?php
$f='app/Providers/EventServiceProvider.php';
$s=file_get_contents($f);
if (strpos($s,"use App\\Listeners\\LogAuthEvents;")===false){
  $s=preg_replace("/use Illuminate\\\\Support\\\\Facades\\\\Event;\\n/","use Illuminate\\\\Support\\\\Facades\\\\Event;\nuse App\\\\Listeners\\\\LogAuthEvents;\n",$s,1);
}
if (strpos($s,"LogAuthEvents::class")===false){
  $s=preg_replace("/public function boot\\(\\): void\\s*\\{/",
    "public function boot(): void\n    {\n        Event::listen(\\Illuminate\\\\Auth\\\\Events\\\\Login::class, [LogAuthEvents::class, 'handle']);\n        Event::listen(\\Illuminate\\\\Auth\\\\Events\\\\Failed::class, [LogAuthEvents::class, 'handle']);\n",$s,1);
}
file_put_contents($f,$s);
echo "[OK] EventServiceProvider wired\n";
PHP
else
  echo "[SKIP] audit_logs table not found; skipping auth event logs"
fi

# 4) Rebuild caches
/usr/local/bin/php artisan optimize:clear >/dev/null
/usr/local/bin/php artisan config:cache >/dev/null
/usr/local/bin/php artisan route:cache  >/dev/null
/usr/local/bin/php artisan view:cache   >/dev/null

# 5) Show quick view & routes confirmation
echo "---- honeypot includes (views) ----"
grep -RIn "components.honeypot" resources/views/auth/login.blade.php resources/views/auth/register.blade.php resources/views/public/contact.blade.php 2>/dev/null || true
echo "---- routes (login/register/contact) ----"
/usr/local/bin/php artisan route:list --columns=Method,URI,Name,Middleware | egrep -i "login|register|contact" || true

echo "== Phase 5 fix done =="
