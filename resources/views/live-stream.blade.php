{{-- resources/views/live-stream.blade.php --}}
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} - {{ $settings->nama_aplikasi ?? 'Display Masjid' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('img/favicon.png') }}?v=3.0.4">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Amiri:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}?v=3.0.4">
    <link rel="stylesheet" href="{{ asset('css/display-theme.css') }}?v=3.0.4">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body, html {
            width: 100vw;
            height: 100vh;
            overflow: hidden;
            background-color: #050807;
            font-family: 'Poppins', sans-serif;
            color: #FFFFFF;
            user-select: none;
        }

        /* 1. LAYER VIDEO STREAMING */
        .video-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
            overflow: hidden;
            background: #000000;
        }

        .video-container iframe {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 100vw;
            height: 100vh;
            transform: translate(-50%, -50%) scale(1.05); /* Sedikit zoom untuk menghilangkan bezel YouTube */
            border: none;
            pointer-events: none;
        }

        @media (min-aspect-ratio: 16/9) {
            .video-container iframe {
                height: 56.25vw;
            }
        }

        @media (max-aspect-ratio: 16/9) {
            .video-container iframe {
                width: 177.78vh;
            }
        }

        /* VIGNETTE SHADOW DI TEPIAN LAYAR */
        .vignette-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 2;
            pointer-events: none;
            background: radial-gradient(circle at center, rgba(0,0,0,0) 50%, rgba(0,0,0,0.65) 100%),
                        linear-gradient(180deg, rgba(3, 15, 8, 0.85) 0%, rgba(0,0,0,0.1) 22%, rgba(0,0,0,0.1) 75%, rgba(2, 10, 5, 0.95) 100%);
        }

        /* 2. OVERLAY SMART MOSQUE */
        .mosque-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 3;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 22px 36px 16px 36px;
            pointer-events: none;
        }

        /* HEADER */
        .overlay-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            width: 100%;
        }

        .channel-info-box {
            display: flex;
            align-items: center;
            gap: 16px;
            background: rgba(8, 28, 18, 0.82);
            border: 1.5px solid rgba(212, 175, 55, 0.45);
            padding: 10px 22px;
            border-radius: 20px;
            backdrop-filter: blur(12px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.6), inset 0 1px 0 rgba(255, 240, 180, 0.2);
        }

        .live-pulse-container {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #E11D48;
            color: #FFFFFF;
            font-size: 13px;
            font-weight: 800;
            letter-spacing: 1.5px;
            padding: 5px 12px;
            border-radius: 30px;
            text-transform: uppercase;
            box-shadow: 0 0 15px rgba(225, 29, 72, 0.6);
        }

        .pulse-dot {
            width: 9px;
            height: 9px;
            background-color: #FFFFFF;
            border-radius: 50%;
            animation: pulseAnim 1.2s infinite ease-in-out;
        }

        @keyframes pulseAnim {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.3; transform: scale(0.7); }
        }

        .channel-text {
            display: flex;
            flex-direction: column;
        }

        .channel-title {
            font-size: clamp(16px, 1.4vw, 22px);
            font-weight: 800;
            letter-spacing: 1px;
            color: #FFFFFF;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.8);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .channel-title i {
            color: {{ $themeColor }};
        }

        .channel-sub {
            font-size: 13px;
            color: #E2E8F0;
            letter-spacing: 0.5px;
            font-weight: 500;
        }

        /* CLOCK WIDGET */
        .clock-widget-box {
            text-align: right;
            background: rgba(8, 28, 18, 0.82);
            border: 1.5px solid rgba(212, 175, 55, 0.45);
            padding: 8px 24px;
            border-radius: 20px;
            backdrop-filter: blur(12px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.6);
        }

        .clock-digits {
            font-family: 'Poppins', sans-serif;
            font-size: clamp(28px, 2.6vw, 42px);
            font-weight: 800;
            color: #F8FAFC;
            letter-spacing: 2px;
            line-height: 1.1;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.7);
        }

        .clock-seconds {
            font-size: 0.65em;
            color: {{ $themeColor }};
            margin-left: 2px;
        }

        .date-text {
            font-size: 13px;
            font-weight: 600;
            color: #CBD5E1;
            letter-spacing: 0.5px;
            margin-top: 2px;
        }

        /* FOOTER AREA */
        .overlay-footer {
            display: flex;
            flex-direction: column;
            gap: 12px;
            width: 100%;
        }

        /* JADWAL SHOLAT MINI BAR */
        .prayer-times-bar {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
            gap: 12px;
            width: 100%;
        }

        .prayer-item {
            background: linear-gradient(180deg, rgba(8, 38, 22, 0.85) 0%, rgba(3, 18, 10, 0.95) 100%);
            border: 1px solid rgba(212, 175, 55, 0.35);
            border-radius: 14px;
            padding: 8px 14px;
            text-align: center;
            backdrop-filter: blur(10px);
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.5);
            transition: all 0.3s ease;
        }

        .prayer-item.active {
            border-color: #F59E0B;
            background: linear-gradient(180deg, rgba(180, 130, 20, 0.4) 0%, rgba(40, 30, 5, 0.95) 100%);
            box-shadow: 0 0 20px rgba(245, 158, 11, 0.4);
            transform: translateY(-2px);
        }

        .prayer-name {
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: #CBD5E1;
        }

        .prayer-item.active .prayer-name {
            color: #FDE68A;
        }

        .prayer-time {
            font-size: clamp(18px, 1.8vw, 24px);
            font-weight: 800;
            color: #FFFFFF;
            letter-spacing: 1px;
            margin-top: 2px;
        }

        /* RUNNING TEXT TICKER */
        .ticker-bar {
            display: flex;
            align-items: center;
            background: rgba(4, 18, 10, 0.9);
            border: 1px solid rgba(212, 175, 55, 0.4);
            border-radius: 30px;
            padding: 6px 18px;
            overflow: hidden;
            backdrop-filter: blur(14px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.6);
        }

        .ticker-badge {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 800;
            letter-spacing: 1px;
            color: #10B981;
            background: rgba(16, 185, 129, 0.15);
            padding: 4px 14px;
            border-radius: 20px;
            white-space: nowrap;
            margin-right: 18px;
        }

        .ticker-marquee {
            flex: 1;
            overflow: hidden;
            white-space: nowrap;
        }

        .ticker-content {
            display: inline-block;
            padding-left: 100%;
            animation: marquee 35s linear infinite;
            font-size: 15px;
            font-weight: 500;
            color: #F1F5F9;
            letter-spacing: 0.5px;
        }

        @keyframes marquee {
            0% { transform: translate3d(0, 0, 0); }
            100% { transform: translate3d(-100%, 0, 0); }
        }

        /* WATERMARK / AUDIO STATUS */
        .stream-status-tag {
            position: absolute;
            bottom: 85px;
            right: 36px;
            background: rgba(0, 0, 0, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.15);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            color: #94A3B8;
            backdrop-filter: blur(6px);
            z-index: 4;
            display: flex;
            align-items: center;
            gap: 6px;
        }
    </style>
</head>

<body>
    <!-- 1. VIDEO STREAMING PLAYER IFRAME -->
    <div class="video-container">
        <iframe 
            id="livePlayer"
            src="{{ $embedUrl }}" 
            title="{{ $title }}" 
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
            allowfullscreen>
        </iframe>
    </div>

    <!-- 2. VIGNETTE FILTER -->
    <div class="vignette-overlay"></div>

    <!-- 3. OVERLAY INFORMASI MASJID -->
    @if($overlayEnabled)
        <div class="mosque-overlay">
            <!-- HEADER -->
            <div class="overlay-header">
                <div class="channel-info-box">
                    <div class="live-pulse-container">
                        <span class="pulse-dot"></span> LIVE
                    </div>
                    <div class="channel-text">
                        <div class="channel-title">
                            @if($channelKey == 'mekah')
                                <i class="fa-solid fa-kaaba"></i> {{ $title }}
                            @else
                                <i class="fa-solid fa-mosque"></i> {{ $title }}
                            @endif
                        </div>
                        <div class="channel-sub">
                            {{ $subtitle }} &bull; {{ $settings->nama_aplikasi ?? 'Masjid Jami\' Al-Jihad' }}
                        </div>
                    </div>
                </div>

                <div class="clock-widget-box">
                    <div class="clock-digits" id="digitalClock">00:00<span class="clock-seconds">:00</span></div>
                    <div class="date-text" id="gregorianDate">Memuat Tanggal...</div>
                </div>
            </div>

            <!-- FOOTER -->
            <div class="overlay-footer">
                <!-- ROW JADWAL SHOLAT MINI -->
                <div class="prayer-times-bar">
                    @php
                        $sholatList = [
                            ['name' => 'SUBUH', 'field' => 'subuh'],
                            ['name' => 'TERBIT', 'field' => 'terbit'],
                            ['name' => 'DZUHUR', 'field' => 'dzuhur'],
                            ['name' => 'ASHAR', 'field' => 'ashar'],
                            ['name' => 'MAGHRIB', 'field' => 'maghrib'],
                            ['name' => 'ISYA', 'field' => 'isya'],
                        ];
                    @endphp

                    @foreach($jadwalSholat as $sholat)
                        <div class="prayer-item" data-waktu="{{ substr($sholat->waktu, 0, 5) }}">
                            <div class="prayer-name">{{ strtoupper($sholat->nama_sholat) }}</div>
                            <div class="prayer-time">{{ substr($sholat->waktu, 0, 5) }}</div>
                        </div>
                    @endforeach
                </div>

                <!-- RUNNING TEXT TICKER -->
                <div class="ticker-bar">
                    <div class="ticker-badge">
                        <i class="fa-solid fa-bullhorn"></i> INFO MASJID
                    </div>
                    <div class="ticker-marquee">
                        <div class="ticker-content">
                            @php
                                $rawRunning = $settings->running_text ?? 'Selamat Datang di ' . ($settings->nama_aplikasi ?? 'Masjid');
                                $cleanRunning = str_replace(["\r\n", "\r", "\n"], " &nbsp; • &nbsp; ", trim($rawRunning));
                            @endphp
                            {!! $cleanRunning !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- STATUS AUDIO -->
        <div class="stream-status-tag">
            @if($audioEnabled)
                <i class="fa-solid fa-volume-high text-success"></i> Audio Aktif
            @else
                <i class="fa-solid fa-volume-xmark"></i> Hening (Muted)
            @endif
            &bull; 24H Live Feed
        </div>
    @endif

    <!-- SCRIPT JAM & REALTIME CLOCK -->
    <script>
        function updateClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');

            const clockEl = document.getElementById('digitalClock');
            if (clockEl) {
                clockEl.innerHTML = `${hours}:${minutes}<span class="clock-seconds">:${seconds}</span>`;
            }

            const days = ['Ahad', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

            const dateStr = `${days[now.getDay()]}, ${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()}`;
            const dateEl = document.getElementById('gregorianDate');
            if (dateEl) {
                dateEl.textContent = dateStr;
            }

            highlightNextPrayer(hours + ':' + minutes);
        }

        function highlightNextPrayer(currentTimeStr) {
            const prayerItems = document.querySelectorAll('.prayer-item');
            let foundNext = false;

            prayerItems.forEach(item => {
                const prayerTime = item.getAttribute('data-waktu');
                if (!foundNext && prayerTime && prayerTime > currentTimeStr) {
                    item.classList.add('active');
                    foundNext = true;
                } else {
                    item.classList.remove('active');
                }
            });

            // Jika semua waktu sholat hari ini sudah lewat, highlight sholat pertama (Subuh esok)
            if (!foundNext && prayerItems.length > 0) {
                prayerItems[0].classList.add('active');
            }
        }

        setInterval(updateClock, 1000);
        updateClock();
    </script>
</body>

</html>
