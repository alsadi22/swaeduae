PHP_BIN=${PHP_BIN:-php}
#!/usr/bin/env bash
set -euo pipefail
STAMP="$(date +%F_%H%M%S)"

CSS="public/css/brand.css"
PARTIAL_HOTFIX="resources/views/partials/org_menu_hotfix.blade.php"

layouts=(
  resources/views/org/argon/app.blade.php
  resources/views/admin/argon/app.blade.php
  resources/views/layouts/app.blade.php
  resources/views/layouts/main.blade.php
  resources/views/app.blade.php
)

echo "== Reverting hotfix includes in layouts =="
for f in "${layouts[@]}"; do
  [ -f "$f" ] || continue
  cp -a "$f" "$f.recover_${STAMP}"

  # Remove the include line we added (idempotent)
  if grep -q "partials\.org_menu_hotfix" "$f"; then
    sed -i.bak "/partials\.org_menu_hotfix/d" "$f"
  fi

  # If a .bak_* exists from earlier, restore the newest one
  last_bak="$(ls -1t "$f".bak_* 2>/dev/null | head -n1 || true)"
  if [ -n "${last_bak}" ]; then
    echo "Restoring $f from $last_bak"
    cp -a "$last_bak" "$f"
  fi
done

echo "== Disabling org menu partial hotfix =="
if [ -f "$PARTIAL_HOTFIX" ]; then
  mv "$PARTIAL_HOTFIX" "${PARTIAL_HOTFIX}.disabled_${STAMP}"
fi

echo "== Removing org-menu CSS block from brand.css (if present) =="
if [ -f "$CSS" ]; then
  cp -a "$CSS" "${CSS}.recover_${STAMP}"
  awk '
    /\/\* org-menu-fix:start \*\// {skip=1}
    !skip {print}
    /\/\* org-menu-fix:end \*\// {skip=0}
  ' "$CSS" > "$CSS.__tmp__" 2>/dev/null || true
  mv "$CSS.__tmp__" "$CSS" 2>/dev/null || true
fi

echo "== Clearing Laravel caches =="
php artisan optimize:clear >/dev/null || true
php artisan view:clear >/dev/null || true

echo "== Quick logs tail (last 60 lines) =="
tail -n 60 storage/logs/laravel.log 2>/dev/null || true

echo "✅ Rollback complete."
