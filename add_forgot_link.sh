#!/usr/bin/env bash
set -euo pipefail
cd "${APP_ROOT:-$( [ -f artisan ] && echo . || echo laravel-app )}"

CANDS=$(grep -RIl --include='*.blade.php' -E "route\(['\"]login['\"]\)|action=['\"][^\"']*/login" resources/views || true)

for f in $CANDS; do
  if ! grep -q "password\.request" "$f"; then
    cp "$f" "$f.bak.$(date +%s)"
    perl -0777 -pe 'BEGIN{
      $snip = qq{\n    @if (Route::has(\'password.request\'))\n      <div class="mt-2">\n        <a href="{{ route(\'password.request\') }}" class="small text-muted">{{ __(\'Forgot your password?\') }}</a>\n      </div>\n    @endif\n  };
    } s!(</form>)!$snip\n$1!s' -i "$f"
    echo "Patched: $f"
  else
    echo "Already contains a reset link: $f"
  fi
done

php artisan view:clear >/dev/null 2>&1 || true
php artisan view:cache >/dev/null 2>&1 || true
echo "Done."
