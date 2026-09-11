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
			padding: 10px 25px 85px 25px;
			box-sizing: border-box;
			justify-content: space-between;
		}

		/* Header */
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
			background: rgba(3, 20, 15, 0.65);
			display: inline-block;
			padding: 5px 28px;
			border-radius: 35px;
			margin-top: 4px;
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
			-webkit-text-stroke: 0.35px #ffffff;
			text-shadow:
				-1px -1px 0 #ffffff,
				 1px -1px 0 #ffffff,
				-1px  1px 0 #ffffff,
				 1px  1px 0 #ffffff,
				 0 2px 10px rgba(0, 0, 0, 0.9);
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

		.chart-container h3,
		.transactions-container h3 {
			font-size: 1.1rem;
			font-weight: 700;
			letter-spacing: 1px;
			margin-bottom: 8px;
			color: #ffffff !important;
			text-shadow: 0 2px 6px rgba(0, 0, 0, 0.9);
			flex-shrink: 0;
			display: flex;
			align-items: center;
			gap: 8px;
		}

		.chart-container h3 {
			text-align: left;
			padding-left: 8px;
		}

		.chart-container h3 i,
		.transactions-container h3 i {
			color: #ffd700;
			filter: drop-shadow(0 0 6px rgba(255, 215, 0, 0.6));
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

		/* 3D ISOMETRIC PIE CHART STYLING */
		.chart-3d-wrapper {
			width: 100%;
			height: 100%;
			display: flex;
			align-items: center;
			justify-content: center;
			position: relative;
		}

		.chart-3d-wrapper svg {
			width: 100%;
			height: 100%;
			max-height: 250px;
			overflow: visible;
		}

		.pie-slice-group {
			transition: transform 0.35s cubic-bezier(0.34, 1.56, 0.64, 1), filter 0.3s ease;
			cursor: pointer;
		}

		.pie-slice-group:hover,
		.pie-slice-group.active {
			transform: translateY(-8px);
			filter: brightness(1.15) drop-shadow(0 10px 18px rgba(0, 0, 0, 0.6));
		}

		.pie-top-face {
			stroke: none;
		}

		.pie-wall {
			stroke: none;
		}

		/* LEGEND KEUANGAN 3D (BAWAH GRAFIK) */
		.pie-legend-grid {
			display: grid;
			grid-template-columns: 1fr 1fr;
			gap: 12px;
			margin-top: 10px;
			padding-top: 10px;
			border-top: 1px solid rgba(255, 255, 255, 0.1);
			flex-shrink: 0;
		}

		.pie-legend-card {
			display: flex;
			align-items: center;
			gap: 10px;
			padding: 8px 14px;
			border-radius: 12px;
			background: rgba(255, 255, 255, 0.05);
			border: 1px solid rgba(255, 255, 255, 0.1);
			transition: all 0.25s ease;
			cursor: pointer;
		}

		.pie-legend-card:hover,
		.pie-legend-card.active {
			background: rgba(255, 255, 255, 0.12);
			border-color: rgba(255, 215, 0, 0.5);
			transform: translateY(-2px);
			box-shadow: 0 4px 15px rgba(0, 0, 0, 0.35);
		}

		.legend-color-pill {
			width: 12px;
			height: 36px;
			border-radius: 6px;
			flex-shrink: 0;
		}

		.legend-meta {
			display: flex;
			flex-direction: column;
			min-width: 0;
		}

		.legend-name {
			font-size: 0.78rem;
			font-weight: 600;
			color: #cbd5e1;
			text-transform: uppercase;
			letter-spacing: 0.6px;
		}

		.legend-val-row {
			display: flex;
			align-items: baseline;
			gap: 6px;
			white-space: nowrap;
		}

		.legend-nominal {
			font-size: 1.12rem;
			font-weight: 800;
			font-family: 'Poppins', monospace;
			font-variant-numeric: tabular-nums;
		}

		.legend-percent {
			font-size: 0.82rem;
			font-weight: 700;
			color: #ffd700;
			background: rgba(255, 215, 0, 0.14);
			padding: 1px 6px;
			border-radius: 6px;
			border: 1px solid rgba(255, 215, 0, 0.25);
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
			-webkit-text-stroke: 0.25px #ffffff;
			text-shadow:
				-1px -1px 0 #ffffff,
				 1px -1px 0 #ffffff,
				-1px  1px 0 #ffffff,
				 1px  1px 0 #ffffff,
				 0 2px 6px rgba(0, 0, 0, 0.9);
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
		@include('partials.medallion-header')

		<div class="container">
			<div class="header">
				<h1>{{ $settings['nama_aplikasi'] ?? 'Masjid Al-Jihad' }}</h1>
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
					<h3><i class="fas fa-chart-pie"></i> Grafik Keuangan 3D</h3>
					<div class="chart-wrapper">
						<div id="chart3dContainer" class="chart-3d-wrapper"></div>
					</div>
					<div class="pie-legend-grid" id="pieLegendGrid"></div>
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

			<!-- Memanggil Komponen Running Text dan Footer -->
			@include('partials.bottom-section')

		</div>
	</div>

	<script>
		function updateDateTime() {
			const now = new Date();
			const dt = document.getElementById('datetime');
			if (dt) {
				dt.innerHTML = typeof getStandardMasjidDateTime === 'function'
					? getStandardMasjidDateTime(now, true)
					: now.toLocaleString('id-ID');
			}
		}
		updateDateTime();
		setInterval(updateDateTime, 1000);

		// 3D Isometric Pie Chart Renderer
		function render3DPieChart(containerId, legendId, items) {
			const container = document.getElementById(containerId);
			const legendEl = document.getElementById(legendId);
			if (!container) return;

			const total = items.reduce((sum, item) => sum + item.value, 0);
			if (total === 0) {
				container.innerHTML = '<div style="display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;color:#94a3b8;"><i class="fas fa-chart-pie fa-2x" style="opacity:0.4;margin-bottom:8px;"></i><p>Belum ada data keuangan</p></div>';
				return;
			}

			const width = 500;
			const height = 240;
			const centerX = width / 2;
			const centerY = 100;
			const radiusX = 145;
			const tilt = 0.54; // isometric perspective
			const radiusY = radiusX * tilt; // ~78px
			const depth = 36; // 3D extrusion thickness
			const explodeDist = 16; // exploded slice separation

			// Helper color darken / brighten
			function shade(color, p) {
				let num = parseInt(color.replace("#",""), 16),
					amt = Math.round(2.55 * p),
					R = Math.min(255, Math.max(0, (num >> 16) + amt)),
					G = Math.min(255, Math.max(0, (num >> 8 & 0x00FF) + amt)),
					B = Math.min(255, Math.max(0, (num & 0x0000FF) + amt));
				return "#" + (0x1000000 + R*0x10000 + G*0x100 + B).toString(16).slice(1);
			}

			// Helper to find intervals of [startA, endA] where sin(angle) >= 0 (Front Hemisphere)
			function getFrontIntervals(startA, endA) {
				const kMin = Math.ceil(startA / Math.PI);
				const kMax = Math.floor(endA / Math.PI);
				const points = [startA];
				for (let k = kMin; k <= kMax; k++) {
					const p = k * Math.PI;
					if (p > startA && p < endA) {
						points.push(p);
					}
				}
				points.push(endA);

				const intervals = [];
				for (let i = 0; i < points.length - 1; i++) {
					const a1 = points[i];
					const a2 = points[i + 1];
					const mid = (a1 + a2) / 2;
					if (Math.sin(mid) > 0.0001) {
						intervals.push({ start: a1, end: a2 });
					}
				}
				return intervals;
			}

			// Compute slice angles (starting from -90 deg / 12 o'clock)
			let currentAngle = -Math.PI / 2;
			const slices = items.map((item, idx) => {
				const span = (item.value / total) * 2 * Math.PI;
				const start = currentAngle;
				const end = currentAngle + span;
				const mid = (start + end) / 2;
				currentAngle = end;
				return {
					...item,
					index: idx,
					startAngle: start,
					endAngle: end,
					midAngle: mid,
					span: span,
					percentage: ((item.value / total) * 100).toFixed(1)
				};
			});

			// Sort slices back-to-front by sin(midAngle) so front slices naturally occlude back slices
			const sortedSlices = [...slices].sort((a, b) => Math.sin(a.midAngle) - Math.sin(b.midAngle));

			let svgHtml = `
			<svg viewBox="0 0 ${width} ${height}" preserveAspectRatio="xMidYMid meet">
				<defs>
					<filter id="pieSoftShadowFilter" x="-20%" y="-20%" width="140%" height="140%">
						<feGaussianBlur in="SourceGraphic" stdDeviation="12" />
					</filter>
					${slices.map(s => `
						<!-- Top Face Gradient: Smooth specular illumination, no harsh lines -->
						<linearGradient id="topGrad_${s.index}" x1="0%" y1="0%" x2="100%" y2="100%">
							<stop offset="0%" stop-color="${shade(s.color, 24)}" />
							<stop offset="50%" stop-color="${s.color}" />
							<stop offset="100%" stop-color="${shade(s.color, -10)}" />
						</linearGradient>

						<!-- Front Curved Rim Gradient: Seamless cylindrical 3D lighting without lines -->
						<linearGradient id="rimGrad_${s.index}" x1="0%" y1="0%" x2="100%" y2="0%">
							<stop offset="0%" stop-color="${shade(s.color, -12)}" />
							<stop offset="35%" stop-color="${shade(s.color, +8)}" />
							<stop offset="70%" stop-color="${shade(s.color, -18)}" />
							<stop offset="100%" stop-color="${shade(s.color, -38)}" />
						</linearGradient>

						<!-- Radial Cut Wall Gradients (Start & End Walls) -->
						<linearGradient id="cutGradStart_${s.index}" x1="0%" y1="0%" x2="0%" y2="100%">
							<stop offset="0%" stop-color="${shade(s.color, -15)}" />
							<stop offset="100%" stop-color="${shade(s.color, -35)}" />
						</linearGradient>
						<linearGradient id="cutGradEnd_${s.index}" x1="0%" y1="0%" x2="0%" y2="100%">
							<stop offset="0%" stop-color="${shade(s.color, -18)}" />
							<stop offset="100%" stop-color="${shade(s.color, -38)}" />
						</linearGradient>
					`).join('')}
				</defs>

				<!-- Soft 3D Drop Shadow under entire pie -->
				<ellipse cx="${centerX}" cy="${centerY + depth + 14}" rx="${radiusX * 1.05}" ry="${radiusY * 1.05}"
						 fill="rgba(0,0,0,0.5)" filter="url(#pieSoftShadowFilter)" />
			`;

			sortedSlices.forEach(s => {
				const ox = Math.cos(s.midAngle) * explodeDist;
				const oy = Math.sin(s.midAngle) * explodeDist * tilt;
				const cx = centerX + ox;
				const cy = centerY + oy;

				const x1 = cx + radiusX * Math.cos(s.startAngle);
				const y1 = cy + radiusY * Math.sin(s.startAngle);
				const x2 = cx + radiusX * Math.cos(s.endAngle);
				const y2 = cy + radiusY * Math.sin(s.endAngle);

				const largeArc = s.span > Math.PI ? 1 : 0;

				let sliceSvg = `<g class="pie-slice-group" id="slice_${s.index}" data-index="${s.index}">`;

				// 1. Radial Cut Wall at start angle (visible if cos(startAngle) < 0.05)
				if (Math.cos(s.startAngle) < 0.05) {
					sliceSvg += `
						<path class="pie-wall" d="M ${cx},${cy} L ${x1},${y1} L ${x1},${y1 + depth} L ${cx},${cy + depth} Z"
							  fill="url(#cutGradStart_${s.index})" />
					`;
				}

				// 2. Radial Cut Wall at end angle (visible if cos(endAngle) > -0.05)
				if (Math.cos(s.endAngle) > -0.05) {
					sliceSvg += `
						<path class="pie-wall" d="M ${cx},${cy} L ${x2},${y2} L ${x2},${y2 + depth} L ${cx},${cy + depth} Z"
							  fill="url(#cutGradEnd_${s.index})" />
					`;
				}

				// 3. PURE SMOOTH CURVED OUTER CYLINDER RIM (Single continuous path per front arc - ZERO lines!)
				const frontArcs = getFrontIntervals(s.startAngle, s.endAngle);
				frontArcs.forEach(arc => {
					if (arc.end - arc.start < 0.005) return;
					const ax1 = cx + radiusX * Math.cos(arc.start);
					const ay1 = cy + radiusY * Math.sin(arc.start);
					const ax2 = cx + radiusX * Math.cos(arc.end);
					const ay2 = cy + radiusY * Math.sin(arc.end);

					sliceSvg += `
						<path class="pie-wall"
							  d="M ${ax1},${ay1} 
							     A ${radiusX},${radiusY} 0 0,1 ${ax2},${ay2} 
							     L ${ax2},${ay2 + depth} 
							     A ${radiusX},${radiusY} 0 0,0 ${ax1},${ay1 + depth} 
							     Z"
							  fill="url(#rimGrad_${s.index})" />
					`;
				});

				// 4. Top Elliptical Face (tanpa teks apapun di dalam grafik)
				sliceSvg += `
					<path class="pie-top-face"
						  d="M ${cx},${cy} L ${x1},${y1} A ${radiusX},${radiusY} 0 ${largeArc},1 ${x2},${y2} Z"
						  fill="url(#topGrad_${s.index})" />
				`;

				sliceSvg += `</g>`;
				svgHtml += sliceSvg;
			});

			svgHtml += `</svg>`;
			container.innerHTML = svgHtml;

			// Render Legend di Bawah Grafik
			if (legendEl) {
				legendEl.innerHTML = slices.map(s => `
					<div class="pie-legend-card" id="legendItem_${s.index}" data-index="${s.index}">
						<div class="legend-color-pill" style="background: linear-gradient(180deg, ${s.color} 0%, ${shade(s.color, -32)} 100%); box-shadow: 0 0 10px ${s.color}66;"></div>
						<div class="legend-meta">
							<span class="legend-name">${s.label}</span>
							<div class="legend-val-row">
								<span class="legend-nominal" style="color: ${s.color};">Rp ${s.value.toLocaleString('id-ID')}</span>
								<span class="legend-percent">${s.percentage}%</span>
							</div>
						</div>
					</div>
				`).join('');

				// Interaktivitas hover sinkron antara legend dan 3D slice
				slices.forEach(s => {
					const sliceEl = document.getElementById(`slice_${s.index}`);
					const legEl = document.getElementById(`legendItem_${s.index}`);

					if (sliceEl && legEl) {
						legEl.addEventListener('mouseenter', () => {
							sliceEl.classList.add('active');
							legEl.classList.add('active');
						});
						legEl.addEventListener('mouseleave', () => {
							sliceEl.classList.remove('active');
							legEl.classList.remove('active');
						});
						sliceEl.addEventListener('mouseenter', () => {
							sliceEl.classList.add('active');
							legEl.classList.add('active');
						});
						sliceEl.addEventListener('mouseleave', () => {
							sliceEl.classList.remove('active');
							legEl.classList.remove('active');
						});
					}
				});
			}
		}

		// Inisialisasi Grafik 3D dengan Data Pemasukan & Pengeluaran
		render3DPieChart('chart3dContainer', 'pieLegendGrid', [
			{ label: 'Pemasukan', value: {{ $totalPemasukan ?? 0 }}, color: '#00e676' },
			{ label: 'Pengeluaran', value: {{ $totalPengeluaran ?? 0 }}, color: '#ff4757' }
		]);
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