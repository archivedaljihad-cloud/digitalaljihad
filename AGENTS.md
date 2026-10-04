# ATURAN WAJIB PENGEMBANGAN AI AGENT

Dokumen ini adalah aturan tetap (*standing rules*) untuk setiap AI Agent atau programmer yang berpasangan dengan pengguna di repositori ini:

1. **SELALU PERBARUI `LATEST_UPDATE.md`:**
   - Setiap kali selesai melakukan penambahan fitur, perubahan, atau perbaikan kode apapun, Anda **WAJIB memperbarui file `LATEST_UPDATE.md`**.
   - Cantumkan apa yang baru saja diubah, berkas apa saja yang terkait, rute baru (jika ada), serta kolom database baru (jika ada).
   - Tujuannya adalah agar ketika pengguna membuka sesi percakapan baru di laptop/PC atau akun yang berbeda, Agent berikutnya bisa langsung memahami seluruh riwayat dan struktur proyek tanpa kehilangan konteks.

2. **SELALU OTOMATIS SINKRONKAN KE CLOUD PRODUCTION, GIT, & LOKAL BACKUP:**
   - **DATA CENTER UTAMA:** **Cloudflare Workers (`https://digitalaljihad.my.id/`)**, **GitHub (`main`)**, dan **Supabase Cloud Database** adalah Data Center & Lingkungan Produksi Utama (Active Production). Seluruh petugas masjid menginput dan memantau data secara live dari Cloud.
   - **FOLDER LOKAL SEBAGAI BACKUP:** Folder lokal `C:\Users\anthu\Documents\【Digital WebSTATIS】\` berfungsi sebagai salinan cadangan arsip (*cold backup*).
   - Setiap kali selesai melakukan penambahan fitur atau perbaikan kode, Anda **WAJIB LANGSUNG SECARA OTOMATIS**:
     a. Lakukan `git add .`, `git commit -m "..."`, dan `git push origin main` ke repositori GitHub.
     b. Jalankan `npx wrangler deploy` untuk langsung memperbarui aset Cloudflare Workers Live di **`https://digitalaljihad.my.id/`**.
     c. Salin berkas pembaruan ke folder cadangan lokal: `C:\Users\anthu\Documents\【Digital WebSTATIS】\`.
   - **JANGAN MENUNGGU PERINTAH DARI PENGGUNA** untuk melakukan tahapan sinkronisasi ini.

3. **PRESERVASI NILAI DEFAULT & FALLBACK AMAN:**
   - Di Controller, Blade view, maupun JavaScript web statis, gunakan selalu fallback (*null coalescing* `?? true`, `?? 50`, `?? ''`) agar sistem tidak crash jika data belum tersedia atau terjadi keterlambatan jaringan.
