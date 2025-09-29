#!/usr/bin/env bash
set -euo pipefail
urls=(
  "https://swaeduae.ae/org/register"
  # add others here if needed, e.g. contact page etc.
)
for u in "${urls[@]}"; do
  printf "\n== %s ==\n" "$u"
  ./tools/form_probe.sh "$u"
  ./tools/form_probe.sh "$u" --bot
done
