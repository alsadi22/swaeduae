#!/usr/bin/env bash
set -euo pipefail
cd /home3/vminingc/swaeduae.ae/laravel-app

# Ensure the checker exists
if [ ! -x tools/p3_opportunities_check.sh ]; then
  echo "ERROR: tools/p3_opportunities_check.sh missing or not executable." >&2
  exit 1
fi

OUT_DIR="tools/reports"
mkdir -p "$OUT_DIR"
STAMP="$(date +'%Y-%m-%d-%H%M%S')"
OUT_FILE="$OUT_DIR/p3_opportunities_check_${STAMP}.txt"

# Run and capture all output
bash tools/p3_opportunities_check.sh > "$OUT_FILE" 2>&1

echo "Saved to: $OUT_FILE"
ls -lh "$OUT_FILE"
echo "---- last 30 lines ----"
tail -n 30 "$OUT_FILE"
