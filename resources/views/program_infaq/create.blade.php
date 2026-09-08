<!-- resources/views/program_infaq/create.blade.php -->
@extends('layouts.admin')

@section('main-content')
<div class="container-fluid">
	<div class="d-sm-flex align-items-center justify-content-between mb-4">
		<h1 class="h3 mb-0 text-gray-800">
			<i class="fas fa-plus-circle text-primary mr-2"></i> Tambah Program Penggalangan Infaq
		</h1>
		<a href="{{ route('program-infaq.index') }}" class="btn btn-secondary btn-sm shadow-sm">
			<i class="fas fa-arrow-left"></i> Kembali
		</a>
	</div>

	<div class="row justify-content-center">
		<div class="col-lg-8">
			<div class="card shadow mb-4 border-left-primary">
				<div class="card-header py-3">
					<h6 class="m-0 font-weight-bold text-primary">Informasi & Target Program Infaq</h6>
				</div>
				<div class="card-body">
					<form action="{{ route('program-infaq.store') }}" method="POST">
						@csrf

						<div class="form-group">
							<label for="nama_program" class="font-weight-bold text-gray-700">
								Nama Kegiatan / Program Infaq <span class="text-danger">*</span>
							</label>
							<input type="text" class="form-control @error('nama_program') is-invalid @enderror" 
								id="nama_program" name="nama_program" value="{{ old('nama_program') }}" 
								placeholder="Contoh: Pengadaan AC Baru, Penambahan Keramik, Pembelian Karpet Masjid" required>
							@error('nama_program')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
							<small class="text-muted">Nama ini akan tampil sebagai judul besar program di layar monitor TV.</small>
						</div>

						<div class="form-group">
							<label for="target_dana" class="font-weight-bold text-gray-700">
								Target Total Penggalangan Dana (Rp) <span class="text-danger">*</span>
							</label>
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text font-weight-bold">Rp</span>
								</div>
								<input type="number" class="form-control font-weight-bold text-primary @error('target_dana') is-invalid @enderror" 
									id="target_dana" name="target_dana" value="{{ old('target_dana') }}" 
									placeholder="Contoh: 25000000" min="0" step="1000" required>
							</div>
							@error('target_dana')
								<div class="text-danger small mt-1">{{ $message }}</div>
							@enderror
							<small class="text-muted">Target nominal yang dibutuhkan untuk menyelesaikan kegiatan.</small>
						</div>

						<div class="row">
							<div class="col-md-6 form-group">
								<label for="tanggal_mulai" class="font-weight-bold text-gray-700">Tanggal Mulai</label>
								<input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai" value="{{ old('tanggal_mulai', date('Y-m-d')) }}">
							</div>
							<div class="col-md-6 form-group">
								<label for="tanggal_selesai" class="font-weight-bold text-gray-700">Target Tanggal Selesai</label>
								<input type="date" class="form-control" id="tanggal_selesai" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}">
							</div>
						</div>

						<div class="form-group">
							<label for="keterangan" class="font-weight-bold text-gray-700">Deskripsi / Keterangan Program</label>
							<textarea class="form-control" id="keterangan" name="keterangan" rows="3" 
								placeholder="Contoh: Penggalangan dana bersama jamaah untuk pengadaan 4 unit AC Standing 5 PK demi kenyamanan ibadah shalat berjamaah.">{{ old('keterangan') }}</textarea>
						</div>

						<div class="form-group">
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
								<label class="custom-control-label font-weight-bold text-success" for="is_active">
									<i class="fas fa-tv mr-1"></i> Aktifkan langsung untuk tampil di Layar Monitor TV
								</label>
							</div>
							<small class="text-muted">Jika dicentang, program ini akan menjadi program utama yang tayang pada rotasi display TV.</small>
						</div>

						<hr>

						<div class="d-flex justify-content-end">
							<a href="{{ route('program-infaq.index') }}" class="btn btn-secondary mr-2">Batal</a>
							<button type="submit" class="btn btn-primary px-4 shadow-sm">
								<i class="fas fa-save mr-1"></i> Simpan Program Infaq
							</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
