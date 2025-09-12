# Changelog

## 2025-08-17
- Fixed volunteer login redirect loop; `/volunteer/login` now returns 200.
- Added `App\Http\Controllers\Auth\SocialAuthController` and routes:
  - `GET /auth/google/redirect` → working (302 to accounts.google.com)
  - `GET /auth/google/callback`
  - `GET /auth/facebook/redirect|callback` (present; requires FACEBOOK_* env)
- Added UAE PASS scaffolding:
  - `App\Http\Controllers\Auth\UAEPassController` with `redirect`/`callback`
  - Routes: `GET /auth/uaepass/redirect|callback`
  - Installed `jumbojett/openid-connect-php`; awaiting `UAEPASS_ISSUER` and `UAEPASS_DISCOVERY_URL` envs.
- Organization registration:
  - `/org/register` now renders the rich form (`resources/views/org/register.blade.php`)
  - Added **logo** and **trade license** file inputs (multipart form)
  - POST wired to `OrganizationRegisterController@store` for:
    - `POST /org/register` (name: `org.register.submit`)
    - `POST /organization/register` (name: `register.organization.store`)
  - Created & ran migration: `create_organization_registrations_table`
  - Files stored on `public` disk (symlink OK).
- Legacy aliases added:
  - `GET /organization/login` → `login.organization`
  - `GET /organization/register` → `register.organization`
- Removed old `abort(501)` POST stubs.
- Volunteer login blade now includes social partial; Google visible; UAEPASS button placeholder; Facebook shows once env is provided.

## 2025-08-16
- Normalized volunteer auth routes/names.
- Ensured `config/services.php` entries: google, facebook (optional), uaepass (with redirect).
- Added `auth/partials/vol-social.blade.php` and included it from `auth/volunteer_login.blade.php`.
