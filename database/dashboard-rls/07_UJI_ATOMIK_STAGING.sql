-- HANYA staging, setelah RPC terpasang. Tidak menyimpan probe karena ROLLBACK.
-- Ganti UUID akun ADMIN staging. Sengaja pakai prayer invalid untuk membuktikan rollback.
BEGIN;
SELECT set_config('request.jwt.claims','{"sub":"00000000-0000-0000-0000-000000000000","role":"authenticated","app_metadata":{"signage_role":"admin"}}',true);
SET LOCAL ROLE authenticated;
DO $$ DECLARE before_state jsonb; after_state jsonb; rejected boolean:=false; BEGIN
 IF public.signage_effective_role() IS DISTINCT FROM 'admin' THEN RAISE EXCEPTION 'Isi UUID admin staging'; END IF;
 SELECT jsonb_agg(to_jsonb(s) ORDER BY id) INTO before_state FROM public.app_settings s;
 BEGIN
  PERFORM public.signage_commit_settings('{"rotation_interval":19}'::jsonb,'{"prayer_countdown_style":"flip"}'::jsonb,'[{"name":"INVALID_PRAYER","time":"04:00"}]'::jsonb);
 EXCEPTION WHEN OTHERS THEN
  IF SQLERRM NOT LIKE '%Invalid/duplicate prayer%' THEN RAISE; END IF;
  rejected:=true;
 END;
 IF NOT rejected THEN RAISE EXCEPTION 'FAIL: payload invalid diterima'; END IF;
 SELECT jsonb_agg(to_jsonb(s) ORDER BY id) INTO after_state FROM public.app_settings s;
 IF before_state IS DISTINCT FROM after_state THEN RAISE EXCEPTION 'FAIL: row/KV berubah setelah RPC gagal'; END IF;
 RAISE NOTICE 'PASS: kegagalan prayer me-rollback row dan KV dalam transaksi RPC';
END $$;
ROLLBACK;
