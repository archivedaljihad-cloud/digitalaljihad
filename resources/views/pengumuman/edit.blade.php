<!-- resources/views/pengumuman/edit.blade.php -->
@extends('layouts.admin')

@section('main-content')
<div class="container-fluid">
	<!-- Page Heading -->
	<div class="d-sm-flex align-items-center justify-content-between mb-4">
		<h1 class="h3 mb-0 text-gray-800">
			<i class="fas fa-edit text-primary"></i> Edit Informasi Kegiatan & Kajian
		</h1>
		<a href="{{ route('pengumuman.index') }}" class="btn btn-secondary btn-sm shadow-sm">
			<i class="fas fa-arrow-left"></i> Kembali
		</a>
	</div>

	<div class="row">
		<div class="col-lg-10 mx-auto">
			<div class="card shadow mb-4">
				<div class="card-header py-3 bg-gradient-primary text-white d-flex justify-content-between align-items-center flex-wrap">
					<h6 class="m-0 font-weight-bold">
						<i class="fas fa-edit mr-1"></i> Form Edit Kegiatan & Kajian Masjid
					</h6>
					<button type="button" class="btn btn-warning btn-sm font-weight-bold shadow-sm mt-1 mt-md-0" data-toggle="modal" data-target="#aiModal" style="color: #071a10; border-radius: 20px; border: 1px solid #ffd700;">
						<i class="fas fa-magic mr-1"></i> ✨ Susun dengan AI
					</button>
				</div>
				<div class="card-body">
					<form action="{{ route('pengumuman.update', $pengumuman) }}" method="POST" enctype="multipart/form-data">
						@csrf
						@method('PUT')

						<div class="row">
							<!-- Kolom Kiri: Detail Kegiatan -->
							<div class="col-md-7">
								<div class="form-group">
									<label for="judul"><strong>Judul / Nama Kegiatan</strong></label>
									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text"><i class="fas fa-bullhorn text-primary"></i></span>
										</div>
										<input type="text" name="judul" id="judul" class="form-control @error('judul') is-invalid @enderror" 
										value="{{ old('judul', $pengumuman->judul) }}" placeholder="Contoh: Kajian Rutin Ahad Pagi / Tahsin Al-Qur'an">
									</div>
									<small class="form-text text-muted">Akan ditampilkan sebagai judul utama berukuran besar pada layar TV.</small>
								</div>

								<div class="form-group">
									<label for="pemateri"><strong>Nama Ustadz / Pemateri / Narasumber</strong></label>
									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text"><i class="fas fa-user-tie text-success"></i></span>
										</div>
										<input type="text" name="pemateri" id="pemateri" class="form-control @error('pemateri') is-invalid @enderror" 
										value="{{ old('pemateri', $pengumuman->pemateri) }}" placeholder="Contoh: Ustadz Dr. H. Fulan, Lc., M.A.">
									</div>
								</div>

								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label for="tanggal"><strong>Tanggal Kegiatan <span class="text-danger">*</span></strong></label>
											<div class="input-group">
												<div class="input-group-prepend">
													<span class="input-group-text"><i class="fas fa-calendar-day text-info"></i></span>
												</div>
												<input type="date" name="tanggal" id="tanggal" class="form-control @error('tanggal') is-invalid @enderror" 
												value="{{ old('tanggal', $pengumuman->tanggal) }}" required>
											</div>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label for="waktu"><strong>Waktu / Jam</strong></label>
											<div class="input-group">
												<div class="input-group-prepend">
													<span class="input-group-text"><i class="fas fa-clock text-warning"></i></span>
												</div>
												<input type="text" name="waktu" id="waktu" class="form-control @error('waktu') is-invalid @enderror" 
												value="{{ old('waktu', $pengumuman->waktu) }}" placeholder="Contoh: Ba'da Maghrib / 08.00 WIB">
											</div>
										</div>
									</div>
								</div>

								<div class="form-group">
									<label for="tempat"><strong>Tempat / Lokasi</strong></label>
									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text"><i class="fas fa-map-marker-alt text-danger"></i></span>
										</div>
										<input type="text" name="tempat" id="tempat" class="form-control @error('tempat') is-invalid @enderror" 
										value="{{ old('tempat', $pengumuman->tempat ?? 'Ruang Utama Masjid Jami Al-Jihad') }}" placeholder="Contoh: Ruang Utama Masjid">
									</div>
								</div>
							</div>

							<!-- Kolom Kanan: Foto Ustadz / Flyer -->
							<div class="col-md-5">
								<div class="form-group">
									<label for="foto"><strong>Foto Ustadz / Pamflet Kegiatan</strong></label>
									<div class="custom-file mb-2">
										<input type="file" name="foto" id="foto" class="custom-file-input @error('foto') is-invalid @enderror" accept=".jpg,.jpeg,.png,.webp">
										<label class="custom-file-label" for="foto">{{ $pengumuman->foto ? basename($pengumuman->foto) : 'Pilih Foto Baru...' }}</label>
									</div>
									<small class="form-text text-muted">Biarkan kosong jika tidak ingin mengubah foto yang ada saat ini.</small>
									@error('foto')
									<small class="text-danger d-block">{{ $message }}</small>
									@enderror
								</div>

								<div class="card p-2 text-center bg-light border-dashed mb-3" style="min-height: 200px; display: flex; align-items: center; justify-content: center;">
									@if($pengumuman->foto_url)
										<img id="imgPreview" src="{{ $pengumuman->foto_url }}" alt="Preview Foto" class="img-fluid rounded shadow-sm" style="max-height: 210px; object-fit: cover;">
										<div id="noPreviewText" class="text-muted p-3" style="display: none;">
											<i class="fas fa-user-circle fa-4x text-gray-300 mb-2"></i>
											<div>Pratinjau Foto</div>
										</div>
									@else
										<img id="imgPreview" src="" alt="Preview Foto" class="img-fluid rounded shadow-sm" style="max-height: 210px; display: none; object-fit: cover;">
										<div id="noPreviewText" class="text-muted p-3">
											<i class="fas fa-user-circle fa-4x text-gray-300 mb-2"></i>
											<div>Belum ada foto</div>
											<small class="text-secondary">(Opsi default islami di layar TV)</small>
										</div>
									@endif
								</div>
							</div>
						</div>

						<!-- Keterangan / Deskripsi -->
						<div class="form-group mt-2">
							<label for="isi"><strong>Deskripsi / Keterangan Tambahan <span class="text-danger">*</span></strong></label>
							<textarea name="isi" id="isi" class="form-control @error('isi') is-invalid @enderror" 
							rows="4" required placeholder="Tuliskan keterangan kegiatan, materi bahasan, atau ajakan untuk jamaah...">{{ old('isi', $pengumuman->isi) }}</textarea>
							<div class="d-flex justify-content-between mt-1 text-muted small">
								<span><i class="fas fa-info-circle"></i> Pesan ini akan tampil di layar TV & berjalan pada sistem display.</span>
								<span><span id="charCount">{{ strlen($pengumuman->isi) }}</span> karakter</span>
							</div>
							@error('isi')
							<small class="text-danger">{{ $message }}</small>
							@enderror
						</div>

						<hr>

						<div class="form-group text-center mb-0">
							<button type="submit" class="btn btn-primary btn-lg px-5 shadow">
								<i class="fas fa-save mr-1"></i> Update Kegiatan
							</button>
							<a href="{{ route('pengumuman.index') }}" class="btn btn-secondary btn-lg px-4 ml-2">
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
    // Preview image
    document.getElementById('foto').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const preview = document.getElementById('imgPreview');
        const noPreview = document.getElementById('noPreviewText');
        const label = document.querySelector('.custom-file-label');
        
        if (file) {
            label.textContent = file.name;
            const reader = new FileReader();
            reader.onload = function(evt) {
                preview.src = evt.target.result;
                preview.style.display = 'block';
                if (noPreview) noPreview.style.display = 'none';
            }
            reader.readAsDataURL(file);
        }
    });

    // Character counter
	const textarea = document.getElementById('isi');
	const charCount = document.getElementById('charCount');
	textarea.addEventListener('input', function() {
		charCount.textContent = this.value.length;
	});
</script>

@include('pengumuman.partials.ai-modal')
@endsection