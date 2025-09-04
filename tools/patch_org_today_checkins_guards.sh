#!/usr/bin/env bash
set -euo pipefail
PART="resources/views/org/partials/today_checkins.blade.php"
[ -f "$PART" ] || { echo "Missing $PART"; exit 1; }

# 1) Backup
cp "$PART" "${PART}.bak_$(date +%F_%H%M%S)"

# 2) Ensure global safe defaults are included
grep -q "org.dashboard._safe_defaults" "$PART" || sed -i "1i @include('org.dashboard._safe_defaults')" "$PART"

# 3) Guard: coerce $rows to a Collection so ->isEmpty(), foreach, etc. work
if ! grep -q "__TODAY_CHECKINS_GUARDS__" "$PART"; then
  sed -i "1i @php /*__TODAY_CHECKINS_GUARDS__*/ \$rows = collect(\$rows ?? []); \$rows_count = \$rows->count(); @endphp" "$PART"
fi

# 4) If the template ever prints $rows directly, switch to a safe scalar
grep -q "{{ \$rows_count }}" "$PART" || sed -i "s/{{[[:space:]]*\\\$rows[[:space:]]*}}/{{ \$rows_count }}/g" "$PART"

echo "Patched $PART"
