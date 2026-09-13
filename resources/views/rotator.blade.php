<!-- resources/views/rotator.blade.php -->
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Informasi Masjid - Rotating Display</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        @font-face {
            font-family: 'Masking Renta';
            src: url('{{ asset("fonts/MaskingRenta.otf") }}') format('opentype');
            font-weight: normal;
            font-style: normal;
            font-display: swap;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            overflow: hidden;
            background-color: #050505;
        }

        .iframe-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: none;
            overflow: hidden;
        }

        iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: none;
            background-color: #050505;
            will-change: opacity, transform;
            backface-visibility: hidden;
            perspective: 1000px;
            pointer-events: none;
            opacity: 0;
            transform: scale(0.995);
            /* Transisi cross-fade siaran TV: sangat halus tanpa pergeseran horizontal */
            transition: opacity 1.2s cubic-bezier(0.4, 0, 0.2, 1), transform 1.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        iframe.active {
            opacity: 1;
            transform: scale(1);
            z-index: 2;
            pointer-events: auto;
            transition: opacity 1.2s cubic-bezier(0.4, 0, 0.2, 1), transform 1.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        iframe.outgoing {
            opacity: 0;
            transform: scale(1.005);
            z-index: 1;
            pointer-events: none;
            transition: opacity 1.2s cubic-bezier(0.4, 0, 0.2, 1), transform 1.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Mode standby instan tanpa animasi saat memuat halaman berikutnya */
        iframe.standby {
            transition: none !important;
            opacity: 0 !important;
            transform: scale(0.995) !important;
            z-index: 1 !important;
            pointer-events: none !important;
        }

        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #0a4d68, #088395);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            transition: opacity 0.5s ease;
        }

        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-top: 4px solid #ffd700;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .page-indicator {
            display: none;
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(10px);
            padding: 8px 16px;
            border-radius: 30px;
            font-size: 0.9rem;
            color: white;
            z-index: 1001;
            font-family: monospace;
            align-items: center;
            gap: 10px;
            border-left: 3px solid #ffd700;
        }

        .page-indicator.debug-visible { display: flex; }

        .page-indicator .page-name {
            font-weight: bold;
            color: #ffd700;
        }

        .page-indicator .countdown {
            background: rgba(255, 255, 255, 0.2);
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 0.8rem;
        }

        .auto-update-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(10px);
            padding: 10px 20px;
            border-radius: 8px;
            color: #ffd700;
            font-size: 0.8rem;
            z-index: 1002;
            transform: translateX(150%);
            transition: transform 0.3s ease;
            border-left: 3px solid #ffd700;
        }

        .auto-update-notification.show {
            transform: translateX(0);
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.05); opacity: 0.8; }
        }

        @media (max-width: 768px) {
            .page-indicator {
                bottom: 10px;
                right: 10px;
                padding: 5px 12px;
                font-size: 0.75rem;
            }
        }

        /* =====================================================
           FLOATING SMART NEXT PRAYER BAR (Kapsul Mewah Sholat Berikutnya)
           ===================================================== */
        .next-prayer-bar-wrapper {
            position: fixed;
            top: 18px;
            right: 28px;
            z-index: 999;
            pointer-events: none;
            transition: opacity 0.5s ease, transform 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .next-prayer-bar {
            display: flex;
            align-items: center;
            gap: 12px;
            background: linear-gradient(135deg, rgba(6, 26, 17, 0.90) 0%, rgba(2, 14, 9, 0.96) 100%);
            border: 1.5px solid rgba(212, 175, 55, 0.5);
            border-radius: 35px;
            padding: 7px 18px 7px 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.7), 0 0 18px rgba(212, 175, 55, 0.22), inset 0 1px 0 rgba(255, 238, 170, 0.3);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            position: relative;
            overflow: hidden;
        }

        .npb-shimmer {
            position: absolute;
            top: 0;
            left: -100%;
            width: 60%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.18), transparent);
            transform: skewX(-20deg);
            animation: npbShimmer 6s infinite;
            pointer-events: none;
        }

        @keyframes npbShimmer {
            0%, 80% { left: -100%; }
            100% { left: 200%; }
        }

        .npb-icon {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(212, 175, 55, 0.3) 0%, rgba(212, 175, 55, 0.05) 100%);
            border: 1px solid rgba(212, 175, 55, 0.6);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #FFD700;
            font-size: 16px;
            box-shadow: 0 0 12px rgba(212, 175, 55, 0.35);
            flex-shrink: 0;
        }

        .npb-content {
            display: flex;
            flex-direction: column;
            line-height: 1.15;
        }

        .npb-label-row {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .npb-badge {
            font-size: 9px;
            font-weight: 800;
            letter-spacing: 1px;
            color: #10B981;
            background: rgba(16, 185, 129, 0.18);
            border: 0.5px solid rgba(16, 185, 129, 0.4);
            padding: 1px 6px;
            border-radius: 10px;
        }

        .npb-prayer-name {
            font-size: 13px;
            font-weight: 800;
            color: #FFD700;
            letter-spacing: 1px;
            text-transform: uppercase;
            text-shadow: 0 1px 4px rgba(0, 0, 0, 0.8);
        }

        .npb-time-row {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-top: 2px;
        }

        .npb-schedule-time {
            font-size: 11px;
            color: #CBD5E1;
            font-weight: 600;
        }

        .npb-sep {
            color: rgba(212, 175, 55, 0.6);
            font-size: 10px;
        }

        .npb-countdown {
            font-size: 13px;
            font-weight: 800;
            color: #FFFFFF;
            font-family: monospace;
            letter-spacing: 0.5px;
            text-shadow: 0 0 8px rgba(255, 255, 255, 0.5);
        }

        .npb-pulse-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: #10B981;
            box-shadow: 0 0 10px #10B981;
            animation: npbPulse 1.5s infinite ease-in-out;
            margin-left: 4px;
            flex-shrink: 0;
        }

        @keyframes npbPulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.3; transform: scale(0.7); }
        }

        /* =====================================================
           DYNAMIC AMBIENT THEMES IN ROTATOR
           ===================================================== */
        body.theme-subuh { --ambient-orb-1: rgba(56, 189, 248, 0.22); --ambient-orb-2: rgba(251, 191, 36, 0.16); }
        body.theme-dhuha { --ambient-orb-1: rgba(250, 204, 21, 0.22); --ambient-orb-2: rgba(16, 185, 129, 0.20); }
        body.theme-dzuhur { --ambient-orb-1: rgba(5, 150, 105, 0.25); --ambient-orb-2: rgba(255, 215, 0, 0.22); }
        body.theme-ashar { --ambient-orb-1: rgba(249, 115, 22, 0.22); --ambient-orb-2: rgba(217, 119, 6, 0.22); }
        body.theme-maghrib { --ambient-orb-1: rgba(225, 29, 72, 0.22); --ambient-orb-2: rgba(139, 92, 246, 0.20); }
        body.theme-isya { --ambient-orb-1: rgba(59, 130, 246, 0.22); --ambient-orb-2: rgba(212, 175, 55, 0.20); }

        .ambient-lighting-layer {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 0;
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
    </style>
</head>

<body>

    <!-- DYNAMIC AMBIENT LIGHTING ORBS LAYER -->
    <div class="ambient-lighting-layer" id="ambientLighting">
        <div class="ambient-orb-top"></div>
        <div class="ambient-orb-bottom"></div>
    </div>

    <!-- FLOATING SMART NEXT PRAYER BAR (Kapsul Mewah Sholat Berikutnya) -->
    @if(!isset($settings) || $settings->isNextPrayerBarEnabled())
    <div class="next-prayer-bar-wrapper" id="nextPrayerBarWrapper">
        <div class="next-prayer-bar" id="nextPrayerBar">
            <div class="npb-shimmer"></div>
            <div class="npb-icon">
                <i class="fa-solid fa-mosque"></i>
            </div>
            <div class="npb-content">
                <div class="npb-label-row">
                    <span class="npb-badge">SELANJUTNYA</span>
                    <span class="npb-prayer-name" id="npbPrayerName">MEMUAT...</span>
                </div>
                <div class="npb-time-row">
                    <span class="npb-schedule-time" id="npbScheduleTime">--:--</span>
                    <span class="npb-sep">•</span>
                    <span class="npb-countdown" id="npbCountdown">-00:00:00</span>
                </div>
            </div>
            <div class="npb-pulse-dot"></div>
        </div>
    </div>
    @endif

    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner"></div>
    </div>

    <div class="page-indicator" id="pageIndicator">
        <span class="page-name" id="pageName">Memuat...</span>
        <span style="opacity:.5;">●</span>
        <span class="countdown" id="countdown">-- dtk</span>
    </div>

    <div class="auto-update-notification" id="notification">
        <i class="fas fa-sync-alt"></i> Pengaturan diperbarui...
    </div>

    <div class="iframe-container" id="iframeContainer">
        <!-- Dua iframe untuk efek slide -->
        <iframe id="frame1" class="active" src="" title="Rotating Content" allow="autoplay"></iframe>
        <iframe id="frame2" src="" title="Rotating Content" allow="autoplay"></iframe>
    </div>

    <script>
        const DEBUG_MODE = new URLSearchParams(window.location.search).get('debug') === '1';
        const parseBool = (val) => val === true || val === 1 || val === "1" || String(val).toLowerCase() === "true";

        let rotationInterval = parseInt({{ $rotationInterval ?? 20 }});
        if (isNaN(rotationInterval) || rotationInterval < 5) rotationInterval = 20;
        let rotationEnabled = parseBool("{{ $rotationEnabled ?? true }}");

        @php
        $safePages = $rotationPages ?? [];
        if (is_string($safePages)) {
            $decoded = json_decode($safePages, true);
            if (is_array($decoded)) {
                $safePages = $decoded;
            } else {
                $decoded2 = json_decode($decoded, true);
                if (is_array($decoded2)) {
                    $safePages = $decoded2;
                }
            }
        }
        if (!is_array($safePages)) { $safePages = []; }
        @endphp

        let pages = {!! json_encode($safePages) !!};
        if (!Array.isArray(pages)) { pages = []; }

        let activePages = pages.filter(page => parseBool(page.active));

        if (activePages.length === 0) {
            activePages = [{ url: '/utama-embed', name: 'Jadwal Sholat', active: true }];
        }

        let currentIndex = 0;
        let countdown = rotationInterval;
        let countdownIntervalId = null;
        let isLoading = false;
        let failsafeTimeout = null;

        // Iframe management for sliding
        let currentFrameId = 'frame1';

        function getFrames() {
            if (currentFrameId === 'frame1') {
                return { current: document.getElementById('frame1'), next: document.getElementById('frame2') };
            } else {
                return { current: document.getElementById('frame2'), next: document.getElementById('frame1') };
            }
        }

        function isPrayerActive() {
            return localStorage.getItem('lockPageRotation') === 'true';
        }

        function applyDebugVisibility() {
            const pageIndicator = document.getElementById('pageIndicator');
            if (pageIndicator) {
                if (DEBUG_MODE) {
                    pageIndicator.classList.add('debug-visible');
                } else {
                    pageIndicator.classList.remove('debug-visible');
                }
            }
        }

        function loadPage(index) {
            if (isLoading) return;
            isLoading = true;

            const frames = getFrames();
            const nextFrame = frames.next;
            const loadingOverlay = document.getElementById('loadingOverlay');
            const pageName = document.getElementById('pageName');

            // If it's the very first load, show loader
            if (!frames.current.src || frames.current.src === window.location.href) {
                loadingOverlay.style.display = 'flex';
            }

            if (activePages[index] && activePages[index].name) {
                pageName.textContent = activePages[index].name;
            }

            if (activePages[index] && activePages[index].url) {
                let routeUrl = activePages[index].url;
                if (!routeUrl.startsWith('/') && !routeUrl.startsWith('http')) {
                    routeUrl = '/' + routeUrl;
                }
                
                // Siapkan iframe berikutnya di posisi standby (sisi kanan) seketika tanpa transisi
                nextFrame.className = 'standby'; 
                void nextFrame.offsetWidth; // Force layout reflow
                // Set src untuk mulai memuat
                nextFrame.src = routeUrl;
            }

            clearTimeout(failsafeTimeout);
            failsafeTimeout = setTimeout(() => {
                if (isLoading) {
                    console.warn('Iframe load failsafe triggered.');
                    loadingOverlay.style.display = 'none';
                    isLoading = false;
                    resetCountdown();
                }
            }, 6000);

            // Jangan kurangi detik saat iframe masih proses muat konten.
            // Countdown akan berjalan utuh saat onFrameLoad selesai menampilkan transisi.
            if (countdownIntervalId) {
                clearInterval(countdownIntervalId);
                countdownIntervalId = null;
            }
            countdown = rotationInterval;
            updateCountdownDisplay();
        }

        function resetCountdown() {
            if (countdownIntervalId) clearInterval(countdownIntervalId);
            countdown = rotationInterval;
            updateCountdownDisplay();

            if (rotationEnabled && activePages.length > 1) {
                countdownIntervalId = setInterval(() => {
                    if (isPrayerActive()) {
                        return;
                    }

                    if (countdown > 0 && !isLoading) {
                        countdown--;
                        updateCountdownDisplay();
                        if (countdown === 0) {
                            nextPage();
                        }
                    }
                }, 1000);
            }
        }

        function updateCountdownDisplay() {
            const countdownEl = document.getElementById('countdown');
            if (countdownEl) {
                countdownEl.textContent = `${countdown} dtk`;
                if (countdown <= 3 && countdown > 0) {
                    countdownEl.style.background = '#dc3545';
                    countdownEl.style.animation = 'pulse 0.5s ease infinite';
                } else {
                    countdownEl.style.background = 'rgba(255, 255, 255, 0.2)';
                    countdownEl.style.animation = 'none';
                }
            }
        }

        function nextPage() {
            if (isPrayerActive()) return;
            if (isLoading || !rotationEnabled || activePages.length <= 1) return;
            currentIndex = (currentIndex + 1) % activePages.length;
            loadPage(currentIndex);
        }

        function skipToNext() {
            if (isPrayerActive()) return;
            if (isLoading || !rotationEnabled || activePages.length <= 1) return;
            nextPage();
        }

        function openSettings() {
            window.open('/settings', '_blank');
        }

        function showNotification(message) {
            const notification = document.getElementById('notification');
            notification.innerHTML = `<i class="fas fa-sync-alt fa-spin"></i> ${message}`;
            notification.classList.add('show');
            setTimeout(() => notification.classList.remove('show'), 3500);
        }

        async function checkPrayerModeAPI() {
            try {
                const response = await fetch('/prayer-mode/status?_=' + Date.now(), {
                    cache: 'no-store',
                    headers: { 'Cache-Control': 'no-cache', 'Pragma': 'no-cache' }
                });
                const data = await response.json();
                
                if (data.active) {
                    if (localStorage.getItem('lockPageRotation') !== 'true') {
                        localStorage.setItem('lockPageRotation', 'true');
                        // Segera hentikan countdown timer & proses rotasi halaman manapun
                        if (countdownIntervalId) {
                            clearInterval(countdownIntervalId);
                            countdownIntervalId = null;
                        }
                        clearTimeout(failsafeTimeout);
                        isLoading = false;

                        const pageName = document.getElementById('pageName');
                        if (pageName) {
                            pageName.textContent = 'Mode Sholat (' + (data.prayer || 'Waktu Sholat') + ')';
                        }
                        const countdownEl = document.getElementById('countdown');
                        if (countdownEl) {
                            countdownEl.textContent = 'Sholat';
                        }

                        // Langsung muat halaman Mode Sholat pada iframe next dan tampilkan
                        const frames = getFrames();
                        frames.next.className = 'standby';
                        frames.next.src = '/prayer-mode';
                    }
                } else {
                    if (localStorage.getItem('lockPageRotation') === 'true') {
                        localStorage.setItem('lockPageRotation', 'false');
                        // Resume normal rotation seketika saat waktu sholat selesai
                        currentIndex = 0; // Kembalikan ke halaman pertama / utama
                        isLoading = false;
                        if (activePages.length > 0) {
                            loadPage(currentIndex);
                        }
                    }
                }
            } catch (error) {
                console.error('Prayer Mode Check Failed:', error);
            }
        }

        async function fetchLatestSettings() {
            try {
                const response = await fetch('/rotation-settings?_=' + Date.now(), {
                    cache: 'no-store',
                    headers: { 'Cache-Control': 'no-cache', 'Pragma': 'no-cache' }
                });
                const data = await response.json();

                let apiIntervalRaw = data.interval !== undefined ? data.interval : data.rotation_interval;
                if (apiIntervalRaw !== undefined) {
                    let apiInterval = parseInt(apiIntervalRaw);
                    if (!isNaN(apiInterval) && apiInterval >= 5 && apiInterval !== rotationInterval) {
                        rotationInterval = apiInterval;
                        showNotification(`Interval rotasi diperbarui: ${rotationInterval} detik`);
                        if (!isLoading && rotationEnabled && activePages.length > 1) {
                            resetCountdown();
                        } else {
                            countdown = rotationInterval;
                            updateCountdownDisplay();
                        }
                    }
                }

                let apiEnabledRaw = data.enabled !== undefined ? data.enabled : data.rotation_enabled;
                if (apiEnabledRaw !== undefined) {
                    let apiEnabled = parseBool(apiEnabledRaw);
                    if (apiEnabled !== rotationEnabled) {
                        rotationEnabled = apiEnabled;
                        showNotification(rotationEnabled ? 'Rotasi diaktifkan' : 'Rotasi dinonaktifkan');
                        if (rotationEnabled && !isLoading) {
                            resetCountdown();
                        } else if (!rotationEnabled && countdownIntervalId) {
                            clearInterval(countdownIntervalId);
                            countdownIntervalId = null;
                        }
                    }
                }

                let apiPagesRaw = data.pages !== undefined ? data.pages : data.rotation_pages;
                if (apiPagesRaw !== undefined) {
                    let newPages = [];
                    if (typeof apiPagesRaw === 'string') {
                        try {
                            newPages = JSON.parse(apiPagesRaw);
                            if (typeof newPages === 'string') newPages = JSON.parse(newPages);
                        } catch (e) {
                            console.error('Parse JSON Halaman API Error:', e);
                        }
                    } else {
                        newPages = apiPagesRaw;
                    }

                    if (!Array.isArray(newPages)) newPages = [];
                    let newActivePages = newPages.filter(page => parseBool(page.active));

                    if (newActivePages.length === 0) {
                        newActivePages = [{ url: '/utama-embed', name: 'Jadwal Sholat', active: true }];
                    }

                    const oldActiveUrls = activePages.map(p => p.url).sort().join(',');
                    const newActiveUrls = newActivePages.map(p => p.url).sort().join(',');

                    if (oldActiveUrls !== newActiveUrls) {
                        pages = newPages;
                        activePages = newActivePages;
                        if (currentIndex >= activePages.length) currentIndex = 0;
                        showNotification(`Daftar halaman diperbarui`);
                        if (!isLoading) {
                            loadPage(currentIndex);
                        }
                    }
                }
            } catch (error) {
                console.error('Error fetching rotation settings:', error);
            }
        }

        // Setup event listener untuk frame 1 & 2
        function onFrameLoad(frameElement) {
            const frames = getFrames();
            
            // Jika ini frame pertama yang dimuat saat awal buka halaman
            if (!frames.current.src || frames.current.src === window.location.href) {
                clearTimeout(failsafeTimeout);
                document.getElementById('loadingOverlay').style.display = 'none';
                
                // Jadikan frame yang baru dimuat ini sebagai current (karena current tadinya kosong)
                frameElement.className = 'active';
                currentFrameId = frameElement.id;
                
                isLoading = false;
                if (rotationEnabled && activePages.length > 1) {
                    resetCountdown();
                }
                return;
            }

            // Jika yang selesai memuat adalah iframe berikutnya (next)
            if (frameElement === frames.next && frameElement.src && frameElement.src !== window.location.href) {
                clearTimeout(failsafeTimeout);
                
                // Beri waktu 80ms dan double requestAnimationFrame agar iframe baru selesai initial layout paint
                // Hal ini menghilangkan stutter/jank (hentakan awal) secara total
                setTimeout(() => {
                    requestAnimationFrame(() => {
                        requestAnimationFrame(() => {
                            // Lakukan transisi slide & fade yang lembut
                            frames.current.className = 'outgoing'; // Meluncur pelan ke kiri sambil memudar
                            frames.next.className = 'active';     // Masuk anggun dari kanan ke tengah

                            // Swap ID
                            currentFrameId = frames.next.id;
                            
                            isLoading = false;
                            if (rotationEnabled && activePages.length > 1) {
                                resetCountdown();
                            }
                        });
                    });
                }, 80);
            }
        }

        function onFrameError(frameElement) {
            const frames = getFrames();
            if (frameElement === frames.next) {
                console.error('Iframe Error Event Terpicu pada halaman baru');
                clearTimeout(failsafeTimeout);
                isLoading = false;
                document.getElementById('loadingOverlay').style.display = 'none';

                setTimeout(() => {
                    if (activePages.length > 0) {
                        loadPage(currentIndex);
                    }
                }, 3000);
            }
        }

        document.getElementById('frame1').addEventListener('load', function() { onFrameLoad(this); });
        document.getElementById('frame2').addEventListener('load', function() { onFrameLoad(this); });
        
        document.getElementById('frame1').addEventListener('error', function() { onFrameError(this); });
        document.getElementById('frame2').addEventListener('error', function() { onFrameError(this); });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowRight') nextPage();
            else if (e.key === ' ' || e.key === 'Space') { e.preventDefault(); skipToNext(); }
            else if (e.key === 's' || e.key === 'S') openSettings();
        });

        applyDebugVisibility();
        setInterval(checkPrayerModeAPI, 2000); // Periksa status sholat setiap 2 detik untuk respon cepat
        setInterval(fetchLatestSettings, 5000);
        checkPrayerModeAPI();

        console.log('Rotator Initialized. Active Pages:', activePages.length, '| DEBUG_MODE:', DEBUG_MODE);

        if (activePages.length > 0) {
            loadPage(0);
        } else {
            document.getElementById('pageName').textContent = 'Tidak ada halaman (Sistem Error)';
        }

        /* =====================================================
           FLOATING SMART NEXT PRAYER COUNTDOWN ENGINE
           ===================================================== */
        @php
        $prayerData = [];
        if (isset($jadwalSholat) && count($jadwalSholat) > 0) {
            foreach ($jadwalSholat as $js) {
                $prayerData[] = [
                    'name' => strtoupper($js->nama_sholat),
                    'time' => substr($js->waktu, 0, 5),
                ];
            }
        }
        @endphp

        const prayerList = {!! json_encode($prayerData) !!};

        function updateNextPrayerBar() {
            const barWrapper = document.getElementById('nextPrayerBarWrapper');
            if (!barWrapper) return;

            // Sembunyikan otomatis jika prayer mode sedang aktif
            if (localStorage.getItem('lockPageRotation') === 'true') {
                barWrapper.style.opacity = '0';
                barWrapper.style.pointerEvents = 'none';
                return;
            } else {
                barWrapper.style.opacity = '1';
            }

            if (!prayerList || prayerList.length === 0) return;

            const now = new Date();
            const currentSeconds = now.getHours() * 3600 + now.getMinutes() * 60 + now.getSeconds();

            let nextPrayer = null;
            let minDiff = Infinity;

            prayerList.forEach(p => {
                const parts = p.time.split(':');
                if (parts.length >= 2) {
                    const prayerSeconds = parseInt(parts[0], 10) * 3600 + parseInt(parts[1], 10) * 60;
                    let diff = prayerSeconds - currentSeconds;
                    if (diff > 0 && diff < minDiff) {
                        minDiff = diff;
                        nextPrayer = p;
                    }
                }
            });

            // Jika seluruh waktu sholat hari ini sudah lewat, target sholat pertama besok (Subuh)
            if (!nextPrayer && prayerList.length > 0) {
                nextPrayer = prayerList[0];
                const parts = nextPrayer.time.split(':');
                const prayerSeconds = parseInt(parts[0], 10) * 3600 + parseInt(parts[1], 10) * 60;
                minDiff = (86400 - currentSeconds) + prayerSeconds;
            }

            if (nextPrayer) {
                const elName = document.getElementById('npbPrayerName');
                const elTime = document.getElementById('npbScheduleTime');
                const elCd = document.getElementById('npbCountdown');

                if (elName) elName.textContent = nextPrayer.name;
                if (elTime) elTime.textContent = nextPrayer.time + ' WIB';

                const h = Math.floor(minDiff / 3600);
                const m = Math.floor((minDiff % 3600) / 60);
                const s = minDiff % 60;

                const pad = (n) => String(n).padStart(2, '0');
                if (elCd) {
                    elCd.textContent = `-${pad(h)}:${pad(m)}:${pad(s)}`;
                }
            }
        }

        setInterval(updateNextPrayerBar, 1000);
        updateNextPrayerBar();

        /* =====================================================
           DYNAMIC AMBIENT THEMES IN ROTATOR
           ===================================================== */
        function applyRotatorAmbientTheme() {
            const now = new Date();
            const totalMins = now.getHours() * 60 + now.getMinutes();

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
            themeClasses.forEach(c => {
                if (c === targetTheme) {
                    document.body.classList.add(c);
                } else {
                    document.body.classList.remove(c);
                }
            });
        }
        applyRotatorAmbientTheme();
        setInterval(applyRotatorAmbientTheme, 30000);
    </script>
</body>
</html>