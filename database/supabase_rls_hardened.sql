-- STAGED MIGRATION: do not apply to the legacy anonymous-writing frontend.
-- Prerequisites and recovery: database/RLS_MIGRATION.md.
-- Run the WHOLE transaction with stop-on-error. No data rows are modified.
BEGIN;

DO $preflight$
BEGIN
    IF current_setting('signage.verified_auth_ready', true) IS DISTINCT FROM 'true'
       OR current_setting('signage.database_backup_verified', true) IS DISTINCT FROM 'true' THEN
        RAISE EXCEPTION 'Migration blocked: verify database backup and authenticated frontend in staging first. See RLS_MIGRATION.md';
    END IF;
    IF NOT EXISTS (
        SELECT 1 FROM auth.users
        WHERE raw_app_meta_data ->> 'signage_role' = 'admin'
    ) THEN
        RAISE EXCEPTION 'Migration blocked: no server-assigned Supabase Auth signage admin';
    END IF;
END
$preflight$;

DO $policies$
DECLARE
    tbl text;
    old_policy record;
    write_roles text[];
    read_predicate text;
    write_predicate text;
    finance_tables text[] := ARRAY['keuangan', 'keuangan_ambulance', 'program_infaq', 'donasi_infaq', 'qris'];
    content_tables text[] := ARRAY['jadwal_sholat', 'sholat_jumat', 'sholat_idul_fitri',
        'sholat_idul_adha', 'pengumuman', 'slides', 'slide_informasis', 'agenda_kajian'];
BEGIN
    FOREACH tbl IN ARRAY ARRAY['app_settings', 'jadwal_sholat', 'sholat_jumat',
        'sholat_idul_fitri', 'sholat_idul_adha', 'pengumuman', 'keuangan',
        'keuangan_ambulance', 'program_infaq', 'donasi_infaq', 'qris', 'slides',
        'slide_informasis', 'agenda_kajian', 'roles', 'role_widgets', 'users']
    LOOP
        IF to_regclass(format('public.%I', tbl)) IS NULL THEN
            RAISE NOTICE 'Skipping absent table: %', tbl;
            CONTINUE;
        END IF;
        EXECUTE format('ALTER TABLE public.%I ENABLE ROW LEVEL SECURITY', tbl);

        -- Remove ALL old permissive policies: PostgreSQL OR-combines permissive policies.
        -- Merely adding a restrictive-looking replacement would leave old FOR ALL grants active.
        FOR old_policy IN
            SELECT policyname FROM pg_policies WHERE schemaname = 'public' AND tablename = tbl
        LOOP
            EXECUTE format('DROP POLICY %I ON public.%I', old_policy.policyname, tbl);
        END LOOP;

        EXECUTE format('REVOKE ALL ON TABLE public.%I FROM PUBLIC, anon, authenticated', tbl);
        IF tbl = 'users' THEN
            -- Legacy password hashes/tokens are not exposed, even to browser admins.
            EXECUTE 'GRANT SELECT (id, name, email, role_id, created_at) ON public.users TO authenticated';
            EXECUTE 'CREATE POLICY signage_users_read ON public.users FOR SELECT TO authenticated USING ((select auth.uid()) IS NOT NULL AND (select auth.jwt()) -> ''app_metadata'' ->> ''signage_role'' = ''admin'')';
            CONTINUE;
        END IF;

        IF tbl IN ('roles', 'role_widgets') THEN
            read_predicate := '(select auth.uid()) IS NOT NULL AND (select auth.jwt()) -> ''app_metadata'' ->> ''signage_role'' = ''admin''';
            EXECUTE format('GRANT SELECT ON TABLE public.%I TO authenticated', tbl);
            EXECUTE format('CREATE POLICY signage_read ON public.%I FOR SELECT TO authenticated USING (%s)', tbl, read_predicate);
        ELSE
            read_predicate := 'true';
            IF tbl = 'app_settings' THEN
                -- Remove rbac_users_list after migration; never publish account credentials to TV.
                read_predicate := '(key IS NULL OR key <> ''rbac_users_list'')';
            END IF;
            EXECUTE format('GRANT SELECT ON TABLE public.%I TO anon, authenticated', tbl);
            EXECUTE format('CREATE POLICY signage_read ON public.%I FOR SELECT TO anon, authenticated USING (%s)', tbl, read_predicate);
        END IF;

        write_roles := ARRAY['admin'];
        IF tbl = ANY(finance_tables) THEN write_roles := ARRAY['admin', 'bendahara']; END IF;
        IF tbl = ANY(content_tables) THEN write_roles := ARRAY['admin', 'petugas']; END IF;
        -- Shared settings remain admin-only; never authorize by browser role/localStorage or user_metadata.
        write_predicate := format('(select auth.uid()) IS NOT NULL AND ((select auth.jwt()) -> ''app_metadata'' ->> ''signage_role'') = ANY (%L::text[])', write_roles);
        EXECUTE format('GRANT INSERT, UPDATE, DELETE ON TABLE public.%I TO authenticated', tbl);
        EXECUTE format('CREATE POLICY signage_insert ON public.%I FOR INSERT TO authenticated WITH CHECK (%s)', tbl, write_predicate);
        EXECUTE format('CREATE POLICY signage_update ON public.%I FOR UPDATE TO authenticated USING (%s) WITH CHECK (%s)', tbl, write_predicate, write_predicate);
        EXECUTE format('CREATE POLICY signage_delete ON public.%I FOR DELETE TO authenticated USING (%s)', tbl, write_predicate);
    END LOOP;

    -- Internal tables have no browser grants or permissive browser policies.
    FOREACH tbl IN ARRAY ARRAY['cache', 'cache_locks', 'failed_jobs', 'job_batches',
        'jobs', 'migrations', 'password_reset_tokens', 'sessions']
    LOOP
        IF to_regclass(format('public.%I', tbl)) IS NULL THEN CONTINUE; END IF;
        EXECUTE format('ALTER TABLE public.%I ENABLE ROW LEVEL SECURITY', tbl);
        EXECUTE format('REVOKE ALL ON TABLE public.%I FROM PUBLIC, anon, authenticated', tbl);
    END LOOP;
END
$policies$;

COMMIT;
