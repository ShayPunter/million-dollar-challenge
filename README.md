# My Million Dollar Grid

Track your journey to $1,000,000. Every $1,000 you earn fills one square of a 1,000-square grid.
Log revenue against your projects, and share a public page of your grid at `/grid/<your-slug>`.

**Live:** https://mymilliondollargrid.com/

## Stack

Laravel 12 (PHP 8.2+) · Inertia.js + Vue 3 · Tailwind CSS · Vite · MariaDB in production ·
Google sign-in via Laravel Socialite · Pest for tests · Playwright for end-to-end tests.

## Development

Requirements: PHP 8.2+ (with `pdo_sqlite` for tests), Composer, Node 20+.

```bash
composer install
npm ci
cp .env.example .env
php artisan key:generate
php artisan migrate
composer run dev          # app server, queue, logs and Vite together
```

For Google sign-in locally, set `GOOGLE_CLIENT_ID`, `GOOGLE_SECRET` and `GOOGLE_REDIRECT` in `.env`.

## Tests

```bash
./vendor/bin/pint --test  # code style
php artisan test          # unit + feature tests (in-memory SQLite)
npm run build
npm run test:e2e          # Playwright smoke test against a throwaway SQLite DB
```

## Deployment

Production runs on Laravel Forge at https://mymilliondollargrid.com/.

## Licence

MIT. See [LICENSE](LICENSE).
