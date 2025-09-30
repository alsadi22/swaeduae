PHP_BIN=${PHP_BIN:-php}
#!/usr/bin/env bash
set -euo pipefail
export LANG=C
BASE="${BASE:-https://swaeduae.ae}"
langs=(en ar)
paths=(/ /about /events /opportunities /gallery /verify /contact /login /register)

hr(){ printf '\n==== %s ====\n' "$*"; }
row(){ printf '%-5s %-14s %-4s %-6s %-7s %-5s %-3s\n' "Lang" "Path" "Code" "<main" "<footer" "argon" "CSP"; }

hr "Headers + structure (by language)"
row
for lang in "${langs[@]}"; do
  for p in "${paths[@]}"; do
    H="$(curl -sS -H "Accept-Language: $lang" -D - "$BASE$p" -o -)"
    code=$(printf '%s' "$H" | sed -n '1s/.* \([0-9][0-9][0-9]\).*/\1/p')
    body=$(printf '%s' "$H" | awk 'f{print} /^$/{f=1}')
    mains=$(printf '%s' "$body" | tr -d '\n' | grep -o '<main' | wc -l | tr -d ' ')
    foots=$(printf '%s' "$body" | tr -d '\n' | grep -o '<footer' | wc -l | tr -d ' ')
    argon=$(printf '%s' "$body" | grep -E 'argon-dashboard|min\.css|nucleo-icons|perfect-scrollbar' | wc -l | tr -d ' ')
    csp=$(printf '%s' "$H" | grep -i '^Content-Security-Policy:' >/dev/null && echo yes || echo no)
    printf '%-5s %-14s %-4s %-6s %-7s %-5s %-3s\n' "$lang" "$p" "$code" "$mains" "$foots" "$argon" "$csp"
  done
done

hr "Assets reachable on each page (CSS/JS)"
for p in "${paths[@]}"; do
  echo "-- $p"
  HTML=$(curl -sS "$BASE$p")
  printf '%s\n' "$HTML" | grep -oE 'href="[^"]+\.css[^"]+"' | sed -E 's/^href="|"$//g' \
    | while IFS= read -r u; do curl -sS -o /dev/null -w "CSS\thttp=%{http_code}\tct=%{content_type}\t%{url_effective}\n" "$u"; done
  printf '%s\n' "$HTML" | grep -oE 'src="[^"]+\.js[^"]+"'  | sed -E 's/^src="|"$//g' \
    | while IFS= read -r u; do curl -sS -o /dev/null -w "JS \thttp=%{http_code}\tct=%{content_type}\t%{url_effective}\n" "$u"; done
done

hr "Raw <main>/<footer> occurrences in views (should be only in layout)"
echo "-- <main> outside layout:"
grep -RIn "<main\\b" resources/views | grep -v "resources/views/layouts/app.blade.php" || true
echo "-- <footer> outside canonical partials:"
grep -RIn "<footer\\b" resources/views | grep -v -E "layouts/partials/footer|components/footer|argon_front/_footer" || true

hr "Blade compile sanity (will show errors if any)"
php artisan view:cache >/dev/null 2>&1 || { echo "❌ view:cache failed — see storage/logs/laravel-*.log"; exit 1; }
echo "✅ Blade compiled cleanly."
php artisan view:clear >/dev/null
