# SwaedUAE Delivery Process

1) Start a feature branch
   - `start_feature <slug>`  (or `git checkout -b feature/<slug>`)

2) Code → commit → push → PR → auto-merge
   - `ship "feat|fix: short message"`

3) Watch CI & deploy
   - `watch_branch feature/<slug>`
   - `watch_branch main`

4) Verify / rollback
   - on server: `releases_status` + `verify_site`
   - if needed: `rollback_prev`

Rules:
- Never edit /var/www/swaeduae/current directly.
- All changes land via PR to main. CI must be green.
- Migrations run on deploy (`artisan migrate --force`).
