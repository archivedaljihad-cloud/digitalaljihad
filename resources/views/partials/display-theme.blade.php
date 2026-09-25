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
    transition: background-color 2.5s ease !important;
}

/* =====================================================
   DYNAMIC AMBIENT THEMES BY PRAYER TIME (Siklus Suasana Waktu Sholat)
   ===================================================== */
body.theme-subuh {
    --display-bg: #051321 !important;
    --ambient-orb-1: rgba(56, 189, 248, 0.22);
    --ambient-orb-2: rgba(251, 191, 36, 0.16);
    --ambient-accent: #38bdf8;
}

body.theme-dhuha {
    --display-bg: #032018 !important;
    --ambient-orb-1: rgba(250, 204, 21, 0.22);
    --ambient-orb-2: rgba(16, 185, 129, 0.20);
    --ambient-accent: #facc15;
}

body.theme-dzuhur {
    --display-bg: #062b2b !important;
    --ambient-orb-1: rgba(5, 150, 105, 0.25);
    --ambient-orb-2: rgba(255, 215, 0, 0.22);
    --ambient-accent: #ffd700;
}

body.theme-ashar {
    --display-bg: #211608 !important;
    --ambient-orb-1: rgba(249, 115, 22, 0.22);
    --ambient-orb-2: rgba(217, 119, 6, 0.22);
    --ambient-accent: #f97316;
}

body.theme-maghrib {
    --display-bg: #1f0b18 !important;
    --ambient-orb-1: rgba(225, 29, 72, 0.22);
    --ambient-orb-2: rgba(139, 92, 246, 0.20);
    --ambient-accent: #e11d48;
}

body.theme-isya {
    --display-bg: #050e24 !important;
    --ambient-orb-1: rgba(59, 130, 246, 0.22);
    --ambient-orb-2: rgba(212, 175, 55, 0.20);
    --ambient-accent: #3b82f6;
}

/* Lapisan Pencahayaan Halus Ambient Orb */
.ambient-glow-layer {
    position: fixed !important;
    inset: 0;
    pointer-events: none;
    z-index: -8 !important;
    overflow: hidden;
}

.ambient-orb-top {
    position: absolute;
    width: 45vw;
    height: 45vw;
    top: -15vw;
    left: -10vw;
    border-radius: 50%;
    filter: blur(85px);
    background: radial-gradient(circle, var(--ambient-orb-1, rgba(255,215,0,0.18)) 0%, rgba(0,0,0,0) 70%);
    animation: orbFloatingA 16s ease-in-out infinite alternate;
}

.ambient-orb-bottom {
    position: absolute;
    width: 45vw;
    height: 45vw;
    bottom: -15vw;
    right: -10vw;
    border-radius: 50%;
    filter: blur(85px);
    background: radial-gradient(circle, var(--ambient-orb-2, rgba(13,110,110,0.22)) 0%, rgba(0,0,0,0) 70%);
    animation: orbFloatingB 18s ease-in-out infinite alternate;
}

@keyframes orbFloatingA {
    0% { transform: translate(0, 0) scale(1); opacity: 0.6; }
    50% { transform: translate(3vw, 4vh) scale(1.1); opacity: 0.8; }
    100% { transform: translate(-2vw, -2vh) scale(0.95); opacity: 0.6; }
}

@keyframes orbFloatingB {
    0% { transform: translate(0, 0) scale(1); opacity: 0.6; }
    50% { transform: translate(-4vw, -3vh) scale(1.1); opacity: 0.8; }
    100% { transform: translate(2vw, 3vh) scale(0.95); opacity: 0.6; }
}

/* =====================================================
   DISPLAY BACKGROUND (Cerah, Tajam, Jelas Seperti Gambar Aslinya)
   ===================================================== */
.display-background {
    position: fixed !important;
    inset: 0;
    z-index: -10 !important;
    background-image: url('{{ asset("image/display/background/BG1.png") }}');
    background-position: center !important;
    background-repeat: no-repeat !important;
    background-size: cover !important;
    opacity: 1 !important; 
    filter: brightness(0.96) contrast(1.06) saturate(1.08) !important;
}

/* =====================================================
   DISPLAY OVERLAY (Lapisan Penyejuk Adem, Anti-Silau & Pelindung Teks)
   Menjaga seluruh teks tetap super kontras dan mudah dibaca
   tanpa menutupi keindahan, ketajaman & kecerahan foto masjid
   ===================================================== */
.display-overlay {
    position: fixed !important;
    inset: 0;
    pointer-events: none;
    z-index: -9 !important;
    background: 
        /* Vignette lembut sudut luar layar untuk keteduhan visual & bebas silau */
        radial-gradient(ellipse at 50% 50%, rgba(2, 14, 10, 0.04) 0%, rgba(1, 12, 8, 0.32) 100%),
        /* Gradasi vertikal penopang kontras Header atas dan Running Text bawah */
        linear-gradient(180deg, 
            rgba(2, 16, 11, 0.78) 0%, 
            rgba(2, 16, 11, 0.50) 15%, 
            rgba(2, 16, 11, 0.20) 28%, 
            rgba(2, 16, 11, 0.12) 50%, 
            rgba(2, 16, 11, 0.18) 75%, 
            rgba(2, 16, 11, 0.52) 88%, 
            rgba(2, 16, 11, 0.75) 100%
        ) !important;
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
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
    justify-content: center !important;
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
    -webkit-text-fill-color: #085a2b !important;
    color: #085a2b !important;
    text-shadow:
        1px 1px 0 #ffd700,
        -1px 1px 0 #ffd700,
        1px -1px 0 #ffd700,
        -1px -1px 0 #ffd700,
        2px 2px 0 #000000,
        -2px 2px 0 #000000,
        2px -2px 0 #000000,
        -2px -2px 0 #000000,
        3px 3px 0 #000000,
        -3px 3px 0 #000000,
        3px -3px 0 #000000,
        -3px -3px 0 #000000,
        0 5px 12px rgba(0, 0, 0, 0.95),
        0 0 20px rgba(255, 215, 0, 0.65) !important;
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
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    padding: 5px 28px !important;
    border-radius: 35px !important;
    margin: 4px auto 0 auto !important;
    font-weight: 600 !important;
    letter-spacing: 0.8px !important;
    border: 1.5px solid rgba(255, 215, 0, 0.5) !important;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.4) !important;
    backdrop-filter: blur(8px) !important;
    -webkit-backdrop-filter: blur(8px) !important;
    color: #ffffff !important;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.8) !important;
    white-space: nowrap !important;
    box-sizing: border-box !important;
}

.datetime .masehi-date {
    color: #ffffff !important;
    font-weight: 600 !important;
}

.datetime .hijri-date {
    color: #ffd700 !important;
    font-weight: 700 !important;
    letter-spacing: 1px !important;
    text-shadow: 0 0 10px rgba(255, 215, 0, 0.65), 0 0 20px rgba(255, 170, 0, 0.35) !important;
    padding: 0 2px !important;
}

.datetime .jam-time {
    color: #ffffff !important;
    font-weight: 700 !important;
    font-variant-numeric: tabular-nums !important;
}

.datetime .dt-sep {
    color: #ffd700 !important;
    opacity: 0.85 !important;
    margin: 0 10px !important;
    font-weight: bold !important;
    text-shadow: 0 0 8px rgba(255, 215, 0, 0.5) !important;
}

/* Penataan khusus datetime di dalam Header Card (Idul Fitri, Idul Adha) */
.idul-header .datetime,
.header-clock .datetime,
.idul-card .datetime {
    display: inline-flex !important;
    flex-direction: column !important;
    align-items: flex-end !important; /* Rata kanan agar jam sejajar pas di bawah kalender Hijriah */
    justify-content: center !important;
    text-align: right !important;
    padding: 6px 18px !important;
    border-radius: 14px !important;
    background: rgba(3, 20, 15, 0.7) !important;
    border: 1.5px solid rgba(255, 215, 0, 0.5) !important;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.4) !important;
    line-height: 1.35 !important;
    white-space: nowrap !important;
    flex-shrink: 0 !important;
}

.idul-header .datetime .dt-date-row,
.header-clock .datetime .dt-date-row,
.idul-card .datetime .dt-date-row {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: flex-end !important;
    font-size: 1.05rem !important;
    font-weight: 600 !important;
}

.idul-header .datetime .dt-sep-time,
.header-clock .datetime .dt-sep-time,
.idul-card .datetime .dt-sep-time {
    display: none !important;
}

.idul-header .datetime .dt-time-row,
.header-clock .datetime .dt-time-row,
.idul-card .datetime .dt-time-row {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: flex-end !important;
    gap: 6px !important;
    font-size: 1.02rem !important;
    color: #ffffff !important;
    margin-top: 2px !important;
    font-weight: 700 !important;
    text-align: right !important;
}

.idul-header .datetime .dt-time-row::before,
.header-clock .datetime .dt-time-row::before {
    content: "\f017";
    font-family: "Font Awesome 5 Free";
    font-weight: 900;
    color: #ffd700;
    font-size: 0.95rem;
}

/* =====================================================
   MEDALI KALIGRAFI 3D EMAS MENYALA (ALLAH & MUHAMMAD)
   ===================================================== */
.kaligrafi-medallion {
    position: absolute !important;
    top: 14px !important;
    width: 125px !important;
    height: 125px !important;
    z-index: 10 !important;
    user-select: none !important;
    pointer-events: none !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
}

.kaligrafi-medallion::before {
    content: '' !important;
    position: absolute !important;
    top: 50% !important;
    left: 50% !important;
    transform: translate(-50%, -50%) !important;
    width: 110% !important;
    height: 110% !important;
    border-radius: 50% !important;
    background: radial-gradient(circle, rgba(255, 215, 0, 0.45) 0%, rgba(255, 170, 0, 0.22) 48%, rgba(255, 140, 0, 0) 72%) !important;
    filter: blur(8px) !important;
    z-index: -1 !important;
    pointer-events: none !important;
    animation: medallionAuraPulse 4s ease-in-out infinite alternate !important;
}

.kaligrafi-medallion img {
    width: 100% !important;
    height: 100% !important;
    object-fit: contain !important;
    filter: drop-shadow(0 0 12px rgba(255, 220, 50, 0.95)) 
           drop-shadow(0 0 28px rgba(255, 175, 0, 0.8))
           drop-shadow(0 0 50px rgba(255, 140, 0, 0.5))
           drop-shadow(0 10px 20px rgba(0, 0, 0, 0.85)) !important;
    animation: goldenMedallionGlow 3.5s ease-in-out infinite alternate !important;
}

.kaligrafi-medallion.kaligrafi-allah {
    right: 28px !important;
    left: auto !important;
}

.kaligrafi-medallion.kaligrafi-muhammad {
    left: 28px !important;
    right: auto !important;
}

@keyframes goldenMedallionGlow {
    0% {
        filter: drop-shadow(0 0 10px rgba(255, 215, 0, 0.85)) 
               drop-shadow(0 0 22px rgba(255, 160, 0, 0.6))
               drop-shadow(0 0 40px rgba(255, 130, 0, 0.4))
               drop-shadow(0 8px 16px rgba(0, 0, 0, 0.8));
        transform: scale(1);
    }
    50% {
        filter: drop-shadow(0 0 18px rgba(255, 240, 90, 1)) 
               drop-shadow(0 0 38px rgba(255, 190, 0, 0.9))
               drop-shadow(0 0 65px rgba(255, 150, 0, 0.6))
               drop-shadow(0 10px 22px rgba(0, 0, 0, 0.9));
        transform: scale(1.035);
    }
    100% {
        filter: drop-shadow(0 0 12px rgba(255, 220, 50, 0.9)) 
               drop-shadow(0 0 26px rgba(255, 170, 0, 0.7))
               drop-shadow(0 0 45px rgba(255, 140, 0, 0.45))
               drop-shadow(0 8px 18px rgba(0, 0, 0, 0.8));
        transform: scale(1.01);
    }
}

@keyframes medallionAuraPulse {
    0% {
        opacity: 0.7;
        transform: translate(-50%, -50%) scale(0.92);
    }
    50% {
        opacity: 1;
        transform: translate(-50%, -50%) scale(1.15);
    }
    100% {
        opacity: 0.7;
        transform: translate(-50%, -50%) scale(0.92);
    }
}

/* =====================================================
   KONTEN UTAMA HALUS (ENTRANCE FADE IN UP)
   Menjaga header & footer tetap kokoh di tempatnya
   ===================================================== */
.display-content > div:not(.header):not(.header-section):not(.bottom-section):not(.kaligrafi-medallion),
.page-layout {
    animation: contentPanelFadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) both;
}

@keyframes contentPanelFadeIn {
    0% {
        opacity: 0;
        transform: translateY(12px);
    }
/* =====================================================
   MEDIA QUERIES RESPONSIF: TABLET & SMARTPHONE (< 768px)
   ===================================================== */
@media (max-width: 1199px) {
    .header h1,
    .header-section h1 {
        font-size: clamp(1.8rem, 4vw, 2.5rem) !important;
        letter-spacing: 3px !important;
    }

    .header h2.sub-header,
    .header h3.sub-header,
    .header .sub-header,
    .header-section h2.sub-header,
    .header-section h3.sub-header,
    h3.sub-header,
    h2.sub-header {
        font-size: clamp(0.85rem, 1.8vw, 1.05rem) !important;
        letter-spacing: 2px !important;
    }

    .header .datetime,
    .header-section .datetime {
        font-size: 1.15rem !important;
        padding: 4px 20px !important;
    }

    .kaligrafi-medallion {
        width: 75px !important;
        height: 75px !important;
        top: 10px !important;
    }
}

/* Smartphone / HP (< 768px) */
@media (max-width: 767px) {
    .header,
    .header-section {
        padding: 0 8px !important;
        margin-top: 0 !important;
        margin-bottom: 6px !important;
        position: relative !important;
        z-index: 5 !important;
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: center !important;
        width: 100% !important;
        box-sizing: border-box !important;
    }

    .header h1,
    .header-section h1 {
        padding: 0 46px !important;
        box-sizing: border-box !important;
        width: 100% !important;
        text-align: center !important;
        font-size: clamp(1.15rem, 4.8vw, 1.55rem) !important;
        letter-spacing: 1.2px !important;
        line-height: 1.2 !important;
        margin: 0 0 2px 0 !important;
        text-shadow:
            1px 1px 0 #d4af37,
            -1px 1px 0 #d4af37,
            1px -1px 0 #d4af37,
            -1px -1px 0 #d4af37,
            1px 1px 4px rgba(0, 0, 0, 0.9),
            0 0 10px rgba(255, 215, 0, 0.5) !important;
    }

    .header h2.sub-header,
    .header h3.sub-header,
    .header .sub-header,
    .header-section h2.sub-header,
    .header-section h3.sub-header,
    .header-section .sub-header,
    h3.sub-header,
    h2.sub-header {
        padding: 0 46px !important;
        box-sizing: border-box !important;
        width: 100% !important;
        text-align: center !important;
        font-size: clamp(0.65rem, 2.5vw, 0.78rem) !important;
        letter-spacing: 0.8px !important;
        line-height: 1.25 !important;
        margin-top: 1px !important;
        margin-bottom: 6px !important;
        white-space: normal !important;
        word-break: break-word !important;
        opacity: 0.9 !important;
    }

    /* KAPSUL WAKTU & JAM RESPONSIVE MOBILE - 100% CENTER, ANTI-OVERFLOW, 2-BARIS MEWAH */
    .header .datetime,
    .header-section .datetime {
        display: inline-flex !important;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: center !important;
        text-align: center !important;
        margin: 3px auto !important;
        padding: 5px 18px !important;
        border-radius: 24px !important;
        max-width: calc(100vw - 20px) !important;
        width: auto !important;
        box-sizing: border-box !important;
        white-space: normal !important;
        overflow: hidden !important;
        border: 1.5px solid rgba(255, 215, 0, 0.6) !important;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.55), 0 0 14px rgba(255, 215, 0, 0.25) !important;
        line-height: 1.2 !important;
    }

    .header .datetime .dt-date-row,
    .header-section .datetime .dt-date-row {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        flex-wrap: wrap !important;
        gap: 2px 6px !important;
        font-size: clamp(0.70rem, 2.7vw, 0.82rem) !important;
        line-height: 1.25 !important;
        text-align: center !important;
    }

    .header .datetime .dt-date-row .dt-sep,
    .header-section .datetime .dt-date-row .dt-sep {
        margin: 0 3px !important;
        font-size: 0.70rem !important;
    }

    .header .datetime .dt-sep-time,
    .header-section .datetime .dt-sep-time {
        display: none !important;
    }

    .header .datetime .dt-time-row,
    .header-section .datetime .dt-time-row {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 6px !important;
        font-size: clamp(0.92rem, 3.6vw, 1.08rem) !important;
        color: #ffffff !important;
        margin-top: 3px !important;
        font-weight: 700 !important;
        letter-spacing: 1.2px !important;
        text-align: center !important;
    }

    .header .datetime .dt-time-row::before,
    .header-section .datetime .dt-time-row::before {
        content: "\f017" !important;
        font-family: "Font Awesome 5 Free" !important;
        font-weight: 900 !important;
        color: #ffd700 !important;
        font-size: 0.85rem !important;
        filter: drop-shadow(0 0 6px rgba(255, 215, 0, 0.7)) !important;
    }

    .header .datetime .dt-time-row .jam-time,
    .header-section .datetime .dt-time-row .jam-time {
        color: #ffffff !important;
        font-variant-numeric: tabular-nums !important;
        letter-spacing: 1px !important;
        text-shadow: 0 0 8px rgba(255, 255, 255, 0.4) !important;
    }

    .kaligrafi-medallion {
        width: 48px !important;
        height: 48px !important;
        top: 6px !important;
        z-index: 10 !important;
    }
    .kaligrafi-medallion.kaligrafi-allah {
        right: 6px !important;
        left: auto !important;
    }
    .kaligrafi-medallion.kaligrafi-muhammad {
        left: 6px !important;
        right: auto !important;
    }
}

/* Smartphone Layar Kecil (< 380px) */
@media (max-width: 380px) {
    .header,
    .header-section {
        padding: 0 4px !important;
    }
    .header h1,
    .header-section h1 {
        padding: 0 42px !important;
        font-size: 1.1rem !important;
        letter-spacing: 0.8px !important;
    }
    .header h2.sub-header,
    .header h3.sub-header,
    .header .sub-header,
    .header-section h2.sub-header,
    .header-section h3.sub-header,
    .header-section .sub-header,
    h3.sub-header,
    h2.sub-header {
        padding: 0 42px !important;
        font-size: 0.62rem !important;
    }
    .header .datetime,
    .header-section .datetime {
        padding: 4px 12px !important;
        border-radius: 20px !important;
        max-width: calc(100vw - 10px) !important;
    }
    .header .datetime .dt-date-row,
    .header-section .datetime .dt-date-row {
        font-size: 0.66rem !important;
        gap: 2px 4px !important;
    }
    .header .datetime .dt-time-row,
    .header-section .datetime .dt-time-row {
        font-size: 0.85rem !important;
        letter-spacing: 0.8px !important;
        margin-top: 2px !important;
    }
    .header .datetime .dt-time-row::before,
    .header-section .datetime .dt-time-row::before {
        font-size: 0.75rem !important;
    }
    .kaligrafi-medallion {
        width: 42px !important;
        height: 42px !important;
        top: 6px !important;
    }
}
</style>

<script>
/**
 * Global Unified Hijri + Masehi + Realtime Clock Formatter
 * Format: [Hari, DD MMMM YYYY] <span class="dt-sep">•</span> <span class="hijri-date">[DD BulanHijri YYYY H]</span> <span class="dt-sep">•</span> [HH:mm:ss WIB]
 */
function getStandardHijriDate(date = new Date()) {
    const hijriMonths = [
        '',
        'Muharram',
        'Safar',
        'Rabiul Awal',
        'Rabiul Akhir',
        'Jumadil Awal',
        'Jumadil Akhir',
        'Rajab',
        'Sya\'ban',
        'Ramadhan',
        'Syawal',
        'Dzulqa\'dah',
        'Dzulhijjah'
    ];

    const gregorianToHijriMonthMap = {
        'januari': 1, 'january': 1, 'jan': 1,
        'februari': 2, 'february': 2, 'feb': 2,
        'maret': 3, 'march': 3, 'mar': 3,
        'april': 4, 'apr': 4,
        'mei': 5, 'may': 5,
        'juni': 6, 'june': 6, 'jun': 6,
        'juli': 7, 'july': 7, 'jul': 7,
        'agustus': 8, 'august': 8, 'aug': 8, 'agu': 8,
        'september': 9, 'sep': 9,
        'oktober': 10, 'october': 10, 'okt': 10, 'oct': 10,
        'november': 11, 'nov': 11,
        'desember': 12, 'december': 12, 'des': 12, 'dec': 12
    };

    let day = null;
    let month = null;
    let year = null;

    // 1. Ekstraksi numerik murni via Intl Ummul Qura (menghindari bug Smart TV ICU yang salah menerjemahkan nama bulan dan era)
    try {
        const fmt = new Intl.DateTimeFormat('en-u-ca-islamic-umalqura', {
            day: 'numeric',
            month: 'numeric',
            year: 'numeric',
            timeZone: 'Asia/Jakarta'
        });

        if (typeof fmt.formatToParts === 'function') {
            const parts = fmt.formatToParts(date);
            for (let i = 0; i < parts.length; i++) {
                const p = parts[i];
                if (p.type === 'day') day = parseInt(p.value, 10);
                if (p.type === 'month') month = parseInt(p.value, 10);
                if (p.type === 'year') year = parseInt(p.value, 10);
            }
        } else {
            const str = fmt.format(date);
            const nums = str.match(/(\d+)[^\d]+(\d+)[^\d]+(\d+)/);
            if (nums) {
                month = parseInt(nums[1], 10);
                day = parseInt(nums[2], 10);
                year = parseInt(nums[3], 10);
            }
        }
    } catch (e) {}

    // 2. Fallback islamic biasa jika umalqura tidak didukung browser TV
    if (!day || !month || !year || month < 1 || month > 12 || year < 1300 || year > 1600) {
        try {
            const fmt2 = new Intl.DateTimeFormat('en-u-ca-islamic', {
                day: 'numeric',
                month: 'numeric',
                year: 'numeric',
                timeZone: 'Asia/Jakarta'
            });
            if (typeof fmt2.formatToParts === 'function') {
                const parts = fmt2.formatToParts(date);
                for (let i = 0; i < parts.length; i++) {
                    const p = parts[i];
                    if (p.type === 'day') day = parseInt(p.value, 10);
                    if (p.type === 'month') month = parseInt(p.value, 10);
                    if (p.type === 'year') year = parseInt(p.value, 10);
                }
            }
        } catch (e) {}
    }

    // 3. Tangani format TV lama yang menghasilkan teks "27 Maret 1448 SM"
    if (!day || !month || !year || month < 1 || month > 12 || year < 1300 || year > 1600) {
        try {
            const tvStr = new Intl.DateTimeFormat('id-ID-u-ca-islamic-umalqura', {
                day: 'numeric',
                month: 'long',
                year: 'numeric',
                timeZone: 'Asia/Jakarta'
            }).format(date);

            const mMatch = tvStr.match(/(\d+)\s+([a-zA-Z']+)\s+(\d+)/);
            if (mMatch) {
                day = parseInt(mMatch[1], 10);
                const mWord = mMatch[2].toLowerCase();
                year = parseInt(mMatch[3], 10);
                if (gregorianToHijriMonthMap[mWord]) {
                    month = gregorianToHijriMonthMap[mWord];
                }
            }
        } catch (e) {}
    }

    // 4. Fallback perhitungan kalender astronomis Islam (Kuwaiti algorithm) jika browser TV tidak mendukung Intl Islamic
    if (!day || !month || !year || month < 1 || month > 12 || year < 1300 || year > 1600) {
        const fallback = computeKuwaitiHijri(date);
        day = fallback.day;
        month = fallback.month;
        year = fallback.year;
    }

    const monthName = hijriMonths[month] || 'Rabiul Awal';
    let result = `${day} ${monthName} ${year} H`;

    // Sanitasi akhir: pastikan tidak ada teks "SM" atau nama bulan Masehi yang tersisa
    result = result.replace(/\bSM\b/gi, 'H');
    for (const [greg, idx] of Object.entries(gregorianToHijriMonthMap)) {
        const reg = new RegExp('\\b' + greg + '\\b', 'gi');
        if (reg.test(result)) {
            result = result.replace(reg, hijriMonths[idx]);
        }
    }
    if (!result.endsWith('H') && !result.endsWith('H.')) {
        result += ' H';
    }

    return result;
}

function computeKuwaitiHijri(date) {
    let day = date.getDate();
    let month = date.getMonth();
    let year = date.getFullYear();

    let m = month + 1;
    let y = year;
    if (m < 3) {
        y -= 1;
        m += 12;
    }

    let a = Math.floor(y / 100);
    let b = 2 - a + Math.floor(a / 4);
    if (y < 1583) b = 0;
    if (y === 1582) {
        if (m > 10) b = -10;
        if (m === 10) {
            b = 0;
            if (day > 4) b = -10;
        }
    }

    let jd = Math.floor(365.25 * (y + 4716)) + Math.floor(30.6001 * (m + 1)) + day + b - 1524;
    b = 0;
    if (jd > 2299160) {
        a = Math.floor((jd - 1867216.25) / 36524.25);
        b = 1 + a - Math.floor(a / 4);
    }
    let bb = jd + b + 1524;
    let cc = Math.floor((bb - 122.1) / 365.25);
    let dd = Math.floor(365.25 * cc);
    let ee = Math.floor((bb - dd) / 30.6001);
    day = (bb - dd) - Math.floor(30.6001 * ee);
    month = ee - 1;
    if (ee > 13) {
        cc += 1;
        month = ee - 13;
    }
    year = cc - 4716;

    let l = jd - 1948440 + 10632;
    let n = Math.floor((l - 1) / 10631);
    l = l - 10631 * n + 354;
    let j = (Math.floor((10985 - l) / 5316)) * (Math.floor((50 * l) / 17719)) + (Math.floor(l / 5670)) * (Math.floor((43 * l) / 15238));
    l = l - (Math.floor((30 - j) / 15)) * (Math.floor((17719 * j) / 50)) - (Math.floor(j / 16)) * (Math.floor((15238 * j) / 43)) + 29;
    let mH = Math.floor((24 * l) / 709);
    let dH = l - Math.floor((709 * mH) / 24);
    let yH = 30 * n + j - 30;

    return { day: dH, month: mH, year: yH };
}

function getStandardMasjidDateTime(now = new Date(), asHtml = true) {
    try {
        const masehi = new Intl.DateTimeFormat('id-ID', {
            weekday: 'long',
            day: 'numeric',
            month: 'long',
            year: 'numeric',
            timeZone: 'Asia/Jakarta'
        }).format(now);

        const hijri = getStandardHijriDate(now);

        const time = new Intl.DateTimeFormat('id-ID', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false,
            timeZone: 'Asia/Jakarta'
        }).format(now).replace(/\./g, ':');

        if (asHtml) {
            return `<div class="dt-date-row"><span class="masehi-date">${masehi}</span><span class="dt-sep">•</span><span class="hijri-date">${hijri}</span></div><span class="dt-sep dt-sep-time">•</span><div class="dt-time-row"><span class="jam-time">${time} WIB</span></div>`;
        }
        return `${masehi} • ${hijri} • ${time} WIB`;
    } catch (err) {
        return now.toLocaleString('id-ID');
    }
}

// Auto-bind realtime clock ke #datetime jika elemen tersedia
(function initGlobalClock() {
    function refreshClock() {
        const el = document.getElementById('datetime');
        if (el) {
            el.innerHTML = getStandardMasjidDateTime(new Date(), true);
        }
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            refreshClock();
            setInterval(refreshClock, 1000);
        });
    } else {
        refreshClock();
        setInterval(refreshClock, 1000);
    }
})();

// Auto-inject and run Dynamic Ambient Lighting by Prayer Time
(function initDynamicAmbientLighting() {
    function applyDynamicTheme() {
        const now = new Date();
        const totalMins = now.getHours() * 60 + now.getMinutes();

        // 03:30 (210) - 06:00 (360) : Subuh
        // 06:00 (360) - 11:30 (690) : Dhuha / Pagi
        // 11:30 (690) - 15:00 (900) : Dzuhur
        // 15:00 (900) - 17:45 (1065): Ashar
        // 17:45 (1065) - 19:15 (1155): Maghrib
        // 19:15 (1155) - 03:30 (210) : Isya & Malam
        let targetTheme = 'theme-isya';
        if (totalMins >= 210 && totalMins < 360) {
            targetTheme = 'theme-subuh';
        } else if (totalMins >= 360 && totalMins < 690) {
            targetTheme = 'theme-dhuha';
        } else if (totalMins >= 690 && totalMins < 900) {
            targetTheme = 'theme-dzuhur';
        } else if (totalMins >= 900 && totalMins < 1065) {
            targetTheme = 'theme-ashar';
        } else if (totalMins >= 1065 && totalMins < 1155) {
            targetTheme = 'theme-maghrib';
        }

        const themeClasses = ['theme-subuh', 'theme-dhuha', 'theme-dzuhur', 'theme-ashar', 'theme-maghrib', 'theme-isya'];
        if (document.body) {
            themeClasses.forEach(c => {
                if (c === targetTheme) {
                    document.body.classList.add(c);
                } else {
                    document.body.classList.remove(c);
                }
            });
        }

        // Pastikan ambient-glow-layer ada di dalam body
        if (!document.getElementById('ambientGlowLayer') && document.body) {
            const glowLayer = document.createElement('div');
            glowLayer.id = 'ambientGlowLayer';
            glowLayer.className = 'ambient-glow-layer';
            glowLayer.innerHTML = '<div class="ambient-orb-top"></div><div class="ambient-orb-bottom"></div>';
            document.body.appendChild(glowLayer);
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            applyDynamicTheme();
            setInterval(applyDynamicTheme, 30000);
        });
    } else {
        applyDynamicTheme();
        setInterval(applyDynamicTheme, 30000);
    }
})();
</script>