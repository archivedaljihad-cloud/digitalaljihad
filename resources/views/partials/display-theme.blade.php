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
    margin-top: 4px !important;
    font-weight: 600 !important;
    letter-spacing: 0.8px !important;
    border: 1.5px solid rgba(255, 215, 0, 0.5) !important;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.4) !important;
    backdrop-filter: blur(8px) !important;
    -webkit-backdrop-filter: blur(8px) !important;
    color: #ffffff !important;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.8) !important;
    white-space: nowrap !important;
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
    top: 15px !important;
    width: 92px !important;
    height: 92px !important;
    z-index: 10 !important;
    user-select: none !important;
    pointer-events: none !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
}

.kaligrafi-medallion img {
    width: 100% !important;
    height: 100% !important;
    object-fit: contain !important;
    filter: drop-shadow(0 0 10px rgba(255, 215, 0, 0.85)) 
           drop-shadow(0 0 22px rgba(255, 170, 0, 0.6))
           drop-shadow(0 8px 16px rgba(0, 0, 0, 0.7)) !important;
    animation: goldenMedallionGlow 4s ease-in-out infinite alternate !important;
}

.kaligrafi-medallion.kaligrafi-allah {
    right: 32px !important;
    left: auto !important;
}

.kaligrafi-medallion.kaligrafi-muhammad {
    left: 32px !important;
    right: auto !important;
}

@keyframes goldenMedallionGlow {
    0% {
        filter: drop-shadow(0 0 8px rgba(255, 215, 0, 0.7)) 
               drop-shadow(0 0 18px rgba(255, 170, 0, 0.45))
               drop-shadow(0 8px 16px rgba(0, 0, 0, 0.7));
        transform: scale(1);
    }
    50% {
        filter: drop-shadow(0 0 14px rgba(255, 225, 50, 0.95)) 
               drop-shadow(0 0 30px rgba(255, 190, 0, 0.75))
               drop-shadow(0 8px 18px rgba(0, 0, 0, 0.8));
        transform: scale(1.02);
    }
    100% {
        filter: drop-shadow(0 0 8px rgba(255, 215, 0, 0.7)) 
               drop-shadow(0 0 18px rgba(255, 170, 0, 0.45))
               drop-shadow(0 8px 16px rgba(0, 0, 0, 0.7));
        transform: scale(1);
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
    100% {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

<script>
/**
 * Global Unified Hijri + Masehi + Realtime Clock Formatter
 * Format: [Hari, DD MMMM YYYY] <span class="dt-sep">•</span> <span class="hijri-date">[DD BulanHijri YYYY H]</span> <span class="dt-sep">•</span> [HH:mm:ss WIB]
 */
function getStandardMasjidDateTime(now = new Date(), asHtml = true) {
    try {
        const masehi = new Intl.DateTimeFormat('id-ID', {
            weekday: 'long',
            day: 'numeric',
            month: 'long',
            year: 'numeric',
            timeZone: 'Asia/Jakarta'
        }).format(now);

        const hijri = new Intl.DateTimeFormat('id-ID-u-ca-islamic-umalqura', {
            day: 'numeric',
            month: 'long',
            year: 'numeric',
            timeZone: 'Asia/Jakarta'
        }).format(now);

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
</script>