---
name: testing-quality-assurance
description: >-
  Use this skill whenever running automated regression tests, validating syntax integrity,
  verifying prayer schedule transitions, testing multi-device logins, or establishing QA release gates.
---

# Testing & Quality Assurance Playbook

This skill establishes automated testing standards, regression verification gates, and continuous quality assurance procedures.

---

## 1. The Zero-Regression Deployment Gate

Under the Project SOP, **NO CODE MAY BE COMMITTED OR DEPLOYED** if any automated regression test fails.

```
       [CODE CHANGES COMPLETE]
                  │
                  ▼
         Run: `npm test`
                  │
        ┌─────────┴─────────┐
        ▼                   ▼
    [FAIL ❌]           [PASS ✅]
  Stop Pipeline       Proceed with 4 Pillars:
  Fix Issues Now      1. Cloudflare Deploy
  Re-test             2. Supabase Sync
                      3. Git Push
                      4. Cold Local Backup
```

---

## 2. Automated Test Suite Architecture (`npm test`)

The test suite validates multiple critical sub-systems across the entire project:

Assertion Domain | What It Validates | Failure Action
:--- | :--- | :---
**Syntax Integrity** | Checks all `.html`, `.js`, `.json`, and CSS files for syntax errors or broken tags. | Halt immediately; fix unclosed tag/syntax error.
**24-Hour Prayer Cycle** | Simulates all prayer times (Subuh, Terbit, Dzuhur, Ashar, Maghrib, Isya) and transitions (Iqomah, Sholat, Khutbah Jumat). | Prevent deployment of corrupted prayer calculations.
**Watchdog & Screen Protection** | Verifies watchdog timers keep slides running and prevent permanent black screens on TV displays. | Ensure watchdog recovery logic is intact.
**Cache Consistency** | Validates Service Worker `CACHE_NAME` bumping and `_headers` cache rules. | Fix stale cache configs.
**RBAC Multi-Device Auth** | Verifies authentication flows for Super Admin, Bendahara, and Petugas across mobile and desktop. | Fix login credential logic.

---

## 3. QA Checklist Before Any Release

Every agent or developer must verify:
- [ ] Ran `npm test` and achieved **100% PASS** on all assertions.
- [ ] Verified responsive layout across mobile (`375px`), tablet (`768px`), and TV (`1920x1080`).
- [ ] Confirmed no JavaScript console errors on key pages:
  - `web-statis/index.html` (Smart TV Display)
  - `web-statis/admin.html` (Admin Panel)
  - `web-statis/petugas.html` (Petugas Input)
  - `web-statis/login.html` (Auth Portal)
  - `web-statis/prayer-mode.html` (Prayer Display)
- [ ] Confirmed fallback values exist for all remote Supabase data fields.
- [ ] Updated `LATEST_UPDATE.md` with release notes and affected files.
