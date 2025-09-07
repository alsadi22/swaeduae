# Operations Guide

## Deployment Model

Releases live under `releases/` with a shared `.env` outside each release. A `current` symlink points to the active release.
Deploys use `/usr/local/bin/swaed_deploy_pr.sh <PR#>` for pull requests or `/usr/local/bin/swaed_deploy_pr.sh main` for the main branch to fetch the release, install dependencies, and swap the symlink.

### Deploy

`tools/deploy.sh` runs during each release and handles the environment link and cache refresh automatically:

```
ENV_LINKED=/var/www/swaeduae/shared/.env
```

It symlinks `current/.env` to `shared/.env` and rebuilds the config cache so Laravel reads the shared settings.

## Environment

- `APP_DEBUG=false`
- `LOG_LEVEL=info`
- `QUEUE_CONNECTION=database`
- Queue worker service: `swaed-queue.service`
- SQLite is the default database. A MySQL connection is scaffolded in `config/database.php` for future migration.

## Database Migrations

All migrations are idempotent and can be re-run safely. Each migration checks for existing tables or columns before applying
changes. Continuous integration verifies this by running `php artisan migrate --env=testing --force` against a sqlite database.

## Health Checks

Nightly at 03:20 UTC the scheduler runs `swaed:full-health` from `/var/www/swaeduae/current` and writes reports to `public/health/`.

Backups for code and DB live under `/root/backups/{code,db}`.

To run health scripts locally:

```bash
bash tools/health.sh
bash tools/full_health.sh
bash tools/roadmap_check.sh
bash tools/deep_check.sh
```

## Admin Panel Quickstart

The admin site lives on the `admin.swaeduae.ae` subdomain and uses the Argon layout.
Only users granted the `admin-access` ability can sign in. Common tasks:

- **Dashboard** – summarizes total users, organizations, pending approvals and
  hours. Cards link to Approvals, Certificates, Reports and Settings.
- **Approvals** – review organization sign‑ups and approve or suspend them.
- **Applicants** – browse opportunity applicants and export CSV lists.
- **Certificates** – search issued certificates, download the PDF, resend by
  email or revoke.
- **QR Verify** – manual lookup form that redirects to the public
  `/qr/verify/{code}` page.

From the main domain any request to `/admin/*` is redirected to the admin
subdomain. This separation keeps the public TravelPro pages free of admin
assets and avoids accidental Vite usage.
