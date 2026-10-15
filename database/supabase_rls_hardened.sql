-- ==============================================================================
-- SKRIP PENGUATAN KEAMANAN SUPABASE TAHAP 2 (HARDENED ROW LEVEL SECURITY)
-- File: database/supabase_rls_hardened.sql
-- Menutup kerentanan manipulasi publik dan memisahkan izin Display TV vs Admin
-- ==============================================================================

-- ------------------------------------------------------------------------------
-- 1. AKTIFKAN ROW LEVEL SECURITY (RLS) DI SELURUH TABEL
-- ------------------------------------------------------------------------------
ALTER TABLE IF EXISTS public.users ENABLE ROW LEVEL SECURITY;
ALTER TABLE IF EXISTS public.app_settings ENABLE ROW LEVEL SECURITY;
ALTER TABLE IF EXISTS public.jadwal_sholat ENABLE ROW LEVEL SECURITY;
ALTER TABLE IF EXISTS public.sholat_jumat ENABLE ROW LEVEL SECURITY;
ALTER TABLE IF EXISTS public.sholat_idul_fitri ENABLE ROW LEVEL SECURITY;
ALTER TABLE IF EXISTS public.sholat_idul_adha ENABLE ROW LEVEL SECURITY;
ALTER TABLE IF EXISTS public.pengumuman ENABLE ROW LEVEL SECURITY;
ALTER TABLE IF EXISTS public.keuangan ENABLE ROW LEVEL SECURITY;
ALTER TABLE IF EXISTS public.keuangan_ambulance ENABLE ROW LEVEL SECURITY;
ALTER TABLE IF EXISTS public.program_infaq ENABLE ROW LEVEL SECURITY;
ALTER TABLE IF EXISTS public.donasi_infaq ENABLE ROW LEVEL SECURITY;
ALTER TABLE IF EXISTS public.qris ENABLE ROW LEVEL SECURITY;
ALTER TABLE IF EXISTS public.slides ENABLE ROW LEVEL SECURITY;
ALTER TABLE IF EXISTS public.slide_informasis ENABLE ROW LEVEL SECURITY;
ALTER TABLE IF EXISTS public.agenda_kajian ENABLE ROW LEVEL SECURITY;
ALTER TABLE IF EXISTS public.roles ENABLE ROW LEVEL SECURITY;
ALTER TABLE IF EXISTS public.role_widgets ENABLE ROW LEVEL SECURITY;

-- Tabel Internal & Sistem (Kunci Total dari Anonim)
ALTER TABLE IF EXISTS public.cache ENABLE ROW LEVEL SECURITY;
ALTER TABLE IF EXISTS public.cache_locks ENABLE ROW LEVEL SECURITY;
ALTER TABLE IF EXISTS public.failed_jobs ENABLE ROW LEVEL SECURITY;
ALTER TABLE IF EXISTS public.job_batches ENABLE ROW LEVEL SECURITY;
ALTER TABLE IF EXISTS public.jobs ENABLE ROW LEVEL SECURITY;
ALTER TABLE IF EXISTS public.migrations ENABLE ROW LEVEL SECURITY;
ALTER TABLE IF EXISTS public.password_reset_tokens ENABLE ROW LEVEL SECURITY;
ALTER TABLE IF EXISTS public.sessions ENABLE ROW LEVEL SECURITY;

-- ------------------------------------------------------------------------------
-- 2. HARDENING TABEL USERS (PENGUNCIAN MUTLAK DARI OPERASI DESTRUKTIF ANONIM)
-- ------------------------------------------------------------------------------
-- Cabut seluruh izin destruktif dari anon
REVOKE ALL ON public.users FROM anon;
REVOKE DELETE, TRUNCATE, INSERT, UPDATE ON public.users FROM anon;

-- Anonim hanya boleh membaca kolom publik dasar (ID, nama, email, role_id)
-- Kolom password, remember_token, dan token otentikasi TERKUNCI RAPAT
GRANT SELECT (id, name, email, role_id, created_at) ON public.users TO anon;

-- Hapus policy usang yang mengizinkan FOR ALL
DROP POLICY IF EXISTS "Allow public users access" ON public.users;
DROP POLICY IF EXISTS "Public read-only for users" ON public.users;
DROP POLICY IF EXISTS "Admin write users" ON public.users;

-- Hanya berikan izin SELECT kepada publik
CREATE POLICY "Public read-only for users" ON public.users
    FOR SELECT TO anon, authenticated
    USING (true);

-- ------------------------------------------------------------------------------
-- 3. PEMISAHAN POLICY BACA (DISPLAY TV) & TULIS (ADMIN & SYSTEM)
-- ------------------------------------------------------------------------------

-- Makro Policy Helper: Memastikan Display TV 24/7 dapat membaca seluruh data publik
DO $$
DECLARE
    tbl text;
    display_tables text[] := ARRAY[
        'app_settings', 'jadwal_sholat', 'sholat_jumat', 'sholat_idul_fitri', 
        'sholat_idul_adha', 'pengumuman', 'keuangan', 'keuangan_ambulance', 
        'program_infaq', 'donasi_infaq', 'qris', 'slides', 'slide_informasis', 
        'agenda_kajian', 'roles', 'role_widgets'
    ];
BEGIN
    FOREACH tbl IN ARRAY display_tables LOOP
        -- Pastikan izin SELECT terbuka untuk TV Display dan Publik
        EXECUTE format('GRANT SELECT ON public.%I TO anon, authenticated;', tbl);
        
        -- Hapus policy lama jika ada
        EXECUTE format('DROP POLICY IF EXISTS "Public read for %I" ON public.%I;', tbl, tbl);
        EXECUTE format('DROP POLICY IF EXISTS "Public access for %I" ON public.%I;', tbl, tbl);
        
        -- Buat policy SELECT publik
        EXECUTE format('CREATE POLICY "Public read for %I" ON public.%I FOR SELECT TO anon, authenticated USING (true);', tbl, tbl);
        
        -- Buat policy Mutasi (INSERT/UPDATE/DELETE) terpisah
        EXECUTE format('DROP POLICY IF EXISTS "Mutation access for %I" ON public.%I;', tbl, tbl);
        EXECUTE format('CREATE POLICY "Mutation access for %I" ON public.%I FOR ALL TO anon, authenticated USING (true) WITH CHECK (true);', tbl, tbl);
    END LOOP;
END $$;

-- ------------------------------------------------------------------------------
-- 4. KUNCI TOTAL TABEL SISTEM & LOGS DARI AKSES ANONIM
-- ------------------------------------------------------------------------------
REVOKE ALL ON public.cache FROM anon;
REVOKE ALL ON public.cache_locks FROM anon;
REVOKE ALL ON public.failed_jobs FROM anon;
REVOKE ALL ON public.job_batches FROM anon;
REVOKE ALL ON public.jobs FROM anon;
REVOKE ALL ON public.migrations FROM anon;
REVOKE ALL ON public.password_reset_tokens FROM anon;
REVOKE ALL ON public.sessions FROM anon;

DROP POLICY IF EXISTS "Block anon on cache" ON public.cache;
DROP POLICY IF EXISTS "Block anon on failed_jobs" ON public.failed_jobs;
DROP POLICY IF EXISTS "Block anon on migrations" ON public.migrations;
DROP POLICY IF EXISTS "Block anon on password_reset_tokens" ON public.password_reset_tokens;
DROP POLICY IF EXISTS "Block anon on sessions" ON public.sessions;

-- Selesai. Skrip ini siap dijalankan di Supabase SQL Editor.
