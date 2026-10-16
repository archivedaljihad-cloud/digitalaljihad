-- Jalankan blok A, B, C secara TERPISAH. Semua write-probe diakhiri ROLLBACK.
-- SQL Editor pemilik database bypass RLS. SET LOCAL ROLE wajib untuk menguji policy.

-- A. Pemeriksaan metadata dari SQL Editor pemilik proyek.
SELECT c.relname,c.relrowsecurity FROM pg_class c JOIN pg_namespace n ON n.oid=c.relnamespace
WHERE n.nspname='public' AND c.relname IN('app_settings','jadwal_sholat','sholat_jumat','keuangan','users','sessions','signage_remote_commands');
SELECT tablename,policyname,roles,cmd,qual,with_check FROM pg_policies WHERE schemaname='public' ORDER BY tablename,policyname;
-- Diharapkan 0 baris: policy tulis anonim pada tabel yang dikeraskan.
SELECT tablename,policyname FROM pg_policies WHERE schemaname='public'
AND tablename IN('app_settings','jadwal_sholat','sholat_jumat','sholat_idul_fitri','sholat_idul_adha','pengumuman','keuangan','keuangan_ambulance','program_infaq','donasi_infaq','qris','slides','slide_informasis','agenda_kajian','undangan_eksternal','roles','role_widgets','users','cache','cache_locks','failed_jobs','job_batches','jobs','migrations','password_reset_tokens','sessions','signage_remote_commands')
AND cmd IN('ALL','INSERT','UPDATE','DELETE') AND ('anon'=ANY(roles) OR 'public'=ANY(roles));
-- Tabel publik di luar cakupan paket: tinjau sendiri, hasil bukan otomatis aman.
SELECT tablename FROM pg_tables WHERE schemaname='public' AND tablename NOT IN('app_settings','jadwal_sholat','sholat_jumat','sholat_idul_fitri','sholat_idul_adha','pengumuman','keuangan','keuangan_ambulance','program_infaq','donasi_infaq','qris','slides','slide_informasis','agenda_kajian','undangan_eksternal','roles','role_widgets','users','cache','cache_locks','failed_jobs','job_batches','jobs','migrations','password_reset_tokens','sessions','signage_remote_commands');

-- B. Simulasi TV anonim. Tampilan boleh dibaca; tabel mentah/private dan write ditolak.
BEGIN;
SET LOCAL ROLE anon;
SELECT set_config('request.jwt.claims','{"role":"anon"}',true);
SELECT id,key FROM public.signage_display_settings;
SELECT count(*) AS readable_prayers FROM public.jadwal_sholat;
DO $$ DECLARE n integer; BEGIN
 IF has_table_privilege(current_user,'public.app_settings','SELECT') THEN RAISE EXCEPTION 'FAIL: anon dapat membaca raw settings'; END IF;
 IF has_column_privilege(current_user,'public.app_settings','value','UPDATE') THEN RAISE EXCEPTION 'FAIL: sisa column grant anonim'; END IF;
 BEGIN
  UPDATE public.jadwal_sholat SET waktu=waktu;
  GET DIAGNOSTICS n=ROW_COUNT;
  IF n>0 THEN RAISE EXCEPTION 'FAIL: anon dapat mengubah jadwal'; END IF;
 EXCEPTION WHEN insufficient_privilege THEN NULL;
 END;
 RAISE NOTICE 'PASS: akses anonim sesuai pemeriksaan';
END $$;
ROLLBACK;

-- C. Ulangi untuk akun admin, bendahara, petugas (ganti UUID & role pada JSON).
-- Jangan menjalankan dengan UUID nol. Ini simulasi policy, BUKAN pengujian password/JWT signature.
BEGIN;
SELECT set_config('request.jwt.claims','{"sub":"00000000-0000-0000-0000-000000000000","role":"authenticated","app_metadata":{"signage_role":"petugas"}}',true);
SET LOCAL ROLE authenticated;
DO $$ DECLARE expected text; actual text; n integer; BEGIN
 expected:=auth.jwt()->'app_metadata'->>'signage_role'; actual:=public.signage_effective_role();
 IF actual IS NULL OR actual IS DISTINCT FROM expected THEN RAISE EXCEPTION 'Isi UUID akun terverifikasi dan peran server yang benar'; END IF;
 UPDATE public.jadwal_sholat SET waktu=waktu;
 GET DIAGNOSTICS n=ROW_COUNT;
 IF expected IN('admin','petugas') AND n=0 THEN RAISE EXCEPTION 'FAIL/SKIP: jadwal kosong atau akses jadwal tidak tersedia'; END IF;
 IF expected='bendahara' AND n<>0 THEN RAISE EXCEPTION 'FAIL: bendahara mengubah jadwal'; END IF;
 UPDATE public.keuangan SET id=id;
 GET DIAGNOSTICS n=ROW_COUNT;
 IF expected IN('admin','bendahara') AND n=0 THEN RAISE EXCEPTION 'FAIL/SKIP: keuangan kosong atau akses keuangan tidak tersedia'; END IF;
 IF expected='petugas' AND n<>0 THEN RAISE EXCEPTION 'FAIL: petugas mengubah keuangan'; END IF;
 IF expected='petugas' THEN
  PERFORM public.signage_commit_settings('{}'::jsonb,'{"prayer_countdown_style":"flip"}'::jsonb,NULL);
 END IF;
 RAISE NOTICE 'PASS: uji peran %, seluruh probe akan di-rollback',expected;
END $$;
ROLLBACK;
-- Jika ada error sehingga transaksi aborted, jalankan ROLLBACK; sebelum query berikutnya.
