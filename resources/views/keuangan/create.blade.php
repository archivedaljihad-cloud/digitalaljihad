<!-- resources/views/keuangan/create.blade.php -->
@extends('layouts.admin')

@section('main-content')
<div class="container-fluid">
	<!-- Page Heading -->
	<div class="d-sm-flex align-items-center justify-content-between mb-4">
		<h1 class="h3 mb-0 text-gray-800">
			<i class="fas fa-plus-circle"></i> Tambah Transaksi Keuangan
		</h1>
		<a href="{{ route('keuangan.index') }}" class="btn btn-secondary btn-sm shadow-sm">
			<i class="fas fa-arrow-left"></i> Kembali
		</a>
	</div>

	<div class="row">
		<div class="col-lg-8 mx-auto">
			<div class="card shadow mb-4">
				<div class="card-header py-3">
					<h6 class="m-0 font-weight-bold text-primary">
						<i class="fas fa-info-circle"></i> Form Transaksi
					</h6>
				</div>
				<div class="card-body">
					<form action="{{ route('keuangan.store') }}" method="POST" id="keuanganForm">
						@csrf

						<div class="form-group">
							<label for="tanggal">Tanggal <span class="text-danger">*</span></label>
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
								</div>
								<input type="date" class="form-control @error('tanggal') is-invalid @enderror" 
								id="tanggal" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" required>
							</div>
							@error('tanggal')
							<small class="text-danger">{{ $message }}</small>
							@enderror
						</div>

						<div class="form-group">
							<label for="deskripsi">Deskripsi <span class="text-danger">*</span></label>
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text"><i class="fas fa-align-left"></i></span>
								</div>
								<input type="text" class="form-control @error('deskripsi') is-invalid @enderror" 
								id="deskripsi" name="deskripsi" value="{{ old('deskripsi') }}" required placeholder="Contoh: Infak Jumat, Pembelian ATK, dll">
							</div>
							@error('deskripsi')
							<small class="text-danger">{{ $message }}</small>
							@enderror
						</div>

						<div class="form-group">
							<label for="kategori">Kategori</label>
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text"><i class="fas fa-tag"></i></span>
								</div>
								<select class="form-control @error('kategori') is-invalid @enderror" id="kategori" name="kategori">
									<option value="" {{ old('kategori') == '' ? 'selected' : '' }}>- Pilih Kategori -</option>
									<option value="Infak" {{ old('kategori') == 'Infak' ? 'selected' : '' }}>Infak</option>
									<option value="Sumbangan" {{ old('kategori') == 'Sumbangan' ? 'selected' : '' }}>Sumbangan</option>
									<option value="Donatur" {{ old('kategori') == 'Donatur' ? 'selected' : '' }}>Donatur Tetap</option>
									<option value="Zakat" {{ old('kategori') == 'Zakat' ? 'selected' : '' }}>Zakat</option>
									<option value="Operasional" {{ old('kategori') == 'Operasional' ? 'selected' : '' }}>Operasional</option>
									<option value="Konsumsi" {{ old('kategori') == 'Konsumsi' ? 'selected' : '' }}>Konsumsi</option>
									<option value="Perawatan" {{ old('kategori') == 'Perawatan' ? 'selected' : '' }}>Perawatan</option>
									<option value="Inventaris" {{ old('kategori') == 'Inventaris' ? 'selected' : '' }}>Inventaris</option>
									<option value="Pembangunan" {{ old('kategori') == 'Pembangunan' ? 'selected' : '' }}>Pembangunan</option>
									<option value="Kegiatan" {{ old('kategori') == 'Kegiatan' ? 'selected' : '' }}>Kegiatan</option>
									<option value="Utilitas" {{ old('kategori') == 'Utilitas' ? 'selected' : '' }}>Utilitas (Listrik/Air)</option>
								</select>
							</div>
							@error('kategori')
							<small class="text-danger">{{ $message }}</small>
							@enderror
						</div>

						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label for="pemasukan">Pemasukan (Rp)</label>
									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text font-weight-bold text-success">Rp</span>
										</div>
										<input type="text" inputmode="numeric" class="form-control rupiah-input @error('pemasukan') is-invalid @enderror" 
										id="pemasukan" name="pemasukan" value="{{ old('pemasukan') ? number_format((float)str_replace(['.', ','], ['', '.'], old('pemasukan')), 0, ',', '.') : '' }}" placeholder="Contoh: 500.000">
									</div>
									@error('pemasukan')
									<small class="text-danger">{{ $message }}</small>
									@enderror
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label for="pengeluaran">Pengeluaran (Rp)</label>
									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text font-weight-bold text-danger">Rp</span>
										</div>
										<input type="text" inputmode="numeric" class="form-control rupiah-input @error('pengeluaran') is-invalid @enderror" 
										id="pengeluaran" name="pengeluaran" value="{{ old('pengeluaran') ? number_format((float)str_replace(['.', ','], ['', '.'], old('pengeluaran')), 0, ',', '.') : '' }}" placeholder="Contoh: 2.000.000">
									</div>
									@error('pengeluaran')
									<small class="text-danger">{{ $message }}</small>
									@enderror
								</div>
							</div>
						</div>

						<hr>

						<div class="alert alert-info">
							<i class="fas fa-info-circle"></i>
							<strong>Informasi:</strong> Nilai rupiah otomatis diberi titik pemisah ribuan (contoh: <strong>500.000</strong> atau <strong>2.000.000</strong>) untuk memudahkan bendahara.
						</div>

						<div class="form-group text-center">
							<button type="submit" class="btn btn-primary btn-lg px-5">
								<i class="fas fa-save"></i> Simpan Transaksi
							</button>
							<a href="{{ route('keuangan.index') }}" class="btn btn-secondary btn-lg px-4">
								<i class="fas fa-times"></i> Batal
							</a>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	function formatRupiah(angka) {
		let number_string = angka.replace(/[^0-9]/g, '').toString();
		let sisa = number_string.length % 3;
		let rupiah = number_string.substr(0, sisa);
		let ribuan = number_string.substr(sisa).match(/\d{3}/gi);

		if (ribuan) {
			let separator = sisa ? '.' : '';
			rupiah += separator + ribuan.join('.');
		}
		return rupiah;
	}

	document.querySelectorAll('.rupiah-input').forEach(function(input) {
		input.addEventListener('input', function() {
			this.value = formatRupiah(this.value);
		});
		input.addEventListener('blur', function() {
			if (this.value) {
				this.value = formatRupiah(this.value);
			}
		});
		if (input.value) {
			input.value = formatRupiah(input.value);
		}
	});
</script>
@endsection