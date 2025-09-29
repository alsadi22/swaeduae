#!/usr/bin/env bash
set -euo pipefail
PART="resources/views/org/partials/dashboard_v1.blade.php"
[ -f "$PART" ] || { echo "Missing $PART"; exit 1; }

# 1) Backup
cp "$PART" "${PART}.bak_$(date +%F_%H%M%S)"

# 2) Add a safe, scalar display variable right at the top (once)
if ! grep -q "__UPCOMING_DISPLAY__" "$PART"; then
  sed -i '1i @php /*__UPCOMING_DISPLAY__*/ $upcoming_display = isset($upcomingOpps) ? (int)$upcomingOpps : (is_countable($upcoming ?? null) ? count($upcoming) : (int)($upcoming ?? 0)); @endphp' "$PART"
  echo "Injected \$upcoming_display guard."
fi

# 3) Replace any direct print of {{ $upcoming }} with {{ $upcoming_display }} (only if not already swapped)
grep -q '{{ $upcoming_display }}' "$PART" || \
  sed -i 's/{{[[:space:]]*\$upcoming[[:space:]]*}}/{{ $upcoming_display }}/g' "$PART"

echo "Patched $PART safely."
