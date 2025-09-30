PHP_BIN=${PHP_BIN:-php}
#!/usr/bin/env bash
set -euo pipefail
# Pick the newest backup timestamp across edited files
TS=$(ls -1 resources/views/org/**/*.bak_* 2>/dev/null | sed -E 's/.*\.bak_//' | sort -u | tail -n1 || true)
[ -z "$TS" ] && { echo "No backups found."; exit 0; }
echo "Reverting files with timestamp $TS"
for bak in $(ls -1 resources/views/org/**/*.bak_"$TS" 2>/dev/null); do
  orig="${bak%.bak_$TS}"
  cp -a "$bak" "$orig"
  echo "  ← $orig"
done
php artisan view:clear >/dev/null || true
php artisan view:cache >/dev/null || true
echo "Reverted."
