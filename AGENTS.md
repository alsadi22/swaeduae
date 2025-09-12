# AGENTS.md — SwaedUAE no-drift rules
# Repository Guidelines

## Project Structure & Module Organization
- `app/`: Laravel application code (HTTP controllers, models, jobs, policies).
- `routes/`: Route files (`web.php`, `api.php`, feature-specific route sets).
- `resources/`: Blade views, Tailwind assets, language files.
- `public/`: Public web root (no secrets). SEO assets: `robots.txt`, `sitemap.xml`.
- `config/`, `database/`, `bootstrap/`, `storage/`: Framework config, migrations/seeders, cache/bootstrap, app storage.
- `tests/`: PHPUnit tests (`Feature/` and `Unit/`). Add tests alongside changed routes/views.

## Build, Test, and Development Commands
- `composer install --no-interaction`: Install PHP deps.
- `npm ci && npm run build`: Install and build frontend assets.
- `php artisan migrate --seed`: Apply DB schema locally.
- `phpunit` or `php artisan test`: Run all tests; `phpunit --filter NameTest` for one.
- Caching (automation): `sudo -u www-data php artisan optimize && sudo -u www-data php artisan view:cache`.

## Coding Style & Naming Conventions
- PHP: PSR-12, 4-space indent; strict types where feasible.
- Controllers: `PascalCase`, actions return typed responses. Blade views: `kebab-case.blade.php` in feature folders.
- Routes: Group by middleware/prefix; use named routes (`name('area.action')`).
- Assets: Use Vite helpers in Blade; avoid hard-coded asset paths.

## Testing Guidelines
- Framework: PHPUnit; prefer Feature tests for routes/controllers and Blade rendering.
- Coverage: Exercise happy-path + auth/permission gates. Include CSRF and throttling when applicable.
- Naming: `Feature/<Area>/<Action>Test.php`; method names `test_*`.
- Run: `phpunit` locally before PR; ensure failing tests are fixed or skipped with rationale.

## Commit & Pull Request Guidelines
- Commits: Short, imperative subject; reference scope (`feat(auth): add login throttle)`. Keep diffs minimal.
- PRs: Describe intent, link issues, include screenshots for UI, and list changed routes/views. Update or add tests and the agent checker when routes/views/middleware change. Do not commit secrets.

## Security & Configuration Tips (No-Drift)
- Environment: `APP_ENV=production`, `APP_DEBUG=false`. Cookies: SameSite=Lax, Secure.
- Email: Zoho SMTP with App Password only; do not change mail stack.
- Forms: Require CSRF, honeypot field `__website`, throttle 5/min.
- CSP: Present; allow `plausible.io` (and reCAPTCHA domains when added).
- SEO: Per-page `<title>` and meta description; JSON-LD Organization + WebSite (+ SearchAction); maintain `robots.txt` and `sitemap.xml`.
- Ops: Cloudflare in front; origin bound to `127.0.0.1`. Run artisan caches as `www-data` in automation.

## Agent-Specific (SwaedUAE)
- Mission: Maintain and harden this Laravel app via small, safe patches with focused tests.
- Tech snapshot: Nginx + Cloudflare (EDGE), PHP-FPM, origin `127.0.0.1`, Zoho SMTP; CSP in `app/Http/Middleware/SecurityHeaders.php`; checker `./_tools/agent_check.sh`.
- Guardrails: Never weaken security headers. Make a timestamped backup before edits: `cp -a file file.bak.$(date +%F-%H%M%S)`. Prefer aliases/redirects over renaming canonical route names. Explain root cause in PRs and show a unified diff.
- Canonical routes: `home`→`/`, `about`→`/about`, `services`→`/services`, `contact.show`→`/contact-us`, `contact.send`→`POST /contact`. Aliases: `pages.about`→redirect `about`; `/destination`→redirect `/services`.
## Useful commands
- Deep health check: `./_tools/agent_check.sh`
- Rebuild caches (as www-data):
**Stack:** Laravel behind Cloudflare. Email via **Zoho SMTP** (App Password). DNS: Cloudflare.
Do **not** switch mail stack or add other mail services.

## Security
- APP_ENV=production, APP_DEBUG=false
- Cookies: SameSite=Lax, Secure
- Contact form: CSRF required, honeypot `__website`, throttle 5/min
- CSP present; allow plausible.io (and reCAPTCHA domains when added)

## SEO/Content
- Per-page <title> + meta description
- JSON-LD: Organization + WebSite (+ SearchAction)
- robots.txt + sitemap.xml

## Ops
- Cloudflare in front; origin at 127.0.0.1
- Run artisan caches as www-data in automation

## With Code Agents
- Minimal diffs/PRs; don’t commit secrets
- Prefer PHPUnit feature tests for any route/view changes
- If routes/views/middleware change, update tests and agent checker
