# DOKUMEN STATUS & RANGKUMAN PROYEK DIGITALv304
**Sistem Informasi Digital Masjid Jami' Al Jihad**
*Terakhir Diperbarui: 10 September 2026*

Dokumen ini disusun untuk memudahkan pemindahan kerja ke laptop baru atau server baru agar pengembang maupun AI Assistant dapat langsung melanjutkan pekerjaan dengan pemahaman konteks 100%.

---

## 1. Identitas Proyek & Repositori
- **Nama Aplikasi:** Sistem Informasi Digital Masjid Jami' Al Jihad (DIGITALv304)
- **Versi:** WEB APP. VERSION 3.0.4
- **Framework:** Laravel 10 / PHP 8.x + Bootstrap 4 (SB Admin 2)
- **Repositori GitHub:** `https://github.com/mydowndrive-ops/digitalaljihad001.git`
- **Branch Utama:** `main`

---

## 2. Fitur & Modifikasi Terbaru yang Sudah Selesai
1. **Redesain Halaman Login (`resources/views/auth/login.blade.php` & `resources/views/layouts/auth.blade.php`):**
   - **Tema Visual:** Nuansa Hijau NU (Nahdlatul Ulama) dipadukan ornamen geometris islami arabesque dan aksen emas kuning (`#ffd700`).
   - **Posisi Logo:** Logo Masjid Al-Jihad (`public/img/logo-aljihad.png`) berada di **paling atas-center** di dalam lingkaran berlatar putih dengan bingkai emas bercahaya (*halo gold ring*).
   - **Kalimat Basmalah:** Huruf Arab emas (`بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ`) tepat di bawah logo.
   - **Ayat Al-Qur'an:** Menampilkan QS. Hud: 88 (*"Dan tidak ada taufikku melainkan dengan pertolongan Allah"*).
   - **Fitur Password:** Tombol icon mata (*FontAwesome* `fa-eye` / `fa-eye-slash`) di sisi kanan input untuk tampilkan/sembunyikan kata sandi.
   - **Navigasi & Bantuan:**
     - Tombol **"Kembali ke Display TV"** (`<i class="fas fa-tv"></i>`).
     - Tombol **"Butuh Bantuan"** langsung menghubungkan ke WhatsApp Admin ke nomor `087758767000`.
   - **Footer 4 Baris:**
     - Baris 1: `System Informasi Digital ini dibuat dan di kembangkan oleh :`
     - Baris 2: `Masjid Jami' Al Jihad`
     - Baris 3: `WEB APP. VERSION 3.0.4`
     - Baris 4: `[Icon Copyright] 2026 Powered by MASJID AL JIHAD GRAHA ASRI`
2. **Jadwal Petugas Sholat Jumat (`resources/views/jumat.blade.php` & controller):**
   - Penggabungan kolom Imam dan Khotib menjadi satu kolom (*Imam & Khotib*).
   - Penambahan kolom *Bilal* dan *Nama Bilal* di bawah Muadzin.
   - Integrasi upload dan penampilan foto petugas/imam.

---

## 3. Langkah-Langkah Setup di Laptop Baru

### A. Clone Kode dari GitHub
```bash
git clone https://github.com/mydowndrive-ops/digitalaljihad001.git
cd digitalaljihad001
```

### B. Salin File Konfigurasi `.env`
- File `.env` **tidak ada di GitHub** (karena memuat password database dan App Key rahasia).
- Ambil file `.env` dari laptop lama (via flashdisk atau drive pribadi) dan letakkan di root folder laptop baru.

### C. Import Database MySQL
1. Buka database tool di laptop lama (phpMyAdmin / HeidiSQL / Laragon).
2. Export database proyek ke file `.sql`.
3. Di laptop baru, buat database baru (misal: `digitalv304`) dan import file `.sql` tersebut.
4. Sesuaikan pengaturan `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` di file `.env`.

### D. Install Dependencies & Storage Link
Buka terminal di folder proyek laptop baru dan jalankan:
```bash
composer install
npm install
php artisan storage:link
php artisan key:generate (jika belum ada di .env)
```

### E. Menjalankan Aplikasi
```bash
php artisan serve
```
Akses di browser: `http://127.0.0.1:8000`

---

## 4. Cara Melanjutkan Obrolan dengan AI di Laptop Baru
Ketika membuka Antigravity IDE di laptop baru:
1. Buka folder proyek `DIGITALv304`.
2. Pada panel chat, kirimkan pesan pembuka berikut:
   > *"Halo Antigravity, saya melanjutkan pengembangan proyek DIGITALv304 (Sistem Informasi Masjid Jami' Al Jihad) dari laptop sebelumnya. Silakan baca file `RESUME_PROJECT.md` dan struktur repositori untuk memahami seluruh status proyek kita, lalu siap menerima instruksi berikutnya."*
3. AI akan secara otomatis membaca berkas ini dan seluruh kode repositori, lalu siap melanjutkan pekerjaan tepat dari titik terakhir.
