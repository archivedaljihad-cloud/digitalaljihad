@extends('layouts.admin')
@section('main-content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">
        Edit Slide Informasi
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
            <form
                action="{{ route('slides.update', $slide->id) }}"
                method="POST"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label>
                        Judul
                    </label>
                    <input
                        type="text"
                        name="judul"
                        class="form-control"
                        value="{{ old('judul', $slide->judul) }}"
                        required>
                </div>
                <div class="form-group">
                    <label>
                        Deskripsi
                    </label>
                    <textarea
                        name="deskripsi"
                        rows="4"
                        class="form-control">{{ old('deskripsi', $slide->deskripsi) }}</textarea>
                </div>
                <div class="form-group">
                    <label>
                        Gambar Saat Ini
                    </label>
                    <div class="mt-2">
                        @if($slide->gambar)
                            <img
                                src="{{ $slide->gambar_url }}"
                                class="img-thumbnail"
                                style="max-width:220px; object-fit:contain; background:#fdfdfd;"
                                onerror="this.onerror=null; this.src='{{ asset('storage/slides/E6Vbbx4rwXUpvmdD8LodQkbK9x82dYzX723Fb386.webp') }}';">
                        @else
                            <span class="text-muted">
                                Tidak ada gambar.
                            </span>
                        @endif
                    </div>
                </div>
                <div class="form-group">
                    <label>
                        Ganti Gambar
                    </label>
                    <input
                        type="file"
                        name="gambar"
                        class="form-control"
                        accept=".jpg,.jpeg,.png,.webp">
                    <small class="text-muted">
                        Format: JPG, JPEG, PNG, WEBP. Maksimal 10 MB. Kosongkan jika gambar tidak ingin diganti.
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
                            value="{{ old('urutan', $slide->urutan) }}"
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
                            value="{{ old('durasi', $slide->durasi) }}"
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
                        {{ old('aktif', $slide->aktif) ? 'checked' : '' }}>
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
                    Update
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
