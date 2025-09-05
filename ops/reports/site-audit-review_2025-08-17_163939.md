# SWAED UAE — Full Audit Review

**Scope:** routes, auth flows, QR check-in/out, minutes aggregation, UAE PASS wiring, geolocation, logs, backups, public views.

## Executive summary
- Core flows are present: public opportunities list/show, login (volunteer/org), profile + avatar, organizer QR issue, check-in/out, minutes roll-up. 
- Nothing critical removed; many backups exist in-tree; environment & headers need small tweaks to avoid breaking geo and UAE PASS.

## High-priority items
1) **UAE PASS config mismatch**
   - Controller expects `UAEPASS_DISCOVERY_URL` + `UAEPASS_ISSUER` and uses `config('services.uaepass')`.
   - Action: add a `services.uaepass` block and provide the issuer/discovery values in `.env`. Keep current stubs until prod values are ready.

2) **Geolocation blocked by headers**
   - Global `Permissions-Policy: geolocation=()` prevents browser geo prompts.
   - Action: allow on QR pages, e.g. `geolocation=(self)`, or apply a looser policy only on `/attendance/*`.

3) **Public error log present**
   - `public/error_log` exists; avoid exposing logs via the web root.
   - Action: move logging to `storage/logs` and block/cleanup the public file.

4) **Third-party QR image service**
   - QR images are rendered via an external API. Tokens/URLs are sent to that service.
   - Action: plan to switch to local generation (e.g. `simplesoftwareio/simple-qrcode`) to keep tokens on your host.

5) **CSRF exception path drift**
   - Legacy exception for `qr-scan/*` remains, while current routes use `/attendance/check-in|check-out`.
   - Action: either add `/attendance/*` to the exception (if still needed) or remove the stale pattern.

## What’s confirmed working/present
- **Routes:** admin/opportunities, org routes, public opportunities, QR endpoints, profile avatar upload/delete, sign-in aliasing.  
- **Public opportunities:** resilient to missing table; picks first existing view among `opportunities.public.index`, `opportunities.index`, `public.opportunities`.  
- **QR flow:** Organizer QR page renders check-in/out codes; dedicated attendance routes exist.  
- **Minutes:** Scan->attendance captures check-in/out; service proposes minutes and dashboard aggregates per user.  
- **UAE PASS stubs:** Controller + Socialite provider scaffolding exist; gated until `.env` is filled.  
- **Route files present:** `_early_auth_overrides.php`, `_opportunities.php`, `admin.php`, `org.php`, etc.  
- **Storage link noticed:** ensure `php artisan storage:link` is in place (symlink touched recently).  

## Lower-priority hygiene
- Many `*.bak` controllers and `routes/web.php.*` versions under app/. Consider moving to `_backups/` (non-autoloaded) or keeping via git only.
- Keep `Auth::routes()` if you rely on defaults; otherwise you can trim overlaps once custom auth is fully stable.

## Suggested next steps (safe + incremental)
1) Add `services.uaepass` and the missing `UAEPASS_DISCOVERY_URL` / `UAEPASS_ISSUER` in `.env`; keep your current placeholders for client credentials.
2) Relax geolocation header on QR pages (or globally to `geolocation=(self)`).
3) Ensure logs are not web-served; rotate into `storage/logs`.
4) Plan a local QR image route; keep external API as fallback until then.
5) Align CSRF exemptions with the final QR endpoints.

— end —
