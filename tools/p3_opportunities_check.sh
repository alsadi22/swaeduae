#!/usr/bin/env bash
set -euo pipefail

say(){ printf "%-44s %s\n" "$1" "$2"; }
ok(){  say "$1" "PASS"; }
warn(){ say "$1" "WARN"; }
miss(){ say "$1" "MISS"; }

# Collect route list once (tolerate errors)
RL="$(php artisan route:list 2>/dev/null || true)"

# ---------- ROUTES ----------
if echo "$RL" | grep -qE "[[:space:]]events\.index($|[[:space:]])"; then ok "Route name events.index present"; else warn "Route name events.index"; fi
if echo "$RL" | grep -qE "[[:space:]]events\.show($|[[:space:]])"; then ok "Route name events.show present"; else warn "Route name events.show"; fi
if echo "$RL" | grep -qE "[[:space:]]opportunities\.index($|[[:space:]])"; then warn "Legacy name opportunities.index also exists"; else ok "No duplicate opportunities.* names"; fi

# show route param shape
SHOW_LINE="$(echo "$RL" | awk '/events\.show/{print; exit}')"
if echo "$SHOW_LINE" | grep -q "{id}"; then warn "Show route uses {id} (no slug)"; else ok "Show route parameter is not plain {id}"; fi

# sitemap vs routes consistency
if echo "$RL" | grep -q "/opportunities"; then ok "Sitemap path /opportunities seems valid"; else
  if grep -q "/opportunities" app/Http/Controllers/SitemapController.php 2>/dev/null; then warn "Sitemap lists /opportunities but site uses /events";
  else ok "Sitemap not advertising /opportunities"; fi
fi

# ---------- CONTROLLER ----------
CTL=""
for c in app/Http/Controllers/EventController.php app/Http/Controllers/OpportunityController.php; do
  [ -f "$c" ] && CTL="$c" && break
done
if [ -n "${CTL:-}" ]; then ok "Controller exists: ${CTL#app/Http/Controllers/}"; else miss "Event/Opportunity controller missing"; fi

if [ -n "${CTL:-}" ]; then
  grep -q "paginate(" "$CTL" 2>/dev/null && ok "Index uses pagination" || warn "Index pagination not detected"
  grep -q -Ei "where\(['\"](published|is_published)['\"]\s*,\s*(1|true)\)" "$CTL" 2>/dev/null && ok "Published filter present" || warn "Published filter not detected"
  grep -q -E "orderBy\(|latest\(|oldest\(" "$CTL" 2>/dev/null && ok "Explicit ordering present" || warn "No explicit ordering in index"
  grep -q -E "with\(|load\(" "$CTL" 2>/dev/null && ok "Eager loading detected" || warn "Eager loading not detected (risk of N+1)"
fi

# ---------- MODEL ----------
MODEL=""
for m in app/Models/Opportunity.php app/Models/Event.php; do
  [ -f "$m" ] && MODEL="$m" && break
done
if [ -n "${MODEL:-}" ]; then ok "Model exists: ${MODEL#app/Models/}"; else miss "Opportunity/Event model missing"; fi

if [ -n "${MODEL:-}" ]; then
  grep -q -E "scopePublished\s*\(" "$MODEL" 2>/dev/null && ok "scopePublished() present" || warn "scopePublished() missing"
  grep -q -E "slug" "$MODEL" 2>/dev/null && ok "Model references slug" || warn "Model has no slug reference"
  grep -q -E "belongsTo\(|hasMany\(" "$MODEL" 2>/dev/null && ok "Model relationships defined" || warn "No relationships detected"
fi

# ---------- MIGRATIONS (schema) ----------
MIG_FILE="$(ls -1 database/migrations/*create_*opportunit*table*.php 2>/dev/null | tail -n1 || true)"
[ -z "$MIG_FILE" ] && MIG_FILE="$(ls -1 database/migrations/*create_*event*table*.php 2>/dev/null | tail -n1 || true)"

if [ -n "${MIG_FILE:-}" ]; then
  ok "Migration found: ${MIG_FILE##*/}"
  grep -q -E "\$table->string\('title'\)" "$MIG_FILE"  && ok "title column"        || warn "title column"
  grep -q -E "\$table->text\('description'\)" "$MIG_FILE" && ok "description column" || warn "description column"
  grep -q -E "\$table->string\('slug'\)" "$MIG_FILE"   && ok "slug column"         || warn "slug column"
  grep -q -E "date(Time)?\('start|starts_at|start_at" "$MIG_FILE" && ok "start date/time" || warn "start date/time"
  grep -q -E "date(Time)?\('end|ends_at|end_at" "$MIG_FILE"       && ok "end date/time"   || warn "end date/time"
  grep -q -E "\$table->(unsignedBigInteger|foreignId)\('org" "$MIG_FILE" && ok "org/owner FK" || warn "org/owner FK"
  grep -q -E "index\(" "$MIG_FILE" && ok "indexes present" || warn "no indexes detected"
else
  miss "No events/opportunities migration found"
fi

# ---------- VIEWS ----------
IDX=""
SHOW=""
for v in resources/views/events/index.blade.php resources/views/opportunities/index.blade.php; do [ -f "$v" ] && IDX="$v" && break; done
for v in resources/views/events/show.blade.php  resources/views/opportunities/show.blade.php; do [ -f "$v" ] && SHOW="$v" && break; done

[ -n "${IDX:-}" ] && ok "Index view: ${IDX#resources/views/}" || miss "Index view missing"
[ -n "${SHOW:-}" ] && ok "Show view: ${SHOW#resources/views/}"   || miss "Show view missing"

if [ -n "${IDX:-}" ]; then
  grep -q -E "\{\{\s*\$.*->links\(" "$IDX" 2>/dev/null && ok "Index view renders pagination links" || warn "Pagination links missing in index view"
  grep -q -E "__\(" "$IDX" 2>/dev/null && ok "Index view uses i18n __()" || warn "Index view i18n not detected"
fi
if [ -n "${SHOW:-}" ]; then
  grep -q -E "__\(" "$SHOW" 2>/dev/null && ok "Show view uses i18n __()" || warn "Show view i18n not detected"
fi

# ---------- REGISTER/APPLY PATH ----------
echo "$RL" | grep -q "volunteer\.registerEvent" && ok "Apply route volunteer.registerEvent present" || warn "volunteer.registerEvent route"
( echo "$RL" | grep -q "volunteer.registerEvent" || echo "$RL" | grep -q "/volunteer/events/.*/register" ) && ok "Apply POST path present" || warn "Apply POST path not found"

echo "— end —"
