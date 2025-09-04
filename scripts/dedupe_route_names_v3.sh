#!/bin/sh
set -eu
cd "$(dirname "$0")/.."

echo "[*] Detecting duplicate route names: logout, vol.dashboard"

find_files() {
  # $1 = route name
  grep -RIl --include='*.php' -e "->name('$1')" -- routes app 2>/dev/null || true
}

canonical_for() {
  n="$1"
  if [ "$n" = "logout" ]; then
    echo "routes/web.php"; return
  fi
  if [ "$n" = "vol.dashboard" ] && grep -q -e "->name('vol.dashboard')" routes/me.php 2>/dev/null; then
    echo "routes/me.php"; return
  fi
  find_files "$n" | head -n 1
}

process_name() {
  n="$1"
  tmp="$(mktemp)"; find_files "$n" > "$tmp"
  count="$(wc -l < "$tmp" | tr -d ' ')"

  if [ "$count" -le 1 ]; then
    echo "[OK] '$n' defined $count time(s) — nothing to do."
    rm -f "$tmp"; return
  fi

  canon="$(canonical_for "$n")"
  [ -f "$canon" ] || canon="$(head -n 1 "$tmp")"

  echo "[*] '$n' appears in $count file(s). Canonical: $canon"
  echo "    Others will be renamed to '$n.alt' (set APPLY=1 to modify)."

  while IFS= read -r f; do
    [ -z "$f" ] && continue
    if [ "$f" = "$canon" ]; then
      echo "    - KEEP:   $f"
      continue
    fi
    echo "    - RENAME: $f"
    if [ "${APPLY:-0}" = "1" ]; then
      cp "$f" "$f.$(date +%Y%m%d_%H%M%S).bak"
      sed -i "s/->name('$n')/->name('$n.alt')/g" "$f"
    fi
  done < "$tmp"
  rm -f "$tmp"
}

process_name "logout"
process_name "vol.dashboard"

if [ "${APPLY:-0}" = "1" ]; then
  echo "[*] Clearing caches and verifying..."
  php artisan view:clear && php artisan route:clear && php artisan config:clear && php artisan cache:clear
  php artisan config:cache

  php artisan route:list --json > storage/routes.json
  php -r 'echo "Duplicate route names:\n"; $j=json_decode(file_get_contents("storage/routes.json"),true); $m=[]; foreach($j as $r){$n=$r["name"]??null; if($n) $m[$n]=($m[$n]??0)+1;} foreach($m as $n=>$c){ if($c>1) echo "$n x$c\n"; }'
else
  echo "[DRY-RUN] To apply changes set APPLY=1, e.g.: APPLY=1 sh scripts/dedupe_route_names_v3.sh"
fi
