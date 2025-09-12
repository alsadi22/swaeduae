# Next Steps

- [ ] **UAE PASS (OIDC)**
  - Provide `UAEPASS_ISSUER` and `UAEPASS_DISCOVERY_URL` in `.env` (plus `UAEPASS_CLIENT_ID/SECRET`).
  - Implement full OIDC flow in `UAEPassController` using discovery doc and map user attributes → `users` table.
  - Add error handling for denied/expired auth; verify callback URL allowlisted in UAE PASS console.
  - Write feature tests for redirect/callback.

- [ ] **Facebook Login**
  - Add `FACEBOOK_CLIENT_ID`, `FACEBOOK_CLIENT_SECRET`, `FACEBOOK_REDIRECT_URI` to `.env`.
  - Verify app in Facebook developer console; test `oauth.facebook.redirect`.

- [ ] **Organization Registration Flow**
  - Replace temp `organization_registrations` insert with real `Organization` model/table.
  - Add admin review/approval + email notifications.
  - Harden validation (max sizes, mime whitelist) and virus scan if required.

- [ ] **Security & UX**
  - Ensure CSRF on forms; confirm CSP exceptions if any external scripts are introduced.
  - Surface friendly flash messages after social/UAEPASS callbacks.
  - Clean up legacy/bak view files to avoid confusion.

- [ ] **Tests & CI**
  - Add route/feature tests for: volunteer login page, Google redirect, org register submit & file upload.
  - Consider GitHub Actions to run `phpunit`, `php artisan test`, and `phpstan`.

- [ ] **Docs**
  - Update README with auth routes, required `.env` keys, and how to run social/UAEPASS locally.

