# SwaedUAE Ops Reference
- Stack: Laravel 11, PHP 8.4, Argon theme (no NPM, no Vite).
- Theme applied in Blade; assets under public/vendor/argon/.
- Auth: unified login/register; volunteer + organization via query param `type`.
- GET /logout allowed via route handler using `auth()->logout()`; POST logout from framework remains.
- CSP via app/Http/Middleware/SecurityHeaders.php; extra hosts from CSP_EXTRA in .env.
- Useful commands:
  - php artisan optimize:clear
  - ./ops/doctor.sh
  - sed/awk/cat for audits (see commit history).

## Auth routes quick reference
- `/signin` → options page (Google/Apple/UAE PASS) or email login fallback. **name:** `signin.options`
- `/login` → shows options unless `?type=email` (then email form).
- `/login/email` → direct email form. **name:** `login.email`
- `/register` → registration page.
- Legacy aliases:
  - `/volunteer/login` → 302 → `/login?type=volunteer`
  - `/org/login`, `/organization/login` → 302 → `/login?type=organization`
- Logout:
  - `POST /logout` (framework) **name:** `logout` / `logout.perform` depending on guards
  - `GET /logout` (compat) **name:** `logout.get` → safely logs out via `auth()->logout()`
- Profile alias: `/profile` → 302 → `volunteer.profile`
- Email verification:
  - `POST /email/verification-notification` **name:** `verification.send`
  - `GET /email/verify` **name:** `verification.notice`
