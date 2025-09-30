#!/usr/bin/env bash
PHP_BIN=${PHP_BIN:-php}
set -euo pipefail
STAMP="$(date +%F_%H%M%S)"

CFG="config/app.php"
cp -a "$CFG" "$CFG.bak_$STAMP"

# 1) Ensure fallback locale is English
sed -i -E "s/('fallback_locale'\s*=>\s*)'[^']*'/\1'en'/" "$CFG"

# 2) Minimal English translations
mkdir -p resources/lang/en
cat > resources/lang/en/app.php <<'PHP'
<?php
return [
  'site' => ['name' => 'Find Volunteer Opportunities in the UAE'],
  'nav'  => ['home' => 'Home'],
  'auth' => ['signin' => 'Sign in', 'register' => 'Register'],
];
PHP

# 3) Mirror to Arabic if missing (so 'ar' won’t show raw keys)
if [ ! -f resources/lang/ar/app.php ]; then
  mkdir -p resources/lang/ar
  cp resources/lang/en/app.php resources/lang/ar/app.php
fi

# 4) Clear caches
php artisan config:clear >/dev/null || true
php artisan cache:clear  >/dev/null || true
php artisan view:clear   >/dev/null || true
php artisan view:cache   >/dev/null || true

echo "✅ Translations in place. Backup: $CFG.bak_$STAMP"
