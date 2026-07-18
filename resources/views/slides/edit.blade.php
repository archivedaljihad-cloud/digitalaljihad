@extends('layouts.admin')

@section('main-content')

<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">
        Edit Slide
    </h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow">

        <div class="card-body">

            <form action="{{ route('slides.update', $slide->id) }}"
                  method="POST"
                  enctype="multipart/form-data">

                @csrf
                @method('PUT')

                <div class="mb-3">

                    <label class="form-label">
                        Judul
                    </label>

                    <input
                        type="text"
                        name="judul"
                        class="form-control"
                        value="{{ old('judul', $slide->judul) }}"
                        required>

                </div>

                <div class="mb-3">

                    <label class="form-label">
                        Deskripsi
                    </label>

                    <textarea
                        name="deskripsi"
                        class="form-control"
                        rows="4">{{ old('deskripsi', $slide->deskripsi) }}</textarea>

                </div>

                <div class="mb-3">

                    <label class="form-label">
                        Gambar Sekarang
                    </label>

                    <br>

                    <img
                        src="{{ asset('uploads/slides/'.$slide->gambar) }}"
                        width="220"
                        class="img-thumbnail">

                </div>

                <div class="mb-3">

                    <label class="form-label">
                        Ganti Gambar
                    </label>

                    <input
                        type="file"
                        name="gambar"
                        class="form-control">

                    <small class="text-muted">
                        Kosongkan jika tidak ingin mengganti gambar.
                    </small>

                </div>

                <div class="mb-3">

                    <label class="form-label">
                        Urutan
                    </label>

                    <input
                        type="number"
                        name="urutan"
                        class="form-control"
                        value="{{ old('urutan', $slide->urutan) }}">

                </div>

                <div class="form-check mb-4">

                    <input
                        class="form-check-input"
                        type="checkbox"
                        name="aktif"
                        value="1"
                        {{ $slide->aktif ? 'checked' : '' }}>

                    <label class="form-check-label">

                        Aktif

                    </label>

                </div>

                <button
                    type="submit"
                    class="btn btn-primary">

                    Update

                </button>

                <a
                    href="{{ route('slides.index') }}"
                    class="btn btn-secondary">

                    Kembali

                </a>

            </form>

        </div>

    </div>

</div>

@endsection