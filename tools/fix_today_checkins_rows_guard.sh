#!/usr/bin/env bash
set -euo pipefail
PART="resources/views/org/partials/today_checkins.blade.php"
[ -f "$PART" ] || { echo "Missing $PART"; exit 1; }
cp "$PART" "${PART}.bak_$(date +%F_%H%M%S)"

php <<'PHP'
<?php
$part = "resources/views/org/partials/today_checkins.blade.php";
$src  = file_get_contents($part);

// 1) Ensure global safe defaults are available
if (strpos($src, "org.dashboard._safe_defaults") === false) {
  $src = "@include('org.dashboard._safe_defaults')\n".$src;
}

// 2) Strong, idempotent guard at the very top
$marker = "__TODAY_CHECKINS_GUARDS__";
$guard  = "@php /*{$marker}*/ ".
          "\$rows = collect(\$rows ?? []); ".
          "\$rows_count = \$rows->count(); ".
          "@endphp";

if (preg_match("/@php[^@]*{$marker}[^@]*@endphp/s", $src)) {
  $src = preg_replace("/@php[^@]*{$marker}[^@]*@endphp/s", $guard, $src, 1);
} else {
  $src = $guard . "\n" . $src;
}

// 3) If template ever echoes {{ $rows }}, show a safe scalar
$src = preg_replace("/{{\s*\$rows\s*}}/", "{{ \$rows_count }}", $src);

file_put_contents($part, $src);
echo "Patched: $part\n";
PHP

echo "Rebuilding Blade cache…"
php artisan view:clear && php artisan view:cache

echo
echo "Dashboard smoke test:"
php tools/check_org_dashboard.php || true
