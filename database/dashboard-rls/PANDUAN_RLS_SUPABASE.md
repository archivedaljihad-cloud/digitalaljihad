# Paket SQL Dashboard Supabase — Al-Jihad Digital Signage

Tanggal: 17 Oktober 2026, WIB. Disiapkan dari repositori DIGITALv304, baseline komit `42798dd` (v5.8.97), dengan persiapan Auth/RPC lokal yang **belum dirilis**.

## Status dan batas penggunaan

**Jangan menjalankan aktivasi RLS produksi sekarang.** Konfigurasi lokal masih `securityMode: 'legacy'` dan `atomicRpc: false`. Kode Auth dan transaksi yang disiapkan belum selesai diuji menyeluruh dan belum dipublikasikan. Mengubah dua flag itu saja belum cukup untuk menyatakan cutover siap.

Paket ini menyiapkan SQL, urutan pengujian, dan rollback. **Tidak ada SQL di paket ini yang telah dijalankan pada Supabase produksi.** Pengujian lokal memakai PostgreSQL WASM/PGlite dengan skema fixture; bukan validasi skema produksi, layanan Supabase Auth, PostgREST, atau Realtime produksi.

Yang aman dilakukan sekarang: jalankan `00_PERIKSA_TANPA_MENGUBAH.sql` melalui SQL Editor dan pelajari hasilnya. Persiapan akun dan snapshot dilakukan setelah backup. Aktivasi menunggu semua gerbang kesiapan di bawah terpenuhi.

## Isi paket

| Berkas | Fungsi | Kapan dijalankan |
|---|---|---|
| `00_PERIKSA_TANPA_MENGUBAH.sql` | Inventaris tabel, kolom, policy, grant fungsi dan jumlah akun; hanya SELECT | Sekarang, staging lalu produksi |
| `01_SIMPAN_KEBIJAKAN_LAMA.sql` | Snapshot privat policy, RLS flags, grant tabel/kolom/sequence untuk rollback | Setelah backup database lengkap, sebelum perubahan kebijakan |
| `02_TETAPKAN_PERAN_AKUN.sql` | Tetapkan `app_metadata.signage_role` untuk satu akun Auth terverifikasi | Setelah akun dibuat dan identitas diverifikasi |
| `03_PASANG_RPC_DAN_INTEGRASI_JUMAT.sql` | RPC transaksi row+KV+jadwal; trigger sinkronisasi Jumat dua arah | Staging dulu, setelah backup berhasil dipulihkan |
| `04_AKTIFKAN_RLS_SETELAH_WEB_SIAP.sql` | RLS, view pengaturan publik, pengamanan data akun lama, tabel remote command | Hanya saat semua syarat cutover terpenuhi |
| `05_UJI_SETELAH_AKTIVASI.sql` | Audit policy dan simulasi TV/peran dengan transaksi rollback | Staging dulu, lalu pemeriksaan produksi |
| `06_ROLLBACK_KEBIJAKAN.sql` | Pulihkan kebijakan dan grant lama tanpa menghapus data baru | Jika cutover gagal; uji di staging terlebih dahulu |
| `07_UJI_ATOMIK_STAGING.sql` | Buktikan kegagalan jadwal membatalkan perubahan row dan KV | Hanya staging |
| `test.cjs` | Pengujian otomatis PostgreSQL lokal terhadap SQL paket | Untuk Antigravity IDE |
| `SHA256SUMS.txt` | Checksum berkas paket | Verifikasi integritas unduhan |

## 1. Backup wajib sebelum perubahan

Backup kode terbaru sudah dibuat di environment kerja:

- Arsip: `digitalaljihad-before-dashboard-rls-20261016-181554.tar.gz`
- SHA-256: `eb122251ee50840201e60a2cb41c2c23d946c52806d31dad96f6ec3568290cbd`
- 718 berkas diverifikasi terhadap isi arsip. Ini **backup kode**, bukan backup Supabase. Tidak mencakup database, akun Auth, atau file Storage.

Untuk database:

1. Buka proyek Supabase yang benar. Catat project reference tanpa menyalin secret.
2. Periksa menu Database/Backups; nama dan kemampuan menu bergantung paket Supabase. Simpan backup yang dapat dipulihkan. Jika unduhan/restore terpisah tidak tersedia, gunakan proses backup PostgreSQL yang didukung Supabase bersama pengelola proyek.
3. Pastikan backup meliputi skema dan data aplikasi, policy/grant/sequence, serta data Auth yang diperlukan. Cadangkan file Storage terpisah jika dipakai; backup database tidak menyimpan isi berkas Storage.
4. Pulihkan backup ke **proyek staging terpisah**, bukan menimpa produksi. Ikuti prosedur restore Supabase untuk objek yang dikelola platform. Verifikasi jumlah baris, pengaturan, data Jumat, keuangan, akun, serta akses Storage.
5. Catat tanggal backup, lokasi penyimpanan privat, dan hasil uji restore. Ekspor CSV beberapa tabel saja tidak menggantikan backup ini.
6. Jalankan `01` pada staging dan, kelak, produksi. Simpan hasil `restore_sql` secara privat di luar Supabase sebagai salinan metadata rollback. Jangan menimpa snapshot pertama.

**Berhenti jika backup tidak bisa dipulihkan.** Script `01` hanya snapshot konfigurasi akses, bukan pengganti backup data penuh. Jangan menganggap salinan di database yang sama sebagai backup bencana.

## 2. Cara menggunakan SQL Editor

1. Buka Supabase Dashboard → pilih proyek → **SQL Editor** → **New query**.
2. Untuk pemeriksaan/migrasi gunakan peran pemilik proyek yang biasa dipakai SQL Editor, umumnya `postgres`. Akun pengurus web berbeda dari akun SQL Editor ini.
3. Salin **satu berkas** yang sesuai tahap, baca komentar, lalu Run. Jangan gabungkan semua berkas sekaligus.
4. Jika ada error di dalam `BEGIN`, jalankan `ROLLBACK;` sebelum melanjutkan. Simpan pesan error tanpa password/token/isi akun.
5. Jangan menjalankan `06` sebagai bagian urutan normal; berkas itu khusus pemulihan.
6. Semua flag kesiapan di `03` dan `04` sengaja bernilai `'false'`. Ubah menjadi `'true'` pada salinan SQL Editor hanya ketika kondisi tersebut benar-benar sudah dibuktikan. Flag adalah checklist manual, bukan detektor otomatis dan bukan bukti restore.

## 3. Pemeriksaan awal — jalankan `00`

Periksa hasil query satu per satu. Tidak menampilkan nilai pengaturan atau password; daftar key, struktur dan ekspresi policy tetap sebaiknya dibagikan secara privat.

Syarat yang harus diperiksa sebelum `03`/`04`:

- `app_settings` memiliki `id`, `key`, `value`; `key` mempunyai unique constraint yang mendukung `ON CONFLICT(key)`; `value` kompatibel teks JSON. Baris utama `id=1` tepat satu.
- `app_settings` memiliki kolom yang digunakan payload, khususnya `rotation_interval` dan `prayer_mode_jumat_duration`. RPC menolak kolom di luar daftar yang diizinkan. RPC memakai tipe kolom aktual saat konversi payload.
- `sholat_jumat` memiliki `id`, `tanggal`, `khatib`, `imam`, `muadzin`, `bilal`, `foto_imam`; cek kolom wajib tambahan, panjang kolom, default, FK, dan trigger lama. Jangan menerapkan asumsi fixture tanpa meninjau skema aktual.
- `jadwal_sholat` memiliki `nama_sholat` dan `waktu`. Nama Subuh, Dzuhur, Ashar, Maghrib, Isya, Imsak, Terbit/Syuruk harus cocok tepat satu baris untuk setiap jadwal yang diedit. RPC membatalkan transaksi jika cocok nol atau lebih dari satu.
- Periksa policy `FOR ALL`/write yang terbuka. `USING(true)` **pada SELECT data publik** dapat memang diperlukan TV; yang ditutup adalah write anonim dan akses data privat.
- Tinjau fungsi `SECURITY DEFINER` yang masih bisa dipanggil `anon`, role membership tambahan, serta tabel di luar daftar cakupan. Paket ini tidak otomatis mengamankan seluruh RPC, Storage, atau broadcast Realtime.
- Jangan membagikan `rbac_users_list`, PIN, hash/password, service-role key, JWT, atau connection string. `00` tidak membaca isi pengaturan tersebut.

Jika struktur berbeda, jangan menghapus kolom atau memaksakan skrip; sesuaikan dan uji ulang di staging.

## 4. Persiapan akun — setelah backup

1. Buka **Authentication → Users**. Buat akun pengurus yang benar dengan email yang dikuasainya dan password baru yang kuat. Verifikasi identitas/email sesuai prosedur pengurus. Jangan menggunakan ulang password default publik dari aplikasi lama.
2. Sediakan akun admin dan akun uji bendahara/petugas; akun uji hanya untuk staging. Sebaiknya ada admin cadangan terverifikasi untuk pemulihan.
3. Salin UUID akun dari Dashboard. Dalam `02`, ganti UUID nol, ganti `target_role` dengan persis `admin`, `bendahara`, atau `petugas`, dan ubah `identity_verified` menjadi `true` setelah identitas diverifikasi.
4. Jalankan satu akun setiap kali. Skrip menolak akun tanpa email terkonfirmasi. Skrip tidak membuat akun, tidak mengirim email, dan tidak menyalin kredensial lama.
5. Peran disimpan pada **`raw_app_meta_data.signage_role`**, bukan `user_metadata` yang dapat diedit pengguna. Perubahan role harus lewat administrasi server/Dashboard tepercaya.
6. Logout/login ulang untuk JWT baru. Pengguna tidak boleh diberi role hanya berdasarkan UUID atau email yang dikirim pihak tak terverifikasi.

## 5. Gerbang wajib kesiapan web

Antigravity/pengelola harus menyelesaikan dan menguji ini sebelum menjalankan `04` pada produksi:

- Login menggunakan Supabase Auth dengan email/password, restore sesi, refresh token, logout/idle logout, dan pemulihan akses admin. Default login lama serta Quick PIN tidak menjadi jalur otorisasi alternatif.
- Semua write PostgREST memakai JWT pengguna yang valid, bukan hanya publishable/anon key. `service_role` tidak pernah berada pada HTML, JavaScript publik, localStorage atau log.
- TV membaca pengaturan melalui `signage_display_settings`, bukan membaca tabel mentah `app_settings`. Setelah RLS, raw settings hanya dapat dibaca admin; bendahara/petugas memakai proyeksi publik untuk pengaturan display.
- Penyimpanan campuran memakai RPC `signage_commit_settings` (satu transaksi), termasuk form durasi+jurnal jadwal dan pengaturan Jumat. Semua jalur tulis langsung/legacy ditinjau.
- Pengelolaan akun tidak membaca/menulis password ke `app_settings`; endpoint server untuk administrasi akun sudah diuji jika UI pengelolaan akun dipakai. SQL ini tidak men-deploy Edge Function.
- Remote command memakai tabel terlindungi `signage_remote_commands` sebagai sumber perintah; broadcast publik hanya menjadi petunjuk untuk membaca ulang data terverifikasi. **Tabel baru saja tidak menutup penyalahgunaan channel broadcast lama.** Konsumen/pengirim frontend harus disesuaikan dan diuji dahulu.
- Tampilan TV, ACK render, prayer countdown, agenda picker, foto petugas, offline cache, serta pembacaan data terbaru setelah refresh/reconnect sudah diuji. Pertahankan startup SW 15 detik dan precache maksimal 3 unduhan.
- Bump versi cache/frontend saat cutover; pastikan admin/TV yang masih terbuka memperbarui aset. Klien lama tidak otomatis menjadi kompatibel setelah RLS.

Semua ini masih merupakan gerbang rilis, bukan klaim sudah selesai. SQL Editor saja tidak menggantikan perubahan frontend tersebut.

## 6. Urutan staging

1. Restore backup produksi ke staging dan pastikan konfigurasi web staging menunjuk **proyek staging**, bukan produksi. Hindari invitation/remote command produksi dari staging.
2. Jalankan `00`; review skema dan akses.
3. Jalankan `01`; ekspor hasil `restore_sql` dan simpan privat.
4. Buat akun staging, jalankan `02` untuk setiap role.
5. Dalam `03`, ubah `signage.database_backup_verified` menjadi `'true'` setelah uji restore; jalankan seluruh berkas dalam satu eksekusi.
6. Koordinasikan pengujian frontend Auth/RPC baru pada staging. Gunakan jendela cutover uji untuk menguji keadaan sesudah RLS juga; legacy grant sebelum `04` belum membuktikan keamanan.
7. Setelah gerbang web dan backup terpenuhi, ubah tiga flag di `04` menjadi `'true'`, jalankan utuh. Skrip mengamankan tabel dalam satu transaksi; kegagalan menyebabkan pembatalan perubahan transaksi itu.
8. Jalankan `05`: blok A, B, C terpisah. Pada C ganti UUID dan role JSON lalu ulangi untuk admin, bendahara, petugas. Semua probe diakhiri `ROLLBACK`; bila gagal jalankan `ROLLBACK;` manual.
9. Jalankan `07` hanya staging dengan UUID admin staging.
10. Jalankan matriks pengujian aplikasi di bawah menggunakan sesi login nyata.
11. Uji `06`, periksa kembalinya kebijakan/grant lama dan akses frontend lama. Sesudah berhasil, aktivasi staging dapat diuji ulang dengan snapshot awal yang sama; jangan menjalankan `01` ulang menimpa snapshot.
12. Simpan hasil per tahap. Jangan lanjut produksi jika ada gagal atau hasil ambigu.

### Hasil SQL yang diharapkan

- `05 A`: RLS aktif pada tabel dalam cakupan; query policy write anonim menghasilkan **0 baris**. Tabel di luar cakupan tetap harus ditinjau terpisah.
- `05 B`: view publik dan jadwal bisa dibaca; raw settings dan write anonim tidak diizinkan. NOTICE `PASS` muncul.
- `05 C`: UUID harus akun Auth nyata dengan role server yang sama dengan JSON. Admin bisa probe jadwal/keuangan; bendahara hanya keuangan; petugas hanya jadwal dan RPC pengaturan yang diizinkan. Tabel kosong membuat uji melaporkan FAIL/SKIP, bukan lulus palsu; siapkan fixture staging, jangan mengisi data produksi demi tes.
- `07`: NOTICE `PASS` menunjukkan kegagalan prayer membatalkan perubahan row+KV. Tidak ada perubahan probe yang tersimpan.
- SQL Editor normal milik pemilik tabel sering bypass RLS. Sukses SELECT/UPDATE dari sesi pemilik **bukan** bukti policy aman. Simulasi memakai `SET LOCAL ROLE`; UUID/JWT claims tiruan ini hanya alat uji dalam SQL Editor tepercaya, bukan login web.

## 7. Matriks pengujian aplikasi dengan login nyata

| Aktor/kondisi | Uji | Hasil wajib |
|---|---|---|
| TV tanpa login | Buka halaman utama dan slide Jumat/keuangan | Data publik tampil; tidak perlu akun admin |
| Pengunjung anonim | REST insert/update/delete ke tabel operasional | Ditolak; tidak mengubah data |
| Anon | Baca raw `app_settings`, users, sessions | Ditolak; view publik tidak berisi akun/PIN/API secret |
| Admin | Login dari HP dan laptop; edit pengaturan/Jumat | Simpan terkonfirmasi cloud, TV kemudian ACK sesuai render |
| Bendahara | Edit keuangan; coba jadwal/petugas | Keuangan diizinkan; perubahan di luar peran ditolak server |
| Petugas | Edit jadwal/Jumat; coba keuangan/peran | Konten diizinkan sesuai kontrak; keuangan/peran ditolak |
| Akun tanpa role | Login dan mencoba write | Tidak mendapat hak pengurus |
| Role diubah/dicabut | Gunakan sesi lama lalu login ulang | Write memakai role lama ditolak; akun baru mengikuti role server |
| Manipulasi browser | Ubah localStorage, `user_metadata`, flag Quick PIN | Tidak memberi izin tulis server |
| Jaringan terputus | Putus saat satu RPC menyimpan beberapa key | UI tidak mengklaim sukses; tidak ada partial commit dalam satu RPC |
| Timeout sesudah commit | Simulasikan respons hilang sesudah request | UI menyatakan belum terkonfirmasi, baca ulang cloud sebelum retry |
| Reconnect/cache | Buka dua perangkat, edit lalu reconnect | Data terbaru muncul; nilai lama tidak menimpa perubahan baru |
| Remote control | Kirim broadcast palsu tanpa record resmi | Tidak memicu kontrol TV |
| Regresi | Countdown, agenda picker, offline, semua slide | Tidak rusak; ACK hanya setelah render nyata |

Catatan transaksi: satu pemanggilan RPC bersifat atomik di database. Jika respons jaringan hilang setelah database commit, browser tidak dapat menyimpulkan bahwa data batal tersimpan; baca ulang cloud. Beberapa request RPC terpisah tetap bukan satu transaksi lintas request. Dua editor yang menulis snapshot sama masih membutuhkan pengendalian konflik; paket ini belum menerapkan optimistic version checking.

## 8. Aktivasi produksi — hanya setelah staging lulus

1. Jadwalkan jendela perubahan singkat dan siapkan frontend rilis sebelumnya untuk rollback. Pastikan ada akses Dashboard pemilik proyek.
2. Ambil backup produksi terbaru dan verifikasi prosedur pemulihannya. Ulangi `00` untuk mendeteksi perubahan skema sejak staging.
3. Jalankan `01` di produksi; jangan memakai snapshot staging. Simpan salinan privat metadata rollback.
4. Siapkan akun produksi terverifikasi dengan `02`; jangan menyalin UUID staging.
5. Pasang `03` setelah flag backup benar. Cek Jumat dan jadwal tidak berubah secara tak diinginkan.
6. Koordinasikan rilis frontend Auth/RPC yang sudah diuji dan `04` dalam jendela perubahan. Ketiga flag `04` hanya diubah setelah kondisi benar. Pengaman ini sengaja mencegah menjalankan skrip pada web lama.
7. Jalankan audit `05 A/B`; uji login nyata dan TV. Probe C pada produksi opsional dalam jendela perubahan dan selalu rollback; trigger/notifikasi di luar DB tetap perlu dipertimbangkan.
8. Verifikasi PostgREST (skrip mengirim schema reload), Realtime, cache lama, dua perangkat dan sesi admin cadangan.
9. Jika ada regresi yang tidak dapat diatasi segera, gunakan prosedur rollback berikut. Jangan menonaktifkan RLS massal sebagai percobaan.

## 9. Rollback jika gagal

1. Catat error dan hentikan penyimpanan admin sementara supaya perubahan bersamaan tidak membingungkan diagnosis.
2. Buka SQL Editor pemilik proyek; jalankan `ROLLBACK;` bila transaksi sebelumnya masih aborted.
3. Jalankan `06_ROLLBACK_KEBIJAKAN.sql`. Ia memakai snapshot privat `01` untuk mengembalikan policy, status RLS/force-RLS, grant tabel/kolom/sequence browser pada objek yang ada saat snapshot.
4. Ia mengembalikan key akun legacy dari salinan privat `04` hanya jika key tersebut belum ada. Tidak menimpa key yang sudah ada.
5. Pulihkan frontend sebelumnya (`securityMode: legacy`, `atomicRpc: false`) beserta versi cache yang sesuai. Verifikasi TV dan login lama.
6. Periksa perubahan data yang dibuat setelah cutover; jangan menimpa seluruh database tanpa menilai kehilangan data baru.

**Batas rollback:** ini rollback kebijakan, bukan restore data penuh. Akun Auth/peran yang dibuat dengan `02` tidak otomatis dihapus/dikembalikan. Fungsi RPC, trigger Jumat, schema privat, view publik dan tabel remote command yang baru tetap ada; tabel itu mempertahankan kebijakan amannya. Kolom/skema yang berubah setelah snapshot dapat membuat rollback gagal dan harus ditangani pengelola. Kebijakan lama yang terbuka ikut dipulihkan; rollback adalah pemulihan layanan sementara, bukan hasil keamanan akhir. Bila `01` belum dibuat, jangan menjalankan SQL penonaktifan massal; gunakan backup dan prosedur pemulihan yang sudah diuji.

## 10. Penjelasan teknis untuk Antigravity IDE

- `signage_effective_role()` adalah helper SECURITY DEFINER dengan search_path tetap. Memeriksa UUID, konfirmasi email, ban akun, role server saat ini dan kecocokan role JWT. `user_metadata` tidak memberi hak. Tidak diberikan kepada anon.
- RLS memisahkan SELECT publik dari write authenticated. Data pengaturan mentah hanya untuk admin; proyeksi view mengizinkan daftar kolom/key display yang eksplisit, tidak semua key baru.
- `rbac_users_list`/`quick_petugas_state_v2` dipindahkan ke schema privat untuk pemulihan, lalu dihapus dari tabel publik saat `04`. Secret lain seperti API key tidak diproyeksikan ke view, tetapi perlu audit pemindahan ke server sebagai pekerjaan terpisah.
- RPC menggunakan daftar kolom/key yang diizinkan per role, advisory transaction lock, dan satu transaksi row+KV+jadwal. Menolak kosong/invalid/duplikat nama prayer atau target tak tepat satu. Row utama harus tersedia.
- Trigger Jumat menjaga kompatibilitas JSON schema 2 dan tabel legacy. Foto inline panjang dipertahankan pada snapshot, sedangkan legacy menyimpan penanda `custom_uploaded`. Uji nama panjang, foto, bilal/maklumat, tanggal dan integrasi Laravel aktual sebelum rilis.
- Snapshot rollback menyimpan metadata akses secara privat dan menolak overwrite. Tidak mengubah data operasional saat dibuat.
- Remote command hanya admin terverifikasi; TV boleh membaca command yang masih berlaku. Konsumen frontend, deduplikasi, TTL, reconnect dan pembersihan historis tetap harus diterapkan/ditinjau. Skrip ini tidak menjamin keamanan channel broadcast lama atau menyediakan identitas kriptografis perangkat TV.
- Cakupan tabel ditulis eksplisit di `04`; tabel tambahan, fungsi lama, grants lewat role lain, Storage policies, publication Realtime dan endpoint Worker/Laravel harus diaudit terpisah. Jangan menyatakan seluruh proyek aman hanya karena SQL berhasil.

## 11. Bukti pengujian paket

Pengujian lokal: `node work/rls-dashboard/test.cjs` dari root repositori (setelah `npm ci`). Untuk paket unduhan, salin seluruh direktori paket ke `work/rls-dashboard/`, atau jalankan dari lokasi di bawah repositori agar Node menemukan dependensi `@electric-sql/pglite`.

**32 pemeriksaan PostgreSQL lokal lulus**, meliputi:

- preflight dapat dijalankan tanpa membaca isi kredensial;
- snapshot dan larangan menimpa snapshot;
- pengaman backup dan pengaman kesiapan web;
- SQL RPC, trigger, view, policy serta pencabutan column grant;
- anon tidak mendapat data privat maupun hak write;
- pemisahan hak admin/bendahara/petugas dan penolakan role palsu lewat user_metadata;
- pencabutan role server menolak JWT role lama;
- satu transaksi gagal tidak menyisakan perubahan row/KV;
- bridge Jumat dua arah mempertahankan foto inline;
- SQL probe `05` untuk tiga role dan `07` dapat dieksekusi;
- rollback mengembalikan policy lama, akses lama dan akun legacy yang dipindahkan.

Bukti ini memakai skema fixture, bukan produksi. Pengujian nyata pada staging dan verifikasi backup/restore tetap wajib. Pipeline regresi repositori dijalankan terpisah; tidak menggantikan uji Auth/Realtime di Supabase.

## Langkah Anda sekarang

Buka SQL Editor dan jalankan **hanya `00_PERIKSA_TANPA_MENGUBAH.sql`**. Simpan hasil struktur/policy untuk ditinjau bersama Antigravity. Jangan menjalankan `04` sebelum seluruh gerbang web dan backup terpenuhi. Anda tidak perlu memberikan password database atau service-role key kepada saya untuk menjalankan paket melalui Dashboard sendiri.
