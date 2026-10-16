-- STAGED. Apply only after a private database backup has been restored and verified.
BEGIN;
-- Ganti false hanya setelah backup database lengkap berhasil dipulihkan di staging.
SET LOCAL signage.database_backup_verified='false';
DO $$ BEGIN
 IF current_setting('signage.database_backup_verified', true) IS DISTINCT FROM 'true' THEN
  RAISE EXCEPTION 'Verified database backup required';
 END IF;
END $$;

CREATE OR REPLACE FUNCTION public.signage_effective_role() RETURNS text
LANGUAGE sql STABLE SECURITY DEFINER SET search_path=public,pg_temp AS $role$
 SELECT u.raw_app_meta_data->>'signage_role' FROM auth.users u
 WHERE u.id=auth.uid() AND u.email_confirmed_at IS NOT NULL
 AND (u.banned_until IS NULL OR u.banned_until<=now())
 AND u.raw_app_meta_data->>'signage_role'=auth.jwt()->'app_metadata'->>'signage_role'
 AND u.raw_app_meta_data->>'signage_role' IN('admin','bendahara','petugas')
$role$;
REVOKE ALL ON FUNCTION public.signage_effective_role() FROM PUBLIC,anon;
GRANT EXECUTE ON FUNCTION public.signage_effective_role() TO authenticated;

CREATE OR REPLACE FUNCTION public.signage_commit_settings(p_row jsonb DEFAULT '{}'::jsonb, p_items jsonb DEFAULT '{}'::jsonb, p_prayers jsonb DEFAULT NULL)
RETURNS jsonb LANGUAGE plpgsql SECURITY DEFINER SET search_path = public, pg_temp AS $$
DECLARE
 role_name text := public.signage_effective_role();
 allowed_row text[] := ARRAY['nama_aplikasi','footer','live_makkah_url','live_madinah_url','cctv_mimbar_url','rotation_interval','prayer_mode_before_adzan','prayer_mode_adzan_duration','prayer_mode_iqamah_duration','prayer_mode_after_prayer','prayer_mode_jumat_duration','audio_tarhim','tarhim_trigger_seconds','yasin_mode_enabled','yasin_start_time','yasin_scroll_speed','auto_switch_views','rotation_pages','running_text_pages','enable_dynamic_theme','enable_next_prayer_bar','cctv_mimbar_enabled','cctv_auto_switch_khutbah','running_text','daily_hikmah_cache','daily_hikmah_date','value'];
 operator_row text[] := ARRAY['prayer_mode_before_adzan','prayer_mode_adzan_duration','prayer_mode_iqamah_duration','prayer_mode_after_prayer','prayer_mode_jumat_duration','audio_tarhim','tarhim_trigger_seconds','yasin_mode_enabled','yasin_start_time','yasin_scroll_speed','value'];
 content_keys text[] := ARRAY['sholat_jumat','pengumuman_jumat','kegiatan_rutin_settings','kajian_sabtu_data','kajian_sabtu_enabled','kajian_sabtu_start_time','kajian_rotasi_pekan_detik','kajian_lock_mode','prayer_mode_jumat_duration','prayer_countdown_style','undangan_eksternal','undangan_eksternal_list','slides_custom_data','yasin_settings','yasin_display_mode','yasin_step_duration','yasin_mushaf_duration'];
 finance_keys text[] := ARRAY['pengumuman_jumat','ramadhan_infaq','cached_qurban_data'];
 item record; assignments text[] := ARRAY[]::text[]; result_row jsonb := '{}'::jsonb; result_items jsonb := '{}'::jsonb;
 actual_row jsonb; val text; changed integer; prayer jsonb; names text[] := ARRAY[]::text[]; normalized text;
 previous jsonb; incoming jsonb;
BEGIN
 IF auth.uid() IS NULL OR role_name IS NULL OR role_name NOT IN ('admin','bendahara','petugas') THEN RAISE EXCEPTION 'Authenticated signage role required' USING ERRCODE='42501'; END IF;
 IF jsonb_typeof(p_row) IS DISTINCT FROM 'object' OR jsonb_typeof(p_items) IS DISTINCT FROM 'object' THEN RAISE EXCEPTION 'Object payload required'; END IF;
 IF p_row='{}'::jsonb AND p_items='{}'::jsonb AND p_prayers IS NULL THEN RAISE EXCEPTION 'Empty mutation'; END IF;
 -- Lock before all rows so calls to this RPC have a consistent lock order.
 PERFORM pg_advisory_xact_lock(hashtext('signage:settings-transaction'));
 FOR item IN SELECT key,value FROM jsonb_each(p_row) LOOP
  IF NOT item.key=ANY(allowed_row) OR role_name='bendahara' OR (role_name='petugas' AND NOT item.key=ANY(operator_row)) THEN RAISE EXCEPTION 'Column not permitted: %',item.key USING ERRCODE='42501'; END IF;
  assignments := array_append(assignments,format('%I = (jsonb_populate_record(NULL::public.app_settings, $1)).%I',item.key,item.key));
 END LOOP;
 FOR item IN SELECT key,value FROM jsonb_each(p_items) LOOP
  IF item.key IN ('rbac_users_list','quick_petugas_state_v2','gemini_api_key') OR item.key ~* '(password|secret|token)' THEN RAISE EXCEPTION 'Private/account setting cannot be changed here' USING ERRCODE='42501'; END IF;
  IF (role_name='petugas' AND NOT item.key=ANY(content_keys)) OR (role_name='bendahara' AND NOT item.key=ANY(finance_keys)) THEN RAISE EXCEPTION 'Setting not permitted: %',item.key USING ERRCODE='42501'; END IF;
  IF item.key='pengumuman_jumat' AND role_name<>'admin' THEN
   SELECT CASE WHEN jsonb_typeof(to_jsonb(value))='string' THEN (value::text)::jsonb ELSE to_jsonb(value) END INTO previous FROM public.app_settings WHERE key=item.key LIMIT 1;
   previous := coalesce(previous,'{}'::jsonb); incoming := item.value;
   IF role_name='bendahara' AND incoming->'petugas' IS DISTINCT FROM previous->'petugas' THEN RAISE EXCEPTION 'Finance role cannot change Friday officers' USING ERRCODE='42501'; END IF;
   IF role_name='petugas' AND (incoming - ARRAY['petugas','himbauan','himbauanText','tanggalJudul','titimangsa']) IS DISTINCT FROM (previous - ARRAY['petugas','himbauan','himbauanText','tanggalJudul','titimangsa']) THEN RAISE EXCEPTION 'Operator cannot change financial report' USING ERRCODE='42501'; END IF;
  END IF;
 END LOOP;
 IF cardinality(assignments)>0 THEN
  EXECUTE 'UPDATE public.app_settings SET '||array_to_string(assignments,', ')||' WHERE id=1 RETURNING to_jsonb(app_settings.*)' INTO actual_row USING p_row;
  IF actual_row IS NULL THEN RAISE EXCEPTION 'Main settings row missing'; END IF;
  FOR item IN SELECT key FROM jsonb_each(p_row) LOOP result_row:=result_row||jsonb_build_object(item.key,actual_row->item.key); END LOOP;
 END IF;
 FOR item IN SELECT key,value FROM jsonb_each(p_items) LOOP
  val:=CASE WHEN jsonb_typeof(item.value)='string' THEN item.value#>>'{}' ELSE item.value::text END;
  INSERT INTO public.app_settings(key,value) VALUES(item.key,val) ON CONFLICT(key) DO UPDATE SET value=excluded.value;
  result_items:=result_items||jsonb_build_object(item.key,item.value);
 END LOOP;
 IF p_prayers IS NOT NULL THEN
  IF role_name NOT IN ('admin','petugas') OR jsonb_typeof(p_prayers) IS DISTINCT FROM 'array' OR jsonb_array_length(p_prayers)>7 THEN RAISE EXCEPTION 'Prayer mutation not permitted' USING ERRCODE='42501'; END IF;
  FOR prayer IN SELECT value FROM jsonb_array_elements(p_prayers) LOOP
   normalized:=lower(prayer->>'name'); IF normalized='syuruk' THEN normalized:='terbit'; END IF;
   IF normalized IS NULL OR normalized NOT IN ('imsak','subuh','terbit','dzuhur','ashar','maghrib','isya') OR normalized=ANY(names) OR (prayer->>'time') !~ '^([01][0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$' OR prayer->>'time' IS NULL THEN RAISE EXCEPTION 'Invalid/duplicate prayer'; END IF;
   names:=array_append(names,normalized);
   UPDATE public.jadwal_sholat SET waktu=(prayer->>'time')::time WHERE lower(nama_sholat) LIKE '%'||normalized||'%' OR (normalized='terbit' AND lower(nama_sholat) LIKE '%syuruk%');
   GET DIAGNOSTICS changed=ROW_COUNT; IF changed<>1 THEN RAISE EXCEPTION 'Prayer % must match exactly one row',normalized; END IF;
  END LOOP;
 END IF;
 RETURN jsonb_build_object('success',true,'row',result_row,'items',result_items,'prayers',p_prayers);
END $$;
REVOKE ALL ON FUNCTION public.signage_commit_settings(jsonb,jsonb,jsonb) FROM PUBLIC,anon;
GRANT EXECUTE ON FUNCTION public.signage_commit_settings(jsonb,jsonb,jsonb) TO authenticated;

-- Bidirectional compatibility. Neither branch performs a second HTTP request.
CREATE OR REPLACE FUNCTION public.signage_friday_bridge() RETURNS trigger
LANGUAGE plpgsql SECURITY DEFINER SET search_path=public,pg_temp AS $$
DECLARE snap jsonb; old_snap jsonb; stamp date; photo text; row_data jsonb; affected integer;
BEGIN
 IF pg_trigger_depth()>1 THEN IF TG_OP='DELETE' THEN RETURN OLD; ELSE RETURN NEW; END IF; END IF;
 IF TG_TABLE_NAME='app_settings' THEN
  IF NEW.key IS DISTINCT FROM 'sholat_jumat' THEN RETURN NEW; END IF;
  snap:=NEW.value::jsonb;
  IF snap->>'schema_version' IS DISTINCT FROM '2' THEN RAISE EXCEPTION 'Friday snapshot schema_version 2 required'; END IF;
  stamp:=(snap->>'tanggal')::date;
  IF stamp IS NULL OR coalesce(snap->>'khatib',snap->>'imam','')='' THEN RAISE EXCEPTION 'Friday date/name required'; END IF;
  photo:=coalesce(snap->>'foto_imam','image/display/default_imam.jpg');
  IF length(photo)>200 OR photo LIKE 'data:%' THEN photo:='custom_uploaded'; END IF;
  UPDATE public.sholat_jumat SET tanggal=stamp,khatib=snap->>'khatib',imam=coalesce(snap->>'imam',snap->>'khatib'),muadzin=snap->>'muadzin',bilal=coalesce(snap->>'bilal','')||CASE WHEN coalesce(snap->>'maklumat','')<>'' THEN ' / '||(snap->>'maklumat') ELSE '' END,foto_imam=photo WHERE tanggal=stamp;
  GET DIAGNOSTICS affected=ROW_COUNT;
  IF affected=0 THEN INSERT INTO public.sholat_jumat(tanggal,khatib,imam,muadzin,bilal,foto_imam) VALUES(stamp,snap->>'khatib',coalesce(snap->>'imam',snap->>'khatib'),snap->>'muadzin',coalesce(snap->>'bilal','')||CASE WHEN coalesce(snap->>'maklumat','')<>'' THEN ' / '||(snap->>'maklumat') ELSE '' END,photo); END IF;
  IF snap ? 'durasi' THEN UPDATE public.app_settings SET prayer_mode_jumat_duration=(snap->>'durasi')::integer WHERE id=1; END IF;
  RETURN NEW;
 END IF;
 SELECT value::jsonb INTO old_snap FROM public.app_settings WHERE key='sholat_jumat' LIMIT 1;
 IF TG_OP='DELETE' THEN
  IF old_snap->>'tanggal'=to_char(OLD.tanggal,'YYYY-MM-DD') THEN DELETE FROM public.app_settings WHERE key='sholat_jumat'; END IF;
  RETURN OLD;
 END IF;
 row_data:=to_jsonb(NEW);
 snap:=CASE WHEN old_snap->>'tanggal'=to_char(NEW.tanggal,'YYYY-MM-DD') THEN old_snap ELSE '{}'::jsonb END;
 snap:=snap||jsonb_build_object('schema_version',2,'tanggal',to_char(NEW.tanggal,'YYYY-MM-DD'),'khatib',NEW.khatib,'imam',NEW.imam,'muadzin',NEW.muadzin,'bilal',split_part(coalesce(NEW.bilal,''),' / ',1),'maklumat',coalesce(row_data->>'pembaca_maklumat',nullif(split_part(coalesce(NEW.bilal,''),' / ',2),''),snap->>'maklumat',''),'updated_at',clock_timestamp());
 IF NEW.foto_imam IS DISTINCT FROM 'custom_uploaded' THEN snap:=snap||jsonb_build_object('foto_imam',coalesce(NEW.foto_imam,'image/display/default_imam.jpg')); END IF;
 INSERT INTO public.app_settings(key,value) VALUES('sholat_jumat',snap::text) ON CONFLICT(key) DO UPDATE SET value=excluded.value;
 RETURN NEW;
END $$;
REVOKE ALL ON FUNCTION public.signage_friday_bridge() FROM PUBLIC,anon,authenticated;
DROP TRIGGER IF EXISTS signage_friday_from_snapshot ON public.app_settings;
CREATE TRIGGER signage_friday_from_snapshot AFTER INSERT OR UPDATE OF value ON public.app_settings FOR EACH ROW EXECUTE FUNCTION public.signage_friday_bridge();
DROP TRIGGER IF EXISTS signage_friday_from_legacy ON public.sholat_jumat;
CREATE TRIGGER signage_friday_from_legacy AFTER INSERT OR UPDATE OR DELETE ON public.sholat_jumat FOR EACH ROW EXECUTE FUNCTION public.signage_friday_bridge();
COMMIT;
