#!/usr/bin/env bash
# SwaedUAE FULL BACKUP (DB + app + configs) — robust & verifiable
set -u
set -o pipefail
umask 027

APP_ROOT="${APP_ROOT:-/var/www/swaeduae/current}"
APP_NAME="${APP_NAME:-swaeduae}"
BACKUP_ROOT="${BACKUP_ROOT:-/root/backups/swaeduae}"

TS="$(date +%F_%H%M%S)"
WORK="$BACKUP_ROOT/${APP_NAME}.${TS}"
LOG="$WORK/backup.log"

sudo mkdir -p "$WORK"
exec > >(sudo tee -a "$LOG") 2>&1

echo "=== SwaedUAE FULL BACKUP ${TS} ==="
echo "APP_ROOT=$APP_ROOT"
echo "BACKUP_ROOT=$BACKUP_ROOT"
echo "WORK=$WORK"

# ---- Versions snapshot ----
{
  echo "== Versions =="
  php -v 2>/dev/null | head -n1 || true
  nginx -v 2>&1 | head -n1 || true
  (mysql --version 2>/dev/null || mariadb --version 2>/dev/null) | head -n1 || true
  composer -V 2>/dev/null || true
  node -v 2>/dev/null || true
} | sudo tee "$WORK/versions.txt" >/dev/null

# ---- Parse .env for DB creds (no PHP; tolerant) ----
ENV_FILE="$APP_ROOT/.env"
getenv() { sudo grep -E "^$1=" "$ENV_FILE" 2>/dev/null | tail -n1 | cut -d= -f2- | tr -d '"'\'''; }
DB_NAME="$(getenv DB_DATABASE || true)"
DB_USER="$(getenv DB_USERNAME || true)"
DB_PASS="$(getenv DB_PASSWORD || true)"
DB_HOST="$(getenv DB_HOST || echo 127.0.0.1)"
DB_PORT="$(getenv DB_PORT || echo 3306)"

echo "DB: ${DB_NAME:-<none>}@$DB_HOST:$DB_PORT as ${DB_USER:-<none>}" | sudo tee "$WORK/db.info" >/dev/null

# ---- MySQL dump (mysqldump or mariadb-dump) ----
DUMPBIN=""
command -v mysqldump >/dev/null 2>&1 && DUMPBIN="$(command -v mysqldump)"
[ -z "$DUMPBIN" ] && command -v mariadb-dump >/dev/null 2>&1 && DUMPBIN="$(command -v mariadb-dump)"

if [ -n "${DB_NAME:-}" ] && [ -n "${DB_USER:-}" ] && [ -n "$DUMPBIN" ]; then
  echo "== Dumping database '$DB_NAME' with $DUMPBIN =="
  export MYSQL_PWD="$DB_PASS"
  sudo env MYSQL_PWD="$MYSQL_PWD" "$DUMPBIN" \
    --single-transaction --routines --triggers --events --default-character-set=utf8mb4 \
    --host="$DB_HOST" --port="$DB_PORT" --user="$DB_USER" "$DB_NAME" \
    2>"$WORK/db_dump.stderr" \
    | gzip -1 | sudo tee "$WORK/db_${DB_NAME}_${TS}.sql.gz" >/dev/null || echo "[WARN] DB dump returned non-zero" | sudo tee -a "$WORK/WARNINGS.txt" >/dev/null
  unset MYSQL_PWD
else
  echo "[WARN] Skipping DB dump (missing tool or creds)" | sudo tee -a "$WORK/WARNINGS.txt" >/dev/null
fi

# ---- App archive (from inside APP_ROOT) ----
echo "== Archiving application =="
sudo tar --dereference -h --numeric-owner --acls --xattrs \
  --exclude='storage/framework/cache/*' \
  --exclude='storage/framework/sessions/*' \
  --exclude='storage/framework/views/*' \
  --exclude='storage/logs/*' \
  --exclude='node_modules' \
  -czf "$WORK/app_${APP_NAME}_${TS}.tar.gz" -C "$APP_ROOT" .

# ---- System configs ----
echo "== Archiving system config =="
ETC_LIST=()
[ -d /etc/nginx ] && ETC_LIST+=("/etc/nginx")
[ -d /etc/php ] && ETC_LIST+=("/etc/php")
[ -d /etc/letsencrypt ] && ETC_LIST+=("/etc/letsencrypt")
[ -d /etc/cloudflared ] && ETC_LIST+=("/etc/cloudflared")
[ -f /etc/systemd/system/swaed-queue-worker.service ] && ETC_LIST+=("/etc/systemd/system/swaed-queue-worker.service")
if [ ${#ETC_LIST[@]} -gt 0 ]; then
  sudo tar -czf "$WORK/etc_configs_${TS}.tar.gz" "${ETC_LIST[@]}" || true
fi

# ---- Cron ----
echo "== Saving cron =="
(crontab -l 2>/dev/null || true) | sudo tee "$WORK/crontab_root.txt" >/dev/null
sudo -u www-data crontab -l 2>/dev/null | sudo tee "$WORK/crontab_www-data.txt" >/dev/null || true
sudo tar -czf "$WORK/cron_system_${TS}.tar.gz" /etc/crontab /etc/cron.d /var/spool/cron/crontabs 2>/dev/null || true

# ---- Package list ----
dpkg -l 2>/dev/null | sudo tee "$WORK/packages.txt" >/dev/null || true

# ---- Restore README ----
cat <<'READ' | sudo tee "$WORK/RESTORE_README.txt" >/dev/null
Quick restore (reference):
  1) Move bundle to target server.
  2) Extract:   tar -xzf full_backup_*.tar.gz -C /root/backups
  3) App:       tar -xzf <WORK>/app_*.tar.gz -C /var/www/swaeduae/current
  4) DB:        gunzip -c <WORK>/db_*.sql.gz | mysql -u <user> -p <db>
  5) Configs:   tar -xzf <WORK>/etc_configs_*.tar.gz -C /
  6) Caches:    php artisan optimize:clear && php artisan config:cache && php artisan route:cache && php artisan view:cache
  7) Restart:   systemctl restart php8.3-fpm nginx
READ

# ---- Manifest + checksums ----
{
  echo "BACKUP_TS=$TS"
  echo "APP_ROOT=$APP_ROOT"
  echo "APP_NAME=$APP_NAME"
  echo "FILES:"
  ls -lh "$WORK"
} | sudo tee "$WORK/MANIFEST.txt" >/dev/null

( cd "$WORK" && sudo sha256sum *.gz 2>/dev/null ) | sudo tee "$WORK/SHA256SUMS.txt" >/dev/null || true

# ---- Final bundle ----
FINAL="$BACKUP_ROOT/full_backup_${APP_NAME}_${TS}.tar.gz"
echo "== Creating final bundle: $FINAL =="
sudo tar -czf "$FINAL" -C "$BACKUP_ROOT" "$(basename "$WORK")"
sudo ls -lh "$FINAL"

echo "== DONE =="
