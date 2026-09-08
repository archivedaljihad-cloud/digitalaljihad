<!-- resources/views/pengumuman.blade.php -->
<!DOCTYPE html>
<html lang="id">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Sistem Informasi Masjid - Pengumuman</title>
	<link rel="icon" type="image/x-icon" href="{{ asset($settings['favicon'] ? 'storage/' . $settings['favicon'] : 'favicon.ico') }}?v={{ time() }}">
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

		/* Kaligrafi */
		.kaligrafi {
			position: absolute;
			top: 15px;
			font-family: 'Amiri', serif;
			font-size: 4.5rem;
			opacity: 0.85;
			z-index: 2;
			user-select: none;
			background: linear-gradient(to right, #ffd700, #ffffff);
			-webkit-background-clip: text;
			-webkit-text-fill-color: transparent;
			text-shadow: 3px 3px 0 rgba(0, 0, 0, 0.4);
		}

		.kaligrafi-allah {
			right: 35px;
		}

		.kaligrafi-muhammad {
			left: 35px;
		}



		/* Container Utama */
		.container {
			position: relative;
			z-index: 5;
			height: 100vh;
			width: 100%;
			display: flex;
			flex-direction: column;
			justify-content: space-between;
			padding: 10px 25px 85px 25px;
			box-sizing: border-box;
		}

		/* Header Atas (Nama Masjid & Jam) */
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
			background: rgba(3, 20, 15, 0.65);
			display: inline-block;
			padding: 5px 28px;
			border-radius: 35px;
			margin-top: 4px;
			font-weight: 600;
			letter-spacing: 1px;
			border: 1.5px solid rgba(255, 215, 0, 0.5);
			box-shadow: 0 5px 20px rgba(0, 0, 0, 0.4);
			backdrop-filter: blur(8px);
			-webkit-backdrop-filter: blur(8px);
		}

		/* Main Announcement Card - Floating Glassmorphism di Tengah */
		.announcement-container {
			flex: 1;
			display: flex;
			align-items: center;
			justify-content: center;
			min-height: 0;
			margin: 10px 0;
		}

		.announcement-card {
			background: rgba(10, 30, 25, 0.35);
			backdrop-filter: blur(12px);
			-webkit-backdrop-filter: blur(12px);
			border-radius: 24px;
			padding: 30px 40px;
			border: 1px solid rgba(255, 215, 0, 0.4);
			border-left: 6px solid #ffd700;
			box-shadow: 0 15px 45px rgba(0, 0, 0, 0.4);
			width: 100%;
			max-width: 950px;
			display: flex;
			flex-direction: column;
			justify-content: center;
			animation: fadeIn 0.5s ease;
		}

		@keyframes fadeIn {
			from {
				opacity: 0;
				transform: scale(0.98);
			}

			to {
				opacity: 1;
				transform: scale(1);
			}
		}

		/* Header Card */
		.card-header {
			display: flex;
			justify-content: space-between;
			align-items: center;
			margin-bottom: 20px;
			padding-bottom: 14px;
			border-bottom: 2px solid rgba(255, 215, 0, 0.3);
		}

		.page-title-badge {
			display: flex;
			align-items: center;
			gap: 10px;
			background: rgba(255, 215, 0, 0.15);
			backdrop-filter: blur(8px);
			padding: 6px 20px;
			border-radius: 30px;
			border: 1px solid rgba(255, 215, 0, 0.4);
			box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
		}

		.page-title-badge i {
			font-size: 1.3rem;
			color: #ffd700;
		}

		.page-title-badge span {
			font-size: 1.05rem;
			font-weight: 700;
			color: #ffd700;
			letter-spacing: 1px;
			text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
		}

		.date-badge {
			background: rgba(255, 215, 0, 0.15);
			color: #ffd700;
			padding: 6px 18px;
			border-radius: 30px;
			font-size: 1rem;
			font-weight: 600;
			border: 1px solid rgba(255, 215, 0, 0.3);
			backdrop-filter: blur(5px);
			display: flex;
			align-items: center;
			gap: 8px;
		}

		.date-badge i {
			font-size: 1.1rem;
		}

		/* Announcement Content */
		.announcement-content {
			font-size: 1.8rem;
			line-height: 1.5;
			font-weight: 600;
			color: #ffffff;
			text-align: center;
			word-break: break-word;
			padding: 15px;
			text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
		}

		/* Progress & Indicators */
		.progress-container {
			flex-shrink: 0;
			margin-top: 8px;
			margin-bottom: 8px;
			max-width: 950px;
			margin-left: auto;
			margin-right: auto;
			width: 100%;
		}

		.progress-bar {
			height: 4px;
			background: rgba(255, 255, 255, 0.2);
			border-radius: 4px;
			overflow: hidden;
		}

		.progress-fill {
			height: 100%;
			background: #ffd700;
			width: 0%;
			transition: width linear;
		}

		.indicator-container {
			display: flex;
			justify-content: center;
			gap: 10px;
			margin-top: 6px;
			flex-shrink: 0;
		}

		.indicator-dot {
			width: 8px;
			height: 8px;
			border-radius: 50%;
			background: rgba(255, 255, 255, 0.3);
			transition: all 0.3s ease;
			cursor: pointer;
		}

		.indicator-dot.active {
			background: #ffd700;
			width: 22px;
			border-radius: 10px;
		}

		/* Bagian Bawah (Running Text & Footer) - Transparan Total Tanpa Kotak */
		.bottom-section {
			flex-shrink: 0;
			width: 100%;
			margin-top: auto;
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
			/* Tambahkan !important di baris bawah ini */
			font-size: 1.1rem !important; 
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
			color: #ffd700;
			text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.9);
		}

		/* Responsive */
		@media (max-width: 1024px) {
			.page-title-badge {
				display: none;
			}

			.announcement-content {
				font-size: 1.5rem;
			}
		}
	</style>

	{{-- Posisikan include di bawah agar menang menimpa style --}}
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
			<div class="datetime" id="datetime"></div>
		</div>

		<div class="announcement-container" id="announcementContainer">
			<div class="announcement-card" id="announcementCard">
				<div class="card-header">
					<div class="page-title-badge">
						<i class="fas fa-bullhorn"></i>
						<span>Informasi Kegiatan</span>
					</div>
					<span class="date-badge" id="announcementDate">
						<i class="fas fa-calendar-alt"></i> Memuat...
					</span>
				</div>
				<div class="announcement-content" id="announcementContent">
					Memuat pengumuman...
				</div>
			</div>
		</div>

		<!-- Progress Bar -->
		<div class="progress-container">
			<div class="progress-bar">
				<div class="progress-fill" id="progressFill"></div>
			</div>
			<!-- Indicator Dots -->
			<div class="indicator-container" id="indicatorContainer"></div>
		</div>

		@include('partials.bottom-section')
	</div>

	<script>
		const announcements = @json($pengumuman);
		const displayDuration = 8000;
		let currentIndex = 0;
		let intervalId = null;
		let progressIntervalId = null;

		function updateDateTime() {
			const now = new Date();
			const dt = document.getElementById('datetime');
			if (dt) {
				dt.textContent = typeof getStandardMasjidDateTime === 'function'
					? getStandardMasjidDateTime(now)
					: now.toLocaleString('id-ID');
			}
		}
		updateDateTime();
		setInterval(updateDateTime, 1000);

		function displayAnnouncement(index) {
			if (!announcements.length) {
				document.getElementById('announcementContent').innerHTML = '<i class="fas fa-inbox"></i> Belum ada pengumuman';
				document.getElementById('announcementDate').innerHTML = '<i class="fas fa-calendar-alt"></i> -';
				return;
			}

			const item = announcements[index];
			const date = new Date(item.tanggal);
			const formattedDate = date.toLocaleDateString('id-ID', {
				day: 'numeric',
				month: 'short',
				year: 'numeric'
			});

			const card = document.getElementById('announcementCard');
			card.style.animation = 'none';
			card.offsetHeight;
			card.style.animation = 'fadeIn 0.5s ease';

			document.getElementById('announcementContent').innerHTML = item.isi;
			document.getElementById('announcementDate').innerHTML = `<i class="fas fa-calendar-alt"></i> ${formattedDate}`;

			updateActiveDot(index);
		}

		function updateActiveDot(index) {
			const dots = document.querySelectorAll('.indicator-dot');
			dots.forEach((dot, i) => {
				if (i === index) {
					dot.classList.add('active');
				} else {
					dot.classList.remove('active');
				}
			});
		}

		function createIndicators() {
			const container = document.getElementById('indicatorContainer');
			container.innerHTML = '';

			if (!announcements.length) {
				const dot = document.createElement('div');
				dot.className = 'indicator-dot active';
				container.appendChild(dot);
				return;
			}

			announcements.forEach((_, index) => {
				const dot = document.createElement('div');
				dot.className = 'indicator-dot';
				if (index === currentIndex) dot.classList.add('active');
				dot.addEventListener('click', () => {
					resetProgress();
					currentIndex = index;
					displayAnnouncement(currentIndex);
					startProgress();
				});
				container.appendChild(dot);
			});
		}

		function startProgress() {
			if (progressIntervalId) clearInterval(progressIntervalId);

			const progressFill = document.getElementById('progressFill');
			progressFill.style.width = '0%';
			progressFill.style.transition = 'none';
			progressFill.offsetHeight;
			progressFill.style.transition = `width ${displayDuration}ms linear`;

			setTimeout(() => {
				progressFill.style.width = '100%';
			}, 10);
		}

		function resetProgress() {
			const progressFill = document.getElementById('progressFill');
			progressFill.style.transition = 'none';
			progressFill.style.width = '0%';
			if (progressIntervalId) clearInterval(progressIntervalId);
		}

		function nextAnnouncement() {
			if (!announcements.length) return;
			currentIndex = (currentIndex + 1) % announcements.length;
			displayAnnouncement(currentIndex);
			startProgress();
		}

		function startRotation() {
			if (intervalId) clearInterval(intervalId);

			if (announcements.length > 1) {
				intervalId = setInterval(nextAnnouncement, displayDuration);
				startProgress();
			} else if (announcements.length === 1) {
				const progressFill = document.getElementById('progressFill');
				progressFill.style.width = '100%';
				progressFill.style.background = '#ffd700';
			}
		}

		function init() {
			createIndicators();
			if (announcements.length) {
				displayAnnouncement(0);
				startRotation();
			} else {
				document.getElementById('announcementContent').innerHTML = `
					<div class="no-data">
						<i class="fas fa-inbox"></i>
						<p>Belum ada pengumuman tersedia</p>
					</div>
				`;
				document.getElementById('announcementDate').innerHTML = '<i class="fas fa-calendar-alt"></i> -';
				const progressFill = document.getElementById('progressFill');
				progressFill.style.width = '100%';
				progressFill.style.background = '#6c757d';
			}
		}

		init();
		
@push('styles')
<style>
    /* Memaksa kontainer, header, dan area pengumuman menjadi transparan */
    .container, .header, #announcement-container, .announcement-container {
        background: rgba(0, 0, 0, 0.1) !important;
        background-color: rgba(0, 0, 0, 0.1) !important;
        backdrop-filter: blur(5px);
        -webkit-backdrop-filter: blur(5px);
        border: 1px solid rgba(255, 255, 255, 0.15);
    }
</style>
@endpush

	</script>
	<!-- SCRIPT UNTUK BACKGROUND SLIDESHOW (EFEK BLINDS / TRIVISION) -->
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
		
		// PENGATURAN EFEK BLINDS
		const numBlinds = 10; // Jumlah bilah potongan (Bisa diubah jadi 8, 12, atau 15)
		let blinds = [];

		if (backgroundImages.length > 0 && bgContainer) {
			// 1. Siapkan kontainer utama
			bgContainer.style.setProperty('overflow', 'hidden', 'important');
			bgContainer.style.setProperty('position', 'absolute', 'important');
			bgContainer.style.setProperty('z-index', '-1', 'important');
			bgContainer.style.setProperty('perspective', '1500px', 'important');
			bgContainer.style.setProperty('display', 'flex', 'important');

			// 2. Buat bilah-bilah (slices) gambar
			for (let i = 0; i < numBlinds; i++) {
				const blind = document.createElement('div');
				blind.style.flex = '1';
				blind.style.height = '100%';
				blind.style.position = 'relative';
				blind.style.transformStyle = 'preserve-3d';
				
				// Kecepatan putaran dan efek bergelombang (stagger)
				blind.style.transition = 'transform 1s cubic-bezier(0.4, 0, 0.2, 1)';
				blind.style.transitionDelay = `${i * 0.1}s`; // Jeda putaran antar bilah

				// Rumus agar posisi gambar di tiap potongan tersambung sempurna
				const bgPosX = numBlinds === 1 ? 0 : (i / (numBlinds - 1)) * 100;

				// Fungsi pembuat sisi depan dan belakang untuk tiap bilah
				const createFace = (isFront) => {
					const face = document.createElement('div');
					face.style.position = 'absolute';
					face.style.width = '100%';
					face.style.height = '100%';
					face.style.backfaceVisibility = 'hidden';
					face.style.backgroundSize = '100vw 100vh'; // Kunci agar gambar memenuhi layar
					face.style.backgroundPosition = `${bgPosX}% center`;
					face.style.backgroundRepeat = 'no-repeat';
					if (!isFront) {
						face.style.transform = 'rotateY(180deg)';
					}
					return face;
				};

				const front = createFace(true);
				const back = createFace(false);

				// Pasang gambar pertama di sisi depan
				front.style.backgroundImage = `url('${backgroundImages[0]}')`;

				blind.appendChild(front);
				blind.appendChild(back);
				bgContainer.appendChild(blind);

				// Simpan ke memori array
				blinds.push({ el: blind, front: front, back: back, isFlipped: false });
			}

			// 3. Fungsi memutar bilah-bilah layaknya Trivision
			function changeBackground() {
				currentBgIndex = (currentBgIndex + 1) % backgroundImages.length;
				const nextImgUrl = `url('${backgroundImages[currentBgIndex]}')`;

				blinds.forEach((blind) => {
					if (!blind.isFlipped) {
						// Jika sedang menghadap depan, siapkan gambar baru di belakang, lalu putar!
						blind.back.style.backgroundImage = nextImgUrl;
						blind.el.style.transform = 'rotateY(180deg)';
					} else {
						// Jika sedang menghadap belakang, siapkan gambar baru di depan, lalu putar balik!
						blind.front.style.backgroundImage = nextImgUrl;
						blind.el.style.transform = 'rotateY(0deg)';
					}
					blind.isFlipped = !blind.isFlipped;
				});
			}

			// Waktu pergantian gambar (10000 = 10 detik)
			setInterval(changeBackground, 10000); 
		}
	</script>	
</body>

</html>