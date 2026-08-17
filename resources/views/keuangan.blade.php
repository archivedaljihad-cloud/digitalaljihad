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
			padding: 12px 25px;
			box-sizing: border-box;
			position: relative;
			z-index: 5;
		}

		.header {
			text-align: center;
			flex-shrink: 0;
		}

		.header h1 {
			font-family: 'Masking Renta', sans-serif !important;
			font-size: 3.2rem !important;
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
			margin-bottom: -15px !important; 
		}

		.header h3.sub-header {
			font-family: 'Poppins', sans-serif !important;
			font-size: 1.25rem !important;
			font-weight: 500 !important;
			letter-spacing: 4px !important;
			color: #ffffff !important;
			text-transform: uppercase !important;
			opacity: 0.95 !important;
			text-shadow: 0 2px 5px rgba(0, 0, 0, 0.8) !important;
			margin-top: 10px !important;
		}

		.datetime {
			font-size: 1.2rem;
			margin-top: 2px;
			background: rgba(0, 0, 0, 0.4);
			display: inline-block;
			padding: 3px 16px;
			border-radius: 30px;
			font-weight: 500;
			border: 1px solid rgba(255, 215, 0, 0.4);
			box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
			backdrop-filter: blur(5px);
		}

		.main-content {
			display: flex;
			justify-content: center;
			align-items: center;
			flex: 1;
			margin: 5px 0;
		}

		.panel {
			background: rgba(10, 30, 25, 0.45);
			border-radius: 18px;
			padding: 15px 25px;
			backdrop-filter: blur(12px);
			-webkit-backdrop-filter: blur(12px);
			box-shadow: 0 15px 45px rgba(0, 0, 0, 0.4);
			border: 1px solid rgba(255, 215, 0, 0.4);
			width: 100%;
			max-width: 1350px;
			display: flex;
			flex-direction: column;
		}

		.keuangan h2 {
			font-size: 1.8rem;
			margin-bottom: 8px;
			padding-bottom: 4px;
			position: relative;
			color: var(--secondary-color);
			text-align: center;
			text-shadow: 0 2px 4px rgba(0,0,0,0.5);
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
			margin-bottom: 10px;
			padding: 8px 12px;
			background: rgba(0, 0, 0, 0.35);
			border-radius: 10px;
			border: 1px solid rgba(255, 215, 0, 0.25);
		}

		.summary p {
			margin: 0;
			font-size: 1.05rem;
			font-weight: 600;
		}

		.summary p i {
			margin-right: 6px;
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
			max-height: 360px;
			min-height: 360px;
			overflow: hidden;
			position: relative;
			border-radius: 8px;
		}

		.keuangan table {
			width: 100%;
			border-collapse: collapse;
			font-size: 1rem;
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
			will-change: transform;
			/* PENGATURAN KECEPATAN: Ubah angka 15s di bawah ini */
			/* 15s = 15 detik. Perbesar untuk MELAMBATKAN (misal 25s), perkecil untuk MEMPERCEPAT (misal 10s) */
			animation: scrollUp 40s linear infinite; 
		}

		.keuangan tbody tr {
			display: table;
			width: 100%;
			table-layout: fixed;
		}

		.keuangan th,
		.keuangan td {
			padding: 9px 12px;
			text-align: left;
			width: 16.66%;
			word-wrap: break-word;
		}

		.keuangan th {
			background: #0d382b !important;
			color: var(--secondary-color);
			font-weight: 600;
			position: sticky;
			top: 0;
			z-index: 5;
			border-bottom: 2px solid var(--secondary-color);
			box-shadow: 0 4px 6px rgba(0,0,0,0.3);
		}

		.keuangan tbody:hover {
			animation-play-state: paused;
		}

		.keuangan tr.income {
			background: rgba(0, 230, 118, 0.08);
		}

		.keuangan tr.expense {
			background: rgba(255, 107, 107, 0.08);
		}

		.keuangan tr:hover {
			background: rgba(255, 215, 0, 0.2);
		}

		.keuangan td i {
			margin-right: 6px;
			color: var(--secondary-color);
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
	<div class="display-overlay"></div>
	
	<div class="kaligrafi kaligrafi-allah">ﷲ</div>
	<div class="kaligrafi kaligrafi-muhammad">ﷺ</div>

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
						<tbody id="keuangan-tbody">
							@if(isset($keuangan) && $keuangan->isEmpty())
								<tr>
									<td colspan="6" class="no-data">Tidak ada data keuangan tersedia.</td>
								</tr>
							@else
								@foreach ($keuangan as $item)
									<tr class="{{ $item->pemasukan > 0 ? 'income' : 'expense' }}">
										<td>{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d F Y') }}</td>
										<td><i class="fas {{ $item->pemasukan > 0 ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i> {{ $item->deskripsi }}</td>
										<td style="{{ $item->pemasukan > 0 ? 'color: #00e676; font-weight: 600;' : 'opacity: 0.5;' }}">
											Rp {{ number_format($item->pemasukan, 2, ',', '.') }}
										</td>
										<td style="{{ $item->pengeluaran > 0 ? 'color: #ff6b6b; font-weight: 600;' : 'opacity: 0.5;' }}">
											Rp {{ number_format($item->pengeluaran, 2, ',', '.') }}
										</td>
										<td>Rp {{ number_format($item->saldo, 2, ',', '.') }}</td>
										<td class="kategori">{{ $item->kategori ?? '-' }}</td>
									</tr>
								@endforeach
								{{-- Duplikasi untuk animasi loop mulus --}}
								@foreach ($keuangan as $item)
									<tr class="{{ $item->pemasukan > 0 ? 'income' : 'expense' }}">
										<td>{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d F Y') }}</td>
										<td><i class="fas {{ $item->pemasukan > 0 ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i> {{ $item->deskripsi }}</td>
										<td style="{{ $item->pemasukan > 0 ? 'color: #00e676; font-weight: 600;' : 'opacity: 0.5;' }}">
											Rp {{ number_format($item->pemasukan, 2, ',', '.') }}
										</td>
										<td style="{{ $item->pengeluaran > 0 ? 'color: #ff6b6b; font-weight: 600;' : 'opacity: 0.5;' }}">
											Rp {{ number_format($item->pengeluaran, 2, ',', '.') }}
										</td>
										<td>Rp {{ number_format($item->saldo, 2, ',', '.') }}</td>
										<td class="kategori">{{ $item->kategori ?? '-' }}</td>
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
			const dt = document.getElementById('datetime');
			if(dt) dt.textContent = now.toLocaleString('id-ID', options);
		}
		updateDateTime();
		setInterval(updateDateTime, 1000);

		document.addEventListener('DOMContentLoaded', function () {
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