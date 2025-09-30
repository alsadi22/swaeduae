#!/usr/bin/env bash
PHP_BIN=${PHP_BIN:-php}
set -euo pipefail
LAY='resources/views/public/layout.blade.php'
echo "[public/layout.blade.php first 120 lines]"
nl -ba "$LAY" | sed -n '1,120p'
echo
echo "[@include lines found]"
grep -n "@include" "$LAY" || echo "(no includes)"
echo
echo "[does it yield content?]"
grep -n "@yield('content')" "$LAY" || echo "(no @yield('content'))"
