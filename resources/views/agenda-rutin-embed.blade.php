<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Kegiatan Rutin Masjid - {{ $settings->nama_aplikasi ?? "Masjid Jami' Al Jihad" }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&family=Poppins:wght@300;400;500;600;700;800&family=Outfit:wght@400;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Stylesheets Tema Seragam Masjid -->
    <link rel="stylesheet" href="{{ asset('css/display-theme.css') }}">
    <link rel="stylesheet" href="{{ asset('css/partials-theme.css') }}">

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
            font-family: 'Poppins', sans-serif;
            color: var(--text-light, #ffffff);
            height: 100vh;
            width: 100vw;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            overflow: hidden;
            background-color: #041913;
        }

        .container {
            width: 100%;
            max-width: 1720px;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 10px 30px 48px 30px;
            position: relative;
            z-index: 5;
        }

        /* HEADER SERAGAM MASTER MASJID */
        .header {
            text-align: center;
            margin-top: 0;
            margin-bottom: 6px;
            flex-shrink: 0;
            position: relative;
        }

        .schedule-header-section {
            text-align: center;
            flex-shrink: 0;
            margin-top: 8px;
            display: flex;
            justify-content: center;
            width: 100%;
        }

        .title-with-icons {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 16px;
            background: linear-gradient(135deg, rgba(2, 25, 17, 0.9) 0%, rgba(4, 40, 26, 0.85) 100%);
            border: 1.5px solid rgba(255, 215, 0, 0.7);
            border-radius: 35px;
            padding: 5px 28px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.75), 0 0 20px rgba(255, 215, 0, 0.25);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .title-icon-badge {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: rgba(3, 22, 15, 0.95);
            border: 1.5px solid rgba(255, 215, 0, 0.65);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffd700;
            font-size: 1.35rem;
            flex-shrink: 0;
            box-shadow: 0 4px 10px rgba(0,0,0,0.5);
        }

        .title-text-wrap h2 {
            font-size: 1.55rem;
            color: #ffffff !important;
            letter-spacing: 2px;
            font-weight: 800;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.95);
            margin: 0;
            text-transform: uppercase;
        }

        .title-text-wrap p {
            margin: 0;
            font-size: 0.85rem;
            color: #ffd700;
            font-weight: 600;
            letter-spacing: 1px;
        }

        /* Main Content Grid */
        .main-stage {
            position: relative;
            z-index: 10;
            flex: 1;
            padding: 10px 10px 6px 10px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-height: 0;
        }

        .cards-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            width: 100%;
            height: 100%;
            max-height: 65vh;
            align-items: stretch;
        }

        /* Agenda Card */
        .agenda-card {
            background: linear-gradient(170deg, rgba(4, 41, 27, 0.92) 0%, rgba(2, 26, 17, 0.95) 100%);
            border: 1.5px solid rgba(212, 175, 55, 0.35);
            border-radius: 18px;
            padding: 16px 18px 12px 18px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }

        /* Highlight Hari Ini (Aura Glowing) */
        .agenda-card.today-active {
            border: 2px solid #ffd700 !important;
            box-shadow: 0 0 25px rgba(255, 215, 0, 0.5), inset 0 0 15px rgba(255, 215, 0, 0.18) !important;
            background: linear-gradient(170deg, rgba(8, 60, 40, 0.96) 0%, rgba(3, 35, 23, 0.98) 100%);
            transform: translateY(-4px);
        }

        .today-floating-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: #000;
            font-size: 9.5px;
            font-weight: 800;
            padding: 3px 10px;
            border-radius: 20px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            box-shadow: 0 2px 8px rgba(0,0,0,0.5);
            animation: pulseBadge 1.8s infinite;
            z-index: 2;
        }

        @keyframes pulseBadge {
            0% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.06); opacity: 0.9; }
            100% { transform: scale(1); }
        }

        .card-top {
            position: relative;
            display: flex;
            flex-direction: column;
        }

        .card-category-wrap {
            text-align: center;
            margin-bottom: 6px;
        }

        .card-category {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 10.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            padding: 3px 12px;
            border-radius: 20px;
        }

        .cat-yasin { background: rgba(16, 185, 129, 0.2); color: #6ee7b7; border: 1px solid rgba(16, 185, 129, 0.4); }
        .cat-kajian { background: rgba(59, 130, 246, 0.2); color: #93c5fd; border: 1px solid rgba(59, 130, 246, 0.4); }
        .cat-tahsin { background: rgba(245, 158, 11, 0.2); color: #fde68a; border: 1px solid rgba(245, 158, 11, 0.4); }
        .cat-tafsir { background: rgba(168, 85, 247, 0.2); color: #d8b4fe; border: 1px solid rgba(168, 85, 247, 0.4); }

        /* Teks Judul Center */
        .card-title {
            font-size: 1.15rem;
            font-weight: 800;
            color: #ffffff;
            line-height: 1.35;
            margin-bottom: 10px;
            min-height: 44px;
            text-align: center !important;
            display: flex;
            align-items: center;
            justify-content: center;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.8);
        }

        /* ========================================================
           FORMAT 2 BARIS PER ITEM (STACKED CLEAN LAYOUT)
           Baris 1: Label Emas (Hari, Waktu, Imam, Tempat, dll)
           Baris 2: Value Putih Bersih (Indentasi di bawahnya)
           ======================================================== */
        .info-item {
            margin-bottom: 8px;
            display: flex;
            flex-direction: column;
            text-align: left;
        }

        .info-label {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.78rem;
            font-weight: 700;
            color: #ffd700; /* Warna Emas Kuning Berbeda dengan teks di bawahnya */
            text-transform: uppercase;
            letter-spacing: 0.7px;
            margin-bottom: 2px;
        }

        .info-label i {
            color: #ffd700;
            font-size: 11px;
            width: 14px;
            text-align: center;
        }

        .info-value {
            font-size: 0.94rem;
            font-weight: 600;
            color: #ffffff; /* Warna Putih Bersih Kontras */
            line-height: 1.3;
            padding-left: 20px; /* Indentasi rapi di bawah teks label */
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.85);
        }

        /* Day Pills Box (Tahsin) */
        .day-pills-box {
            background: rgba(0, 0, 0, 0.35);
            border-radius: 12px;
            padding: 7px 10px;
            margin-top: 4px;
            border: 1px solid rgba(212, 175, 55, 0.2);
            text-align: left;
        }

        .day-pills-title {
            font-size: 10px;
            font-weight: 700;
            color: var(--gold-light);
            text-transform: uppercase;
            margin-bottom: 5px;
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
            font-size: 10px;
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

        /* Bagian bawah kotak: badge status di-center */
        .card-bottom {
            margin-top: 8px;
            padding-top: 8px;
            border-top: 1px solid rgba(212, 175, 55, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.80rem;
            color: #94a3b8;
            text-align: center;
        }

        .card-bottom .status-badge {
            font-size: 11px;
            font-weight: 700;
            color: var(--gold-light);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        /* Bottom Ticker */
        .bottom-ticker {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 38px;
            z-index: 20;
            background: rgba(2, 16, 11, 0.92);
            border-top: 1px solid rgba(212, 175, 55, 0.4);
            backdrop-filter: blur(10px);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 35px;
            font-size: 0.82rem;
            color: #cbd5e1;
        }

        .ticker-left {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--gold-light);
            font-weight: 600;
        }

        .ticker-right {
            color: #ffffff;
            font-weight: 600;
            font-size: 0.82rem;
            letter-spacing: 0.5px;
        }
    </style>
</head>

<body>
    <div class="display-background"></div>
    <div class="display-overlay"></div>

    <!-- MEDALI KALIGRAFI EMAS 3D (MUHAMMAD & ALLAH) SERAGAM MASJID -->
    <div class="kaligrafi-medallion kaligrafi-muhammad">
        <img src="{{ asset('image/display/medallion/muhammad_3d.png') }}" alt="Kaligrafi Muhammad SAW">
    </div>
    <div class="kaligrafi-medallion kaligrafi-allah">
        <img src="{{ asset('image/display/medallion/allah_3d.png') }}" alt="Kaligrafi Allah SWT">
    </div>

    <div class="container">
        <!-- HEADER SERAGAM MASTER MASJID -->
        <div class="header">
            <h1 id="nama-masjid">{{ $settings->nama_aplikasi ?? "MASJID JAMI' AL JIHAD" }}</h1>
            <h3 class="sub-header" id="sub-header">{{ $settings->sub_header ?? "SISTEM INFORMASI DIGITAL" }}</h3>
            <div class="datetime" id="datetime">Memuat Waktu...</div>

            <div class="schedule-header-section">
                <div class="title-with-icons">
                    <div class="title-icon-badge left-icon"><i class="fas fa-calendar-check"></i></div>
                    <div class="title-text-wrap">
                        <h2>JADWAL KEGIATAN RUTIN MASJID</h2>
                        <p>Agenda Ta'lim & Ibadah Berjamaah Pekanan & Dwi-Mingguan</p>
                    </div>
                    <div class="title-icon-badge right-icon"><i class="fas fa-star-and-crescent"></i></div>
                </div>
            </div>
        </div>

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
                <!-- 1. SURAT YAASIIN 83 AYAT (FORMAT 2 BARIS STACKED) -->
                <div class="agenda-card {{ $isYasinToday ? 'today-active' : '' }}">
                    @if($isYasinToday)
                    <div class="today-floating-badge"><i class="fa-solid fa-bell mr-1"></i> MALAM INI</div>
                    @endif
                    <div class="card-top">
                        <div class="card-category-wrap">
                            <span class="card-category cat-yasin"><i class="fa-solid fa-book-quran"></i> Rutin Setiap Pekan</span>
                        </div>
                        <h2 class="card-title">{{ $kegiatan['yasin']['judul'] ?? 'Pembacaan Surat Yaasiin & Tahlil' }}</h2>

                        <div class="info-item">
                            <div class="info-label"><i class="fa-solid fa-calendar-day"></i> Hari :</div>
                            <div class="info-value">Setiap Kamis (Malam Jum'at)</div>
                        </div>

                        <div class="info-item">
                            <div class="info-label"><i class="fa-solid fa-clock"></i> Waktu :</div>
                            <div class="info-value">{{ $kegiatan['yasin']['waktu'] ?? 'Ba\'da Sholat Maghrib s/d Isya' }}</div>
                        </div>

                        <div class="info-item">
                            <div class="info-label"><i class="fa-solid fa-user-tie"></i> Imam / Pembimbing :</div>
                            <div class="info-value">{{ $kegiatan['yasin']['pembimbing'] ?? 'Ketua / Pengurus DKM Al Jihad' }}</div>
                        </div>

                        <div class="info-item">
                            <div class="info-label"><i class="fa-solid fa-location-dot"></i> Tempat :</div>
                            <div class="info-value">{{ $kegiatan['yasin']['lokasi'] ?? 'Ruang Utama Masjid Jami\' Al Jihad' }}</div>
                        </div>
                    </div>
                    <div class="card-bottom">
                        <span class="status-badge"><i class="fa-solid fa-users"></i> Terbuka untuk Jamaah Umum</span>
                    </div>
                </div>

                <!-- 2. KAJIAN MALAM AHAD (FORMAT 2 BARIS STACKED) -->
                <div class="agenda-card {{ $isAhadKajianToday ? 'today-active' : '' }}">
                    @if($isAhadKajianToday)
                    <div class="today-floating-badge"><i class="fa-solid fa-bell mr-1"></i> MALAM INI</div>
                    @endif
                    <div class="card-top">
                        <div class="card-category-wrap">
                            <span class="card-category cat-kajian"><i class="fa-solid fa-book-reader"></i> Rutin Setiap Pekan</span>
                        </div>
                        <h2 class="card-title">{{ $kegiatan['kajian_ahad']['judul'] ?? 'Kajian Umum Malam Ahad' }}</h2>

                        <div class="info-item">
                            <div class="info-label"><i class="fa-solid fa-calendar-day"></i> Hari :</div>
                            <div class="info-value">Setiap Sabtu (Malam Ahad)</div>
                        </div>

                        <div class="info-item">
                            <div class="info-label"><i class="fa-solid fa-clock"></i> Waktu :</div>
                            <div class="info-value">{{ $kegiatan['kajian_ahad']['waktu'] ?? 'Ba\'da Sholat Maghrib s/d Isya' }}</div>
                        </div>

                        <div class="info-item">
                            <div class="info-label"><i class="fa-solid fa-user-tie"></i> Pemateri :</div>
                            <div class="info-value">{{ $kegiatan['kajian_ahad']['pembimbing'] ?? 'Ust. H. Ahmad Sholeh Al-Hafidz' }}</div>
                        </div>

                        <div class="info-item">
                            <div class="info-label"><i class="fa-solid fa-book-open"></i> Kitab / Tema :</div>
                            <div class="info-value">{{ $kegiatan['kajian_ahad']['keterangan'] ?? 'Kitab Bidayatul Hidayah & Fiqih' }}</div>
                        </div>
                    </div>
                    <div class="card-bottom">
                        <span class="status-badge"><i class="fa-solid fa-circle-question"></i> Sesi Tanya Jawab</span>
                    </div>
                </div>

                <!-- 3. TAHSIN AL-QUR'AN (FORMAT 2 BARIS STACKED) -->
                <div class="agenda-card {{ $isTahsinToday ? 'today-active' : '' }}">
                    @if($isTahsinToday)
                    <div class="today-floating-badge"><i class="fa-solid fa-bell mr-1"></i> HARI INI</div>
                    @endif
                    <div class="card-top">
                        <div class="card-category-wrap">
                            <span class="card-category cat-tahsin"><i class="fa-solid fa-quran"></i> Bimbingan Tartil</span>
                        </div>
                        <h2 class="card-title">{{ $kegiatan['tahsin']['judul'] ?? 'Bimbingan Tahsin Al-Qur\'an' }}</h2>

                        <div class="info-item">
                            <div class="info-label"><i class="fa-solid fa-clock"></i> Waktu :</div>
                            <div class="info-value">{{ $kegiatan['tahsin']['waktu'] ?? 'Ba\'da Sholat Isya (20:00 WIB)' }}</div>
                        </div>

                        <div class="info-item">
                            <div class="info-label"><i class="fa-solid fa-user-tie"></i> Pembina :</div>
                            <div class="info-value">{{ $kegiatan['tahsin']['pembimbing'] ?? 'Ust. Pembina Tahsin Al-Qur\'an' }}</div>
                        </div>

                        <div class="info-item">
                            <div class="info-label"><i class="fa-solid fa-location-dot"></i> Tempat :</div>
                            <div class="info-value">{{ $kegiatan['tahsin']['lokasi'] ?? 'Serambi & Ruang Utama Masjid' }}</div>
                        </div>

                        <div class="day-pills-box">
                            <div class="day-pills-title">
                                <span class="info-label" style="margin-bottom: 0;"><i class="fa-solid fa-calendar-week"></i> Jadwal Pekan Ini :</span>
                                @if($isTahsinToday)
                                <span style="color: #6ee7b7; font-size: 10px; font-weight: 700;"><i class="fa-solid fa-circle-check"></i> Hari Ini Ada</span>
                                @endif
                            </div>
                            <div class="pills-container" style="margin-top: 5px; padding-left: 4px;">
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
                    </div>
                </div>

                <!-- 4. TAFSIR AL-QUR'AN (FORMAT 2 BARIS STACKED) -->
                <div class="agenda-card {{ $isTafsirToday ? 'today-active' : '' }}">
                    @if($isTafsirToday)
                    <div class="today-floating-badge"><i class="fa-solid fa-bell mr-1"></i> PAGI INI</div>
                    @elseif($isTafsirThisWeek)
                    <div class="today-floating-badge" style="background: #3b82f6; color: #fff;"><i class="fa-solid fa-calendar-check mr-1"></i> PEKAN INI</div>
                    @endif
                    <div class="card-top">
                        <div class="card-category-wrap">
                            <span class="card-category cat-tafsir"><i class="fa-solid fa-sun"></i> Tiap 2 Pekan Sekali</span>
                        </div>
                        <h2 class="card-title">{{ $kegiatan['tafsir']['judul'] ?? 'Kajian Tafsir Al-Qur\'an Tematik' }}</h2>

                        <div class="info-item">
                            <div class="info-label"><i class="fa-solid fa-calendar-day"></i> Jadwal :</div>
                            <div class="info-value">Setiap Ahad (Pekan ke-{{ implode(' & ke-', $tafsirPekan) }})</div>
                        </div>

                        <div class="info-item">
                            <div class="info-label"><i class="fa-solid fa-clock"></i> Waktu :</div>
                            <div class="info-value">{{ $kegiatan['tafsir']['waktu'] ?? 'Ba\'da Sholat Subuh (05:15 WIB)' }}</div>
                        </div>

                        <div class="info-item">
                            <div class="info-label"><i class="fa-solid fa-user-tie"></i> Pemateri :</div>
                            <div class="info-value">{{ $kegiatan['tafsir']['pembimbing'] ?? 'Asatidz Dewan Syari\'ah Masjid' }}</div>
                        </div>

                        <div class="info-item">
                            <div class="info-label"><i class="fa-solid fa-utensils"></i> Fasilitas :</div>
                            <div class="info-value">{{ $kegiatan['tafsir']['keterangan'] ?? 'Tafsir Ayat & Sarapan Pagi Bersama' }}</div>
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
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- BOTTOM TICKER -->
    <footer class="bottom-ticker">
        <div class="ticker-left">
            <i class="fa-solid fa-circle-info text-warning"></i>
            <span>Mari makmurkan rumah Allah SWT dengan menghadiri majelis ilmu & kegiatan ibadah bersama keluarga tercinta.</span>
        </div>
        <div class="ticker-right">
            <span>Sistem Informasi Digital — Masjid Jami' Al Jihad</span>
        </div>
    </footer>

    <!-- CORE CLOCK SCRIPT -->
    <script>
        function updateClock() {
            const now = new Date();
            const days = ['Ahad', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            
            const dayName = days[now.getDay()];
            const date = now.getDate();
            const monthName = months[now.getMonth()];
            const year = now.getFullYear();

            const h = String(now.getHours()).padStart(2, '0');
            const m = String(now.getMinutes()).padStart(2, '0');
            const s = String(now.getSeconds()).padStart(2, '0');

            const dtEl = document.getElementById('datetime');
            if (dtEl) {
                dtEl.textContent = `${dayName}, ${date} ${monthName} ${year} • ${h}:${m}:${s} WIB`;
            }
        }
        setInterval(updateClock, 1000);
        updateClock();
    </script>
</body>

</html>
