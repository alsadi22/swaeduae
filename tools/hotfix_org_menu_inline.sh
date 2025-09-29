#!/usr/bin/env bash
set -euo pipefail
STAMP="$(date +%F_%H%M%S)"

# Find the org menu partial automatically
MENU_FILE=""
for f in \
  resources/views/org/partials/menu.blade.php \
  resources/views/org/argon/_menu.blade.php \
  resources/views/org/argon/_navbar.blade.php \
  resources/views/admin/argon/_navbar.blade.php \
  resources/views/partials/menu.blade.php \
  resources/views/partials/navbar.blade.php
do
  [ -f "$f" ] || continue
  if grep -qi "Organization Console" "$f"; then MENU_FILE="$f"; break; fi
done

# Fallback: try grep to locate the file
if [ -z "${MENU_FILE}" ]; then
  MENU_FILE="$(grep -RIl --include='*.blade.php' -n 'Organization Console' resources/views | head -n1 || true)"
fi

[ -n "$MENU_FILE" ] || { echo "Could not locate the org menu partial."; exit 1; }

cp -a "$MENU_FILE" "$MENU_FILE.bak_${STAMP}"

# Remove any previous hotfix block then append a fresh one (idempotent)
awk '
  /<!-- org-menu-hotfix:start -->/ {skip=1}
  !skip {print}
  /<!-- org-menu-hotfix:end -->/ {skip=0}
' "$MENU_FILE" > "$MENU_FILE.__tmp__" || true
mv "$MENU_FILE.__tmp__" "$MENU_FILE" || true

cat >> "$MENU_FILE" <<'BLADE'
<!-- org-menu-hotfix:start -->
@push('styles')
<style>
/* Keep the dropdown as a slim fixed panel */
.org-panel {
  position: fixed !important;
  top: 64px !important;              /* adjust if your navbar is taller */
  right: .75rem !important;
  left: auto !important;
  width: min(92vw, 360px) !important;
  max-height: calc(100vh - 88px) !important;
  overflow: auto !important;
  border-radius: 14px !important;
  box-shadow: 0 24px 48px rgba(2,6,23,.24) !important;
  z-index: 1050 !important;
}
[dir="rtl"] .org-panel, [lang="ar"] .org-panel {
  left: .75rem !important; right: auto !important;
}

/* Page backdrop + lock scroll while menu is open */
body.org-panel-open { overflow: hidden !important; }
.org-panel-backdrop {
  position: fixed; inset: 0;
  background: rgba(15,23,42,.35);
  z-index: 1049;
}
</style>
@endpush

@push('scripts')
<script>
(function () {
  if (!window.bootstrap) return;

  // Attach once
  if (window.__orgMenuHotfixAttached) return;
  window.__orgMenuHotfixAttached = true;

  document.addEventListener('shown.bs.dropdown', function (e) {
    // The dropdown wrapper that fired the event (contains the menu)
    var container = e.target;
    var menu = container && container.querySelector('.dropdown-menu');
    if (!menu) return;

    // Make it a fixed side panel
    menu.classList.add('org-panel');

    // Add viewport backdrop + lock scroll
    if (!document.querySelector('.org-panel-backdrop')) {
      var bd = document.createElement('div');
      bd.className = 'org-panel-backdrop';
      document.body.appendChild(bd);
      document.body.classList.add('org-panel-open');

      // Clicking the backdrop closes the dropdown
      bd.addEventListener('click', function () {
        try {
          var inst = bootstrap.Dropdown.getInstance(container.querySelector('[data-bs-toggle="dropdown"]'));
          if (inst) inst.hide();
        } catch (_) {}
      }, { once: true });
    }
  });

  document.addEventListener('hide.bs.dropdown', function () {
    document.body.classList.remove('org-panel-open');
    document.querySelectorAll('.org-panel-backdrop').forEach(function (el) { el.remove(); });
  });
})();
</script>
@endpush
<!-- org-menu-hotfix:end -->
BLADE

echo "Hotfix injected into: $MENU_FILE"
php artisan view:clear >/dev/null || true
php artisan view:cache >/dev/null || true
echo "✅ Done. Hard-refresh your browser (Ctrl/Cmd+Shift+R)."
