<!-- resources/views/rotator.blade.php -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Informasi Masjid - Rotating Display</title>

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
        }

        iframe {
            width: 100%;
            height: 100%;
            border: none;
            transition: opacity 0.5s ease-in-out;
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

        /*
         * Page Indicator (nama halaman + countdown)
         * Default: TERSEMBUNYI (mode Production).
         * Hanya dimunculkan oleh JavaScript jika DEBUG_MODE aktif (?debug=1).
         */
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

        .page-indicator.debug-visible {
            display: flex;
        }

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

        .page-indicator button {
            background: rgba(255, 215, 0, 0.3);
            border: none;
            color: white;
            padding: 4px 10px;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .page-indicator button:hover {
            background: rgba(255, 215, 0, 0.6);
            transform: scale(1.05);
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

    <div class="iframe-container">
        <iframe id="contentFrame" src="" title="Rotating Content"></iframe>
    </div>

    <script>
        // ==========================================================
        // DEBUG MODE
        // Dideklarasikan paling awal agar tidak pernah terjadi
        // ReferenceError / "DEBUG_MODE is not defined".
        // Aktif jika URL dibuka dengan ?debug=1
        // ==========================================================
        const DEBUG_MODE =
            new URLSearchParams(window.location.search)
                .get('debug') === '1';

        // Helper Parser: Mendeteksi nilai true/false dengan sangat aman dari MySQL
        const parseBool = (val) => val === true || val === 1 || val === "1" || String(val).toLowerCase() === "true";

        // Data awal dari server Blade
        let rotationInterval = parseInt({{ $rotationInterval ?? 10 }});
        if (isNaN(rotationInterval) || rotationInterval < 1) rotationInterval = 10;
        let rotationEnabled = parseBool("{{ $rotationEnabled ?? true }}");

        // Parsing JSON Data Pages yang tahan terhadap Double Stringify Error
        let rawPages = @json($rotationPages ?? []);
        let pages = [];

        try {
            if (typeof rawPages === 'string') {
                pages = JSON.parse(rawPages);
                if (typeof pages === 'string') {
                    pages = JSON.parse(pages);
                }
            } else {
                pages = rawPages;
            }
        } catch (e) {
            console.error('Gagal membaca data halaman awal:', e);
        }

        if (!Array.isArray(pages)) pages = [];

        // FIX UTAMA: Gunakan parseBool untuk mengatasi masalah tipe data
        let activePages = pages.filter(page => parseBool(page.active));

        // Fallback default jika data dari DB kosong atau tidak ada yang aktif
        if (activePages.length === 0) {
            activePages = [
                { url: '/welcome-embed', name: 'Dashboard Lengkap', active: true }
            ];
        }

        let currentIndex = 0;
        let countdown = rotationInterval;
        let countdownIntervalId = null;
        let isLoading = false;
        let failsafeTimeout = null;

        // ==========================================================
        // Terapkan Mode Debug/Production ke tampilan Page Indicator
        // ==========================================================
        function applyDebugVisibility() {
            const pageIndicator = document.getElementById('pageIndicator');
            if (!pageIndicator) return;

            if (DEBUG_MODE) {
                pageIndicator.classList.add('debug-visible');
            } else {
                pageIndicator.classList.remove('debug-visible');
            }
        }

        // Fungsi memuat halaman ke dalam Iframe
        function loadPage(index) {
            if (isLoading) return;
            isLoading = true;

            const iframe = document.getElementById('contentFrame');
            const loadingOverlay = document.getElementById('loadingOverlay');
            const pageName = document.getElementById('pageName');

            loadingOverlay.style.display = 'flex';

            if (activePages[index] && activePages[index].name) {
                pageName.textContent = activePages[index].name;
            }

            if (activePages[index] && activePages[index].url) {
                let routeUrl = activePages[index].url;
                if (!routeUrl.startsWith('/') && !routeUrl.startsWith('http')) {
                    routeUrl = '/' + routeUrl;
                }
                iframe.src = routeUrl;
            }

            // Failsafe Timeout: Membuka kunci jika iframe gagal load (mati di tengah jalan)
            clearTimeout(failsafeTimeout);
            failsafeTimeout = setTimeout(() => {
                if (isLoading) {
                    console.warn('Iframe load failsafe triggered. Membuka kunci loading.');
                    loadingOverlay.style.display = 'none';
                    isLoading = false;
                    resetCountdown();
                }
            }, 6000); // Batas maksimal loading 6 detik

            resetCountdown(); // Secara visual mereset teks countdown
        }

        // Reset dan mulai ulang timer countdown
        function resetCountdown() {
            if (countdownIntervalId) {
                clearInterval(countdownIntervalId);
            }
            countdown = rotationInterval;
            updateCountdownDisplay();

            // Timer hanya akan berjalan jika halaman lebih dari 1 dan rotasi diaktifkan
            if (rotationEnabled && activePages.length > 1) {
                countdownIntervalId = setInterval(() => {
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

        // Update tampilan indikator timer di layar
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

        // Halaman berikutnya
        function nextPage() {
            if (isLoading || !rotationEnabled || activePages.length <= 1) return;
            currentIndex = (currentIndex + 1) % activePages.length;
            loadPage(currentIndex);
        }

        function skipToNext() {
            if (isLoading || !rotationEnabled || activePages.length <= 1) return;
            nextPage();
        }

        function openSettings() {
            window.open('/settings', '_blank'); // Diubah ke /settings agar lebih generik
        }

        // Tampilkan notifikasi
        function showNotification(message) {
            const notification = document.getElementById('notification');
            notification.innerHTML = `<i class="fas fa-sync-alt fa-spin"></i> ${message}`;
            notification.classList.add('show');
            setTimeout(() => {
                notification.classList.remove('show');
            }, 3500);
        }

        // Pengecekan Mode Sholat (Layar Penuh API)
        async function checkPrayerMode() {
            try {
                const response = await fetch('/prayer-mode/status?_=' + Date.now());
                const data = await response.json();

                if (data.active) {
                    window.location.href = '/prayer-mode'; // Hard redirect ke controller sholat
                }
            } catch (error) {
                console.error('Prayer Mode Check Failed:', error);
            }
        }

        // Polling pengaturan rotasi terbaru
        async function fetchLatestSettings() {
            try {
                const response = await fetch('/rotation-settings?_=' + Date.now());
                const data = await response.json();
                let hasChanges = false;

                // Cek Perubahan Interval
                let apiIntervalRaw = data.interval !== undefined ? data.interval : data.rotation_interval;
                if (apiIntervalRaw !== undefined) {
                    let apiInterval = parseInt(apiIntervalRaw);
                    if (!isNaN(apiInterval) && apiInterval >= 1 && apiInterval !== rotationInterval) {
                        rotationInterval = apiInterval;
                        hasChanges = true;
                        showNotification(`Interval berubah menjadi ${rotationInterval} detik`);
                    }
                }

                // Cek Perubahan Status Rotasi
                let apiEnabledRaw = data.enabled !== undefined ? data.enabled : data.rotation_enabled;
                if (apiEnabledRaw !== undefined) {
                    let apiEnabled = parseBool(apiEnabledRaw);
                    if (apiEnabled !== rotationEnabled) {
                        rotationEnabled = apiEnabled;
                        hasChanges = true;
                        showNotification(rotationEnabled ? 'Rotasi diaktifkan' : 'Rotasi dinonaktifkan');
                    }
                }

                // Cek Perubahan Daftar Halaman
                let apiPagesRaw = data.pages !== undefined ? data.pages : data.rotation_pages;
                if (apiPagesRaw !== undefined) {
                    let newPages = [];
                    if (typeof apiPagesRaw === 'string') {
                        try {
                            newPages = JSON.parse(apiPagesRaw);
                            if (typeof newPages === 'string') {
                                newPages = JSON.parse(newPages);
                            }
                        } catch (e) {
                            console.error('Parse JSON Halaman API Error:', e);
                        }
                    } else {
                        newPages = apiPagesRaw;
                    }

                    if (!Array.isArray(newPages)) newPages = [];

                    // Filter menggunakan parseBool yang diperbaiki
                    let newActivePages = newPages.filter(page => parseBool(page.active));

                    if (newActivePages.length === 0) {
                        newActivePages = [{ url: '/welcome-embed', name: 'Dashboard Lengkap', active: true }];
                    }

                    const oldActiveUrls = activePages.map(p => p.url).sort().join(',');
                    const newActiveUrls = newActivePages.map(p => p.url).sort().join(',');

                    if (oldActiveUrls !== newActiveUrls) {
                        pages = newPages;
                        activePages = newActivePages;
                        hasChanges = true;
                        if (currentIndex >= activePages.length) {
                            currentIndex = 0;
                        }
                        showNotification(`Halaman yang ditampilkan diperbarui`);
                    }
                }

                // Eksekusi pembaruan jika ada perubahan
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

        // Event listener saat Iframe selesai memuat konten
        document.getElementById('contentFrame').addEventListener('load', function () {
            clearTimeout(failsafeTimeout); // Hapus failsafe timer karena sudah sukses
            setTimeout(() => {
                const loadingOverlay = document.getElementById('loadingOverlay');
                loadingOverlay.style.display = 'none';
                isLoading = false;

                if (rotationEnabled && activePages.length > 1) {
                    resetCountdown();
                }
            }, 500); // Efek transisi halus 0.5s
        });

        // Error Handling Iframe
        document.getElementById('contentFrame').addEventListener('error', function () {
            console.error('Iframe Error Event Terpicu');
            clearTimeout(failsafeTimeout);
            isLoading = false;
            document.getElementById('loadingOverlay').style.display = 'none';

            setTimeout(() => {
                if (activePages.length > 0) {
                    loadPage(currentIndex);
                }
            }, 3000);
        });

        // Shortcut Keyboard Navigasi
        document.addEventListener('keydown', function (e) {
            if (e.key === 'ArrowRight') {
                nextPage();
            } else if (e.key === ' ' || e.key === 'Space') {
                e.preventDefault();
                skipToNext();
            } else if (e.key === 's' || e.key === 'S') {
                openSettings();
            }
        });

        // Terapkan mode debug/production ke Page Indicator sekarang
        applyDebugVisibility();

        // Daftarkan Task Polling Asynchronous
        setInterval(fetchLatestSettings, 5000);
        setInterval(checkPrayerMode, 5000);

        // Eksekusi Pengecekan Pertama Kali
        checkPrayerMode();

        // Debug Log Console
        console.log('Rotator Initialized. Active Pages:', activePages.length, '| DEBUG_MODE:', DEBUG_MODE);

        // Mulai Rotasi Sistem
        if (activePages.length > 0) {
            loadPage(0);
        } else {
            document.getElementById('pageName').textContent = 'Tidak ada halaman (Sistem Error)';
        }
    </script>

</body>
</html>
