@extends('layouts.admin')

@section('main-content')

<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">Tambah Slide</h1>

    <div class="card">
        <div class="card-body">

            <form
                action="{{ route('slides.store') }}"
                method="POST"
                enctype="multipart/form-data"
                onsubmit="alert('FORM DIKIRIM');"
            >

                @csrf

                <div class="mb-3">
                    <label>Judul</label>
                    <input type="text"
                           name="judul"
                           class="form-control"
                           required>
                </div>

                <div class="mb-3">
                    <label>Deskripsi</label>
                    <textarea
                        name="deskripsi"
                        class="form-control"
                        rows="4"></textarea>
                </div>

                <div class="mb-3">
                    <label>Gambar</label>
                    <input
                        type="file"
                        name="gambar"
                        class="form-control"
                        required>
                </div>

                <div class="mb-3">
                    <label>Urutan</label>
                    <input
                        type="number"
                        name="urutan"
                        class="form-control"
                        value="0">
                </div>

                <div class="form-check mb-3">
                    <input
                        type="checkbox"
                        class="form-check-input"
                        name="aktif"
                        value="1"
                        checked>

                    <label class="form-check-label">
                        Aktif
                    </label>
                </div>

                <button type="submit" class="btn btn-primary">
                    Simpan
                </button>

                <a href="{{ route('slides.index') }}"
                   class="btn btn-secondary">
                    Kembali
                </a>

            </form>

        </div>
    </div>

</div>

@endsection