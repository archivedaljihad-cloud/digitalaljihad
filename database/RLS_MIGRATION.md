# Migrasi izin database — belum diterapkan ke cloud

Frontend saat ini menulis dengan anon key. Login AdminAuth di localStorage bukan
sesi Supabase Auth. Menjalankan RLS baru tanpa mengganti jalur autentikasi akan
memutus penyimpanan pengurus. Karena itu skrip baru sengaja memiliki pengaman
aktivasi dan transaksi atomik. Ini tidak mengubah kebijakan database produksi.

## Backup wajib sebelum aktivasi

Backup proyek lokal: `/workspace/backups/digitalaljihad-before-fix-20261016.tar.gz`.
SHA-256: `a816612827325817ce92bd72c8d2d6df24a4c5b90ccdff35a7d0a019eeaaa40a`.
Arsip mencakup 887 berkas (termasuk .git); gzip serta seluruh hash berkas diverifikasi.
Backup ini BUKAN backup data Supabase atau konfigurasi Cloudflare.

Sebelum DDL cloud, ekspor database (schema, policy, grants/ACL, sequences, data)
melalui alat backup Supabase/Postgres yang berwenang. Simpan secara privat dan
uji restore ke database terpisah. Catat kebijakan dan hak akses lama, jangan
menggunakan SQL lama yang membuka akses anonim sebagai skrip rollback.

## Prasyarat staging

1. Migrasikan login dan semua mutasi REST/SDK ke sesi Supabase Auth (access token
   pengguna), termasuk admin, petugas, modeinput, remote command dan auto-update
   jadwal. Jangan taruh service-role key di browser.
2. Tetapkan `app_metadata.signage_role` melalui backend/Admin API tepercaya:
   `admin`, `bendahara`, atau `petugas`. Jangan gunakan user_metadata, input form,
   role dari localStorage, atau sekadar role authenticated untuk otorisasi.
3. Pindahkan kredensial dari app_settings.rbac_users_list. Pastikan row settings
   publik juga tidak berisi salinan password/token dalam kolom lain atau JSON.
4. Cocokkan tabel/kolom aktual. Matriks awal: admin mengelola semua tabel operasional;
   bendahara tabel keuangan/infaq/QRIS; petugas jadwal/konten. app_settings dan tabel
   peran admin-only. Pengguna legacy tidak dapat dimutasi melalui browser.
5. Verifikasi izin sequence untuk INSERT sesuai skema aktual; skrip tidak memberi
   grant sequence global. Berikan hanya sequence yang memang diperlukan.
6. Uji SELECT TV tanpa sesi, penolakan INSERT/UPDATE/DELETE anonim, penolakan silang
   peran, simpan admin/petugas/bendahara, login HP/PC, event realtime, reconnect dan
   refresh token. Periksa Supabase Realtime Broadcast/Presence secara terpisah:
   RLS tabel public tidak otomatis mengamankan channel broadcast publik.
7. Skrip mengganti seluruh policy pada tabel yang tercantum. Tinjau policy khusus
   yang ingin dipertahankan, lalu integrasikan dengan sengaja; policy permissive
   lama tidak boleh tertinggal karena digabung menggunakan OR.

Setelah semua pemeriksaan lulus, pada sesi database yang sama set kedua pengaman:

```sql
SET signage.verified_auth_ready = 'true';
SET signage.database_backup_verified = 'true';
```

Kemudian jalankan SELURUH `supabase_rls_hardened.sql` dengan stop-on-error.
Skrip menolak aktivasi jika belum ada admin Supabase Auth dengan metadata server.
Pada kegagalan sebelum COMMIT, jalankan ROLLBACK. Tidak ada data aplikasi yang
INSERT/UPDATE/DELETE oleh skrip ini.

## Pemulihan

Sebelum COMMIT: ROLLBACK memulihkan DDL/grants/policy dalam transaksi tersebut.
Setelah COMMIT: pulihkan policy/grants dari snapshot terverifikasi, dengan frontend
rilis yang sesuai; lakukan di staging dahulu. Restore data hanya bila dibutuhkan,
agar transaksi pengguna yang masuk setelah backup tidak hilang.

Untuk pemulihan kode lokal, ekstrak arsip ke DIREKTORI BARU terlebih dahulu,
periksa hasilnya, lalu pulihkan hanya berkas yang diperlukan. Jangan menimpa
proyek aktif atau .git secara membabi buta.
