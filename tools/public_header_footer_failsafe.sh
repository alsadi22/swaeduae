#!/usr/bin/env bash
PHP_BIN=${PHP_BIN:-php}
set -euo pipefail
CSS='public/css/site.css'
STAMP="$(date +%F_%H%M%S)"
[ -f "$CSS" ] || touch "$CSS"
cp -a "$CSS" "$CSS.bak_$STAMP"

# remove previous block, then append fresh
awk '/\/\* public-header-failsafe:start \*\//{skip=1} !skip{print} /\/\* public-header-failsafe:end \*\//{skip=0}' "$CSS" > "$CSS.__tmp__" || true
mv "$CSS.__tmp__" "$CSS" 2>/dev/null || true

cat >> "$CSS" <<'EOF'
/* public-header-failsafe:start */
.site-header{display:block !important; position:sticky; top:0; z-index:1000; background:#fff;}
.site .content{margin-top:1rem;}
.site-footer{display:block !important;}
/* public-header-failsafe:end */
EOF

echo "Wrote failsafe CSS to $CSS (backup: $CSS.bak_$STAMP)"
