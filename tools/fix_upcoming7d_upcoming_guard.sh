#!/usr/bin/env bash
set -euo pipefail
PART="resources/views/org/partials/upcoming_7d.blade.php"
[ -f "$PART" ] || { echo "Missing $PART"; exit 1; }

# Backup
cp "$PART" "${PART}.bak_$(date +%F_%H%M%S)"

# Ensure global safe defaults are available once
grep -q "org.dashboard._safe_defaults" "$PART" || sed -i "1i @include('org.dashboard._safe_defaults')" "$PART"

GUARD_LINE="@php /*__UPCOMING7D_GUARDS__*/ \$upcoming = collect(\$upcoming ?? (\$list ?? [])); \$list = isset(\$list) ? \$list : \$upcoming; if (!(\$list instanceof \Illuminate\Support\Collection)) { \$list = collect(\$list ?? []); } \$list_count = \$list->count(); @endphp"

# If guard exists, replace that entire line; else insert at the top
if grep -q "__UPCOMING7D_GUARDS__" "$PART"; then
  sed -i "/__UPCOMING7D_GUARDS__/c\\$GUARD_LINE" "$PART"
else
  sed -i "1i $GUARD_LINE" "$PART"
fi

# If the template ever echoes {{ $list }} or {{ $upcoming }}, switch to a safe scalar
sed -i -E 's/{{[[:space:]]*\$list[[:space:]]*}}|{{[[:space:]]*\$upcoming[[:space:]]*}}/{{ $list_count }}/g' "$PART"

echo "Rebuilding Blade cache…"
php artisan view:clear && php artisan view:cache

echo
echo "Re-checking dashboard quickly:"
php tools/check_org_dashboard.php || true
