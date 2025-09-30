set -euo pipefail
PHP_BIN=${PHP_BIN:-php}
cd "$(dirname "$0")/.."

echo "[1/5] Strip Honeypot from AdminLoginController"
php -r '$f="app/Http/Controllers/Admin/Auth/AdminLoginController.php"; $c=@file_get_contents($f); if($c===false){fwrite(STDERR,"Missing $f\n"); exit(1);} $cnt=0; $c=preg_replace('/\$this->middleware\(\s*\[(?:\\\\?App\\\\Http\\\\Middleware\\\\Honeypot::class\s*,\s*)?[\'"]throttle:login[\'"]\s*\]\s*\)->only\(\s*[\'"]login[\'"]\s*\);/',"$"."this->middleware(\'throttle:login\')->only(\'login\');",$c,1,$cnt); if($cnt){file_put_contents($f,$c); echo "  - Honeypot removed";} else { echo "  - No Honeypot line found (already clean)"; } echo PHP_EOL;'

echo "[2/5] Comment bad App\\Http\\org lines in Kernel (backup first)"
cp app/Http/Kernel.php app/Http/Kernel.php.bak.$(date +%s)
sed -i -E 's|(^\s*)([^/].*App\\\Http\\\org.*)|\1// \2|' app/Http/Kernel.php || true

echo "[3/5] Clear caches"
php artisan route:clear >/dev/null 2>&1 || true
php artisan optimize:clear

echo "[4/5] Snapshot routes"
php tools/route_snapshot.php || true

echo "[5/5] Quick server-side admin login test"
set +H
jar=/tmp/admin.cookies
base=https://swaeduae.ae
admin_email='admin@swaeduae.ae'
admin_pass='Temp\!234'
curl -skc "$jar" "$base/admin/login" -o /tmp/a.html
tok=$(grep -oP 'name="_token"\s+value="[^"]+"' /tmp/a.html | sed -E 's/.*value="([^"]+)".*/\1/')
curl -skb "$jar" -c "$jar" "$base/admin/login" \
  --data-urlencode "_token=$tok" \
  --data-urlencode "email=$admin_email" \
  --data-urlencode "password=$admin_pass" -D - -o /dev/null
curl -skb "$jar" -L "$base/admin/dashboard" -w "\nADMIN dash: %{http_code} %{url_effective}\n" -o /dev/null
