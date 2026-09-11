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
			font-size: 1.85rem;
			font-weight: 700;
			letter-spacing: 1.5px;
			margin: 0 auto 16px auto;
			padding: 7px 32px;
			color: #ffffff !important;
			text-align: center;
			text-shadow: 0 2px 8px rgba(0, 0, 0, 0.95);
			display: table;
			background: linear-gradient(135deg, rgba(2, 25, 17, 0.85) 0%, rgba(5, 42, 28, 0.8) 100%);
			border: 1.5px solid rgba(255, 215, 0, 0.65);
			border-radius: 35px;
			box-shadow: 
				0 8px 25px rgba(0, 0, 0, 0.65),
				0 0 20px rgba(255, 215, 0, 0.25),
				inset 0 1px 1px rgba(255, 255, 255, 0.2);
			backdrop-filter: blur(10px);
			-webkit-backdrop-filter: blur(10px);
		}

		.keuangan h2 i {
			color: #ffd700;
			margin-right: 10px;
		}

		/* 3 KARTU METRIK RINGKASAN KEUANGAN (KPI CARDS) - ANTI TUMPUK */
		.summary-grid {
			display: grid;
			grid-template-columns: repeat(3, 1fr);
			gap: 16px;
			margin-bottom: 16px;
			width: 100%;
		}

		.kpi-card {
			display: flex;
			align-items: center;
			gap: 16px;
			padding: 12px 20px;
			border-radius: 16px;
			backdrop-filter: blur(12px);
			-webkit-backdrop-filter: blur(12px);
			box-shadow: 0 8px 24px rgba(0, 0, 0, 0.45);
			position: relative;
			overflow: hidden;
		}

		.kpi-card::before {
			content: '';
			position: absolute;
			top: 0;
			left: 0;
			right: 0;
			height: 3px;
		}

		.kpi-card.kpi-income {
			background: linear-gradient(135deg, rgba(0, 36, 18, 0.82) 0%, rgba(2, 22, 14, 0.85) 100%);
			border: 1.5px solid rgba(0, 230, 118, 0.45);
		}
		.kpi-card.kpi-income::before {
			background: #00e676;
			box-shadow: 0 0 10px #00e676;
		}

		.kpi-card.kpi-expense {
			background: linear-gradient(135deg, rgba(45, 10, 15, 0.82) 0%, rgba(24, 4, 9, 0.85) 100%);
			border: 1.5px solid rgba(255, 107, 107, 0.45);
		}
		.kpi-card.kpi-expense::before {
			background: #ff6b6b;
			box-shadow: 0 0 10px #ff6b6b;
		}

		.kpi-card.kpi-balance {
			background: linear-gradient(135deg, rgba(42, 32, 4, 0.85) 0%, rgba(22, 16, 2, 0.88) 100%);
			border: 1.5px solid rgba(255, 215, 0, 0.6);
			box-shadow: 0 8px 25px rgba(0, 0, 0, 0.45), 0 0 16px rgba(255, 215, 0, 0.18);
		}
		.kpi-card.kpi-balance::before {
			background: #ffd700;
			box-shadow: 0 0 12px #ffd700;
		}

		.kpi-icon {
			width: 46px;
			height: 46px;
			border-radius: 12px;
			display: flex;
			align-items: center;
			justify-content: center;
			font-size: 1.4rem;
			flex-shrink: 0;
		}

		.kpi-card.kpi-income .kpi-icon {
			background: rgba(0, 230, 118, 0.15);
			color: #00e676;
			border: 1px solid rgba(0, 230, 118, 0.35);
		}

		.kpi-card.kpi-expense .kpi-icon {
			background: rgba(255, 107, 107, 0.15);
			color: #ff6b6b;
			border: 1px solid rgba(255, 107, 107, 0.35);
		}

		.kpi-card.kpi-balance .kpi-icon {
			background: rgba(255, 215, 0, 0.18);
			color: #ffd700;
			border: 1px solid rgba(255, 215, 0, 0.45);
		}

		.kpi-body {
			display: flex;
			flex-direction: column;
			min-width: 0;
		}

		.kpi-title {
			font-size: 0.85rem;
			font-weight: 600;
			text-transform: uppercase;
			letter-spacing: 0.8px;
			opacity: 0.9;
			margin-bottom: 2px;
		}

		.kpi-card.kpi-income .kpi-title { color: #a7f3d0; }
		.kpi-card.kpi-expense .kpi-title { color: #fecaca; }
		.kpi-card.kpi-balance .kpi-title { color: #fef08a; }

		.kpi-amount {
			display: flex;
			align-items: baseline;
			gap: 4px;
			white-space: nowrap !important;
			line-height: 1.1;
		}

		.kpi-rp {
			font-size: 1.05rem;
			font-weight: 600;
			opacity: 0.9;
		}

		.kpi-card.kpi-income .kpi-rp {
			color: #00e676;
			-webkit-text-stroke: 0.25px #ffffff;
			text-shadow:
				-1px -1px 0 #ffffff,
				 1px -1px 0 #ffffff,
				-1px  1px 0 #ffffff,
				 1px  1px 0 #ffffff,
				 0 2px 6px rgba(0, 0, 0, 0.9);
		}
		.kpi-card.kpi-expense .kpi-rp { color: #ff6b6b; }
		.kpi-card.kpi-balance .kpi-rp { color: #ffd700; }

		.kpi-num {
			font-size: 1.6rem;
			font-weight: 800;
			font-family: 'Poppins', sans-serif;
			font-variant-numeric: tabular-nums;
			white-space: nowrap;
			letter-spacing: 0.5px;
		}

		.kpi-card.kpi-income .kpi-num {
			color: #00e676;
			-webkit-text-stroke: 0.35px #ffffff;
			text-shadow:
				-1px -1px 0 #ffffff,
				 1px -1px 0 #ffffff,
				-1px  1px 0 #ffffff,
				 1px  1px 0 #ffffff,
				 0 2px 10px rgba(0, 0, 0, 0.9);
		}

		.kpi-card.kpi-expense .kpi-num {
			color: #ff6b6b;
			text-shadow: 0 0 12px rgba(255, 107, 107, 0.35);
		}

		.kpi-card.kpi-balance .kpi-num {
			color: #ffd700;
			text-shadow: 0 0 15px rgba(255, 215, 0, 0.45);
		}

		.table-wrapper {
			max-height: 420px;
			min-height: 380px;
			overflow: hidden;
			position: relative;
			border-radius: 14px;
			border: 1.5px solid rgba(255, 215, 0, 0.35);
			background: rgba(0, 0, 0, 0.3);
			box-shadow: inset 0 0 25px rgba(0, 0, 0, 0.5);
		}

		.keuangan table {
			width: 100%;
			border-collapse: collapse;
			font-size: 1.12rem;
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
			padding: 11px 14px;
			vertical-align: middle;
		}

		/* Proporsi Lebar Kolom yang Ideal & Seimbang (Bebas Tumpuk) */
		.keuangan th:nth-child(1), .keuangan td:nth-child(1) { width: 13%; text-align: center; white-space: nowrap !important; } /* Tanggal */
		.keuangan th:nth-child(2), .keuangan td:nth-child(2) { width: 29%; text-align: left; } /* Deskripsi */
		.keuangan th:nth-child(3), .keuangan td:nth-child(3) { width: 16%; text-align: right; white-space: nowrap !important; } /* Pemasukan */
		.keuangan th:nth-child(4), .keuangan td:nth-child(4) { width: 16%; text-align: right; white-space: nowrap !important; } /* Pengeluaran */
		.keuangan th:nth-child(5), .keuangan td:nth-child(5) { width: 16%; text-align: right; white-space: nowrap !important; } /* Saldo */
		.keuangan th:nth-child(6), .keuangan td:nth-child(6) { width: 10%; text-align: center; } /* Kategori */

		.keuangan th {
			background: linear-gradient(180deg, #0d3829 0%, #08241a 100%) !important;
			color: #ffd700;
			font-weight: 700;
			font-size: 1.15rem;
			letter-spacing: 0.5px;
			position: sticky;
			top: 0;
			z-index: 5;
			border-bottom: 2px solid rgba(255, 215, 0, 0.7);
			box-shadow: 0 4px 10px rgba(0,0,0,0.4);
		}

		.keuangan th i {
			color: #ffd700;
			margin-right: 6px;
			font-size: 1.05rem;
		}

		.keuangan tbody:hover {
			animation-play-state: paused;
		}

		.keuangan tbody tr {
			border-bottom: 1px solid rgba(255, 255, 255, 0.08);
			transition: background 0.2s ease;
		}

		.keuangan tr.income {
			background: rgba(0, 230, 118, 0.05);
		}

		.keuangan tr.expense {
			background: rgba(255, 107, 107, 0.05);
		}

		.keuangan tr:hover {
			background: rgba(255, 215, 0, 0.18);
		}

		.keuangan td.col-tanggal {
			font-size: 1.05rem;
			font-weight: 500;
			color: #f1f5f9;
			white-space: nowrap !important;
		}

		.keuangan td.col-deskripsi {
			font-size: 1.08rem;
			font-weight: 500;
			color: #ffffff;
			word-wrap: break-word;
		}

		.keuangan td.amount-income,
		.keuangan td.amount-expense,
		.keuangan td.amount-saldo {
			white-space: nowrap !important;
			font-family: 'Poppins', monospace;
			font-variant-numeric: tabular-nums;
			letter-spacing: 0.3px;
		}

		.keuangan td.amount-income .nominal-val {
			color: #00e676;
			font-weight: 700;
			font-size: 1.15rem;
			-webkit-text-stroke: 0.35px #ffffff;
			text-shadow:
				-1px -1px 0 #ffffff,
				 1px -1px 0 #ffffff,
				-1px  1px 0 #ffffff,
				 1px  1px 0 #ffffff,
				 0 2px 8px rgba(0, 0, 0, 0.9);
		}

		.keuangan td.amount-expense .nominal-val {
			color: #ff6b6b;
			font-weight: 700;
			font-size: 1.15rem;
		}

		.keuangan td.amount-saldo .nominal-val {
			color: #ffd700;
			font-weight: 800;
			font-size: 1.20rem;
			text-shadow: 0 0 10px rgba(255, 215, 0, 0.25);
		}

		.rp-prefix {
			font-size: 0.82em;
			font-weight: 600;
			margin-right: 4px;
			opacity: 0.85;
			display: inline-block;
		}

		.dash-empty {
			color: rgba(255, 255, 255, 0.28);
			font-size: 1.25rem;
			font-weight: 700;
			display: inline-block;
		}

		.badge-kategori-pill {
			display: inline-block;
			padding: 3px 12px;
			border-radius: 20px;
			font-size: 0.86rem;
			font-weight: 600;
			background: rgba(255, 255, 255, 0.08);
			color: #f1f5f9;
			border: 1px solid rgba(255, 215, 0, 0.3);
			letter-spacing: 0.3px;
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
			.summary-grid {
				grid-template-columns: 1fr;
				gap: 10px;
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
				<h2><i class="fas fa-file-invoice-dollar"></i> Rincian Keuangan Kas Masjid</h2>
				
				<!-- KPI SUMMARY CARDS: BERSIH, MEWAH & BEBAS TUMPUK -->
				<div class="summary-grid">
					<div class="kpi-card kpi-income">
						<div class="kpi-icon"><i class="fas fa-arrow-circle-down"></i></div>
						<div class="kpi-body">
							<span class="kpi-title">Total Pemasukan</span>
							<div class="kpi-amount">
								<span class="kpi-rp">Rp</span>
								<span class="kpi-num">{{ ($totalPemasukan == round($totalPemasukan)) ? number_format($totalPemasukan, 0, ',', '.') : number_format($totalPemasukan, 2, ',', '.') }}</span>
							</div>
						</div>
					</div>
					<div class="kpi-card kpi-expense">
						<div class="kpi-icon"><i class="fas fa-arrow-circle-up"></i></div>
						<div class="kpi-body">
							<span class="kpi-title">Total Pengeluaran</span>
							<div class="kpi-amount">
								<span class="kpi-rp">Rp</span>
								<span class="kpi-num">{{ ($totalPengeluaran == round($totalPengeluaran)) ? number_format($totalPengeluaran, 0, ',', '.') : number_format($totalPengeluaran, 2, ',', '.') }}</span>
							</div>
						</div>
					</div>
					<div class="kpi-card kpi-balance">
						<div class="kpi-icon"><i class="fas fa-wallet"></i></div>
						<div class="kpi-body">
							<span class="kpi-title">Sisa Saldo Kas</span>
							<div class="kpi-amount">
								<span class="kpi-rp">Rp</span>
								<span class="kpi-num">{{ ($saldo == round($saldo)) ? number_format($saldo, 0, ',', '.') : number_format($saldo, 2, ',', '.') }}</span>
							</div>
						</div>
					</div>
				</div>

				<div class="table-wrapper">
					<table id="keuangan-table">
						<thead>
							<tr>
								<th><i class="fas fa-calendar-alt"></i> Tanggal</th>
								<th><i class="fas fa-file-invoice"></i> Deskripsi Transaksi</th>
								<th><i class="fas fa-arrow-circle-down"></i> Pemasukan</th>
								<th><i class="fas fa-arrow-circle-up"></i> Pengeluaran</th>
								<th><i class="fas fa-wallet"></i> Saldo Kas</th>
								<th class="kategori"><i class="fas fa-tag"></i> Kategori</th>
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
										<td class="col-tanggal">{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat("d M 'y") }}</td>
										<td class="col-deskripsi">
											<i class="fas {{ $item->pemasukan > 0 ? 'fa-arrow-up' : 'fa-arrow-down' }}" style="color: {{ $item->pemasukan > 0 ? '#00e676' : '#ff6b6b' }}; margin-right: 8px;"></i>
											{{ $item->deskripsi }}
										</td>
										<td class="amount-income">
											@if($item->pemasukan > 0)
												<span class="rp-prefix">Rp</span><span class="nominal-val">{{ ($item->pemasukan == round($item->pemasukan)) ? number_format($item->pemasukan, 0, ',', '.') : number_format($item->pemasukan, 2, ',', '.') }}</span>
											@else
												<span class="dash-empty">-</span>
											@endif
										</td>
										<td class="amount-expense">
											@if($item->pengeluaran > 0)
												<span class="rp-prefix">Rp</span><span class="nominal-val">{{ ($item->pengeluaran == round($item->pengeluaran)) ? number_format($item->pengeluaran, 0, ',', '.') : number_format($item->pengeluaran, 2, ',', '.') }}</span>
											@else
												<span class="dash-empty">-</span>
											@endif
										</td>
										<td class="amount-saldo">
											<span class="rp-prefix">Rp</span><span class="nominal-val">{{ ($item->saldo == round($item->saldo)) ? number_format($item->saldo, 0, ',', '.') : number_format($item->saldo, 2, ',', '.') }}</span>
										</td>
										<td class="col-kategori">
											<span class="badge-kategori-pill">{{ $item->kategori ?? '-' }}</span>
										</td>
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