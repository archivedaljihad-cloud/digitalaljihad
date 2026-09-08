<!-- resources/views/rotator.blade.php -->
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Informasi Masjid - Rotating Display</title>

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
            will-change: transform, opacity;
            backface-visibility: hidden;
            perspective: 1000px;
            pointer-events: none;
            opacity: 0;
            transform: translate3d(40px, 0, 0);
            /* Transisi anggun: awal meluncur tenang tanpa sentakan, berpadu lembut dengan fade */
            transition: transform 2.8s cubic-bezier(0.4, 0, 0.2, 1), opacity 2.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        iframe.active {
            transform: translate3d(0, 0, 0);
            opacity: 1;
            z-index: 2;
            pointer-events: auto;
            transition: transform 2.8s cubic-bezier(0.4, 0, 0.2, 1), opacity 2.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        iframe.outgoing {
            transform: translate3d(-40px, 0, 0);
            opacity: 0;
            z-index: 1;
            pointer-events: none;
            transition: transform 2.8s cubic-bezier(0.4, 0, 0.2, 1), opacity 2.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Mode standby instan tanpa animasi saat me-reset frame berikutnya ke sisi kanan */
        iframe.standby {
            transition: none !important;
            transform: translate3d(40px, 0, 0) !important;
            opacity: 0 !important;
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
    </style>
</head>

<body>

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

        let rotationInterval = parseInt({{ $rotationInterval ?? 15 }});
        if (isNaN(rotationInterval) || rotationInterval < 10) rotationInterval = 15;
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

            resetCountdown();
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
                const response = await fetch('/prayer-mode/status?_=' + Date.now());
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
                const response = await fetch('/rotation-settings?_=' + Date.now());
                const data = await response.json();
                let hasChanges = false;

                let apiIntervalRaw = data.interval !== undefined ? data.interval : data.rotation_interval;
                if (apiIntervalRaw !== undefined) {
                    let apiInterval = parseInt(apiIntervalRaw);
                    if (!isNaN(apiInterval) && apiInterval >= 1 && apiInterval !== rotationInterval) {
                        rotationInterval = apiInterval;
                        hasChanges = true;
                        showNotification(`Interval berubah menjadi ${rotationInterval} detik`);
                    }
                }

                let apiEnabledRaw = data.enabled !== undefined ? data.enabled : data.rotation_enabled;
                if (apiEnabledRaw !== undefined) {
                    let apiEnabled = parseBool(apiEnabledRaw);
                    if (apiEnabled !== rotationEnabled) {
                        rotationEnabled = apiEnabled;
                        hasChanges = true;
                        showNotification(rotationEnabled ? 'Rotasi diaktifkan' : 'Rotasi dinonaktifkan');
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
                        hasChanges = true;
                        if (currentIndex >= activePages.length) currentIndex = 0;
                        showNotification(`Halaman yang ditampilkan diperbarui`);
                    }
                }

                if (hasChanges && !isLoading) {
                    if (countdownIntervalId) clearInterval(countdownIntervalId);
                    if (activePages.length > 0 && currentIndex < activePages.length) {
                        loadPage(currentIndex);
                    } else if (activePages.length > 0) {
                        currentIndex = 0;
                        loadPage(0);
                    }
                    resetCountdown();
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
    </script>
</body>
</html>