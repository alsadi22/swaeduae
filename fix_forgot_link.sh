#!/usr/bin/env bash
set -euo pipefail
F=resources/views/auth/partials/login-form.blade.php

echo "=== BEFORE (context) ==="
nl -ba "$F" | sed -n "$(grep -n -m1 -E 'password\.request|\?Forgot|Forgot your password' "$F" | head -n1 | cut -d: -f1 | awk '{s=$1-5;if(s<1)s=1;print s","$1+12}')p"

cp "$F" "$F.bak.$(date +%s)"

# Replace bad snippet with a correct Blade @if block, and normalize the trailing question mark
perl -0777 -i -pe '
  $snip = qq{\n@if (Route::has("password.request"))\n  <div class="mt-3 text-center">\n    <a class="small text-muted" href="{{ route("password.request") }}">{{ __("Forgot your password?") }}</a>\n  </div>\n@endif\n};
  s/\?\s*Forgot your password/Forgot your password?/g;
  s/\@\s*\(Route::has\([\"\x27]password\.request[\"\x27]\)\)/@if (Route::has("password.request"))/g;  # fix "@(..."
  s/^\s*\(\s*Route::has\([\"\x27]password\.request[\"\x27]\)\s*\)\s*$//mg;                           # remove lone "(Route::has(...))" lines
  # ensure closing @endif exists after the link block
  if ($_ !~ /password\.request.*?@endif/s) {
    s!(href=\{\{\s*route\([\"\x27]password\.request[\"\x27]\)\s*\}\}[^\>]*\>.*?</a>\s*</div>\s*)!$1\n@endif\n!s;
  }
' "$F"

echo "=== AFTER (context) ==="
nl -ba "$F" | sed -n "$(grep -n -m1 -E 'password\.request|Forgot your password' "$F" | head -n1 | cut -d: -f1 | awk '{s=$1-5;if(s<1)s=1;print s","$1+12}')p"

php artisan view:clear >/dev/null
php artisan view:cache >/dev/null

# Verify the rendered HTML
php -r '
require "vendor/autoload.php"; $app=require "bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$r=app(Illuminate\Contracts\Http\Kernel::class)->handle(Illuminate\Http\Request::create("/login","GET"));
$ok = (strpos($r->getContent(),"password/reset")!==false) && (strpos($r->getContent(),"(Route::has")===false);
echo $ok ? "OK: link renders and no raw Route::has text\n" : "STILL WRONG: inspect the login views\n";
'
