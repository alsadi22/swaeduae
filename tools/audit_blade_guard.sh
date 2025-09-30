#!/usr/bin/env bash
set -euo pipefail
fail=0
grep -RIn --include="*.blade.php" '@extends(' resources/views | grep -v "@extends('" | grep -v '@extends("' && fail=1 || true
grep -RIn --include="*.blade.php" '@section(' resources/views | grep -v "@section('" | grep -v '@section("' && fail=1 || true
grep -RIn --include="*.blade.php" '@include('   resources/views | grep -v "@include('"   | grep -v '@include("' && fail=1 || true
grep -RIn --include="*.blade.php" '@includeIf(' resources/views | grep -v "@includeIf('" | grep -v '@includeIf("' && fail=1 || true
grep -RIn --include="*.blade.php" '{{ *url(/' resources/views && fail=1 || true
grep -RIn --include="*.blade.php" 'request( *code *)' resources/views && fail=1 || true
grep -RIn --include="*.blade.php" '{{ *code *}}' resources/views && fail=1 || true
grep -RIn --include="*.blade.php" -E "date\\(\\s*Y\\s*\\)|->format\\(\\s*Y\\s*\\)" resources/views && fail=1 || true
php artisan route:list| awk -F '|' 'NR>2 {gsub(/ /,"",$2); if($2!="") c[$2]++} END{for(n in c) if(c[n]>1) print n" x"c[n]}' | grep . && fail=1 || true
find app routes -name '*.php' -print0 | xargs -0 -n1 -P4 php -l | grep -E 'Parse error|syntax error|Errors parsing' && fail=1 || true
exit $fail
