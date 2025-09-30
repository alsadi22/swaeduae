#!/usr/bin/env bash
PHP_BIN=${PHP_BIN:-php}
set -euo pipefail

UPCOMING="resources/views/org/partials/upcoming_7d.blade.php"
DASHV1="resources/views/org/partials/dashboard_v1.blade.php"

[ -f "$UPCOMING" ] || { echo "Missing $UPCOMING"; exit 1; }
[ -f "$DASHV1" ]   || { echo "Missing $DASHV1"; exit 1; }

cp "$UPCOMING" "${UPCOMING}.bak_$(date +%F_%H%M%S)"
cp "$DASHV1"   "${DASHV1}.bak_$(date +%F_%H%M%S)"

# ---------- Patch upcoming_7d: make $upcoming/$list safe Collections ----------
php <<'PHP'
<?php
$part = "resources/views/org/partials/upcoming_7d.blade.php";
$src  = file_get_contents($part);

// Ensure global safe defaults include
if (strpos($src, "org.dashboard._safe_defaults") === false) {
  $src = "@include('org.dashboard._safe_defaults')\n".$src;
}

// Guard block (no instanceof to avoid class name typos)
$guard = "@php /*__UPCOMING7D_GUARDS__*/ ".
         "\$upcoming = \$upcoming ?? []; ".
         "\$list = \$list ?? \$upcoming; ".
         "\$list = collect(\$list); ".
         "\$list_count = \$list->count(); ".
         "@endphp";

// Replace existing guard or insert a fresh one at the top
if (preg_match("/@php[^@]*__UPCOMING7D_GUARDS__[^@]*@endphp/s", $src)) {
  $src = preg_replace("/@php[^@]*__UPCOMING7D_GUARDS__[^@]*@endphp/s", $guard, $src);
} else {
  $src = $guard . "\n" . $src;
}

// If template ever echoes {{ $list }} or {{ $upcoming }}, switch to a safe scalar
$src = preg_replace("/{{\s*\$list\s*}}|{{\s*\$upcoming\s*}}/", "{{ \$list_count }}", $src);

file_put_contents($part, $src);
echo "Patched: $part\n";
PHP

# ---------- Re-assert KPI guards in dashboard_v1 ----------
php <<'PHP'
<?php
$part = "resources/views/org/partials/dashboard_v1.blade.php";
$src  = file_get_contents($part);

$guard = "@php /*__ORG_GUARDS__*/ ".
         "\$appsTotal=\$appsTotal??0; ".
         "\$appsPending=\$appsPending??0; ".
         "\$appsApproved=\$appsApproved??0; ".
         "\$checkinsToday=\$checkinsToday??0; ".
         "@endphp";

if (preg_match("/@php[^@]*__ORG_GUARDS__[^@]*@endphp/s", $src)) {
  $src = preg_replace("/@php[^@]*__ORG_GUARDS__[^@]*@endphp/s", $guard, $src, 1);
} else {
  $src = $guard . "\n" . $src;
}

file_put_contents($part, $src);
echo "Patched: $part\n";
PHP

echo "Rebuilding Blade cache…"
php artisan view:clear && php artisan view:cache

echo
echo "Dashboard smoke test:"
php tools/check_org_dashboard.php || true
