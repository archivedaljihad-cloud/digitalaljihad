@extends('layouts.admin')

@section('main-content')

<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">
        Slide Informasi
    </h1>

    @if(session('success'))

        <div class="alert alert-success">
            {{ session('success') }}
        </div>

    @endif

    <a href="{{ route('slides.create') }}"
       class="btn btn-primary mb-3">

        <i class="fas fa-plus"></i>

        Tambah Slide

    </a>

    <div class="card shadow">

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered table-hover">

                    <thead>

                        <tr>

                            <th width="60">
                                No
                            </th>

                            <th width="140">
                                Gambar
                            </th>

                            <th>
                                Judul
                            </th>

                            <th width="100">
                                Urutan
                            </th>

                            <th width="100">
                                Status
                            </th>

                            <th width="180">
                                Aksi
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($slides as $slide)

                            <tr>

                                <td>
                                    {{ $loop->iteration }}
                                </td>

                                <td>

                                    <img
                                        src="{{ asset('uploads/slides/'.$slide->gambar) }}"
                                        width="120"
                                        class="img-thumbnail">

                                </td>

                                <td>

                                    <strong>

                                        {{ $slide->judul }}

                                    </strong>

                                    @if($slide->deskripsi)

                                        <br>

                                        <small class="text-muted">

                                            {{ $slide->deskripsi }}

                                        </small>

                                    @endif

                                </td>

                                <td>

                                    {{ $slide->urutan }}

                                </td>

                                <td>

                                    @if($slide->aktif)

                                        <span class="badge badge-success">

                                            Aktif

                                        </span>

                                    @else

                                        <span class="badge badge-secondary">

                                            Nonaktif

                                        </span>

                                    @endif

                                </td>

                                <td>

                                    <a
                                        href="{{ route('slides.edit',$slide->id) }}"
                                        class="btn btn-warning btn-sm">

                                        Edit

                                    </a>

                                    <form
                                        action="{{ route('slides.destroy',$slide->id) }}"
                                        method="POST"
                                        style="display:inline-block">

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="btn btn-danger btn-sm"
                                            onclick="return confirm('Yakin ingin menghapus slide ini?')">

                                            Hapus

                                        </button>

                                    </form>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="6" class="text-center">

                                    Belum ada data slide.

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

@endsection