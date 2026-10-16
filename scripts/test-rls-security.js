// Disposable PostgreSQL (WASM), never connects to Supabase or a network database.
const { PGlite } = require('@electric-sql/pglite');
const assert = require('assert/strict');
const fs = require('fs');
const path = require('path');
const migration = fs.readFileSync(path.join(__dirname, '../database/supabase_rls_hardened.sql'), 'utf8');
let checks = 0;
(async () => {
    const db = new PGlite();
    const adminId = '11111111-1111-4111-8111-111111111111';
    await db.exec(`
        CREATE ROLE anon; CREATE ROLE authenticated;
        CREATE SCHEMA auth;
        CREATE TABLE auth.users (id uuid PRIMARY KEY, raw_app_meta_data jsonb);
        CREATE FUNCTION auth.jwt() RETURNS jsonb LANGUAGE sql STABLE AS
            $$ SELECT coalesce(nullif(current_setting('request.jwt.claims', true), ''), '{}')::jsonb $$;
        CREATE FUNCTION auth.uid() RETURNS uuid LANGUAGE sql STABLE AS
            $$ SELECT nullif(auth.jwt()->>'sub', '')::uuid $$;
        GRANT USAGE ON SCHEMA public, auth TO anon, authenticated;
        GRANT EXECUTE ON FUNCTION auth.jwt(), auth.uid() TO anon, authenticated;
        CREATE TABLE public.app_settings (id int PRIMARY KEY, key text, value text);
        CREATE TABLE public.jadwal_sholat (id int PRIMARY KEY, waktu text);
        CREATE TABLE public.keuangan (id int PRIMARY KEY, nominal int);
        CREATE TABLE public.users (id int PRIMARY KEY, name text, email text, role_id int, created_at timestamp, password text);
        CREATE TABLE public.sessions (id int PRIMARY KEY, payload text);
        INSERT INTO public.app_settings VALUES (1, NULL, 'public'), (2, 'rbac_users_list', 'PRIVATE');
        INSERT INTO public.jadwal_sholat VALUES (1, '04:30');
        INSERT INTO public.keuangan VALUES (1, 100);
        INSERT INTO public.users VALUES (1, 'Test', 'test@example.invalid', 3, now(), 'PRIVATE');
        ALTER TABLE public.jadwal_sholat ENABLE ROW LEVEL SECURITY;
        GRANT ALL ON TABLE public.jadwal_sholat TO anon, authenticated;
        CREATE POLICY old_insecure ON public.jadwal_sholat FOR ALL TO anon, authenticated USING (true) WITH CHECK (true);
    `);
    await assert.rejects(db.exec(migration), /Migration blocked/); checks++;
    await db.exec('ROLLBACK');
    assert.equal((await db.query("SELECT count(*)::int AS n FROM pg_policies WHERE policyname = 'old_insecure'")).rows[0].n, 1); checks++;
    await db.exec("SET signage.verified_auth_ready='true'; SET signage.database_backup_verified='true'");
    await assert.rejects(db.exec(migration), /no server-assigned/); checks++;
    await db.exec('ROLLBACK');
    await db.query('INSERT INTO auth.users VALUES ($1, $2)', [adminId, { signage_role: 'admin' }]);
    // A schema mismatch halfway through DDL must roll back prior policy/grant changes.
    await db.exec('ALTER TABLE public.users RENAME COLUMN created_at TO old_created_at');
    await assert.rejects(db.exec(migration), /created_at/); checks++;
    await db.exec('ROLLBACK');
    assert.equal((await db.query("SELECT count(*)::int AS n FROM pg_policies WHERE policyname='old_insecure'")).rows[0].n, 1); checks++;
    await db.exec('ALTER TABLE public.users RENAME COLUMN old_created_at TO created_at');
    await db.exec(migration);
    assert.equal((await db.query("SELECT count(*)::int AS n FROM pg_policies WHERE policyname='old_insecure'")).rows[0].n, 0); checks++;
    async function claims(role, metadata, userMetadata = {}) {
        await db.exec('RESET ROLE');
        await db.query("SELECT set_config('request.jwt.claims', $1, false)", [JSON.stringify({ ...(role === 'authenticated' ? { sub: adminId } : {}), app_metadata: metadata, user_metadata: userMetadata })]);
        await db.exec('SET ROLE ' + role);
    }
    async function denied(sql) {
        try { const result = await db.query(sql); assert.equal(result.affectedRows, 0, 'must affect no rows'); }
        catch (e) { if (e instanceof assert.AssertionError) throw e; assert.match(e.message, /permission denied|row-level security/); }
        checks++;
    }
    await claims('anon', {});
    assert.equal((await db.query('SELECT * FROM public.jadwal_sholat')).rows.length, 1); checks++;
    assert.equal((await db.query('SELECT * FROM public.app_settings')).rows.length, 1); checks++;
    await denied("INSERT INTO public.jadwal_sholat VALUES (2,'05:00')");
    await denied("UPDATE public.jadwal_sholat SET waktu='05:00'");
    await denied('DELETE FROM public.jadwal_sholat');
    await denied('SELECT password FROM public.users');
    await denied('SELECT * FROM public.sessions');
    await claims('authenticated', {}, { signage_role: 'admin' });
    await denied("UPDATE public.jadwal_sholat SET waktu='05:00'");
    await claims('authenticated', { signage_role: 'petugas' });
    assert.equal((await db.query("UPDATE public.jadwal_sholat SET waktu='04:45' WHERE id=1")).affectedRows, 1); checks++;
    await denied('UPDATE public.keuangan SET nominal=999');
    await denied("UPDATE public.app_settings SET value='x'");
    await claims('authenticated', { signage_role: 'bendahara' });
    assert.equal((await db.query('UPDATE public.keuangan SET nominal=200 WHERE id=1')).affectedRows, 1); checks++;
    await denied("UPDATE public.jadwal_sholat SET waktu='05:00'");
    await claims('authenticated', { signage_role: 'admin' });
    assert.equal((await db.query("UPDATE public.app_settings SET value='updated' WHERE id=1")).affectedRows, 1); checks++;
    assert.equal((await db.query('SELECT name FROM public.users')).rows.length, 1); checks++;
    await denied('SELECT password FROM public.users');
    await db.exec('RESET ROLE');
    // Repeatability: re-running does not resurrect old policies.
    await db.exec(migration);
    checks++;
    console.log(`RLS PostgreSQL: ${checks} checks passed`);
    await db.close();
})().catch(e => { console.error(e); process.exitCode = 1; });
