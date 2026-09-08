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
					<thead class="thead-light">
						<tr>
							<th width="50" class="text-center">No</th>
							<th width="130">Tanggal</th>
							<th>Deskripsi</th>
							<th width="140">Kategori</th>
							<th width="150" class="text-right">Pemasukan</th>
							<th width="150" class="text-right">Pengeluaran</th>
							<th width="150" class="text-right">Saldo</th>
						</tr>
					</thead>
					<tbody>
						@forelse($keuangan as $index => $item)
						<tr>
							<td class="text-center">{{ $index + 1 }}</td>
							<td>{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d F Y') }}</td>
							<td>{{ $item->deskripsi }}</td>
							<td>
								<span class="badge badge-secondary px-2 py-1">
									{{ $item->kategori ?? '-' }}
								</span>
							</td>
							<td class="text-right text-success font-weight-bold">
								{{ $item->pemasukan > 0 ? 'Rp ' . number_format($item->pemasukan, 0, ',', '.') : '-' }}
							</td>
							<td class="text-right text-danger font-weight-bold">
								{{ $item->pengeluaran > 0 ? 'Rp ' . number_format($item->pengeluaran, 0, ',', '.') : '-' }}
							</td>
							<td class="text-right font-weight-bold">Rp {{ number_format($item->saldo, 0, ',', '.') }}</td>
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
					<tfoot class="thead-light">
						<tr>
							<th colspan="4" class="text-right font-weight-bold">Total:</th>
							<th class="text-right text-success font-weight-bold">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</th>
							<th class="text-right text-danger font-weight-bold">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</th>
							<th class="text-right text-primary font-weight-bold">Rp {{ number_format($saldo, 0, ',', '.') }}</th>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
	</div>
</div>
@endsection
