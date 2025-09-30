#!/usr/bin/env bash
PHP_BIN=${PHP_BIN:-php}
set -euo pipefail
cd /var/www/swaeduae

print_routes_php='
require "vendor/autoload.php";
$app = require "bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
foreach (app("router")->getRoutes() as $r) { echo ltrim($r->uri(), "/"), PHP_EOL; }'

if php artisan route:list --help 2>/dev/null | grep -q -- '--json' && command -v jq >/dev/null 2>&1; then
  uris=$(php artisan route:list --json | jq -r ".[].uri")
else
  uris=$(php -r "$print_routes_php")
fi

echo "$uris" | while IFS= read -r p; do
  p="$(printf '%s' "$p" | tr -d '\r' | sed 's/^[[:space:]]\+//; s/[[:space:]]\+$//')"
  [ -z "$p" ] && continue
  [ "$p" = "/" ] && continue
  printf '%s' "$p" | grep -q '{' && continue
  case "$p" in js*|css*|img*|images*|storage*) continue ;; esac
  [ -d "public/$p" ] && echo "WARNING: public/$p shadows a route"
done
