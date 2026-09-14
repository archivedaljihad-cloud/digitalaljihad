<!-- resources/views/about.blade.php -->
@extends('layouts.admin')
@section('title', 'Tentang Aplikasi & Panduan Sistem')
@section('main-content')

<div class="row justify-content-center">
    <div class="col-xl-11 col-lg-12">
        <!-- Main Card -->
        <div class="card shadow-lg mb-4" style="border: none; border-radius: 20px; overflow: hidden; background: #ffffff;">
            
            <!-- Hero Header with Premium Islamic Gradient & Hex Pattern -->
            <div class="card-header py-4 px-4 text-center position-relative" style="background: linear-gradient(135deg, #071a10 0%, #0e3521 50%, #1a5235 100%); color: white; border-bottom: 3px solid #c9a03d;">
                <div class="d-flex justify-content-center mb-3">
                    <div style="position: relative;">
                        <img src="{{ isset($setting->logo) && $setting->logo ? asset('storage/' . $setting->logo) : asset('img/default-logo.png') }}"
                             alt="Logo Aplikasi"
                             class="img-fluid"
                             style="max-width: 90px; height: 90px; object-fit: cover; border-radius: 50%; border: 3px solid #c9a03d; padding: 4px; background: #ffffff; box-shadow: 0 8px 25px rgba(0,0,0,0.3);">
                        <span class="badge position-absolute" style="bottom: 0; right: -10px; background: #c9a03d; color: #071a10; font-weight: 700; font-size: 11px; padding: 4px 8px; border-radius: 12px; box-shadow: 0 2px 6px rgba(0,0,0,0.2);">
                            v4.0
                        </span>
                    </div>
                </div>

                <h3 class="font-weight-bold mb-1" style="font-family: 'Amiri', serif; letter-spacing: 0.5px; text-shadow: 0 2px 4px rgba(0,0,0,0.3);">
                    {{ $setting->nama_aplikasi ?? 'Sistem Informasi Masjid Digital' }}
                </h3>
                <p class="mb-2" style="font-size: 15px; color: rgba(255,255,255,0.85); font-weight: 400;">
                    Platform Digital Signage Masjid Terintegrasi & Smart Prayer Display System
                </p>
                
                <!-- Quick Status Badges -->
                <div class="d-flex flex-wrap justify-content-center align-items-center mt-3" style="gap: 8px;">
                    <span class="badge px-3 py-2" style="background: rgba(201, 160, 61, 0.2); color: #ffd700; border: 1px solid rgba(201, 160, 61, 0.4); border-radius: 20px; font-weight: 500;">
                        <i class="fas fa-code-branch mr-1"></i> Versi 4.0 (Update Sep 2026)
                    </span>
                    <span class="badge px-3 py-2" style="background: rgba(255, 255, 255, 0.1); color: #ffffff; border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 20px; font-weight: 500;">
                        <i class="fab fa-laravel mr-1" style="color: #ff2d20;"></i> Laravel 13 & PHP 8.3
                    </span>
                    @if($setting->rotation_enabled ?? false)
                    <span class="badge px-3 py-2" style="background: rgba(40, 167, 69, 0.25); color: #a3e635; border: 1px solid rgba(40, 167, 69, 0.5); border-radius: 20px; font-weight: 500;">
                        <i class="fas fa-play-circle mr-1"></i> Rotasi TV: AKTIF ({{ $setting->rotation_interval ?? 10 }}s)
                    </span>
                    @else
                    <span class="badge px-3 py-2" style="background: rgba(108, 117, 125, 0.25); color: #cbd5e1; border: 1px solid rgba(108, 117, 125, 0.4); border-radius: 20px; font-weight: 500;">
                        <i class="fas fa-pause-circle mr-1"></i> Rotasi TV: NONAKTIF
                    </span>
                    @endif
                    <a href="{{ url('/rotator') }}" target="_blank" class="badge px-3 py-2 text-decoration-none" style="background: #c9a03d; color: #071a10; font-weight: 600; border-radius: 20px; transition: transform 0.2s ease;">
                        <i class="fas fa-external-link-alt mr-1"></i> Buka Display TV
                    </a>
                </div>
            </div>

            <!-- Card Body -->
            <div class="card-body p-4 p-md-5">

                <!-- 1. INTRODUKSI SISTEM & UNGKAPAN RASA SYUKUR -->
                <section class="mb-5">
                    <div class="d-flex align-items-center mb-3">
                        <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(30,90,58,0.1); color: #1e5a3a; display: flex; align-items: center; justify-content: center; font-size: 18px; margin-right: 12px;">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <h4 class="font-weight-bold m-0" style="color: #0e3521;">Tentang Sistem Digital Masjid</h4>
                    </div>

                    <!-- Mukaddimah Arab (Semua Huruf Arab Center Tengah) -->
                    <div class="p-4 rounded mb-4" style="background: linear-gradient(135deg, rgba(30,90,58,0.04) 0%, rgba(201,160,61,0.06) 100%); border-top: 2px solid #c9a03d; border-bottom: 2px solid #c9a03d; border-radius: 16px !important;">
                        <div class="text-center mb-3" style="font-family: 'Amiri', serif; font-size: 26px; font-weight: 700; color: #1e5a3a; line-height: 1.8;">
                            بِسْمِ اللَّهِ الرَّحْمَنِ الرَّحِيمِ
                        </div>
                        <div class="text-center mb-3" style="font-family: 'Amiri', serif; font-size: 22px; font-weight: 600; color: #0e3521; line-height: 1.8;">
                            السَّلَامُ عَلَيْكُمْ وَرَحْمَةُ اللَّهِ وَبَرَكَاتُهُ
                        </div>
                        <div class="text-center px-md-4" style="font-family: 'Amiri', serif; font-size: 20px; color: #1a5235; line-height: 2.2; direction: rtl;">
                            الْحَمْدُ لِلَّهِ رَبِّ الْعَالَمِينَ، وَبِهِ نَسْتَعِينُ عَلَى أُمُورِ الدُّنْيَا وَالدِّينِ. أَشْهَدُ أَنْ لَا إِلَهَ إِلَّا اللَّهُ، وَأَشْهَدُ أَنَّ مُحَمَّدًا عَبْدُهُ وَرَسُولُهُ. اللَّهُمَّ صَلِّ عَلَى سَيِّدِنَا مُحَمَّدٍ وَعَلَى آلِهِ وَصَحْبِهِ أَجْمَعِينَ.
                        </div>
                    </div>

                    <!-- Isi Sambutan & Penjelasan Sistem -->
                    <div style="color: #374151; line-height: 1.9; font-size: 15px;">
                        <p class="text-justify mb-3">
                            Puji dan syukur marilah kita panjatkan ke hadirat Allah SWT, yang telah memberikan kita nikmat kesehatan, kemudahan, serta kesempatan sehingga pada hari yang penuh berkah ini, aplikasi web untuk Masjid Al-Jihad dapat diselesaikan dan siap untuk dipergunakan.
                        </p>

                        <p class="text-justify mb-3">
                            Jujur, dalam proses pembuatannya, saya sangat menyadari segala keterbatasan dan kekurangan kemampuan yang saya miliki. Namun, hanya atas izin, petunjuk, dan pertolongan dari Allah SWT belaka (<span style="font-family: 'Amiri', serif; font-size: 17px; color: #1e5a3a; font-weight: 600;">لَا حَوْلَ وَلَا قُوَّةَ إِلَّا بِاللَّهِ</span>), niat baik ini akhirnya dapat terwujud.
                        </p>

                        <p class="text-justify mb-3">
                            Hadirnya sistem aplikasi web ini tidak lain hanyalah sebuah sarana ikhtiar kecil yang dapat saya persembahkan untuk membantu merapikan administrasi, meningkatkan transparansi, serta memudahkan pelayanan ibadah dan pengelolaan kegiatan di Masjid Al-Jihad yang kita cintai ini.
                        </p>

                        <p class="text-justify mb-3">
                            <strong>{{ $setting->nama_aplikasi ?? "MASJID JAMI' AL JIHAD" }}</strong> adalah platform digital signage dan sistem otomasi masjid modern yang dirancang khusus untuk memenuhi kebutuhan syiar, transparansi kas, serta ketertiban ibadah. Aplikasi ini berjalan dengan arsitektur <em>Dual Engine Rotator</em> (Layar TV Utama & Layar TV Luar/Serambi), serta terintegrasi penuh dengan <em>Prayer Mode Otomatis</em> dan <em>CCTV Mimbar Live Stream</em>.
                        </p>

                        <p class="text-justify mb-3">
                            Sistem ini mengedepankan performa tinggi dengan teknologi murni <strong>Vanilla JS & GPU-Accelerated CSS</strong> sehingga mampu menampilkan visual yang sangat mewah, halus (*60 FPS tanpa jeda kedip*), dan ramah terhadap perangkat TV box berdaya rendah tanpa membebani memori. Demi efisiensi daya dan waktu pengoperasian, sistem TV ini juga dilengkapi dengan <strong>Smart Breaker</strong> dan <strong>Smart IR Remote Control</strong> sehingga beroperasi penuh secara mandiri (<em>full auto self running</em>).
                        </p>

                        <p class="text-justify mb-3">
                            Besar harapan saya, platform ini dapat memberikan manfaat yang nyata bagi pengurus maupun seluruh jama'ah. Sekali lagi saya mohon ma'af jika dalam menyelesaikan web aplikasi ini ada keterlambatan dan masih banyak kekurangan.
                        </p>

                        <p class="text-justify mb-4">
                            Terima kasih yang sebesar-besarnya saya sampaikan kepada seluruh pihak dan pengurus Masjid Al-Jihad yang telah memberikan kepercayaan, dukungan, serta doa selama proses pembuatan. Semoga setiap usaha dan ikhtiar kita ini dicatat oleh Allah SWT sebagai amal jariyah yang pahalanya terus mengalir. Aamiin Ya Rabbal 'Alamin.
                        </p>

                        <!-- Penutup Salam Arab (Center Tengah) -->
                        <div class="text-center my-4 py-3" style="font-family: 'Amiri', serif; font-size: 22px; font-weight: 600; color: #0e3521; border-top: 1px dashed rgba(201,160,61,0.4); border-bottom: 1px dashed rgba(201,160,61,0.4);">
                            وَالسَّلاَمُ عَلَيْكُمْ وَرَحْمَةُ اللهِ وَبَرَكَاتُهُ
                        </div>
                    </div>
                </section>

                <!-- 2. FITUR TAMPILAN TV MUTAKHIR (YANG DIPERBARUI TOTAL) -->
                <section class="mb-5">
                    <div class="d-flex align-items-center mb-2">
                        <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(201,160,61,0.15); color: #c9a03d; display: flex; align-items: center; justify-content: center; font-size: 18px; margin-right: 12px;">
                            <i class="fas fa-tv"></i>
                        </div>
                        <div>
                            <h4 class="font-weight-bold m-0" style="color: #0e3521;">Fitur Tampilan TV Mutakhir</h4>
                            <p class="small text-muted mb-0">Teknologi visual canggih yang tampil di layar TV masjid Anda</p>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <!-- TV Fitur 1 -->
                        <div class="col-md-6 col-lg-3 mb-4">
                            <div class="card h-100 shadow-sm feature-box" style="border-radius: 16px; border: 1px solid rgba(30,90,58,0.1); background: #ffffff;">
                                <div class="card-body p-4 text-center">
                                    <div class="feature-icon mb-3" style="background: linear-gradient(135deg, #1e5a3a, #0a2e1f); color: #ffd700; width: 56px; height: 56px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 22px; margin: 0 auto; box-shadow: 0 4px 12px rgba(30,90,58,0.25);">
                                        <i class="fas fa-clone"></i>
                                    </div>
                                    <h6 class="font-weight-bold" style="color: #0e3521; font-size: 15px;">Dual Engine Crossfade</h6>
                                    <p class="small text-muted mb-0" style="line-height: 1.6;">
                                        Arsitektur dual-iframe cerdas. Pergantian antar slide berlangsung mulus tanpa layar berkedip hitam (<em>zero-flicker transitions</em>).
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- TV Fitur 2 -->
                        <div class="col-md-6 col-lg-3 mb-4">
                            <div class="card h-100 shadow-sm feature-box" style="border-radius: 16px; border: 1px solid rgba(30,90,58,0.1); background: #ffffff;">
                                <div class="card-body p-4 text-center">
                                    <div class="feature-icon mb-3" style="background: linear-gradient(135deg, #c9a03d, #8f6c18); color: #ffffff; width: 56px; height: 56px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 22px; margin: 0 auto; box-shadow: 0 4px 12px rgba(201,160,61,0.25);">
                                        <i class="fas fa-hourglass-half"></i>
                                    </div>
                                    <h6 class="font-weight-bold" style="color: #0e3521; font-size: 15px;">Smart Next Prayer Bar</h6>
                                    <p class="small text-muted mb-0" style="line-height: 1.6;">
                                        Kapsul kaca transparan (<em>glassmorphism</em>) di pojok atas yang menghitung mundur waktu sholat berikutnya detik-demi-detik.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- TV Fitur 3 -->
                        <div class="col-md-6 col-lg-3 mb-4">
                            <div class="card h-100 shadow-sm feature-box" style="border-radius: 16px; border: 1px solid rgba(30,90,58,0.1); background: #ffffff;">
                                <div class="card-body p-4 text-center">
                                    <div class="feature-icon mb-3" style="background: linear-gradient(135deg, #0e3521, #1a5235); color: #5eead4; width: 56px; height: 56px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 22px; margin: 0 auto; box-shadow: 0 4px 12px rgba(14,53,33,0.25);">
                                        <i class="fas fa-palette"></i>
                                    </div>
                                    <h6 class="font-weight-bold" style="color: #0e3521; font-size: 15px;">Dynamic Ambient Theme</h6>
                                    <p class="small text-muted mb-0" style="line-height: 1.6;">
                                        Pendaran aura cahaya latar layar (<em>floating ambient orbs</em>) yang otomatis berganti warna mengikuti 6 siklus waktu sholat.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- TV Fitur 4 -->
                        <div class="col-md-6 col-lg-3 mb-4">
                            <div class="card h-100 shadow-sm feature-box" style="border-radius: 16px; border: 1px solid rgba(30,90,58,0.1); background: #ffffff;">
                                <div class="card-body p-4 text-center">
                                    <div class="feature-icon mb-3" style="background: linear-gradient(135deg, #1e5a3a, #071a10); color: #fef08a; width: 56px; height: 56px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 22px; margin: 0 auto; box-shadow: 0 4px 12px rgba(30,90,58,0.25);">
                                        <i class="fas fa-mosque"></i>
                                    </div>
                                    <h6 class="font-weight-bold" style="color: #0e3521; font-size: 15px;">Prayer & Khutbah Engine</h6>
                                    <p class="small text-muted mb-0" style="line-height: 1.6;">
                                        Otomasi 5 fase ibadah: Audio Tarhim, Adzan, Hitung Mundur Iqamah, Layar Gelap Sholat Khusyuk, serta Mode Khutbah Jum'at.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- TV Fitur 5 -->
                        <div class="col-md-6 col-lg-3 mb-4">
                            <div class="card h-100 shadow-sm feature-box" style="border-radius: 16px; border: 1px solid rgba(30,90,58,0.1); background: #ffffff;">
                                <div class="card-body p-4 text-center">
                                    <div class="feature-icon mb-3" style="background: linear-gradient(135deg, #b91c1c, #7f1d1d); color: #ffffff; width: 56px; height: 56px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 22px; margin: 0 auto; box-shadow: 0 4px 12px rgba(185,28,28,0.25);">
                                        <i class="fas fa-video"></i>
                                    </div>
                                    <h6 class="font-weight-bold" style="color: #0e3521; font-size: 15px;">CCTV Mimbar Otomatis</h6>
                                    <p class="small text-muted mb-0" style="line-height: 1.6;">
                                        TV Luar/Serambi otomatis beralih menampilkan siaran langsung kamera mimbar saat Khutbah Jum'at atau Sholat Ied dimulai.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- TV Fitur 6 -->
                        <div class="col-md-6 col-lg-3 mb-4">
                            <div class="card h-100 shadow-sm feature-box" style="border-radius: 16px; border: 1px solid rgba(30,90,58,0.1); background: #ffffff;">
                                <div class="card-body p-4 text-center">
                                    <div class="feature-icon mb-3" style="background: linear-gradient(135deg, #0284c7, #0369a1); color: #ffffff; width: 56px; height: 56px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 22px; margin: 0 auto; box-shadow: 0 4px 12px rgba(2,132,199,0.25);">
                                        <i class="fas fa-kaaba"></i>
                                    </div>
                                    <h6 class="font-weight-bold" style="color: #0e3521; font-size: 15px;">Live Makkah & Madinah</h6>
                                    <p class="small text-muted mb-0" style="line-height: 1.6;">
                                        Siaran langsung 24 jam Masjidil Haram dan Masjid Nabawi dilengkapi Smart Mosque Overlay (jam & jadwal sholat transparan).
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- TV Fitur 7 -->
                        <div class="col-md-6 col-lg-3 mb-4">
                            <div class="card h-100 shadow-sm feature-box" style="border-radius: 16px; border: 1px solid rgba(30,90,58,0.1); background: #ffffff;">
                                <div class="card-body p-4 text-center">
                                    <div class="feature-icon mb-3" style="background: linear-gradient(135deg, #4f46e5, #3730a3); color: #ffffff; width: 56px; height: 56px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 22px; margin: 0 auto; box-shadow: 0 4px 12px rgba(79,70,229,0.25);">
                                        <i class="fas fa-bullhorn"></i>
                                    </div>
                                    <h6 class="font-weight-bold" style="color: #0e3521; font-size: 15px;">Smart Running Text</h6>
                                    <p class="small text-muted mb-0" style="line-height: 1.6;">
                                        Pengumuman teks berjalan (*marquee*) yang super halus, kecepatan dapat disesuaikan, dan terintegrasi pesan dinamis masjid.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- TV Fitur 8 -->
                        <div class="col-md-6 col-lg-3 mb-4">
                            <div class="card h-100 shadow-sm feature-box" style="border-radius: 16px; border: 1px solid rgba(30,90,58,0.1); background: #ffffff;">
                                <div class="card-body p-4 text-center">
                                    <div class="feature-icon mb-3" style="background: linear-gradient(135deg, #059669, #047857); color: #ffffff; width: 56px; height: 56px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 22px; margin: 0 auto; box-shadow: 0 4px 12px rgba(5,150,105,0.25);">
                                        <i class="fas fa-images"></i>
                                    </div>
                                    <h6 class="font-weight-bold" style="color: #0e3521; font-size: 15px;">Slide Poster & Brosur</h6>
                                    <p class="small text-muted mb-0" style="line-height: 1.6;">
                                        Penayangan brosur kajian, laporan donasi, dan poster dakwah beresolusi tinggi dengan sistem auto-scaling ke layar TV.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Smart Breaker & IR Remote Control Banner -->
                    <div class="card mt-2 mb-2 border-0 shadow-sm" style="border-radius: 16px; background: linear-gradient(135deg, rgba(30,90,58,0.06) 0%, rgba(201,160,61,0.08) 100%); border: 1px solid rgba(201,160,61,0.25) !important;">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center flex-wrap flex-md-nowrap">
                                <div class="mr-md-4 mb-3 mb-md-0 text-center" style="flex-shrink: 0;">
                                    <div style="width: 60px; height: 60px; border-radius: 16px; background: linear-gradient(135deg, #0e3521, #1e5a3a); color: #ffd700; display: flex; align-items: center; justify-content: center; font-size: 26px; box-shadow: 0 4px 14px rgba(14,53,33,0.25); margin: 0 auto;">
                                        <i class="fas fa-power-off"></i>
                                    </div>
                                </div>
                                <div style="flex: 1;">
                                    <div class="d-flex align-items-center mb-1 flex-wrap">
                                        <h6 class="font-weight-bold m-0 mr-2" style="color: #0e3521; font-size: 16px;">
                                            Efisiensi Daya & Otomasi Mandiri (Full Auto Self-Running)
                                        </h6>
                                        <span class="badge px-2 py-1 mt-1 mt-md-0" style="background: #c9a03d; color: #071a10; font-weight: 700; font-size: 11px; border-radius: 8px;">
                                            <i class="fas fa-microchip mr-1"></i> Smart Hardware
                                        </span>
                                    </div>
                                    <p class="small mb-0 text-muted text-justify" style="line-height: 1.7;">
                                        Demi efisiensi daya dan waktu pengoperasian, TV ini dilengkapi dengan <strong>Smart Breaker</strong> dan <strong>Smart IR Remote Control</strong> sehingga beroperasi penuh secara mandiri (<em>full auto self running</em>). Seluruh rangkaian TV display dapat menyala dan mati secara otomatis dan terjadwal tanpa memerlukan campur tangan manual dari pengurus setiap hari.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- 3. FITUR ROTASI HALAMAN DINAMIS & SALURAN DISPLAY (YANG DIPERBARUI TOTAL) -->
                <section class="mb-5">
                    <div class="d-flex align-items-center mb-2">
                        <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(30,90,58,0.1); color: #1e5a3a; display: flex; align-items: center; justify-content: center; font-size: 18px; margin-right: 12px;">
                            <i class="fas fa-exchange-alt"></i>
                        </div>
                        <div>
                            <h4 class="font-weight-bold m-0" style="color: #0e3521;">Fitur Rotasi Halaman Dinamis</h4>
                            <p class="small text-muted mb-0">Manajemen urutan siaran, multi-saluran, dan kontrol akses cerdas</p>
                        </div>
                    </div>

                    <div class="card shadow-sm border-0 mt-3" style="border-radius: 16px; background: #fafdfb; border: 1px solid rgba(30,90,58,0.12) !important;">
                        <div class="card-body p-4">
                            <div class="row">
                                <div class="col-lg-6 mb-4 mb-lg-0">
                                    <h6 class="font-weight-bold" style="color: #0e3521; font-size: 16px;">
                                        <i class="fas fa-question-circle mr-2" style="color: #c9a03d;"></i>Apa itu Sistem Rotasi Halaman?
                                    </h6>
                                    <p class="small text-muted text-justify" style="line-height: 1.8;">
                                        Sistem rotasi halaman adalah mesin display yang mengatur pergantian berbagai informasi masjid di layar TV secara otomatis. Layar TV akan memutar slide secara bergantian sesuai interval waktu (contoh: 10–20 detik per halaman) yang telah ditentukan oleh pengurus.
                                    </p>
                                    
                                    <h6 class="font-weight-bold mt-3" style="color: #0e3521; font-size: 15px;">
                                        <i class="fas fa-sort-amount-down mr-2" style="color: #c9a03d;"></i>Fitur Baru: Pengaturan Urutan Prioritas
                                    </h6>
                                    <ul class="small text-muted pl-3 mb-0" style="line-height: 1.8;">
                                        <li><strong>Super Admin:</strong> Dapat menggeser urutan prioritas slide dengan tombol <strong>Naik (▲)</strong> dan <strong>Turun (▼)</strong> sesuai agenda prioritas masjid.</li>
                                        <li><strong>Operator / Petugas:</strong> Urutan halaman dikunci demi keamanan penayangan, namun tetap dapat mengaktifkan atau menonaktifkan slide tertentu.</li>
                                        <li><strong>Sinkronisasi Instan:</strong> Perubahan rotasi langsung diterapkan ke layar TV tanpa perlu menyentuh atau me-refresh TV secara manual.</li>
                                    </ul>
                                </div>

                                <div class="col-lg-6">
                                    <div class="p-4 rounded-lg" style="background: #ffffff; border: 1px solid rgba(30,90,58,0.12); border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
                                        <h6 class="font-weight-bold mb-3" style="color: #0e3521;">
                                            <i class="fas fa-sliders-h mr-2" style="color: #c9a03d;"></i>Langkah Pengaturan Rotasi:
                                        </h6>
                                        <ol class="small text-muted pl-3 mb-3" style="line-height: 1.9;">
                                            <li>Buka menu <strong>Rotasi Halaman</strong> pada panel navigasi admin.</li>
                                            <li>Pastikan toggle switch <strong>Status Rotasi</strong> dalam posisi Aktif.</li>
                                            <li>Tentukan <strong>Interval Rotasi</strong> (waktu tampil per slide, rekomendasi: 12–20 detik).</li>
                                            <li>Gunakan tombol <strong>▲ Naik</strong> atau <strong>▼ Turun</strong> untuk menentukan urutan tayang.</li>
                                            <li>Centang slide yang ingin ditayangkan di TV, lalu klik <strong>Simpan Pengaturan</strong>.</li>
                                        </ol>
                                        <div class="d-flex align-items-center p-3 rounded" style="background: rgba(201,160,61,0.1); border-left: 4px solid #c9a03d;">
                                            <i class="fas fa-bolt mr-3" style="color: #c9a03d; font-size: 20px;"></i>
                                            <span class="small" style="color: #6b5012; font-weight: 500;">
                                                Perubahan langsung aktif secara *real-time* di layar TV tanpa perlu me-reload perangkat TV!
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4" style="border-color: rgba(30,90,58,0.1);">

                            <!-- 13 Saluran Slide Mutakhir -->
                            <div>
                                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
                                    <h6 class="font-weight-bold m-0" style="color: #0e3521;">
                                        <i class="fas fa-th-large mr-2" style="color: #c9a03d;"></i>Daftar 13 Saluran Slide Display Aktif:
                                    </h6>
                                    <span class="small text-muted">Klik salah satu saluran untuk melihat pratinjau langsung:</span>
                                </div>

                                <div class="row" style="gap: 0px;">
                                    @php
                                        $slides = [
                                            ['url' => 'utama-embed', 'name' => 'Jadwal Sholat 5 Waktu', 'icon' => 'fa-clock', 'color' => '#1e5a3a'],
                                            ['url' => 'keuangan-embed', 'name' => 'Rincian Kas Utama', 'icon' => 'fa-wallet', 'color' => '#0e3521'],
                                            ['url' => 'keuangan-summary-embed', 'name' => 'Grafik Arus Kas', 'icon' => 'fa-chart-pie', 'color' => '#0284c7'],
                                            ['url' => 'ambulance-embed', 'name' => 'Kas Mobil Ambulance', 'icon' => 'fa-ambulance', 'color' => '#b91c1c'],
                                            ['url' => 'infaq-embed', 'name' => 'Program Donasi & Infaq', 'icon' => 'fa-hand-holding-usd', 'color' => '#059669'],
                                            ['url' => 'jumat-embed', 'name' => 'Petugas Sholat Jum\'at', 'icon' => 'fa-users', 'color' => '#c9a03d'],
                                            ['url' => 'pengumuman-embed', 'name' => 'Daftar Pengumuman', 'icon' => 'fa-bullhorn', 'color' => '#4f46e5'],
                                            ['url' => 'slide-embed', 'name' => 'Slide Poster Informasi', 'icon' => 'fa-images', 'color' => '#d97706'],
                                            ['url' => 'qris-embed', 'name' => 'QRIS Infaq Digital', 'icon' => 'fa-qrcode', 'color' => '#0f172a'],
                                            ['url' => 'live-mekah-embed', 'name' => 'Live TV Makkah (Ka\'bah)', 'icon' => 'fa-kaaba', 'color' => '#7c3aed'],
                                            ['url' => 'live-madinah-embed', 'name' => 'Live TV Madinah (Nabawi)', 'icon' => 'fa-mosque', 'color' => '#16a34a'],
                                            ['url' => 'idul-fitri-embed', 'name' => 'Petugas Idul Fitri', 'icon' => 'fa-star-and-crescent', 'color' => '#c9a03d'],
                                            ['url' => 'idul-adha-embed', 'name' => 'Petugas Idul Adha', 'icon' => 'fa-moon', 'color' => '#1e5a3a'],
                                        ];
                                    @endphp

                                    @foreach($slides as $s)
                                    <div class="col-md-4 col-sm-6 mb-2">
                                        <a href="{{ url($s['url']) }}" target="_blank" class="d-flex align-items-center p-2 rounded text-decoration-none slide-pill" style="background: #ffffff; border: 1px solid rgba(0,0,0,0.08); transition: all 0.2s ease;">
                                            <div style="width: 32px; height: 32px; border-radius: 8px; background: {{ $s['color'] }}15; color: {{ $s['color'] }}; display: flex; align-items: center; justify-content: center; font-size: 14px; margin-right: 10px; flex-shrink: 0;">
                                                <i class="fas {{ $s['icon'] }}"></i>
                                            </div>
                                            <div class="overflow-hidden" style="flex: 1;">
                                                <div class="small font-weight-bold text-truncate" style="color: #1f2937;">{{ $s['name'] }}</div>
                                                <div style="font-size: 10px; color: #6b7280;">/{{ $s['url'] }}</div>
                                            </div>
                                            <i class="fas fa-external-link-alt text-muted ml-2" style="font-size: 11px;"></i>
                                        </a>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- 4. PEMBAGIAN HAK AKSES PENGGUNA (RBAC) -->
                <section class="mb-5">
                    <div class="d-flex align-items-center mb-3">
                        <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(30,90,58,0.1); color: #1e5a3a; display: flex; align-items: center; justify-content: center; font-size: 18px; margin-right: 12px;">
                            <i class="fas fa-users-cog"></i>
                        </div>
                        <div>
                            <h4 class="font-weight-bold m-0" style="color: #0e3521;">Pembagian Hak Akses Pengguna (RBAC)</h4>
                            <p class="small text-muted mb-0">Keamanan data terstruktur sesuai bidang tugas kepengurusan DKM</p>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Super Admin -->
                        <div class="col-lg-4 mb-3">
                            <div class="card h-100 shadow-sm" style="border-radius: 16px; border-top: 4px solid #1e5a3a; border-left: none; border-right: none; border-bottom: none;">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center mb-3">
                                        <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(30,90,58,0.1); color: #1e5a3a; display: flex; align-items: center; justify-content: center; font-size: 18px; margin-right: 10px;">
                                            <i class="fas fa-user-shield"></i>
                                        </div>
                                        <div>
                                            <h6 class="font-weight-bold m-0" style="color: #0e3521;">Administrator</h6>
                                            <span class="badge badge-success" style="font-size: 10px;">Akses Penuh (Full Control)</span>
                                        </div>
                                    </div>
                                    <ul class="small text-muted pl-3 mb-0" style="line-height: 1.8;">
                                        <li>Kelola akun pengguna & pembagian peran (RBAC)</li>
                                        <li>Pengaturan urutan putaran slide TV (Naik/Turun)</li>
                                        <li>Konfigurasi identitas masjid, logo, & audio tarhim</li>
                                        <li>Integrasi link Live Makkah, Madinah, & CCTV Mimbar</li>
                                        <li>Tombol 1-klik sinkronisasi database server (`/settings/migrate`)</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Petugas Operator -->
                        <div class="col-lg-4 mb-3">
                            <div class="card h-100 shadow-sm" style="border-radius: 16px; border-top: 4px solid #c9a03d; border-left: none; border-right: none; border-bottom: none;">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center mb-3">
                                        <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(201,160,61,0.15); color: #c9a03d; display: flex; align-items: center; justify-content: center; font-size: 18px; margin-right: 10px;">
                                            <i class="fas fa-user-edit"></i>
                                        </div>
                                        <div>
                                            <h6 class="font-weight-bold m-0" style="color: #0e3521;">Petugas / Operator</h6>
                                            <span class="badge badge-warning" style="font-size: 10px; color: #1e5a3a;">Operasional Harian</span>
                                        </div>
                                    </div>
                                    <ul class="small text-muted pl-3 mb-0" style="line-height: 1.8;">
                                        <li>Penyesuaian jadwal sholat & waktu jeda iqamah</li>
                                        <li>Petugas Sholat Jum'at, Idul Fitri, & Idul Adha</li>
                                        <li>Pengumuman teks berjalan & jadwal agenda taklim</li>
                                        <li>Upload poster & brosur kegiatan dakwah masjid</li>
                                        <li>Aktif/nonaktifkan slide TV (urutan terproteksi)</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Bendahara -->
                        <div class="col-lg-4 mb-3">
                            <div class="card h-100 shadow-sm" style="border-radius: 16px; border-top: 4px solid #0284c7; border-left: none; border-right: none; border-bottom: none;">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center mb-3">
                                        <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(2,132,199,0.1); color: #0284c7; display: flex; align-items: center; justify-content: center; font-size: 18px; margin-right: 10px;">
                                            <i class="fas fa-wallet"></i>
                                        </div>
                                        <div>
                                            <h6 class="font-weight-bold m-0" style="color: #0e3521;">Bendahara</h6>
                                            <span class="badge badge-info" style="font-size: 10px;">Manajemen Keuangan</span>
                                        </div>
                                    </div>
                                    <ul class="small text-muted pl-3 mb-0" style="line-height: 1.8;">
                                        <li>Pencatatan Buku Kas Utama (infaq tromol & operasional)</li>
                                        <li>Pencatatan Kas Operasional Mobil Ambulance Masjid</li>
                                        <li>Program penggalangan donasi & target infaq terarah</li>
                                        <li>Pemantauan grafik ringkasan keuangan di TV</li>
                                        <li>Ekspor pembukuan kas resmi ke format PDF & Excel</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- 5. ARSITEKTUR TEKNOLOGI -->
                <section class="mb-5">
                    <div class="d-flex align-items-center mb-3">
                        <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(30,90,58,0.1); color: #1e5a3a; display: flex; align-items: center; justify-content: center; font-size: 18px; margin-right: 12px;">
                            <i class="fas fa-microchip"></i>
                        </div>
                        <h4 class="font-weight-bold m-0" style="color: #0e3521;">Teknologi & Standar Kinerja</h4>
                    </div>

                    <div class="row">
                        <div class="col-md-6 col-lg-3 mb-3">
                            <div class="card shadow-sm h-100 text-center p-3" style="border-radius: 14px; border: 1px solid rgba(0,0,0,0.06);">
                                <div class="mb-2" style="font-size: 32px; color: #ff2d20;"><i class="fab fa-laravel"></i></div>
                                <h6 class="font-weight-bold mb-1" style="color: #0e3521;">Laravel 13 & PHP 8.3</h6>
                                <p class="small text-muted mb-0">Backend handal, aman, & responsif dengan arsitektur MVC modern</p>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3 mb-3">
                            <div class="card shadow-sm h-100 text-center p-3" style="border-radius: 14px; border: 1px solid rgba(0,0,0,0.06);">
                                <div class="mb-2" style="font-size: 32px; color: #1e5a3a;"><i class="fas fa-gem"></i></div>
                                <h6 class="font-weight-bold mb-1" style="color: #0e3521;">Islamic Material Design 3</h6>
                                <p class="small text-muted mb-0">Antarmuka mewah terinspirasi Google Material You & estetika Islami</p>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3 mb-3">
                            <div class="card shadow-sm h-100 text-center p-3" style="border-radius: 14px; border: 1px solid rgba(0,0,0,0.06);">
                                <div class="mb-2" style="font-size: 32px; color: #0284c7;"><i class="fas fa-bolt"></i></div>
                                <h6 class="font-weight-bold mb-1" style="color: #0e3521;">GPU Accelerated Engine</h6>
                                <p class="small text-muted mb-0">Animasi 60 FPS diproses oleh GPU, sangat ramah pada Android TV Box</p>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3 mb-3">
                            <div class="card shadow-sm h-100 text-center p-3" style="border-radius: 14px; border: 1px solid rgba(0,0,0,0.06);">
                                <div class="mb-2" style="font-size: 32px; color: #4479a1;"><i class="fas fa-database"></i></div>
                                <h6 class="font-weight-bold mb-1" style="color: #0e3521;">MySQL / MariaDB</h6>
                                <p class="small text-muted mb-0">Penyimpanan basis data relasional dengan perlindungan migrasi otomatis</p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- 6. PANDUAN PENGGUNAAN LENGKAP -->
                <section class="mb-5">
                    <div class="d-flex align-items-center mb-3">
                        <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(30,90,58,0.1); color: #1e5a3a; display: flex; align-items: center; justify-content: center; font-size: 18px; margin-right: 12px;">
                            <i class="fas fa-book-open"></i>
                        </div>
                        <h4 class="font-weight-bold m-0" style="color: #0e3521;">Panduan Pengoperasian Sistem</h4>
                    </div>

                    <div class="accordion" id="usageGuide">
                        <!-- Panduan 1: Display TV -->
                        <div class="card shadow-sm mb-2" style="border-radius: 12px; border: 1px solid rgba(30,90,58,0.1); overflow: hidden;">
                            <div class="card-header py-3" id="headingOne" style="background: rgba(30,90,58,0.03);">
                                <h6 class="mb-0">
                                    <button class="btn btn-link w-100 text-left d-flex justify-content-between align-items-center" type="button" data-toggle="collapse" data-target="#collapseOne" style="color: #0e3521; text-decoration: none; font-weight: 600;">
                                        <span><i class="fas fa-tv mr-2" style="color: #c9a03d;"></i>1. Menampilkan Sistem di Layar TV (Display TV Dalam & Luar)</span>
                                        <i class="fas fa-chevron-down small text-muted"></i>
                                    </button>
                                </h6>
                            </div>
                            <div id="collapseOne" class="collapse show" data-parent="#usageGuide">
                                <div class="card-body small" style="line-height: 1.8; color: #374151;">
                                    <p class="mb-2"><strong>A. Layar TV Utama (Di Dalam Masjid):</strong></p>
                                    <ol class="pl-3 mb-3">
                                        <li>Buka aplikasi browser (Chrome / Edge / Browser TV) pada perangkat Smart TV atau Android Box yang terhubung ke TV.</li>
                                        <li>Ketikkan alamat website utama masjid (contoh: <code>{{ url('/') }}</code> atau <code>{{ url('/rotator') }}</code>).</li>
                                        <li>Tekan tombol <strong>F11</strong> pada keyboard (atau aktifkan mode <em>Fullscreen / Kiosk</em> di browser TV) untuk menyembunyikan address bar.</li>
                                        <li>Layar akan otomatis berputar menampilkan jadwal sholat, pengumuman, siaran live, dan laporan keuangan secara bergantian.</li>
                                    </ol>
                                    <p class="mb-2"><strong>B. Layar TV Luar / Serambi Masjid:</strong></p>
                                    <ol class="pl-3 mb-3">
                                        <li>Gunakan alamat khusus TV Luar: <code>{{ url('/tv-outdoor') }}</code>.</li>
                                        <li>Pada hari dan jam biasa, layar TV luar berputar menampilkan informasi umum seperti TV utama.</li>
                                        <li>Saat waktu Khutbah Jum'at atau Sholat Ied tiba, layar TV luar akan <strong>secara otomatis beralih</strong> menampilkan siaran langsung CCTV Mimbar agar jamaah di luar dapat menyimak khutbah dengan jelas.</li>
                                    </ol>
                                    <p class="mb-2"><strong>C. Otomasi Mandiri Hemat Daya (Full Auto Self-Running):</strong></p>
                                    <p class="mb-0 text-muted">
                                        Demi efisiensi konsumsi daya listrik serta kepraktisan waktu pengoperasian, rangkaian TV ini telah terintegrasi dengan <strong>Smart Breaker</strong> dan <strong>Smart IR Remote Control</strong>. Sistem TV dapat menyala (<em>power ON</em>) dan mati/standby (<em>power OFF</em>) secara otomatis dan terjadwal, sehingga beroperasi penuh secara mandiri (*full auto self running*) tanpa perlu campur tangan manual marbot atau pengurus setiap hari.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Panduan 2: Mode Sholat Otomatis -->
                        <div class="card shadow-sm mb-2" style="border-radius: 12px; border: 1px solid rgba(30,90,58,0.1); overflow: hidden;">
                            <div class="card-header py-3" id="headingTwo" style="background: rgba(30,90,58,0.03);">
                                <h6 class="mb-0">
                                    <button class="btn btn-link collapsed w-100 text-left d-flex justify-content-between align-items-center" type="button" data-toggle="collapse" data-target="#collapseTwo" style="color: #0e3521; text-decoration: none; font-weight: 600;">
                                        <span><i class="fas fa-mosque mr-2" style="color: #c9a03d;"></i>2. Alur Kerja Otomatis Mode Sholat (Prayer Mode & Khutbah)</span>
                                        <i class="fas fa-chevron-down small text-muted"></i>
                                    </button>
                                </h6>
                            </div>
                            <div id="collapseTwo" class="collapse" data-parent="#usageGuide">
                                <div class="card-body small" style="line-height: 1.8; color: #374151;">
                                    <p class="mb-2">Sistem bekerja mandiri mengunci rotasi TV saat masuk waktu ibadah:</p>
                                    <ol class="pl-3 mb-0">
                                        <li class="mb-2"><strong>Fase Menjelang Adzan (Tarhim):</strong> Audio tarhim/murottal dapat berputar otomatis beberapa menit sebelum adzan sebagai pengingat jamaah.</li>
                                        <li class="mb-2"><strong>Fase Adzan:</strong> Layar menghentikan perputaran slide dan menampilkan pengingat adzan berkumandang.</li>
                                        <li class="mb-2"><strong>Fase Iqamah:</strong> Menampilkan hitungan mundur jeda sholat sunnah hingga iqamah ditegakkan.</li>
                                        <li class="mb-2"><strong>Fase Sholat Berjamaah:</strong> Layar TV otomatis menjadi gelap syahdu bertuliskan <em>"Luruskan dan Rapatkan Shaf Anda"</em> agar tidak mengganggu kekhusyukan jamaah.</li>
                                        <li class="mb-2"><strong>Khusus Sholat Jum'at:</strong> Layar menampilkan 4 kartu petugas resmi (Khatib, Imam, Muadzin, Bilal) serta plakat hadits adab mendengarkan khutbah selama durasi khutbah berlangsung.</li>
                                        <li><strong>Selesai Sholat:</strong> Setelah waktu ibadah usai, layar TV otomatis kembali berotasi menampilkan informasi masjid seperti semula.</li>
                                    </ol>
                                </div>
                            </div>
                        </div>

                        <!-- Panduan 3: Sinkronisasi Database -->
                        <div class="card shadow-sm mb-2" style="border-radius: 12px; border: 1px solid rgba(30,90,58,0.1); overflow: hidden;">
                            <div class="card-header py-3" id="headingThree" style="background: rgba(30,90,58,0.03);">
                                <h6 class="mb-0">
                                    <button class="btn btn-link collapsed w-100 text-left d-flex justify-content-between align-items-center" type="button" data-toggle="collapse" data-target="#collapseThree" style="color: #0e3521; text-decoration: none; font-weight: 600;">
                                        <span><i class="fas fa-sync-alt mr-2" style="color: #c9a03d;"></i>3. Pemeliharaan & Sinkronisasi Database 1-Klik (`/settings/migrate`)</span>
                                        <i class="fas fa-chevron-down small text-muted"></i>
                                    </button>
                                </h6>
                            </div>
                            <div id="collapseThree" class="collapse" data-parent="#usageGuide">
                                <div class="card-body small" style="line-height: 1.8; color: #374151;">
                                    <p class="mb-2">Saat sistem mendapatkan pembaruan fitur (misal: kolom durasi Jum'at baru, pengaturan CCTV, dll.), administrator tidak perlu membuka terminal SSH atau hosting:</p>
                                    <ul class="pl-3 mb-0">
                                        <li class="mb-2">Buka menu <strong>Pengaturan Aplikasi</strong> di sidebar.</li>
                                        <li class="mb-2">Klik tombol <strong>"Sinkronkan Database (Migrate)"</strong> di pojok kanan atas halaman.</li>
                                        <li>Sistem akan mengeksekusi migrasi database secara otomatis dan menampilkan notifikasi sukses tanpa risiko kehilangan data yang sudah tersimpan.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- 7. RIWAYAT VERSI (CHANGELOG RESMI) -->
                <section class="mb-5">
                    <div class="d-flex align-items-center mb-3">
                        <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(30,90,58,0.1); color: #1e5a3a; display: flex; align-items: center; justify-content: center; font-size: 18px; margin-right: 12px;">
                            <i class="fas fa-history"></i>
                        </div>
                        <h4 class="font-weight-bold m-0" style="color: #0e3521;">Riwayat Pembaruan Sistem</h4>
                    </div>

                    <div class="card shadow-sm border-0" style="border-radius: 16px; background: #ffffff; border: 1px solid rgba(0,0,0,0.08) !important;">
                        <div class="card-body p-4">
                            <ul class="small mb-0" style="line-height: 1.8; list-style: none; padding-left: 0;">
                                
                                <!-- Versi 4.0.0 -->
                                <li class="mb-4 pb-3 border-bottom position-relative pl-4" style="border-left: 3px solid #1e5a3a;">
                                    <div class="d-flex align-items-center mb-2 flex-wrap">
                                        <span class="badge px-3 py-1 mr-2" style="background: #1e5a3a; color: #ffd700; font-weight: 700; font-size: 12px; border-radius: 8px;">
                                            Versi 4.0.0 (Terbaru)
                                        </span>
                                        <span class="text-muted font-weight-bold">14 September 2026</span>
                                    </div>
                                    <p class="font-weight-bold mb-1" style="color: #0e3521;">Grand Redesign: Islamic Material Design 3 & Total Feature Sync</p>
                                    <ul class="pl-3 mb-0 text-muted">
                                        <li>Redesain panel admin dengan filosofi Islamic Material Design 3 (Sidebar 260px, Islamic Hex Pattern, Nav Icon Chips).</li>
                                        <li>Penambahan <strong>Live Digital Clock Widget</strong> & <strong>Realtime Prayer Pill</strong> pada header admin topbar.</li>
                                        <li>Pembaruan menyeluruh modul <strong>Tentang Aplikasi</strong> agar 100% selaras dengan 13 saluran slide, TV Luar & CCTV mimbar.</li>
                                        <li>Perlindungan performa GPU 60 FPS murni tanpa tambahan library pihak ketiga (Zero New Heavy Libraries).</li>
                                    </ul>
                                </li>

                                <!-- Versi 3.0.4 -->
                                <li class="mb-4 pb-3 border-bottom position-relative pl-4" style="border-left: 3px solid #c9a03d;">
                                    <div class="d-flex align-items-center mb-2 flex-wrap">
                                        <span class="badge px-3 py-1 mr-2" style="background: #c9a03d; color: #071a10; font-weight: 700; font-size: 12px; border-radius: 8px;">
                                            Versi 3.0.4
                                        </span>
                                        <span class="text-muted font-weight-bold">September 2026</span>
                                    </div>
                                    <p class="font-weight-bold mb-1" style="color: #0e3521;">Fitur TV Luar, CCTV Mimbar & Sinkronisasi Hosting</p>
                                    <ul class="pl-3 mb-0 text-muted">
                                        <li>Penambahan Laporan Kas Ambulance & Program Penggalangan Infaq Pembangunan.</li>
                                        <li>Penambahan fitur Reorder Urutan Rotasi Halaman (Tombol Naik/Turun) berbasis RBAC.</li>
                                        <li>Penambahan Live Streaming Makkah & Madinah dengan Smart Overlay jam & jadwal sholat.</li>
                                        <li>Penambahan mode khusus Sholat Jum'at (`phase: khutbah`) dengan kartu 4 petugas & hadits adab khutbah.</li>
                                        <li>Integrasi CCTV Mimbar untuk Layar TV Luar/Serambi saat khutbah.</li>
                                        <li>Sistem tombol sinkronisasi migrasi database 1-klik di panel admin (`/settings/migrate`).</li>
                                    </ul>
                                </li>

                                <!-- Versi 3.0.3 -->
                                <li class="mb-4 pb-3 border-bottom position-relative pl-4" style="border-left: 3px solid #6b7280;">
                                    <div class="d-flex align-items-center mb-2 flex-wrap">
                                        <span class="badge px-3 py-1 mr-2" style="background: #6b7280; color: #ffffff; font-weight: 700; font-size: 12px; border-radius: 8px;">
                                            Versi 3.0.3
                                        </span>
                                        <span class="text-muted font-weight-bold">Agustus 2026</span>
                                    </div>
                                    <ul class="pl-3 mb-2 text-muted">
                                        <li>Implementasi awal Prayer Mode & timer hitung mundur iqamah.</li>
                                        <li>Penambahan integrasi pemutaran audio tarhim sebelum adzan.</li>
                                    </ul>
                                    <div class="p-3 rounded bg-light border" style="font-size: 12px; color: #4b5563; line-height: 1.6;">
                                        <strong>Catatan Sejarah:</strong> NDILALAH TV masjid terbeli dan diserahkan lagi. Bracket dipasang Sabtu malam minggu 5 Sept '26, langsung GAZZZ beli SamSoe 3 slop, trus lanjut ke versi 3.0.4!
                                    </div>
                                </li>

                                <!-- Versi 3.0.2 - 1.0.0 -->
                                <li class="position-relative pl-4" style="border-left: 3px solid #cbd5e1;">
                                    <span class="text-muted font-weight-bold">Versi 1.0.0 – 3.0.2 (Juli – Agustus 2026)</span>
                                    <p class="text-muted mb-0">Inisiasi konsep dasar, pengembangan tampilan awal, transisi layout modern, dan penyusunan modul kas & jadwal sholat.</p>
                                </li>
                            </ul>
                        </div>
                    </div>
                </section>

                <!-- 8. DEVELOPER & FOOTER INFO -->
                <section class="text-center">
                    <div class="card shadow-sm border-0" style="border-radius: 16px; background: linear-gradient(135deg, #071a10 0%, #0e3521 100%); color: white;">
                        <div class="card-body py-4 px-3">
                            <div class="mb-2">
                                <i class="fas fa-mosque" style="font-size: 28px; color: #c9a03d;"></i>
                            </div>
                            <h6 class="font-weight-bold mb-1" style="font-family: 'Amiri', serif; font-size: 18px; color: #ffd700;">
                                {{ $setting->nama_aplikasi ?? 'Sistem Informasi Masjid Digital' }}
                            </h6>
                            {!! $setting->footer ?? '<p class="small mb-0 text-light opacity-80">Copyright &copy; 2026 Pengurus Masjid. All Rights Reserved.</p>' !!}
                            <div class="mt-3 pt-2 border-top border-secondary small text-muted d-flex justify-content-center flex-wrap" style="gap: 15px;">
                                <span><i class="fas fa-shield-alt text-success mr-1"></i> Status Sistem: Aktif</span>
                                <span><i class="fas fa-server text-info mr-1"></i> PHP 8.3 & Laravel 13</span>
                                <span><i class="fas fa-check-circle text-warning mr-1"></i> Teruji di Tampilan TV 1080p / 4K</span>
                            </div>
                        </div>
                    </div>
                </section>

            </div>
        </div>
    </div>
</div>

<!-- Custom Page Styling -->
<style>
    .feature-box {
        transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
    }
    .feature-box:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 25px rgba(14, 53, 33, 0.12) !important;
        border-color: rgba(201, 160, 61, 0.4) !important;
    }
    .slide-pill:hover {
        background: #f4fbf7 !important;
        border-color: #1e5a3a !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(30,90,58,0.08);
    }
    .accordion .btn-link:focus {
        outline: none;
        box-shadow: none;
    }
</style>

@endsection