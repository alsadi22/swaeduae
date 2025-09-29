#!/usr/bin/env bash
set -euo pipefail
PART=resources/views/org/partials/dashboard_v1.blade.php
[ -f "$PART" ] || { echo "Missing $PART"; exit 1; }

# Backup once per run
cp "$PART" "${PART}.bak_$(date +%F_%H%M%S)"

# Insert a compact guard line at the very top if not already present
if ! grep -q "__ORG_GUARDS__" "$PART"; then
  sed -i '1i @php /*__ORG_GUARDS__*/ $appsTotal=$appsTotal??0; $appsPending=$appsPending??0; $appsApproved=$appsApproved??0; $checkinsToday=$checkinsToday??0; @endphp' "$PART"
  echo "Patched guards at top of partial."
else
  echo "Guards already present (no change)."
fi
