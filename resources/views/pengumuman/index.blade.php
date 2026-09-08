<!-- resources/views/pengumuman/index.blade.php -->
@extends('layouts.admin')

@section('main-content')
<div class="container-fluid">
	<!-- Page Heading -->
	<div class="d-sm-flex align-items-center justify-content-between mb-4">
		<h1 class="h3 mb-0 text-gray-800">
			<i class="fas fa-bullhorn"></i> Kelola Pengumuman
		</h1>
		<div>
			<a href="{{ route('pengumuman.create') }}" class="btn btn-primary btn-sm shadow-sm">
				<i class="fas fa-plus-circle"></i> Tambah Pengumuman
			</a>
			<div class="btn-group ml-2">
				<button type="button" class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown">
					<i class="fas fa-download"></i> Export
				</button>
				<div class="dropdown-menu dropdown-menu-right">
					<a class="dropdown-item" href="{{ route('export.pengumuman', ['type' => 'excel']) }}">
						<i class="fas fa-file-excel text-success"></i> Export ke Excel
					</a>
					<a class="dropdown-item" href="{{ route('export.pengumuman', ['type' => 'pdf']) }}">
						<i class="fas fa-file-pdf text-danger"></i> Export ke PDF
					</a>
				</div>
			</div>
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

	<!-- Stats Cards -->
	<div class="row mb-4">
		<div class="col-md-4">
			<div class="card border-left-primary shadow h-100">
				<div class="card-body">
					<div class="row no-gutters align-items-center">
						<div class="col mr-2">
							<div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
								<i class="fas fa-total"></i> Total Pengumuman
							</div>
							<div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pengumuman->count() }}</div>
						</div>
						<div class="col-auto">
							<i class="fas fa-database fa-2x text-gray-300"></i>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-4">
			<div class="card border-left-success shadow h-100">
				<div class="card-body">
					<div class="row no-gutters align-items-center">
						<div class="col mr-2">
							<div class="text-xs font-weight-bold text-success text-uppercase mb-1">
								<i class="fas fa-calendar-week"></i> Pengumuman Aktif
							</div>
							<div class="h5 mb-0 font-weight-bold text-gray-800">
								{{ $pengumuman->where('tanggal', '>=', now())->count() }}
							</div>
						</div>
						<div class="col-auto">
							<i class="fas fa-check-circle fa-2x text-gray-300"></i>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-4">
			<div class="card border-left-warning shadow h-100">
				<div class="card-body">
					<div class="row no-gutters align-items-center">
						<div class="col mr-2">
							<div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
								<i class="fas fa-hourglass-end"></i> Telah Berlalu
							</div>
							<div class="h5 mb-0 font-weight-bold text-gray-800">
								{{ $pengumuman->where('tanggal', '<', now())->count() }}
								</div>
							</div>
							<div class="col-auto">
								<i class="fas fa-history fa-2x text-gray-300"></i>
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
    <i class="fas fa-list" style="color: #ffd700;"></i> Daftar Pengumuman
				</h6>
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
						<thead>
							<tr>
								<th width="30">No</th>
								<th width="70">Foto</th>
								<th>Kegiatan & Ustadz</th>
								<th>Keterangan / Isi</th>
								<th width="160">Waktu & Tempat</th>
								<th width="90">Status</th>
								<th width="100">Aksi</th>
							</tr>
						</thead>
						<tbody>
							@foreach ($pengumuman as $index => $item)
							@php
							$isActive = \Carbon\Carbon::parse($item->tanggal)->gte(now()->startOfDay());
							$isToday = \Carbon\Carbon::parse($item->tanggal)->isToday();
							@endphp
							<tr>
								<td class="text-center align-middle">{{ $index + 1 }}</td>
								<td class="text-center align-middle">
									@if($item->foto_url)
										<img src="{{ $item->foto_url }}" alt="Foto" class="rounded shadow-sm" style="width: 50px; height: 50px; object-fit: cover; border: 2px solid #ffd700;">
									@else
										<div class="bg-light text-secondary rounded d-flex align-items-center justify-content-center mx-auto" style="width: 50px; height: 50px; border: 1px dashed #ccc;">
											<i class="fas fa-user-tie fa-lg text-gray-400"></i>
										</div>
									@endif
								</td>
								<td class="align-middle">
									<strong class="text-primary font-weight-bold" style="font-size: 1.05rem;">
										{{ $item->judul ?: 'Kegiatan / Pengumuman' }}
									</strong>
									@if($item->pemateri)
									<div class="text-dark small font-weight-bold mt-1">
										<i class="fas fa-user-check text-success mr-1"></i> {{ $item->pemateri }}
									</div>
									@endif
								</td>
								<td class="align-middle">
									<div class="announcement-text">
										{{ \Illuminate\Support\Str::limit($item->isi, 90) }}
									</div>
									@if(strlen($item->isi) > 90)
									<a href="#" class="small text-primary read-more" data-content="{{ $item->isi }}">
										<i class="fas fa-expand-alt"></i> Selengkapnya
									</a>
									@endif
								</td>
								<td class="align-middle">
									<span class="badge {{ $isToday ? 'badge-success' : ($isActive ? 'badge-primary' : 'badge-secondary') }} p-2 d-block mb-1">
										<i class="fas fa-calendar-day"></i> 
										{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d M Y') }}
									</span>
									@if($item->waktu)
									<div class="small text-dark font-weight-bold">
										<i class="fas fa-clock text-warning mr-1"></i> {{ $item->waktu }}
									</div>
									@endif
									@if($item->tempat)
									<div class="small text-muted text-truncate" style="max-width: 150px;">
										<i class="fas fa-map-marker-alt text-danger mr-1"></i> {{ $item->tempat }}
									</div>
									@endif
								</td>
								<td class="text-center align-middle">
									@if($isToday)
									<span class="badge badge-success">
										<i class="fas fa-bell"></i> Hari Ini
									</span>
									@elseif($isActive)
									<span class="badge badge-primary">
										<i class="fas fa-clock"></i> Aktif
									</span>
									@else
									<span class="badge badge-secondary">
										<i class="fas fa-check-double"></i> Berlalu
									</span>
									@endif
								</td>
								<td class="text-center align-middle">
									<a href="{{ route('pengumuman.edit', $item) }}" class="btn btn-sm btn-warning" data-toggle="tooltip" title="Edit">
										<i class="fas fa-edit"></i>
									</a>
									<form action="{{ route('pengumuman.destroy', $item) }}" method="POST" class="d-inline delete-form" id="delete-form-{{ $item->id }}">
										@csrf
										@method('DELETE')
										<button type="button" class="btn btn-sm btn-danger delete-btn" data-id="{{ $item->id }}" data-title="{{ $item->judul ?: \Illuminate\Support\Str::limit($item->isi, 40) }}" data-toggle="tooltip" title="Hapus">
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

<!-- Modal for Read More -->
<div class="modal fade" id="readMoreModal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header bg-primary text-white">
				<h5 class="modal-title">
					<i class="fas fa-bullhorn"></i> Detail Pengumuman
				</h5>
				<button type="button" class="close text-white" data-dismiss="modal">
					<span>&times;</span>
				</button>
			</div>
			<div class="modal-body" id="modalContent">
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
			</div>
		</div>
	</div>
</div>

<!-- Custom CSS -->
<style>
	.announcement-text {
		line-height: 1.5;
		color: #333;
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
	.read-more {
		cursor: pointer;
		text-decoration: none;
		font-size: 12px;
	}
	.read-more:hover {
		text-decoration: underline;
	}
</style>

<!-- Custom Script -->
<script>
	document.addEventListener('DOMContentLoaded', function() {
        // Tooltip initialization
		if (typeof $ !== 'undefined' && $.fn.tooltip) {
			$('[data-toggle="tooltip"]').tooltip();
		}
		
        // Read More functionality
		document.querySelectorAll('.read-more').forEach(link => {
			link.addEventListener('click', function(e) {
				e.preventDefault();
				const content = this.getAttribute('data-content');
				const modalContent = document.getElementById('modalContent');
				modalContent.innerHTML = '<div class="p-3">' + content + '</div>';
				$('#readMoreModal').modal('show');
			});
		});
		
        // Delete confirmation
		const deleteButtons = document.querySelectorAll('.delete-btn');
		
		deleteButtons.forEach(button => {
			button.addEventListener('click', function(e) {
				e.preventDefault();
				e.stopPropagation();
				
				const formId = this.getAttribute('data-id');
				const title = this.getAttribute('data-title') || 'pengumuman ini';
				const form = document.getElementById(`delete-form-${formId}`);
				
				if (typeof Swal !== 'undefined' && Swal.fire) {
					Swal.fire({
						title: 'Apakah Anda yakin?',
						html: `Pengumuman "<strong>${title}</strong>" akan dihapus secara permanen!`,
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
					if (confirm(`Apakah Anda yakin ingin menghapus ${title}?`)) {
						form.submit();
					}
				}
			});
		});
	});
</script>
@endsection