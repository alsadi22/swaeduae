#!/bin/bash
set -euo pipefail
cd "$(dirname "$0")/.."

echo "[*] Detecting duplicate route names: logout, vol.dashboard"

# Helper: list files containing a route name
find_files() {
  local n="$1"
  grep -RIl --include="*.php" "->name('$n')" routes app || true
}

# Decide canonical file for a name
canonical_for() {
  local n="$1"
  if [[ "$n" == "logout" ]]; then
    echo "routes/web.php"
    return
  fi
  if [[ "$n" == "vol.dashboard" ]]; then
    # prefer me.php if it defines the name
    if grep -q "->name('vol.dashboard')" routes/me.php 2>/dev/null; then
      echo "routes/me.php"
      return
    fi
  fi
  # fallback: first file that defines it
  find_files "$n" | head -n 1
}

process_name() {
  local n="$1"
  local files; IFS=$'\n' read -r -d '' -a files < <(find_files "$n" | tr -d '\r' && printf '\0')
  local count="${#files[@]}"

  if [[ "$count" -le 1 ]]; then
    echo "[OK] '$n' defined $count time(s) — nothing to do."
    return
  fi

  local canon; canon="$(canonical_for "$n")"
  if [[ -z "${canon:-}" || ! -f "$canon" ]]; then
    echo "[WARN] Could not determine canonical file for '$n'. Using first occurrence."
    canon="${files[0]}"
  fi

  echo "[*] '$n' appears in $count file(s). Canonical: $canon"
  echo "    Others will be renamed to '$n.alt' (dry-run unless APPLY=1)."

  for f in "${files[@]}"; do
    if [[ "$f" == "$canon" ]]; then
      echo "    - KEEP: $f"
      continue
    fi
    echo "    - RENAME in: $f"
    if [[ "${APPLY:-0}" == "1" ]]; then
      cp "$f" "$f.$(date +%Y%m%d_%H%M%S).bak"
      # replace ONLY exact ->name('name') occurrences
      sed -i "s/->name('$n')/->name('$n.alt')/g" "$f"
    fi
  done
}

process_name "logout"
process_name "vol.dashboard"

if [[ "${APPLY:-0}" == "1" ]]; then
  echo "[*] Clearing caches and verifying..."
  php artisan view:clear && php artisan route:clear && php artisan config:clear && php artisan cache:clear
  php artisan config:cache

  php artisan route:list --json > storage/routes.json
  php -r 'echo "Duplicate route names:\n"; $j=json_decode(file_get_contents("storage/routes.json"),true); $m=[]; foreach($j as $r){$n=$r["name"]??null; if($n) $m[$n]=($m[$n]??0)+1;} foreach($m as $n=>$c){ if($c>1) echo "$n x$c\n"; }'
else
  echo "[DRY-RUN] To apply changes set APPLY=1, e.g.: APPLY=1 bash scripts/dedupe_route_names.sh"
fi
