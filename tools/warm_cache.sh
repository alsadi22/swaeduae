#!/usr/bin/env bash
set -e
PHP_BIN="${PHP_BIN:-/opt/alt/php84/usr/bin/php}"; [ -x "$PHP_BIN" ] || PHP_BIN="php"

$PHP_BIN artisan config:clear  >/dev/null || true
$PHP_BIN artisan cache:clear   >/dev/null || true
$PHP_BIN artisan route:clear   >/dev/null || true
$PHP_BIN artisan view:clear    >/dev/null || true

$PHP_BIN artisan config:cache
$PHP_BIN artisan route:cache
$PHP_BIN artisan view:cache
