<!-- resources/views/sholat-jumat.blade.php -->
<!DOCTYPE html>
<html lang="id">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Sistem Informasi Masjid - Jadwal Sholat Jumat</title>
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&display=swap" rel="stylesheet">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
	<link rel="stylesheet" href="{{ asset('css/display-theme.css') }}">
	@include('partials.display-theme')
	<style>
		* {
			margin: 0;
			padding: 0;
			box-sizing: border-box;
		}

		body {
			font-family: 'Poppins', sans-serif;
			color: #ffffff;
			height: 100vh;
			width: 100vw;
			overflow: hidden;
			position: relative;
		}

		.kaligrafi {
			position: absolute;
			top: 15px;
			font-family: 'Amiri', serif;
			font-size: 4.5rem;
			z-index: 2;
			user-select: none;
			background: linear-gradient(to right, #ffd700, #ffffff);
			-webkit-background-clip: text;
			-webkit-text-fill-color: transparent;
			text-shadow: 3px 3px 0 rgba(0, 0, 0, 0.4);
			opacity: 0.85;
		}

		.kaligrafi-allah {
			right: 35px;
		}

		.kaligrafi-muhammad {
			left: 35px;
		}

		.container {
			position: relative;
			z-index: 5;
			height: 100vh;
			width: 100%;
			max-width: 1600px;
			margin: 0 auto;
			display: flex;
			flex-direction: column;
			justify-content: space-between;
			padding: 10px 25px 85px 25px;
		}

		.header {
			text-align: center;
			margin-top: 0;
			margin-bottom: 12px;
			flex-shrink: 0;
			position: relative;
		}

		.header h1 {
			font-family: 'Masking Renta', sans-serif !important;
			font-size: 3.2rem !important;
			line-height: 1.1 !important;
			letter-spacing: 5px !important;
			text-transform: uppercase !important;
			background: none !important;
			-webkit-background-clip: initial !important;
			-webkit-text-fill-color: initial !important;
			color: #0b4f26 !important;
			text-shadow:
				2px 2px 0 #ffffff,
				-2px 2px 0 #ffffff,
				2px -2px 0 #ffffff,
				-2px -2px 0 #ffffff,
				0 0 12px #ffd700,
				0 0 25px #ffd700,
				0 0 40px rgba(255, 170, 0, 0.06) !important;
			filter: none !important;
			margin: 0 0 -15px 0 !important;
			padding: 0 !important;
		}

		.header h3.sub-header {
			font-family: 'Poppins', sans-serif !important;
			font-size: 1.25rem !important;
			font-weight: 500 !important;
			line-height: 1.2 !important;
			letter-spacing: 4px !important;
			color: #ffffff !important;
			text-transform: uppercase !important;
			opacity: 0.95 !important;
			text-shadow: 0 2px 5px rgba(0, 0, 0, 0.8) !important;
			margin-top: 10px !important;
			margin-bottom: 6px !important;
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

		.jumat-header-section {
			text-align: center;
			flex-shrink: 0;
			margin: 5px 0;
		}

		.jumat-header-section i {
			font-size: 3.2rem;
			color: #ffd700;
			margin-bottom: 2px;
			text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
		}

		.jumat-header-section h2 {
			font-size: 2.2rem;
			color: #ffd700;
			letter-spacing: 1.5px;
			font-weight: 700;
			text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
		}

		.jumat-header-section p {
			font-size: 1.1rem;
			color: rgba(255, 255, 255, 0.85);
			margin-top: 2px;
		}

		/* RAMPING, ELEGAN & TERBACA JELAS DARI 15 METER */
		.info-stack {
			display: flex;
			flex-direction: column;
			gap: 10px;
			flex: 1;
			justify-content: center;
			max-width: 860px;
			width: 100%;
			margin: 0 auto;
		}

		.info-row {
			display: flex;
			gap: 14px;
			width: 100%;
		}

		/* Kotak Kiri (Label) */
		.info-box-label {
			flex: 0 0 220px;
			background: rgba(4, 25, 18, 0.72);
			backdrop-filter: blur(12px);
			-webkit-backdrop-filter: blur(12px);
			border-radius: 14px;
			padding: 11px 20px;
			display: flex;
			align-items: center;
			gap: 14px;
			font-size: 1.25rem;
			font-weight: 600;
			color: #ffd700;
			border: 1.5px solid rgba(255, 215, 0, 0.45);
			box-shadow: 0 8px 25px rgba(0, 0, 0, 0.35);
		}

		.info-box-label i {
			font-size: 1.45rem;
			color: #ffd700;
			width: 28px;
			text-align: center;
		}

		/* Kotak Kanan (Nilai / Nama) */
		.info-box-value {
			flex: 1;
			background: rgba(4, 25, 18, 0.72);
			backdrop-filter: blur(12px);
			-webkit-backdrop-filter: blur(12px);
			border-radius: 14px;
			padding: 11px 24px;
			display: flex;
			align-items: center;
			justify-content: space-between;
			border: 1.5px solid rgba(255, 215, 0, 0.45);
			box-shadow: 0 8px 25px rgba(0, 0, 0, 0.35);
		}

		.info-box-value .value {
			font-size: 1.55rem;
			font-weight: 700;
			color: #ffffff;
			text-shadow: 0 2px 5px rgba(0, 0, 0, 0.8);
		}

		.jadwal-mendatang-badge {
			background: #ffd700;
			color: #000000 !important;
			padding: 5px 14px;
			border-radius: 20px;
			font-size: 0.95rem;
			font-weight: 800 !important;
			display: inline-flex;
			align-items: center;
			gap: 6px;
			letter-spacing: 0.5px;
			box-shadow: 0 2px 8px rgba(0, 0, 0, 0.35);
			border: 1px solid rgba(255, 255, 255, 0.4);
		}

		.jadwal-mendatang-badge i {
			color: inherit !important;
		}

		.jadwal-mendatang-badge.badge-hari-ini {
			background: #28a745 !important;
			color: #ffffff !important;
		}

		.bottom-section {
			flex-shrink: 0;
			width: 100%;
			margin-top: auto;
		}

		.running-text-container {
			background: rgba(10, 30, 25, 0.65) !important;
			border-radius: 12px !important;
			padding: 8px 18px;
			margin-bottom: 8px;
			overflow: hidden;
			border: 1px solid rgba(255, 215, 0, 0.35) !important;
			backdrop-filter: blur(10px) !important;
			-webkit-backdrop-filter: blur(10px) !important;
			box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3) !important;
		}

		.running-text-container {
			background: transparent !important;
			border: none !important;
			box-shadow: none !important;
			backdrop-filter: none !important;
			-webkit-backdrop-filter: none !important;
			padding: 6px 15px;
			margin-bottom: 6px;
			overflow: hidden;
		}

		.running-text {
			white-space: nowrap;
			animation: marquee 25s linear infinite;
			font-size: 1.1rem;
			letter-spacing: 0.5px;
			font-weight: 500;
			text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.9);
		}

		.running-text i {
			margin-right: 8px;
			color: #ffd700;
		}

		@keyframes marquee {
			0% {
				transform: translateX(100%);
			}

			100% {
				transform: translateX(-100%);
			}
		}

		.footer {
			text-align: center;
			padding: 6px;
			border: none !important;
			background: transparent !important;
			box-shadow: none !important;
			backdrop-filter: none !important;
			-webkit-backdrop-filter: none !important;
			font-size: 0.8rem;
			color: rgba(255, 255, 255, 0.95);
			text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.9);
		}

		@media (max-width: 1024px) {
			.info-row {
				flex-direction: column;
				gap: 6px;
			}

			.info-box-label {
				flex: none;
				width: 100%;
			}

			.info-box-value {
				width: 100%;
			}
		}

		.no-data {
			text-align: center;
			padding: 50px;
			background: rgba(10, 30, 25, 0.4);
			border-radius: 14px;
			border: 1px solid rgba(255, 215, 0, 0.3);
			max-width: 820px;
			margin: 0 auto;
			width: 100%;
		}

		.no-data i {
			font-size: 3.5rem;
			color: #ffd700;
			opacity: 0.7;
			margin-bottom: 15px;
			display: block;
		}

		.no-data p {
			font-size: 1.3rem;
			opacity: 0.85;
		}

		/* Paksa hapus total background dan border di seluruh bagian bawah */
		.bottom-section,
		.bottom-section div,
		.running-text-container,
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
	<div class="display-background"></div>
	<div class="display-overlay"></div>
	@include('partials.medallion-header')

	<div class="container">
		<div class="header">
			<h1>{{ $settings['nama_aplikasi'] ?? 'Masjid Al-Jihad' }}</h1>
			<h3 class="sub-header">SISTEM INFORMASI DIGITAL</h3>
			<div class="datetime" id="datetime"></div>
		</div>

		<div class="jumat-header-section">
			<i class="fas fa-mosque"></i>
			<h2>Jadwal Sholat Jumat</h2>
			<p>Informasi Imam, Khatib & Muadzin</p>
		</div>

		@php
		$sholatJumat = isset($sholatJumat) ? $sholatJumat : null;
		@endphp

		@if($sholatJumat && isset($sholatJumat->tanggal))
		@php
		$tanggalObj = \Carbon\Carbon::parse($sholatJumat->tanggal);
		$today = \Carbon\Carbon::today('Asia/Jakarta');
		$isToday = $tanggalObj->isToday();
		$isMendatang = $tanggalObj->greaterThan($today);

		$hariMap = [
		'Sunday' => 'Ahad', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
		'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
		];
		$bulanMap = [
		'January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret',
		'April' => 'April', 'May' => 'Mei', 'June' => 'Juni',
		'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September',
		'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'
		];

		$hariInggris = $tanggalObj->format('l');
		$hari = isset($hariMap[$hariInggris]) ? $hariMap[$hariInggris] : $hariInggris;
		$tanggal = $tanggalObj->format('j');
		$bulanInggris = $tanggalObj->format('F');
		$bulan = isset($bulanMap[$bulanInggris]) ? $bulanMap[$bulanInggris] : $bulanInggris;
		$tahun = $tanggalObj->format('Y');
		$formattedDate = "$hari, $tanggal $bulan $tahun";
		@endphp

		<div class="info-stack">
			<div class="info-row">
				<div class="info-box-label">
					<i class="fas fa-calendar-alt"></i> Tanggal
				</div>
				<div class="info-box-value">
					<span class="value">{{ $formattedDate }}</span>
					@if($isToday)
					<span class="jadwal-mendatang-badge badge-hari-ini" style="background: #28a745; color: #ffffff !important;"><i class="fas fa-clock mr-1"></i> Hari Ini</span>
					@elseif($isMendatang)
					<span class="jadwal-mendatang-badge" style="background: #ffd700; color: #000000 !important; font-weight: 800;"><i class="fas fa-calendar-week mr-1" style="color: #000000 !important;"></i> Jumat Mendatang</span>
					@endif
				</div>
			</div>

			<div class="info-row">
				<div class="info-box-label">
					<i class="fas fa-user"></i> Imam
				</div>
				<div class="info-box-value">
					<span class="value">{{ $sholatJumat->imam ?? 'Belum Ditetapkan' }}</span>
				</div>
			</div>

			<div class="info-row">
				<div class="info-box-label">
					<i class="fas fa-book"></i> Khatib
				</div>
				<div class="info-box-value">
					<span class="value">{{ $sholatJumat->khatib ?? 'Belum Ditetapkan' }}</span>
				</div>
			</div>

			<div class="info-row">
				<div class="info-box-label">
					<i class="fas fa-microphone-alt"></i> Muadzin
				</div>
				<div class="info-box-value">
					<span class="value">{{ $sholatJumat->muadzin ?? 'Belum Ditetapkan' }}</span>
				</div>
			</div>
		</div>
		@else
		<div class="no-data">
			<i class="fas fa-calendar-times"></i>
			<p>Belum ada jadwal Sholat Jumat untuk minggu mendatang</p>
		</div>
		@endif

		@include('partials.bottom-section')
				
		</div>
	</div>

	<script>
		function updateDateTime() {
			const now = new Date();
			const datetimeElement = document.getElementById('datetime');
			if (datetimeElement) {
				datetimeElement.innerHTML = typeof getStandardMasjidDateTime === 'function'
					? getStandardMasjidDateTime(now, true)
					: now.toLocaleString('id-ID');
			}
		}
		if (document.getElementById('datetime')) {
			updateDateTime();
			setInterval(updateDateTime, 1000);
		}
	</script>
<!-- SCRIPT UNTUK BACKGROUND SLIDESHOW (VERSI SUPER KUAT) -->
	<script>
		const backgroundImages = [
			"{{ asset('image/display/background/BG1.png') }}",
			"{{ asset('image/display/background/BG2.png') }}",
			"{{ asset('image/display/background/BG3.png') }}",
			"{{ asset('image/display/background/BG4.png') }}",
			"{{ asset('image/display/background/BG5.png') }}",
			"{{ asset('image/display/background/BG6.png') }}",
			"{{ asset('image/display/background/BG7.png') }}",
			"{{ asset('image/display/background/BG8.png') }}",
			"{{ asset('image/display/background/BG9.png') }}",
			"{{ asset('image/display/background/BG10.png') }}",
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