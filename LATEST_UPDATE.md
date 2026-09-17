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
| `/yasin-embed` | `yasin.embed` | Layar penuh Surat Yaasiin 83 ayat teks Arab auto-scroll |

### Rute Admin Dashboard (`/login`)
| URL | Controller | Keterangan |
|:---|:---|:---|
| `/home` | `HomeController` | Dashboard utama statistik masjid |
| `/jadwal_sholat` | `JadwalSholatController` | Kelola jadwal sholat 5 waktu & durasi Jum'at |
| `/rotation` | `RotationController` | Kelola urutan & halaman aktif rotasi TV |
| `/settings` | `AppSettingController` | Pengaturan umum, prayer mode, live stream, CCTV mimbar, agenda malam Jum'at |
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
| `yasin_mode_enabled` | `boolean` | Saklar aktif agenda malam Jum'at Surat Yaasiin (default: 1) |
| `yasin_start_time` | `string` | Jam mulai pembacaan Yaasiin tiap Kamis malam (default: '18:30') |
| `yasin_scroll_speed` | `string` | Kecepatan gulir teks Arab: slow, medium, fast (default: 'medium') |

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

## 📢 12. FITUR TEKS BERJALAN KHUSUS TIAP HALAMAN DISPLAY (OPSI 3) — v4.2

**Tanggal:** 15 September 2026 | **Versi:** 4.2.0

Sistem Teks Berjalan (*Running Text*) ditingkatkan secara cerdas dengan menerapkan **Opsi 3: Panel Pemetaan 1-Tempat Terpusat (*Dedicated Mapping Accordion*)**. Masalah overlap kalimat antar rotasi halaman di TV teratasi tuntas karena setiap halaman kini dapat menampilkan pesan yang berbeda dan selaras dengan konten yang sedang tayang.

### Fitur & Cara Kerja:
1. **Panel Input Terpusat di Menu Pengaturan (`/settings`):**
   - **Teks Berjalan Utama / Default:** Tetap tersedia di kolom atas untuk pesan global. Halaman yang tidak memiliki teks kustom akan otomatis memakai teks ini (*fallback*).
   - **Accordion Pemetaan Khusus 15 Halaman Display:**
     - Tersedia daftar lipat (*accordion*) interaktif untuk seluruh halaman TV:
       - `/utama-embed` (Jadwal Sholat 5 Waktu)
       - `/keuangan-embed` (Rincian Kas Utama)
       - `/keuangan-summary-embed` (Ringkasan Grafik Kas)
       - `/jumat-embed` (Petugas Sholat Jumat)
       - `/pengumuman-embed` (Pengumuman DKM)
       - `/qris-embed` (QRIS Infaq Digital)
       - `/slide-embed` (Slide Poster Informasi)
       - `/ambulance-embed` (Kas Layanan Ambulance)
       - `/infaq-embed` (Program Donasi & Infaq)
       - `/hikmah-embed` (Mutiara Hadits & Hikmah)
       - `/live-mekah-embed` (Live TV Mekah)
       - `/live-madinah-embed` (Live TV Madinah)
       - `/idul-fitri-embed` (Petugas Idul Fitri)
       - `/idul-adha-embed` (Petugas Idul Adha)
       - `/welcome-embed` (Dashboard Lengkap)
     - Setiap item dilengkapi icon tematik, badge status (hijau *"Kustom Aktif"* jika terisi, abu-abu *"Default Umum"* jika kosong), dan contoh placeholder teks yang relevan.
     - Dilengkapi tombol **Buka / Tutup Semua Panel** untuk kepraktisan admin.
2. **Dukungan Multi-Pesan (Enter) Per Halaman:**
   - Di masing-masing halaman kustom, admin tetap bisa menekan **Enter** untuk memasukkan lebih dari satu pesan.
   - Pesan akan ditampilkan bergantian satu per satu secara berkesinambungan di halaman tersebut.
3. **Penyelarasan Tampilan TV (`partials/bottom-section.blade.php`):**
   - Sistem secara otomatis mendeteksi URL halaman embed yang sedang aktif (`request()->path()`).
   - Mengambil teks spesifik milik halaman tersebut via helper `AppSetting::getRunningTextForPage($currentPath)`.
   - Menambahkan `@include('partials.bottom-section')` pada slide baru `/hikmah-embed` sehingga seluruh 14 saluran display TV masjid memiliki footer running text yang seragam, mewah, dan bebas kedip.

### Struktur Database & Berkas Terkait:
- **Migration:** `database/migrations/2026_09_15_000002_add_running_text_pages_to_app_settings.php` (menambahkan kolom `running_text_pages` bertipe `JSON`).
- **Model:** `app/Models/AppSetting.php` (menambahkan fillable, casts `array`, helper `getRunningTextPages()`, `getRunningTextForPage()`, dan `getDisplayPageCatalog()`).
- **Controller:** `app/Http/Controllers/AppSettingController.php` (validasi & sanitasi array `running_text_pages`) dan `RotationController.php`.
- **Views:**
  - `resources/views/settings/edit.blade.php` (UI Accordion 15 Halaman & tombol Expand/Collapse).
  - `resources/views/partials/bottom-section.blade.php` (Logika seleksi pesan per halaman & animasi mulus).
  - `resources/views/hikmah-embed.blade.php` (Integrasi partial bottom section).

---

---

## 🕌 13. PERBAIKAN BUG: PENGECUALIAN SYURUK & IMSAK DARI MODE SHOLAT (v4.2.1)

**Tanggal:** 15 September 2026 | **Versi:** 4.2.1

### Akar Masalah:
- Di `PrayerModeController.php`, filter jadwal sholat sebelumnya hanya mengecualikan `['imsak', 'terbit']`.
- Pada database dan layanan auto-update resmi aplikasi (`jadwal_sholat`), nama waktu terbit matahari tersimpan sebagai **`Syuruk`**.
- Akibatnya, sistem menganggap waktu `Syuruk` sebagai salah satu sholat fardhu berjamaah sehingga memicu:
  - Fase Countdown / Tarhim (menjelang Syuruk).
  - Fase Adzan Syuruk.
  - Fase Iqamah Syuruk ("MENUNGGU WAKTU IQAMAH • SYURUK • MENUJU IQAMAH").
  - Fase Sholat Syuruk ("Luruskan dan Rapatkan Shaf").
- Secara syariat Islam, **Syuruk (terbit matahari)** dan **Imsak (penanda menahan diri sebelum fajar)** bukanlah sholat fardhu, tidak memiliki adzan maupun iqamah, dan saat terbit matahari justru merupakan waktu yang dilarang/makruh tahrim untuk mendirikan sholat hingga matahari meninggi (waktu Dhuha).

### Solusi & Perubahan:
1. **Pengecualian Komprehensif di `PrayerModeController.php`:**
   - Variasi penamaan non-sholat fardhu kini diekspansi secara ketat:
     `$nonPrayerTimes = ['imsak', 'imsyak', 'terbit', 'syuruk', 'shuruk', 'sunrise', 'dhuha', 'duha'];`
   - Hanya 5 waktu sholat fardhu (Subuh, Dzuhur / Sholat Jum'at, Ashar, Maghrib, Isya) yang dapat memicu Prayer Mode otomatis di layar TV.
2. **Kalkulasi & Return `next_prayer` untuk Admin Topbar & Widget:**
   - Menambahkan kalkulasi sholat fardhu berikutnya (`next_prayer`) pada status respon JSON `/prayer-mode/status` agar widget kapsul "Sholat [Nama]" pada topbar admin terisi akurat.
3. **Penyelarasan Floating Smart Next Prayer Bar di `rotator.blade.php`:**
   - Menambahkan filter `$nonPrayerTimes` pada array `prayerList` di rotator utama display TV sehingga bar hitung mundur melayang di pojok kanan atas tidak akan pernah menghitung mundur menuju Syuruk atau Imsak sebagai "sholat".

### Berkas yang Dimodifikasi:
- `app/Http/Controllers/PrayerModeController.php`
- `resources/views/rotator.blade.php`
- `LATEST_UPDATE.md`

---

## 🧭 14. RELOKASI BADGE KAPSUL SHOLAT BERIKUTNYA & HEADER JADWAL SHOLAT (v4.2.2)

**Tanggal:** 15 September 2026 | **Versi:** 4.2.2

### Latar Belakang & Masukan Pengguna:
- Keberadaan badge kapsul melayang (*Floating Smart Next Prayer Bar*) di pojok kanan atas layar rotator TV (`rotator.blade.php`) terasa mengganggu pandangan karena selalu muncul menutupi sudut atas di seluruh halaman display yang berputar (seperti Keuangan, Pengumuman, QRIS, Hadits Hikmah, Live TV, dll.).
- Pengguna menghendaki:
  1. Badge kapsul hitung mundur sholat berikutnya **hanya muncul di halaman Jadwal Sholat (`utama.blade.php`)**.
  2. Header badge **"Jadwal Sholat"** dipindahkan ke atas, tepat di bawah kotak kapsul tanggal & jam (*"hari, tgl, bln, tahun, jam"*).
  3. Badge kapsul hitung mundur sholat berikutnya ditempatkan **pas di tengah-tengah** (*centered*) secara horizontal dan vertikal di antara header "Jadwal Sholat" (atas) dan deretan kotak-kotak sholat 7 waktu (bawah).

### Perubahan yang Diterapkan:
1. **Pembersihan di Layar Induk Rotator (`resources/views/rotator.blade.php`):**
   - Menghapus elemen HTML `#nextPrayerBarWrapper` dan seluruh CSS serta skrip JS hitung mundurnya dari file rotator utama.
   - Hasilnya: Pojok kanan atas di seluruh 14 halaman display kini bersih, lega, dan bebas dari tumpukan elemen mengambang.
2. **Penataan Ulang Tata Letak di `resources/views/utama.blade.php`:**
   - **Header Jadwal Sholat (`.jadwal-sholat-title`):** Dipindahkan ke dalam `.header-section`, bersanding rapi tepat di bawah kapsul tanggal/jam (`.datetime`) dengan pembungkus flex `.jadwal-sholat-title-wrap`.
   - **Badge Kapsul Sholat Berikutnya (`.next-prayer-center-container`):** Diturunkan ke `.bottom-section` tepat di atas deretan kartu sholat (`.sholat-list`) dengan jarak tipis/sedikit (`margin: 0 auto 10px auto;`).
   - **Pemandangan Latar Belakang Terbuka Utuh:** Area tengah layar TV (yang menampilkan kubah hijau dan menara Masjid Nabawi) kini menjadi 100% bebas hambatan, sangat memanjakan mata jamaah tanpa tertutup elemen apapun.
   - **Kotak-Kotak Sholat (`.sholat-list`):** Tetap berada di `.bottom-section` pada bagian bawah layar display di atas running text footer.
3. **Engine Hitung Mundur Khusus Halaman Sholat:**
   - Memindahkan logika countdown JS mandiri langsung ke dalam `utama.blade.php`, memfilter nama non-fardhu (Imsak, Syuruk, Terbit), dan otomatis menyesuaikan nama "DZUHUR" menjadi "SHOLAT JUM'AT" khusus di hari Jum'at.

### Berkas yang Dimodifikasi:
- `resources/views/rotator.blade.php`
- `resources/views/utama.blade.php`
- `LATEST_UPDATE.md`

---

## 👥 15. HAK AKSES TEKS BERJALAN UNTUK OPERATOR/PETUGAS & PERBAIKAN KONTRAS PANEL ACCORDION (v4.2.3)

**Tanggal:** 15 September 2026 | **Versi:** 4.2.3

### 1. Akses Pengaturan Teks Berjalan untuk Akun Operator / Petugas:
- **Menu Sidebar Baru untuk Petugas:**
  - Menambahkan menu **Teks Berjalan TV** berikon megafon emas (`fas fa-bullhorn`) di sidebar admin untuk akun role `petugas`.
  - Operator/petugas kini dapat langsung membuka halaman pengaturan teks berjalan dengan satu kali klik tanpa harus meminta bantuan admin/superadmin.
- **Penyelarasan Hak Akses & Proteksi Sistem:**
  - Akun operator/petugas kini dapat mengubah **Teks Berjalan Utama (Default)** maupun **Teks Berjalan Khusus Tiap Halaman (Opsi 3)**.
  - Tombol berbahaya tingkat teknis sistem seperti **Sinkronkan Database (Migrate)** diproteksi secara ketat sehingga hanya muncul bagi akun Super Admin (`admin`).
  - Judul halaman secara dinamis menyesuaikan: *"Pengaturan Teks Berjalan & Tampilan TV"* lengkap dengan lencana badge informasi *"Akses Operator"*.

### 2. Perbaikan Total Kontras & Kemewahan Panel Accordion Opsi 3:
- **Masalah Visual Sebelumnya:** Header accordion pada tema gelap SB Admin Al-Jihad menampilkan teks judul halaman (`.text-dark`) dan badge yang gelap di atas latar belakang hijau tua `#0e3521`, sehingga sulit dibaca oleh pengguna.
- **Penyempurnaan Desain Islamic Material Design 3:**
  - **Judul Halaman Display:** Berubah menjadi **putih bersih mengkilap (`#ffffff`)**, cetak tebal (*bold*), dengan efek bayangan halus (*text-shadow*) yang sangat tajam dan kontras.
  - **Badge Rute URL (`/utama-embed`, dll.):** Diberi gaya kapsul emas islami (`color: #ffd700`, latar belakang transparan dengan border emas).
  - **Teks Deskripsi Halaman:** Dibuat dengan warna abu-abu terang kontras (`#cbd5e1`), sangat jelas dan nyaman dibaca.
  - **Ikon Lingkaran Halaman:** Berpendar emas dengan lingkaran transparan saat default, dan hijau emerald saat kustom aktif.
  - **Status Badge:** Tampil lebih tegas dan elegan (*Kustom Aktif* hijau cerah & *Default Umum* transparan perak berbingkai).
  - **Form Textarea:** Menggunakan latar belakang putih bersih (`#ffffff`) dengan teks gelap kontras tinggi (`#0f172a`), border tegas, dan panduan multi-pesan (Enter).

### Berkas yang Dimodifikasi:
- `resources/views/layouts/admin.blade.php` (Penambahan menu Teks Berjalan TV untuk role petugas)
- `resources/views/settings/edit.blade.php` (Perbaikan kontras header accordion, proteksi tombol migrate, dan judul ramah operator)
- `LATEST_UPDATE.md` (Pencatatan riwayat pembaruan v4.2.3)

---

## 🔒 16. PROTEKSI PENGUNCIAN 'NAMA APLIKASI' & 'FOOTER TEXT' UNTUK OPERATOR (v4.2.4)

**Tanggal:** 15 September 2026 | **Versi:** 4.2.4

### Latar Belakang & Kebutuhan Pengguna:
- Untuk menjaga integritas identitas resmi masjid, kolom **Nama Aplikasi** dan **Footer Text** tidak boleh sembarangan diubah oleh akun operator/petugas.
- Kedua kolom ini harus dikunci (*locked/readonly*) bagi operator sehingga hanya Super Admin (`admin`) yang berhak memperbaruinya.

### Perubahan yang Diterapkan:
1. **Antarmuka Pengguna (*Frontend Blade Protection*):**
   - Pada `resources/views/settings/edit.blade.php`, jika akun yang login bukan `admin`:
     - Kolom `nama_aplikasi` dan `footer` secara otomatis diberi atribut `readonly`.
     - Diberi tampilan visual terkunci: latar belakang abu-abu halus (`#f1f5f9`), kursor tanda larang (`cursor: not-allowed`), teks abu-abu gelap, serta badge gembok eksplisit: `<span class="badge badge-secondary"><i class="fas fa-lock mr-1"></i> Terkunci (Super Admin)</span>`.
     - Keterangan bantuan di bawah kolom: *"Nama aplikasi / teks footer hanya dapat diubah oleh Super Admin."*
2. **Keamanan Sisi Server (*Backend Controller Protection*):**
   - Pada `app/Http/Controllers/AppSettingController.php` method `update()`:
     - Ditambahkan pengecekan peran pengguna: `$isSuperAdmin = auth()->check() && auth()->user()->hasRole('admin');`.
     - Kolom `nama_aplikasi` dan `footer` hanya diperbarui ke database jika user terverifikasi sebagai Super Admin.
     - Jika operator melakukan submit form (misalnya saat menyimpan perubahan teks berjalan), sistem backend menolak menimpa nilai `nama_aplikasi` dan `footer` yang sudah ditetapkan sebelumnya.

### Berkas yang Dimodifikasi:
- `resources/views/settings/edit.blade.php`
- `app/Http/Controllers/AppSettingController.php`
- `LATEST_UPDATE.md`

---

## 🎨 17. PENATAAN ULANG FORM PENGATURAN UMUM & TEKS BERJALAN (v4.2.5)

**Tanggal:** 15 September 2026 | **Versi:** 4.2.5

### Latar Belakang & Kebutuhan Pengguna:
- Menghilangkan kesan kaku dan potensi prasangka antar pengurus/operator terkait tulisan gembok "(Terkunci Super Admin)" dan keterangan teks yang menyebutkan pembatasan Super Admin.
- Mengefisiensikan ruang tata letak form: mengecilkan kolom **Footer Text** menjadi 1 baris (*single-line input*) sejajar di sebelah kanan **Nama Aplikasi** (`col-md-6` + `col-md-6`).
- Melebarkan kotak isian **Teks Berjalan Utama / Default (Semua Halaman)** menjadi membentang penuh (*full width* `col-12`) dari ujung kiri hingga ujung kanan agar operator dapat melihat teks berjalan panjang secara leluasa dan nyaman saat menginput pesan.

### Perubahan yang Diterapkan:
1. **Penghapusan Badge & Teks Pembatasan Provokatif:**
   - Menghapus badge `<span class="badge badge-secondary"><i class="fas fa-lock mr-1"></i> Terkunci (Super Admin)</span>` pada label Nama Aplikasi dan Footer Text.
   - Menghapus teks informasi *"Nama aplikasi hanya dapat diubah oleh Super Admin"* dan *"Teks footer hanya dapat diubah oleh Super Admin"*.
   - Proteksi keamanan tetap aktif 100%: untuk operator/non-admin, kedua kolom tetap berstatus `readonly` dengan warna netral yang elegan (`#f8fafc`), dan sisi controller backend tetap mengunci pembaruan nilainya hanya untuk Super Admin.
2. **Kompensasi Tata Letak 1 Baris Sejajar (Nama Aplikasi & Footer Text):**
   - Mengubah kolom `footer` dari `<textarea>` menjadi `<input type="text">` satu baris.
   - Mengatur posisi **Nama Aplikasi** di kolom kiri (`col-md-6`) dan **Footer Text** di kolom kanan (`col-md-6`).
3. **Pelebaran Penuh Kotak Teks Berjalan Utama (`col-12` Full Width):**
   - Menempatkan kotak **Teks Berjalan Utama / Default (Semua Halaman)** membentang penuh horizontal (`col-12`) dengan `rows="4"`, sehingga teks berjalan panjang dapat terbaca jelas tanpa terpotong sempit.
4. **Perapihan Kolom Media (Favicon, Logo, Background):**
   - Menata ulang input upload Favicon, Logo Aplikasi, dan Background Sidebar menjadi sejajar simetris dalam satu baris (`col-md-4`, `col-md-4`, `col-md-4`).

### Berkas yang Dimodifikasi:
- `resources/views/settings/edit.blade.php` (Penataan ulang grid layout tab general, single-line footer text, col-12 running text, dan penghapusan teks gembok)
- `LATEST_UPDATE.md` (Pencatatan riwayat pembaruan v4.2.5)

---

## 🤖 18. DOKUMENTASI FITUR AI GEMINI DI HALAMAN ABOUT, PENYEMPURNAAN PRAYER MODE & REPOSISI BADGE SHOLAT (v4.2.6)

**Tanggal:** 15 September 2026 | **Versi:** 4.2.6

### Latar Belakang & Kebutuhan Pengguna:
1. **Pengecualian Syuruk & Imsak dari Prayer Mode:** Pengguna menanyakan mengapa saat Syuruk juga masuk ke prayer mode, padahal Syuruk dan Imsak bukanlah waktu sholat fardhu sehingga tidak boleh masuk ke mode layar gelap/mati sholat berjamaah.
2. **Reposisi Badge Kapsul Sholat:** Pengguna mendapati badge kapsul di pojok kanan atas di setiap halaman terasa mengganggu gambar latar belakang (*background*). Pengguna meminta agar badge kapsul hanya muncul di halaman jadwal sholat, posisinya tepat di tengah di atas kotak-kotak jadwal sholat dengan jarak sedikit/tipis.
3. **Dokumentasi Fitur Google Gemini AI di Halaman Tentang Aplikasi (`/about`):** Memastikan penambahan fitur AI (AI One-Click Copywriter Pengumuman & Slide TV Mutiara Hadits Hikmah `/hikmah-embed`) sudah diinformasikan dan terdokumentasi rapi di halaman Tentang Aplikasi (`/about`) agar seluruh pengurus dan pengguna memahami fitur canggih ini.

### Perubahan yang Diterapkan:
1. **Penyempurnaan Logika Prayer Mode (`public/js/prayer-engine.js` & `resources/views/partials/prayer-overlay.blade.php`):**
   - Menambahkan pengecualian (*exclusion*) untuk sholat bernilai `'syuruk'`, `'sunrise'`, dan `'imsak'` pada pengecekan deteksi jadwal sholat.
   - Waktu Syuruk dan Imsak tetap berstatus sebagai penanda informasi pergantian waktu dan countdown, namun tidak akan pernah memicu fase Tarhim, Adzan, Iqamah, maupun Layar Gelap Sholat Khusyuk.
2. **Reposisi & Kondisional Badge Kapsul Countdown Sholat:**
   - Membatasi kemunculan badge kapsul sholat berikutnya agar hanya tampil pada slide/halaman utama jadwal sholat (`/utama-embed`).
   - Memindahkan posisi badge kapsul countdown turun pas di atas baris kotak-kotak sholat dengan jarak tipis (*subtle gap*) di tengah-tengah secara presisi, sehingga gambar latar belakang (*wallpaper background*) masjid tidak lagi tertutupi dan tampil bersih nan megah.
3. **Pembaruan Menyeluruh Halaman Tentang Aplikasi (`resources/views/about.blade.php`):**
   - **Badge Versi:** Diperbarui menjadi `v4.2.6` dengan tag baru `<span class="badge"><i class="fas fa-robot"></i> Google Gemini AI Inside</span>`.
   - **Fitur Tampilan TV (TV Fitur 9):** Menambahkan kartu fitur baru *Hadits Hikmah Harian (AI)* (`/hikmah-embed`).
   - **Showcase Banner Khusus AI:** Banner visual megah bertema emerald-indigo dengan penjelasan *AI One-Click Copywriter Pengumuman* (form tambah/edit pengumuman & running text marquee 150 karakter) dan *Kanal TV Hadits Hikmah Harian* (matan Arab font Amiri, terjemahan, hikmah, 24h caching hemat kuota, & 7 fallback hadits offline).
   - **Hak Akses Pengguna (RBAC):** Menambahkan rincian izin konfigurasi API Key Google Gemini untuk Administrator dan pembuatan pengumuman cerdas berbasis AI untuk Petugas/Operator.
   - **Arsitektur Teknologi:** Menambahkan kartu *Google Gemini AI REST API* (arsitektur cURL native yang ringan tanpa dependensi library eksternal).
   - **Panduan Pengoperasian (Accordion 4):** Menambahkan panduan praktis 3 langkah: konfigurasi API Key & tes koneksi, cara menggunakan tombol "Tulis dengan AI" pada pengumuman, serta penayangan slide Hadits Hikmah di rotasi TV.
   - **Riwayat Pembaruan Sistem (Changelog):** Menambahkan catatan rilis resmi Versi 4.2.6 (15 September 2026).

### Berkas yang Dimodifikasi:
- `public/js/prayer-engine.js` (Pengecualian Syuruk & Imsak dari prayer mode)
- `resources/views/partials/prayer-overlay.blade.php` (Proteksi pengecualian Syuruk & Imsak)
- `resources/views/utama-embed.blade.php` (Reposisi badge kapsul countdown pas di atas kotak jadwal sholat)
- `resources/views/about.blade.php` (Penambahan dokumentasi lengkap Google Gemini AI, banner showcase, panduan pengoperasian, dan changelog v4.2.6)
- `LATEST_UPDATE.md` (Pencatatan riwayat pembaruan v4.2.6)

---

---

## 📺 19. PERBAIKAN TAMPILAN MONITOR TV: WARNA KONTRAS TINGGI NOMINAL UANG & RESOLUSI IKON FONT AWESOME LOKAL/SVG (v4.2.7)

**Tanggal:** 15 September 2026 | **Versi:** 4.2.7

### Latar Belakang & Masalah Tampilan TV:
1. **Tulisan/Font Nominal Uang Berwarna Hijau Tidak Terbaca di Layar TV:**
   - Pada slide keuangan (Kas Ambulance `/ambulance-embed`, Kas Masjid `/keuangan-embed`, `/keuangan-summary-embed`, dan Program Infaq `/infaq-embed`), angka nominal penerimaan/pemasukan sebelumnya menggunakan warna hijau (`#00e676`).
   - Karena warna kartu dan tabel berlatar belakang hijau tua (*dark green* / *emerald*), dari jarak pandang jamaah di TV (5-10 meter) tulisan angka hijau tersebut membaur dengan latar belakang (*muddy / low contrast*), sehingga sangat sulit dibaca.
2. **Ikon / Simbol Font Awesome Menjadi Kotak Silang / Tofu Box (`🗌`) di TV:**
   - Di monitor TV (Android TV / WebOS / Tizen / Smart TV Browser), seluruh ikon Font Awesome (seperti ikon mobil ambulans di samping judul, panah transaksi, dompet, pengeras suara running text, kalender, penanda foto ustadz, dan hadits) berubah menjadi kotak segi empat bersilang atau kosong (*tofu character*).
   - **Penyebab Utama:** Halaman-halaman embed TV sebelumnya hanya memanggil Font Awesome via CDN luar (`https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css`). TV browser memblokir font cross-origin (`@font-face`) via aturan ketat CORS atau TV berada di jaringan LAN/hotspot tanpa akses terbuka ke Cloudflare CDN / masalah root certificate TLS pada Smart TV, sehingga font `.woff2` gagal dimuat dan karakter Private Unicode (`\uf0f9`, `\uf0a1`, dsb.) dirender sebagai karakter hilang (*tofu box*). Di PC tampil normal karena PC memiliki koneksi internet bebas dan cache browser modern.

### Solusi & Perubahan yang Diterapkan:
1. **Warna Kontras Tinggi untuk Seluruh Nominal Uang Penerimaan / Pemasukan:**
   - Mengganti warna font nominal uang penerimaan (`.kpi-card.kpi-income .kpi-num`, `.kpi-card.kpi-income .kpi-rp`, `.keuangan td.amount-income .nominal-val`, `.stat-card.income .stat-value`, `.transaction-amount.income`, dan `.stat-pill.terkumpul .stat-value`) menjadi **Putih Bersih Kontras Tinggi (`#ffffff`)** dengan ketebalan ekstra (`font-weight: 800`) dan pendaran bayangan lembut (*crisp text-shadow*).
   - Memberikan rasio kontras tinggi (>15:1) di atas panel kartu dan baris tabel hijau gelap, sehingga nominal uang langsung mencolok, tajam, dan sangat mudah dibaca oleh jamaah dari jarak jauh.
2. **Implementasi Font Awesome Lokal & Dual-Engine (WebFont + SVG Fallback):**
   - Menghubungkan aset lokal Font Awesome Free 6.5.1 yang sudah tersedia di repositori: `public/vendor/fontawesome-free/css/all.min.css` (beserta file webfonts `.woff2` dan `.ttf` lokal).
   - Menambahkan `@import url('../vendor/fontawesome-free/css/all.min.css');` di baris paling atas `public/css/display-theme.css`.
   - Mengunduh dan menyertakan `public/vendor/fontawesome-free/js/all.min.js` (Font Awesome SVG with JS Engine) dengan atribut `defer`.
   - Memperbarui pemanggilan di semua file embed rotasi TV (`ambulance-embed`, `keuangan`, `keuangan-summary`, `jumat`, `pengumuman`, `infaq-embed`, `slide-embed`, `idul-fitri-embed`, `idul-adha-embed`, `utama`, `welcome`, `rotator`, `rotator-outdoor`, `prayer-mode`, `qris/embed`, `hikmah-embed`, dan `layouts/admin`):
     - Prioritas 1: Aset lokal `asset('vendor/fontawesome-free/css/all.min.css')` (100% same-origin, bebas blokir CORS, tanpa perlu internet luar).
     - Prioritas 2: CDN Fallback `cdnjs.cloudflare.com` dengan atribut `crossorigin="anonymous"`.
     - Prioritas 3: Mesin vektor SVG lokal `asset('vendor/fontawesome-free/js/all.min.js')` yang secara otomatis mengganti tag `<i>` menjadi vektor path `<svg>`, sehingga dijamin 100% tampil tajam di browser TV apa pun tanpa pernah menampilkan tofu box.
3. **Penyelarasan Selektor CSS untuk Tag `<i>` dan `<svg>`:**
   - Memperbarui seluruh selektor CSS terkait ikon (misal: `.keuangan h2 i, .keuangan h2 svg`, `.kpi-icon i, .kpi-icon svg`, `.keuangan th i, .keuangan th svg`, `.title-icon-badge i, .title-icon-badge svg`, `.speaker-avatar-fallback i, .speaker-avatar-fallback svg`, `.meta-pill i, .meta-pill svg`, `.running-single-item i, .running-single-item svg`, `.no-data i, .no-data svg`, `.jumat-hadits-box i, .jumat-hadits-box svg`) dengan ukuran lebar dan tinggi eksplisit, sehingga tampilan ikon tetap proporsional dan sempurna baik dalam mode WebFont maupun mode SVG.

### Berkas yang Dimodifikasi:
- `public/css/display-theme.css` (Import lokal Font Awesome di baris pertama)
- `public/vendor/fontawesome-free/js/all.min.js` (Penyediaan mesin vektor SVG lokal)
- `resources/views/ambulance-embed.blade.php` (Pembaruan Font Awesome lokal/SVG, font nominal penerimaan putih kontras tinggi, styling selektor ikon)
- `resources/views/keuangan.blade.php` (Pembaruan Font Awesome lokal/SVG, font nominal penerimaan putih kontras tinggi, styling selektor ikon)
- `resources/views/keuangan-summary.blade.php` (Pembaruan Font Awesome lokal/SVG, nominal pemasukan putih kontras tinggi)
- `resources/views/infaq-embed.blade.php` (Pembaruan Font Awesome lokal/SVG, nominal terkumpul & tabel donatur putih kontras tinggi)
- `resources/views/jumat.blade.php` (Pembaruan Font Awesome lokal/SVG, selektor svg untuk badge judul, no-data, hadits)
- `resources/views/pengumuman.blade.php` (Pembaruan Font Awesome lokal/SVG, selektor svg untuk avatar ustadz, meta-pills)
- `resources/views/slide-embed.blade.php` (Pembaruan Font Awesome lokal/SVG)
- `resources/views/idul-fitri-embed.blade.php` (Pembaruan Font Awesome lokal/SVG)
- `resources/views/idul-adha-embed.blade.php` (Pembaruan Font Awesome lokal/SVG)
- `resources/views/utama.blade.php` (Pembaruan Font Awesome lokal/SVG)
- `resources/views/welcome.blade.php` (Pembaruan Font Awesome lokal/SVG)
- `resources/views/rotator.blade.php` (Pembaruan Font Awesome lokal/SVG)
- `resources/views/rotator-outdoor.blade.php` (Pembaruan Font Awesome lokal/SVG)
- `resources/views/prayer-mode.blade.php` (Pembaruan Font Awesome lokal/SVG)
- `resources/views/qris/embed.blade.php` (Pembaruan Font Awesome lokal/SVG)
- `resources/views/hikmah-embed.blade.php` (Pembaruan Font Awesome lokal/SVG)
- `resources/views/layouts/admin.blade.php` (Pembaruan Font Awesome lokal)
- `resources/views/partials/bottom-section.blade.php` (Penyelarasan selektor svg running text marquee)
- `LATEST_UPDATE.md` (Pencatatan dokumentasi rilis v4.2.7)

---

## 📺 20. CATATAN PEMBARUAN TERAKHIR (16 SEPTEMBER 2026 - v4.2.8): PENINGKATAN VISUAL KALIGRAFI EMAS & DOT PULSE KAPSUL DI LAYAR TV

### Ringkasan Permintaan Pengguna:
1. Memperbesar kaligrafi Allah dan Muhammad di pojok atas kanan dan kiri serta menambahkan efek pendaran agar tampak jelas dan megah saat dilihat di layar TV.
2. Memperbesar dot denyut di dalam kotak kapsul jadwal sholat berikutnya, serta mengganti warnanya dari hijau menjadi kuning/putih bersinar agar lebih kontras dan mudah terlihat di layar TV.

### Solusi & Implementasi Teknis:
1. **Peningkatan Dimensi & Posisi Medali Kaligrafi Allah & Muhammad (`.kaligrafi-medallion`):**
   - Dimensi medali diperbesar secara proporsional dari **92px** menjadi **125px** (naik ~35%), sehingga sangat terbaca jelas dari jarak pandang jauh di ruang utama masjid.
   - Posisi disetel presisi pada `top: 14px`, `left: 28px` (Muhammad), dan `right: 28px` (Allah) agar seimbang dan tidak bertabrakan dengan header utama.
2. **Efek Pendaran Sakral & Ambient Backlight Emas:**
   - Menambahkan pseudo-element `.kaligrafi-medallion::before` dengan gradien radial emas halus (`background: radial-gradient(...)`) dan blur 8px yang membentuk aura backlight bercahaya lembut di belakang medali kaligrafi.
   - Mengintegrasikan animasi denyut aura ambient `medallionAuraPulse` berdurasi 4 detik secara bolak-balik (*infinite alternate*).
   - Memperkuat filter multi-layer drop-shadow emas pada gambar kaligrafi (`drop-shadow(0 0 12px rgba(255, 220, 50, 0.95)) drop-shadow(0 0 28px rgba(255, 175, 0, 0.8)) drop-shadow(0 0 50px rgba(255, 140, 0, 0.5)) drop-shadow(0 10px 20px rgba(0, 0, 0, 0.85))`) serta animasi pernafasan kilau emas `goldenMedallionGlow`.
   - Diimplementasikan di `resources/views/partials/display-theme.blade.php` dan disinkronkan ke `public/css/display-theme.css`.
3. **Pembaruan Dot Denyut Kapsul Sholat Berikutnya (`.npb-pulse-dot`):**
   - Ukuran dot denyut diperbesar dari **10px** menjadi **14px** agar tidak tampak kecil di layar TV beresolusi tinggi (1080p/4K).
   - Warna dot diubah dari hijau gelap menjadi kombinasi **putih dan kuning bersinar**: `radial-gradient(circle, #FFFFFF 25%, #FFF475 60%, #FFD700 100%)`.
   - Pendaran neon diperkuat dengan 3 lapisan pendaran cahaya: `box-shadow: 0 0 10px #FFD700, 0 0 20px rgba(255, 215, 0, 0.9), 0 0 30px rgba(255, 255, 255, 0.75)`.
   - Animasi `@keyframes npbPulse` diperbarui sehingga saat berdenyut, titik putih-kuning memancarkan kilau neon yang sangat kontras di atas latar belakang gelap kapsul.

### Berkas yang Dimodifikasi:
- `resources/views/partials/display-theme.blade.php` (Perbesaran dimensi kaligrafi 125px, penambahan aura backlight emas `::before`, animasi pendaran aura)
- `public/css/display-theme.css` (Sinkronisasi aturan `.kaligrafi-medallion` dan efek pendaran)
- `resources/views/utama.blade.php` (Perbesaran `.npb-pulse-dot` ke 14px, gradien putih-kuning bersinar, animasi pendaran neon)
- `resources/views/jumat.blade.php` (Reposisi kapsul Jadwal Sholat Jumat ke atas tepat di bawah kapsul tanggal/jam dan perampingan kapsul agar background mihrab/interior terlihat luas)
- `public/image/display/background/BG3.png` (Gambar background baru resmi pengganti BG3 lama)
- `public/image/display/background/BG3.jpg` (Aset gambar background baru resolusi tinggi)
- `LATEST_UPDATE.md` (Pencatatan dokumentasi rilis v4.2.8 & reposisi kapsul Jumat)

---

## 📢 21. CATATAN PEMBARUAN TERAKHIR (16 SEPTEMBER 2026 - v4.2.9): PEMUSATAN & PENYEDERHANAAN PENGATURAN TEKS BERJALAN TIAP HALAMAN DISPLAY TV

### Latar Belakang & Masukan Pengguna:
Pengurus/operator masjid memerlukan kejelasan dan kesederhanaan dalam mengelola teks berjalan (*running text*). Mengingat warta pengumuman utama sudah memiliki halaman slide tersendiri (`/pengumuman-embed` dan `/slide-embed`), maka teks berjalan di bagian bawah TV difungsikan murni untuk pesan/hadits tematik yang spesifik untuk masing-masing slide (hadits sholat di slide jadwal sholat, adab khutbah di slide Jumat, hadits sedekah di slide kas/QRIS, kontak darurat di slide ambulance, dll). Oleh karena itu, kolom ganda *"Teks Berjalan Utama / Default (Semua Halaman)"* dihapus dari form dan dipusatkan seutuhnya ke pengaturan per halaman.

### Solusi & Implementasi Teknis:
1. **Penyederhanaan Antarmuka Form Pengaturan Operator (`settings/edit.blade.php`):**
   - Menghapus kolom input ganda `Teks Berjalan Utama / Default (Semua Halaman)` yang sebelumnya memicu kebingungan operator.
   - Memusatkan seluruh pengaturan teks berjalan ke panel **Pengaturan Teks Berjalan Tiap Halaman Display TV**.
   - Setiap panel halaman dilengkapi teks hadits rekomendasi (*placeholder*) yang siap pakai serta indikator status yang jelas (`Teks Kustom` warna hijau jika telah diubah, atau `Rekomendasi Bawaan` jika belum diubah).
2. **Perlindungan Otomatis (*Smart Contextual Fallback*) di Model `AppSetting.php`:**
   - Metode `getRunningTextForPage($pageKey)` diperbarui: Jika operator mengosongkan teks berjalan pada halaman tertentu (misal: halaman Jum'at), sistem secara cerdas menyuplai teks hadits rekomendasi spesifik dari katalog halaman tersebut, bukan teks generik acak.
   - Menjamin bahwa layar TV tidak akan pernah kosong melompong meskipun operator belum sempat mengisi seluruh 15 halaman display.
3. **Preservasi Database di Controller `AppSettingController.php`:**
   - Menyimpan seluruh array pemetaan ke kolom JSON `running_text_pages`.
   - Di balik layar, sistem tetap menyinkronkan kolom `running_text` warisan dari halaman utama (`utama-embed`), sehingga fitur lain (seperti live streaming atau API luar) tetap berjalan 100% tanpa risiko *error*.

### Berkas yang Dimodifikasi:
- `resources/views/settings/edit.blade.php` (Penyederhanaan form, penghapusan kolom default redundan, pemusatan per halaman)
- `app/Models/AppSetting.php` (Smart contextual fallback hadits per halaman pada `getRunningTextForPage`)
- `app/Http/Controllers/AppSettingController.php` (Sinkronisasi otomatis kolom `running_text` dari halaman utama)
- `LATEST_UPDATE.md` (Pencatatan dokumentasi rilis v4.2.9)

---

## 🔒 22. CATATAN PEMBARUAN TERAKHIR (16 SEPTEMBER 2026 - v4.3.0): PROTEKSI PENGUNCIAN FAVICON, LOGO APLIKASI, & BACKGROUND SIDEBAR UNTUK OPERATOR/PETUGAS

### Latar Belakang & Permintaan Pengguna:
Untuk menjaga konsistensi identitas visual dan branding resmi masjid, elemen grafis inti sistem seperti **Favicon**, **Logo Aplikasi**, dan **Background Sidebar** tidak boleh diubah sembarangan oleh akun `operator`/`petugas`. Bagian ini dikhususkan hanya untuk Administrator / Super Admin, sehingga operator dapat tetap fokus mengelola pesan hadits/teks berjalan dan operasional harian display TV.

### Solusi & Implementasi Teknis:
1. **Proteksi Tampilan Antarmuka (`resources/views/settings/edit.blade.php`):**
   - Kolom input file `favicon`, `logo`, dan `background` dinonaktifkan (`disabled`) secara otomatis jika pengguna yang masuk bukan Administrator (`!$isAdmin`).
   - Ditambahkan lencana status gembok yang rapi pada judul label: `<span class="badge badge-light text-muted border"><i class="fas fa-lock text-muted mr-1"></i> Terkunci</span>`.
   - Teks label input file berubah informatif menjadi: *"Terkunci untuk Operator"*.
   - Tombol *"Browse"* dan kotak input diberikan gaya visual tidak aktif (`cursor: not-allowed`, latar `#f8fafc`, teks `#94a3b8`, tombol `#e2e8f0`) sehingga jelas terlihat dan tidak dapat diklik.
   - Area pratinjau gambar aktif (*Favicon Saat Ini*, *Logo Saat Ini*, *Background Saat Ini*) tetap ditampilkan agar operator tetap dapat melihat aset yang sedang digunakan tanpa bisa menggantinya.
2. **Proteksi Backend Controller (`app/Http/Controllers/AppSettingController.php`):**
   - Pemrosesan unggah berkas fisik (`$this->handleFileUpload`) untuk `favicon`, `background`, dan `logo` dibungkus dalam pengecekan ketat `if ($isSuperAdmin)`.
   - Menggagalkan dan mengabaikan upaya modifikasi atau injeksi file favicon/logo/background oleh akun selain Administrator.

### Berkas yang Dimodifikasi:
- `resources/views/settings/edit.blade.php` (Penambahan atribut disabled, lencana status gembok, teks terkunci, dan styling cursor not-allowed untuk operator)
- `app/Http/Controllers/AppSettingController.php` (Penguncian backend upload file favicon, logo, dan background khusus Super Admin)
- `LATEST_UPDATE.md` (Pencatatan dokumentasi rilis v4.3.0)

---

## 🧹 23. CATATAN PEMBARUAN TERAKHIR (16 SEPTEMBER 2026 - v4.3.1): PENYEMBUNYIAN KOLOM IDENTITAS MASJID & MEDIA UNTUK OPERATOR (ANTARMUKA BERSIH & FOKUS 100%)

### Latar Belakang & Masukan Pengguna:
Alih-alih sekadar menampilkan kolom nonaktif dengan ikon gembok yang berpotensi membingungkan atau mengganggu pandangan (*visual clutter*), kolom **Nama Aplikasi**, **Footer Text**, **Favicon**, **Logo Aplikasi**, dan **Background Sidebar** diputuskan untuk disembunyikan seutuhnya dari antarmuka akun Operator / Petugas.

### Solusi & Implementasi Teknis:
1. **Pembersihan Antarmuka Form (`resources/views/settings/edit.blade.php`):**
   - **Nama Aplikasi & Footer Text:** Dibungkus dalam kondisi `@if($isAdmin)...@else...`. Bagi akun operator, kedua kolom ini disembunyikan dari layar dan digantikan dengan elemen `<input type="hidden">`, menjamin nilai asli nama masjid dan footer tetap terkirim dengan aman tanpa mengganggu pemandangan operator.
   - **Media & Gambar (Favicon, Logo, Background Sidebar):** Seluruh baris upload dan pratinjau media dibungkus dalam `@if($isAdmin)...@endif`. Operator tidak lagi melihat kolom upload gambar yang tidak relevan dengan tugas harian mereka.
   - **Tampilan Operator yang Bebas Gangguan:** Begitu operator membuka tab pengaturan, layar langsung menyajikan panel **Pengaturan Teks Berjalan Tiap Halaman Display TV** di urutan teratas, diikuti oleh switch tampilan visual cerdas (Dynamic Theme & Next Prayer Bar).
2. **Fleksibilitas Validasi Backend Controller (`app/Http/Controllers/AppSettingController.php`):**
   - Aturan validasi `nama_aplikasi` disesuaikan: `'required|string|max:255'` khusus untuk Super Admin, dan `'nullable|string|max:255'` untuk operator (dengan fallback nilai tersimpan sebelumnya: `$validated['nama_aplikasi'] ?? $setting->nama_aplikasi`).
   - Mencegah terjadinya error validasi form saat operator menekan tombol simpan.
   - Menjaga hak akses penuh tetap eksklusif hanya untuk Administrator saat login.

### Berkas yang Dimodifikasi:
- `resources/views/settings/edit.blade.php` (Penyembunyian Nama Aplikasi, Footer, Favicon, Logo, Background Sidebar untuk operator)
- `app/Http/Controllers/AppSettingController.php` (Penyelarasan validasi nama_aplikasi dan pengamanan nilai simpan)
- `LATEST_UPDATE.md` (Pencatatan dokumentasi rilis v4.3.1)

---

## 🛠️ 24. CATATAN PEMBARUAN TERAKHIR (16 SEPTEMBER 2026 - v4.3.2): PERBAIKAN STRUKTUR HTML FOOTER KEMBALI KE POSISI BAWAH LAYAR

### Akar Masalah (*Root Cause*):
Pada halaman pengaturan saat diakses oleh akun Operator, elemen `<footer class="sticky-footer">` tiba-tiba terdorong ke samping kanan layar menyerupai kolom vertikal. Setelah dianalisis secara mendalam, ditemukan adanya ketidakseimbangan penutup tag `</div>` pada view `settings/edit.blade.php`:
- Ketika elemen `Nama Aplikasi` dan `Footer Text` dibungkus kondisi `@if($isAdmin)`, tag pembuka `<div class="row">` sebelumnya tidak dieksekusi untuk akun non-admin. Namun tag penutup `</div>` di bawahnya tetap dieksekusi, sehingga terjadi kelebihan satu tag penutup (`</div>`).
- Kelebihan tag penutup ini menutup kontainer `#content-wrapper` secara prematur sebelum waktunya, sehingga elemen `<footer>` terlempar keluar dari alur vertikal dan menjadi anak sejajar (*flex sibling*) dari kontainer utama `#wrapper`, menyebabkannya tampil di sisi kanan layar.

### Solusi & Implementasi Teknis:
1. **Penyeimbangan Struktur Tag HTML (`resources/views/settings/edit.blade.php`):**
   - Menambahkan tag pembuka `<div class="row">` mandiri sebelum blok kontainer panel accordion `Pengaturan Teks Berjalan Tiap Halaman Display TV` (`col-12 mt-2`).
   - Memastikan rasio tag pembuka dan penutup `<div>` seimbang 100% (*Diff: 0*) baik saat dibuka oleh akun Administrator maupun Operator.
2. **Hasil Visual:**
   - Elemen footer aplikasi masjid (`© 2026 Powered by DKM AL JIHAD` dan ikon ornamen Islami) kini **kembali duduk dengan sempurna di bagian paling bawah halaman (*bottom footer*)**, membentang horizontal secara elegan seperti sedia kala.
   - Kolom anomali di sebelah kanan layar otomatis hilang seutuhnya.

### Berkas yang Dimodifikasi:
- `resources/views/settings/edit.blade.php` (Penyeimbangan kontainer row dan tag div untuk alur layout SB Admin 2)
- `LATEST_UPDATE.md` (Pencatatan dokumentasi rilis perbaikan layout v4.3.2)

---

## 📖 25. CATATAN PEMBARUAN TERAKHIR (17 SEPTEMBER 2026 - v4.4.0): FITUR AGENDA MALAM JUM'AT (PEMBACAAN SURAT YAASIIN AUTO-SCROLL FULL ARAB)

### Latar Belakang Kebutuhan Jamaah:
Setiap hari Kamis malam (malam Jum'at) ba'da Maghrib (pukul 18:30) hingga masuk waktu sholat Isya, di Masjid selalu diselenggarakan kegiatan rutin pembacaan Tahlil, Tahmid, dan Surat Yaasiin bersama jamaah. Jika layar TV terus berotasi menampilkan laporan kas atau pengumuman biasa, konsentrasi jamaah dapat terganggu. Pengguna memilih **OPSI B: Quiet Mode dengan Surat Yaasiin Lengkap 83 Ayat Bahasa Arab Tanpa Terjemahan bergulir otomatis (*smooth auto-scroll*)**, serta dilengkapi saklar ON/OFF darurat di Dashboard Operator.

### Implementasi Fitur & Arsitektur Teknis:
1. **Dataset Offline 83 Ayat Uthmani (`resources/data/surah_yasin.json`):**
   - Berisi 83 ayat lengkap Surah Yaasiin teks Arab berharakat resmi mushaf Madinah/Kemenag.
   - Disimpan 100% lokal di dalam proyek sehingga sistem bebas dari ketergantungan API pihak ketiga dan tetap berjalan normal tanpa koneksi internet.
2. **Halaman Khidmat & Megah (`/yasin-embed` & `resources/views/yasin-embed.blade.php`):**
   - **Tema Visual:** *Royal Emerald & Gold Mihrab* yang selaras dengan tema Prayer Mode masjid.
   - **Tipografi:** Menggunakan font Arab kaligrafi *Amiri* dan *Scheherazade New* berukuran besar (`clamp(34px, 3.2vw, 50px)`) dengan nomor ayat ornamen lingkaran emas Islami.
   - **Header Atas Minimalis:** Menampilkan nama masjid, badge `AGENDA MALAM JUM'AT`, jam digital, dan kapsul hitung mundur waktu Isya (`Menuju Isya: [MM:SS]`).
   - **Continuous Smooth Auto-Scroll Engine:** Halaman bergulir secara otomatis dan sangat halus menggunakan `requestAnimationFrame` (pilihan kecepatan: *Santai*, *Normal*, *Cepat*). Dilengkapi jeda otomatis saat layar disentuh/di-scroll mouse oleh operator.
   - **Safety Lock / Auto-Yield ke Mode Sholat:** Halaman secara berkala memonitor `/prayer-mode/status`. Begitu waktu Adzan Isya atau masa hitung mundur adzan Isya tiba, tampilan Yaasiin **langsung mengalah secara otomatis** dan beralih ke *Prayer Mode* Sholat Isya.
3. **Integrasi Mesin Rotasi TV (`rotator.blade.php` & `rotator-outdoor.blade.php`):**
   - Pada hari Kamis pukul 18:30 s/d Adzan Isya, sistem secara otomatis mendeteksi `yasin_active: true` dari status API.
   - Layar TV otomatis mengunci putaran dan memuat `/yasin-embed`.
   - Ketika sholat Isya selesai, TV otomatis kembali berputar normal seperti biasa.
4. **Dashboard Operator (`resources/views/settings/edit.blade.php` & `AppSettingController.php`):**
   - Menambahkan tab khusus **"Agenda Malam Jum'at"** di menu Pengaturan Aplikasi:
     - Saklar *Toggle On/Off*: Aktifkan / Nonaktifkan agenda Yaasiin jika ada agenda mendadak/acara lain.
     - Pilihan Jam Mulai: Default `18:30` (bisa diubah fleksibel).
     - Pilihan Kecepatan Gulir: *Santai (~25 menit)*, *Normal (~18 menit)*, atau *Cepat (~12 menit)*.
     - Tombol *Preview* cepat untuk menguji tampilan layar penuh di tab baru.
5. **Cadangan Demo Standalone (`public/preview-yasin.html`):**
   - File HTML mandiri yang dapat dibuka langsung di Google Chrome / Microsoft Edge tanpa perlu menyalakan server lokal.

### Berkas Baru & Dimodifikasi:
- `database/migrations/2026_09_17_000001_add_yasin_settings_to_app_settings.php` (Migrasi kolom `yasin_mode_enabled`, `yasin_start_time`, `yasin_scroll_speed`)
- `resources/data/surah_yasin.json` (Dataset 83 ayat teks Arab Utsmani Surah Yaasiin)
- `resources/views/yasin-embed.blade.php` (Tampilan layar penuh Surat Yaasiin smooth auto-scroll)
- `public/preview-yasin.html` (Preview HTML mandiri)
- `app/Models/AppSetting.php` (Fillable, casts, dan helper methods `isYasinModeEnabled()`, `getYasinStartTime()`, `getYasinScrollSpeed()`)
- `app/Providers/AppServiceProvider.php` (Auto-provisioning kolom setting baru)
- `app/Http/Controllers/WelcomeController.php` (Method `yasinEmbed()`)
- `app/Http/Controllers/PrayerModeController.php` (Deteksi Kamis malam 18:30 - Isya & flag `yasin_active`)
- `app/Http/Controllers/AppSettingController.php` (Penyimpanan konfigurasi agenda malam Jum'at)
- `routes/web.php` (Rute publik `/yasin-embed`)
- `resources/views/rotator.blade.php` (Otomasi peralihan ke mode Yaasiin di TV dalam)
- `resources/views/rotator-outdoor.blade.php` (Otomasi peralihan ke mode Yaasiin di TV luar)
- `resources/views/settings/edit.blade.php` (Tab & form pengaturan agenda malam Jum'at)
- `LATEST_UPDATE.md` (Pencatatan rilis v4.4.0)

---

## 🎨 26. CATATAN PEMBARUAN TERAKHIR (17 SEPTEMBER 2026 - v4.4.1): PENINGKATAN VISIBILITAS KAPSUL PRAYER MODE (EFEK DENYUT EMAS & NAMA SHOLAT PUTIH KONTRAST TINGGI)

### Masalah Visual Sebelumnya:
Pada tampilan **Mode Sholat (*Prayer Mode*)**, tulisan nama sholat di dalam kotak kapsul (`.prayer-name`) sebelumnya menggunakan gradien emas-putih transparan (`-webkit-background-clip: text; background: linear-gradient(...)`). Karena latar belakang kapsul bernuansa hijau-emas redup, warna gradien teks tersebut memudar (*washout*) dan menyatu dengan background, sehingga nama sholat (misal: "ASHAR", "MAGHRIB", "SHOLAT JUM'AT") sulit terbaca dari jarak jauh oleh jamaah masjid.

### Solusi & Peningkatan Estetika:
1. **Tulisan Nama Sholat Putih Solid Kontras Tinggi (`color: #FFFFFF`):**
   - Mengubah warna font `.prayer-name` menjadi **putih murni (`#FFFFFF`) solid** dengan ketebalan ekstra (`font-weight: 900`).
   - Ditambahkan efek bayangan teks berlapis (*multi-layer text shadow*): bayangan gelap pekat (`0 2px 4px rgba(0,0,0,0.9)` dan `0 4px 14px rgba(0,0,0,0.8)`) serta pendaran lembut putih (`0 0 12px rgba(255,255,255,0.4)`).
   - Rasio kontras melonjak drastis sehingga nama sholat dapat terbaca dengan sangat tajam bahkan dari jarak 15–20 meter.
2. **Efek Denyut Pendaran Emas (*Breathing Golden Pulse*):**
   - Kapsul sholat (`.prayer-badge`) kini dilengkapi animasi denyut bernapas lembut (`@keyframes prayerBadgePulse 2.8s ease-in-out infinite`).
   - Pendaran aura kuning emas (`box-shadow: 0 0 40px rgba(255, 215, 0, 0.85)`) berdenyut perlahan memancarkan kesan sakral, hidup, dan mewah tanpa menyilaukan mata jamaah.
   - Latar belakang dalam kapsul dipertajam menjadi hijau zamrud pekat (`rgba(8, 48, 28, 0.94)` ke `rgba(2, 22, 12, 0.98)`) dengan bingkai emas 2px dan batu permata emas kiri-kanan (`#FFD700`) yang ikut berpendar.
3. **Penyelarasan Berkas:**
   - Diterapkan pada file utama [`resources/views/prayer-mode.blade.php`](file:///c:/Users/anthu/Documents/【Project】/DIGITALv304/resources/views/prayer-mode.blade.php).
   - Diterapkan pada file demo offline [`public/preview-prayer-mode.html`](file:///c:/Users/anthu/Documents/【Project】/DIGITALv304/public/preview-prayer-mode.html).

### Berkas yang Dimodifikasi:
- `resources/views/prayer-mode.blade.php` (Peningkatan styling CSS kapsul denyut emas & teks putih)
- `public/preview-prayer-mode.html` (Penyelarasan demo offline)
- `LATEST_UPDATE.md` (Pencatatan rilis v4.4.1)

---

## 💎 27. CATATAN PEMBARUAN TERAKHIR (17 SEPTEMBER 2026 - v4.4.2): TAMPILAN AWAL STARTUP/SPLASH TV HIJAU ZAMRUD GELAP & LOGO MASJID AL-JIHAD

### Masalah / Kebutuhan Pengguna:
Saat layar TV masjid pertama kali dinyalakan (*TV ON* / startup boot) atau memuat ulang halaman, sistem menampilkan layar pemuatan (*loading overlay*) dengan latar belakang warna cyan/teal polos (`linear-gradient(135deg, #0a4d68, #088395)`) beserta spinner lingkaran abu-abu/kuning kecil di tengah. Pengguna menginginkan warna dasar tersebut diganti menjadi **warna hijau zamrud yang agak gelap (*deep royal emerald*)**, dan di bagian tengahnya disematkan **Logo Masjid Jami' Al-Jihad** agar nuansa TV sejak detik pertama menyala sudah terasa agung, islami, dan prestisius.

### Solusi Desain & Implementasi:
1. **Latar Belakang Gradien Hijau Zamrud Gelap (*Deep Royal Emerald Radial Gradient*):**
   - Mengganti latar belakang `.loading-overlay` dari cyan/teal menjadi radial gradient mewah:
     `background: radial-gradient(ellipse at center, #0d4a2b 0%, #052917 55%, #01140b 100%);`
   - Memberikan ilusi kedalaman pendaran cahaya dari tengah layar dengan nuansa hijau kubah/mihrab masjid.
2. **Logo Masjid Al-Jihad di Tengah Layar dengan Efek Berdenyut (*Pulsing Gold Glow*):**
   - Menempatkan logo resmi Masjid Al-Jihad (`public/img/logo-aljihad-transparent.png` dengan fallback logo dinamis dari `AppSetting`) persis di tengah layar.
   - Dilengkapi drop-shadow bercahaya emas (`filter: drop-shadow(0 0 22px rgba(255, 215, 0, 0.5)) drop-shadow(0 6px 16px rgba(0,0,0,0.7))`).
   - Animasi berdenyut lembut (`@keyframes splashLogoPulse 3s ease-in-out infinite alternate`).
3. **Cincin Putar Emas Elegan (*Golden Rotating Orbit Ring*):**
   - Mengelilingi logo dengan cincin loading putar halus beraksen emas (`border-top: 3px solid #FFD700; border-right: 3px solid rgba(255, 215, 0, 0.6)`) yang berputar kontinu (`@keyframes splashSpin 1.4s linear infinite`).
4. **Tipografi & Indikator Status Memuat (*Glass Pill Indicator*):**
   - Menampilkan nama masjid: `MASJID JAMI' AL JIHAD` (dinamis dari `$settings->nama_aplikasi`) berfont tebal *Poppins* dengan bayangan emas.
   - Kapsul kaca hitam transparan (`.splash-loading-pill`) bertuliskan *"MEMUAT TAMPILAN..."* lengkap dengan titik emas berdenyut (`.splash-dot`).
5. **Diterapkan Serentak pada Dual-Engine Rotator:**
   - [`resources/views/rotator.blade.php`](file:///c:/Users/anthu/Documents/【Project】/DIGITALv304/resources/views/rotator.blade.php) (Layar TV Utama Dalam Masjid)
   - [`resources/views/rotator-outdoor.blade.php`](file:///c:/Users/anthu/Documents/【Project】/DIGITALv304/resources/views/rotator-outdoor.blade.php) (Layar TV Serambi/Luar Masjid)
   - Tetap kompatibel 100% dengan mekanisme JavaScript penghilangan otomatis `#loadingOverlay` saat iframe pertama berhasil dimuat (`onFrameLoad`).

### Berkas yang Dimodifikasi:
- `resources/views/rotator.blade.php` (CSS & markup HTML splash loading emerald + logo)
- `resources/views/rotator-outdoor.blade.php` (CSS & markup HTML splash loading emerald + logo)
- `scratch/preview_splash.html` (Pratinjau HTML mandiri)
- `LATEST_UPDATE.md` (Pencatatan rilis v4.4.2)

---

## 🔒 28. PRINSIP PENGEMBANGAN BERIKUTNYA (ATURAN WAJIB)

Setiap AI Agent atau pengembang yang bekerja pada proyek ini **WAJIB MEMATUHI**:
1. **Preservasi Nilai Default & Fallback Aman:** Selalu sertakan operator *null coalescing* (`?? true`, `?? 50`) pada Blade view dan Controller, serta perlindungan `Schema::hasColumn()` agar aplikasi tidak pernah *crash* jika kolom baru belum dimigrasi di database hosting/lokal.
2. **Sinkronisasi Git Otomatis:** Setelah menyelesaikan modifikasi atau perbaikan, **WAJIB langsung melakukan commit dan push ke branch `main` GitHub**.
3. **Pembaruan Dokumen Ini:** Setiap kali ada fitur baru atau perubahan alur, perbarui file `LATEST_UPDATE.md` ini agar riwayat pekerjaan selalu berkesinambungan.

---
*Terakhir Diperbarui: 17 September 2026 (Splash Loading Screen TV Hijau Zamrud & Logo Masjid Al-Jihad v4.4.2) &bull; Komitmen: Sinkron Penuh dengan GitHub `origin/main`.*



