#!/usr/bin/env bash
set -euo pipefail
cd "$(dirname "$0")/.."

# Ensure the base script exists
if [ ! -x tools/p3_opportunities_drilldown.sh ]; then
  echo "ERROR: tools/p3_opportunities_drilldown.sh not found or not executable." >&2
  exit 1
fi

STAMP="$(date +%F-%H%M%S)"
OUT="reports/p3_opportunities_drilldown_${STAMP}.txt"
mkdir -p reports

# Run the drilldown and capture everything
bash tools/p3_opportunities_drilldown.sh > "$OUT" 2>&1

echo "Saved to: $OUT"
