// web-statis/js/supabase-db.js
/**
 * Supabase DB Client & Realtime Data Manager untuk Display Masjid
 */

(function (window) {
    let sbClient = null;

    function getSbConfig() {
        if (typeof SUPABASE_CONFIG !== 'undefined' && SUPABASE_CONFIG && SUPABASE_CONFIG.url) return SUPABASE_CONFIG;
        if (typeof window !== 'undefined' && window.SUPABASE_CONFIG && window.SUPABASE_CONFIG.url) return window.SUPABASE_CONFIG;
        return {
            url: 'https://xskusfacwsclbgdtgier.supabase.co',
            anonKey: 'sb_publishable_lsUgbFcTmwuwiiV70rzSWQ_V0JUR-mX'
        };
    }

    function getClient() {
        if (!sbClient) {
            if (typeof window.supabase === 'undefined' || !window.supabase.createClient) {
                console.warn('Supabase JS SDK belum dimuat. Menggunakan fallback lokal.');
                return null;
            }
            const cfg = getSbConfig();
            sbClient = window.supabase.createClient(cfg.url, cfg.anonKey);
        }
        return sbClient;
    }

    const SupabaseDB = {
        /**
         * Akses instans client Supabase JS SDK resmi (diperlukan form mobile & admin)
         */
        getClient() {
            return getClient();
        },

        // Fallback default settings jika offline atau database belum terhubung
        defaultSettings: {
            nama_aplikasi: 'MASJID JAMI\' AL-JIHAD',
            sub_header: 'SISTEM INFORMASI DIGITAL',
            rotation_interval: 10,
            rotation_enabled: true,
            prayer_mode_enabled: true,
            prayer_mode_before_adzan: 5,
            prayer_mode_adzan_duration: 5,
            prayer_mode_iqamah_duration: 10,
            prayer_mode_duration: 10,
            prayer_mode_after_prayer: 10,
            prayer_mode_jumat_duration: 50,
            prayer_countdown_style: 'flip',
            enable_dynamic_theme: true,
            enable_next_prayer_bar: true,
            live_makkah_url: 'https://www.youtube.com/watch?v=eC4LfEVxvKg',
            live_madinah_url: 'https://www.youtube.com/watch?v=Rs7St51oDDc',
            cctv_mimbar_url: '',
            running_text: 'Selamat Datang di Masjid Jami\' Al-Jihad. Luruskan dan rapatkan shaf saat sholat berjamaah. Jagalah kebersihan dan kesucian masjid.',
            running_text_pages: {},
            kajian_sabtu_enabled: true,
            kajian_sabtu_start_time: '18:25',
            kajian_sabtu_data: null,
            kegiatan_rutin_settings: null,
            pengumuman_jumat: null,
            ramadhan_infaq: [],
            rbac_users_list: [],
            rotation_pages: [
                { page: '/utama-embed', path: 'slides/utama.html', name: 'Jadwal Sholat 5 Waktu', active: true, order: 1 },
                { page: '/keuangan-embed', path: 'slides/keuangan.html', name: 'Rincian Kas Masjid', active: true, order: 2 },
                { page: '/jumat-embed', path: 'slides/jumat.html', name: 'Petugas Sholat Jum\'at', active: true, order: 3 },
                { page: '/pengumuman-embed', path: 'slides/pengumuman.html', name: 'Pengumuman DKM', active: true, order: 4 },
                { page: '/keuangan-summary-embed', path: 'slides/keuangan-summary.html', name: 'Grafik Kas', active: true, order: 5 },
                { page: '/qris-embed', path: 'slides/qris.html', name: 'QRIS Infaq Digital', active: true, order: 6 },
                { page: '/slide-embed', path: 'slides/slide.html', name: 'Poster & Brosur Slide', active: true, order: 7 },
                { page: '/ambulance-embed', path: 'slides/ambulance.html', name: 'Kas Mobil Ambulance', active: true, order: 8 },
                { page: '/infaq-embed', path: 'slides/infaq.html', name: 'Program Donasi Infaq', active: true, order: 9 },
                { page: '/hikmah-embed', path: 'slides/hikmah.html', name: 'Mutiara Hadits & Hikmah', active: true, order: 10 },
                { page: '/qurban-embed', path: 'slides/qurban.html', name: 'Penerimaan Hewan Qurban', active: true, order: 11 },
                { page: '/yasin-embed', path: 'slides/yasin.html', name: 'Surat Yaasiin 83 Ayat', active: true, order: 12 },
                { page: '/live-mekah-embed', path: 'slides/live-mekah.html', name: 'Live TV Makkah (Masjidil Haram)', active: true, order: 13 },
                { page: '/live-madinah-embed', path: 'slides/live-madinah.html', name: 'Live TV Madinah (Masjid Nabawi)', active: true, order: 14 },
                { page: '/live-mimbar-embed', path: 'slides/live-mimbar.html', name: 'Live CCTV Mimbar Khutbah', active: true, order: 15 },
                { page: '/idul-fitri-embed', path: 'slides/idul-fitri.html', name: 'Petugas Sholat Idul Fitri', active: false, order: 16 },
                { page: '/idul-adha-embed', path: 'slides/idul-adha.html', name: 'Petugas Sholat Idul Adha', active: false, order: 17 },
                { page: '/ramadhan-embed', path: 'slides/ramadhan.html', name: 'Semarak Ramadhan & Kas Tromol', active: true, order: 18 },
                { page: '/undangan-embed', path: 'slides/undangan.html', name: 'Undangan Luar (Ukhuwah)', active: true, order: 19 },
                { page: '/kas-jumat-embed', path: 'slides/kas-jumat.html', name: "Maklumat & Kas Jum'at (Khusus Jum'at)", active: true, order: 20 }
            ]
        },

        /**
         * Direct Native REST Fetch ke Supabase Cloud (Zero Dependency)
         * Berjalan 100% tanpa bergantung pada ketersediaan CDN JS SDK
         */
        async restFetch(path, options = {}) {
            const cfg = getSbConfig();
            const headers = Object.assign({
                'apikey': cfg.anonKey,
                'Authorization': `Bearer ${cfg.anonKey}`,
                'Content-Type': 'application/json'
            }, options.headers || {});

            const cleanPath = path.startsWith('/') ? path.slice(1) : path;
            const res = await fetch(`${cfg.url}/rest/v1/${cleanPath}`, {
                ...options,
                headers
            });
            if (!res.ok) {
                throw new Error(`Supabase REST HTTP ${res.status}`);
            }
            return await res.json();
        },

        /**
         * Mutasi REST Supabase yang Aman & Tervalidasi (PATCH, POST, DELETE)
         * Memastikan res.ok, status HTTP, serta memverifikasi baris terpengaruh (rowsAffected > 0).
         * Menangani timeout jaringan dan mengembalikan status cloudConfirmed secara jujur.
         */
        async safeRestMutation(endpoint, method, payload, options = {}) {
            const cfg = getSbConfig();
            if (!cfg || !cfg.url) {
                return {
                    success: false,
                    cloudConfirmed: false,
                    status: 0,
                    error: 'Konfigurasi Supabase tidak ditemukan'
                };
            }

            const headers = Object.assign({
                'apikey': cfg.anonKey,
                'Authorization': `Bearer ${cfg.anonKey}`,
                'Content-Type': 'application/json',
                'Prefer': 'return=representation'
            }, options.headers || {});

            const cleanPath = endpoint.startsWith('http') ? endpoint : `${cfg.url}/rest/v1/${endpoint.replace(/^\//, '')}`;
            
            let controller = null;
            let timeoutId = null;
            if (typeof AbortController !== 'undefined') {
                controller = new AbortController();
                timeoutId = setTimeout(() => {
                    try { controller.abort(); } catch(e) {}
                }, options.timeoutMs || 10000);
            }

            try {
                const fetchOpts = {
                    method: String(method || 'POST').toUpperCase(),
                    headers: headers
                };
                if (controller) {
                    fetchOpts.signal = controller.signal;
                }
                if (payload !== undefined && payload !== null) {
                    fetchOpts.body = typeof payload === 'string' ? payload : JSON.stringify(payload);
                }

                const res = await fetch(cleanPath, fetchOpts);
                if (timeoutId) clearTimeout(timeoutId);

                if (!res.ok) {
                    let errMsg = `HTTP ${res.status}`;
                    try {
                        const errJson = await res.json();
                        errMsg = errJson.message || errJson.hint || errJson.details || errMsg;
                    } catch (e) {
                        errMsg = `${res.status} ${res.statusText || 'Error'}`;
                    }
                    return {
                        success: false,
                        cloudConfirmed: false,
                        status: res.status,
                        error: errMsg,
                        rowsAffected: 0,
                        data: null
                    };
                }

                let data = null;
                try {
                    data = await res.json();
                } catch (e) {
                    data = null;
                }

                const rows = Array.isArray(data) ? data : (data ? [data] : []);
                const rowsAffected = rows.length;

                // Khusus PATCH & DELETE: jika respons 200/204 tapi baris kosong, berarti filter tidak cocok (0 rows)
                const isUpdateOrDelete = fetchOpts.method === 'PATCH' || fetchOpts.method === 'DELETE';
                if ((isUpdateOrDelete || fetchOpts.method === 'POST') && rowsAffected === 0 && !options.allowZeroRows) {
                    return {
                        success: false,
                        cloudConfirmed: false,
                        status: res.status,
                        rowsAffected: 0,
                        error: 'Data target tidak ditemukan di cloud (0 baris terpengaruh)',
                        data: []
                    };
                }

                return {
                    success: true,
                    cloudConfirmed: true,
                    status: res.status,
                    rowsAffected: rowsAffected,
                    data: data
                };
            } catch (err) {
                if (timeoutId) clearTimeout(timeoutId);
                const isAbort = err.name === 'AbortError';
                return {
                    success: false,
                    cloudConfirmed: false,
                    status: 0,
                    isTimeout: isAbort,
                    error: isAbort ? 'Waktu koneksi ke Supabase habis (Timeout 10s)' : (err.message || 'Kegagalan jaringan')
                };
            }
        },

        // =========================================================================
        // SMART CACHE & EGRESS BANDWIDTH OPTIMIZER (Anti-Overquota Protection)
        // =========================================================================
        _cache: {},
        _cacheTimestamps: {},
        _cacheTTL: 10 * 60 * 1000, // 10 menit default TTL (invalidasi instan saat ada realtime event)

        invalidateCache(table = null) {
            if (!table) {
                this._cache = {};
                this._cacheTimestamps = {};
            } else {
                const t = String(table).toLowerCase();
                delete this._cache[t];
                delete this._cacheTimestamps[t];

                if (['agenda_rutin', 'kegiatan_rutin_settings', 'kajian', 'pengumuman_jumat'].includes(t)) {
                    delete this._cache.settings;
                    delete this._cacheTimestamps.settings;
                }

                // Jika event berasal dari quick_petugas atau app_settings, hapus seluruh sub-cache
                if (t === 'app_settings' || t === 'quick_petugas' || t === 'settings' || t === 'all') {
                    delete this._cache['settings'];
                    delete this._cache['sholat_jumat'];
                    delete this._cache['jadwal_sholat'];
                    delete this._cache['keuangan'];
                    delete this._cache['keuangan_kas_utama'];
                    delete this._cache['keuangan_ambulance'];
                    delete this._cache['program_infaq'];
                    delete this._cache['donasi_infaq'];
                    delete this._cache['slides'];
                    delete this._cache['undangan'];
                    delete this._cache['pengumuman'];
                    delete this._cache['pengumuman_jumat'];
                    delete this._cache['ramadhan'];
                    delete this._cache['qurban'];
                    delete this._cache['kajian'];
                    delete this._cache['agenda_rutin'];
                    delete this._cache['idul_fitri'];
                    delete this._cache['idul_adha'];
                    delete this._cache['quick_petugas'];
                    delete this._cacheTimestamps['settings'];
                    delete this._cacheTimestamps['sholat_jumat'];
                    delete this._cacheTimestamps['jadwal_sholat'];
                    delete this._cacheTimestamps['keuangan'];
                    delete this._cacheTimestamps['keuangan_kas_utama'];
                    delete this._cacheTimestamps['keuangan_ambulance'];
                    delete this._cacheTimestamps['program_infaq'];
                    delete this._cacheTimestamps['donasi_infaq'];
                    delete this._cacheTimestamps['slides'];
                    delete this._cacheTimestamps['undangan'];
                    delete this._cacheTimestamps['pengumuman'];
                    delete this._cacheTimestamps['pengumuman_jumat'];
                    delete this._cacheTimestamps['ramadhan'];
                    delete this._cacheTimestamps['qurban'];
                    delete this._cacheTimestamps['kajian'];
                    delete this._cacheTimestamps['agenda_rutin'];
                    delete this._cacheTimestamps['idul_fitri'];
                    delete this._cacheTimestamps['idul_adha'];
                    delete this._cacheTimestamps['quick_petugas'];
                }
                if (t === 'keuangan' || t === 'transaksi_kas') {
                    delete this._cache['keuangan'];
                    delete this._cache['keuangan_kas_utama'];
                    delete this._cache['keuangan_ambulance'];
                    delete this._cacheTimestamps['keuangan'];
                    delete this._cacheTimestamps['keuangan_kas_utama'];
                    delete this._cacheTimestamps['keuangan_ambulance'];
                }
                if (t === 'sholat_jumat' || t === 'jumat') {
                    delete this._cache['settings'];
                    delete this._cacheTimestamps['settings'];
                    delete this._cache['sholat_jumat'];
                    delete this._cacheTimestamps['sholat_jumat'];
                }
                if (t === 'jadwal_sholat') {
                    delete this._cache['jadwal_sholat'];
                    delete this._cacheTimestamps['jadwal_sholat'];
                }
                if (t === 'program_infaq' || t === 'donasi_infaq') {
                    delete this._cache['program_infaq'];
                    delete this._cache['donasi_infaq'];
                    delete this._cacheTimestamps['program_infaq'];
                    delete this._cacheTimestamps['donasi_infaq'];
                }
                if (t === 'slides') {
                    delete this._cache['slides'];
                    delete this._cacheTimestamps['slides'];
                }
                if (t === 'undangan' || t === 'undangan_eksternal') {
                    delete this._cache['undangan'];
                    delete this._cacheTimestamps['undangan'];
                }
            }
            // Sinkronkan invalidasi ke parent jika di iframe
            try {
                if (typeof window !== 'undefined' && window.parent && window.parent !== window && window.parent.SupabaseDB && typeof window.parent.SupabaseDB.invalidateCache === 'function') {
                    window.parent.SupabaseDB.invalidateCache(table);
                }
            } catch(e) {}
        },

        getCached(key) {
            // 1. Cek jika berjalan di dalam iframe TV, pinjam cache dari parent window jika tersedia
            try {
                if (typeof window !== 'undefined' && window.parent && window.parent !== window && window.parent.SupabaseDB && typeof window.parent.SupabaseDB.getCached === 'function') {
                    const parentVal = window.parent.SupabaseDB.getCached(key);
                    if (parentVal !== null && parentVal !== undefined) return parentVal;
                }
            } catch(e) {}

            const ts = this._cacheTimestamps[key] || 0;
            if (this._cache[key] !== undefined && (Date.now() - ts) < this._cacheTTL) {
                return this._cache[key];
            }
            return null;
        },

        setCached(key, data) {
            if (data === undefined || data === null) return;
            this._cache[key] = data;
            this._cacheTimestamps[key] = Date.now();
            try {
                if (typeof window !== 'undefined' && window.parent && window.parent !== window && window.parent.SupabaseDB && typeof window.parent.SupabaseDB.setCached === 'function') {
                    window.parent.SupabaseDB.setCached(key, data);
                }
            } catch(e) {}
        },

        /**
         * Ambil pengaturan umum (app_settings) dari Supabase Cloud
         * Mengambil seluruh baris konfigurasi (row 1 + baris key-value dinamis)
         */
        async getSettings(forceRefresh = false, requireCloud = false) {
            if (forceRefresh || requireCloud) return this._readSettings(forceRefresh, requireCloud);
            if (this._settingsReadInFlight) return this._settingsReadInFlight;
            const pending = this._readSettings(false, false);
            this._settingsReadInFlight = pending;
            try { return await pending; } finally { if (this._settingsReadInFlight === pending) this._settingsReadInFlight = null; }
        },

        async _readSettings(forceRefresh = false, requireCloud = false) {
            if (!forceRefresh && !requireCloud) {
                const cached = this.getCached('settings');
                if (cached) return cached;
            }
            try {
                const client = getClient();
                let data = null;
                if (client) {
                    try {
                        const res = await client.from('app_settings')
                            .select('*')
                            .or('key.is.null,key.neq.slides_custom_data')
                            .order('id', { ascending: true });
                        if (!res.error && res.data && res.data.length > 0) {
                            data = res.data;
                        }
                    } catch (sdkFilterErr) {
                        // Fallback jika PostgREST .or() versi lama tidak didukung
                        const fallbackRes = await client.from('app_settings').select('*').order('id', { ascending: true });
                        if (!fallbackRes.error && fallbackRes.data && fallbackRes.data.length > 0) {
                            data = fallbackRes.data;
                        }
                    }
                }
                // Direct REST Fallback jika client SDK null / gagal (Filter key slides_custom_data untuk zero egress waste)
                if (!data || data.length === 0) {
                    try {
                        const restData = await this.restFetch('app_settings?or=(key.is.null,key.neq.slides_custom_data)&select=*&order=id.asc');
                        if (Array.isArray(restData) && restData.length > 0) {
                            data = restData;
                        }
                    } catch (restErr) {
                        try {
                            const unfilterData = await this.restFetch('app_settings?select=*&order=id.asc');
                            if (Array.isArray(unfilterData) && unfilterData.length > 0) {
                                data = unfilterData;
                            }
                        } catch (e) {
                            console.warn('[SupabaseDB] REST fetch app_settings:', e);
                        }
                    }
                }

                if (data && data.length > 0) {
                    // Baris utama id = 1
                    const row1 = data.find(r => r.id === 1) || data[0];
                    const settings = Object.assign({}, this.defaultSettings, row1);

                    // Gabungkan baris key-value tambahan dari Supabase (filter out slides_custom_data agar payload ringan)
                    data.forEach(r => {
                        if (r.key && r.id !== 1 && r.key !== 'slides_custom_data' && r.value !== null && r.value !== undefined) {
                            let parsedVal = r.value;
                            if (typeof r.value === 'string' && (r.value.startsWith('{') || r.value.startsWith('['))) {
                                try { parsedVal = JSON.parse(r.value); } catch(e) {}
                            }
                            settings[r.key] = parsedVal;
                        }
                    });

                    // Parse JSON fields jika bertipe string
                    if (typeof settings.rotation_pages === 'string') {
                        try { settings.rotation_pages = JSON.parse(settings.rotation_pages); } catch (e) {}
                    }
                    if (typeof settings.running_text_pages === 'string') {
                        try { settings.running_text_pages = JSON.parse(settings.running_text_pages); } catch (e) {}
                    }

                    // Normalisasi aman status prayer_mode_enabled dari kolom value row 1 atau key-value
                    if (settings.value !== undefined && settings.value !== null) {
                        settings.prayer_mode_enabled = (settings.value === '1' || settings.value === 1 || settings.value === true);
                    } else if (settings.prayer_mode_enabled !== undefined) {
                        settings.prayer_mode_enabled = (settings.prayer_mode_enabled === true || settings.prayer_mode_enabled === '1' || settings.prayer_mode_enabled === 1);
                    } else {
                        settings.prayer_mode_enabled = true;
                    }

                    // Sanitasi nama masjid & sub header agar tidak ada nilai dummy lama
                    if (!settings.nama_aplikasi || settings.nama_aplikasi.trim().toUpperCase() === 'DISPLAY MASJID' || settings.nama_aplikasi.trim().toUpperCase() === 'NAMA MASJID') {
                        settings.nama_aplikasi = 'MASJID JAMI\' AL-JIHAD';
                    }
                    if (!settings.sub_header || settings.sub_header.includes('Kebon Jeruk') || settings.sub_header.includes('Melati') || settings.sub_header.includes('Graha Asri')) {
                        settings.sub_header = 'SISTEM INFORMASI DIGITAL';
                    }

                    // Normalisasi alias durasi sholat agar kompatibel antar-klien
                    if (settings.prayer_mode_before_adzan !== undefined) settings.countdown_adzan_duration = settings.prayer_mode_before_adzan;
                    if (settings.prayer_mode_adzan_duration !== undefined) settings.adzan_duration = settings.prayer_mode_adzan_duration;
                    if (settings.prayer_mode_iqamah_duration !== undefined) settings.iqamah_duration = settings.prayer_mode_iqamah_duration;
                    if (settings.prayer_mode_after_prayer !== undefined) settings.prayer_mode_duration = settings.prayer_mode_after_prayer;
                    if (settings.prayer_mode_jumat_duration !== undefined) settings.jumat_durasi = settings.prayer_mode_jumat_duration;

                    // Simpan ke cache lokal browser untuk offline resilience
                    try {
                        localStorage.setItem('cached_app_settings', JSON.stringify(settings));
                        if (settings.rotation_interval) {
                            localStorage.setItem('display_rotation_interval', settings.rotation_interval);
                        }
                        if (settings.kajian_sabtu_data) {
                            localStorage.setItem('cached_kajian_sabtu', typeof settings.kajian_sabtu_data === 'string' ? settings.kajian_sabtu_data : JSON.stringify(settings.kajian_sabtu_data));
                        }
                        if (settings.kajian_sabtu_enabled !== undefined) {
                            localStorage.setItem('cached_kajian_sabtu_enabled', settings.kajian_sabtu_enabled);
                        }
                        if (settings.kajian_sabtu_start_time) {
                            localStorage.setItem('cached_kajian_sabtu_start_time', settings.kajian_sabtu_start_time);
                        }
                        if (settings.kegiatan_rutin_settings) {
                            localStorage.setItem('agenda_rutin_settings', typeof settings.kegiatan_rutin_settings === 'string' ? settings.kegiatan_rutin_settings : JSON.stringify(settings.kegiatan_rutin_settings));
                        }
                        if (settings.pengumuman_jumat) {
                            localStorage.setItem('pengumuman_jumat_data', typeof settings.pengumuman_jumat === 'string' ? settings.pengumuman_jumat : JSON.stringify(settings.pengumuman_jumat));
                        }
                        if (settings.rbac_users_list) {
                            localStorage.setItem('aljihad_users_list', typeof settings.rbac_users_list === 'string' ? settings.rbac_users_list : JSON.stringify(settings.rbac_users_list));
                        }
                        if (settings.cached_qris_list) {
                            localStorage.setItem('cached_qris_list', typeof settings.cached_qris_list === 'string' ? settings.cached_qris_list : JSON.stringify(settings.cached_qris_list));
                        }
                        if (settings.prayer_countdown_style) {
                            localStorage.setItem('prayer_countdown_style', settings.prayer_countdown_style);
                        }
                    } catch (e) {}

                    this.setCached('settings', settings);
                    return settings;
                }
            } catch (err) {
                console.warn('Gagal membaca app_settings dari Supabase:', err);
            }

            if (requireCloud) throw new Error('Data settings cloud belum terkonfirmasi');

            // Fallback ke localStorage atau default
            const cached = localStorage.getItem('cached_app_settings');
            let fallbackSettings = Object.assign({}, this.defaultSettings);
            if (cached) {
                try {
                    const parsed = JSON.parse(cached);
                    if (!parsed.nama_aplikasi || parsed.nama_aplikasi.trim().toUpperCase() === 'DISPLAY MASJID' || parsed.nama_aplikasi.trim().toUpperCase() === 'NAMA MASJID') {
                        parsed.nama_aplikasi = 'MASJID JAMI\' AL-JIHAD';
                    }
                    if (!parsed.sub_header || parsed.sub_header.includes('Kebon Jeruk') || parsed.sub_header.includes('Melati') || parsed.sub_header.includes('Graha Asri')) {
                        parsed.sub_header = 'SISTEM INFORMASI DIGITAL';
                    }
                    fallbackSettings = Object.assign({}, fallbackSettings, parsed);
                } catch (e) {}
            }
            const localRot = localStorage.getItem('display_rotation_interval');
            if (localRot && !isNaN(parseInt(localRot))) {
                fallbackSettings.rotation_interval = Math.max(1, parseInt(localRot));
            }
            // Lengkapi dengan local cache spesifik jika belum terisi
            try {
                if (!fallbackSettings.kajian_sabtu_data && localStorage.getItem('cached_kajian_sabtu')) {
                    fallbackSettings.kajian_sabtu_data = JSON.parse(localStorage.getItem('cached_kajian_sabtu'));
                }
                if (!fallbackSettings.kegiatan_rutin_settings && localStorage.getItem('agenda_rutin_settings')) {
                    fallbackSettings.kegiatan_rutin_settings = JSON.parse(localStorage.getItem('agenda_rutin_settings'));
                }
                if (!fallbackSettings.pengumuman_jumat && localStorage.getItem('pengumuman_jumat_data')) {
                    fallbackSettings.pengumuman_jumat = JSON.parse(localStorage.getItem('pengumuman_jumat_data'));
                }
                if (!fallbackSettings.prayer_countdown_style && localStorage.getItem('prayer_countdown_style')) {
                    fallbackSettings.prayer_countdown_style = localStorage.getItem('prayer_countdown_style');
                }
            } catch (e) {}

            this.setCached('settings', fallbackSettings);
            return fallbackSettings;
        },

        /**
         * Simpan / Perbarui satu item pengaturan key-value ke Cloud Supabase
         * @param {string} key
         * @param {any} value
         */
        async saveSettingItem(key, value) {
            if (!key) return false;
            try {
                const valStr = typeof value === 'string' ? value : JSON.stringify(value);
                const checkRes = await this.safeRestMutation(`app_settings?key=eq.${encodeURIComponent(key)}&select=id`, 'GET', null, { timeoutMs: 5000 });
                
                if (!checkRes.success || !checkRes.cloudConfirmed || !Array.isArray(checkRes.data)) return false;

                let mutationResult;
                if (checkRes.success && Array.isArray(checkRes.data) && checkRes.data.length > 0) {
                    mutationResult = await this.safeRestMutation(
                        `app_settings?key=eq.${encodeURIComponent(key)}`,
                        'PATCH',
                        { value: valStr }
                    );
                } else {
                    mutationResult = await this.safeRestMutation(
                        'app_settings',
                        'POST',
                        { key: key, value: valStr }
                    );
                }

                if (!mutationResult.success || !mutationResult.cloudConfirmed) {
                    console.warn(`[SupabaseDB] Gagal saveSettingItem (${key}):`, mutationResult.error);
                    return false;
                }
                return true;
            } catch (e) {
                console.warn(`[SupabaseDB] Gagal saveSettingItem (${key}):`, e);
                return false;
            }
        },

        parseMoney(value) {
            if (typeof value === 'string') {
                value = value.trim().replace(/^Rp\s*/i, '').replace(/\s/g, '');
                if (/^-?\d{1,3}(\.\d{3})+(,\d+)?$/.test(value)) value = value.replace(/\./g, '').replace(',', '.');
                else if (/^-?\d+,\d+$/.test(value)) value = value.replace(',', '.');
            }
            if (value === '' || value === null || value === undefined || !Number.isFinite(Number(value))) throw new Error('Nominal keuangan tidak valid');
            return Number(value);
        },

        buildKasJumatSnapshot(rows, base) {
            if (!Array.isArray(rows)) throw new Error('Data keuangan belum terkonfirmasi');
            const transactions = rows.filter(row => this.isKasUtamaMasjid(row));
            if (!transactions.length) {
                const sum = items => items.reduce((total, item) => total + this.parseMoney(item.nominal ?? item.jumlah ?? 0), 0);
                const income = Array.isArray(base.pemasukan) ? base.pemasukan : (base.rincian_pemasukan || []);
                const expense = Array.isArray(base.pengeluaran) ? base.pengeluaran : (base.rincian_pengeluaran || []);
                const masuk = Array.isArray(base.pemasukan) ? sum(income) : this.parseMoney(base.pemasukan ?? base.total_pemasukan ?? sum(income));
                const keluar = Array.isArray(base.pengeluaran) ? sum(expense) : this.parseMoney(base.pengeluaran ?? base.total_pengeluaran ?? sum(expense));
                const opening = this.parseMoney(base.saldo_awal ?? base.saldoAwal ?? 0);
                const closing = this.parseMoney(base.saldo_akhir ?? base.saldoAkhir ?? (opening + masuk - keluar));
                income.forEach(item => this.parseMoney(item.nominal ?? item.jumlah ?? 0));
                expense.forEach(item => this.parseMoney(item.nominal ?? item.jumlah ?? 0));
                return { ...base, saldo_awal: opening, saldo_akhir: closing, pemasukan: masuk, pengeluaran: keluar,
                    rincian_pemasukan: income, rincian_pengeluaran: expense };
            }
            // Current Friday reporting week; opening balance comes from earlier ledger entries.
            const now = new Date();
            const start = new Date(now.getFullYear(), now.getMonth(), now.getDate());
            const daysSinceFriday = (start.getDay() + 2) % 7;
            start.setDate(start.getDate() - daysSinceFriday);
            const dateKey = d => `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
            const from = dateKey(start), until = dateKey(now);
            let opening = 0; const income = [], expense = [];
            for (const row of transactions) {
                const masuk = this.parseMoney(row.pemasukan ?? 0), keluar = this.parseMoney(row.pengeluaran ?? 0);
                const date = String(row.tanggal || '').slice(0, 10);
                if (!/^\d{4}-\d{2}-\d{2}$/.test(date)) throw new Error('Tanggal transaksi tidak valid');
                if (date > until) continue;
                if (date < from) opening += masuk - keluar;
                else {
                    if (masuk) income.push({ ket: row.deskripsi || row.uraian || row.keterangan || 'Penerimaan', jumlah: masuk });
                    if (keluar) expense.push({ ket: row.deskripsi || row.uraian || row.keterangan || 'Pengeluaran', jumlah: keluar });
                }
            }
            const pemasukan = income.reduce((sum, row) => sum + row.jumlah, 0);
            const pengeluaran = expense.reduce((sum, row) => sum + row.jumlah, 0);
            return { ...base, saldo_awal: opening, pemasukan, pengeluaran, saldo_akhir: opening + pemasukan - pengeluaran,
                rincian_pemasukan: income, rincian_pengeluaran: expense, sumber: 'keuangan', periode_mulai: from, periode_selesai: until };
        },

        /** One PostgREST upsert = one PostgreSQL transaction. No per-key fallback. */
        async saveSettingsBatch(settingsObject) {
            let rows;
            try {
                if (!settingsObject || typeof settingsObject !== 'object' || Array.isArray(settingsObject)) return false;
                rows = Object.entries(settingsObject).map(([key, value]) => {
                    if (!key || ['__proto__', 'constructor', 'prototype'].includes(key) || value === undefined) throw new Error('Key/value tidak valid');
                    const encoded = typeof value === 'string' ? value : JSON.stringify(value, (name, item) => {
                        if (typeof item === 'number' && !Number.isFinite(item)) throw new Error('Angka settings tidak valid');
                        return item;
                    });
                    if (typeof encoded !== 'string') throw new Error('Value tidak dapat diserialisasi');
                    return { key, value: encoded };
                });
                if (!rows.length) return false;
            } catch (error) { this.lastSettingsBatchError = error.message; return false; }
            const save = async () => {
                this.lastSettingsBatchError = null;
                try {
                    const result = await this.safeRestMutation('app_settings?on_conflict=key', 'POST', rows, {
                        headers: { Prefer: 'resolution=merge-duplicates,return=representation' }
                    });
                    if (!result.success || !result.cloudConfirmed || !Array.isArray(result.data) ||
                        rows.some(row => !result.data.some(saved => saved.key === row.key && saved.value === row.value))) {
                        this.lastSettingsBatchError = result.error || 'Respons cloud tidak mengonfirmasi seluruh key';
                        return false;
                    }
                    this.invalidateCache('app_settings');
                    return true;
                } catch (error) { this.lastSettingsBatchError = error.message; return false; }
            };
            this._settingsBatchQueue = (this._settingsBatchQueue || Promise.resolve()).then(save, save);
            return this._settingsBatchQueue;
        },

        /** Canonical Friday snapshot in one settings row, including photo/time/second muezzin. */
        async saveSholatJumat(input) {
            let snapshot;
            try {
                snapshot = JSON.parse(JSON.stringify(input));
                if (!/^\d{4}-\d{2}-\d{2}$/.test(snapshot.tanggal || '') || !(snapshot.khatib || snapshot.imam || '').trim()) throw new Error('Tanggal dan Khatib/Imam wajib diisi');
                if (snapshot.waktu && !/^([01]\d|2[0-3]):[0-5]\d$/.test(snapshot.waktu)) throw new Error('Waktu Jumat tidak valid');
                snapshot.schema_version = 2;
                snapshot.updated_at = new Date().toISOString();

                const khatibVal = (snapshot.khatib || snapshot.imam || '').trim();
                const imamVal = (snapshot.imam || snapshot.khatib || '').trim();

                // 1. Mutasi ke tabel sholat_jumat di Supabase Cloud (tabel data utama TV display)
                try {
                    if (typeof this.restFetch === 'function' && typeof this.safeRestMutation === 'function') {
                        const existingRows = await this.restFetch('sholat_jumat?order=tanggal.desc,id.desc&limit=1');
                        const targetId = (Array.isArray(existingRows) && existingRows.length > 0) ? existingRows[0].id : null;
                        const jumatRowPayload = {
                            tanggal: snapshot.tanggal,
                            khatib: khatibVal,
                            imam: imamVal,
                            muadzin: (snapshot.muadzin || '').trim(),
                            bilal: snapshot.maklumat 
                                ? `${(snapshot.bilal || '').trim()} / ${(snapshot.maklumat || '').trim()}`
                                : (snapshot.bilal || '').trim(),
                            foto_imam: snapshot.foto_imam || 'img/logo-aljihad-circle.png',
                            updated_at: snapshot.updated_at
                        };

                        if (targetId) {
                            await this.safeRestMutation(`sholat_jumat?id=eq.${targetId}`, 'PATCH', jumatRowPayload, {
                                headers: { Prefer: 'return=representation' }
                            });
                        } else {
                            await this.safeRestMutation('sholat_jumat', 'POST', jumatRowPayload, {
                                headers: { Prefer: 'return=representation' }
                            });
                        }
                    }
                } catch (tblErr) {
                    console.warn('[SupabaseDB] Gagal mutasi tabel sholat_jumat:', tblErr);
                }

                // 2. Simpan durasi sholat jumat di app_settings (kolom prayer_mode_jumat_duration row 1)
                if (snapshot.durasi !== undefined && Number.isFinite(Number(snapshot.durasi)) && typeof this.safeRestMutation === 'function') {
                    try {
                        await this.safeRestMutation('app_settings?id=eq.1', 'PATCH', {
                            prayer_mode_jumat_duration: Number(snapshot.durasi)
                        });
                    } catch (durErr) {}
                }

                // 3. Simpan setting sholat_jumat di app_settings (key/value) & pengumuman_jumat jika ada
                const batch = { sholat_jumat: snapshot };
                try {
                    const currentSettings = await this.getSettings(true, true);
                    if (currentSettings && currentSettings.pengumuman_jumat) {
                        const pj = typeof currentSettings.pengumuman_jumat === 'string' ? JSON.parse(currentSettings.pengumuman_jumat) : JSON.parse(JSON.stringify(currentSettings.pengumuman_jumat));
                        pj.petugas = { ...pj.petugas, imamKhotib: snapshot.khatib === snapshot.imam ? snapshot.khatib : snapshot.khatib + ' / ' + snapshot.imam,
                            muadzin: snapshot.muadzin || '', muadzin2: snapshot.muadzin2 || '', bilal: snapshot.bilal || '', maklumat: snapshot.maklumat || '', waktu: snapshot.waktu || '11:45' };
                        batch.pengumuman_jumat = pj;
                    }
                } catch (e) {}

                if (snapshot.durasi !== undefined) {
                    if (!Number.isFinite(Number(snapshot.durasi)) || Number(snapshot.durasi) <= 0) throw new Error('Durasi Jumat tidak valid');
                    batch.prayer_mode_jumat_duration = Number(snapshot.durasi);
                }
                if (await this.saveSettingsBatch(batch) !== true) throw new Error(this.lastSettingsBatchError || 'Penyimpanan cloud belum terkonfirmasi');

                // 4. Update cache lokal & siarkan realtime ke seluruh TV display & admin
                this.cacheConfirmedSetting('sholat_jumat', snapshot, { cached_sholat_jumat: snapshot });
                if (snapshot.foto_imam) localStorage.setItem('cached_jumat_foto_imam', snapshot.foto_imam);
                if (batch.pengumuman_jumat) this.cacheConfirmedSetting('pengumuman_jumat', batch.pengumuman_jumat, { pengumuman_jumat_data: batch.pengumuman_jumat });
                if (snapshot.durasi !== undefined) this.cacheConfirmedSetting('prayer_mode_jumat_duration', Number(snapshot.durasi), { cached_prayer_mode_jumat_duration: snapshot.durasi });
                this.notifyConfirmedChange('sholat_jumat', snapshot, 'SYNC_JUMAT');
                if (typeof this.broadcastChange === 'function') {
                    this.broadcastChange('sholat_jumat', 'UPDATE', snapshot, 'Admin');
                }
                return { success: true, cloudConfirmed: true, data: snapshot };
            } catch (error) {
                console.error('[SupabaseDB] saveSholatJumat error:', error);
                return { success: false, cloudConfirmed: false, error: error.message };
            }
        },

        /** A fresh snapshot is mandatory before an edit can receive a render ACK. */
        async getSlideSnapshot(slide, fresh = false) {
            let settings = null;
            if (!fresh) {
                try { if (window.parent !== window) settings = window.parent.settings || null; } catch (error) {}
            }
            if (!settings) settings = await this.getSettings(fresh, fresh);
            const snapshot = { settings };
            if (slide === 'jumat') snapshot.jumat = await this.getSholatJumat(fresh, fresh, settings);
            if (slide === 'kas-jumat') snapshot.keuangan = fresh
                ? await this.restFetch('keuangan?select=id,tanggal,deskripsi,pemasukan,pengeluaran,saldo,kategori&order=tanggal.desc&limit=200') : await this.getKeuangan();
            if (slide === 'slide') {
                snapshot.slides = fresh ? await this.restFetch('slides?order=urutan.asc') : await this.getSlides();
                let custom = settings.slides_custom_data || await this.getSlidesCustomData(fresh);
                if (typeof custom === 'string') {
                    try { custom = JSON.parse(custom); } catch(e) {}
                }
                custom = custom || {};
                snapshot.slides = snapshot.slides.map(row => custom[row.id] && String(row.gambar || '').includes('custom_uploaded') ? { ...row, gambar: custom[row.id] } : row);
            }
            if (slide === 'undangan') {
                const stored = settings.undangan_eksternal_list || settings.undangan_eksternal;
                snapshot.undangan = stored ? (typeof stored === 'string' ? JSON.parse(stored) : stored)
                    : (fresh ? await this.restFetch('undangan_eksternal?order=urutan.asc') : await this.getUndanganEksternal());
                if (!Array.isArray(snapshot.undangan)) snapshot.undangan = [snapshot.undangan];
            }
            if (slide === 'yasin') snapshot.jadwal = await this.getJadwalSholat(fresh, fresh);
            return snapshot;
        },

        /** Reusable lifecycle. Startup readiness never acknowledges an unrelated user edit. */
        bindSlideRenderer(slide, tables, renderer) {
            let generation = 0;
            const run = async (event = null) => {
                const ticket = ++generation;
                const context = { isCurrent: () => ticket === generation };
                try {
                    const result = await renderer(!!event, context);
                    if (result !== true || !context.isCurrent()) return;
                    const visibleImages = Array.from(document.images || []).filter(img => img.getBoundingClientRect().width > 0 && img.getBoundingClientRect().height > 0);
                    await Promise.all(visibleImages.map(img => new Promise((resolve, reject) => {
                        if (img.complete && img.naturalWidth > 0) return resolve();
                        let timer;
                        const cleanup = () => { clearTimeout(timer); img.removeEventListener('load', done); img.removeEventListener('error', failed); };
                        const done = () => { cleanup(); resolve(); };
                        const failed = () => { cleanup(); reject(new Error('Gambar belum siap')); };
                        img.addEventListener('load', done, { once: true });
                        img.addEventListener('error', failed, { once: true });
                        timer = setTimeout(failed, 7000);
                    })));
                    if (typeof window.requestAnimationFrame !== 'function') return;
                    await new Promise(resolve => window.requestAnimationFrame(() => window.requestAnimationFrame(resolve)));
                    if (!context.isCurrent() || document.visibilityState === 'hidden' || window.parent === window) return;
                    window.parent.postMessage({ type: 'SLIDE_RENDER_CONFIRMED', slide, timestamp: Date.now(),
                        phase: event && event.event_id ? 'update' : 'ready', scope: 'dom',
                        ...(event && event.event_id ? { event_id: event.event_id, table: event.table } : {})
                    }, window.location.origin);
                } catch (error) { console.warn(`[${slide}] Render belum terkonfirmasi:`, error); }
            };
            this.onDataChange(event => { if (tables.includes(String(event.table || '').toLowerCase())) return run(event); });
            window.addEventListener('message', event => {
                if (event.origin !== window.location.origin || event.source !== window.parent || !event.data) return;
                const msg = event.data;
                // Correlated DATA_UPDATED is handled once through onDataChange's deduplicating queue.
                if (msg.event_id) return;
                if (msg.type === 'INIT_DISPLAY_DATA' || msg.type === 'UPDATE_SETTINGS' || /^SYNC_/.test(msg.type || '')) run();
            });
            const start = () => run();
            if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', start, { once: true });
            else start();
            return { refresh: run };
        },

        /**
         * Perbarui cache lokal hanya setelah cloud mengonfirmasi penyimpanan.
         */
        cacheConfirmedSetting(key, value, entries = {}) {
            this.invalidateCache('app_settings');
            try {
                const raw = localStorage.getItem('cached_app_settings');
                const cached = raw ? JSON.parse(raw) : {};
                cached[key] = value;
                localStorage.setItem('cached_app_settings', JSON.stringify(cached));
                Object.entries(entries).forEach(([name, val]) => {
                    localStorage.setItem(name, typeof val === 'object' ? JSON.stringify(val) : String(val));
                });
            } catch (err) {
                console.warn('[SupabaseDB] Cloud tersimpan; cache lokal tidak dapat diperbarui:', err);
            }
        },

        notifyConfirmedChange(table, data, legacyCommand) {
            try {
                if (typeof this.broadcastChange === 'function') this.broadcastChange(table, 'SYNC', data);
                else if (typeof this.sendRemoteCommand === 'function') {
                    Promise.resolve(this.sendRemoteCommand(legacyCommand, data)).catch(err => console.warn('[SupabaseDB] Broadcast tertunda:', err));
                }
            } catch (err) {
                console.warn('[SupabaseDB] Cloud tersimpan; broadcast belum terkonfirmasi:', err);
            }
        },

        /** Simpan seluruh key kajian; partial failure tidak boleh diklaim sukses. */
        async saveKajianData(payloadData, enabled = true, jamMulai = '18:25') {
            try {
                payloadData = JSON.parse(JSON.stringify(payloadData));
                if (await this.saveSettingsBatch({
                    kajian_sabtu_data: payloadData,
                    kajian_sabtu_enabled: enabled,
                    kajian_sabtu_start_time: jamMulai,
                    kajian_rotasi_pekan_detik: payloadData.rotasi_pekan_detik || 9,
                    kajian_lock_mode: payloadData.lock_mode || 'full'
                }) !== true) return false;
                this.cacheConfirmedSetting('kajian_sabtu_data', payloadData, {
                    cached_kajian_sabtu: payloadData,
                    cached_kajian_sabtu_enabled: enabled,
                    cached_kajian_sabtu_start_time: jamMulai,
                    cached_kajian_rotasi_pekan_detik: payloadData.rotasi_pekan_detik || 9,
                    cached_kajian_lock_mode: payloadData.lock_mode || 'full',
                    cached_kajian_lock_duration_minutes: payloadData.lock_duration_minutes ?? 25,
                    cached_kajian_interleave_interval: payloadData.interleave_interval ?? 10
                });
                // Preserve all confirmed settings needed by offline prayer-mode readers.
                try {
                    const cached = JSON.parse(localStorage.getItem('cached_app_settings') || '{}');
                    Object.assign(cached, { kajian_sabtu_enabled: enabled, kajian_sabtu_start_time: jamMulai });
                    localStorage.setItem('cached_app_settings', JSON.stringify(cached));
                } catch (err) { console.warn('[SupabaseDB] Cache kajian:', err); }
                this.notifyConfirmedChange('kajian', { data: payloadData, enabled, jamMulai }, 'SYNC_KAJIAN');
                return true;
            } catch (err) {
                console.warn('[SupabaseDB] Gagal saveKajianData:', err);
                return false;
            }
        },

        /** Simpan agenda berurutan per browser; false tidak pernah disiarkan sebagai sukses. */
        async saveAgendaRutin(payload) {
            let snapshot;
            try { snapshot = JSON.parse(JSON.stringify(payload)); } catch (err) { return false; }
            const save = async () => {
                try {
                    if (await this.saveSettingItem('kegiatan_rutin_settings', snapshot) !== true) return false;
                    this.cacheConfirmedSetting('kegiatan_rutin_settings', snapshot, { agenda_rutin_settings: snapshot });
                    this.notifyConfirmedChange('agenda_rutin', snapshot, 'SYNC_AGENDA_RUTIN');
                    return true;
                } catch (err) {
                    console.warn('[SupabaseDB] Gagal saveAgendaRutin:', err);
                    return false;
                }
            };
            this._agendaSaveQueue = (this._agendaSaveQueue || Promise.resolve()).then(save, save);
            return this._agendaSaveQueue;
        },

        async savePengumumanJumat(pjData) {
            try {
                if (pjData && ('saldoAwal' in pjData || 'saldo_awal' in pjData)) this.buildKasJumatSnapshot([], pjData);
                pjData = JSON.parse(JSON.stringify(pjData));
                if (pjData.petugas) {
                    const current = await this.getSholatJumat(true, true);
                    const names = String(pjData.petugas.imamKhotib || '').split('/').map(name => name.trim());
                    const friday = { ...current, khatib: names[0] || current.khatib, imam: names[1] || names[0] || current.imam,
                        muadzin: pjData.petugas.muadzin ?? current.muadzin, muadzin2: pjData.petugas.muadzin2 ?? current.muadzin2,
                        bilal: pjData.petugas.bilal ?? current.bilal, maklumat: pjData.petugas.maklumat ?? current.maklumat,
                        waktu: pjData.petugas.waktu || current.waktu || '11:45', schema_version: 2, updated_at: new Date().toISOString() };
                    if (await this.saveSettingsBatch({ pengumuman_jumat: pjData, sholat_jumat: friday }) !== true) return false;
                    this.cacheConfirmedSetting('sholat_jumat', friday, { cached_sholat_jumat: friday });
                    this.notifyConfirmedChange('sholat_jumat', friday, 'SYNC_JUMAT');
                } else if (await this.saveSettingItem('pengumuman_jumat', pjData) !== true) return false;
                this.cacheConfirmedSetting('pengumuman_jumat', pjData, { pengumuman_jumat_data: pjData });
                this.notifyConfirmedChange('pengumuman_jumat', pjData, 'SYNC_PENGUMUMAN_JUMAT');
                return true;
            } catch (err) {
                console.warn('[SupabaseDB] Gagal savePengumumanJumat:', err);
                return false;
            }
        },



        /**
         * Simpan Transaksi Kas Terpadu (Kas Utama & Ambulance - Bendahara)
         * Menyimpan langsung ke tabel SQL 'keuangan' atau 'keuangan_ambulance',
         * update cache lokal, dan trigger broadcast seketika (<50ms)
         * @param {Object} payload { tanggal, deskripsi, pemasukan, pengeluaran, saldo, kategori }
         */
        async saveTransaksiKas(payload) {
            try {
                if (!payload?.deskripsi?.trim()) throw new Error('Deskripsi wajib diisi');
                const cleanPayload = { ...payload, pemasukan: this.parseMoney(payload.pemasukan ?? 0), pengeluaran: this.parseMoney(payload.pengeluaran ?? 0) };
                if (cleanPayload.pemasukan < 0 || cleanPayload.pengeluaran < 0 || cleanPayload.pemasukan + cleanPayload.pengeluaran <= 0) throw new Error('Nominal transaksi tidak valid');
                if (!/^\d{4}-\d{2}-\d{2}$/.test(cleanPayload.tanggal || '')) throw new Error('Tanggal transaksi wajib diisi');
                const table = payload.kategori === 'Kas Ambulance' ? 'keuangan_ambulance' : 'keuangan';
                const result = await this.safeRestMutation(table, 'POST', cleanPayload);
                if (!result.success || !result.cloudConfirmed || !(result.rowsAffected > 0)) return { success: false, cloudConfirmed: false, error: result.error || 'Tidak ada baris yang dikonfirmasi' };
                this.invalidateCache(table);
                this.notifyConfirmedChange(table, result.data[0], table === 'keuangan' ? 'SYNC_KEUANGAN' : 'SYNC_AMBULANCE');
                return { success: true, cloudConfirmed: true, data: result.data[0] };
            } catch (error) { return { success: false, cloudConfirmed: false, error: error.message }; }
        },

        /**
         * Hapus Transaksi Kas Terpadu
         * @param {number|string} id
         * @param {string} table 'keuangan' | 'keuangan_ambulance'
         */
        async deleteTransaksiKas(id, table = 'keuangan') {
            if (!id) return false;
            try {
                const res = await this.safeRestMutation(`${table}?id=eq.${id}`, 'DELETE', null);
                if (res.success && res.cloudConfirmed) {
                    if (typeof this.broadcastChange === 'function') {
                        this.broadcastChange(table, 'DELETE', { id }, 'Bendahara Unified');
                    }
                    if (typeof this.sendRemoteCommand === 'function') {
                        this.sendRemoteCommand(table === 'keuangan_ambulance' ? 'SYNC_AMBULANCE' : 'SYNC_KEUANGAN', { id });
                    }
                    return true;
                }
                return false;
            } catch (err) {
                console.warn('[SupabaseDB] deleteTransaksiKas failed:', err);
                return false;
            }
        },

        /**
         * Simpan Data Qurban Terpadu (Admin & Petugas Mobile)
         * @param {Object} payload { qurban_data, qurban_sapi, qurban_kambing, qurban_donatur, qurban_jam_sembelih, qurban_lokasi_sembelih }
         */
        async saveQurbanUnified(payload) {
            if (!payload) return { success: false, cloudConfirmed: false };
            try {
                const qList = Array.isArray(payload.qurban_data) ? payload.qurban_data : [];
                const updateObj = {
                    qurban_data: qList,
                    qurban_sapi: payload.qurban_sapi ?? payload.sapi ?? 0,
                    qurban_kambing: payload.qurban_kambing ?? payload.kambing ?? 0,
                    qurban_donatur: payload.qurban_donatur ?? payload.donatur ?? 0,
                    qurban_jam_sembelih: payload.qurban_jam_sembelih || '07:30 WIB s/d Selesai',
                    qurban_lokasi_sembelih: payload.qurban_lokasi_sembelih || "Halaman Belakang Masjid Jami' Al-Jihad"
                };

                const patchRes = await this.safeRestMutation('app_settings?id=eq.1', 'PATCH', updateObj);
                const itemOk = await this.saveSettingItem('cached_qurban_data', qList);

                if (!patchRes.success || !patchRes.cloudConfirmed) {
                    return { success: false, cloudConfirmed: false, error: patchRes.error || 'Gagal menyimpan data qurban ke cloud' };
                }

                localStorage.setItem('cached_qurban_data', JSON.stringify(qList));

                if (typeof this.broadcastChange === 'function') {
                    this.broadcastChange('qurban', 'UPDATE', updateObj, 'Qurban Unified');
                }
                if (typeof this.sendRemoteCommand === 'function') {
                    this.sendRemoteCommand('SYNC_QURBAN', updateObj);
                }
                return { success: true, cloudConfirmed: true, data: updateObj };
            } catch (err) {
                console.error('[SupabaseDB] saveQurbanUnified failed:', err);
                return { success: false, cloudConfirmed: false, error: err.message };
            }
        },

        /**
         * Simpan Petugas & Tromol Ramadhan Terpadu
         * @param {Object} payload { malam_ke, imam, muadzin, kultum_penceramah, kultum_tema, bilal, infaq_tromol, ... }
         */
        async saveRamadhanUnified(payload) {
            if (!payload) return { success: false, cloudConfirmed: false };
            try {
                let currentPetugas = {};
                try {
                    const c = localStorage.getItem('cached_ramadhan_petugas');
                    if (c) currentPetugas = JSON.parse(c);
                } catch(e) {}

                const dataPetugas = Object.assign(currentPetugas, {
                    malam_ke: payload.malam_ke || currentPetugas.malam_ke || 'Malam Ramadhan',
                    imam: payload.imam || currentPetugas.imam || '-',
                    muadzin: payload.muadzin || currentPetugas.muadzin || '-',
                    kultum_penceramah: payload.kultum_penceramah || payload.kultum || currentPetugas.kultum_penceramah || '-',
                    kultum_tema: payload.kultum_tema || payload.tema || currentPetugas.kultum_tema || '',
                    bilal: payload.bilal || currentPetugas.bilal || '-'
                });

                const patchBody = { ramadhan_petugas: dataPetugas };

                // Jika ada data mutasi tromol baru yang disertakan
                if (payload.tromol_item) {
                    let infaqList = [];
                    try {
                        const raw = localStorage.getItem('cached_ramadhan_infaq');
                        if (raw) infaqList = JSON.parse(raw);
                    } catch(e) {}
                    infaqList.unshift(payload.tromol_item);
                    localStorage.setItem('cached_ramadhan_infaq', JSON.stringify(infaqList));
                    patchBody.ramadhan_infaq = infaqList;
                }

                const patchRes = await this.safeRestMutation('app_settings?id=eq.1', 'PATCH', patchBody);
                if (!patchRes.success || !patchRes.cloudConfirmed) {
                    return { success: false, cloudConfirmed: false, error: patchRes.error || 'Gagal menyimpan data ramadhan ke cloud' };
                }

                localStorage.setItem('cached_ramadhan_petugas', JSON.stringify(dataPetugas));

                if (typeof this.broadcastChange === 'function') {
                    this.broadcastChange('ramadhan', 'UPDATE', patchBody, 'Ramadhan Unified');
                }
                if (typeof this.sendRemoteCommand === 'function') {
                    this.sendRemoteCommand('SYNC_RAMADHAN', patchBody);
                }
                return { success: true, cloudConfirmed: true, data: patchBody };
            } catch (err) {
                console.error('[SupabaseDB] saveRamadhanUnified failed:', err);
                return { success: false, cloudConfirmed: false, error: err.message };
            }
        },

        /**
         * Simpan Undangan Jama'ah Terpadu (Mendukung baik Array maupun Single Object)
         * @param {Array|Object} inputData
         */
        async saveUndanganUnified(inputData) {
            if (!inputData) return { success: false };
            try {
                let list = [];
                try {
                    const c = localStorage.getItem('aljihad_undangan_eksternal') || localStorage.getItem('cached_undangan_eksternal');
                    if (c) list = JSON.parse(c);
                } catch(e) {}

                if (Array.isArray(inputData)) {
                    list = inputData;
                } else if (typeof inputData === 'object') {
                    const item = {
                        id: inputData.id || Date.now(),
                        nama_pengundang: inputData.pengundang || inputData.nama_pengundang || '',
                        penceramah: inputData.penceramah || '',
                        waktu_acara: inputData.waktu || inputData.waktu_acara || '',
                        tanggal_acara: inputData.tanggal || inputData.tanggal_acara || '',
                        nama_acara: inputData.acara || inputData.nama_acara || '',
                        tempat_acara: inputData.tempat || inputData.tempat_acara || '',
                        keterangan: inputData.keterangan || '',
                        urutan: inputData.urutan || 1,
                        is_active: inputData.is_active !== false
                    };
                    const existIdx = list.findIndex(u => String(u.id) === String(item.id));
                    if (existIdx !== -1) {
                        list[existIdx] = item;
                    } else {
                        list.unshift(item);
                    }
                    // Simpan juga versi single object untuk kompatibilitas form mobile
                    await this.saveSettingItem('undangan_eksternal', inputData);
                }

                localStorage.setItem('aljihad_undangan_eksternal', JSON.stringify(list));
                localStorage.setItem('cached_undangan_eksternal', JSON.stringify(list));

                await this.saveSettingItem('undangan_eksternal_list', list);

                // Coba simpan ke tabel jika ada
                try {
                    const client = getClient();
                    if (client) {
                        for (const u of list) {
                            await client.from('undangan_eksternal').upsert([u]).catch(() => {});
                        }
                    }
                } catch(e) {}

                if (typeof this.broadcastChange === 'function') {
                    this.broadcastChange('undangan', 'SYNC', list, 'Undangan Unified');
                }
                if (typeof this.sendRemoteCommand === 'function') {
                    this.sendRemoteCommand('SYNC_UNDANGAN', list);
                }
                return { success: true, data: list };
            } catch (err) {
                console.error('[SupabaseDB] saveUndanganUnified failed:', err);
                return { success: false, error: err.message };
            }
        },

        /**
         * Simpan Pengaturan Durasi & Jadwal Sholat Terpadu
         * @param {Object} payload { prayer_mode_enabled, countdown_adzan_duration, adzan_duration, prayer_mode_duration, prayer_mode_jumat_duration, ... }
         */
        async saveJadwalSholatUnified(payload) {
            if (!payload) return { success: false };
            try {
                const cdAdzan = parseInt(payload.prayer_mode_before_adzan || payload.countdown_adzan_duration || payload.cd_adzan || 5, 10);
                const adzanDur = parseInt(payload.prayer_mode_adzan_duration || payload.adzan_duration || payload.adzan || 3, 10);
                const iqamahDur = parseInt(payload.prayer_mode_iqamah_duration || payload.iqamah_duration || payload.iqomah || 10, 10);
                const sholatDur = parseInt(payload.prayer_mode_after_prayer || payload.prayer_mode_duration || payload.sholat || 15, 10);
                const jumatDur = parseInt(payload.prayer_mode_jumat_duration || payload.jumat_durasi || 50, 10);
                const isEnabled = payload.prayer_mode_enabled !== false;

                // 1. Kolom bersih sesuai skema tabel app_settings Supabase (bebas dari PGRST204)
                const dbPayload = {
                    prayer_mode_before_adzan: cdAdzan,
                    prayer_mode_adzan_duration: adzanDur,
                    prayer_mode_iqamah_duration: iqamahDur,
                    prayer_mode_after_prayer: sholatDur,
                    prayer_mode_jumat_duration: jumatDur
                };
                if (payload.value !== undefined) dbPayload.value = payload.value;

                // 2. Object lengkap dengan seluruh alias untuk local cache & realtime broadcast
                const fullObj = {
                    prayer_mode_enabled: isEnabled,
                    prayer_mode_before_adzan: cdAdzan,
                    countdown_adzan_duration: cdAdzan,
                    cd_adzan: cdAdzan,
                    prayer_mode_adzan_duration: adzanDur,
                    adzan_duration: adzanDur,
                    adzan: adzanDur,
                    prayer_mode_iqamah_duration: iqamahDur,
                    iqamah_duration: iqamahDur,
                    iqomah: iqamahDur,
                    prayer_mode_after_prayer: sholatDur,
                    prayer_mode_duration: sholatDur,
                    sholat: sholatDur,
                    prayer_mode_jumat_duration: jumatDur,
                    jumat_durasi: jumatDur
                };

                let currentSettings = {};
                try {
                    const c = localStorage.getItem('cached_app_settings');
                    if (c) currentSettings = JSON.parse(c);
                } catch(e) {}
                const merged = Object.assign({}, currentSettings, fullObj);
                localStorage.setItem('cached_app_settings', JSON.stringify(merged));
                localStorage.setItem('display_prayer_mode_enabled', isEnabled);
                localStorage.setItem('display_prayer_mode_jumat_duration', jumatDur);

                // 3. Kirim PATCH ke Supabase dengan hanya kolom yang valid
                const patchRes = await this.safeRestMutation('app_settings?id=eq.1', 'PATCH', dbPayload);
                if (!patchRes.success || !patchRes.cloudConfirmed) {
                    return { success: false, cloudConfirmed: false, error: patchRes.error || 'Gagal menyimpan jadwal sholat ke cloud' };
                }

                if (typeof this.broadcastChange === 'function') {
                    this.broadcastChange('app_settings', 'UPDATE', fullObj, 'Jadwal Sholat Unified');
                }
                if (typeof this.sendRemoteCommand === 'function') {
                    this.sendRemoteCommand('SYNC_JADWAL_SHOLAT', fullObj);
                    this.sendRemoteCommand('UPDATE_SETTINGS', fullObj);
                }
                return { success: true, cloudConfirmed: true, data: fullObj };
            } catch (err) {
                console.error('[SupabaseDB] saveJadwalSholatUnified failed:', err);
                return { success: false, cloudConfirmed: false, error: err.message };
            }
        },

        /**
         * Simpan / Perbarui app_settings ke Cloud Supabase (id=1 dan key-value dinamis)
         * Mendukung pembaruan sebagian (partial update) dan pembaruan menyeluruh
         * @param {Object} updatedFields
         */
        async saveSettings(updatedFields) {
            if (!updatedFields || typeof updatedFields !== 'object') {
                return { success: false, cloudConfirmed: false, error: 'Payload tidak valid' };
            }

            // Draft and confirmed state must remain separate until the cloud replies.
            const snapshot = JSON.parse(JSON.stringify(updatedFields));
            updatedFields = snapshot;
            try { localStorage.setItem('draft_app_settings', JSON.stringify(snapshot)); } catch (error) {}

            // 2. Pisahkan field kolom row 1 vs key-value baris tambahan
            const validRow1Columns = [
                'id', 'key', 'value', 'nama_aplikasi', 'footer', 'live_makkah_url', 'live_madinah_url', 'cctv_mimbar_url',
                'gemini_api_key', 'gemini_model', 'rotation_interval', 'prayer_mode_before_adzan',
                'prayer_mode_adzan_duration', 'prayer_mode_iqamah_duration', 'prayer_mode_after_prayer',
                'prayer_mode_jumat_duration', 'audio_tarhim', 'tarhim_trigger_seconds', 'yasin_mode_enabled',
                'yasin_start_time', 'yasin_scroll_speed', 'auto_switch_views', 'rotation_pages', 'running_text_pages',
                'enable_dynamic_theme', 'enable_next_prayer_bar', 'cctv_mimbar_enabled', 'cctv_auto_switch_khutbah',
                'running_text', 'daily_hikmah_cache', 'daily_hikmah_date'
            ];

            const row1Payload = {};
            const keyValuePayloads = {};

            for (const [k, v] of Object.entries(updatedFields)) {
                if (validRow1Columns.includes(k)) {
                    row1Payload[k] = v;
                } else {
                    keyValuePayloads[k] = v;
                }
            }

            if (updatedFields.prayer_mode_enabled !== undefined) {
                row1Payload.value = updatedFields.prayer_mode_enabled ? '1' : '0';
            }
            if (updatedFields.prayer_mode_duration !== undefined) {
                row1Payload.prayer_mode_after_prayer = updatedFields.prayer_mode_duration;
            }

            // Proteksi Skema SQL: Kolom yasin_scroll_speed di PostgreSQL adalah varchar(20).
            // Format JSON panjang (>20 char) wajib dikonversi menjadi format ringkas (mode:step:mushaf) agar tidak ditolak dengan error 22001.
            if (row1Payload.yasin_scroll_speed) {
                if (typeof row1Payload.yasin_scroll_speed === 'object') {
                    const m = row1Payload.yasin_scroll_speed.mode || 'step';
                    const s = row1Payload.yasin_scroll_speed.step_duration || 20;
                    const h = row1Payload.yasin_scroll_speed.mushaf_duration || 120;
                    row1Payload.yasin_scroll_speed = `${m}:${s}:${h}`.substring(0, 20);
                } else if (typeof row1Payload.yasin_scroll_speed === 'string' && row1Payload.yasin_scroll_speed.length > 20) {
                    try {
                        const parsed = JSON.parse(row1Payload.yasin_scroll_speed);
                        const m = parsed.mode || 'step';
                        const s = parsed.step_duration || 20;
                        const h = parsed.mushaf_duration || 120;
                        row1Payload.yasin_scroll_speed = `${m}:${s}:${h}`.substring(0, 20);
                    } catch (e) {
                        row1Payload.yasin_scroll_speed = row1Payload.yasin_scroll_speed.substring(0, 20);
                    }
                }
            }

            // 3. Kirim update ke Cloud Supabase
            try {
                if (SUPABASE_CONFIG && SUPABASE_CONFIG.url) {
                    let hasError = false;
                    let lastErrMsg = '';

                    // Update row 1 jika ada field row 1
                    if (Object.keys(row1Payload).length > 0) {
                        const patchRes = await this.safeRestMutation('app_settings?id=eq.1', 'PATCH', row1Payload);
                        if (!patchRes.success || !patchRes.cloudConfirmed) {
                            hasError = true;
                            lastErrMsg = `Gagal update app_settings id=1: ${patchRes.error}`;
                            console.warn('[SupabaseDB]', lastErrMsg);
                        }
                    }

                    if (Object.keys(keyValuePayloads).length && await this.saveSettingsBatch(keyValuePayloads) !== true) {
                        hasError = true;
                        lastErrMsg = this.lastSettingsBatchError || 'Batch settings belum terkonfirmasi';
                    }

                    if (hasError) {
                        return { success: false, cloudConfirmed: false, error: lastErrMsg };
                    }

                    try {
                        const confirmed = JSON.parse(localStorage.getItem('cached_app_settings') || '{}');
                        Object.assign(confirmed, updatedFields);
                        localStorage.setItem('cached_app_settings', JSON.stringify(confirmed));
                        if (localStorage.getItem('draft_app_settings') === JSON.stringify(snapshot)) localStorage.removeItem('draft_app_settings');
                    } catch (error) {}
                    this.invalidateCache('app_settings');

                    // Kirim sinyal broadcast remote UPDATE_SETTINGS agar display TV langsung adopsi tanpa jeda
                    if (typeof this.broadcastChange === 'function') {
                        this.broadcastChange('app_settings', 'UPDATE', updatedFields);
                    } else if (typeof this.sendRemoteCommand === 'function') {
                        this.sendRemoteCommand('UPDATE_SETTINGS', updatedFields).catch(() => {});
                    }

                    console.log('✅ [SupabaseDB] Sukses sinkronisasi settings ke cloud:', updatedFields);
                    return { success: true, cloudConfirmed: true, data: updatedFields };
                }
            } catch (err) {
                console.warn('⚠️ [SupabaseDB] Jaringan error saat saveSettings:', err);
                return { success: false, cloudConfirmed: false, error: err.message };
            }

            return { success: false, cloudConfirmed: false, error: 'Koneksi ke server tidak tersedia' };
        },

        /**
         * Ambil jadwal sholat dari tabel jadwal_sholat
         */
        async getJadwalSholat(forceRefresh = false, requireCloud = false) {
            if (!forceRefresh && !requireCloud) {
                const cached = this.getCached('jadwal_sholat');
                if (cached) return cached;
            }
            try {
                const client = getClient();
                if (client) {
                    const { data, error } = await client.from('jadwal_sholat').select('*').order('id', { ascending: true });
                    if (!error && data && data.length > 0) {
                        localStorage.setItem('cached_jadwal_sholat', JSON.stringify(data));
                        this.setCached('jadwal_sholat', data);
                        return data;
                    }
                }
            } catch (err) {
                console.warn('Gagal membaca jadwal_sholat dari Supabase SDK:', err);
            }
            // Direct REST API Fallback
            try {
                const rows = await this.restFetch('jadwal_sholat?order=id.asc');
                if (Array.isArray(rows) && rows.length > 0) {
                    localStorage.setItem('cached_jadwal_sholat', JSON.stringify(rows));
                    this.setCached('jadwal_sholat', rows);
                    return rows;
                }
            } catch (restErr) {
                console.warn('REST fallback jadwal_sholat:', restErr);
            }

            if (requireCloud) throw new Error('Jadwal cloud belum terkonfirmasi');

            const cached = localStorage.getItem('cached_jadwal_sholat');
            if (cached) {
                try {
                    const parsed = JSON.parse(cached);
                    this.setCached('jadwal_sholat', parsed);
                    return parsed;
                } catch (e) {}
            }
            return [];
        },

        /**
         * Ambil daftar pengumuman
         */
        async getPengumuman(forceRefresh = false) {
            if (!forceRefresh) {
                const cached = this.getCached('pengumuman');
                if (cached) return cached;
            }
            try {
                const client = getClient();
                if (client) {
                    const { data, error } = await client.from('pengumuman').select('*').order('id', { ascending: false });
                    if (!error && data) {
                        localStorage.setItem('cached_pengumuman', JSON.stringify(data));
                        this.setCached('pengumuman', data);
                        return data;
                    }
                }
            } catch (err) {
                console.warn('Gagal membaca pengumuman SDK:', err);
            }
            // Direct REST API Fallback
            try {
                const rows = await this.restFetch('pengumuman?order=id.desc');
                if (Array.isArray(rows)) {
                    localStorage.setItem('cached_pengumuman', JSON.stringify(rows));
                    this.setCached('pengumuman', rows);
                    return rows;
                }
            } catch (restErr) {
                console.warn('REST fallback pengumuman:', restErr);
            }
            const cached = localStorage.getItem('cached_pengumuman');
            const resPengumuman = cached ? JSON.parse(cached) : [];
            this.setCached('pengumuman', resPengumuman);
            return resPengumuman;
        },

        /**
         * Ambil petugas sholat Jumat terbaru (Normalized & Unified)
         * Mengembalikan objek data terstandarisasi dengan pemisahan bilal & maklumat yang bersih
         */
        async getSholatJumat(forceRefresh = false, requireCloud = false, knownSettings = null) {
            const settings = knownSettings || await this.getSettings(forceRefresh, requireCloud);
            let canonical = settings && settings.sholat_jumat;
            if (typeof canonical === 'string') { try { canonical = JSON.parse(canonical); } catch (error) { canonical = null; } }
            if (canonical && canonical.schema_version === 2) {
                try {
                    localStorage.setItem('cached_sholat_jumat', JSON.stringify(canonical));
                    if (canonical.foto_imam) localStorage.setItem('cached_jumat_foto_imam', canonical.foto_imam);
                } catch (error) {}
                this.setCached('sholat_jumat', canonical);
                return canonical;
            }
            if (!forceRefresh) {
                const cached = this.getCached('sholat_jumat');
                if (cached) return cached;
            }
            let row = null;
            try {
                const client = getClient();
                if (client) {
                    const { data, error } = await client.from('sholat_jumat').select('*').order('tanggal', { ascending: false }).order('id', { ascending: false }).limit(1);
                    if (!error && data && data.length > 0) {
                        row = data[0];
                    }
                }
            } catch (err) {
                console.warn('Gagal membaca sholat_jumat SDK:', err);
            }

            // Direct REST API Fallback (Bypass SDK jika CDN belum siap / offline)
            if (!row) {
                try {
                    const rows = await this.restFetch('sholat_jumat?order=tanggal.desc,id.desc&limit=1');
                    if (Array.isArray(rows) && rows.length > 0) {
                        row = rows[0];
                    }
                    else if (Array.isArray(rows) && requireCloud) return {};
                } catch (restErr) {
                    console.warn('Gagal membaca sholat_jumat via REST:', restErr);
                }
            }

            if (!row && requireCloud) throw new Error('Petugas Jumat cloud belum terkonfirmasi');

            if (!row) {
                const cached = localStorage.getItem('cached_sholat_jumat');
                if (cached) {
                    try { row = JSON.parse(cached); } catch(e) {}
                }
            }

            if (!row) return null;

            // Normalisasi terpadu: Ekstrak bilal dan maklumat secara bersih
            const rawBilal = (row.bilal || '').trim();
            let bilalClean = rawBilal;
            let maklumatClean = (row.maklumat || '').trim();

            if (rawBilal.includes('/')) {
                const bParts = rawBilal.split('/').map(s => s.trim());
                bilalClean = bParts[0] || '';
                if (bParts[1]) maklumatClean = bParts[1];
            }

            // Fallback maklumat dari settings running_text_pages jika belum terisi
            if (!maklumatClean) {
                try {
                    const cachedRt = localStorage.getItem('cached_running_text_pages');
                    if (cachedRt) {
                        const rt = JSON.parse(cachedRt);
                        if (rt.jumat_maklumat) maklumatClean = rt.jumat_maklumat;
                    }
                } catch (e) {}
            }

            let resolvedFoto = row.foto_imam || 'image/display/default_imam.jpg';
            if (!resolvedFoto || resolvedFoto === 'custom_uploaded') {
                try {
                    const cachedRt = localStorage.getItem('cached_running_text_pages');
                    if (cachedRt) {
                        const rt = JSON.parse(cachedRt);
                        if (rt.jumat_foto_imam_b64) resolvedFoto = rt.jumat_foto_imam_b64;
                    }
                } catch(e) {}
            }

            const normalized = Object.assign({}, row, {
                raw_bilal: rawBilal,
                bilal: bilalClean,
                maklumat: maklumatClean,
                foto_imam: resolvedFoto
            });

            try { localStorage.setItem('cached_sholat_jumat', JSON.stringify(normalized)); } catch (e) {}
            this.setCached('sholat_jumat', normalized);
            return normalized;
        },

        /**
         * Ambil data transaksi keuangan kas masjid
         */
        async getKeuangan(forceRefresh = false) {
            if (!forceRefresh) {
                const cached = this.getCached('keuangan');
                if (cached) return cached;
            }
            try {
                const client = getClient();
                if (client) {
                    const { data, error } = await client.from('keuangan')
                        .select('id,tanggal,deskripsi,pemasukan,pengeluaran,saldo,kategori')
                        .order('tanggal', { ascending: false })
                        .limit(200);
                    if (!error && data) {
                        localStorage.setItem('cached_keuangan', JSON.stringify(data));
                        this.setCached('keuangan', data);
                        return data;
                    }
                }
            } catch (err) {
                console.warn('Gagal membaca keuangan SDK:', err);
            }
            // Direct REST API Fallback
            try {
                const rows = await this.restFetch('keuangan?select=id,tanggal,deskripsi,pemasukan,pengeluaran,saldo,kategori&order=tanggal.desc&limit=200');
                if (Array.isArray(rows)) {
                    localStorage.setItem('cached_keuangan', JSON.stringify(rows));
                    this.setCached('keuangan', rows);
                    return rows;
                }
            } catch (restErr) {
                console.warn('REST fallback keuangan:', restErr);
            }
            const cached = localStorage.getItem('cached_keuangan');
            const resKeuangan = cached ? JSON.parse(cached) : [];
            this.setCached('keuangan', resKeuangan);
            return resKeuangan;
        },

        /**
         * Validasi apakah transaksi merupakan Kas Utama Masjid murni
         */
        isKasUtamaMasjid(item) {
            if (!item) return false;
            const kat = (item.kategori || '').toLowerCase().trim();
            const bukanKasUtama = [
                'penggalangan',
                'program infaq',
                'infaq program',
                'infaq renovasi',
                'renovasi',
                'ambulance',
                'ambulans',
                'qurban',
                'ramadhan',
                'donasi infaq',
                'donatur'
            ];
            for (const pos of bukanKasUtama) {
                if (kat.includes(pos)) return false;
            }
            return true;
        },

        /**
         * Ambil transaksi murni Kas Utama Masjid (Mengecualikan program penggalangan, infaq program, ambulans, dll)
         */
        async getKeuanganKasUtama(forceRefresh = false) {
            if (!forceRefresh) {
                const cached = this.getCached('keuangan_kas_utama');
                if (cached) return cached;
            }
            const all = await this.getKeuangan(forceRefresh);
            const filtered = (all || []).filter(item => this.isKasUtamaMasjid(item));
            this.setCached('keuangan_kas_utama', filtered);
            return filtered;
        },

        /**
         * Ambil data transaksi kas mobil ambulance
         */
        async getKeuanganAmbulance(forceRefresh = false) {
            if (!forceRefresh) {
                const cached = this.getCached('keuangan_ambulance');
                if (cached) return cached;
            }
            try {
                const client = getClient();
                if (client) {
                    const { data, error } = await client.from('keuangan_ambulance')
                        .select('id,tanggal,deskripsi,pemasukan,pengeluaran,saldo,kategori')
                        .order('tanggal', { ascending: false })
                        .limit(200);
                    if (!error && data) {
                        localStorage.setItem('cached_keuangan_ambulance', JSON.stringify(data));
                        this.setCached('keuangan_ambulance', data);
                        return data;
                    }
                }
            } catch (err) {
                console.warn('Gagal membaca keuangan_ambulance:', err);
            }
            // Direct REST Fallback
            try {
                const rows = await this.restFetch('keuangan_ambulance?select=id,tanggal,deskripsi,pemasukan,pengeluaran,saldo,kategori&order=tanggal.desc&limit=200');
                if (Array.isArray(rows)) {
                    localStorage.setItem('cached_keuangan_ambulance', JSON.stringify(rows));
                    this.setCached('keuangan_ambulance', rows);
                    return rows;
                }
            } catch (restErr) {}
            const cached = localStorage.getItem('cached_keuangan_ambulance');
            const resAmb = cached ? JSON.parse(cached) : [];
            this.setCached('keuangan_ambulance', resAmb);
            return resAmb;
        },

        /**
         * Ambil program infaq & target donasi
         */
        async getProgramInfaq(forceRefresh = false) {
            if (!forceRefresh) {
                const cached = this.getCached('program_infaq');
                if (cached) return cached;
            }
            try {
                const client = getClient();
                if (client) {
                    const { data, error } = await client.from('program_infaq').select('*').order('id', { ascending: false });
                    if (!error && data) {
                        localStorage.setItem('cached_program_infaq', JSON.stringify(data));
                        this.setCached('program_infaq', data);
                        return data;
                    }
                }
            } catch (err) {
                console.warn('Gagal membaca program_infaq SDK:', err);
            }
            // Direct REST API Fallback
            try {
                const rows = await this.restFetch('program_infaq?order=id.desc');
                if (Array.isArray(rows) && rows.length > 0) {
                    localStorage.setItem('cached_program_infaq', JSON.stringify(rows));
                    this.setCached('program_infaq', rows);
                    return rows;
                }
            } catch (restErr) {
                console.warn('REST fallback program_infaq:', restErr);
            }
            const cached = localStorage.getItem('cached_program_infaq');
            const resProg = cached ? JSON.parse(cached) : [];
            this.setCached('program_infaq', resProg);
            return resProg;
        },

        /**
         * Ambil daftar donasi infaq per program atau keseluruhan
         */
        async getDonasiInfaq(programId = null, forceRefresh = false) {
            const cKey = programId ? ('donasi_infaq_' + programId) : 'donasi_infaq_all';
            if (!forceRefresh) {
                const cached = this.getCached(cKey);
                if (cached) return cached;
            }
            try {
                const client = getClient();
                if (client) {
                    let query = client.from('donasi_infaq').select('*').order('tanggal', { ascending: true }).order('id', { ascending: true });
                    if (programId) query = query.eq('program_infaq_id', programId);
                    const { data, error } = await query;
                    if (!error && data) {
                        this.setCached(cKey, data);
                        return data;
                    }
                }
            } catch (err) {
                console.warn('Gagal membaca donasi_infaq SDK:', err);
            }
            // Direct REST API Fallback
            try {
                const query = programId ? `donasi_infaq?program_infaq_id=eq.${programId}&order=tanggal.asc,id.asc` : 'donasi_infaq?order=tanggal.asc,id.asc';
                const rows = await this.restFetch(query);
                if (Array.isArray(rows) && rows.length > 0) {
                    this.setCached(cKey, rows);
                    return rows;
                }
            } catch (restErr) {
                console.warn('REST fallback donasi_infaq:', restErr);
            }
            const cached = localStorage.getItem('cached_donasi_infaq');
            const allDonasi = cached ? JSON.parse(cached) : [];
            const filtered = programId ? allDonasi.filter(d => d.program_infaq_id == programId) : allDonasi;
            return filtered.sort((a, b) => {
                const dateA = new Date(a.tanggal || 0).getTime();
                const dateB = new Date(b.tanggal || 0).getTime();
                if (dateA !== dateB) return dateA - dateB;
                return (parseInt(a.id) || 0) - (parseInt(b.id) || 0);
            });
        },

        /**
         * Ambil jadwal Sholat Idul Fitri terbaru
         */
        async getIdulFitri(forceRefresh = false) {
            if (!forceRefresh) {
                const cached = this.getCached('idul_fitri');
                if (cached) return cached;
            }
            try {
                const client = getClient();
                if (client) {
                    const { data, error } = await client.from('sholat_idul_fitri').select('*').order('id', { ascending: false }).limit(1);
                    if (!error && data && data.length > 0) {
                        localStorage.setItem('cached_idul_fitri', JSON.stringify(data[0]));
                        this.setCached('idul_fitri', data[0]);
                        return data[0];
                    }
                }
            } catch (err) {
                console.warn('Gagal membaca sholat_idul_fitri SDK:', err);
            }
            // Direct REST API Fallback
            try {
                const rows = await this.restFetch('sholat_idul_fitri?order=id.desc&limit=1');
                if (Array.isArray(rows) && rows.length > 0) {
                    localStorage.setItem('cached_idul_fitri', JSON.stringify(rows[0]));
                    this.setCached('idul_fitri', rows[0]);
                    return rows[0];
                }
            } catch (restErr) {
                console.warn('REST fallback idul_fitri:', restErr);
            }
            const cached = localStorage.getItem('cached_idul_fitri');
            const resFitri = cached ? JSON.parse(cached) : null;
            if (resFitri) this.setCached('idul_fitri', resFitri);
            return resFitri;
        },

        /**
         * Ambil jadwal Sholat Idul Adha terbaru
         */
        async getIdulAdha(forceRefresh = false) {
            if (!forceRefresh) {
                const cached = this.getCached('idul_adha');
                if (cached) return cached;
            }
            try {
                const client = getClient();
                if (client) {
                    const { data, error } = await client.from('sholat_idul_adha').select('*').order('id', { ascending: false }).limit(1);
                    if (!error && data && data.length > 0) {
                        localStorage.setItem('cached_idul_adha', JSON.stringify(data[0]));
                        this.setCached('idul_adha', data[0]);
                        return data[0];
                    }
                }
            } catch (err) {
                console.warn('Gagal membaca sholat_idul_adha SDK:', err);
            }
            // Direct REST API Fallback
            try {
                const rows = await this.restFetch('sholat_idul_adha?order=id.desc&limit=1');
                if (Array.isArray(rows) && rows.length > 0) {
                    localStorage.setItem('cached_idul_adha', JSON.stringify(rows[0]));
                    this.setCached('idul_adha', rows[0]);
                    return rows[0];
                }
            } catch (restErr) {
                console.warn('REST fallback idul_adha:', restErr);
            }
            const cached = localStorage.getItem('cached_idul_adha');
            const resAdha = cached ? JSON.parse(cached) : null;
            if (resAdha) this.setCached('idul_adha', resAdha);
            return resAdha;
        },

        /**
         * Ambil daftar galeri informasi masjid
         */
        async getSlides(forceRefresh = false) {
            if (!forceRefresh) {
                const cached = this.getCached('slides');
                if (cached) return cached;
            }

            // Helper merge custom base64 image agar poster kustom selalu muncul di TV
            const mergeCustomSlideImages = (list) => {
                if (!Array.isArray(list)) return list;
                let b64Map = {};
                try {
                    const raw = localStorage.getItem('cached_slides_custom_data');
                    if (raw) b64Map = JSON.parse(raw);
                } catch(e) {}
                return list.map(s => {
                    if ((s.gambar === 'image/slides/custom_uploaded.png' || s.gambar === '../image/slides/custom_uploaded.png') && b64Map[s.id]) {
                        return Object.assign({}, s, { gambar: b64Map[s.id], gambar_base64: b64Map[s.id] });
                    }
                    return s;
                });
            };

            // 1. Coba baca dari cloud database Supabase
            try {
                const client = getClient();
                if (client) {
                    const { data, error } = await client.from('slides').select('*').order('urutan', { ascending: true });
                    if (!error && Array.isArray(data) && data.length > 0) {
                        const mergedData = mergeCustomSlideImages(data);
                        try {
                            localStorage.setItem('aljihad_galeri_informasi', JSON.stringify(mergedData));
                            localStorage.setItem('cached_slides', JSON.stringify(mergedData));
                        } catch (e) {}
                        this.setCached('slides', mergedData);
                        return mergedData;
                    }
                }
            } catch (err) {
                console.warn('Gagal membaca slides dari Supabase SDK:', err);
            }

            // 2. Direct REST API Fallback
            try {
                const rows = await this.restFetch('slides?order=urutan.asc');
                if (Array.isArray(rows) && rows.length > 0) {
                    const mergedRows = mergeCustomSlideImages(rows);
                    try {
                        localStorage.setItem('aljihad_galeri_informasi', JSON.stringify(mergedRows));
                        localStorage.setItem('cached_slides', JSON.stringify(mergedRows));
                    } catch (e) {}
                    this.setCached('slides', mergedRows);
                    return mergedRows;
                }
            } catch (restErr) {
                console.warn('REST fallback slides:', restErr);
            }

            // 3. Coba baca dari localStorage lokal
            try {
                const local = localStorage.getItem('aljihad_galeri_informasi');
                if (local) {
                    const parsed = JSON.parse(local);
                    if (Array.isArray(parsed) && parsed.length > 0) {
                        const mergedLocal = mergeCustomSlideImages(parsed);
                        this.setCached('slides', mergedLocal);
                        return mergedLocal;
                    }
                }
            } catch (e) {}

            // 4. Default fallback 3 arsip dokumentasi & sertifikasi resmi Masjid Al-Jihad
            const defaultSlides = [
                {
                    id: 1,
                    judul: 'Arah Qiblat',
                    kategori: 'Pengukuran & Validasi',
                    deskripsi: 'Hasil pengecekkan arah qiblat pada hari Kamis, 16 Juli 2026 Jam: 16:27 WIB di Masjid Jami\' Al-Jihad.',
                    gambar: 'image/slides/custom_uploaded.png',
                    urutan: 1,
                    durasi: 10,
                    aktif: true
                },
                {
                    id: 2,
                    judul: 'Qiblat Sertifikat',
                    kategori: 'Sertifikasi Resmi',
                    deskripsi: 'Sertifikasi Gerakan Nasional 1.148K Rasdhul Qiblat Kementerian Agama Republik Indonesia.',
                    gambar: 'image/slides/GkxyYVJO2IdZoU1X6mgSNUcghs0gu1HqVtYlxgYA.png',
                    urutan: 2,
                    durasi: 10,
                    aktif: true
                },
                {
                    id: 3,
                    judul: 'Sistem Informasi Masjid KEMENAG (SIMAS)',
                    kategori: 'Legalitas & Perizinan',
                    deskripsi: 'Surat Tanda Daftar Masjid Jami\' Al Jihad terdaftar resmi di Kementerian Agama (KEMENAG) RI.',
                    gambar: 'image/slides/E6Vbbx4rwXUpvmdD8LodQkbK9x82dYzX723Fb386.webp',
                    urutan: 3,
                    durasi: 10,
                    aktif: true
                }
            ];

            const mergedDefaults = mergeCustomSlideImages(defaultSlides);
            try {
                localStorage.setItem('aljihad_galeri_informasi', JSON.stringify(mergedDefaults));
            } catch (e) {}

            this.setCached('slides', mergedDefaults);
            return mergedDefaults;
        },

        /**
         * Mengambil data gambar custom slide terisolasi secara on-demand (mencegah pembengkakan kuota egress di getSettings umum)
         */
        async getSlidesCustomData(forceRefresh = false) {
            if (!forceRefresh) {
                const cachedMem = this.getCached('slides_custom_data');
                if (cachedMem) return cachedMem;
                try {
                    const localRaw = localStorage.getItem('cached_slides_custom_data');
                    if (localRaw) {
                        const parsed = JSON.parse(localRaw);
                        if (parsed && typeof parsed === 'object') {
                            this.setCached('slides_custom_data', parsed);
                            return parsed;
                        }
                    }
                } catch(e) {}
            }
            try {
                const client = getClient();
                let val = null;
                if (client) {
                    const { data, error } = await client.from('app_settings').select('value').eq('key', 'slides_custom_data').limit(1);
                    if (!error && data && data.length > 0) {
                        val = data[0].value;
                    }
                }
                if (!val) {
                    try {
                        const rows = await this.restFetch('app_settings?key=eq.slides_custom_data&select=value&limit=1');
                        if (Array.isArray(rows) && rows.length > 0 && rows[0].value) {
                            val = rows[0].value;
                        }
                    } catch (e) {}
                }
                if (val) {
                    const parsed = typeof val === 'string' ? JSON.parse(val) : val;
                    try { localStorage.setItem('cached_slides_custom_data', JSON.stringify(parsed)); } catch(e) {}
                    this.setCached('slides_custom_data', parsed);
                    return parsed;
                }
            } catch(e) {
                console.warn('[SupabaseDB] getSlidesCustomData:', e);
            }
            return {};
        },

        /**
         * Mengambil data QRIS donasi digital dari cloud Supabase / cache lokal
         */
        async getQrisList(forceRefresh = false) {
            if (!forceRefresh) {
                const cachedMem = this.getCached('cached_qris_list');
                if (cachedMem && Array.isArray(cachedMem) && cachedMem.length > 0) return cachedMem;
                try {
                    const localRaw = localStorage.getItem('cached_qris_list');
                    if (localRaw) {
                        const parsed = JSON.parse(localRaw);
                        if (Array.isArray(parsed) && parsed.length > 0) {
                            this.setCached('cached_qris_list', parsed);
                            return parsed;
                        }
                    }
                } catch(e) {}
            }
            try {
                const client = getClient();
                let val = null;
                if (client) {
                    const { data, error } = await client.from('app_settings').select('value').eq('key', 'cached_qris_list').limit(1);
                    if (!error && data && data.length > 0) val = data[0].value;
                }
                if (!val) {
                    const rows = await this.restFetch('app_settings?key=eq.cached_qris_list&select=value&limit=1');
                    if (Array.isArray(rows) && rows.length > 0 && rows[0].value) val = rows[0].value;
                }
                if (val) {
                    const parsed = typeof val === 'string' ? JSON.parse(val) : val;
                    if (Array.isArray(parsed) && parsed.length > 0) {
                        try { localStorage.setItem('cached_qris_list', JSON.stringify(parsed)); } catch(e) {}
                        this.setCached('cached_qris_list', parsed);
                        return parsed;
                    }
                }
            } catch(e) {
                console.warn('[SupabaseDB] getQrisList:', e);
            }
            try {
                const local = localStorage.getItem('cached_qris_list');
                if (local) {
                    const parsed = JSON.parse(local);
                    if (Array.isArray(parsed) && parsed.length > 0) return parsed;
                }
            } catch(e) {}
            return [];
        },

        /**
         * Simpan seluruh daftar galeri informasi ke localStorage & Cloud Supabase
         */
        async saveSlides(slidesList) {
            try {
                localStorage.setItem('aljihad_galeri_informasi', JSON.stringify(slidesList));
                localStorage.setItem('cached_slides', JSON.stringify(slidesList));
            } catch (e) {}

            try {
                const client = getClient();
                if (client && SUPABASE_CONFIG.url) {
                    console.log('📡 [SupabaseDB] Menyimpan galeri informasi ke cloud Supabase...');
                    const customBase64Map = {};
                    const cleanSlides = slidesList.map(s => {
                        let imgVal = s.gambar || '';
                        let b64Val = s.gambar_base64 || null;

                        // Jika imgVal adalah string data URI base64 atau melebihi 250 karakter
                        if (imgVal.startsWith('data:image/') || imgVal.length > 250) {
                            b64Val = imgVal;
                            imgVal = 'image/slides/custom_uploaded.png';
                        }
                        if (b64Val) {
                            customBase64Map[s.id] = b64Val;
                        }

                        // HANYA kirim field yang ada di skema SQL tabel slides (Bebas dari PGRST204)
                        return {
                            id: s.id,
                            judul: s.judul || '',
                            deskripsi: s.deskripsi || '',
                            gambar: imgVal.substring(0, 250),
                            urutan: parseInt(s.urutan, 10) || 1,
                            durasi: parseInt(s.durasi, 10) || 10,
                            aktif: s.aktif !== false,
                            updated_at: new Date().toISOString()
                        };
                    });

                    // Simpan map foto base64 ke app_settings agar persist ke TV
                    if (Object.keys(customBase64Map).length > 0) {
                        try {
                            localStorage.setItem('cached_slides_custom_data', JSON.stringify(customBase64Map));
                            await this.saveSettingItem('slides_custom_data', customBase64Map).catch(() => {});
                        } catch(e) {}
                    }

                    // Upsert seluruh baris slide ke tabel slides
                    const { error: upsertErr } = await client.from('slides').upsert(cleanSlides, { onConflict: 'id' });
                    if (upsertErr) {
                        console.warn('Gagal upsert slides ke Supabase:', upsertErr);
                    } else {
                        console.log('✅ [SupabaseDB] Sukses menyinkronkan slides ke cloud!');
                    }

                    // Hapus slide yang tidak ada lagi dalam daftar
                    const activeIds = cleanSlides.map(s => s.id).filter(Boolean);
                    if (activeIds.length > 0) {
                        await client.from('slides').delete().not('id', 'in', `(${activeIds.join(',')})`);
                    }
                }
            } catch (err) {
                console.warn('Gagal sync galeri ke Supabase:', err);
            }

            if (typeof this.broadcastChange === 'function') {
                this.broadcastChange('slides', 'UPDATE', { list: slidesList }, 'Admin Galeri');
            }

            return slidesList;
        },

        /**
         * Master Teks Berjalan Kontekstual untuk Seluruh 19 Halaman Rotasi Display TV
         */
        DEFAULT_RUNNING_TEXTS: {
            'utama': "🌙 \"Luruskan dan rapatkan shaf saat sholat berjamaah, karena lurus dan rapatnya shaf termasuk kesempurnaan sholat.\" (HR. Bukhari & Muslim) • 📱 Mohon menonaktifkan atau mengalihkan HP ke mode hening saat berada di dalam ruang sholat • 🤲 \"Barangsiapa yang menanti sholat maka dia senantiasa dihitung berada dalam sholat.\" (HR. Bukhari)",
            'keuangan': "📊 Laporan kas keuangan Masjid Jami' Al-Jihad dicatat transparan, akuntabel, dan diaudit secara berkala • 🤲 \"Perumpamaan orang yang menafkahkan hartanya di jalan Allah seperti sebutir benih yang menumbuhkan tujuh bulir, pada tiap bulir seratus biji.\" (QS. Al-Baqarah: 261) • Jazakumullah khairan katsiran kepada seluruh jamaah dan donatur",
            'jumat': "🌿 \"Jika khatib telah naik ke mimbar dan menyampaikan khutbah, maka dengarkanlah dan diamlah dengan seksama.\" (HR. Bukhari & Muslim) • 🕌 Sunnah Hari Jum'at: Mandi besar, memakai wewangian, mengenakan pakaian terbaik, bersiwak, dan memperbanyak sholawat atas Nabi Muhammad ﷺ",
            'pengumuman': "📢 Warta & Agenda DKM Masjid Jami' Al-Jihad: Seluruh kegiatan peribadatan dan majelis ta'lim terbuka untuk seluruh kaum muslimin • 🧹 Mari senantiasa menjaga kebersihan, ketertiban, dan kesucian area masjid • Hubungi Sekretariat DKM untuk informasi kegiatan dakwah",
            'keuangan-summary': "📈 Ringkasan grafik arus kas masjid: Pengalokasian dana difokuskan untuk kemakmuran masjid, operasional ibadah, santunan dhuafa, dan pemeliharaan fasilitas • 💎 \"Sedekah tidak akan mengurangi harta, melainkan menambah keberkahan dan melapangkan rezeki.\" (HR. Muslim)",
            'qris': "💳 Salurkan infaq dan donasi terbaik Anda melalui scan QRIS resmi Masjid Jami' Al-Jihad menggunakan Mobile Banking atau Dompet Digital apa saja • Bebas biaya admin, aman, cepat, dan tercatat otomatis ke rekening kas masjid • \"Naungan orang beriman di hari kiamat adalah sedekahnya.\" (HR. Ahmad)",
            'slide': "📸 Dokumentasi kegiatan, syiar dakwah, dan informasi program pembinaan umat Masjid Jami' Al-Jihad • 🤝 \"Barangsiapa mengajak kepada kebaikan, maka ia memperoleh pahala semisal pahala orang yang mengikutinya.\" (HR. Muslim) • Mari aktif memakmurkan rumah Allah",
            'ambulance': "🚑 Layanan Mobil Ambulance Gratis DKM Masjid Jami' Al-Jihad siaga 24 jam melayani warga yang sakit dan pengantaran jenazah • ☎️ Hotline Siaga Ambulance: 0812-3456-7890 • Didukung oleh kas operasional umat: Salurkan infaq khusus armada ambulance Anda untuk kepedulian sesama",
            'infaq': "🏗️ Mari berpartisipasi dalam program wakaf pengembangan sarana dan prasarana ibadah Masjid Jami' Al-Jihad • 🤲 \"Apabila anak Adam meninggal dunia, terputuslah amalannya kecuali sedekah jariyah, ilmu yang bermanfaat, atau anak sholeh yang mendoakannya.\" (HR. Muslim)",
            'hikmah': "✨ \"Sebaik-baik manusia adalah yang paling banyak memberikan manfaat bagi manusia lainnya.\" (HR. Ath-Thabrani) • 🌿 \"Barangsiapa menempuh suatu jalan untuk mencari ilmu, maka Allah akan mudahkan baginya jalan menuju surga.\" (HR. Muslim) • Tetaplah istiqomah dalam ketaatan",
            'qurban': "🥩 Panitia Penerimaan & Penyaluran Hewan Qurban Masjid Jami' Al-Jihad siap melayani ibadah qurban Anda • 📜 \"Daging-daging dan darah qurban itu sekali-kali tidak dapat mencapai keridhaan Allah, tetapi ketakwaan dari kamulah yang mencapainya.\" (QS. Al-Hajj: 37) • Hubungi panitia DKM untuk pendaftaran qurban",
            'yasin': "📖 Agenda Rutin Malam Jum'at: Pembacaan Surat Yaasiin 83 Ayat, Dzikir Bersama & Doa untuk Keselamatan Umat • 🤲 \"Surat Yaasiin adalah jantung Al-Qur'an. Barangsiapa membacanya karena mengharapkan ridha Allah dan akhirat, maka diampuni dosa-dosanya.\" • Mari bersiap menyimak dan membaca bersama",
            'live-mekah': "🕋 Siaran Langsung 24 Jam Ka'bah Masjidil Haram Makkah Al-Mukarramah • 🕊️ \"Labbaik Allahumma Labbaik, Labbaika Laa Syariika Laka Labbaik, Innal Hamda Wan Ni'mata Laka Wal Mulk, Laa Syariika Lak.\" • Semoga Allah karuniakan kita kesempatan berhaji dan berumrah ke Baitullah",
            'live-madinah': "🕌 Siaran Langsung 24 Jam Masjid Nabawi Madinah Al-Munawwarah • 💚 \"Allahumma shalli 'alaa Sayyidina Muhammad wa 'alaa aali Sayyidina Muhammad.\" • Salam dan sholawat tercurah selalu kepada junjungan mulia Baginda Rasulullah Muhammad ﷺ",
            'live-mimbar': "🎙️ Siaran Langsung Mimbar Utama Masjid Jami' Al-Jihad • Mohon tenang dan mendengarkan tausiyah serta nasihat keagamaan dengan penuh ketundukan hati • \"Dengarkanlah khutbah dengan seksama agar kalian mendapatkan rahmat dari Allah SWT.\"",
            'idul-fitri': "🌙 Selamat Hari Raya Idul Fitri 1 Syawal • Taqabbalallahu minna wa minkum, shiyamana wa shiyamakum • Mohon maaf lahir dan batin atas segala khilaf • Pelaksanaan Sholat Idul Fitri dimulai tepat pukul 06.45 WIB, mohon hadir tepat waktu dengan membawa perlengkapan sholat",
            'idul-adha': "🐑 Selamat Hari Raya Idul Adha 10 Dzulhijjah & Ibadah Qurban • \"Tiada amal ibadah anak Adam pada hari Nahr yang lebih dicintai Allah melebihi menyembelih hewan qurban.\" (HR. Tirmidzi) • Sholat Ied dimulai pukul 06.45 WIB dilanjutkan prosesi penyembelihan hewan qurban",
            'ramadhan': "🌙 Marhaban Ya Ramadhan • \"Barangsiapa berpuasa di bulan Ramadhan karena iman dan mengharap pahala dari Allah, niscaya diampuni dosa-dosanya yang telah lalu.\" (HR. Bukhari) • Laporan tromol infaq tarawih dan sedekah ifthar ramadhan disajikan transparan setiap hari",
            'kajian': "📚 Kajian Rutin Malam Ahad Ba'da Maghrib Masjid Jami' Al-Jihad • 🌿 \"Barangsiapa yang menempuh jalan untuk menuntut ilmu syar'i, Allah akan mudahkan baginya jalan menuju surga.\" (HR. Muslim) • Terbuka untuk umum jamaah ikhwan & akhwat, scan QR untuk sesi tanya jawab digital"
        },

        /**
         * Ambil teks berjalan untuk halaman tertentu (Multi-Halaman Rotasi Unik)
         */
        getRunningTextForPage(pagePath, settings) {
            const canonicalKey = (pagePath || '')
                .toString()
                .toLowerCase()
                .replace(/^https?:\/\/[^\/]+/, '')
                .replace(/^\/+/, '')
                .replace(/^slides\//, '')
                .replace(/-embed$/, '')
                .replace(/\.html$/, '')
                .trim();

            let pagesMapping = {};
            if (settings && settings.running_text_pages) {
                if (typeof settings.running_text_pages === 'string') {
                    try { pagesMapping = JSON.parse(settings.running_text_pages); } catch (e) {}
                } else if (typeof settings.running_text_pages === 'object') {
                    pagesMapping = settings.running_text_pages;
                }
            }

            // Cek variasi kunci URL di mapping tersimpan
            const candidates = [
                canonicalKey,
                canonicalKey + '-embed',
                '/' + canonicalKey + '-embed',
                '/' + canonicalKey,
                'slides/' + canonicalKey + '.html',
                pagePath
            ];

            for (const key of candidates) {
                if (key && pagesMapping[key] && pagesMapping[key].trim() !== '') {
                    return pagesMapping[key].trim();
                }
            }

            // Kembalikan teks hadits/warta spesifik rekomendasi untuk halaman ini (Tanpa fallback teks global monoton)
            if (this.DEFAULT_RUNNING_TEXTS[canonicalKey]) {
                return this.DEFAULT_RUNNING_TEXTS[canonicalKey];
            }

            return "🕌 Selamat Datang di Masjid Jami' Al-Jihad • Luruskan dan rapatkan shaf saat sholat berjamaah • Jagalah kebersihan dan kesucian masjid";
        },

        /**
         * Konversi berbagai format URL YouTube (watch, share, live, embed, short ID)
         * menjadi URL sematan resmi (embed) yang valid dan mematuhi kebijakan Google/YouTube
         * @param {string} url
         * @param {string} fallbackUrl
         */
        formatYouTubeEmbed(url, fallbackUrl = '') {
            if (!url || typeof url !== 'string') return fallbackUrl;
            const trimmed = url.trim();
            if (!trimmed) return fallbackUrl;
            let videoId = '';

            // 0. Auto-resolve jika pengguna menempelkan link dari portal makkahlive.net atau handle channel YouTube
            if (trimmed.includes('makkahlive.net')) {
                if (trimmed.includes('madin') || trimmed.includes('medin')) {
                    videoId = 'Rs7St51oDDc'; // Saudi Sunnah TV Madinah
                } else {
                    videoId = 'eC4LfEVxvKg'; // Saudi Quran TV Makkah
                }
            } else if (trimmed.includes('@SaudiQuranTv') || trimmed.includes('SaudiQuranTv')) {
                videoId = 'eC4LfEVxvKg';
            } else if (trimmed.includes('@SaudiSunnahTv') || trimmed.includes('SaudiSunnahTv')) {
                videoId = 'Rs7St51oDDc';
            }

            // Format: youtube.com/watch?v=VIDEO_ID atau /watch?xxx&v=VIDEO_ID
            if (!videoId) {
                const vMatch = trimmed.match(/[?&]v=([a-zA-Z0-9_-]{11})/);
                if (vMatch) videoId = vMatch[1];
            }

            // Format: youtube.com/live/VIDEO_ID
            if (!videoId) {
                const liveMatch = trimmed.match(/youtube\.com\/live\/([a-zA-Z0-9_-]{11})/);
                if (liveMatch) videoId = liveMatch[1];
            }

            // Format: youtu.be/VIDEO_ID
            if (!videoId) {
                const shortMatch = trimmed.match(/youtu\.be\/([a-zA-Z0-9_-]{11})/);
                if (shortMatch) videoId = shortMatch[1];
            }

            // Format: youtube.com/embed/VIDEO_ID atau youtube-nocookie.com/embed/VIDEO_ID
            if (!videoId) {
                const embedMatch = trimmed.match(/youtube(?:-nocookie)?\.com\/embed\/([a-zA-Z0-9_-]{11})/);
                if (embedMatch) videoId = embedMatch[1];
            }

            // Format: hanya 11 karakter Video ID langsung
            if (!videoId && /^[a-zA-Z0-9_-]{11}$/.test(trimmed)) {
                videoId = trimmed;
            }

            if (videoId) {
                return `https://www.youtube.com/embed/${videoId}?autoplay=1&mute=1&controls=0&showinfo=0&rel=0&loop=1&playlist=${videoId}&enablejsapi=1&playsinline=1`;
            }

            // Abaikan endpoint live_stream?channel= yang telah dimatikan total oleh YouTube
            if (trimmed.includes('live_stream?channel=') || trimmed.includes('channel=')) {
                return fallbackUrl;
            }

            // Jika URL iframe / stream lain (misal RTSP HLS http/https)
            if (trimmed.startsWith('http://') || trimmed.startsWith('https://')) {
                let stream = trimmed;
                if (!stream.includes('autoplay=')) {
                    stream += (stream.includes('?') ? '&' : '?') + 'autoplay=1&mute=1&controls=0';
                }
                return stream;
            }

            return fallbackUrl;
        },

        /**
         * Pasang pendengar Realtime WebSocket Supabase
         * Memanggil callback begitu ada INSERT, UPDATE, atau DELETE di database
         * Dilengkapi Auto-Reconnect jika koneksi Smart TV terputus/sleep
         */
        _realtimeChannel: null,
        _realtimeReconnectTimer: null,
        subscribeRealtime(onUpdate) {
            // Guard: Di dalam iframe TV, delegasikan ke parent window untuk menghemat koneksi WebSocket & Egress
            const isInsideIframe = (typeof window !== 'undefined' && window.parent && window.parent !== window);
            if (isInsideIframe) return null;

            const client = getClient();
            if (!client || !client.channel) return null;

            if (this._realtimeChannel) {
                try { client.removeChannel(this._realtimeChannel); } catch (e) {}
                this._realtimeChannel = null;
            }

            const channel = client.channel('mosque-display-realtime')
                .on(
                    'postgres_changes',
                    { event: '*', schema: 'public' },
                    (payload) => {
                        console.log('⚡ [Realtime Supabase] Data berubah:', payload.table, payload.eventType);
                        if (typeof onUpdate === 'function') {
                            onUpdate(payload);
                        }
                    }
                )
                .subscribe((status) => {
                    if (status === 'SUBSCRIBED') {
                        console.log('🟢 [Realtime Supabase] Terhubung ke WebSocket cloud!');
                        if (this._realtimeReconnectTimer) {
                            clearTimeout(this._realtimeReconnectTimer);
                            this._realtimeReconnectTimer = null;
                        }
                    } else if (status === 'CHANNEL_ERROR' || status === 'TIMED_OUT' || status === 'CLOSED') {
                        console.warn(`⚠️ [Realtime Supabase] Status WebSocket: ${status}. Menjadwalkan reconnect otomatis dalam 5 detik...`);
                        if (!this._realtimeReconnectTimer) {
                            this._realtimeReconnectTimer = setTimeout(() => {
                                this._realtimeReconnectTimer = null;
                                this.subscribeRealtime(onUpdate);
                            }, 5000);
                        }
                    }
                });

            this._realtimeChannel = channel;
            return channel;
        },

        /**
         * =====================================================
         * MODUL LAYANAN REMOTE TV & DIAGNOSTIK JARAK JAUH
         * =====================================================
         */
        _remoteChannel: null,

        getRemoteChannel() {
            if (!this._remoteChannel || this._remoteChannel.state === 'closed' || this._remoteChannel.state === 'errored') {
                const client = getClient();
                if (client && client.channel) {
                    this._remoteChannel = client.channel('mosque-tv-remote-channel', {
                        config: {
                            broadcast: { ack: true },
                            presence: { key: 'device_id' }
                        }
                    });
                }
            }
            return this._remoteChannel;
        },

        /**
         * Kirim perintah remote kontrol ke seluruh layar TV yang terhubung di masjid
         * @param {string} command - Jenis perintah ('NEXT', 'PREV', 'PAUSE', 'JUMP', 'RELOAD', 'EMERGENCY_ALERT', 'CLEAR_ALERT')
         * @param {object} data - Data pelengkap (url slide, teks peringatan, warna, durasi, dll.)
         * @param {string} sender - Identitas pengirim perintah (misal: 'Super Admin - Luar Kota')
         */
        async sendRemoteCommand(command, data = {}, sender = 'Super Admin') {
            const payload = {
                command: command,
                data: data,
                sender: sender,
                timestamp: Date.now()
            };

            console.log('📡 [Remote Super Admin] Mengirim perintah:', command, payload);

            // 1. Kirim via Realtime WebSocket Broadcast (< 100ms ultra cepat)
            try {
                const channel = this.getRemoteChannel();
                if (channel) {
                    if (channel.state !== 'joined') {
                        await new Promise((resolve) => {
                            channel.subscribe((status) => {
                                if (status === 'SUBSCRIBED') resolve();
                            });
                            setTimeout(resolve, 1500); // timeout aman
                        });
                    }

                    await channel.send({
                        type: 'broadcast',
                        event: 'tv_command',
                        payload: payload
                    });
                }
            } catch (err) {
                console.warn('Gagal broadcast remote command via WebSocket:', err);
            }

            // 2. Simpan juga ke app_settings.remote_command (Dual-Track Redundancy)
            try {
                if (SUPABASE_CONFIG.url) {
                    await fetch(`${SUPABASE_CONFIG.url}/rest/v1/app_settings?id=eq.1`, {
                        method: 'PATCH',
                        headers: {
                            'apikey': SUPABASE_CONFIG.anonKey,
                            'Authorization': `Bearer ${SUPABASE_CONFIG.anonKey}`,
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            remote_command: payload
                        })
                    }).catch(() => {});
                }
            } catch (e) {}

            return {
                success: true,
                ...payload
            };
        },

        /**
         * Pasang pendengar perintah remote di layar TV (Receiver)
         */
        _remoteReconnectTimer: null,
        subscribeRemoteCommands(onCommand) {
            const isInsideIframe = (typeof window !== 'undefined' && window.parent && window.parent !== window);
            if (isInsideIframe) return null;

            const channel = this.getRemoteChannel();
            if (!channel) return null;

            channel
                .on('broadcast', { event: 'tv_command' }, ({ payload }) => {
                    const cmdStr = (payload && payload.command) ? String(payload.command) : '';
                    const cmdData = (payload && payload.data) ? payload.data : {};
                    console.log('🎮 [TV Receiver] Menerima sinyal remote:', cmdStr, payload);
                    if (typeof onCommand === 'function') {
                        // Bungkus payload agar pemanggil yang menguji (cmd === 'STRING')
                        // maupun (payload.command === 'STRING') keduanya berhasil berjalan mulus
                        try {
                            const wrappedPayload = Object.assign(Object.create({
                                toString() { return cmdStr; },
                                valueOf() { return cmdStr; }
                            }), payload);
                            onCommand(wrappedPayload, cmdData, cmdStr);
                        } catch (e) {
                            onCommand(payload, cmdData, cmdStr);
                        }
                    }
                })
                .subscribe((status) => {
                    if (status === 'SUBSCRIBED') {
                        console.log('🟢 [TV Receiver] Saluran remote control siap menerima sinyal');
                        if (this._remoteReconnectTimer) {
                            clearTimeout(this._remoteReconnectTimer);
                            this._remoteReconnectTimer = null;
                        }
                    } else if (status === 'CHANNEL_ERROR' || status === 'TIMED_OUT' || status === 'CLOSED') {
                        console.warn(`⚠️ [TV Receiver Remote] Status: ${status}. Reconnect otomatis dalam 5 detik...`);
                        if (!this._remoteReconnectTimer) {
                            this._remoteReconnectTimer = setTimeout(() => {
                                this._remoteReconnectTimer = null;
                                this._remoteChannel = null;
                                this.subscribeRemoteCommands(onCommand);
                            }, 5000);
                        }
                    }
                });

            return channel;
        },

        /**
         * =====================================================
         * UNIVERSAL REAL-TIME CROSS-ACCOUNT SYNC ENGINE
         * =====================================================
         * Memancarkan pembaruan data universal ke seluruh layar TV,
         * tab admin lain, dan perangkat seluler secara seketika (<100ms)
         */
        _localBroadcastChannel: null,

        getLocalBroadcastChannel() {
            if (!this._localBroadcastChannel && typeof BroadcastChannel !== 'undefined') {
                try {
                    this._localBroadcastChannel = new BroadcastChannel('mosque_display_channel');
                } catch (e) {}
            }
            return this._localBroadcastChannel;
        },

        broadcastChange(table, action = 'UPDATE', data = {}, sender = 'Admin') {
            this.invalidateCache(table);
            const timestamp = Date.now();
            const eventId = 'evt_' + timestamp + '_' + Math.random().toString(36).substr(2, 6);
            const payload = {
                event_id: eventId,
                table: table,
                action: action,
                data: data,
                sender: sender,
                timestamp: timestamp
            };

            this._sentTvEvents = this._sentTvEvents || new Map();
            for (const [id, sent] of this._sentTvEvents) {
                if (timestamp - sent.timestamp > 120000) this._sentTvEvents.delete(id);
            }
            this._sentTvEvents.set(eventId, { table: String(table).toLowerCase(), timestamp });

            console.log(`📡 [Universal Sync] Memancarkan pembaruan realtime [${table} - ${action}]:`, payload);

            // Track 1: WebSocket Broadcast ke seluruh TV via Remote Channel Supabase
            this.sendRemoteCommand('DATA_UPDATED', payload, sender).catch(() => {});

            // Track 2: Command khusus legacy untuk kompatibilitas slide
            const syncCmdMap = {
                'kajian': 'SYNC_KAJIAN',
                'agenda_rutin': 'SYNC_AGENDA_RUTIN',
                'pengumuman_jumat': 'SYNC_PENGUMUMAN_JUMAT',
                'ramadhan': 'SYNC_RAMADHAN',
                'keuangan': 'SYNC_KEUANGAN',
                'keuangan_ambulance': 'SYNC_AMBULANCE',
                'program_infaq': 'SYNC_INFAQ',
                'sholat_jumat': 'SYNC_JUMAT',
                'app_settings': 'UPDATE_SETTINGS'
            };
            if (syncCmdMap[table]) {
                this.sendRemoteCommand(syncCmdMap[table], data, sender).catch(() => {});
            }

            // Track 3: BroadcastChannel lokal antar-tab / antar-window pada perangkat yang sama
            try {
                const bc = this.getLocalBroadcastChannel();
                if (bc) {
                    bc.postMessage({ type: 'DATA_UPDATED', ...payload });
                }
            } catch (e) {}

            // Track 4: LocalStorage Event (Dual local redundancy)
            try {
                if (typeof localStorage !== 'undefined') {
                    localStorage.setItem('mosque_realtime_sync', JSON.stringify({
                        ...payload,
                        _rand: Math.random() // Memastikan storage event selalu terpicu di tab lain
                    }));
                }
            } catch (e) {}

            // Track 5: PostMessage ke window induk (jika dijalankan dari iframe admin / slide) dengan targetOrigin yang aman
            try {
                if (typeof window !== 'undefined' && window.parent && window.parent !== window) {
                    const targetOrigin = (window.location.origin && window.location.origin !== 'null') ? window.location.origin : '*';
                    window.parent.postMessage({ type: 'DATA_UPDATED', ...payload }, targetOrigin);
                }
            } catch (e) {}
            return eventId;
        },

        /**
         * Mengambil / membuat Device ID unik untuk layar TV Display
         * Mendukung URL query parameter (?tv_id= atau ?device_id=) dan localStorage
         */
        getTvDeviceId() {
            try {
                if (typeof window !== 'undefined' && window.location) {
                    const params = new URLSearchParams(window.location.search);
                    const qId = params.get('device_id') || params.get('tv_id');
                    if (qId) return qId.trim();
                }
                if (typeof localStorage !== 'undefined') {
                    let stored = localStorage.getItem('aljihad_tv_device_id');
                    if (stored) return stored.trim();
                    stored = 'TV_MASJID_' + Math.random().toString(36).substring(2, 7).toUpperCase();
                    localStorage.setItem('aljihad_tv_device_id', stored);
                    return stored;
                }
            } catch (e) {}
            return 'TV_MASJID_UTAMA';
        },

        /**
         * Kirim konfirmasi penerimaan & penerapan data dari layar TV (TV Acknowledgment)
         * Memastikan pengurus mengetahui secara pasti bahwa layar TV telah menerapkan event tersebut
         */
        async sendTvAcknowledgment(deviceId, eventId, table, action = 'APPLIED') {
            try {
                const channel = this.getRemoteChannel();
                if (!channel) return;
                const devId = deviceId || this.getTvDeviceId();
                const ackPayload = {
                    type: 'TV_ACK',
                    device_id: devId,
                    event_id: eventId,
                    table: table,
                    action: action,
                    timestamp: Date.now()
                };
                if (channel.state === 'joined') {
                    await channel.send({
                        type: 'broadcast',
                        event: 'tv_ack',
                        payload: ackPayload
                    });
                }
            } catch (e) {
                console.warn('[SupabaseDB] Gagal kirim TV ACK:', e);
            }
        },

        /**
         * Pasang pendengar konfirmasi penerimaan TV di dashboard Admin / Petugas
         */
        isOwnTvAcknowledgment(ack) {
            const sent = ack && this._sentTvEvents && this._sentTvEvents.get(ack.event_id);
            if (!(sent && ack.device_id && ack.table === sent.table &&
                Date.now() - sent.timestamp <= 120000 &&
                ['RECEIVED', 'APPLIED', 'RENDER_UNCONFIRMED'].includes(ack.action))) return false;
            sent.devices = sent.devices || new Map();
            const previous = sent.devices.get(ack.device_id);
            if (previous === 'APPLIED' || previous === ack.action ||
                (previous === 'RENDER_UNCONFIRMED' && ack.action === 'RECEIVED')) return false;
            sent.devices.set(ack.device_id, ack.action);
            return true;
        },

        onTvAcknowledgment(callback) {
            const channel = this.getRemoteChannel();
            if (!channel) return null;
            channel.on('broadcast', { event: 'tv_ack' }, ({ payload }) => {
                if (typeof callback === 'function' && this.isOwnTvAcknowledgment(payload)) {
                    callback(payload);
                }
            });
            return true;
        },

        // A timeout proves only that render could not be confirmed. It must never turn green.
        beginTvRenderConfirmation(event, frame, isStillVisible, timeoutMs = 10000) {
            if (!event.event_id) return;
            this._pendingTvAcks = this._pendingTvAcks || new Map();
            for (const [id, pending] of this._pendingTvAcks) {
                if (id === event.event_id || pending.table === event.table) {
                    clearTimeout(pending.timer);
                    this._pendingTvAcks.delete(id);
                }
            }
            const pending = {
                table: event.table, frame, source: frame && frame.contentWindow,
                url: frame && frame.src, isStillVisible,
                devId: this.getTvDeviceId()
            };
            pending.timer = setTimeout(() => {
                if (this._pendingTvAcks.get(event.event_id) !== pending) return;
                this._pendingTvAcks.delete(event.event_id);
                this.sendTvAcknowledgment(pending.devId, event.event_id, pending.table, 'RENDER_UNCONFIRMED');
            }, timeoutMs);
            this._pendingTvAcks.set(event.event_id, pending);
            this.sendTvAcknowledgment(pending.devId, event.event_id, pending.table, 'RECEIVED');
        },

        recordSlideReadiness(message) {
            if (!message || message.origin !== window.location.origin || message.data?.type !== 'SLIDE_RENDER_CONFIRMED' ||
                message.data.event_id || message.data.phase !== 'ready') return false;
            const frame = Array.from(document.querySelectorAll('iframe')).find(frame => frame.contentWindow === message.source);
            if (!frame) return false;
            const name = new URL(frame.src, window.location.href).pathname.split('/').pop().replace(/\.html$/, '');
            if (name !== message.data.slide) return false;
            this._slideReadiness = this._slideReadiness || new Map();
            this._slideReadiness.set(name, { timestamp: Date.now(), frame: frame.id, scope: 'dom' });
            return true;
        },

        acceptTvRenderConfirmation(message) {
            if (!message || message.origin !== window.location.origin || !message.data ||
                message.data.type !== 'SLIDE_RENDER_CONFIRMED') return false;
            const data = message.data;
            const pending = this._pendingTvAcks && this._pendingTvAcks.get(data.event_id);
            if (!pending || !pending.source || message.source !== pending.source ||
                data.table !== pending.table || pending.frame.src !== pending.url ||
                !pending.isStillVisible()) return false;
            if (data.slide && data.slide !== new URL(pending.url, window.location.href).pathname.split('/').pop().replace(/\.html$/, '')) return false;
            clearTimeout(pending.timer);
            this._pendingTvAcks.delete(data.event_id);
            this.sendTvAcknowledgment(pending.devId, data.event_id, pending.table, 'APPLIED');
            return true;
        },

        /**
         * Pasang pendengar pembaruan data universal (Universal Real-Time Listener)
         * Mengikat 5 saluran redundan sekaligus dengan deduplikasi cerdas & per-tabel debounce:
         * 1. Supabase Postgres WAL Changes
         * 2. Supabase Realtime WebSocket Remote Broadcast
         * 3. Local BroadcastChannel
         * 4. Window postMessage (Origin Validated)
         * 5. Window storage sync event
         * @param {Function} callback - function({ table, action, data, sender, source, timestamp, event_id })
         */
        onDataChange(callback) {
            if (typeof callback !== 'function') return null;

            // Debounce per-tabel dan deduplikasi cerdas multi-track dengan pengumpulan baris (Batch Buffer)
            const debounceTimers = new Map();
            const recentSignatures = new Map();
            const tableEventQueues = new Map();

            const safeTrigger = (evt) => {
                if (!evt) return;
                const table = String(evt.table || 'all').toLowerCase();
                const action = evt.action || 'UPDATE';
                if (table && table !== 'all') {
                    this.invalidateCache(table);
                }

                const now = Date.now();
                // Bersihkan entri lama (>5 detik)
                for (const [key, t] of recentSignatures.entries()) {
                    if (now - t > 5000) recentSignatures.delete(key);
                }

                // Signature unik untuk deduplikasi antar-saluran (WebSocket + BroadcastChannel + LocalStorage)
                const rowId = (evt.data && (evt.data.id || evt.data.tanggal || evt.data.key)) || '';
                const dataHash = evt.event_id || `${table}_${action}_${rowId}_${JSON.stringify(evt.data || {}).slice(0, 50)}`;

                // Jika event persis sama sudah diproses dalam 250ms terakhir dari track lain, abaikan redundansi
                if (recentSignatures.has(dataHash) && (now - recentSignatures.get(dataHash)) < 250) {
                    return;
                }
                recentSignatures.set(dataHash, now);

                // Kumpulkan seluruh perubahan baris per tabel dalam debounce window (Batch Buffer)
                if (!tableEventQueues.has(table)) {
                    tableEventQueues.set(table, []);
                }
                const queue = tableEventQueues.get(table);
                if (rowId) {
                    const existingIdx = queue.findIndex(item => {
                        const itemRowId = (item.data && (item.data.id || item.data.tanggal || item.data.key)) || '';
                        return itemRowId && String(itemRowId) === String(rowId);
                    });
                    if (existingIdx >= 0) {
                        queue[existingIdx] = evt; // Perbarui baris yang sama dengan data mutakhir
                    } else {
                        queue.push(evt); // Kumpulkan baris berbeda ke dalam antrean
                    }
                } else {
                    queue.push(evt);
                }

                // Debounce terisolasi per-tabel (80ms):
                // Mengumpulkan seluruh perubahan baris dalam tabel dan mengeksekusi semua event tanpa terlewat
                if (debounceTimers.has(table)) {
                    clearTimeout(debounceTimers.get(table));
                }

                const timer = setTimeout(() => {
                    debounceTimers.delete(table);
                    const eventsToDispatch = tableEventQueues.get(table) || [];
                    tableEventQueues.delete(table);

                    eventsToDispatch.forEach(item => {
                        try {
                            const enriched = Object.assign({}, item, {
                                batch: eventsToDispatch,
                                batch_count: eventsToDispatch.length
                            });
                            // Undefined (including legacy fire-and-forget handlers) is NOT proof of render.
                            Promise.resolve().then(() => callback(enriched)).then(async (result) => {
                                if (!isInsideIframe || !enriched.event_id || result !== true) return;
                                if (typeof window.requestAnimationFrame !== 'function') return;
                                await new Promise(resolve => window.requestAnimationFrame(() => window.requestAnimationFrame(resolve)));
                                if (document.visibilityState === 'hidden') return;
                                window.parent.postMessage({
                                    type: 'SLIDE_RENDER_CONFIRMED',
                                    slide: (window.location.pathname || '').split('/').pop().replace(/\.html$/, ''),
                                    event_id: enriched.event_id,
                                    table: enriched.table || table,
                                    timestamp: Date.now()
                                }, window.location.origin);
                            }).catch(err => {
                                console.warn('[SupabaseDB] Render belum terkonfirmasi:', err);
                            });
                        } catch (err) {
                            console.error(`[SupabaseDB.onDataChange] Callback error on table ${table}:`, err);
                        }
                    });
                }, 80);

                debounceTimers.set(table, timer);
            };

            // Cek jika berjalan di dalam iframe TV (child frame)
            const isInsideIframe = (typeof window !== 'undefined' && window.parent && window.parent !== window);

            // 1 & 2: HANYA buka koneksi WebSocket langsung ke Supabase Cloud jika berada di top-level window (TV induk atau admin standalone)
            // Di dalam iframe, event realtime diterima instan lewat postMessage parent TV & BroadcastChannel lokal (hemat bandwidth & zero WS churn)
            if (!isInsideIframe) {
                // 1. Supabase Realtime Postgres Changes
                try {
                    this.subscribeRealtime((payload) => {
                        this.invalidateCache(payload.table);
                        safeTrigger({
                            table: payload.table,
                            action: payload.eventType || 'UPDATE',
                            data: payload.new || payload.old || {},
                            source: 'postgres_changes',
                            timestamp: Date.now()
                        });
                    });
                } catch (e) {}

                // 2. Supabase Remote Broadcast Commands
                try {
                    this.subscribeRemoteCommands((cmdPayload, data, commandName) => {
                        const cmd = (typeof cmdPayload === 'string' ? cmdPayload : (cmdPayload?.command || commandName || '')).toUpperCase();
                        const d = (cmdPayload && cmdPayload.data) ? cmdPayload.data : (data || {});

                        if (cmd === 'DATA_UPDATED') {
                            safeTrigger({
                                event_id: cmdPayload?.event_id || d?.event_id,
                                table: d.table,
                                action: d.action || 'UPDATE',
                                data: d.data || {},
                                sender: cmdPayload?.sender || 'Remote Admin',
                                source: 'remote_broadcast',
                                timestamp: d.timestamp || Date.now()
                            });
                        } else if (cmd.startsWith('SYNC_')) {
                            const tableMap = {
                                'SYNC_KAJIAN': 'kajian',
                                'SYNC_AGENDA_RUTIN': 'agenda_rutin',
                                'SYNC_PENGUMUMAN_JUMAT': 'pengumuman_jumat',
                                'SYNC_RAMADHAN': 'ramadhan',
                                'SYNC_KEUANGAN': 'keuangan',
                                'SYNC_AMBULANCE': 'keuangan_ambulance',
                                'SYNC_INFAQ': 'program_infaq',
                                'SYNC_JUMAT': 'sholat_jumat',
                                'SYNC_JADWAL_SHOLAT': 'jadwal_sholat',
                                'SYNC_SLIDES': 'slides',
                                'SYNC_QRIS': 'qris',
                                'SYNC_QURBAN': 'qurban',
                                'SYNC_UNDANGAN': 'undangan',
                                'SYNC_RUNNING_TEXT': 'running_text'
                            };
                            safeTrigger({
                                event_id: cmdPayload?.event_id,
                                table: tableMap[cmd] || cmd.toLowerCase(),
                                action: 'SYNC',
                                data: d,
                                sender: cmdPayload?.sender || 'Remote Admin',
                                source: 'remote_sync_command',
                                timestamp: Date.now()
                            });
                        } else if (cmd === 'UPDATE_SETTINGS') {
                            safeTrigger({
                                event_id: cmdPayload?.event_id,
                                table: 'app_settings',
                                action: 'UPDATE',
                                data: d,
                                sender: cmdPayload?.sender || 'Remote Admin',
                                source: 'remote_settings',
                                timestamp: Date.now()
                            });
                        }
                    });
                } catch (e) {}
            }

            // 3. Local BroadcastChannel
            try {
                const bc = this.getLocalBroadcastChannel();
                if (bc) {
                    bc.addEventListener('message', (event) => {
                        if (event.data && (event.data.type === 'DATA_UPDATED' || event.data.table)) {
                            safeTrigger({
                                event_id: event.data.event_id,
                                table: event.data.table,
                                action: event.data.action || 'UPDATE',
                                data: event.data.data || {},
                                source: 'broadcast_channel',
                                timestamp: event.data.timestamp || Date.now()
                            });
                        }
                    });
                }
            } catch (e) {}

            // 4. Window postMessage (dari Parent TV atau iframe) dengan Validasi Origin & Window Source yang Aman
            try {
                if (typeof window !== 'undefined') {
                    window.addEventListener('message', (event) => {
                        if (!event.data) return;

                        // Validasi Origin: tolak pesan dari origin asing
                        const myOrigin = window.location.origin;
                        if (myOrigin && myOrigin !== 'null' && event.origin && event.origin !== 'null' && event.origin !== myOrigin) {
                            console.warn('[SupabaseDB] postMessage ditolak: Origin asing tidak dipercaya:', event.origin);
                            return;
                        }

                        // Validasi Source Window
                        if (event.source !== window && event.source !== window.parent && (!window.frames || !Array.from(window.frames).includes(event.source))) {
                            return;
                        }

                        if (event.data.type === 'DATA_UPDATED') {
                            safeTrigger({
                                event_id: event.data.event_id,
                                table: event.data.table,
                                action: event.data.action || 'UPDATE',
                                data: event.data.data || event.data.payload || {},
                                source: 'window_message',
                                timestamp: event.data.timestamp || Date.now()
                            });
                        } else if (typeof event.data.type === 'string' && event.data.type.startsWith('SYNC_')) {
                            const tableMap = {
                                'SYNC_KAJIAN': 'kajian',
                                'SYNC_AGENDA_RUTIN': 'agenda_rutin',
                                'SYNC_PENGUMUMAN_JUMAT': 'pengumuman_jumat',
                                'SYNC_RAMADHAN': 'ramadhan',
                                'SYNC_KEUANGAN': 'keuangan',
                                'SYNC_AMBULANCE': 'keuangan_ambulance',
                                'SYNC_INFAQ': 'program_infaq',
                                'SYNC_JUMAT': 'sholat_jumat'
                            };
                            safeTrigger({
                                event_id: event.data.event_id,
                                table: tableMap[event.data.type] || event.data.type.toLowerCase(),
                                action: 'SYNC',
                                data: event.data.data || {},
                                source: 'window_message_sync',
                                timestamp: Date.now()
                            });
                        }
                    });
                }
            } catch (e) {}

            // 5. Window Storage Sync Event
            try {
                if (typeof window !== 'undefined') {
                    window.addEventListener('storage', (e) => {
                        if (e.key === 'mosque_realtime_sync' && e.newValue) {
                            try {
                                const parsed = JSON.parse(e.newValue);
                                safeTrigger({
                                    table: parsed.table,
                                    action: parsed.action || 'UPDATE',
                                    data: parsed.data || {},
                                    source: 'storage_sync',
                                    timestamp: parsed.timestamp || Date.now()
                                });
                            } catch (err) {}
                        }
                    });
                }
            } catch (e) {}

            return true;
        },

        /**
         * Daftarkan status kehadiran TV (Presence Heartbeat)
         */
        _lastPresenceTrack: 0,
        async trackDevicePresence(deviceInfo) {
            const now = Date.now();
            // Throttle presence heartbeat: hanya kirim ke cloud Supabase maksimal 1x per 60 detik (menghemat 85% egress)
            if (now - this._lastPresenceTrack < 60000 && !(deviceInfo && deviceInfo.force)) {
                return;
            }
            this._lastPresenceTrack = now;

            const channel = this.getRemoteChannel();
            if (!channel) return;

            const devId = (deviceInfo && deviceInfo.device_id) || this.getTvDeviceId();
            const payload = Object.assign({
                type: 'tv-display',
                device_id: devId,
                device_name: devId === 'TV_MASJID_UTAMA' ? 'TV Layar Utama Masjid Jami\' Al-Jihad' : `Smart TV Layar Masjid (${devId})`,
                online_since: this._onlineSince || (this._onlineSince = new Date().toISOString()),
                current_slide: 'slides/utama.html',
                currentSlide: 'slides/utama.html',
                currentSlideTitle: 'Slide Utama',
                resolution: '1920 x 1080 px',
                width: typeof window !== 'undefined' ? (window.innerWidth || 1920) : 1920,
                height: typeof window !== 'undefined' ? (window.innerHeight || 1080) : 1080,
                userAgent: typeof navigator !== 'undefined' ? (navigator.userAgent || 'Smart TV Browser') : 'Smart TV Browser',
                isPaused: false,
                last_heartbeat: Date.now()
            }, deviceInfo);

            const sendTrack = async () => {
                try {
                    await channel.track(payload);
                    console.log('📡 [TV Presence] Status TV online terdaftar di cloud:', payload);
                } catch (e) {
                    console.warn('Gagal track presence TV:', e);
                }
            };

            if (channel.state === 'joined') {
                await sendTrack();
            } else {
                channel.subscribe(async (status) => {
                    if (status === 'SUBSCRIBED') {
                        await sendTrack();
                    }
                });
            }
        },

        /**
         * Pantau kehadiran perangkat TV yang online dari dashboard Admin
         */
        subscribeDevicePresence(onSync) {
            const channel = this.getRemoteChannel();
            if (!channel) return null;

            const handleSync = () => {
                const state = channel.presenceState();
                console.log('👥 [Admin Presence] Daftar TV Online Sync:', state);
                if (typeof onSync === 'function') {
                    onSync(state);
                }
            };

            channel
                .on('presence', { event: 'sync' }, handleSync)
                .on('presence', { event: 'join' }, handleSync)
                .on('presence', { event: 'leave' }, handleSync);

            if (channel.state === 'joined') {
                handleSync();
            } else {
                channel.subscribe((status) => {
                    if (status === 'SUBSCRIBED') {
                        handleSync();
                    }
                });
            }

            return channel;
        },

        /**
         * Ambil daftar Surat Undangan Eksternal (Ukhuwah Antar Masjid)
         */
        async getUndanganEksternal(forceRefresh = false) {
            if (!forceRefresh) {
                const cached = this.getCached('undangan');
                if (cached) return cached;
            }
            // 1. Coba baca dari cloud database Supabase jika tabel ada
            try {
                const client = getClient();
                if (client) {
                    const { data, error } = await client.from('undangan_eksternal').select('*').order('urutan', { ascending: true });
                    if (!error && Array.isArray(data) && data.length > 0) {
                        localStorage.setItem('aljihad_undangan_eksternal', JSON.stringify(data));
                        localStorage.setItem('cached_undangan_eksternal', JSON.stringify(data));
                        return data;
                    }
                }
            } catch (err) {
                console.warn('Gagal membaca undangan_eksternal dari Supabase SDK:', err);
            }

            // 2. Direct REST API Fallback
            try {
                const rows = await this.restFetch('undangan_eksternal?order=urutan.asc');
                if (Array.isArray(rows) && rows.length > 0) {
                    localStorage.setItem('aljihad_undangan_eksternal', JSON.stringify(rows));
                    localStorage.setItem('cached_undangan_eksternal', JSON.stringify(rows));
                    return rows;
                }
            } catch (restErr) {
                // Table might not exist yet, fallback silently
            }

            // 3. Coba baca dari localStorage lokal
            try {
                const local = localStorage.getItem('aljihad_undangan_eksternal') || localStorage.getItem('cached_undangan_eksternal');
                if (local) {
                    const parsed = JSON.parse(local);
                    if (Array.isArray(parsed) && parsed.length > 0) {
                        return parsed;
                    }
                }
            } catch (e) {}

            // 3. Default fallback 2 undangan resmi untuk demonstrasi & tampilan awal
            const defaultUndangan = [
                {
                    id: 1,
                    nama_pengundang: "Mushola Al-Ikhlas",
                    nama_acara: "Peringatan Maulid Nabi Muhammad SAW 1448 H",
                    penceramah: "Ustadz Dr. H. Ahmad Fauzi, M.Ag",
                    tanggal_acara: "Sabtu, 15 Oktober 2026",
                    waktu_acara: "20:00 WIB (Ba'da Isya) ~ Selesai",
                    tempat_acara: "Mushola Al-Ikhlas, Jl. Melati Raya Blok B No. 12",
                    keterangan: "Mengharap kehadiran dan kebersamaan seluruh jamaah Masjid Jami' Al-Jihad dalam mempererat tali silaturahmi.",
                    is_active: true,
                    urutan: 1
                },
                {
                    id: 2,
                    nama_pengundang: "Masjid Baitul Muttaqin",
                    nama_acara: "Tabligh Akbar & Santunan Anak Yatim",
                    penceramah: "Ustadz H. Abdul Somad, Lc., MA",
                    tanggal_acara: "Ahad, 23 Oktober 2026",
                    waktu_acara: "08:30 WIB ~ Dzuhur Berjamaah",
                    tempat_acara: "Masjid Baitul Muttaqin, Jl. Kemuning Asri No. 8",
                    keterangan: "Terbuka untuk umum kaum muslimin dan muslimat. Disediakan hidangan sarapan dan ramah tamah bersama.",
                    is_active: true,
                    urutan: 2
                }
            ];

            try {
                localStorage.setItem('aljihad_undangan_eksternal', JSON.stringify(defaultUndangan));
                localStorage.setItem('cached_undangan_eksternal', JSON.stringify(defaultUndangan));
            } catch (e) {}

            return defaultUndangan;
        },

        /**
         * Simpan / Perbarui daftar Surat Undangan Eksternal
         */
        async saveUndanganEksternal(undanganList) {
            return this.saveUndanganUnified(undanganList);
        },

        /**
         * Pembersih Cache Usang & Optimasi Responsivitas Browser
         * Menghapus kunci storage yang sudah tidak relevan, menyinkronkan format lama ke baru,
         * dan mengosongkan cache Service Worker usang.
         */
        cleanupObsoleteCache() {
            const removed = [];
            try {
                // 1. Kunci LocalStorage yang sudah usang atau usang redundant
                const obsoleteKeys = [
                    'cached_prayer_mode_enabled',
                    'cached_prayer_mode_jumat_duration',
                    'nu_last_sync_date',
                    'cached_ramadhan_legacy',
                    'cached_tarhim_legacy',
                    'cached_jumat_legacy',
                    'old_quick_petugas_state',
                    'cached_display_config_legacy',
                    'portal_gateway_active',
                    'portal_auth_target',
                    'aljihad_gateway_token',
                    'backup_petugas_old',
                    'cached_prayer_settings_old'
                ];

                obsoleteKeys.forEach(k => {
                    if (localStorage.getItem(k) !== null) {
                        localStorage.removeItem(k);
                        removed.push(k);
                    }
                });

                // 2. Periksa base64 foto imam di localStorage (jika > 500KB dibersihkan agar storage tidak bocor)
                const cachedPhoto = localStorage.getItem('cached_jumat_foto_imam');
                if (cachedPhoto && cachedPhoto.length > 500000) {
                    localStorage.removeItem('cached_jumat_foto_imam');
                    removed.push('cached_jumat_foto_imam (oversized >500KB)');
                }

                // 3. Bersihkan Service Worker Cache usang via message jika controller aktif
                if (typeof navigator !== 'undefined' && 'serviceWorker' in navigator && navigator.serviceWorker.controller) {
                    navigator.serviceWorker.controller.postMessage({ action: 'clearCache' });
                }

                if (removed.length > 0) {
                    console.log('🧹 [SupabaseDB] Pembersihan cache usang berhasil:', removed);
                }
            } catch (e) {
                console.warn('[SupabaseDB] Cleanup cache warning:', e);
            }
            return removed;
        }
    };

    // Jalankan auto-cleanup ringan saat inisialisasi
    try {
        SupabaseDB.cleanupObsoleteCache();
    } catch(e) {}

    window.SupabaseDB = SupabaseDB;
})(window);
