#!/usr/bin/env bash
PHP_BIN=${PHP_BIN:-php}
set -euo pipefail

host="https://swaeduae.ae"
admin_host="https://admin.swaeduae.ae"

check() {
  local path="$1" expected="$2" base="${3:-$host}"
  code=$(curl -s -o /dev/null -w "%{http_code}" "$base$path")
  if [ "$code" != "$expected" ]; then
    echo "FAIL $base$path expected $expected got $code"; exit 1
  else
    echo "OK   $base$path $code"
  fi
}

# Public
check "/" 200
check "/opportunities" 200
check "/partners" 200
check "/qr/verify" 200

# Protected (should be 302 when guest)
check "/applications" 302
check "/certificates" 302
check "/my/profile" 302
check "/admin" 302 "$admin_host"
