# LATEST UPDATE - SISTEM INFORMASI DISPLAY MASJID (DIGITALv304)

> **Catatan Penting untuk AI Agent / Pengembang Baru:**  
> Dokumen ini adalah **titik acuan utama (*single source of truth / handover guide*)**. Setiap kali Anda ingin melanjutkan pengembangan, memperbaiki bug, atau memodifikasi fitur di aplikasi ini menggunakan komputer, akun, atau percakapan baru, **baca dokumen ini terlebih dahulu**. Seluruh struktur arsitektur, rute, tabel database, logika peran, dan fitur mutakhir terdokumentasi lengkap di sini.

---

## 📌 1. INFORMASI UMUM PROYEK

- **Nama Aplikasi:** Sistem Informasi Display Masjid (Digital Signage Masjid)
- **Repositori GitHub:** `https://github.com/mydowndrive-ops/digitalaljihad001.git`
- **Branch Utama:** `main`
- **Lingkungan Teknologi:**
  - **Framework Backend:** Laravel 13 (PHP 8.3)
  - **Database:** MySQL / MariaDB (Database default: `masjidv2`)
  - **Frontend:** Blade Templates, Bootstrap 4 (SB Admin 2), Vanilla CSS & JavaScript murni (tanpa ketergantungan framework frontend berat)
  - **Paket Tambahan:**
    - `barryvdh/laravel-dompdf`: Generator laporan keuangan dan panduan cetak PDF
    - `intervention/image`: Pengolahan gambar & logo
    - `maatwebsite/excel`: Export laporan excel
- **Tujuan Sistem:** Menampilkan informasi jadwal sholat 5 waktu, laporan kas & keuangan, pengumuman, siaran langsung Makkah/Madinah, siaran CCTV mimbar saat khutbah, dan mode sholat otomatis (*Prayer Mode*) di TV layar masjid secara elegan, modern, dan profesional.

---

## 🏛️ 2. FITUR-FITUR UTAMA & ARSITEKTUR SISTEM

### A. Dual Engine Rotator Layar TV
1. **Layar TV Utama Dalam Masjid (`/` atau `/rotator`):**
   - Menggunakan arsitektur 2 iframe bergantian (*dual-iframe crossfade scaling*) untuk mencegah layar berkedip (*no flicker/jank*) saat pergantian slide.
   - Dilengkapi **Floating Smart Next Prayer Bar**: Kapsul kaca transparan (*glassmorphism*) di pojok kanan atas yang menghitung mundur sholat berikutnya secara detik-demi-detik.
   - Dilengkapi **Dynamic Ambient Themes by Prayer Time**: Pencahayaan dan warna pendaran aura di sekeliling layar (*floating ambient orbs*) yang berganti secara otomatis mengikuti 6 siklus waktu sholat (Subuh, Dhuha, Dzuhur, Ashar, Maghrib, Isya).
   - Memonitor status waktu sholat via polling latar belakang ke `/prayer-mode/status` tiap 3 detik.
2. **Layar TV Luar / Serambi Masjid (`/tv-outdoor`):**
   - Di hari/jam biasa: Memutar rotasi informasi masjid seperti TV utama.
   - **Saat Khutbah Jum'at / Sholat Ied dimulai**: Secara cerdas dan otomatis beralih (*auto-switch*) menampilkan **Siaran Langsung CCTV Mimbar (`/live-mimbar-embed`)**, dan kembali berotasi normal saat sholat usai.
   - Parameter simulasi uji coba: `http://localhost:8000/tv-outdoor?mimbar=1`.

### B. Mode Sholat Cerdas (Prayer Mode & Khutbah Engine)
- **Hari Biasa (5 Waktu Sholat):**
  - Fase Tarhim (audio tarhim otomatis berbunyi beberapa menit sebelum adzan).
  - Fase Adzan (hitung mundur adzan, pesan adzan).
  - Fase Iqamah (hitung mundur iqamah menuju sholat).
  - Fase Sholat (layar gelap syahdu bertuliskan "Luruskan dan Rapatkan Shaf", rotasi TV dikunci).
- **Hari Khusus Sholat Jum'at (`phase: khutbah`):**
  - Pada hari Jum'at di waktu Dzuhur, sistem tidak masuk ke hitungan iqamah biasa, melainkan beralih ke mode **Khutbah & Sholat Berjamaah**.
  - Durasi dapat diatur di database (default: 50 menit via `prayer_mode_jumat_duration`).
  - Menampilkan 4 kartu petugas resmi: **Khatib, Imam, Muadzin, Bilal** (diambil otomatis dari tabel `sholat_jumats`).
  - Plakat hadits adab khutbah (HR. Bukhari no. 934 & Muslim no. 851: larangan berbicara saat imam berkhutbah).
  - Tidak menampilkan angka ticker mundur di layar agar fokus jamaah tidak terpecah.

### C. Saluran Siaran Langsung (Live TV Streaming)
1. **Live TV Makkah (`/live-mekah-embed`):** Siaran langsung 24 jam Masjidil Haram (Ka'bah) dilengkapi Smart Mosque Overlay.
2. **Live TV Madinah (`/live-madinah-embed`):** Siaran langsung 24 jam Masjid Nabawi dilengkapi Smart Mosque Overlay.
3. **Live CCTV Mimbar (`/live-mimbar-embed`):** Menampilkan siaran kamera mimbar (dari CCTV analog kabel BNC via DVR atau IP Cam) lengkap dengan badge *LIVE*, nama khatib/imam, jam digital, dan hadits.

### D. Pengaturan Urutan Tampilan TV Berbasis Peran (RBAC)
- Di menu **Rotasi Halaman TV** (`/rotation`):
  - **Super Admin (`role: admin / superadmin`)**: Memiliki tombol interaktif **Naik (▲)** dan **Turun (▼)** untuk memindah susunan urutan putaran siaran TV.
  - **Operator / Petugas (`role: petugas / user`)**: Urutan dikunci (*read-only*). Operator hanya diizinkan mencentang aktif/nonaktif tanpa bisa mengacak nomor urut halaman.

---

## 🌐 3. DAFTAR RUTE LENGKAP (ROUTES)

### Rute Publik (Tampilan Display TV)
| URL | Nama Rute | Keterangan |
|:---|:---|:---|
| `/` | `rotator` | Layar display utama dalam masjid |
| `/tv-outdoor` | `rotator.outdoor` | Layar display khusus TV serambi/luar masjid |
| `/prayer-mode` | `prayer-mode` | Halaman layar penuh saat masuk waktu sholat |
| `/prayer-mode/status`| `prayer-mode.status`| Endpoint JSON status sholat & fase khutbah |
| `/live-mekah-embed` | `live-mekah.embed` | Embed live TV Ka'bah Makkah |
| `/live-madinah-embed`| `live-madinah.embed`| Embed live TV Masjid Nabawi Madinah |
| `/live-mimbar-embed` | `live-mimbar.embed` | Embed live kamera mimbar / khutbah |
| `/utama-embed` | `utama.embed` | Slide Jadwal Sholat 5 Waktu |
| `/keuangan-embed` | `keuangan.embed` | Slide Rincian Keuangan Masjid |
| `/jumat-embed` | `jumat.embed` | Slide Petugas Sholat Jum'at |
| `/pengumuman-embed` | `pengumuman.embed` | Slide Daftar Pengumuman |
| `/keuangan-summary-embed` | `keuangan-summary.embed` | Slide Ringkasan Grafik Keuangan |
| `/qris-embed` | `qris.embed` | Slide QRIS Donasi & Infaq |
| `/slide-embed` | `slide.embed` | Slideshow gambar informasi masjid |
| `/idul-fitri-embed` | `idul-fitri.embed` | Slide Petugas Sholat Idul Fitri |
| `/idul-adha-embed` | `idul-adha.embed` | Slide Petugas Sholat Idul Adha |
| `/ambulance-embed` | `ambulance.embed` | Slide Rincian Kas Ambulance |
| `/infaq-embed` | `infaq.embed` | Slide Program Penggalangan Infaq |

### Rute Admin Dashboard (`/login`)
| URL | Controller | Keterangan |
|:---|:---|:---|
| `/home` | `HomeController` | Dashboard utama statistik masjid |
| `/jadwal_sholat` | `JadwalSholatController` | Kelola jadwal sholat 5 waktu & durasi Jum'at |
| `/rotation` | `RotationController` | Kelola urutan & halaman aktif rotasi TV |
| `/settings` | `AppSettingController` | Pengaturan umum, prayer mode, live stream, CCTV mimbar |
| `/settings/migrate` | `AppSettingController` | Tombol 1-klik jalankan migrasi database di server |
| `/users` | `UserController` | Kelola akun administrator & operator |
| `/keuangan` | `KeuanganController` | Pencatatan arus kas pemasukan/pengeluaran |
| `/ambulance` | `KeuanganAmbulanceController`| Kas operasional mobil ambulance masjid |
| `/program-infaq`| `ProgramInfaqController` | Program infaq pembangunan/donasi |
| `/slides` | `SlideController` | Upload poster & gambar pengumuman |

---

## 🗄️ 4. STRUKTUR BASIS DATA KUNCI (`app_settings`)

Tabel `app_settings` adalah konfigurasi pusat sistem. Kolom-kolom penting mutakhir meliputi:

| Nama Kolom | Tipe Data | Keterangan |
|:---|:---|:---|
| `nama_aplikasi` | `string` | Nama masjid (Contoh: MASJID AL-IKHLAS) |
| `rotation_enabled` | `boolean` | Status perputaran rotasi TV (1 = aktif, 0 = mati) |
| `rotation_interval`| `integer` | Durasi tampil tiap halaman dalam detik (default: 10-20) |
| `rotation_pages` | `json/text` | Susunan terurut seluruh halaman beserta status aktif |
| `prayer_mode_enabled` | `boolean` | Mengaktifkan layar mode sholat otomatis |
| `prayer_mode_jumat_duration` | `integer` | Durasi penguncian TV saat Khutbah Jum'at (default: 50 menit) |
| `enable_dynamic_theme` | `boolean` | Pendaran aura warna latar otomatis sesuai waktu sholat |
| `enable_next_prayer_bar` | `boolean` | Widget kapsul kaca hitung mundur sholat berikutnya |
| `live_makkah_url` | `string` | URL / Video ID siaran Makkah |
| `live_madinah_url`| `string` | URL / Video ID siaran Madinah |
| `live_stream_overlay` | `boolean` | Menampilkan jam & jadwal sholat di atas siaran live |
| `live_stream_audio` | `boolean` | Mute/Unmute audio siaran live (default: 0 / Mute) |
| `cctv_mimbar_url` | `string` | URL RTSP / Web stream kamera CCTV mimbar |
| `cctv_mimbar_enabled` | `boolean` | Status aktif integrasi kamera CCTV mimbar |
| `cctv_auto_switch_khutbah` | `boolean` | Otomatis alihkan TV luar ke CCTV saat khutbah dimulai |

---

## 🚀 5. CARA MENJALANKAN DI PC / LAPTOP BARU

Jika proyek ini di-*clone* ke komputer atau laptop baru:

1. **Clone Repositori:**
   ```bash
   git clone https://github.com/mydowndrive-ops/digitalaljihad001.git
   cd digitalaljihad001
   ```
2. **Siapkan Konfigurasi `.env`:**
   - Salin `.env.example` menjadi `.env`.
   - Sesuaikan konfigurasi database MySQL:
     ```env
     DB_CONNECTION=mysql
     DB_HOST=127.0.0.1
     DB_PORT=3306
     DB_DATABASE=masjidv2
     DB_USERNAME=root
     DB_PASSWORD=
     ```
3. **Instal Dependensi & Generate Key:**
   ```bash
   composer install
   php artisan key:generate
   ```
4. **Jalankan Migrasi Database:**
   ```bash
   php artisan migrate
   ```
5. **Jalankan Server Lokal:**
   ```bash
   php artisan serve
   ```
   Aplikasi siap diakses di `http://localhost:8000`.

---

## 📹 6. INTEGRASI CCTV MIMBAR (DVR KABEL BNC)

Untuk menghubungkan kamera analog kabel BNC yang sudah ada di mimbar:
1. Hubungkan port LAN di belakang mesin DVR CCTV ke router Wi-Fi masjid via kabel LAN.
2. Baca panduan lengkap operasional yang sudah kami siapkan di:
    - File Markdown: [`USER GUIDE/TUTORIAL_CCTV_MIMBAR_TV_LUAR.md`](file:///c:/Users/anthu/Documents/【Project】/DIGITALv304/USER%20GUIDE/TUTORIAL_CCTV_MIMBAR_TV_LUAR.md)
    - File PDF Resmi: [`USER GUIDE/TUTORIAL_CCTV_MIMBAR_TV_LUAR.pdf`](file:///c:/Users/anthu/Documents/【Project】/DIGITALv304/USER%20GUIDE/TUTORIAL_CCTV_MIMBAR_TV_LUAR.pdf)
    - File HTML Cetak: [`USER GUIDE/TUTORIAL_CCTV_MIMBAR_TV_LUAR.html`](file:///c:/Users/anthu/Documents/【Project】/DIGITALv304/USER%20GUIDE/TUTORIAL_CCTV_MIMBAR_TV_LUAR.html)

---

## 🛠️ 7. SISTEM SINKRONISASI MIGRASI DATABASE (HOSTING / RENDER / TIDB)

Untuk mencegah error `1054 Unknown column` (seperti saat menyimpan URL Live Makkah/Madinah atau CCTV Mimbar di server hosting seperti Render.com):
1. **Auto-Migrate Fallback:**
   - Di `AppSettingController@update`, sistem secara otomatis mendeteksi ketiadaan kolom baru dan memicu `Artisan::call('migrate', ['--force' => true])`.
   - Seluruh penugasan atribut dilindungi oleh pengecekan `Schema::hasColumn('app_settings', ...)` sehingga aplikasi tidak akan pernah mengalami crash HTTP 500 jika kolom baru belum dieksekusi di database.
2. **Tombol Sinkronisasi Web 1-Klik:**
   - Rute: `GET /settings/migrate` (nama: `settings.migrate`).
   - Tombol **"Sinkronkan Database (Migrate)"** tersedia di pojok kanan atas halaman Pengaturan Aplikasi (`/settings`). Cukup klik tombol tersebut, Laravel di server hosting akan menjalankan migrasi database secara instan tanpa perlu akses terminal SSH.

---

---

## 🎨 9. REDESAIN DASHBOARD ADMIN (Islamic Material Design 3 — v4.0)

**Tanggal:** 14 September 2026 | **File:** `resources/views/layouts/admin.blade.php`

Redesain premium dashboard admin panel dengan filosofi **Islamic Material Design 3** — terinspirasi Google Material You, dikombinasikan identitas visual islami. **Nol library baru ditambahkan.**

### Perubahan yang Diterapkan

| Komponen | Perubahan |
|:---|:---|
| **Sidebar Lebar** | 224px → **260px** (lebih lega, teks tidak terpotong) |
| **Sidebar Gradient** | Lebih gelap dan elegan: `#071a10` → `#0e3521` → `#1a5235` |
| **Sidebar Pattern** | Hexagonal geometric SVG pattern emas (opacity 14%) |
| **Nav Links** | Border-left lama → **Pill-style** rounded (border-radius 10px) |
| **Active State** | `background highlight` + **gold dot indicator** (`::after`) kanan |
| **Nav Icon Chip** | Icon dibungkus chip 28×28px via **JS auto-wrap** (tanpa ubah HTML) |
| **User Panel** | Panel baru bawah logo: avatar emas + nama + role + **pulse dot hijau** |
| **Topbar: Live Clock** | Widget jam detik-per-detik + tanggal lengkap (Vanilla JS `setInterval`) |
| **Topbar: Prayer Pill** | Kapsul "Sholat [Nama]" fetch dari `/prayer-mode/status` tiap 1 menit |
| **Topbar: Search** | Border-radius pill `20px`, subtle green border on focus |
| **Dropdown/Modal/Card** | `border-radius` 14–18px, `box-shadow` lebih soft dan elevated |
| **Responsive** | Clock & prayer pill hilang otomatis di `max-width: 767px` |

### Teknik Implementasi
- **CSS Override Layer**: Ditambahkan setelah block CSS lama, menggunakan `!important` untuk override SB Admin 2
- **JS DOMContentLoaded**: Icon chip di-inject saat DOM siap (tidak perlu ubah setiap `nav-link`)
- **PHP/Blade Logic**: Tidak ada perubahan — semua RBAC, fallback `??`, dan rute tetap utuh

---

## 📝 10. PEMBARUAN BESAR HALAMAN TENTANG APLIKASI (`/about`) — v4.0

**Tanggal:** 14 September 2026 | **File:** `resources/views/about.blade.php`

Halaman **Tentang Aplikasi (`/about`)** diperbarui total 100% selaras dengan kondisi riil aplikasi saat ini:
1. **Hero Header Islamic Material Design 3:**
   - Kartu header elegan dengan gradasi hijau islami `#071a10 → #0e3521 → #1a5235` dan aksen emas `#c9a03d`.
   - Menampilkan badge status langsung: Versi 4.0, Laravel 13 & PHP 8.3, status rotasi TV (`rotation_enabled`), interval, serta tombol pintas pratinjau display TV.
2. **Pembaruan Fitur Tampilan TV Mutakhir (Menggantikan 4 card dasar lama):**
   - **Dual Engine Crossfade:** Transisi 60 FPS tanpa jeda hitam/kedip (*zero-flicker*).
   - **Smart Next Prayer Bar:** Kapsul kaca *glassmorphism* hitung mundur sholat berikutnya.
   - **Dynamic Ambient Theme:** Pendaran aura warna latar otomatis sesuai 6 waktu sholat.
   - **Prayer & Khutbah Engine:** Otomasi 5 fase ibadah dan penguncian layar khutbah Jum'at 50 menit.
   - **CCTV Mimbar Otomatis:** TV luar beralih ke kamera mimbar secara mandiri saat khutbah dimulai.
   - **Live TV Makkah & Madinah:** Siaran langsung 24 jam dengan Smart Mosque Overlay transparan.
   - **Smart Running Text & Slide Poster:** Pengumuman berjalan halus & brosur dakwah beresolusi tinggi.
3. **Pembaruan Fitur Rotasi Halaman Dinamis:**
   - Penjelasan fitur baru Reorder Prioritas Urutan (Tombol Naik ▲ dan Turun ▼) untuk Super Admin & kunci proteksi untuk Operator.
   - Menampilkan **13 Saluran Slide Display Aktif** lengkap dengan ikon, path embed, warna tematik, dan tautan pratinjau langsung:
     - `/utama-embed` (Jadwal Sholat 5 Waktu)
     - `/keuangan-embed` (Rincian Kas Utama)
     - `/keuangan-summary-embed` (Grafik Arus Kas)
     - `/ambulance-embed` (Kas Mobil Ambulance)
     - `/infaq-embed` (Program Donasi & Infaq)
     - `/jumat-embed` (Petugas Sholat Jum'at)
     - `/pengumuman-embed` (Daftar Pengumuman)
     - `/slide-embed` (Slide Poster Informasi)
     - `/qris-embed` (QRIS Infaq Digital)
     - `/live-mekah-embed` (Live TV Makkah)
     - `/live-madinah-embed` (Live TV Madinah)
     - `/idul-fitri-embed` (Petugas Idul Fitri)
     - `/idul-adha-embed` (Petugas Idul Adha)
4. **Pembaruan Hak Akses Pengguna (RBAC):**
   - Menegaskan peran Administrator (Full Control & Reorder), Petugas/Operator (Operasional & Konten), dan Bendahara (Kas Utama, Ambulance, Donasi & Ekspor).
5. **Pembaruan Panduan Pengoperasian & Riwayat Versi:**
   - Menambahkan panduan TV Luar & CCTV Mimbar, Prayer Mode otomatis, serta tombol 1-klik sinkronisasi database server (`/settings/migrate`).
   - Riwayat versi mencantumkan **Versi 4.0.0 (September 2026)** dengan tetap menjaga catatan sejarah asli proyek sebelumnya.
6. **Integrasi Smart Hardware & Full Auto Self-Running:**
   - Menambahkan dokumentasi integrasi **Smart Breaker** dan **Smart IR Remote Control** demi efisiensi konsumsi daya listrik serta kepraktisan waktu pengoperasian TV masjid secara mandiri tanpa campur tangan manual marbot (*full auto self running*).
7. **Teks Sambutan & Ungkapan Rasa Syukur Pengembang:**
   - Bagian awal pengantar sistem disempurnakan dengan doa basmalah, salam pembuka, tahmid dan shalawat berbahasa Arab berformat **rata tengah (*center*)**, diikuti ungkapan rasa syukur, ikhtiar dedikasi untuk Masjid Al-Jihad, permohonan maaf atas kekurangan, serta doa keberkahan amal jariyah bagi seluruh pengurus masjid dan jamaah.

---

## 🤖 11. INTEGRASI GOOGLE GEMINI AI (PENGUMUMAN & MUTIARA HADITS TV) — v4.1

**Tanggal:** 15 September 2026 | **Versi:** 4.1.0

Fitur kecerdasan buatan (*Artificial Intelligence*) resmi diintegrasikan ke dalam Sistem Display Masjid menggunakan Google Gemini API via official endpoint REST Google AI Studio dengan prinsip **zero heavy vendor library** (memanfaatkan HTTP Client bawaan Laravel `Illuminate\Support\Facades\Http`).

### Fitur AI yang Diimplementasikan:
1. **AI One-Click Copywriter Pengumuman & Running Text:**
   - DKM cukup mengetikkan poin-poin mentah sederhana (contoh: *"kajian ahad subuh ustadz fulan bawa infaq terbaik"*).
   - Gemini AI menyusunnya menjadi:
     - Judul pengumuman resmi islami yang menarik dan santun.
     - Redaksi isi pengumuman lengkap dengan salam, basmalah, dalil ringkas, waktu & tempat, serta penutup doa.
     - Teks ringkasan *running text* siap tayang (maks. 150 karakter) untuk teks berjalan TV.
   - Tersedia tombol **✨ Susun dengan AI** di form pembuatan dan edit pengumuman (`resources/views/pengumuman/create.blade.php` dan `edit.blade.php`) yang membuka modal interaktif (`resources/views/pengumuman/partials/ai-modal.blade.php`).
   - Tombol "Terapkan ke Formulir" otomatis mengisikan judul, isi, dan teks ringkasan ke form utama.

2. **AI Generator Hadits & Mutiara Hikmah Harian Display TV (`/hikmah-embed`):**
   - Saluran slide baru berlayar penuh (*full-screen*) yang dirancang khusus untuk rotasi display TV masjid.
   - **Desain Mewah Islamic Material Design 3:**
     - Matan hadits berbahasa Arab berharakat lengkap dengan kaligrafi font *Amiri* berukuran besar dan **rata tengah (*center*)**.
     - Terjemahan bahasa Indonesia yang puitis dan menggetarkan hati.
     - Sanad perawi shahih (mis. HR. Bukhari, Muslim, Abu Dawud, At-Tirmidzi).
     - Sari hikmah praktis untuk diamalkan jamaah sehari-hari.
     - Kapsul tanggal Hijriyah & Masehi, ornamen sudut islami emas (*gold Islamic corners*), serta aura pendaran latar (*ambient glow*).
   - **Efisiensi TV & Cache Harian 24 Jam:**
     - TV tidak memanggil API AI secara berulang. AI dieksekusi di backend server dan disimpan di kolom database `daily_hikmah_cache` selama 24 jam (`daily_hikmah_date`).
     - Display TV hanya memuat HTML/CSS biasa (100% GPU accelerated, 60 FPS, tidak memberatkan perangkat TV Stick / Android TV).
   - **Fallback Offline Cerdas (7 Koleksi Hadits Otentik):**
     - Jika API Key belum diisi atau server kehilangan koneksi internet, sistem otomatis menampilkan hadits shahih otentik bergilir sesuai hari (Senin s/d Ahad) tanpa error atau jeda.
   - Terdaftar sebagai **Saluran Slide ke-14** pada sistem rotasi display TV masjid.

3. **Panel Konfigurasi Google Gemini AI di Admin Settings (`/settings`):**
   - Tab baru **Google Gemini AI** di halaman Pengaturan Aplikasi (`resources/views/settings/edit.blade.php`):
     - Form input API Key (tipe password dengan tombol intip/toggle intip sandi).
     - Pemilihan Model AI: `gemini-1.5-flash` (Rekomendasi Cepat & Gratis), `gemini-1.5-pro`, dan `gemini-2.0-flash`.
     - Tombol **Uji Koneksi AI**: Mengetes langsung keabsahan API Key ke server Google.
     - Tombol **Generate Hadits Hari Ini Sekarang**: Untuk memaksa pembaruan konten slide TV seketika.
     - Panduan 3 langkah mudah mendapatkan Google AI Studio API Key 100% Gratis.

### Struktur Database & Berkas Baru:
- **Migration:** `database/migrations/2026_09_15_000001_add_gemini_ai_settings_to_app_settings.php`
  - Kolom baru pada tabel `app_settings`: `gemini_api_key`, `gemini_model`, `daily_hikmah_cache`, `daily_hikmah_date`.
- **Model:** `app/Models/AppSetting.php`
  - Ditambahkan `$fillable`, casts JSON, dan mendaftarkan `hikmah-embed` ke `getDefaultRotationPagesList()`.
- **Service:** `app/Services/GeminiService.php`
  - Logika pemanggilan Gemini REST API, prompt engineering islami ketat, caching hadits 24 jam, dan 7 template hadits fallback offline.
- **Controller:** `app/Http/Controllers/AiController.php`
  - Rute publik: `GET /hikmah-embed`
  - Rute terproteksi auth: `POST /ai/generate-pengumuman`, `POST /ai/refresh-hikmah`, `POST /ai/test-connection`.
- **Blade Views:**
  - `resources/views/hikmah-embed.blade.php` (Slide display TV Hadits Mutiara Hikmah).
  - `resources/views/pengumuman/partials/ai-modal.blade.php` (Modal AI Copywriter).
- **Environment:**
  - `.env.example` ditambahkan `GEMINI_API_KEY=` dan `GEMINI_MODEL=gemini-1.5-flash`.

---

## 🔒 12. PRINSIP PENGEMBANGAN BERIKUTNYA (ATURAN WAJIB)

Setiap AI Agent atau pengembang yang bekerja pada proyek ini **WAJIB MEMATUHI**:
1. **Preservasi Nilai Default & Fallback Aman:** Selalu sertakan operator *null coalescing* (`?? true`, `?? 50`) pada Blade view dan Controller, serta perlindungan `Schema::hasColumn()` agar aplikasi tidak pernah *crash* jika kolom baru belum dimigrasi di database hosting/lokal.
2. **Sinkronisasi Git Otomatis:** Setelah menyelesaikan modifikasi atau perbaikan, **WAJIB langsung melakukan commit dan push ke branch `main` GitHub**.
3. **Pembaruan Dokumen Ini:** Setiap kali ada fitur baru atau perubahan alur, perbarui file `LATEST_UPDATE.md` ini agar riwayat pekerjaan selalu berkesinambungan.

---
*Terakhir Diperbarui: 15 September 2026 (Integrasi Fitur AI Copywriter Pengumuman & Slide TV Mutiara Hadits Harian v4.1 via Google Gemini API) &bull; Komitmen: Sinkron Penuh dengan GitHub `origin/main`.*
