#!/usr/bin/env bash
set -euo pipefail
APP="/home3/vminingc/swaeduae.ae/laravel-app"
cd "$APP"
TS=$(date +%F-%H%M%S)

echo "== Phase 5: auth hardening =="

# --- A) Honeypot middleware ---
mkdir -p app/Http/Middleware
cat > app/Http/Middleware/Honeypot.php <<'PHP'
<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Honeypot
{
    /** Minimum seconds between form render and submit */
    private int $minDelay = 3;

    public function handle(Request $request, Closure $next): Response
    {
        // Only guard POST requests
        if ($request->isMethod('post')) {
            $trap = trim((string)$request->input('_hp', ''));
            $ts   = (int)$request->input('_hpt', 0);
            $age  = time() - $ts;

            // Bot filled the hidden field OR submitted too fast
            if ($trap !== '' || $age < $this->minDelay) {
                // Optionally log here; we just abort quietly
                abort(422, 'Unprocessable request');
            }
        }
        return $next($request);
    }
}
PHP
echo "[OK] Honeypot middleware created"

# Register alias in Kernel
K="app/Http/Kernel.php"
cp -a "$K" "$K.bak_$TS"
php -r '
$k="app/Http/Kernel.php"; $s=file_get_contents($k);
if (strpos($s, "'\''honeypot'\''")===false) {
  $s=preg_replace("/(protected\s+\$middlewareAliases\s*=\s*\[)/",
    "$1\n        '\''honeypot'\'' => \\\\App\\\\Http\\\\Middleware\\\\Honeypot::class,",
    $s, 1);
  file_put_contents($k,$s);
  echo "[OK] Kernel alias added\n";
} else { echo "[SKIP] Kernel alias exists\n"; }
'

# --- B) Blade partial for honeypot fields ---
mkdir -p resources/views/components
cat > resources/views/components/honeypot.blade.php <<'BLADE'
{{-- Simple, CSS-hidden trap + render timestamp --}}
<input type="text" name="_hp" value="" autocomplete="off" tabindex="-1"
       style="position:absolute;left:-10000px;top:auto;width:1px;height:1px;overflow:hidden" aria-hidden="true">
<input type="hidden" name="_hpt" value="{{ now()->timestamp }}">
BLADE
echo "[OK] Blade honeypot component added"

# Try to inject into common forms (best-effort, safe if not present)
for f in resources/views/auth/login.blade.php resources/views/auth/register.blade.php resources/views/public/contact.blade.php; do
  if [ -f "$f" ] && ! grep -q "components/honeypot" "$f"; then
    cp -a "$f" "$f.bak_$TS"
    # insert before closing </form>
    sed -i '0,/<\/form>/s//\ \ \ \ @include("components.honeypot")\n<\/form>/' "$f" \
      && echo "[OK] Honeypot included in $f" || echo "[WARN] Could not inject into $f"
  fi
done

# --- C) Named rate limiter for login ---
mkdir -p app/Providers
cat > app/Providers/RateLimitServiceProvider.php <<'PHP'
<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiting\Limit;

class RateLimitServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // 5 attempts per minute per IP+email; 1 minute decay
        RateLimiter::for('login', function (Request $request) {
            $key = sha1(($request->input('email') ?? 'guest').'|'.$request->ip());
            return [ Limit::perMinute(5)->by($key)->response(function() {
                return response('Too many login attempts. Please try again in a minute.', 429);
            }) ];
        });
    }
}
PHP
echo "[OK] RateLimitServiceProvider created"

# Ensure provider is registered
CFG="config/app.php"
if grep -q "RateLimitServiceProvider" "$CFG"; then
  echo "[SKIP] Provider already in config/app.php"
else
  cp -a "$CFG" "$CFG.bak_$TS"
  php -r '
  $f="config/app.php"; $s=file_get_contents($f);
  $s=preg_replace("/(\'providers\'\s*=>\s*\[)/","$1\n        \App\Providers\RateLimitServiceProvider::class,", $s,1);
  file_put_contents($f,$s);
  echo "[OK] Provider registered in config/app.php\n";
  '
fi

# --- D) Add throttle + honeypot to routes (login/register/contact) ---
# Patch any Route::post('/login' ...) to add middleware if missing
for R in routes/web.php routes/auth.php; do
  [ -f "$R" ] || continue
  cp -a "$R" "$R.bak_$TS"
  php -r '
    $f="'$R'"; $s=file_get_contents($f);
    // login
    $s=preg_replace(
      "/Route::post\((\s*[\'\"]\/login[\'\"].*?)(\)\s*;)/s",
      "Route::post($1->middleware(['honeypot','throttle:login'])$2",
      $s, -1, $c1
    );
    // register
    $s=preg_replace(
      "/Route::post\((\s*[\'\"]\/register[\'\"].*?)(\)\s*;)/s",
      "Route::post($1->middleware(['honeypot'])$2",
      $s, -1, $c2
    );
    // contact form (best-effort)
    $s=preg_replace(
      "/Route::post\((\s*[\'\"]\/contact[\'\"].*?)(\)\s*;)/s",
      "Route::post($1->middleware(['honeypot'])$2",
      $s, -1, $c3
    );
    file_put_contents($f,$s);
    echo "[OK] Patched $f (login:$c1, register:$c2, contact:$c3)\n";
  '
done

# --- E) Log auth events into audit_logs (if table exists) ---
if php artisan db:table audit_logs >/dev/null 2>&1; then
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

  EP="app/Providers/EventServiceProvider.php"
  cp -a "$EP" "$EP.bak_$TS"
  php -r '
    $f="app/Providers/EventServiceProvider.php"; $s=file_get_contents($f);
    if (strpos($s,"LogAuthEvents::class")===false) {
      $s=preg_replace("/use Illuminate\\\\Support\\\\Facades\\\\Event;\\n/",
        "use Illuminate\\\\Support\\\\Facades\\\\Event;\\nuse App\\\\Listeners\\\\LogAuthEvents;\\n", $s,1);
      $s=preg_replace("/public function boot\\(\\): void\\n\\s*\\{/",
        "public function boot(): void\n    {\n        Event::listen(\\Illuminate\\\\Auth\\\\Events\\\\Login::class, [LogAuthEvents::class, 'handle']);\n        Event::listen(\\Illuminate\\\\Auth\\\\Events\\\\Failed::class, [LogAuthEvents::class, 'handle']);\n", $s,1);
      file_put_contents($f,$s);
      echo "[OK] Event listeners wired\n";
    } else { echo "[SKIP] Event listeners already present\n"; }
  '
else
  echo "[SKIP] audit_logs table not found; skipping auth event logs"
fi

# --- F) Rebuild caches ---
/usr/local/bin/php artisan optimize:clear >/dev/null
/usr/local/bin/php artisan config:cache >/dev/null
/usr/local/bin/php artisan route:cache  >/dev/null
/usr/local/bin/php artisan view:cache   >/dev/null
echo "[OK] Laravel caches rebuilt"

echo "== Phase 5 auth hardening: DONE =="
