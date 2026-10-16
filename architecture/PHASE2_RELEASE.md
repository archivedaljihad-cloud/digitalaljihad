# Tahap 2 — v5.8.97

Tanggal: 17 Oktober 2026 WIB. Basis: a2bd1cf (lockfile) setelah f7773cd.

## Backup dan pemulihan kode

Sebelum perubahan, arsip basis a2bd1cf disimpan di
`/workspace/backups/digitalaljihad-stage2-base-a2bd1cf.tar.gz`.
Seluruh 710 berkas dibandingkan hash-nya dengan sumber.
SHA-256: `743d91ce9a78646012e4b7e50b6ee1f4f407a52a55570a4439759b0e2ce7ebf5`.
Jika rollback kode dilakukan setelah pengurus menyimpan snapshot Jumat versi 2,
ekspor snapshot key sholat_jumat tersebut terlebih dahulu. Kode v5.8.96 belum
membaca snapshot baru; jangan menghapusnya atau menganggap tabel lama otomatis
berisi perubahan terakhir. Pulihkan/migrasikan snapshot melalui jalur berwenang
sebelum kembali memakai penulis legacy.

Arsip tambahan sebelum fast-forward juga tersedia: `digitalaljihad-before-stage2-20261016-170853.tar.gz`.
Ini backup kode, bukan backup data Supabase. Ekstrak ke direktori terpisah saat pemulihan.

## ACK render

Delapan slide: jumat, kas-jumat, kajian, slide, live-madinah, yasin, undangan, hikmah.
`SupabaseDB.bindSlideRenderer` menangani startup, refresh, sumber pesan parent,
respons kalah urutan, gambar terlihat yang belum dimuat, dan dua animation frame.
Pembacaan edit memakai snapshot cloud ketat, bukan fallback offline.
Startup mengirim `phase: ready`; tidak pernah mengubah status edit menjadi sukses.
Edit menyertakan `event_id`, `table`, `slide`, `timestamp` dan `scope: dom`.
Parent memvalidasi origin, source, iframe yang masih terlihat, URL, nama slide,
dan event yang ditunggu. Dashboard Admin menampilkan status per modul/per TV
hanya untuk event yang dikirim browser Admin tersebut.

Tidak ada klaim bahwa stream YouTube sudah memainkan video; ACK live Madinah
hanya mengonfirmasi URL embed dan DOM. Parser YouTube mendapat deklarasi videoId
lokal yang sebelumnya hilang. Rotasi poster biasa tetap memakai transisi lama;
refresh data memakai render langsung agar ACK tidak mendahului transisi.

Renderer agenda-rutin/pengumuman kini menerapkan enabled pada kartu dan mengembalikan
true setelah label, highlight, dan visibilitas diperbarui. Slide lain di luar delapan
tersebut dan slide utama tidak otomatis dinyatakan sudah memiliki kontrak penuh.

## Jumat: satu snapshot lengkap, semua penulis frontend statis memakai helper yang sama

Sumber baru: `app_settings` key `sholat_jumat`, JSON `schema_version: 2`:
tanggal, khatib, imam, muadzin, muadzin2, bilal, maklumat, waktu, durasi, foto_imam,
updated_at. Kolom value menyimpan JSON sehingga foto base64 tidak dimasukkan ke
kolom foto_imam varchar tabel lama. `getSholatJumat` membaca snapshot ini lebih dulu;
tabel `sholat_jumat` lama tetap fallback saat snapshot versi 2 belum ada.

`saveSholatJumat` menyimpan snapshot, durasi, dan pengumuman_jumat yang sudah ada
melalui SATU batch. Field keuangan pengumuman dipertahankan. Admin Petugas Jumat,
Petugas/Mode Input yang sudah memakai helper ini, serta editor Pengumuman Jumat
menggunakan jalur baru. Editor Pengumuman Jumat menyimpan snapshot petugas dan
pengumuman bersama; bukan menulis langsung ke row id=9.

Form utama memiliki debounce 700 ms, antrean simpan, draft terpisah,
pemulihan draft setelah hidrasi cloud, nama Khatib/Imam terpisah, Muadzin 2,
waktu Jumat, serta foto. Badge nama satu baris dengan scaler, ellipsis dan title.
Tidak ada lagi penulisan cache terkonfirmasi hanya karena durasi dipreview.

Tabel lama tidak ditimpa/mirror secara terpisah: itu akan merusak jaminan atomik.
Jika masih ada aplikasi Laravel atau integrasi eksternal yang menulis tabel lama,
migrasikan penulis itu sebelum dipakai bersama snapshot versi 2. Tidak ada DDL
atau perubahan RLS produksi pada pekerjaan ini. Kebijakan RLS staging kelak perlu
mempertimbangkan izin key Jumat untuk peran petugas saat migrasi Auth dilakukan.

## Kas

Kontrak form tambah/edit transaksi menjangkau helper simpan cloud dan empat nilai
DOM kas-jumat. Input nonfinite/invalid dan respons nol baris ditolak. Cache/TV tidak
mengklaim sukses sebelum cloud membalas. Draft transaksi gagal tetap disimpan.

`keuangan` dibaca ulang saat event perubahan dan dihitung untuk Kas Utama Masjid,
tanpa mencampur kategori Ambulance/program donasi. Periode laporan: Jumat terakhir
sampai hari ini; saldo awal adalah net transaksi sebelum periode, saldo akhir
saldo awal + pemasukan - pengeluaran. Transaksi masa depan tidak dihitung.
Jika ledger belum berisi transaksi, snapshot pengumuman Jumat yang terkonfirmasi
menjadi fallback. Angka nol sah dipertahankan; kegagalan jaringan tidak membuat nol
pengganti. Sebelum data pertama tersedia, label menunggu berupa elipsis, bukan
angka historis buatan. Rupiah menggunakan Intl.NumberFormat id-ID.

## Batch dan batas transaksi

`saveSettingsBatch(object)` mengirim SATU POST ke `app_settings?on_conflict=key`
dengan `resolution=merge-duplicates,return=representation`. Memerlukan UNIQUE(key)
yang sudah dinyatakan migrasi skema app_settings. Tidak membuat constraint live;
jika tidak tersedia, request gagal dan UI tetap unconfirmed. Semua baris diperiksa
pada respons. Batch lokal diserialkan dan payload disalin sebelum masuk antrean.
Kajian lima key dan Jumat memakai batch ini. Tidak ada fallback loop per-key.

Kegagalan statement PostgreSQL membatalkan seluruh key. Jika koneksi putus setelah
commit tetapi sebelum respons sampai, status adalah BELUM TERKONFIRMASI; seluruh
batch mungkin sudah tersimpan. Jangan mengklaim rollback cloud untuk kasus ini.
Draft dipertahankan, cache confirmed tidak dipromosikan, dan payload upsert yang
sama aman dicoba kembali. Tidak ada CAS/revision lintas perangkat: edit bersamaan
masih last-writer-wins. saveSettings campuran kolom row1 + key-value tetap dua
operasi; helper batch menjamin atomik untuk key-value saja, bukan lintas tabel.

## Pengujian dan batas audit

`npm test`: regresi engine, Auth, ACK, loading, PostgreSQL RLS, penyimpanan,
binding, reference store dan tambahan Tahap 2. PostgreSQL sungguhan (PGlite)
membuktikan rollback keseluruhan upsert saat satu key melanggar constraint.
`npm run test:browser:stage2`: delapan slide, delayed reads, offline, lalu form
Jumat dengan HTTP403 dan retry satu batch. Semua request eksternal diintersep;
tidak ada data produksi yang diubah oleh tes.
`npm run test:browser:agenda` tetap menjadi regresi pilihan kosong/tanpa reload.

Audit menghapus hanya 23 temuan UNCLASSIFIED_FORM yang kontraknya kini terpasang.
190 utang legacy lain tetap dilaporkan, tidak dihapus atau diterima ulang sebagai
baseline baru. Service worker tetap menunggu startup + 15 detik; precache tetap
maksimal tiga unduhan. Versi cache/admin menjadi 5.8.97. Tidak ada route atau kolom
baru. Folder backup Windows pengguna tidak tersedia di environment cloud ini.

Hasil quality gate rilis: 588 pemeriksaan npm test lulus; sembilan skenario browser
Tahap 2 (delapan slide + form Jumat) dan regresi browser Agenda Rutin lulus.
