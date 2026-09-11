<!-- resources/views/infaq-embed.blade.php -->
<!DOCTYPE html>
<html lang="id">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Sistem Informasi Masjid - Penggalangan Infaq & Donasi Khusus</title>
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

		.main-content {
			display: flex;
			justify-content: center;
			align-items: center;
			flex: 1;
			margin: 2px 0;
			min-height: 0;
		}

		.panel {
			background: rgba(4, 25, 18, 0.76);
			border-radius: 18px;
			padding: 14px 24px;
			backdrop-filter: blur(14px);
			-webkit-backdrop-filter: blur(14px);
			box-shadow: 0 15px 45px rgba(0, 0, 0, 0.5);
			border: 1.5px solid rgba(255, 215, 0, 0.45);
			width: 100%;
			max-width: 1440px;
			display: flex;
			flex-direction: column;
		}

		/* Judul Program Banner */
		.program-title-banner {
			text-align: center;
			margin-bottom: 10px;
			position: relative;
		}

		.program-title-banner h2 {
			font-size: 1.8rem;
			font-weight: 700;
			color: #ffffff !important;
			letter-spacing: 1.5px;
			text-transform: uppercase;
			text-shadow: 0 2px 8px rgba(0, 0, 0, 0.95);
			margin: 0 auto 8px auto;
			display: inline-flex;
			align-items: center;
			justify-content: center;
			padding: 7px 32px;
			background: linear-gradient(135deg, rgba(2, 25, 17, 0.85) 0%, rgba(5, 42, 28, 0.8) 100%);
			border: 1.5px solid rgba(255, 215, 0, 0.65);
			border-radius: 35px;
			box-shadow: 0 8px 25px rgba(0, 0, 0, 0.65), 0 0 20px rgba(255, 215, 0, 0.25);
			backdrop-filter: blur(10px);
			-webkit-backdrop-filter: blur(10px);
		}

		.program-title-banner h2 i {
			color: #ffd700;
			margin-right: 10px;
			filter: drop-shadow(0 0 8px rgba(255, 215, 0, 0.65));
		}

		.program-title-banner .divider-line {
			display: none;
		}

		/* Summary Stats Grid */
		.summary-grid {
			display: grid;
			grid-template-columns: repeat(4, 1fr);
			gap: 12px;
			margin-bottom: 10px;
		}

		.stat-pill {
			background: rgba(0, 0, 0, 0.45);
			border-radius: 12px;
			padding: 8px 14px;
			display: flex;
			align-items: center;
			gap: 12px;
			border: 1px solid rgba(255, 215, 0, 0.3);
			box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
		}

		.stat-pill .icon-box {
			width: 44px;
			height: 44px;
			border-radius: 10px;
			display: flex;
			align-items: center;
			justify-content: center;
			font-size: 1.35rem;
			flex-shrink: 0;
		}

		.stat-pill.target .icon-box {
			background: rgba(0, 180, 216, 0.25);
			color: #00b4d8;
			border: 1px solid rgba(0, 180, 216, 0.4);
		}

		.stat-pill.terkumpul .icon-box {
			background: rgba(0, 230, 118, 0.25);
			color: #00e676;
			border: 1px solid rgba(0, 230, 118, 0.4);
		}

		.stat-pill.kekurangan .icon-box {
			background: rgba(255, 82, 82, 0.25);
			color: #ff5252;
			border: 1px solid rgba(255, 82, 82, 0.4);
		}

		.stat-pill.donatur .icon-box {
			background: rgba(255, 215, 0, 0.25);
			color: #ffd700;
			border: 1px solid rgba(255, 215, 0, 0.4);
		}

		.stat-pill .stat-info {
			display: flex;
			flex-direction: column;
			min-width: 0;
		}

		.stat-pill .stat-label {
			font-size: 0.75rem;
			text-transform: uppercase;
			letter-spacing: 0.8px;
			font-weight: 600;
			color: rgba(255, 255, 255, 0.85);
			white-space: nowrap;
		}

		.stat-pill .stat-value {
			font-size: 1.22rem;
			font-weight: 800;
			letter-spacing: 0.5px;
			white-space: nowrap;
			text-shadow: 0 2px 4px rgba(0, 0, 0, 0.8);
		}

		.stat-pill.target .stat-value { color: #00b4d8; }
		.stat-pill.terkumpul .stat-value {
			color: #00e676;
			-webkit-text-stroke: 0.35px #ffffff;
			text-shadow:
				-1px -1px 0 #ffffff,
				 1px -1px 0 #ffffff,
				-1px  1px 0 #ffffff,
				 1px  1px 0 #ffffff,
				 0 2px 8px rgba(0, 0, 0, 0.9);
		}
		.stat-pill.kekurangan .stat-value { color: #ff5252; }
		.stat-pill.donatur .stat-value { color: #ffd700; }

		/* Glossy Progress Bar */
		.progress-section {
			background: rgba(0, 0, 0, 0.35);
			border-radius: 10px;
			padding: 7px 14px;
			margin-bottom: 10px;
			border: 1px solid rgba(255, 215, 0, 0.25);
		}

		.progress-header {
			display: flex;
			justify-content: space-between;
			align-items: center;
			margin-bottom: 4px;
			font-size: 0.85rem;
			font-weight: 700;
			letter-spacing: 0.5px;
		}

		.progress-bar-track {
			height: 18px;
			background: rgba(255, 255, 255, 0.1);
			border-radius: 10px;
			overflow: hidden;
			position: relative;
			box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.6);
		}

		.progress-bar-fill {
			height: 100%;
			background: linear-gradient(90deg, #00b4d8, #00e676, #ffd700);
			border-radius: 10px;
			transition: width 1s ease-in-out;
			position: relative;
			box-shadow: 0 0 12px rgba(0, 230, 118, 0.6);
		}

		.progress-bar-fill::after {
			content: '';
			position: absolute;
			top: 0; left: 0; bottom: 0; right: 0;
			background-image: linear-gradient(
				-45deg,
				rgba(255, 255, 255, 0.2) 25%,
				transparent 25%,
				transparent 50%,
				rgba(255, 255, 255, 0.2) 50%,
				rgba(255, 255, 255, 0.2) 75%,
				transparent 75%,
				transparent
			);
			background-size: 30px 30px;
			animation: move-stripes 2s linear infinite;
		}

		@keyframes move-stripes {
			0% { background-position: 0 0; }
			100% { background-position: 30px 0; }
		}

		/* Table Donatur */
		.table-wrapper {
			max-height: 380px;
			min-height: 340px;
			overflow: hidden;
			position: relative;
			border-radius: 12px;
			border: 1px solid rgba(255, 215, 0, 0.3);
			background: rgba(0, 0, 0, 0.25);
			box-shadow: inset 0 0 20px rgba(0, 0, 0, 0.5);
		}

		table {
			width: 100%;
			border-collapse: collapse;
			table-layout: fixed;
		}

		th {
			position: sticky;
			top: 0;
			z-index: 20;
			background: #03140f !important;
			color: #ffd700;
			font-size: 1.15rem;
			font-weight: 700;
			letter-spacing: 1px;
			padding: 10px 14px;
			border-bottom: 2px solid rgba(255, 215, 0, 0.65);
			box-shadow: 0 4px 10px rgba(0, 0, 0, 0.6);
			text-shadow: 0 2px 4px rgba(0, 0, 0, 0.8);
		}

		td {
			padding: 9px 14px;
			font-size: 1.12rem;
			border-bottom: 1px solid rgba(255, 255, 255, 0.08);
			color: #ffffff;
			text-shadow: 0 1px 3px rgba(0, 0, 0, 0.8);
		}

		tbody tr:nth-child(even) {
			background: rgba(255, 255, 255, 0.03);
		}

		tbody tr:hover {
			background: rgba(255, 215, 0, 0.1);
		}

		.col-no { width: 5%; text-align: center; white-space: nowrap; }
		.col-tanggal { width: 12%; text-align: center; white-space: nowrap; }
		.col-nama { width: 46%; text-align: left; }
		.col-nominal { width: 21%; text-align: right; white-space: nowrap; }
		.col-ket { width: 16%; text-align: center; }

		.badge-hamba-allah {
			background: linear-gradient(135deg, rgba(0, 230, 118, 0.25), rgba(0, 180, 216, 0.25));
			color: #00e676;
			border: 1px solid rgba(0, 230, 118, 0.5);
			padding: 3px 12px;
			border-radius: 20px;
			font-size: 0.95rem;
			font-weight: 700;
			letter-spacing: 0.5px;
			display: inline-flex;
			align-items: center;
			gap: 6px;
		}

		.nominal-val {
			color: #00e676;
			font-weight: 800;
			font-size: 1.15rem;
			-webkit-text-stroke: 0.35px #ffffff;
			text-shadow:
				-1px -1px 0 #ffffff,
				 1px -1px 0 #ffffff,
				-1px  1px 0 #ffffff,
				 1px  1px 0 #ffffff,
				 0 2px 8px rgba(0, 0, 0, 0.9);
		}

		.empty-notice {
			text-align: center;
			padding: 40px 20px;
			color: rgba(255, 255, 255, 0.7);
			font-size: 1.25rem;
			font-weight: 500;
		}

		.empty-notice i {
			font-size: 2.5rem;
			color: #ffd700;
			margin-bottom: 12px;
			display: block;
		}
	</style>
</head>

<body>
	<div class="display-background"></div>
	<div class="display-overlay"></div>
	@include('partials.medallion-header')

	<div class="container">
		<!-- Master Header Bersama (Pixel-Locked) -->
		<div class="header">
			<h1>{{ $settings['nama_aplikasi'] ?? "MASJID JAMI' AL JIHAD" }}</h1>
			<h3 class="sub-header">SISTEM INFORMASI DIGITAL</h3>
			<div class="datetime" id="datetime"></div>
		</div>

		<!-- Main Panel -->
		<div class="main-content">
			<div class="panel">
				<!-- Judul Program Kegiatan -->
				<div class="program-title-banner">
					<h2>
						<i class="fas fa-hand-holding-heart"></i>
						{{ $program ? strtoupper($program->nama_program) : 'PENGGALANGAN INFAQ & DONASI KHUSUS' }}
					</h2>
					<div class="divider-line"></div>
				</div>

				<!-- 4 Statistik Angka -->
				<div class="summary-grid">
					<!-- Target Dana Infaq -->
					<div class="stat-pill target">
						<div class="icon-box">
							<i class="fas fa-bullseye"></i>
						</div>
						<div class="stat-info">
							<span class="stat-label">Target Dana Infaq</span>
							<span class="stat-value">Rp {{ number_format($targetDana, 0, ',', '.') }}</span>
						</div>
					</div>

					<!-- Dana Terkumpul -->
					<div class="stat-pill terkumpul">
						<div class="icon-box">
							<i class="fas fa-hand-holding-usd"></i>
						</div>
						<div class="stat-info">
							<span class="stat-label">Terkumpul ({{ $persentase }}%)</span>
							<span class="stat-value">Rp {{ number_format($totalTerkumpul, 0, ',', '.') }}</span>
						</div>
					</div>

					<!-- Sisa Kekurangan -->
					<div class="stat-pill kekurangan">
						<div class="icon-box">
							<i class="fas fa-hourglass-half"></i>
						</div>
						<div class="stat-info">
							<span class="stat-label">Sisa Kekurangan</span>
							<span class="stat-value">Rp {{ number_format($sisaDana, 0, ',', '.') }}</span>
						</div>
					</div>

					<!-- Total Donatur -->
					<div class="stat-pill donatur">
						<div class="icon-box">
							<i class="fas fa-users"></i>
						</div>
						<div class="stat-info">
							<span class="stat-label">Jumlah Donatur</span>
							<span class="stat-value">{{ $totalDonatur }} Donatur</span>
						</div>
					</div>
				</div>

				<!-- Animated Glossy Progress Bar -->
				<div class="progress-section">
					<div class="progress-header">
						<span style="color: #ffd700;"><i class="fas fa-chart-line mr-1"></i> Progres Pengumpulan Dana</span>
						<span style="color: #00e676; font-size: 0.95rem;">{{ $persentase }}% Tercapai</span>
					</div>
					<div class="progress-bar-track">
						<div class="progress-bar-fill" style="width: {{ $persentase }}%;"></div>
					</div>
				</div>

				<!-- Tabel Donatur Infinite Scroll-Up -->
				<div class="table-wrapper" id="scrollContainer">
					<table id="donasiTable">
						<thead>
							<tr>
								<th class="col-no">No</th>
								<th class="col-tanggal">Tanggal</th>
								<th class="col-nama">Nama Donatur</th>
								<th class="col-nominal">Nominal Infaq</th>
								<th class="col-ket">Ket</th>
							</tr>
						</thead>
						<tbody id="tableBody">
							@forelse($donasiList as $index => $item)
							<tr>
								<td class="col-no">{{ $index + 1 }}</td>
								<td class="col-tanggal">{{ $item->tanggal ? $item->tanggal->format('d/m/Y') : '-' }}</td>
								<td class="col-nama font-weight-bold">
									@if($item->is_anonim || strtolower(trim($item->nama_donatur)) === 'hamba allah')
										<span class="badge-hamba-allah">
											<i class="fas fa-user-secret"></i> Hamba Allah
										</span>
									@else
										<i class="fas fa-user mr-1 text-warning" style="font-size: 0.9rem;"></i> {{ $item->nama_donatur }}
									@endif
								</td>
								<td class="col-nominal nominal-val">
									Rp {{ number_format($item->nominal, 0, ',', '.') }}
								</td>
								<td class="col-ket" style="font-size: 0.95rem; color: rgba(255,255,255,0.75);">
									{{ $item->keterangan ?? '-' }}
								</td>
							</tr>
							@empty
							<tr>
								<td colspan="5" class="empty-notice">
									<i class="fas fa-heart"></i>
									Mari salurkan infaq dan wakaf terbaik Anda untuk keberkahan program ini.
								</td>
							</tr>
							@endforelse
						</tbody>
					</table>
				</div>
			</div>
		</div>

		<!-- Running Text & Footer -->
		@include('partials.bottom-section')
	</div>

	<!-- Script Jam Digital & Tanggal Hijriah/Masehi -->
	<script>
		function updateDateTime() {
			const now = new Date();
			const el = document.getElementById('datetime');
			if (el) {
				el.innerHTML = typeof getStandardMasjidDateTime === 'function'
					? getStandardMasjidDateTime(now, true)
					: now.toLocaleString('id-ID');
			}
		}
		setInterval(updateDateTime, 1000);
		updateDateTime();
	</script>

	<!-- Script Infinite Seamless Vertical Auto-Scroll (Hanya Berjalan Jika Data Melebihi Layar) -->
	<script>
		document.addEventListener("DOMContentLoaded", function() {
			const container = document.getElementById('scrollContainer');
			const tbody = document.getElementById('tableBody');
			if (!container || !tbody) return;

			function initAutoScroll() {
				// Cek apakah data melebihi tinggi area pandang tabel (overflow)
				// Jika data donatur masih muat (seperti 6 donatur saat ini), JANGAN di-scroll dan JANGAN digandakan!
				const isOverflowing = tbody.scrollHeight > (container.clientHeight + 10);
				if (!isOverflowing) {
					container.scrollTop = 0;
					return;
				}

				// HANYA jika data panjang dan melebihi batas layar, gandakan untuk loop animasi yang mulus
				const originalRows = Array.from(tbody.querySelectorAll('tr'));
				if (originalRows.length <= 1) return;

				originalRows.forEach(row => {
					const clone = row.cloneNode(true);
					tbody.appendChild(clone);
				});

				let scrollSpeed = 0.5; // Kecepatan scroll sangat tenang & mudah dibaca jamaah
				let isPaused = false;

				container.addEventListener('mouseenter', () => isPaused = true);
				container.addEventListener('mouseleave', () => isPaused = false);

				function autoScroll() {
					if (!isPaused) {
						container.scrollTop += scrollSpeed;
						// Bila telah mencapai setengah konten (batas clone)
						if (container.scrollTop >= (tbody.scrollHeight / 2)) {
							container.scrollTop = 0;
						}
					}
					requestAnimationFrame(autoScroll);
				}
				requestAnimationFrame(autoScroll);
			}

			// Beri jeda sejenak agar rendering layout & font selesai sempurna sebelum menghitung tinggi elemen
			setTimeout(initAutoScroll, 350);
		});
	</script>

	<!-- Script Rotasi Background Slideshow (Halus & Elegan) -->
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
				bgElement.style.setProperty('background-image', `url('${backgroundImages[currentBgIndex]}')`, 'important');
			}

			setInterval(changeBackground, 10000);
		}
	</script>
</body>

</html>
