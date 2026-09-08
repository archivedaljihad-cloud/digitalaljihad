<!-- resources/views/pengumuman.blade.php -->
<!DOCTYPE html>
<html lang="id">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Sistem Informasi Masjid - Informasi Kegiatan & Kajian</title>
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

		/* Container Utama Pixel-Locked */
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
			margin-bottom: 8px;
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

		/* Main Announcement Stage */
		.announcement-container {
			flex: 1;
			display: flex;
			align-items: center;
			justify-content: center;
			min-height: 0;
			width: 100%;
			max-width: 1450px;
			margin: 0 auto;
			padding: 0 15px;
			box-sizing: border-box;
		}

		/* Premiere 2-Column Card (Glassmorphism Elegan) */
		.announcement-card {
			background: rgba(4, 25, 18, 0.76);
			backdrop-filter: blur(14px);
			-webkit-backdrop-filter: blur(14px);
			border-radius: 22px;
			padding: 24px 32px;
			border: 1.5px solid rgba(255, 215, 0, 0.45);
			box-shadow: 0 18px 45px rgba(0, 0, 0, 0.65), inset 0 1px 0 rgba(255, 255, 255, 0.1);
			width: 100%;
			max-height: 58vh;
			display: flex;
			flex-direction: column;
			animation: cardFadeIn 0.6s cubic-bezier(0.25, 1, 0.5, 1);
		}

		@keyframes cardFadeIn {
			from {
				opacity: 0;
				transform: scale(0.97);
			}
			to {
				opacity: 1;
				transform: scale(1);
			}
		}

		/* Card Header */
		.card-header-bar {
			display: flex;
			justify-content: space-between;
			align-items: center;
			margin-bottom: 16px;
			padding-bottom: 12px;
			border-bottom: 1.5px solid rgba(255, 215, 0, 0.3);
			flex-shrink: 0;
		}

		.page-title-badge {
			display: inline-flex;
			align-items: center;
			gap: 9px;
			background: linear-gradient(135deg, rgba(255, 215, 0, 0.22), rgba(0, 230, 118, 0.22));
			padding: 6px 18px;
			border-radius: 30px;
			border: 1px solid rgba(255, 215, 0, 0.5);
			box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
		}

		.page-title-badge i {
			font-size: 1.15rem;
			color: #ffd700;
		}

		.page-title-badge span {
			font-size: 0.98rem;
			font-weight: 700;
			color: #ffd700;
			letter-spacing: 1px;
			text-transform: uppercase;
			text-shadow: 0 1px 3px rgba(0, 0, 0, 0.6);
		}

		.date-badge {
			background: rgba(3, 20, 15, 0.65);
			color: #ffd700;
			padding: 6px 18px;
			border-radius: 30px;
			font-size: 1rem;
			font-weight: 600;
			border: 1px solid rgba(255, 215, 0, 0.35);
			display: inline-flex;
			align-items: center;
			gap: 8px;
			box-shadow: 0 3px 10px rgba(0, 0, 0, 0.3);
		}

		/* Split Layout Body */
		.card-main-body {
			display: flex;
			align-items: center;
			gap: 36px;
			flex: 1;
			min-height: 0;
			overflow: hidden;
		}

		/* Kolom Kiri: Foto Ustadz / Flyer */
		.speaker-col {
			flex: 0 0 280px;
			display: flex;
			flex-direction: column;
			align-items: center;
			justify-content: center;
			position: relative;
		}

		.speaker-frame {
			position: relative;
			width: 230px;
			height: 230px;
			border-radius: 20px;
			padding: 5px;
			background: linear-gradient(135deg, rgba(255, 215, 0, 0.6), rgba(0, 230, 118, 0.3));
			box-shadow: 0 12px 30px rgba(0, 0, 0, 0.7), 0 0 20px rgba(255, 215, 0, 0.2);
			display: flex;
			align-items: center;
			justify-content: center;
			overflow: hidden;
		}

		.speaker-frame img {
			width: 100%;
			height: 100%;
			object-fit: cover;
			border-radius: 16px;
		}

		.speaker-avatar-fallback {
			width: 100%;
			height: 100%;
			background: radial-gradient(circle, rgba(11, 79, 38, 0.8) 0%, rgba(3, 20, 15, 0.95) 100%);
			border-radius: 16px;
			display: flex;
			flex-direction: column;
			align-items: center;
			justify-content: center;
			color: #ffd700;
			gap: 10px;
		}

		.speaker-avatar-fallback i {
			font-size: 4rem;
			filter: drop-shadow(0 4px 10px rgba(0,0,0,0.5));
		}

		.speaker-avatar-fallback span {
			font-size: 0.88rem;
			font-weight: 600;
			letter-spacing: 1px;
			text-transform: uppercase;
			opacity: 0.9;
		}

		/* Kolom Kanan: Rincian Agenda & Materi */
		.details-col {
			flex: 1;
			display: flex;
			flex-direction: column;
			justify-content: center;
			min-width: 0;
			padding-right: 5px;
		}

		.activity-title {
			font-size: 2.15rem;
			font-weight: 700;
			line-height: 1.25;
			color: #ffd700;
			text-shadow: 0 2px 6px rgba(0, 0, 0, 0.8);
			margin-bottom: 6px;
		}

		.speaker-name-badge {
			display: inline-flex;
			align-items: center;
			gap: 9px;
			font-size: 1.28rem;
			font-weight: 600;
			color: #ffffff;
			margin-bottom: 16px;
			text-shadow: 0 2px 5px rgba(0, 0, 0, 0.7);
		}

		.speaker-name-badge i {
			color: #00e676;
			font-size: 1.2rem;
		}

		/* Grid Info Pill (Hari/Tanggal, Waktu, Tempat) */
		.meta-pills-row {
			display: flex;
			gap: 14px;
			flex-wrap: wrap;
			margin-bottom: 16px;
		}

		.meta-pill {
			display: inline-flex;
			align-items: center;
			gap: 9px;
			background: rgba(0, 0, 0, 0.45);
			border: 1px solid rgba(255, 215, 0, 0.35);
			padding: 7px 16px;
			border-radius: 12px;
			font-size: 0.96rem;
			color: #ffffff;
			box-shadow: 0 3px 10px rgba(0, 0, 0, 0.3);
		}

		.meta-pill i {
			font-size: 1.05rem;
		}

		.meta-pill.time-pill i { color: #ffd700; }
		.meta-pill.location-pill i { color: #ff5252; }
		.meta-pill.date-pill i { color: #00b4d8; }

		.meta-pill span.label {
			color: rgba(255, 255, 255, 0.75);
			font-weight: 500;
		}

		.meta-pill span.val {
			font-weight: 700;
			color: #ffffff;
		}

		/* Deskripsi / Isi Pengumuman */
		.activity-description {
			font-size: 1.2rem;
			line-height: 1.55;
			color: rgba(255, 255, 255, 0.92);
			text-shadow: 0 1px 4px rgba(0, 0, 0, 0.7);
			max-height: 18vh;
			overflow-y: auto;
			padding-right: 8px;
			white-space: pre-line;
		}

		.activity-description::-webkit-scrollbar {
			width: 4px;
		}
		.activity-description::-webkit-scrollbar-thumb {
			background: rgba(255, 215, 0, 0.4);
			border-radius: 8px;
		}

		/* Progress & Indicators */
		.progress-container {
			flex-shrink: 0;
			margin-top: 6px;
			margin-bottom: 4px;
			max-width: 1450px;
			margin-left: auto;
			margin-right: auto;
			width: 100%;
			padding: 0 15px;
		}

		.progress-bar {
			height: 4px;
			background: rgba(255, 255, 255, 0.2);
			border-radius: 4px;
			overflow: hidden;
		}

		.progress-fill {
			height: 100%;
			background: linear-gradient(90deg, #00b4d8, #00e676, #ffd700);
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

		.empty-activity {
			text-align: center;
			padding: 40px;
			color: rgba(255, 255, 255, 0.8);
		}
		.empty-activity i {
			font-size: 3.5rem;
			color: #ffd700;
			margin-bottom: 12px;
		}
	</style>
</head>

<body>
	<div class="display-background"></div>
	<div class="display-overlay"></div>

	@include('partials.medallion-header')

	<div class="container">
		<!-- Header Standar Bersama (Pixel-Locked) -->
		<div class="header">
			<h1>{{ $settings['nama_aplikasi'] ?? "MASJID JAMI' AL JIHAD" }}</h1>
			<h3 class="sub-header">SISTEM INFORMASI DIGITAL</h3>
			<div class="datetime" id="datetime"></div>
		</div>

		<!-- Stage Kartu Premiere Kegiatan (2-Column Showcase) -->
		<div class="announcement-container" id="announcementContainer">
			<div class="announcement-card" id="announcementCard">
				
				<!-- Top Bar: Badge Kategori & Badge Tanggal -->
				<div class="card-header-bar">
					<div class="page-title-badge">
						<i class="fas fa-bullhorn"></i>
						<span>Kajian & Kegiatan Masjid</span>
					</div>
					<span class="date-badge" id="announcementDate">
						<i class="fas fa-calendar-alt"></i> Memuat...
					</span>
				</div>

				<!-- Main 2-Column Content -->
				<div class="card-main-body" id="cardMainBody">
					
					<!-- Kolom Kiri: Foto Ustadz / Flyer -->
					<div class="speaker-col">
						<div class="speaker-frame" id="speakerFrame">
							<div class="speaker-avatar-fallback" id="speakerFallback">
								<i class="fas fa-user-tie"></i>
								<span>Info Kegiatan</span>
							</div>
							<img id="speakerImage" src="" alt="Pemateri" style="display: none;" onerror="this.style.display='none'; document.getElementById('speakerFallback').style.display='flex';">
						</div>
					</div>

					<!-- Kolom Kanan: Detail Judul, Pemateri, Jadwal, Lokasi, Deskripsi -->
					<div class="details-col">
						<div class="activity-title" id="activityTitle">
							Memuat kegiatan...
						</div>

						<div class="speaker-name-badge" id="speakerBadge" style="display: none;">
							<i class="fas fa-microphone-alt"></i>
							<span id="speakerName">-</span>
						</div>

						<!-- Grid Info Pill -->
						<div class="meta-pills-row">
							<div class="meta-pill date-pill" id="pillDateContainer">
								<i class="fas fa-calendar-check"></i>
								<span class="label">Hari:</span>
								<span class="val" id="pillDateVal">-</span>
							</div>
							<div class="meta-pill time-pill" id="pillTimeContainer" style="display: none;">
								<i class="fas fa-clock"></i>
								<span class="label">Waktu:</span>
								<span class="val" id="pillTimeVal">-</span>
							</div>
							<div class="meta-pill location-pill" id="pillLocationContainer">
								<i class="fas fa-map-marker-alt"></i>
								<span class="label">Lokasi:</span>
								<span class="val" id="pillLocationVal">Ruang Utama Masjid Al-Jihad</span>
							</div>
						</div>

						<!-- Keterangan / Deskripsi Lengkap -->
						<div class="activity-description" id="announcementContent">
							Memuat keterangan...
						</div>
					</div>

				</div>
			</div>
		</div>

		<!-- Progress Bar Pergantian Slide -->
		<div class="progress-container">
			<div class="progress-bar">
				<div class="progress-fill" id="progressFill"></div>
			</div>
			<!-- Indicator Dots -->
			<div class="indicator-container" id="indicatorContainer"></div>
		</div>

		<!-- Memanggil Running Text dan Footer Terpusat -->
		@include('partials.bottom-section')
	</div>

	<script>
		const announcements = @json($pengumuman);
		const displayDuration = 10000;
		let currentIndex = 0;
		let intervalId = null;
		let progressIntervalId = null;

		function updateDateTime() {
			const now = new Date();
			const dt = document.getElementById('datetime');
			if (dt) {
				dt.innerHTML = typeof getStandardMasjidDateTime === 'function'
					? getStandardMasjidDateTime(now, true)
					: now.toLocaleString('id-ID');
			}
		}
		updateDateTime();
		setInterval(updateDateTime, 1000);

		function displayAnnouncement(index) {
			if (!announcements.length) {
				document.getElementById('cardMainBody').innerHTML = `
					<div class="empty-activity" style="width: 100%;">
						<i class="fas fa-calendar-times"></i>
						<h3 style="font-size: 1.6rem; color: #fff; margin-top: 10px;">Belum Ada Agenda Kegiatan Aktif</h3>
						<p style="color: rgba(255,255,255,0.7); margin-top: 5px;">Silakan tambahkan informasi kajian atau pengumuman melalui Dashboard Admin.</p>
					</div>
				`;
				document.getElementById('announcementDate').innerHTML = '<i class="fas fa-calendar-alt"></i> -';
				return;
			}

			const item = announcements[index];
			const date = new Date(item.tanggal);
			
			// Format Hari dan Tanggal Bahasa Indonesia
			const formattedFullDate = date.toLocaleDateString('id-ID', {
				weekday: 'long',
				day: 'numeric',
				month: 'long',
				year: 'numeric'
			});

			const formattedShortDate = date.toLocaleDateString('id-ID', {
				day: 'numeric',
				month: 'short',
				year: 'numeric'
			});

			const card = document.getElementById('announcementCard');
			card.style.animation = 'none';
			card.offsetHeight;
			card.style.animation = 'cardFadeIn 0.6s cubic-bezier(0.25, 1, 0.5, 1)';

			// 1. Tanggal Badge Atas
			document.getElementById('announcementDate').innerHTML = `<i class="fas fa-calendar-alt"></i> ${formattedShortDate}`;

			// 2. Judul Kegiatan
			const titleEl = document.getElementById('activityTitle');
			titleEl.textContent = item.judul ? item.judul : (item.isi ? item.isi.split('\n')[0].substring(0, 60) : 'Informasi Kegiatan');

			// 3. Nama Ustadz / Pemateri
			const speakerBadge = document.getElementById('speakerBadge');
			const speakerName = document.getElementById('speakerName');
			if (item.pemateri && item.pemateri.trim() !== '') {
				speakerName.textContent = item.pemateri;
				speakerBadge.style.display = 'inline-flex';
			} else {
				speakerBadge.style.display = 'none';
			}

			// 4. Foto Ustadz / Pamflet
			const imgEl = document.getElementById('speakerImage');
			const fallbackEl = document.getElementById('speakerFallback');
			const photoUrl = item.foto ? (item.foto.startsWith('http') ? item.foto : ('{{ asset("storage") }}/' + item.foto)) : null;
			
			if (photoUrl) {
				imgEl.src = photoUrl;
				imgEl.style.display = 'block';
				fallbackEl.style.display = 'none';
			} else {
				imgEl.style.display = 'none';
				fallbackEl.style.display = 'flex';
			}

			// 5. Pill Jadwal & Lokasi
			document.getElementById('pillDateVal').textContent = formattedFullDate;
			
			const pillTime = document.getElementById('pillTimeContainer');
			const pillTimeVal = document.getElementById('pillTimeVal');
			if (item.waktu && item.waktu.trim() !== '') {
				pillTimeVal.textContent = item.waktu;
				pillTime.style.display = 'inline-flex';
			} else {
				pillTime.style.display = 'none';
			}

			const pillLocVal = document.getElementById('pillLocationVal');
			pillLocVal.textContent = item.tempat && item.tempat.trim() !== '' ? item.tempat : 'Ruang Utama Masjid Al-Jihad';

			// 6. Deskripsi / Keterangan Tambahan
			document.getElementById('announcementContent').innerHTML = item.isi;

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
				displayAnnouncement(0);
				const progressFill = document.getElementById('progressFill');
				progressFill.style.width = '100%';
				progressFill.style.background = '#6c757d';
			}
		}

		init();
	</script>

	<!-- SCRIPT UNTUK BACKGROUND SLIDESHOW (EFEK BLINDS / TRIVISION) -->
	<script>
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
		
		const numBlinds = 10;
		let blinds = [];

		if (backgroundImages.length > 0 && bgContainer) {
			bgContainer.style.setProperty('overflow', 'hidden', 'important');
			bgContainer.style.setProperty('position', 'absolute', 'important');
			bgContainer.style.setProperty('z-index', '-1', 'important');
			bgContainer.style.setProperty('perspective', '1500px', 'important');
			bgContainer.style.setProperty('display', 'flex', 'important');

			for (let i = 0; i < numBlinds; i++) {
				const blind = document.createElement('div');
				blind.style.flex = '1';
				blind.style.height = '100%';
				blind.style.position = 'relative';
				blind.style.transformStyle = 'preserve-3d';
				blind.style.transition = 'transform 1s cubic-bezier(0.4, 0, 0.2, 1)';
				blind.style.transitionDelay = `${i * 0.1}s`;

				const bgPosX = numBlinds === 1 ? 0 : (i / (numBlinds - 1)) * 100;

				const createFace = (isFront) => {
					const face = document.createElement('div');
					face.style.position = 'absolute';
					face.style.width = '100%';
					face.style.height = '100%';
					face.style.backfaceVisibility = 'hidden';
					face.style.backgroundSize = '100vw 100vh';
					face.style.backgroundPosition = `${bgPosX}% center`;
					face.style.backgroundRepeat = 'no-repeat';
					if (!isFront) {
						face.style.transform = 'rotateY(180deg)';
					}
					return face;
				};

				const front = createFace(true);
				const back = createFace(false);

				front.style.backgroundImage = `url('${backgroundImages[0]}')`;

				blind.appendChild(front);
				blind.appendChild(back);
				bgContainer.appendChild(blind);

				blinds.push({ el: blind, front: front, back: back, isFlipped: false });
			}

			function changeBackground() {
				currentBgIndex = (currentBgIndex + 1) % backgroundImages.length;
				const nextImgUrl = `url('${backgroundImages[currentBgIndex]}')`;

				blinds.forEach((blind) => {
					if (!blind.isFlipped) {
						blind.back.style.backgroundImage = nextImgUrl;
						blind.el.style.transform = 'rotateY(180deg)';
					} else {
						blind.front.style.backgroundImage = nextImgUrl;
						blind.el.style.transform = 'rotateY(0deg)';
					}
					blind.isFlipped = !blind.isFlipped;
				});
			}

			setInterval(changeBackground, 10000); 
		}
	</script>	
</body>

</html>