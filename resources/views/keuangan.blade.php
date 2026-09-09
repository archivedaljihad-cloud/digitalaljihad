<!-- resources/views/keuangan.blade.php -->
<!DOCTYPE html>
<html lang="id">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Sistem Informasi Masjid - Keuangan</title>
	<link rel="icon" type="image/png" href="{{ asset('img/favicon.png') }}?v={{ time() }}">
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
		rel="stylesheet">
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
			margin: 5px 0;
		}

		.panel {
			background: rgba(4, 25, 18, 0.72);
			border-radius: 18px;
			padding: 18px 28px;
			backdrop-filter: blur(12px);
			-webkit-backdrop-filter: blur(12px);
			box-shadow: 0 15px 45px rgba(0, 0, 0, 0.45);
			border: 1.5px solid rgba(255, 215, 0, 0.45);
			width: 100%;
			max-width: 1400px;
			display: flex;
			flex-direction: column;
		}

		.keuangan h2 {
			font-size: 2rem;
			margin-bottom: 12px;
			padding-bottom: 6px;
			position: relative;
			color: var(--secondary-color);
			text-align: center;
			text-shadow: 0 2px 5px rgba(0,0,0,0.6);
		}

		.keuangan h2:after {
			content: '';
			position: absolute;
			left: 50%;
			transform: translateX(-50%);
			bottom: 0;
			width: 440px;
			height: 3px;
			background: var(--secondary-color);
			border-radius: 3px;
		}

		.summary {
			display: flex;
			justify-content: space-around;
			align-items: center;
			margin-bottom: 14px;
			padding: 12px 18px;
			background: rgba(0, 0, 0, 0.4);
			border-radius: 12px;
			border: 1px solid rgba(255, 215, 0, 0.3);
		}

		.summary p {
			margin: 0;
			font-size: 1.35rem;
			font-weight: 700;
			letter-spacing: 0.5px;
		}

		.summary p i {
			margin-right: 8px;
			color: var(--secondary-color);
		}

		.summary .income {
			color: #00e676;
		}

		.summary .expense {
			color: #ff6b6b;
		}

		.summary .balance {
			color: #ffd700;
		}

		.table-wrapper {
			max-height: 420px;
			min-height: 380px;
			overflow: hidden;
			position: relative;
			border-radius: 12px;
			border: 1px solid rgba(255, 215, 0, 0.25);
			background: rgba(0, 0, 0, 0.2);
			box-shadow: inset 0 0 20px rgba(0, 0, 0, 0.4);
		}

		.keuangan table {
			width: 100%;
			border-collapse: collapse;
			font-size: 1.18rem;
			table-layout: fixed;
		}

		.keuangan thead {
			display: block;
			width: 100%;
		}

		.keuangan thead tr {
			display: table;
			width: 100%;
			table-layout: fixed;
		}

		.keuangan tbody {
			display: block;
			width: 100%;
		}

		.keuangan tbody.can-scroll {
			will-change: transform;
			/* Kecepatan dibuat sangat smooth & tenang: dihitung otomatis per baris atau fallback 85 detik */
			animation: scrollUp var(--scroll-duration, 85s) linear infinite; 
		}

		.keuangan tbody tr {
			display: table;
			width: 100%;
			table-layout: fixed;
		}

		.keuangan th,
		.keuangan td {
			padding: 10px 14px;
			text-align: left;
			word-wrap: break-word;
			vertical-align: middle;
		}

		/* Proporsi Lebar Kolom yang Ideal & Seimbang */
		.keuangan th:nth-child(1), .keuangan td:nth-child(1) { width: 14%; } /* Tanggal */
		.keuangan th:nth-child(2), .keuangan td:nth-child(2) { width: 32%; } /* Deskripsi */
		.keuangan th:nth-child(3), .keuangan td:nth-child(3) { width: 14%; text-align: right; } /* Pemasukan */
		.keuangan th:nth-child(4), .keuangan td:nth-child(4) { width: 14%; text-align: right; } /* Pengeluaran */
		.keuangan th:nth-child(5), .keuangan td:nth-child(5) { width: 14%; text-align: right; } /* Saldo */
		.keuangan th:nth-child(6), .keuangan td:nth-child(6) { width: 12%; text-align: center; } /* Kategori */

		.keuangan th {
			background: linear-gradient(180deg, #0f3d2e 0%, #09261c 100%) !important;
			color: #ffd700;
			font-weight: 700;
			font-size: 1.22rem;
			letter-spacing: 0.5px;
			position: sticky;
			top: 0;
			z-index: 5;
			border-bottom: 2px solid rgba(255, 215, 0, 0.7);
			box-shadow: 0 4px 10px rgba(0,0,0,0.4);
		}

		.keuangan tbody:hover {
			animation-play-state: paused;
		}

		.keuangan tbody tr {
			border-bottom: 1px solid rgba(255, 255, 255, 0.08);
			transition: background 0.2s ease;
		}

		.keuangan tr.income {
			background: rgba(0, 230, 118, 0.07);
		}

		.keuangan tr.expense {
			background: rgba(255, 107, 107, 0.07);
		}

		.keuangan tr:hover {
			background: rgba(255, 215, 0, 0.22);
		}

		.keuangan td i {
			margin-right: 8px;
			color: var(--secondary-color);
		}

		.keuangan td.amount-income {
			color: #00e676;
			font-weight: 700;
			font-family: 'Poppins', monospace;
			letter-spacing: 0.3px;
		}

		.keuangan td.amount-expense {
			color: #ff6b6b;
			font-weight: 700;
			font-family: 'Poppins', monospace;
			letter-spacing: 0.3px;
		}

		.keuangan td.amount-saldo {
			color: #ffd700;
			font-weight: 700;
			font-family: 'Poppins', monospace;
			letter-spacing: 0.3px;
		}

		.keuangan td.badge-kategori {
			font-size: 0.92rem;
			color: #ffffff;
			opacity: 0.9;
		}

		.no-data {
			text-align: center;
			font-size: 1.1rem;
			padding: 20px;
			color: rgba(255, 255, 255, 0.7);
		}

		@keyframes scrollUp {
			0% { transform: translateY(0); }
			100% { transform: translateY(-50%); }
		}

		@media (max-width: 1024px) {
			.summary {
				flex-direction: column;
				gap: 4px;
				text-align: center;
			}
		}
	</style>
</head>

<body>
	<div class="display-background"></div>
	@include('partials.medallion-header')

	<div class="container">
		<div class="header">
			<h1 id="nama-masjid">{{ $settings['nama_aplikasi'] ?? 'Masjid Al-Jihad' }}</h1>
			<h3 class="sub-header">SISTEM INFORMASI DIGITAL</h3>
			<div class="datetime" id="datetime"></div>
		</div>

		<div class="main-content">
			<div class="panel keuangan">
				<h2>Rincian Keuangan Kas Masjid</h2>
				<div class="summary">
					<p class="income"><i class="fas fa-coins"></i> Total Pemasukan: Rp {{ number_format($totalPemasukan, 2, ',', '.') }}</p>
					<p class="expense"><i class="fas fa-coins"></i> Total Pengeluaran: Rp {{ number_format($totalPengeluaran, 2, ',', '.') }}</p>
					<p class="balance"><i class="fas fa-coins"></i> Saldo: Rp {{ number_format($saldo, 2, ',', '.') }}</p>
				</div>
				<div class="table-wrapper">
					<table id="keuangan-table">
						<thead>
							<tr>
								<th>Tanggal</th>
								<th>Deskripsi</th>
								<th>Pemasukan</th>
								<th>Pengeluaran</th>
								<th>Saldo</th>
								<th class="kategori">Kategori</th>
							</tr>
						</thead>
						@php
							$rowCount = isset($keuangan) ? $keuangan->count() : 0;
							// Tiap baris diberi waktu 5.5 detik agar sangat santai dan mudah dibaca jamaah, minimal 75 detik
							$calcDuration = max(75, $rowCount * 5.5);
						@endphp
						<tbody id="keuangan-tbody" style="--scroll-duration: {{ $calcDuration }}s;">
							@if(isset($keuangan) && $keuangan->isEmpty())
								<tr>
									<td colspan="6" class="no-data">Tidak ada data keuangan tersedia.</td>
								</tr>
							@else
								@foreach ($keuangan as $item)
									<tr class="{{ $item->pemasukan > 0 ? 'income' : 'expense' }}">
										<td>{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d F Y') }}</td>
										<td><i class="fas {{ $item->pemasukan > 0 ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i> {{ $item->deskripsi }}</td>
										<td class="amount-income" style="{{ $item->pemasukan > 0 ? '' : 'opacity: 0.35; font-weight: normal;' }}">
											Rp {{ number_format($item->pemasukan, 2, ',', '.') }}
										</td>
										<td class="amount-expense" style="{{ $item->pengeluaran > 0 ? '' : 'opacity: 0.35; font-weight: normal;' }}">
											Rp {{ number_format($item->pengeluaran, 2, ',', '.') }}
										</td>
										<td class="amount-saldo">Rp {{ number_format($item->saldo, 2, ',', '.') }}</td>
										<td class="badge-kategori">{{ $item->kategori ?? '-' }}</td>
									</tr>
								@endforeach
							@endif
						</tbody>
					</table>
				</div>
			</div>
		</div>

		<!-- Memanggil Komponen Running Text dan Footer -->
		@include('partials.bottom-section')

	</div>

	<script>
		function updateDateTime() {
			const now = new Date();
			const dt = document.getElementById('datetime');
			if(dt) {
				dt.innerHTML = typeof getStandardMasjidDateTime === 'function'
					? getStandardMasjidDateTime(now, true)
					: now.toLocaleString('id-ID');
			}
		}
		updateDateTime();
		setInterval(updateDateTime, 1000);

		document.addEventListener('DOMContentLoaded', function () {
			// Auto-scroll tabel secara cerdas HANYA jika baris data melebihi area pandang layar
			const tableWrapper = document.querySelector('.table-wrapper');
			const tbody = document.getElementById('keuangan-tbody');
			if (tbody && tableWrapper) {
				if (tbody.scrollHeight > tableWrapper.clientHeight + 10) {
					// Duplikasi secara dinamis saat runtime HANYA jika data panjang dan butuh animasi loop
					const originalRows = Array.from(tbody.children);
					originalRows.forEach(row => {
						tbody.appendChild(row.cloneNode(true));
					});
					tbody.classList.add('can-scroll');
				}
			}

			let lastTimestamp = null;
			async function checkForUpdates() {
				try {
					const response = await fetch('{{ route("data.timestamp") }}');
					const data = await response.json();
					const newTimestamp = data.timestamp;
					if (lastTimestamp && newTimestamp && newTimestamp !== lastTimestamp) {
						window.location.reload();
					}
					lastTimestamp = newTimestamp;
				} catch (error) {
					console.error('Error checking for updates:', error);
				}
			}
			checkForUpdates();
			setInterval(checkForUpdates, 30000);
		});
	</script>

	<!-- SCRIPT UNTUK BACKGROUND SLIDESHOW (VERSI SUPER KUAT) -->
	<script>
		const backgroundImages = [
			"{{ asset('image/display/background/BG2.png') }}",
			"{{ asset('image/display/background/BG10.png') }}",
			"{{ asset('image/display/background/BG4.png') }}",
			"{{ asset('image/display/background/BG7.png') }}",
			"{{ asset('image/display/background/BG11.png') }}",
			"{{ asset('image/display/background/BG6.png') }}",
			"{{ asset('image/display/background/BG1.png') }}",
			"{{ asset('image/display/background/BG9.png') }}",
			"{{ asset('image/display/background/BG5.png') }}",
			"{{ asset('image/display/background/BG8.png') }}",
			"{{ asset('image/display/background/BG3.png') }}"
		];

		let currentBgIndex = 0;
		// Mencari div background, jika tidak ditemukan, langsung targetkan seluruh layar (body)
		const bgElement = document.querySelector('.display-background') || document.body;

		if (backgroundImages.length > 0) {
			// Menggunakan setProperty('...', '...', 'important') untuk menjebol CSS bawaan
			bgElement.style.setProperty('transition', 'background-image 1.5s ease-in-out', 'important');
			bgElement.style.setProperty('background-size', 'cover', 'important');
			bgElement.style.setProperty('background-position', 'center', 'important');
			bgElement.style.setProperty('background-repeat', 'no-repeat', 'important');
			
			// Pasang gambar pertama
			bgElement.style.setProperty('background-image', `url('${backgroundImages[0]}')`, 'important');

			// Fungsi putar otomatis
			function changeBackground() {
				currentBgIndex = (currentBgIndex + 1) % backgroundImages.length;
				bgElement.style.setProperty('background-image', `url('${backgroundImages[currentBgIndex]}')`, 'important');
			}

			// Ganti setiap 10 detik
			setInterval(changeBackground, 10000); 
		}
	</script>
</body>

</html>