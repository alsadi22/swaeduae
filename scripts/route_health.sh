#!/usr/bin/env bash
set -euo pipefail

OUT="storage/logs/routes_health.$(date +%F_%H%M%S).log"
TMP="$(mktemp)"
trap 'rm -f "$TMP"' EXIT

echo "== Route health check: $(date) ==" | tee -a "$OUT"
echo "Generating route list..." | tee -a "$OUT"
php artisan route:list --no-ansi > "$TMP"

echo "" | tee -a "$OUT"
echo "Top-level counts:" | tee -a "$OUT"
grep -Eo '\borg\.[[:alnum:]._:-]+' "$TMP" | sort -u | wc -l | awk '{print " org.* routes: " $1}' | tee -a "$OUT"
grep -Eo '\badmin\.[[:alnum:]._:-]+' "$TMP" | sort -u | wc -l | awk '{print " admin.* routes: " $1}' | tee -a "$OUT"

fail=0
pass=0

check_present () {
  local name="$1"
  if grep -q " $name " "$TMP"; then
    echo "  ✔ $name" | tee -a "$OUT"
    ((pass++))
  else
    echo "  ✖ $name (missing)" | tee -a "$OUT"
    ((fail++))
  fi
}

check_block_has () {
  local name="$1" ; shift
  local expect="$1"
  local ln
  ln="$(grep -n " $name " "$TMP" | head -1 | cut -d: -f1 || true)"
  if [[ -z "${ln:-}" ]]; then
    echo "  ✖ $name (missing, cannot check '$expect')" | tee -a "$OUT"
    ((fail++))
    return
  fi
  # Look at the line + the next 3 (to catch wrapped Middleware columns)
  if sed -n "${ln},$((ln+3))p" "$TMP" | grep -q "$expect"; then
    echo "  ✔ $name has '$expect'" | tee -a "$OUT"
    ((pass++))
  else
    echo "  ✖ $name missing '$expect'" | tee -a "$OUT"
    ((fail++))
  fi
}

echo "" | tee -a "$OUT"
echo "Checking critical org routes (presence)..." | tee -a "$OUT"
for r in \
  "org.dashboard" \
  "org.settings.edit" "org.settings.update" \
  "org.attendance.scan" "org.attendance.checkin" "org.attendance.checkout" "org.attendance.undo" \
  "org.opportunities.attendance.settings" "org.opportunities.attendance.settings.save" \
  "org.team.index" "org.team.invite" "org.team.remove" \
  "org.kyc.edit" "org.kyc.update" \
  "org.emails.preview"
do
  check_present "$r"
done

echo "" | tee -a "$OUT"
echo "Checking admin routes (presence)..." | tee -a "$OUT"
for r in \
  "admin.kyc.index" "admin.kyc.approve" "admin.kyc.decline"
do
  check_present "$r"
done

echo "" | tee -a "$OUT"
echo "Validating middleware guards on sensitive routes..." | tee -a "$OUT"
# Org routes should include both 'auth' and 'org:org'
for r in \
  "org.settings.edit" "org.settings.update" \
  "org.attendance.scan" "org.attendance.checkin" "org.attendance.checkout" "org.attendance.undo" \
  "org.opportunities.attendance.settings" "org.opportunities.attendance.settings.save" \
  "org.team.index" "org.team.invite" "org.team.remove" \
  "org.kyc.edit" "org.kyc.update"
do
  check_block_has "$r" "auth"
  check_block_has "$r" "org:org"
done

# Admin KYC routes should include 'admin' middleware
for r in "admin.kyc.index" "admin.kyc.approve" "admin.kyc.decline"; do
  check_block_has "$r" "admin"
done

echo "" | tee -a "$OUT"
echo "Scanning for duplicate imports of controllers in routes/web.php..." | tee -a "$OUT"
if grep -nE '^use App\\Http\\Controllers\\Org\\TeamController;' routes/web.php | awk 'NR>1' | grep -q .; then
  echo "  ✖ Duplicate 'use ... TeamController;' lines found in routes/web.php" | tee -a "$OUT"
  ((fail++))
else
  echo "  ✔ No duplicate TeamController imports" | tee -a "$OUT"
  ((pass++))
fi

echo "" | tee -a "$OUT"
echo "Linting controllers for syntax errors..." | tee -a "$OUT"
if find app/Http/Controllers -type f -name "*.php" -print0 | xargs -0 -n1 php -l | grep -v "No syntax errors detected" | tee -a "$OUT" | grep -q .; then
  echo "  ✖ Controller syntax issues detected above" | tee -a "$OUT"
  ((fail++))
else
  echo "  ✔ Controllers pass php -l" | tee -a "$OUT"
  ((pass++))
fi

echo "" | tee -a "$OUT"
echo "SUMMARY: ${pass} passed, ${fail} failed" | tee -a "$OUT"
test "$fail" -eq 0
