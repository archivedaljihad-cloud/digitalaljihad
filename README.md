# 🕌 SISTEM INFORMASI DIGITAL MASJID JAMI' AL-JIHAD
### *Platform Smart Display TV & Dashboard Manajemen Masjid Berbasis Web Statis Modern*
> **Domain Live:** [https://digitalaljihad.my.id/](https://digitalaljihad.my.id/)  
> **Email Konfirmasi, Backup & Reset:** `archived.aljihad@gmail.com`  
> **Kontak Resmi Pengurus (WhatsApp):** [0877 5876 7000](https://wa.me/6287758767000)  

---

## 📖 SEJARAH & LATAR BELAKANG KELAHIRAN WEB STATIS

### 1. Titik Awal: Era Web Dinamis & Idealisme Digitalisasi Masjid
Pada mulanya, proyek sistem informasi digital masjid ini dibangun mengadopsi konsep **web dinamis konvensional** (berbasis *monolith* framework Laravel, runtime PHP, web server Apache/Nginx, dan basis data relasional MySQL lokal). Tujuannya mulia: menghadirkan digitalisasi modern untuk **Masjid Jami' Al-Jihad Graha Asri** agar jadwal sholat, transparansi kas keuangan, petugas Jum'at, pengumuman, dan materi kajian dapat tayang secara otomatis di layar monitor / TV display masjid.

### 2. Berbagai Kendala & Realitas Pahit di Lapangan
Namun, seiring pengoperasian langsung di lingkungan masjid, sistem web dinamis tradisional menghadapi segudang kendala teknis dan operasional yang sangat memberatkan:
1. **Ketergantungan Server Backend yang Ruwet & Mahal:**  
   Menjalankan web dinamis menuntut server backend (hosting cPanel, VPS, atau mini PC lokal) yang harus hidup 24 jam nonstop. Biaya sewa server rutin menjadi beban operasional, dan jika server down atau kehabisan memori, layar TV masjid seketika padam (*blank*) menampilkan pesan error 500.
2. **Kerentanan Basis Data (Database Crash & Corrupted):**  
   Di lingkungan masjid di mana pemadaman listrik PLN bisa terjadi sewaktu-waktu tanpa UPS cadangan, database MySQL lokal kerap mengalami kerusakan (*table crash/corrupt*). Akibatnya, pengurus masjid yang awam IT kesulitan memperbaikinya.
3. **Ketergantungan Total pada Internet (Tidak Tahan Offline):**  
   Layar display TV masjid wajib menyala prima sepanjang waktu. Pada sistem dinamis lama, begitu koneksi WiFi masjid mengalami gangguan, layar TV display terhenti (*stuck*), gagal me-render halaman, dan menampilkan tampilan rusak.
4. **Beban Maintenance & Atribut Bawaan yang Kuno:**  
   Arsitektur lama dipenuhi ketergantungan paket library yang rumit (*dependency hell*), konfigurasi deployment yang panjang, serta banyaknya atribut bawaan template lama yang kaku, ter-hardcode, dan tidak fleksibel untuk disesuaikan dengan kebutuhan riil jamaah.

### 3. Titik Balik & Revolusi Total 1000% Menuju WEB STATIS MODERN
Menyadari seluruh kendala di atas tidak boleh dibiarkan menghambat syiar dakwah dan transparansi masjid, maka diputuskan sebuah langkah berani: **Merombak dan merevolusi total sistem hingga 1000% dari format aslinya**, melahirkan **SISTEM WEB STATIS SERVERLESS MODERN**.

Seluruh beban backend server yang berat dan rapuh dipangkas habis. Sistem ditransformasikan menjadi perpaduan teknologi mutakhir berkelas dunia:
- **Cloudflare Edge Network:** Seluruh tampilan antarmuka disajikan dari ratusan data center Cloudflare di seluruh dunia. Halaman terbuka secepat kilat (*zero latency*), anti-down, kebal serangan siber, dan gratis selamanya tanpa biaya server bulanan.
- **Supabase BaaS (Backend-as-a-Service) Realtime:** Menggantikan database lokal yang rentan. Data kas keuangan, infaq donasi, jadwal kajian, hingga susunan slide TV tersimpan di cloud database enterprise dan disinkronkan secara *real-time* via WebSockets. Sekali pengurus menekan tombol simpan di ponsel, layar TV di masjid langsung berubah seketika tanpa perlu me-reload halaman.
- **Service Worker PWA Offline Resilience:** Dilengkapi mesin caching lokal canggih. Seluruh 18 slide TV, desain ornamen islami, dan audio adzan/tarhim tersimpan aman di memori browser TV. **Jika internet masjid padam total, layar TV display tetap tayang anggun, stabil, dan lancar tanpa gangguan sedikit pun.**
- **Desain Mewah & Mandiri Bebas Atribut Lama:** Menghapus seluruh nama pengembang lama, kontak lawas, maupun atribut kaku masa lalu. Sistem ini kini berdiri mandiri, elegan, dan siap digunakan untuk masjid mana pun dengan identitas yang bersih, profesional, dan berwibawa.

---

## 🎯 3 HAK AKSES RESMI PENGURUS (RBAC)

Sistem membagi wewenang secara aman dan terisolasi berdasarkan peran tanggung jawab:

| No | Peran (Role) | Kredensial Login Bawaan | Lingkup Hak Akses |
|---|---|---|---|
| **1** | **👑 Super Admin** | `archived.aljihad@gmail.com`<br>Pass: `admin123` | Akses Penuh 100%: Pengaturan Sistem, Kelola Akun Pengurus, Reorder/Rotasi Slide TV (▲/▼), Keuangan, dan Pengumuman. |
| **2** | **💼 Bendahara** | `bendahara@aljihad.com`<br>Pass: `bendahara123` | Buku Kas Utama, Kas Ambulance, Pencatatan Donasi Infaq Program, Laporan Arus Kas, dan Ekspor Laporan Excel. |
| **3** | **📺 Petugas / Operator** | `petugas@aljihad.com`<br>Pass: `operator123` | Jadwal Sholat, Petugas Khotib/Imam Jum'at, Pengumuman AI, Teks Berjalan (Running Text), dan Pemantau Slide TV. |

> **Catatan Pengamanan:** Nama sapaan masing-masing dashboard, alamat email, dan kata sandi di atas dapat diedit/disetting secara dinamis kapan saja oleh Super Admin melalui menu **Kelola Hak Akses > Edit Akun**.

---

## 🌟 FITUR-FITUR UNGGULAN SISTEM

1. **Layar Display TV Otomatis (18 Slide Terintegrasi):**
   - Slide Utama (Jam Digital, Waktu Sholat, Hitung Mundur Adzan & Iqomah).
   - Slide Laporan Kas Transparan (Kas Utama & Kas Operasional).
   - Slide Kas Khusus Mobil Ambulance Siaga.
   - Slide Penggalangan Dana & Infaq Terikat (Target Dana, Progress Donasi, & Nama Donatur).
   - Slide Semarak Ramadhan, Sholat Tarawih, Kultum, & Kas Tromol Infaq.
   - Slide Petugas Sholat Jum'at, Kajian Rutin, Agenda Pengajian, dan Hari Besar Islam.
2. **Input Nominal Rupiah Pintar:**  
   Pencatatan keuangan otomatis memformat tanda titik ribuan secara langsung saat mengetik (contoh: `1000000` langsung menjadi `1.000.000`), memudahkan bendahara dan mencegah salah ketik nol.
3. **Format Sapaan Dashboard Islami Dinamis:**  
   Menyapa pengurus mengikuti jam lokal secara santun (`Selamat Pagi,`, `Selamat Siang,`, `Selamat Sore,`, `Selamat Malam,`) disusul nama pengurus dan gelar kehormatan resmi masjid.
4. **Pusat Bantuan & Pemulihan Darurat:**  
   Seluruh keperluan konfirmasi, pemulihan akun, pencadangan (*backup*), maupun pengaturan ulang (*reset*) diarahkan ke saluran resmi:
   - 📧 **Email:** `archived.aljihad@gmail.com`
   - 📱 **WhatsApp:** `0877 5876 7000`

---

## 🏛 STRUKTUR REPOSITORI
- `web-statis/` : Sumber kode utama web statis (HTML5, Vanilla CSS Modern, JS ES6, Supabase BaaS, Cloudflare Configuration).
- `web-statis/slides/` : Kumpulan 18 halaman slide display TV resolusi tinggi (Full HD / 4K Ready).
- `web-statis/js/admin-auth.js` : Modul otentikasi mandiri, RBAC, dan sanitasi sesi pengguna.
- `web-statis/sw.js` : Service Worker PWA untuk ketahanan operasional offline 100%.
- `resources/` & `app/` : Basis arsip historis dan modul pelengkap terdahulu.
- `LATEST_UPDATE.md` : Dokumentasi kronologis seluruh bab evolusi sistem dari Bab 1 hingga pembaruan terkini.

---
*Didedikasikan untuk kemaslahatan ummat, syiar Islam yang berkemajuan, dan tata kelola masjid yang transparan, profesional, dan modern.*
