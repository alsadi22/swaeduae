#!/usr/bin/env bash
set -euo pipefail
files=$(./tools/list_org_includes.sh | awk '{print $2}' | grep '^resources/' || true)
echo "Files:"; echo "$files"
echo; echo "Vars referenced:"
grep -hRoE '\$[A-Za-z_][A-Za-z0-9_]*' $files \
 | sed -E 's/[^$A-Za-z0-9_].*//' \
 | grep -Ev '^\$(loop|errors|message|slot|__data|__path|attributes)$' \
 | sort -u | nl -ba
