# JoomCCK E2E Tests

Playwright tests against a local JoomCCK install.

## Prerequisites

- WAMP/XAMPP running with JoomCCK at `http://localhost/joomcck`
- Node.js 18+
- A record URL with multirating enabled. The default `RECORD_URL` points at
  record id 114 (Spider Man 2002) under the Movies section, which has
  multirating enabled on Type id 20.

## Install

```bash
cd tests/e2e
npm install
npx playwright install chromium
```

## Run

```bash
# Default URL (Spider Man 2002):
npm test

# Override per-run:
RECORD_URL='http://localhost/joomcck/index.php?option=com_joomcck&view=record&id=123&Itemid=133' npm test
```

Screenshots are written to `tests/e2e/screenshots/`.

## Overriding base URL

```bash
JOOMCCK_BASE_URL='http://localhost:8080/joomcck' npm test
```
