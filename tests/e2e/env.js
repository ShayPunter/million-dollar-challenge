import { execSync } from 'node:child_process';
import { resolve } from 'node:path';

// Shared by playwright.config.js (the server) and the specs (artisan calls),
// so both sides use the same throwaway SQLite database and APP_URL.
const dbPath = resolve('storage/e2e/e2e.sqlite');

export const port = Number(process.env.E2E_PORT ?? 8123);

export const env = {
    ...process.env,
    APP_ENV: 'testing',
    APP_DEBUG: 'true',
    APP_URL: `http://127.0.0.1:${port}`,
    DB_CONNECTION: 'sqlite',
    DB_DATABASE: dbPath,
    SESSION_DRIVER: 'file',
    CACHE_STORE: 'array',
    QUEUE_CONNECTION: 'sync',
    MAIL_MAILER: 'array',
};

/**
 * The link from the verification email. The array mailer keeps mail in the
 * server's memory, so build the same signed URL the email would contain.
 * Runs through the shell so a `php` shim (.bat on Windows) works too.
 */
export function verificationUrl(email) {
    if (!/^[\w.+-]+@[\w.-]+$/.test(email)) {
        throw new Error(`Unexpected email: ${email}`);
    }

    return execSync(`php tests/e2e/verification-url.php ${email}`, { env, encoding: 'utf8' }).trim();
}
