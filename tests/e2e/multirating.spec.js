import { test, expect } from '@playwright/test';
import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));

const RECORD_URL =
  process.env.RECORD_URL ||
  'http://localhost/joomcck/index.php?option=com_joomcck&view=record&id=114&Itemid=133';

const SCREENSHOT_DIR = path.join(__dirname, 'screenshots');
fs.mkdirSync(SCREENSHOT_DIR, { recursive: true });

test.describe('multirating template', () => {
  test('renders list-group layout on desktop (1280x800)', async ({ page }) => {
    await page.setViewportSize({ width: 1280, height: 800 });
    await page.goto(RECORD_URL);

    const container = page.locator('.joomcck-multirating');
    await expect(container).toBeVisible();

    const items = container.locator('.list-group-item');
    expect(await items.count()).toBeGreaterThanOrEqual(2);

    await expect(items.first()).toHaveClass(/list-group-item-primary/);

    const flexDirection = await items
      .first()
      .evaluate((el) => getComputedStyle(el).flexDirection);
    expect(flexDirection).toBe('row');

    const box = await container.boundingBox();
    await page.screenshot({
      path: path.join(SCREENSHOT_DIR, 'multirating-desktop.png'),
      clip: box ?? undefined,
    });
  });

  test('stacks vertically on mobile (375x667)', async ({ page }) => {
    await page.setViewportSize({ width: 375, height: 667 });
    await page.goto(RECORD_URL);

    const container = page.locator('.joomcck-multirating');
    await expect(container).toBeVisible();

    const firstItem = container.locator('.list-group-item').first();
    const flexDirection = await firstItem.evaluate(
      (el) => getComputedStyle(el).flexDirection
    );
    expect(flexDirection).toBe('column');

    const box = await container.boundingBox();
    await page.screenshot({
      path: path.join(SCREENSHOT_DIR, 'multirating-mobile.png'),
      clip: box ?? undefined,
    });
  });
});
