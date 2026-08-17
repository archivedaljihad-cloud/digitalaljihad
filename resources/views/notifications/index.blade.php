@extends('layouts.admin')

@section('main-content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">
            <i class="fas fa-bell text-success mr-2"></i> Daftar Semua Notifikasi
        </h1>
    </div>

    <!-- Content Row -->
    <div class="card shadow mb-4 border-0" style="border-radius: 12px;">
        <div class="card-header py-3 bg-success text-white" style="border-radius: 12px 12px 0 0;">
            <h6 class="m-0 font-weight-bold">
                <i class="fas fa-list mr-1"></i> Riwayat Notifikasi Sistem
            </h6>
        </div>
        <div class="card-body">
            <div class="list-group list-group-flush">
                
                <!-- Contoh Item Notifikasi -->
                <div class="list-group-item list-group-item-action flex-column align-items-start py-3 border-bottom">
                    <div class="d-flex w-100 justify-content-between align-items-center">
                        <h5 class="mb-1 text-success font-weight-bold">
                            <i class="fas fa-clock mr-2 text-warning"></i> Waktu Sholat
                        </h5>
                        <small class="text-muted">Hari ini</small>
                    </div>
                    <p class="mb-1 text-dark font-weight-medium">Waktu Dzhuhur akan segera masuk.</p>
                    <small class="text-muted">Sistem Pengingat Otomatis</small>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection