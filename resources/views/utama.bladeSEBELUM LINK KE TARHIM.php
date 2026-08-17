<!-- resources/views/utama.blade.php -->
<!DOCTYPE html>
<html lang="id">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Sistem Informasi Masjid</title>
	<link rel="icon" type="image/x-icon"
		href="{{ asset($settings['favicon'] ?? ($settings->favicon ?? '')) ? asset('storage/' . str_replace('storage/', '', $settings['favicon'] ?? ($settings->favicon ?? ''))) : asset('favicon.ico') }}?v={{ time() }}">
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
		rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&display=swap" rel="stylesheet">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
	<link rel="stylesheet" href="{{ asset('css/display-theme.css') }}?v={{ time() }}">
	@include('partials.display-theme')
	<style>
		:root {
			--primary-color: #0d6e6e;
			--secondary-color: #ffd700;
			--accent-color: #0a4d68;
			--text-light: #ffffff;
			--text-dark: #333333;
			--success-color: #28a745;
			--info-color: #17a2b8;
		}

		body {
			margin: 0;
			padding: 0;
			font-family: 'Poppins', sans-serif;
			color: var(--text-light);
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
			z-index: 10;
			user-select: none;
			background: linear-gradient(to right, #ffd700, #ffffff);
			-webkit-background-clip: text;
			-webkit-text-fill-color: transparent;
			text-shadow: 3px 3px 0 rgba(0, 0, 0, 0.4), 6px 6px 0 rgba(0, 0, 0, 0.2);
			opacity: 0.9;
		}

		.kaligrafi-allah {
			right: 35px;
		}

		.kaligrafi-muhammad {
			left: 35px;
		}

		.page-layout {
			position: relative;
			width: 100%;
			max-width: 1800px;
			margin: 0 auto;
			height: 100vh;
			display: flex;
			flex-direction: column;
			justify-content: space-between;
			padding: 15px 20px;
			box-sizing: border-box;
			z-index: 5;
		}

		.header-section {
			text-align: center;
			width: 100%;
			margin-top: 0;
		}

		.header-section h1 {
			font-family: 'Masking Renta', sans-serif !important;
			font-size: 3.2rem !important;
			margin: 0;
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
		}
		.header-section h3.sub-header {
			font-family: 'Poppins', sans-serif !important;
			font-size: 1.25rem !important;
			font-weight: 500 !important;
			letter-spacing: 4px !important;
			color: #ffffff !important;
			text-transform: uppercase !important;
			opacity: 0.95 !important;
			text-shadow: 0 2px 5px rgba(0, 0, 0, 0.8) !important;
			margin-top: -5px !important;
			margin-bottom: 8px !important;
		}

		.datetime {
			font-size: 1.2rem;
			margin-top: 4px;
			background: rgba(0, 0, 0, 0.4);
			display: inline-block;
			padding: 4px 20px;
			border-radius: 30px;
			font-weight: 500;
			border: 1px solid rgba(255, 215, 0, 0.4);
			box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
			backdrop-filter: blur(5px);
		}

		.bottom-section {
    width: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
    top: -20px; /* Semakin besar nilai minusnya, semakin ke atas posisinya */
}

		.jadwal-sholat-title {
			font-size: 2rem;
			font-weight: bold !important;
			margin-bottom: 12px;
			padding-bottom: 4px;
			position: relative;
			color: var(--secondary-color);
			display: flex;
			align-items: center;
			gap: 8px;
			text-shadow: 0 2px 4px rgba(0, 0, 0, 0.6);
		}

		.jadwal-sholat-title i {
			animation: spin 5s linear infinite;
		}

		@keyframes spin {
			0% {
				transform: rotate(0deg);
			}
			100% {
				transform: rotate(360deg);
			}
		}

		.jadwal-sholat-title:after {
			content: '';
			position: absolute;
			left: 50%;
			transform: translateX(-50%);
			bottom: 0;
			width: 140px;
			height: 3px;
			background: var(--secondary-color);
			border-radius: 3px;
		}

		.sholat-list {
			display: flex;
			justify-content: center;
			gap: 15px;
			width: 100%;
			margin-bottom: 12px;
		}

		.sholat-card {
			background: rgba(10, 30, 25, 0.35);
			border-radius: 14px;
			padding: 12px 10px;
			backdrop-filter: blur(8px);
			-webkit-backdrop-filter: blur(8px);
			box-shadow: 0 8px 25px rgba(0, 0, 0, 0.35);
			border: 1px solid rgba(255, 215, 0, 0.4);
			flex: 1;
			max-width: 190px;
			text-align: center;
		}

		.sholat-card.active {
			background: radial-gradient(circle at center,
					rgba(255, 213, 79, 0.4) 0%,
					rgba(10, 46, 31, 0.8) 100%);
			border: 2px solid #FFD54F;
			box-shadow: 0 0 25px rgba(255, 213, 79, 0.45);
			transform: scale(1.03);
		}

		.sholat-card.khusus-imsak-terbit {
			border: 1px solid rgba(255, 255, 255, 0.4);
		}

		.sholat-card.khusus-imsak-terbit i {
			color: #fff;
		}

		.sholat-card i {
			font-size: 1.3rem;
			color: var(--secondary-color);
			margin-bottom: 4px;
			display: block;
		}

		.sholat-card .nama-sholat {
			font-size: 1.25rem;
			font-weight: 600;
			color: var(--text-light);
			text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
		}

		.sholat-card.khusus-imsak-terbit .nama-sholat {
			color: #FFEB3B;
		}

		.sholat-card .waktu-sholat {
			font-size: 1.6rem;
			font-weight: 600;
			color: var(--secondary-color);
			text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
			margin-top: 2px;
		}

		.auto-update-status {
			position: fixed;
			bottom: 10px;
			left: 10px;
			background: rgba(0, 0, 0, 0.6);
			backdrop-filter: blur(5px);
			padding: 5px 12px;
			border-radius: 30px;
			font-size: 0.8rem;
			border-left: 3px solid var(--info-color);
			z-index: 1000;
			display: flex;
			align-items: center;
			gap: 6px;
			border: 1px solid rgba(255, 255, 255, 0.15);
		}

		.auto-update-status i {
			color: var(--info-color);
		}

		.auto-update-status .badge {
			background: var(--info-color);
			color: white;
			padding: 2px 5px;
			border-radius: 10px;
			font-size: 0.6rem;
			margin-left: 3px;
		}

		.auto-update-status.active i {
			color: var(--success-color);
		}

		.auto-update-status.active .badge {
			background: var(--success-color);
		}
	</style>
</head>

<body>
	<div class="display-background"></div>
	<div class="display-overlay"></div>

	<div class="kaligrafi kaligrafi-allah">ﷲ</div>
	<div class="kaligrafi kaligrafi-muhammad">ﷺ</div>

	<!-- Elemen Audio Tarhim Otomatis -->
	<audio id="tarhimAudio" preload="auto">
		<source src="{{ asset('audioTarhim1.mp3') }}" type="audio/mpeg">
	</audio>

	@if($settings->auto_update_jadwal ?? false)
	<div class="auto-update-status active" id="autoUpdateStatus">
		<i class="fas fa-sync-alt fa-spin"></i>
		<span>Auto-Update Aktif</span>
		<span class="badge">{{ $settings['auto_update_city'] ?? ($settings->auto_update_city ?? 'Jakarta') }}</span>
	</div>
	@else
	<div class="auto-update-status" id="autoUpdateStatus">
		<i class="fas fa-clock"></i>
		<span>Update Manual</span>
	</div>
	@endif

	<div class="page-layout">
		<div class="header-section">
			<h1 id="nama-masjid">{{ $settings['nama_aplikasi'] ?? ($settings->nama_aplikasi ?? 'Masjid Al-Ikhlas') }}</h1>
			<h3 class="sub-header">SISTEM INFORMASI DIGITAL</h3>
			<div class="datetime" id="datetime"></div>
		</div>

		<div class="bottom-section">
			<div class="jadwal-sholat-title"><i class="fas fa-mosque"></i> Jadwal Sholat</div>

			<div class="sholat-list">
				@php
				$now = \Carbon\Carbon::now('Asia/Jakarta');
				$currentHour = $now->format('H:i');

				$customOrder = ['Imsak', 'Subuh', 'Terbit', 'Dzuhur', 'Ashar', 'Maghrib', 'Isya'];

				$sortedJadwal = $jadwalSholat->sortBy(function ($item) use ($customOrder) {
				$index = array_search(trim($item->nama_sholat), $customOrder);
				return $index !== false ? $index : 99;
				});
				@endphp

				@foreach ($sortedJadwal as $jadwal)
				@php
				$isImsakOrTerbit = in_array(strtolower(trim($jadwal->nama_sholat)), ['imsak', 'terbit']);
				@endphp

				<div
					class="sholat-card {{ $isImsakOrTerbit ? 'khusus-imsak-terbit' : '' }}" data-waktu="{{ \Carbon\Carbon::parse($jadwal->waktu)->format('H:i') }}">
					<i class="fas {{ strtolower(trim($jadwal->nama_sholat)) == 'terbit' ? 'fa-sun' : (strtolower(trim($jadwal->nama_sholat)) == 'imsak' ? 'fa-utensils' : 'fa-mosque') }}"></i>
					<div class="nama-sholat">{{ $jadwal->nama_sholat }}</div>
					<div class="waktu-sholat">{{ \Carbon\Carbon::parse($jadwal->waktu)->format('H:i') }}</div>
				</div>
				@endforeach
			</div>
		</div>
	</div>

	<!-- Running teks diletakkan terpisah di luar page-layout agar posisinya pas di bawah -->
	@include('partials.bottom-section')

	<script>
		function updateDateTime() {
			const now = new Date();
			const options = {
				weekday: 'long',
				year: 'numeric',
				month: 'long',
				day: 'numeric',
				hour: '2-digit',
				minute: '2-digit',
				second: '2-digit',
				timeZone: 'Asia/Jakarta'
			};
			const formattedDateTime = now.toLocaleString('id-ID', options);
			document.getElementById('datetime').textContent = formattedDateTime;

			const currentHours = now.getHours();
			const currentMinutes = now.getMinutes();
			const currentTotalMinutes = (currentHours * 60) + currentMinutes;

			document.querySelectorAll('.sholat-card').forEach(card => {
				const waktuText = card.getAttribute('data-waktu');
				if (!waktuText) return;

				const [jadwalHours, jadwalMinutes] = waktuText.split(':').map(Number);
				let jadwalTotalMinutes = (jadwalHours * 60) + jadwalMinutes;

				let startMinutes = jadwalTotalMinutes - 2;
				let endMinutes = jadwalTotalMinutes + 30;

				if (currentTotalMinutes >= startMinutes && currentTotalMinutes <= endMinutes) {
					card.classList.add('active');
				} else {
					card.classList.remove('active');
				}
			});
		}
		updateDateTime();
		setInterval(updateDateTime, 1000);

		document.addEventListener('DOMContentLoaded', function() {
			let tarhimPlayed = false;
			const tarhimAudio = document.getElementById('tarhimAudio');

			async function checkTarhimStatus() {
				try {
					const response = await fetch('{{ url("/prayer-mode/status") }}');
					if (response.ok) {
						const data = await response.json();

						if (data.active && data.phase === 'countdown') {
							if (tarhimAudio && tarhimAudio.paused && !tarhimPlayed) {
								tarhimAudio.play().then(() => {
									tarhimPlayed = true;
								}).catch(e => {
									console.log("Autoplay dicegah browser:", e);
								});
							}
						} else {
							if (data.phase !== 'countdown') {
								tarhimPlayed = false;
								if (tarhimAudio && !tarhimAudio.paused) {
									tarhimAudio.pause();
									tarhimAudio.currentTime = 0;
								}
							}
						}
					}
				} catch (error) {
					console.error('Gagal mengecek status tarhim:', error);
				}
			}

			setInterval(checkTarhimStatus, 5000);

			let lastTimestamp = null;
			let refreshInterval = 30000;

			async function checkForUpdates() {
				try {
					const response = await fetch('{{ route("data.timestamp") }}', {
						method: 'GET',
					});

					if (!response.ok) {
						throw new Error('Network response was not ok');
					}

					const data = await response.json();
					const newTimestamp = data.timestamp;

					if (lastTimestamp && newTimestamp && newTimestamp !== lastTimestamp) {
						setTimeout(() => {
							window.location.reload();
						}, 2000);
					}

					lastTimestamp = newTimestamp;

				} catch (error) {
					console.error('Error checking for updates:', error);
				}
			}

			checkForUpdates();
			setInterval(checkForUpdates, refreshInterval);
		});
	</script>

	<!-- SCRIPT UNTUK BACKGROUND SLIDESHOW -->
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

		if (backgroundImages.length > 0 && bgContainer) {
			bgContainer.style.setProperty('overflow', 'hidden', 'important');
			bgContainer.style.setProperty('position', 'absolute', 'important');
			bgContainer.style.setProperty('z-index', '-1', 'important');

			function createBgElement(imageUrl, initialOpacity) {
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
                div.style.opacity = initialOpacity;
                div.style.transition = 'opacity 2s ease-in-out'; 
                return div;
            }

			let currentBg = createBgElement(backgroundImages[0], 1);
			bgContainer.appendChild(currentBg);

			function changeBackground() {
				currentBgIndex = (currentBgIndex + 1) % backgroundImages.length;
				const nextBg = createBgElement(backgroundImages[currentBgIndex], 0);
				bgContainer.appendChild(nextBg);

				void nextBg.offsetWidth;
				nextBg.style.opacity = 1;

				const oldBg = currentBg;
				setTimeout(() => {
					if (oldBg && oldBg.parentNode) {
						oldBg.parentNode.removeChild(oldBg);
					}
				}, 2000);

				currentBg = nextBg;
			}

			setInterval(changeBackground, 10000); 
		}
	</script>
</body>

</html>