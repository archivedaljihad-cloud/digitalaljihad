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

    /* 1. Logo Masjid Al-Jihad di Posisi Atas-Center dengan Background Putih */
    .logo-center-wrap {
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 16px auto;
    }

    .logo-halo-ring {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background-color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 3.5px solid #ffd700;
        box-shadow: 
            0 0 24px rgba(255, 215, 0, 0.55),
            0 10px 30px rgba(0, 0, 0, 0.6);
        padding: 8px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .logo-halo-ring:hover {
        transform: scale(1.05);
        box-shadow: 
            0 0 34px rgba(255, 215, 0, 0.75),
            0 14px 35px rgba(0, 0, 0, 0.7);
    }

    .logo-center-img {
        max-height: 96px;
        max-width: 96px;
        object-fit: contain;
        filter: drop-shadow(0 2px 5px rgba(0, 0, 0, 0.15));
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
        padding: 0 50px 0 48px;
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
        top: 50%;
        transform: translateY(-50%);
        background: transparent;
        border: none;
        outline: none;
        color: #ffd700;
        font-size: 1.15rem;
        cursor: pointer;
        padding: 6px 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.25s ease;
        z-index: 5;
    }

    .pwd-visibility-toggle:hover {
        color: #ffffff;
        text-shadow: 0 0 10px rgba(255, 215, 0, 0.85);
        transform: translateY(-50%) scale(1.12);
    }

    .pwd-visibility-toggle:focus {
        outline: none;
        color: #ffffff;
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

    /* Back Link & Help Support */
    .auth-nav-links {
        margin-top: 18px;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
    }

    .return-home-link {
        color: rgba(255, 255, 255, 0.78);
        font-size: 0.88rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 7px;
        transition: all 0.2s ease;
    }

    .return-home-link:hover {
        color: #ffd700;
        text-decoration: none;
        transform: translateY(-1px);
    }

    .return-home-link i {
        color: #ffd700;
        font-size: 0.95rem;
    }

    .help-support-link {
        color: #d1fae5;
        font-size: 0.84rem;
        font-weight: 500;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 16px;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 215, 0, 0.3);
        border-radius: 20px;
        transition: all 0.25s ease;
    }

    .help-support-link:hover {
        color: #ffffff;
        background: rgba(37, 211, 102, 0.22);
        border-color: #25d366;
        text-decoration: none;
        box-shadow: 0 0 14px rgba(37, 211, 102, 0.4);
        transform: translateY(-1px);
    }

    .help-support-link .help-icon {
        color: #ffd700;
        font-size: 0.95rem;
    }

    .help-support-link .wa-icon {
        color: #25d366;
        font-size: 1.05rem;
    }

    /* 5. FOOTER PERSIS SESUAI INSTRUKSI USER */
    .secret-login-footer {
        margin-top: 26px;
        padding-top: 20px;
        border-top: 1px solid rgba(255, 215, 0, 0.25);
        text-align: center;
    }

    .footer-credit-intro {
        font-size: 0.84rem;
        color: rgba(255, 255, 255, 0.88);
        font-weight: 400;
        letter-spacing: 0.3px;
        margin-bottom: 3px;
        line-height: 1.4;
    }

    .footer-credit-brand {
        font-size: 1.05rem;
        font-weight: 700;
        color: #ffffff;
        letter-spacing: 0.5px;
        text-shadow: 0 0 10px rgba(255, 215, 0, 0.35);
        margin-bottom: 10px;
    }

    .footer-version-container {
        margin-bottom: 10px;
    }

    .footer-version-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: linear-gradient(135deg, #ffd700 0%, #d4af37 100%);
        color: #032314;
        font-weight: 800;
        font-size: 0.78rem;
        padding: 4px 16px;
        border-radius: 20px;
        letter-spacing: 1px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.35);
    }

    .footer-text-copyright {
        font-size: 0.78rem;
        color: rgba(255, 255, 255, 0.75);
        letter-spacing: 0.5px;
        text-transform: uppercase;
        margin-bottom: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
    }

    .footer-text-copyright i {
        color: #ffd700;
        font-size: 0.85rem;
    }

    .footer-text-copyright strong {
        color: #ffd700;
        font-weight: 700;
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

                    <!-- 1. Logo Masjid Al-Jihad di Atas-Center Kalimat Bismillah -->
                    <div class="logo-center-wrap">
                        <div class="logo-halo-ring">
                            <img src="{{ asset('img/logo-aljihad.png') }}" alt="Logo Masjid Al-Jihad" class="logo-center-img" onerror="this.onerror=null; this.src='{{ asset('img/logo.png') }}';">
                        </div>
                    </div>

                    <!-- 2. Kalimat Pembuka Bismillah dalam Huruf Arab Berwarna Kuning Emas -->
                    <div class="bismillah-text">
                        بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ
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

                    <!-- Link Navigasi Display TV & Bantuan WhatsApp -->
                    <div class="auth-nav-links">
                        <div>
                            <a href="{{ url('/') }}" class="return-home-link">
                                <i class="fas fa-tv"></i> Kembali ke Display TV
                            </a>
                        </div>
                        <div>
                            <a href="https://wa.me/6287758767000?text=Assalamu%27alaikum%20Admin%2C%20saya%20butuh%20bantuan%20login%20System%20Informasi%20Digital%20Masjid%20Jami%27%20Al%20Jihad" target="_blank" rel="noopener noreferrer" class="help-support-link" title="Hubungi Bantuan via WhatsApp 087758767000">
                                <i class="fas fa-headset help-icon"></i>
                                <span>Butuh Bantuan</span>
                                <i class="fab fa-whatsapp wa-icon"></i>
                            </a>
                        </div>
                    </div>

                    <!-- 5. FOOTER SESUAI INSTRUKSI USER PERSIS -->
                    <div class="secret-login-footer">
                        <div class="footer-credit-intro">
                            System Informasi Digital ini dibuat dan di kembangkan oleh :
                        </div>
                        <div class="footer-credit-brand">
                            Masjid Jami' Al Jihad
                        </div>
                        <div class="footer-version-container">
                            <span class="footer-version-badge">
                                <i class="fas fa-code-branch mr-1"></i> WEB APP. VERSION 3.0.4
                            </span>
                        </div>
                        <div class="footer-text-copyright">
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