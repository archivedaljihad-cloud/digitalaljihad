{{-- resources/views/idul-adha-embed.blade.php --}}
<!DOCTYPE html>
<html lang="id">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
	<title>Jadwal Sholat Idul Adha - Masjid Al-Ikhlas</title>
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
	<link rel="stylesheet" href="{{ asset('css/display-theme.css') }}">

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
			top: 20px;
			font-family: 'Amiri', serif;
			font-size: 5rem;
			opacity: 0.85;
			z-index: 0;
			user-select: none;
			background: linear-gradient(to right, #ffd700, #ffffff);
			-webkit-background-clip: text;
			-webkit-text-fill-color: transparent;
			pointer-events: none;
			text-shadow: 3px 3px 0 rgba(0, 0, 0, 0.4);
		}

		.kaligrafi-allah {
			right: 30px;
		}

		.kaligrafi-muhammad {
			left: 30px;
		}

		.container {
			position: relative;
			z-index: 1;
			height: 100vh;
			width: 100%;
			display: flex;
			flex-direction: column;
			padding: 10px 25px 85px 25px;
			box-sizing: border-box;
			justify-content: space-between;
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
             2px  2px 0 #ffffff,
            -2px  2px 0 #ffffff,
             2px -2px 0 #ffffff,
            -2px -2px 0 #ffffff,
             0    0  12px #ffd700,
             0    0  25px #ffd700,
             0    0  40px rgba(255, 170, 0, 0.06) !important;
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

		.main-content {
			flex: 1;
			display: flex;
			justify-content: center;
			align-items: center;
			min-height: 0;
			margin: 10px 0;
		}

		/* KARTU KACA MELAYANG (FLOATING GLASSMORPHISM) */
		.idul-card {
			background: rgba(4, 25, 18, 0.72);
			backdrop-filter: blur(12px);
			-webkit-backdrop-filter: blur(12px);
			border-radius: 24px;
			border: 1.5px solid rgba(255, 215, 0, 0.45);
			box-shadow: 0 15px 45px rgba(0, 0, 0, 0.45);
			width: 100%;
			max-width: 1050px;
			overflow: hidden;
			animation: fadeInUp 0.6s ease-out;
		}

		@keyframes fadeInUp {
			from {
				opacity: 0;
				transform: translateY(30px);
			}

			to {
				opacity: 1;
				transform: translateY(0);
			}
		}

		.idul-header {
			background: rgba(0, 0, 0, 0.25);
			padding: 18px 25px;
			border-bottom: 1px solid rgba(255, 215, 0, 0.4);
			display: flex;
			justify-content: space-between;
			align-items: center;
			gap: 20px;
		}

		.header-left {
			display: flex;
			align-items: center;
			gap: 15px;
		}

		.header-left div {
			text-align: left;
		}

		.header-clock .datetime {
			font-size: 1.2rem;
			padding: 8px 16px;
			background: rgba(0, 0, 0, 0.3);
			border: 1px solid rgba(255, 215, 0, 0.4);
			border-radius: 20px;
			backdrop-filter: blur(5px);
			white-space: nowrap;
		}

		.idul-header i {
			font-size: 2.2rem;
			color: #ffd700;
			margin-bottom: 4px;
			display: block;
			filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.5));
		}

		.idul-header h2 {
			font-size: 2rem;
			font-weight: 700;
			color: #ffd700;
			letter-spacing: 1.5px;
			text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
		}

		.idul-header p {
			font-size: 0.95rem;
			opacity: 0.9;
			margin-top: 3px;
		}

		/* Body 2 Kolom */
		.idul-body {
			padding: 25px;
			display: flex;
			gap: 25px;
			flex-wrap: wrap;
		}

		.info-column,
		.officials-column {
			flex: 1;
			min-width: 250px;
		}

		.section-title {
			display: flex;
			align-items: center;
			gap: 10px;
			margin-bottom: 15px;
			padding-bottom: 8px;
			border-bottom: 2px solid rgba(255, 215, 0, 0.3);
		}

		.section-title i {
			font-size: 1.2rem;
			color: #ffd700;
		}

		.section-title h3 {
			font-size: 1.1rem;
			font-weight: 600;
			color: #ffd700;
			letter-spacing: 1px;
		}

		.info-grid,
		.officials-grid {
			display: flex;
			flex-direction: column;
			gap: 12px;
		}

		.info-item,
		.official-item {
			background: rgba(0, 0, 0, 0.25);
			border-radius: 12px;
			padding: 12px 15px;
			display: flex;
			align-items: center;
			gap: 15px;
			border-left: 4px solid #ffd700;
			border-top: 1px solid rgba(255, 255, 255, 0.08);
			border-right: 1px solid rgba(255, 255, 255, 0.08);
			border-bottom: 1px solid rgba(255, 255, 255, 0.08);
			transition: all 0.3s ease;
		}

		.info-item:hover,
		.official-item:hover {
			background: rgba(0, 0, 0, 0.4);
			transform: translateX(4px);
		}

		.info-item i,
		.official-item i {
			font-size: 1.4rem;
			color: #ffd700;
			width: 35px;
			text-align: center;
		}

		.info-item .label,
		.official-info .label {
			font-size: 0.7rem;
			text-transform: uppercase;
			letter-spacing: 1px;
			opacity: 0.7;
			margin-bottom: 3px;
		}

		.info-item .value,
		.official-info .name {
			font-size: 1rem;
			font-weight: 600;
			line-height: 1.3;
		}

		.date-badge {
			background: rgba(255, 215, 0, 0.15);
			border-radius: 20px;
			padding: 8px 15px;
			display: inline-block;
			margin-top: 15px;
			font-size: 0.85rem;
			border: 1px solid rgba(255, 215, 0, 0.3);
			text-align: center;
			width: 100%;
			backdrop-filter: blur(5px);
		}

		.date-badge i {
			margin-right: 6px;
			color: #ffd700;
		}

		/* Bagian Bawah (Running Text & Footer) - Transparan Total Tanpa Kotak */
		.bottom-section {
			flex-shrink: 0;
			width: 100%;
		}

		.running-text-container,
		.footer {
			background: transparent !important;
			background-color: transparent !important;
			background-image: none !important;
			border: none !important;
			box-shadow: none !important;
			backdrop-filter: none !important;
			-webkit-backdrop-filter: none !important;
		}

		.running-text-container {
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
			color: #ffffff;
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
			font-size: 0.8rem;
			color: rgba(255, 255, 255, 0.95);
			text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.9);
			flex-shrink: 0;
		}

		.empty-state {
			text-align: center;
			padding: 50px;
			width: 100%;
		}

		.empty-state i {
			font-size: 3.5rem;
			color: rgba(255, 215, 0, 0.5);
			margin-bottom: 12px;
			display: block;
		}
	</style>

	{{-- Posisikan include di bawah agar menang menimpa style bawaan --}}
	@include('partials.display-theme')
</head>

<body>
	<div class="display-background"></div>
	<div class="display-overlay"></div>
		@include('partials.medallion-header')

		<div class="container">
			<div class="header">
				<h1>{{ $settings['nama_aplikasi'] ?? 'Masjid Al-Jihad' }}</h1>
				<h3 class="sub-header">SISTEM INFORMASI DIGITAL</h3>
			</div>

			<div class="main-content">
				@if(isset($idulAdha) && $idulAdha)
				<div class="idul-card">
					<div class="idul-header">
						<div class="header-left">
							<i class="fas fa-kaaba"></i>
							<div>
								<h2>Sholat Idul Adha</h2>
								<p>{{ $idulAdha->tahun }} M / {{ $idulAdha->tahun - 1 }} H</p>
							</div>
						</div>
						<div class="header-clock">
							<div class="datetime" id="datetime"></div>
						</div>
					</div>
					<div class="idul-body">
						<!-- KOLOM KIRI: Informasi Tanggal & Waktu -->
						<div class="info-column">
							<div class="section-title">
								<i class="fas fa-info-circle"></i>
								<h3>Informasi Sholat</h3>
							</div>
							<div class="info-grid">
								<div class="info-item">
									<i class="fas fa-calendar-alt"></i>
									<div>
										<div class="label">Hari & Tanggal</div>
										<div class="value" id="formattedDate">
											@php
											setlocale(LC_TIME, 'id_ID', 'Indonesian');
											\Carbon\Carbon::setLocale('id');
											$tanggal = \Carbon\Carbon::parse($idulAdha->tanggal);
											@endphp
											{{ $tanggal->translatedFormat('l') }}, {{ $tanggal->translatedFormat('d') }}
											{{ $tanggal->translatedFormat('F') }} {{ $tanggal->translatedFormat('Y') }}
										</div>
									</div>
								</div>
								<div class="info-item">
									<i class="fas fa-clock"></i>
									<div>
										<div class="label">Waktu Pelaksanaan</div>
										<div class="value">{{ \Carbon\Carbon::parse($idulAdha->waktu)->format('H:i') }} WIB</div>
									</div>
								</div>
							</div>
							@if($idulAdha->keterangan)
							<div class="date-badge">
								<i class="fas fa-info-circle"></i> {{ $idulAdha->keterangan }}
							</div>
							@endif
						</div>

						<!-- KOLOM KANAN: Petugas Sholat -->
						<div class="officials-column">
							<div class="section-title">
								<i class="fas fa-users"></i>
								<h3>Petugas Sholat</h3>
							</div>
							<div class="officials-grid">
								<div class="official-item">
									<i class="fas fa-user"></i>
									<div class="official-info">
										<div class="label">Imam</div>
										<div class="name">{{ $idulAdha->imam ?? 'Belum Ditentukan' }}</div>
									</div>
								</div>
								<div class="official-item">
									<i class="fas fa-book"></i>
									<div class="official-info">
										<div class="label">Khatib</div>
										<div class="name">{{ $idulAdha->khatib ?? 'Belum Ditentukan' }}</div>
									</div>
								</div>
								<div class="official-item">
									<i class="fas fa-microphone-alt"></i>
									<div class="official-info">
										<div class="label">Muadzin</div>
										<div class="name">{{ $idulAdha->muadzin ?? 'Belum Ditentukan' }}</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				@else
				<div class="idul-card">
					<div class="idul-header">
						<div class="header-left">
							<i class="fas fa-kaaba"></i>
							<div>
								<h2>Sholat Idul Adha</h2>
							</div>
						</div>
						<div class="header-clock">
							<div class="datetime" id="datetime"></div>
						</div>
					</div>
					<div class="empty-state">
						<i class="fas fa-calendar-times"></i>
						<h3>Belum Ada Jadwal</h3>
						<p>Jadwal sholat Idul Adha akan diumumkan kemudian</p>
					</div>
				</div>
				@endif
			</div>

	          @include('partials.bottom-section')	
		</div>
	</div>

	<script>
		const hariIndo = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
		const bulanIndo = [
			'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
			'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
		];

		function updateDateTime() {
			const now = new Date();
			const dtEl = document.getElementById('datetime');
			if (dtEl) {
				dtEl.innerHTML = typeof getStandardMasjidDateTime === 'function'
					? getStandardMasjidDateTime(now, true)
					: now.toLocaleString('id-ID');
			}
		}

		updateDateTime();
		setInterval(updateDateTime, 1000);
	</script>
<!-- SCRIPT UNTUK BACKGROUND SLIDESHOW (EFEK KEN BURNS / ZOOM & PAN) -->
	<script>
		// URUTAN GAMBAR BACKGROUND (Silakan sesuaikan urutan dan daftarnya)
		const backgroundImages = [
			"{{ asset('image/display/background/BG1.png') }}",
			"{{ asset('image/display/background/BG2.png') }}",
			"{{ asset('image/display/background/BG3.png') }}",
			"{{ asset('image/display/background/BG4.png') }}",
			"{{ asset('image/display/background/BG5.png') }}",
			"{{ asset('image/display/background/BG6.png') }}",
			"{{ asset('image/display/background/BG7.png') }}"
		];

		let currentBgIndex = 0;
		const bgContainer = document.querySelector('.display-background');

		if (backgroundImages.length > 0 && bgContainer) {
			// 1. Pastikan kontainer menahan pergerakan gambar
			bgContainer.style.setProperty('overflow', 'hidden', 'important');
			bgContainer.style.setProperty('position', 'absolute', 'important');
			bgContainer.style.setProperty('z-index', '-1', 'important');

			// Kumpulan arah pergerakan agar tidak membosankan (kiri atas, kanan bawah, dll)
			const movements = [
				'scale(1.15) translate(-1%, -1%)',
				'scale(1.15) translate(1%, 1%)',
				'scale(1.15) translate(-1%, 1%)',
				'scale(1.15) translate(1%, -1%)',
				'scale(1.15) translate(0%, 0%)' // Hanya zoom lurus ke tengah
			];

			// 2. Fungsi pembuat lapisan gambar
			function createBgElement(imageUrl) {
				const div = document.createElement('div');
				div.style.position = 'absolute';
				div.style.top = '0';
				div.style.left = '0';
				div.style.width = '100%';
				div.style.height = '100%';
				div.style.backgroundImage = `url('${imageUrl}')`;
				div.style.backgroundSize = 'cover';
				div.style.backgroundPosition = 'center';
				div.style.backgroundRepeat = 'no-repeat';
				
				// Memulai dari transparan dan ukuran normal (tidak dizoom)
				div.style.opacity = '0';
				div.style.transform = 'scale(1)';
				
				// Animasi Ganda: Opacity (memudar 2 detik), Transform (bergerak/zoom 12 detik)
				div.style.transition = 'opacity 2s ease-in-out, transform 12s linear'; 
				return div;
			}

			// 3. Pasang gambar pertama
			let currentBg = createBgElement(backgroundImages[0]);
			bgContainer.appendChild(currentBg);

			// Beri sedikit jeda agar browser siap menjalankan animasi gambar pertama
			setTimeout(() => {
				currentBg.style.opacity = '1';
				currentBg.style.transform = movements[0];
			}, 50);

			// 4. Fungsi eksekusi Ken Burns dan Crossfade
			function changeBackground() {
				currentBgIndex = (currentBgIndex + 1) % backgroundImages.length;
				
				// Siapkan gambar baru
				const nextBg = createBgElement(backgroundImages[currentBgIndex]);
				bgContainer.appendChild(nextBg);

				// Pilih arah pergerakan secara bergantian
				const randomMove = movements[currentBgIndex % movements.length];

				// Mulai animasikan gambar baru
				setTimeout(() => {
					nextBg.style.opacity = '1'; // Muncul perlahan
					nextBg.style.transform = randomMove; // Bergerak perlahan
				}, 50);

				// Pudarkan gambar lama
				const oldBg = currentBg;
				oldBg.style.opacity = '0';

				// Hapus gambar lama dari memori setelah benar-benar pudar (2 detik)
				setTimeout(() => {
					if (oldBg && oldBg.parentNode) {
						oldBg.parentNode.removeChild(oldBg);
					}
				}, 2000);

				currentBg = nextBg;
			}

			// Waktu pergantian gambar (10000 = 10 detik)
			setInterval(changeBackground, 10000); 
		}
	</script>
</body>

</html>