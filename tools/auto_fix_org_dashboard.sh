#!/usr/bin/env bash
PHP_BIN=${PHP_BIN:-php}
set -euo pipefail

say(){ printf "\n== %s ==\n" "$*"; }

LAY="resources/views/org/layout.blade.php"
DASH="resources/views/org/dashboard.blade.php"
BRAND="public/css/brand.css"

# --- 1) Fix layout includes & assets
say "Repair layout includes & assets"
cp "$LAY" "${LAY}.bak_$(date +%F_%H%M%S)"

# Remove stray orphan lines like:  ('view.name')
sed -Ei "/^[[:space:]]*\('[^']+'\)[[:space:]]*$/d" "$LAY"

# Ensure includeIf lines exist (idempotent)
grep -q "org.argon._sidenav"  "$LAY" || sed -i "21i @includeIf('org.argon._sidenav')" "$LAY"
grep -q "admin.argon._navbar" "$LAY" || sed -i "28i @includeIf('admin.argon._navbar')" "$LAY"
grep -q "org.partials.menu"   "$LAY" || sed -i "45i @includeIf('org.partials.menu')" "$LAY"
grep -q "admin.argon._footer" "$LAY" || sed -i "51i @includeIf('admin.argon._footer')" "$LAY"

# Ensure Argon CSS/JS present
grep -q "argon-dashboard.min.css" "$LAY" || sed -i "14i <link id=\"pagestyle\" rel=\"stylesheet\" href=\"{{ asset('vendor/argon/assets/css/argon-dashboard.min.css') }}\">" "$LAY"
grep -q "argon-dashboard.min.js"  "$LAY" || sed -i "59i <script src=\"{{ asset('vendor/argon/assets/js/argon-dashboard.min.js') }}\"></script>" "$LAY"

# Make sure stacks exist
grep -q "@stack('head')"    "$LAY" || sed -i "17i @stack('head')" "$LAY"
grep -q "@stack('scripts')" "$LAY" || sed -i "61i @stack('scripts')" "$LAY"

# --- 2) Ensure dashboard skeleton and partials ordering
say "Normalize org/dashboard.blade.php"
cp "$DASH" "${DASH}.bak_$(date +%F_%H%M%S)"

php <<'PHP'
<?php
$dash = "resources/views/org/dashboard.blade.php";
$src  = file_get_contents($dash);

// Enforce extends + safe defaults include once
if (strpos($src, "@extends('org.layout')") === false) {
  $src = "@extends('org.layout')\n".$src;
}
if (strpos($src, "org.dashboard._safe_defaults") === false) {
  $src = preg_replace("/@extends\\(['\"]org\\.layout['\"]\\)/",
          "@extends('org.layout')\n@include('org.dashboard._safe_defaults')", $src, 1);
}

// Desired partial order
$want = [
  "org/dashboard/_kpis",
  "org/partials/apps_vs_attend",
  "org/partials/hours_chart",
  "org/partials/recent_activity",
  "org/partials/upcoming_7d",
  "org/partials/today_checkins",
];

// Remove dupes and re-append in order if missing
foreach ($want as $v) {
  $src = preg_replace("#@include\\(['\"]".preg_quote($v,'#')."['\"]\\)\\s*#","", $src);
}
$block = "";
foreach ($want as $v) $block .= "@include('".$v."')\n";
if (strpos($src, $block) === false) {
  // Put the block after the page header if present, else at bottom
  if (preg_match('/@section\\([\'"]content[\'"]\\)/', $src)) {
    $src = preg_replace('/@section\\([\'"]content[\'"]\\)/',
            "@section('content')\n".$block, $src, 1);
  } else {
    $src .= "\n".$block;
  }
}

file_put_contents($dash,$src);
echo "Dashboard normalized.\n";
PHP

# --- 3) Guards for all partials (idempotent)
say "Apply safe guards to partials"
guard_php () {
php <<'PHP'
<?php
$part = getenv('PART');
$marker = getenv('MARK');
$guard  = getenv('GUARD');

$src = file_get_contents($part);
if (strpos($src, "org.dashboard._safe_defaults") === false) {
  $src = "@include('org.dashboard._safe_defaults')\n".$src;
}
if (preg_match("/@php[^@]*{$marker}[^@]*@endphp/s",$src)) {
  $src = preg_replace("/@php[^@]*{$marker}[^@]*@endphp/s",$guard,$src,1);
} else {
  $src = $guard."\n".$src;
}
file_put_contents($part,$src);
echo "Guarded: $part\n";
PHP
}

export PART="resources/views/org/partials/dashboard_v1.blade.php"
export MARK="__ORG_GUARDS__"
export GUARD="@php /*__ORG_GUARDS__*/ \$appsTotal=\$appsTotal??0; \$appsPending=\$appsPending??0; \$appsApproved=\$appsApproved??0; \$checkinsToday=\$checkinsToday??0; @endphp"
guard_php

export PART="resources/views/org/partials/apps_vs_attend.blade.php"
export MARK="__APPS_ATTEND_GUARDS__"
export GUARD="@php /*__APPS_ATTEND_GUARDS__*/ \$appAttend = is_array(\$appAttend ?? null) ? \$appAttend : []; \$appAttend = array_merge(['labels'=>[], 'apps'=>[], 'attend'=>[]], \$appAttend); @endphp"
guard_php

export PART="resources/views/org/partials/hours_chart.blade.php"
export MARK="__HOURS_CHART_GUARDS__"
export GUARD="@php /*__HOURS_CHART_GUARDS__*/ \$hoursChart = is_array(\$hoursChart ?? null) ? \$hoursChart : []; \$labels = is_array(\$labels ?? null) ? \$labels : []; \$hours=(float)(\$hours ?? 0); @endphp"
guard_php

export PART="resources/views/org/partials/recent_activity.blade.php"
export MARK="__RECENT_ACTIVITY_GUARDS__"
export GUARD="@php /*__RECENT_ACTIVITY_GUARDS__*/ \$recentActivity = collect(\$recentActivity ?? []); @endphp"
guard_php

export PART="resources/views/org/partials/upcoming_7d.blade.php"
export MARK="__UPCOMING7D_GUARDS__"
export GUARD="@php /*__UPCOMING7D_GUARDS__*/ \$upcoming = \$upcoming ?? []; \$list = \$list ?? \$upcoming; \$list = collect(\$list); \$list_count=\$list->count(); @endphp"
guard_php

export PART="resources/views/org/partials/today_checkins.blade.php"
export MARK="__TODAY_CHECKINS_GUARDS__"
export GUARD="@php /*__TODAY_CHECKINS_GUARDS__*/ \$rows = collect(\$rows ?? []); \$rows_count=\$rows->count(); @endphp"
guard_php

# --- 4) Light visual polish (kept minimal & safe)
say "Polish brand.css"
mkdir -p "$(dirname "$BRAND")"
touch "$BRAND"
grep -q "/* dashboard-polish */" "$BRAND" || cat >> "$BRAND" <<'CSS'

/* dashboard-polish */
.card { border-radius: 14px; }
.card .card-body { padding: 1.1rem 1.25rem; }
.kpi .h3, .kpi .h2 { font-weight: 700; letter-spacing: .2px; }
.btn-group .btn, .nav .btn { border-radius: 10px !important; }
.list-clean { list-style: none; margin: 0; padding-left: 0; }
.badge.bg-primary-subtle { background: rgba(59,130,246,.12); color: #1e40af; }
CSS

# --- 5) Rebuild + audit
say "Rebuild Blade cache"
php artisan view:clear >/dev/null && php artisan view:cache >/dev/null

say "Run audit"
php tools/audit_org_dashboard.php || true
