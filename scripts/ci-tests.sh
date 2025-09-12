#!/usr/bin/env bash
set -euo pipefail
php artisan test --filter=VolunteerHoursTest
php artisan test --filter=VolunteerHoursEdgeTest
