-- Pemulihan POLICY/GRANT, BUKAN restore seluruh data. Ini bisa mengembalikan policy lama yang terbuka.
-- Jalankan hanya untuk insiden cutover, dari SQL Editor pemilik proyek.
BEGIN;
DO $$ DECLARE saved text; BEGIN
 SELECT restore_sql INTO saved FROM signage_private.dashboard_rls_backup WHERE name='dashboard-20261017';
 IF saved IS NULL THEN RAISE EXCEPTION 'Snapshot tidak ditemukan; jangan disable RLS massal'; END IF;
 EXECUTE saved;
 -- Kembalikan kredensial legacy secara privat untuk rollback login lama, tanpa menimpa data baru.
 IF to_regclass('signage_private.legacy_account_backup') IS NOT NULL THEN
  INSERT INTO public.app_settings(key,value) SELECT key,value FROM signage_private.legacy_account_backup ON CONFLICT(key) DO NOTHING;
 END IF;
END $$;
NOTIFY pgrst, 'reload schema';
COMMIT;
-- Sesuaikan juga frontend kembali ke rilis sebelumnya (securityMode legacy, atomicRpc false).
-- RPC, trigger Jumat, view publik, dan tabel perintah tetap ada. Tidak menghapus data pasca-cutover.
