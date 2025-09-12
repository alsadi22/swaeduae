#!/usr/bin/env bash
set -euo pipefail
section(){ printf "\n=== %s ===\n" "$1"; }

section "PWD"
pwd

# Cache once; tolerate errors
RL="$(php artisan route:list 2>/dev/null || true)"

section "1) ROUTES — core"
php artisan route:list | egrep "events\.index|events\.show|opportunit|volunteer\.registerEvent" || true
grep -Rni --color -E "Route::get\('/events|Route::get\('/opportunit|name\('opportunit" routes || true

section "1a) SHOW route param shape"
SHOW_LINE="$(echo "$RL" | awk '/events\.show/ {print; exit}')"
echo "$SHOW_LINE"
if echo "$SHOW_LINE" | grep -q "{id}"; then
  echo "WARN: events.show uses {id} (no slug)"
else
  echo "OK: events.show param is not plain {id}"
fi

section "2) CONTROLLER — Event/Opportunity"
for c in app/Http/Controllers/EventController.php app/Http/Controllers/OpportunityController.php; do
  [ -f "$c" ] && { echo "---- $c"; sed -n '1,240p' "$c" | nl | sed -n '1,240p'; }
done

section "2a) Controller signals"
grep -nE "paginate\(|orderBy\(|latest\(|oldest\(" app/Http/Controllers/EventController.php 2>/dev/null || true
grep -nEi "where\(['\"](published|is_published)['\"]\s*,\s*(1|true)\)" app/Http/Controllers/EventController.php 2>/dev/null || true
grep -nE "with\(|load\(" app/Http/Controllers/EventController.php 2>/dev/null || true

section "3) MODEL — which is used"
ls -1 app/Models | egrep -i "event|opportun" || true
for m in app/Models/Event.php app/Models/Opportunity.php; do
  [ -f "$m" ] && { echo "---- $m"; sed -n '1,220p' "$m" | nl | sed -n '1,220p'; }
done

section "3a) Model signals (scopes/slug/relations)"
grep -nE "function\s+scopePublished\s*\(" app/Models/* 2>/dev/null || true
grep -nE "\bslug\b" app/Models/* 2>/dev/null || true
grep -nE "belongsTo\(|hasMany\(" app/Models/* 2>/dev/null || true

section "4) MIGRATIONS — primary tables (not applications)"
ls -1 database/migrations | egrep -i "create_.*(event|opportun).*table" || true
grep -Rni --color -E "Schema::create\('(events|event|opportunities|opportunity)'" database/migrations || true
for f in $(grep -Rli "Schema::create" database/migrations | egrep -i "(event|opportun)" || true); do
  echo "---- $f"
  sed -n '1,220p' "$f" | nl | sed -n '1,220p'
done

section "5) VIEWS — index pagination + i18n"
if [ -f resources/views/events/index.blade.php ]; then
  echo "---- resources/views/events/index.blade.php"
  sed -n '1,200p' resources/views/events/index.blade.php | nl | sed -n '1,200p'
  echo "-- grep ->links("
  grep -n "\->links(" resources/views/events/index.blade.php || true
fi

section "6) SHOW ROUTE — definitions"
php artisan route:list | awk '/events\.show/ {print}'
grep -Rni --color -E "Route::get\('/events/\{.*\}'|name\('events\.show'\)" routes || true

section "7) APPLY PATH — volunteer register"
php artisan route:list | awk '/volunteer\/events\/\{eventId\}\/register/ {print}'

echo
echo "— end —"
