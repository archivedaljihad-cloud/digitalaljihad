{{-- resources/views/qris/embed.blade.php --}}
<!DOCTYPE html>
<html lang="id">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
	<title>QRIS - Masjid Al-Jihad</title>
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
	<link rel="stylesheet" href="{{ asset('css/display-theme.css') }}">
	@include('partials.display-theme')
	<style>
		/* Mengecilkan tinggi dan jarak padding pada container utama QRIS */
		.container,
		.main-card,
		.card,
		.content-wrapper {
			padding-top: 0px !important;
			padding-bottom: 0px !important;
			margin-top: 0px !important;
		}

		/* Jika kontainer QRIS memiliki kelas spesifik */
		.qris-container,
		.panel-qris {
			padding: 0px !important;
			margin-top: 25px !important;
		}

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

		/* Kaligrafi Background */
		.kaligrafi {
			position: absolute;
			top: 20px;
			font-family: 'Amiri', serif;
			font-size: 7rem;
			/* <--- UBAH DARI 5rem JADI 7rem AGAR LEBIH BESAR */
			opacity: 0.7;
			/* <--- UBAH DARI 0.2 JADI 0.7 AGAR SANGAT JELAS */
			z-index: 0;
			user-select: none;
			background: linear-gradient(to right, #ffd700, #ffffff);
			-webkit-background-clip: text;
			-webkit-text-fill-color: transparent;
			pointer-events: none;
		}

		.kaligrafi-allah {
			right: 30px;
		}

		.kaligrafi-muhammad {
			left: 30px;
		}

		/* Container Utama - Fullscreen */
		.container {
			position: relative;
			z-index: 1;
			height: 100vh;
			width: 100%;
			display: flex;
			flex-direction: column;
			padding: 10px 25px 85px 25px;
			box-sizing: border-box;
		}

		/* Header - Hanya Nama Masjid di atas */
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

		/* Tampilan Jam & Tanggal Hijriah Card Header */
		.datetime {
			font-size: 1.05rem !important;
			background: rgba(3, 20, 15, 0.72) !important;
			padding: 6px 18px !important;
			border-radius: 14px !important;
			border: 1.5px solid rgba(255, 215, 0, 0.5) !important;
			box-shadow: 0 4px 15px rgba(0, 0, 0, 0.4) !important;
			color: #ffffff !important;
			font-weight: 500;
			display: inline-flex !important;
			flex-direction: column !important;
			align-items: flex-end !important;
			justify-content: center !important;
			text-align: right !important;
			line-height: 1.35 !important;
			white-space: nowrap !important;
			flex-shrink: 0 !important;
		}

		.datetime .dt-date-row {
			display: inline-flex !important;
			align-items: center !important;
			justify-content: flex-end !important;
			font-size: 1.05rem !important;
			font-weight: 600 !important;
		}

		.datetime .dt-sep-time {
			display: none !important;
		}

		.datetime .dt-time-row {
			display: inline-flex !important;
			align-items: center !important;
			justify-content: flex-end !important;
			gap: 6px !important;
			font-size: 1.02rem !important;
			color: #ffffff !important;
			margin-top: 2px !important;
			font-weight: 700 !important;
			text-align: right !important;
		}

		.datetime .dt-time-row::before {
			content: "\f017";
			font-family: "Font Awesome 5 Free";
			font-weight: 900;
			color: #ffd700;
			font-size: 0.95rem;
		}

		/* Main Content - Area konten utama */
		.main-content {
			flex: 1;
			display: flex;
			justify-content: center;
			align-items: center;
			min-height: 0;
			padding: 10px 0;
		}

		/* QRIS Card - Layout 2 Kolom */
		.qris-card {
			background: rgba(4, 25, 18, 0.75);
			backdrop-filter: blur(14px);
			-webkit-backdrop-filter: blur(14px);
			border-radius: 20px;
			border: 1.5px solid rgba(255, 215, 0, 0.45);
			box-shadow: 0 15px 45px rgba(0, 0, 0, 0.45);
			width: 100%;
			max-width: 1320px;
			overflow: hidden;
			transition: transform 0.3s ease, box-shadow 0.3s ease;
			animation: fadeInUp 0.6s ease-out;
		}

		/* Header Card */
		.qris-header {
			background: linear-gradient(135deg, rgba(0, 0, 0, 0.45), rgba(4, 25, 18, 0.6));
			padding: 14px 28px;
			display: flex;
			justify-content: space-between;
			align-items: center;
			border-bottom: 1.5px solid rgba(255, 215, 0, 0.35);
		}

		.qris-header h2 {
			font-size: 2rem;
			font-weight: 700;
			color: #ffd700;
			letter-spacing: 1.5px;
			text-shadow: 0 2px 5px rgba(0, 0, 0, 0.6);
			white-space: nowrap !important;
			margin: 0;
			display: flex;
			align-items: center;
		}

		.qris-header h2 i {
			margin-right: 12px;
			animation: pulse 2s ease infinite;
		}

		@keyframes pulse {

			0%,
			100% {
				transform: scale(1);
			}

			50% {
				transform: scale(1.05);
			}
		}

		/* Body Card - Layout 2 Kolom */
		.qris-body {
			padding: 20px 32px 18px 32px;
			display: flex;
			gap: 32px;
			flex-wrap: nowrap;
			align-items: center;
		}

		/* Kolom Kiri - Informasi Rekening */
		.info-column {
			flex: 1;
			min-width: 250px;
			display: flex;
			flex-direction: column;
			justify-content: center;
		}

		.qris-description {
			background: rgba(3, 22, 16, 0.75);
			border: 1.5px solid rgba(255, 215, 0, 0.4);
			border-radius: 16px;
			padding: 14px 18px;
			margin-bottom: 14px;
			box-shadow: 0 8px 24px rgba(0, 0, 0, 0.35);
			backdrop-filter: blur(8px);
			-webkit-backdrop-filter: blur(8px);
		}

		.qris-main-instruction {
			font-size: 1.08rem;
			line-height: 1.5;
			color: #ffffff;
			margin-bottom: 10px;
			text-align: justify;
			letter-spacing: 0.3px;
		}

		.qris-warning-box {
			background: linear-gradient(135deg, rgba(255, 170, 0, 0.16), rgba(255, 82, 82, 0.18));
			border-left: 4px solid #ffd700;
			border-radius: 10px;
			padding: 8px 12px;
			margin-bottom: 8px;
			display: flex;
			align-items: center;
			gap: 10px;
		}

		.qris-warning-icon {
			color: #ffd700;
			font-size: 1.3rem;
			flex-shrink: 0;
			animation: pulse 2s infinite;
		}

		.qris-warning-text {
			font-size: 1.02rem;
			line-height: 1.45;
			color: #ffffff;
		}

		.qris-warning-alert {
			color: #ff5252;
			font-weight: 800;
			letter-spacing: 0.5px;
			text-shadow: 0 0 10px rgba(255, 82, 82, 0.4);
		}

		.qris-warning-name {
			color: #ffd700;
			font-weight: 800;
			font-size: 1.1rem;
			letter-spacing: 0.5px;
			text-shadow: 0 0 12px rgba(255, 215, 0, 0.4);
			display: inline-block;
			background: rgba(0, 0, 0, 0.45);
			padding: 2px 10px;
			margin-left: 6px;
			border-radius: 6px;
			border: 1px solid rgba(255, 215, 0, 0.4);
			vertical-align: middle;
		}

		.qris-gratitude {
			text-align: center;
			margin-top: 4px;
			padding-top: 6px;
			border-top: 1px dashed rgba(255, 215, 0, 0.3);
			font-family: 'Poppins', sans-serif;
			font-size: 1.15rem;
			font-weight: 700;
			font-style: italic;
			color: #ffd700;
			letter-spacing: 1px;
			text-shadow: 0 2px 8px rgba(0, 0, 0, 0.6), 0 0 15px rgba(255, 215, 0, 0.3);
		}

		/* Bank Info */
		.bank-info {
			background: rgba(0, 0, 0, 0.35);
			border-radius: 14px;
			padding: 14px 18px;
			text-align: left;
			border-left: 5px solid #ffd700;
			margin-bottom: 14px;
		}

		.bank-info h4 {
			color: #ffd700;
			margin-bottom: 10px;
			font-size: 0.98rem;
			display: flex;
			align-items: center;
			gap: 10px;
		}

		.bank-info p {
			margin: 6px 0;
			color: rgba(255, 255, 255, 0.9);
			font-size: 0.88rem;
			display: flex;
			align-items: center;
			gap: 12px;
			padding: 4px 0;
			border-bottom: 1px solid rgba(255, 255, 255, 0.1);
		}

		.bank-info p:last-child {
			border-bottom: none;
		}

		.bank-info i {
			width: 25px;
			color: #ffd700;
			font-size: 0.9rem;
		}

		.bank-info strong {
			color: #ffd700;
			font-weight: 600;
		}

		/* Status Badge */
		.status-badge {
			display: inline-flex;
			align-items: center;
			gap: 8px;
			padding: 7px 20px;
			border-radius: 30px;
			font-size: 0.82rem;
			font-weight: 600;
			background: rgba(0, 230, 118, 0.2);
			color: #00e676;
			border: 1px solid rgba(0, 230, 118, 0.3);
			animation: blink 3s ease infinite;
			width: fit-content;
		}

		@keyframes blink {

			0%,
			100% {
				opacity: 1;
			}

			50% {
				opacity: 0.85;
			}
		}

		/* Kolom Kanan - QRIS Image */
		.qris-column {
			flex: 1;
			min-width: 250px;
			display: flex;
			flex-direction: column;
			align-items: center;
			justify-content: center;
		}

		.qris-image {
			display: flex;
			justify-content: center;
			align-items: center;
			width: 100%;
		}

		.qris-image img {
			max-width: 305px;
			width: 100%;
			height: auto;
			border-radius: 18px;
			box-shadow: 0 10px 30px rgba(0, 0, 0, 0.45);
			border: 3px solid rgba(255, 215, 0, 0.45);
			transition: transform 0.3s ease, box-shadow 0.3s ease;
			cursor: pointer;
			background: white;
			padding: 10px;
		}

		.qris-image img:hover {
			transform: scale(1.02);
			box-shadow: 0 15px 40px rgba(0, 0, 0, 0.5);
			border-color: #ffd700;
		}

		.scan-hint {
			margin-top: 10px;
			font-size: 0.98rem;
			font-weight: 500;
			color: #ffd700;
			display: flex;
			align-items: center;
			gap: 8px;
			text-shadow: 0 2px 4px rgba(0, 0, 0, 0.7);
		}

		/* Footer */
		.footer {
			text-align: center;
			margin-top: 10px;
			padding-top: 8px;
			border-top: 1px solid rgba(255, 255, 255, 0.15);
			font-size: 0.9rem;
			opacity: 0.6;
			flex-shrink: 0;
		}

		/* Loading */
		.loading {
			position: fixed;
			top: 50%;
			left: 50%;
			transform: translate(-50%, -50%);
			background: rgba(0, 0, 0, 0.85);
			backdrop-filter: blur(10px);
			color: white;
			padding: 15px 25px;
			border-radius: 10px;
			z-index: 9999;
			display: none;
			font-size: 0.9rem;
			border-left: 3px solid #ffd700;
		}

		.fade-out {
			animation: fadeOut 0.5s ease forwards;
		}

		@keyframes fadeOut {
			from {
				opacity: 1;
			}

			to {
				opacity: 0;
			}
		}

		/* Membuat kotak utama QRIS menjadi transparan sepenuhnya */
		.card,
		.main-card,
		.content-card,
		.panel {
			background: transparent !important;
			background-color: rgba(0, 0, 0, 0) !important;
			box-shadow: none !important;
		}

		/* MENJINAKKAN RUNNING TEXT AGAR MASUK KE DALAM KOTAK QRIS */
		.qris-card .running-text-container {
			position: relative !important;
			bottom: auto !important;
			left: auto !important;
			z-index: 1 !important;
			background-color: rgba(0, 0, 0, 0.4) !important;
			border-bottom: 1px solid rgba(255, 215, 0, 0.3) !important;
			box-shadow: none !important;
			padding: 5px 0 !important;
			margin-top: 0 !important;
		}

		.qris-card .running-text-content {
			font-size: 1rem !important;
		}
	</style>
</head>

<body>
	<div class="display-background"></div>
	<div class="display-overlay"></div>
		<!-- Medali Kaligrafi 3D Emas Menyala -->
		@include('partials.medallion-header')

		<div class="container" id="mainContainer">
			<!-- Header Atas (Hanya Nama Masjid) -->
			<div class="header">
				<h1>{{ $settings['nama_aplikasi'] ?? 'Masjid Al-Jihad' }}</h1>
				<h3 class="sub-header">SISTEM INFORMASI DIGITAL</h3>
			</div>

			<div class="main-content">
				<div class="qris-card">
					<!-- Header Card dengan Judul & Jam -->
					<div class="qris-header">
						<h2>
							<i class="fas fa-qrcode"></i>
							QRIS INFAQ DAN SEDEKAH
						</h2>
						<div class="datetime" id="datetime"></div>
					</div>

					<div class="qris-body">
						@if(isset($qris) && $qris)
						<!-- KOLOM KIRI: Informasi Rekening -->
						<div class="info-column">
							<div class="qris-description">
								<div class="qris-main-instruction">
									<i class="fas fa-hand-holding-heart" style="color: #ffd700; margin-right: 8px;"></i>
									Bagi siapa saja yang ingin berinfaq atau sedekah secara digital, silakan men-scan QRIS yang ada di area masjid ini.
								</div>
								
								<div class="qris-warning-box">
									<i class="fas fa-exclamation-triangle qris-warning-icon"></i>
									<div class="qris-warning-text">
										Sebelum memasukkan PIN TRANSAKSI, <span class="qris-warning-alert">PASTIKAN !!!</span> QRIS tersebut atas nama :
										<span class="qris-warning-name">{{ !empty($qris->atas_nama) ? $qris->atas_nama : 'DKM Jami Al Jihad' }}</span>
									</div>
								</div>

								<div class="qris-gratitude">
									" Jazakumullah khairan katsiran "
								</div>
							</div>

							@if(!empty($qris->bank) || !empty($qris->nomor_rekening) || !empty($qris->atas_nama))
							<div class="bank-info">
								<h4>
									<i class="fas fa-university"></i>
									Informasi Rekening
								</h4>
								@if(!empty($qris->bank))
								<p>
									<i class="fas fa-building"></i>
									<strong>Bank:</strong> <span style="margin-left: 5px;">{{ $qris->bank }}</span>
								</p>
								@endif
								@if(!empty($qris->nomor_rekening))
								<p>
									<i class="fas fa-credit-card"></i>
									<strong>No. Rekening:</strong> <span style="margin-left: 5px; font-family: monospace; font-size: 1.1rem;">{{ $qris->nomor_rekening }}</span>
								</p>
								@endif
								@if(!empty($qris->atas_nama))
								<p>
									<i class="fas fa-user"></i>
									<strong>Atas Nama:</strong> <span style="margin-left: 5px;">{{ $qris->atas_nama }}</span>
								</p>
								@endif
							</div>
							@endif

							<div class="status-badge">
								<i class="fas fa-check-circle"></i>
								QRIS Aktif - Siap Digunakan
							</div>
						</div>

						<!-- KOLOM KANAN: QRIS Image -->
						<div class="qris-column">
							<div class="qris-image">
								@if(!empty($qris->gambar))
								<img id="qrisImage" src="{{ asset('storage/' . str_replace('storage/', '', $qris->gambar)) }}" alt="QRIS Donasi" onerror="this.onerror=null; this.src='{{ asset('storage/qris/1784990365_QRIS-cGPT.png') }}';">
								@else
								<div style="padding: 20px; background: rgba(0,0,0,0.2); border-radius: 10px; text-align: center; color: #ffd700;">
									<i class="fas fa-exclamation-triangle"></i> Gambar QRIS belum diunggah
								</div>
								@endif
							</div>
							<div class="scan-hint">
								<i class="fas fa-phone-alt"></i>
								<i class="fas fa-qrcode"></i>
								<span>Scan QR Code diatas untuk Infaq dan Sedekah</span>
							</div>
						</div>
						@else
						<div style="text-align: center; padding: 40px; width: 100%;">
							<i class="fas fa-qrcode" style="font-size: 4rem; color: rgba(255,215,0,0.5); margin-bottom: 20px; display: block;"></i>
							<h3 style="color: #ffd700; font-size: 1.2rem;">Belum Ada QRIS Tersedia</h3>
							<p style="color: rgba(255,255,255,0.7); margin-top: 10px; font-size: 0.9rem;">Silakan hubungi admin untuk menambahkan QRIS donasi.</p>
						</div>
						@endif
					</div>
				</div>
			</div>

			<!-- Memanggil Komponen Running Text dan Footer -->
			@include('partials.bottom-section')

		</div>
	</div>

	<div class="loading" id="loading">
		<i class="fas fa-spinner fa-spin"></i> Memperbarui data...
	</div>

	<script>
		// Update datetime
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

		// Auto-refresh untuk update QRIS
		let refreshInterval = 30000;
		let lastTimestamp = null;

		function fadeOutAndRefresh() {
			const container = document.getElementById('mainContainer');
			if (container) {
				container.classList.add('fade-out');
				setTimeout(() => {
					window.location.reload();
				}, 500);
			} else {
				window.location.reload();
			}
		}

		async function checkForUpdates() {
			try {
				const response = await fetch('{{ route("data.timestamp") }}', {
					method: 'GET',
					headers: {
						'Cache-Control': 'no-cache',
						'Pragma': 'no-cache',
						'X-Requested-With': 'XMLHttpRequest'
					}
				});

				if (!response.ok) {
					throw new Error('Network response was not ok');
				}

				const data = await response.json();
				const newTimestamp = data.timestamp;

				if (lastTimestamp && newTimestamp && newTimestamp !== lastTimestamp) {
					fadeOutAndRefresh();
				}

				lastTimestamp = newTimestamp;

			} catch (error) {
				console.error('Error checking for updates:', error);
			}
		}

		@if(isset($qris) && $qris)
		checkForUpdates();
		setInterval(checkForUpdates, refreshInterval);
		@endif

		const qrisImage = document.getElementById('qrisImage');
		if (qrisImage) {
			qrisImage.addEventListener('click', function() {
				this.style.transform = 'scale(1.05)';
				setTimeout(() => {
					this.style.transform = 'scale(1)';
				}, 300);
			});
		}

		document.body.addEventListener('touchmove', function(e) {
			e.preventDefault();
		}, {
			passive: false
		});
	</script>
<!-- SCRIPT UNTUK BACKGROUND SLIDESHOW (EFEK VERTICAL SLIDE / GESER ATAS-BAWAH) -->
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
			// 1. Memastikan kontainer menahan gambar agar tidak bocor keluar TV
			bgContainer.style.setProperty('overflow', 'hidden', 'important');
			bgContainer.style.setProperty('position', 'absolute', 'important');
			bgContainer.style.setProperty('z-index', '-1', 'important');

			// 2. Fungsi pembuat lapisan gambar
			function createBgElement(imageUrl, initialTop) {
				const div = document.createElement('div');
				div.style.position = 'absolute';
				div.style.left = '0';
				div.style.top = initialTop; // Posisi awal (Tengah, Atas, atau Bawah)
				div.style.width = '100%';
				div.style.height = '100%';
				div.style.backgroundImage = `url('${imageUrl}')`;
				div.style.backgroundSize = 'cover';
				div.style.backgroundPosition = 'center';
				div.style.backgroundRepeat = 'no-repeat';
				
				// Pengaturan kecepatan dan gaya geser (1.5 detik dengan efek rem halus di akhir)
				div.style.transition = 'top 1.5s cubic-bezier(0.25, 1, 0.5, 1)'; 
				return div;
			}

			// 3. Tampilkan gambar pertama tepat di tengah layar (0%)
			let currentBg = createBgElement(backgroundImages[0], '0%');
			bgContainer.appendChild(currentBg);

			// 4. Fungsi untuk menggeser gambar secara vertikal
			function changeBackground() {
				currentBgIndex = (currentBgIndex + 1) % backgroundImages.length;
				
				// Siapkan gambar berikutnya tersembunyi di luar layar bagian BAWAH (100%)
				const nextBg = createBgElement(backgroundImages[currentBgIndex], '100%');
				bgContainer.appendChild(nextBg);

				// Pancing sistem browser agar membaca posisi awal sebelum bergerak
				void nextBg.offsetWidth;

				// LAKUKAN PERGESERAN VERTIKAL! 
				// Gambar lama didorong ke ATAS (-100%), gambar baru naik ke TENGAH (0%)
				currentBg.style.top = '-100%';
				nextBg.style.top = '0%';

				// Hapus gambar lama dari memori setelah transisi geser selesai (1.5 detik)
				const oldBg = currentBg;
				setTimeout(() => {
					if (oldBg && oldBg.parentNode) {
						oldBg.parentNode.removeChild(oldBg);
					}
				}, 1500);

				// Jadikan gambar baru sebagai gambar utama saat ini
				currentBg = nextBg;
			}

			// Waktu pergantian gambar (10000 = 10 detik)
			setInterval(changeBackground, 10000); 
		}
	</script>
	<!-- RUNNING TEXT DITEMPEL DI BAWAH GARIS HEADER QRIS -->
					@include('partials.bottom-section')
</body>

</html>