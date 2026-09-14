<!-- resources/views/about.blade.php -->
@extends('layouts.admin')
@section('title', 'Tentang Aplikasi')
@section('main-content')

<div class="row justify-content-center">
    <div class="col-lg-10">
        <!-- Main Card -->
        <div class="card shadow mb-4" style="border-left: 4px solid var(--islamic-gold, #c9a03d); border-radius: 15px;">
            <!-- Card Header with Logo -->
            <div class="card-header py-3 text-center" style="background: linear-gradient(135deg, #1e5a3a, #0a2e1f); color: white; border-radius: 15px 15px 0 0;">
                <img src="{{ isset($setting['logo']) ? asset('storage/' . $setting['logo']) : asset('img/default-logo.png') }}"
                alt="Logo Aplikasi"
                class="img-fluid mb-3"
                style="max-width: 80px; border-radius: 50%; border: 3px solid #c9a03d; padding: 5px; background: white;">
                <h4 class="m-0 font-weight-bold" style="font-family: 'Amiri', serif;">{{ $setting->nama_aplikasi ?? 'Sistem Informasi Masjid Digital' }}</h4>
                <p class="mb-0 small" style="opacity: 0.9;">Solusi Digital untuk Manajemen Masjid dengan Tampilan TV Dinamis</p>
            </div>

            <!-- Card Body -->
            <div class="card-body">
                <!-- Introduction -->
                <section class="mb-5">
                    <h5 class="font-weight-bold mb-3" style="color: #1e5a3a; border-left: 4px solid #c9a03d; padding-left: 15px;">
                        <i class="fas fa-info-circle mr-2" style="color: #c9a03d;"></i>Tentang Aplikasi Ini
                    </h5>
                    <p class="text-justify">
                        <strong>{{ $setting->nama_aplikasi ?? 'Sistem Informasi Masjid Digital' }}</strong> adalah solusi terintegrasi berbasis web untuk manajemen masjid modern yang dibangun dengan Laravel 12. Aplikasi ini menampilkan informasi masjid secara real-time pada layar TV digital dengan desain yang dinamis dan responsif.
                    </p>
                    <p class="text-justify">
                        Sistem ini dirancang khusus untuk menampilkan jadwal sholat, pengumuman, informasi keuangan, dan kegiatan masjid secara digital dengan tampilan yang elegan dan mudah dibaca dari jarak jauh. Dilengkapi dengan antarmuka admin yang lengkap untuk mengelola semua konten yang ditampilkan.
                    </p>
                </section>

                <!-- Main Features -->
                <section class="mb-5">
                    <h5 class="font-weight-bold mb-3" style="color: #1e5a3a; border-left: 4px solid #c9a03d; padding-left: 15px;">
                        <i class="fas fa-star-of-life mr-2" style="color: #c9a03d;"></i>Fitur Utama
                    </h5>
                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 shadow-sm" style="border-left: 4px solid #1e5a3a; border-radius: 10px;">
                                <div class="card-body">
                                    <h6 class="font-weight-bold" style="color: #1e5a3a;"><i class="fas fa-tv mr-2" style="color: #c9a03d;"></i>Tampilan TV Dinamis</h6>
                                    <p class="small text-justify">
                                        Menampilkan informasi masjid secara real-time pada layar TV dengan desain modern dan animasi yang menarik.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 shadow-sm" style="border-left: 4px solid #1e5a3a; border-radius: 10px;">
                                <div class="card-body">
                                    <h6 class="font-weight-bold" style="color: #1e5a3a;"><i class="fas fa-clock mr-2" style="color: #c9a03d;"></i>Jadwal Sholat Otomatis</h6>
                                    <p class="small text-justify">
                                        Menampilkan jadwal sholat dengan waktu yang akurat dan update otomatis sesuai lokasi masjid.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 shadow-sm" style="border-left: 4px solid #1e5a3a; border-radius: 10px;">
                                <div class="card-body">
                                    <h6 class="font-weight-bold" style="color: #1e5a3a;"><i class="fas fa-hand-holding-heart mr-2" style="color: #c9a03d;"></i>Laporan Keuangan</h6>
                                    <p class="small text-justify">
                                        Menampilkan informasi keuangan masjid secara transparan dengan grafik dan detail pemasukan/pengeluaran.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 shadow-sm" style="border-left: 4px solid #1e5a3a; border-radius: 10px;">
                                <div class="card-body">
                                    <h6 class="font-weight-bold" style="color: #1e5a3a;"><i class="fas fa-bullhorn mr-2" style="color: #c9a03d;"></i>Pengumuman Digital</h6>
                                    <p class="small text-justify">
                                        Menampilkan pengumuman penting masjid secara bergulir dengan efek animasi yang menarik.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 shadow-sm" style="border-left: 4px solid #1e5a3a; border-radius: 10px;">
                                <div class="card-body">
                                    <h6 class="font-weight-bold" style="color: #1e5a3a;"><i class="fas fa-sync-alt mr-2" style="color: #c9a03d;"></i>Auto-Update Jadwal</h6>
                                    <p class="small text-justify">
                                        Jadwal sholat diperbarui secara otomatis melalui API eksternal sesuai lokasi yang ditentukan.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 shadow-sm" style="border-left: 4px solid #1e5a3a; border-radius: 10px;">
                                <div class="card-body">
                                    <h6 class="font-weight-bold" style="color: #1e5a3a;"><i class="fas fa-exchange-alt mr-2" style="color: #c9a03d;"></i>Rotasi Halaman Dinamis</h6>
                                    <p class="small text-justify">
                                        Halaman tampilan TV berganti secara otomatis dengan interval yang dapat diatur sesuai kebutuhan.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- TV Display Features -->
                <section class="mb-5">
                    <h5 class="font-weight-bold mb-3" style="color: #1e5a3a; border-left: 4px solid #c9a03d; padding-left: 15px;">
                        <i class="fas fa-tv mr-2" style="color: #c9a03d;"></i>Fitur Tampilan TV
                    </h5>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="card shadow-sm h-100" style="border-radius: 10px;">
                                <div class="card-body text-center">
                                    <div style="background: linear-gradient(135deg, #1e5a3a, #0a2e1f); color: white; border-radius: 50%; width: 50px; height: 50px; line-height: 50px; font-size: 20px; margin: 0 auto 15px;">1</div>
                                    <h6 class="font-weight-bold" style="color: #1e5a3a;">Tampilan Waktu</h6>
                                    <p class="small">Menampilkan waktu sholat, tanggal Hijriah & Masehi secara real-time</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card shadow-sm h-100" style="border-radius: 10px;">
                                <div class="card-body text-center">
                                    <div style="background: linear-gradient(135deg, #1e5a3a, #0a2e1f); color: white; border-radius: 50%; width: 50px; height: 50px; line-height: 50px; font-size: 20px; margin: 0 auto 15px;">2</div>
                                    <h6 class="font-weight-bold" style="color: #1e5a3a;">Running Text</h6>
                                    <p class="small">Pengumuman bergulir dengan kecepatan yang dapat disesuaikan</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card shadow-sm h-100" style="border-radius: 10px;">
                                <div class="card-body text-center">
                                    <div style="background: linear-gradient(135deg, #1e5a3a, #0a2e1f); color: white; border-radius: 50%; width: 50px; height: 50px; line-height: 50px; font-size: 20px; margin: 0 auto 15px;">3</div>
                                    <h6 class="font-weight-bold" style="color: #1e5a3a;">Animasi Kaligrafi</h6>
                                    <p class="small">Elemen kaligrafi Islami dengan efek visual yang indah</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card shadow-sm h-100" style="border-radius: 10px;">
                                <div class="card-body text-center">
                                    <div style="background: linear-gradient(135deg, #1e5a3a, #0a2e1f); color: white; border-radius: 50%; width: 50px; height: 50px; line-height: 50px; font-size: 20px; margin: 0 auto 15px;">4</div>
                                    <h6 class="font-weight-bold" style="color: #1e5a3a;">Rotasi Otomatis</h6>
                                    <p class="small">Halaman berganti otomatis dengan interval yang dapat diatur</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- New Feature: Rotation Display -->
                <section class="mb-5">
                    <h5 class="font-weight-bold mb-3" style="color: #1e5a3a; border-left: 4px solid #c9a03d; padding-left: 15px;">
                        <i class="fas fa-exchange-alt mr-2" style="color: #c9a03d;"></i>Fitur Rotasi Halaman Dinamis
                    </h5>
                    <div class="card shadow-sm" style="border-left: 4px solid #c9a03d; border-radius: 10px;">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="font-weight-bold" style="color: #1e5a3a;">Apa itu Rotasi Halaman?</h6>
                                    <p class="small">
                                        Fitur ini memungkinkan tampilan TV untuk berganti secara otomatis antara beberapa halaman yang berbeda. 
                                        Anda dapat mengatur halaman mana saja yang ingin ditampilkan dan berapa lama waktu pergantiannya.
                                    </p>
                                    <h6 class="font-weight-bold mt-3" style="color: #1e5a3a;">Keuntungan:</h6>
                                    <ul class="small">
                                        <li>Informasi lebih lengkap dan bervariasi</li>
                                        <li>Tampilan tidak monoton</li>
                                        <li>Dapat menampilkan lebih banyak informasi</li>
                                        <li>Mudah diatur melalui panel admin</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <div style="background: linear-gradient(135deg, rgba(30,90,58,0.05), rgba(201,160,61,0.05); padding: 15px; border-radius: 10px;">
                                        <h6 class="font-weight-bold" style="color: #1e5a3a;">Cara Mengatur:</h6>
                                        <ol class="small">
                                            <li>Buka menu <strong>Rotasi Halaman</strong> di sidebar</li>
                                            <li>Aktifkan fitur rotasi dengan toggle switch</li>
                                            <li>Atur interval waktu pergantian (1-3600 detik)</li>
                                            <li>Pilih halaman yang ingin ditampilkan</li>
                                            <li>Klik Simpan Pengaturan</li>
                                            <li>Buka halaman utama untuk melihat hasilnya</li>
                                        </ol>
                                        <div class="alert mt-2 mb-0" style="background: linear-gradient(135deg, #1e5a3a, #0a2e1f); color: white; border-radius: 10px;">
                                            <i class="fas fa-info-circle"></i> <strong>Info:</strong> Perubahan pengaturan akan langsung diterapkan tanpa perlu refresh browser!
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr class="my-3">
                            <div class="row text-center">
                                <div class="col-4">
                                    <span class="badge p-2" style="background: #1e5a3a; color: white;">Dashboard Lengkap</span>
                                </div>
                                <div class="col-4">
                                    <span class="badge p-2" style="background: #c9a03d; color: #1e5a3a;">Jadwal Sholat</span>
                                </div>
                                <div class="col-4">
                                    <span class="badge p-2" style="background: #1e5a3a; color: white;">Rincian Keuangan</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- User Roles & Access -->
                <section class="mb-5">
                    <h5 class="font-weight-bold mb-3" style="color: #1e5a3a; border-left: 4px solid #c9a03d; padding-left: 15px;">
                        <i class="fas fa-users-cog mr-2" style="color: #c9a03d;"></i>Pembagian Hak Akses Pengguna
                    </h5>
                    <div class="card shadow-sm" style="border-radius: 10px;">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <div class="p-3 h-100 rounded" style="background: rgba(30,90,58,0.04); border-left: 4px solid #1e5a3a;">
                                        <h6 class="font-weight-bold" style="color: #1e5a3a;">
                                            <i class="fas fa-user-edit mr-2" style="color: #c9a03d;"></i>Hak Akses: Petugas / Operator Masjid
                                        </h6>
                                        <p class="small text-muted mb-2">Bertanggung jawab atas manajemen konten operasional dan tampilan display masjid:</p>
                                        <ul class="small mb-0 pl-3">
                                            <li>Pengaturan waktu dan penyesuaian jadwal sholat 5 waktu</li>
                                            <li>Pengisian data petugas Sholat Jum'at, Idul Fitri, dan Idul Adha (Khatib, Imam, Muadzin, Bilal)</li>
                                            <li>Pengelolaan pengumuman teks berjalan (*running text*) dan agenda kajian rutin</li>
                                            <li>Upload slide gambar dan poster brosur kegiatan dakwah masjid</li>
                                            <li>Pemilihan halaman yang aktif pada putaran rotasi layar TV</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="p-3 h-100 rounded" style="background: rgba(201,160,61,0.06); border-left: 4px solid #c9a03d;">
                                        <h6 class="font-weight-bold" style="color: #1e5a3a;">
                                            <i class="fas fa-wallet mr-2" style="color: #c9a03d;"></i>Hak Akses: Bendahara Masjid
                                        </h6>
                                        <p class="small text-muted mb-2">Bertanggung jawab atas transparansi dan akuntabilitas keuangan masjid:</p>
                                        <ul class="small mb-0 pl-3">
                                            <li>Pencatatan buku kas utama (pemasukan infaq/shodaqoh & pengeluaran operasional)</li>
                                            <li>Pencatatan kas operasional dan pemeliharaan mobil ambulance masjid</li>
                                            <li>Pengelolaan program penggalangan dana / infaq pembangunan khusus</li>
                                            <li>Pemantauan grafik ringkasan keuangan dan ekspor laporan kas untuk jamaah</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Technologies Used -->
                <section class="mb-5">
                    <h5 class="font-weight-bold mb-3" style="color: #1e5a3a; border-left: 4px solid #c9a03d; padding-left: 15px;">
                        <i class="fas fa-microchip mr-2" style="color: #c9a03d;"></i>Teknologi yang Digunakan
                    </h5>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="card shadow-sm" style="border-radius: 10px;">
                                <div class="card-body text-center">
                                    <img src="https://cdn.simpleicons.org/laravel/FF2D20" alt="Laravel" style="height: 40px;" class="mb-2">
                                    <h6 class="font-weight-bold" style="color: #1e5a3a;">Laravel 12</h6>
                                    <p class="small">Framework PHP untuk backend</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card shadow-sm" style="border-radius: 10px;">
                                <div class="card-body text-center">
                                    <img src="https://cdn.simpleicons.org/bootstrap/7952B3" alt="Bootstrap" style="height: 40px;" class="mb-2">
                                    <h6 class="font-weight-bold" style="color: #1e5a3a;">Bootstrap 5</h6>
                                    <p class="small">Framework CSS untuk frontend</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card shadow-sm" style="border-radius: 10px;">
                                <div class="card-body text-center">
                                    <img src="{{ asset('img/jquery.svg') }}" alt="jQuery" style="height: 40px;" class="mb-2">
                                    <h6 class="font-weight-bold" style="color: #1e5a3a;">jQuery & AJAX</h6>
                                    <p class="small">Untuk update data real-time tanpa refresh</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card shadow-sm" style="border-radius: 10px;">
                                <div class="card-body text-center">
                                    <img src="https://cdn.simpleicons.org/mysql/4479A1" alt="MySQL" style="height: 40px;" class="mb-2">
                                    <h6 class="font-weight-bold" style="color: #1e5a3a;">MySQL</h6>
                                    <p class="small">Database management system</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Quick Guide -->
                <section class="mb-5">
                    <h5 class="font-weight-bold mb-3" style="color: #1e5a3a; border-left: 4px solid #c9a03d; padding-left: 15px;">
                        <i class="fas fa-book-open mr-2" style="color: #c9a03d;"></i>Panduan Penggunaan
                    </h5>
                    <div class="card shadow-sm" style="border-radius: 10px;">
                        <div class="card-body">
                            <div class="accordion" id="usageGuide">

                                <!-- Panduan 1: Display TV -->
                                <div class="card shadow-none border mb-2" style="border-radius: 8px;">
                                    <div class="card-header py-2" id="headingOne" style="background: rgba(30,90,58,0.03);">
                                        <h6 class="mb-0">
                                            <button class="btn btn-link w-100 text-left d-flex justify-content-between align-items-center" type="button" data-toggle="collapse" data-target="#collapseOne" style="color: #1e5a3a; text-decoration: none; font-weight: 600;">
                                                <span><i class="fas fa-tv mr-2" style="color: #c9a03d;"></i>1. Menampilkan Sistem di Layar TV (Display TV)</span>
                                                <i class="fas fa-chevron-down small text-muted"></i>
                                            </button>
                                        </h6>
                                    </div>
                                    <div id="collapseOne" class="collapse show" data-parent="#usageGuide">
                                        <div class="card-body small" style="line-height: 1.8; color: #374151;">
                                            <p class="mb-2"><strong>A. Layar TV Utama (Di Dalam Masjid):</strong></p>
                                            <ol class="pl-3 mb-3">
                                                <li>Buka aplikasi browser (Chrome / Edge / Browser TV) pada perangkat Smart TV atau Mini PC yang terhubung ke TV.</li>
                                                <li>Ketikkan alamat website utama masjid (contoh: <code>https://digital-aljihad.onrender.com/</code>).</li>
                                                <li>Tekan tombol <strong>F11</strong> pada keyboard (atau aktifkan mode <em>Fullscreen / Kiosk</em> di browser TV) untuk menyembunyikan address bar.</li>
                                                <li>Layar akan otomatis berputar menampilkan jadwal sholat, pengumuman, dan laporan keuangan secara bergantian.</li>
                                            </ol>
                                            <p class="mb-2"><strong>B. Layar TV Luar / Serambi Masjid:</strong></p>
                                            <ol class="pl-3 mb-0">
                                                <li>Gunakan alamat khusus TV Luar: <code>https://digital-aljihad.onrender.com/tv-outdoor</code>.</li>
                                                <li>Pada hari dan jam biasa, layar TV luar berputar menampilkan informasi umum seperti TV utama.</li>
                                                <li>Saat waktu Khutbah Jum'at atau Sholat Ied tiba, layar TV luar akan <strong>otomatis beralih</strong> menampilkan siaran langsung CCTV Mimbar agar jamaah di luar dapat menyimak khutbah dengan jelas.</li>
                                            </ol>
                                        </div>
                                    </div>
                                </div>

                                <!-- Panduan 2: Panduan Operator -->
                                <div class="card shadow-none border mb-2" style="border-radius: 8px;">
                                    <div class="card-header py-2" id="headingTwo" style="background: rgba(30,90,58,0.03);">
                                        <h6 class="mb-0">
                                            <button class="btn btn-link collapsed w-100 text-left d-flex justify-content-between align-items-center" type="button" data-toggle="collapse" data-target="#collapseTwo" style="color: #1e5a3a; text-decoration: none; font-weight: 600;">
                                                <span><i class="fas fa-user-edit mr-2" style="color: #c9a03d;"></i>2. Panduan Operasional Petugas / Operator Masjid</span>
                                                <i class="fas fa-chevron-down small text-muted"></i>
                                            </button>
                                        </h6>
                                    </div>
                                    <div id="collapseTwo" class="collapse" data-parent="#usageGuide">
                                        <div class="card-body small" style="line-height: 1.8; color: #374151;">
                                            <ul class="pl-3 mb-0">
                                                <li class="mb-2"><strong>Mengatur Jadwal Sholat:</strong> Buka menu <em>Jadwal Sholat</em> untuk menyesuaikan waktu sholat 5 waktu, waktu syuruq, serta durasi jeda hitung mundur iqamah tiap sholat.</li>
                                                <li class="mb-2"><strong>Petugas Sholat Jum'at & Hari Raya:</strong> Buka menu <em>Sholat Jum'at</em>, <em>Idul Fitri</em>, atau <em>Idul Adha</em> untuk memperbarui nama Khatib, Imam, Muadzin, dan Bilal yang bertugas setiap minggunya.</li>
                                                <li class="mb-2"><strong>Membuat Pengumuman:</strong> Buka menu <em>Pengumuman</em> untuk menambah pesan teks penting. Pengumuman aktif akan otomatis muncul pada teks berjalan (*running text*) di bagian bawah layar TV.</li>
                                                <li class="mb-2"><strong>Agenda Kajian & Taklim:</strong> Buka menu <em>Agenda Kajian</em> untuk mencantumkan jadwal pengajian rutin, nama pemateri / ustadz, dan tema kajian.</li>
                                                <li><strong>Slide Poster Informasi:</strong> Buka menu <em>Slide Informasi</em> untuk mengunggah poster kegiatan atau brosur infaq dalam format gambar (.jpg / .png) untuk ditampilkan di TV.</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <!-- Panduan 3: Panduan Bendahara -->
                                <div class="card shadow-none border mb-2" style="border-radius: 8px;">
                                    <div class="card-header py-2" id="headingThree" style="background: rgba(30,90,58,0.03);">
                                        <h6 class="mb-0">
                                            <button class="btn btn-link collapsed w-100 text-left d-flex justify-content-between align-items-center" type="button" data-toggle="collapse" data-target="#collapseThree" style="color: #1e5a3a; text-decoration: none; font-weight: 600;">
                                                <span><i class="fas fa-hand-holding-usd mr-2" style="color: #c9a03d;"></i>3. Panduan Pengelolaan Keuangan (Khusus Bendahara)</span>
                                                <i class="fas fa-chevron-down small text-muted"></i>
                                            </button>
                                        </h6>
                                    </div>
                                    <div id="collapseThree" class="collapse" data-parent="#usageGuide">
                                        <div class="card-body small" style="line-height: 1.8; color: #374151;">
                                            <ul class="pl-3 mb-0">
                                                <li class="mb-2"><strong>Kas Utama Masjid:</strong> Buka menu <em>Keuangan</em> untuk mencatat setiap transaksi pemasukan (infaq tromol Jum'at, transfer bank, donatur tetap) dan pengeluaran operasional (listrik, air, kebersihan, insentif). Saldo akhir akan otomatis terhitung dan tersaji di layar TV.</li>
                                                <li class="mb-2"><strong>Kas Operasional Ambulance:</strong> Buka menu <em>Kas Ambulance</em> untuk mencatat sedekah ambulance dan pengeluaran biaya perawatan mobil, bahan bakar, dan honor pengemudi.</li>
                                                <li class="mb-2"><strong>Program Penggalangan Donasi/Infaq:</strong> Buka menu <em>Program Infaq</em> untuk membuat program donasi terarah (misal: Renovasi Kubah, Santunan Yatim, Karpet Baru). Masukkan target dana, dan layar TV akan menampilkan progress bar capaian donasi secara transparan.</li>
                                                <li><strong>Cetak Laporan Keuangan:</strong> Gunakan tombol <em>Ekspor / Cetak</em> pada masing-masing menu keuangan untuk mengunduh rekapitulasi pembukuan dalam format PDF atau Excel.</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <!-- Panduan 4: Rotasi Halaman TV -->
                                <div class="card shadow-none border mb-2" style="border-radius: 8px;">
                                    <div class="card-header py-2" id="headingFour" style="background: rgba(30,90,58,0.03);">
                                        <h6 class="mb-0">
                                            <button class="btn btn-link collapsed w-100 text-left d-flex justify-content-between align-items-center" type="button" data-toggle="collapse" data-target="#collapseFour" style="color: #1e5a3a; text-decoration: none; font-weight: 600;">
                                                <span><i class="fas fa-exchange-alt mr-2" style="color: #c9a03d;"></i>4. Mengatur Rotasi Slide Halaman TV</span>
                                                <i class="fas fa-chevron-down small text-muted"></i>
                                            </button>
                                        </h6>
                                    </div>
                                    <div id="collapseFour" class="collapse" data-parent="#usageGuide">
                                        <div class="card-body small" style="line-height: 1.8; color: #374151;">
                                            <ol class="pl-3 mb-0">
                                                <li class="mb-2">Buka menu <strong>Rotasi Halaman</strong> pada sidebar panel admin.</li>
                                                <li class="mb-2">Pastikan toggle <strong>Status Rotasi</strong> dalam posisi aktif.</li>
                                                <li class="mb-2">Atur <strong>Interval Rotasi</strong> (waktu tampil tiap slide, rekomendasi: 10–20 detik).</li>
                                                <li class="mb-2">Centang slide yang ingin ditampilkan (misal: Jadwal Sholat, Rincian Keuangan, Pengumuman, Live Mekah/Madinah), atau hilangkan centang jika slide sedang tidak diperlukan.</li>
                                                <li class="mb-2">Klik tombol <strong>Simpan Perubahan</strong>. Pengaturan akan langsung diterapkan di layar TV tanpa perlu me-reload browser TV.</li>
                                                <li>Gunakan tombol <strong>Preview Rotasi</strong> di pojok kanan atas untuk melihat pratinjau tampilan rotasi secara langsung di layar komputer Anda.</li>
                                            </ol>
                                        </div>
                                    </div>
                                </div>

                                <!-- Panduan 5: Prayer Mode -->
                                <div class="card shadow-none border" style="border-radius: 8px;">
                                    <div class="card-header py-2" id="headingFive" style="background: rgba(30,90,58,0.03);">
                                        <h6 class="mb-0">
                                            <button class="btn btn-link collapsed w-100 text-left d-flex justify-content-between align-items-center" type="button" data-toggle="collapse" data-target="#collapseFive" style="color: #1e5a3a; text-decoration: none; font-weight: 600;">
                                                <span><i class="fas fa-mosque mr-2" style="color: #c9a03d;"></i>5. Alur Otomatis Mode Sholat (Prayer Mode)</span>
                                                <i class="fas fa-chevron-down small text-muted"></i>
                                            </button>
                                        </h6>
                                    </div>
                                    <div id="collapseFive" class="collapse" data-parent="#usageGuide">
                                        <div class="card-body small" style="line-height: 1.8; color: #374151;">
                                            <p class="mb-2">Sistem bekerja secara otomatis mengunci dan menyesuaikan layar saat memasuki waktu ibadah:</p>
                                            <ol class="pl-3 mb-0">
                                                <li class="mb-2"><strong>Fase Menjelang Adzan (Tarhim):</strong> Beberapa menit sebelum adzan tiba, audio tarhim/murottal dapat berputar otomatis untuk mengingatkan jamaah bersiap.</li>
                                                <li class="mb-2"><strong>Fase Adzan:</strong> Saat waktu sholat masuk, layar menghentikan perputaran slide dan menampilkan pengumuman waktu adzan berkumandang.</li>
                                                <li class="mb-2"><strong>Fase Iqamah:</strong> Menampilkan hitungan mundur waktu jeda sholat sunnah hingga iqamah dikumandangkan.</li>
                                                <li class="mb-2"><strong>Fase Sholat Berjamaah:</strong> Layar TV otomatis menjadi gelap syahdu bertuliskan pesan adab <em>"Luruskan dan Rapatkan Shaf Anda"</em> agar tidak mengganggu kekhusyukan sholat jamaah.</li>
                                                <li class="mb-2"><strong>Khusus Sholat Jum'at:</strong> Layar menampilkan kartu petugas resmi (Khatib, Imam, Bilal) dan plakat adab mendengarkan khutbah selama durasi khutbah berlangsung.</li>
                                                <li><strong>Selesai Sholat:</strong> Setelah waktu sholat usai, layar TV akan otomatis kembali berotasi menampilkan informasi masjid seperti semula.</li>
                                            </ol>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </section>

                <!-- Version History -->
                <section class="mb-5">
                    <h5 class="font-weight-bold mb-3" style="color: #1e5a3a; border-left: 4px solid #c9a03d; padding-left: 15px;">
                        <i class="fas fa-history mr-2" style="color: #c9a03d;"></i>Riwayat Versi
                    </h5>
                    <div class="card shadow-sm" style="border-radius: 10px;">
                        <div class="card-body">
                            <ul class="small mb-0" style="line-height: 1.8;">
                                <li class="mb-3">
                                    <strong style="color: #1e5a3a; font-size: 14px;">Versi 3.0.4 (September 2026)</strong>
                                    <ul>
                                        <li>Penambahan Laporan Kas Ambulance</li>
                                        <li>Penambahan Penggalangan Donasi/Infaq</li>
                                        <li>Penambahan pengaturan urutan rotasi halaman</li>
                                        <li>Penambahan fitur Live streaming dari Mekah dan Madinah</li>
                                        <li>Penambahan fitur prayer mode khus hari Jum'at</li>
                                        <li>Perubahan posisi pemutaran audio tarhim</li>
                                        <li>Penambahan fitur untuk TV di luar masjid dengan CCTV Live mimbar (jika nanti dipakai)</li>
                                        <li>Penambahan sinkronisasi jadwal sholat dengan jadwal sholat KEMENAG RI</li>
                                        <li>Sudah lupa penambahan-penambahan yang lainnya...</li>
                                    </ul>
                                </li>
                                <li class="mb-3">
                                    <strong style="color: #1e5a3a; font-size: 14px;">Versi 3.0.3 (Agustus 2026)</strong>
                                    <p class="mb-1">Perubahan tampilan dan penambahan beberapa fitur:</p>
                                    <ul>
                                        <li>Penambahan prayer mode</li>
                                        <li>Penambahan timer saat prayer mode</li>
                                        <li>Pembuatan dan implementasi proyer mode</li>
                                        <li>Penambahan fitur tarhim saat prayer mode ON</li>
                                    </ul>
                                    <div class="p-2 mt-2 mb-2 rounded bg-light border" style="font-size: 12px; color: #4b5563; line-height: 1.6;">
                                        <strong>TGL : 25 Agustus 2026 :</strong> NDILALAH TV sudah terbeli dan diserahkan lagi (programer abal-bal mumet lagi)<br>
                                        <strong>Kamis, 3 Sept 2026 :</strong> diajak pasang bracket TV dulu kemudian dilanjut di<br>
                                        <strong>Sabtu malam minggu 5 Sept '26 :</strong> TV bener-bener dipasang.<br>
                                        langsung GAZZZ beli SamSoe 3 slop, trus lanjut ke versi 3.04
                                    </div>
                                </li>
                                <li class="mb-3">
                                    <strong style="color: #1e5a3a; font-size: 14px;">Versi 3.0.2 (Agustus 2026)</strong>
                                    <ul>
                                        <li>Perubahan tampilan</li>
                                        <li>Penambahan beberapa efek background di web</li>
                                        <li>Sudah lupa ada penambahan apa lagi</li>
                                        <li>Re layout total design web aplikasi dari versi 1.0.0</li>
                                    </ul>
                                </li>
                                <li class="mb-3">
                                    <strong style="color: #1e5a3a; font-size: 14px;">Versi 3.0.1 (Agustus 2026)</strong>
                                    <ul>
                                        <li>Mulai ngoding, dan test tampilan</li>
                                        <li>Laptop ngadat, harus install ulang</li>
                                        <li>Programer abal-abal MUMET karena bahan-bahan project sebelumnya rusak dan banyak error</li>
                                        <li>Pokoknya sedih banget dah kalo dicertain....</li>
                                        <li>Akhirnya GAZZ beli aja SamSoe 2 slop, trus lanjut ke Versi 3.0.2</li>
                                    </ul>
                                </li>
                                <li>
                                    <strong style="color: #1e5a3a; font-size: 14px;">Versi 1.0.0 (Juli 2026)</strong>
                                    <p class="mb-0">Request konsep dasar program dan tampilan</p>
                                </li>
                            </ul>
                        </div>
                    </div>
                </section>

                <!-- Developer Info -->
                <section class="text-center">
                    <div class="card shadow-sm" style="border-radius: 10px; background: linear-gradient(135deg, #1e5a3a, #0a2e1f); color: white;">
                        <div class="card-body">
                            {!! $setting->footer ?? '<p class="small mb-0">Copyright &copy; 2026 Masjid Al-Jihad Dev. System</p>' !!}
                            <p class="small mt-2 mb-0">Versi Aplikasi: 3.0.4 (Update: {{ now()->format('d F Y') }})</p>
                            <p class="small mt-2 mb-0">
                                <i class="fas fa-exchange-alt" style="color: #c9a03d;"></i> Fitur Rotasi Halaman: 
                                @if($setting->rotation_enabled ?? false)
                                <span class="badge" style="background: #c9a03d; color: #1e5a3a;">AKTIF</span> (Interval: {{ $setting->rotation_interval ?? 10 }} detik)
                                @else
                                <span class="badge" style="background: #6c757d; color: white;">NONAKTIF</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>

<!-- Custom CSS -->
<style>
    .card-header button.btn-link:focus {
        text-decoration: none;
        outline: none;
    }
    
    .accordion .card {
        border: 1px solid rgba(30,90,58,0.1);
    }
    
    .accordion .card-header {
        border-bottom: 1px solid rgba(30,90,58,0.1);
    }
    
    .card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }
    
    section {
        animation: fadeInUp 0.5s ease-out;
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

@endsection