PHP_BIN=${PHP_BIN:-php}
#!/usr/bin/env bash
set -euo pipefail
PART="resources/views/org/partials/certs_quick.blade.php"
[ -f "$PART" ] || { echo "Missing $PART"; exit 1; }

# 1) Backup
cp "$PART" "${PART}.bak_$(date +%F_%H%M%S)"

# 2) Ensure safe defaults are included once
grep -q "org.dashboard._safe_defaults" "$PART" || sed -i "1i @include('org.dashboard._safe_defaults')" "$PART"

# 3) Replace our previous guard line to use a Collection (so ->isEmpty() works)
if grep -q "__CERTS_QUICK_GUARDS__" "$PART"; then
  # Replace the whole guard line
  sed -i \
    "s#@php .*__CERTS_QUICK_GUARDS__.*@endphp#@php /*__CERTS_QUICK_GUARDS__*/ \$recentOpps = collect(\$recentOpps ?? []); \$recentOpps_count = \$recentOpps->count(); @endphp#" \
    "$PART"
else
  # Insert fresh guard if it wasn't there (rare)
  sed -i \
    "1i @php /*__CERTS_QUICK_GUARDS__*/ \$recentOpps = collect(\$recentOpps ?? []); \$recentOpps_count = \$recentOpps->count(); @endphp" \
    "$PART"
fi

# 4) If template prints $recentOpps directly, keep using the scalar count
grep -q "{{ \$recentOpps_count }}" "$PART" || sed -i "s/{{[[:space:]]*\\\$recentOpps[[:space:]]*}}/{{ \$recentOpps_count }}/g" "$PART"

echo "Patched $PART"
