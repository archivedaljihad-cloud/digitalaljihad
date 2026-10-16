-- STAGED: stop-on-error; run after 001 and successful Auth/restore tests.
BEGIN;
-- Semua false secara sengaja. Jangan diubah sebelum syarat panduan benar-benar terpenuhi.
SET LOCAL signage.database_backup_verified='false';
SET LOCAL signage.verified_auth_ready='false';
SET LOCAL signage.web_cutover_ready='false';
DO $$ BEGIN
 IF current_setting('signage.web_cutover_ready',true) IS DISTINCT FROM 'true' THEN
  RAISE EXCEPTION 'STOP: deploy dan uji web Supabase Auth/atomic RPC dahulu; web legacy akan gagal menyimpan';
 END IF;
 IF NOT EXISTS(SELECT 1 FROM signage_private.dashboard_rls_backup WHERE name='dashboard-20261017') THEN RAISE EXCEPTION 'Policy/grant rollback snapshot required'; END IF;
END $$;
DO $$ BEGIN
 IF current_setting('signage.database_backup_verified',true) IS DISTINCT FROM 'true' OR current_setting('signage.verified_auth_ready',true) IS DISTINCT FROM 'true' THEN RAISE EXCEPTION 'Backup restore and Auth staging verification required'; END IF;
 IF NOT EXISTS(SELECT 1 FROM auth.users WHERE raw_app_meta_data->>'signage_role'='admin') THEN RAISE EXCEPTION 'Verified Supabase Auth admin required'; END IF;
 IF to_regprocedure('public.signage_commit_settings(jsonb,jsonb,jsonb)') IS NULL THEN RAISE EXCEPTION 'Install atomic RPC first'; END IF;
END $$;
CREATE SCHEMA IF NOT EXISTS signage_private;
REVOKE ALL ON SCHEMA signage_private FROM PUBLIC,anon,authenticated;
CREATE TABLE IF NOT EXISTS signage_private.legacy_account_backup(key text PRIMARY KEY,value text,captured_at timestamptz NOT NULL DEFAULT now());
REVOKE ALL ON ALL TABLES IN SCHEMA signage_private FROM PUBLIC,anon,authenticated;
INSERT INTO signage_private.legacy_account_backup(key,value) SELECT key,value FROM public.app_settings WHERE key IN('rbac_users_list','quick_petugas_state_v2') ON CONFLICT(key) DO NOTHING;
DELETE FROM public.app_settings WHERE key IN('rbac_users_list','quick_petugas_state_v2');

-- Fixed public projection. New/secret columns and unknown settings keys are private by default.
CREATE OR REPLACE VIEW public.signage_display_settings WITH (security_barrier=true) AS
SELECT s.id,s.key,
 (SELECT coalesce(jsonb_object_agg(item.key,item.value),'{}'::jsonb) FROM jsonb_each(to_jsonb(s)) item
  WHERE item.key=ANY(ARRAY['id','key','value','nama_aplikasi','sub_header','footer','rotation_interval','rotation_enabled','rotation_pages','prayer_mode_enabled','prayer_mode_before_adzan','prayer_mode_adzan_duration','prayer_mode_iqamah_duration','prayer_mode_after_prayer','prayer_mode_duration','prayer_mode_jumat_duration','audio_tarhim','tarhim_trigger_seconds','running_text','running_text_pages','live_makkah_url','live_madinah_url','cctv_mimbar_url','cctv_mimbar_enabled','cctv_auto_switch_khutbah','yasin_mode_enabled','yasin_start_time','yasin_scroll_speed','auto_switch_views','enable_dynamic_theme','enable_next_prayer_bar','daily_hikmah_cache','daily_hikmah_date','updated_at'])
  AND (item.key<>'value' OR s.id<>1 OR (item.value#>>'{}') IN('0','1','true','false'))
 ) AS data
FROM public.app_settings s
WHERE s.id=1 OR s.key=ANY(ARRAY['sholat_jumat','pengumuman_jumat','kegiatan_rutin_settings','kajian_sabtu_data','kajian_sabtu_enabled','kajian_sabtu_start_time','kajian_rotasi_pekan_detik','kajian_lock_mode','prayer_mode_jumat_duration','prayer_countdown_style','undangan_eksternal','undangan_eksternal_list','slides_custom_data','yasin_settings','yasin_display_mode','yasin_step_duration','yasin_mushaf_duration','ramadhan_infaq','cached_qurban_data']);
REVOKE ALL ON public.signage_display_settings FROM PUBLIC;
GRANT SELECT ON public.signage_display_settings TO anon,authenticated;

DO $$ DECLARE tbl text; policy record; write_roles text[]; predicate text; seq text; BEGIN
 FOREACH tbl IN ARRAY ARRAY['app_settings','jadwal_sholat','sholat_jumat','sholat_idul_fitri','sholat_idul_adha','pengumuman','keuangan','keuangan_ambulance','program_infaq','donasi_infaq','qris','slides','slide_informasis','agenda_kajian','undangan_eksternal','roles','role_widgets','users','cache','cache_locks','failed_jobs','job_batches','jobs','migrations','password_reset_tokens','sessions'] LOOP
  IF to_regclass(format('public.%I',tbl)) IS NULL THEN CONTINUE; END IF;
  EXECUTE format('ALTER TABLE public.%I ENABLE ROW LEVEL SECURITY',tbl);
  FOR policy IN SELECT policyname FROM pg_policies WHERE schemaname='public' AND tablename=tbl LOOP EXECUTE format('DROP POLICY %I ON public.%I',policy.policyname,tbl); END LOOP;
  EXECUTE format('REVOKE ALL ON TABLE public.%I FROM PUBLIC,anon,authenticated',tbl);
  -- Revoke old column grants too; table-level REVOKE alone does not remove them.
  FOR policy IN SELECT column_name FROM information_schema.columns WHERE table_schema='public' AND table_name=tbl LOOP EXECUTE format('REVOKE ALL (%I) ON public.%I FROM PUBLIC,anon,authenticated',policy.column_name,tbl); END LOOP;
  IF tbl=ANY(ARRAY['users','cache','cache_locks','failed_jobs','job_batches','jobs','migrations','password_reset_tokens','sessions']) THEN CONTINUE; END IF;
  IF tbl='app_settings' OR tbl=ANY(ARRAY['roles','role_widgets']) THEN
   EXECUTE format('GRANT SELECT ON public.%I TO authenticated',tbl);
   EXECUTE format('CREATE POLICY signage_read ON public.%I FOR SELECT TO authenticated USING ((select auth.uid()) IS NOT NULL AND (select public.signage_effective_role())=''admin'')',tbl);
  ELSE
   EXECUTE format('GRANT SELECT ON public.%I TO anon,authenticated',tbl);
   EXECUTE format('CREATE POLICY signage_read ON public.%I FOR SELECT TO anon,authenticated USING (true)',tbl);
  END IF;
  IF tbl=ANY(ARRAY['roles','role_widgets']) THEN CONTINUE; END IF;
  write_roles:=ARRAY['admin'];
  IF tbl=ANY(ARRAY['keuangan','keuangan_ambulance','program_infaq','donasi_infaq','qris']) THEN write_roles:=ARRAY['admin','bendahara'];
  ELSIF tbl<>'app_settings' THEN write_roles:=ARRAY['admin','petugas']; END IF;
  predicate:=format('(select auth.uid()) IS NOT NULL AND (select public.signage_effective_role())=ANY(%L::text[])',write_roles);
  EXECUTE format('GRANT INSERT,UPDATE,DELETE ON public.%I TO authenticated',tbl);
  EXECUTE format('CREATE POLICY signage_insert ON public.%I FOR INSERT TO authenticated WITH CHECK (%s)',tbl,predicate);
  EXECUTE format('CREATE POLICY signage_update ON public.%I FOR UPDATE TO authenticated USING (%s) WITH CHECK (%s)',tbl,predicate,predicate);
  EXECUTE format('CREATE POLICY signage_delete ON public.%I FOR DELETE TO authenticated USING (%s)',tbl,predicate);
  seq:=pg_get_serial_sequence(format('public.%I',tbl),'id');
  IF seq IS NOT NULL THEN EXECUTE format('REVOKE ALL ON SEQUENCE %s FROM PUBLIC,anon,authenticated',seq); EXECUTE format('GRANT USAGE,SELECT ON SEQUENCE %s TO authenticated',seq); END IF;
 END LOOP;
END $$;

-- Public TVs can read recent commands; only signed admin sessions can create them.
CREATE TABLE IF NOT EXISTS public.signage_remote_commands (
 id bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
 command text NOT NULL CHECK(command=ANY(ARRAY['NEXT','PREV','PAUSE','RESUME','TOGGLE_PAUSE','JUMP','RELOAD','REFRESH_DISPLAY','EMERGENCY_ALERT','CLEAR_ALERT','PING'])),
 data jsonb NOT NULL DEFAULT '{}'::jsonb,
 sender_id uuid NOT NULL DEFAULT auth.uid(),
 created_at timestamptz NOT NULL DEFAULT now(),
 expires_at timestamptz NOT NULL DEFAULT(now()+interval '60 seconds')
);
ALTER TABLE public.signage_remote_commands ENABLE ROW LEVEL SECURITY;
REVOKE ALL ON public.signage_remote_commands FROM PUBLIC,anon,authenticated;
GRANT SELECT ON public.signage_remote_commands TO anon,authenticated;
GRANT INSERT(command,data) ON public.signage_remote_commands TO authenticated;
GRANT USAGE ON SEQUENCE public.signage_remote_commands_id_seq TO authenticated;
DROP POLICY IF EXISTS signage_command_read ON public.signage_remote_commands;
CREATE POLICY signage_command_read ON public.signage_remote_commands FOR SELECT TO anon,authenticated USING(expires_at>now() AND created_at>now()-interval '60 seconds');
DROP POLICY IF EXISTS signage_command_write ON public.signage_remote_commands;
CREATE POLICY signage_command_write ON public.signage_remote_commands FOR INSERT TO authenticated WITH CHECK(auth.uid() IS NOT NULL AND public.signage_effective_role()='admin' AND sender_id=auth.uid() AND expires_at<=created_at+interval '60 seconds');
NOTIFY pgrst, 'reload schema';
COMMIT;
