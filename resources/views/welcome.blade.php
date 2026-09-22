<!-- resources/views/welcome.blade.php -->
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Informasi Masjid</title>
    <!-- Favicon Diperbaiki -->
    <link rel="icon" type="image/png" href="{{ !empty($setting['logo']) ? asset('storage/' . str_replace('storage/', '', $setting['logo'])) . '?v=3.0.4' : asset('img/logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}?v=3.0.4">
    <link rel="stylesheet" href="{{ asset('css/display-theme.css') }}?v=3.0.4">

    <!-- Memuat Tema & Background Nabawi -->
    @include('partials.display-theme')

    <style>
        :root {
            --primary-color: #0d6e6e;
            --secondary-color: #ffd700;
            --accent-color: #0a4d68;
            --text-light: #ffffff;
            --text-dark: #333333;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --info-color: #17a2b8;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            color: var(--text-light);
            overflow: hidden;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }

        .kaligrafi {
            position: absolute;
            top: 20px;
            font-family: 'Amiri', serif;
            font-size: 5rem;
            z-index: 2;
            user-select: none;
            background: linear-gradient(to right, #ffd700, #ffffff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 3px 3px 0 rgba(0, 0, 0, 0.4), 6px 6px 0 rgba(0, 0, 0, 0.2);
            opacity: 0.9;
            animation: kaligrafiFade 5s ease infinite;
        }

        @keyframes kaligrafiFade {

            0%,
            100% {
                opacity: 0.9;
            }

            50% {
                opacity: 0.6;
            }
        }

        .kaligrafi-allah {
            right: 40px;
        }

        .kaligrafi-muhammad {
            left: 40px;
        }

        .container {
            width: 100%;
            max-width: 1920px;
            height: 100%;
            display: flex;
            flex-direction: column;
            padding: 10px 25px 85px 25px;
            box-sizing: border-box;
            position: relative;
            z-index: 2;
        }

        .header {
            text-align: center;
            margin-top: 0;
            margin-bottom: 12px;
            position: relative;
            flex-shrink: 0;
        }

        .header h1 {
        font-family: 'Masking Renta', sans-serif !important;
        font-size: 3.2rem !important;
        line-height: 1.1 !important;
        letter-spacing: 5px !important;
        text-transform: uppercase !important;
        background: none !important;
        -webkit-background-clip: initial !important;
        -webkit-text-fill-color: #085a2b !important;
        color: #085a2b !important; 
        text-shadow: 
            1px 1px 0 #ffd700,
            -1px 1px 0 #ffd700,
            1px -1px 0 #ffd700,
            -1px -1px 0 #ffd700,
            2px 2px 0 #000000,
            -2px 2px 0 #000000,
            2px -2px 0 #000000,
            -2px -2px 0 #000000,
            3px 3px 0 #000000,
            -3px 3px 0 #000000,
            3px -3px 0 #000000,
            -3px -3px 0 #000000,
            0 5px 12px rgba(0, 0, 0, 0.95),
            0 0 20px rgba(255, 215, 0, 0.65) !important;
        filter: none !important;
        margin: 0 0 -15px 0 !important;
        padding: 0 !important;
    }

    .header h3.sub-header {
        font-family: 'Poppins', sans-serif !important;
        font-size: 1.25rem !important;
        font-weight: 500 !important;
        letter-spacing: 4px !important;
        color: #ffffff !important;
        text-transform: uppercase !important;
        opacity: 0.95 !important;
        text-shadow: 0 2px 5px rgba(0, 0, 0, 0.8) !important;
        margin-top: 10px !important;
        margin-bottom: 6px !important;
        line-height: 1.2 !important;
        padding: 0 !important;
    }

        .datetime {
            font-size: 1.55rem;
            margin-top: 4px;
            background: rgba(3, 20, 15, 0.65);
            display: inline-block;
            padding: 5px 28px;
            border-radius: 35px;
            font-weight: 600;
            letter-spacing: 1px;
            border: 1.5px solid rgba(255, 215, 0, 0.5);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }

        .running-text {
            font-size: 1.35rem;
            font-weight: 500;
            letter-spacing: 0.5px;
            margin-top: 10px;
            color: #ffffff !important;
            max-width: 85%;
            margin-left: auto;
            margin-right: auto;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.9);
        }

        .main-content {
            display: flex;
            flex: 1;
            gap: 22px;
            flex-direction: row;
            align-items: stretch;
            padding-bottom: 10px;
        }

        /* KARTU KACA MELAYANG (FLOATING GLASSMORPHISM) - Elegan, seimbang & kontras */
        .panel {
            background: rgba(5, 25, 20, 0.65);
            border-radius: 18px;
            padding: 20px;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.45);
            border: 1.5px solid rgba(255, 215, 0, 0.4);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .panel:hover {
            transform: translateY(-4px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5);
            border-color: rgba(255, 215, 0, 0.7);
        }

        .panel h2 {
            font-size: 1.75rem;
            font-weight: 700;
            margin-top: 0;
            margin-bottom: 15px;
            padding-bottom: 8px;
            position: relative;
            color: #ffffff !important;
            display: flex;
            align-items: center;
            gap: 10px;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.95);
        }

        .panel h2 i {
            color: #ffd700;
            filter: drop-shadow(0 0 6px rgba(255, 215, 0, 0.65));
        }

        @keyframes spin {
            0% {
				transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .panel h2::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 50px;
            height: 3px;
            background: var(--secondary-color);
            border-radius: 3px;
        }

        .jadwal-sholat table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 1.25rem;
            border-radius: 12px;
            overflow: hidden;
        }

        .jadwal-sholat th,
        .jadwal-sholat td {
            padding: 10px 12px;
            text-align: left;
        }

        .jadwal-sholat th {
            background: rgba(255, 215, 0, 0.9);
            color: var(--accent-color);
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .jadwal-sholat tr:nth-child(even) {
            background: rgba(255, 255, 255, 0.06);
        }

        .jadwal-sholat tr:nth-child(odd) {
            background: rgba(0, 0, 0, 0.25);
        }

        .jadwal-sholat tr:hover {
            background: rgba(255, 215, 0, 0.2);
        }

        .jadwal-sholat tr.active {
            border: 2px solid var(--success-color);
            background: rgba(40, 167, 69, 0.35);
        }

        .sholat-jumat .card {
            background: rgba(0, 0, 0, 0.35);
            border-radius: 12px;
            padding: 14px 16px;
            margin-bottom: 8px;
            border-left: 5px solid var(--secondary-color);
            border: 1px solid rgba(255, 215, 0, 0.25);
        }

        .sholat-jumat .card p {
            margin: 6px 0;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.2rem;
        }

        .sholat-jumat .card p i {
            color: var(--secondary-color);
        }

        .sholat-jumat .card .date-badge {
            background: var(--primary-color);
            color: var(--text-light);
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .pengumuman .announcement-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .pengumuman .announcement-item {
            background: rgba(0, 0, 0, 0.25);
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .pengumuman .announcement-item i {
            color: var(--secondary-color);
            font-size: 1.1rem;
        }

        .pengumuman .announcement-item p {
            margin: 0;
            font-size: 0.95rem;
            line-height: 1.4;
        }

        .pengumuman .no-announcement {
            display: flex;
            align-items: center;
            gap: 10px;
            color: rgba(255, 255, 255, 0.85);
            padding: 15px;
        }

        .pengumuman .no-announcement i {
            color: var(--secondary-color);
        }

        .keuangan .finance-card {
            background: rgba(0, 0, 0, 0.25);
            border-radius: 10px;
            padding: 12px 15px;
            margin-bottom: 8px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .keuangan .finance-item {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 6px 0;
            font-size: 1.05rem;
        }

        .keuangan .finance-item i.fa-arrow-up {
            color: var(--success-color);
        }

        .keuangan .finance-item i.fa-arrow-down {
            color: var(--danger-color);
        }

        .keuangan .finance-item i.fa-balance-scale {
            color: var(--secondary-color);
        }

        .keuangan .progress-bar {
            width: 100%;
            height: 6px;
            background: rgba(0, 0, 0, 0.4);
            border-radius: 5px;
            overflow: hidden;
            margin-top: 3px;
            margin-bottom: 8px;
        }

        .keuangan .progress-bar .progress {
            height: 100%;
            transition: width 1s ease;
        }

        .keuangan .progress-bar.income .progress {
            background: var(--success-color);
        }

        .keuangan .progress-bar.expense .progress {
            background: var(--danger-color);
        }

        /* Auto-Update Status */
        .auto-update-status {
            position: fixed;
            bottom: 15px;
            left: 15px;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(5px);
            padding: 6px 14px;
            border-radius: 30px;
            font-size: 0.85rem;
            border-left: 3px solid var(--info-color);
            z-index: 1000;
            display: flex;
            align-items: center;
            gap: 8px;
            border: 1px solid rgba(255, 255, 255, 0.15);
        }

        .auto-update-status i {
            color: var(--info-color);
        }

        .auto-update-status .badge {
            background: var(--info-color);
            color: white;
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 0.65rem;
            margin-left: 4px;
        }

        .auto-update-status.active i {
            color: var(--success-color);
        }

        .auto-update-status.active .badge {
            background: var(--success-color);
        }

        .footer {
            text-align: center;
            font-size: 0.85rem;
            margin-top: 10px;
            color: #ffd700;
            padding: 10px;
            background: rgba(0, 0, 0, 0.35);
            border-radius: 8px;
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .running-text,
        footer,
        .footer {
            background: transparent !important;
            background-color: transparent !important;
            border: none !important;
            box-shadow: none !important;
            backdrop-filter: none !important;
            -webkit-backdrop-filter: none !important;
        }
    </style>
</head>

<body>
    <!-- ELEMEN PENTING PEMANGGIL BACKGROUND MASJID NABAWI -->
    <div class="display-background"></div>
    <div class="display-overlay"></div>

    @include('partials.medallion-header')

    <!-- Auto-Update Status -->
    @if($settings->auto_update_jadwal ?? false)
    <div class="auto-update-status active" id="autoUpdateStatus">
        <i class="fas fa-sync-alt fa-spin"></i>
        <span>Auto-Update Aktif</span>
        <span class="badge">{{ $settings->auto_update_city ?? 'Jakarta' }}</span>
    </div>
    @else
    <div class="auto-update-status" id="autoUpdateStatus">
        <i class="fas fa-clock"></i>
        <span>Update Manual</span>
    </div>
    @endif

    <div class="container">
        <div class="header">
            <h1 id="nama-masjid">{{ $settings['nama_aplikasi'] ?? 'Masjid Al-Jihad' }}</h1>
            <h3 class="sub-header">SISTEM INFORMASI DIGITAL</h3>
            <div class="datetime" id="datetime"></div>
            @php
                $rawTextWelcome = $settings['running_text'] ?? null;
                $runningTextListWelcome = [];
                if (!empty($rawTextWelcome)) {
                    $linesWelcome = preg_split('/\r\n|\r|\n/', $rawTextWelcome);
                    foreach ($linesWelcome as $lw) {
                        $tw = trim($lw);
                        if (!empty($tw)) {
                            $runningTextListWelcome[] = $tw;
                        }
                    }
                }
            @endphp
            @if(!empty($runningTextListWelcome))
            <div class="running-text" style="overflow: hidden; white-space: nowrap; position: relative; min-height: 28px;">
                <span id="welcomeRunningText" style="transition: opacity 0.5s ease; display: inline-block;">{!! $runningTextListWelcome[0] !!}</span>
            </div>
            <script>
                (function() {
                    const msgs = {!! json_encode(array_values($runningTextListWelcome)) !!};
                    if (!msgs || msgs.length <= 1) return;
                    let idx = 0;
                    const wEl = document.getElementById('welcomeRunningText');
                    if (!wEl) return;
                    setInterval(() => {
                        wEl.style.opacity = '0';
                        setTimeout(() => {
                            idx = (idx + 1) % msgs.length;
                            wEl.innerHTML = msgs[idx];
                            wEl.style.opacity = '1';
                        }, 500);
                    }, 8000);
                })();
            </script>
            @endif
        </div>
        <div class="main-content">
            <div class="panel jadwal-sholat">
                <h2><i class="fas fa-mosque"></i> Jadwal Sholat</h2>
                <table>
                    <tr>
                        <th>Sholat</th>
                        <th>Waktu</th>
                    </tr>
                    @php
                    $now = \Carbon\Carbon::now('Asia/Jakarta');
                    $currentHour = $now->format('H:i');
                    @endphp
                    @foreach ($jadwalSholat as $jadwal)
                    <tr
                        class="{{ \Carbon\Carbon::parse($jadwal->waktu)->format('H:i') <= $currentHour && \Carbon\Carbon::parse($jadwal->waktu)->addMinutes(30)->format('H:i') >= $currentHour ? 'active' : '' }}">
                        <td>{{ $jadwal->nama_sholat }}</td>
                        <td>{{ \Carbon\Carbon::parse($jadwal->waktu)->format('H:i') }}</td>
                    </tr>
                    @endforeach
                </table>
            </div>
            <div class="panel sholat-jumat">
                <h2><i class="fas fa-pray"></i> Sholat Jumat</h2>
                <div class="card">
                    @if($sholatJumat)
                    <p><i class="fas fa-calendar-alt"></i> <strong>Tanggal:</strong> <span
                            class="date-badge">{{ \Carbon\Carbon::parse($sholatJumat->tanggal)->translatedFormat('d F Y') }}</span>
                    </p>
                    <p><i class="fas fa-user"></i> <strong>Imam:</strong>
                        {{ $sholatJumat->imam ?? 'Belum Ditetapkan' }}
                    </p>
                    <p><i class="fas fa-book"></i> <strong>Khatib:</strong>
                        {{ $sholatJumat->khatib ?? 'Belum Ditetapkan' }}
                    </p>
                    <p><i class="fas fa-microphone"></i> <strong>Muadzin:</strong>
                        {{ $sholatJumat->muadzin ?? 'Belum Ditetapkan' }}
                    </p>
                    @else
                    <p><i class="fas fa-calendar-alt"></i> <strong>Tanggal:</strong> Belum Ditetapkan</p>
                    <p><i class="fas fa-user"></i> <strong>Imam:</strong> Belum Ditetapkan</p>
                    <p><i class="fas fa-book"></i> <strong>Khatib:</strong> Belum Ditetapkan</p>
                    <p><i class="fas fa-microphone"></i> <strong>Muadzin:</strong> Belum Ditetapkan</p>
                    @endif
                </div>
            </div>
            <div class="panel pengumuman">
                <h2><i class="fas fa-bullhorn"></i> Pengumuman</h2>
                @if($pengumuman->isEmpty())
                <div class="no-announcement">
                    <i class="fas fa-info-circle"></i>
                    <p>Tidak ada pengumuman mendatang.</p>
                </div>
                @else
                <ul class="announcement-list">
                    @foreach ($pengumuman as $index => $item)
                    <li class="announcement-item">
                        <i class="fas fa-bullhorn"></i>
                        <p><strong>{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d F Y') }}:</strong>
                            {{ $item->isi }}
                        </p>
                    </li>
                    @endforeach
                </ul>
                @endif
            </div>
            <div class="panel keuangan">
                <h2><i class="fas fa-wallet"></i> Keuangan</h2>
                <div class="finance-card">
                    <div class="finance-item">
                        <i class="fas fa-arrow-up"></i>
                        <p><strong>Total Pemasukan:</strong> Rp
                            {{ number_format($keuanganSummary['total_pemasukan'], 2, ',', '.') }}
                        </p>
                    </div>
                    <div class="progress-bar income">
                        <div class="progress"
                            style="width: {{ $keuanganSummary['total_pemasukan'] > 0 ? min(($keuanganSummary['total_pemasukan'] / ($keuanganSummary['total_pemasukan'] + $keuanganSummary['total_pengeluaran']) * 100), 100) : 0 }}%;">
                        </div>
                    </div>
                    <div class="finance-item">
                        <i class="fas fa-arrow-down"></i>
                        <p><strong>Total Pengeluaran:</strong> Rp
                            {{ number_format($keuanganSummary['total_pengeluaran'], 2, ',', '.') }}
                        </p>
                    </div>
                    <div class="progress-bar expense">
                        <div class="progress"
                            style="width: {{ $keuanganSummary['total_pengeluaran'] > 0 ? min(($keuanganSummary['total_pengeluaran'] / ($keuanganSummary['total_pemasukan'] + $keuanganSummary['total_pengeluaran']) * 100), 100) : 0 }}%;">
                        </div>
                    </div>
                    <div class="finance-item">
                        <i class="fas fa-balance-scale"></i>
                        <p><strong>Saldo:</strong> Rp {{ number_format($keuanganSummary['saldo'], 2, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer">
            @if(!empty($settings['footer']))
            {!! $settings['footer'] !!}
            @else
            © {{ date('Y') }}
            <span class="footer-gold">
                DKM AL JIHAD
            </span>
            @endif
        </div>
    </div>

    <script>
        function updateDateTime() {
            const now = new Date();
            const el = document.getElementById('datetime');
            if (el) {
                el.innerHTML = typeof getStandardMasjidDateTime === 'function'
                    ? getStandardMasjidDateTime(now, true)
                    : now.toLocaleString('id-ID');
            }
        }
        updateDateTime();
        setInterval(updateDateTime, 1000);

        document.addEventListener('DOMContentLoaded', function() {
            let lastTimestamp = null;
            async function checkForUpdates() {
                try {
                    const response = await fetch('{{ route("data.timestamp") }}', {
                        method: 'GET',
                        headers: {
                            'Cache-Control': 'no-cache',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    if (!response.ok) throw new Error('Network response was not ok');
                    const data = await response.json();
                    if (lastTimestamp && data.timestamp && data.timestamp !== lastTimestamp) {
                        window.location.reload();
                    }
                    lastTimestamp = data.timestamp;
                } catch (error) {
                    console.error('Error checking for updates:', error);
                }
            }
            setInterval(checkForUpdates, 30000);
        });
    </script>   

<!-- SCRIPT UNTUK BACKGROUND SLIDESHOW (VERSI SUPER KUAT) -->
	<script>
		const backgroundImages = [
			"{{ asset('image/display/background/BG6.png') }}",
			"{{ asset('image/display/background/BG3.png') }}",
			"{{ asset('image/display/background/BG7.png') }}",
			"{{ asset('image/display/background/BG1.png') }}",
			"{{ asset('image/display/background/BG4.png') }}",
			"{{ asset('image/display/background/BG10.png') }}",
			"{{ asset('image/display/background/BG8.png') }}",
			"{{ asset('image/display/background/BG9.png') }}",
			"{{ asset('image/display/background/BG2.png') }}",
			"{{ asset('image/display/background/BG5.png') }}",
			"{{ asset('image/display/background/BG11.png') }}"
		];

		let currentBgIndex = 0;
		// Mencari div background, jika tidak ditemukan, langsung targetkan seluruh layar (body)
		const bgElement = document.querySelector('.display-background') || document.body;

		if (backgroundImages.length > 0) {
			// Menggunakan setProperty('...', '...', 'important') untuk menjebol CSS bawaan
			bgElement.style.setProperty('transition', 'background-image 1.5s ease-in-out', 'important');
			bgElement.style.setProperty('background-size', 'cover', 'important');
			bgElement.style.setProperty('background-position', 'center', 'important');
			bgElement.style.setProperty('background-repeat', 'no-repeat', 'important');
			
			// Pasang gambar pertama
			bgElement.style.setProperty('background-image', `url('${backgroundImages[0]}')`, 'important');

			// Fungsi putar otomatis
			function changeBackground() {
				currentBgIndex = (currentBgIndex + 1) % backgroundImages.length;
				bgElement.style.setProperty('background-image', `url('${backgroundImages[currentBgIndex]}')`, 'important');
			}

			// Ganti setiap 10 detik
			setInterval(changeBackground, 10000); 
		}
	</script>

</body>

</html>