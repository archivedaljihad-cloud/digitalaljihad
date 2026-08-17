<!-- resources/views/partials/display-theme.blade.php -->
<style>
:root {
    --primary-color: #0d6e6e;
    --secondary-color: #ffd700;
    --accent-color: #0a4d68;
    --text-light: #ffffff;
    --text-dark: #333333;
    --bg-gradient: linear-gradient(135deg, #0a4d68, #088395);
    --income-bg: rgba(0,255,127,.15);
    --expense-bg: rgba(255,99,71,.15);
    --display-bg: #062b2b;
}

body {
    background-color: var(--display-bg) !important;
}

/* =====================================================
   DISPLAY BACKGROUND (Gambar Masjid Nabawi)
   ===================================================== */
.display-background {
    position: fixed !important;
    inset: 0;
    z-index: -10 !important;
    background-image: url('{{ asset("image/display/background/Nabawi001.jpg") }}') !important;
    background-position: center !important;
    background-repeat: no-repeat !important;
    background-size: cover !important;
    opacity: 0.3 !important; 
}

.display-overlay {
    position: fixed !important;
    inset: 0;
    pointer-events: none;
    z-index: -9 !important;
    background: transparent !important;
}

.display-content {
    position: relative !important;
    z-index: 1 !important;
}

/* =====================================================
   PENGATURAN RUNNING TEXT GLOBAL UNTUK SEMUA HALAMAN
   ===================================================== */
.bottom-section {
    flex-shrink: 0;
    width: 100%;
    margin-top: auto;
}

.running-text-container, 
.bottom-section,
.footer {
    background: transparent !important;
    background-color: transparent !important;
    background-image: none !important;
    border: none !important;
    box-shadow: none !important;
    backdrop-filter: none !important;
    -webkit-backdrop-filter: none !important;
}

.running-text-container {
    padding: 6px 15px;
    margin-bottom: 6px;
    overflow: hidden;
}

.running-text {
    white-space: nowrap;
    animation: marquee 50s linear infinite !important; /* Kecepatan pas dan santai */
    font-size: 1.1rem !important; /* Ukuran besar 5rem */
    letter-spacing: 0.5px;
    font-weight: 500;
    color: #ffffff;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.9);
}

.running-text i {
    margin-right: 8px;
    color: #ffd700;
}

@keyframes marquee {
    0% { transform: translateX(100%); }
    100% { transform: translateX(-100%); }
}

.footer {
    text-align: center;
    padding: 6px;
    font-size: 0.8rem;
    color: rgba(255, 255, 255, 0.95);
    text-shadow: 1px 1px 3px rgba(0,0,0,0.9);
}
</style>