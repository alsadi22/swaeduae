PHP_BIN=${PHP_BIN:-php}
#!/usr/bin/env bash
set -euo pipefail

HCH="resources/views/org/partials/hours_chart.blade.php"
KPI="resources/views/org/dashboard/_kpis.blade.php"

[ -f "$HCH" ] || { echo "Missing $HCH"; exit 1; }
[ -f "$KPI" ] || { echo "Missing $KPI"; exit 1; }

cp "$HCH" "${HCH}.bak_$(date +%F_%H%M%S)"
cp "$KPI" "${KPI}.bak_$(date +%F_%H%M%S)"

php <<'PHP'
<?php
// ---- hours_chart: alias both naming styles + safe defaults
$hch = "resources/views/org/partials/hours_chart.blade.php";
$src = file_get_contents($hch);

if (strpos($src, "org.dashboard._safe_defaults") === false) {
  $src = "@include('org.dashboard._safe_defaults')\n".$src;
}

$marker = "__HOURS_CHART_EXTRA_GUARDS__";
$guard  = "@php /*{$marker}*/ ".
          // Accept either naming style as source
          "\$hoursChart = is_array(\$hoursChart ?? null) ? \$hoursChart : (is_array(\$hoursSeries ?? null) ? \$hoursSeries : []); ".
          "\$labels     = is_array(\$labels ?? null) ? \$labels : (is_array(\$monthLabels ?? null) ? \$monthLabels : []); ".
          // Provide both names for the template to use
          "\$hoursSeries  = \$hoursChart; ".
          "\$monthLabels  = \$labels; ".
          "\$hours        = (float) (\$hours ?? 0); ".
          "@endphp";

if (preg_match("/@php[^@]*{$marker}[^@]*@endphp/s", $src)) {
  $src = preg_replace("/@php[^@]*{$marker}[^@]*@endphp/s", $guard, $src, 1);
} else {
  $src = $guard . "\n" . $src;
}
file_put_contents($hch, $src);
echo "Hardened: $hch\n";

// ---- _kpis: ensure keys/values always present
$kpi = "resources/views/org/dashboard/_kpis.blade.php";
$src = file_get_contents($kpi);

if (strpos($src, "org.dashboard._safe_defaults") === false) {
  $src = "@include('org.dashboard._safe_defaults')\n".$src;
}

$marker = "__KPIS_GUARDS__";
$guard  = "@php /*{$marker}*/ ".
          "\$kpis = is_array(\$kpis ?? null) ? \$kpis : []; ".
          "\$kpis = array_replace(['volunteers'=>0,'events'=>0,'hours'=>0,'applications'=>0], \$kpis); ".
          "\$volunteersHosted   = (int) (\$volunteersHosted   ?? (\$kpis['volunteers']   ?? 0)); ".
          "\$totalHours         = (float)(\$totalHours         ?? (\$kpis['hours']        ?? 0)); ".
          "\$upcomingOpps       = (int) (\$upcomingOpps       ?? 0); ".
          "\$certificatesIssued = (int) (\$certificatesIssued ?? 0); ".
          "@endphp";

if (preg_match("/@php[^@]*{$marker}[^@]*@endphp/s", $src)) {
  $src = preg_replace("/@php[^@]*{$marker}[^@]*@endphp/s", $guard, $src, 1);
} else {
  $src = $guard . "\n" . $src;
}
file_put_contents($kpi, $src);
echo "Hardened: $kpi\n";
PHP

echo "Rebuilding Blade cache…"
php artisan view:clear && php artisan view:cache

echo
echo "Dashboard smoke test:"
php tools/check_org_dashboard.php || true

echo
echo "If still failing, last errors:"
( tail -n 400 "storage/logs/laravel-$(date +%F).log" 2>/dev/null || tail -n 400 storage/logs/laravel.log 2>/dev/null ) \
 | egrep -i 'org|dashboard|ViewException|Undefined|Trying|exception' | tail -n 80 || true
