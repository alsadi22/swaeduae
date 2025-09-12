#!/usr/bin/env bash
set -euo pipefail

PRJ="/home3/vminingc/swaeduae.ae/laravel-app"
BKDIR="/home3/vminingc/backups/sawaeduae"
STAMP="$(date +%Y%m%d_%H%M%S)"
mkdir -p "$BKDIR"
cd "$PRJ"

# 1) Find the file with the incomplete 'org.events.volunteers' line
VOL_FILE="$(grep -Rnl \"return view('org.events.volunteers', \\[\" app routes 2>/dev/null | head -1 || true)"
if [ -z \"${VOL_FILE}\" ]; then
  echo \"No incomplete 'org.events.volunteers' pattern found.\"
  exit 0
fi
echo \"Target: $VOL_FILE\"

# 2) Backup the file
cp -v \"$VOL_FILE\" \"$BKDIR/$(basename \"$VOL_FILE\").$STAMP.bak\"

# 3) Show BEFORE context
LINE_NO=\"$(grep -n \"return view('org.events.volunteers', \\[\" \"$VOL_FILE\" | head -1 | cut -d: -f1)\"
START=$((LINE_NO-8)); [ \"$START\" -lt 1 ] && START=1
END=$((LINE_NO+12))
echo \"---- BEFORE ----\"
nl -ba \"$VOL_FILE\" | sed -n \"${START},${END}p\"

# 4) Patch to temp using a here-doc sed script
cat > \"$BKDIR/patch_$STAMP.sed\" <<'SED_EOF'
s|return view\('org\.events\.volunteers',\s*\[$|return view('org.events.volunteers', ['rows' => (\$rows ?? []), 'opportunityId' => (\$opportunityId ?? request()->input('opportunity_id'))]);|g
SED_EOF

sed -E -f \"$BKDIR/patch_$STAMP.sed\" \"$VOL_FILE\" > \"$VOL_FILE.patched.$STAMP\"

echo \"---- DIFF ----\"
diff -u \"$VOL_FILE\" \"$VOL_FILE.patched.$STAMP\" || true

# 5) Overwrite original via cat (per our standard)
cat \"$VOL_FILE.patched.$STAMP\" > \"$VOL_FILE\"
rm -f \"$VOL_FILE.patched.$STAMP\"

echo \"---- AFTER ----\"
nl -ba \"$VOL_FILE\" | sed -n \"${START},$((START+20))p\"

# 6) Clear caches and verify routes
php artisan view:clear
php artisan route:clear
php artisan config:clear
php artisan cache:clear
php artisan config:cache

php artisan route:list | grep org. || true
