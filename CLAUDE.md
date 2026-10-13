# My Million Dollar Grid

Laravel 11 + Inertia/Vue 3 app for tracking revenue towards $1M as a 1,000-square grid.
Repo: `ShayPunter/million-dollar-challenge` (stays on Shay's personal account, not PunterDigital).
Live: https://mymilliondollargrid.com/ (Laravel Forge, MariaDB).

## Commands

| What | Command |
|---|---|
| Install | `composer install && npm ci` |
| Run (dev) | `composer run dev` (needs `.env` + a database) |
| Lint | `./vendor/bin/pint --test` (fix: `./vendor/bin/pint`) |
| Unit/feature tests | `php artisan test` |
| Build | `npm run build` |
| E2E | `npm run build && npm run test:e2e` |

## Layout

- `routes/web.php`: all routes (no separate API routes file).
- `app/Http/Controllers`: Dashboard, Project, Revenue, UserPage (public grid), Google (OAuth), Admin.
- `resources/js/Pages`: Inertia pages. `PublicPage.vue` is the shareable grid at `/grid/{slug}`.
- Grid maths lives in controllers: `completed_cubes = floor(total / 1000)`.

## Testing notes

- Feature tests render the Blade layout and need `public/build/manifest.json`: run `npm run build`
  before `php artisan test` on a fresh checkout.
- `phpunit.xml` forces in-memory SQLite. Never run tests against the `.env` database: the Pest
  suite uses `RefreshDatabase` and would wipe it.
- `pint` is not always idempotent (a trailing comment after a method signature took two passes);
  run `pint --test` after `pint`.
- Playwright (`playwright.config.js`) starts `php artisan serve` with env-var overrides pointing at
  `storage/e2e/e2e.sqlite`. Environment variables beat `.env` in Laravel, so the real database is
  never touched. The DB is reset by the webServer command, not by the config file (workers reload
  the config after the server has migrated).
- A stale `public/hot` file (left by `npm run dev`) makes Laravel load assets from the Vite dev
  server and the E2E pages render blank. Delete it before running E2E against a build.

## Deployment / release

- Forge appears to auto-deploy `main` on push (the admin panel went live when PR #1 merged).
  Treat every merge to `main` as a production deploy: CI must be green and E2E run first.
- Web-only app: no desktop builds. A release is a `vX.Y.Z` tag + GitHub Release; the deploy is Forge.

## Quirks

- `app/Models/Challenge.php` has no namespace and the `/v1/challenges` routes are broken (500).
  Leftover from an earlier design; see the audit card before touching it.
- `config/app.php` `admin_emails` gates `/admin`.
