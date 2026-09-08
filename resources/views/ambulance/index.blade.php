<!-- resources/views/ambulance/index.blade.php -->
@extends('layouts.admin')

@section('main-content')
<div class="container-fluid">
	<!-- Page Heading -->
	<div class="d-sm-flex align-items-center justify-content-between mb-4">
		<h1 class="h3 mb-0 text-gray-800">
			<i class="fas fa-ambulance text-danger mr-2"></i> Buku Kas Ambulance
		</h1>
		<div>
			<a href="{{ route('ambulance.create') }}" class="btn btn-primary btn-sm shadow-sm">
				<i class="fas fa-plus-circle"></i> Tambah Transaksi
			</a>
			<div class="btn-group ml-2">
				<button type="button" class="btn btn-success btn-sm dropdown-toggle shadow-sm" data-toggle="dropdown">
					<i class="fas fa-download"></i> Export
				</button>
				<div class="dropdown-menu dropdown-menu-right">
					<a class="dropdown-item" href="{{ route('export.ambulance', ['type' => 'excel']) }}">
						<i class="fas fa-file-excel text-success mr-2"></i> Export ke Excel
					</a>
					<a class="dropdown-item" href="{{ route('export.ambulance', ['type' => 'pdf']) }}">
						<i class="fas fa-file-pdf text-danger mr-2"></i> Export ke PDF
					</a>
				</div>
			</div>
			<a href="{{ route('laporan.ambulance') }}" class="btn btn-info btn-sm shadow-sm ml-2">
				<i class="fas fa-chart-line"></i> Laporan
			</a>
		</div>
	</div>

	@if(session('success'))
	<div class="alert alert-success alert-dismissible fade show" role="alert">
		<i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
		<button type="button" class="close" data-dismiss="alert" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		</button>
	</div>
	@endif

	@if(session('error'))
	<div class="alert alert-danger alert-dismissible fade show" role="alert">
		<i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
		<button type="button" class="close" data-dismiss="alert" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		</button>
	</div>
	@endif

	<!-- Summary Cards -->
	<div class="row mb-4">
		<div class="col-xl-4 col-md-6 mb-4">
			<div class="card border-left-success shadow h-100">
				<div class="card-body">
					<div class="row no-gutters align-items-center">
						<div class="col mr-2">
							<div class="text-xs font-weight-bold text-success text-uppercase mb-1">
								<i class="fas fa-arrow-down"></i> Total Pemasukan Kas Ambulance
							</div>
							<div class="h5 mb-0 font-weight-bold text-gray-800">
								Rp {{ number_format($totalPemasukan, 0, ',', '.') }}
							</div>
							<div class="mt-2 small text-muted">
								<i class="fas fa-hand-holding-heart text-success"></i> Infak & sumbangan operasional
							</div>
						</div>
						<div class="col-auto">
							<i class="fas fa-hand-holding-usd fa-2x text-gray-300"></i>
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
								<i class="fas fa-arrow-up"></i> Total Pengeluaran Kas Ambulance
							</div>
							<div class="h5 mb-0 font-weight-bold text-gray-800">
								Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}
							</div>
							<div class="mt-2 small text-muted">
								<i class="fas fa-gas-pump text-danger"></i> BBM, service, perbaikan & driver
							</div>
						</div>
						<div class="col-auto">
							<i class="fas fa-receipt fa-2x text-gray-300"></i>
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
								<i class="fas fa-wallet"></i> Saldo Kas Ambulance
							</div>
							<div class="h5 mb-0 font-weight-bold {{ $saldo >= 0 ? 'text-primary' : 'text-danger' }}">
								Rp {{ number_format($saldo, 0, ',', '.') }}
							</div>
							<div class="mt-2 small text-muted">
								<i class="fas fa-coins text-primary"></i> Saldo terkini kas ambulance
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

	<!-- Data Table Card -->
	<div class="card shadow mb-4">
		<div class="card-header py-3 bg-gradient-success text-white d-flex flex-row align-items-center justify-content-between" style="background: #0b4f26 !important;">
			<h6 class="m-0 font-weight-bold text-white">
				<i class="fas fa-list-ul mr-2" style="color: #ffd700;"></i> Daftar Transaksi Kas Ambulance
			</h6>
			<span class="badge badge-light text-dark font-weight-bold">{{ $keuangan->count() }} Transaksi</span>
		</div>
		<div class="card-body">
			<div class="table-responsive">
				<table class="table table-bordered table-hover align-middle" id="dataTable" width="100%" cellspacing="0">
					<thead class="thead-light">
						<tr>
							<th width="50" class="text-center">No</th>
							<th width="120" class="text-center">Tanggal</th>
							<th>Deskripsi</th>
							<th width="130" class="text-center">Kategori</th>
							<th width="150" class="text-right">Pemasukan</th>
							<th width="150" class="text-right">Pengeluaran</th>
							<th width="150" class="text-right">Saldo</th>
							<th width="110" class="text-center">Aksi</th>
						</tr>
					</thead>
					<tbody>
						@forelse($keuangan as $item)
						<tr>
							<td class="text-center">{{ $loop->iteration }}</td>
							<td class="text-center">
								<span class="badge badge-info px-2 py-1">
									{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d M Y') }}
								</span>
							</td>
							<td>
								<div class="d-flex align-items-center">
									@if($item->pemasukan > 0)
									<span class="badge badge-success mr-2 p-1"><i class="fas fa-arrow-down"></i></span>
									@else
									<span class="badge badge-danger mr-2 p-1"><i class="fas fa-arrow-up"></i></span>
									@endif
									<span>{{ $item->deskripsi }}</span>
								</div>
							</td>
							<td class="text-center">
								<span class="badge badge-secondary px-2 py-1">
									<i class="fas fa-tag mr-1"></i> {{ $item->kategori ?? '-' }}
								</span>
							</td>
							<td class="text-right font-weight-bold text-success">
								{{ $item->pemasukan > 0 ? 'Rp ' . number_format($item->pemasukan, 0, ',', '.') : '-' }}
							</td>
							<td class="text-right font-weight-bold text-danger">
								{{ $item->pengeluaran > 0 ? 'Rp ' . number_format($item->pengeluaran, 0, ',', '.') : '-' }}
							</td>
							<td class="text-right font-weight-bold text-dark">
								Rp {{ number_format($item->saldo, 0, ',', '.') }}
							</td>
							<td class="text-center">
								<a href="{{ route('ambulance.edit', $item->id) }}" class="btn btn-warning btn-sm" title="Edit Transaksi">
									<i class="fas fa-edit"></i>
								</a>
								<form action="{{ route('ambulance.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data kas ambulance ini?')">
									@csrf
									@method('DELETE')
									<button type="submit" class="btn btn-danger btn-sm" title="Hapus Transaksi">
										<i class="fas fa-trash"></i>
									</button>
								</form>
							</td>
						</tr>
						@empty
						<tr>
							<td colspan="8" class="text-center py-4 text-muted">
								<i class="fas fa-ambulance fa-3x mb-3 text-gray-300 d-block"></i>
								Belum ada transaksi kas ambulance yang tercatat.
							</td>
						</tr>
						@endforelse
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
@endsection
