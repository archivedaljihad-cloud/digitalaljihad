BEGIN;
CREATE SCHEMA IF NOT EXISTS signage_private;
REVOKE ALL ON SCHEMA signage_private FROM PUBLIC,anon,authenticated;
CREATE TABLE IF NOT EXISTS signage_private.dashboard_rls_backup (
 name text PRIMARY KEY, saved_at timestamptz NOT NULL DEFAULT now(), restore_sql text NOT NULL
);
REVOKE ALL ON signage_private.dashboard_rls_backup FROM PUBLIC,anon,authenticated;
DO $snapshot$
DECLARE t record; pol record; grant_row record; role_list text; commands text:=''; object_name text;
BEGIN
 IF EXISTS(SELECT 1 FROM signage_private.dashboard_rls_backup WHERE name='dashboard-20261017') THEN
  RAISE EXCEPTION 'Snapshot already exists; do not overwrite the original rollback';
 END IF;
 FOR t IN SELECT c.oid,c.relname,c.relkind,c.relrowsecurity,c.relforcerowsecurity FROM pg_class c JOIN pg_namespace n ON n.oid=c.relnamespace WHERE n.nspname='public' AND c.relkind IN('r','p','v','m','S') LOOP
  object_name:=format('public.%I',t.relname);
  IF t.relkind IN('r','p') THEN
   commands:=commands||format('DO $drop$ DECLARE p record; BEGIN FOR p IN SELECT policyname FROM pg_policies WHERE schemaname=''public'' AND tablename=%L LOOP EXECUTE format(''DROP POLICY %%I ON %s'',p.policyname); END LOOP; END $drop$;',t.relname,object_name)||chr(10);
   FOR pol IN SELECT * FROM pg_policies WHERE schemaname='public' AND tablename=t.relname LOOP
    SELECT string_agg(CASE WHEN r::text='public' THEN 'PUBLIC' ELSE quote_ident(r::text) END,',') INTO role_list FROM unnest(pol.roles) r;
    commands:=commands||format('CREATE POLICY %I ON %s AS %s FOR %s TO %s%s%s;',pol.policyname,object_name,pol.permissive,pol.cmd,role_list,CASE WHEN pol.qual IS NOT NULL THEN ' USING ('||pol.qual||')' ELSE '' END,CASE WHEN pol.with_check IS NOT NULL THEN ' WITH CHECK ('||pol.with_check||')' ELSE '' END)||chr(10);
   END LOOP;
   commands:=commands||format('ALTER TABLE %s %s ROW LEVEL SECURITY; ALTER TABLE %s %s FORCE ROW LEVEL SECURITY;',object_name,CASE WHEN t.relrowsecurity THEN 'ENABLE' ELSE 'DISABLE' END,object_name,CASE WHEN t.relforcerowsecurity THEN '' ELSE 'NO' END)||chr(10);
  END IF;
  commands:=commands||format('REVOKE ALL ON %s %s FROM PUBLIC,anon,authenticated;',CASE WHEN t.relkind='S' THEN 'SEQUENCE' ELSE 'TABLE' END,object_name)||chr(10);
  IF t.relkind<>'S' THEN
   FOR pol IN SELECT attname FROM pg_attribute WHERE attrelid=t.oid AND attnum>0 AND NOT attisdropped LOOP
    commands:=commands||format('REVOKE ALL (%I) ON %s FROM PUBLIC,anon,authenticated;',pol.attname,object_name)||chr(10);
   END LOOP;
  END IF;
  FOR grant_row IN SELECT a.*,CASE WHEN a.grantee=0 THEN 'PUBLIC' ELSE quote_ident(pg_get_userbyid(a.grantee)) END AS recipient FROM pg_class c CROSS JOIN LATERAL aclexplode(c.relacl) a WHERE c.oid=t.oid AND (a.grantee=0 OR pg_get_userbyid(a.grantee) IN('anon','authenticated')) LOOP
   commands:=commands||format('GRANT %s ON %s %s TO %s%s;',grant_row.privilege_type,CASE WHEN t.relkind='S' THEN 'SEQUENCE' ELSE 'TABLE' END,object_name,grant_row.recipient,CASE WHEN grant_row.is_grantable THEN ' WITH GRANT OPTION' ELSE '' END)||chr(10);
  END LOOP;
  FOR grant_row IN SELECT a.attname,g.*,CASE WHEN g.grantee=0 THEN 'PUBLIC' ELSE quote_ident(pg_get_userbyid(g.grantee)) END AS recipient FROM pg_attribute a CROSS JOIN LATERAL aclexplode(a.attacl) g WHERE a.attrelid=t.oid AND a.attnum>0 AND NOT a.attisdropped AND (g.grantee=0 OR pg_get_userbyid(g.grantee) IN('anon','authenticated')) LOOP
   commands:=commands||format('GRANT %s (%I) ON %s TO %s%s;',grant_row.privilege_type,grant_row.attname,object_name,grant_row.recipient,CASE WHEN grant_row.is_grantable THEN ' WITH GRANT OPTION' ELSE '' END)||chr(10);
  END LOOP;
 END LOOP;
 INSERT INTO signage_private.dashboard_rls_backup(name,restore_sql) VALUES('dashboard-20261017',commands);
END $snapshot$;
COMMIT;
SELECT name,saved_at,length(restore_sql) AS rollback_characters FROM signage_private.dashboard_rls_backup;
-- Simpan hasil query berikut secara privat (metadata policy/grant):
SELECT restore_sql FROM signage_private.dashboard_rls_backup WHERE name='dashboard-20261017';
