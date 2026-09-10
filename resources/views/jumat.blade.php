<!-- resources/views/sholat-jumat.blade.php -->
<!DOCTYPE html>
<html lang="id">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Sistem Informasi Masjid - Jadwal Sholat Jumat</title>
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

		/* SECTION JUDUL DENGAN DUA IKON (NO 1 = ICON 1, NO 2 = ICON 2) */
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
			background: linear-gradient(135deg, rgba(2, 25, 17, 0.85) 0%, rgba(5, 42, 28, 0.8) 100%);
			border: 1.5px solid rgba(255, 215, 0, 0.65);
			border-radius: 40px;
			padding: 8px 30px;
			box-shadow: 
				0 8px 25px rgba(0, 0, 0, 0.65),
				0 0 20px rgba(255, 215, 0, 0.25),
				inset 0 1px 1px rgba(255, 255, 255, 0.2);
			backdrop-filter: blur(10px);
			-webkit-backdrop-filter: blur(10px);
		}

		.title-icon-badge {
			width: 60px;
			height: 60px;
			border-radius: 50%;
			background: rgba(4, 25, 18, 0.9);
			border: 1.5px solid rgba(255, 215, 0, 0.6);
			box-shadow: 0 4px 15px rgba(0, 0, 0, 0.5), 0 0 15px rgba(255, 215, 0, 0.25);
			display: flex;
			align-items: center;
			justify-content: center;
			flex-shrink: 0;
		}

		.title-icon-badge i {
			font-size: 1.85rem;
			color: #ffd700;
			filter: drop-shadow(0 0 8px rgba(255, 215, 0, 0.65));
		}

		.title-text-wrap h2 {
			font-size: 2.1rem;
			color: #ffffff !important;
			letter-spacing: 2px;
			font-weight: 700;
			text-shadow: 0 2px 8px rgba(0, 0, 0, 0.95);
			margin: 0;
			line-height: 1.15;
		}

		.title-text-wrap p {
			font-size: 1rem;
			color: #ffd700;
			letter-spacing: 0.5px;
			font-weight: 500;
			margin-top: 3px;
			margin-bottom: 0;
			text-shadow: 0 1px 4px rgba(0, 0, 0, 0.8);
		}

		/* LAYOUT KONTEN: FOTO IMAM DI KIRI (KOTAK NO 3) & INFO DI KANAN */
		.schedule-content-layout {
			display: flex;
			align-items: center;
			justify-content: center;
			gap: 22px;
			max-width: 1000px;
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

		/* KANAN: INFO ROWS STACK */
		.info-stack {
			display: flex;
			flex-direction: column;
			gap: 12px;
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
			padding: 11px 18px;
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
			font-size: 1.35rem;
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
			padding: 11px 20px;
			display: flex;
			align-items: center;
			justify-content: space-between;
			border: 1.5px solid rgba(255, 215, 0, 0.45);
			box-shadow: 0 8px 25px rgba(0, 0, 0, 0.35);
		}

		.info-box-value .value {
			font-size: 1.45rem;
			font-weight: 700;
			color: #ffffff;
			text-shadow: 0 2px 5px rgba(0, 0, 0, 0.8);
			white-space: nowrap;
		}

		.jadwal-mendatang-badge {
			background: #ffd700;
			color: #000000 !important;
			padding: 4px 14px;
			border-radius: 20px;
			font-size: 0.92rem;
			font-weight: 800 !important;
			display: inline-flex;
			align-items: center;
			gap: 6px;
			letter-spacing: 0.5px;
			box-shadow: 0 2px 8px rgba(0, 0, 0, 0.35);
			border: 1px solid rgba(255, 255, 255, 0.4);
			white-space: nowrap;
			flex-shrink: 0;
		}

		.jadwal-mendatang-badge.badge-hari-ini {
			background: #28a745 !important;
			color: #ffffff !important;
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

		/* Custom Theme Background Spesifik Sholat Jumat (Kemegahan Interior Raudhah Masjid Nabawi) */
		.display-background {
			background-image: url('{{ asset("image/display/background/bg_jumat.jpg") }}?v={{ time() }}') !important;
			background-position: center center !important;
			background-repeat: no-repeat !important;
			background-size: cover !important;
			filter: brightness(0.95) contrast(1.05) saturate(1.05) !important;
		}

		/* Overlay Penyejuk Syahdu & Khusyuk */
		.display-overlay {
			background: 
				radial-gradient(ellipse at 50% 45%, rgba(2, 14, 10, 0.04) 0%, rgba(1, 15, 10, 0.38) 100%),
				linear-gradient(180deg, 
					rgba(2, 16, 11, 0.72) 0%, 
					rgba(2, 16, 11, 0.35) 15%, 
					rgba(2, 16, 11, 0.06) 35%, 
					rgba(2, 16, 11, 0.06) 70%, 
					rgba(2, 16, 11, 0.45) 88%, 
					rgba(2, 16, 11, 0.75) 100%
				) !important;
		}

		/* =====================================================
		   FESTIVE & SACRED ANIMATIONS (THE RAUDHAH SANCTUARY)
		   ===================================================== */
		/* 1. Partikel Kilau Nur Jum'at (Floating Sacred Stardust) */
		.festive-particles {
			position: fixed;
			inset: 0;
			pointer-events: none;
			z-index: 1;
			overflow: hidden;
		}

		.stardust-particle {
			position: absolute;
			bottom: -20px;
			border-radius: 50%;
			background: radial-gradient(circle, #ffffff 0%, #fff7b2 35%, #ffd700 70%, transparent 100%);
			box-shadow: 0 0 8px #ffd700, 0 0 16px rgba(255, 215, 0, 0.7);
			will-change: transform, opacity;
			animation: floatUpStardust linear infinite;
		}

		@keyframes floatUpStardust {
			0% {
				transform: translateY(0) translateX(0) scale(0.6);
				opacity: 0;
			}
			15% {
				opacity: 0.9;
				transform: translateY(-15vh) translateX(12px) scale(1);
			}
			50% {
				opacity: 0.75;
				transform: translateY(-50vh) translateX(-14px) scale(1.15);
			}
			85% {
				opacity: 0.85;
				transform: translateY(-85vh) translateX(10px) scale(0.9);
			}
			100% {
				transform: translateY(-105vh) translateX(-6px) scale(0.4);
				opacity: 0;
			}
		}

		/* 2. Pendaran Cahaya Lampu Kubah Raudhah */
		.lantern-glow-ambient {
			position: fixed;
			top: -40px;
			left: 50%;
			transform: translateX(-50%);
			width: 480px;
			height: 280px;
			border-radius: 50%;
			background: radial-gradient(ellipse, rgba(255, 230, 130, 0.28) 0%, rgba(255, 180, 0, 0.12) 45%, rgba(255, 140, 0, 0.03) 70%, transparent 85%);
			pointer-events: none;
			z-index: 0;
			filter: blur(25px);
			animation: divineChandelierGlow 5s infinite alternate ease-in-out;
		}

		@keyframes divineChandelierGlow {
			0% {
				opacity: 0.55;
				transform: translateX(-50%) scale(0.95);
			}
			100% {
				opacity: 0.95;
				transform: translateX(-50%) scale(1.05);
				filter: blur(30px);
			}
		}

		/* 3. Aura Medali Kaligrafi 3D Emas Menyala */
		.kaligrafi-medallion {
			animation: medallionAuraPulse 4.5s infinite alternate ease-in-out;
		}
		.kaligrafi-muhammad {
			animation-delay: 0.5s;
		}
		@keyframes medallionAuraPulse {
			0% {
				filter: drop-shadow(0 0 10px rgba(255, 215, 0, 0.65));
			}
			100% {
				filter: drop-shadow(0 0 22px rgba(255, 230, 100, 0.95)) drop-shadow(0 0 35px rgba(255, 175, 0, 0.5));
			}
		}

		/* 4. Sapuan Kilau Emas pada Judul (Metallic Shimmer Sweep) */
		.title-with-icons {
			position: relative;
			overflow: hidden;
		}

		.title-with-icons::after {
			content: '';
			position: absolute;
			top: -60%;
			left: -130%;
			width: 60%;
			height: 220%;
			background: linear-gradient(
				115deg,
				transparent 35%,
				rgba(255, 255, 255, 0.2) 45%,
				rgba(255, 245, 180, 0.75) 50%,
				rgba(255, 255, 255, 0.95) 53%,
				rgba(255, 245, 180, 0.75) 56%,
				rgba(255, 255, 255, 0.2) 65%,
				transparent 75%
			);
			transform: rotate(25deg);
			animation: goldShimmerSweep 7s infinite ease-in-out;
			pointer-events: none;
		}

		@keyframes goldShimmerSweep {
			0%, 20% {
				left: -130%;
				opacity: 0;
			}
			32% {
				opacity: 1;
			}
			48%, 100% {
				left: 210%;
				opacity: 0;
			}
		}

		/* 5. Efek Pendaran Emas pada Border Kartu (Luxury Sheen) */
		.imam-card-box,
		.info-box-label,
		.info-box-value {
			animation: goldenBorderSheen 6s infinite ease-in-out;
		}
		.info-box-value {
			animation-delay: 0.7s;
		}

		@keyframes goldenBorderSheen {
			0%, 100% {
				border-color: rgba(255, 215, 0, 0.45);
				box-shadow: 0 8px 25px rgba(0, 0, 0, 0.35), 0 0 10px rgba(255, 215, 0, 0.1);
			}
			50% {
				border-color: rgba(255, 235, 120, 0.85);
				box-shadow: 0 8px 28px rgba(0, 0, 0, 0.42), 0 0 18px rgba(255, 215, 0, 0.32);
			}
		}

		/* 6. Kaligrafi Sholawat Nabi Berpendar Emas */
		.sholawat-greeting {
			font-family: 'Amiri', serif;
			font-size: 1.28rem;
			color: #fff4b8;
			text-shadow: 0 0 12px rgba(255, 215, 0, 0.85), 0 2px 6px rgba(0, 0, 0, 0.9);
			margin-top: 3px;
			letter-spacing: 1px;
			line-height: 1.2;
		}

		/* 7. Pita Mutiara Sunnah Jum'at & Dzikir Mustajab */
		.jumat-hadits-box {
			margin: 10px auto 0 auto;
			max-width: 1000px;
			width: 100%;
			background: linear-gradient(135deg, rgba(2, 25, 17, 0.88) 0%, rgba(4, 40, 26, 0.88) 100%);
			border: 1.2px solid rgba(255, 215, 0, 0.55);
			border-radius: 30px;
			padding: 7px 25px;
			box-shadow: 0 6px 20px rgba(0, 0, 0, 0.5), 0 0 15px rgba(255, 215, 0, 0.2);
			backdrop-filter: blur(10px);
			-webkit-backdrop-filter: blur(10px);
			display: flex;
			align-items: center;
			justify-content: center;
			gap: 15px;
			text-align: center;
		}

		.jumat-hadits-box i {
			color: #ffd700;
			font-size: 1.2rem;
			filter: drop-shadow(0 0 6px rgba(255, 215, 0, 0.7));
			flex-shrink: 0;
		}

		.jumat-hadits-text {
			font-size: 1.05rem;
			font-weight: 500;
			color: #ffffff;
			letter-spacing: 0.3px;
			transition: opacity 0.5s ease-in-out;
		}

		.jumat-hadits-text strong {
			color: #ffd700;
		}
	</style>
</head>

<body>
	<div class="display-background"></div>
	<div class="display-overlay"></div>
	<div class="lantern-glow-ambient"></div>
	<div class="festive-particles" id="festiveParticles"></div>
	@include('partials.medallion-header')

	<div class="container">
		<!-- HEADER DENGAN LOCK PIXEL SAMA PERSIS -->
		<div class="header">
			<h1>{{ $settings['nama_aplikasi'] ?? 'MASJID JAMI\' AL JIHAD' }}</h1>
			<h3 class="sub-header">SISTEM INFORMASI DIGITAL</h3>
			<div class="datetime" id="datetime"></div>
		</div>

		<!-- SECTION JUDUL: NO 1 (MASJID) DI KIRI, NO 2 (KA'BAH) DI KANAN -->
		<div class="schedule-header-section">
			<div class="title-with-icons">
				<div class="title-icon-badge left-icon" title="Masjid">
					<i class="fas fa-mosque"></i>
				</div>
				<div class="title-text-wrap">
					<h2>Jadwal Sholat Jumat</h2>
					<p>Informasi Imam, Khotib, Muadzin & Bilal</p>
					<div class="sholawat-greeting">اللَّهُمَّ صَلِّ عَلَى سَيِّدِنَا مُحَمَّدٍ وَعَلَى آلِ سَيِّدِنَا مُحَمَّدٍ</div>
				</div>
				<div class="title-icon-badge right-icon" title="Ka'bah">
					<i class="fas fa-kaaba"></i>
				</div>
			</div>
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
				'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => "Jum'at", 'Saturday' => 'Sabtu'
			];
			$bulanMap = [
				'Jan' => 'Jan', 'Feb' => 'Feb', 'Mar' => 'Mar',
				'Apr' => 'Apr', 'May' => 'Mei', 'Jun' => 'Jun',
				'Jul' => 'Jul', 'Aug' => 'Agu', 'Sep' => 'Sep',
				'Oct' => 'Okt', 'Nov' => 'Nov', 'Dec' => 'Des'
			];

			$hariInggris = $tanggalObj->format('l');
			$hari = isset($hariMap[$hariInggris]) ? $hariMap[$hariInggris] : $hariInggris;
			$tanggal = $tanggalObj->format('j');
			$bulanInggris = $tanggalObj->format('M');
			$bulan = isset($bulanMap[$bulanInggris]) ? $bulanMap[$bulanInggris] : $bulanInggris;
			$tahun = $tanggalObj->format('y');
			$formattedDate = "$hari, $tanggal $bulan '$tahun";
		@endphp

		<!-- LAYOUT UTAMA: KOTAK NO 3 (FOTO IMAM) DI KIRI & INFO ROWS DI KANAN -->
		<div class="schedule-content-layout">
			<!-- KOTAK NO 3: FOTO IMAM -->
			<div class="imam-card-box">
				<div class="imam-photo-frame">
					<img src="{{ !empty($sholatJumat->foto_imam) ? asset('storage/' . $sholatJumat->foto_imam) : asset('image/display/default_imam.jpg') }}" alt="Foto Imam" class="imam-photo" onerror="this.onerror=null;this.src='{{ asset('image/display/default_imam.jpg') }}';">
					<div class="imam-badge-overlay">
						<span class="badge-role"><i class="fas fa-quran mr-1"></i> Imam & Khotib</span>
						<span class="badge-name">{{ $sholatJumat->imam ?? $sholatJumat->khatib ?? 'Ustd. Imam & Khotib' }}</span>
					</div>
				</div>
			</div>

			<!-- KANAN: INFO ROWS (TANGGAL, IMAM & KHOTIB, MUADZIN, BILAL) -->
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
						@elseif($isMendatang)
						<span class="jadwal-mendatang-badge"><i class="fas fa-calendar-week mr-1"></i> Jumat Mendatang</span>
						@endif
					</div>
				</div>

				<!-- Row 2: Imam & Khotib (1 Kolom) -->
				<div class="info-row">
					<div class="info-box-label">
						<i class="fas fa-user-tie"></i> Imam & Khotib
					</div>
					<div class="info-box-value">
						<span class="value">
							@if(!empty($sholatJumat->imam) && !empty($sholatJumat->khatib) && $sholatJumat->imam !== $sholatJumat->khatib)
								{{ $sholatJumat->imam }} / {{ $sholatJumat->khatib }}
							@else
								{{ $sholatJumat->imam ?? $sholatJumat->khatib ?? 'Belum Ditetapkan' }}
							@endif
						</span>
					</div>
				</div>

				<!-- Row 3: Muadzin -->
				<div class="info-row">
					<div class="info-box-label">
						<i class="fas fa-microphone-alt"></i> Muadzin
					</div>
					<div class="info-box-value">
						<span class="value">{{ $sholatJumat->muadzin ?? 'Belum Ditetapkan' }}</span>
					</div>
				</div>

				<!-- Row 4: Bilal -->
				<div class="info-row">
					<div class="info-box-label">
						<i class="fas fa-bullhorn"></i> Bilal
					</div>
					<div class="info-box-value">
						<span class="value">{{ $sholatJumat->bilal ?? 'Belum Ditetapkan' }}</span>
					</div>
				</div>
			</div>
		</div>
		@else
		<div class="no-data">
			<i class="fas fa-calendar-times"></i>
			<p>Belum ada jadwal Sholat Jumat untuk minggu mendatang</p>
		</div>
		@endif

		<!-- Pita Mutiara Sunnah Jum'at (Dzikir & Do'a Mustajab) -->
		<div class="jumat-hadits-box">
			<i class="fas fa-quran"></i>
			<div class="jumat-hadits-text" id="jumatHaditsRotator">
				<strong>Keutamaan Hari Jum'at:</strong> "Perbanyaklah shalawat kepadaku di hari Jum'at, karena shalawatmu disampaikan kepadaku." (HR. Abu Dawud)
			</div>
			<i class="fas fa-hands-praying"></i>
		</div>

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
		if (document.getElementById('datetime')) {
			updateDateTime();
			setInterval(updateDateTime, 1000);
		}
	</script>

	<!-- Script Tema Raudhah Nabawi, Floating Stardust & Dzikir Rotator -->
	<script>
		document.addEventListener('DOMContentLoaded', function () {
			// 1. Set Background Interior Raudhah Nabawi dengan Cache-Buster
			const bgElement = document.querySelector('.display-background') || document.body;
			if (bgElement) {
				const raudhahBg = new Image();
				raudhahBg.src = "{{ asset('image/display/background/bg_jumat.jpg') }}?v={{ time() }}";
				raudhahBg.onload = function () {
					bgElement.style.backgroundImage = `url('${raudhahBg.src}')`;
					bgElement.style.opacity = '1';
				};
			}

			// 2. Generator Butiran Debu Cahaya Emas / Nur Jum'at (Floating Stardust Particles)
			const particleContainer = document.getElementById('festiveParticles');
			if (particleContainer) {
				const particleCount = 28;
				for (let i = 0; i < particleCount; i++) {
					const p = document.createElement('div');
					p.className = 'stardust-particle';
					
					const size = Math.random() * 4 + 2; // 2px - 6px
					const left = Math.random() * 100; // 0% - 100%
					const duration = Math.random() * 8 + 7; // 7s - 15s
					const delay = Math.random() * 10; // 0s - 10s
					const opacity = Math.random() * 0.5 + 0.45; // 0.45 - 0.95

					p.style.width = `${size}px`;
					p.style.height = `${size}px`;
					p.style.left = `${left}%`;
					p.style.animationDuration = `${duration}s`;
					p.style.animationDelay = `${delay}s`;
					p.style.opacity = opacity;

					particleContainer.appendChild(p);
				}
			}

			// 3. Rotator Mutiara Sunnah Jum'at, Dzikir & Do'a Mustajab
			const haditsList = [
				{
					topic: "Keutamaan Shalawat:",
					text: '"Perbanyaklah shalawat kepadaku di hari Jum\'at, karena shalawatmu disampaikan kepadaku." (HR. Abu Dawud)'
				},
				{
					topic: "Sayyidul Ayyam (Rajanya Hari):",
					text: '"Sebaik-baik hari di mana matahari terbit adalah hari Jum\'at; padanya Adam diciptakan dan padanya ia dimasukkan ke surga." (HR. Muslim)'
				},
				{
					topic: "Waktu Mustajab Berdo'a:",
					text: '"Di hari Jum\'at ada satu waktu, tidaklah seorang hamba muslim memohon kebaikan kepada Allah melainkan pasti dikabulkan." (HR. Bukhari & Muslim)'
				},
				{
					topic: "Cahaya Surat Al-Kahfi:",
					text: '"Barangsiapa membaca Surat Al-Kahfi pada hari Jum\'at, maka akan dipancarkan cahaya baginya di antara dua Jum\'at." (HR. An-Nasa\'i & Al-Hakim)'
				},
				{
					topic: "Dzikir & Penghapus Dosa:",
					text: '"Shalat lima waktu dan shalat Jum\'at ke Jum\'at berikutnya adalah penggugur dosa di antara keduanya selama dosa besar dijauhi." (HR. Muslim)'
				}
			];

			let currentHaditsIndex = 0;
			const haditsRotatorEl = document.getElementById('jumatHaditsRotator');

			if (haditsRotatorEl && haditsList.length > 0) {
				setInterval(function () {
					haditsRotatorEl.style.opacity = '0';
					setTimeout(function () {
						currentHaditsIndex = (currentHaditsIndex + 1) % haditsList.length;
						const item = haditsList[currentHaditsIndex];
						haditsRotatorEl.innerHTML = `<strong>${item.topic}</strong> ${item.text}`;
						haditsRotatorEl.style.opacity = '1';
					}, 500);
				}, 9000);
			}
		});
	</script>
</body>

</html>