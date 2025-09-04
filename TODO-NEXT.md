# SwaedUAE — Next Fixes & Implementations (Authoritative Checklist)

## 0) Current state (baseline we saw)
- Routes in place for: `/signin` (redirect), `/login` (volunteer/org views), `/opportunities` (public index + show), `/verify`, `/gallery`, profile + avatar.
- Migrations added: `volunteer_hours`, `opportunity_applications`, `qr_scans`, `geo_logs`.
- Views exist for volunteer profile (avatar upload), public opportunities, auth pages.
- Admin routes exist for opportunities and QR issuance.
- Socialite & UAE PASS provider scaffolding present; env placeholders exist.

---

## 1) Blockers to stabilize this week
- [ ] **Make org login/register explicit & named**
  - Route: `Route::get('/org/login', fn()=>view('auth.organization_login',['type'=>'org']))->name('org.login');`
  - Route: `Route::get('/org/register', fn()=>view('org.register'))->name('org.register');`
  - Header “Login / Register” → `/signin` (so `?type=` is respected).
- [ ] **/signin redirects tested** for `?type=organization|volunteer` and no type.
- [ ] **Canonical opportunities**: keep public index/show in one place; remove duplicates if any.

---

## 2) QR Attendance (scan) — complete flow
- [ ] **Controller** (`QrScanController@store`):
  - Input: `code` (string, optional), `opportunity_id` (int, optional), `action` = checkin|checkout, `lat`,`lng` optional.
  - Resolve opportunity by: exact ID, or `OP-<id>`, or matches `opportunities.code/qr_code` if columns exist.
  - Write row to `qr_scans` (save IP, user_id, time).
  - On **check-in**: create a “session” marker (e.g., store last check-in in cache or find latest unmatched check-in).
  - On **check-out**: compute minutes from last check-in → insert into `volunteer_hours` with `opportunity_id`.
  - Double-submit guard: ignore second identical action within N seconds.
- [ ] **HoursService**:
  - `start(user_id, opportunity_id, t_in)`
  - `finish(user_id, opportunity_id, t_out)` → minutes = max(0, t_out - t_in); round to 0.25h; write `volunteer_hours`.
  - Edge cases: missing check-in → log and warn; multiple overlaps → close oldest.
- [ ] **Frontend scan page** (`/scan`):
  - [ ] Ask for geolocation; show a “Enable location” helper button; degrade gracefully if denied.
  - [ ] If URL has `?op=ID&code=XYZ&action=checkin`, auto-prefill and focus button.
  - [ ] Success & error toasts (localized EN/AR).
- [ ] **Geofence (optional, if needed now)**:
  - Add to `opportunities`: `lat`, `lng`, `radius_m` (default null).
  - When present, enforce distance <= `radius_m`; otherwise warn and allow admin override.

---

## 3) Opportunities (public & org)
- [ ] Public **index**: search, category, region filters; pagination keeps query.
- [ ] Public **show**: “Apply / Register” → if not logged in, go to `/signin?next=/opportunities/{id}`; else create application.
- [ ] Admin: ensure routes
  - `admin/opportunities/{id}/qr/issue` → printable QR sheet (A4) using `simple-qrcode` (already installed).
  - `admin/opportunities/{id}/qr/reset` to invalidate old codes if needed.
- [ ] Org panel: attendance and shortlist already wired; ensure exports & slot caps work with new hours.

---

## 4) UAE PASS (productionize)
- [ ] Fill `.env`: `UAEPASS_CLIENT_ID`, `UAEPASS_CLIENT_SECRET`, endpoints & redirect.
- [ ] Register provider (present in `bootstrap/providers.php`); add login buttons on both volunteer & org pages.
- [ ] Callback: map attributes → `users` table; auto-create user (role default volunteer; org flow optional).
- [ ] Test sandbox → prod; add error fallbacks to classic email login.

---

## 5) Emails & notifications
- [ ] Set `MAIL_FROM_ADDRESS` and name; verify reset password and verification emails.
- [ ] Optional: notify user on check-in/out with summary.
- [ ] Admin daily report: new scans, orphaned check-ins, hours added.

---

## 6) Data model & indexes
- [ ] Indexes:
  - `qr_scans`: `user_id`, `opportunity_id`, `action`, `code`, `scanned_at`.
  - `volunteer_hours`: `user_id`, `opportunity_id`, `created_at`.
- [ ] If using geofence: migrate `opportunities` with `lat,lng,radius_m` and seed some samples.

---

## 7) Admin tools
- [ ] **Attendance Log screen** (filter by date/opportunity/user; export CSV).
- [ ] **Orphaned sessions fixer**: list open check-ins > X hours; bulk close with chosen cutoff.
- [ ] **Manual adjust**: add/subtract minutes for a user/opportunity; audit log entry.

---

## 8) Profile & dashboard polish
- [ ] Avatar placeholder & size limits (already enforced); compress to webp on upload (optional).
- [ ] Dashboard KPIs: total hours (from `volunteer_hours`), upcoming (from `opportunity_applications` + `opportunities`).
- [ ] Certificates area: link to verify page with code.

---

## 9) Internationalization (EN/AR)
- [ ] Ensure all new strings are in lang files; `rtl_overrides.blade.php` loaded on AR.
- [ ] Persist lang choice; switcher in header; meta tags with `dir="rtl"` on AR.

---

## 10) Security & reliability
- [ ] Rate limit `/login` and `/scan` actions (e.g., 20/min per IP).
- [ ] CSRF on forms (already default).
- [ ] Custom 404/500 pages with header/footer.
- [ ] Backups: DB nightly + weekly; file backup for `storage/app/public`.
- [ ] Error monitoring: log channels rotate; optional external notifier.
- [ ] Sitemap via spatie; robots.txt allow public pages.

---

## 11) DevOps
- [ ] Make **staging** subdomain with same env minus payment/UAE PASS prod keys.
- [ ] Deployment script: backup → migrate → cache clear → cache routes/config/views.
- [ ] CI reminder: run `phpstan`/`pint` (optional), and smoke tests after deploy.

---

## 12) Acceptance checklist (go-live)
- [ ] Volunteer can login (email & UAE PASS), view dashboard, check-in/out, see hours.
- [ ] Org can login, issue QR, see scans, export hours.
- [ ] Admin can finalize hours and export reports.
- [ ] Public can browse opportunities, apply, and verify certificates.
- [ ] EN/AR correct, mobile responsive, no 404s from nav.

---

## 13) Nice-to-have (post-launch)
- [ ] Real-time queue (Horizon) for emails/exports.
- [ ] PWA install banner and offline QR capture (sync later).
- [ ] Attendance kiosk mode (tablet friendly).
- [ ] Leaderboard / badges from `volunteer_hours`.

---

## Owners (suggested)
- Auth/UAE PASS: @backend
- QR/Hours/Scan: @backend + @frontend
- Opportunities public/SEO/i18n: @frontend
- Admin tools/Exports: @backend
- Ops/Monitoring: @devops

