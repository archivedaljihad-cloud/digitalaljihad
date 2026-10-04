<!-- resources/views/program_infaq/index.blade.php -->
@extends('layouts.admin')

@section('main-content')
<div class="container-fluid">
	<!-- Page Heading -->
	<div class="d-sm-flex align-items-center justify-content-between mb-4">
		<div>
			<h1 class="h3 mb-0 text-gray-800">
				<i class="fas fa-donate text-warning mr-2"></i> Program Infaq & Donasi Khusus
			</h1>
			<p class="text-muted small mb-0 mt-1">Kelola program penggalangan dana masjid terukur dengan target, pencatatan donatur, dan display TV monitor.</p>
		</div>
		<div class="mt-3 mt-sm-0">
			<a href="{{ route('infaq.embed') }}" target="_blank" class="btn btn-info btn-sm shadow-sm font-weight-bold">
				<i class="fas fa-tv mr-1"></i> Buka Layar TV
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

	@if(session('error'))
	<div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
		<i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
		<button type="button" class="close" data-dismiss="alert" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		</button>
	</div>
	@endif

	<!-- Selector Program yang Sedang Dikelola -->
	@if($programs->count() > 0)
	<div class="card shadow-sm mb-4 border-0">
		<div class="card-body py-3">
			<div class="d-flex flex-column" style="gap: 12px;">
				<!-- Baris 1: Pilih Program -->
				<div class="d-flex align-items-center flex-wrap">
					<span class="font-weight-bold text-gray-700 mr-2 text-nowrap"><i class="fas fa-list-ul mr-1"></i> Pilih Program:</span>
					<form method="GET" action="{{ route('program-infaq.index') }}" class="form-inline">
						<select name="program_id" class="form-control form-control-sm font-weight-bold text-primary" onchange="this.form.submit()" style="max-width: 520px; min-width: 250px;">
							@foreach($programs as $p)
								<option value="{{ $p->id }}" {{ ($selectedProgram && $selectedProgram->id === $p->id) ? 'selected' : '' }}>
									{{ $p->nama_program }} {{ $p->is_active ? '★ (Tampil di TV)' : '' }}
								</option>
							@endforeach
						</select>
					</form>
				</div>
				<!-- Baris 2: Tombol Aksi Program (Kiri: Status TV, Edit, Hapus | Kanan: Buat Program Baru) -->
				<div class="d-flex align-items-center justify-content-between flex-wrap" style="gap: 8px;">
					@if($selectedProgram)
					<div class="d-flex align-items-center flex-wrap">
						@if($selectedProgram->is_active)
							<span class="badge badge-success px-3 py-2 mr-2 mb-1" style="font-size: 0.85rem;">
								<i class="fas fa-broadcast-tower mr-1"></i> Sedang Aktif di TV Monitor
							</span>
						@else
							<form action="{{ route('program-infaq.activate', $selectedProgram->id) }}" method="POST" class="d-inline mr-2 mb-1">
								@csrf
								@method('PUT')
								<button type="submit" class="btn btn-outline-success btn-sm shadow-sm font-weight-bold">
									<i class="fas fa-check-circle mr-1"></i> Aktifkan Tampil di TV
								</button>
							</form>
						@endif

						<a href="{{ route('program-infaq.edit', $selectedProgram->id) }}" class="btn btn-warning btn-sm shadow-sm mr-2 mb-1 font-weight-bold">
							<i class="fas fa-edit mr-1"></i> Edit Target
						</a>

						<form action="{{ route('program-infaq.destroy', $selectedProgram->id) }}" method="POST" class="d-inline mb-1" onsubmit="return confirm('Yakin ingin menghapus program ini beserta seluruh riwayat donasinya?');">
							@csrf
							@method('DELETE')
							<button type="submit" class="btn btn-danger btn-sm shadow-sm font-weight-bold">
								<i class="fas fa-trash mr-1"></i> Hapus
							</button>
						</form>
					</div>
					@else
					<div></div>
					@endif

					<div class="ml-auto my-1">
						<a href="{{ route('program-infaq.create') }}" class="btn btn-sm shadow-sm font-weight-bold btn-nu-emerald text-nowrap">
							<i class="fas fa-plus-circle mr-1"></i> Buat Program Baru
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
	@endif

	@if($selectedProgram)
	<!-- Banner Detail Program & Progress Bar -->
	<div class="card shadow mb-4 border-left-primary">
		<div class="card-body">
			<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">
				<div>
					<h4 class="font-weight-bold text-primary mb-1">{{ $selectedProgram->nama_program }}</h4>
					@if($selectedProgram->keterangan)
						<p class="text-muted mb-0 small">{{ $selectedProgram->keterangan }}</p>
					@endif
				</div>
				<div class="text-md-right mt-2 mt-md-0">
					<span class="badge badge-light border text-dark px-3 py-2">
						<i class="fas fa-calendar-alt text-primary mr-1"></i> 
						Periode: {{ $selectedProgram->tanggal_mulai ? $selectedProgram->tanggal_mulai->format('d/m/Y') : 'Mulai' }} s/d {{ $selectedProgram->tanggal_selesai ? $selectedProgram->tanggal_selesai->format('d/m/Y') : 'Selesai' }}
					</span>
				</div>
			</div>

			<!-- Progress Bar Persentase Terkumpul -->
			<div class="mb-2">
				<div class="d-flex justify-content-between text-xs font-weight-bold text-uppercase mb-1">
					<span class="text-primary font-weight-bold">Progres Pencapaian Dana</span>
					<span class="text-success font-weight-bold" style="font-size: 0.95rem;">{{ $persentase }}% Terkumpul</span>
				</div>
				<div class="progress" style="height: 24px; border-radius: 12px; background-color: #e9ecef; box-shadow: inset 0 1px 3px rgba(0,0,0,0.15);">
					<div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" 
						style="width: {{ $persentase }}%; font-weight: bold; font-size: 0.85rem; border-radius: 12px;" 
						aria-valuenow="{{ $persentase }}" aria-valuemin="0" aria-valuemax="100">
						{{ $persentase }}%
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- 4 Summary Cards -->
	<div class="row mb-4">
		<!-- Target Dana -->
		<div class="col-xl-3 col-md-6 mb-4">
			<div class="card border-left-info shadow h-100">
				<div class="card-body py-3">
					<div class="row no-gutters align-items-center">
						<div class="col mr-2">
							<div class="text-xs font-weight-bold text-info text-uppercase mb-1">
								<i class="fas fa-bullseye mr-1"></i> Target Dana
							</div>
							<div class="h5 mb-0 font-weight-bold text-gray-800">
								Rp {{ number_format($targetDana, 0, ',', '.') }}
							</div>
						</div>
						<div class="col-auto">
							<i class="fas fa-crosshairs fa-2x text-gray-300"></i>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Dana Terkumpul -->
		<div class="col-xl-3 col-md-6 mb-4">
			<div class="card border-left-success shadow h-100">
				<div class="card-body py-3">
					<div class="row no-gutters align-items-center">
						<div class="col mr-2">
							<div class="text-xs font-weight-bold text-success text-uppercase mb-1">
								<i class="fas fa-arrow-down mr-1"></i> Dana Terkumpul
							</div>
							<div class="h5 mb-0 font-weight-bold text-success">
								Rp {{ number_format($totalTerkumpul, 0, ',', '.') }}
							</div>
						</div>
						<div class="col-auto">
							<i class="fas fa-hand-holding-usd fa-2x text-gray-300"></i>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Sisa Kekurangan Dana -->
		<div class="col-xl-3 col-md-6 mb-4">
			<div class="card border-left-danger shadow h-100">
				<div class="card-body py-3">
					<div class="row no-gutters align-items-center">
						<div class="col mr-2">
							<div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
								<i class="fas fa-hourglass-half mr-1"></i> Sisa Kekurangan
							</div>
							<div class="h5 mb-0 font-weight-bold text-danger">
								Rp {{ number_format($sisaDana, 0, ',', '.') }}
							</div>
						</div>
						<div class="col-auto">
							<i class="fas fa-wallet fa-2x text-gray-300"></i>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Total Donatur -->
		<div class="col-xl-3 col-md-6 mb-4">
			<div class="card border-left-warning shadow h-100">
				<div class="card-body py-3">
					<div class="row no-gutters align-items-center">
						<div class="col mr-2">
							<div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
								<i class="fas fa-users mr-1"></i> Jumlah Donatur
							</div>
							<div class="h5 mb-0 font-weight-bold text-gray-800">
								{{ $totalDonatur }} <span class="small font-weight-normal text-muted">Orang/Hamba Allah</span>
							</div>
						</div>
						<div class="col-auto">
							<i class="fas fa-user-friends fa-2x text-gray-300"></i>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Section Catat Donasi (Popdown Menu) & Tabel Donatur Memanjang Penuh -->
	<div class="row">
		<!-- Popdown Menu / Collapsible Card: Form Catat Donasi Masuk -->
		<div class="col-12 mb-3 collapse" id="collapseCatatDonasi">
			<div class="card shadow border-left-success" style="border-radius: 12px; overflow: hidden; border: 1.5px solid rgba(16, 185, 129, 0.35);">
				<div class="card-header py-2.5 px-3 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, var(--islamic-green), var(--islamic-dark)); border-bottom: 2px solid var(--islamic-gold);">
					<h6 class="m-0 font-weight-bold text-white d-flex align-items-center" style="font-size: 0.95rem;">
						<i class="fas fa-hand-holding-heart text-warning mr-2"></i> Formulir Catat Donasi Masuk
					</h6>
					<button type="button" class="btn btn-sm btn-outline-light py-0.5 px-2.5" data-toggle="collapse" data-target="#collapseCatatDonasi" title="Tutup Formulir" style="font-size: 0.8rem; border-radius: 6px;">
						<i class="fas fa-times mr-1"></i> Tutup Formulir
					</button>
				</div>
				<div class="card-body p-3 p-md-4" style="background: #f8fafc;">
					<form action="{{ route('program-infaq.donasi.store', $selectedProgram->id) }}" method="POST">
						@csrf
						<div class="form-row">
							<div class="form-group col-md-3 col-sm-6 mb-3">
								<label class="font-weight-bold text-gray-800 small mb-1">Tanggal Infaq <span class="text-danger">*</span></label>
								<input type="date" name="tanggal" class="form-control form-control-sm font-weight-bold text-gray-800" value="{{ date('Y-m-d') }}" required>
							</div>

							<div class="form-group col-md-5 col-sm-6 mb-3">
								<label class="font-weight-bold text-gray-800 small mb-1">Nama Donatur</label>
								<input type="text" name="nama_donatur" id="namaDonaturInput" class="form-control form-control-sm" placeholder="Contoh: H. Ahmad, Ibu Fatimah">
								<div class="custom-control custom-checkbox mt-1">
									<input type="checkbox" class="custom-control-input" id="isAnonimCheck" name="is_anonim" value="1" onchange="toggleAnonim(this)">
									<label class="custom-control-label font-weight-bold text-success small" for="isAnonimCheck">
										<i class="fas fa-user-secret mr-1"></i> Hamba Allah (Sembunyikan Nama di Layar TV)
									</label>
								</div>
							</div>

							<div class="form-group col-md-4 col-sm-12 mb-3">
								<label class="font-weight-bold text-gray-800 small mb-1">Nominal Infaq (Rp) <span class="text-danger">*</span></label>
								<div class="input-group input-group-sm">
									<div class="input-group-prepend">
										<span class="input-group-text font-weight-bold text-success">Rp</span>
									</div>
									<input type="text" name="nominal" class="form-control form-control-sm font-weight-bold text-success" 
										placeholder="0" onkeyup="formatRupiahInput(this)" autocomplete="off" style="font-size: 1.05rem;" required>
								</div>
								<small class="text-gray-600 font-weight-500 d-block mt-0.5">Titik pemisah ribuan otomatis.</small>
							</div>

							<div class="form-group col-md-9 col-sm-8 mb-2 mb-md-0">
								<label class="font-weight-bold text-gray-800 small mb-1">Keterangan / Doa / Catatan (Opsional)</label>
								<input type="text" name="keterangan" class="form-control form-control-sm text-gray-800" placeholder="Contoh: Wakaf atas nama orang tua, Infaq jariyah hamba Allah">
							</div>

							<div class="form-group col-md-3 col-sm-4 mb-0 d-flex align-items-end">
								<button type="submit" class="btn btn-success btn-block shadow-sm font-weight-bold btn-sm py-2" style="font-size: 0.9rem;">
									<i class="fas fa-save mr-1"></i> Simpan Donasi Infaq
								</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>

		<!-- Kolom Penuh: Daftar Donatur Terkini (Memanjang Penuh dari Kiri ke Kanan) -->
		<div class="col-12 mb-4">
			<div class="card shadow border-0" style="border-radius: 12px; overflow: hidden;">
				<div class="card-header py-2.5 px-3 d-flex flex-wrap align-items-center justify-content-between" style="background: linear-gradient(135deg, var(--islamic-green), var(--islamic-dark)); border-bottom: 2px solid var(--islamic-gold);">
					<div class="d-flex align-items-center mb-1 mb-md-0">
						<h6 class="m-0 font-weight-bold text-white d-flex align-items-center" style="font-size: 1rem;">
							<i class="fas fa-clipboard-list text-warning mr-2"></i> Daftar Penerimaan Infaq Donatur
							<span class="badge badge-warning text-dark font-weight-bold ml-2 px-2 py-0.5" style="font-size: 0.82rem; border-radius: 6px;">
								{{ $donasiList->count() }} Data
							</span>
						</h6>
					</div>
					<div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
						<button type="button" class="btn btn-sm btn-success font-weight-bold text-white shadow-sm py-1.5 px-3" data-toggle="collapse" data-target="#collapseCatatDonasi" aria-expanded="false" aria-controls="collapseCatatDonasi" style="font-size: 0.82rem; border-radius: 6px;" title="Buka/Tutup Menu Catat Donasi Masuk">
							<i class="fas fa-plus-circle mr-1 text-warning"></i> Catat Donasi Masuk <i class="fas fa-chevron-down ml-1"></i>
						</button>
						<span class="small font-weight-bold text-white ml-2 d-none d-md-inline" style="opacity: 0.95; font-size: 0.82rem;">
							<i class="fas fa-tv text-warning mr-1"></i> Ditampilkan otomatis di layar TV
						</span>
					</div>
				</div>
				<div class="card-body">
					<div class="table-responsive">
						<table class="table table-bordered table-hover text-center" id="dataTable" width="100%" cellspacing="0">
							<thead style="background: linear-gradient(135deg, #1e5a3a 0%, #0a2e1f 100%);">
								<tr>
									<th style="width: 5%; color: #ffffff !important; font-weight: 700; vertical-align: middle; border: none; font-size: 0.9rem; letter-spacing: 0.5px;">No</th>
									<th style="width: 14%; color: #ffffff !important; font-weight: 700; vertical-align: middle; border: none; font-size: 0.9rem; letter-spacing: 0.5px;">Tanggal</th>
									<th style="width: 32%; color: #ffffff !important; font-weight: 700; vertical-align: middle; border: none; font-size: 0.9rem; letter-spacing: 0.5px;">Nama Donatur</th>
									<th style="width: 21%; color: #ffffff !important; font-weight: 700; vertical-align: middle; border: none; font-size: 0.9rem; letter-spacing: 0.5px;">Nominal (Rp)</th>
									<th style="width: 18%; color: #ffffff !important; font-weight: 700; vertical-align: middle; border: none; font-size: 0.9rem; letter-spacing: 0.5px;">Keterangan</th>
									<th style="width: 10%; color: #ffffff !important; font-weight: 700; vertical-align: middle; border: none; font-size: 0.9rem; letter-spacing: 0.5px;">Aksi</th>
								</tr>
							</thead>
							<tbody>
								@forelse($donasiList as $index => $donasi)
								<tr>
									<td class="font-weight-bold text-dark align-middle" style="font-size: 0.95rem;">
										{{ $index + 1 }}
									</td>
									<td class="align-middle" style="white-space: nowrap;">
										<span class="font-weight-bold text-dark" style="font-size: 0.95rem; color: #1e293b !important;">
											<i class="far fa-calendar-alt text-success mr-1"></i>
											{{ $donasi->tanggal ? $donasi->tanggal->format('d/m/Y') : '-' }}
										</span>
									</td>
									<td class="text-left align-middle">
										@if($donasi->is_anonim)
											<span class="badge badge-success px-2 py-1 font-weight-bold" style="font-size: 0.85rem;">
												<i class="fas fa-user-secret mr-1"></i> Hamba Allah
											</span>
										@else
											<span class="font-weight-bold text-dark" style="font-size: 0.95rem; color: #0f172a !important;">
												<i class="fas fa-user-circle text-primary mr-1"></i> {{ $donasi->nama_donatur }}
											</span>
										@endif
									</td>
									<td class="text-right align-middle">
										<span class="font-weight-bold text-success" style="font-size: 1rem; color: #047857 !important;">
											Rp {{ number_format($donasi->nominal, 0, ',', '.') }}
										</span>
									</td>
									<td class="text-left align-middle">
										<span class="font-weight-bold text-dark small" style="color: #334155 !important;">
											{{ $donasi->keterangan ?? '-' }}
										</span>
									</td>
									<td class="align-middle">
										<form action="{{ route('program-infaq.donasi.destroy', $donasi->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus catatan donasi ini?');">
											@csrf
											@method('DELETE')
											<button type="submit" class="btn btn-danger btn-circle btn-sm shadow-sm" title="Hapus Donasi">
												<i class="fas fa-trash"></i>
											</button>
										</form>
									</td>
								</tr>
								@empty
								<tr>
									<td colspan="6" class="text-center py-5" style="background-color: #f8fafc;">
										<i class="fas fa-info-circle fa-3x mb-2 text-info d-block"></i>
										<h6 class="font-weight-bold text-gray-800 mb-1" style="font-size: 1.05rem;">
											Belum ada catatan infaq untuk program ini
										</h6>
										<p class="text-gray-600 small mb-0 font-weight-500">
											Klik tombol <span class="badge badge-success px-2 py-1"><i class="fas fa-plus-circle mr-1"></i> Catat Donasi Masuk</span> di atas untuk membuka formulir dan mencatat donasi masuk.
										</p>
									</td>
								</tr>
								@endforelse
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>

	@else
	<!-- Empty State Jika Belum Ada Program -->
	<div class="card shadow py-5 text-center">
		<div class="card-body">
			<i class="fas fa-donate fa-4x text-gray-300 mb-3"></i>
			<h4 class="font-weight-bold text-gray-700">Belum Ada Program Penggalangan Infaq</h4>
			<p class="text-muted">Buat program penggalangan dana infaq pertama untuk masjid Anda, seperti pembelian AC, karpet, atau renovasi.</p>
			<a href="{{ route('program-infaq.create') }}" class="btn btn-primary px-4 shadow-sm">
				<i class="fas fa-plus-circle mr-1"></i> Buat Program Infaq Sekarang
			</a>
		</div>
	</div>
	@endif

</div>

<script>
function toggleAnonim(checkbox) {
	const input = document.getElementById('namaDonaturInput');
	if (checkbox.checked) {
		input.value = 'Hamba Allah';
		input.setAttribute('disabled', 'disabled');
	} else {
		input.value = '';
		input.removeAttribute('disabled');
		input.focus();
	}
}

function formatRupiahInput(el) {
	let value = el.value.replace(/[^0-9]/g, '');
	if (value) {
		el.value = parseInt(value, 10).toLocaleString('id-ID');
	} else {
		el.value = '';
	}
}
</script>
@endsection
