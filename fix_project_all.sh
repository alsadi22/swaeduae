#!/bin/bash
set -euo pipefail

# ---------- Preflight ----------
test -f artisan || { echo "Run this from Laravel root (artisan not found)"; exit 1; }
STAMP="$(date +%F-%H%M%S)"
BK="_cleanup_backup_${STAMP}"
mkdir -p "$BK"

echo "==> Laravel version:"
php artisan --version || true
php -v | head -n1 || true

# ---------- 1) Fix Arabic translations (merge into single return) ----------
echo "==> Fixing resources/lang/ar/messages.php"
mkdir -p resources/lang/ar resources/lang/en
if [ -f resources/lang/ar/messages.php ]; then
  cp resources/lang/ar/messages.php "$BK/messages.ar.before.php"
fi
cat > resources/lang/ar/messages.php << 'PHP'
<?php

return [
    'site_title' => 'سواعد الإمارات',
    'site_title_full' => 'سواعد الإمارات: تطوع في الإمارات',
    'site_subtitle' => 'انضم لأكبر منصة للمتطوعين في الإمارات وابدأ التغيير اليوم!',
    'search' => 'بحث',
    'search_opportunities_placeholder' => 'ابحث عن فرصة تطوع...',
    'featured_opportunities' => 'أبرز فرص التطوع',
    'no_featured_opportunities' => 'لا توجد فرص مميزة حاليا.',
    'browse_all_opportunities' => 'تصفح جميع الفرص التطوعية',
    'view_details' => 'تفاصيل',
    'all_regions' => 'كل المناطق',

    'logout' => 'تسجيل الخروج',
    'my_profile' => 'ملفي الشخصي',
    'contact' => 'تواصل معنا',
    'about' => 'عن المنصة',
    'faq' => 'الأسئلة الشائعة',
    'opportunities' => 'الفرص',
    'home' => 'الرئيسية',

    'active_volunteer' => 'متطوع نشط',
    'member_since' => 'عضو منذ',
    'volunteer_hours' => 'ساعات التطوع',
    'certificates' => 'الشهادات',
    'events' => 'الفعاليات',
    'qr_code' => 'رمز الاستجابة السريعة',
    'no_qr' => 'لا يوجد رمز QR',

    'profile_management' => 'إدارة الملف الشخصي',
    'full_name' => 'الاسم الكامل',
    'email' => 'البريد الإلكتروني',
    'region' => 'المنطقة',
    'national_id' => 'رقم الهوية',
    'marital_status' => 'الحالة الاجتماعية',
    'birth_place' => 'مكان الميلاد',
    'birth_date' => 'تاريخ الميلاد',
    'education' => 'التعليم',
    'level' => 'المستوى',
    'edit_profile' => 'تعديل الملف',

    'my_certificates' => 'شهاداتي',
    'view' => 'عرض',
    'no_certificates' => 'لا توجد شهادات حتى الآن.',
    'recent_events' => 'الفعاليات الأخيرة',
    'no_events' => 'لا توجد فعاليات حديثة.',

    'dashboard_title' => 'لوحة التحكم',
    'dashboard_welcome' => 'مرحبًا بك في لوحة التحكم الخاصة بك!',
];
PHP

# ---------- 2) Ensure public named routes + admin toggle exist ----------
echo "==> Verifying/patching routes/web.php"
cp routes/web.php "$BK/web.before.php"

# If toggle route missing, append it inside admin group quickly (idempotent)
grep -q "users\.toggle" routes/web.php || \
  awk '
    /Route::middleware\(\[.*auth.*admin.*\]\).*->group\(function/ && !p {print; print "    Route::post('\''users/{user}/toggle'\'', [UserController::class, '\''toggle'\''])->name('\''users.toggle'\'');"; p=1; next} {print}
  ' routes/web.php > routes/web.tmp && mv routes/web.tmp routes/web.php

# Ensure about/contact/faq named routes exist (use Route::view if missing)
ensure_view_route () {
  local path="$1" name="$2" view="$3"
  grep -q "->name('$name')" routes/web.php || echo "Route::view('$path', '$view')->name('$name');" >> routes/web.php
}
ensure_view_route "/about" "about" "about"
ensure_view_route "/contact" "contact" "contact"
ensure_view_route "/faq" "faq" "faq"

# Make sure auth.php is required if present
grep -q "require __DIR__.'/auth.php';" routes/web.php || \
  sed -i "/Route::view('\/',/a if (file_exists(__DIR__.'\/auth.php')) { require __DIR__.'\/auth.php'; }" routes/web.php

# ---------- 3) Clean database/migrations (dedupe + quarantine wrong files) ----------
echo "==> Cleaning database/migrations"
mkdir -p "$BK/migrations_misplaced" "$BK/migrations_duplicates"

# Move obvious non-migration files out of migrations/
find database/migrations -maxdepth 1 -type f ! -name "*.php" -print -exec mv {} "$BK/migrations_misplaced/" \; || true
# PHP files that are clearly not timestamped migrations (e.g., controllers/views dumped there)
find database/migrations -maxdepth 1 -type f -name "*.php" ! -regex '.*/[0-9]\{4\}_[0-9_]\+_.*\.php' -print -exec mv {} "$BK/migrations_misplaced/" \; || true

# Dedupe create_settings_table*
mapfile -t SETTINGS < <(ls -1 database/migrations/*create_settings_table*.php 2>/dev/null | sort || true)
if [ "${#SETTINGS[@]}" -gt 1 ]; then
  KEEP="${SETTINGS[-1]}"
  echo "   keeping: $(basename "$KEEP")"
  for f in "${SETTINGS[@]::${#SETTINGS[@]}-1}"; do
    echo "   moving duplicate: $(basename "$f")"
    mv "$f" "$BK/migrations_duplicates/"
  done
fi

# Dedupe event city/description migrations
mapfile -t EVENTCITY < <(ls -1 database/migrations/*add_*city*description*events*table*.php 2>/dev/null | sort || true)
if [ "${#EVENTCITY[@]}" -gt 1 ]; then
  KEEP="${EVENTCITY[0]}"
  echo "   keeping: $(basename "$KEEP")"
  for f in "${EVENTCITY[@]:1}"; do
    echo "   moving duplicate: $(basename "$f")"
    mv "$f" "$BK/migrations_duplicates/"
  done
fi

# Remove backup .bak migration files
find database/migrations -maxdepth 1 -type f -name "*.bak" -print -exec mv {} "$BK/migrations_duplicates/" \; || true

# ---------- 4) Quarantine stray backups & weird files safely ----------
echo "==> Quarantining backups and temp files"
# Common noisy backups
for PAT in "*.bak" "*.save" "*.save.*" "*.swp" "*.swo"; do
  find app routes resources -type f -name "$PAT" -print -exec mv {} "$BK/" \; || true
done

# Quarantine stray files under routes/ that are not canonical .php route files
if [ -d routes/app ]; then mv routes/app "$BK/routes_app_${STAMP}"; fi
if [ -d routes/resources ]; then mv routes/resources "$BK/routes_resources_${STAMP}"; fi
test -f ./web.php && mv ./web.php "$BK/web.php" || true

# ---------- 5) .gitignore for shared hosting safety ----------
echo "==> Patching .gitignore"
if [ -f .gitignore ]; then cp .gitignore "$BK/.gitignore.before"; fi
# Append ignores if not present
append_ignore () { grep -qxF "$1" .gitignore || echo "$1" >> .gitignore; }
append_ignore ".env"
append_ignore "/public/opcache_clear.php"
append_ignore "error_log"
append_ignore "public/error_log"
append_ignore "/_routes_misplaced_backup*"
append_ignore "/_cleanup_backup_*"
append_ignore "/SWAED_*"
append_ignore "latest_errors.txt"
append_ignore "*.zip"

# ---------- 6) Final cache clears and status ----------
echo "==> Clearing caches & optimizing"
php artisan route:clear || true
php artisan config:clear || true
php artisan view:clear || true
php artisan optimize || true

echo "==> Route checks:"
php artisan route:list --name=home --name=about --name=contact --name=faq --name=admin.users.toggle || true

echo "==> Migration status:"
php artisan migrate:status || true

echo "==> Done. Backups at: $BK"
echo "==> Review, then (optional) run migrations:"
echo "    php artisan migrate"
