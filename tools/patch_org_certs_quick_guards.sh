#!/usr/bin/env bash
PHP_BIN=${PHP_BIN:-php}
set -euo pipefail
PART="resources/views/org/partials/certs_quick.blade.php"
[ -f "$PART" ] || { echo "Missing $PART"; exit 1; }

# 1) Backup
cp "$PART" "${PART}.bak_$(date +%F_%H%M%S)"

# 2) Ensure global safe defaults are available in this partial
grep -q "org.dashboard._safe_defaults" "$PART" || sed -i "1i @include('org.dashboard._safe_defaults')" "$PART"

# 3) Inline guards for this partial
if ! grep -q "__CERTS_QUICK_GUARDS__" "$PART"; then
  sed -i "1i @php /*__CERTS_QUICK_GUARDS__*/ \$recentOpps = \$recentOpps ?? []; \$recentOpps_count = (is_countable(\$recentOpps) ? count(\$recentOpps) : (int)\$recentOpps); @endphp" "$PART"
fi

# 4) If the template prints $recentOpps directly, switch to $recentOpps_count
grep -q "{{ \$recentOpps_count }}" "$PART" || sed -i "s/{{[[:space:]]*\\\$recentOpps[[:space:]]*}}/{{ \$recentOpps_count }}/g" "$PART"

echo "Patched $PART"
