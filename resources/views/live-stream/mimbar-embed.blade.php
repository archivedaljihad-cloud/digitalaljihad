<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Siaran Langsung Mimbar & Khutbah - {{ $settings->nama_aplikasi ?? 'Masjid' }}</title>
    
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}?v=3.0.4">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800&family=Amiri:wght@700&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body, html {
            width: 100%;
            height: 100%;
            overflow: hidden;
            background-color: #000000;
            font-family: 'Poppins', sans-serif;
            color: #ffffff;
        }

        .stream-container {
            position: relative;
            width: 100vw;
            height: 100vh;
            overflow: hidden;
            background: #050505;
        }

        /* Video Player Wrapper */
        .video-wrapper {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
        }

        .video-frame {
            width: 100%;
            height: 100%;
            border: none;
            object-fit: cover;
        }

        /* Standby / Fallback Display saat CCTV belum tersambung */
        .standby-screen {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background: radial-gradient(circle at center, #0e2a1d 0%, #030d08 100%);
            z-index: 1;
            text-align: center;
            padding: 40px;
        }

        .standby-icon {
            font-size: 5rem;
            color: #ffd700;
            margin-bottom: 20px;
            animation: pulseGlow 2.5s infinite alternate;
        }

        .standby-title {
            font-size: 2.2rem;
            font-weight: 800;
            letter-spacing: 2px;
            color: #ffffff;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .standby-subtitle {
            font-size: 1.1rem;
            color: rgba(255, 255, 255, 0.7);
            max-width: 650px;
            line-height: 1.6;
        }

        /* SMART BROADCAST OVERLAY */
        .overlay-layer {
            position: absolute;
            inset: 0;
            z-index: 10;
            pointer-events: none;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 30px 40px;
            background: linear-gradient(180deg, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0) 25%, rgba(0,0,0,0) 70%, rgba(0,0,0,0.85) 100%);
        }

        /* Top Bar */
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .mosque-brand {
            display: flex;
            align-items: center;
            gap: 15px;
            background: rgba(10, 30, 20, 0.75);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 215, 0, 0.3);
            border-radius: 50px;
            padding: 8px 24px 8px 16px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.4);
        }

        .mosque-icon {
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, #ffd700, #b8860b);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #0a2e1f;
            font-size: 1.2rem;
        }

        .mosque-name {
            font-size: 1.15rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            color: #ffffff;
        }

        .live-status-pill {
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(220, 38, 38, 0.25);
            border: 1px solid rgba(239, 68, 68, 0.6);
            backdrop-filter: blur(10px);
            border-radius: 50px;
            padding: 8px 20px;
            font-weight: 700;
            font-size: 0.95rem;
            letter-spacing: 1px;
            color: #fecaca;
        }

        .pulsing-dot {
            width: 12px;
            height: 12px;
            background-color: #ef4444;
            border-radius: 50%;
            box-shadow: 0 0 12px #ef4444;
            animation: pulseRed 1.2s infinite;
        }

        /* Bottom Section */
        .bottom-bar {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 30px;
        }

        /* Officer Card */
        .officer-box {
            background: rgba(15, 25, 20, 0.85);
            backdrop-filter: blur(15px);
            border-left: 5px solid #ffd700;
            border-radius: 12px;
            padding: 16px 24px;
            min-width: 380px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }

        .officer-event-badge {
            font-size: 0.8rem;
            font-weight: 800;
            color: #ffd700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .officer-name {
            font-size: 1.35rem;
            font-weight: 800;
            color: #ffffff;
            line-height: 1.2;
            margin-bottom: 4px;
        }

        .officer-role {
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.7);
        }

        /* Hadits & Adab Khutbah Pill */
        .hadits-box {
            flex: 1;
            background: rgba(0, 0, 0, 0.65);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 12px;
            padding: 14px 20px;
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .hadits-icon {
            font-size: 1.8rem;
            color: #ffd700;
        }

        .hadits-text {
            font-size: 0.92rem;
            color: #e2e8f0;
            line-height: 1.4;
        }

        .hadits-source {
            font-weight: 700;
            color: #facc15;
        }

        /* Clock Card */
        .clock-box {
            background: rgba(15, 25, 20, 0.85);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 215, 0, 0.25);
            border-radius: 12px;
            padding: 12px 24px;
            text-align: right;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }

        .clock-time {
            font-size: 1.8rem;
            font-weight: 800;
            color: #ffd700;
            line-height: 1;
            font-variant-numeric: tabular-nums;
        }

        .clock-date {
            font-size: 0.82rem;
            color: rgba(255, 255, 255, 0.8);
            margin-top: 4px;
        }

        @keyframes pulseRed {
            0% { transform: scale(0.95); opacity: 0.8; }
            50% { transform: scale(1.25); opacity: 1; }
            100% { transform: scale(0.95); opacity: 0.8; }
        }

        @keyframes pulseGlow {
            0% { transform: scale(0.98); opacity: 0.8; }
            100% { transform: scale(1.05); opacity: 1; filter: drop-shadow(0 0 25px rgba(255,215,0,0.6)); }
        }
    </style>
</head>
<body>

<div class="stream-container">
    <!-- VIDEO PLAYER LAYER -->
    <div class="video-wrapper">
        @if(!empty($streamUrl))
            @if($isYouTube)
                <iframe class="video-frame" 
                    src="{{ $streamUrl }}" 
                    title="CCTV Mimbar Live Stream" 
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                    allowfullscreen>
                </iframe>
            @elseif($isIframe)
                <iframe class="video-frame" 
                    src="{{ $streamUrl }}" 
                    title="CCTV Mimbar Stream" 
                    allow="autoplay">
                </iframe>
            @else
                <!-- HTML5 Video Player untuk HLS / MP4 -->
                <video class="video-frame" autoplay playsinline muted loop>
                    <source src="{{ $streamUrl }}">
                    Browser tidak mendukung pemutaran video.
                </video>
            @endif
        @else
            <!-- STANDBY SCREEN JIKA CCTV BELUM DIHUBUNGKAN -->
            <div class="standby-screen">
                <div class="standby-icon">
                    <i class="fa-solid fa-video"></i>
                </div>
                <div class="standby-title">Kamera Mimbar Siap Tayang</div>
                <div class="standby-subtitle">
                    Siaran langsung mimbar masjid akan aktif saat khutbah berlangsung. Silakan hubungkan stream CCTV/DVR di menu Pengaturan Aplikasi.
                </div>
            </div>
        @endif
    </div>

    <!-- SMART BROADCAST OVERLAY -->
    <div class="overlay-layer">
        <!-- TOP BAR -->
        <div class="top-bar">
            <div class="mosque-brand">
                <div class="mosque-icon">
                    <i class="fa-solid fa-mosque"></i>
                </div>
                <div class="mosque-name">
                    {{ $settings->nama_aplikasi ?? 'MASJID AL-IKHLAS' }}
                </div>
            </div>

            <div class="live-status-pill">
                <div class="pulsing-dot"></div>
                <span>SIARAN LANGSUNG KHUTBAH MIMBAR</span>
            </div>
        </div>

        <!-- BOTTOM BAR -->
        <div class="bottom-bar">
            <!-- Officer Card -->
            <div class="officer-box">
                <div class="officer-event-badge">
                    <i class="fa-solid fa-microphone-lines"></i> {{ $officers['type'] }}
                </div>
                <div class="officer-name">
                    {{ $officers['khatib'] }}
                </div>
                <div class="officer-role">
                    Khatib &bull; Imam: <strong>{{ $officers['imam'] }}</strong>
                </div>
            </div>

            <!-- Hadits Pill -->
            <div class="hadits-box">
                <div class="hadits-icon">
                    <i class="fa-solid fa-volume-xmark"></i>
                </div>
                <div class="hadits-text">
                    "Jika engkau berkata kepada saudaramu: <em>'Diamlah!'</em> pada hari Jum'at saat imam berkhutbah, maka sungguh engkau telah berbuat sia-sia." <span class="hadits-source">(HR. Bukhari & Muslim)</span>
                </div>
            </div>

            <!-- Clock Card -->
            <div class="clock-box">
                <div class="clock-time" id="liveClock">--:--:--</div>
                <div class="clock-date" id="liveDate">Memuat Tanggal...</div>
            </div>
        </div>
    </div>
</div>

<script>
    // Live Clock Updater
    function updateClock() {
        const now = new Date();
        const pad = (n) => String(n).padStart(2, '0');
        const timeStr = `${pad(now.getHours())}:${pad(now.getMinutes())}:${pad(now.getSeconds())} WIB`;
        
        const days = ['Ahad', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        const dateStr = `${days[now.getDay()]}, ${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()}`;

        const elClock = document.getElementById('liveClock');
        const elDate = document.getElementById('liveDate');
        if (elClock) elClock.textContent = timeStr;
        if (elDate) elDate.textContent = dateStr;
    }

    updateClock();
    setInterval(updateClock, 1000);
</script>

</body>
</html>
