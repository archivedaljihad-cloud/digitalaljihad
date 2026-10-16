-- DEPRECATED ENTRY POINT. The old anonymous-write policies were unsafe.
-- Use supabase_rls_hardened.sql after completing RLS_MIGRATION.md.
-- Fail before any DDL so an old deployment cannot accidentally reopen writes.
DO $$
BEGIN
    RAISE EXCEPTION 'Deprecated RLS script blocked. Follow database/RLS_MIGRATION.md and use supabase_rls_hardened.sql';
END
$$;
