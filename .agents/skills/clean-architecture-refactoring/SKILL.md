---
name: clean-architecture-refactoring
description: >-
  Use this skill whenever refactoring legacy code, restructuring modules, decoupling components,
  preventing memory leaks in long-running applications, or establishing clean code standards.
---

# Clean Architecture & Refactoring Playbook

This skill provides architectural guidelines and refactoring patterns to ensure codebases remain maintainable, extensible, and completely bug-free.

---

## 1. Modular Architecture & Separation of Concerns

Avoid giant monolithic files by decoupling systems into distinct layers:

```
┌────────────────────────────────────────────────────────┐
│                   PRESENTATION LAYER                   │
│   (index.html, admin.html, petugas.html, slide views)   │
└───────────────────────────┬────────────────────────────┘
                            │
┌───────────────────────────▼────────────────────────────┐
│                    APPLICATION LOGIC                   │
│       (SlideEngine, PrayerScheduler, AdminAuth)        │
└───────────────────────────┬────────────────────────────┘
                            │
┌───────────────────────────▼────────────────────────────┐
│                   DATA & SYNC LAYER                    │
│   (SupabaseClient, LocalStorageCache, OfflineStore)    │
└────────────────────────────────────────────────────────┘
```

1. **Presentation Layer:** Purely renders DOM elements and captures user interaction.
2. **Application Logic:** Manages state transitions, timers, and business calculations.
3. **Data Layer:** Handles network calls, local storage persistence, and failover fallbacks.

---

## 2. Defensive Coding & Safe Fallbacks (SOP Mandate)

Long-running Smart TV signage and admin forms must NEVER crash or render empty white screens due to missing keys or network lag.

### Anti-Pattern ❌
```javascript
// High risk of uncaught TypeError: Cannot read property of undefined
const duration = data.settings.slide_duration;
const enabled = response.data.modules.prayer;
document.getElementById('ticker').innerText = data.text;
```

### Clean Defensive Pattern ✅
```javascript
// Guaranteed safe fallback with nullish coalescing
const duration = data?.settings?.slide_duration ?? 10;
const enabled = response?.data?.modules?.prayer ?? true;
const tickerText = data?.text?.trim() ?? 'Selamat Datang di Masjid Al-Jihad';
```

---

## 3. Preventing Memory Leaks in 24/7 Display Applications

Digital displays run continuously for months without page reloads. Watch out for memory leak traps:

1. **Timer Accumulation:**
   - Always clear old intervals before re-assigning:
     ```javascript
     if (window._prayerInterval) clearInterval(window._prayerInterval);
     window._prayerInterval = setInterval(updatePrayerClock, 1000);
     ```
2. **Event Listener Leaks:**
   - Remove listeners when components unmount or slides switch, or use the `{ once: true }` option for single triggers.
3. **Detached DOM Nodes:**
   - Clean up cached DOM references when regenerating table rows or slide items dynamically.
4. **Broadcast Channels & WebSocket Cleanup:**
   - Gracefully unsubscribe and close Supabase channels when navigating away.

---

## 4. Refactoring Step-by-Step SOP

When refactoring existing code:
1. **Preserve External API & Behavior:** Keep public method signatures unchanged.
2. **Add Unit/Integration Tests First:** Ensure assertions pass before and after the change.
3. **Small Iterative Commits:** Do not mix massive UI redesigns with logic refactoring in a single step.
