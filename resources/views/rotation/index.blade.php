{{-- resources/views/rotation/index.blade.php --}}
@extends('layouts.admin')

@section('main-content')
<div class="container-fluid">
	<div class="d-sm-flex align-items-center justify-content-between mb-4">
		<div>
			<h1 class="h3 mb-1 text-gray-800 font-weight-bold">
				<i class="fas fa-exchange-alt text-primary mr-2"></i> Pengaturan Rotasi & Urutan Layar TV
			</h1>
			<p class="text-muted mb-0">Kelola durasi interval, halaman yang aktif, dan urutan putaran tampilan siaran display masjid.</p>
		</div>
		<div class="mt-3 mt-sm-0 d-flex align-items-center">
			@if($isSuperAdmin ?? false)
				<span class="badge badge-warning px-3 py-2 mr-2 font-weight-bold shadow-sm" style="font-size: 0.85rem; background: linear-gradient(135deg, #ffd700, #ffae00); color: #212529;">
					<i class="fas fa-crown mr-1"></i> Mode Super Admin: Atur Urutan Aktif
				</span>
			
			@endif
			<a href="{{ route('rotator') }}" class="btn btn-success shadow-sm" target="_blank">
				<i class="fas fa-tv mr-1"></i> Lihat Layar TV (Rotator)
			</a>
		</div>
	</div>

	@if(session('success'))
	<div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
		<i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
		<button type="button" class="close" data-dismiss="alert" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		</button>
	</div>
	@endif

		<!-- Banner Pintasan Pengaturan Durasi Prayer Mode -->
	<div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #e0f2fe 0%, #f0f9ff 100%); border-left: 5px solid #0284c7 !important; border-radius: 10px;">
		<div class="card-body p-3 d-flex flex-column flex-md-row align-items-md-center justify-content-between">
			<div class="d-flex align-items-center mb-3 mb-md-0">
				<div class="rounded-circle d-flex align-items-center justify-content-center mr-3 shadow-sm" style="width: 44px; height: 44px; background: #0284c7; color: #fff; min-width: 44px;">
					<i class="fas fa-stopwatch fa-lg"></i>
				</div>
				<div>
					<h6 class="font-weight-bold text-dark mb-1">
						Pengaturan Durasi Countdown Sebelum Adzan, Adzan, Iqamah & Sholat
					</h6>
					<p class="text-muted small mb-0">
						Durasi hitung mundur sebelum adzan, lama adzan, iqamah, mode sholat, dan audio tarhim diatur melalui menu <strong>Jadwal Sholat</strong>.
					</p>
				</div>
			</div>
			<a href="{{ route('jadwal_sholat.index') }}#durasi-sholat" class="btn btn-info font-weight-bold text-nowrap shadow-sm ml-md-3">
				<i class="fas fa-clock mr-1"></i> Buka Pengaturan Prayer Mode &rarr;
			</a>
		</div>
	</div>

	<div class="card shadow mb-4 border-0">
		<div class="card-header py-3 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #1e5a3a 0%, #0a2e1f 100%);">
			<h6 class="m-0 font-weight-bold text-white">
				<i class="fas fa-sliders-h text-warning mr-2"></i> Konfigurasi Siaran Layar TV
			</h6>
			<span class="text-white-50 small">Total {{ count($pages ?? []) }} Halaman Tersedia</span>
		</div>
		<div class="card-body">
			<form method="POST" action="{{ route('rotation.update') }}" id="rotationForm">
				@csrf
				@method('PUT')
				
				<div class="row mb-4">
					<div class="col-md-6 mb-3 mb-md-0">
						<div class="p-3 border rounded bg-light h-100 shadow-sm">
							<label class="font-weight-bold d-block text-gray-800">Status Perputaran Layar</label>
							<div class="d-flex align-items-center mt-2">
								<label class="switch mr-3 mb-0">
									<input type="checkbox" name="rotation_enabled" value="1" {{ $setting->rotation_enabled ? 'checked' : '' }}>
									<span class="slider round"></span>
								</label>
								<div>
									<span class="font-weight-bold {{ $setting->rotation_enabled ? 'text-success' : 'text-danger' }}">
										{{ $setting->rotation_enabled ? 'ROTASI AKTIF BERPUTAR' : 'ROTASI DINONAKTIFKAN (DIHENTIKAN)' }}
									</span>
									<small class="d-block text-muted">Bila dimatikan, display TV hanya menampilkan satu halaman default.</small>
								</div>
							</div>
						</div>
					</div>

					<div class="col-md-6">
						<div class="p-3 border rounded bg-light h-100 shadow-sm">
							<label for="rotation_interval" class="font-weight-bold text-gray-800">Durasi Interval Tiap Halaman (Detik)</label>
							<div class="input-group mt-2">
								<input type="number" id="rotation_interval" name="rotation_interval" class="form-control" value="{{ $setting->rotation_interval ?? 10 }}" min="1" max="3600" required>
								<div class="input-group-append">
									<span class="input-group-text font-weight-bold bg-white">Detik / Halaman</span>
								</div>
							</div>
							<small class="form-text text-muted">Contoh: 10 detik atau 15 detik untuk setiap perputaran slide.</small>
						</div>
					</div>
				</div>

				<hr class="my-4">

				<div class="d-flex justify-content-between align-items-center mb-3">
					<div>
						<h5 class="font-weight-bold text-gray-800 mb-1">
							<i class="fas fa-list-ol text-primary mr-1"></i> Susunan Urutan & Halaman TV
						</h5>
						<p class="text-muted small mb-0">
							Centang untuk mengaktifkan. 
							@if($isSuperAdmin ?? false)
								Gunakan tombol <strong>Naik (▲)</strong> dan <strong>Turun (▼)</strong> untuk memindahkan posisi urutan tayang sesuai keinginan Anda.
							@else
								<span class="text-warning font-weight-bold"><i class="fas fa-lock"></i> Posisi urutan saat ini dikunci dan hanya dapat dipindahkan oleh Super Admin.</span>
							@endif
						</p>
					</div>
					<div>
						<button type="button" class="btn btn-sm btn-outline-secondary" id="btnSelectAll">
							<i class="fas fa-check-square mr-1"></i> Centang Semua
						</button>
						<button type="button" class="btn btn-sm btn-outline-secondary" id="btnUnselectAll">
							<i class="fas fa-square mr-1"></i> Hapus Centang
						</button>
					</div>
				</div>

				@if(!($isSuperAdmin ?? false))
				<div class="alert alert-warning py-2 px-3 small border-0 shadow-sm mb-3">
					<i class="fas fa-info-circle mr-1"></i> <strong>Akses Terbatas:</strong> Anda login sebagai Petugas/Operator. Anda dapat memilih halaman mana saja yang aktif, namun susunan urutan halaman dikunci khusus untuk <strong>Super Admin</strong> demi keamanan tata letak siaran masjid.
				</div>
				@endif
				
				<div class="table-responsive shadow-sm rounded">
					<table class="table table-hover table-striped align-middle mb-0" id="rotationTable">
						<thead class="thead-dark">
							<tr>
								<th width="80" class="text-center">Urutan</th>
								<th width="90" class="text-center">Tampilkan</th>
								<th width="260">Nama Halaman</th>
								<th width="200">URL Halaman</th>
								<th>Keterangan</th>
								@if($isSuperAdmin ?? false)
								<th width="140" class="text-center">Atur Urutan</th>
								@endif
							</tr>
						</thead>
						<tbody id="sortablePagesBody">
							@foreach($pages as $index => $page)
							<tr class="page-row" data-url="{{ $page['url'] }}">
								{{-- Hidden input order: ikut berpindah otomatis saat posisi <tr> bergeser di DOM --}}
								<input type="hidden" name="page_order[]" value="{{ $page['url'] }}">

								{{-- Kolom Urutan --}}
								<td class="text-center align-middle">
									<span class="badge badge-pill badge-primary px-3 py-2 row-order-badge font-weight-bold" style="font-size: 0.95rem;">
										#{{ $index + 1 }}
									</span>
								</td>

								{{-- Kolom Checkbox --}}
								<td class="text-center align-middle">
									<div class="custom-control custom-checkbox d-inline-block">
										<input type="checkbox" class="custom-control-input page-checkbox" 
											id="check_{{ md5($page['url']) }}" 
											name="active_pages[]" 
											value="{{ $page['url'] }}" 
											{{ $page['active'] ? 'checked' : '' }}>
										<label class="custom-control-label font-weight-bold" for="check_{{ md5($page['url']) }}"></label>
									</div>
								</td>

								{{-- Kolom Nama --}}
								<td class="align-middle">
									<div class="font-weight-bold text-gray-900 page-name-label">
										{{ $page['name'] }}
									</div>
								</td>

								{{-- Kolom URL --}}
								<td class="align-middle">
									<code class="text-primary font-weight-bold bg-white px-2 py-1 rounded border">{{ $page['url'] }}</code>
								</td>

								{{-- Kolom Keterangan --}}
								<td class="align-middle">
									<small class="text-muted">{{ $page['desc'] }}</small>
								</td>

								{{-- Kolom Tombol Naik/Turun Khusus Super Admin --}}
								@if($isSuperAdmin ?? false)
								<td class="text-center align-middle">
									<div class="btn-group btn-group-sm" role="group">
										<button type="button" class="btn btn-outline-primary btn-move-up" title="Pindahkan Urutan ke Atas">
											<i class="fas fa-arrow-up"></i> Naik
										</button>
										<button type="button" class="btn btn-outline-primary btn-move-down" title="Pindahkan Urutan ke Bawah">
											<i class="fas fa-arrow-down"></i> Turun
										</button>
									</div>
								</td>
								@endif
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
				
				<div class="d-flex justify-content-between align-items-center mt-4">
					<div class="small text-muted">
						<i class="fas fa-lightbulb text-warning mr-1"></i> Perubahan urutan akan langsung diterapkan pada layar TV begitu Anda menekan tombol <strong>Simpan Pengaturan</strong>.
					</div>
					<button type="submit" class="btn btn-primary btn-lg px-4 shadow font-weight-bold">
						<i class="fas fa-save mr-2"></i> Simpan Pengaturan & Urutan
					</button>
				</div>
			</form>
		</div>
	</div>
</div>

<style>
	/* Switch Slider Styling */
	.switch {
		position: relative;
		display: inline-block;
		width: 54px;
		height: 28px;
	}
	.switch input {
		opacity: 0;
		width: 0;
		height: 0;
	}
	.slider {
		position: absolute;
		cursor: pointer;
		top: 0; left: 0; right: 0; bottom: 0;
		background-color: #cbd5e1;
		transition: .3s;
		border-radius: 28px;
	}
	.slider:before {
		position: absolute;
		content: "";
		height: 20px;
		width: 20px;
		left: 4px;
		bottom: 4px;
		background-color: white;
		transition: .3s;
		border-radius: 50%;
		box-shadow: 0 2px 4px rgba(0,0,0,0.2);
	}
	input:checked + .slider {
		background: linear-gradient(135deg, #10b981, #059669);
	}
	input:checked + .slider:before {
		transform: translateX(26px);
	}

	/* Row highlight animation */
	.row-highlight {
		background-color: #fff9db !important;
		transition: background-color 0.8s ease;
	}

	.btn-move-up:hover, .btn-move-down:hover {
		background-color: #1e5a3a;
		color: #ffffff;
		border-color: #1e5a3a;
	}
</style>

@if($isSuperAdmin ?? false)
<script>
document.addEventListener('DOMContentLoaded', function () {
	const tbody = document.getElementById('sortablePagesBody');
	if (!tbody) return;

	function flashRow(row) {
		row.classList.add('row-highlight');
		setTimeout(() => {
			row.classList.remove('row-highlight');
		}, 600);
	}

	function updateRowIndexes() {
		const rows = tbody.querySelectorAll('tr.page-row');
		const total = rows.length;

		rows.forEach((row, idx) => {
			// Update badge nomor urut
			const badge = row.querySelector('.row-order-badge');
			if (badge) {
				badge.textContent = '#' + (idx + 1);
				if (idx === 0) {
					badge.className = 'badge badge-pill badge-warning text-dark px-3 py-2 row-order-badge font-weight-bold';
				} else {
					badge.className = 'badge badge-pill badge-primary px-3 py-2 row-order-badge font-weight-bold';
				}
			}

			// Update status tombol naik / turun
			const btnUp = row.querySelector('.btn-move-up');
			const btnDown = row.querySelector('.btn-move-down');

			if (btnUp) {
				btnUp.disabled = (idx === 0);
				btnUp.style.opacity = (idx === 0) ? '0.4' : '1';
			}
			if (btnDown) {
				btnDown.disabled = (idx === total - 1);
				btnDown.style.opacity = (idx === total - 1) ? '0.4' : '1';
			}
		});
	}

	// Delegasi Event Klik Tombol Naik
	tbody.addEventListener('click', function (e) {
		const btnUp = e.target.closest('.btn-move-up');
		if (btnUp && !btnUp.disabled) {
			const currentRow = btnUp.closest('tr.page-row');
			const prevRow = currentRow.previousElementSibling;
			if (prevRow && prevRow.classList.contains('page-row')) {
				tbody.insertBefore(currentRow, prevRow);
				flashRow(currentRow);
				updateRowIndexes();
			}
			return;
		}

		const btnDown = e.target.closest('.btn-move-down');
		if (btnDown && !btnDown.disabled) {
			const currentRow = btnDown.closest('tr.page-row');
			const nextRow = currentRow.nextElementSibling;
			if (nextRow && nextRow.classList.contains('page-row')) {
				tbody.insertBefore(nextRow, currentRow);
				flashRow(currentRow);
				updateRowIndexes();
			}
			return;
		}
	});

	// Tombol Centang Semua & Hapus Centang
	const btnSelectAll = document.getElementById('btnSelectAll');
	const btnUnselectAll = document.getElementById('btnUnselectAll');

	if (btnSelectAll) {
		btnSelectAll.addEventListener('click', function () {
			tbody.querySelectorAll('.page-checkbox').forEach(cb => cb.checked = true);
		});
	}
	if (btnUnselectAll) {
		btnUnselectAll.addEventListener('click', function () {
			tbody.querySelectorAll('.page-checkbox').forEach(cb => cb.checked = false);
		});
	}

	// Inisialisasi awal nomor urut dan tombol
	updateRowIndexes();
});
</script>
@else
<script>
document.addEventListener('DOMContentLoaded', function () {
	const btnSelectAll = document.getElementById('btnSelectAll');
	const btnUnselectAll = document.getElementById('btnUnselectAll');
	const tbody = document.getElementById('sortablePagesBody');

	if (btnSelectAll && tbody) {
		btnSelectAll.addEventListener('click', function () {
			tbody.querySelectorAll('.page-checkbox').forEach(cb => cb.checked = true);
		});
	}
	if (btnUnselectAll && tbody) {
		btnUnselectAll.addEventListener('click', function () {
			tbody.querySelectorAll('.page-checkbox').forEach(cb => cb.checked = false);
		});
	}
});
</script>
@endif
@endsection