PHP_BIN=${PHP_BIN:-php}
#!/usr/bin/env bash
set -euo pipefail
APP="$(cd "$(dirname "$0")/.." && pwd)"
LAST="$APP/storage/app/agent/guard.last"
SIG=""

if [ -d "$APP/.git" ] && command -v git >/dev/null 2>&1; then
  SIG="$(git -C "$APP" rev-parse HEAD 2>/dev/null || echo "")"
else
  SIG="$(find "$APP/app" "$APP/resources" "$APP/routes" "$APP/public" -type f -printf '%T@\n' 2>/dev/null | sort -nr | head -n1)"
fi

mkdir -p "$(dirname "$LAST")"
[ -f "$LAST" ] || echo "" > "$LAST"
PREV="$(cat "$LAST" || true)"

if [ "$SIG" != "$PREV" ]; then
  echo "$SIG" > "$LAST"
  echo "== change detected: $SIG =="
  if "$APP/tools/agent_guard.sh"; then
    echo "guard: OK"
  else
    echo "guard: FAIL (see storage/logs/agent_guard_*.log)"
    exit 1
  fi
else
  echo "no-change"
fi
