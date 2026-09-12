# 📖 DOKUMENTASI & PANDUAN PENGGUNAAN SISTEM INFORMASI DISPLAY MASJID
## Masjid Jami' Al-Jihad (Digital Display & Management System v3.04)

---

## DAFTAR ISI
1. [Informasi Umum & Arsitektur Sistem](#1-informasi-umum--arsitektur-sistem)
2. [Cara Kerja Tampilan TV & Rotasi Halaman (Rotator & Prayer Mode)](#2-cara-kerja-tampilan-tv--rotasi-halaman)
3. [Hak Akses Berdasarkan Peran (User Roles)](#3-hak-akses-berdasarkan-peran-user-roles)
4. [Dashboard & Pengaturan Bendahara (Keuangan & Infaq)](#4-dashboard--pengaturan-bendahara)
5. [Dashboard & Pengaturan Petugas/Operator (Operasional Layar TV)](#5-dashboard--pengaturan-petugasoperator)
6. [Panduan Pengoperasian Praktis (Langkah demi Langkah)](#6-panduan-pengoperasian-praktis)

---

## 1. INFORMASI UMUM & ARSITEKTUR SISTEM

### 1.1 Deskripsi Aplikasi
**Sistem Informasi Display Masjid Jami' Al-Jihad** adalah aplikasi berbasis web yang dirancang khusus untuk memadukan fungsi **Media Informasi Digital Jamaah (Smart TV Kiosk Display)** dengan **Sistem Manajemen Administrasi & Keuangan Masjid (DKM Management Dashboard)**.

Aplikasi ini hadir untuk memodernisasi tata kelola masjid secara transparan, profesional, dan akuntabel, mulai dari otomatisasi jadwal sholat harian, pengelolaan jadwal sholat Jumat dan hari raya, transparansi kas umum & ambulans, hingga penggalangan infaq melalui QRIS digital.

### 1.2 Arsitektur Teknologi
* **Backend Framework:** [Laravel](https://laravel.com/) (PHP 8.3+) dengan struktur MVC (*Model-View-Controller*) yang kokoh, modular, dan terstruktur.
* **Frontend Templating & Styling:** Laravel Blade Engine dipadukan dengan **Vanilla CSS Modern (Glassmorphism & High-Contrast Typography)**, bebas ketergantungan berlebih agar performa rendering di prosesor Smart TV / Android TV Box tetap ringan dan gegas (*smooth 60fps*).
* **Interaktivitas Real-Time:** Menggunakan Vanilla JavaScript ES6+ dan asynchronous polling (AJAX Fetch API) untuk deteksi waktu sholat, rotasi tayangan, dan pencegahan layar berkedip (*anti-flicker dual-buffer*).
* **Ekspor Dokumen:** Terintegrasi dengan `barryvdh/laravel-dompdf` untuk cetak laporan PDF resmi siap edar, serta `maatwebsite/excel` untuk ekspor pembukuan ke Microsoft Excel.

### 1.3 Integrasi Basis Data & Layanan Cloud
* **Database Engine:** MySQL / MariaDB (Lokal) & TiDB Serverless (Cloud Deployment), dikelola menggunakan Laravel Eloquent ORM, migrasi skema tabel, dan relasi data berintegritas tinggi.
* **Sinkronisasi Jadwal Sholat Resmi:** Terintegrasi langsung dengan **Open API Bimas Islam Kementerian Agama Republik Indonesia** untuk penentuan waktu sholat astronomis presisi khusus wilayah **Provinsi Jawa Barat - Kabupaten Bekasi (Kode Wilayah: 1203)**, dilengkapi algoritma *fallback calculation* jika server pusat pemerintah sedang *maintenance*.
* **Infrastruktur Email Darurat (Anti-Timeout):** Menggunakan **Resend API via HTTPS (Port 443)** untuk pengiriman tautan pemulihan kata sandi Super Admin secara instan tanpa khawatir terkena pemblokiran port SMTP pada penyedia *cloud hosting* seperti Render.com.

---

## 2. CARA KERJA TAMPILAN TV & ROTASI HALAMAN

### 2.1 Konsep Layar TV Publik (Kiosk Rotator)
Halaman publik utama masjid diakses melalui alamat utama (`/` atau rute `rotator`). Halaman ini dirancang khusus untuk menyala nonstop pada Smart TV / TV LED di masjid tanpa perlu campur tangan operator setiap saat.

```
+-------------------------------------------------------------------------+
|                  ROTATOR ENGINE (rotator.blade.php)                     |
|                                                                         |
|  [Iframe Aktif (Visible)]  <--->  [Iframe Cadangan (Preload Hidden)]    |
|   (Efek Fade Transisi Mulus tanpa kedip / Anti White Flash Screen)       |
|                                                                         |
|  +-------------------------------------------------------------------+  |
|  | Polling Otomatis /prayer-mode/status (Tiap 3 Detik)               |  |
|  | - Jika Waktu Sholat Tiba: Interupsi Otomatis Masuk ke PRAYER MODE |  |
|  | - Jika Kondisi Normal   : Melanjutkan Rotasi Slide Biasa          |  |
|  +-------------------------------------------------------------------+  |
+-------------------------------------------------------------------------+
```

### 2.2 Mekanisme Rotasi Halaman (Dual-Iframe Buffering)
Agar pergantian slide di layar TV tidak mengalami kedipan putih (*white flash*) atau jeda pemuatan yang mengganggu estetika:
1. **Dua Kanvas Iframe (`#frameA` dan `#frameB`):** Saat slide pertama sedang ditampilkan di TV, sistem diam-diam memuat (*preload*) konten slide berikutnya di iframe kedua yang tersembunyi.
2. **Transisi Silang Mulus (*Cross-Fade*):** Ketika interval waktu tayang slide habis (misal 15 detik), sistem menukar visibilitas kedua iframe menggunakan efek transisi opasitas (*opacity fade*).
3. **Pilihan Modul Slide (Dapat Diatur via `RotationController`):**
   * **Jadwal Sholat Utama (`utama-embed`):** Jam digital akurat, kalender Masehi & Hijriyah, status auto-update Kemenag, dan hitung mundur waktu sholat berikutnya.
   * **Slide Informasi & Pengumuman (`slide-embed` & `pengumuman-embed`):** Poster kajian ilmiah, sertifikat arah kiblat Kemenag, running text, dan pesan penting DKM.
   * **Jadwal Petugas Sholat Jumat (`jumat-embed`):** Menampilkan Khatib, Imam, Muadzin, dan waktu sholat Jumat pekan berjalan.
   * **Ringkasan Kas Masjid (`keuangan-summary-embed` / `keuangan-embed`):** Transparansi total penerimaan, pengeluaran, dan sisa saldo kas masjid.
   * **Kas & Layanan Ambulans (`ambulance-embed`):** Transparansi kas operasional ambulans serta nomor darurat layanan antar-jemput pasien/jenazah.
   * **Program Infaq Bertarget (`infaq-embed`):** Menampilkan progres pengumpulan donasi (misal: Renovasi Masjid, Karpet, Sound System) lengkap dengan persentase dan daftar muhsinin/donatur.
   * **QRIS Donasi (`qris-embed`):** Kode QR Standar Pembayaran Nasional untuk memudahkan jamaah berinfaq via m-Banking/E-Wallet.
   * **Sholat Hari Raya (`idul-fitri-embed` & `idul-adha-embed`):** Tayangan khusus musiman menjelang sholat Ied.

### 2.3 Mekanisme Prioritas Sholat (Prayer Mode Engine)
Sistem memiliki pengaman prioritas ibadah (*Prayer Mode Interruption*) yang dikendalikan oleh `PrayerModeController`. Ketika waktu sholat menjelang tiba, rotasi slide biasa **otomatis dihentikan paksa** dan digantikan oleh 4 fase ibadah:

| Fase | Waktu / Trigger | Tampilan Visual Layar TV | Perilaku Audio Sistem |
| :--- | :--- | :--- | :--- |
| **1. Countdown (Menuju Adzan)** | 5 s/d 10 Menit Sebelum Waktu Sholat Masuk | Tema hijau gelap islami, hitung mundur menit & detik, pesan adab bersiap & hadits keutamaan sholat berjamaah. | **Audio Tarhim Otomatis Berbunyi** (`Subuh.mp3` untuk Subuh, `Isya.mp3`/`Dzuhur.mp3`/`Ashar.mp3`/`Maghrib.mp3` untuk sholat lainnya). |
| **2. Adzan (Waktu Tiba)** | Saat Hitungan Mundur Mencapai `00:00` | Pengumuman visual *"WAKTU ADZAN TELAH TIBA"*, teks ajakan mendengarkan dan menjawab adzan. | **Audio Tarhim Otomatis Berhenti (Mute/Reset).** Hening agar Muadzin mengumandangkan adzan di masjid. |
| **3. Iqamah** | Selesai Adzan s/d Waktu Masuk Sholat | Hitung mundur waktu jeda sholat sunnah & anjuran berdoa antara adzan & iqamah. | Hening / audio pengingat iqamah. |
| **4. Sholat Berlangsung** | Selama Sholat Berjamaah Berlangsung (10 Menit) | Tampilan redup/minimalis: *"Luruskan & Rapatkan Shaf, Harap Matikan/Silent Nada Dering HP"*. | Hening total demi kekhusyukan jamaah. |

---

## 3. HAK AKSES BERDASARKAN PERAN (USER ROLES)

Sistem membagi wewenang pengguna ke dalam **3 Tingkat Peran (Role-Based Access Control)** untuk menjaga keamanan data dan mempermudah pembagian tugas pengurus DKM:

```
                  +-------------------------------+
                  |      SUPER ADMIN (DKM)        |
                  | Akses Penuh: Konfigurasi,     |
                  | Pengguna, Password, TV & Kas  |
                  +---------------+---------------+
                                  |
         +------------------------+------------------------+
         |                                                 |
+--------v----------------------+         +----------------v----------------------+
|       PETUGAS / OPERATOR      |         |               BENDAHARA               |
| - Pengaturan Konten Layar TV  |         | - Pencatatan Kas Masjid               |
| - Jadwal Sholat & Sholat Jumat|         | - Pencatatan Kas Ambulans             |
| - Pengumuman & Agenda Kajian  |         | - Program Infaq Bertarget             |
| - Upload Slide & Urutan Rotasi|         | - Cetak Laporan Keuangan & PDF        |
+-------------------------------+         +---------------------------------------+
```

### Rincian Perbedaan Hak Akses:
1. **Super Admin (`role:admin`):**
   * Memiliki akses tak terbatas ke seluruh fitur sistem.
   * Mengatur identitas masjid (Nama, Logo, Favicon, Banner Background, Footer, Running Text).
   * Mengatur koordinat astronomis jadwal sholat Kemenag RI & parameter auto-update.
   * Manajemen akun pengguna (*Users Management*) dan pembuatan kata sandi.
   * Akses jalur pemulihan kata sandi darurat via Resend API & URL Reset Rahasia.
   * Mengelola akun QRIS Donasi resmi masjid.
2. **Petugas / Operator (`role:petugas`):**
   * Fokus pada pengelolaan visual dan informasi harian jamaah yang tampil di TV.
   * Memperbarui petugas sholat Jumat mingguan, Idul Fitri, dan Idul Adha.
   * Menambah dan menyunting agenda kajian rutin dan tabligh akbar.
   * Mengunggah slide poster digital (PNG, JPG, WebP) dan mengatur durasi tayangnya.
   * *Catatan Keamanan:* Petugas **tidak dapat** melihat atau memanipulasi data pembukuan kas masjid.
3. **Bendahara (`role:bendahara`):**
   * Didedikasikan untuk transparansi finansial masjid.
   * Menginput seluruh arus kas masuk (kotak amal Jumat, donasi transfer, infaq harian).
   * Menginput seluruh pengeluaran operasional (listrik, kebersihan, pemeliharaan, santunan).
   * Mengelola kas khusus layanan mobil ambulans DKM.
   * Mengelola kampanye program infaq bertarget (progres bar donasi).
   * Menghasilkan dan mencetak laporan keuangan resmi format Excel dan PDF.
   * *Catatan Keamanan:* Bendahara **tidak dapat** mengubah konfigurasi sistem atau slide layar TV.

---

## 4. DASHBOARD & PENGATURAN BENDAHARA

Bendahara memiliki ruang kerja khusus yang menjamin pencatatan keuangan akurat, cepat, dan transparan:

### 4.1 Pencatatan Kas Umum Masjid (`/keuangan`)
* **Pencatatan Transaksi:** Input pemasukan dan pengeluaran kas dengan data tanggal, kategori, nama transaksi/pemberi infaq, nominal (Rp), dan keterangan.
* **Kategori Transaksi Lengkap:**
  * *Pemasukan:* Infaq Kotak Amal Jumat, Infaq Harian, Zakat Mal/Fitrah, Donasi Pembangunan, Bagi Hasil Rekening Bank, dll.
  * *Pengeluaran:* Honorarium Khatib/Imam, Operasional Listrik/PLN & Air/PDAM, Kebersihan & Perlengkapan, Perbaikan Sarana/Prasarana, Santunan Yatim & Dhuafa.
* **Auto Saldo:** Sistem secara otomatis menghitung akumulasi total pemasukan, total pengeluaran, dan sisa saldo kas secara akurat.

### 4.2 Pencatatan Kas Ambulans Masjid (`/ambulance`)
* Khusus mencatat sirkulasi dana operasional armada mobil ambulans Masjid Jami' Al-Jihad.
* Meliputi infaq operasional ambulans dari para muhsinin serta pengeluaran rutin: BBM (bensin/solar), servis berkala, ganti oli, tol, dan apresiasi pengemudi ambulans.
* Dilengkapi tampilan khusus pada rotasi TV (`ambulance-embed`) yang menampilkan sisa saldo kas ambulans serta nomor hotline yang dapat dihubungi keluarga pasien.

### 4.3 Program Infaq & Donasi Bertarget (`/program-infaq`)
* Memfasilitasi penggalangan dana untuk program pembangunan khusus (misalnya: *Renovasi Tempat Wudhu*, *Pengadaan Genset Masjid*, *Pembelian Karpet Baru*).
* **Fitur Utama:**
  * Menentukan nama program, batas target nominal dana, dan deskripsi tujuan.
  * Menandai status program (*Aktif* atau *Selesai*). Program yang diaktifkan akan **langsung muncul di layar TV masjid (`infaq-embed`)**.
  * Input donasi masuk per individu/hamba Allah dengan tanggal dan nominal.
  * Menampilkan bilah progres (*progress bar*) persentase capaian target secara langsung.

### 4.4 Laporan Keuangan & Cetak PDF (`/laporan/keuangan`)
* **Penyaringan Data (*Filtering*):** Bendahara dapat memfilter rekap laporan berdasarkan rentang tanggal, bulan, maupun tahun buku tertentu.
* **Ekspor Microsoft Excel (`.xlsx`):** Menghasilkan tabel pembukuan siap olah untuk rapat pengurus DKM.
* **Cetak PDF Resmi:** Menghasilkan lembar laporan keuangan bertanda tangan pengurus DKM dengan tata letak kop surat resmi masjid, cocok untuk ditempel di papan pengumuman atau dibagikan saat laporan sholat Jumat.

---

## 5. DASHBOARD & PENGATURAN PETUGAS / OPERATOR

Petugas/Operator memegang kendali penuh atas informasi edukatif dan jadwal ibadah yang dinikmati jamaah:

### 5.1 Manajemen Jadwal Sholat (`/jadwal_sholat`)
* Melihat data jadwal sholat 5 waktu ditambah Imsak, Terbit, dan Dhuha.
* Melakukan koreksi menit manual jika dewan hisab rukyat masjid menetapkan penyesuaian khusus (*misal: ikhtiyat +2 menit*).

### 5.2 Petugas Sholat Jumat (`/sholat_jumat`)
* Memperbarui informasi petugas sholat Jumat untuk pekan ini dan pekan depan:
  * **Khatib:** Nama ustadz/kyai pengisi khutbah.
  * **Imam:** Nama imam sholat Jumat.
  * **Muadzin:** Nama muadzin pengumandang adzan.
  * **Bilal / Muraqqi:** Nama petugas tarhim/bilal Jumat.
  * **Waktu Dimulai:** Waktu pembukaan ibadah Jumat.

### 5.3 Petugas Sholat Idul Fitri & Idul Adha (`/idul-fitri` & `/idul-adha`)
* Menampilkan informasi pelaksanaan sholat Idul Fitri (1 Syawal) dan Idul Adha (10 Dzulhijjah):
  * Nama Khatib & Tema Khutbah.
  * Nama Imam Sholat Id.
  * Lokasi Sholat (Masjid Utama / Lapangan Terbuka).
  * Waktu Pelaksanaan dan ketentuan khusus (Zakat Fitrah / Penyerahan Hewan Qurban).

### 5.4 Pengumuman & Agenda Kajian (`/pengumuman` & `/agenda_kajian`)
* **Pengumuman:** Membuat siaran duka cita, informasi penerimaan zakat, kegiatan gotong royong, atau barang hilang/tertinggal.
* **Agenda Kajian:** Menyusun jadwal taklim rutin (Waktu/Hari, Judul Kajian, Nama Pemateri, Kitab Rujukan, dan Tempat).

### 5.5 Manajemen Slide Digital & Media (`/slides`)
* Mengunggah poster kegiatan atau pengumuman visual dalam format gambar (resolusi terbaik: 1920x1080 Full HD).
* Dilengkapi fitur penyimpanan cadangan *Base64 Resilience* agar gambar tidak mudah hilang meski server di-restart.
* Pengaturan masa berlaku tayang slide (slide otomatis tidak muncul jika masa tayangnya sudah lewat).

### 5.6 Pengaturan Rotasi Tampilan Layar TV (`/rotation`)
* Mengaktifkan atau menonaktifkan halaman mana saja yang berhak muncul di layar Smart TV masjid.
* Mengatur durasi waktu tampil masing-masing slide dalam hitungan detik (contoh: Jadwal Sholat 20 detik, Keuangan 15 detik, Slide Kajian 25 detik).

---

## 6. PANDUAN PENGOPERASIAN PRAKTIS (LANGKAH DEMI LANGKAH)

### 6.1 Panduan untuk Operator: Menyalakan & Menyetel TV Layar Masjid
1. Nyalakan TV LED / Smart TV masjid dan pastikan perangkat terhubung ke internet (WiFi masjid).
2. Buka aplikasi browser (Google Chrome disarankan) pada TV Box / Mini PC yang terhubung ke TV.
3. Ketik alamat website masjid: `https://digital-aljihad.onrender.com/` (atau alamat lokal `http://localhost:8000`).
4. Tekan tombol **F11** di keyboard untuk masuk ke **Layar Penuh (Full Screen Mode)** agar bilah alamat browser dan menu Windows hilang dari pandangan jamaah.
5. Lakukan satu kali klik bebas di area layar menggunakan mouse untuk membuka izin audio browser (*browser autoplay policy*).
6. Layar akan otomatis berputar menampilkan slide informasi, dan otomatis beralih ke Mode Sholat saat waktu adzan tiba.

### 6.2 Panduan untuk Operator: Mengunggah Poster Kajian Baru
1. Buka halaman login di laptop/HP: `/login`.
2. Masukkan akun Petugas/Admin Anda.
3. Pada menu navigasi sebelah kiri, klik menu **Slide Informasi**.
4. Klik tombol hijau **Tambah Slide Baru**.
5. Isi **Judul Slide** (contoh: *Kajian Akhir Pekan Ustadz Fulan*), lalu pilih file gambar poster kajian Anda.
6. Tentukan tanggal masa aktif tayang jika diperlukan, lalu klik **Simpan**.
7. Poster baru akan otomatis masuk ke antrean rotasi TV masjid dalam hitungan detik.

### 6.3 Panduan untuk Bendahara: Mencatat Infaq Kotak Amal Jumat
1. Login menggunakan akun Bendahara Anda.
2. Masuk ke menu **Kas Masjid** (`/keuangan`).
3. Klik tombol **Tambah Transaksi Kas**.
4. Pilih **Jenis Transaksi:** `Pemasukan`.
5. Pilih **Kategori:** `Infaq Kotak Amal Jumat`.
6. Masukkan **Tanggal:** Pilih tanggal hari Jumat bersangkutan.
7. Masukkan **Nominal (Rp):** (contoh: `4850000` tanpa tanda titik/koma).
8. Beri catatan pada kolom **Keterangan:** (contoh: *Penerimaan kotak amal sholat Jumat pekan 2*).
9. Klik **Simpan Transaksi**. Sisa saldo kas di layar TV masjid akan otomatis terbarui secara langsung!

### 6.4 Panduan untuk Bendahara: Mengaktifkan Program Donasi Bertarget
1. Masuk ke menu **Program Infaq** (`/program-infaq`).
2. Klik **Tambah Program Infaq Baru**.
3. Masukkan judul (misal: *Pengadaan Karpet Masjid Shaf Utama*), target nominal (misal: `35000000`), dan deskripsi.
4. Klik tombol **Aktifkan Program Ini**.
5. Layar TV masjid pada slide Program Infaq (`/infaq-embed`) akan langsung menampilkan penggalangan dana karpet tersebut lengkap dengan *progress bar* capaian. Setiap kali ada donasi masuk, bendahara cukup klik **Input Donasi** pada program tersebut.

### 6.5 Panduan untuk Bendahara: Mencetak Laporan PDF Bulanan
1. Masuk ke menu **Laporan Kas** -> **Cetak Laporan**.
2. Pilih filter **Bulan** dan **Tahun** yang ingin dilaporkan.
3. Klik tombol merah **Cetak PDF**.
4. File dokumen PDF resmi bertata letak rapi akan terunduh otomatis, siap dicetak pada kertas printer HVS/A4 dan ditandatangani oleh Ketua DKM & Bendahara.

---

> 📌 **Catatan Keamanan untuk Super Admin:**  
> Jika suatu saat Super Admin lupa kata sandi login, Anda dapat menggunakan tombol **"Lupa Kata Sandi?"** pada halaman login (email pemulihan akan dikirimkan secara instan via Resend API ke `archived.aljihad@gmail.com`), atau membuka rute darurat langsung di peramban:  
> `https://digital-aljihad.onrender.com/reset-admin-password/KATA_SANDI_BARU_ANDA`
