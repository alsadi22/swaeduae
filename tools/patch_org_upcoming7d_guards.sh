PHP_BIN=${PHP_BIN:-php}
#!/usr/bin/env bash
set -euo pipefail
PART="resources/views/org/partials/upcoming_7d.blade.php"
[ -f "$PART" ] || { echo "Missing $PART"; exit 1; }

# 1) Backup
cp "$PART" "${PART}.bak_$(date +%F_%H%M%S)"

# 2) Ensure global safe defaults are available in this partial
grep -q "org.dashboard._safe_defaults" "$PART" || sed -i "1i @include('org.dashboard._safe_defaults')" "$PART"

# 3) Inline guards: coerce $list to a Collection so ->isEmpty(), foreach, etc. won't crash
if ! grep -q "__UPCOMING7D_GUARDS__" "$PART"; then
  sed -i "1i @php /*__UPCOMING7D_GUARDS__*/ \$list = collect(\$list ?? []); \$list_count = \$list->count(); @endphp" "$PART"
fi

# 4) If the template ever prints $list directly, switch it to the safe count
grep -q "{{ \$list_count }}" "$PART" || sed -i "s/{{[[:space:]]*\\\$list[[:space:]]*}}/{{ \$list_count }}/g" "$PART"

echo "Patched $PART"
