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

		.title-icon-badge img {
			width: 40px;
			height: 40px;
			object-fit: contain;
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

		/* Custom Theme Background Spesifik Sholat Idul Adha (Flipped: Hewan di Kanan, Masjid di Kiri) */
		.display-background {
			background-image: url('{{ asset("image/display/background/bg_idul_adha.jpg") }}?v={{ time() }}') !important;
			background-position: center center !important;
			background-repeat: no-repeat !important;
			background-size: cover !important;
			filter: brightness(0.96) contrast(1.05) saturate(1.05) !important;
		}

		/* Overlay Penyejuk Elegan: Menjaga Keterbacaan Teks Sekaligus Menonjolkan Nuansa Hewan Qurban & Masjid */
		.display-overlay {
			background: 
				radial-gradient(ellipse at 50% 50%, rgba(2, 14, 10, 0.04) 0%, rgba(1, 15, 10, 0.38) 100%),
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
		   FESTIVE LUXURY ANIMATIONS (THE GOLDEN TRIO)
		   ===================================================== */
		/* 1. Partikel Kilau Emas Mengambang (Floating Stardust) */
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

		/* 2. Pendaran Hangat Lentera Kanan Atas */
		.lantern-glow-ambient {
			position: fixed;
			top: -40px;
			right: 30px;
			width: 320px;
			height: 320px;
			border-radius: 50%;
			background: radial-gradient(circle, rgba(255, 215, 0, 0.32) 0%, rgba(255, 160, 0, 0.16) 45%, rgba(255, 140, 0, 0.04) 68%, transparent 80%);
			pointer-events: none;
			z-index: 0;
			filter: blur(18px);
			animation: lanternBreath 4s infinite alternate ease-in-out;
		}

		@keyframes lanternBreath {
			0% {
				opacity: 0.55;
				transform: scale(0.94);
			}
			100% {
				opacity: 0.95;
				transform: scale(1.06);
				filter: blur(24px);
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

		/* 6. Animasi Ikon Hewan Qurban */
		.title-icon-badge img {
			animation: festiveIconPulse 3.5s infinite ease-in-out;
		}

		@keyframes festiveIconPulse {
			0%, 100% {
				filter: drop-shadow(0 0 8px rgba(255, 215, 0, 0.65));
				transform: scale(1);
			}
			50% {
				filter: drop-shadow(0 0 18px rgba(255, 240, 140, 0.98));
				transform: scale(1.05);
			}
		}

		/* 7. Ucapan Selamat Hari Raya Kaligrafi Emas */
		.festive-greeting {
			font-family: 'Amiri', serif;
			font-size: 1.25rem;
			color: #fff4b8;
			text-shadow: 0 0 10px rgba(255, 215, 0, 0.8), 0 2px 5px rgba(0, 0, 0, 0.9);
			margin-top: 3px;
			letter-spacing: 1px;
			line-height: 1.2;
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
					<div class="festive-greeting">عِيدٌ مُبَارَكٌ • تَقَبَّلَ اللَّهُ مِنَّا وَمِنْكُمْ</div>
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
						<span class="badge-role"><i class="fas fa-quran mr-1"></i> Imam & Khotib</span>
						<span class="badge-name">{{ $idulAdha->imam ?? $idulAdha->khatib ?? 'Ustd. Imam & Khotib' }}</span>
					</div>
				</div>
			</div>

			<!-- KANAN: INFO ROWS (TANGGAL, WAKTU, IMAM & KHOTIB, MUADZIN, BILAL) -->
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

				<!-- Row 3: Imam & Khotib (1 Kolom) -->
				<div class="info-row">
					<div class="info-box-label">
						<i class="fas fa-user-tie"></i> Imam & Khotib
					</div>
					<div class="info-box-value">
						<span class="value">
							@if(!empty($idulAdha->imam) && !empty($idulAdha->khatib) && $idulAdha->imam !== $idulAdha->khatib)
								{{ $idulAdha->imam }} / {{ $idulAdha->khatib }}
							@else
								{{ $idulAdha->imam ?? $idulAdha->khatib ?? 'Belum Ditetapkan' }}
							@endif
						</span>
					</div>
				</div>

				@php
					$muadzinAdha = $idulAdha->muadzin ?? null;
					$bilalAdha = $idulAdha->bilal ?? null;
					if (empty($bilalAdha) && !empty($muadzinAdha) && stripos($muadzinAdha, 'bilal') !== false) {
						$bilalAdha = $muadzinAdha;
						$muadzinAdha = 'Belum Ditetapkan';
					}
				@endphp

				<!-- Row 4: Muadzin (Kolom Tersendiri) -->
				<div class="info-row">
					<div class="info-box-label">
						<i class="fas fa-microphone-alt"></i> Muadzin
					</div>
					<div class="info-box-value">
						<span class="value">{{ !empty($muadzinAdha) ? $muadzinAdha : 'Belum Ditetapkan' }}</span>
					</div>
				</div>

				<!-- Row 5: Bilal (Kolom Tersendiri) -->
				<div class="info-row">
					<div class="info-box-label">
						<i class="fas fa-bullhorn"></i> Bilal
					</div>
					<div class="info-box-value">
						<span class="value">{{ !empty($bilalAdha) ? $bilalAdha : 'Belum Ditetapkan' }}</span>
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

	<!-- Script Background Khusus Idul Adha (Gambar Pilihan DKM - Hewan di Kanan) -->
	<script>
		const bgElement = document.querySelector('.display-background') || document.body;
		if (bgElement) {
			bgElement.style.setProperty('background-image', "url('{{ asset('image/display/background/bg_idul_adha.jpg') }}?v={{ time() }}')", 'important');
			bgElement.style.setProperty('background-size', 'cover', 'important');
			bgElement.style.setProperty('background-position', 'center center', 'important');
			bgElement.style.setProperty('background-repeat', 'no-repeat', 'important');
		}
	</script>

	<!-- Generator Partikel Kilau Emas Mengambang (Floating Golden Stardust) -->
	<script>
		(function initFestiveParticles() {
			const container = document.getElementById('festiveParticles');
			if (!container) return;
			const particleCount = 28;
			for (let i = 0; i < particleCount; i++) {
				const p = document.createElement('div');
				p.className = 'stardust-particle';
				const size = (Math.random() * 3.5 + 2.5).toFixed(1);
				p.style.width = size + 'px';
				p.style.height = size + 'px';
				p.style.left = (Math.random() * 100).toFixed(2) + '%';
				p.style.animationDuration = (Math.random() * 8 + 8).toFixed(1) + 's';
				p.style.animationDelay = (Math.random() * 10).toFixed(1) + 's';
				container.appendChild(p);
			}
		})();
	</script>
</body>

</html>