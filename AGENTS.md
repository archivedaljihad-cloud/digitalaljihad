# ATURAN WAJIB PENGEMBANGAN AI AGENT

Dokumen ini adalah aturan tetap (*standing rules*) untuk setiap AI Agent atau programmer yang berpasangan dengan pengguna di repositori ini:

1. **SELALU PERBARUI `LATEST_UPDATE.md`:**
   - Setiap kali selesai melakukan penambahan fitur, perubahan, atau perbaikan kode apapun, Anda **WAJIB memperbarui file `LATEST_UPDATE.md`**.
   - Cantumkan apa yang baru saja diubah, berkas apa saja yang terkait, rute baru (jika ada), serta kolom database baru (jika ada).
   - Tujuannya adalah agar ketika pengguna membuka sesi percakapan baru di laptop/PC atau akun yang berbeda, Agent berikutnya bisa langsung memahami seluruh riwayat dan struktur proyek tanpa kehilangan konteks.

2. **SELALU OTOMATIS SINKRONKAN KE GIT, LOKAL, & DOMAIN LIVE (TANPA MENUNGGU PERINTAH):**
   - Setiap kali selesai melakukan pembaruan, penambahan fitur, atau perbaikan kode apapun, Anda **WAJIB LANGSUNG SECARA OTOMATIS**:
     a. Sinkronkan berkas ke folder mandiri lokal: `C:\Users\anthu\Documents\【Digital WebSTATIS】\`.
     b. Lakukan `git add .`, `git commit -m "..."`, dan `git push origin main`.
     c. Karena GitHub `main` terhubung otomatis dengan *deployment* Cloudflare Pages, pembaruan akan langsung tayang (*live*) di **`https://digitalaljihad.my.id/`**.
   - **JANGAN MENUNGGU PERINTAH DARI PENGGUNA** untuk melakukan tahapan sinkronisasi ini.

3. **PRESERVASI NILAI DEFAULT & FALLBACK AMAN:**
   - Di Controller, Blade view, maupun JavaScript web statis, gunakan selalu fallback (*null coalescing* `?? true`, `?? 50`, `?? ''`) agar sistem tidak crash jika data belum tersedia atau terjadi keterlambatan jaringan.
