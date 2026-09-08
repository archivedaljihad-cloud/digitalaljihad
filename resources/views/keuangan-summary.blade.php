<!-- resources/views/keuangan-summary.blade.php -->
<!DOCTYPE html>
<html lang="id">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Sistem Informasi Masjid - Ringkasan Keuangan</title>
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap"
		rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&display=swap" rel="stylesheet">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
	<link rel="stylesheet" href="{{ asset('css/display-theme.css') }}">
	@include('partials.display-theme')
	<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
	<style>
		* {
			margin: 0;
			padding: 0;
			box-sizing: border-box;
		}

		body {
			font-family: 'Poppins', sans-serif;
			/* background: linear-gradient(135deg, #0a4d68, #088395); Removed for Nabawi background */
			color: #ffffff;
			height: 100vh;
			width: 100vw;
			overflow: hidden;
			position: relative;
		}

		.kaligrafi {
			position: absolute;
			top: 20px;
			font-family: 'Amiri', serif;
			font-size: 5rem;
			opacity: 0.15;
			z-index: 0;
		}

		.kaligrafi-allah {
			right: 30px;
		}

		.kaligrafi-muhammad {
			left: 30px;
		}

		.container {
			position: relative;
			z-index: 1;
			height: 100vh;
			width: 100%;
			display: flex;
			flex-direction: column;
			padding: 15px 25px;
		}

		/* Header */
		.header {
			text-align: center;
			margin-bottom: 12px;
			flex-shrink: 0;
		}

		.header h1 {
        font-family: 'Masking Renta', sans-serif !important;
        font-size: 3.2rem !important;
        /* Perbesar jarak antar huruf di sini */
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
        
        /* Kurangi jarak bawah h1 */
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
        
        /* Tarik h3 sedikit ke atas mendekati h1 */
        margin-top: 10px !important;
    }

		.datetime {
			font-size: 1.55rem;
			background: rgba(3, 20, 15, 0.65);
			display: inline-block;
			padding: 6px 28px;
			border-radius: 35px;
			margin-top: 6px;
			font-weight: 600;
			letter-spacing: 1px;
			border: 1.5px solid rgba(255, 215, 0, 0.5);
			box-shadow: 0 5px 20px rgba(0, 0, 0, 0.4);
			backdrop-filter: blur(8px);
			-webkit-backdrop-filter: blur(8px);
		}

		/* Stats Cards - 3 kolom dengan warna kontras */
		.stats-grid {
			display: grid;
			grid-template-columns: repeat(3, 1fr);
			gap: 16px;
			margin-bottom: 15px;
			flex-shrink: 0;
		}

		.stat-card {
			background: rgba(4, 25, 18, 0.72);
			backdrop-filter: blur(12px);
			-webkit-backdrop-filter: blur(12px);
			border-radius: 16px;
			padding: 16px 20px;
			text-align: center;
			transition: transform 0.2s ease;
			box-shadow: 0 8px 25px rgba(0, 0, 0, 0.35);
		}

		/* Warna Kontras untuk setiap card */
		.stat-card.income {
			border-left: 5px solid #00e676;
			border-right: 1px solid rgba(0, 230, 118, 0.35);
			box-shadow: 0 0 20px rgba(0, 230, 118, 0.15);
		}

		.stat-card.income i {
			color: #00e676;
			text-shadow: 0 0 8px rgba(0, 230, 118, 0.5);
		}

		.stat-card.income .stat-value {
			color: #00e676;
		}

		.stat-card.expense {
			border-left: 5px solid #ff6b6b;
			border-right: 1px solid rgba(255, 107, 107, 0.35);
			box-shadow: 0 0 20px rgba(255, 107, 107, 0.15);
		}

		.stat-card.expense i {
			color: #ff6b6b;
			text-shadow: 0 0 8px rgba(255, 107, 107, 0.5);
		}

		.stat-card.expense .stat-value {
			color: #ff6b6b;
		}

		.stat-card.balance {
			border-left: 5px solid #ffd700;
			border-right: 1px solid rgba(255, 215, 0, 0.35);
			box-shadow: 0 0 20px rgba(255, 215, 0, 0.15);
			background: linear-gradient(135deg, rgba(4, 25, 18, 0.8), rgba(255, 215, 0, 0.12));
		}

		.stat-card.balance i {
			color: #ffd700;
			text-shadow: 0 0 8px rgba(255, 215, 0, 0.5);
		}

		.stat-card.balance .stat-value {
			color: #ffd700;
		}

		.stat-card i {
			font-size: 2rem;
			margin-bottom: 6px;
		}

		.stat-label {
			font-size: 0.95rem;
			text-transform: uppercase;
			letter-spacing: 1.5px;
			font-weight: 600;
			color: #e0e0e0;
			margin-bottom: 4px;
		}

		.stat-value {
			font-size: 1.8rem;
			font-weight: 700;
			margin-top: 6px;
			letter-spacing: 0.5px;
			text-shadow: 0 2px 5px rgba(0, 0, 0, 0.8);
		}

		/* Chart & Transaction - 2 kolom */
		.dashboard-row {
			display: grid;
			grid-template-columns: 1fr 1fr;
			gap: 15px;
			flex: 1;
			min-height: 0;
		}

		.chart-container {
			background: rgba(0, 0, 0, 0.35);
			backdrop-filter: blur(10px);
			border-radius: 12px;
			padding: 12px;
			border: 1px solid rgba(255, 215, 0, 0.2);
			display: flex;
			flex-direction: column;
		}

		.chart-container h3 {
			text-align: left;
			padding-left: 10px;
			font-size: 0.9rem;
			margin-bottom: 8px;
			color: #ffd700;
			flex-shrink: 0;
		}

		.chart-wrapper {
			position: relative;
			width: 100%;
			flex: 1;
			min-height: 0;
			display: flex;
			justify-content: center;
			align-items: center;
			padding: 5px;
		}

		.chart-wrapper canvas {
			max-width: 100% !important;
			max-height: 100% !important;
		}


		.transactions-container {
			background: rgba(0, 0, 0, 0.35);
			backdrop-filter: blur(10px);
			border-radius: 12px;
			padding: 12px;
			border: 1px solid rgba(255, 215, 0, 0.2);
			display: flex;
			flex-direction: column;
		}

		.transactions-container h3 {
			font-size: 0.9rem;
			margin-bottom: 8px;
			color: #ffd700;
			flex-shrink: 0;
		}

		.transaction-list {
			flex: 1;
			overflow-y: auto;
			min-height: 0;
		}

		.transaction-item {
			display: flex;
			justify-content: space-between;
			align-items: center;
			padding: 8px;
			border-bottom: 1px solid rgba(255, 255, 255, 0.1);
		}

		.transaction-item:last-child {
			border-bottom: none;
		}

		.transaction-info {
			display: flex;
			align-items: center;
			gap: 10px;
		}

		.transaction-icon {
			width: 32px;
			height: 32px;
			border-radius: 50%;
			display: flex;
			align-items: center;
			justify-content: center;
			font-size: 0.9rem;
		}

		.transaction-icon.income {
			background: rgba(0, 230, 118, 0.2);
			color: #00e676;
			box-shadow: 0 0 5px rgba(0, 230, 118, 0.3);
		}

		.transaction-icon.expense {
			background: rgba(255, 107, 107, 0.2);
			color: #ff6b6b;
			box-shadow: 0 0 5px rgba(255, 107, 107, 0.3);
		}

		.transaction-desc {
			font-size: 0.8rem;
			font-weight: 500;
			color: #ffffff;
		}

		.transaction-date {
			font-size: 0.65rem;
			opacity: 0.7;
			color: #c0c0c0;
		}

		.transaction-amount {
			font-size: 0.85rem;
			font-weight: 700;
		}

		.transaction-amount.income {
			color: #00e676;
			text-shadow: 0 0 2px rgba(0, 230, 118, 0.3);
		}

		.transaction-amount.expense {
			color: #ff6b6b;
			text-shadow: 0 0 2px rgba(255, 107, 107, 0.3);
		}

		.footer {
			text-align: center;
			margin-top: 10px;
			padding-top: 6px;
			border-top: 1px solid rgba(255, 255, 255, 0.15);
			font-size: 0.8rem;
			opacity: 0.6;
			flex-shrink: 0;
		}

		/* Scrollbar */
		.transaction-list::-webkit-scrollbar {
			width: 4px;
		}

		.transaction-list::-webkit-scrollbar-track {
			background: rgba(255, 255, 255, 0.1);
			border-radius: 4px;
		}

		.transaction-list::-webkit-scrollbar-thumb {
			background: #ffd700;
			border-radius: 4px;
		}

		/* Responsive */
		@media (max-width: 1024px) {
			.container {
				padding: 12px 20px;
			}

			.stat-value {
				font-size: 1.1rem;
			}
		}
	</style>
</head>

<body>
	<div class="display-background"></div>
	<div class="display-overlay"></div>
	<div class="display-content">
		<div class="kaligrafi kaligrafi-allah">ﷲ</div>
		<div class="kaligrafi kaligrafi-muhammad">ﷺ</div>

		<div class="container">
			<div class="header">
				<h1>{{ $settings['nama_aplikasi'] ?? 'Masjid Al-Ikhlas' }}</h1>
				<h3 class="sub-header">SISTEM INFORMASI DIGITAL</h3>
				<div class="datetime" id="datetime"></div>
			</div>

			<!-- Stats Cards dengan Warna Kontras -->
			<div class="stats-grid">
				<div class="stat-card income">
					<i class="fas fa-arrow-down"></i>
					<div class="stat-label">Total Pemasukan</div>
					<div class="stat-value">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</div>
				</div>
				<div class="stat-card expense">
					<i class="fas fa-arrow-up"></i>
					<div class="stat-label">Total Pengeluaran</div>
					<div class="stat-value">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</div>
				</div>
				<div class="stat-card balance">
					<i class="fas fa-wallet"></i>
					<div class="stat-label">Saldo Akhir</div>
					<div class="stat-value">Rp {{ number_format($saldo, 0, ',', '.') }}</div>
				</div>
			</div>

			<div class="dashboard-row">
				<div class="chart-container">
					<h3><i class="fas fa-chart-pie"></i> Grafik Keuangan</h3>
					<div class="chart-wrapper">
						<canvas id="financeChart"></canvas>
					</div>
				</div>

				<div class="transactions-container">
					<h3><i class="fas fa-history"></i> Transaksi Terbaru</h3>
					<div class="transaction-list">
						@forelse($recentTransactions as $item)
						<div class="transaction-item">
							<div class="transaction-info">
								<div class="transaction-icon {{ $item->pemasukan > 0 ? 'income' : 'expense' }}">
									<i class="fas fa-{{ $item->pemasukan > 0 ? 'arrow-down' : 'arrow-up' }}"></i>
								</div>
								<div>
									<div class="transaction-desc">
										{{ \Illuminate\Support\Str::limit($item->deskripsi, 30) }}
									</div>
									<div class="transaction-date">
										{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d M Y') }}
									</div>
								</div>
							</div>
							<div class="transaction-amount {{ $item->pemasukan > 0 ? 'income' : 'expense' }}">
								{{ $item->pemasukan > 0 ? '+' : '-' }} Rp
								{{ number_format($item->pemasukan > 0 ? $item->pemasukan : $item->pengeluaran, 0, ',', '.') }}
							</div>
						</div>
						@empty
						<div class="text-center" style="padding: 30px;">
							<i class="fas fa-inbox fa-2x" style="opacity: 0.5; color: #ffd700;"></i>
							<p style="margin-top: 10px; color: #ccc;">Belum ada transaksi</p>
						</div>
						@endforelse
					</div>
				</div>
			</div>


		</div>
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
			document.getElementById('datetime').textContent = now.toLocaleString('id-ID', options);
		}
		updateDateTime();
		setInterval(updateDateTime, 1000);

		// Chart dengan warna kontras
		const ctx = document.getElementById('financeChart').getContext('2d');
		new Chart(ctx, {
			type: 'doughnut',
			data: {
				labels: ['Pemasukan', 'Pengeluaran'],
				datasets: [{
					data: [{{ $totalPemasukan }}, {{ $totalPengeluaran }}],
					backgroundColor: ['#00e676', '#ff6b6b'],
					borderColor: '#ffffff',
					borderWidth: 2,
					hoverOffset: 10
				}]
			},
			options: {
				responsive: true,
				maintainAspectRatio: true,
				cutout: '60%',
				layout: {
					padding: {
						top: 5,
						bottom: 10,
						left: 10,
						right: 10
					}
				},
				plugins: {
					legend: {
						position: 'bottom',
						labels: {
							color: '#ffffff',
							font: {size: 11, weight: 'bold'},
							padding: 10
						}
					},
					tooltip: {
						callbacks: {
							label: function (context) {
								let label = context.label || '';
								let value = context.raw || 0;
								let total = context.dataset.data.reduce((a, b) => a + b, 0);
								let percentage = ((value / total) * 100).toFixed(1);
								return `${label}: Rp ${value.toLocaleString('id-ID')} (${percentage}%)`;
							}
						}
					}
				}
			}
		});
	</script>
	<!-- SCRIPT UNTUK BACKGROUND SLIDESHOW (VERSI SUPER KUAT) -->
	<script>
		const backgroundImages = [
			"{{ asset('image/display/background/BG1.png') }}",
			"{{ asset('image/display/background/BG2.png') }}",
			"{{ asset('image/display/background/BG9.png') }}",
			"{{ asset('image/display/background/BG4.png') }}",
			"{{ asset('image/display/background/BG1.png') }}",
			"{{ asset('image/display/background/BG8.png') }}",
			"{{ asset('image/display/background/BG6.png') }}",
			"{{ asset('image/display/background/BG10.png') }}",
			"{{ asset('image/display/background/BG3.png') }}",
			"{{ asset('image/display/background/BG5.png') }}",
			"{{ asset('image/display/background/BG7.png') }}"
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