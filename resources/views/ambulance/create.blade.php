<!-- resources/views/ambulance/create.blade.php -->
@extends('layouts.admin')

@section('main-content')
<div class="container-fluid">
	<!-- Page Heading -->
	<div class="d-sm-flex align-items-center justify-content-between mb-4">
		<h1 class="h3 mb-0 text-gray-800">
			<i class="fas fa-plus-circle"></i> Tambah Transaksi Kas Ambulance
		</h1>
		<a href="{{ route('ambulance.index') }}" class="btn btn-secondary btn-sm shadow-sm">
			<i class="fas fa-arrow-left"></i> Kembali
		</a>
	</div>

	<div class="row">
		<div class="col-lg-8 mx-auto">
			<div class="card shadow mb-4">
				<div class="card-header py-3">
					<h6 class="m-0 font-weight-bold text-primary">
						<i class="fas fa-ambulance"></i> Form Transaksi Kas Ambulance
					</h6>
				</div>
				<div class="card-body">
					<form action="{{ route('ambulance.store') }}" method="POST" id="ambulanceForm">
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
								id="deskripsi" name="deskripsi" value="{{ old('deskripsi') }}" required placeholder="Contoh: Infak Operasional Ambulance, Pembelian BBM & Tol, Service Rutin">
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
									<option value="Operasional" {{ old('kategori') == 'Operasional' ? 'selected' : '' }}>Operasional</option>
									<option value="Konsumsi" {{ old('kategori') == 'Konsumsi' ? 'selected' : '' }}>Konsumsi</option>
									<option value="Perawatan" {{ old('kategori') == 'Perawatan' ? 'selected' : '' }}>Perawatan</option>
									<option value="Inventaris" {{ old('kategori') == 'Inventaris' ? 'selected' : '' }}>Inventaris</option>
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
										id="pemasukan" name="pemasukan" value="{{ (old('pemasukan') !== null && old('pemasukan') !== '' && (float)str_replace(['.', ','], ['', '.'], (string)old('pemasukan')) > 0) ? number_format((float)str_replace(['.', ','], ['', '.'], (string)old('pemasukan')), 0, ',', '.') : ((string)old('pemasukan') === '0' ? '0' : '') }}" placeholder="Contoh: 500.000">
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
										id="pengeluaran" name="pengeluaran" value="{{ (old('pengeluaran') !== null && old('pengeluaran') !== '' && (float)str_replace(['.', ','], ['', '.'], (string)old('pengeluaran')) > 0) ? number_format((float)str_replace(['.', ','], ['', '.'], (string)old('pengeluaran')), 0, ',', '.') : ((string)old('pengeluaran') === '0' ? '0' : '') }}" placeholder="Contoh: 250.000">
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
							<a href="{{ route('ambulance.index') }}" class="btn btn-secondary btn-lg px-4">
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
		if (angka === null || angka === undefined) return '';
		let str = angka.toString().trim();
		if (str === '' || str === '-') return '';
		let number_string = str.replace(/[^0-9]/g, '');
		if (!number_string) return '';

		// Hilangkan leading zero kecuali jika nilai hanya '0'
		if (number_string.length > 1 && number_string.startsWith('0')) {
			number_string = parseInt(number_string, 10).toString();
		}

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
