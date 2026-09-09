<!-- resources/views/sholat_jumat/create.blade.php -->
@extends('layouts.admin')

@section('main-content')
<div class="container-fluid">
	<!-- Page Heading -->
	<div class="d-sm-flex align-items-center justify-content-between mb-4">
		<h1 class="h3 mb-0 text-gray-800">
			<i class="fas fa-plus-circle text-primary"></i> Tambah Jadwal Sholat Jumat
		</h1>
		<a href="{{ route('sholat_jumat.index') }}" class="btn btn-secondary btn-sm shadow-sm">
			<i class="fas fa-arrow-left"></i> Kembali
		</a>
	</div>

	<div class="row">
		<div class="col-lg-11 mx-auto">
			<div class="card shadow mb-4">
				<div class="card-header py-3 bg-gradient-primary text-white">
					<h6 class="m-0 font-weight-bold">
						<i class="fas fa-calendar-check mr-1"></i> Form Rincian Petugas & Foto Sholat Jumat
					</h6>
				</div>
				<div class="card-body">
					<form action="{{ route('sholat_jumat.store') }}" method="POST" enctype="multipart/form-data" id="createForm">
						@csrf

						<div class="row">
							<!-- Kolom Kiri: Rincian Petugas Sholat Jumat -->
							<div class="col-md-7">
								<div class="form-group">
									<label for="tanggal"><strong>Tanggal Sholat Jumat <span class="text-danger">*</span></strong></label>
									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text"><i class="fas fa-calendar-alt text-primary"></i></span>
										</div>
										<input type="date" name="tanggal" id="tanggal" class="form-control @error('tanggal') is-invalid @enderror" 
										value="{{ old('tanggal') }}" required>
									</div>
									<small class="form-text text-muted">
										<i class="fas fa-info-circle"></i> Pilih tanggal pelaksanaan sholat Jumat
									</small>
									@error('tanggal')
									<small class="text-danger">{{ $message }}</small>
									@enderror
								</div>

								<div class="form-group">
									<label for="imam"><strong>Imam</strong></label>
									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text"><i class="fas fa-user text-success"></i></span>
										</div>
										<input type="text" name="imam" id="imam" class="form-control @error('imam') is-invalid @enderror" 
										value="{{ old('imam') }}" placeholder="Masukkan nama imam (contoh: Ust. H. Abdullah)">
									</div>
									<small class="form-text text-muted">
										<i class="fas fa-info-circle"></i> Nama imam yang akan memimpin sholat
									</small>
									@error('imam')
									<small class="text-danger">{{ $message }}</small>
									@enderror
								</div>

								<div class="form-group">
									<label for="khatib"><strong>Khatib</strong></label>
									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text"><i class="fas fa-chalkboard-user text-info"></i></span>
										</div>
										<input type="text" name="khatib" id="khatib" class="form-control @error('khatib') is-invalid @enderror" 
										value="{{ old('khatib') }}" placeholder="Masukkan nama khatib">
									</div>
									<small class="form-text text-muted">
										<i class="fas fa-info-circle"></i> Nama khatib yang menyampaikan khutbah Jumat
									</small>
									@error('khatib')
									<small class="text-danger">{{ $message }}</small>
									@enderror
								</div>

								<div class="form-group">
									<label for="muadzin"><strong>Muadzin</strong></label>
									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text"><i class="fas fa-microphone-alt text-warning"></i></span>
										</div>
										<input type="text" name="muadzin" id="muadzin" class="form-control @error('muadzin') is-invalid @enderror" 
										value="{{ old('muadzin') }}" placeholder="Masukkan nama muadzin">
									</div>
									<small class="form-text text-muted">
										<i class="fas fa-info-circle"></i> Nama muadzin yang mengumandangkan adzan
									</small>
									@error('muadzin')
									<small class="text-danger">{{ $message }}</small>
									@enderror
								</div>

								<div class="form-group">
									<label for="bilal"><strong>Bilal</strong></label>
									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text"><i class="fas fa-bullhorn text-danger"></i></span>
										</div>
										<input type="text" name="bilal" id="bilal" class="form-control @error('bilal') is-invalid @enderror" 
										value="{{ old('bilal') }}" placeholder="Masukkan nama bilal">
									</div>
									<small class="form-text text-muted">
										<i class="fas fa-info-circle"></i> Nama bilal yang memandu sholat Jumat
									</small>
									@error('bilal')
									<small class="text-danger">{{ $message }}</error>
									@enderror
								</div>
							</div>

							<!-- Kolom Kanan: Upload Foto Imam -->
							<div class="col-md-5">
								<div class="form-group">
									<label for="foto_imam"><strong>Foto Imam / Khotib</strong></label>
									<div class="custom-file mb-2">
										<input type="file" name="foto_imam" id="foto_imam" class="custom-file-input @error('foto_imam') is-invalid @enderror" accept=".jpg,.jpeg,.png,.webp">
										<label class="custom-file-label" for="foto_imam">Pilih Foto...</label>
									</div>
									<small class="form-text text-muted">
										<i class="fas fa-info-circle"></i> Format: JPG, PNG, WEBP. Maks 10MB.<br>
										Rasio <strong>4:5 (portrait)</strong> sangat direkomendasikan agar pas sempurna di bingkai TV.
									</small>
									@error('foto_imam')
									<small class="text-danger d-block">{{ $message }}</small>
									@enderror
								</div>

								<!-- Kotak Preview Foto Rasio 4:5 -->
								<div class="card p-2 text-center bg-light border-dashed mb-3" style="border: 2px dashed #cbd5e0; border-radius: 12px; min-height: 290px; display: flex; align-items: center; justify-content: center;">
									<div id="previewContainer" style="width: 200px; aspect-ratio: 4 / 5; border-radius: 10px; overflow: hidden; position: relative; background: #02120b; border: 1.5px solid #ffd700; box-shadow: 0 4px 15px rgba(0,0,0,0.3); display: none;">
										<img id="imgPreview" src="" alt="Preview Foto Imam" style="width: 100%; height: 100%; object-fit: cover; object-position: center 15%;">
										<div style="position: absolute; bottom: 0; left: 0; right: 0; background: linear-gradient(0deg, rgba(2,16,11,0.95), transparent); padding: 8px 4px 4px; color: #ffd700; font-size: 11px; font-weight: bold;">
											PREVIEW RASIO 4:5
										</div>
									</div>

									<div id="noPreviewText" class="text-muted p-4">
										<img src="{{ asset('image/display/default_imam.jpg') }}" alt="Default" style="width: 140px; aspect-ratio: 4 / 5; object-fit: cover; border-radius: 8px; border: 1px solid #ddd; margin-bottom: 10px; box-shadow: 0 3px 10px rgba(0,0,0,0.15);">
										<div class="font-weight-bold text-dark">Foto Default Aktif</div>
										<small class="text-secondary">(Akan menggunakan foto ini jika tidak mengunggah foto baru)</small>
									</div>
								</div>
							</div>
						</div>

						<hr>

						<div class="form-group text-center mt-3">
							<button type="submit" class="btn btn-primary btn-lg px-5 shadow-sm">
								<i class="fas fa-save mr-1"></i> Simpan Jadwal & Foto
							</button>
							<a href="{{ route('sholat_jumat.index') }}" class="btn btn-secondary btn-lg px-4 ml-2">
								<i class="fas fa-times mr-1"></i> Batal
							</a>
						</div>
					</form>
				</div>
			</div>

			<!-- Informasi Card -->
			<div class="card shadow mb-4 border-left-warning">
				<div class="card-header py-3">
					<h6 class="m-0 font-weight-bold text-warning">
						<i class="fas fa-lightbulb"></i> Informasi Tambahan
					</h6>
				</div>
				<div class="card-body">
					<ul class="mb-0 text-muted">
						<li>Foto imam akan ditampilkan di kotak sebelah kiri halaman Sholat Jumat dengan rasio <strong>4:5</strong> lock-pixel.</li>
						<li>Jika foto imam tidak diisi/diunggah, sistem otomatis menampilkan foto siluet imam standar Islami.</li>
						<li>Pastikan nama imam, khatib, muadzin, dan bilal sudah sesuai sebelum menyimpan.</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	document.addEventListener('DOMContentLoaded', function() {
		// Set default date to next Friday
		const tanggalInput = document.getElementById('tanggal');
		if (tanggalInput && !tanggalInput.value) {
			const today = new Date();
			const day = today.getDay();
			const daysUntilFriday = (5 - day + 7) % 7 || 7;
			const nextFriday = new Date(today);
			nextFriday.setDate(today.getDate() + daysUntilFriday);
			tanggalInput.value = nextFriday.toISOString().split('T')[0];
		}

		// Interactive File Upload Preview
		const fileInput = document.getElementById('foto_imam');
		const imgPreview = document.getElementById('imgPreview');
		const previewContainer = document.getElementById('previewContainer');
		const noPreviewText = document.getElementById('noPreviewText');
		const label = document.querySelector('.custom-file-label');

		if (fileInput) {
			fileInput.addEventListener('change', function(e) {
				const file = e.target.files[0];
				if (file) {
					if (label) label.textContent = file.name;
					const reader = new FileReader();
					reader.onload = function(evt) {
						imgPreview.src = evt.target.result;
						previewContainer.style.display = 'block';
						noPreviewText.style.display = 'none';
					};
					reader.readAsDataURL(file);
				} else {
					if (label) label.textContent = 'Pilih Foto...';
					previewContainer.style.display = 'none';
					noPreviewText.style.display = 'block';
				}
			});
		}
	});
</script>
@endsection