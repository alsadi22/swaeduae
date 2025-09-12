# SWAED UAE — Full Product & Engineering Roadmap

_Last updated: $(date)_

This document captures **where we are** (features/pages that exist), **what’s scaffolded**, and **what’s left to finish** — based on the current codebase you showed (routes, controllers, blades, migrations, and configs).

---

## 0) Principles (to stop regressions)
- **Additive changes only.** Never delete/replace existing files; prefer new files and small includes.
- **Feature flags via env/config.** Stubbed integrations (e.g., UAE PASS) stay disabled until credentials exist.
- **Public pages must never 500** — always pick an existing fallback view.
- **Audit trail.** Save every plan/report under `docs/` and `ops/` and commit to git.

---

## 1) Environment & stack (observed)
- **PHP** 8.4.x, **Laravel** 11.x.
- Storage symlink present; `public/storage` -> `storage/app/public`.
- Many route groups & controllers for **Admin**, **Org**, **Volunteer**, **Public**.
- Migrations recently added for: `volunteer_hours`, `opportunity_applications`, `qr_scans`, `geo_logs`.

---

## 2) Feature Map — What exists now

### Public site
- **Home/News/Events/Downloads/Partners/Sharjah region** pages wired.
- **Opportunities (public)**  
  - Routes: `GET /opportunities`, `GET /opportunities/{id}`  
  - Defensive: checks table + chooses first existing view among  
    `resources/views/opportunities/public/{index,show}.blade.php`, fallbacks to `opportunities.index/show` or `public.*`.
- **Verify certificate (public)** `GET|POST /verify` returns `public.verify`.
- **Gallery** `GET /gallery` (blade exists).

### Authentication & Users
- **Laravel Auth routes** (`Auth::routes()`).
- **Signin alias**: `GET /signin` and `/sign-in` redirect to appropriate login.
- **Volunteer login/register blades** under `resources/views/auth`.
- **Org login/register blades** under `resources/views/auth` and `resources/views/org/auth`.
- **Roles** via Spatie Permission (controllers test `hasRole('org'|'admin')`).
- **Volunteer Profile**  
  - Route: `GET /volunteer/profile` and alias `GET /profile` (same controller).  
  - Blade `resources/views/volunteer/profile.blade.php` shows KPIs, upcoming, latest certificates.
  - **Avatar upload/remove**: `POST/DELETE /profile/avatar` to `Volunteer\AvatarController`.

### Attendance / QR / Minutes / Geo (present)
- Organizer QR pages & QR endpoints exist (Admin/Org area).
- `qr_scans` + `geo_logs` tables in place.  
- Minutes pipeline present: check-in/out + minutes aggregation into `volunteer_hours` (dashboard reads total hours).

### Organization Portal
- Controllers & blades for: dashboard, events, opportunities, applicants, shortlist, attendance settings, reports, KYC, team, certificates, setup, settings.

### Admin Portal
- Controllers & blades for dashboards, users, events, opportunities, attendance, QR management (issue/reset/finalize), categories, media, news, pages, reports, exports, logs, site settings.

### Certificates / Transcript
- Controllers for `Certificate*`, public verify, and volunteer “latest certificates” table on profile.
- Transcript/printable PDFs wired through DomPDF packages (present in composer).

### Payments
- Stripe/PayTabs controller actions and routes under `/payment` (page, process, success). Views present/expected.

### SEO / Sitemap / Misc
- Sitemap controller present, SEO partials, social partials, RTL overrides, reusable header/footer/nav blades.

---

## 3) Scaffolded or stubbed (requires config/finishing)

1) **UAE PASS SSO**
   - Controllers + provider scaffolding exist.
   - **Need**: `.env` credentials & discovery/issuer; `config/services.php` block; route enable/guard; end-to-end test.

2) **Geolocation gating**
   - Browser geolocation is required on QR confirm.
   - **Blocker**: restrictive `Permissions-Policy: geolocation=()` header (if present globally).  
   - **Need**: allow `geolocation=(self)` (globally or at QR routes).

3) **CSRF exception drift**
   - Legacy exception for `qr-scan/*`; current routes use `/attendance/*`.  
   - **Need**: align CSRF exemptions or remove if fetch+token already correct.

4) **External QR image generation**
   - Using a third-party QR image endpoint for tokens.  
   - **Need**: local QR generation (Simple QrCode) + route to render PNG inline.

5) **Public error logs**
   - `public/error_log` observed.  
   - **Need**: move/rotate to `storage/logs`, clean webroot artifacts.

---

## 4) The “Do Not Lose” list (confirmed present)
- Public Opportunities list/show with graceful fallbacks.
- Volunteer profile + avatar upload/remove.
- Org QR issue/reset/finalize + attendee flows.
- Minutes sum on profile (via `volunteer_hours` fallback to certificates sum).
- Certificate verify public route + Admin/Org certificate management.
- Payments (Stripe/PayTabs) controllers & routes.
- KYC controllers (user/org).
- SEO/sitemap controllers & blades.
- Socialite stubs for future SSO, plus classic auth.

---

## 5) Work Breakdown — Phases & Tasks

### Phase A — Stabilize & Guard Rails (NOW)
- [ ] Add `docs/` roadmap (this file) and commit.  
- [ ] Remove/relocate `public/error_log` → `storage/logs` and ensure webserver doesn’t write to webroot.  
- [ ] Ensure `public/storage` symlink exists (it does).  
- [ ] Verify all public routes render even with empty DB (done for opportunities).  
- [ ] Add 5-minute smoke tests (curl/HTTP 200) for: `/`, `/opportunities`, `/opportunities/1?if-exists`, `/signin`, `/verify`.

### Phase B — Geolocation Unblock (QR)
- [ ] Adjust headers to permit geolocation on QR/attendance pages (`geolocation=(self)`).  
- [ ] QA in real phone browsers; confirm location prompt and lat/lng recorded into `geo_logs`/`qr_scans`.

### Phase C — Local QR Generation
- [ ] Add controller `QrImageController@show(token)` → returns PNG using `Simple QrCode`.  
- [ ] Replace external `<img src>` with local route.  
- [ ] Keep external as fallback behind a feature flag.

### Phase D — UAE PASS Integration
- [ ] Add `.env` keys: `UAEPASS_CLIENT_ID`, `UAEPASS_CLIENT_SECRET`, `UAEPASS_REDIRECT_URI`, `UAEPASS_DISCOVERY_URL`, `UAEPASS_ISSUER`.  
- [ ] Add `config/services.php` entry `uaepass` (uses env).  
- [ ] Wire routes: `/uaepass/redirect`, `/uaepass/callback`.  
- [ ] Map UAE PASS userinfo to local user; role default “volunteer”; optional org claim mapping.  
- [ ] End-to-end QA on staging with real credentials.

### Phase E — Attendance & Minutes Polish
- [ ] Validate clock-in/clock-out double-scan handling (idempotency).  
- [ ] Add admin override screen to correct minutes for a user/opportunity.  
- [ ] Export CSV for attendance with geo columns (lat/lng, IP, accuracy if available).  
- [ ] Add rate-limit / replay protection for QR tokens.

### Phase F — Volunteer Portal Finish
- [ ] “My Hours” page bound to `volunteer_hours` (with filters + CSV).  
- [ ] Show upcoming from applications join (already coded; ensure data exists).  
- [ ] “Download Transcript” link wired to DomPDF route (exists in blade via `Route::has`).  
- [ ] Avatar preview in header (via `components/avatar-controls` URL) across site.

### Phase G — Organization Portal Finish
- [ ] Applicants list filters & bulk decisions (routes exist).  
- [ ] Shortlist capacity controls (routes exist).  
- [ ] Attendance settings (grace periods, geo radius, QR refresh interval).  
- [ ] Reports: per-opportunity hours, geo map (static map first), CSV.

### Phase H — Admin Portal Finish
- [ ] Global dashboards: total volunteers, hours, events, active opportunities.  
- [ ] Site settings (logo, colors, footer links) read from DB.  
- [ ] Backup/export actions (routes exist) — ensure storage path & retention.

### Phase I — Payments (Stripe / PayTabs)
- [ ] Confirm keys in `.env`; sandbox test -> success page.  
- [ ] Payment webhooks route; mark order/donation records.  
- [ ] Admin ledger & refunds (manual link-outs initially).

### Phase J — SEO / Sitemap / Performance
- [ ] Ensure `sitemap.xml` includes `/opportunities` and `/opportunities/{id}`.  
- [ ] Add meta/og tags for opportunity show.  
- [ ] Cache public queries (index pagination, filters) with tags; invalidate on create/update.

### Phase K — Internationalization / RTL
- [ ] Verify `lang.switch` persists session.  
- [ ] Audit blades for `@lang()` / `__()` coverage; add missing strings.  
- [ ] Ensure all layouts behave in RTL (partial already present).

### Phase L — Security / Compliance
- [ ] CSRF exception paths: align with actual attendance endpoints or remove if not needed.  
- [ ] Rate-limit login, SSO callback, QR validations.  
- [ ] Avoid logging PII in `public` scope; move all logs to storage and rotate.

---

## 6) Acceptance Checks (quick)
- **Public list:** `/opportunities` returns 200 with empty DB (cards show “no results”).  
- **Public show:** `/opportunities/{id}` returns 200 when record exists; 404 otherwise.  
- **Signin:** `/signin?type=org` → org login view; `/signin?type=volunteer` → volunteer login view.  
- **Profile:** `/volunteer/profile` shows avatar controls and KPIs; avatar upload writes to `storage/app/public/avatars`.  
- **QR:** Organizer QR page shows PNG from local route; scanning records `qr_scans` row + geo prompt works.  
- **Minutes:** total hours on profile matches sum of hours in `volunteer_hours`.  
- **UAE PASS:** redirect/callback complete; account created or linked; then redirected to correct dashboard.

---

## 7) Risks & Mitigations
- **Geo blocked by headers** → fix header for geolocation on QR pages.  
- **External QR** → replace with local generation.  
- **Credential gaps** → keep features behind env checks until live.  
- **File sprawl (`*.bak` in app/`)** → move to `backups/` or track in git only to reduce autoload noise.

---

## 8) Tracking (owner / status / notes) — fill as you go

| Area | Task | Owner | Status | Notes |
|------|------|-------|--------|-------|
| Geo | Relax Permissions-Policy on QR pages |  | ☐ |  |
| QR  | Local QR image route |  | ☐ |  |
| SSO | UAE PASS env + services.php |  | ☐ |  |
| SSO | Redirect/callback wiring & tests |  | ☐ |  |
| Vol | My Hours page polish/export |  | ☐ |  |
| Org | Attendance settings |  | ☐ |  |
| Admin | Site settings UI |  | ☐ |  |
| Pay | Stripe sandbox pass |  | ☐ |  |
| SEO | sitemap.xml includes opps |  | ☐ |  |

---

## 9) Immediate next 5 actions (safe to do today)
1. Allow browser **geolocation** on QR pages (`geolocation=(self)`).
2. Add `services.uaepass` config block (env-gated).
3. Implement **local QR** render route and swap `<img>` sources.
4. Move **public error_log** to `storage/logs` and rotate.
5. Add curl smoke tests and a GitHub action to run them on deploy.

— end —
