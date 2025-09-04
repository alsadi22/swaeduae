#!/usr/bin/env bash
set -euo pipefail

MAIN="resources/views/org/dashboard.blade.php"
LAY="resources/views/org/layout.blade.php"

[ -f "$MAIN" ] || { echo "Missing $MAIN"; exit 1; }
[ -f "$LAY" ]  || { echo "Missing $LAY"; exit 1; }

cp "$MAIN" "${MAIN}.bak_$(date +%F_%H%M%S)"
cp "$LAY"  "${LAY}.bak_$(date +%F_%H%M%S)"

# --- Ensure the layout file has the four includes (safe/idempotent) ---
# These may have been broken before; re-enforce them cleanly.
sed -Ei \
  -e "s/^[[:space:]]*\('org\.argon\._sidenav'\)[[:space:]]*$/@includeIf('org.argon._sidenav')/g" \
  -e "s/^[[:space:]]*\('admin\.argon\._navbar'\)[[:space:]]*$/@includeIf('admin.argon._navbar')/g" \
  -e "s/^[[:space:]]*\('org\.partials\.menu'\)[[:space:]]*$/@includeIf('org.partials.menu')/g" \
  -e "s/^[[:space:]]*\('admin\.argon\._footer'\)[[:space:]]*$/@includeIf('admin.argon._footer')/g" \
  "$LAY"
sed -Ei "/^[[:space:]]*@includeIf[[:space:]]*$/d" "$LAY"

# --- Make sure the dashboard view extends the correct layout ---
if grep -q '^@extends(' "$MAIN"; then
  # normalize any other extends to org.layout
  sed -Ei "s/^@extends\([^)]*\)/@extends('org.layout')/" "$MAIN"
else
  # no extends at all -> put one at the top
  sed -i '1i @extends('"'"'org.layout'"'"')' "$MAIN"
fi

# --- Ensure _safe_defaults is included ONCE right after the extends line ---
if ! grep -q "@include('org.dashboard._safe_defaults')" "$MAIN"; then
  sed -i -E "/^@extends\(['\"]org\.layout['\"]\)/a @include('org.dashboard._safe_defaults')" "$MAIN"
fi
# keep only the first occurrence of _safe_defaults
awk 'BEGIN{seen=0}
{
  if ($0 ~ /@include\((\"|\x27)org\.dashboard\._safe_defaults(\"|\x27)\)/) {
    if (seen) next; else seen=1
  }
  print
}' "$MAIN" > "$MAIN.tmp" && mv "$MAIN.tmp" "$MAIN"

# --- Wrap content in @section('content') ... @endsection if missing ---
if ! grep -q "^@section('content')" "$MAIN"; then
  # insert after the extends and _safe_defaults lines
  awk '
    BEGIN{inserted=0}
    {
      print
      if (!inserted && $0 ~ /@include\((\"|\x27)org\.dashboard\._safe_defaults(\"|\x27)\)/) {
        print "@section('\''content'\'')"
        inserted=1
      }
    }
    END{
      if (!inserted) print "@section('\''content'\'')"
    }' "$MAIN" > "$MAIN.tmp" && mv "$MAIN.tmp" "$MAIN"
fi
# Ensure we have a closing @endsection (append if missing)
grep -q "^@endsection" "$MAIN" || echo "@endsection" >> "$MAIN"

# --- Also (re)apply minimal guards inside the main partial, just in case ---
PART="resources/views/org/partials/dashboard_v1.blade.php"
if [ -f "$PART" ] && ! grep -q "__ORG_GUARDS__" "$PART"; then
  cp "$PART" "${PART}.bak_$(date +%F_%H%M%S)"
  sed -i '1i @php /*__ORG_GUARDS__*/ $appsTotal=$appsTotal??0; $appsPending=$appsPending??0; $appsApproved=$appsApproved??0; $checkinsToday=$checkinsToday??0; @endphp' "$PART"
fi

echo "Rebuilding Blade cache…"
php artisan view:clear && php artisan view:cache

echo
echo "Dashboard smoke test:"
php tools/check_org_dashboard.php || true

echo
echo "Last relevant errors (if any):"
( tail -n 300 "storage/logs/laravel-$(date +%F).log" 2>/dev/null || tail -n 300 storage/logs/laravel.log 2>/dev/null ) \
 | egrep -i 'org|dashboard|Undefined|exception|View|layout' | tail -n 40 || true
