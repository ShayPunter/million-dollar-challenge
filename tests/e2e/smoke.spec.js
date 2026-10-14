import { expect, test } from '@playwright/test';

test('home page renders', async ({ page }) => {
    const errors = [];
    page.on('pageerror', (err) => errors.push(err.message));

    await page.goto('/');
    await expect(page.getByRole('link', { name: 'Get Started' }).first()).toBeVisible();
    expect(errors).toEqual([]);
});

test('register, build a grid and see it on the public page', async ({ page, browser }) => {
    const id = Date.now();
    const slug = `e2e-${id}`;

    await page.goto('/register');
    await page.locator('#name').fill('E2E User');
    await page.locator('#email').fill(`e2e-${id}@example.test`);
    await page.locator('#password').fill('correct-horse-battery');
    await page.locator('#password_confirmation').fill('correct-horse-battery');
    await page.getByRole('button', { name: 'Register' }).click();
    await expect(page).toHaveURL(/\/dashboard$/);

    await page.locator('#slug').fill(slug);
    await page.locator('#title').fill('My E2E Grid');
    await page.locator('#description').fill('Created by the Playwright smoke test.');
    await page.getByRole('button', { name: 'Create Page' }).click();
    await expect(page.getByRole('heading', { name: 'Projects' })).toBeVisible();

    await page.getByRole('button', { name: 'Add Project' }).click();
    await page.locator('#project_name').fill('Side hustle');
    await page.getByRole('button', { name: 'Create Project' }).click();
    await expect(page.getByText('Side hustle')).toBeVisible();

    await page.getByRole('button', { name: 'Add Revenue' }).first().click();
    await page.locator('#revenue_amount').fill('2500');
    await page.locator('form').getByRole('button', { name: 'Add Revenue' }).click();
    await expect(page.getByText('$2,500').first()).toBeVisible();

    await page.goto(`/grid/${slug}`);
    await expect(page.getByText('My E2E Grid')).toBeVisible();
    await expect(page.getByText('$2,500').first()).toBeVisible();

    // A guest must not see the owner's account details (Inertia props are in the HTML).
    const guest = await browser.newContext();
    const guestPage = await guest.newPage();
    await guestPage.goto(`/grid/${slug}`);
    await expect(guestPage.getByText('My E2E Grid')).toBeVisible();
    const html = await guestPage.content();
    expect(html).not.toContain(`e2e-${id}@example.test`);
    expect(html).not.toContain('email_verified_at');
    await guest.close();
});

test('a failed Google sign-in shows a generic error on the login page', async ({ page }) => {
    // No OAuth state in the session, so Socialite rejects the callback.
    await page.goto('/auth/google/callback?code=fake&state=fake');
    await expect(page).toHaveURL(/\/login$/);
    await expect(page.getByRole('alert')).toHaveText('Google sign-in failed, please try again.');
});
