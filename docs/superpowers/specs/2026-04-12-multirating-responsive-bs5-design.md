# Multirating Template — Responsive BS5 Redesign

## Context

The multirating template at `components/com_joomcck/views/rating_tmpls/multirating/default.php` currently renders as a Bootstrap 3-era `<table class="table-rating table table-hover table-bordered table-condensed">` with `width="1%"` / `nowrap="nowrap"` inline attributes. It does not adapt to narrow viewports: labels, star widgets, and rating text are forced into fixed-width table cells that overflow on mobile.

A prior WIP version (pasted in the conversation, not yet committed) attempted a `row` / `col-sm-6` rewrite, but:
- `col-sm-6` stacks only below 576px — cramped between 576–768px
- `text-bg-dark` / `text-bg-secondary` / `text-bg-light` banding is visually noisy on mobile
- No layout accommodation for the rating text (`small#rating-text-*`) wrapping beside the star widget

## Goal

Replace the table with a modern, mobile-first BS5 `list-group` layout. No custom CSS. The markup must look clean on both 375 px mobile and ≥1280 px desktop viewports, and preserve all existing behavior (rating widgets, click handlers, DOM IDs used by JS callbacks).

## Non-goals

- Changing the PHP data contract (`$type`, `$record`, `$options`, `$result`, `self::canRate(...)`, `RatingHelp::loadRating(...)` signatures stay identical).
- Modifying the other rating templates in `components/com_joomcck/views/rating_tmpls/` (single-rating templates are out of scope).
- Changing language strings (`CTOTALRATING`, `CRAINGDATA`) or their parameters.
- Automated regression tests beyond the one Playwright E2E specified below.

## Design

### Markup

```html
<div class="joomcck-multirating list-group mb-3">
  <!-- Total rating row (highlighted) -->
  <div class="list-group-item list-group-item-primary d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
    <div class="fw-bold">{CTOTALRATING}</div>
    <div class="text-md-end">
      {RatingHelp::loadRating(...) widget}
      <small id="rating-text-{record->id}" class="d-block d-md-inline ms-md-2 text-muted">
        {CRAINGDATA sprintf with votes_result, votes}
      </small>
    </div>
  </div>

  <!-- Per-option rows -->
  <?php foreach ($options as $key => $option): ?>
    <div class="list-group-item d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
      <div class="fw-semibold">{$parts[0] language-translated}</div>
      <div class="text-md-end">
        {RatingHelp::loadRating(...) widget}
      </div>
    </div>
  <?php endforeach; ?>
</div>
```

### Behavior preserved

- `RatingHelp::loadRating(...)` is called with the exact same 7-argument signature for the total row and 6-argument signature for per-option rows as in the current on-disk template.
- `self::canRate(...)` computes the `$canRate` flag per option, same as today.
- `$parts = explode('::', $option)` to allow per-option widget override.
- DOM ID `rating-text-{record->id}` preserved for any JS that targets it.
- Container class `joomcck-multirating` is new and used only as a stable selector for the Playwright test — no CSS attached.

### Responsive behavior

- `d-flex flex-column flex-md-row` — each row is a vertical stack below 768 px; horizontal (label left, stars right) at ≥768 px.
- `justify-content-between align-items-md-center` — on desktop, label is left-aligned and stars right-aligned on the same baseline.
- `text-md-end` on the right column — stars align to the right edge on desktop; left-aligned on mobile.
- `d-block d-md-inline` on the `<small>` rating text — wraps to its own line on mobile, inlines beside the widget on desktop.
- `gap-2` gives consistent spacing in both stacked and inline modes.

### Rationale

BS5 `list-group` with flex utilities replaces the table without any custom CSS or grid breakpoints — it behaves correctly at every viewport width because the breakpoint is a single `md` switch on the flex direction. The `list-group-item-primary` modifier visually elevates the total rating without needing ad-hoc `text-bg-*` banding, which was the WIP version's weakness.

## Files to change

| File | Change |
|------|--------|
| `components/com_joomcck/views/rating_tmpls/multirating/default.php` | Replace `<table>` markup with the `list-group` layout above |
| `tests/e2e/multirating.spec.js` (new) | Playwright test described below |
| `tests/e2e/playwright.config.js` (new, if no config exists) | Minimal config pointing at `http://localhost/joomcck` |
| `tests/e2e/package.json` (new, if no package.json exists at that location) | `@playwright/test` devDependency |

## E2E Test (Playwright)

### Setup assumption

The test assumes a record exists with multirating enabled. The JoomCCK demo instance (`http://localhost/joomcck`) is expected to have at least one type with `properties.rate_type = 'multirating'` configured and at least one published record using it. **Before implementation, the exact URL of such a record must be discovered** (see implementation plan step). If none exists, the test itself creates one via admin login first — but that path is heavy and will be decided during plan-writing.

### Test flow

1. **Login** via `http://localhost/joomcck/administrator/` using credentials `admin` / `system123zz@@`.
2. **Navigate** to the record URL with multirating.
3. **Desktop viewport (1280×800):**
   - Assert `.joomcck-multirating` container is visible.
   - Assert at least 2 `.list-group-item` descendants (1 total row + ≥1 option row).
   - Assert the first `.list-group-item` has class `list-group-item-primary` (total row).
   - Assert computed `flex-direction` of the first item is `row`.
   - Screenshot to `tests/e2e/screenshots/multirating-desktop.png`.
4. **Mobile viewport (375×667):**
   - Re-assert container visible.
   - Assert computed `flex-direction` of the first item is `column`.
   - Screenshot to `tests/e2e/screenshots/multirating-mobile.png`.
5. **Behavior:**
   - Assert the rating widget element(s) inside `.list-group-item` are present (selector to be determined from `RatingHelp::loadRating` output during implementation).

### Out of scope for this test

- Clicking stars to submit a vote (requires authenticated non-author user + backend round-trip; separate test).
- Visual regression comparison (just capture screenshots, don't compare).

## Risks

- **WIP version collision:** the user has a working copy of a BS5 rewrite they've been testing. The new version replaces the whole file, discarding their WIP. (They've seen this spec and approved, so accepted.)
- **Test fragility if no multirating record exists:** test precondition relies on demo data. If the demo instance lacks such a record, the test needs a fixture-creation step (deferred to planning).
- **`RatingHelp::loadRating` output:** the exact rendered HTML structure is unknown from this file alone. The widget selector used in the E2E test may need adjustment once observed live.

## Acceptance

- `components/com_joomcck/views/rating_tmpls/multirating/default.php` renders the `list-group` markup above.
- Playwright E2E test passes against `http://localhost/joomcck` at both viewports.
- Two screenshots exist in `tests/e2e/screenshots/`.
- No changes to other rating templates, no new CSS files, no JS changes.
