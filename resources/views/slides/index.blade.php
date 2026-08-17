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
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    <div class="mb-3">
        <a href="{{ route('slides.create') }}"
           class="btn btn-primary">
            <i class="fas fa-plus"></i>
            Tambah Slide Informasi
        </a>
    </div>
    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="thead-light">
                        <tr>
                            <th width="60" class="text-center">
                                No
                            </th>
                            <th width="150" class="text-center">
                                Gambar
                            </th>
                            <th>
                                Informasi Slide
                            </th>
                            <th width="90" class="text-center">
                                Urutan
                            </th>
                            <th width="90" class="text-center">
                                Durasi
                            </th>
                            <th width="100" class="text-center">
                                Status
                            </th>
                            <th width="180" class="text-center">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($slides as $slide)
                        <tr>
                            <td class="text-center">
                                {{ $loop->iteration }}
                            </td>
                            <td class="text-center">
                                @if($slide->gambar)
                                    <img
                                        src="{{ asset('storage/'.$slide->gambar) }}"
                                        class="img-thumbnail"
                                        style="max-width:120px; max-height:90px;"
                                        alt="{{ $slide->judul }}">
                                @else
                                    <span class="text-muted">
                                        Tidak ada gambar
                                    </span>
                                @endif
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
                            <td class="text-center">
                                {{ $slide->urutan }}
                            </td>
                            <td class="text-center">
                                {{ $slide->durasi }} detik
                            </td>              
                            <td class="text-center">
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
                            <td class="text-center">
                                <a href="{{ route('slides.edit', $slide->id) }}"
                                   class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                    Edit
                                </a>
                                <form action="{{ route('slides.destroy', $slide->id) }}"
                                      method="POST"
                                      class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        type="submit"
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirm('Yakin ingin menghapus Slide Informasi ini?')">
                                        <i class="fas fa-trash"></i>
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                Belum ada data Slide Informasi.
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
