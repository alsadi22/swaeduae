#!/usr/bin/env bash
set -euo pipefail
STAMP="$(date +%F_%H%M%S)"

echo "== 1) Create public parent layout (uses your partials) =="
mkdir -p resources/views/layouts resources/views/partials
[ -f resources/views/partials/navbar.blade.php ] || { echo "⚠️ missing partials/navbar.blade.php (using placeholder)"; cat > resources/views/partials/navbar.blade.php <<'BLADE'
<nav class="navbar navbar-expand-lg bg-white border-bottom shadow-sm"><div class="container"><a class="navbar-brand" href="{{ url('/') }}">{{ config('app.name','SwaedUAE') }}</a></div></nav>
BLADE
}
[ -f resources/views/partials/footer.blade.php ] || { echo "⚠️ missing partials/footer.blade.php (using placeholder)"; cat > resources/views/partials/footer.blade.php <<'BLADE'
<footer class="py-4 border-top bg-light"><div class="container small">&copy; {{ date('Y') }} {{ config('app.name','SwaedUAE') }}</div></footer>
BLADE
}

PUB="resources/views/layouts/public.blade.php"
cp -a "$PUB" "$PUB.bak_$STAMP" 2>/dev/null || true
cat > "$PUB" <<'BLADE'
@php $rtl = app()->getLocale()==='ar'; @endphp
<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ $rtl ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  @includeIf('components.seo')
  <link rel="stylesheet" href="{{ asset('vendor/argon/assets/css/argon-dashboard.min.css') }}">
  <link rel="stylesheet" href="{{ asset('css/brand.css') }}">
  <link rel="stylesheet" href="{{ asset('css/site.css') }}">
  @stack('head')
</head>
<body class="bg-gray-100 site">
  @includeIf('partials.navbar')
  <main class="content wrap">@yield('content')</main>
  @includeIf('partials.footer')

  <script src="{{ asset('vendor/argon/assets/js/core/bootstrap.min.js') }}"></script>
  <script src="{{ asset('js/public-ui.js') }}" defer></script>
  @stack('scripts')
</body>
</html>
BLADE

echo "== 2) Point public home views to layouts.public =="
for V in resources/views/home.blade.php resources/views/public/home.blade.php; do
  if [ -f "$V" ]; then
    cp -a "$V" "$V.bak_$STAMP"
    # Normalize first line to @extends('layouts.public')
    awk 'NR==1{print "@extends('\''layouts.public'\'')"; next} {print}' "$V" > "$V.__tmp__" && mv "$V.__tmp__" "$V"
  fi
done

echo "== 3) Move org dropdown/sidenav styles into brand.css =="
CSS="public/css/brand.css"
mkdir -p public/css public/js
touch "$CSS"
cp -a "$CSS" "$CSS.bak_$STAMP"
# Drop prior org-ui block if present, then append fresh scoped rules
awk '/\/\* org-ui:start \*\//{skip=1} !skip{print} /\/\* org-ui:end \*\//{skip=0}' "$CSS" > "$CSS.__tmp__" || true
mv "$CSS.__tmp__" "$CSS" 2>/dev/null || true
cat >> "$CSS" <<'CSS'
/* org-ui:start */
/* Sidenav margin for Argon org layout */
body.g-sidenav-show.g-sidenav-pinned .main-content{ margin-left:280px; }
[dir="rtl"] body.g-sidenav-show.g-sidenav-pinned .main-content{ margin-left:0; margin-right:280px; }

/* Right-aligned navbar dropdown in org header (no overlap, fixed) */
.navbar .dropdown-menu-end.show, .navbar .dropdown-menu-right.show{
  position: fixed !important;
  top: 64px !important; right: .75rem !important; left: auto !important;
  width: min(92vw, 360px) !important;
  max-height: calc(100vh - 96px) !important;
  overflow: auto !important; border-radius: 14px;
  box-shadow: 0 24px 48px rgba(2,6,23,.24); z-index: 1050;
  transform: none !important;
}
[dir="rtl"] .navbar .dropdown-menu-end.show, [lang="ar"] .navbar .dropdown-menu-end.show{
  left: .75rem !important; right: auto !important;
}

/* When the org-console dropdown is opened, give a little breathing room */
body.org-console-open .main-content{ margin-right: 320px; }
[dir="rtl"] body.org-console-open .main-content{ margin-right:0; margin-left: 320px; }
/* org-ui:end */
CSS

echo "== 4) Create org UI JS (toggle pin + console-open hook) =="
ORGJS="public/js/org-ui.js"
cp -a "$ORGJS" "$ORGJS.bak_$STAMP" 2>/dev/null || true
cat > "$ORGJS" <<'JS'
(function(){
  try{
    // Persist sidenav pin state
    var KEY='org_sidenav_pinned';
    function setPinned(p){
      document.body.classList.toggle('g-sidenav-pinned', !!p);
      try{ localStorage.setItem(KEY, p?'1':'0'); }catch(e){}
    }
    try{ setPinned(localStorage.getItem(KEY)!=='0'); }catch(e){}
    document.addEventListener('click', function(ev){
      var btn = ev.target.closest && ev.target.closest('#org-sidenav-toggle');
      if(!btn) return;
      ev.preventDefault();
      setPinned(!document.body.classList.contains('g-sidenav-pinned'));
    });

    // Add/remove .org-console-open when a navbar dropdown opens
    function onOpen(){ document.body.classList.add('org-console-open'); }
    function onClose(){ document.body.classList.remove('org-console-open'); }
    if (window.bootstrap && document.addEventListener){
      document.addEventListener('shown.bs.dropdown', function(e){
        if (e && e.target && e.target.closest('.navbar')) onOpen();
      });
      document.addEventListener('hide.bs.dropdown', function(e){
        if (e && e.target && e.target.closest('.navbar')) onClose();
      });
    } else {
      // Fallback: watch any .navbar .dropdown-menu show/hide
      var mo = new MutationObserver(function(muts){
        var anyOpen = !!document.querySelector('.navbar .dropdown-menu.show');
        if (anyOpen) onOpen(); else onClose();
      });
      mo.observe(document.documentElement, {subtree:true, attributes:true, attributeFilter:['class']});
    }
  }catch(e){}
})();
JS

# Minimal public JS (safe no-op if missing)
PUBJS="public/js/public-ui.js"
[ -f "$PUBJS" ] || echo "(function(){ /* public UI hook */ })();" > "$PUBJS"

echo "== 5) Strip inline org hacks & load org-ui.js in org layout =="
LAY="resources/views/org/layout.blade.php"
if [ -f "$LAY" ]; then
  cp -a "$LAY" "$LAY.bak_$STAMP"
  # Remove org-menu-minimal & org-console-shift CSS blocks
  awk '
    /\/\* org-menu-minimal:start \*\//{skip=1}
    /\/\* org-menu-minimal:end \*\//{skip=0; next}
    /\/\* org-console-shift:start \*\//{skip=1}
    /\/\* org-console-shift:end \*\//{skip=0; next}
    !skip{print}
  ' "$LAY" > "$LAY.__tmp__" && mv "$LAY.__tmp__" "$LAY"

  # Remove the old inline "org-console-shift JS" <script> if present
  awk '
    /<script>\/\* org-console-shift JS \*\//{skip=1}
    skip && /<\/script>/{skip=0; next}
    !skip{print}
  ' "$LAY" > "$LAY.__tmp__" && mv "$LAY.__tmp__" "$LAY"

  # Ensure we include the external JS once (before </body>)
  grep -q 'js/org-ui.js' "$LAY" || awk '
    /<\/body>/ && !done { print "  <script src=\"{{ asset('\''js/org-ui.js'\'') }}\" defer></script>"; print; done=1; next }
    {print}
  ' "$LAY" > "$LAY.__tmp__" && mv "$LAY.__tmp__" "$LAY"
fi

echo "== 6) Make bare dropdowns right-aligned in Argon navbar (once) =="
NAV="resources/views/admin/argon/_navbar.blade.php"
if [ -f "$NAV" ]; then
  cp -a "$NAV" "$NAV.bak_$STAMP"
  # Insert dropdown-menu-end where a dropdown-menu lacks -end/-right
  perl -0777 -pe 's/class="dropdown-menu(?![^"]*(dropdown-menu-(end|right)))/class="dropdown-menu dropdown-menu-end/g' -i "$NAV"
else
  echo "⚠️ $NAV not found (skipping)"
fi

echo "== 7) Cache rebuild =="
php artisan view:clear >/dev/null || true
php artisan view:cache >/dev/null || true
php artisan route:clear >/dev/null || true
php artisan route:cache >/dev/null || true

echo "== 8) Quick smoke checks =="
echo -n "Home has navbar? "; curl -sk https://swaeduae.ae/ | grep -m1 -o '<nav[^>]*>' || echo "MISSING"
echo -n "Home has footer? "; curl -sk https://swaeduae.ae/ | grep -m1 -o '<footer[^>]*>' || echo "MISSING"

echo "✅ Done. Public pages use layouts/public; Org/Admin keep Argon. Inline org hacks moved to brand.css + org-ui.js"
echo "Backups tagged with .$STAMP"
