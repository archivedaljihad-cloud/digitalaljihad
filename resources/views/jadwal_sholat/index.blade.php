<!-- resources/views/jadwal_sholat/index.blade.php -->
@extends('layouts.admin')

@section('main-content')
	<div class="container-fluid">
		<!-- Page Heading -->
		<div class="d-sm-flex align-items-center justify-content-between mb-4">
			<h1 class="h3 mb-0 text-gray-800">
				<i class="fas fa-mosque"></i> Kelola Jadwal Sholat
			</h1>
			<div>
				<a href="{{ route('jadwal_sholat.create') }}" class="btn btn-primary btn-sm shadow-sm">
					<i class="fas fa-plus-circle"></i> Tambah Jadwal
				</a>
				<div class="btn-group ml-2">
					<button type="button" class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown">
						<i class="fas fa-download"></i> Export
					</button>
					<div class="dropdown-menu dropdown-menu-right">
						<a class="dropdown-item" href="{{ route('export.jadwal-sholat', ['type' => 'excel']) }}">
							<i class="fas fa-file-excel text-success"></i> Export ke Excel
						</a>
						<a class="dropdown-item" href="{{ route('export.jadwal-sholat', ['type' => 'pdf']) }}">
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

		<!-- Cards untuk menampilkan waktu sholat saat ini -->
		<div class="row mb-4">
			<div class="col-xl-2 col-md-4 col-sm-6 mb-3">
				<div class="card border-left-primary shadow h-100 py-2">
					<div class="card-body">
						<div class="row no-gutters align-items-center">
							<div class="col mr-2">
								<div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Subuh</div>
								<div class="h5 mb-0 font-weight-bold text-gray-800">
									@php
										$subuh = $jadwal->where('nama_sholat', 'Subuh')->first();
									@endphp
									{{ $subuh ? \Carbon\Carbon::parse($subuh->waktu)->format('H:i') : '--:--' }}
								</div>
							</div>
							<div class="col-auto">
								<i class="fas fa-clock fa-2x text-gray-300"></i>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-xl-2 col-md-4 col-sm-6 mb-3">
				<div class="card border-left-success shadow h-100 py-2">
					<div class="card-body">
						<div class="row no-gutters align-items-center">
							<div class="col mr-2">
								<div class="text-xs font-weight-bold text-success text-uppercase mb-1">Dzuhur</div>
								<div class="h5 mb-0 font-weight-bold text-gray-800">
									@php
										$dzuhur = $jadwal->where('nama_sholat', 'Dzuhur')->first();
									@endphp
									{{ $dzuhur ? \Carbon\Carbon::parse($dzuhur->waktu)->format('H:i') : '--:--' }}
								</div>
							</div>
							<div class="col-auto">
								<i class="fas fa-clock fa-2x text-gray-300"></i>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-xl-2 col-md-4 col-sm-6 mb-3">
				<div class="card border-left-info shadow h-100 py-2">
					<div class="card-body">
						<div class="row no-gutters align-items-center">
							<div class="col mr-2">
								<div class="text-xs font-weight-bold text-info text-uppercase mb-1">Ashar</div>
								<div class="h5 mb-0 font-weight-bold text-gray-800">
									@php
										$ashar = $jadwal->where('nama_sholat', 'Ashar')->first();
									@endphp
									{{ $ashar ? \Carbon\Carbon::parse($ashar->waktu)->format('H:i') : '--:--' }}
								</div>
							</div>
							<div class="col-auto">
								<i class="fas fa-clock fa-2x text-gray-300"></i>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-xl-2 col-md-4 col-sm-6 mb-3">
				<div class="card border-left-warning shadow h-100 py-2">
					<div class="card-body">
						<div class="row no-gutters align-items-center">
							<div class="col mr-2">
								<div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Maghrib</div>
								<div class="h5 mb-0 font-weight-bold text-gray-800">
									@php
										$maghrib = $jadwal->where('nama_sholat', 'Maghrib')->first();
									@endphp
									{{ $maghrib ? \Carbon\Carbon::parse($maghrib->waktu)->format('H:i') : '--:--' }}
								</div>
							</div>
							<div class="col-auto">
								<i class="fas fa-clock fa-2x text-gray-300"></i>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-xl-2 col-md-4 col-sm-6 mb-3">
				<div class="card border-left-danger shadow h-100 py-2">
					<div class="card-body">
						<div class="row no-gutters align-items-center">
							<div class="col mr-2">
								<div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Isya</div>
								<div class="h5 mb-0 font-weight-bold text-gray-800">
									@php
										$isya = $jadwal->where('nama_sholat', 'Isya')->first();
									@endphp
									{{ $isya ? \Carbon\Carbon::parse($isya->waktu)->format('H:i') : '--:--' }}
								</div>
							</div>
							<div class="col-auto">
								<i class="fas fa-clock fa-2x text-gray-300"></i>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-xl-2 col-md-4 col-sm-6 mb-3">
				<div class="card border-left-secondary shadow h-100 py-2">
					<div class="card-body">
						<div class="row no-gutters align-items-center">
							<div class="col mr-2">
								<div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Total Jadwal</div>
								<div class="h5 mb-0 font-weight-bold text-gray-800">{{ $jadwal->count() }}</div>
							</div>
							<div class="col-auto">
								<i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Pengaturan Waktu Sistem & Durasi Prayer Mode -->
		<div class="card shadow mb-4 border-left-success" id="durasi-sholat">
			<div class="card-header py-3 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center" style="background: linear-gradient(135deg, #0b4629 0%, #13653b 100%);">
				<h6 class="m-0 font-weight-bold" style="color: #ffd700 !important;">
					<i class="fas fa-stopwatch mr-2"></i> Pengaturan Durasi Sholat, Adzan, Iqamah & Waktu Sistem
				</h6>
				<span class="badge badge-warning text-dark font-weight-bold px-3 py-1 mt-2 mt-sm-0 shadow-sm">
					<i class="fas fa-tv mr-1"></i> Terhubung Langsung ke Display TV
				</span>
			</div>

			<div class="card-body">
				<form action="{{ route('settings.prayer.update') }}" method="POST">
					@csrf
					@method('PUT')

					<!-- Status Aktivasi & Interval Rotasi TV -->
					<div class="row mb-3">
						<div class="col-md-6 mb-3">
							<div class="card bg-light border-left-success h-100 p-3">
								<div class="custom-control custom-switch">
									<input type="hidden" name="prayer_mode_enabled" value="0">
									<input type="checkbox" class="custom-control-input" id="prayer_mode_enabled"
										name="prayer_mode_enabled" value="1" {{ old('prayer_mode_enabled', $setting->prayer_mode_enabled ?? 1) ? 'checked' : '' }}>
									<label class="custom-control-label font-weight-bold text-dark" for="prayer_mode_enabled">
										Aktifkan Prayer Mode Otomatis di Layar TV
									</label>
								</div>
								<small class="text-muted mt-2 d-block">
									Jika aktif, layar TV otomatis masuk ke mode hitung mundur, kumandang adzan, iqamah, dan mode sholat hening saat waktu sholat tiba.
								</small>
							</div>
						</div>

						<div class="col-md-6 mb-3">
							<div class="card bg-light border-left-primary h-100 p-3">
								<label class="font-weight-bold text-dark mb-1">
									<i class="fas fa-sync-alt mr-1 text-primary"></i> Interval Rotasi Halaman TV
								</label>
								<div class="input-group">
									<input type="number" name="rotation_interval" class="form-control"
										value="{{ old('rotation_interval', $setting->rotation_interval ?? 20) }}" min="1" max="3600" required>
									<div class="input-group-append">
										<span class="input-group-text font-weight-bold">detik</span>
									</div>
								</div>
								<small class="text-muted mt-1 d-block">Lama waktu tiap halaman ditampilkan di TV sebelum beralih ke slide berikutnya.</small>
							</div>
						</div>
					</div>

					<div class="card p-3 mb-4 border-0" style="background: rgba(13, 110, 110, 0.05); border-radius: 10px;">
						<h6 class="font-weight-bold text-success mb-3">
							<i class="fas fa-hourglass-half mr-2"></i> Durasi Fase Sholat Reguler (5 Waktu)
						</h6>

						<div class="row">
							<!-- Durasi Sebelum Adzan -->
							<div class="col-md-4 mb-3">
								<div class="form-group mb-0">
									<label class="font-weight-bold text-dark">
										<i class="fas fa-bell text-warning mr-1"></i> Countdown Sebelum Adzan
									</label>
									<div class="input-group">
										<input type="number" name="prayer_mode_before_adzan" class="form-control"
											value="{{ old('prayer_mode_before_adzan', $setting->prayer_mode_before_adzan ?? 5) }}" min="0" max="60" required>
										<div class="input-group-append">
											<span class="input-group-text font-weight-bold">menit</span>
										</div>
									</div>
									<small class="text-muted">Layar TV mulai menghitung mundur sekian menit sebelum waktu adzan tiba.</small>
								</div>
							</div>

							<!-- Durasi Adzan -->
							<div class="col-md-4 mb-3">
								<div class="form-group mb-0">
									<label class="font-weight-bold text-dark">
										<i class="fas fa-volume-up text-info mr-1"></i> Durasi Saat Adzan
									</label>
									<div class="input-group">
										<input type="number" name="prayer_mode_adzan_duration" class="form-control"
											value="{{ old('prayer_mode_adzan_duration', $setting->prayer_mode_adzan_duration ?? 4) }}" min="1" max="60" required>
										<div class="input-group-append">
											<span class="input-group-text font-weight-bold">menit</span>
										</div>
									</div>
									<small class="text-muted">Lama waktu tampilan layar saat adzan berkumandang.</small>
								</div>
							</div>

							<!-- Durasi Iqamah -->
							<div class="col-md-4 mb-3">
								<div class="form-group mb-0">
									<label class="font-weight-bold text-dark">
										<i class="fas fa-stopwatch-20 text-danger mr-1"></i> Durasi Iqamah
									</label>
									<div class="input-group">
										<input type="number" name="prayer_mode_iqamah_duration" class="form-control"
											value="{{ old('prayer_mode_iqamah_duration', $setting->prayer_mode_iqamah_duration ?? 10) }}" min="1" max="60" required>
										<div class="input-group-append">
											<span class="input-group-text font-weight-bold">menit</span>
										</div>
									</div>
									<small class="text-muted">Hitung mundur iqamah menuju pelaksanaan sholat berjamaah.</small>
								</div>
							</div>
						</div>

						<div class="row mt-2">
							<!-- Durasi Sholat / Mode Hening -->
							<div class="col-md-4 mb-3">
								<div class="form-group mb-0">
									<label class="font-weight-bold text-dark">
										<i class="fas fa-pray text-success mr-1"></i> Durasi Sholat (Layar Hening)
									</label>
									<div class="input-group">
										<input type="number" name="prayer_mode_duration" class="form-control"
											value="{{ old('prayer_mode_duration', $setting->prayer_mode_duration ?? 10) }}" min="1" max="120" required>
										<div class="input-group-append">
											<span class="input-group-text font-weight-bold">menit</span>
										</div>
									</div>
									<small class="text-muted">Durasi layar TV terkunci hening/gelap saat sholat berlangsung.</small>
								</div>
							</div>

							<!-- Durasi Setelah Sholat -->
							<div class="col-md-4 mb-3">
								<div class="form-group mb-0">
									<label class="font-weight-bold text-dark">
										<i class="fas fa-comment-dots text-secondary mr-1"></i> Durasi Setelah Sholat
									</label>
									<div class="input-group">
										<input type="number" name="prayer_mode_after_prayer" class="form-control"
											value="{{ old('prayer_mode_after_prayer', $setting->prayer_mode_after_prayer ?? 3) }}" min="0" max="60" required>
										<div class="input-group-append">
											<span class="input-group-text font-weight-bold">menit</span>
										</div>
									</div>
									<small class="text-muted">Lama pesan setelah sholat sebelum TV kembali berotasi normal.</small>
								</div>
							</div>

							<!-- Audio Tarhim Sebelum Adzan -->
							<div class="col-md-4 mb-3">
								<div class="form-group mb-0">
									<label class="font-weight-bold text-dark">
										<i class="fas fa-music text-primary mr-1"></i> Waktu Audio Tarhim
									</label>
									@php
										$curTarhimSec = old('tarhim_trigger_seconds', $setting->tarhim_trigger_seconds ?? 300);
										$curTarhimMin = round($curTarhimSec / 60);
									@endphp
									<div class="input-group">
										<select class="form-control" id="tarhim_select_min" onchange="document.getElementById('tarhim_trigger_seconds_input').value = this.value * 60;">
											<option value="3" {{ $curTarhimMin == 3 ? 'selected' : '' }}>3 Menit Sebelum Adzan</option>
											<option value="5" {{ $curTarhimMin == 5 || empty($curTarhimMin) ? 'selected' : '' }}>5 Menit Sebelum Adzan (Standar)</option>
											<option value="10" {{ $curTarhimMin == 10 ? 'selected' : '' }}>10 Menit Sebelum Adzan</option>
											<option value="15" {{ $curTarhimMin == 15 ? 'selected' : '' }}>15 Menit Sebelum Adzan</option>
										</select>
										<input type="hidden" name="tarhim_trigger_seconds" id="tarhim_trigger_seconds_input" value="{{ $curTarhimSec }}">
									</div>
									<small class="text-muted">Audio tarhim otomatis berbunyi sebelum adzan tiba.</small>
								</div>
							</div>
						</div>
					</div>

					<!-- Durasi Khusus Sholat Jumat -->
					<div class="row">
						<div class="col-md-12 mb-3">
							<div class="card p-3 shadow-sm" style="background: #f0fdf4; border: 1.5px solid #86efac; border-left: 5px solid #10b981; border-radius: 16px;">
								<div class="d-flex justify-content-between align-items-center flex-wrap" style="gap: 16px;">
									<div style="flex: 1; min-width: 280px;">
										<div class="d-flex align-items-center mb-1">
											<i class="fas fa-mosque mr-2" style="font-size: 18px; color: #10b981;"></i>
											<span class="font-weight-bold" style="font-size: 16px; color: #10b981;">
												Durasi Sholat Jum'at <span style="font-weight: 700;">(Khutbah & Sholat)</span>
											</span>
										</div>
										<p class="mb-2" style="font-size: 13.5px; color: #475569; line-height: 1.45;">
											Khusus hari Jum'at waktu Dzuhur, TV otomatis masuk ke Mode Khutbah (nama Khatib, Imam, Muadzin, Bilal, hadits adab). Layar terkunci tenang selama durasi ini.
										</p>
										<div class="d-flex justify-content-end pr-md-4 mt-1">
											<span class="badge shadow-sm" style="background: #10b981; color: #ffffff; border-radius: 20px; padding: 5px 14px; font-size: 11.5px; font-weight: 700; letter-spacing: 0.2px;">
												<i class="fas fa-user-edit mr-1"></i> Bebas Diedit oleh Operator / Petugas
											</span>
										</div>
									</div>
									<div class="d-flex flex-column align-items-center justify-content-center text-center ml-md-3" style="min-width: 145px;">
										<div class="input-group shadow-sm" style="width: 145px; border: 1.5px solid #cbd5e1; border-radius: 10px; overflow: hidden; background: #ffffff;">
											<input type="number" 
												   name="prayer_mode_jumat_duration" 
												   id="prayer_mode_jumat_duration" 
												   class="form-control text-center font-weight-bold" 
												   value="{{ old('prayer_mode_jumat_duration', $setting->prayer_mode_jumat_duration ?? 50) }}" 
												   min="10" max="180" required
												   style="border: none; font-size: 18px; font-weight: 800; color: #10b981; height: 44px; box-shadow: none; padding-right: 0;">
											<div class="input-group-append">
												<span class="input-group-text bg-white" style="border: none; border-left: 1.5px solid #e2e8f0; font-size: 14px; font-weight: 800; color: #10b981; padding: 0 12px;">menit</span>
											</div>
										</div>
										<small class="mt-2 text-center font-weight-bold" style="font-size: 11.5px; color: #64748b; max-width: 155px; line-height: 1.3;">Bisa disesuaikan tiap pekan (35–60 mnt)</small>
									</div>
								</div>
							</div>
						</div>
					</div>

					<hr class="mt-2 mb-3">

					<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center">
						<small class="text-muted mb-2 mb-sm-0">
							<i class="fas fa-shield-alt text-success mr-1"></i> Perubahan durasi akan langsung diterapkan ke display TV secara otomatis.
						</small>
						<div>
							<button type="reset" class="btn btn-secondary px-3 mr-2">
								<i class="fas fa-undo mr-1"></i> Reset
							</button>
							<button type="submit" class="btn btn-primary font-weight-bold px-4 shadow-sm">
								<i class="fas fa-save mr-1"></i> Simpan Pengaturan Durasi
							</button>
						</div>
					</div>

				</form>
			</div>
		</div>

		<!-- Data Table -->
		<div class="card shadow mb-4">
			<div class="card-header py-3">
				<h6 class="m-0 font-weight-bold" style="color: #ffd700 !important;">
					<i class="fas fa-list" style="color: #ffd700 !important;"></i> <span
						style="color: #ffd700 !important;">Daftar Jadwal Sholat</span>
				</h6>
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
						<thead>
							<tr>
								<th width="50">No</th>
								<th>Nama Sholat</th>
								<th>Waktu</th>
								<th>Status</th>
								<th width="120">Aksi</th>
							</tr>
						</thead>
						<tbody>
							@foreach ($jadwal as $index => $item)
								@php
									$now = \Carbon\Carbon::now('Asia/Jakarta');
									$waktuSholat = \Carbon\Carbon::parse($item->waktu);
									$isNext = $waktuSholat->format('H:i') >= $now->format('H:i');
									$diffMinutes = $now->diffInMinutes($waktuSholat, false);
								@endphp
								<tr>
									<td class="text-center">{{ $index + 1 }}</td>
									<td>
										<div class="d-flex align-items-center">
											@php
												$bgColor = 'secondary';
												if (strtolower($item->nama_sholat) == 'imsak') $bgColor = 'dark';
												elseif (strtolower($item->nama_sholat) == 'subuh') $bgColor = 'primary';
												elseif (strtolower($item->nama_sholat) == 'syuruk' || strtolower($item->nama_sholat) == 'terbit') $bgColor = 'warning';
												elseif (strtolower($item->nama_sholat) == 'dzuhur') $bgColor = 'success';
												elseif (strtolower($item->nama_sholat) == 'ashar') $bgColor = 'info';
												elseif (strtolower($item->nama_sholat) == 'maghrib') $bgColor = 'warning';
												elseif (strtolower($item->nama_sholat) == 'isya') $bgColor = 'danger';

												$icon = 'fa-mosque';
												if (strtolower($item->nama_sholat) == 'imsak') $icon = 'fa-utensils';
												elseif (strtolower($item->nama_sholat) == 'syuruk' || strtolower($item->nama_sholat) == 'terbit') $icon = 'fa-sun';
											@endphp
											<div class="icon-circle bg-{{ $bgColor }} text-white mr-3"
												style="width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
												<i class="fas {{ $icon }}"></i>
											</div>
											<div>
												<strong>{{ $item->nama_sholat }}</strong>
											</div>
										</div>
									</td>
									<td>
										<span class="badge badge-dark p-2" style="font-size: 14px;">
											<i class="fas fa-clock"></i>
											{{ \Carbon\Carbon::parse($item->waktu)->format('H:i') }} WIB
										</span>
									</td>
									<td>
										@if($isNext && $diffMinutes <= 60 && $diffMinutes > 0)
											<span class="badge badge-warning">
												<i class="fas fa-hourglass-half"></i> Akan datang dalam {{ $diffMinutes }} menit
											</span>
										@elseif($isNext)
											<span class="badge badge-secondary">
												<i class="fas fa-calendar"></i> Mendatang
											</span>
										@else
											<span class="badge badge-success">
												<i class="fas fa-check-circle"></i> Telah Berlalu
											</span>
										@endif
									</td>
									<td class="text-center">
										<a href="{{ route('jadwal_sholat.edit', $item) }}" class="btn btn-sm btn-warning"
											data-toggle="tooltip" title="Edit">
											<i class="fas fa-edit"></i>
										</a>
										<form action="{{ route('jadwal_sholat.destroy', $item) }}" method="POST"
											class="d-inline delete-form">
											@csrf
											@method('DELETE')
											<button type="submit" class="btn btn-sm btn-danger delete-btn" data-toggle="tooltip"
												title="Hapus">
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
		document.addEventListener('DOMContentLoaded', function () {
			// Tooltip initialization
			$('[data-toggle="tooltip"]').tooltip();

			// Delete confirmation
			document.querySelectorAll('.delete-btn').forEach(button => {
				button.addEventListener('click', function (e) {
					e.preventDefault();
					const form = this.closest('.delete-form');

					Swal.fire({
						title: 'Apakah Anda yakin?',
						text: "Jadwal sholat akan dihapus secara permanen!",
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
				});
			});
		});
	</script>
@endsection