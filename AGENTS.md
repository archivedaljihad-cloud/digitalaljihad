# ATURAN WAJIB PENGEMBANGAN AI AGENT

Dokumen ini adalah aturan tetap (*standing rules*) untuk setiap AI Agent atau programmer yang berpasangan dengan pengguna di repositori ini:

1. **SELALU PERBARUI `LATEST_UPDATE.md`:**
   - Setiap kali selesai melakukan penambahan fitur, perubahan, atau perbaikan kode apapun, Anda **WAJIB memperbarui file `LATEST_UPDATE.md`**.
   - Cantumkan apa yang baru saja diubah, berkas apa saja yang terkait, rute baru (jika ada), serta kolom database baru (jika ada).
   - Tujuannya adalah agar ketika pengguna membuka sesi percakapan baru di laptop/PC atau akun yang berbeda, Agent berikutnya bisa langsung memahami seluruh riwayat dan struktur proyek tanpa kehilangan konteks.

2. **SELALU SINKRONKAN DENGAN GITHUB `main`:**
   - Setelah selesai melakukan pengeditan dan pengujian (`php -l`), lakukan `git add`, `git commit -m "..."`, dan `git push origin main`.
   - Pastikan repositori lokal dan remote GitHub selalu berada dalam kondisi sinkron 100%.

3. **PRESERVASI NILAI DEFAULT & FALLBACK AMAN:**
   - Di Controller maupun Blade view, gunakan selalu fallback (*null coalescing* `?? true`, `?? 50`, `?? ''`) agar sistem tidak crash jika kolom database baru belum dieksekusi migrasinya oleh pengguna di database lokalnya.
