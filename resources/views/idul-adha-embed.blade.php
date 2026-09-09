<!-- resources/views/idul-adha-embed.blade.php -->
<!DOCTYPE html>
<html lang="id">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Sistem Informasi Masjid - Jadwal Sholat Idul Adha</title>
	<link rel="icon" type="image/png" href="{{ asset('img/favicon.png') }}?v={{ time() }}">
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&display=swap" rel="stylesheet">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
	<link rel="stylesheet" href="{{ asset('css/display-theme.css') }}?v={{ time() }}">

	@include('partials.display-theme')

	<style>
		* {
			margin: 0;
			padding: 0;
			box-sizing: border-box;
		}

		body {
			font-family: 'Poppins', sans-serif;
			color: var(--text-light);
			height: 100vh;
			width: 100vw;
			display: flex;
			justify-content: center;
			align-items: center;
			position: relative;
			overflow: hidden;
		}

		.container {
			width: 100%;
			max-width: 1600px;
			height: 100vh;
			display: flex;
			flex-direction: column;
			justify-content: space-between;
			padding: 10px 25px 85px 25px;
			box-sizing: border-box;
			position: relative;
			z-index: 5;
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

		.header .datetime {
			font-size: 1.55rem !important;
			color: #ffffff;
			text-shadow: 0 2px 4px rgba(0, 0, 0, 0.8);
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

		/* SECTION JUDUL DENGAN DUA IKON (NO 1 = SAPI, NO 2 = KAMBING) */
		.schedule-header-section {
			text-align: center;
			flex-shrink: 0;
			margin: 2px 0 10px 0;
		}

		.title-with-icons {
			display: inline-flex;
			align-items: center;
			justify-content: center;
			gap: 22px;
		}

		.title-icon-badge {
			width: 64px;
			height: 64px;
			border-radius: 50%;
			background: rgba(4, 25, 18, 0.78);
			border: 1.5px solid rgba(255, 215, 0, 0.55);
			box-shadow: 0 4px 15px rgba(0, 0, 0, 0.4), 0 0 15px rgba(255, 215, 0, 0.25);
			display: flex;
			align-items: center;
			justify-content: center;
			backdrop-filter: blur(8px);
			-webkit-backdrop-filter: blur(8px);
			flex-shrink: 0;
		}

		.title-icon-badge img {
			width: 42px;
			height: 42px;
			object-fit: contain;
			filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.6));
		}

		.title-text-wrap h2 {
			font-size: 2.2rem;
			color: #ffd700;
			letter-spacing: 1.5px;
			font-weight: 700;
			text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
			margin: 0;
			line-height: 1.15;
		}

		.title-text-wrap p {
			font-size: 1.05rem;
			color: rgba(255, 255, 255, 0.88);
			margin-top: 3px;
			margin-bottom: 0;
		}

		/* LAYOUT KONTEN: FOTO IMAM DI KIRI (KOTAK NO 3) & INFO DI KANAN */
		.schedule-content-layout {
			display: flex;
			align-items: center;
			justify-content: center;
			gap: 24px;
			max-width: 1080px;
			width: 100%;
			margin: 0 auto;
			flex: 1;
		}

		/* KOTAK NO 3: FOTO IMAM RASIO 4:5 */
		.imam-card-box {
			flex: 0 0 270px;
			width: 270px;
			background: rgba(4, 25, 18, 0.75);
			backdrop-filter: blur(12px);
			-webkit-backdrop-filter: blur(12px);
			border-radius: 18px;
			border: 1.5px solid rgba(255, 215, 0, 0.45);
			box-shadow: 0 8px 25px rgba(0, 0, 0, 0.35);
			padding: 8px;
			display: flex;
			flex-direction: column;
			align-self: center;
			box-sizing: border-box;
		}

		.imam-photo-frame {
			position: relative;
			width: 100%;
			aspect-ratio: 4 / 5;
			border-radius: 12px;
			overflow: hidden;
			border: 1px solid rgba(255, 215, 0, 0.3);
			background: #02120b;
		}

		.imam-photo {
			width: 100%;
			height: 100%;
			aspect-ratio: 4 / 5;
			object-fit: cover;
			object-position: center 15%;
			display: block;
		}

		.imam-badge-overlay {
			position: absolute;
			bottom: 0;
			left: 0;
			right: 0;
			background: linear-gradient(0deg, rgba(2, 16, 11, 0.96) 0%, rgba(2, 16, 11, 0.8) 70%, transparent 100%);
			padding: 16px 10px 8px 10px;
			text-align: center;
			display: flex;
			flex-direction: column;
			gap: 3px;
			border-bottom-left-radius: 12px;
			border-bottom-right-radius: 12px;
		}

		.imam-badge-overlay .badge-role {
			font-size: 0.82rem;
			font-weight: 700;
			color: #ffd700;
			letter-spacing: 1.2px;
			text-transform: uppercase;
			display: inline-flex;
			align-items: center;
			justify-content: center;
			gap: 5px;
		}

		.imam-badge-overlay .badge-name {
			font-size: 1.02rem;
			font-weight: 700;
			color: #ffffff;
			line-height: 1.25;
			text-shadow: 0 2px 4px rgba(0, 0, 0, 0.9);
			word-break: break-word;
		}

		/* KANAN: INFO ROWS STACK (PERSIS SEPERTI JUMAT & IDUL FITRI) */
		.info-stack {
			display: flex;
			flex-direction: column;
			gap: 9px;
			flex: 1;
			justify-content: center;
		}

		.info-row {
			display: flex;
			gap: 12px;
			width: 100%;
		}

		.info-box-label {
			flex: 0 0 190px;
			background: rgba(4, 25, 18, 0.72);
			backdrop-filter: blur(12px);
			-webkit-backdrop-filter: blur(12px);
			border-radius: 14px;
			padding: 9px 18px;
			display: flex;
			align-items: center;
			gap: 12px;
			font-size: 1.15rem;
			font-weight: 600;
			color: #ffd700;
			border: 1.5px solid rgba(255, 215, 0, 0.45);
			box-shadow: 0 8px 25px rgba(0, 0, 0, 0.35);
		}

		.info-box-label i {
			font-size: 1.3rem;
			color: #ffd700;
			width: 24px;
			text-align: center;
		}

		.info-box-value {
			flex: 1;
			background: rgba(4, 25, 18, 0.72);
			backdrop-filter: blur(12px);
			-webkit-backdrop-filter: blur(12px);
			border-radius: 14px;
			padding: 9px 20px;
			display: flex;
			align-items: center;
			justify-content: space-between;
			border: 1.5px solid rgba(255, 215, 0, 0.45);
			box-shadow: 0 8px 25px rgba(0, 0, 0, 0.35);
		}

		.info-box-value .value {
			font-size: 1.4rem;
			font-weight: 700;
			color: #ffffff;
			text-shadow: 0 2px 5px rgba(0, 0, 0, 0.8);
		}

		.jadwal-mendatang-badge {
			background: #ffd700;
			color: #000000 !important;
			padding: 4px 12px;
			border-radius: 20px;
			font-size: 0.9rem;
			font-weight: 800 !important;
			display: inline-flex;
			align-items: center;
			gap: 5px;
			letter-spacing: 0.5px;
			box-shadow: 0 2px 8px rgba(0, 0, 0, 0.35);
			border: 1px solid rgba(255, 255, 255, 0.4);
		}

		.jadwal-mendatang-badge.badge-hari-ini {
			background: #28a745 !important;
			color: #ffffff !important;
		}

		.jadwal-mendatang-badge.badge-hijriah {
			background: linear-gradient(135deg, #0b6623, #15803d) !important;
			color: #ffffff !important;
			border: 1px solid rgba(255, 215, 0, 0.6);
		}

		.no-data {
			text-align: center;
			padding: 40px;
			background: rgba(4, 25, 18, 0.72);
			backdrop-filter: blur(12px);
			border-radius: 16px;
			border: 1.5px solid rgba(255, 215, 0, 0.45);
			max-width: 860px;
			margin: 20px auto;
			width: 100%;
		}

		.no-data i {
			font-size: 3rem;
			color: #ffd700;
			margin-bottom: 12px;
		}

		.no-data p {
			font-size: 1.25rem;
			color: #ffffff;
		}
	</style>
</head>

<body>
	<div class="display-background"></div>
	<div class="display-overlay"></div>
	@include('partials.medallion-header')

	<div class="container">
		<!-- HEADER DENGAN LOCK PIXEL SAMA PERSIS SEPERTI JUMAT & UTAMA -->
		<div class="header">
			<h1>{{ $settings['nama_aplikasi'] ?? 'MASJID JAMI\' AL JIHAD' }}</h1>
			<h3 class="sub-header">SISTEM INFORMASI DIGITAL</h3>
			<div class="datetime" id="datetime"></div>
		</div>

		<!-- SECTION JUDUL: IKON 1 (SAPI) DI KIRI, IKON 2 (KAMBING) DI KANAN -->
		<div class="schedule-header-section">
			<div class="title-with-icons">
				<div class="title-icon-badge left-icon" title="Hewan Qurban Sapi">
					<img src="{{ asset('image/icons/sapi.svg') }}" alt="Sapi">
				</div>
				<div class="title-text-wrap">
					<h2>Sholat Idul Adha</h2>
					<p>{{ $idulAdha->tahun ?? now()->year }} M / {{ isset($idulAdha->tahun) ? $idulAdha->tahun - 1 : now()->year - 1 }} H • 10 Dzulhijjah</p>
				</div>
				<div class="title-icon-badge right-icon" title="Hewan Qurban Kambing">
					<img src="{{ asset('image/icons/kambing.svg') }}" alt="Kambing">
				</div>
			</div>
		</div>

		@if(isset($idulAdha) && $idulAdha)
		@php
			$tanggalObj = $idulAdha->tanggal ? \Carbon\Carbon::parse($idulAdha->tanggal) : null;
			$isToday = $tanggalObj ? $tanggalObj->isToday() : false;
			$formattedDate = $tanggalObj ? $tanggalObj->locale('id')->translatedFormat('l, d F Y') : '-';
			$waktuStr = $idulAdha->waktu ? \Carbon\Carbon::parse($idulAdha->waktu)->format('H:i') : '06:30';
		@endphp

		<!-- LAYOUT UTAMA: KOTAK NO 3 (FOTO IMAM) DI KIRI & INFO ROWS DI KANAN -->
		<div class="schedule-content-layout">
			<!-- KOTAK NO 3: FOTO IMAM -->
			<div class="imam-card-box">
				<div class="imam-photo-frame">
					<img src="{{ !empty($idulAdha->foto_imam) ? asset('storage/' . $idulAdha->foto_imam) : asset('image/display/default_imam.jpg') }}" alt="Foto Imam" class="imam-photo">
					<div class="imam-badge-overlay">
						<span class="badge-role"><i class="fas fa-quran mr-1"></i> Imam Sholat</span>
						<span class="badge-name">{{ $idulAdha->imam ?? 'Ustd. Imam Sholat' }}</span>
					</div>
				</div>
			</div>

			<!-- KANAN: INFO ROWS (TANGGAL, WAKTU, IMAM, KHATIB, MUADZIN) -->
			<div class="info-stack">
				<!-- Row 1: Tanggal -->
				<div class="info-row">
					<div class="info-box-label">
						<i class="fas fa-calendar-alt"></i> Tanggal
					</div>
					<div class="info-box-value">
						<span class="value">{{ $formattedDate }}</span>
						@if($isToday)
						<span class="jadwal-mendatang-badge badge-hari-ini"><i class="fas fa-clock mr-1"></i> Hari Ini</span>
						@else
						<span class="jadwal-mendatang-badge badge-hijriah"><i class="fas fa-star-and-crescent mr-1"></i> 10 Dzulhijjah</span>
						@endif
					</div>
				</div>

				<!-- Row 2: Waktu Pelaksanaan -->
				<div class="info-row">
					<div class="info-box-label">
						<i class="fas fa-clock"></i> Waktu
					</div>
					<div class="info-box-value">
						<span class="value">{{ $waktuStr }} WIB</span>
						<span class="jadwal-mendatang-badge" style="background: #ffd700; color: #000000 !important;"><i class="fas fa-sun mr-1"></i> Pagi Hari</span>
					</div>
				</div>

				<!-- Row 3: Imam -->
				<div class="info-row">
					<div class="info-box-label">
						<i class="fas fa-user-tie"></i> Imam
					</div>
					<div class="info-box-value">
						<span class="value">{{ $idulAdha->imam ?? 'Belum Ditetapkan' }}</span>
					</div>
				</div>

				<!-- Row 4: Khatib -->
				<div class="info-row">
					<div class="info-box-label">
						<i class="fas fa-book-reader"></i> Khatib
					</div>
					<div class="info-box-value">
						<span class="value">{{ $idulAdha->khatib ?? 'Belum Ditetapkan' }}</span>
					</div>
				</div>

				<!-- Row 5: Muadzin / Bilal -->
				<div class="info-row">
					<div class="info-box-label">
						<i class="fas fa-microphone-alt"></i> Bilal / Muadzin
					</div>
					<div class="info-box-value">
						<span class="value">{{ $idulAdha->muadzin ?? 'Belum Ditetapkan' }}</span>
					</div>
				</div>
			</div>
		</div>
		@else
		<div class="no-data">
			<i class="fas fa-calendar-times"></i>
			<p>Belum ada jadwal pelaksanaan Sholat Idul Adha yang ditetapkan.</p>
		</div>
		@endif

		@include('partials.bottom-section')
	</div>

	<!-- Script Jam Digital & Tanggal Lock-Pixel Standar Masjid -->
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
		updateDateTime();
		setInterval(updateDateTime, 1000);
	</script>

	<!-- Script Background Slideshow -->
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
		const bgElement = document.querySelector('.display-background') || document.body;

		if (backgroundImages.length > 0 && bgElement) {
			bgElement.style.setProperty('transition', 'background-image 1.5s ease-in-out', 'important');
			bgElement.style.setProperty('background-size', 'cover', 'important');
			bgElement.style.setProperty('background-position', 'center', 'important');
			bgElement.style.setProperty('background-repeat', 'no-repeat', 'important');

			function changeBackground() {
				currentBgIndex = (currentBgIndex + 1) % backgroundImages.length;
				const nextImg = new Image();
				nextImg.src = backgroundImages[currentBgIndex];
				nextImg.onload = function () {
					bgElement.style.backgroundImage = `url('${backgroundImages[currentBgIndex]}')`;
				};
			}
			setInterval(changeBackground, 15000);
		}
	</script>
</body>

</html>