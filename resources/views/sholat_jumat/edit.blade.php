<!-- resources/views/sholat_jumat/edit.blade.php -->
@extends('layouts.admin')

@section('main-content')
<div class="container-fluid">
	<!-- Page Heading -->
	<div class="d-sm-flex align-items-center justify-content-between mb-4">
		<h1 class="h3 mb-0 text-gray-800">
			<i class="fas fa-edit text-warning"></i> Edit Jadwal Sholat Jumat
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
						<i class="fas fa-edit mr-1"></i> Form Edit: {{ \Carbon\Carbon::parse($sholat_jumat->tanggal)->translatedFormat('l, d F Y') }}
					</h6>
				</div>
				<div class="card-body">
					<form action="{{ route('sholat_jumat.update', $sholat_jumat) }}" method="POST" enctype="multipart/form-data" id="editForm">
						@csrf
						@method('PUT')

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
										value="{{ old('tanggal', $sholat_jumat->tanggal) }}" required>
									</div>
									<small class="form-text text-muted">
										<i class="fas fa-info-circle"></i> Tanggal pelaksanaan sholat Jumat
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
										value="{{ old('imam', $sholat_jumat->imam) }}" placeholder="Masukkan nama imam">
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
										value="{{ old('khatib', $sholat_jumat->khatib) }}" placeholder="Masukkan nama khatib">
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
										value="{{ old('muadzin', $sholat_jumat->muadzin) }}" placeholder="Masukkan nama muadzin">
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
										value="{{ old('bilal', $sholat_jumat->bilal) }}" placeholder="Masukkan nama bilal">
									</div>
									<small class="form-text text-muted">
										<i class="fas fa-info-circle"></i> Nama bilal yang memandu sholat Jumat
									</small>
									@error('bilal')
									<small class="text-danger">{{ $message }}</error>
									@enderror
								</div>
							</div>

							<!-- Kolom Kanan: Upload / Ganti Foto Imam -->
							<div class="col-md-5">
								<div class="form-group">
									<label for="foto_imam"><strong>Foto Imam / Khotib</strong></label>
									<div class="custom-file mb-2">
										<input type="file" name="foto_imam" id="foto_imam" class="custom-file-input @error('foto_imam') is-invalid @enderror" accept=".jpg,.jpeg,.png,.webp">
										<label class="custom-file-label" for="foto_imam">
											{{ !empty($sholat_jumat->foto_imam) ? basename($sholat_jumat->foto_imam) : 'Pilih Foto Baru...' }}
										</label>
									</div>
									<small class="form-text text-muted">
										<i class="fas fa-info-circle"></i> Format: JPG, PNG, WEBP. Maks 10MB.<br>
										Rasio <strong>4:5 (portrait)</strong> sangat direkomendasikan agar pas di TV.
									</small>
									@error('foto_imam')
									<small class="text-danger d-block">{{ $message }}</small>
									@enderror
								</div>

								<!-- Kotak Preview Foto Rasio 4:5 -->
								<div class="card p-2 text-center bg-light mb-3" style="border: 2px dashed #cbd5e0; border-radius: 12px; min-height: 290px; display: flex; align-items: center; justify-content: center;">
									<div id="previewContainer" style="width: 200px; aspect-ratio: 4 / 5; border-radius: 10px; overflow: hidden; position: relative; background: #02120b; border: 1.5px solid #ffd700; box-shadow: 0 4px 15px rgba(0,0,0,0.3);">
										@php
											$hasCustomPhoto = !empty($sholat_jumat->foto_imam);
											$imgSrc = $hasCustomPhoto ? asset('storage/' . $sholat_jumat->foto_imam) : asset('image/display/default_imam.jpg') . '?v=3.0.4';
										@endphp
										<img id="imgPreview" src="{{ $imgSrc }}" alt="Foto Imam / Logo Al-Jihad" style="width: 100%; height: 100%; object-fit: cover; object-position: center 15%;">
										<div id="previewLabel" style="position: absolute; bottom: 0; left: 0; right: 0; background: linear-gradient(0deg, rgba(2,16,11,0.95), transparent); padding: 8px 4px 4px; color: #ffd700; font-size: 11px; font-weight: bold;">
											{{ $hasCustomPhoto ? 'FOTO AKTIF (4:5)' : 'LOGO DEFAULT AL-JIHAD (4:5)' }}
										</div>
									</div>

									@if($hasCustomPhoto)
									<div class="mt-2" id="deletePhotoWrap">
										<div class="custom-control custom-checkbox">
											<input type="checkbox" class="custom-control-input" id="hapus_foto" name="hapus_foto" value="1">
											<label class="custom-control-label text-danger font-weight-bold" for="hapus_foto">
												<i class="fas fa-trash-alt"></i> Hapus foto ini & gunakan default
											</label>
										</div>
									</div>
									@endif
								</div>
							</div>
						</div>

						<hr>

						<div class="form-group text-center mt-3">
							<button type="submit" class="btn btn-primary btn-lg px-5 shadow-sm">
								<i class="fas fa-save mr-1"></i> Update Jadwal & Foto
							</button>
							<a href="{{ route('sholat_jumat.index') }}" class="btn btn-secondary btn-lg px-4 ml-2">
								<i class="fas fa-times mr-1"></i> Batal
							</a>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	document.addEventListener('DOMContentLoaded', function() {
		// Interactive File Upload Preview
		const fileInput = document.getElementById('foto_imam');
		const imgPreview = document.getElementById('imgPreview');
		const previewLabel = document.getElementById('previewLabel');
		const label = document.querySelector('.custom-file-label');
		const hapusCheckbox = document.getElementById('hapus_foto');

		if (fileInput) {
			fileInput.addEventListener('change', function(e) {
				const file = e.target.files[0];
				if (file) {
					if (label) label.textContent = file.name;
					const reader = new FileReader();
					reader.onload = function(evt) {
						imgPreview.src = evt.target.result;
						if (previewLabel) {
							previewLabel.textContent = 'PREVIEW FOTO BARU (4:5)';
							previewLabel.style.color = '#00ff88';
						}
					};
					reader.readAsDataURL(file);
					if (hapusCheckbox) {
						hapusCheckbox.checked = false;
					}
				}
			});
		}

		if (hapusCheckbox) {
			hapusCheckbox.addEventListener('change', function(e) {
				if (e.target.checked) {
					imgPreview.src = "{{ asset('image/display/default_imam.jpg') }}?v=3.0.4";
					if (previewLabel) {
						previewLabel.textContent = 'AKAN KEMBALI KE LOGO AL-JIHAD';
						previewLabel.style.color = '#ff9999';
					}
				} else {
					imgPreview.src = "{{ !empty($sholat_jumat->foto_imam) ? asset('storage/' . $sholat_jumat->foto_imam) : asset('image/display/default_imam.jpg') . '?v=3.0.4' }}";
					if (previewLabel) {
						previewLabel.textContent = "{{ !empty($sholat_jumat->foto_imam) ? 'FOTO AKTIF (4:5)' : 'LOGO DEFAULT AL-JIHAD (4:5)' }}";
						previewLabel.style.color = '#ffd700';
					}
				}
			});
		}
	});
</script>
@endsection