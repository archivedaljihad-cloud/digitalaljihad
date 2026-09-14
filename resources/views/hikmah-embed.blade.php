<!-- resources/views/hikmah-embed.blade.php -->
<!DOCTYPE html>
<html lang="id">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>{{ $setting->nama_aplikasi ?? 'Sistem Informasi Masjid' }} - Mutiara Hadits & Hikmah Harian</title>
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
			color: var(--text-light, #ffffff);
			height: 100vh;
			width: 100vw;
			display: flex;
			justify-content: center;
			align-items: center;
			position: relative;
			overflow: hidden;
			background: linear-gradient(135deg, #071a10 0%, #0e3521 50%, #1a5235 100%);
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
			width: 100%;
			max-width: 1600px;
			height: 100vh;
			display: flex;
			flex-direction: column;
			justify-content: space-between;
			padding: 15px 30px 85px 30px;
			box-sizing: border-box;
			position: relative;
			z-index: 5;
		}

		/* Header */
		.header {
			display: flex;
			justify-content: space-between;
			align-items: center;
			padding: 10px 20px;
			background: rgba(7, 26, 16, 0.65);
			backdrop-filter: blur(12px);
			border: 1px solid rgba(201, 160, 61, 0.35);
			border-radius: 18px;
			box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
		}

		.logo-section {
			display: flex;
			align-items: center;
			gap: 15px;
		}

		.logo-img {
			width: 60px;
			height: 60px;
			border-radius: 50%;
			border: 2px solid #ffd700;
			padding: 3px;
			background: #ffffff;
			object-fit: cover;
		}

		.masjid-title {
			font-family: 'Amiri', serif;
			font-size: 1.8rem;
			font-weight: 700;
			color: #ffd700;
			text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
			line-height: 1.2;
		}

		.masjid-subtitle {
			font-size: 0.9rem;
			color: rgba(255, 255, 255, 0.85);
			font-weight: 400;
		}

		.clock-section {
			text-align: right;
		}

		.live-time {
			font-size: 2.2rem;
			font-weight: 700;
			color: #ffd700;
			line-height: 1;
			letter-spacing: 1px;
			text-shadow: 0 2px 8px rgba(0,0,0,0.5);
		}

		.live-date {
			font-size: 0.88rem;
			color: rgba(255, 255, 255, 0.85);
			margin-top: 4px;
		}

		/* Main Content Card */
		.main-content {
			flex: 1;
			display: flex;
			flex-direction: column;
			justify-content: center;
			align-items: center;
			padding: 20px 0;
		}

		.hadith-card {
			width: 100%;
			max-width: 1400px;
			background: rgba(14, 53, 33, 0.7);
			backdrop-filter: blur(16px);
			border: 2px solid rgba(201, 160, 61, 0.4);
			border-radius: 26px;
			padding: 35px 50px;
			box-shadow: 0 16px 48px rgba(0, 0, 0, 0.4), inset 0 0 30px rgba(201, 160, 61, 0.08);
			position: relative;
			text-align: center;
		}

		.theme-badge {
			display: inline-flex;
			align-items: center;
			gap: 8px;
			padding: 8px 24px;
			background: linear-gradient(135deg, rgba(201, 160, 61, 0.25), rgba(255, 215, 0, 0.15));
			border: 1px solid rgba(201, 160, 61, 0.6);
			border-radius: 30px;
			color: #ffd700;
			font-size: 1.1rem;
			font-weight: 600;
			margin-bottom: 25px;
			text-transform: uppercase;
			letter-spacing: 1px;
			box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
		}

		/* Matan Hadits Arab (Center Tengah) */
		.matan-arab {
			font-family: 'Amiri', serif;
			font-size: 2.5rem;
			line-height: 2.3;
			color: #ffffff;
			text-shadow: 0 2px 8px rgba(0, 0, 0, 0.6);
			margin-bottom: 25px;
			padding: 0 20px;
			direction: rtl;
			text-align: center;
		}

		.divider-ornament {
			display: flex;
			align-items: center;
			justify-content: center;
			gap: 15px;
			margin: 15px auto 25px;
			width: 60%;
		}

		.divider-line {
			flex: 1;
			height: 1px;
			background: linear-gradient(to right, transparent, rgba(201, 160, 61, 0.6), transparent);
		}

		/* Terjemahan Indonesia */
		.terjemahan-text {
			font-size: 1.35rem;
			line-height: 1.8;
			color: rgba(255, 255, 255, 0.95);
			max-width: 1200px;
			margin: 0 auto 20px;
			font-weight: 400;
			font-style: italic;
			text-shadow: 0 1px 3px rgba(0,0,0,0.5);
		}

		.perawi-tag {
			display: inline-block;
			font-size: 1rem;
			font-weight: 700;
			color: #ffd700;
			background: rgba(0, 0, 0, 0.35);
			padding: 6px 20px;
			border-radius: 20px;
			border: 1px solid rgba(201, 160, 61, 0.4);
			margin-bottom: 20px;
		}

		/* Sari Hikmah Box */
		.hikmah-box {
			background: rgba(7, 26, 16, 0.7);
			border: 1px solid rgba(40, 167, 69, 0.4);
			border-radius: 16px;
			padding: 12px 25px;
			max-width: 1000px;
			margin: 0 auto;
			display: flex;
			align-items: center;
			justify-content: center;
			gap: 12px;
		}

		.hikmah-box i {
			color: #ffd700;
			font-size: 1.2rem;
		}

		.hikmah-box p {
			margin: 0;
			font-size: 1.05rem;
			color: #d1fae5;
			font-weight: 500;
		}
	</style>
</head>

<body>
	<div class="kaligrafi kaligrafi-allah">الله</div>
	<div class="kaligrafi kaligrafi-muhammad">محمد</div>

	<div class="container">
		<!-- Header -->
		<div class="header">
			<div class="logo-section">
				<img src="{{ isset($setting->logo) && $setting->logo ? asset('storage/' . $setting->logo) : asset('img/default-logo.png') }}"
					 alt="Logo Masjid" class="logo-img">
				<div>
					<div class="masjid-title">{{ $setting->nama_aplikasi ?? "MASJID JAMI' AL JIHAD" }}</div>
					<div class="masjid-subtitle">Mutiara Hadits & Hikmah Harian Jamaah</div>
				</div>
			</div>

			<div class="clock-section">
				<div class="live-time" id="clock">00:00:00</div>
				<div class="live-date" id="date">Memuat...</div>
			</div>
		</div>

		<!-- Main Hadits Content -->
		<div class="main-content">
			<div class="hadith-card">
				<div class="theme-badge">
					<i class="fas fa-star-and-crescent"></i>
					<span>{{ $hikmah['tema'] ?? 'Mutiara Hadits Pilihan' }}</span>
				</div>

				<!-- Matan Arab Rata Tengah -->
				<div class="matan-arab">
					{{ $hikmah['arab'] ?? 'مَنْ سَلَكَ طَرِيقًا يَلْتَمِسُ فِيهِ عِلْمًا، سَهَّلَ اللَّهُ لَهُ بِهِ طَرِيقًا إِلَى الْجَنَّةِ' }}
				</div>

				<div class="divider-ornament">
					<div class="divider-line"></div>
					<i class="fas fa-gem" style="color: #ffd700; font-size: 14px;"></i>
					<div class="divider-line"></div>
				</div>

				<!-- Terjemahan -->
				<div class="terjemahan-text">
					"{{ $hikmah['terjemahan'] ?? 'Barangsiapa menempuh suatu jalan untuk menuntut ilmu (agama), maka Allah akan memudahkan baginya jalan menuju surga.' }}"
				</div>

				<!-- Perawi -->
				<div>
					<span class="perawi-tag">
						<i class="fas fa-book-open mr-2"></i> {{ $hikmah['perawi'] ?? 'HR. Muslim' }}
					</span>
				</div>

				<!-- Intisari Hikmah -->
				@if(!empty($hikmah['hikmah']))
				<div class="hikmah-box">
					<i class="fas fa-lightbulb"></i>
					<p><strong>Tadabbur Hari Ini:</strong> {{ $hikmah['hikmah'] }}</p>
				</div>
				@endif
			</div>
		</div>
	</div>

	<script>
		// Live Clock & Date
		function updateClock() {
			const now = new Date();
			const hours = String(now.getHours()).padStart(2, '0');
			const minutes = String(now.getMinutes()).padStart(2, '0');
			const seconds = String(now.getSeconds()).padStart(2, '0');
			document.getElementById('clock').textContent = `${hours}:${minutes}:${seconds}`;

			const days = ['Ahad', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
			const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

			const dayName = days[now.getDay()];
			const date = now.getDate();
			const monthName = months[now.getMonth()];
			const year = now.getFullYear();

			document.getElementById('date').textContent = `${dayName}, ${date} ${monthName} ${year}`;
		}

		setInterval(updateClock, 1000);
		updateClock();
	</script>
</body>

</html>
