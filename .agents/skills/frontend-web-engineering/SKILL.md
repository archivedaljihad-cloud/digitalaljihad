---
name: frontend-web-engineering
description: >-
  Use this skill whenever building, modifying, or optimizing frontend user interfaces,
  web animations, responsive layouts (from mobile 320px to Smart TV 4K 16:9),
  Core Web Vitals (LCP, CLS, INP), accessibility (a11y), and premium visual design.
---

# Frontend & Web Engineering Playbook

This skill provides comprehensive standards and operational procedures for building world-class, premium, and zero-regression web interfaces.

---

## 1. Visual & Aesthetic Principles

1. **Rich & High-End Aesthetics:**
   - Avoid generic, plain colors (e.g., pure red `#ff0000`, plain blue `#0000ff`).
   - Use curated color palettes with deep contrast, subtle gradients, and dark-mode elegance (e.g., emerald green `#064e3b`, deep navy `#0f172a`, rich gold `#d97706`).
   - Implement subtle glassmorphism (`backdrop-filter: blur(12px); background: rgba(15, 23, 42, 0.75); border: 1px solid rgba(255, 255, 255, 0.1);`).
   - Use premium modern typography (Inter, Outfit, Plus Jakarta Sans, Amiri for Arabic scripts).

2. **Micro-Animations & Motion Design:**
   - Smooth CSS transitions (`transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);`).
   - Hardware-accelerated transforms (`transform: translate3d(0, 0, 0)`, `will-change: transform, opacity`).
   - Continuous subtle motion for digital displays (marquee ticker, soft breathing glow on prayer countdowns).

---

## 2. Fluid Cross-Device & Multi-Display Architecture

Interfaces must render flawlessly across the entire spectrum of display devices:

Device Form Factor | Viewport Range | Key UX & Architectural Requirements
:--- | :--- | :---
**Smartphone / Mobile** | `320px` - `767px` | Single-column flex/grid stack, min 44x44px touch targets, `overflow-x: hidden`, virtual keyboard friendly, Quick PIN inputs.
**Tablet / iPad** | `768px` - `1024px` | Adaptive 2-column layouts, touch-friendly navigation drawer, modal dialogs centered with padding.
**Desktop / Laptop** | `1025px` - `1920px`| Full multi-column dashboard, mouse hover states, keyboard shortcuts (`Ctrl+S`, `Esc`).
**Smart TV / Digital Signage** | `1920x1080` (1080p) & 4K (16:9) | High-contrast viewing from >3 meters, large typography (min 24px body, 48px+ headers), zero cursor dependency, 24/7 continuous stability.

### Media Query Standards:
```css
/* Mobile First Base */
:root {
  --font-base: clamp(14px, 1.2vw, 18px);
  --tv-scale: 1;
}

/* Tablet */
@media (min-width: 768px) and (max-width: 1024px) {
  .dashboard-grid { grid-template-columns: repeat(2, 1fr); }
}

/* Smart TV & Large 1080p/4K Screens */
@media (min-width: 1920px) {
  :root { --tv-scale: 1.25; }
  .prayer-card { font-size: calc(1.5rem * var(--tv-scale)); }
}
```

---

## 3. Performance & Core Web Vitals (CWV)

1. **Largest Contentful Paint (LCP < 2.5s):**
   - Preload critical hero assets and fonts (`<link rel="preload" as="font" crossorigin>`).
   - Inlined critical above-the-fold CSS for TV signage headers and prayer countdown cards.
   - Use `fetchpriority="high"` for prominent display slides and logos.

2. **Cumulative Layout Shift (CLS < 0.1):**
   - Explicitly define `width` and `height` (or `aspect-ratio`) on all `<img>`, `<video>`, and `<iframe>` elements.
   - Reserve space for dynamic text widgets (running text, live prayer clocks) using `min-height` and fixed grid dimensions.

3. **Interaction to Next Paint (INP < 200ms):**
   - Debounce heavy form inputs and live search fields (150-300ms).
   - Offload long scripts using `requestAnimationFrame` or `setTimeout(..., 0)` to keep the UI thread responsive.

---

## 4. Zero-Regression & Modular Isolation

When editing any page in a multi-page web application:
- **Scope CSS selectors:** Avoid styling raw HTML tags directly in global sheets (e.g., use `.admin-table th` instead of raw `th`).
- **Namespace Global JS:** Prevent variable collision by encapsulating module scripts in IIFE or scoped objects (`window.SlideEngine = { ... }`).
- **Preserve Fallbacks:** Always use defensive null-coalescing for dynamic text (`item?.title ?? 'Default Title'`).
