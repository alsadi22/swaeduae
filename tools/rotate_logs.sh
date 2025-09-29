#!/usr/bin/env bash
set -euo pipefail
cd /home3/vminingc/swaeduae.ae/laravel-app

stamp=$(date +%F-%H%M%S)
for f in storage/logs/scheduler.log storage/logs/sitemap.cron.log; do
  [ -f "$f" ] || continue
  sz=$(stat -c%s "$f" 2>/dev/null || stat -f%z "$f")
  if [ "${sz:-0}" -gt $((5*1024*1024)) ]; then  # >5MB → rotate
    cp -a "$f" "${f}.${stamp}"
    : > "$f"
  fi
done

# keep rotated cron logs 30 days
find storage/logs -type f \( -name "scheduler.log.*" -o -name "sitemap.cron.log.*" \) -mtime +30 -delete
