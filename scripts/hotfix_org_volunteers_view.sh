#!/usr/bin/env bash
set -euo pipefail

PRJ="/home3/vminingc/swaeduae.ae/laravel-app"
BKDIR="/home3/vminingc/backups/sawaeduae"
STAMP="$(date +%Y%m%d_%H%M%S)"
mkdir -p "$BKDIR"

cd "$PRJ"

echo "[1/7] Full project backup (zip)…"
cd "$PRJ/.."
zip -r "$BKDIR/swaeduae2025x_hotfix_org_volview_$STAMP.zip" "swaeduae.ae/laravel-app" -q
cd "$PRJ"

echo "[2/7] Locate file with incomplete 'org.events.volunteers' view array…"
VOL_FILE="$(grep -Rnl "return view('org.events.volunteers', \[" app routes 2>/dev/null | head -1 || true)"
if [ -z "${VOL_FILE}" ]; then
  echo "No incomplete 'org.events.volunteers' pattern found. Nothing to do."
  exit 0
fi
echo "Target file: $VOL_FILE"

echo "[3/7] Per-file backup…"
cp -v "$VOL_FILE" "$BKDIR/$(basename "$VOL_FILE").$STAMP.bak"

echo "[4/7] BEFORE context:"
LINE_NO="$(grep -n "return view('org.events.volunteers', \[" "$VOL_FILE" | head -1 | cut -d: -f1)"
START=$((LINE_NO-10)); [ $START -lt 1 ] && START=1
END=$((LINE_NO+20))
nl -ba "$VOL_FILE" | sed -n "${START},${END}p"

echo "[5/7] Build sed script via here-doc, patch to temp…"
cat > "$BKDIR/patch_$STAMP.sed" <<'SED_EOF'
s|return view\('org\.events\.volunteers',\s*\[$|return view('org.events.volunteers', ['rows' => (\$rows ?? []), 'opportunityId' => (\$opportunityId ?? request()->input('opportunity_id'))]);|g
SED_EOF

sed -E -f "$BKDIR/patch_$STAMP.sed" "$VOL_FILE" > "$VOL_FILE.patched.$STAMP"

echo "[DIFF] (patched vs original):"
diff -u "$VOL_FILE" "$VOL_FILE.patched.$STAMP" || true

echo "[6/7] Write patched content using cat (no inline sed writes)…"
cat "$VOL_FILE.patched.$STAMP" > "$VOL_FILE"
rm -f "$VOL_FILE.patched.$STAMP"

echo "[AFTER] context:"
nl -ba "$VOL_FILE" | sed -n "${START},$((START+25))p"

echo "[7/7] Clear caches & verify routes…"
php artisan view:clear
php artisan route:clear
php artisan config:clear
php artisan cache:clear
php artisan config:cache

php artisan route:list | grep org. || true

echo "✅ Hotfix completed."
