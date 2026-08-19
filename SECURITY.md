# Security Policy

## Supported Versions

| Version | Supported          |
| ------- | ------------------ |
| >= 6.4.2 | :white_check_mark: |
| <= 6.4.1 | :x:                |

## Reporting a Vulnerability

If you discover a security vulnerability in JoomCCK, please report it responsibly.

**Contact**: support@joomcoder.com

- Include a detailed description of the vulnerability and steps to reproduce it.
- We will acknowledge receipt within **48 hours**.
- We aim to provide an initial assessment within **5 business days**.
- Please do not publicly disclose the vulnerability until we have released a fix and notified affected users.

We appreciate responsible disclosure and will credit reporters (with permission) in our security advisories.

## Security Advisories

### August 2026 — Unauthenticated Remote Code Execution (Critical)

- **Severity**: Critical (CVSS 3.1 — 9.8)
- **Affected versions**: <= 6.4.1
- **Fixed in**: 6.4.2
- **Description**: The front-end `template.save` task wrote attacker-controlled content to a template file whose path and extension came directly from the request, with no CSRF token and no authorization check, letting an unauthenticated attacker plant an executable `.php` file in a web-served directory and achieve remote code execution (reported as GHSA-whf2-8p4w-v6xv). A follow-up audit for the same class of bug found and fixed additional unauthenticated file-write / file-delete paths reachable through the generic field dispatchers (`field.call` / `ajax.field_call`) — including an image-upload path with no extension allow-list, and image/signature file-delete paths accepting traversal — plus missing CSRF or authorization checks on `templates.save`, `records.delete` and `pack.build`. Template output directories now also ship `.htaccess` / `web.config` rules that block direct execution of files placed there.
- **Recommendation**: Update to version 6.4.2 or later immediately. Check for unexpected `.php` files under `components/com_joomcck/views/*/tmpl/` and `components/com_joomcck/configs/`, and review your site for other signs of compromise (web shells, modified files, unfamiliar administrator accounts).
- **Download**: https://github.com/JoomCoder-com/JoomCCK/releases/tag/6.4.2
- **Credit**: `template.save` remote code execution reported by EQSTLab via coordinated disclosure (GitHub Security Advisory GHSA-whf2-8p4w-v6xv). Additional file-write / file-delete and CSRF hardening identified during the follow-up internal audit.

### June 2026 — Unauthenticated SQL Injection (Critical)

- **Severity**: Critical (CVSS 3.1 — 9.8)
- **Affected versions**: <= 6.4.0
- **Fixed in**: 6.4.1
- **Description**: The front-end `tags.save` task built a SQL query by concatenating an unescaped request parameter and was reachable without authentication or a CSRF token, allowing an unauthenticated attacker to read arbitrary data from the database (including user credentials). Related state-changing front-end tasks were also missing authorization checks.
- **Recommendation**: Update to version 6.4.1 or later immediately. Review your site for signs of unauthorized database access.
- **Download**: https://github.com/JoomCoder-com/JoomCCK/releases/tag/6.4.1
- **Credit**: Reported by Kamil Soltanov via coordinated disclosure (Joomla Security Strike Team); CVE-2026-49048.

### February 2026 — Authentication Bypass (Critical)

- **Severity**: Critical
- **Affected versions**: <= 6.2.0
- **Fixed in**: 6.2.1
- **Description**: A security vulnerability allowed unauthenticated access to certain component controllers, potentially exposing administrative functionality to unauthorized users.
- **Recommendation**: Update to version 6.2.1 or later immediately. Review your site for any signs of unauthorized access or suspicious files.
- **Download**: https://github.com/JoomCoder-com/JoomCCK/releases
