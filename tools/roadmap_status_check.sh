#!/usr/bin/env bash
set -euo pipefail

say(){ printf "%-38s %s\n" "$1" "$2"; }
ok(){  say "$1" "PASS"; }
warn(){ say "$1" "WARN"; }
miss(){ say "$1" "MISS"; }

# Collect route list once (tolerate errors)
RL="$(php artisan route:list 2>/dev/null || true)"

has_route_name(){ grep -qE "[[:space:]]$1($|[[:space:]])" <<<"$RL"; }
route_name_count(){ grep -Eo "[[:space:]]$1($|[[:space:]])" <<<"$RL" | wc -l | tr -d ' '; }
route_is_post_only(){ awk '/[[:space:]]'"$1"'($|[[:space:]])/{print $1}' <<<"$RL" | grep -q '^POST$'; }

have_file(){ [ -f "$1" ]; }
have_dir(){ [ -d "$1" ]; }
grep_file(){ local pat="$1" f="$2"; grep -qE "$pat" "$f" 2>/dev/null; }

echo "=== Roadmap status (read-only) ==="

# P0–P1 Stabilize + Auth
has_route_name "login"            && ok  "P1 signin named 'login'" || warn "P1 signin route name 'login'"
has_route_name "register"         && ok  "P1 GET register named 'register'" || warn "P1 GET register route"
has_route_name "register.perform" && ok  "P1 POST register.perform" || warn "P1 POST register.perform"
has_route_name "logout" && route_is_post_only "logout" \
                                 && ok  "P1 logout is POST-only" || warn "P1 logout POST-only"
has_route_name "org.login"        && ok  "P1 org.login"           || warn "P1 org.login"
has_route_name "org.register"     && ok  "P1 org.register"        || warn "P1 org.register"
has_route_name "org.logout"       && ok  "P1 org.logout"          || warn "P1 org.logout"
grep -q "Admin\\\\Dashboard" <<<"$RL" && ok "P0 admin split (dashboard route)" || warn "P0 admin split (dashboard)"

# P2 Public site & SEO
have_file "resources/views/partials/seo.blade.php" && ok  "P2 SEO partial present" || miss "P2 SEO partial"
have_file "public/robots.txt"                      && ok  "P2 robots.txt present"   || miss "P2 robots.txt"
has_route_name "sitemap"                           && ok  "P2 /sitemap.xml route"   || miss "P2 /sitemap.xml"
have_file "resources/lang/en.json"                 && ok  "P2 en.json present"       || miss "P2 en.json"
have_file "resources/lang/ar.json"                 && ok  "P2 ar.json present"       || miss "P2 ar.json"
grep_file "SetLocaleFromRequest" "app/Http/Kernel.php" && ok "P2 locale middleware wired" || miss "P2 locale middleware"
# Locale switch duplication check
CNT=$(route_name_count "locale.switch"); [ "$CNT" -eq 1 ] && ok "P2 locale.switch single definition" || warn "P2 locale.switch duplicated ($CNT)"
# Public pages
have_file "resources/views/pages/about.blade.php"   && ok "P2 /about view"   || miss "P2 /about view"
have_file "resources/views/pages/privacy.blade.php" && ok "P2 /privacy view" || miss "P2 /privacy view"
have_file "resources/views/pages/terms.blade.php"   && ok "P2 /terms view"   || miss "P2 /terms view"
# Rate limit + MicroCache
grep_file "FormRateLimit::class" "app/Http/Kernel.php" && ok "P2 FormRateLimit in web group" || miss "P2 FormRateLimit in web"
have_file "app/Http/Middleware/MicroCache.php"          && ok "P2 MicroCache present"        || warn "P2 MicroCache missing"
grep_file "lang.switch|locale.switch" "app/Http/Middleware/MicroCache.php" && ok "P2 MicroCache skips locale switch" || warn "P2 MicroCache skip for locale"

# P3 Opportunities (public)
(has_route_name "opportunities.index" || grep -qE "/opportunit" <<<"$RL") && ok "P3 opportunities route exists" || warn "P3 opportunities route"
have_file "app/Models/Opportunity.php"            && ok "P3 Opportunity model" || warn "P3 Opportunity model"
have_file "resources/views/opportunities/index.blade.php" && ok "P3 index view" || warn "P3 index view"
have_file "resources/views/opportunities/show.blade.php"  && ok "P3 show view"  || warn "P3 show view"

# P4 Organizations portal
grep -qE "[[:space:]]org\." <<<"$RL"               && ok "P4 org.* routes present" || warn "P4 org.* routes"
have_dir "resources/views/org"                     && ok "P4 org views dir"        || warn "P4 org views dir"

# P6 Attendance/QR
grep -RqiE "QrCode|qr[_-]?code|attendance|checkin" app/ routes resources 2>/dev/null \
                                                  && ok "P6 QR/Attendance code refs" || warn "P6 QR/Attendance refs"
has_route_name "attendance.checkin" && ok "P6 attendance route" || true

# P5 Admin polish & audit
grep -qE "[[:space:]]admin\." <<<"$RL"             && ok "P5 admin.* routes present" || warn "P5 admin.* routes"
have_file "resources/views/admin/layout.blade.php"  && ok "P5 admin layout view"      || warn "P5 admin layout view"
have_file "public/css/admin-fixes.css"             && ok "P5 admin-fixes.css"         || warn "P5 admin-fixes.css"
have_file "tools/admin_sanity_check.sh"            && ok "P5 admin sanity script"     || warn "P5 admin sanity script"

# P7 Payments hardening
grep -qE '"laravel/cashier|stripe/stripe-php|omnipay|paytabs|payfort|tap-payments"' composer.json 2>/dev/null \
                                                  && ok "P7 payment library present" || warn "P7 payment library"
grep -RqiE "PaymentController|Payments?Controller|Cashier" app/ 2>/dev/null \
                                                  && ok "P7 payment controllers"     || warn "P7 payment controllers"

# P9–P11 Messaging, Ops, Accessibility
# Messaging
have_dir "app/Mail" || have_dir "resources/views/emails" \
                                                  && ok "P9 mailers/templates exist" || warn "P9 mailers/templates"
grep_file "^MAIL_MAILER=" ".env"                  && ok "P9 MAIL_MAILER set"         || warn "P9 MAIL_MAILER"
# Ops
have_file "tools/db_backup.sh"                    && ok "P11 DB backup script"       || warn "P11 DB backup script"
have_file "tools/form_probe.sh"                   && ok "P11 form_probe.sh"          || warn "P11 form_probe.sh"
have_file "tools/apply_db_index_suggestions.php"  && ok "P11 index applier"          || warn "P11 index applier"
# Accessibility (light heuristic)
grep -Rqi 'lang=' resources/views/layouts 2>/dev/null && ok "P10 lang attribute in layout(s)" || warn "P10 lang attr in layout"

# P12–P13 Data migration, Perf/Sec
have_file "database/seeders/RolesAndPermissionsSeeder.php" && ok "P12 Roles&Perms seeder" || warn "P12 Roles&Perms seeder"
have_file "database/seeders/AdminUserSeeder.php"            && ok "P12 AdminUser seeder"   || warn "P12 AdminUser seeder"
have_file "tools/db_index_audit.php" || have_file "tools/db_index_audit.sh" \
                                                           && ok "P13 DB index auditor"   || warn "P13 DB index auditor"
grep -Rqi "Honeypot" routes app/Http 2>/dev/null            && ok "P13 Honeypot wired"     || warn "P13 Honeypot"

# P14 UAT & Launch
grep_file "^APP_ENV=production" ".env"           && ok "P14 APP_ENV=production"      || warn "P14 APP_ENV"
grep_file "^APP_DEBUG=false" ".env"              && ok "P14 APP_DEBUG=false"         || warn "P14 APP_DEBUG"
# Route cache health (don’t run route:cache; just detect known duplication)
[ "$(route_name_count "locale.switch")" -eq 1 ]  && ok "P14 route names unique (locale.switch)" || warn "P14 duplicate route name: locale.switch"

echo "=== End of report ==="
