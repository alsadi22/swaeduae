#!/usr/bin/env bash
set -euo pipefail
LAY=resources/views/org/layout.blade.php
[ -f "$LAY" ] || { echo "Missing $LAY"; exit 1; }
cp "$LAY" "${LAY}.bak_$(date +%F_%H%M%S)"

# Fix @include('view', )  ->  @include('view')
sed -Ei "s/@include\(\s*(['\"][^'\"]+['\"])\\s*,\\s*\)/@include(\1)/g" "$LAY"

# (Optional hardening) convert @include to @includeIf to avoid future missing-partial crashes
# sed -Ei "s/@include\(/@includeIf(/g" "$LAY"

echo "Patched $LAY"
