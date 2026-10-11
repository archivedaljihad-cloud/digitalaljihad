---
name: devops-cloud-infra
description: >-
  Use this skill whenever deploying code, managing edge caching, configuring Cloudflare Workers,
  synchronizing Supabase cloud databases, managing Git/GitHub repositories, updating Service Worker caches,
  or automating backup pipelines.
---

# DevOps, Cloud & Infrastructure Playbook

This skill outlines operational procedures for zero-downtime deployment, continuous edge caching synchronization, real-time database propagation, and resilient disaster recovery.

---

## 1. The 4-Pillar Synchronization Mandate

Every update, bug fix, or feature deployment MUST synchronously update all 4 pillars of the architecture:

```
                  ┌──────────────────────────────────────────────┐
                  │          DEPLOYMENT PIPELINE TRIGGER         │
                  └──────────────────────┬───────────────────────┘
                                         │
        ┌───────────────────┬────────────┴───────┬───────────────────┐
        ▼                   ▼                    ▼                   ▼
  [1. CLOUDFLARE]     [2. SUPABASE]         [3. GITHUB]         [4. LOCAL BACKUP]
  Workers & Pages     Cloud Realtime        Git Repository      Cold Mirror
  Edge Cache Sync     Postgres Changes      Branch 'main'       Offline Archive
```

### Pillar Checklist:
1. **Pillar 1: Cloudflare Workers (`https://digitalaljihad.my.id/`)**
   - Execute: `npx wrangler deploy`
   - Validates that edge static assets and routing logic are live immediately across Cloudflare PoPs.
2. **Pillar 2: Supabase Cloud Database**
   - Ensure payload, schema, and `app_settings` keys are synchronized.
   - Verify active Postgres Changes and Realtime Broadcast listeners are functioning without disconnects.
3. **Pillar 3: GitHub Central Repository**
   - Execute:
     ```powershell
     git add .
     git commit -m "feat/fix: <descriptive message>"
     git push origin main
     ```
4. **Pillar 4: Local Cold Backup Mirror**
   - Target directory: `C:\Users\anthu\Documents\【Digital WebSTATIS】\`
   - Copy all updated web assets to ensure an offline physical backup is always up to date:
     ```powershell
     Copy-Item -Recurse -Force web-statis\* "C:\Users\anthu\Documents\【Digital WebSTATIS】\"
     ```

---

## 2. Cache Invalidation & Service Worker Bumping SOP

When deploying new assets, obsolete browser cache will cause client devices (especially 24/7 Smart TVs) to run stale code. Always enforce:

### Step A: Service Worker Version Bumping (`sw.js`)
- Increment `CACHE_NAME` in `web-statis/sw.js` (e.g., from `v2.x.x` to `v2.x.y`).
- Ensure the `activate` event purges old caches:
  ```javascript
  self.addEventListener('activate', event => {
    event.waitUntil(
      caches.keys().then(keys => {
        return Promise.all(
          keys.filter(k => k !== CACHE_NAME).map(k => caches.delete(k))
        );
      })
    );
  });
  ```

### Step B: Cloudflare Headers Verification (`_headers`)
Verify that critical routes always bypass stale edge caching:
```text
/*.html
  Cache-Control: public, max-age=0, must-revalidate
/sw.js
  Cache-Control: public, max-age=0, must-revalidate
/slides/*
  Cache-Control: public, max-age=0, must-revalidate
```

---

## 3. Deployment Health Check & Rollback Strategy

1. **Pre-flight Check:** Run full automated regression tests (`npm test`). Do NOT deploy if any assertion fails.
2. **Live Edge Verification:** Run `curl -I https://digitalaljihad.my.id/` to verify HTTP 200 OK and proper headers.
3. **Disaster Recovery (Rollback):**
   - If a regression occurs in production, revert Git commit (`git revert HEAD`) or restore from the local cold backup folder and redeploy immediately.
