# Multirating Responsive BS5 Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace the table-based multirating rating template with a Bootstrap 5 `list-group` + flex layout that is responsive at all viewports, and add a Playwright E2E test that verifies rendering at mobile and desktop widths.

**Architecture:** The multirating template is a single PHP view partial (`components/com_joomcck/views/rating_tmpls/multirating/default.php`) invoked by `RatingHelp::loadMultiratings()` (in `components/com_joomcck/library/php/helpers/rating.php:118-162`) via `include $path`. Replacing only the markup in that file is sufficient — no PHP API, no JS, and no CSS changes are needed. The E2E test is bootstrapped as a small Playwright setup at the repo root (no existing test infra).

**Tech Stack:** PHP 8.1+, Joomla 4.2+, Bootstrap 5, Playwright (Node.js, new), running against the local WAMP demo at `http://localhost/joomcck`.

---

## File Structure

**Modified:**
- `components/com_joomcck/views/rating_tmpls/multirating/default.php` — swap table markup for `list-group`

**Created:**
- `tests/e2e/package.json` — Playwright devDep
- `tests/e2e/playwright.config.js` — Playwright config
- `tests/e2e/multirating.spec.js` — the E2E test
- `tests/e2e/.gitignore` — ignore `node_modules`, `test-results`, `playwright-report`
- `tests/e2e/README.md` — how to run the test

**Reference (read-only during implementation):**
- `components/com_joomcck/library/php/helpers/rating.php:87-116` — `loadRating()` signature (7 args, last optional), confirms the arguments passed from the template are still valid.
- `components/com_joomcck/library/php/helpers/rating.php:118-162` — `loadMultiratings()` shows the template is reached when `properties.rate_multirating=true` and `count($options) > 1`.

---

## Task 1: Replace multirating template with BS5 list-group

**Files:**
- Modify: `components/com_joomcck/views/rating_tmpls/multirating/default.php` (full body replacement, lines 13-43)

- [ ] **Step 1: Read the current file to confirm its exact state**

Run: `cat components/com_joomcck/views/rating_tmpls/multirating/default.php`

Expected: the current contents begin with `<table class="table-rating table table-hover table-bordered table-condensed">` and end with `</table>`. If the file has already been edited to use `row`/`col-sm-6`, proceed anyway — the new markup replaces everything below the PHP header.

- [ ] **Step 2: Write the new markup**

Replace the entire file with this content (preserving the existing license header verbatim):

```php
<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

?>
<div class="joomcck-multirating list-group mb-3">
    <div class="list-group-item list-group-item-primary d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
        <div class="fw-bold"><?php echo \Joomla\CMS\Language\Text::_('CTOTALRATING'); ?></div>
        <div class="text-md-end">
            <?php echo RatingHelp::loadRating($type->params->get('properties.tmpl_rating'), round(@$record->votes_result), $record->id, 500, 'Joomcck.ItemRatingCallBack', false, $record->id); ?>
            <small id="rating-text-<?php echo $record->id; ?>" class="d-block d-md-inline ms-md-2 text-muted"><?php echo \Joomla\CMS\Language\Text::sprintf('CRAINGDATA', $record->votes_result, $record->votes); ?></small>
        </div>
    </div>

    <?php foreach ($options as $key => $option): ?>
        <?php
        $parts   = explode('::', $option);
        $canRate = self::canRate('record', $record->user_id, $record->id, $type->params->get('properties.rate_access'), $key, $type->params->get('properties.rate_access_author', 0));
        ?>
        <div class="list-group-item d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
            <div class="fw-semibold"><?php echo \Joomla\CMS\Language\Text::_($parts[0]); ?></div>
            <div class="text-md-end">
                <?php echo RatingHelp::loadRating(
                    isset($parts[1]) ? $parts[1] : $type->params->get('properties.tmpl_rating'),
                    round((int) @$result[$key]['sum']),
                    $record->id,
                    $key,
                    'Joomcck.ItemRatingCallBackMulti',
                    $canRate
                ); ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>
```

- [ ] **Step 3: Syntax-check the PHP file**

Run: `php -l components/com_joomcck/views/rating_tmpls/multirating/default.php`

Expected output: `No syntax errors detected in components/com_joomcck/views/rating_tmpls/multirating/default.php`

- [ ] **Step 4: Commit**

```bash
git add components/com_joomcck/views/rating_tmpls/multirating/default.php
git commit -m "feat(multirating): replace table with BS5 list-group for responsive layout"
```

---

## Task 2: Bootstrap Playwright test infrastructure

**Files:**
- Create: `tests/e2e/package.json`
- Create: `tests/e2e/playwright.config.js`
- Create: `tests/e2e/.gitignore`

- [ ] **Step 1: Create `tests/e2e/package.json`**

```json
{
  "name": "joomcck-e2e",
  "private": true,
  "version": "0.0.0",
  "scripts": {
    "test": "playwright test"
  },
  "devDependencies": {
    "@playwright/test": "^1.48.0"
  }
}
```

- [ ] **Step 2: Create `tests/e2e/playwright.config.js`**

```javascript
import { defineConfig } from '@playwright/test';

export default defineConfig({
  testDir: '.',
  timeout: 30_000,
  retries: 0,
  reporter: [['list']],
  use: {
    baseURL: process.env.JOOMCCK_BASE_URL || 'http://localhost/joomcck',
    headless: true,
    screenshot: 'only-on-failure',
    trace: 'retain-on-failure',
  },
});
```

- [ ] **Step 3: Create `tests/e2e/.gitignore`**

```
node_modules/
test-results/
playwright-report/
screenshots/
```

- [ ] **Step 4: Install Playwright**

Run:
```bash
cd tests/e2e && npm install && npx playwright install chromium
```

Expected: `npm install` completes, then `playwright install chromium` downloads the Chromium browser binary (≈ 170 MB). No errors.

- [ ] **Step 5: Commit**

```bash
git add tests/e2e/package.json tests/e2e/playwright.config.js tests/e2e/.gitignore
git commit -m "test(e2e): bootstrap Playwright for JoomCCK E2E tests"
```

Note: `tests/e2e/package-lock.json` may also be generated — include it in the commit if present so the lockfile is checked in.

---

## Task 3: Discover or create a multirating-enabled record URL

**Files:** none — this is a manual/interactive discovery step that produces a URL used in Task 4.

- [ ] **Step 1: Log in to admin**

Open `http://localhost/joomcck/administrator/` in a browser, log in with `admin` / `system123zz@@`.

- [ ] **Step 2: Find or create a Type with multirating enabled**

Go to `Components → JoomCCK → Types`. For each Type, open it and check the **Rating** tab:
- Enable **Rate access** (any non-"No rating" value)
- Enable **Multirating** (set to Yes)
- In **Multirating options**, enter at least two lines, each formatted `LANG_KEY_OR_LABEL::rating_tmpl_name` — for example:
  ```
  Quality::star
  Value::star
  Service::star
  ```
- Save the Type.

- [ ] **Step 3: Find a record that uses this Type**

Go to `Components → JoomCCK → Records`. Find any published record belonging to the Type you edited. Click the frontend link (or navigate the frontend) to open the record view. Copy the URL — it will look roughly like `http://localhost/joomcck/index.php?option=com_joomcck&view=record&id=<N>&...`.

- [ ] **Step 4: Verify in a private/incognito window**

Open the record URL in a private window (so you're logged out). Confirm you see the multirating block with the total rating row and one row per option. Save the URL verbatim — this is the `RECORD_URL` used in Task 4.

- [ ] **Step 5: Record the URL**

No commit. Write the `RECORD_URL` value into the `env` section at the top of `tests/e2e/multirating.spec.js` in Task 4 below. If creating the record required changes to demo data, no repo commit is needed (demo DB is separate from the repo).

---

## Task 4: Write and run the Playwright E2E test

**Files:**
- Create: `tests/e2e/multirating.spec.js`
- Create: `tests/e2e/README.md`

- [ ] **Step 1: Write the failing test**

Create `tests/e2e/multirating.spec.js`:

```javascript
import { test, expect } from '@playwright/test';
import fs from 'node:fs';
import path from 'node:path';

// Paste the URL discovered in Task 3 here, OR set the RECORD_URL env var when running.
const RECORD_URL = process.env.RECORD_URL || 'REPLACE_WITH_URL_FROM_TASK_3';

const SCREENSHOT_DIR = path.join(import.meta.dirname, 'screenshots');
fs.mkdirSync(SCREENSHOT_DIR, { recursive: true });

test.describe('multirating template', () => {
  test('renders list-group layout on desktop (1280x800)', async ({ page }) => {
    await page.setViewportSize({ width: 1280, height: 800 });
    await page.goto(RECORD_URL);

    const container = page.locator('.joomcck-multirating');
    await expect(container).toBeVisible();

    const items = container.locator('.list-group-item');
    await expect(items).toHaveCount(await items.count());
    expect(await items.count()).toBeGreaterThanOrEqual(2);

    await expect(items.first()).toHaveClass(/list-group-item-primary/);

    const flexDirection = await items.first().evaluate(
      (el) => getComputedStyle(el).flexDirection
    );
    expect(flexDirection).toBe('row');

    await page.screenshot({
      path: path.join(SCREENSHOT_DIR, 'multirating-desktop.png'),
      fullPage: false,
      clip: await container.boundingBox() ?? undefined,
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

    await page.screenshot({
      path: path.join(SCREENSHOT_DIR, 'multirating-mobile.png'),
      fullPage: false,
      clip: await container.boundingBox() ?? undefined,
    });
  });
});
```

**Important:** replace `'REPLACE_WITH_URL_FROM_TASK_3'` with the actual URL copied in Task 3. If you prefer not to commit the URL, leave the placeholder and always run with `RECORD_URL=... npm test`.

- [ ] **Step 2: Run the test to confirm it fails initially if Task 1 was skipped**

(Only applicable if running the plan out of order. If Task 1 is already committed, skip this step — the test should pass on first run.)

Run from `tests/e2e/`:
```bash
npm test
```

Expected if Task 1 is **not** yet applied: the `.joomcck-multirating` selector is not found → test fails with "expected visible, received hidden" or "waiting for locator".

- [ ] **Step 3: Run the test with Task 1 applied**

Run from `tests/e2e/`:
```bash
npm test
```

Expected: 2 passed. Two PNG files appear in `tests/e2e/screenshots/`:
- `multirating-desktop.png` — shows a row of label | stars, highlighted first row
- `multirating-mobile.png` — shows stacked label-above-stars layout

- [ ] **Step 4: Eyeball the screenshots**

Open both PNGs. Desktop: first row is the primary-colored total row; each row shows label on the left and stars/rating text on the right. Mobile: label sits on its own line above the stars; rating text wraps underneath the stars on the total row.

If either screenshot looks wrong (e.g., stars overflow, labels truncated awkwardly), note the specific issue and fix in Task 1's markup before continuing. Otherwise proceed.

- [ ] **Step 5: Write a short README so others can run the test**

Create `tests/e2e/README.md`:

```markdown
# JoomCCK E2E Tests

Playwright tests against a local JoomCCK install.

## Prerequisites

- WAMP/XAMPP running with JoomCCK at `http://localhost/joomcck`
- Node.js 18+
- A record URL with multirating enabled (see project spec for setup)

## Install

```bash
cd tests/e2e
npm install
npx playwright install chromium
```

## Run

```bash
# If RECORD_URL is hardcoded in the spec file:
npm test

# Or override per-run:
RECORD_URL='http://localhost/joomcck/index.php?option=com_joomcck&view=record&id=123' npm test
```

Screenshots are written to `tests/e2e/screenshots/`.
```

- [ ] **Step 6: Commit**

```bash
git add tests/e2e/multirating.spec.js tests/e2e/README.md
git commit -m "test(e2e): add Playwright test for multirating responsive layout"
```

---

## Acceptance

- Task 1's PHP change is committed.
- `npm test` in `tests/e2e/` passes both assertions (desktop row, mobile column) against the discovered `RECORD_URL`.
- Two screenshots are produced in `tests/e2e/screenshots/` and visually show clean desktop and mobile layouts.
- No other files changed; no CSS added; no JS added.

## Notes for the implementer

- If the WAMP server is down when running the test, `page.goto` times out after 30 s — start Apache before running.
- If no record with multirating exists in the demo DB, Task 3 creates one. Don't shortcut by creating fixture data programmatically — the Joomla admin UI is authoritative for this config.
- The `canRate` check may render an inert (non-clickable) widget if the current (logged-out) visitor isn't allowed to rate — that's fine; the test only checks layout, not interaction.
