# PANDUAN PRAKTIS: MENGHUBUNGKAN CCTV LIVE MIMBAR (KABEL BNC) KE TV LUAR MASJID

Buku panduan langkah demi langkah untuk menyiarkan video langsung kamera mimbar/khatib (dari kamera analog kabel BNC yang terhubung ke DVR masjid) ke TV-TV di serambi dan halaman luar masjid saat **Sholat Jum'at, Idul Fitri, dan Idul Adha**.

---

## 📋 GAMBARAN ALUR SISTEM (ARSITEKTUR)

```
[ Kamera Mimbar (BNC) ] 
         │ (Kabel BNC Coaxial yang sudah ada)
         ▼
    [ DVR Masjid ] ──(Kabel LAN)──▶ [ Router Wi-Fi Masjid ]
                                            │
                                            ├──▶ [ Laptop / PC Server Masjid ] (Menjalankan Laravel)
                                            │
                                            └──▶ [ Smart TV / TV Box di Luar ] (Membuka /tv-outdoor)
```

---

## 🛠️ LANGKAH 1: Sambungkan DVR ke Jaringan Masjid
1. Ambil kabel LAN (kabel internet RJ45).
2. Tancapkan satu ujung ke port **LAN / Network** di bagian belakang mesin DVR CCTV.
3. Tancapkan ujung satunya lagi ke port LAN pada **Router Wi-Fi Masjid** (atau Switch Hub masjid).
4. Pastikan lampu indikator port LAN di belakang DVR menyala/berkedip hijau/oranye.

---

## 🔍 LANGKAH 2: Cari Tahu Alamat IP DVR Masjid
1. Di layar monitor DVR masjid, buka menu: **Main Menu > Configuration > Network** (atau **Jaringan**).
2. Lihat bagian **IP Address** DVR (Contoh: `192.168.1.100` atau `192.168.1.50`).
3. Catat juga **Username** (biasanya `admin`) dan **Password** DVR Anda.
4. Perhatikan kamera mimbar terpasang di channel nomor berapa pada DVR (misal: **Channel 1** atau **Camera 01**).

---

## 📺 LANGKAH 3: Format Alamat Stream RTSP Sesuai Merk DVR

Hampir semua DVR modern otomatis memancarkan video channel kameranya lewat protokol RTSP lokal:

| Merk DVR CCTV | Format URL RTSP Lokal | Keterangan |
|:---|:---|:---|
| **Hikvision / Hilook** | `rtsp://admin:Password123@192.168.1.100:554/Streaming/Channels/101` | Channel 1 resolusi utama (HD) |
| **Dahua / IMOU** | `rtsp://admin:Password123@192.168.1.100:554/cam/realmonitor?channel=1&subtype=0` | Channel 1 resolusi utama |
| **XMeye / SPC / OEM** | `rtsp://admin:Password123@192.168.1.100:554/user=admin_password=Password123_channel=1_stream=0.sdp` | Channel 1 |
| **Avtech** | `rtsp://admin:Password123@192.168.1.100:554/live/ch0` | Channel 1 |

> *Ganti `admin`, `Password123`, dan `192.168.1.100` sesuai dengan username, password, dan IP DVR masjid Anda.*

---

## ⚡ LANGKAH 4: Menjalankan RTSP-to-Web Converter di Laptop/PC Server (Sangat Disarankan - 0 Delay)

Karena browser Chrome/Edge di TV tidak bisa memutar RTSP secara mentah tanpa converter, gunakan software gratis dan sangat ringan bernama **go2rtc** (hanya 1 file `.exe`, ukuran ~15 MB, tanpa perlu install):

1. **Download go2rtc**:
   - Buka link: `https://github.com/AlexxIT/go2rtc/releases`
   - Unduh file `go2rtc_win64.zip`, lalu ekstrak foldernya (misal di `C:\go2rtc\`).
2. **Buat file `go2rtc.yaml` di folder yang sama**:
   ```yaml
   streams:
     mimbar: rtsp://admin:Password123@192.168.1.100:554/Streaming/Channels/101
   ```
3. **Klik ganda `go2rtc.exe` untuk menjalankannya**.
4. Sekarang, siaran kamera mimbar sudah berubah menjadi web stream berkecepatan tinggi:
   ```
   http://localhost:1984/stream.html?src=mimbar
   ```
   *(Jika diakses dari TV luar yang satu Wi-Fi, ganti `localhost` dengan IP Laptop Server, contoh: `http://192.168.1.10:1984/stream.html?src=mimbar`)*.

---

## ⚙️ LANGKAH 5: Masukkan Pengaturan di Dashboard Admin Masjid

1. Login ke dashboard admin masjid (`http://localhost:8000/login`).
2. Buka menu **Pengaturan Aplikasi** di sidebar kiri.
3. Klik tab **CCTV Mimbar & TV Luar**.
4. Lakukan pengaturan:
   - **Aktifkan Fitur CCTV Mimbar**: Geser saklar ke posisi **AKTIF** (Hijau).
   - **Otomatis Beralih saat Khutbah**: Geser saklar ke posisi **AKTIF** (Hijau).
   - **URL Stream Kamera Mimbar**: Tempelkan alamat stream yang sudah disiapkan di Langkah 4 (misal: `http://192.168.1.10:1984/stream.html?src=mimbar` atau link YouTube live jika menggunakan YouTube).
5. Klik tombol **Simpan Semua Perubahan** di bagian bawah.

---

## 📺 LANGKAH 6: Cara Menyalakan TV di Luar Masjid

1. Nyalakan Smart TV atau Android TV Box yang terpasang di serambi/halaman luar masjid.
2. Pastikan TV/TV Box tersebut tersambung ke **Wi-Fi / Kabel LAN masjid yang sama** dengan laptop server.
3. Buka aplikasi **Browser** (Chrome, Firefox, atau browser bawaan TV).
4. Masukkan alamat khusus TV Luar:
   ```
   http://192.168.1.10:8000/tv-outdoor
   ```
   *(Ganti `192.168.1.10` dengan alamat IP lokal laptop/PC server masjid Anda).*
5. Tekan tombol **Layar Penuh (Fullscreen / F11)** pada browser TV.

### Perilaku Layar TV Luar:
- **Di hari biasa & sebelum sholat**: TV luar akan memutar rotasi display jadwal sholat, laporan keuangan, pengumuman, dan slide infaq secara bergantian.
- **Begitu masuk waktu Khutbah Jum'at / Sholat Hari Raya**: Layar TV luar akan **secara otomatis langsung berganti menampilkan wajah khatib di mimbar** lengkap dengan nama khatib, jam digital, dan teks hadits larangan berbicara saat khutbah!
- **Setelah sholat selesai**: Layar TV luar otomatis kembali memutar informasi masjid seperti biasa.

---

## 🔧 TIPS & TROUBLESHOOTING

1. **Bagaimana cara menguji tampilan TV Luar tanpa menunggu hari Jum'at?**
   - Anda cukup membuka alamat URL ini di browser untuk simulasi tes siaran mimbar:
     ```
     http://localhost:8000/tv-outdoor?mimbar=1
     ```
2. **Video di TV luar tidak muncul / hitam?**
   - Pastikan laptop server dan TV luar berada di dalam satu router Wi-Fi yang sama (satu segmen IP, misalnya sama-sama berawalan `192.168.1.xxx`).
   - Pastikan aplikasi `go2rtc.exe` di laptop server dalam keadaan berjalan (*running*).
   - Pastikan Windows Firewall di laptop server mengizinkan akses port 8000 dan 1984 untuk jaringan privat (*Private Network*).
