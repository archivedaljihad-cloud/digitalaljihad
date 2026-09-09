<!-- resources/views/ambulance/laporan.blade.php -->
@extends('layouts.admin')

@section('main-content')
<div class="container-fluid">
	<!-- Page Heading -->
	<div class="d-sm-flex align-items-center justify-content-between mb-4">
		<h1 class="h3 mb-0 text-gray-800">
			<i class="fas fa-ambulance text-danger mr-2"></i> Laporan Kas Ambulance
		</h1>
		<div>
			<a href="{{ route('export.ambulance', ['type' => 'pdf', 'dari' => $dari, 'sampai' => $sampai]) }}" class="btn btn-danger btn-sm shadow-sm">
				<i class="fas fa-file-pdf"></i> Download PDF
			</a>
			<a href="{{ route('export.ambulance', ['type' => 'excel', 'dari' => $dari, 'sampai' => $sampai]) }}" class="btn btn-success btn-sm shadow-sm">
				<i class="fas fa-file-excel"></i> Export Excel
			</a>
			<a href="{{ route('ambulance.index') }}" class="btn btn-secondary btn-sm shadow-sm ml-2">
				<i class="fas fa-arrow-left"></i> Kembali
			</a>
		</div>
	</div>

	<!-- Filter Form -->
	<div class="card shadow mb-4">
		<div class="card-header py-3">
			<h6 class="m-0 font-weight-bold text-primary">
				<i class="fas fa-filter"></i> Filter Periode Laporan
			</h6>
		</div>
		<div class="card-body">
			<form method="GET" action="{{ route('laporan.ambulance') }}" class="form-inline">
				<div class="form-group mb-2 mx-2">
					<label for="dari" class="mx-2">Dari Tanggal:</label>
					<input type="date" name="dari" id="dari" class="form-control" value="{{ $dari }}">
				</div>
				<div class="form-group mb-2 mx-2">
					<label for="sampai" class="mx-2">Sampai Tanggal:</label>
					<input type="date" name="sampai" id="sampai" class="form-control" value="{{ $sampai }}">
				</div>
				<button type="submit" class="btn btn-primary mb-2 mx-2">
					<i class="fas fa-search"></i> Tampilkan
				</button>
				<a href="{{ route('laporan.ambulance') }}" class="btn btn-secondary mb-2 mx-2">
					<i class="fas fa-sync-alt"></i> Reset
				</a>
			</form>
		</div>
	</div>

	<!-- Summary Cards -->
	<div class="row mb-4">
		<div class="col-xl-4 col-md-6 mb-4">
			<div class="card border-left-success shadow h-100">
				<div class="card-body">
					<div class="row no-gutters align-items-center">
						<div class="col mr-2">
							<div class="text-xs font-weight-bold text-success text-uppercase mb-1">
								Total Pemasukan Ambulance
							</div>
							<div class="h5 mb-0 font-weight-bold text-gray-800">
								Rp {{ number_format($totalPemasukan, 0, ',', '.') }}
							</div>
						</div>
						<div class="col-auto">
							<i class="fas fa-arrow-down fa-2x text-gray-300"></i>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-xl-4 col-md-6 mb-4">
			<div class="card border-left-danger shadow h-100">
				<div class="card-body">
					<div class="row no-gutters align-items-center">
						<div class="col mr-2">
							<div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
								Total Pengeluaran Ambulance
							</div>
							<div class="h5 mb-0 font-weight-bold text-gray-800">
								Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}
							</div>
						</div>
						<div class="col-auto">
							<i class="fas fa-arrow-up fa-2x text-gray-300"></i>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-xl-4 col-md-6 mb-4">
			<div class="card border-left-primary shadow h-100">
				<div class="card-body">
					<div class="row no-gutters align-items-center">
						<div class="col mr-2">
							<div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
								Saldo Kas Ambulance
							</div>
							<div class="h5 mb-0 font-weight-bold {{ $saldo >= 0 ? 'text-gray-800' : 'text-danger' }}">
								Rp {{ number_format($saldo, 0, ',', '.') }}
							</div>
						</div>
						<div class="col-auto">
							<i class="fas fa-ambulance fa-2x text-gray-300"></i>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Data Table -->
	<div class="card shadow mb-4">
		<div class="card-header py-3">
			<h6 class="m-0 font-weight-bold text-primary">
				<i class="fas fa-table"></i> Detail Transaksi Kas Ambulance
				<span class="float-right">Periode: {{ \Carbon\Carbon::parse($dari)->translatedFormat('d F Y') }} - {{ \Carbon\Carbon::parse($sampai)->translatedFormat('d F Y') }}</span>
			</h6>
		</div>
		<div class="card-body">
			<div class="table-responsive">
				<table class="table table-bordered table-hover" width="100%" cellspacing="0">
					<thead style="background: linear-gradient(135deg, #1e5a3a 0%, #0a2e1f 100%);">
						<tr>
							<th width="50" class="text-center" style="color: #ffffff !important; font-weight: 700; border: none; font-size: 0.9rem;">No</th>
							<th width="140" style="color: #ffffff !important; font-weight: 700; border: none; font-size: 0.9rem;">Tanggal</th>
							<th style="color: #ffffff !important; font-weight: 700; border: none; font-size: 0.9rem;">Deskripsi</th>
							<th width="140" style="color: #ffffff !important; font-weight: 700; border: none; font-size: 0.9rem;">Kategori</th>
							<th width="160" class="text-right" style="color: #ffffff !important; font-weight: 700; border: none; font-size: 0.9rem;">Pemasukan</th>
							<th width="160" class="text-right" style="color: #ffffff !important; font-weight: 700; border: none; font-size: 0.9rem;">Pengeluaran</th>
							<th width="160" class="text-right" style="color: #ffffff !important; font-weight: 700; border: none; font-size: 0.9rem;">Saldo</th>
						</tr>
					</thead>
					<tbody>
						@forelse($keuangan as $index => $item)
						<tr>
							<td class="text-center font-weight-bold text-dark align-middle" style="font-size: 0.95rem;">{{ $index + 1 }}</td>
							<td class="align-middle text-nowrap">
								<span class="font-weight-bold text-dark" style="color: #0f172a !important; font-size: 0.92rem;">
									<i class="far fa-calendar-alt text-success mr-1"></i>
									{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d F Y') }}
								</span>
							</td>
							<td class="align-middle font-weight-bold text-dark" style="color: #0f172a !important; font-size: 0.95rem;">{{ $item->deskripsi }}</td>
							<td class="align-middle">
								<span class="badge badge-light border border-secondary text-dark font-weight-bold px-2 py-1" style="font-size: 0.85rem; color: #1e293b !important;">
									<i class="fas fa-tag text-primary mr-1"></i> {{ $item->kategori ?? '-' }}
								</span>
							</td>
							<td class="text-right text-success font-weight-bold align-middle" style="font-size: 1rem; color: #047857 !important;">
								{{ $item->pemasukan > 0 ? 'Rp ' . number_format($item->pemasukan, 0, ',', '.') : '-' }}
							</td>
							<td class="text-right text-danger font-weight-bold align-middle" style="font-size: 1rem; color: #b91c1c !important;">
								{{ $item->pengeluaran > 0 ? 'Rp ' . number_format($item->pengeluaran, 0, ',', '.') : '-' }}
							</td>
							<td class="text-right font-weight-bold text-dark align-middle" style="font-size: 1rem; color: #0f172a !important;">Rp {{ number_format($item->saldo, 0, ',', '.') }}</td>
						</tr>
						@empty
						<tr>
							<td colspan="7" class="text-center text-muted py-4">
								<i class="fas fa-ambulance fa-2x mb-2 d-block text-gray-300"></i>
								Tidak ada data transaksi kas ambulance pada periode ini
							</td>
						</tr>
						@endforelse
					</tbody>
					<tfoot style="background-color: #f8fafc; border-top: 2px solid #cbd5e1;">
						<tr>
							<th colspan="4" class="text-right font-weight-bold text-dark" style="font-size: 1rem;">Total:</th>
							<th class="text-right text-success font-weight-bold" style="font-size: 1.05rem; color: #047857 !important;">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</th>
							<th class="text-right text-danger font-weight-bold" style="font-size: 1.05rem; color: #b91c1c !important;">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</th>
							<th class="text-right text-dark font-weight-bold" style="font-size: 1.05rem; color: #0f172a !important;">Rp {{ number_format($saldo, 0, ',', '.') }}</th>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
	</div>
</div>
@endsection
