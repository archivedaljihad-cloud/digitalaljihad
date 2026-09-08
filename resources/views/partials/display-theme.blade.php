<!-- resources/views/partials/display-theme.blade.php -->
<style>
/* =====================================================
   CUSTOM FONTS (Masking Renta, Aloevera, Sabiyah)
   ===================================================== */
@font-face {
    font-family: 'Masking Renta';
    src: url('{{ asset("fonts/MaskingRenta.otf") }}') format('opentype');
    font-weight: normal;
    font-style: normal;
    font-display: swap;
}

@font-face {
    font-family: 'Aloevera';
    src: url('{{ asset("fonts/Aloevera.ttf") }}') format('truetype');
    font-weight: normal;
    font-style: normal;
    font-display: swap;
}

@font-face {
    font-family: 'Sabiyah';
    src: url('{{ asset("fonts/Sabiyah.ttf") }}') format('truetype');
    font-weight: normal;
    font-style: normal;
    font-display: swap;
}

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

/* =====================================================
   MASTER UNIFIED HEADER (H1, SUB-HEADER, DATETIME)
   Menyeragamkan posisi & ukuran font di semua halaman
   ===================================================== */
.header,
.header-section {
    text-align: center !important;
    width: 100% !important;
    margin-top: 0 !important;
    margin-bottom: 12px !important;
    padding: 0 !important;
    flex-shrink: 0 !important;
    position: relative !important;
}

.header h1,
.header-section h1 {
    font-family: 'Masking Renta', sans-serif !important;
    font-size: 3.2rem !important;
    line-height: 1.1 !important;
    letter-spacing: 5px !important;
    text-transform: uppercase !important;
    background: none !important;
    -webkit-background-clip: initial !important;
    -webkit-text-fill-color: initial !important;
    color: #0b4f26 !important;
    text-shadow:
        2px 2px 0 #ffffff,
        -2px 2px 0 #ffffff,
        2px -2px 0 #ffffff,
        -2px -2px 0 #ffffff,
        0 0 12px #ffd700,
        0 0 25px #ffd700,
        0 0 40px rgba(255, 170, 0, 0.06) !important;
    filter: none !important;
    margin: 0 0 -15px 0 !important;
    padding: 0 !important;
}

.header h2.sub-header,
.header h3.sub-header,
.header .sub-header,
.header-section h2.sub-header,
.header-section h3.sub-header,
.header-section .sub-header,
h3.sub-header,
h2.sub-header {
    font-family: 'Poppins', sans-serif !important;
    font-size: 1.25rem !important;
    font-weight: 500 !important;
    line-height: 1.2 !important;
    letter-spacing: 4px !important;
    color: #ffffff !important;
    text-transform: uppercase !important;
    opacity: 0.95 !important;
    text-shadow: 0 2px 5px rgba(0, 0, 0, 0.8) !important;
    margin-top: 10px !important;
    margin-bottom: 6px !important;
    padding: 0 !important;
}

.header .datetime,
.header-section .datetime {
    font-size: 1.55rem !important;
    background: rgba(3, 20, 15, 0.65) !important;
    display: inline-block !important;
    padding: 5px 28px !important;
    border-radius: 35px !important;
    margin-top: 4px !important;
    font-weight: 600 !important;
    letter-spacing: 1px !important;
    border: 1.5px solid rgba(255, 215, 0, 0.5) !important;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.4) !important;
    backdrop-filter: blur(8px) !important;
    -webkit-backdrop-filter: blur(8px) !important;
}
</style>