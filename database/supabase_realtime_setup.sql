-- ==============================================================================
-- AKTIVASI SUPABASE REALTIME REPLICATION (POSTGRES CHANGES)
-- Proyek Target: digitalaljihad-cloud (xskusfacwsclbgdtgier)
-- Menjamin Event Realtime (<100ms) terkirim otomatis ke Smart TV Display
-- ==============================================================================

-- 1. Tambahkan seluruh tabel display ke publikasi supabase_realtime
DO $$
BEGIN
    -- Tambahkan tabel jika belum ada di publication
    BEGIN
        ALTER PUBLICATION supabase_realtime ADD TABLE 
            public.app_settings,
            public.jadwal_sholat,
            public.sholat_jumat,
            public.sholat_idul_fitri,
            public.sholat_idul_adha,
            public.pengumuman,
            public.keuangan,
            public.keuangan_ambulance,
            public.program_infaq,
            public.donasi_infaq,
            public.qris,
            public.slides;
    EXCEPTION
        WHEN duplicate_object THEN
            RAISE NOTICE 'Tabel sudah terdaftar di publication supabase_realtime.';
    END;
END $$;

-- 2. Set Replica Identity FULL agar seluruh field (sebelum & sesudah) terkirim di payload
ALTER TABLE IF EXISTS public.app_settings REPLICA IDENTITY FULL;
ALTER TABLE IF EXISTS public.jadwal_sholat REPLICA IDENTITY FULL;
ALTER TABLE IF EXISTS public.sholat_jumat REPLICA IDENTITY FULL;
ALTER TABLE IF EXISTS public.keuangan REPLICA IDENTITY FULL;
ALTER TABLE IF EXISTS public.keuangan_ambulance REPLICA IDENTITY FULL;
ALTER TABLE IF EXISTS public.program_infaq REPLICA IDENTITY FULL;
ALTER TABLE IF EXISTS public.donasi_infaq REPLICA IDENTITY FULL;
ALTER TABLE IF EXISTS public.qris REPLICA IDENTITY FULL;
ALTER TABLE IF EXISTS public.slides REPLICA IDENTITY FULL;
ALTER TABLE IF EXISTS public.pengumuman REPLICA IDENTITY FULL;

-- 3. Verifikasi daftar tabel yang aktif di publication
SELECT schemaname, tablename 
FROM pg_publication_tables 
WHERE pubname = 'supabase_realtime';
