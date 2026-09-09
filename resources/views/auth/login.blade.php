@extends('layouts.auth')

@section('main-content')
<style>
    .login-container-wrap {
        width: 100%;
        max-width: 600px;
        margin: 20px auto;
        position: relative;
        z-index: 10;
    }

    .secret-login-card {
        background: linear-gradient(180deg, rgba(6, 42, 25, 0.94) 0%, rgba(3, 26, 15, 0.98) 100%);
        border: 1.5px solid rgba(255, 215, 0, 0.45);
        border-radius: 26px;
        box-shadow: 
            0 25px 60px rgba(0, 0, 0, 0.75),
            0 0 35px rgba(18, 128, 73, 0.28),
            inset 0 1px 0 rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(18px);
        -webkit-backdrop-filter: blur(18px);
        padding: 40px 42px 32px 42px;
        text-align: center;
        position: relative;
        overflow: hidden;
        animation: fadeInUp 0.7s cubic-bezier(0.16, 1, 0.3, 1);
    }

    /* Gold top accent glow line */
    .secret-login-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 10%;
        right: 10%;
        height: 3px;
        background: linear-gradient(90deg, transparent, #ffd700, #fff6b8, #ffd700, transparent);
        box-shadow: 0 0 15px rgba(255, 215, 0, 0.85);
    }

    /* 3. Kalimat Pembuka Basmalah */
    .bismillah-text {
        font-family: 'Amiri', serif;
        font-size: 2.15rem;
        font-weight: 700;
        line-height: 1.35;
        color: #ffd700;
        text-shadow: 
            0 0 15px rgba(255, 215, 0, 0.55),
            0 2px 6px rgba(0, 0, 0, 0.85);
        margin-bottom: 12px;
        letter-spacing: 1px;
        display: inline-block;
    }

    /* 2. Logo Masjid Al-Jihad di Tengah Halaman */
    .logo-center-wrap {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin: 4px auto 16px auto;
    }

    .logo-halo-ring {
        width: 112px;
        height: 112px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(255, 215, 0, 0.2) 0%, rgba(6, 68, 38, 0.4) 60%, transparent 70%);
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid rgba(255, 215, 0, 0.6);
        box-shadow: 
            0 8px 25px rgba(0, 0, 0, 0.55),
            0 0 22px rgba(255, 215, 0, 0.35);
        padding: 8px;
        background-color: rgba(2, 23, 13, 0.88);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .logo-halo-ring:hover {
        transform: scale(1.04);
        box-shadow: 
            0 10px 30px rgba(0, 0, 0, 0.6),
            0 0 30px rgba(255, 215, 0, 0.5);
    }

    .logo-center-img {
        max-height: 88px;
        max-width: 88px;
        object-fit: contain;
        filter: drop-shadow(0 4px 8px rgba(0,0,0,0.45));
    }

    /* 4. Ucapan Selamat Datang */
    .secret-badge {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        background: rgba(255, 215, 0, 0.12);
        border: 1px solid rgba(255, 215, 0, 0.45);
        color: #ffd700;
        font-size: 0.76rem;
        font-weight: 700;
        letter-spacing: 2px;
        text-transform: uppercase;
        padding: 4px 16px;
        border-radius: 30px;
        margin-bottom: 10px;
    }

    .welcome-heading {
        font-family: 'Outfit', sans-serif;
        font-size: 1.45rem;
        font-weight: 700;
        color: #ffffff;
        line-height: 1.35;
        letter-spacing: 0.5px;
        margin-bottom: 2px;
        text-shadow: 0 2px 6px rgba(0, 0, 0, 0.7);
    }

    .sub-heading-masjid {
        font-family: 'Outfit', sans-serif;
        font-size: 1.15rem;
        font-weight: 600;
        color: #ffd700;
        letter-spacing: 1px;
        text-transform: uppercase;
        margin-bottom: 14px;
        text-shadow: 0 0 12px rgba(255, 215, 0, 0.4);
    }

    /* Ayat Arab & Artinya (Tidak Dihilangkan) */
    .verse-banner {
        background: rgba(2, 20, 12, 0.65);
        border-left: 3px solid #ffd700;
        border-right: 3px solid #ffd700;
        border-radius: 12px;
        padding: 10px 18px;
        margin: 10px auto 16px auto;
        max-width: 500px;
    }

    .verse-arabic-text {
        font-family: 'Amiri', serif;
        font-size: 1.38rem;
        font-weight: 700;
        color: #ffffff;
        margin-bottom: 2px;
        text-shadow: 0 1px 3px rgba(0,0,0,0.8);
    }

    .verse-meaning-text {
        font-size: 0.84rem;
        font-style: italic;
        color: rgba(255, 255, 255, 0.85);
        line-height: 1.35;
    }

    /* Petunjuk Kredensial */
    .credential-guide {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        font-size: 0.88rem;
        color: rgba(255, 255, 255, 0.92);
        margin-bottom: 22px;
        background: rgba(255, 255, 255, 0.05);
        padding: 7px 20px;
        border-radius: 20px;
        border: 1px solid rgba(255, 215, 0, 0.2);
    }

    .credential-guide i {
        color: #ffd700;
    }

    /* Input Fields */
    .custom-field-group {
        margin-bottom: 18px;
        text-align: left;
    }

    .custom-field-label {
        display: block;
        font-size: 0.82rem;
        font-weight: 600;
        color: #d1fae5;
        margin-bottom: 6px;
        letter-spacing: 0.3px;
    }

    .custom-field-box {
        position: relative;
        display: flex;
        align-items: center;
    }

    .field-lead-icon {
        position: absolute;
        left: 16px;
        color: #ffd700;
        font-size: 1rem;
        pointer-events: none;
        z-index: 2;
    }

    .custom-auth-input {
        width: 100%;
        height: 50px;
        background: rgba(2, 23, 13, 0.82);
        border: 1.5px solid rgba(255, 215, 0, 0.38);
        border-radius: 12px;
        padding: 0 45px 0 48px;
        font-family: 'Poppins', sans-serif;
        font-size: 0.95rem;
        color: #ffffff;
        transition: all 0.25s ease;
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.4);
    }

    .custom-auth-input:focus {
        outline: none;
        border-color: #ffd700;
        background: rgba(3, 33, 19, 0.96);
        box-shadow: 
            0 0 16px rgba(255, 215, 0, 0.38),
            inset 0 1px 3px rgba(0,0,0,0.3);
    }

    .custom-auth-input::placeholder {
        color: rgba(255, 255, 255, 0.42);
        font-size: 0.9rem;
    }

    .pwd-visibility-toggle {
        position: absolute;
        right: 14px;
        background: transparent;
        border: none;
        color: rgba(255, 255, 255, 0.6);
        font-size: 1rem;
        cursor: pointer;
        padding: 4px;
        transition: color 0.2s;
        z-index: 2;
    }

    .pwd-visibility-toggle:hover {
        color: #ffd700;
    }

    /* Options Row (Remember Me & Forgot Password) */
    .auth-options-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        font-size: 0.86rem;
    }

    .remember-label-click {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: rgba(255, 255, 255, 0.88);
        cursor: pointer;
        user-select: none;
        margin-bottom: 0;
    }

    .remember-custom-tick {
        appearance: none;
        -webkit-appearance: none;
        width: 18px;
        height: 18px;
        border: 1.5px solid rgba(255, 215, 0, 0.65);
        border-radius: 4px;
        background: rgba(2, 23, 13, 0.85);
        cursor: pointer;
        position: relative;
        outline: none;
        transition: all 0.2s;
    }

    .remember-custom-tick:checked {
        background: #ffd700;
        border-color: #ffd700;
    }

    .remember-custom-tick:checked::after {
        content: '✓';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: #032314;
        font-size: 12px;
        font-weight: 900;
    }

    .forgot-pwd-link {
        color: #ffd700;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.2s;
    }

    .forgot-pwd-link:hover {
        color: #ffffff;
        text-decoration: underline;
        text-shadow: 0 0 8px rgba(255, 215, 0, 0.6);
    }

    /* Submit Button */
    .btn-submit-dashboard {
        width: 100%;
        height: 52px;
        background: linear-gradient(135deg, #128049 0%, #08552f 50%, #04361e 100%);
        border: 1.5px solid rgba(255, 215, 0, 0.75);
        border-radius: 12px;
        font-family: 'Outfit', sans-serif;
        font-size: 1.05rem;
        font-weight: 700;
        letter-spacing: 0.8px;
        color: #ffffff;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        box-shadow: 
            0 8px 25px rgba(0, 0, 0, 0.5),
            0 0 20px rgba(18, 128, 73, 0.4);
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        position: relative;
        overflow: hidden;
    }

    .btn-submit-dashboard::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: 0.5s;
    }

    .btn-submit-dashboard:hover {
        transform: translateY(-2px);
        border-color: #ffd700;
        box-shadow: 
            0 12px 30px rgba(0, 0, 0, 0.6),
            0 0 25px rgba(255, 215, 0, 0.5);
        background: linear-gradient(135deg, #159556 0%, #0a6639 50%, #064426 100%);
    }

    .btn-submit-dashboard:hover::before {
        left: 100%;
    }

    .btn-submit-dashboard i {
        color: #ffd700;
        font-size: 1.15rem;
        transition: transform 0.2s;
    }

    .btn-submit-dashboard:hover i {
        transform: translateX(4px);
    }

    /* Back Link */
    .return-home-wrap {
        margin-top: 18px;
    }

    .return-home-link {
        color: rgba(255, 255, 255, 0.72);
        font-size: 0.88rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: color 0.2s;
    }

    .return-home-link:hover {
        color: #ffd700;
        text-decoration: underline;
    }

    /* 5. FOOTER PERSIS SESUAI INSTRUKSI USER */
    .secret-login-footer {
        margin-top: 26px;
        padding-top: 20px;
        border-top: 1px solid rgba(255, 215, 0, 0.25);
        text-align: center;
    }

    .footer-text-line1 {
        font-size: 0.84rem;
        color: rgba(255, 255, 255, 0.92);
        font-weight: 500;
        letter-spacing: 0.3px;
        margin-bottom: 6px;
        line-height: 1.4;
    }

    .footer-text-line1 strong {
        color: #ffffff;
    }

    .footer-version-badge {
        display: inline-block;
        background: linear-gradient(135deg, #ffd700 0%, #d4af37 100%);
        color: #032314;
        font-weight: 800;
        font-size: 0.76rem;
        padding: 3px 14px;
        border-radius: 20px;
        letter-spacing: 1px;
        margin-bottom: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.3);
    }

    .footer-text-line3 {
        font-size: 0.78rem;
        color: rgba(255, 255, 255, 0.7);
        letter-spacing: 0.5px;
        text-transform: uppercase;
        margin-bottom: 0;
    }

    .footer-text-line3 strong {
        color: #ffd700;
    }

    /* Responsive */
    @media (max-width: 576px) {
        .secret-login-card {
            padding: 28px 20px 24px 20px;
            border-radius: 20px;
        }
        .bismillah-text {
            font-size: 1.7rem;
        }
        .welcome-heading {
            font-size: 1.25rem;
        }
        .sub-heading-masjid {
            font-size: 1rem;
        }
    }
</style>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="login-container-wrap">
                <div class="secret-login-card">

                    <!-- 3. Kalimat Pembuka Bismillah dalam Huruf Arab Berwarna Kuning Emas -->
                    <div class="bismillah-text">
                        بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ
                    </div>

                    <!-- 2. Logo Masjid Al-Jihad di Posisi Tengah -->
                    <div class="logo-center-wrap">
                        <div class="logo-halo-ring">
                            @if(isset($setting['logo']) && !empty($setting['logo']))
                            @php
                            $logoPath = $setting['logo'];
                            $imageUrl = Str::startsWith($logoPath, 'storage/') ? asset($logoPath) : asset('storage/' . $logoPath);
                            @endphp
                            <img src="{{ $imageUrl }}" alt="Logo Masjid Al-Jihad" class="logo-center-img" onerror="this.onerror=null; this.src='{{ asset('img/logo.png') }}';">
                            @else
                            <img src="{{ asset('img/logo.png') }}" alt="Logo Masjid Al-Jihad" class="logo-center-img">
                            @endif
                        </div>
                    </div>

                    <!-- 4. Ucapan Selamat Datang & Nama Aplikasi -->
                    <div class="secret-badge">
                        <i class="fas fa-shield-alt"></i> SECRET CREDENTIAL PORTAL
                    </div>
                    <h1 class="welcome-heading">Selamat datang di halaman LOGIN</h1>
                    <h2 class="sub-heading-masjid">System Informasi Digital Masjid Jami' Al Jihad</h2>

                    <!-- Ayat Arab & Artinya (Tidak Dihilangkan) -->
                    <div class="verse-banner">
                        <div class="verse-arabic-text">وَمَا تَوْفِيقِي إِلَّا بِاللَّهِ</div>
                        <div class="verse-meaning-text">"Dan tidak ada taufikku melainkan dengan pertolongan Allah" (QS. Hud: 88)</div>
                    </div>

                    <!-- Keterangan Masukkan Kredensial -->
                    <div class="credential-guide">
                        <i class="fas fa-key"></i> Masukkan kredensial untuk mengakses halaman dashboard
                    </div>

                    <!-- Alerts Session & Errors -->
                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert" style="background: rgba(18, 128, 73, 0.9); color: white; border: 1px solid #ffd700; border-radius: 12px;">
                        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                        <button type="button" class="close text-white" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    @endif

                    @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="background: rgba(180, 40, 40, 0.9); color: white; border: 1px solid #ff9999; border-radius: 12px; text-align: left;">
                        <ul class="mb-0 pl-3">
                            @foreach($errors->all() as $error)
                            <li><i class="fas fa-exclamation-circle mr-1"></i> {{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="close text-white" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    @endif

                    <!-- Form Login -->
                    <form class="user" method="POST" action="{{ route('login') }}" id="loginForm">
                        @csrf

                        <!-- Kolom Email / Username -->
                        <div class="custom-field-group">
                            <label class="custom-field-label">
                                <i class="fas fa-user-circle mr-1 text-warning"></i> Email / Username
                            </label>
                            <div class="custom-field-box">
                                <i class="fas fa-envelope field-lead-icon"></i>
                                <input type="text" class="custom-auth-input @error('email') is-invalid @enderror"
                                    name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="nama@email.com atau username">
                            </div>
                        </div>

                        <!-- Kolom Password -->
                        <div class="custom-field-group">
                            <label class="custom-field-label">
                                <i class="fas fa-lock mr-1 text-warning"></i> Kata Sandi
                            </label>
                            <div class="custom-field-box">
                                <i class="fas fa-shield-alt field-lead-icon"></i>
                                <input type="password" class="custom-auth-input @error('password') is-invalid @enderror"
                                    name="password" id="loginPasswordInput" required autocomplete="current-password" placeholder="Masukkan kata sandi rahasia">
                                <button type="button" class="pwd-visibility-toggle" onclick="togglePasswordVisibilityLogin()" title="Tampilkan / Sembunyikan Kata Sandi">
                                    <i class="fas fa-eye" id="pwdToggleIcon"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Tik Ingatkan Saya & Lupa Kata Sandi -->
                        <div class="auth-options-row">
                            <label class="remember-label-click" for="rememberTick">
                                <input type="checkbox" class="remember-custom-tick" id="rememberTick" name="remember" {{ old('remember') ? 'checked' : '' }}>
                                <span>Ingatkan saya</span>
                            </label>
                            @if (Route::has('password.request'))
                            <a class="forgot-pwd-link" href="{{ route('password.request') }}">
                                <i class="fas fa-question-circle mr-1"></i> Lupa Kata Sandi?
                            </a>
                            @endif
                        </div>

                        <!-- Tombol Masuk ke Dashboard -->
                        <button type="submit" class="btn-submit-dashboard" id="btnSubmitDashboard">
                            <span>Masuk ke Dashboard</span>
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    </form>

                    <!-- Link Kembali ke Beranda / Display TV -->
                    <div class="return-home-wrap">
                        <a href="{{ url('/') }}" class="return-home-link">
                            <i class="fas fa-arrow-left"></i> Kembali ke Beranda / Display TV
                        </a>
                    </div>

                    <!-- 5. FOOTER SESUAI INSTRUKSI USER PERSIS -->
                    <div class="secret-login-footer">
                        <div class="footer-text-line1">
                            System Informasi Digital ini dibuat dan di kembangkan oleh <strong>Masjid Jami' Al Jihad</strong>
                        </div>
                        <div class="footer-version-badge">
                            <i class="fas fa-code-branch mr-1"></i> Web Aplication VERSION 3.0.4
                        </div>
                        <div class="footer-text-line3">
                            <i class="far fa-copyright mr-1"></i> 2026 Powered by <strong>MASJID AL JIHAD GRAHA ASRI</strong>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function togglePasswordVisibilityLogin() {
        const input = document.getElementById('loginPasswordInput');
        const icon = document.getElementById('pwdToggleIcon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
</script>
@endsection