# Operations Guide

## Deployment Model

Releases live under `releases/` with a shared `.env` outside each release. A `current` symlink points to the active release.
Deploys use `/usr/local/bin/swaed_deploy_pr.sh <PR#>` for pull requests or `/usr/local/bin/swaed_deploy_pr.sh main` for the main branch to fetch the release, install dependencies, and swap the symlink.

## Environment

- `APP_DEBUG=false`
- `LOG_LEVEL=info`
- `QUEUE_CONNECTION=database`
- Queue worker: `swaed-queue.service`
- SQLite is the default database. A MySQL connection is scaffolded in `config/database.php` for future migration.

## Health Checks

Nightly at 03:20 UTC the scheduler runs `swaed:full-health` from `/var/www/swaeduae/current` and writes reports to `public/health/`.

To run health scripts locally:

```bash
bash tools/health.sh
bash tools/full_health.sh
bash tools/roadmap_check.sh
bash tools/deep_check.sh
```
