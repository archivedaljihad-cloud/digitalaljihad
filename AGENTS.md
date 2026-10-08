# ATURAN WAJIB PENGEMBANGAN AI AGENT & STANDAR OPERASIONAL PROSEDUR (SOP)

> **SOP UTAMA PROYEK:**  
> **"Segera sinkronisasi data dengan cloudflare, supabase, github dan folder mandiri lokal sehingga tampilan di TV tersinkron secepatnya secara realtime."**

Dokumen ini adalah aturan tetap (*standing rules*) dan Standar Operasional Prosedur (SOP) wajib untuk setiap AI Agent atau programmer yang berpasangan dengan pengguna di repositori ini:

1. **SOP SINKRONISASI MENYELURUH (CLOUDFLARE, SUPABASE, GITHUB, & FOLDER MANDIRI LOKAL):**
   - **MANDAT UTAMA:** *"Segera sinkronisasi data dengan cloudflare, supabase, github dan folder mandiri lokal sehingga tampilan di TV tersinkron secepatnya secara realtime."*
   - **DATA CENTER & ACTIVE PRODUCTION:**
     - **Cloudflare Workers (`https://digitalaljihad.my.id/`)**: Hosting dan edge caching produksi utama untuk tampilan TV Display dan halaman Admin.
     - **Supabase Cloud Database**: Basis data cloud utama dan mesin transmisi realtime (*Postgres Changes* & *Broadcast Channel*) untuk sinkronisasi instan (<100ms) ke Smart TV masjid.
     - **GitHub (`main`)**: Repositori pusat kode sumber aplikasi.
   - **FOLDER LOKAL SEBAGAI BACKUP:**
     - Folder lokal `C:\Users\anthu\Documents\【Digital WebSTATIS】\` berfungsi sebagai salinan cadangan arsip (*cold backup*).
   - **TAHAPAN OTOMATIS SETIAP SELESAI PENAMBAHAN FITUR / PERBAIKAN KODE:**
     Setiap kali selesai melakukan penambahan fitur atau perbaikan kode apapun, Anda **WAJIB LANGSUNG SECARA OTOMATIS MENJALANKAN (JANGAN MENUNGGU PERINTAH DARI PENGGUNA)**:
     a. **Pengujian Regresi Otomatis**: Jalankan `npm test` (memverifikasi 48+ assertion checks: sintaks seluruh file HTML/JS, simulasi 24 jam sholat, proteksi watchdog, konsistensi cache). Jika ada 1 saja yang gagal, **DILARANG COMMIT/DEPLOY** sebelum diperbaiki!
     b. **Supabase Cloud**: Pastikan tabel / `app_settings` / skema cloud tersinkron dengan payload terkini.
     c. **GitHub**: Lakukan `git add .`, `git commit -m "..."`, dan `git push origin main`.
     d. **Cloudflare Workers**: Jalankan `npx wrangler deploy` untuk langsung memperbarui aset di **`https://digitalaljihad.my.id/`**.
     e. **Folder Lokal**: Salin berkas pembaruan ke folder cadangan lokal `C:\Users\anthu\Documents\【Digital WebSTATIS】\`.

2. **SOP PEMBERSIHAN & INVALIDASI CACHE USANG (SERVICE WORKER, PERAMBAN KLIEN, & TV DISPLAY):**
   - **MANDAT CACHE (DARI CEO):** *"Setelah 4 Pilar Sinkronisasi Otomatis selesai dilakukan, wajib cek cache usang/sudah tidak digunakan lagi. Jika menemukan cache yang usang/sudah tidak terpakai lagi, segera hapus agar tampilan di TV dan di device lain yang sedang membuka web ini selalu mendapatkan data yang terbaru dan realtime."*
   - **TAHAPAN PEMERIKSAAN & PEMBERSIHAN CACHE:**
     a. **Pemeriksaan & Bumping Cache Service Worker (`web-statis/sw.js`)**:
        - Setiap terjadi perubahan berkas frontend/aset/markup, versi `CACHE_NAME` wajib dinaikkan (*cache version bumping*).
        - Pastikan logika event `activate` di `sw.js` menyisir seluruh cache browser (`caches.keys()`) dan menghapus cache versi lama (`caches.delete(key)`).
     b. **Pembersihan Cache di Panel Admin (`web-statis/admin.html`)**:
        - Pastikan skrip pembersihan cache lokal di panel Admin disinkronkan dengan versi Service Worker terbaru sehingga cache usang langsung dibersihkan.
     c. **Integritas Header Cloudflare (`web-statis/_headers`)**:
        - Pastikan rute `/*.html`, `/slides/*`, dan `/sw.js` tetap memiliki `Cache-Control: public, max-age=0, must-revalidate` agar TV display dan browser klien langsung mendeteksi rilis baru seketika.

3. **SELALU PERBARUI `LATEST_UPDATE.md`:**
   - Setiap kali selesai melakukan penambahan fitur, perubahan, atau perbaikan kode apapun, Anda **WAJIB memperbarui file `LATEST_UPDATE.md`**.
   - Cantumkan apa yang baru saja diubah, berkas apa saja yang terkait, rute baru (jika ada), serta kolom database baru (jika ada).
   - Tujuannya adalah agar ketika pengguna membuka sesi percakapan baru di laptop/PC atau akun yang berbeda, Agent berikutnya bisa langsung memahami seluruh riwayat dan struktur proyek tanpa kehilangan konteks.

4. **PRESERVASI NILAI DEFAULT & FALLBACK AMAN:**
   - Di Controller, Blade view, maupun JavaScript web statis, gunakan selalu fallback (*null coalescing* `?? true`, `?? 50`, `?? ''`) agar sistem tidak crash jika data belum tersedia atau terjadi keterlambatan jaringan, memastikan layar TV tidak pernah menampilkan data kosong atau angka nol saat proses sinkronisasi.
