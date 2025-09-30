PHP_BIN=${PHP_BIN:-php}
#!/usr/bin/env bash
set -euo pipefail
MAIN="resources/views/org/dashboard.blade.php"
PART="resources/views/org/partials/dashboard_v1.blade.php"

[ -f "$MAIN" ] || { echo "Missing $MAIN"; exit 1; }
[ -f "$PART" ] || { echo "Missing $PART"; exit 1; }

cp "$MAIN" "${MAIN}.bak_$(date +%F_%H%M%S)"
cp "$PART" "${PART}.bak_$(date +%F_%H%M%S)"

# 1) Make sure safe defaults are included ONCE right after @extends('org.layout')
if ! grep -q "@include('org.dashboard._safe_defaults')" "$MAIN"; then
  sed -i -E "/@extends\(['\"]org\.layout['\"]\)/a @include('org.dashboard._safe_defaults')" "$MAIN"
fi
# keep only the first occurrence if multiple slipped in
awk 'BEGIN{seen=0}
{
  if ($0 ~ /@include\((\"|\x27)org\.dashboard\._safe_defaults(\"|\x27)\)/) {
    if (seen) next; else seen=1
  }
  print
}' "$MAIN" > "$MAIN.tmp" && mv "$MAIN.tmp" "$MAIN"

# 2) Inline guards at top of the partial (idempotent)
if ! grep -q "__ORG_GUARDS__" "$PART"; then
  sed -i '1i @php /*__ORG_GUARDS__*/ $appsTotal=$appsTotal??0; $appsPending=$appsPending??0; $appsApproved=$appsApproved??0; $checkinsToday=$checkinsToday??0; @endphp' "$PART"
fi

# Also normalize "upcoming" display, just in case
if ! grep -q "__UPCOMING_DISPLAY__" "$PART"; then
  sed -i '1i @php /*__UPCOMING_DISPLAY__*/ $upcoming_display = isset($upcomingOpps) ? (int)$upcomingOpps : (is_countable($upcoming ?? null) ? count($upcoming) : (int)($upcoming ?? 0)); @endphp' "$PART"
  sed -i 's/{{[[:space:]]*\$upcoming[[:space:]]*}}/{{ $upcoming_display }}/g' "$PART"
fi

echo "Rebuilding Blade cache…"
php artisan view:clear && php artisan view:cache

echo "Re-checking:"
php tools/check_org_dashboard.php || true

echo
echo "If it still fails, show the compiled chunk for this partial:"
echo "php -r 'foreach (glob(\"storage/framework/views/*.php\") as \$f){if(strpos(file_get_contents(\$f),\"org/partials/dashboard_v1.blade.php\")!==false){echo \"== \$f ==\\n\"; system(\"nl -ba \".escapeshellarg(\$f).\" | sed -n 1,40p\");}}'"
