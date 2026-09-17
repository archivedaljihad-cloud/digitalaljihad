<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda Malam Jum'at - Surat Yaasiin | {{ $settings->nama_aplikasi ?? "MASJID JAMI' AL JIHAD" }}</title>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400&family=Cinzel:wght@600;700;800;900&family=Poppins:wght@300;400;500;600;700;800&family=Scheherazade+New:wght@600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}?v={{ time() }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet" crossorigin="anonymous">

    <style>
        :root {
            --emerald-deep: #01140b;
            --emerald-dark: #032314;
            --emerald-mid: #084026;
            --emerald-accent: #0f5935;
            --gold-light: #FFF8E1;
            --gold-primary: #D4AF37;
            --gold-dark: #9A7514;
            --gold-glow: rgba(212, 175, 55, 0.4);
            --gold-gradient: linear-gradient(180deg, #FFFDF0 0%, #F5DC8C 28%, #D4AF37 58%, #9E7416 88%, #F7DE88 100%);
            --text-arabic: #FFFFFF;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            width: 100%;
            height: 100%;
            overflow: hidden;
            font-family: 'Poppins', sans-serif;
            background-color: var(--emerald-deep);
            color: #ffffff;
            user-select: none;
        }

        body {
            display: flex;
            flex-direction: column;
            position: relative;
            background: radial-gradient(circle at 50% 15%, #083c23 0%, #031e11 50%, #010f08 100%);
        }

        /* Latar Belakang Ornamen Islami Faint Pattern */
        .bg-pattern {
            position: absolute;
            inset: 0;
            background-image: 
                radial-gradient(rgba(212, 175, 55, 0.08) 1px, transparent 1px),
                radial-gradient(rgba(212, 175, 55, 0.05) 1px, transparent 1px);
            background-size: 40px 40px;
            background-position: 0 0, 20px 20px;
            pointer-events: none;
            z-index: 1;
            opacity: 0.8;
        }

        /* Bingkai Mihrab Luar */
        .mihrab-frame {
            position: absolute;
            inset: 12px;
            border: 2px solid rgba(212, 175, 55, 0.4);
            border-radius: 24px;
            pointer-events: none;
            z-index: 2;
            box-shadow: inset 0 0 40px rgba(0, 0, 0, 0.8), 0 0 30px rgba(0, 0, 0, 0.9);
        }

        .mihrab-frame::before {
            content: '';
            position: absolute;
            inset: 4px;
            border: 1px solid rgba(212, 175, 55, 0.2);
            border-radius: 20px;
        }

        /* =====================================================
           TOP FIXED HEADER BAR
           ===================================================== */
        .top-header-bar {
            position: relative;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 36px 12px 36px;
            background: linear-gradient(180deg, rgba(1, 18, 10, 0.95) 0%, rgba(2, 28, 16, 0.85) 80%, rgba(2, 28, 16, 0) 100%);
            border-bottom: 1px solid rgba(212, 175, 55, 0.25);
            backdrop-filter: blur(12px);
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .mosque-icon-badge {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: rgba(212, 175, 55, 0.15);
            border: 1px solid rgba(212, 175, 55, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--gold-primary);
            font-size: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4);
        }

        .mosque-info h1 {
            font-size: clamp(16px, 1.4vw, 22px);
            font-weight: 700;
            letter-spacing: 1px;
            color: #ffffff;
            text-transform: uppercase;
        }

        .mosque-info p {
            font-size: 11px;
            color: var(--gold-light);
            letter-spacing: 2px;
            text-transform: uppercase;
            opacity: 0.85;
        }

        /* Center Event Badge */
        .header-center {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .event-badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 8px 24px;
            background: linear-gradient(135deg, rgba(8, 48, 28, 0.9) 0%, rgba(3, 28, 15, 0.95) 100%);
            border: 1.5px solid var(--gold-primary);
            border-radius: 30px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.5), 0 0 15px var(--gold-glow);
        }

        .event-badge i {
            color: var(--gold-primary);
            font-size: 15px;
        }

        .event-badge span {
            font-family: 'Cinzel', serif;
            font-weight: 800;
            font-size: 13px;
            letter-spacing: 2.5px;
            color: var(--gold-light);
            text-transform: uppercase;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
        }

        /* Right Clock & Countdown */
        .header-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .isya-pill {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 7px 18px;
            background: rgba(0, 0, 0, 0.45);
            border: 1px solid rgba(212, 175, 55, 0.35);
            border-radius: 20px;
            font-size: 13px;
        }

        .isya-pill i {
            color: #ffd700;
            font-size: 14px;
        }

        .isya-pill .time-val {
            color: var(--gold-primary);
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .isya-pill .countdown-val {
            background: rgba(212, 175, 55, 0.2);
            color: #ffffff;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 1px;
            border: 1px solid rgba(212, 175, 55, 0.4);
        }

        .live-clock {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: clamp(18px, 1.6vw, 24px);
            color: #ffffff;
            letter-spacing: 1.5px;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.6);
        }

        /* =====================================================
           SCROLLABLE MUSHAF CONTAINER
           ===================================================== */
        .mushaf-scroll-wrapper {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            position: relative;
            z-index: 5;
            padding: 20px 48px 120px 48px;
            scroll-behavior: smooth;
        }

        /* Sembunyikan scrollbar bawaan agar layar TV tetap bersih */
        .mushaf-scroll-wrapper::-webkit-scrollbar {
            width: 6px;
        }

        .mushaf-scroll-wrapper::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.2);
        }

        .mushaf-scroll-wrapper::-webkit-scrollbar-thumb {
            background: rgba(212, 175, 55, 0.3);
            border-radius: 10px;
        }

        .mushaf-inner-content {
            max-width: 1400px;
            margin: 0 auto;
            position: relative;
        }

        /* Surah Header Banner */
        .surah-header-banner {
            text-align: center;
            margin: 20px auto 40px auto;
            padding: 24px 30px;
            background: linear-gradient(180deg, rgba(8, 48, 28, 0.85) 0%, rgba(3, 24, 13, 0.95) 100%);
            border: 2px solid rgba(212, 175, 55, 0.5);
            border-radius: 24px;
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.6), inset 0 1px 0 rgba(255, 240, 180, 0.2);
            position: relative;
            overflow: hidden;
        }

        .surah-title-arabic {
            font-family: 'Amiri', 'Scheherazade New', serif;
            font-size: clamp(48px, 5.5vw, 76px);
            font-weight: 700;
            background: var(--gold-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            line-height: 1.3;
            filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.7));
            margin-bottom: 6px;
        }

        .surah-subtitle-info {
            display: inline-flex;
            align-items: center;
            gap: 16px;
            font-size: clamp(12px, 1.1vw, 15px);
            color: var(--gold-light);
            letter-spacing: 2px;
            text-transform: uppercase;
            font-weight: 500;
            opacity: 0.9;
        }

        .surah-subtitle-info .bullet {
            color: var(--gold-primary);
        }

        /* Ornate Bismillah Header */
        .bismillah-box {
            text-align: center;
            margin: 30px auto 50px auto;
            position: relative;
            padding: 16px 20px;
        }

        .bismillah-arabic {
            font-family: 'Amiri', 'Scheherazade New', serif;
            font-size: clamp(38px, 4vw, 56px);
            font-weight: 700;
            color: #ffffff;
            text-shadow: 0 4px 14px rgba(0, 0, 0, 0.8), 0 0 20px rgba(212, 175, 55, 0.4);
            line-height: 1.6;
        }

        .bismillah-divider {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 16px;
            margin-top: 10px;
        }

        .divider-line {
            height: 1px;
            width: 140px;
            background: linear-gradient(90deg, transparent, rgba(212, 175, 55, 0.7), transparent);
        }

        .divider-gem {
            width: 10px;
            height: 10px;
            transform: rotate(45deg);
            background: var(--gold-primary);
            box-shadow: 0 0 10px var(--gold-glow);
        }

        /* =====================================================
           AYAT FLOW CONTAINER (Continuous Uthmani Arabic Text)
           ===================================================== */
        .ayat-flow-container {
            text-align: justify;
            text-justify: kashida;
            direction: rtl;
            line-height: 2.8;
            padding: 10px 20px;
        }

        .ayah-segment {
            display: inline;
            font-family: 'Amiri', 'Scheherazade New', serif;
            font-size: clamp(34px, 3.2vw, 50px);
            font-weight: 700;
            color: var(--text-arabic);
            text-shadow: 0 3px 8px rgba(0, 0, 0, 0.7);
            letter-spacing: 0.5px;
            transition: color 0.3s ease;
        }

        /* Ornamental Verse Number Badge */
        .ayah-number-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: clamp(40px, 3.4vw, 54px);
            height: clamp(40px, 3.4vw, 54px);
            margin: 0 10px 0 12px;
            vertical-align: middle;
            position: relative;
            background: radial-gradient(circle, rgba(212, 175, 55, 0.25) 0%, rgba(8, 48, 28, 0.6) 80%);
            border: 1.5px solid var(--gold-primary);
            border-radius: 50%;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5), inset 0 0 6px rgba(255, 235, 150, 0.3);
        }

        .ayah-number-text {
            font-family: 'Amiri', serif;
            font-size: clamp(16px, 1.4vw, 22px);
            font-weight: 700;
            color: var(--gold-light);
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.8);
            line-height: 1;
            direction: ltr;
        }

        /* Penutup / Doa Khatmil Quran Card di Akhir Ayat */
        .khatam-box {
            text-align: center;
            margin: 70px auto 40px auto;
            padding: 36px 30px;
            background: linear-gradient(180deg, rgba(8, 48, 28, 0.9) 0%, rgba(2, 20, 10, 0.98) 100%);
            border: 2px solid var(--gold-primary);
            border-radius: 24px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.7);
            max-width: 900px;
        }

        .khatam-arabic {
            font-family: 'Amiri', serif;
            font-size: clamp(28px, 2.6vw, 42px);
            color: var(--gold-light);
            font-weight: 700;
            margin-bottom: 12px;
            line-height: 1.8;
            direction: rtl;
        }

        .khatam-sub {
            font-size: 14px;
            color: #e0e0e0;
            letter-spacing: 1px;
            line-height: 1.6;
        }

        /* =====================================================
           FLOATING OPERATOR CONTROLS (Discreet on Bottom Right)
           ===================================================== */
        .floating-controls {
            position: fixed;
            bottom: 24px;
            right: 28px;
            z-index: 50;
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(1, 18, 10, 0.88);
            border: 1px solid rgba(212, 175, 55, 0.4);
            border-radius: 30px;
            padding: 6px 14px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(10px);
            opacity: 0.25;
            transition: opacity 0.3s ease;
        }

        .floating-controls:hover {
            opacity: 1;
        }

        .control-btn {
            background: transparent;
            border: none;
            color: var(--gold-light);
            font-size: 14px;
            padding: 6px 10px;
            border-radius: 20px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
        }

        .control-btn:hover {
            background: rgba(212, 175, 55, 0.2);
            color: #ffffff;
        }

        .speed-badge {
            font-size: 11px;
            padding: 2px 8px;
            border-radius: 10px;
            background: rgba(212, 175, 55, 0.2);
            border: 1px solid rgba(212, 175, 55, 0.3);
            color: var(--gold-primary);
            font-weight: 700;
        }

        /* Pause Notification Overlay */
        .pause-indicator {
            position: fixed;
            top: 90px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 60;
            background: rgba(0, 0, 0, 0.85);
            border: 1px solid var(--gold-primary);
            color: var(--gold-light);
            padding: 8px 24px;
            border-radius: 20px;
            font-size: 12px;
            letter-spacing: 1px;
            display: none;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.6);
        }

        .pause-indicator.show {
            display: flex;
            align-items: center;
            gap: 8px;
        }
    </style>
</head>

<body>
    <!-- Background Atmosphere -->
    <div class="bg-pattern"></div>
    <div class="mihrab-frame"></div>

    <!-- Pause Indicator Notification -->
    <div class="pause-indicator" id="pauseIndicator">
        <i class="fa-solid fa-circle-pause text-warning"></i>
        <span>Gulir Otomatis Dijeda (Sentuh layar atau klik tombol untuk melanjutkan)</span>
    </div>

    <!-- 1. TOP HEADER BAR -->
    <header class="top-header-bar">
        <div class="header-left">
            <div class="mosque-icon-badge">
                <i class="fa-solid fa-mosque"></i>
            </div>
            <div class="mosque-info">
                <h1>{{ $settings->nama_aplikasi ?? "MASJID JAMI' AL JIHAD" }}</h1>
                <p>Digital Signage System</p>
            </div>
        </div>

        <div class="header-center">
            <div class="event-badge">
                <i class="fa-solid fa-moon"></i>
                <span>AGENDA MALAM JUM'AT</span>
                <i class="fa-solid fa-star-and-crescent"></i>
            </div>
        </div>

        <div class="header-right">
            <div class="isya-pill">
                <i class="fa-regular fa-clock"></i>
                <span>Isya: <span class="time-val" id="isyaTimeDisplay">{{ $isyaTime }} WIB</span></span>
                <span class="countdown-val" id="isyaCountdownDisplay">--:--</span>
            </div>
            <div class="live-clock" id="liveClockDisplay">--:--:--</div>
        </div>
    </header>

    <!-- 2. SCROLLABLE MUSHAF CONTAINER -->
    <main class="mushaf-scroll-wrapper" id="yasinScrollContainer">
        <div class="mushaf-inner-content">

            <!-- Surah Header Banner -->
            <div class="surah-header-banner">
                <h2 class="surah-title-arabic">سُورَةُ يسٓ</h2>
                <div class="surah-subtitle-info">
                    <span>Surah ke-36</span>
                    <span class="bullet">•</span>
                    <span>83 Ayat</span>
                    <span class="bullet">•</span>
                    <span>Makkiyyah</span>
                </div>
            </div>

            <!-- Bismillah Header Box -->
            <div class="bismillah-box">
                <div class="bismillah-arabic">بِسْمِ ٱللَّهِ ٱلرَّحْمَٰنِ ٱلرَّحِيمِ</div>
                <div class="bismillah-divider">
                    <div class="divider-line"></div>
                    <div class="divider-gem"></div>
                    <div class="divider-line"></div>
                </div>
            </div>

            <!-- Ayat Flow Container (Continuous Uthmani Arabic Text) -->
            <div class="ayat-flow-container">
                @foreach ($ayahs as $ayah)
                    <span class="ayah-segment" id="ayah-{{ $ayah['number'] }}">
                        {{ $ayah['text'] }}
                        <span class="ayah-number-badge" title="Ayat {{ $ayah['number'] }}">
                            <span class="ayah-number-text">{{ $ayah['number'] }}</span>
                        </span>
                    </span>
                @endforeach
            </div>

            <!-- Penutup Khatam Yaasiin & Doa -->
            <div class="khatam-box">
                <div class="khatam-arabic">
                    صَدَقَ اللهُ الْعَظِيْمُ وَبَلَّغَ رَسُوْلُهُ الْكَرِيْمُ
                </div>
                <p class="khatam-sub">
                    Semoga Allah SWT menerima pahala bacaan Surah Yaasiin kita semua, mengampuni dosa para pendahulu & orang tua kita, serta melimpahkan ketenteraman dan keberkahan bagi jamaah Masjid. Aamiin Yaa Rabbal 'Aalamiin.
                </p>
            </div>

        </div>
    </main>

    <!-- 3. FLOATING OPERATOR CONTROLS -->
    <div class="floating-controls">
        <button class="control-btn" id="btnToggleScroll" title="Jeda / Lanjutkan Gulir">
            <i class="fa-solid fa-pause" id="iconToggleScroll"></i>
            <span id="textToggleScroll">Jeda</span>
        </button>
        <button class="control-btn" id="btnSpeedToggle" title="Ganti Kecepatan Gulir">
            <i class="fa-solid fa-gauge-high"></i>
            <span class="speed-badge" id="speedBadgeDisplay">{{ strtoupper($scrollSpeed ?? 'MEDIUM') }}</span>
        </button>
        <button class="control-btn" id="btnRestartScroll" title="Ulangi dari Awal">
            <i class="fa-solid fa-rotate-left"></i>
            <span>Awal</span>
        </button>
    </div>

    <!-- =====================================================
         JAVASCRIPT ENGINES (Clock, Countdown & Smooth Auto-Scroll)
         ===================================================== -->
    <script>
        // Konfigurasi Kecepatan Gulir (pixel per animation frame ~ 60fps)
        const SPEED_MAP = {
            'slow': 0.38,
            'medium': 0.68,
            'fast': 1.15
        };

        let currentSpeedKey = '{{ $scrollSpeed ?? "medium" }}'.toLowerCase();
        if (!SPEED_MAP[currentSpeedKey]) currentSpeedKey = 'medium';

        let scrollSpeed = SPEED_MAP[currentSpeedKey];
        let isPaused = false;
        let isUserInteracting = false;
        let interactionTimeout = null;

        const scrollContainer = document.getElementById('yasinScrollContainer');
        const pauseIndicator = document.getElementById('pauseIndicator');
        const btnToggleScroll = document.getElementById('btnToggleScroll');
        const iconToggleScroll = document.getElementById('iconToggleScroll');
        const textToggleScroll = document.getElementById('textToggleScroll');
        const btnSpeedToggle = document.getElementById('btnSpeedToggle');
        const speedBadgeDisplay = document.getElementById('speedBadgeDisplay');
        const btnRestartScroll = document.getElementById('btnRestartScroll');

        // State akumulator sub-pixel scroll
        let scrollPos = 0;

        function smoothScrollLoop() {
            if (!isPaused && !isUserInteracting && scrollContainer) {
                const maxScroll = scrollContainer.scrollHeight - scrollContainer.clientHeight;
                if (maxScroll > 0) {
                    scrollPos += scrollSpeed;
                    if (scrollPos >= maxScroll) {
                        // Jika sudah mencapai akhir, tunggu 10 detik lalu perlahan kembali ke atas
                        scrollPos = maxScroll;
                        scrollContainer.scrollTop = maxScroll;
                        isPaused = true;
                        setTimeout(() => {
                            scrollContainer.scrollTo({ top: 0, behavior: 'smooth' });
                            setTimeout(() => {
                                scrollPos = 0;
                                isPaused = false;
                            }, 3000);
                        }, 10000);
                    } else {
                        scrollContainer.scrollTop = Math.floor(scrollPos);
                    }
                }
            }
            requestAnimationFrame(smoothScrollLoop);
        }

        // Jalankan loop scroll
        requestAnimationFrame(smoothScrollLoop);

        // Kontrol Manual: Jeda / Lanjutkan
        btnToggleScroll.addEventListener('click', () => {
            isPaused = !isPaused;
            updatePauseUI();
        });

        function updatePauseUI() {
            if (isPaused) {
                iconToggleScroll.className = 'fa-solid fa-play';
                textToggleScroll.textContent = 'Mulai';
                pauseIndicator.classList.add('show');
            } else {
                iconToggleScroll.className = 'fa-solid fa-pause';
                textToggleScroll.textContent = 'Jeda';
                pauseIndicator.classList.remove('show');
                scrollPos = scrollContainer.scrollTop; // Sinkronkan posisi riil
            }
        }

        // Kontrol Manual: Ganti Kecepatan
        btnSpeedToggle.addEventListener('click', () => {
            const keys = ['slow', 'medium', 'fast'];
            let nextIndex = (keys.indexOf(currentSpeedKey) + 1) % keys.length;
            currentSpeedKey = keys[nextIndex];
            scrollSpeed = SPEED_MAP[currentSpeedKey];
            speedBadgeDisplay.textContent = currentSpeedKey.toUpperCase();
        });

        // Kontrol Manual: Ulangi dari Awal
        btnRestartScroll.addEventListener('click', () => {
            scrollContainer.scrollTo({ top: 0, behavior: 'smooth' });
            setTimeout(() => {
                scrollPos = 0;
            }, 800);
        });

        // Jeda sementara saat mouse wheel / touch scroll dilakukan oleh operator
        scrollContainer.addEventListener('wheel', () => {
            isUserInteracting = true;
            clearTimeout(interactionTimeout);
            interactionTimeout = setTimeout(() => {
                isUserInteracting = false;
                scrollPos = scrollContainer.scrollTop;
            }, 3500);
        }, { passive: true });

        scrollContainer.addEventListener('touchstart', () => {
            isUserInteracting = true;
            clearTimeout(interactionTimeout);
        }, { passive: true });

        scrollContainer.addEventListener('touchend', () => {
            clearTimeout(interactionTimeout);
            interactionTimeout = setTimeout(() => {
                isUserInteracting = false;
                scrollPos = scrollContainer.scrollTop;
            }, 3500);
        }, { passive: true });

        // =====================================================
        // JAM DIGITAL & HITUNG MUNDUR ISYA
        // =====================================================
        const elClock = document.getElementById('liveClockDisplay');
        const elCountdown = document.getElementById('isyaCountdownDisplay');
        let initialRemainingSeconds = {{ $isyaRemainingSeconds ?? 0 }};
        let countdownTimer = initialRemainingSeconds;

        function updateClockAndCountdown() {
            const now = new Date();
            const h = String(now.getHours()).padStart(2, '0');
            const m = String(now.getMinutes()).padStart(2, '0');
            const s = String(now.getSeconds()).padStart(2, '0');
            if (elClock) elClock.textContent = `${h}:${m}:${s}`;

            if (countdownTimer > 0) {
                countdownTimer--;
                const mins = Math.floor(countdownTimer / 60);
                const secs = countdownTimer % 60;
                if (elCountdown) {
                    elCountdown.textContent = `${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
                }
            } else {
                if (elCountdown) elCountdown.textContent = 'WAKTU ISYA';
            }
        }
        setInterval(updateClockAndCountdown, 1000);
        updateClockAndCountdown();

        // =====================================================
        // SAFETY LOCK: POLLING STATUS PRAYER MODE ISYA
        // =====================================================
        // Begitu waktu Adzan Isya tiba (atau masa countdown Isya dimulai),
        // TV langsung mengalah dan beralih ke Mode Sholat Isya!
        async function checkPrayerModeSafety() {
            try {
                const res = await fetch('/prayer-mode/status?_=' + Date.now());
                const data = await res.json();
                
                // Jika Prayer Mode Sholat aktif (Countdown/Adzan/Iqamah/Sholat Isya)
                if (data.active) {
                    window.location.href = '/prayer-mode';
                }
            } catch (err) {
                // Abaikan kesalahan koneksi sementara
            }
        }
        setInterval(checkPrayerModeSafety, 3000);
    </script>
</body>

</html>
