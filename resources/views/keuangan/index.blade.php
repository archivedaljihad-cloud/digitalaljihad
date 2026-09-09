<!-- resources/views/keuangan/index.blade.php -->
@extends('layouts.admin')

@section('main-content')
<div class="container-fluid">
	<!-- Page Heading -->
	<div class="d-sm-flex align-items-center justify-content-between mb-4">
		<h1 class="h3 mb-0 text-gray-800">
			<i class="fas fa-wallet"></i> Kelola Keuangan
		</h1>
		<div>
			<a href="{{ route('keuangan.create') }}" class="btn btn-primary btn-sm shadow-sm">
				<i class="fas fa-plus-circle"></i> Tambah Transaksi
			</a>
			<div class="btn-group ml-2">
				<button type="button" class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown">
					<i class="fas fa-download"></i> Export
				</button>
				<div class="dropdown-menu dropdown-menu-right">
					<a class="dropdown-item" href="{{ route('export.keuangan', ['type' => 'excel']) }}">
						<i class="fas fa-file-excel text-success"></i> Export ke Excel
					</a>
					<a class="dropdown-item" href="{{ route('export.keuangan', ['type' => 'pdf']) }}">
						<i class="fas fa-file-pdf text-danger"></i> Export ke PDF
					</a>
				</div>
			</div>
			<a href="{{ route('laporan.keuangan') }}" class="btn btn-info btn-sm shadow-sm ml-2">
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
								<i class="fas fa-arrow-down"></i> Total Pemasukan
							</div>
							<div class="h5 mb-0 font-weight-bold text-gray-800">
								Rp {{ number_format($totalPemasukan, 0, ',', '.') }}
							</div>
							<div class="mt-2 small text-muted">
								<i class="fas fa-chart-line"></i> 
								@php
								$lastMonthIncome = \App\Models\Keuangan::whereMonth('tanggal', now()->subMonth())->sum('pemasukan');
								$diffIncome = $totalPemasukan - $lastMonthIncome;
								$percentIncome = $lastMonthIncome > 0 ? ($diffIncome / $lastMonthIncome) * 100 : 0;
								@endphp
								@if($diffIncome > 0)
								<span class="text-success"><i class="fas fa-arrow-up"></i> {{ number_format($percentIncome, 1) }}%</span>
								@elseif($diffIncome < 0)
								<span class="text-danger"><i class="fas fa-arrow-down"></i> {{ number_format(abs($percentIncome), 1) }}%</span>
								@else
								<span class="text-muted">0%</span>
								@endif
								dari bulan lalu
							</div>
						</div>
						<div class="col-auto">
							<i class="fas fa-chart-line fa-2x text-gray-300"></i>
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
								<i class="fas fa-arrow-up"></i> Total Pengeluaran
							</div>
							<div class="h5 mb-0 font-weight-bold text-gray-800">
								Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}
							</div>
							<div class="mt-2 small text-muted">
								<i class="fas fa-chart-line"></i>
								@php
								$lastMonthExpense = \App\Models\Keuangan::whereMonth('tanggal', now()->subMonth())->sum('pengeluaran');
								$diffExpense = $totalPengeluaran - $lastMonthExpense;
								$percentExpense = $lastMonthExpense > 0 ? ($diffExpense / $lastMonthExpense) * 100 : 0;
								@endphp
								@if($diffExpense > 0)
								<span class="text-danger"><i class="fas fa-arrow-up"></i> {{ number_format($percentExpense, 1) }}%</span>
								@elseif($diffExpense < 0)
								<span class="text-success"><i class="fas fa-arrow-down"></i> {{ number_format(abs($percentExpense), 1) }}%</span>
								@else
								<span class="text-muted">0%</span>
								@endif
								dari bulan lalu
							</div>
						</div>
						<div class="col-auto">
							<i class="fas fa-chart-pie fa-2x text-gray-300"></i>
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
								<i class="fas fa-wallet"></i> Saldo
							</div>
							<div class="h5 mb-0 font-weight-bold text-gray-800">
								Rp {{ number_format($saldo, 0, ',', '.') }}
							</div>
							<div class="mt-2 small text-muted">
								<i class="fas fa-calendar-week"></i> Saldo terkini
							</div>
						</div>
						<div class="col-auto">
							<i class="fas fa-coins fa-2x text-gray-300"></i>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Data Table -->
	<div class="card shadow mb-4">
		<div class="card-header py-3">
			<h6 class="m-0 font-weight-bold" style="color: #ffd700;">
    <i class="fas fa-list" style="color: #ffd700;"></i> Daftar Transaksi
			</h6>
		</div>
		<div class="card-body">
			<div class="table-responsive">
				<table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
					<thead style="background: linear-gradient(135deg, #1e5a3a 0%, #0a2e1f 100%);">
						<tr>
							<th width="40" class="text-center" style="color: #ffffff !important; font-weight: 700; vertical-align: middle; border: none; font-size: 0.9rem;">No</th>
							<th width="130" style="color: #ffffff !important; font-weight: 700; vertical-align: middle; border: none; font-size: 0.9rem;">Tanggal</th>
							<th style="color: #ffffff !important; font-weight: 700; vertical-align: middle; border: none; font-size: 0.9rem;">Deskripsi</th>
							<th width="130" style="color: #ffffff !important; font-weight: 700; vertical-align: middle; border: none; font-size: 0.9rem;">Kategori</th>
							<th width="150" class="text-right" style="color: #ffffff !important; font-weight: 700; vertical-align: middle; border: none; font-size: 0.9rem;">Pemasukan</th>
							<th width="150" class="text-right" style="color: #ffffff !important; font-weight: 700; vertical-align: middle; border: none; font-size: 0.9rem;">Pengeluaran</th>
							<th width="150" class="text-right" style="color: #ffffff !important; font-weight: 700; vertical-align: middle; border: none; font-size: 0.9rem;">Saldo</th>
							<th width="100" class="text-center" style="color: #ffffff !important; font-weight: 700; vertical-align: middle; border: none; font-size: 0.9rem;">Aksi</th>
						</tr>
					</thead>
					<tbody>
						@foreach ($keuangan as $index => $item)
						<tr>
							<td class="text-center font-weight-bold text-dark align-middle" style="font-size: 0.95rem;">{{ $index + 1 }}</td>
							<td class="text-nowrap align-middle">
								<span class="font-weight-bold text-dark" style="color: #0f172a !important; font-size: 0.92rem;">
									<i class="far fa-calendar-alt text-success mr-1"></i> 
									{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d M Y') }}
								</span>
							</td>
							<td class="align-middle">
								<div class="d-flex align-items-center">
									<div class="icon-circle bg-{{ $item->pemasukan > 0 ? 'success' : 'danger' }} text-white mr-2" 
										style="width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.75rem;">
										<i class="fas fa-{{ $item->pemasukan > 0 ? 'arrow-down' : 'arrow-up' }}"></i>
									</div>
									<span class="font-weight-bold text-dark" style="color: #0f172a !important; font-size: 0.95rem;">{{ \Illuminate\Support\Str::limit($item->deskripsi, 50) }}</span>
								</div>
							</td>
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
							<td class="text-right text-dark font-weight-bold align-middle" style="font-size: 1rem; color: #0f172a !important;">
								Rp {{ number_format($item->saldo, 0, ',', '.') }}
							</td>
							<td class="text-center">
								<a href="{{ route('keuangan.edit', $item) }}" class="btn btn-sm btn-warning" data-toggle="tooltip" title="Edit">
									<i class="fas fa-edit"></i>
								</a>
								<form action="{{ route('keuangan.destroy', $item) }}" method="POST" class="d-inline delete-form" id="delete-form-{{ $item->id }}">
									@csrf
									@method('DELETE')
									<button type="button" class="btn btn-sm btn-danger delete-btn" data-id="{{ $item->id }}" data-deskripsi="{{ \Illuminate\Support\Str::limit($item->deskripsi, 30) }}" data-toggle="tooltip" title="Hapus">
										<i class="fas fa-trash-alt"></i>
									</button>
								</form>
							</td>
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<!-- Custom CSS -->
<style>
	.icon-circle {
		width: 35px;
		height: 35px;
		border-radius: 50%;
		display: flex;
		align-items: center;
		justify-content: center;
	}
	.table-hover tbody tr:hover {
		background-color: rgba(10, 77, 104, 0.05);
	}
	.badge {
		font-size: 12px;
		font-weight: 500;
		padding: 5px 10px;
	}
	.btn-sm {
		padding: 0.25rem 0.5rem;
		font-size: 0.75rem;
	}
</style>

<!-- Custom Script -->
<script>
	document.addEventListener('DOMContentLoaded', function() {
        // Tooltip initialization
		if (typeof $ !== 'undefined' && $.fn.tooltip) {
			$('[data-toggle="tooltip"]').tooltip();
		}
		
        // Delete confirmation
		const deleteButtons = document.querySelectorAll('.delete-btn');
		
		deleteButtons.forEach(button => {
			button.addEventListener('click', function(e) {
				e.preventDefault();
				e.stopPropagation();
				
				const formId = this.getAttribute('data-id');
				const deskripsi = this.getAttribute('data-deskripsi') || 'transaksi ini';
				const form = document.getElementById(`delete-form-${formId}`);
				
				if (typeof Swal !== 'undefined' && Swal.fire) {
					Swal.fire({
						title: 'Apakah Anda yakin?',
						html: `Transaksi "<strong>${deskripsi}</strong>" akan dihapus secara permanen!`,
						icon: 'warning',
						showCancelButton: true,
						confirmButtonColor: '#d33',
						cancelButtonColor: '#3085d6',
						confirmButtonText: 'Ya, hapus!',
						cancelButtonText: 'Batal'
					}).then((result) => {
						if (result.isConfirmed) {
							form.submit();
						}
					});
				} else {
					if (confirm(`Apakah Anda yakin ingin menghapus ${deskripsi}?`)) {
						form.submit();
					}
				}
			});
		});
	});
</script>
@endsection