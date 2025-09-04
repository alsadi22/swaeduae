#!/usr/bin/env bash
set -euo pipefail
set +H

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
cd "$ROOT"
PHP_BIN="${PHP_BIN:-/opt/alt/php84/usr/bin/php}"; [ -x "$PHP_BIN" ] || PHP_BIN="php"
COMPOSER_BIN="${COMPOSER_BIN:-/opt/cpanel/composer/bin/composer}"; command -v "$COMPOSER_BIN" >/dev/null 2>&1 || COMPOSER_BIN="composer"

pass(){ printf "PASS  %s\n" "$1"; }
miss(){ printf "MISS  %s\n" "$1"; }

echo "== finalize start =="

# A) SEO env defaults (safe idempotent)
ensure_kv(){ KEY="$1"; VAL="$2"; if grep -q "^${KEY}=" .env; then sed -i.bak."$(date +%F_%H%M%S)" "s#^${KEY}=.*#${KEY}=${VAL}#" .env; else echo "${KEY}=${VAL}" >> .env; fi; }
ensure_kv SEO_SITE_NAME "SWAED UAE"
ensure_kv SEO_DEFAULT_TITLE "SWAED UAE"
ensure_kv SEO_DEFAULT_DESCRIPTION "Volunteer opportunities, organizations and certificates in the UAE."
ensure_kv SEO_TWITTER_HANDLE "@swaeduae"

# B) robots.txt (ensure sitemap hint)
bk public/robots.txt
if grep -q 'Sitemap: https://swaeduae.ae/sitemap.xml' public/robots.txt 2>/dev/null; then
  pass "robots.txt has sitemap"
else
  cat > public/robots.txt <<'TXT'
User-agent: *
Disallow:
Sitemap: https://swaeduae.ae/sitemap.xml
TXT
  pass "robots.txt written"
fi

# C) spatie sitemap package present
if "$PHP_BIN" -r 'require "vendor/autoload.php"; exit(class_exists("Spatie\\Sitemap\\SitemapGenerator")?0:1);'; then
  pass "spatie/laravel-sitemap present"
else
  miss "spatie/laravel-sitemap missing; installing"
  "$COMPOSER_BIN" require spatie/laravel-sitemap:^7 --no-dev --optimize-autoloader --no-interaction --no-progress
fi

# D) GenerateSitemap command file (do not overwrite if exists)
if [ -f app/Console/Commands/GenerateSitemap.php ]; then
  pass "GenerateSitemap command exists"
else
  bk app/Console/Commands/GenerateSitemap.php
  cat > app/Console/Commands/GenerateSitemap.php <<'PHP'
<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use Spatie\Sitemap\SitemapGenerator;

class GenerateSitemap extends Command
{
    protected $signature = 'app:generate-sitemap {--base-url=}';
    protected $description = 'Generate sitemap.xml into public directory';

    public function handle(): int
    {
        $base = $this->option('base-url') ?: config('app.url');
        if (!$base) { $this->error('APP_URL not set and --base-url not provided.'); return self::FAILURE; }
        SitemapGenerator::create($base)->writeToFile(public_path('sitemap.xml'));
        $this->info("Sitemap generated: ".public_path('sitemap.xml')." for {$base}");
        return self::SUCCESS;
    }
}
PHP
  pass "GenerateSitemap command created"
fi

# E) Ensure command is registered (after bootstrapping)
if "$PHP_BIN" -r 'require "vendor/autoload.php"; $app=require "bootstrap/app.php"; $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap(); exit(array_key_exists("app:generate-sitemap", \Artisan::all())?0:1);'; then
  pass "artisan command registered"
else
  miss "artisan command not yet registered (will rebuild caches now)"
fi

# F) Add daily schedule at 02:20 if missing
if grep -q "app:generate-sitemap" app/Console/Kernel.php; then
  pass "Kernel has sitemap schedule"
else
  bk app/Console/Kernel.php
  "$PHP_BIN" -r '
    $f="app/Console/Kernel.php"; $c=file_get_contents($f);
    if (strpos($c,"app:generate-sitemap")===false) {
      $c=preg_replace("/protected\s+function\s+schedule\(.*?\)\s*:\s*void\s*\{\s*/s",
        "$0\n        \$schedule->command(\"app:generate-sitemap\")->dailyAt(\"02:20\");\n", $c, 1);
      file_put_contents($f,$c);
      echo "scheduled\n";
    } else { echo "exists\n"; }
  ' | grep -q scheduled && pass "Kernel schedule added" || pass "Kernel schedule already present"
fi

# G) Canonical link middleware (web group)
if grep -q 'CanonicalLink::class' app/Http/Kernel.php; then
  pass "CanonicalLink already in web group"
else
  if [ ! -f app/Http/Middleware/CanonicalLink.php ]; then
    bk app/Http/Middleware/CanonicalLink.php
    cat > app/Http/Middleware/CanonicalLink.php <<'PHP'
<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;

class CanonicalLink
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        $ct = (string)($response->headers->get('Content-Type',''));
        if ($request->isMethod('GET') && str_contains($ct, 'text/html')) {
            $canonical = rtrim($request->url(), '/');
            if (method_exists($response, 'header')) {
                $response->header('Link', '<'.$canonical.'>; rel="canonical"');
            } else {
                $response->headers->set('Link', '<'.$canonical.'>; rel="canonical"', false);
            }
        }
        return $response;
    }
}
PHP
  fi
  bk app/Http/Kernel.php
  "$PHP_BIN" -r '
    $f="app/Http/Kernel.php"; $c=file_get_contents($f);
    if (strpos($c,"CanonicalLink::class")===false) {
      $c=preg_replace("/(protected\s+\$middlewareGroups\s*=\s*\[\s*'web'\s*=>\s*\[)/",
        "$1\n            \\\\App\\\\Http\\\\Middleware\\\\CanonicalLink::class,", $c, 1);
      file_put_contents($f,$c);
      echo "added\n";
    } else { echo "exists\n"; }
  ' | grep -q added && pass "CanonicalLink added to web group" || pass "CanonicalLink already present"
fi

# H) Warm caches (uses your existing script if present)
if [ -x tools/warm_cache.sh ]; then
  PHP_BIN="$PHP_BIN" bash tools/warm_cache.sh >/dev/null
  pass "caches warmed"
else
  $PHP_BIN artisan config:clear >/dev/null || true
  $PHP_BIN artisan cache:clear  >/dev/null || true
  $PHP_BIN artisan route:clear  >/dev/null || true
  $PHP_BIN artisan view:clear   >/dev/null || true
  $PHP_BIN artisan config:cache >/dev/null
  $PHP_BIN artisan route:cache  >/dev/null
  $PHP_BIN artisan view:cache   >/dev/null
  pass "caches rebuilt"
fi

# I) Generate sitemap now
if $PHP_BIN artisan app:generate-sitemap --base-url="$(grep -E '^APP_URL=' .env | cut -d= -f2)" >/dev/null 2>&1; then
  pass "sitemap generated"
else
  miss "sitemap generation failed (check APP_URL and package install)"
fi

# J) Quick HTTP header spot check (non-fatal)
curl -s -o /dev/null -D - https://swaeduae.ae/ | egrep -i 'Strict-Transport-Security|Content-Security-Policy|X-Frame-Options|X-Content-Type-Options|Referrer-Policy|Permissions-Policy' || true

echo "== finalize done =="
