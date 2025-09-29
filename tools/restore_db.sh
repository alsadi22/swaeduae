#!/usr/bin/env bash
set -euo pipefail
if [[ $# -lt 1 ]]; then
  echo "Usage: $0 /full/path/to/backup-YYYY-MM-DD-HHMMSS.tar.gz [DB_NAME]" >&2
  exit 1
fi
APP="/home3/vminingc/swaeduae.ae/laravel-app"
cd "$APP"
ARCHIVE="$1"
OVERRIDE_DB="${2:-}"

TMPDIR="$(mktemp -d)"
trap 'rm -rf "$TMPDIR"' EXIT

echo "[*] Extracting db.sql.gz ..."
tar -xzf "$ARCHIVE" -C "$TMPDIR" db.sql.gz

# Parse env
DB_HOST=""; DB_PORT="3306"; DB_DATABASE=""; DB_USERNAME=""; DB_PASSWORD=""; DB_SOCKET=""
while IFS='=' read -r k v; do
  [[ "$k" =~ ^#|^$ ]] && continue
  v="${v%$'\r'}"; v="${v%\"}"; v="${v#\"}"
  case "$k" in
    DB_HOST) DB_HOST="$v" ;;
    DB_PORT) DB_PORT="$v" ;;
    DB_DATABASE) DB_DATABASE="$v" ;;
    DB_USERNAME) DB_USERNAME="$v" ;;
    DB_PASSWORD) DB_PASSWORD="$v" ;;
    DB_SOCKET) DB_SOCKET="$v" ;;
  esac
done < .env
[[ -n "$OVERRIDE_DB" ]] && DB_DATABASE="$OVERRIDE_DB"

MYSQL_BIN="${MYSQL_BIN:-/usr/bin/mysql}"
[[ -x "$MYSQL_BIN" ]] || MYSQL_BIN="/usr/local/bin/mysql"

CNF="$TMPDIR/my.cnf"
{
  echo "[client]"
  [[ -n "$DB_HOST" ]] && echo "host=$DB_HOST"
  [[ -n "$DB_PORT" ]] && echo "port=$DB_PORT"
  [[ -n "$DB_SOCKET" ]] && echo "socket=$DB_SOCKET"
  echo "user=$DB_USERNAME"
  echo "password=$DB_PASSWORD"
} > "$CNF"
chmod 600 "$CNF"

echo "[*] Restoring into database '$DB_DATABASE' ..."
gunzip -c "$TMPDIR/db.sql.gz" | $MYSQL_BIN --defaults-extra-file="$CNF" "$DB_DATABASE"
echo "[OK] Restore complete."
