#!/usr/bin/env bash
set -euo pipefail
f="routes/web.php"

# 1) Only one Route import
c=$(grep -cE '^\s*use\s+Illuminate\\Support\\Facades\\Route\s*;' "$f"); [[ $c -ge 1 ]] || { echo "FAIL: Route import missing"; exit 1; }
# 2) No unquoted Route paths
! grep -nE "Route::(get|view)\(\s*/" "$f" || { echo "FAIL: unquoted route path"; exit 1; }
# 3) No bad require syntax
! grep -nE "^\s*require\s+['\"]__DIR__['\"]/.*" "$f" || { echo "FAIL: bad require syntax"; exit 1; }
# 4) Exactly one verify route
[[ $(grep -n "Route::get('/certificates/verify/{code?}'," "$f" | wc -l | tr -d ' ') -eq 1 ]] || { echo "FAIL: duplicate or missing verify route"; exit 1; }
echo "route_guard: OK"
