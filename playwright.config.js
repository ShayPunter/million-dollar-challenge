import { defineConfig, devices } from '@playwright/test';
import { env, port } from './tests/e2e/env.js';

// E2E runs against `php artisan serve` with a throwaway SQLite database, so it
// never touches the MariaDB configured in .env. Environment variables win over
// .env in Laravel, which is what keeps the real database out of reach.
// The database file is reset by the webServer command, not here: this config
// is also loaded by every worker, after the server has migrated.

export default defineConfig({
    testDir: 'tests/e2e',
    fullyParallel: false,
    workers: 1,
    retries: process.env.CI ? 1 : 0,
    reporter: process.env.CI ? [['list'], ['html', { open: 'never' }]] : 'list',
    use: {
        baseURL: env.APP_URL,
        trace: 'retain-on-failure',
    },
    projects: [{ name: 'chromium', use: { ...devices['Desktop Chrome'] } }],
    webServer: {
        command: `php -r "@mkdir('storage/e2e'); file_put_contents('storage/e2e/e2e.sqlite', '');" && php artisan migrate:fresh --force && php artisan serve --host=127.0.0.1 --port=${port}`,
        url: `${env.APP_URL}/up`,
        env,
        reuseExistingServer: false,
        timeout: 120_000,
    },
});
