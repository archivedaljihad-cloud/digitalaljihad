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
    <link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400&family=Poppins:wght@300;400;500;600;700;800&family=Scheherazade+New:wght@600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}?v=3.0.4">

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

        /* Latar Belakang Ornamen Islami */
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

        .mihrab-frame {
            position: absolute;
            inset: 12px;
            border: 2px solid rgba(212, 175, 55, 0.4);
            border-radius: 24px;
            pointer-events: none;
            z-index: 2;
            box-shadow: inset 0 0 40px rgba(0, 0, 0, 0.8), 0 0 30px rgba(0, 0, 0, 0.9);
        }

        /* TOP HEADER BAR */
        .top-header-bar {
            position: relative;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 36px 12px 36px;
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
        }

        .mosque-info h1 {
            font-size: 1.25rem;
            font-weight: 700;
            color: #ffffff;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .mosque-info p {
            font-size: 0.72rem;
            color: var(--gold-light);
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .header-center {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .event-badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: linear-gradient(135deg, rgba(2, 25, 17, 0.9) 0%, rgba(4, 40, 26, 0.9) 100%);
            border: 1.5px solid rgba(255, 215, 0, 0.6);
            padding: 6px 20px;
            border-radius: 30px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.4);
        }

        .event-badge span {
            font-size: 1rem;
            font-weight: 700;
            color: #ffd700;
            letter-spacing: 1.2px;
        }

        .mode-indicator-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(212, 175, 55, 0.18);
            border: 1px solid rgba(212, 175, 55, 0.45);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            color: var(--gold-light);
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .isya-pill {
            display: flex;
            align-items: center;
            gap: 8px;
            background: rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(255, 215, 0, 0.35);
            padding: 5px 14px;
            border-radius: 20px;
            font-size: 0.85rem;
            color: #ffffff;
        }

        .isya-pill .time-val {
            color: #ffd700;
            font-weight: 700;
            font-family: monospace;
        }

        .isya-pill .countdown-val {
            background: rgba(220, 38, 38, 0.35);
            border: 1px solid rgba(248, 113, 113, 0.4);
            color: #fca5a5;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 0.80rem;
            font-weight: 700;
            font-family: monospace;
        }

        .live-clock {
            font-size: 1.4rem;
            font-weight: 700;
            color: #ffd700;
            font-family: monospace;
        }

        /* PROGRESS BAR */
        .progress-bar-wrapper {
            position: relative;
            z-index: 11;
            width: 100%;
            height: 4px;
            background: rgba(255, 255, 255, 0.08);
            overflow: hidden;
        }

        .progress-bar-fill {
            height: 100%;
            width: 0%;
            background: linear-gradient(90deg, #10b981 0%, #ffd700 70%, #fff 100%);
            box-shadow: 0 0 10px rgba(255, 215, 0, 0.8);
            transition: width 0.1s linear;
        }

        /* MAIN STAGE */
        .stage-container {
            flex: 1;
            position: relative;
            z-index: 5;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            padding: 16px 50px 70px 50px;
        }

        .view-pane {
            flex: 1;
            display: none;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            opacity: 0;
            transform: scale(0.98);
            transition: opacity 0.4s ease, transform 0.4s ease;
            width: 100%;
            max-width: 1380px;
            margin: 0 auto;
        }

        .view-pane.active {
            display: flex;
            opacity: 1;
            transform: scale(1);
        }

        /* OPSI 1: STEP-VIEW */
        .step-card {
            width: 100%;
            background: rgba(3, 28, 17, 0.82);
            border: 1.5px solid rgba(212, 175, 55, 0.5);
            border-radius: 28px;
            padding: 30px 48px;
            box-shadow: 0 16px 45px rgba(0, 0, 0, 0.65), inset 0 0 30px rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(8px);
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
        }

        .step-header-banner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
            padding-bottom: 14px;
            border-bottom: 1px solid rgba(212, 175, 55, 0.3);
        }

        .step-badge-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .step-num-tag {
            background: linear-gradient(135deg, #d4af37 0%, #9a7514 100%);
            color: #032314;
            font-size: 0.95rem;
            font-weight: 800;
            padding: 4px 16px;
            border-radius: 20px;
            letter-spacing: 1px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.4);
        }

        .step-range-info {
            font-size: 1.05rem;
            font-weight: 600;
            color: var(--gold-light);
            letter-spacing: 0.5px;
        }

        .step-meta-right {
            font-size: 0.85rem;
            color: #a7f3d0;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .step-bismillah-box {
            text-align: center;
            margin-bottom: 20px;
        }

        .step-bismillah-arabic {
            font-family: 'Amiri', serif;
            font-size: 2.3rem;
            color: #ffffff;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.8);
        }

        .step-ayats-flow {
            direction: rtl;
            text-align: justify;
            text-justify: kashida;
            line-height: 2.8;
            font-family: 'Amiri', 'Scheherazade New', serif;
            font-size: 2.45rem;
            color: #ffffff;
            text-shadow: 0 2px 6px rgba(0, 0, 0, 0.9);
        }

        /* OPSI 2: MUSHAF-VIEW */
        .mushaf-card {
            width: 100%;
            height: 100%;
            max-height: calc(100vh - 150px);
            background: radial-gradient(circle at 50% 50%, rgba(5, 36, 22, 0.92) 0%, rgba(2, 20, 12, 0.98) 100%);
            border: 2px solid rgba(212, 175, 55, 0.6);
            border-radius: 24px;
            padding: 24px 44px;
            box-shadow: 0 18px 50px rgba(0, 0, 0, 0.75), inset 0 0 50px rgba(0, 0, 0, 0.6);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
        }

        .mushaf-header-frame {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-bottom: 12px;
            border-bottom: 1.5px solid rgba(212, 175, 55, 0.4);
            margin-bottom: 14px;
        }

        .mushaf-juz-title {
            font-size: 0.95rem;
            color: var(--gold-light);
            font-weight: 600;
            letter-spacing: 1px;
        }

        .mushaf-surah-title {
            font-family: 'Amiri', serif;
            font-size: 1.8rem;
            font-weight: 700;
            color: #ffd700;
            line-height: 1;
        }

        .mushaf-page-number {
            font-size: 0.95rem;
            color: var(--gold-light);
            font-weight: 700;
            background: rgba(212, 175, 55, 0.15);
            border: 1px solid rgba(212, 175, 55, 0.4);
            padding: 3px 14px;
            border-radius: 14px;
        }

        .mushaf-bismillah-box {
            text-align: center;
            margin-bottom: 14px;
        }

        .mushaf-bismillah-arabic {
            font-family: 'Amiri', serif;
            font-size: 2.1rem;
            color: #ffffff;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.8);
        }

        .mushaf-page-flow {
            flex: 1;
            direction: rtl;
            text-align: justify;
            text-justify: kashida;
            line-height: 2.45;
            font-family: 'Amiri', 'Scheherazade New', serif;
            font-size: 2.05rem;
            color: #ffffff;
            text-shadow: 0 2px 5px rgba(0, 0, 0, 0.9);
            overflow-y: hidden;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        /* BADGE NOMOR AYAT */
        .ayah-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            margin: 0 6px;
            background: radial-gradient(circle, rgba(212, 175, 55, 0.3) 0%, rgba(8, 48, 28, 0.9) 100%);
            border: 1.5px solid var(--gold-primary);
            border-radius: 50%;
            font-family: 'Poppins', sans-serif;
            font-size: 0.80rem;
            font-weight: 700;
            color: #ffd700;
            vertical-align: middle;
            box-shadow: 0 0 10px rgba(212, 175, 55, 0.35);
        }

        .khatam-card {
            text-align: center;
            padding: 24px;
            background: rgba(2, 28, 16, 0.85);
            border: 1.5px solid rgba(212, 175, 55, 0.4);
            border-radius: 20px;
            margin-top: 16px;
        }

        .khatam-arabic {
            font-family: 'Amiri', serif;
            font-size: 1.8rem;
            color: #ffd700;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .khatam-sub {
            font-size: 0.88rem;
            color: #e2e8f0;
            line-height: 1.5;
        }

        /* FLOATING BAR */
        .floating-bar {
            position: fixed;
            bottom: 16px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 50;
            display: flex;
            align-items: center;
            gap: 8px;
            background: rgba(1, 18, 10, 0.92);
            border: 1.5px solid rgba(212, 175, 55, 0.45);
            border-radius: 35px;
            padding: 6px 16px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(12px);
            opacity: 0.4;
            transition: opacity 0.3s ease, transform 0.2s ease;
        }

        .floating-bar:hover {
            opacity: 1;
            transform: translateX(-50%) translateY(-2px);
        }

        .f-btn {
            background: transparent;
            border: none;
            color: var(--gold-light);
            font-size: 12px;
            padding: 6px 12px;
            border-radius: 20px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            font-weight: 600;
            transition: all 0.2s;
        }

        .f-btn:hover {
            background: rgba(212, 175, 55, 0.25);
            color: #ffffff;
        }

        .f-btn-switch {
            background: rgba(16, 185, 129, 0.2);
            border: 1px solid rgba(16, 185, 129, 0.5);
            color: #6ee7b7;
        }

        .f-btn-switch:hover {
            background: rgba(16, 185, 129, 0.35);
            color: #ffffff;
        }

        .f-indicator {
            font-size: 11px;
            padding: 4px 12px;
            border-radius: 14px;
            background: rgba(212, 175, 55, 0.15);
            border: 1px solid rgba(212, 175, 55, 0.3);
            color: #ffd700;
            font-weight: 700;
            font-family: monospace;
        }

        .f-divider {
            width: 1px;
            height: 18px;
            background: rgba(212, 175, 55, 0.25);
        }

        .pause-floating-badge {
            position: fixed;
            top: 76px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 60;
            background: rgba(0, 0, 0, 0.88);
            border: 1.5px solid var(--gold-primary);
            color: #fde047;
            padding: 6px 20px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: none;
            align-items: center;
            gap: 8px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.6);
        }

        .pause-floating-badge.show {
            display: flex;
        }
    </style>
</head>

<body>
    @php
        $rawSpeed = $settings->yasin_scroll_speed ?? 'medium';
        $initMode = 'step';
        $stepDur = 20;
        $mushafDur = 120;
        if (is_string($rawSpeed) && str_starts_with(trim($rawSpeed), '{')) {
            $parsed = json_decode($rawSpeed, true);
            if (!empty($parsed['mode'])) $initMode = $parsed['mode'];
            if (!empty($parsed['step_duration'])) $stepDur = (int)$parsed['step_duration'];
            if (!empty($parsed['mushaf_duration'])) $mushafDur = (int)$parsed['mushaf_duration'];
        } elseif ($rawSpeed === 'slow') {
            $stepDur = 28;
            $mushafDur = 150;
        } elseif ($rawSpeed === 'fast') {
            $stepDur = 14;
            $mushafDur = 90;
        }
    @endphp

    <div class="bg-pattern"></div>
    <div class="mihrab-frame"></div>

    <!-- PAUSE NOTIFICATION BADGE -->
    <div class="pause-floating-badge" id="pauseBadge">
        <i class="fa-solid fa-circle-pause"></i>
        <span>Tayangan Dijeda Sementara (Klik tombol Play atau spasi untuk lanjut)</span>
    </div>

    <!-- TOP HEADER BAR -->
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
            <div class="mode-indicator-pill" id="modeDisplayBadge">
                <i class="fa-solid fa-table-cells-large"></i>
                <span id="modeDisplayBadgeText">Opsi 1: Blok Ayat</span>
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

    <!-- PROGRESS BAR WAKTU BACA -->
    <div class="progress-bar-wrapper">
        <div class="progress-bar-fill" id="progressBarFill"></div>
    </div>

    <!-- MAIN STAGE -->
    <main class="stage-container" id="mainStage">
        <!-- OPSI 1 VIEW: STEP / BLOK AYAT TERFOKUS -->
        <div class="view-pane active" id="yasinStepView">
            <div class="step-card">
                <div class="step-header-banner">
                    <div class="step-badge-left">
                        <span class="step-num-tag" id="stepBadgeNum">BLOK 1 / 17</span>
                        <span class="step-range-info" id="stepBadgeRange">Ayat 1 – 5</span>
                    </div>
                    <div class="step-meta-right">
                        <span>Surat Yaasiin 83 Ayat</span>
                    </div>
                </div>

                <div class="step-bismillah-box" id="stepBismillahBox">
                    <div class="step-bismillah-arabic">بِسْمِ ٱللَّهِ ٱلرَّحْمَٰنِ ٱلرَّحِيمِ</div>
                </div>

                <div class="step-ayats-flow" id="stepAyatsFlow">
                    <!-- Teks Ayat Blok -->
                </div>
            </div>
        </div>

        <!-- OPSI 2 VIEW: LEMBARAN MUSHAF MADINAH (6 HALAMAN) -->
        <div class="view-pane" id="yasinMushafView">
            <div class="mushaf-card">
                <div class="mushaf-header-frame">
                    <div class="mushaf-juz-title" id="mushafJuzTitle">JUZ 22</div>
                    <div class="mushaf-surah-title">سُورَةُ يسٓ</div>
                    <div class="mushaf-page-number" id="mushafPageNumber">Halaman 440 (Hal 1 / 6)</div>
                </div>

                <div class="mushaf-bismillah-box" id="mushafBismillahBox">
                    <div class="mushaf-bismillah-arabic">بِسْمِ ٱللَّهِ ٱلرَّحْمَٰنِ ٱلرَّحِيمِ</div>
                </div>

                <div class="mushaf-page-flow" id="mushafAyatsFlow">
                    <!-- Teks Ayat Mushaf -->
                </div>
            </div>
        </div>
    </main>

    <!-- FLOATING OPERATOR BAR -->
    <div class="floating-bar">
        <button class="f-btn" id="btnPrev" title="Halaman / Blok Sebelumnya">
            <i class="fa-solid fa-chevron-left"></i>
            <span>Sebelumnya</span>
        </button>

        <button class="f-btn" id="btnPlayPause" title="Jeda / Lanjutkan Waktu">
            <i class="fa-solid fa-pause" id="iconPlayPause"></i>
            <span id="textPlayPause">Jeda</span>
        </button>

        <button class="f-btn" id="btnNext" title="Halaman / Blok Berikutnya">
            <span>Berikutnya</span>
            <i class="fa-solid fa-chevron-right"></i>
        </button>

        <div class="f-divider"></div>

        <span class="f-indicator" id="fNavIndicator">1 / 17 (20s)</span>

        <div class="f-divider"></div>

        <button class="f-btn f-btn-switch" id="btnQuickSwitch" title="Autoswitch Ganti Opsi Tampilan">
            <i class="fa-solid fa-repeat"></i>
            <span id="btnQuickSwitchText">Ganti ke Opsi 2 (Mushaf)</span>
        </button>
    </div>

    <script>
        /**
         * ENGINE SURAT YAASIIN 83 AYAT (2 OPSI AUTOSWITCH)
         */
        const STEP_BLOCKS = [
            { block: 1, start: 1, end: 5, bismillah: true },
            { block: 2, start: 6, end: 10, bismillah: false },
            { block: 3, start: 11, end: 15, bismillah: false },
            { block: 4, start: 16, end: 20, bismillah: false },
            { block: 5, start: 21, end: 25, bismillah: false },
            { block: 6, start: 26, end: 30, bismillah: false },
            { block: 7, start: 31, end: 35, bismillah: false },
            { block: 8, start: 36, end: 40, bismillah: false },
            { block: 9, start: 41, end: 45, bismillah: false },
            { block: 10, start: 46, end: 50, bismillah: false },
            { block: 11, start: 51, end: 54, bismillah: false },
            { block: 12, start: 55, end: 59, bismillah: false },
            { block: 13, start: 60, end: 64, bismillah: false },
            { block: 14, start: 65, end: 70, bismillah: false },
            { block: 15, start: 71, end: 75, bismillah: false },
            { block: 16, start: 76, end: 80, bismillah: false },
            { block: 17, start: 81, end: 83, bismillah: false, isKhatam: true }
        ];

        const MUSHAF_PAGES = [
            { page: 1, pageNum: 440, juz: 'JUZ 22', start: 1, end: 12, bismillah: true },
            { page: 2, pageNum: 441, juz: 'JUZ 22', start: 13, end: 27, bismillah: false },
            { page: 3, pageNum: 442, juz: 'JUZ 23', start: 28, end: 40, bismillah: false },
            { page: 4, pageNum: 443, juz: 'JUZ 23', start: 41, end: 54, bismillah: false },
            { page: 5, pageNum: 444, juz: 'JUZ 23', start: 55, end: 70, bismillah: false },
            { page: 6, pageNum: 445, juz: 'JUZ 23', start: 71, end: 83, bismillah: false, isKhatam: true }
        ];

        const allAyahs = @json($ayahs);
        let currentMode = @json($initMode) || 'step';
        let stepDuration = {{ (int)$stepDur }};
        let mushafDuration = {{ (int)$mushafDur }};

        // Fallback LocalStorage jika pernah disimpan di browser
        const localMode = localStorage.getItem('yasin_display_mode');
        if (localMode) currentMode = localMode;
        const localStepDur = parseInt(localStorage.getItem('yasin_step_duration'));
        if (localStepDur > 0) stepDuration = localStepDur;
        const localMushafDur = parseInt(localStorage.getItem('yasin_mushaf_duration'));
        if (localMushafDur > 0) mushafDuration = localMushafDur;

        let currentStepIndex = 0;
        let currentMushafIndex = 0;

        let isPaused = false;
        let timerSecondsRemaining = 0;
        let timerTotalSeconds = 20;
        let intervalTimer = null;

        const yasinStepView = document.getElementById('yasinStepView');
        const yasinMushafView = document.getElementById('yasinMushafView');
        const modeDisplayBadge = document.getElementById('modeDisplayBadge');
        const modeDisplayBadgeText = document.getElementById('modeDisplayBadgeText');
        const progressBarFill = document.getElementById('progressBarFill');
        const pauseBadge = document.getElementById('pauseBadge');

        const btnPrev = document.getElementById('btnPrev');
        const btnPlayPause = document.getElementById('btnPlayPause');
        const btnNext = document.getElementById('btnNext');
        const iconPlayPause = document.getElementById('iconPlayPause');
        const textPlayPause = document.getElementById('textPlayPause');
        const fNavIndicator = document.getElementById('fNavIndicator');
        const btnQuickSwitch = document.getElementById('btnQuickSwitch');
        const btnQuickSwitchText = document.getElementById('btnQuickSwitchText');

        function renderCurrentSlide() {
            if (!allAyahs || allAyahs.length === 0) return;

            if (currentMode === 'step') {
                yasinStepView.classList.add('active');
                yasinMushafView.classList.remove('active');

                modeDisplayBadgeText.textContent = 'Opsi 1: Blok Ayat';
                btnQuickSwitchText.textContent = 'Ganti ke Opsi 2 (Mushaf)';

                const blk = STEP_BLOCKS[currentStepIndex];
                document.getElementById('stepBadgeNum').textContent = `BLOK ${blk.block} / ${STEP_BLOCKS.length}`;
                document.getElementById('stepBadgeRange').textContent = `Ayat ${blk.start} – ${blk.end}`;
                document.getElementById('stepBismillahBox').style.display = blk.bismillah ? 'block' : 'none';

                const ayahsInBlock = allAyahs.filter(a => a.number >= blk.start && a.number <= blk.end);
                const flowEl = document.getElementById('stepAyatsFlow');

                let html = ayahsInBlock.map(a => `
                    <span class="ayah-item" id="step-a-${a.number}">
                        ${a.text}
                        <span class="ayah-badge" title="Ayat ${a.number}">${a.number}</span>
                    </span>
                `).join(' ');

                if (blk.isKhatam) {
                    html += `
                        <div class="khatam-card">
                            <div class="khatam-arabic">صَدَقَ اللهُ الْعَظِيْمُ وَبَلَّغَ رَسُوْلُهُ الْكَرِيْمُ</div>
                            <p class="khatam-sub">
                                Semoga Allah SWT menerima pahala bacaan Surah Yaasiin kita semua, mengampuni dosa para pendahulu & orang tua kita, serta melimpahkan ketenteraman bagi jamaah Masjid. Aamiin Yaa Rabbal 'Aalamiin.
                            </p>
                        </div>
                    `;
                }

                flowEl.innerHTML = html;
                timerTotalSeconds = stepDuration;

            } else {
                yasinStepView.classList.remove('active');
                yasinMushafView.classList.add('active');

                modeDisplayBadgeText.textContent = 'Opsi 2: Mushaf Madinah';
                btnQuickSwitchText.textContent = 'Ganti ke Opsi 1 (Blok)';

                const pge = MUSHAF_PAGES[currentMushafIndex];
                document.getElementById('mushafJuzTitle').textContent = pge.juz;
                document.getElementById('mushafPageNumber').textContent = `Halaman ${pge.pageNum} (Hal ${pge.page} / 6)`;
                document.getElementById('mushafBismillahBox').style.display = pge.bismillah ? 'block' : 'none';

                const ayahsInPage = allAyahs.filter(a => a.number >= pge.start && a.number <= pge.end);
                const flowEl = document.getElementById('mushafAyatsFlow');

                let html = ayahsInPage.map(a => `
                    <span class="ayah-item" id="mushaf-a-${a.number}">
                        ${a.text}
                        <span class="ayah-badge" title="Ayat ${a.number}">${a.number}</span>
                    </span>
                `).join(' ');

                if (pge.isKhatam) {
                    html += `
                        <div class="khatam-card">
                            <div class="khatam-arabic">صَدَقَ اللهُ الْعَظِيْمُ وَبَلَّغَ رَسُوْلُهُ الْكَرِيْمُ</div>
                            <p class="khatam-sub">
                                Semoga Allah SWT menerima pahala bacaan Surah Yaasiin kita semua, mengampuni dosa orang tua kita, serta melimpahkan rahmat bagi kita semua. Aamiin.
                            </p>
                        </div>
                    `;
                }

                flowEl.innerHTML = html;
                timerTotalSeconds = mushafDuration;
            }

            timerSecondsRemaining = timerTotalSeconds;
            updateNavIndicator();
            updateProgressBar();
        }

        function nextSlide() {
            if (currentMode === 'step') {
                currentStepIndex = (currentStepIndex + 1) % STEP_BLOCKS.length;
            } else {
                currentMushafIndex = (currentMushafIndex + 1) % MUSHAF_PAGES.length;
            }
            renderCurrentSlide();
        }

        function prevSlide() {
            if (currentMode === 'step') {
                currentStepIndex = (currentStepIndex - 1 + STEP_BLOCKS.length) % STEP_BLOCKS.length;
            } else {
                currentMushafIndex = (currentMushafIndex - 1 + MUSHAF_PAGES.length) % MUSHAF_PAGES.length;
            }
            renderCurrentSlide();
        }

        function togglePlayPause() {
            isPaused = !isPaused;
            if (isPaused) {
                iconPlayPause.className = 'fa-solid fa-play';
                textPlayPause.textContent = 'Lanjut';
                pauseBadge.classList.add('show');
            } else {
                iconPlayPause.className = 'fa-solid fa-pause';
                textPlayPause.textContent = 'Jeda';
                pauseBadge.classList.remove('show');
            }
        }

        function toggleModeQuick() {
            currentMode = (currentMode === 'step') ? 'mushaf' : 'step';
            localStorage.setItem('yasin_display_mode', currentMode);

            if (currentMode === 'mushaf') {
                const currentBlk = STEP_BLOCKS[currentStepIndex];
                const matchingPageIdx = MUSHAF_PAGES.findIndex(p => currentBlk.start >= p.start && currentBlk.start <= p.end);
                currentMushafIndex = (matchingPageIdx !== -1) ? matchingPageIdx : 0;
            } else {
                const currentPge = MUSHAF_PAGES[currentMushafIndex];
                const matchingStepIdx = STEP_BLOCKS.findIndex(b => currentPge.start >= b.start && currentPge.start <= b.end);
                currentStepIndex = (matchingStepIdx !== -1) ? matchingStepIdx : 0;
            }

            renderCurrentSlide();
        }

        function updateNavIndicator() {
            if (currentMode === 'step') {
                fNavIndicator.textContent = `Blok ${currentStepIndex + 1}/${STEP_BLOCKS.length} (${timerSecondsRemaining}s)`;
            } else {
                const mins = Math.floor(timerSecondsRemaining / 60);
                const secs = timerSecondsRemaining % 60;
                const timeFormatted = `${mins}:${String(secs).padStart(2, '0')}`;
                fNavIndicator.textContent = `Hal ${currentMushafIndex + 1}/6 (${timeFormatted})`;
            }
        }

        function updateProgressBar() {
            if (timerTotalSeconds <= 0) return;
            const elapsed = timerTotalSeconds - timerSecondsRemaining;
            const pct = Math.min(100, Math.max(0, (elapsed / timerTotalSeconds) * 100));
            progressBarFill.style.width = pct + '%';
        }

        function startTimerLoop() {
            if (intervalTimer) clearInterval(intervalTimer);
            intervalTimer = setInterval(() => {
                if (!isPaused) {
                    if (timerSecondsRemaining > 0) {
                        timerSecondsRemaining--;
                        updateNavIndicator();
                        updateProgressBar();
                    } else {
                        nextSlide();
                    }
                }
            }, 1000);
        }

        btnPrev.addEventListener('click', prevSlide);
        btnNext.addEventListener('click', nextSlide);
        btnPlayPause.addEventListener('click', togglePlayPause);
        btnQuickSwitch.addEventListener('click', toggleModeQuick);

        window.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowRight' || e.key === 'PageDown') {
                nextSlide();
            } else if (e.key === 'ArrowLeft' || e.key === 'PageUp') {
                prevSlide();
            } else if (e.key === ' ' || e.key === 'Enter') {
                togglePlayPause();
            } else if (e.key === 'm' || e.key === 'M') {
                toggleModeQuick();
            }
        });

        // Jam & Countdown Isya
        let isyaSecondsRemaining = {{ (int)($isyaRemainingSeconds ?? 0) }};

        function updateClockAndCountdown() {
            const now = new Date();
            const h = String(now.getHours()).padStart(2, '0');
            const m = String(now.getMinutes()).padStart(2, '0');
            const s = String(now.getSeconds()).padStart(2, '0');
            const elClock = document.getElementById('liveClockDisplay');
            if (elClock) elClock.textContent = `${h}:${m}:${s}`;

            const elCd = document.getElementById('isyaCountdownDisplay');
            if (isyaSecondsRemaining > 0) {
                isyaSecondsRemaining--;
                const mins = Math.floor(isyaSecondsRemaining / 60);
                const secs = isyaSecondsRemaining % 60;
                if (elCd) elCd.textContent = `${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
            } else if (elCd) {
                elCd.textContent = 'MASUK WAKTU ISYA';
            }
        }
        setInterval(updateClockAndCountdown, 1000);

        document.addEventListener('DOMContentLoaded', () => {
            renderCurrentSlide();
            startTimerLoop();
        });
    </script>
</body>

</html>
