@extends('layouts.admin')
@section('main-content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-edit"></i>
            Edit Agenda Kajian
        </h1>
        <a href="{{ route('agenda_kajian.index') }}"
           class="btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left"></i>
            Kembali
        </a>
    </div>
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Terjadi kesalahan!</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Form Edit Agenda Kajian
            </h6>
        </div>
        <div class="card-body">
            <form
                action="{{ route('agenda_kajian.update', $agenda->id) }}"
                method="POST"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label>
                        Judul Kajian
                    </label>
                    <input
                        type="text"
                        name="judul"
                        class="form-control @error('judul') is-invalid @enderror"
                        value="{{ old('judul', $agenda->judul) }}"
                        required>
                    @error('judul')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-group">
                    <label>
                        Pemateri
                    </label>
                    <input
                        type="text"
                        name="pemateri"
                        class="form-control @error('pemateri') is-invalid @enderror"
                        value="{{ old('pemateri', $agenda->pemateri) }}"
                        required>
                    @error('pemateri')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>
                            Tanggal
                        </label>
                        <input
                            type="date"
                            name="tanggal"
                            class="form-control @error('tanggal') is-invalid @enderror"
                            value="{{ old('tanggal', \Carbon\Carbon::parse($agenda->tanggal)->format('Y-m-d')) }}"
                            required>
                        @error('tanggal')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-group col-md-6">
                        <label>
                            Waktu
                        </label>
                        <input
                            type="time"
                            name="waktu"
                            class="form-control @error('waktu') is-invalid @enderror"
                            value="{{ old('waktu', \Carbon\Carbon::parse($agenda->waktu)->format('H:i')) }}"
                            required>
                        @error('waktu')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                <div class="form-group">
                    <label>
                        Lokasi
                    </label>
                    <input
                        type="text"
                        name="lokasi"
                        class="form-control @error('lokasi') is-invalid @enderror"
                        value="{{ old('lokasi', $agenda->lokasi) }}"
                        placeholder="Contoh: Masjid Jami' Al Jihad">
                    @error('lokasi')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-group">
                    <label>
                        Deskripsi
                    </label>
                    <textarea
                        name="deskripsi"
                        rows="5"
                        class="form-control @error('deskripsi') is-invalid @enderror"
                        placeholder="Deskripsi singkat agenda kajian...">{{ old('deskripsi', $agenda->deskripsi) }}</textarea>
                    @error('deskripsi')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-group">
                    <label>
                        Poster Saat Ini
                    </label>
                    <div>
                        @if($agenda->gambar)
                            <img
                                src="{{ asset('uploads/agenda/'.$agenda->gambar) }}"
                                class="img-thumbnail mb-3"
                                style="max-width:220px;">
                        @else
                            <div class="text-muted">
                                Belum ada poster.
                            </div>
                        @endif
                    </div>
                </div>
                <div class="form-group">
                    <label>
                        Ganti Poster (Opsional)
                    </label>
                    <input
                        type="file"
                        name="gambar"
                        class="form-control-file @error('gambar') is-invalid @enderror"
                        accept="image/*">
                    <small class="form-text text-muted">
                        Kosongkan jika poster tidak ingin diganti.
                    </small>
                    @error('gambar')
                        <div class="text-danger mt-2">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>
                            Urutan
                        </label>
                        <input
                            type="number"
                            name="urutan"
                            class="form-control @error('urutan') is-invalid @enderror"
                            value="{{ old('urutan', $agenda->urutan) }}"
                            min="1">
                        @error('urutan')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-group col-md-6">
                        <label>
                            Status
                        </label>
                        <select
                            name="aktif"
                            class="form-control @error('aktif') is-invalid @enderror">
                            <option value="1"
                                {{ old('aktif', $agenda->aktif) == 1 ? 'selected' : '' }}>
                                Aktif
                            </option>
                            <option value="0"
                                {{ old('aktif', $agenda->aktif) == 0 ? 'selected' : '' }}>
                                Nonaktif
                            </option>
                        </select>
                        @error('aktif')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>    
                <hr>
                <div class="form-group text-right">
                    <a
                        href="{{ route('agenda_kajian.index') }}"
                        class="btn btn-secondary">
                        <i class="fas fa-times"></i>
                        Batal
                    </a>
                    <button
                        type="submit"
                        class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Update Agenda
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
