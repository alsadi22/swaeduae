# Operations Guide

## Deployment Model

Releases live under `releases/` with a shared `.env` outside each release. A `current` symlink points to the active release.
Deploys use `/usr/local/bin/swaed_deploy_pr.sh <PR#>` for pull requests or `/usr/local/bin/swaed_deploy_pr.sh main` for the main branch to fetch the release, install dependencies, and swap the symlink.

### Deploy

After the `current` symlink is updated, link in the shared environment and rebuild the configuration cache:

```bash
ln -sf /var/www/swaeduae/shared/.env /var/www/swaeduae/current/.env
cd /var/www/swaeduae/current && php artisan config:clear && php artisan config:cache
```

The `.env` resides in `shared` so all releases use identical settings. Refreshing the config cache ensures Laravel picks up those values instead of falling back to framework defaults.

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
