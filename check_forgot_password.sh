#!/usr/bin/env bash
set -euo pipefail
cd "${APP_ROOT:-$( [ -f artisan ] && echo . || echo laravel-app )}"

echo "=== Routes for password reset ==="
php artisan route:list | egrep -i 'password\.(request|email|reset|update)|password/reset|forgot-password' || true

echo
echo "=== Programmatic route existence ==="
php -r 'require "vendor/autoload.php"; $app=require "bootstrap/app.php"; $app->make(Illuminate\\Contracts\\Console\\Kernel::class)->bootstrap(); foreach(["password.request","password.email","password.reset","password.update","password.store"] as $n){ echo $n,": ", Illuminate\\Support\\Facades\\Route::has($n)?"yes":"no", "\\n"; }'

echo
echo "=== Views that usually hold the link ==="
ls -la resources/views/auth 2>/dev/null || true
ls -la resources/views/auth/passwords 2>/dev/null || true
ls -la resources/views/auth | egrep -i 'forgot|reset' 2>/dev/null || true

echo
echo "=== DB tokens table present? ==="
php -r 'require "vendor/autoload.php"; $app=require "bootstrap/app.php"; $app->make(Illuminate\\Contracts\\Console\\Kernel::class)->bootstrap(); $S=Illuminate\\Support\\Facades\\Schema::class; echo "password_resets: ".($S::hasTable("password_resets")?"yes":"no")."\\npassword_reset_tokens: ".($S::hasTable("password_reset_tokens")?"yes":"no")."\\n";'

echo
echo "=== Mail config (needed to send reset email) ==="
php -r 'require "vendor/autoload.php"; $app=require "bootstrap/app.php"; $app->make(Illuminate\\Contracts\\Console\\Kernel::class)->bootstrap(); echo "mail.default=".config("mail.default")." from.address=".config("mail.from.address")."\\n";'

echo
echo "=== Likely login blades (to add the link if missing) ==="
grep -RIl --include='*.blade.php' -E '/login|route\(.+login' resources/views 2>/dev/null || true
