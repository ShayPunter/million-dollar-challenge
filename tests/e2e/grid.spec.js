import { expect, test } from '@playwright/test';
import { seedGrid } from './env.js';

// The public grid (#5750): one node per cube, one shared tooltip, a short
// scale-in wave and a pulse on the newest cube only.

const tooltip = (page) => page.locator('[data-test="cube-tooltip"]');
const cells = (page) => page.locator('[data-test="cube-grid"] > div');

test('the grid stays light, draws within about a second and pulses one cube', async ({ page }) => {
    const slug = seedGrid(`grid-${Date.now()}`, 1000000);
    const errors = [];
    page.on('pageerror', (err) => errors.push(err.message));

    await page.goto(`/grid/${slug}`);
    await expect(page.getByRole('group', { name: /1,000 of 1,000 cubes complete/ })).toBeVisible();

    // 1,000 cells plus the page around them (it was ~5,000 with a tooltip per cell).
    expect(await page.evaluate(() => document.querySelectorAll('*').length)).toBeLessThanOrEqual(1200);

    // Every cube has scaled in within ~1 s of the page appearing (it took 10 s for a full grid).
    await expect
        .poll(() => page.evaluate(() => document.getAnimations().filter((a) => a.animationName === 'scale-in').length), {
            timeout: 3000,
        })
        .toBe(0);

    // Only the newest cube pulses (plus the "Last Update" dot).
    const infinite = await page.evaluate(() =>
        document.getAnimations()
            .filter((a) => a.effect.getTiming().iterations === Infinity)
            .map((a) => a.animationName)
            .sort(),
    );
    expect(infinite).toEqual(['big-pulse', 'pulse']);
    expect(errors).toEqual([]);
});

test('the tooltip shows the amount on hover and with the arrow keys', async ({ page }) => {
    const slug = seedGrid(`grid-tip-${Date.now()}`, 300500);
    await page.setViewportSize({ width: 1280, height: 900 });
    await page.goto(`/grid/${slug}`);
    await expect(cells(page).nth(299)).toHaveClass(/bg-green-500/);
    await expect(cells(page).nth(300)).toHaveClass(/bg-gray-200/);
    await expect(tooltip(page)).toHaveCSS('opacity', '0');

    await cells(page).nth(4).hover();
    await expect(tooltip(page)).toHaveText('$5,000');
    await expect(tooltip(page)).toHaveCSS('opacity', '1');

    await cells(page).nth(799).hover();
    await expect(tooltip(page)).toHaveText('$800,000');

    await page.mouse.move(5, 5);
    await expect(tooltip(page)).toHaveCSS('opacity', '0');

    // Keyboard: Tab onto the grid; the cursor starts on the newest cube.
    for (let n = 0; n < 5 && !(await page.locator('[data-test="cube-grid"]:focus').count()); n++) {
        await page.keyboard.press('Tab');
    }
    await expect(page.locator('[data-test="cube-grid"]')).toBeFocused();
    await expect(tooltip(page)).toHaveText('$300,000');
    await expect(page.locator('[data-test="cube-focus"]')).toBeVisible();

    await page.keyboard.press('ArrowRight');
    await expect(tooltip(page)).toHaveText('$301,000');
    await page.keyboard.press('ArrowDown'); // 40 columns at this width
    await expect(tooltip(page)).toHaveText('$341,000');
    await page.keyboard.press('Home');
    await expect(tooltip(page)).toHaveText('$1,000');
    await page.keyboard.press('ArrowLeft'); // stays on the first cube
    await expect(tooltip(page)).toHaveText('$1,000');
    await page.keyboard.press('End');
    await expect(tooltip(page)).toHaveText('$1,000,000');

    await page.keyboard.press('Shift+Tab');
    await expect(tooltip(page)).toHaveCSS('opacity', '0');
    await expect(page.locator('[data-test="cube-focus"]')).toHaveCount(0);
});

test('nothing animates with reduced motion', async ({ browser }) => {
    const slug = seedGrid(`grid-calm-${Date.now()}`, 420000);
    const context = await browser.newContext({ reducedMotion: 'reduce' });
    const page = await context.newPage();

    await page.goto(`/grid/${slug}`);
    await expect(cells(page).nth(419)).toHaveClass(/bg-green-500/);
    await expect(cells(page).nth(419)).toHaveCSS('opacity', '1');
    expect(await page.evaluate(() => document.getAnimations().length)).toBe(0);
    await context.close();
});
