-- Buat akun melalui Authentication > Users terlebih dahulu.
-- Email harus milik pengurus yang identitasnya sudah diverifikasi, gunakan password baru.
-- Jangan gunakan akun/default password yang pernah dipublikasikan kode lama.
-- Ubah UUID dan peran SATU AKUN setiap eksekusi. Membuat akun tidak dilakukan skrip ini.
BEGIN;
DO $$
DECLARE target_id uuid := '00000000-0000-0000-0000-000000000000';
 target_role text := 'GANTI_DENGAN_admin_ATAU_bendahara_ATAU_petugas';
 identity_verified boolean := false; -- ganti true HANYA setelah verifikasi identitas
 affected integer;
BEGIN
 IF NOT identity_verified OR target_id='00000000-0000-0000-0000-000000000000' OR target_role NOT IN('admin','bendahara','petugas') THEN RAISE EXCEPTION 'Isi UUID, peran dan verifikasi identitas terlebih dahulu'; END IF;
 UPDATE auth.users SET raw_app_meta_data=coalesce(raw_app_meta_data,'{}'::jsonb)||jsonb_build_object('signage_role',target_role), updated_at=now() WHERE id=target_id AND email_confirmed_at IS NOT NULL;
 GET DIAGNOSTICS affected=ROW_COUNT;
 IF affected<>1 THEN RAISE EXCEPTION 'Akun tidak ditemukan / email belum terkonfirmasi'; END IF;
END $$;
COMMIT;
-- Sesudahnya logout lalu login kembali agar JWT memakai peran baru.
