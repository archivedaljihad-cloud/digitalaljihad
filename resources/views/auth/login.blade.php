@extends('layouts.auth')

@section('main-content')
<div class="container">
    <!-- Outer Row -->
    <div class="row justify-content-center">
        <div class="col-xl-10 col-lg-12 col-md-9">
            <div class="card o-hidden border-0 shadow-lg my-5" style="backdrop-filter: blur(10px);">
                <div class="card-body p-0">
                    <!-- Nested Row within Card Body -->
                    <div class="row">

                        <!-- Left Side: Islamic Design Column -->
                        <div class="col-lg-6 d-none d-lg-block position-relative bg-login-image" style="background: linear-gradient(135deg, #0a2e1f 0%, #1e5a3a 100%); position: relative; overflow: hidden;">
                            <!-- Islamic Corner / Pattern Overlay -->
                            <div class="position-absolute w-100 h-100 d-flex flex-column justify-content-between align-items-center text-white p-5 text-center" style="z-index: 2;">
                                <div class="my-auto">
                                    <i class="fas fa-mosque fa-4x mb-3 text-warning"></i>
                                    <h2 class="font-weight-bold" style="font-family: 'Amiri', serif;">وَمَا تَوْفِيقِي إِلَّا بِاللَّهِ</h2>
                                    <p class="text-white-50 mt-2">"Dan tidak ada taufikku melainkan dengan pertolongan Allah"</p>
                                </div>
                                <div>
                                    <div class="mb-2">
                                        <i class="fas fa-star-and-crescent text-warning mx-1"></i>
                                        <i class="fas fa-quran text-warning mx-1"></i>
                                        <i class="fas fa-star-and-crescent text-warning mx-1"></i>
                                    </div>
                                    <p class="small mb-0 text-white-50">Sistem Informasi Masjid Digital<br>Modern | Terintegrasi | Berkah</p>
                                    <span class="badge badge-warning text-dark mt-2 font-weight-bold px-3 py-1" style="border-radius: 20px;">Version 3.0.4</span>
                                </div>
                            </div>
                        </div>

                        <!-- Right Side: Login Form Column -->
                        <div class="col-lg-6 p-5">
                            <div class="text-center mb-4">
                                @if(isset($setting['logo']) && !empty($setting['logo']))
                                @php
                                // Menyesuaikan apakah path di database sudah ada awalan 'storage/' atau belum
                                $logoPath = $setting['logo'];
                                $imageUrl = Str::startsWith($logoPath, 'storage/') ? asset($logoPath) : asset('storage/' . $logoPath);
                                @endphp
                                <img src="{{ $imageUrl }}" alt="Logo Masjid" class="img-fluid" style="max-height: 70px; width: auto;" onerror="this.onerror=null; this.src='{{ asset('img/logo.png') }}';">
                                @else
                                <img src="{{ asset('img/logo.png') }}" alt="Logo Default" class="img-fluid" style="max-height: 70px; width: auto;">
                                @endif

                                <h3 class="h4 text-gray-900 font-weight-bold mt-3" style="font-family: 'Amiri', serif;">
                                    {{ $setting['nama_aplikasi'] ?? 'MASJID JAMI AL-JIHAD' }}
                                </h3>
                                <p class="text-muted small">Masukkan kredensial untuk mengakses dashboard</p>
                            </div>

                            @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            @endif

                            @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <ul class="mb-0 pl-3">
                                    @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            @endif

                            <form class="user" method="POST" action="{{ route('login') }}">
                                @csrf

                                <div class="form-group">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light border-0 text-success"><i class="fas fa-envelope"></i></span>
                                        </div>
                                        <input type="email" class="form-control form-control-user bg-light border-0 @error('email') is-invalid @enderror"
                                            name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="nama@email.com">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light border-0 text-success"><i class="fas fa-lock"></i></span>
                                        </div>
                                        <input type="password" class="form-control form-control-user bg-light border-0 @error('password') is-invalid @enderror"
                                            name="password" required autocomplete="current-password" placeholder="Kata Sandi">
                                    </div>
                                </div>

                                <div class="form-group d-flex justify-content-between align-items-center">
                                    <div class="custom-control custom-checkbox small">
                                        <input type="checkbox" class="custom-control-input" id="customCheck" name="remember" {{ old('remember') ? 'checked' : '' }}>
                                        <label class="custom-control-label text-muted" for="customCheck">Ingat Saya</label>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary btn-user btn-block py-2 font-weight-bold shadow-sm" style="background: linear-gradient(135deg, #1e5a3a, #0a2e1f); border: none;">
                                    <i class="fas fa-sign-in-alt mr-2"></i> Masuk ke Dashboard
                                </button>
                            </form>

                            <div class="text-center mt-4">
                                <p class="text-muted small mb-2">Atau</p>
                                @if (Route::has('password.request'))
                                <a class="small text-success font-weight-bold d-block mb-1" href="{{ route('password.request') }}">
                                    <i class="fas fa-key mr-1"></i> Lupa Kata Sandi?
                                </a>
                                @endif
                                <a class="small text-success font-weight-bold d-block" href="{{ url('/') }}">
                                    <i class="fas fa-home mr-1"></i> Kembali ke Beranda
                                </a>
                            </div>

                            <hr class="my-4">

                            <div class="text-center">
                                <small class="text-muted">&copy; {{ now()->year }} brought to you by {{ $setting['nama_aplikasi'] ?? 'DKM AL JIHAD' }}</small>
                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection