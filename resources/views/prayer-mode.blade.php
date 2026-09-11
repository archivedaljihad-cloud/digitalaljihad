<!-- resources/views/prayer-mode.blade.php -->
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mode Sholat</title>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400&family=Cinzel:wght@600;700;800;900&family=Poppins:wght@300;400;500;600;700;800&family=Scheherazade+New:wght@600;700&display=swap"
        rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --gold-light: #FFF8E1;
            --gold-primary: #D4AF37;
            --gold-dark: #9A7514;
            --gold-glow: rgba(212, 175, 55, 0.35);
            --gold-gradient: linear-gradient(180deg, #FFFDF0 0%, #F5DC8C 28%, #D4AF37 58%, #9E7416 88%, #F7DE88 100%);
            --gold-border: rgba(212, 175, 55, 0.45);
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
            background-color: #01130a;
            color: #ffffff;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }

        /* =====================================================
           BACKGROUND ATMOSPHERE
           ===================================================== */
        @keyframes emeraldMotion {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        @keyframes pulseGlow {
            0%, 100% {
                opacity: 0.6;
                transform: scale(1);
            }
            50% {
                opacity: 0.95;
                transform: scale(1.02);
            }
        }

        @keyframes floatingDust {
            0% {
                transform: translateY(0px) rotate(0deg);
                opacity: 0.2;
            }
            50% {
                transform: translateY(-20px) rotate(180deg);
                opacity: 0.6;
            }
            100% {
                transform: translateY(0px) rotate(360deg);
                opacity: 0.2;
            }
        }

        .bg-layer {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
        }

        /* Gradient Dasar */
        .bg-gradient {
            @if($theme == 'dark')
                background: radial-gradient(circle at 50% 20%, #16181d 0%, #0c0d10 50%, #000000 100%);
            @elseif($theme == 'light')
                background: radial-gradient(circle at 50% 20%, #f7f9fc 0%, #e8edf3 50%, #d5dde6 100%);
            @else
                /* Royal Emerald & Obsidian */
                background: radial-gradient(circle at 50% 15%, #064024 0%, #032716 42%, #01150c 80%, #000c06 100%);
            @endif
        }

        /* Islamic Geometric Pattern */
        .bg-pattern {
            background-image: url('{{ asset("storage/background/islamic.png") }}');
            background-repeat: repeat;
            background-position: center;
            background-size: 260px;
            opacity: 0.08;
            mix-blend-mode: overlay;
        }

        /* Ka'bah Watermark Vignette */
        .bg-kaabah {
            background-image: url('{{ asset("img/Kaabah.png") }}');
            background-size: contain;
            background-position: center 85%;
            background-repeat: no-repeat;
            opacity: 0.22;
            mix-blend-mode: luminosity;
            filter: contrast(1.15) brightness(0.9);
            mask-image: radial-gradient(circle at center 60%, rgba(0, 0, 0, 1) 30%, rgba(0, 0, 0, 0) 75%);
            -webkit-mask-image: radial-gradient(circle at center 60%, rgba(0, 0, 0, 1) 30%, rgba(0, 0, 0, 0) 75%);
        }

        /* Dark Radial Vignette */
        .bg-vignette {
            background: radial-gradient(ellipse at center, rgba(1, 19, 10, 0.15) 0%, rgba(1, 12, 7, 0.75) 75%, rgba(0, 5, 2, 0.95) 100%);
        }

        /* Ambient Sparkles / Light Orbs */
        .ambient-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(45px);
            pointer-events: none;
        }

        .ambient-orb-1 {
            top: 5%;
            left: 20%;
            width: 380px;
            height: 250px;
            background: radial-gradient(circle, rgba(212, 175, 55, 0.15), transparent 70%);
            animation: floatingDust 18s ease-in-out infinite;
        }

        .ambient-orb-2 {
            bottom: 10%;
            right: 20%;
            width: 420px;
            height: 280px;
            background: radial-gradient(circle, rgba(10, 92, 53, 0.35), transparent 70%);
            animation: floatingDust 22s ease-in-out infinite reverse;
        }

        /* =====================================================
           THE ROYAL MIHRAB CARD CONTAINER
           ===================================================== */
        .container {
            position: relative;
            z-index: 5;
            width: 100%;
            max-width: 1400px;
            height: 94vh;
            max-height: 980px;
            margin: 0 auto;
            padding: 24px 38px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            text-align: center;

            /* Luxury Glassmorphism */
            @if($theme == 'dark')
                background: radial-gradient(120% 120% at 50% 0%, rgba(25, 27, 34, 0.78) 0%, rgba(12, 13, 17, 0.92) 100%);
            @elseif($theme == 'light')
                background: radial-gradient(120% 120% at 50% 0%, rgba(255, 255, 255, 0.85) 0%, rgba(240, 244, 248, 0.94) 100%);
                color: #222222;
            @else
                background: radial-gradient(120% 120% at 50% 0%, rgba(5, 45, 25, 0.75) 0%, rgba(2, 20, 11, 0.89) 100%);
            @endif

            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border-radius: 40px;
            border: 1.5px solid var(--gold-border);
            box-shadow:
                0 30px 90px rgba(0, 0, 0, 0.75),
                0 0 50px rgba(212, 175, 55, 0.12),
                inset 0 1px 0 rgba(255, 240, 185, 0.35);
        }

        /* Ornamen Sudut Arabesque */
        .corner-bracket {
            position: absolute;
            width: 32px;
            height: 32px;
            border-color: #D4AF37;
            border-style: solid;
            pointer-events: none;
            opacity: 0.85;
        }

        .corner-top-left {
            top: 14px;
            left: 14px;
            border-width: 2px 0 0 2px;
            border-top-left-radius: 20px;
        }

        .corner-top-right {
            top: 14px;
            right: 14px;
            border-width: 2px 2px 0 0;
            border-top-right-radius: 20px;
        }

        .corner-bottom-left {
            bottom: 14px;
            left: 14px;
            border-width: 0 0 2px 2px;
            border-bottom-left-radius: 20px;
        }

        .corner-bottom-right {
            bottom: 14px;
            right: 14px;
            border-width: 0 2px 2px 0;
            border-bottom-right-radius: 20px;
        }

        /* Ornamen Mihrab Arch Outline di Atas Card */
        .mihrab-arch-top {
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 340px;
            height: 6px;
            background: linear-gradient(90deg, transparent 0%, #D4AF37 50%, transparent 100%);
            box-shadow: 0 0 15px #D4AF37;
            border-radius: 0 0 10px 10px;
        }

        /* =====================================================
           BAGIAN 1: KALIGRAFI ARAB EMAS
           ===================================================== */
        .arabic-section {
            width: 100%;
            padding-top: 6px;
        }

        .arabic-text {
            font-family: 'Amiri', 'Scheherazade New', serif;
            font-size: clamp(46px, 4.4vw, 70px);
            font-weight: 700;
            line-height: 1.25;
            background: var(--gold-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            filter: drop-shadow(0 4px 16px rgba(212, 175, 55, 0.45));
            letter-spacing: 2px;
            margin-bottom: 4px;
        }

        .gold-divider {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 16px;
            max-width: 550px;
            margin: 0 auto;
            opacity: 0.85;
        }

        .gold-divider .divider-line {
            flex: 1;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(212, 175, 55, 0.8), transparent);
        }

        .gold-divider .divider-star {
            color: #D4AF37;
            font-size: 15px;
            letter-spacing: 6px;
            filter: drop-shadow(0 0 8px rgba(212, 175, 55, 0.5));
        }

        /* =====================================================
           BAGIAN 2: PHASE TITLE & NAMA SHOLAT BADGE
           ===================================================== */
        .phase-section {
            margin-top: 4px;
            margin-bottom: 2px;
        }

        .phase-title {
            font-family: 'Poppins', sans-serif;
            font-size: clamp(20px, 2vw, 30px);
            font-weight: 700;
            letter-spacing: 6px;
            text-transform: uppercase;
            @if($theme == 'light')
                color: #1a202c;
            @else
                color: #ffffff;
                text-shadow: 0 2px 12px rgba(0, 0, 0, 0.6);
            @endif
            margin-bottom: 12px;
        }

        .prayer-badge-wrapper {
            display: inline-block;
        }

        .prayer-badge {
            display: inline-flex;
            align-items: center;
            gap: 18px;
            padding: 8px 36px;
            border-radius: 50px;
            border: 1.5px solid rgba(212, 175, 55, 0.65);
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.18) 0%, rgba(4, 40, 22, 0.7) 100%);
            box-shadow:
                0 0 24px rgba(212, 175, 55, 0.22),
                inset 0 1px 0 rgba(255, 238, 170, 0.4);
            backdrop-filter: blur(10px);
        }

        .badge-gem {
            width: 8px;
            height: 8px;
            background: #D4AF37;
            border-radius: 50%;
            box-shadow: 0 0 10px #D4AF37;
            animation: pulseGlow 2.5s infinite;
        }

        .prayer-name {
            font-family: 'Cinzel', serif;
            font-size: clamp(26px, 2.8vw, 42px);
            font-weight: 800;
            letter-spacing: 7px;
            background: linear-gradient(180deg, #FFFFFF 0%, #F5DE94 45%, #D4AF37 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            filter: drop-shadow(0 2px 10px rgba(212, 175, 55, 0.3));
            text-transform: uppercase;
        }

        /* =====================================================
           BAGIAN 3: INTERACTIVE BADGE & SPLIT DUAL-TILE COUNTDOWN
           ===================================================== */
        .countdown-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin: 4px 0;
        }

        /* MENUJU ADZAN INTERACTIVE BADGE */
        @keyframes timerBadgePulse {
            0%, 100% {
                border-color: rgba(212, 175, 55, 0.55);
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5), 0 0 18px rgba(212, 175, 55, 0.28), inset 0 1px 0 rgba(255, 235, 170, 0.4);
                transform: scale(1);
            }
            50% {
                border-color: rgba(255, 228, 130, 0.95);
                box-shadow: 0 6px 28px rgba(0, 0, 0, 0.6), 0 0 35px rgba(212, 175, 55, 0.65), 0 0 15px rgba(255, 215, 0, 0.45), inset 0 1px 0 rgba(255, 255, 255, 0.8);
                transform: scale(1.035);
            }
        }

        @keyframes lightSweep {
            0% {
                transform: translateX(-150%) skewX(-22deg);
            }
            35%, 100% {
                transform: translateX(250%) skewX(-22deg);
            }
        }

        @keyframes clockTick {
            0%, 100% {
                transform: rotate(0deg) scale(1);
                color: #D4AF37;
            }
            20% {
                transform: rotate(14deg) scale(1.25);
                color: #FFF2A8;
            }
            40% {
                transform: rotate(-10deg) scale(1.25);
                color: #FFF2A8;
            }
            60% {
                transform: rotate(6deg) scale(1.1);
            }
            80% {
                transform: rotate(0deg) scale(1);
            }
        }

        .timer-badge {
            position: relative;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            padding: 8px 30px;
            border-radius: 50px;
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.24) 0%, rgba(3, 33, 18, 0.88) 100%);
            border: 1.5px solid rgba(212, 175, 55, 0.65);
            font-size: 15px;
            font-weight: 700;
            letter-spacing: 3.5px;
            color: #FFF5CC;
            text-transform: uppercase;
            overflow: hidden;
            margin-bottom: 16px;
            animation: timerBadgePulse 2.6s infinite ease-in-out;
            backdrop-filter: blur(12px);
        }

        .badge-shimmer {
            position: absolute;
            top: 0;
            left: 0;
            width: 45%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.45), transparent);
            pointer-events: none;
            animation: lightSweep 2.8s ease-in-out infinite;
        }

        .clock-pulse-icon {
            display: inline-block;
            font-size: 15px;
            animation: clockTick 2s infinite ease-in-out;
            transform-origin: center center;
        }

        .pulse-indicator-dot {
            width: 7px;
            height: 7px;
            background: #D4AF37;
            border-radius: 50%;
            box-shadow: 0 0 8px #FFD700;
            animation: pulseGlow 1.5s infinite;
        }

        .adab-notice {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 7px 26px;
            margin-bottom: 12px;
            border-radius: 50px;
            background: rgba(212, 175, 55, 0.16);
            border: 1px solid rgba(212, 175, 55, 0.45);
            color: #FFF3BD;
            font-size: clamp(14px, 1.35vw, 18px);
            font-weight: 600;
            letter-spacing: 0.6px;
            text-shadow: 0 1px 4px rgba(0, 0, 0, 0.6);
        }

        .adab-notice i {
            color: #D4AF37;
            font-size: 15px;
        }

        .phase-quote {
            font-size: clamp(18px, 1.85vw, 26px) !important;
            font-weight: 600 !important;
            color: #FFFDF0 !important;
            line-height: 1.5 !important;
            padding: 6px 12px;
        }

        .countdown-tiles-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 18px;
        }

        .countdown-tile {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .tile-face {
            position: relative;
            width: clamp(130px, 13vw, 190px);
            height: clamp(95px, 9.5vw, 135px);
            background: linear-gradient(180deg, rgba(8, 48, 28, 0.92) 0%, rgba(2, 20, 11, 0.98) 100%);
            border: 1.5px solid rgba(212, 175, 55, 0.45);
            border-radius: 20px;
            box-shadow:
                0 16px 36px rgba(0, 0, 0, 0.65),
                0 0 28px rgba(212, 175, 55, 0.16),
                inset 0 2px 0 rgba(255, 240, 185, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        /* Garis Horisontal Crease (Mewah seperti flip clock) */
        .tile-crease {
            position: absolute;
            left: 0;
            right: 0;
            top: 50%;
            height: 1px;
            background: rgba(0, 0, 0, 0.45);
            border-bottom: 1px solid rgba(212, 175, 55, 0.2);
            pointer-events: none;
        }

        .tile-number {
            font-family: 'Cinzel', 'Poppins', sans-serif;
            font-size: clamp(62px, 6.2vw, 92px);
            font-weight: 800;
            line-height: 1;
            background: var(--gold-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            filter: drop-shadow(0 4px 14px rgba(212, 175, 55, 0.45));
            letter-spacing: 2px;
        }

        .tile-label {
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 4px;
            color: #D4AF37;
            margin-top: 8px;
            text-transform: uppercase;
        }

        .countdown-separator {
            font-family: 'Cinzel', serif;
            font-size: clamp(52px, 5vw, 76px);
            font-weight: 800;
            color: #D4AF37;
            text-shadow: 0 0 18px rgba(212, 175, 55, 0.6);
            margin-bottom: 24px;
            animation: pulseGlow 1.8s infinite;
        }

        /* =====================================================
           BAGIAN 4: PESAN DINAMIS MASJID
           ===================================================== */
        .message-section {
            width: 100%;
            max-width: 1100px;
            margin: 4px auto;
        }

        .message-banner {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 16px;
            padding: 10px 32px;
            border-radius: 16px;
            background: rgba(1, 16, 8, 0.55);
            border: 1px solid rgba(212, 175, 55, 0.25);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.35);
        }

        .message-icon {
            color: #D4AF37;
            font-size: 22px;
        }

        .message-text {
            font-size: clamp(20px, 2vw, 30px);
            font-weight: 600;
            line-height: 1.35;
            color: #FFFFFF;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.5);
        }

        /* Saat Mode Sholat Aktif (Fase Prayer) */
        .prayer-mode-box {
            padding: 24px 44px;
            background: linear-gradient(135deg, rgba(8, 48, 28, 0.95), rgba(2, 20, 11, 0.98));
            border: 2px solid #D4AF37;
            border-radius: 28px;
            box-shadow: 0 0 40px rgba(212, 175, 55, 0.35);
            animation: pulseGlow 4s infinite;
        }

        .prayer-mode-box .icons-row {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 30px;
            margin-bottom: 14px;
            color: #D4AF37;
            font-size: 34px;
        }

        /* =====================================================
           BAGIAN 5: HADITH VIGNETTE PLAKAT
           ===================================================== */
        .hadith-plakat {
            position: relative;
            width: 100%;
            max-width: 1120px;
            margin: 6px auto 2px;
            padding: 14px 48px;
            border-radius: 22px;
            background: rgba(1, 18, 9, 0.65);
            border: 1px solid rgba(212, 175, 55, 0.28);
            box-shadow: inset 0 1px 0 rgba(255, 238, 170, 0.15), 0 10px 30px rgba(0, 0, 0, 0.4);
        }

        .quote-watermark {
            position: absolute;
            font-family: 'Cinzel', serif;
            font-size: 80px;
            color: #D4AF37;
            opacity: 0.14;
            line-height: 1;
            pointer-events: none;
        }

        .quote-left {
            top: 2px;
            left: 18px;
        }

        .quote-right {
            bottom: -22px;
            right: 18px;
        }

        .hadith-header {
            font-size: clamp(14px, 1.4vw, 18px);
            font-weight: 500;
            color: #F0DEAA;
            letter-spacing: 1px;
            margin-bottom: 4px;
        }

        .saw-symbol {
            font-family: 'Amiri', serif;
            font-weight: bold;
            color: #D4AF37;
            font-size: 1.15em;
        }

        .hadith-body {
            font-size: clamp(16px, 1.65vw, 23px);
            font-style: italic;
            font-weight: 500;
            line-height: 1.45;
            color: #F8FAFC;
            text-shadow: 0 1px 4px rgba(0, 0, 0, 0.6);
            margin-bottom: 4px;
        }

        .hadith-source {
            font-size: clamp(12px, 1.1vw, 15px);
            font-weight: 600;
            letter-spacing: 1.5px;
            color: #D4AF37;
        }

        /* =====================================================
           BAGIAN 6: FOOTER MASJID
           ===================================================== */
        .masjid-footer {
            font-size: 14px;
            font-weight: 500;
            letter-spacing: 2px;
            color: rgba(212, 175, 55, 0.75);
            text-transform: uppercase;
            padding-bottom: 2px;
        }

        /* =====================================================
           RESPONSIVE TWEAKS (Full HD, Laptop & Tablet)
           ===================================================== */
        @media (max-height: 800px) {
            .container {
                padding: 16px 28px;
                height: 96vh;
            }
            .arabic-text {
                font-size: 42px;
            }
            .phase-title {
                font-size: 18px;
                margin-bottom: 6px;
            }
            .prayer-badge {
                padding: 6px 28px;
            }
            .prayer-name {
                font-size: 26px;
            }
            .tile-face {
                width: 120px;
                height: 85px;
            }
            .tile-number {
                font-size: 58px;
            }
            .hadith-plakat {
                padding: 10px 32px;
            }
            .hadith-body {
                font-size: 16px;
            }
        }
    </style>
</head>

<body>
    <!-- BACKGROUND MULTI-LAYER -->
    <div class="bg-layer bg-gradient"></div>
    <div class="bg-layer bg-pattern"></div>
    <div class="bg-layer bg-kaabah"></div>
    <div class="bg-layer bg-vignette"></div>

    <!-- CAHAYA AMBIEN HALUS -->
    <div class="ambient-orb ambient-orb-1"></div>
    <div class="ambient-orb ambient-orb-2"></div>

    <!-- THE ROYAL MIHRAB CONTAINER -->
    <div class="container">
        <!-- ORNAMEN SUDUT EMAS -->
        <div class="corner-bracket corner-top-left"></div>
        <div class="corner-bracket corner-top-right"></div>
        <div class="corner-bracket corner-bottom-left"></div>
        <div class="corner-bracket corner-bottom-right"></div>
        <div class="mihrab-arch-top"></div>

        <!-- 1. KALIGRAFI ARAB SAKRAL -->
        <div class="arabic-section">
            <div class="arabic-text">
                ﴿ وَأَقِيمُوا الصَّلَاةَ ﴾
            </div>
            <div class="gold-divider">
                <span class="divider-line"></span>
                <span class="divider-star">✦ ۞ ✦</span>
                <span class="divider-line"></span>
            </div>
        </div>

        <!-- 2. JUDUL FASE & BADGE NAMA SHOLAT -->
        <div class="phase-section">
            <div class="phase-title">
                @if($phase == 'countdown')
                    Menjelang Waktu Sholat
                @elseif($phase == 'adzan')
                    Menuju waktu sholat
                @elseif($phase == 'iqamah')
                    Menunggu Waktu Iqamah
                @elseif($phase == 'prayer')
                    Sholat Sedang Berlangsung
                @else
                    Mode Sholat
                @endif
            </div>

            <div class="prayer-badge-wrapper">
                <div class="prayer-badge">
                    <span class="badge-gem"></span>
                    <span class="prayer-name">
                        @if($currentPrayer)
                            {{ strtoupper(is_object($currentPrayer) ? $currentPrayer->nama_sholat : $currentPrayer) }}
                        @else
                            WAKTU SHOLAT
                        @endif
                    </span>
                    <span class="badge-gem"></span>
                </div>
            </div>
        </div>

        <!-- 3. COUNTDOWN TIMER (SPLIT DUAL-TILE) -->
        @if($phase != 'prayer')
            <div class="countdown-section">
                <!-- Badge Interaktif Menuju Adzan / Fase (Tanpa Kapsul Waktu Sekarang) -->
                <div class="timer-badge">
                    <div class="badge-shimmer"></div>
                    <i class="fa-regular fa-clock clock-pulse-icon"></i>
                    <span class="badge-text">
                        @if($phase == 'countdown')
                            Menuju Adzan
                        @elseif($phase == 'adzan')
                            Adzan sedang di Kumandangkan
                        @elseif($phase == 'iqamah')
                            Menuju Iqamah
                        @else
                            Sisa Waktu
                        @endif
                    </span>
                    <span class="pulse-indicator-dot"></span>
                </div>

                <div class="countdown-tiles-wrapper">
                    <!-- TILE MENIT -->
                    <div class="countdown-tile">
                        <div class="tile-face">
                            <span class="tile-number" id="timerMinutes">00</span>
                            <div class="tile-crease"></div>
                        </div>
                        <div class="tile-label">MENIT</div>
                    </div>

                    <div class="countdown-separator">:</div>

                    <!-- TILE DETIK -->
                    <div class="countdown-tile">
                        <div class="tile-face">
                            <span class="tile-number" id="timerSeconds">00</span>
                            <div class="tile-crease"></div>
                        </div>
                        <div class="tile-label">DETIK</div>
                    </div>
                </div>

                <!-- Elemen tersembunyi untuk kompatibilitas script -->
                <div class="countdown" id="countdown" data-seconds="{{ $remainingSeconds }}" style="display:none;">00:00</div>
            </div>
        @endif

        <!-- 4. PESAN MASJID DINAMIS -->
        <div class="message-section">
            @php
                $displayMessage = '';
                if ($phase == 'countdown') {
                    $displayMessage = $setting->Countdown_Adzan ?? 'Bersiap Masuk Waktu Sholat';
                } elseif ($phase == 'adzan') {
                    $displayMessage = 'Mohon untuk menonaktifkan/silent alat komunikasi';
                } elseif ($phase == 'iqamah') {
                    $displayMessage = $setting->Hitung_Mundur_Iqamah ?? 'Menuju Waktu Iqamah';
                } elseif ($phase == 'prayer') {
                    $displayMessage = 'Iqamah Segera Dikumandangkan. Mari Bersiap Mengisi Shaf Terdepan Yang Masih Kosong, Luruskan dan Rapatkan shaf sholat';
                } else {
                    $displayMessage = $setting->Mode_Sholat ?? 'Harap Tenang & Khusyuk';
                }
            @endphp

            @if($phase == 'prayer')
                <div class="prayer-mode-box">
                    <div class="icons-row">
                        <i class="fa-solid fa-volume-xmark" title="Matikan Suara HP"></i>
                        <i class="fa-solid fa-people-arrows" title="Rapatkan Shaf"></i>
                        <i class="fa-solid fa-mosque"></i>
                    </div>
                    <div class="message-text">
                        {!! nl2br(e($displayMessage)) !!}
                    </div>
                </div>
            @elseif($phase == 'adzan')
                <div class="message-banner">
                    <i class="fa-solid fa-volume-xmark message-icon"></i>
                    <span class="message-text">
                        {!! nl2br(e($displayMessage)) !!}
                    </span>
                    <i class="fa-solid fa-volume-xmark message-icon"></i>
                </div>
            @else
                <div class="message-banner">
                    <i class="fa-solid fa-mosque message-icon"></i>
                    <span class="message-text">
                        {!! nl2br(e($displayMessage)) !!}
                    </span>
                </div>
            @endif
        </div>

        <!-- 5. PLAKAT PESAN & HADITS SHOLAT -->
        <div class="hadith-plakat">
            <span class="quote-watermark quote-left">“</span>

            @if($phase == 'countdown')
                <div class="adab-notice">
                    <i class="fa-solid fa-person-praying"></i>
                    <span>Waktu Adzan akan Segera Tiba. Mari Merapikan Pakaian Dan Berwudhu.</span>
                </div>
                <div class="hadith-header">
                    Rasulullah <span class="saw-symbol">ﷺ</span> bersabda:
                </div>
                <div class="hadith-body">
                    "Shalat berjamaah lebih utama daripada shalat sendirian dengan dua puluh tujuh derajat."
                </div>
                <div class="hadith-source">
                    (HR. Bukhari dan Muslim)
                </div>
            @elseif($phase == 'adzan')
                <div class="hadith-body phase-quote">
                    "Adzan Sedang Dikumandangkan. Dengarkanlah, Jawablah, Dan Raih Pahala Kesempurnaannya."
                </div>
            @elseif($phase == 'iqamah')
                <div class="hadith-body phase-quote">
                    "Adzan Telah selesai, Mari Gunakan Waktu Yang Tersedia Untuk Berdoa. Doa Antara Adzan & Iqamah Tidak Ditolak."
                </div>
            @else
                {{-- prayer phase --}}
                <div class="hadith-header">
                    Rasulullah <span class="saw-symbol">ﷺ</span> bersabda:
                </div>
                <div class="hadith-body">
                    "Shalat berjamaah lebih utama daripada shalat sendirian dengan dua puluh tujuh derajat."
                </div>
                <div class="hadith-source">
                    (HR. Bukhari dan Muslim)
                </div>
            @endif

            <span class="quote-watermark quote-right">”</span>
        </div>

        <!-- 6. FOOTER MASJID -->
        <div class="masjid-footer">
            @php
                $rawFooter = $setting->footer ?? '';
                // Mengonversi kode &copy;, &COPY;, &#169;, dll menjadi simbol © asli
                $cleanFooter = preg_replace('/&amp;copy;|&copy;|&#169;|&#xa9;/i', '©', $rawFooter);
                $cleanFooter = html_entity_decode($cleanFooter, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            @endphp
            {!! $cleanFooter !!}
        </div>
    </div>

    <!-- SCRIPT AUDIO & COUNTDOWN LOGIC -->
    <script>
        let remaining = parseInt(document.getElementById('countdown')?.dataset?.seconds || 0);
        let hasPlayedTarhim = false;
        let isTransitioning = false;
        let countdownTimer = null;

        function formatTwoDigits(num) {
            return String(Math.max(0, num)).padStart(2, '0');
        }

        function updateCountdown() {
            if (isTransitioning) return;

            const elHidden = document.getElementById('countdown');
            const elMinutes = document.getElementById('timerMinutes');
            const elSeconds = document.getElementById('timerSeconds');

            const current = Math.max(0, remaining);
            const minutes = Math.floor(current / 60);
            const seconds = current % 60;

            const minStr = formatTwoDigits(minutes);
            const secStr = formatTwoDigits(seconds);

            if (elMinutes) elMinutes.textContent = minStr;
            if (elSeconds) elSeconds.textContent = secStr;
            if (elHidden) elHidden.textContent = minStr + ':' + secStr;

            // AUDIO TARHIM (Countdown phase <= trigger seconds, default 300)
            @if($phase == 'countdown')
                if (remaining <= {{ $setting->tarhim_trigger_seconds ?? 300 }} && !hasPlayedTarhim) {
                    const tarhimAudio = document.getElementById('audioTarhim');
                    if (tarhimAudio) {
                        tarhimAudio.play().catch(function(error) {
                            console.log("Audio tarhim diblokir browser:", error);
                        });
                    }
                    hasPlayedTarhim = true;
                }
            @endif

            // AUDIO ADZAN (Adzan phase)
            @if($phase == 'adzan')
                const adzanAudio = document.getElementById('audioAdzan');
                if (adzanAudio && !hasPlayedTarhim) {
                    adzanAudio.play().catch(function(error) {
                        console.log("Audio adzan diblokir browser:", error);
                    });
                    hasPlayedTarhim = true;
                }
            @endif

            if (remaining > 0) {
                remaining--;
            } else {
                // Countdown mencapai 00:00! Kunci proses agar tidak memicu reload berulang
                isTransitioning = true;
                if (countdownTimer) {
                    clearInterval(countdownTimer);
                    countdownTimer = null;
                }
                triggerPhaseTransition();
            }
        }

        function triggerPhaseTransition() {
            let attempts = 0;
            const checkNextPhase = setInterval(function () {
                attempts++;
                const debugParam = new URLSearchParams(window.location.search).get('debug');
                const statusUrl = '/prayer-mode/status?_=' + Date.now() + (debugParam ? '&debug=' + debugParam : '');

                fetch(statusUrl)
                    .then(response => response.json())
                    .then(function (data) {
                        if (!data.active) {
                            clearInterval(checkNextPhase);
                            if (window !== window.parent) {
                                // Parent rotator mengurus
                            } else {
                                window.location.href = "/";
                            }
                        } else if (data.phase !== '{{ $phase }}') {
                            // Server telah resmi masuk fase berikutnya!
                            clearInterval(checkNextPhase);
                            if (debugParam) {
                                const currentUrl = new URL(window.location.href);
                                currentUrl.searchParams.set('phase', data.phase);
                                currentUrl.searchParams.delete('remaining');
                                window.location.href = currentUrl.toString();
                            } else {
                                window.location.reload();
                            }
                        }
                    })
                    .catch(function (error) {
                        console.log("Transisi fase polling error:", error);
                    });

                // Failsafe: jika dalam 5 detik server belum berganti, reload sekali
                if (attempts >= 5) {
                    clearInterval(checkNextPhase);
                    window.location.reload();
                }
            }, 1000);
        }

        @if($phase != 'prayer')
            updateCountdown();
            countdownTimer = setInterval(updateCountdown, 1000);
        @endif

        /* Status polling (HANYA reload jika server menyatakan fase sholat telah berubah) */
        setInterval(function () {
            if (isTransitioning) return;

            const debugParam = new URLSearchParams(window.location.search).get('debug');
            const statusUrl = '/prayer-mode/status?_=' + Date.now() + (debugParam ? '&debug=' + debugParam : '');

            fetch(statusUrl)
                .then(response => response.json())
                .then(function (data) {
                    if (!data.active) {
                        if (window !== window.parent) {
                            // Rotator parent mengurus pergantian
                        } else {
                            window.location.href = "/";
                        }
                    } else if (data.phase !== '{{ $phase }}') {
                        // Fase sholat berganti dari server, reload untuk merender fase baru
                        if (debugParam) {
                            const currentUrl = new URL(window.location.href);
                            currentUrl.searchParams.set('phase', data.phase);
                            currentUrl.searchParams.delete('remaining');
                            window.location.href = currentUrl.toString();
                        } else {
                            window.location.reload();
                        }
                    }
                })
                .catch(function (error) {
                    console.log("Status polling error:", error);
                });
        }, 3000);
    </script>

    <!-- ELEMEN AUDIO TERSEMBUNYI -->
    @php
        $namaSholatRaw = $currentPrayer ? (is_object($currentPrayer) ? ($currentPrayer->nama_sholat ?? '') : $currentPrayer) : '';
        $isSubuh = strtolower(trim($namaSholatRaw)) === 'subuh';

        if ($isSubuh) {
            if (!empty($setting->tarhim_audio_subuh)) {
                $tarhimSource = asset('storage/' . $setting->tarhim_audio_subuh);
            } elseif (!empty($setting->tarhim_audio)) {
                $tarhimSource = asset('storage/' . $setting->tarhim_audio);
            } elseif (file_exists(public_path('audio/Subuh.mp3'))) {
                $tarhimSource = asset('audio/Subuh.mp3');
            } else {
                $tarhimSource = asset('audio/tarhim2.mp3');
            }
        } else {
            if (!empty($setting->tarhim_audio_reguler)) {
                $tarhimSource = asset('storage/' . $setting->tarhim_audio_reguler);
            } elseif (!empty($setting->tarhim_audio)) {
                $tarhimSource = asset('storage/' . $setting->tarhim_audio);
            } else {
                $tarhimSource = asset('audio/tarhim2.mp3');
            }
        }
    @endphp
    <audio id="audioTarhim">
        <source src="{{ $tarhimSource }}" type="audio/mpeg">
    </audio>
    <audio id="audioAdzan">
        @php
            $namaSholatClean = ucwords(strtolower(trim($namaSholatRaw)));
            $audioFile = 'adzan.mp3?v=2';
            if (in_array($namaSholatClean, ['Subuh', 'Dzuhur', 'Ashar', 'Maghrib', 'Isya'])) {
                $audioFile = $namaSholatClean . '.mp3';
            }
        @endphp
        <source src="{{ asset('audio/' . $audioFile) }}" type="audio/mpeg">
    </audio>
</body>

</html>