#!/usr/bin/env bash
set -euo pipefail
APP="/home3/vminingc/swaeduae.ae/laravel-app"
cd "$APP"

TS=$(date +%F-%H%M%S)

echo "== Phase 5: headers+caching =="
# 1) Ensure /public/sitemap.xml exists (symlink if allowed, else copy)
if ln -sfn sitemaps/sitemap-index.xml public/sitemap.xml 2>/dev/null; then
  echo "[OK] public/sitemap.xml symlinked -> sitemaps/sitemap-index.xml"
else
  cp -f public/sitemaps/sitemap-index.xml public/sitemap.xml
  echo "[OK] public/sitemap.xml copied from index"
fi

# 2) Harden public/.htaccess (safe IfModule wrappers)
HT="public/.htaccess"
cp -a "$HT" "$HT.bak_$TS" || true
awk '1' "$HT" > "$HT.tmp_$TS"

cat >> "$HT.tmp_$TS" <<'HTACCESS'

### === SwaedUAE: Performance & Security headers ===
<IfModule mod_headers.c>
  Header always set X-Frame-Options "SAMEORIGIN"
  Header always set X-Content-Type-Options "nosniff"
  Header always set Referrer-Policy "strict-origin-when-cross-origin"
  Header always set Permissions-Policy "geolocation=(), microphone=(), camera=()"
  # If site is HTTPS-only, uncomment HSTS (after confirming no subdomain issues):
  # Header always set Strict-Transport-Security "max-age=31536000; includeSubDomains; preload"
</IfModule>

# Gzip / deflate (only if module is available)
<IfModule mod_deflate.c>
  AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript application/json image/svg+xml
</IfModule>

# Far-future expires for static assets (fingerprinted or safe to cache)
<IfModule mod_expires.c>
  ExpiresActive On
  ExpiresByType text/css "access plus 1 month"
  ExpiresByType application/javascript "access plus 1 month"
  ExpiresByType text/javascript "access plus 1 month"
  ExpiresByType image/svg+xml "access plus 1 month"
  ExpiresByType image/x-icon "access plus 1 year"
  ExpiresByType image/png "access plus 1 month"
  ExpiresByType image/jpg "access plus 1 month"
  ExpiresByType image/jpeg "access plus 1 month"
  ExpiresByType image/webp "access plus 1 month"
  ExpiresDefault "access plus 7 days"
</IfModule>

# Cache-Control as a backstop
<IfModule mod_headers.c>
  <FilesMatch "\.(css|js|png|jpe?g|webp|svg|ico)$">
    Header set Cache-Control "public, max-age=2592000, immutable"
  </FilesMatch>
</IfModule>
### === /SwaedUAE ===
HTACCESS

mv -f "$HT.tmp_$TS" "$HT"
echo "[OK] .htaccess updated (headers + caching)"

# 3) Clear & rebuild caches
/usr/local/bin/php artisan optimize:clear >/dev/null
/usr/local/bin/php artisan config:cache >/dev/null
/usr/local/bin/php artisan route:cache  >/dev/null
/usr/local/bin/php artisan view:cache   >/dev/null
echo "[OK] Laravel caches rebuilt"

# 4) Sanity checks
echo "---- robots ----"
curl -s https://swaeduae.ae/robots.txt | head -n 20 || true
echo "---- sitemap.xml ----"
curl -s https://swaeduae.ae/sitemap.xml | head -n 10 || true
echo "== Phase 5 done =="
