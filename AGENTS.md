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
     a. **Supabase Cloud**: Pastikan tabel / `app_settings` / skema cloud tersinkron dengan payload terkini.
     b. **GitHub**: Lakukan `git add .`, `git commit -m "..."`, dan `git push origin main`.
     c. **Cloudflare Workers**: Jalankan `npx wrangler deploy` untuk langsung memperbarui aset di **`https://digitalaljihad.my.id/`**.
     d. **Folder Lokal**: Salin berkas pembaruan ke folder cadangan lokal `C:\Users\anthu\Documents\【Digital WebSTATIS】\`.

2. **SELALU PERBARUI `LATEST_UPDATE.md`:**
   - Setiap kali selesai melakukan penambahan fitur, perubahan, atau perbaikan kode apapun, Anda **WAJIB memperbarui file `LATEST_UPDATE.md`**.
   - Cantumkan apa yang baru saja diubah, berkas apa saja yang terkait, rute baru (jika ada), serta kolom database baru (jika ada).
   - Tujuannya adalah agar ketika pengguna membuka sesi percakapan baru di laptop/PC atau akun yang berbeda, Agent berikutnya bisa langsung memahami seluruh riwayat dan struktur proyek tanpa kehilangan konteks.

3. **PRESERVASI NILAI DEFAULT & FALLBACK AMAN:**
   - Di Controller, Blade view, maupun JavaScript web statis, gunakan selalu fallback (*null coalescing* `?? true`, `?? 50`, `?? ''`) agar sistem tidak crash jika data belum tersedia atau terjadi keterlambatan jaringan, memastikan layar TV tidak pernah menampilkan data kosong atau angka nol saat proses sinkronisasi.
