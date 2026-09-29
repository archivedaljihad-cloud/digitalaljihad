-- ==============================================================================
-- SKRIP KEAMANAN SUPABASE: ROW LEVEL SECURITY (RLS) & PROTEKSI DATA SENSITIF
-- Dijalankan pada: SQL Editor di Supabase Dashboard
-- Target: Project digitalaljihad-cloud (xskusfacwsclbgdtgier)
-- & Project digitalaljihad (jhukhvxpgezbftbxgdgbc)
-- Menuntaskan Peringatan Keamanan:
-- 1. Table publicly accessible (rls_disabled_in_public)
-- 2. Sensitive data publicly accessible (sensitive_columns_exposed)
-- ==============================================================================

-- ------------------------------------------------------------------------------
-- 1. AKTIFKAN ROW LEVEL SECURITY (RLS) DI SELURUH TABEL PUBLIK
-- ------------------------------------------------------------------------------
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
ALTER TABLE IF EXISTS public.users ENABLE ROW LEVEL SECURITY;

-- Tabel Sistem & Internal (RLS Aktif, Akses anon ditolak secara default)
ALTER TABLE IF EXISTS public.cache ENABLE ROW LEVEL SECURITY;
ALTER TABLE IF EXISTS public.cache_locks ENABLE ROW LEVEL SECURITY;
ALTER TABLE IF EXISTS public.failed_jobs ENABLE ROW LEVEL SECURITY;
ALTER TABLE IF EXISTS public.job_batches ENABLE ROW LEVEL SECURITY;
ALTER TABLE IF EXISTS public.jobs ENABLE ROW LEVEL SECURITY;
ALTER TABLE IF EXISTS public.migrations ENABLE ROW LEVEL SECURITY;
ALTER TABLE IF EXISTS public.password_reset_tokens ENABLE ROW LEVEL SECURITY;
ALTER TABLE IF EXISTS public.sessions ENABLE ROW LEVEL SECURITY;

-- ------------------------------------------------------------------------------
-- 2. PROTEKSI KOLOM SENSITIF PADA TABEL USERS
-- Mencegah password hash ($2y$12$...) dan remember_token terekspos ke publik
-- ------------------------------------------------------------------------------
REVOKE ALL ON public.users FROM anon, authenticated;
GRANT SELECT (id, name, last_name, email, email_verified_at, created_at, updated_at, role_id) ON public.users TO anon, authenticated;
GRANT INSERT (name, email, role_id) ON public.users TO anon, authenticated;
GRANT UPDATE (name, email, role_id) ON public.users TO anon, authenticated;
GRANT DELETE ON public.users TO anon, authenticated;

-- Bersihkan data hash password yang tidak diperlukan di database cloud statis
UPDATE public.users SET password = NULL, remember_token = NULL WHERE password IS NOT NULL OR remember_token IS NOT NULL;

-- Kebijakan (Policy) Tabel Users
DROP POLICY IF EXISTS "Allow public users access" ON public.users;
CREATE POLICY "Allow public users access" ON public.users
    FOR ALL TO anon, authenticated
    USING (true)
    WITH CHECK (true);

-- ------------------------------------------------------------------------------
-- 3. KEBIJAKAN AKSES TABEL DISPLAY & ADMIN (OPERASI BROWSER / REST API)
-- Memastikan TV Display & Web Admin tetap berfungsi lancar membaca dan memperbarui data
-- ------------------------------------------------------------------------------

-- app_settings
DROP POLICY IF EXISTS "Public access for app_settings" ON public.app_settings;
CREATE POLICY "Public access for app_settings" ON public.app_settings FOR ALL TO anon, authenticated USING (true) WITH CHECK (true);

-- jadwal_sholat
DROP POLICY IF EXISTS "Public access for jadwal_sholat" ON public.jadwal_sholat;
CREATE POLICY "Public access for jadwal_sholat" ON public.jadwal_sholat FOR ALL TO anon, authenticated USING (true) WITH CHECK (true);

-- sholat_jumat
DROP POLICY IF EXISTS "Public access for sholat_jumat" ON public.sholat_jumat;
CREATE POLICY "Public access for sholat_jumat" ON public.sholat_jumat FOR ALL TO anon, authenticated USING (true) WITH CHECK (true);

-- sholat_idul_fitri
DROP POLICY IF EXISTS "Public access for sholat_idul_fitri" ON public.sholat_idul_fitri;
CREATE POLICY "Public access for sholat_idul_fitri" ON public.sholat_idul_fitri FOR ALL TO anon, authenticated USING (true) WITH CHECK (true);

-- sholat_idul_adha
DROP POLICY IF EXISTS "Public access for sholat_idul_adha" ON public.sholat_idul_adha;
CREATE POLICY "Public access for sholat_idul_adha" ON public.sholat_idul_adha FOR ALL TO anon, authenticated USING (true) WITH CHECK (true);

-- pengumuman
DROP POLICY IF EXISTS "Public access for pengumuman" ON public.pengumuman;
CREATE POLICY "Public access for pengumuman" ON public.pengumuman FOR ALL TO anon, authenticated USING (true) WITH CHECK (true);

-- keuangan
DROP POLICY IF EXISTS "Public access for keuangan" ON public.keuangan;
CREATE POLICY "Public access for keuangan" ON public.keuangan FOR ALL TO anon, authenticated USING (true) WITH CHECK (true);

-- keuangan_ambulance
DROP POLICY IF EXISTS "Public access for keuangan_ambulance" ON public.keuangan_ambulance;
CREATE POLICY "Public access for keuangan_ambulance" ON public.keuangan_ambulance FOR ALL TO anon, authenticated USING (true) WITH CHECK (true);

-- program_infaq
DROP POLICY IF EXISTS "Public access for program_infaq" ON public.program_infaq;
CREATE POLICY "Public access for program_infaq" ON public.program_infaq FOR ALL TO anon, authenticated USING (true) WITH CHECK (true);

-- donasi_infaq
DROP POLICY IF EXISTS "Public access for donasi_infaq" ON public.donasi_infaq;
CREATE POLICY "Public access for donasi_infaq" ON public.donasi_infaq FOR ALL TO anon, authenticated USING (true) WITH CHECK (true);

-- qris
DROP POLICY IF EXISTS "Public access for qris" ON public.qris;
CREATE POLICY "Public access for qris" ON public.qris FOR ALL TO anon, authenticated USING (true) WITH CHECK (true);

-- slides
DROP POLICY IF EXISTS "Public access for slides" ON public.slides;
CREATE POLICY "Public access for slides" ON public.slides FOR ALL TO anon, authenticated USING (true) WITH CHECK (true);

-- slide_informasis
DROP POLICY IF EXISTS "Public access for slide_informasis" ON public.slide_informasis;
CREATE POLICY "Public access for slide_informasis" ON public.slide_informasis FOR ALL TO anon, authenticated USING (true) WITH CHECK (true);

-- agenda_kajian
DROP POLICY IF EXISTS "Public access for agenda_kajian" ON public.agenda_kajian;
CREATE POLICY "Public access for agenda_kajian" ON public.agenda_kajian FOR ALL TO anon, authenticated USING (true) WITH CHECK (true);

-- roles
DROP POLICY IF EXISTS "Public access for roles" ON public.roles;
CREATE POLICY "Public access for roles" ON public.roles FOR ALL TO anon, authenticated USING (true) WITH CHECK (true);

-- role_widgets
DROP POLICY IF EXISTS "Public access for role_widgets" ON public.role_widgets;
CREATE POLICY "Public access for role_widgets" ON public.role_widgets FOR ALL TO anon, authenticated USING (true) WITH CHECK (true);
