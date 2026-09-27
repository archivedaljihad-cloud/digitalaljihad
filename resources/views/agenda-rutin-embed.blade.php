<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Kegiatan Rutin Masjid - {{ $settings->nama_aplikasi ?? "Masjid Jami' Al Jihad" }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --gold-primary: #d4af37;
            --gold-light: #f7e7a9;
            --gold-dark: #aa820a;
            --emerald-dark: #021a11;
            --emerald-card: #04291b;
            --emerald-light: #064e3b;
            --text-gold: #ffd700;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: radial-gradient(circle at center, #063323 0%, #031c13 55%, #010f0a 100%);
            color: #ffffff;
            width: 100vw;
            height: 100vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        /* Background Islamic Pattern */
        .bg-pattern {
            position: absolute;
            inset: 0;
            background-image: radial-gradient(rgba(212, 175, 55, 0.08) 1.5px, transparent 1.5px);
            background-size: 28px 28px;
            opacity: 0.8;
            pointer-events: none;
            z-index: 1;
        }

        /* Top Header */
        .top-header {
            position: relative;
            z-index: 10;
            height: 85px;
            padding: 0 45px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: linear-gradient(180deg, rgba(0, 0, 0, 0.75) 0%, rgba(0, 0, 0, 0) 100%);
            border-bottom: 1px solid rgba(212, 175, 55, 0.25);
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .mosque-icon-badge {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: rgba(212, 175, 55, 0.15);
            border: 1.5px solid var(--gold-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--gold-light);
            font-size: 22px;
            box-shadow: 0 0 15px rgba(212, 175, 55, 0.3);
        }

        .mosque-title h1 {
            font-size: 1.35rem;
            font-weight: 800;
            letter-spacing: 0.8px;
            color: #ffffff;
            text-transform: uppercase;
            text-shadow: 0 2px 4px rgba(0,0,0,0.8);
        }

        .mosque-title p {
            font-size: 0.82rem;
            color: var(--gold-light);
            font-weight: 500;
            letter-spacing: 0.5px;
        }

        .header-center {
            text-align: center;
        }

        .section-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 20px;
            border-radius: 30px;
            background: rgba(212, 175, 55, 0.15);
            border: 1px solid rgba(212, 175, 55, 0.45);
            font-size: 0.88rem;
            font-weight: 700;
            color: #ffd700;
            letter-spacing: 1px;
            text-transform: uppercase;
            box-shadow: 0 4px 15px rgba(0,0,0,0.4);
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .date-box {
            text-align: right;
            line-height: 1.25;
        }

        .date-masehi {
            font-size: 0.95rem;
            font-weight: 700;
            color: #ffffff;
        }

        .date-hijri {
            font-size: 0.82rem;
            color: var(--gold-light);
            font-weight: 500;
        }

        .clock-pill {
            background: rgba(0, 0, 0, 0.5);
            border: 1.5px solid var(--gold-primary);
            border-radius: 12px;
            padding: 6px 16px;
            font-size: 1.35rem;
            font-weight: 800;
            color: #ffffff;
            font-family: monospace;
            letter-spacing: 1.5px;
            box-shadow: 0 0 12px rgba(212, 175, 55, 0.25);
        }

        /* Main Content Grid */
        .main-stage {
            position: relative;
            z-index: 10;
            flex: 1;
            padding: 24px 45px 20px 45px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .cards-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 22px;
            width: 100%;
            height: 100%;
            max-height: 72vh;
        }

        /* Agenda Card */
        .agenda-card {
            background: linear-gradient(170deg, rgba(4, 41, 27, 0.92) 0%, rgba(2, 26, 17, 0.95) 100%);
            border: 1.5px solid rgba(212, 175, 55, 0.35);
            border-radius: 20px;
            padding: 22px 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }

        /* Highlight Hari Ini (Aura Glowing) */
        .agenda-card.today-active {
            border: 2px solid #ffd700 !important;
            box-shadow: 0 0 25px rgba(255, 215, 0, 0.45), inset 0 0 15px rgba(255, 215, 0, 0.15) !important;
            background: linear-gradient(170deg, rgba(8, 60, 40, 0.96) 0%, rgba(3, 35, 23, 0.98) 100%);
            transform: translateY(-4px);
        }

        .today-floating-badge {
            position: absolute;
            top: 12px;
            right: 12px;
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: #000;
            font-size: 10px;
            font-weight: 800;
            padding: 3px 10px;
            border-radius: 20px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            box-shadow: 0 2px 8px rgba(0,0,0,0.4);
            animation: pulseBadge 1.8s infinite;
        }

        @keyframes pulseBadge {
            0% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.05); opacity: 0.85; }
            100% { transform: scale(1); opacity: 1; }
        }

        .card-top {
            position: relative;
        }

        .card-category {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            padding: 4px 12px;
            border-radius: 20px;
            margin-bottom: 12px;
        }

        .cat-yasin { background: rgba(16, 185, 129, 0.2); color: #6ee7b7; border: 1px solid rgba(16, 185, 129, 0.4); }
        .cat-kajian { background: rgba(59, 130, 246, 0.2); color: #93c5fd; border: 1px solid rgba(59, 130, 246, 0.4); }
        .cat-tahsin { background: rgba(245, 158, 11, 0.2); color: #fde68a; border: 1px solid rgba(245, 158, 11, 0.4); }
        .cat-tafsir { background: rgba(168, 85, 247, 0.2); color: #d8b4fe; border: 1px solid rgba(168, 85, 247, 0.4); }

        .card-title {
            font-size: 1.18rem;
            font-weight: 800;
            color: #ffffff;
            line-height: 1.35;
            margin-bottom: 14px;
            min-height: 52px;
        }

        .info-row {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 10px;
            font-size: 0.85rem;
            color: #e2e8f0;
            line-height: 1.4;
        }

        .info-row i {
            color: var(--gold-light);
            font-size: 13px;
            margin-top: 3px;
            flex-shrink: 0;
            width: 16px;
            text-align: center;
        }

        /* Day Pills Box (Tahsin) */
        .day-pills-box {
            background: rgba(0, 0, 0, 0.35);
            border-radius: 12px;
            padding: 8px 10px;
            margin-top: 10px;
            border: 1px solid rgba(212, 175, 55, 0.2);
        }

        .day-pills-title {
            font-size: 10.5px;
            font-weight: 700;
            color: var(--gold-light);
            text-transform: uppercase;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .pills-container {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
        }

        .day-pill {
            font-size: 10.5px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 6px;
            background: rgba(255, 255, 255, 0.1);
            color: #cbd5e1;
        }

        .day-pill.active {
            background: #ffd700;
            color: #000;
            box-shadow: 0 0 8px rgba(255, 215, 0, 0.5);
        }

        .day-pill.today {
            background: #10b981;
            color: #fff;
            animation: pulseToday 1.5s infinite;
        }

        @keyframes pulseToday {
            0% { transform: scale(1); }
            50% { transform: scale(1.08); }
            100% { transform: scale(1); }
        }

        .card-bottom {
            margin-top: 14px;
            padding-top: 12px;
            border-top: 1px solid rgba(212, 175, 55, 0.2);
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.78rem;
            color: #94a3b8;
        }

        .card-bottom .status-badge {
            font-size: 10.5px;
            font-weight: 700;
            color: var(--gold-light);
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* Bottom Ticker */
        .bottom-ticker {
            position: relative;
            z-index: 10;
            height: 40px;
            background: rgba(1, 15, 10, 0.9);
            border-top: 1px solid rgba(212, 175, 55, 0.3);
            display: flex;
            align-items: center;
            padding: 0 45px;
            font-size: 0.82rem;
            color: #cbd5e1;
            justify-content: space-between;
        }

        .ticker-left {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--gold-light);
            font-weight: 600;
        }
    </style>
</head>

<body>
    <div class="bg-pattern"></div>

    <!-- TOP HEADER -->
    <header class="top-header">
        <div class="header-left">
            <div class="mosque-icon-badge">
                <i class="fa-solid fa-mosque"></i>
            </div>
            <div class="mosque-title">
                <h1>{{ $settings->nama_aplikasi ?? "MASJID JAMI' AL JIHAD" }}</h1>
                <p>Digital Signage System</p>
            </div>
        </div>

        <div class="header-center">
            <div class="section-badge">
                <i class="fa-solid fa-calendar-check"></i>
                <span>JADWAL KEGIATAN RUTIN MASJID</span>
                <i class="fa-solid fa-star-and-crescent"></i>
            </div>
        </div>

        <div class="header-right">
            <div class="date-box">
                <div class="date-masehi" id="liveDateMasehi">{{ $now->translatedFormat('l, d F Y') }}</div>
                <div class="date-hijri">Agenda & Majelis Ta'lim Pekan Ini</div>
            </div>
            <div class="clock-pill" id="liveClockDisplay">--:--:--</div>
        </div>
    </header>

    <!-- MAIN 4 CARDS STAGE -->
    <main class="main-stage">
        @php
            $isYasinToday = ($dayOfWeek === 'kamis');
            $isAhadKajianToday = ($dayOfWeek === 'sabtu');
            $tahsinDays = $kegiatan['tahsin']['hari_aktif'] ?? ['senin', 'rabu', 'sabtu'];
            $isTahsinToday = in_array($dayOfWeek, $tahsinDays);
            
            $tafsirPekan = $kegiatan['tafsir']['pekan_aktif'] ?? [1, 3];
            $isTafsirThisWeek = in_array($weekOfMonth, $tafsirPekan);
            $isTafsirToday = ($dayOfWeek === 'ahad' && $isTafsirThisWeek);
        @endphp

        <div class="cards-grid">
            <!-- 1. SURAT YAASIIN 83 AYAT -->
            <div class="agenda-card {{ $isYasinToday ? 'today-active' : '' }}">
                @if($isYasinToday)
                <div class="today-floating-badge"><i class="fa-solid fa-bell mr-1"></i> MALAM INI</div>
                @endif
                <div class="card-top">
                    <span class="card-category cat-yasin"><i class="fa-solid fa-book-quran"></i> Rutin Setiap Pekan</span>
                    <h2 class="card-title">{{ $kegiatan['yasin']['judul'] ?? 'Pembacaan Surat Yaasiin & Tahlil' }}</h2>

                    <div class="info-row">
                        <i class="fa-solid fa-calendar-day"></i>
                        <span><strong>Hari:</strong> Setiap Kamis (Malam Jum'at)</span>
                    </div>
                    <div class="info-row">
                        <i class="fa-solid fa-clock"></i>
                        <span><strong>Waktu:</strong> {{ $kegiatan['yasin']['waktu'] ?? 'Ba\'da Maghrib s/d Isya' }}</span>
                    </div>
                    <div class="info-row">
                        <i class="fa-solid fa-user-tie"></i>
                        <span><strong>Imam:</strong> {{ $kegiatan['yasin']['pembimbing'] ?? 'Imam Rawatib / Asatidz Masjid' }}</span>
                    </div>
                    <div class="info-row">
                        <i class="fa-solid fa-location-dot"></i>
                        <span><strong>Tempat:</strong> {{ $kegiatan['yasin']['lokasi'] ?? 'Ruang Utama Masjid' }}</span>
                    </div>
                </div>

                <div class="card-bottom">
                    <span class="status-badge"><i class="fa-solid fa-users"></i> Terbuka untuk Umum</span>
                    <span>Otomatis di Layar TV</span>
                </div>
            </div>

            <!-- 2. KAJIAN UMUM MALAM AHAD -->
            <div class="agenda-card {{ $isAhadKajianToday ? 'today-active' : '' }}">
                @if($isAhadKajianToday)
                <div class="today-floating-badge"><i class="fa-solid fa-bell mr-1"></i> MALAM INI</div>
                @endif
                <div class="card-top">
                    <span class="card-category cat-kajian"><i class="fa-solid fa-book-reader"></i> Rutin Setiap Pekan</span>
                    <h2 class="card-title">{{ $kegiatan['kajian_ahad']['judul'] ?? 'Kajian Umum Malam Ahad' }}</h2>

                    <div class="info-row">
                        <i class="fa-solid fa-calendar-day"></i>
                        <span><strong>Hari:</strong> Setiap Sabtu (Malam Ahad)</span>
                    </div>
                    <div class="info-row">
                        <i class="fa-solid fa-clock"></i>
                        <span><strong>Waktu:</strong> {{ $kegiatan['kajian_ahad']['waktu'] ?? 'Ba\'da Maghrib s/d Isya' }}</span>
                    </div>
                    <div class="info-row">
                        <i class="fa-solid fa-user-tie"></i>
                        <span><strong>Pemateri:</strong> {{ $kegiatan['kajian_ahad']['pembimbing'] ?? 'Ust. H. Ahmad Sholeh' }}</span>
                    </div>
                    <div class="info-row">
                        <i class="fa-solid fa-book"></i>
                        <span><strong>Kitab:</strong> {{ $kegiatan['kajian_ahad']['keterangan'] ?? 'Bidayatul Hidayah & Fiqih' }}</span>
                    </div>
                </div>

                <div class="card-bottom">
                    <span class="status-badge"><i class="fa-solid fa-circle-question"></i> Sesi Tanya Jawab</span>
                    <span>Ikhwan & Akhwat</span>
                </div>
            </div>

            <!-- 3. TAHSIN AL-QUR'AN (FLEKSIBEL 1-CLICK PICKER) -->
            <div class="agenda-card {{ $isTahsinToday ? 'today-active' : '' }}">
                @if($isTahsinToday)
                <div class="today-floating-badge"><i class="fa-solid fa-bell mr-1"></i> HARI INI</div>
                @endif
                <div class="card-top">
                    <span class="card-category cat-tahsin"><i class="fa-solid fa-quran"></i> Bimbingan Tartil</span>
                    <h2 class="card-title">{{ $kegiatan['tahsin']['judul'] ?? 'Bimbingan Tahsin Al-Qur\'an' }}</h2>

                    <div class="info-row">
                        <i class="fa-solid fa-clock"></i>
                        <span><strong>Waktu:</strong> {{ $kegiatan['tahsin']['waktu'] ?? 'Ba\'da Sholat Isya' }}</span>
                    </div>
                    <div class="info-row">
                        <i class="fa-solid fa-user-tie"></i>
                        <span><strong>Pembina:</strong> {{ $kegiatan['tahsin']['pembimbing'] ?? 'Ust. Pembina Tahsin' }}</span>
                    </div>
                    <div class="info-row">
                        <i class="fa-solid fa-location-dot"></i>
                        <span><strong>Lokasi:</strong> {{ $kegiatan['tahsin']['lokasi'] ?? 'Serambi & Ruang Utama' }}</span>
                    </div>

                    <!-- DAY PILLS JADWAL AKTIF PEKAN INI -->
                    <div class="day-pills-box">
                        <div class="day-pills-title">
                            <span>Jadwal Pekan Ini:</span>
                            @if($isTahsinToday)
                            <span style="color: #6ee7b7;"><i class="fa-solid fa-circle-check"></i> Hari Ini Ada</span>
                            @endif
                        </div>
                        <div class="pills-container">
                            @php
                                $dayLabels = [
                                    'senin' => 'Senin',
                                    'selasa' => 'Selasa',
                                    'rabu' => 'Rabu',
                                    'kamis' => 'Kamis',
                                    'jumat' => 'Jum\'at',
                                    'sabtu' => 'Sabtu',
                                    'ahad' => 'Ahad',
                                ];
                            @endphp
                            @foreach($dayLabels as $dKey => $dName)
                                @php
                                    $isDayActive = in_array($dKey, $tahsinDays);
                                    $isDayCurrent = ($dayOfWeek === $dKey);
                                @endphp
                                @if($isDayActive)
                                    <span class="day-pill {{ $isDayCurrent ? 'today' : 'active' }}">
                                        {{ $dName }}
                                    </span>
                                @endif
                            @endforeach
                            @if(empty($tahsinDays))
                                <span class="day-pill" style="background: rgba(255,255,255,0.05); color: #94a3b8;">Menyesuaikan info asatidz</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card-bottom">
                    <span class="status-badge"><i class="fa-solid fa-graduation-cap"></i> Semua Tingkatan Usia</span>
                    <span>Gratis / Infaq</span>
                </div>
            </div>

            <!-- 4. TAFSIR AL-QUR'AN (2 PEKAN SEKALI) -->
            <div class="agenda-card {{ $isTafsirToday ? 'today-active' : '' }}">
                @if($isTafsirToday)
                <div class="today-floating-badge"><i class="fa-solid fa-bell mr-1"></i> PAGI INI</div>
                @elseif($isTafsirThisWeek)
                <div class="today-floating-badge" style="background: #3b82f6; color: #fff;"><i class="fa-solid fa-calendar-check mr-1"></i> PEKAN INI</div>
                @endif
                <div class="card-top">
                    <span class="card-category cat-tafsir"><i class="fa-solid fa-sun"></i> Tiap 2 Pekan Sekali</span>
                    <h2 class="card-title">{{ $kegiatan['tafsir']['judul'] ?? 'Kajian Tafsir Al-Qur\'an Tematik' }}</h2>

                    <div class="info-row">
                        <i class="fa-solid fa-calendar-day"></i>
                        <span><strong>Jadwal:</strong> Setiap Ahad (Pekan ke-{{ implode(' & ke-', $tafsirPekan) }})</span>
                    </div>
                    <div class="info-row">
                        <i class="fa-solid fa-clock"></i>
                        <span><strong>Waktu:</strong> {{ $kegiatan['tafsir']['waktu'] ?? 'Ba\'da Sholat Subuh' }}</span>
                    </div>
                    <div class="info-row">
                        <i class="fa-solid fa-user-tie"></i>
                        <span><strong>Pemateri:</strong> {{ $kegiatan['tafsir']['pembimbing'] ?? 'Asatidz Dewan Syari\'ah' }}</span>
                    </div>
                    <div class="info-row">
                        <i class="fa-solid fa-utensils"></i>
                        <span><strong>Fasilitas:</strong> {{ $kegiatan['tafsir']['keterangan'] ?? 'Tafsir & Sarapan Bersama' }}</span>
                    </div>
                </div>

                <div class="card-bottom">
                    <span class="status-badge">
                        @if($isTafsirThisWeek)
                        <i class="fa-solid fa-circle-check text-success"></i> Pekan Aktif (Pekan {{ $weekOfMonth }})
                        @else
                        <i class="fa-solid fa-calendar-minus"></i> Istirahat Pekan Ini
                        @endif
                    </span>
                    <span>Keluarga Muslim</span>
                </div>
            </div>
        </div>
    </main>

    <!-- BOTTOM TICKER -->
    <footer class="bottom-ticker">
        <div class="ticker-left">
            <i class="fa-solid fa-circle-info text-warning"></i>
            <span>Mari makmurkan rumah Allah SWT dengan menghadiri majelis ilmu & kegiatan ibadah bersama keluarga tercinta.</span>
        </div>
        <div style="color: #94a3b8; font-size: 0.78rem;">
            <span>Masjid Jami' Al Jihad — Digital Signage System</span>
        </div>
    </footer>

    <script>
        function updateClock() {
            const now = new Date();
            const h = String(now.getHours()).padStart(2, '0');
            const m = String(now.getMinutes()).padStart(2, '0');
            const s = String(now.getSeconds()).padStart(2, '0');
            const clockEl = document.getElementById('liveClockDisplay');
            if (clockEl) clockEl.textContent = `${h}:${m}:${s}`;
        }
        setInterval(updateClock, 1000);
        updateClock();
    </script>
</body>

</html>
