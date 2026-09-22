@extends('layouts.auth')

@section('main-content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400&family=Outfit:wght@300;400;500;600;700;800&family=Scheherazade+New:wght@400;700&display=swap');

    /* 1. OVERRIDE AUTH LAYOUT BACKGROUND & CORNERS */
    body.bg-gradient-primary {
        background: #020810 url('{{ asset("img/login-bg.jpg") }}') no-repeat center center fixed !important;
        background-size: cover !important;
        min-height: 100vh;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        padding: 0 !important;
        overflow-x: hidden;
        font-family: 'Outfit', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }

    /* Sembunyikan ornamen bawaan layouts.auth agar background baru tampil bersih */
    .bg-gradient-primary::before,
    .islamic-corner {
        display: none !important;
    }

    /* 2. SPLIT LAYOUT 2-KOLOM (GAMBAR 2) */
    .login-page-wrap {
        width: 100vw;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 30px 40px;
        position: relative;
        z-index: 10;
        box-sizing: border-box;
    }

    .login-grid-split {
        width: 100%;
        max-width: 1320px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 30px;
    }

    /* SISI KIRI: MANDALA EMAS DENGAN LOGO MASJID AL-JIHAD */
    .login-mandala-side {
        flex: 0 0 38%;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px 10px;
    }

    .mandala-logo-box {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-left: clamp(90px, 10vw, 125px); /* Geser logo tepat ke tengah lingkaran pusat mandala */
        animation: pulseLogoGlow 4s infinite alternate ease-in-out;
    }

    .mandala-logo-img {
        width: 290px;
        max-width: 95%;
        height: auto;
        object-fit: contain;
        filter: 
            drop-shadow(0 0 16px rgba(255, 255, 255, 0.95))
            drop-shadow(0 0 36px rgba(0, 230, 118, 0.5))
            drop-shadow(0 8px 25px rgba(0, 0, 0, 0.95));
        transition: transform 0.35s ease, filter 0.35s ease;
    }

    .mandala-logo-img:hover {
        transform: scale(1.05);
        filter: 
            drop-shadow(0 0 24px rgba(255, 255, 255, 1))
            drop-shadow(0 0 50px rgba(0, 230, 118, 0.75))
            drop-shadow(0 12px 30px rgba(0, 0, 0, 0.98));
    }

    @keyframes pulseLogoGlow {
        0% { transform: scale(1); }
        100% { transform: scale(1.025); }
    }

    /* SISI KANAN: KONTEN LOGIN & FORM */
    .login-content-side {
        flex: 0 0 60%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 10px 15px;
    }

    .login-box-stack {
        width: 100%;
        max-width: 460px;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    /* KALIGRAFI ARAB SALAM & BISMILLAH */
    .arabic-greeting-wrap {
        text-align: center;
        margin-bottom: 10px;
        width: 100%;
    }

    .arabic-salam-text {
        font-family: 'Amiri', 'Scheherazade New', serif;
        font-size: 2.1rem;
        line-height: 1.35;
        font-weight: 700;
        color: #ffd700;
        text-shadow: 
            0 0 12px rgba(255, 215, 0, 0.65),
            0 2px 10px rgba(0, 0, 0, 0.95);
        margin-bottom: 3px;
        letter-spacing: 0.5px;
        direction: rtl;
    }

    .arabic-bismillah-text {
        font-family: 'Amiri', 'Scheherazade New', serif;
        font-size: 1.9rem;
        line-height: 1.35;
        font-weight: 700;
        color: #ffd700;
        text-shadow: 
            0 0 12px rgba(255, 215, 0, 0.65),
            0 2px 10px rgba(0, 0, 0, 0.95);
        margin-bottom: 8px;
        letter-spacing: 0.5px;
        direction: rtl;
    }

    /* UCAPAN SELAMAT DATANG & JUDUL SISTEM */
    .welcome-login-title {
        font-size: 1.22rem;
        font-weight: 700;
        color: #ffffff;
        text-shadow: 0 2px 8px rgba(0, 0, 0, 0.9);
        margin-bottom: 4px;
        text-align: center;
        letter-spacing: 0.5px;
    }

    .welcome-login-subtitle {
        font-size: 0.98rem;
        font-weight: 800;
        color: #ffffff;
        text-shadow: 0 2px 8px rgba(0, 0, 0, 0.9);
        margin-bottom: 14px;
        text-align: center;
        letter-spacing: 1px;
        text-transform: uppercase;
    }

    /* PILL PANDUAN KREDENSIAL */
    .credential-guide-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        padding: 7px 16px;
        background: rgba(4, 40, 24, 0.85);
        border: 1px solid rgba(0, 230, 118, 0.45);
        border-radius: 25px;
        color: #e0f2fe;
        font-size: 0.84rem;
        font-weight: 500;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.5);
        margin-bottom: 13px;
        backdrop-filter: blur(6px);
    }

    .credential-guide-pill i {
        color: #ffd700;
        font-size: 0.98rem;
    }

    /* CARD FORMULIR LOGIN - RAMPING & PRESISI */
    .login-form-card {
        width: 100%;
        background: rgba(3, 26, 17, 0.72);
        border: 1px solid rgba(0, 230, 118, 0.35);
        border-radius: 14px;
        padding: 18px 22px 14px 22px;
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        box-shadow: 
            0 15px 35px rgba(0, 0, 0, 0.75),
            0 0 25px rgba(0, 230, 118, 0.15),
            inset 0 1px 0 rgba(255, 255, 255, 0.08);
        margin-bottom: 14px;
    }

    /* FORM INPUT FIELDS - LEBIH RAMPING */
    .login-field-row {
        margin-bottom: 11px;
        width: 100%;
    }

    .login-field-label {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 0.82rem;
        font-weight: 600;
        color: #f1f5f9;
        margin-bottom: 4px;
        letter-spacing: 0.3px;
    }

    .login-field-label i {
        color: #ffd700;
        font-size: 0.88rem;
    }

    .login-input-container {
        position: relative;
        display: flex;
        align-items: center;
        width: 100%;
    }

    .login-input-icon-lead {
        position: absolute;
        left: 12px;
        color: #f59e0b;
        font-size: 0.95rem;
        pointer-events: none;
        z-index: 2;
    }

    .login-input-control {
        width: 100%;
        height: 38px;
        background: #ffffff !important;
        border: 2px solid transparent;
        border-radius: 8px;
        padding: 6px 36px 6px 36px;
        font-size: 0.88rem;
        color: #1e293b;
        font-weight: 500;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        transition: all 0.25s ease;
        outline: none;
    }

    .login-input-control::placeholder {
        color: #94a3b8;
        font-size: 0.84rem;
    }

    .login-input-control:focus {
        border-color: #ffd700;
        box-shadow: 
            0 0 0 3px rgba(255, 215, 0, 0.35),
            0 3px 10px rgba(0, 0, 0, 0.45);
        background: #ffffff !important;
        color: #0f172a;
    }

    .pwd-visibility-btn {
        position: absolute;
        right: 10px;
        background: transparent;
        border: none;
        color: #d97706;
        cursor: pointer;
        padding: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
        transition: color 0.2s, transform 0.15s;
        z-index: 3;
    }

    .pwd-visibility-btn:hover {
        color: #b45309;
        transform: scale(1.12);
    }

    /* REMEMBER & FORGOT PASSWORD ROW */
    .login-options-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 8px;
        margin-bottom: 13px;
        font-size: 0.80rem;
    }

    .remember-check-label {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #cbd5e1;
        cursor: pointer;
        user-select: none;
        margin-bottom: 0;
        font-weight: 500;
    }

    .remember-check-input {
        width: 15px;
        height: 15px;
        accent-color: #059669;
        cursor: pointer;
        border-radius: 4px;
    }

    .forgot-pwd-anchor {
        color: #ffd700;
        text-decoration: none;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        transition: all 0.2s ease;
    }

    .forgot-pwd-anchor:hover {
        color: #ffffff;
        text-decoration: underline;
        text-shadow: 0 0 8px rgba(255, 215, 0, 0.6);
    }

    /* TOMBOL MASUK KE DASHBOARD - LEBIH KECIL & RAMPING */
    .btn-submit-dashboard {
        width: 100%;
        height: 38px;
        background: linear-gradient(135deg, #056e3b 0%, #034825 100%);
        border: 1px solid #10b981;
        border-radius: 8px;
        padding: 8px 18px;
        color: #ffffff;
        font-size: 0.90rem;
        font-weight: 700;
        letter-spacing: 0.5px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        box-shadow: 
            0 4px 14px rgba(0, 0, 0, 0.5),
            0 0 12px rgba(16, 185, 129, 0.22);
        transition: all 0.3s ease;
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
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.25), transparent);
        transition: 0.6s;
    }

    .btn-submit-dashboard:hover {
        background: linear-gradient(135deg, #07894a 0%, #045a2f 100%);
        border-color: #ffd700;
        transform: translateY(-2px);
        box-shadow: 
            0 8px 20px rgba(0, 0, 0, 0.7),
            0 0 20px rgba(255, 215, 0, 0.4);
    }

    .btn-submit-dashboard:hover::before {
        left: 100%;
    }

    .btn-submit-dashboard i {
        color: #ffd700;
        font-size: 0.95rem;
        transition: transform 0.2s;
    }

    .btn-submit-dashboard:hover i {
        transform: translateX(4px);
    }

    /* ROW LINK NAVIGASI DISPLAY TV & WHATSAPP */
    .login-nav-sublinks {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 11px;
        padding-top: 9px;
        border-top: 1px solid rgba(255, 255, 255, 0.08);
        width: 100%;
    }

    .link-return-disp {
        color: #cbd5e1;
        font-size: 0.80rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-weight: 500;
        transition: color 0.2s;
    }

    .link-return-disp i {
        color: #ffd700;
        font-size: 0.88rem;
    }

    .link-return-disp:hover {
        color: #ffffff;
        text-decoration: underline;
    }

    .btn-help-wa {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: rgba(5, 150, 105, 0.2);
        border: 1px solid rgba(16, 185, 129, 0.4);
        padding: 4px 10px;
        border-radius: 6px;
        color: #e2e8f0;
        font-size: 0.78rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.25s;
    }

    .btn-help-wa i.fab.fa-whatsapp {
        color: #25d366;
        font-size: 0.92rem;
    }

    .btn-help-wa:hover {
        background: rgba(16, 185, 129, 0.35);
        border-color: #25d366;
        color: #ffffff;
        text-decoration: none;
        box-shadow: 0 0 10px rgba(37, 211, 102, 0.35);
    }

    /* CARD FOOTER IDENTITAS DEVELOPER (BAWAH) - LEBIH RAMPING */
    .developer-attribution-card {
        width: 100%;
        max-width: 410px;
        background: rgba(2, 28, 14, 0.85);
        border: 1px solid rgba(0, 230, 118, 0.35);
        border-radius: 10px;
        padding: 7px 16px;
        text-align: center;
        backdrop-filter: blur(8px);
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.5);
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 2px;
    }

    .attribution-intro {
        font-size: 0.70rem;
        color: #cbd5e1;
        font-weight: 400;
        line-height: 1.2;
    }

    .attribution-brand {
        font-size: 0.88rem;
        font-weight: 700;
        color: #ffffff;
        letter-spacing: 0.5px;
        line-height: 1.2;
    }

    .attribution-version-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: #f59e0b;
        color: #000000;
        font-size: 0.65rem;
        font-weight: 800;
        padding: 2px 10px;
        border-radius: 15px;
        letter-spacing: 0.4px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
        margin: 1px 0;
    }

    .attribution-copyright {
        font-size: 0.66rem;
        color: #cbd5e1;
        font-weight: 600;
        letter-spacing: 0.4px;
        text-transform: uppercase;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        line-height: 1.2;
    }

    .attribution-copyright strong {
        color: #ffd700;
        font-weight: 800;
    }

    /* RESPONSIVE LAYOUT BREAKPOINTS */
    @media (max-width: 991px) {
        .login-grid-split {
            flex-direction: column;
            justify-content: center;
            gap: 20px;
        }

        .login-mandala-side {
            flex: 0 0 auto;
            padding: 10px 0;
        }

        .mandala-logo-box {
            margin-left: 0 !important;
        }

        .mandala-logo-img {
            width: 190px;
        }

        .login-content-side {
            flex: 0 0 100%;
            width: 100%;
            padding: 0;
        }

        .arabic-salam-text {
            font-size: 1.8rem;
        }

        .arabic-bismillah-text {
            font-size: 1.6rem;
        }
    }

    @media (max-width: 576px) {
        .login-page-wrap {
            padding: 20px 15px;
        }

        .mandala-logo-img {
            width: 150px;
        }

        .arabic-salam-text {
            font-size: 1.55rem;
        }

        .arabic-bismillah-text {
            font-size: 1.4rem;
        }

        .welcome-login-title {
            font-size: 1.12rem;
        }

        .welcome-login-subtitle {
            font-size: 0.9rem;
        }

        .login-form-card {
            padding: 20px 18px;
        }
    }
</style>

<div class="login-page-wrap">
    <div class="login-grid-split">
        
        <!-- SISI KIRI: MANDALA EMAS DENGAN LOGO AL-JIHAD -->
        <div class="login-mandala-side">
            <div class="mandala-logo-box">
                <img src="{{ asset('img/logo-aljihad-transparent.png') }}" 
                     alt="Logo Masjid Al-Jihad" 
                     class="mandala-logo-img"
                     onerror="this.onerror=null; this.src='{{ asset('img/logo-aljihad.png') }}';">
            </div>
        </div>

        <!-- SISI KANAN: KONTEN LOGIN & FORM -->
        <div class="login-content-side">
            <div class="login-box-stack">
                
                <!-- 1. Kaligrafi Arab Salam & Bismillah (Kuning Emas) -->
                <div class="arabic-greeting-wrap">
                    <div class="arabic-salam-text">
                        ٱلسَّلَامُ عَلَيْكُمْ وَرَحْمَةُ ٱللَّٰهِ وَبَرَكَاتُهُ
                    </div>
                    <div class="arabic-bismillah-text">
                        بِسْمِ ٱللَّهِ ٱلرَّحْمَٰنِ ٱلرَّحِيمِ
                    </div>
                </div>

                <!-- 2. Ucapan Selamat Datang & Nama Sistem -->
                <h1 class="welcome-login-title">Selamat datang di halaman LOGIN</h1>
                <h2 class="welcome-login-subtitle">SYSTEM INFORMASI DIGITAL MASJID JAMI' AL JIHAD</h2>

                <!-- 3. Pill Petunjuk Masukkan Kredensial -->
                <div class="credential-guide-pill">
                    <i class="fas fa-key"></i>
                    <span>Masukkan kredensial untuk mengakses halaman dashboard</span>
                </div>

                <!-- Notifikasi Alert Session & Validation Errors -->
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show w-100" role="alert" style="background: rgba(18, 128, 73, 0.95); color: #ffffff; border: 1px solid #ffd700; border-radius: 10px; font-size: 0.88rem; margin-bottom: 15px;">
                    <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                    <button type="button" class="close text-white" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                @endif

                @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show w-100" role="alert" style="background: rgba(180, 40, 40, 0.95); color: #ffffff; border: 1px solid #ff9999; border-radius: 10px; font-size: 0.86rem; text-align: left; margin-bottom: 15px;">
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

                <!-- 4. Card Form Login -->
                <div class="login-form-card">
                    <form class="user" method="POST" action="{{ route('login') }}" id="loginForm">
                        @csrf

                        <!-- Field Email / Username -->
                        <div class="login-field-row">
                            <label class="login-field-label">
                                <i class="fas fa-user-circle"></i>
                                <span>Email / Username</span>
                            </label>
                            <div class="login-input-container">
                                <i class="fas fa-envelope login-input-icon-lead"></i>
                                <input type="text" 
                                       class="login-input-control @error('email') is-invalid @enderror"
                                       name="email" 
                                       value="{{ old('email') }}" 
                                       required 
                                       autocomplete="email" 
                                       autofocus 
                                       placeholder="bendahara@aljihad.com">
                            </div>
                        </div>

                        <!-- Field Kata Sandi -->
                        <div class="login-field-row">
                            <label class="login-field-label">
                                <i class="fas fa-lock"></i>
                                <span>Kata Sandi</span>
                            </label>
                            <div class="login-input-container">
                                <i class="fas fa-shield-alt login-input-icon-lead"></i>
                                <input type="password" 
                                       class="login-input-control @error('password') is-invalid @enderror"
                                       name="password" 
                                       id="loginPasswordInput" 
                                       required 
                                       autocomplete="current-password" 
                                       placeholder="••••••••••••">
                                <button type="button" 
                                        class="pwd-visibility-btn" 
                                        onclick="togglePasswordVisibilityLogin()" 
                                        title="Tampilkan / Sembunyikan Kata Sandi">
                                    <i class="fas fa-eye" id="pwdToggleIcon"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Row Opsi Ingatkan Saya & Lupa Kata Sandi -->
                        <div class="login-options-row">
                            <label class="remember-check-label" for="rememberTick">
                                <input type="checkbox" 
                                       class="remember-check-input" 
                                       id="rememberTick" 
                                       name="remember" 
                                       {{ old('remember') ? 'checked' : '' }}>
                                <span>Ingatkan saya</span>
                            </label>
                            @if (Route::has('password.request'))
                            <a class="forgot-pwd-anchor" href="{{ route('password.request') }}">
                                <i class="fas fa-question-circle"></i>
                                <span>Lupa Kata Sandi?</span>
                            </a>
                            @endif
                        </div>

                        <!-- Tombol Submit Masuk ke Dashboard -->
                        <button type="submit" class="btn-submit-dashboard" id="btnSubmitDashboard">
                            <span>Masuk ke Dashboard</span>
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    </form>

                    <!-- Navigasi Bawah Form: Kembali ke Display & Butuh Bantuan WA -->
                    <div class="login-nav-sublinks">
                        <a href="{{ url('/') }}" class="link-return-disp">
                            <i class="fas fa-tv"></i>
                            <span>Kembali ke Display</span>
                        </a>
                        <a href="https://wa.me/6287758767000?text=Assalamu%27alaikum%20Admin%2C%20saya%20butuh%20bantuan%20login%20System%20Informasi%20Digital%20Masjid%20Jami%27%20Al%20Jihad" 
                           target="_blank" 
                           rel="noopener noreferrer" 
                           class="btn-help-wa" 
                           title="Hubungi Bantuan via WhatsApp 087758767000">
                            <span>Butuh Bantuan</span>
                            <i class="fab fa-whatsapp"></i>
                        </a>
                    </div>
                </div>

                <!-- 5. Card Footer Identitas Developer & Versi -->
                <div class="developer-attribution-card">
                    <div class="attribution-intro">
                        System Informasi Digital ini dibuat dan di kembangkan oleh :
                    </div>
                    <div class="attribution-brand">
                        Masjid Jami' Al Jihad
                    </div>
                    <div>
                        <span class="attribution-version-badge">
                            <i class="fas fa-bolt mr-1"></i> WEB APP. VERSION 3.0.4
                        </span>
                    </div>
                    <div class="attribution-copyright">
                        <i class="far fa-copyright mr-1"></i> 2026 POWERED BY <strong>MASJID AL JIHAD GRAHA ASRI</strong>
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

    document.getElementById('loginForm')?.addEventListener('submit', function() {
        const btn = document.getElementById('btnSubmitDashboard');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span>Memverifikasi Kredensial...</span> <i class="fas fa-spinner fa-spin ml-2"></i>';
            btn.style.opacity = '0.85';
            btn.style.cursor = 'wait';
        }
    });
</script>
@endsection