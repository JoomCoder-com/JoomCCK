# Security Policy

## Supported Versions

| Version | Supported          |
| ------- | ------------------ |
| >= 6.4.1 | :white_check_mark: |
| <= 6.4.0 | :x:                |

## Reporting a Vulnerability

If you discover a security vulnerability in JoomCCK, please report it responsibly.

**Contact**: support@joomcoder.com

- Include a detailed description of the vulnerability and steps to reproduce it.
- We will acknowledge receipt within **48 hours**.
- We aim to provide an initial assessment within **5 business days**.
- Please do not publicly disclose the vulnerability until we have released a fix and notified affected users.

We appreciate responsible disclosure and will credit reporters (with permission) in our security advisories.

## Security Advisories

### June 2026 — Unauthenticated SQL Injection (Critical)

- **Severity**: Critical (CVSS 3.1 — 9.8)
- **Affected versions**: <= 6.4.0
- **Fixed in**: 6.4.1
- **Description**: The front-end `tags.save` task built a SQL query by concatenating an unescaped request parameter and was reachable without authentication or a CSRF token, allowing an unauthenticated attacker to read arbitrary data from the database (including user credentials). Related state-changing front-end tasks were also missing authorization checks.
- **Recommendation**: Update to version 6.4.1 or later immediately. Review your site for signs of unauthorized database access.
- **Download**: https://github.com/JoomCoder-com/JoomCCK/releases/tag/6.4.1
- **Credit**: Reported by Kamil Soltanov via coordinated disclosure (Joomla Security Strike Team); CVE pending.

### February 2026 — Authentication Bypass (Critical)

- **Severity**: Critical
- **Affected versions**: <= 6.2.0
- **Fixed in**: 6.2.1
- **Description**: A security vulnerability allowed unauthenticated access to certain component controllers, potentially exposing administrative functionality to unauthorized users.
- **Recommendation**: Update to version 6.2.1 or later immediately. Review your site for any signs of unauthorized access or suspicious files.
- **Download**: https://github.com/JoomCoder-com/JoomCCK/releases
