@extends('layouts.admin')
@section('main-content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">
        Tambah Slide Informasi
    </h1>
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="card shadow">
        <div class="card-body">
            <form action="{{ route('slides.store') }}"
                  method="POST"
                  enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label>
                        Judul
                    </label>
                    <input
                        type="text"
                        name="judul"
                        class="form-control"
                        value="{{ old('judul') }}"
                        required>
                </div>
                <div class="form-group">
                    <label>
                        Deskripsi
                    </label>
                    <textarea
                        name="deskripsi"
                        rows="4"
                        class="form-control">{{ old('deskripsi') }}</textarea>
                </div>
                <div class="form-group">
                    <label>
                        Gambar
                    </label>
                    <input
                        type="file"
                        name="gambar"
                        class="form-control"
                        accept=".jpg,.jpeg,.png,.webp"
                        required>
                    <small class="text-muted">
                        Format: JPG, JPEG, PNG, WEBP. Maksimal 10 MB.
                    </small>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>
                            Urutan
                        </label>
                        <input
                            type="number"
                            name="urutan"
                            class="form-control"
                            value="{{ old('urutan',1) }}"
                            min="1"
                            required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>
                            Durasi (detik)
                        </label>
                        <input
                            type="number"
                            name="durasi"
                            class="form-control"
                            value="{{ old('durasi',10) }}"
                            min="1"
                            max="300"
                            required>
                    </div>
                </div>
                <div class="form-check mb-4">
                    <input
                        type="checkbox"
                        class="form-check-input"
                        id="aktif"
                        name="aktif"
                        value="1"
                        {{ old('aktif', true) ? 'checked' : '' }}>
                    <label
                        class="form-check-label"
                        for="aktif">
                        Aktif
                    </label>
                </div>
                <button
                    type="submit"
                    class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Simpan
                </button>
                <a
                    href="{{ route('slides.index') }}"
                    class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Kembali
                </a>
            </form>
        </div>
    </div>
</div>
@endsection
