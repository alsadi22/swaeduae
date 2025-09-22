#!/usr/bin/env bash
set -euo pipefail
pat="Route::middleware\\(.*\\)->\\s*view\\("
files=$(grep -RIl --include="*.php" -E "$pat" routes || true)
if [ -n "$files" ]; then
  echo "ERROR: Chained ->view() after middleware found in:"
  echo "$files"
  exit 1
fi
