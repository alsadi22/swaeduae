#!/usr/bin/env bash
set -euo pipefail

# Link shared .env and rebuild config cache
ln -sf /var/www/swaeduae/shared/.env /var/www/swaeduae/current/.env
cd /var/www/swaeduae/current && php artisan config:clear && php artisan config:cache
ENV_LINKED=$(readlink -f /var/www/swaeduae/current/.env)
echo "ENV_LINKED=$ENV_LINKED"
