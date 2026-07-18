@extends('layouts.admin')
@section('main-content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-calendar-alt"></i> Agenda Kajian
        </h1>
        <a href="{{ route('agenda_kajian.create') }}"
           class="btn btn-primary shadow-sm">
            <i class="fas fa-plus-circle"></i>
            Tambah Agenda
        </a>
    </div>
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
            <button
                type="button"
                class="close"
                data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Daftar Agenda Kajian
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table
                    class="table table-bordered table-hover"
                    id="dataTable"
                    width="100%"
                    cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th width="50">No</th>
                            <th width="110">Poster</th>
                            <th>Judul Kajian</th>
                            <th width="180">Pemateri</th>
                            <th width="120">Tanggal</th>
                            <th width="90">Waktu</th>
                            <th width="90">Status</th>
                            <th width="120">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($agenda as $item)
                        <tr>
                            <td class="text-center">
                                {{ $loop->iteration }}
                            </td>
                            <td class="text-center">
                                @if($item->gambar)
                                    <img
                                        src="{{ asset('uploads/agenda/'.$item->gambar) }}"
                                        class="img-thumbnail"
                                        style="width:90px;height:90px;object-fit:cover;">
                                @else
                                    <span class="text-muted">
                                        Tidak ada gambar
                                    </span>
                                @endif
                            </td>
                            <td>
                                <strong>
                                    {{ $item->judul }}
                                </strong>
                                @if($item->lokasi)
                                    <br>
                                    <small class="text-muted">
                                        <i class="fas fa-map-marker-alt"></i>
                                        {{ $item->lokasi }}
                                    </small>
                                @endif
                            </td>
                            <td>
                                {{ $item->pemateri }}
                            </td>
                            <td class="text-center">
                                {{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}
                            </td>
                            <td class="text-center">
                                {{ \Carbon\Carbon::parse($item->waktu)->format('H:i') }}
                            </td>
                            <td class="text-center">
                                @if($item->aktif)
                                    <span class="badge badge-success">
                                        Aktif
                                    </span>
                                @else
                                    <span class="badge badge-danger">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a
                                href="{{ route('agenda_kajian.edit', $item->id) }}"
                                    class="btn btn-warning btn-sm"
                                    title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form
                                    action="{{ route('agenda_kajian.destroy', $item->id) }}"
                                    method="POST"
                                    class="d-inline"
                                    onsubmit="return confirm('Yakin ingin menghapus agenda kajian ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        type="submit"
                                        class="btn btn-danger btn-sm"
                                        title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">
                                Belum ada data agenda kajian.
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
@push('scripts')
<script>
$(document).ready(function () {
    if ($.fn.DataTable) {
        $('#dataTable').DataTable({
            responsive: true,
            autoWidth: false,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.8/i18n/id.json'
            }
        });
    }
});
</script>
@endpush
