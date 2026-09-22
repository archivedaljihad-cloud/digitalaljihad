<!-- resources/views/utama.blade.php -->
<!DOCTYPE html>
<html lang="id">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Sistem Informasi Masjid</title>
	<link rel="icon" type="image/x-icon"
		href="{{ asset($settings['favicon'] ?? ($settings->favicon ?? '')) ? asset('storage/' . str_replace('storage/', '', $settings['favicon'] ?? ($settings->favicon ?? ''))) : asset('favicon.ico') }}?v=3.0.4">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
		rel="stylesheet">
	<link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}?v=3.0.4">
	<link rel="stylesheet" href="{{ asset('css/display-theme.css') }}?v=3.0.4">
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
			padding: 10px 25px 85px 25px;
			box-sizing: border-box;
			z-index: 5;
		}

		.header-section {
			text-align: center;
			width: 100%;
			margin-top: 0;
			margin-bottom: 12px;
			flex-shrink: 0;
			position: relative;
		}

		.header-section h1 {
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

		.header-section h3.sub-header {
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

		.bottom-section {
			width: 100%;
			display: flex;
			flex-direction: column;
			align-items: center;
			position: relative;
			margin-bottom: 10px;
		}

		.jadwal-sholat-title-wrap {
			margin-top: 10px;
			display: flex;
			justify-content: center;
			width: 100%;
		}

		.jadwal-sholat-title {
			font-size: 1.65rem;
			font-weight: 700 !important;
			letter-spacing: 2.5px;
			margin-bottom: 0;
			padding: 5px 28px;
			position: relative;
			color: #ffffff !important;
			display: inline-flex;
			align-items: center;
			justify-content: center;
			gap: 12px;
			background: linear-gradient(135deg, rgba(2, 25, 17, 0.85) 0%, rgba(5, 42, 28, 0.8) 100%);
			border: 1.5px solid rgba(255, 215, 0, 0.65);
			border-radius: 35px;
			box-shadow: 
				0 8px 25px rgba(0, 0, 0, 0.65),
				0 0 20px rgba(255, 215, 0, 0.25),
				inset 0 1px 1px rgba(255, 255, 255, 0.2);
			backdrop-filter: blur(10px);
			-webkit-backdrop-filter: blur(10px);
			text-shadow: 0 2px 8px rgba(0, 0, 0, 0.9);
		}

		.jadwal-sholat-title i {
			color: #ffd700;
			font-size: 1.5rem;
			filter: drop-shadow(0 0 8px rgba(255, 215, 0, 0.7));
			animation: pulseMosque 3s ease-in-out infinite;
		}

		@keyframes pulseMosque {
			0%, 100% {
				transform: scale(1);
				filter: drop-shadow(0 0 6px rgba(255, 215, 0, 0.5));
			}
			50% {
				transform: scale(1.08);
				filter: drop-shadow(0 0 14px rgba(255, 215, 0, 0.9));
			}
		}

		.jadwal-sholat-title:after {
			display: none;
		}

		/* =====================================================
		   SMART NEXT PRAYER BADGE (PAS DI ATAS KOTAK SHOLAT)
		   ===================================================== */
		.next-prayer-center-container {
			width: 100%;
			display: flex;
			justify-content: center;
			align-items: center;
			margin: 0 auto 10px auto;
			z-index: 10;
		}

		.next-prayer-bar {
			display: inline-flex;
			align-items: center;
			gap: 14px;
			background: linear-gradient(135deg, rgba(6, 26, 17, 0.92) 0%, rgba(2, 14, 9, 0.96) 100%);
			border: 1.5px solid rgba(212, 175, 55, 0.55);
			border-radius: 40px;
			padding: 8px 26px 8px 14px;
			box-shadow: 0 10px 32px rgba(0, 0, 0, 0.75), 0 0 22px rgba(212, 175, 55, 0.25), inset 0 1px 0 rgba(255, 238, 170, 0.3);
			backdrop-filter: blur(14px);
			-webkit-backdrop-filter: blur(14px);
			position: relative;
			overflow: hidden;
		}

		.npb-shimmer {
			position: absolute;
			top: 0;
			left: -100%;
			width: 60%;
			height: 100%;
			background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.18), transparent);
			transform: skewX(-20deg);
			animation: npbShimmer 6s infinite;
			pointer-events: none;
		}

		@keyframes npbShimmer {
			0%, 80% { left: -100%; }
			100% { left: 200%; }
		}

		.npb-icon {
			width: 44px;
			height: 44px;
			border-radius: 50%;
			background: radial-gradient(circle, rgba(212, 175, 55, 0.3) 0%, rgba(212, 175, 55, 0.05) 100%);
			border: 1px solid rgba(212, 175, 55, 0.6);
			display: flex;
			align-items: center;
			justify-content: center;
			color: #FFD700;
			font-size: 19px;
			box-shadow: 0 0 14px rgba(212, 175, 55, 0.4);
			flex-shrink: 0;
		}

		.npb-content {
			display: flex;
			flex-direction: column;
			line-height: 1.15;
		}

		.npb-label-row {
			display: flex;
			align-items: center;
			gap: 8px;
		}

		.npb-badge {
			font-size: 10px;
			font-weight: 800;
			letter-spacing: 1.2px;
			color: #10B981;
			background: rgba(16, 185, 129, 0.18);
			border: 0.5px solid rgba(16, 185, 129, 0.4);
			padding: 2px 7px;
			border-radius: 10px;
		}

		.npb-prayer-name {
			font-size: 15px;
			font-weight: 800;
			color: #FFD700;
			letter-spacing: 1.5px;
			text-transform: uppercase;
			text-shadow: 0 1px 4px rgba(0, 0, 0, 0.8);
		}

		.npb-time-row {
			display: flex;
			align-items: center;
			gap: 8px;
			margin-top: 3px;
		}

		.npb-schedule-time {
			font-size: 13px;
			color: #CBD5E1;
			font-weight: 600;
		}

		.npb-sep {
			color: rgba(212, 175, 55, 0.6);
			font-size: 11px;
		}

		.npb-countdown {
			font-size: 16px;
			font-weight: 800;
			color: #FFFFFF;
			font-family: monospace;
			letter-spacing: 1px;
			text-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
		}

		.npb-pulse-dot {
			width: 14px;
			height: 14px;
			border-radius: 50%;
			background: radial-gradient(circle, #FFFFFF 25%, #FFF475 60%, #FFD700 100%);
			box-shadow: 0 0 10px #FFD700, 0 0 20px rgba(255, 215, 0, 0.9), 0 0 30px rgba(255, 255, 255, 0.75);
			animation: npbPulse 1.5s infinite ease-in-out;
			margin-left: 8px;
			flex-shrink: 0;
		}

		@keyframes npbPulse {
			0%, 100% {
				opacity: 1;
				transform: scale(1);
				box-shadow: 0 0 10px #FFD700, 0 0 22px rgba(255, 215, 0, 0.95), 0 0 32px rgba(255, 255, 255, 0.85);
			}
			50% {
				opacity: 0.45;
				transform: scale(0.75);
				box-shadow: 0 0 5px #FFD700, 0 0 10px rgba(255, 215, 0, 0.5);
			}
		}

		.sholat-list {
			display: flex;
			justify-content: center;
			gap: 16px;
			width: 100%;
			max-width: 1720px;
			margin: 0 auto;
			margin-bottom: 0;
		}

		.sholat-card {
			background: rgba(4, 25, 18, 0.72);
			border-radius: 16px;
			padding: 12px 10px 10px 10px;
			backdrop-filter: blur(12px);
			-webkit-backdrop-filter: blur(12px);
			box-shadow: 0 10px 30px rgba(0, 0, 0, 0.45);
			border: 1.5px solid rgba(255, 215, 0, 0.45);
			flex: 1;
			max-width: 225px;
			text-align: center;
			transition: all 0.3s ease;
		}

		.sholat-card.active {
			background: radial-gradient(circle at center,
					rgba(255, 213, 79, 0.45) 0%,
					rgba(6, 42, 28, 0.95) 100%);
			border: 2.5px solid #FFD54F;
			box-shadow: 0 0 35px rgba(255, 213, 79, 0.55), 0 12px 30px rgba(0, 0, 0, 0.5);
			transform: translateY(-6px) scale(1.04);
		}

		.sholat-card.khusus-imsak-terbit {
			border: 1.5px solid rgba(255, 255, 255, 0.45);
			background: rgba(15, 30, 25, 0.65);
		}

		.sholat-card.khusus-imsak-terbit i {
			color: #ffffff;
		}

		.sholat-card i {
			font-size: 1.35rem;
			color: var(--secondary-color);
			margin-bottom: 4px;
			display: block;
			filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.5));
		}

		.sholat-card .nama-sholat {
			font-size: 1.3rem;
			font-weight: 600;
			letter-spacing: 1.5px;
			text-transform: uppercase;
			color: var(--text-light);
			text-shadow: 0 2px 5px rgba(0, 0, 0, 0.8);
		}

		.sholat-card.khusus-imsak-terbit .nama-sholat {
			color: #FFEB3B;
		}

		.sholat-card .waktu-sholat {
			font-size: 2.25rem;
			font-weight: 700;
			color: #ffe066;
			letter-spacing: 1px;
			text-shadow: 0 3px 8px rgba(0, 0, 0, 0.9);
			margin-top: 3px;
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

		/* RESPONSIF MEDIA QUERIES UNTUK HP / TABLET */
		@media (max-width: 1024px) {
			body {
				height: auto;
				overflow-y: auto;
			}
			.page-layout {
				height: auto;
				min-height: 100vh;
			}
			.sholat-list {
				flex-wrap: wrap;
			}
			.sholat-card {
				flex: 1 1 40%;
				max-width: none;
			}
			.kaligrafi {
				font-size: 2.5rem;
			}
		}

		@media (max-width: 768px) {
			.header-section h1 {
				font-size: 2rem !important;
			}
			.header-section h3.sub-header {
				font-size: 0.9rem !important;
			}
			.sholat-card {
				flex: 1 1 100%;
			}
		}
	</style>
</head>

<body>
	<div class="display-background"></div>
	<div class="display-overlay"></div>

	@include('partials.medallion-header')


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
			<h1 id="nama-masjid">{{ $settings['nama_aplikasi'] ?? ($settings->nama_aplikasi ?? 'Masjid Al-Jihad') }}</h1>
			<h3 class="sub-header">SISTEM INFORMASI DIGITAL</h3>
			<div class="datetime" id="datetime"></div>
			<div class="jadwal-sholat-title-wrap">
				<div class="jadwal-sholat-title"><i class="fas fa-mosque"></i> Jadwal Sholat</div>
			</div>
		</div>

		<div class="bottom-section">
			<!-- BADGE KAPSUL SHOLAT BERIKUTNYA (PAS DI ATAS KOTAK JADWAL SHOLAT DENGAN JARAK TIPIS) -->
			@if(!isset($settings) || $settings->isNextPrayerBarEnabled())
			<div class="next-prayer-center-container">
				<div class="next-prayer-bar" id="nextPrayerBar">
					<div class="npb-shimmer"></div>
					<div class="npb-icon">
						<i class="fa-solid fa-mosque"></i>
					</div>
					<div class="npb-content">
						<div class="npb-label-row">
							<span class="npb-badge">SELANJUTNYA</span>
							<span class="npb-prayer-name" id="npbPrayerName">MEMUAT...</span>
						</div>
						<div class="npb-time-row">
							<span class="npb-schedule-time" id="npbScheduleTime">--:--</span>
							<span class="npb-sep">•</span>
							<span class="npb-countdown" id="npbCountdown">-00:00:00</span>
						</div>
					</div>
					<div class="npb-pulse-dot"></div>
				</div>
			</div>
			@endif

			<div class="sholat-list">
				@php
				$now = \Carbon\Carbon::now('Asia/Jakarta');
				$currentHour = $now->format('H:i');

				$customOrder = ['Imsak', 'Subuh', 'Syuruk', 'Dzuhur', 'Ashar', 'Maghrib', 'Isya'];

				$sortedJadwal = $jadwalSholat->sortBy(function ($item) use ($customOrder) {
				$index = array_search(trim($item->nama_sholat), $customOrder);
				return $index !== false ? $index : 99;
				});
				@endphp

				@foreach ($sortedJadwal as $jadwal)
				@php
				$isImsakOrTerbit = in_array(strtolower(trim($jadwal->nama_sholat)), ['imsak', 'syuruk', 'terbit']);
				@endphp

				<div
					class="sholat-card {{ $isImsakOrTerbit ? 'khusus-imsak-terbit' : '' }}" data-waktu="{{ \Carbon\Carbon::parse($jadwal->waktu)->format('H:i') }}">
					<i class="fas {{ in_array(strtolower(trim($jadwal->nama_sholat)), ['syuruk', 'terbit']) ? 'fa-sun' : (strtolower(trim($jadwal->nama_sholat)) == 'imsak' ? 'fa-utensils' : 'fa-mosque') }}"></i>
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
			const el = document.getElementById('datetime');
			if (el) {
				el.innerHTML = typeof getStandardMasjidDateTime === 'function' 
					? getStandardMasjidDateTime(now, true) 
					: now.toLocaleString('id-ID');
			}

			const currentHours = now.getHours();
			const currentMinutes = now.getMinutes();
			const currentTotalMinutes = (currentHours * 60) + currentMinutes;

			let activePrayerName = null;
			let prayerSchedules = [];

			document.querySelectorAll('.sholat-card').forEach(card => {
				const waktuText = card.getAttribute('data-waktu');
				const namaSholatEl = card.querySelector('.nama-sholat');
				if (!waktuText || !namaSholatEl) return;

				const namaSholat = namaSholatEl.textContent.trim();
				const [jadwalHours, jadwalMinutes] = waktuText.split(':').map(Number);
				let jadwalTotalMinutes = (jadwalHours * 60) + jadwalMinutes;

				// Simpan ke array untuk dibaca rotator induk secara lokal
				prayerSchedules.push({
					name: namaSholat,
					time: waktuText
				});

				// Kotak menyala 5 menit sebelum hingga 30 menit sesudah
				let startMinutes = jadwalTotalMinutes - 5;
				let endMinutes = jadwalTotalMinutes + 30;

				if (currentTotalMinutes >= startMinutes && currentTotalMinutes <= endMinutes) {
					card.classList.add('active');
					activePrayerName = namaSholat;
				} else {
					card.classList.remove('active');
				}
			});
		}

		updateDateTime();
		setInterval(updateDateTime, 1000);

		/* =====================================================
		   SMART NEXT PRAYER COUNTDOWN ENGINE
		   ===================================================== */
		@php
		$prayerData = [];
		$nonPrayerTimes = ['imsak', 'imsyak', 'terbit', 'syuruk', 'shuruk', 'sunrise', 'dhuha', 'duha'];
		if (isset($jadwalSholat) && count($jadwalSholat) > 0) {
			foreach ($jadwalSholat as $js) {
				if (in_array(strtolower(trim($js->nama_sholat)), $nonPrayerTimes)) {
					continue;
				}
				$prayerData[] = [
					'name' => strtoupper($js->nama_sholat),
					'time' => substr($js->waktu, 0, 5),
				];
			}
		}
		@endphp

		const prayerList = {!! json_encode($prayerData) !!};

		function updateNextPrayerBar() {
			const barEl = document.getElementById('nextPrayerBar');
			if (!barEl) return;

			if (!prayerList || prayerList.length === 0) return;

			const now = new Date();
			const currentSeconds = now.getHours() * 3600 + now.getMinutes() * 60 + now.getSeconds();

			let nextPrayer = null;
			let minDiff = Infinity;

			prayerList.forEach(p => {
				const parts = p.time.split(':');
				if (parts.length >= 2) {
					const prayerSeconds = parseInt(parts[0], 10) * 3600 + parseInt(parts[1], 10) * 60;
					let diff = prayerSeconds - currentSeconds;
					if (diff > 0 && diff < minDiff) {
						minDiff = diff;
						nextPrayer = p;
					}
				}
			});

			if (!nextPrayer && prayerList.length > 0) {
				nextPrayer = prayerList[0];
				const parts = nextPrayer.time.split(':');
				const prayerSeconds = parseInt(parts[0], 10) * 3600 + parseInt(parts[1], 10) * 60;
				minDiff = (86400 - currentSeconds) + prayerSeconds;
			}

			if (nextPrayer) {
				const elName = document.getElementById('npbPrayerName');
				const elTime = document.getElementById('npbScheduleTime');
				const elCd = document.getElementById('npbCountdown');

				let displayName = nextPrayer.name;
				if (now.getDay() === 5 && displayName === 'DZUHUR') {
					displayName = "SHOLAT JUM'AT";
				}

				if (elName) elName.textContent = displayName;
				if (elTime) elTime.textContent = nextPrayer.time + ' WIB';

				const h = Math.floor(minDiff / 3600);
				const m = Math.floor((minDiff % 3600) / 60);
				const s = minDiff % 60;

				const pad = (n) => String(n).padStart(2, '0');
				if (elCd) {
					elCd.textContent = `-${pad(h)}:${pad(m)}:${pad(s)}`;
				}
			}
		}

		setInterval(updateNextPrayerBar, 1000);
		updateNextPrayerBar();

		document.addEventListener('DOMContentLoaded', function() {
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

					// Cek apakah halaman sedang dikunci karena ada sholat aktif
					const isLocked = localStorage.getItem('lockPageRotation') === 'true';

					if (lastTimestamp && newTimestamp && newTimestamp !== lastTimestamp) {
						if (!isLocked) {
							setTimeout(() => {
								window.location.reload();
							}, 2000);
						}
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

				// Pencahayaan cerah, tajam dan jelas seperti foto aslinya, tetap adem dan tidak menyilaukan
				div.style.filter = 'brightness(0.96) contrast(1.06) saturate(1.08)';

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