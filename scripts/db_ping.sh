#!/bin/bash
set -euo pipefail
DB_HOST="${DB_HOST:-localhost}"
DB_NAME="${DB_NAME:-vminingc_swaeduae_db}"
DB_USER="${DB_USER:-vminingc_admin}"
DB_PASS="${DB_PASS:-234026.Hg@}"

echo "[*] Pinging MySQL as $DB_USER@$DB_HOST to DB $DB_NAME ..."
MYSQL_PWD="$DB_PASS" mysql -h "$DB_HOST" -u "$DB_USER" -N -e "SELECT DATABASE(); SELECT NOW();" "$DB_NAME"
echo "[OK] MySQL ping OK."
