#!/usr/bin/env bash
PHP_BIN=${PHP_BIN:-php}
set -euo pipefail
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
cd "$ROOT"

IN="resources/css/app.css"
OUTDIR="public/assets"
TMP="$(mktemp)"
install -d "$OUTDIR"

# 1) Build minified CSS
npx --yes tailwindcss@3.4.10 -c tailwind.config.js -i "$IN" -o "$TMP" --minify >/dev/null

# 2) Hash
if command -v sha256sum >/dev/null 2>&1; then HASH="$(sha256sum "$TMP" | cut -c1-10)"
elif command -v shasum >/dev/null 2>&1; then HASH="$(shasum -a 256 "$TMP" | cut -c1-10)"
else HASH="$(md5sum "$TMP" | cut -c1-10)"; fi

OUT="$OUTDIR/app.$HASH.css"
mv -f "$TMP" "$OUT"

# 3) Stable symlink and SAFE PERMS
ln -sfn "app.$HASH.css" "$OUTDIR/app.css"
chmod 644 "$OUT" || true
chmod 755 "$OUTDIR" || true

# 4) Wire versioned query (?v=<hash>) in layout
LAYOUT="resources/views/public/layout.blade.php"
cp -a "$LAYOUT" "$LAYOUT.bak.$(date +%Y%m%d_%H%M%S)"
perl -0777 -i -pe "s#href=\"/assets/app\.css(?:\?v=[^\"]*)?\"#href=\"/assets/app.css?v=$HASH\"#g" "$LAYOUT"

# 5) Budget warn (>80KB)
SIZE=$(stat -c%s "$OUT" 2>/dev/null || wc -c <"$OUT")
if [ "$SIZE" -gt 81920 ]; then
  echo "WARN: CSS is ${SIZE} bytes (>80KB). Review tailwind.config.js:content globs."
fi

echo "Built: $OUT"
ls -lh "$OUT" "$OUTDIR/app.css" | sed 's/^/  /'
grep -n 'href="/assets/app.css' "$LAYOUT" | head -1 | sed 's/^/  /' || true
