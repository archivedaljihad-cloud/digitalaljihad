-- AMAN untuk pemeriksaan awal: hanya SELECT; tidak menampilkan isi password/token.
SELECT current_database() AS database_name, current_user AS sql_role, version();
SELECT schemaname,tablename,rowsecurity FROM pg_tables WHERE schemaname='public' ORDER BY tablename;
SELECT schemaname,tablename,policyname,roles,cmd,qual,with_check FROM pg_policies WHERE schemaname='public' ORDER BY tablename,policyname;
SELECT table_name,column_name,data_type,is_nullable FROM information_schema.columns WHERE table_schema='public' AND table_name IN('app_settings','sholat_jumat','jadwal_sholat','keuangan') ORDER BY table_name,ordinal_position;
SELECT tc.table_name,tc.constraint_type,kcu.column_name FROM information_schema.table_constraints tc LEFT JOIN information_schema.key_column_usage kcu USING(constraint_catalog,constraint_schema,constraint_name) WHERE tc.table_schema='public' AND tc.table_name IN('app_settings','sholat_jumat') ORDER BY tc.table_name,tc.constraint_name;
SELECT key,count(*) AS rows FROM public.app_settings GROUP BY key ORDER BY key;
SELECT count(*) AS main_settings_rows FROM public.app_settings WHERE id=1;
SELECT nama_sholat,count(*) FROM public.jadwal_sholat GROUP BY nama_sholat ORDER BY nama_sholat;
SELECT raw_app_meta_data->>'signage_role' AS server_role,count(*) AS accounts,count(*) FILTER(WHERE email_confirmed_at IS NOT NULL) AS confirmed FROM auth.users GROUP BY 1;
-- Hasil terakhir: tidak ada isi akun yang dibaca/ditampilkan.
-- Tinjau fungsi SECURITY DEFINER lama: RLS tabel saja tidak menutup RPC yang terlalu permisif.
SELECT n.nspname AS schema_name,p.proname,pg_get_function_identity_arguments(p.oid) AS arguments,
 has_function_privilege('anon',p.oid,'EXECUTE') AS anon_execute,
 has_function_privilege('authenticated',p.oid,'EXECUTE') AS authenticated_execute
FROM pg_proc p JOIN pg_namespace n ON n.oid=p.pronamespace
WHERE n.nspname='public' AND p.prosecdef ORDER BY p.proname;
-- Anggota role tambahan dapat menambah hak efektif. Tinjau, jangan ubah sembarangan.
SELECT member_role.rolname AS member,granted_role.rolname AS inherited_role
FROM pg_auth_members m JOIN pg_roles member_role ON member_role.oid=m.member
JOIN pg_roles granted_role ON granted_role.oid=m.roleid
WHERE member_role.rolname IN('anon','authenticated');
