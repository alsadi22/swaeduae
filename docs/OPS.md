# Operations Guide

## Deployment Model

Releases are stored under `releases/` on the server. A `current` symlink points to the active release. Deploys are performed by running `/usr/local/bin/swaed_deploy_pr.sh <sha>` which fetches the release, installs dependencies, and swaps the `current` symlink.

## Environment

- `APP_DEBUG=false`
- `LOG_LEVEL=info`
- `QUEUE_CONNECTION=database`
- SQLite is the default database. A MySQL connection is scaffolded in `config/database.php` for future migration.

## Health Checks

Nightly at 03:20 UTC the scheduler runs `swaed:full-health` which executes `tools/full_health.sh` and writes reports to `public/health/`.

To run health scripts locally:

```bash
bash tools/health.sh
bash tools/full_health.sh
bash tools/roadmap_check.sh
bash tools/deep_check.sh
```
