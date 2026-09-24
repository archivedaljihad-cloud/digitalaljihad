// web-statis/js/supabase-config.js
/**
 * Konfigurasi Supabase BaaS (Database Cloud & Realtime)
 * Dapat diubah kapan saja jika berpindah project Supabase.
 */
const SUPABASE_CONFIG = {
    url: 'https://xskusfacwsclbgdtgier.supabase.co',
    anonKey: 'sb_publishable_lsUgbFcTmwuwiiV70rzSWQ_V0JUR-mX'
};

if (typeof module !== 'undefined' && module.exports) {
    module.exports = SUPABASE_CONFIG;
}
