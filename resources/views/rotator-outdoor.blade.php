<!-- resources/views/rotator-outdoor.blade.php -->
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Display Masjid Luar (Serambi) & Siaran Mimbar Live</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
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
            border-radius: 20px;
            color: white;
            font-size: 14px;
            z-index: 100;
            border: 1px solid rgba(255, 215, 0, 0.3);
        }

        .page-indicator.debug-visible {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .outdoor-badge-pill {
            position: fixed;
            bottom: 18px;
            left: 20px;
            background: rgba(10, 30, 20, 0.8);
            border: 1px solid rgba(255, 215, 0, 0.35);
            backdrop-filter: blur(10px);
            padding: 6px 16px;
            border-radius: 20px;
            color: #ffd700;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.5px;
            z-index: 100;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.4);
        }

        .outdoor-badge-pill i {
            color: #10b981;
        }

        .outdoor-broadcast-active {
            border-color: #ef4444;
            color: #fecaca;
            background: rgba(185, 28, 28, 0.85);
        }

        .auto-update-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background: rgba(40, 167, 69, 0.9);
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            z-index: 2000;
            font-size: 14px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            opacity: 0;
            transition: opacity 0.3s ease;
            pointer-events: none;
        }

        .auto-update-notification.show {
            opacity: 1;
        }

        /* Ambient Lighting */
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
            background: radial-gradient(circle, rgba(255,215,0,0.18) 0%, rgba(0,0,0,0) 70%);
        }

        .ambient-orb-bottom {
            position: absolute;
            width: 45vw;
            height: 45vw;
            bottom: -15vw;
            right: -10vw;
            border-radius: 50%;
            filter: blur(85px);
            background: radial-gradient(circle, rgba(16,185,129,0.20) 0%, rgba(0,0,0,0) 70%);
        }
    </style>
</head>

<body>

    <div class="ambient-lighting-layer">
        <div class="ambient-orb-top"></div>
        <div class="ambient-orb-bottom"></div>
    </div>

    <div class="outdoor-badge-pill" id="outdoorBadge">
        <i class="fa-solid fa-tv"></i>
        <span id="outdoorStatusText">TV SERAMBI / LUAR MASJID</span>
    </div>

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
        <iframe id="frame1" class="active" src="" title="Outdoor Content" allow="autoplay"></iframe>
        <iframe id="frame2" src="" title="Outdoor Content" allow="autoplay"></iframe>
    </div>

    <script>
        const DEBUG_MODE = new URLSearchParams(window.location.search).get('debug') === '1';
        const FORCE_MIMBAR = new URLSearchParams(window.location.search).get('mimbar') === '1';
        const parseBool = (val) => val === true || val === 1 || val === "1" || String(val).toLowerCase() === "true";

        let rotationInterval = parseInt({{ $rotationInterval ?? 20 }});
        if (isNaN(rotationInterval) || rotationInterval < 5) rotationInterval = 20;
        let rotationEnabled = parseBool("{{ $rotationEnabled ?? true }}");
        let cctvAutoSwitch = parseBool("{{ $cctvAutoSwitch ? 'true' : 'false' }}");

        @php
        $safePages = $rotationPages ?? [];
        if (is_string($safePages)) {
            $decoded = json_decode($safePages, true);
            if (is_array($decoded)) {
                $safePages = $decoded;
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
        let isBroadcastingMimbar = false;

        let currentFrameId = 'frame1';
        function getFrames() {
            if (currentFrameId === 'frame1') {
                return { current: document.getElementById('frame1'), next: document.getElementById('frame2') };
            } else {
                return { current: document.getElementById('frame2'), next: document.getElementById('frame1') };
            }
        }

        function isPrayerActive() {
            return localStorage.getItem('lockPageRotationOutdoor') === 'true';
        }

        function loadPage(index) {
            if (isLoading || isBroadcastingMimbar) return;
            isLoading = true;

            const frames = getFrames();
            const nextFrame = frames.next;
            const loadingOverlay = document.getElementById('loadingOverlay');
            const pageName = document.getElementById('pageName');

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
                
                nextFrame.className = 'standby'; 
                void nextFrame.offsetWidth;
                nextFrame.src = routeUrl;
            }

            clearTimeout(failsafeTimeout);
            failsafeTimeout = setTimeout(() => {
                if (isLoading) {
                    loadingOverlay.style.display = 'none';
                    isLoading = false;
                    resetCountdown();
                }
            }, 6000);

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

            if (rotationEnabled && activePages.length > 1 && !isBroadcastingMimbar) {
                countdownIntervalId = setInterval(() => {
                    if (isPrayerActive() || isBroadcastingMimbar) return;

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
            }
        }

        function nextPage() {
            if (isPrayerActive() || isBroadcastingMimbar) return;
            if (isLoading || !rotationEnabled || activePages.length <= 1) return;
            currentIndex = (currentIndex + 1) % activePages.length;
            loadPage(currentIndex);
        }

        function showNotification(message) {
            const notification = document.getElementById('notification');
            notification.innerHTML = `<i class="fas fa-sync-alt fa-spin"></i> ${message}`;
            notification.classList.add('show');
            setTimeout(() => notification.classList.remove('show'), 3500);
        }

        // Beralih ke Siaran CCTV Mimbar Live
        function switchToMimbarLive(eventName = 'KHUTBAH') {
            if (isBroadcastingMimbar) return;
            isBroadcastingMimbar = true;
            localStorage.setItem('lockPageRotationOutdoor', 'true');

            if (countdownIntervalId) {
                clearInterval(countdownIntervalId);
                countdownIntervalId = null;
            }

            const badge = document.getElementById('outdoorBadge');
            const statusText = document.getElementById('outdoorStatusText');
            if (badge) badge.className = 'outdoor-badge-pill outdoor-broadcast-active';
            if (statusText) statusText.innerHTML = `<i class="fa-solid fa-circle-dot fa-beat text-white mr-1"></i> LIVE SIARAN MIMBAR (${eventName})`;

            const frames = getFrames();
            frames.next.className = 'standby';
            frames.next.src = '/live-mimbar-embed';
        }

        // Kembali ke Rotasi Display Normal
        function switchBackToNormal() {
            if (!isBroadcastingMimbar) return;
            isBroadcastingMimbar = false;
            localStorage.setItem('lockPageRotationOutdoor', 'false');

            const badge = document.getElementById('outdoorBadge');
            const statusText = document.getElementById('outdoorStatusText');
            if (badge) badge.className = 'outdoor-badge-pill';
            if (statusText) statusText.textContent = 'TV SERAMBI / LUAR MASJID';

            currentIndex = 0;
            isLoading = false;
            loadPage(currentIndex);
        }

        // Deteksi Otomatis Khutbah & Mode Sholat
        async function checkPrayerModeAPI() {
            if (FORCE_MIMBAR) {
                switchToMimbarLive('TEST MIMBAR');
                return;
            }

            try {
                const response = await fetch('/prayer-mode/status?_=' + Date.now(), {
                    cache: 'no-store',
                    headers: { 'Cache-Control': 'no-cache', 'Pragma': 'no-cache' }
                });
                const data = await response.json();
                
                // Khusus fase Khutbah Jum'at / Sholat Ied
                const isKhutbahPhase = data.active && (data.phase === 'khutbah' || (data.prayer && data.prayer.toLowerCase().includes('jumat')));

                if (isKhutbahPhase && cctvAutoSwitch) {
                    switchToMimbarLive(data.prayer || 'KHUTBAH');
                } else if (data.active) {
                    // Jika sholat reguler biasa (Subuh, Dzuhur, dll)
                    if (!isPrayerActive()) {
                        localStorage.setItem('lockPageRotationOutdoor', 'true');
                        if (countdownIntervalId) clearInterval(countdownIntervalId);
                        const frames = getFrames();
                        frames.next.className = 'standby';
                        frames.next.src = '/prayer-mode';
                    }
                } else {
                    if (isBroadcastingMimbar) {
                        switchBackToNormal();
                    } else if (isPrayerActive()) {
                        localStorage.setItem('lockPageRotationOutdoor', 'false');
                        currentIndex = 0;
                        isLoading = false;
                        loadPage(currentIndex);
                    }
                }
            } catch (error) {
                console.error('Prayer Mode Outdoor Check Failed:', error);
            }
        }

        function onFrameLoad(frameElement) {
            const frames = getFrames();
            
            if (!frames.current.src || frames.current.src === window.location.href) {
                clearTimeout(failsafeTimeout);
                document.getElementById('loadingOverlay').style.display = 'none';
                frameElement.className = 'active';
                currentFrameId = frameElement.id;
                isLoading = false;
                if (!isBroadcastingMimbar && rotationEnabled && activePages.length > 1) {
                    resetCountdown();
                }
                return;
            }

            if (frameElement === frames.next && frameElement.src && frameElement.src !== window.location.href) {
                clearTimeout(failsafeTimeout);
                
                setTimeout(() => {
                    requestAnimationFrame(() => {
                        requestAnimationFrame(() => {
                            frames.current.className = 'outgoing';
                            frames.next.className = 'active';
                            currentFrameId = frames.next.id;
                            isLoading = false;
                            if (!isBroadcastingMimbar && rotationEnabled && activePages.length > 1) {
                                resetCountdown();
                            }
                        });
                    });
                }, 80);
            }
        }

        document.getElementById('frame1').addEventListener('load', function() { onFrameLoad(this); });
        document.getElementById('frame2').addEventListener('load', function() { onFrameLoad(this); });

        setInterval(checkPrayerModeAPI, 3000);
        checkPrayerModeAPI();

        loadPage(0);
    </script>
</body>
</html>
