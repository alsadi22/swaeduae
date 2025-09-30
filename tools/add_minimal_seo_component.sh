PHP_BIN=${PHP_BIN:-php}
#!/usr/bin/env bash
set -euo pipefail
SEO="resources/views/components/seo.blade.php"
STAMP="$(date +%F_%H%M%S)"

if [ -f "$SEO" ]; then
  cp -a "$SEO" "$SEO.bak_$STAMP"
  echo "Backup existing: $SEO.bak_$STAMP"
else
  mkdir -p "$(dirname "$SEO")"
fi

cat > "$SEO" <<'BLADE'
<title>@yield('title', config('app.name','SwaedUAE'))</title>
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="robots" content="index,follow">
<link rel="canonical" href="{{ url()->current() }}">
<link rel="icon" href="{{ asset('favicon.ico') }}">
BLADE

php artisan view:clear >/dev/null || true
php artisan view:cache >/dev/null || true
echo "✅ components/seo.blade.php in place."
