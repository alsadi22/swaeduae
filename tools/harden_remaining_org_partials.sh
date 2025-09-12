#!/usr/bin/env bash
set -euo pipefail

declare -A PARTS=(
  ["apps_vs_attend"]="resources/views/org/partials/apps_vs_attend.blade.php"
  ["hours_chart"]="resources/views/org/partials/hours_chart.blade.php"
  ["recent_activity"]="resources/views/org/partials/recent_activity.blade.php"
)

for key in "${!PARTS[@]}"; do
  f="${PARTS[$key]}"
  [ -f "$f" ] || { echo "Missing $f"; exit 1; }
  cp "$f" "${f}.bak_$(date +%F_%H%M%S)"

  php <<'PHP'
<?php
$key = getenv('KEY');
$f   = getenv('FILE');
$src = file_get_contents($f);

// 1) Ensure global defaults available
if (strpos($src, "org.dashboard._safe_defaults") === false) {
  $src = "@include('org.dashboard._safe_defaults')\n".$src;
}

// 2) Inject per-file guard (idempotent replacement)
switch ($key) {
  case "apps_vs_attend":
    $marker = "__APPS_ATTEND_GUARDS__";
    $guard  = "@php /*__APPS_ATTEND_GUARDS__*/ ".
              "\$appAttend = is_array(\$appAttend ?? null) ? \$appAttend : []; ".
              "\$appAttend = array_merge(['labels'=>[], 'apps'=>[], 'attend'=>[]], \$appAttend); ".
              "\$aa_labels = \$appAttend['labels']; ".
              "\$aa_apps   = \$appAttend['apps']; ".
              "\$aa_attend = \$appAttend['attend']; ".
              "@endphp";
    break;

  case "hours_chart":
    $marker = "__HOURS_CHART_GUARDS__";
    $guard  = "@php /*__HOURS_CHART_GUARDS__*/ ".
              "\$hoursChart = is_array(\$hoursChart ?? null) ? \$hoursChart : []; ".
              "\$labels     = is_array(\$labels ?? null) ? \$labels : []; ".
              "\$hours      = (float) (\$hours ?? 0); ".
              "@endphp";
    break;

  case "recent_activity":
    $marker = "__RECENT_ACTIVITY_GUARDS__";
    $guard  = "@php /*__RECENT_ACTIVITY_GUARDS__*/ ".
              "\$recentActivity = collect(\$recentActivity ?? []); ".
              "@endphp";
    break;

  default:
    exit(0);
}

if (preg_match("/@php[^@]*$marker[^@]*@endphp/s", $src)) {
  $src = preg_replace("/@php[^@]*$marker[^@]*@endphp/s", $guard, $src, 1);
} else {
  $src = $guard . "\n" . $src;
}

file_put_contents($f, $src);
echo "Hardened: $f\n";
PHP
done

echo "Rebuilding Blade cache…"
php artisan view:clear && php artisan view:cache

echo
echo "Dashboard smoke test:"
php tools/check_org_dashboard.php || true

echo
echo "If still 500, show last errors:"
( tail -n 400 "storage/logs/laravel-$(date +%F).log" 2>/dev/null || tail -n 400 storage/logs/laravel.log 2>/dev/null ) \
 | egrep -i 'org|dashboard|ViewException|Undefined|Trying|exception' | tail -n 80 || true
