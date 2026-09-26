<!-- resources/views/home.blade.php -->
@extends('layouts.admin')

@section('main-content')
<!-- Page Heading -->
@php
    $hour = (int) date('H');
    if ($hour >= 3 && $hour < 11) {
        $salamWaktu = 'Selamat Pagi';
    } elseif ($hour >= 11 && $hour < 15) {
        $salamWaktu = 'Selamat Siang';
    } elseif ($hour >= 15 && $hour < 18) {
        $salamWaktu = 'Selamat Sore';
    } else {
        $salamWaktu = 'Selamat Malam';
    }

    $rawRole = strtolower(trim(optional(auth()->user()->role)->name ?? ''));
    $uEmail = strtolower(auth()->user()->email ?? '');
    $rawName = auth()->user()->name ?? 'Pengurus';
    $cleanName = trim(preg_replace('/\s*\([^)]*\)\s*$/', '', $rawName));

    // Prioritas 1: Bendahara
    if ($rawRole === 'bendahara' || str_contains($uEmail, 'bendahara') || str_contains(strtolower($rawName), 'bendahara') || str_contains(strtolower($rawName), 'utut')) {
        $currentRole = 'bendahara';
        if (str_contains(strtolower($cleanName), 'utut') || ! $cleanName) {
            $displayName = "Bpk. H. Utut Priastya";
        } else {
            $displayName = (str_starts_with($cleanName, 'Bpk.') || str_starts_with($cleanName, 'Bapak')) ? $cleanName : 'Bpk. ' . $cleanName;
        }
        $roleLabel = "(Bendahara Masjid Jami' Al Jihad)";
    // Prioritas 2: Admin
    } elseif ($rawRole === 'admin' || $rawRole === 'superadmin' || str_contains($uEmail, 'admin') || str_contains(strtolower($rawName), 'admin') || str_contains(strtolower($rawName), 'sholeh')) {
        $currentRole = 'admin';
        if (strtolower($cleanName) === 'administrator' || ! $cleanName) {
            $displayName = "Bpk. H. M. Sholeh";
        } else {
            $displayName = (str_starts_with($cleanName, 'Bpk.') || str_starts_with($cleanName, 'Bapak')) ? $cleanName : 'Bpk. ' . $cleanName;
        }
        $roleLabel = "(Super Admin Masjid Jami' Al Jihad)";
    // Prioritas 3: Petugas / Operator
    } else {
        $currentRole = 'petugas';
        if (str_contains(strtolower($cleanName), 'ahmad') || strtolower($cleanName) === 'operator' || ! $cleanName) {
            $displayName = "Bpk. Ust. Ahmad";
        } else {
            $displayName = (str_starts_with($cleanName, 'Bpk.') || str_starts_with($cleanName, 'Bapak') || str_starts_with($cleanName, 'Ust.')) ? $cleanName : 'Bpk. ' . $cleanName;
        }
        $roleLabel = "(Pengurus / Operator Masjid Jami' Al Jihad)";
    }
@endphp
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-tachometer-alt"></i> {{ __('Dashboard') }}
        </h1>
        <p class="text-muted mt-1 mb-0">{{ $salamWaktu }}, Selamat Datang, <strong class="text-success">{{ $displayName }} {{ $roleLabel }}</strong>!</p>
    </div>
    <div class="dropdown">
        <button class="btn btn-primary dropdown-toggle" type="button" id="quickActionsDropdown" data-toggle="dropdown">
            <i class="fas fa-bolt"></i> Aksi Cepat
        </button>
        <div class="dropdown-menu dropdown-menu-right">
            @if(in_array($currentRole, ['admin', 'petugas']))
            <a class="dropdown-item" href="{{ route('jadwal_sholat.create') }}">
                <i class="fas fa-plus-circle text-success"></i> Tambah Jadwal Sholat
            </a>
            <a class="dropdown-item" href="{{ route('sholat_jumat.create') }}">
                <i class="fas fa-calendar-plus text-warning"></i> Tambah Sholat Jumat & Foto
            </a>
            <a class="dropdown-item" href="{{ route('pengumuman.create') }}">
                <i class="fas fa-bullhorn text-info"></i> Buat Pengumuman
            </a>
            @endif

            @if(in_array($currentRole, ['admin', 'bendahara']))
            <a class="dropdown-item" href="{{ route('keuangan.create') }}">
                <i class="fas fa-money-bill text-warning"></i> Input Transaksi Kas
            </a>
            <a class="dropdown-item" href="{{ route('ambulance.create') }}">
                <i class="fas fa-ambulance text-danger"></i> Input Kas Ambulance
            </a>
            @endif

            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="{{ route('rotator') }}" target="_blank">
                <i class="fas fa-tv text-primary"></i> Buka Tampilan TV
            </a>
            <a class="dropdown-item" href="{{ route('ambulance.embed') }}" target="_blank">
                <i class="fas fa-ambulance text-success"></i> Monitor TV Kas Ambulance
            </a>
        </div>
    </div>
</div>

<!-- Notification Alerts -->
@if (session('success'))
<div class="alert alert-success border-left-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

@if (session('status'))
<div class="alert alert-success border-left-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle mr-2"></i> {{ session('status') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

@if (session('welcome'))
<div class="alert alert-info border-left-info alert-dismissible fade show" role="alert">
    <i class="fas fa-info-circle mr-2"></i> {{ session('welcome') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

@if (session('error'))
<div class="alert alert-danger border-left-danger alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

<!-- Welcome Card -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card bg-gradient-primary text-white shadow">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="mb-2">{{ $salamWaktu }}, Selamat Datang di Sistem Informasi Masjid</h4>
                        <p class="mb-0">Selamat bertugas, <strong>{{ $displayName }} {{ $roleLabel }}</strong>. Kelola jadwal sholat, pengumuman, keuangan, dan tampilan TV digital dengan mudah.</p>
                        <small class="opacity-75">Terakhir login: {{ auth()->user()->updated_at->diffForHumans() ?? 'Baru saja' }}</small>
                    </div>
                    <div class="col-md-4 text-center">
                        <i class="fas fa-mosque fa-4x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if ($currentRole === 'admin')
<!-- Admin Dashboard Cards -->
<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <a href="{{ route('users.index') }}" class="card-link">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                <i class="fas fa-users"></i> Total Pengguna
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $widget['users'] ?? 0 }}</div>
                            <div class="mt-2 text-xs text-muted">
                                <i class="fas fa-user-check"></i> Aktif: {{ \App\Models\User::where('created_at', '>=', now()->subDays(30))->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <a href="{{ route('keuangan.index') }}" class="card-link">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                <i class="fas fa-wallet"></i> Saldo Kas
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                @php
                                $totalPemasukan = \App\Models\Keuangan::sum('pemasukan');
                                $totalPengeluaran = \App\Models\Keuangan::sum('pengeluaran');
                                $saldo = $totalPemasukan - $totalPengeluaran;
                                $saldoClass = $saldo >= 0 ? 'text-success' : 'text-danger';
                                @endphp
                                <span class="{{ $saldoClass }}">Rp {{ number_format($saldo, 0, ',', '.') }}</span>
                            </div>
                            <div class="mt-2 text-xs text-muted">
                                <i class="fas fa-arrow-up text-success"></i> Masuk: Rp {{ number_format($totalPemasukan, 0, ',', '.') }} |
                                <i class="fas fa-arrow-down text-danger"></i> Keluar: Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <a href="{{ route('jadwal_sholat.index') }}" class="card-link">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                <i class="fas fa-clock"></i> Jadwal Sholat
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{\App\Models\JadwalSholat::count()}} Jadwal
                            </div>
                            <div class="mt-2 text-xs text-muted">
                                <i class="fas fa-hourglass-half"></i> 
                                @php
                                $nextPrayer = \App\Models\JadwalSholat::where('waktu', '>=', now()->format('H:i:s'))->first();
                                @endphp
                                @if($nextPrayer)
                                Selanjutnya: {{ $nextPrayer->nama_sholat }} ({{ \Carbon\Carbon::parse($nextPrayer->waktu)->format('H:i') }})
                                @else
                                Semua jadwal hari ini telah berlalu
                                @endif
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-mosque fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <a href="{{ route('sholat_jumat.index') }}" class="card-link">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                <i class="fas fa-praying-hands"></i> Sholat Jumat
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{\App\Models\SholatJumat::where('tanggal', '>=', now())->count()}} Jadwal
                            </div>
                            <div class="mt-2 text-xs text-muted">
                                <i class="fas fa-calendar-week"></i> 
                                @php
                                $nextFriday = \App\Models\SholatJumat::where('tanggal', '>=', now())->orderBy('tanggal')->first();
                                @endphp
                                @if($nextFriday)
                                Mendatang: {{ \Carbon\Carbon::parse($nextFriday->tanggal)->translatedFormat('d M Y') }}
                                @else
                                Belum ada jadwal
                                @endif
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>

<!-- Second Row -->
<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <a href="{{ route('pengumuman.index') }}" class="card-link">
            <div class="card border-left-purple shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-purple text-uppercase mb-1">
                                <i class="fas fa-bullhorn"></i> Pengumuman
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{\App\Models\Pengumuman::where('tanggal', '>=', now())->count()}} Aktif
                            </div>
                            <div class="mt-2 text-xs text-muted">
                                <i class="fas fa-total"></i> Total: {{\App\Models\Pengumuman::count()}} Pengumuman
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-bullhorn fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <a href="{{ route('settings.edit') }}" class="card-link">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                <i class="fas fa-cogs"></i> Pengaturan
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Aplikasi</div>
                            <div class="mt-2 text-xs text-muted">
                                <i class="fas fa-sliders-h"></i> Konfigurasi sistem
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-cog fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <a href="{{ route('auto_update.index') }}" class="card-link">
            <div class="card border-left-dark shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">
                                <i class="fas fa-sync-alt"></i> Auto-Update
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                @if($setting->auto_update_jadwal ?? false)
                                <span class="badge badge-success">AKTIF</span>
                                @else
                                <span class="badge badge-secondary">NONAKTIF</span>
                                @endif
                            </div>
                            <div class="mt-2 text-xs text-muted">
                                <i class="fas fa-city"></i> Lokasi: {{ $setting->auto_update_city ?? 'Jakarta' }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-cloud-sun fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <a href="{{ route('rotator') }}" target="_blank" class="card-link">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                <i class="fas fa-tv"></i> Tampilan TV
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Display Mode</div>
                            <div class="mt-2 text-xs text-muted">
                                <i class="fas fa-exchange-alt"></i> Rotasi: {{ $setting->rotation_interval ?? 10 }} detik
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-desktop fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>

<!-- Recent Activities & Charts -->
<div class="row">
    <!-- Recent Transactions -->
    <div class="col-lg-7 mb-4">
        <div class="card shadow">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-history"></i> Transaksi Terbaru
                </h6>
                <a href="{{ route('keuangan.index') }}" class="btn btn-sm btn-primary">Lihat Semua</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead style="background: linear-gradient(135deg, #1e5a3a 0%, #0a2e1f 100%);">
                            <tr>
                                <th style="color: #ffffff !important; font-weight: 700; border: none; font-size: 0.9rem;">Tanggal</th>
                                <th style="color: #ffffff !important; font-weight: 700; border: none; font-size: 0.9rem;">Deskripsi</th>
                                <th class="text-right" style="color: #ffffff !important; font-weight: 700; border: none; font-size: 0.9rem;">Pemasukan</th>
                                <th class="text-right" style="color: #ffffff !important; font-weight: 700; border: none; font-size: 0.9rem;">Pengeluaran</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $recentTransactions = \App\Models\Keuangan::orderBy('tanggal', 'desc')->take(5)->get();
                            @endphp
                            @forelse($recentTransactions as $transaction)
                            <tr>
                                <td class="align-middle text-nowrap">
                                    <span class="font-weight-bold text-dark" style="color: #0f172a !important; font-size: 0.92rem;">
                                        <i class="far fa-calendar-alt text-success mr-1"></i>
                                        {{ \Carbon\Carbon::parse($transaction->tanggal)->translatedFormat('d M Y') }}
                                    </span>
                                </td>
                                <td class="align-middle">
                                    <span class="font-weight-bold text-dark" style="color: #0f172a !important; font-size: 0.95rem;">
                                        {{ \Illuminate\Support\Str::limit($transaction->deskripsi, 40) }}
                                    </span>
                                </td>
                                <td class="text-right font-weight-bold text-success align-middle" style="font-size: 1rem; color: #047857 !important;">
                                    @if($transaction->pemasukan > 0)
                                    Rp {{ number_format($transaction->pemasukan, 0, ',', '.') }}
                                    @else
                                    -
                                    @endif
                                </td>
                                <td class="text-right font-weight-bold text-danger align-middle" style="font-size: 1rem; color: #b91c1c !important;">
                                    @if($transaction->pengeluaran > 0)
                                    Rp {{ number_format($transaction->pengeluaran, 0, ',', '.') }}
                                    @else
                                    -
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">Belum ada transaksi</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Upcoming Announcements -->
    <div class="col-lg-5 mb-4">
        <div class="card shadow">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-calendar-alt"></i> Pengumuman Mendatang
                </h6>
                <a href="{{ route('pengumuman.create') }}" class="btn btn-sm btn-success">
                    <i class="fas fa-plus"></i> Tambah
                </a>
            </div>
            <div class="card-body">
                @php
                $upcomingAnnouncements = \App\Models\Pengumuman::where('tanggal', '>=', now())->orderBy('tanggal')->take(5)->get();
                @endphp
                @forelse($upcomingAnnouncements as $announcement)
                <div class="activity-feed mb-3">
                    <div class="feed-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <i class="fas fa-bullhorn text-warning mr-2"></i>
                                <strong>{{ \Carbon\Carbon::parse($announcement->tanggal)->translatedFormat('d M Y') }}</strong>
                            </div>
                            <small class="text-muted">{{ \Carbon\Carbon::parse($announcement->tanggal)->diffForHumans() }}</small>
                        </div>
                        <p class="mb-0 mt-1">{{ \Illuminate\Support\Str::limit($announcement->isi, 60) }}</p>
                    </div>
                </div>
                @empty
                <div class="text-center py-4">
                    <i class="fas fa-inbox fa-3x text-gray-300 mb-2"></i>
                    <p class="text-muted">Tidak ada pengumuman mendatang</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@elseif (in_array($currentRole, ['petugas', 'operator']))
<!-- Petugas Dashboard -->
<div class="row">
    <!-- Jadwal Sholat -->
    <div class="col-xl-3 col-md-6 mb-4">
        <a href="{{ route('jadwal_sholat.index') }}" class="card-link">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                <i class="fas fa-clock"></i> Jadwal Sholat
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{\App\Models\JadwalSholat::count()}} Jadwal
                            </div>
                            <div class="mt-2 text-xs text-muted">Kelola jadwal harian</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-mosque fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Sholat Jumat -->
    <div class="col-xl-3 col-md-6 mb-4">
        <a href="{{ route('sholat_jumat.index') }}" class="card-link">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                <i class="fas fa-calendar-alt"></i> Sholat Jumat
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{\App\Models\SholatJumat::where('tanggal', '>=', now())->count()}} Mendatang
                            </div>
                            <div class="mt-2 text-xs text-muted">Petugas & tema khutbah</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-praying-hands fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Sholat Idul Fitri -->
    <div class="col-xl-3 col-md-6 mb-4">
        <a href="{{ route('idul-fitri.index') }}" class="card-link">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                <i class="fas fa-moon"></i> Idul Fitri
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{\App\Models\SholatIdulFitri::count()}} Data
                            </div>
                            <div class="mt-2 text-xs text-muted">Petugas & zakat fitrah</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-star-and-crescent fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Sholat Idul Adha -->
    <div class="col-xl-3 col-md-6 mb-4">
        <a href="{{ route('idul-adha.index') }}" class="card-link">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                <i class="fas fa-drumstick-bite"></i> Idul Adha
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{\App\Models\SholatIdulAdha::count()}} Data
                            </div>
                            <div class="mt-2 text-xs text-muted">Petugas & info qurban</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-kaaba fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>

<div class="row">
    <!-- Agenda Kajian -->
    <div class="col-xl-3 col-md-6 mb-4">
        <a href="{{ route('agenda_kajian.index') }}" class="card-link">
            <div class="card border-left-purple shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-purple text-uppercase mb-1">
                                <i class="fas fa-book-open"></i> Agenda Kajian
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{\App\Models\AgendaKajian::count()}} Jadwal
                            </div>
                            <div class="mt-2 text-xs text-muted">Jadwal & ustadz kajian</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-quran fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Rotasi Halaman TV -->
    <div class="col-xl-3 col-md-6 mb-4">
        <a href="{{ route('rotation.index') }}" class="card-link">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                <i class="fas fa-exchange-alt"></i> Rotasi Halaman TV
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                @if($setting->rotation_enabled ?? true)
                                <span class="badge badge-success">AKTIF</span>
                                @else
                                <span class="badge badge-secondary">NONAKTIF</span>
                                @endif
                            </div>
                            <div class="mt-2 text-xs text-muted">Atur jeda & urutan slide</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tv fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Pengumuman -->
    <div class="col-xl-3 col-md-6 mb-4">
        <a href="{{ route('pengumuman.index') }}" class="card-link">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                <i class="fas fa-bullhorn"></i> Pengumuman
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{\App\Models\Pengumuman::count()}} Info
                            </div>
                            <div class="mt-2 text-xs text-muted">Kelola warta masjid</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-bullhorn fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Profil Saya -->
    <div class="col-xl-3 col-md-6 mb-4">
        <a href="{{ route('profile') }}" class="card-link">
            <div class="card border-left-dark shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">
                                <i class="fas fa-user"></i> Profil Saya
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ auth()->user()->name }}</div>
                            <div class="mt-2 text-xs text-muted">{{ auth()->user()->email }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>

<!-- Jadwal Sholat Hari Ini -->
<div class="row">
    <div class="col-lg-12 mb-4">
        <div class="card shadow">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-clock"></i> Jadwal Sholat Hari Ini
                </h6>
                <div class="text-muted small">{{ \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('l, d F Y') }}</div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead style="background: linear-gradient(135deg, #1e5a3a 0%, #0a2e1f 100%);">
                            <tr>
                                <th style="color: #ffffff !important; font-weight: 700; border: none; font-size: 0.9rem;">Sholat</th>
                                <th style="color: #ffffff !important; font-weight: 700; border: none; font-size: 0.9rem;">Waktu</th>
                                <th style="color: #ffffff !important; font-weight: 700; border: none; font-size: 0.9rem;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $now = \Carbon\Carbon::now('Asia/Jakarta');
                            $schedules = \App\Models\JadwalSholat::all();
                            @endphp
                            @foreach($schedules as $schedule)
                            @php
                            $waktuSholat = \Carbon\Carbon::parse($schedule->waktu);
                            $isPassed = $waktuSholat->lt($now);
                            $isNow = $waktuSholat->diffInMinutes($now) <= 30 && !$isPassed;
                            @endphp
                            <tr>
                                <td><strong>{{ $schedule->nama_sholat }}</strong></td>
                                <td>{{ $schedule->waktu }} WIB</td>
                                <td>
                                    @if($isNow)
                                    <span class="badge badge-success">
                                        <i class="fas fa-bell"></i> Waktunya sholat!
                                    </span>
                                    @elseif($isPassed)
                                    <span class="badge badge-secondary">
                                        <i class="fas fa-check-circle"></i> Telah berlalu
                                    </span>
                                    @else
                                    <span class="badge badge-info">
                                        <i class="fas fa-hourglass-half"></i> Dalam {{ $waktuSholat->diffForHumans() }}
                                    </span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@elseif ($currentRole === 'bendahara')
<!-- Bendahara Dashboard -->
<div class="row">
    <!-- Saldo Kas Masjid -->
    <div class="col-xl-4 col-md-6 mb-4">
        <a href="{{ route('keuangan.index') }}" class="card-link">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                <i class="fas fa-wallet"></i> Saldo Kas Saat Ini
                            </div>
                            @php
                            $totalPemasukan = \App\Models\Keuangan::sum('pemasukan');
                            $totalPengeluaran = \App\Models\Keuangan::sum('pengeluaran');
                            $saldo = $totalPemasukan - $totalPengeluaran;
                            @endphp
                            <div class="h4 mb-0 font-weight-bold text-gray-800">
                                Rp {{ number_format($saldo, 0, ',', '.') }}
                            </div>
                            <div class="mt-2 text-xs text-muted">Total saldo bersih kas masjid</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-coins fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Total Pemasukan -->
    <div class="col-xl-4 col-md-6 mb-4">
        <a href="{{ route('keuangan.index') }}" class="card-link">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                <i class="fas fa-arrow-down"></i> Total Pemasukan Kas
                            </div>
                            <div class="h4 mb-0 font-weight-bold text-primary">
                                Rp {{ number_format($totalPemasukan, 0, ',', '.') }}
                            </div>
                            <div class="mt-2 text-xs text-muted">Infaq, sedekah, dan donasi</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hand-holding-usd fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Total Pengeluaran -->
    <div class="col-xl-4 col-md-6 mb-4">
        <a href="{{ route('keuangan.index') }}" class="card-link">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                <i class="fas fa-arrow-up"></i> Total Pengeluaran Kas
                            </div>
                            <div class="h4 mb-0 font-weight-bold text-danger">
                                Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}
                            </div>
                            <div class="mt-2 text-xs text-muted">Operasional & perawatan masjid</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-receipt fa-2x text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>

<!-- Kas Ambulance Cards -->
<div class="row">
    <!-- Saldo Kas Ambulance -->
    <div class="col-xl-4 col-md-6 mb-4">
        <a href="{{ route('ambulance.index') }}" class="card-link">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                <i class="fas fa-ambulance"></i> Saldo Kas Ambulance
                            </div>
                            @php
                            $totalPemasukanAmb = \App\Models\KeuanganAmbulance::sum('pemasukan');
                            $totalPengeluaranAmb = \App\Models\KeuanganAmbulance::sum('pengeluaran');
                            $saldoAmb = $totalPemasukanAmb - $totalPengeluaranAmb;
                            @endphp
                            <div class="h4 mb-0 font-weight-bold text-gray-800">
                                Rp {{ number_format($saldoAmb, 0, ',', '.') }}
                            </div>
                            <div class="mt-2 text-xs text-muted">Total saldo kas operasional ambulance</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-ambulance fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Total Pemasukan Kas Ambulance -->
    <div class="col-xl-4 col-md-6 mb-4">
        <a href="{{ route('ambulance.index') }}" class="card-link">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                <i class="fas fa-arrow-down"></i> Pemasukan Ambulance
                            </div>
                            <div class="h4 mb-0 font-weight-bold text-primary">
                                Rp {{ number_format($totalPemasukanAmb, 0, ',', '.') }}
                            </div>
                            <div class="mt-2 text-xs text-muted">Infak & sumbangan ambulance</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hand-holding-usd fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Total Pengeluaran Kas Ambulance -->
    <div class="col-xl-4 col-md-6 mb-4">
        <a href="{{ route('ambulance.index') }}" class="card-link">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                <i class="fas fa-arrow-up"></i> Pengeluaran Ambulance
                            </div>
                            <div class="h4 mb-0 font-weight-bold text-danger">
                                Rp {{ number_format($totalPengeluaranAmb, 0, ',', '.') }}
                            </div>
                            <div class="mt-2 text-xs text-muted">BBM, service & driver ambulance</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-gas-pump fa-2x text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>

<!-- Action Cards & Quick Links untuk Bendahara -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <a href="{{ route('keuangan.create') }}" class="btn btn-success btn-block btn-lg shadow-sm py-3 text-left">
            <div class="d-flex align-items-center">
                <i class="fas fa-plus-circle fa-2x mr-3"></i>
                <div>
                    <strong class="d-block">Input Kas Masjid</strong>
                    <small>Catat transaksi kas umum</small>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-3 mb-3">
        <a href="{{ route('ambulance.create') }}" class="btn btn-danger btn-block btn-lg shadow-sm py-3 text-left">
            <div class="d-flex align-items-center">
                <i class="fas fa-ambulance fa-2x mr-3"></i>
                <div>
                    <strong class="d-block">Input Kas Ambulance</strong>
                    <small>Catat kas ambulance</small>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-3 mb-3">
        <a href="{{ route('laporan.ambulance') }}" class="btn btn-primary btn-block btn-lg shadow-sm py-3 text-left">
            <div class="d-flex align-items-center">
                <i class="fas fa-chart-line fa-2x mr-3"></i>
                <div>
                    <strong class="d-block">Laporan Ambulance</strong>
                    <small>Rekap kas ambulance</small>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-3 mb-3">
        <a href="{{ route('ambulance.index') }}" class="btn btn-warning btn-block btn-lg shadow-sm py-3 text-left text-dark">
            <div class="d-flex align-items-center">
                <i class="fas fa-book-open fa-2x mr-3"></i>
                <div>
                    <strong class="d-block">Buku Kas Ambulance</strong>
                    <small>Kelola seluruh transaksi</small>
                </div>
            </div>
        </a>
    </div>
</div>

<!-- Transaksi Kas Terbaru -->
<div class="row">
    <div class="col-lg-12 mb-4">
        <div class="card shadow">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-history"></i> Transaksi Kas Terbaru
                </h6>
                <a href="{{ route('keuangan.index') }}" class="btn btn-sm btn-outline-primary">
                    Lihat Semua Transaksi <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead style="background: linear-gradient(135deg, #1e5a3a 0%, #0a2e1f 100%);">
                            <tr>
                                <th style="color: #ffffff !important; font-weight: 700; border: none; font-size: 0.9rem;">Tanggal</th>
                                <th style="color: #ffffff !important; font-weight: 700; border: none; font-size: 0.9rem;">Deskripsi</th>
                                <th style="color: #ffffff !important; font-weight: 700; border: none; font-size: 0.9rem;">Kategori</th>
                                <th class="text-right" style="color: #ffffff !important; font-weight: 700; border: none; font-size: 0.9rem;">Pemasukan</th>
                                <th class="text-right" style="color: #ffffff !important; font-weight: 700; border: none; font-size: 0.9rem;">Pengeluaran</th>
                                <th class="text-right" style="color: #ffffff !important; font-weight: 700; border: none; font-size: 0.9rem;">Saldo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $recentKas = \App\Models\Keuangan::orderBy('tanggal', 'desc')->orderBy('id', 'desc')->take(8)->get();
                            @endphp
                            @forelse($recentKas as $item)
                            <tr>
                                <td class="align-middle text-nowrap">
                                    <span class="font-weight-bold text-dark" style="color: #0f172a !important; font-size: 0.92rem;">
                                        <i class="far fa-calendar-alt text-success mr-1"></i>
                                        {{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d M Y') }}
                                    </span>
                                </td>
                                <td class="align-middle">
                                    <span class="font-weight-bold text-dark" style="color: #0f172a !important; font-size: 0.95rem;">
                                        {{ $item->deskripsi }}
                                    </span>
                                </td>
                                <td class="align-middle">
                                    <span class="badge badge-light border border-secondary text-dark font-weight-bold px-2 py-1" style="font-size: 0.85rem; color: #1e293b !important;">
                                        <i class="fas fa-tag text-primary mr-1"></i> {{ $item->kategori ?? '-' }}
                                    </span>
                                </td>
                                <td class="text-right text-success font-weight-bold align-middle" style="font-size: 1rem; color: #047857 !important;">
                                    {{ $item->pemasukan > 0 ? 'Rp ' . number_format($item->pemasukan, 0, ',', '.') : '-' }}
                                </td>
                                <td class="text-right text-danger font-weight-bold align-middle" style="font-size: 1rem; color: #b91c1c !important;">
                                    {{ $item->pengeluaran > 0 ? 'Rp ' . number_format($item->pengeluaran, 0, ',', '.') : '-' }}
                                </td>
                                <td class="text-right text-dark font-weight-bold align-middle" style="font-size: 1rem; color: #0f172a !important;">
                                    Rp {{ number_format($item->saldo, 0, ',', '.') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">Belum ada transaksi tercatat.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif


<!-- Custom CSS -->
<style>
    .card-link {
        text-decoration: none;
        transition: transform 0.3s ease;
        display: block;
    }
    
    .card-link:hover {
        transform: translateY(-5px);
    }
    
    .card {
        border-radius: 12px;
        transition: all 0.3s ease;
        overflow: hidden;
    }
    
    .card:hover {
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }
    
    .border-left-primary { border-left: 4px solid #4e73df; }
    .border-left-success { border-left: 4px solid #1cc88a; }
    .border-left-info { border-left: 4px solid #36b9cc; }
    .border-left-warning { border-left: 4px solid #f6c23e; }
    .border-left-purple { border-left: 4px solid #6f42c1; }
    .border-left-secondary { border-left: 4px solid #858796; }
    .border-left-dark { border-left: 4px solid #5a5c69; }
    .border-left-danger { border-left: 4px solid #e74a3b; }
    
    .bg-gradient-primary {
        background: linear-gradient(135deg, #0a4d68 0%, #088395 100%);
    }
    
    .activity-feed .feed-item {
        padding: 12px;
        border-left: 3px solid #f6c23e;
        background-color: #f8f9fc;
        margin-bottom: 8px;
        border-radius: 0 8px 8px 0;
        transition: all 0.2s ease;
    }
    
    .activity-feed .feed-item:hover {
        background-color: #fff3cd;
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(78, 115, 223, 0.05);
    }
    
    .opacity-75 {
        opacity: 0.75;
    }
    
    .text-purple {
        color: #6f42c1 !important;
    }
</style>
@endsection