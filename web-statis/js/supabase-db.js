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
         * Ambil pengaturan umum (app_settings) dari Supabase Cloud
         * Mengambil seluruh baris konfigurasi (row 1 + baris key-value dinamis)
         */
        async getSettings() {
            try {
                const client = getClient();
                let data = null;
                if (client) {
                    const res = await client.from('app_settings').select('*').order('id', { ascending: true });
                    if (!res.error && res.data && res.data.length > 0) {
                        data = res.data;
                    }
                }
                // Direct REST Fallback jika client SDK null / gagal
                if (!data || data.length === 0) {
                    try {
                        const restData = await this.restFetch('app_settings?select=*&order=id.asc');
                        if (Array.isArray(restData) && restData.length > 0) {
                            data = restData;
                        }
                    } catch (restErr) {
                        console.warn('[SupabaseDB] REST fetch app_settings:', restErr);
                    }
                }

                if (data && data.length > 0) {
                    // Baris utama id = 1
                    const row1 = data.find(r => r.id === 1) || data[0];
                    const settings = Object.assign({}, this.defaultSettings, row1);

                    // Gabungkan baris key-value tambahan dari Supabase
                    data.forEach(r => {
                        if (r.key && r.id !== 1 && r.value !== null && r.value !== undefined) {
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
                    } catch (e) {}

                    return settings;
                }
            } catch (err) {
                console.warn('Gagal membaca app_settings dari Supabase:', err);
            }

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
            } catch (e) {}

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
                const headers = {
                    'apikey': SUPABASE_CONFIG.anonKey,
                    'Authorization': `Bearer ${SUPABASE_CONFIG.anonKey}`,
                    'Content-Type': 'application/json',
                    'Prefer': 'return=representation'
                };

                const checkRes = await fetch(`${SUPABASE_CONFIG.url}/rest/v1/app_settings?key=eq.${key}&select=id`, {
                    headers: { 'apikey': SUPABASE_CONFIG.anonKey, 'Authorization': `Bearer ${SUPABASE_CONFIG.anonKey}` }
                });
                if (checkRes.ok) {
                    const existing = await checkRes.json();
                    if (existing && existing.length > 0) {
                        await fetch(`${SUPABASE_CONFIG.url}/rest/v1/app_settings?key=eq.${key}`, {
                            method: 'PATCH',
                            headers: headers,
                            body: JSON.stringify({ value: valStr })
                        });
                        return true;
                    }
                }

                await fetch(`${SUPABASE_CONFIG.url}/rest/v1/app_settings`, {
                    method: 'POST',
                    headers: headers,
                    body: JSON.stringify({ key: key, value: valStr })
                });
                return true;
            } catch (e) {
                console.warn(`[SupabaseDB] Gagal saveSettingItem (${key}):`, e);
                return false;
            }
        },

        /**
         * Simpan / Perbarui data jadwal Kajian Rutin Malam Ahad 1 Bulan Penuh
         */
        async saveKajianData(payloadData, enabled = true, jamMulai = '18:25') {
            try {
                localStorage.setItem('cached_kajian_sabtu', JSON.stringify(payloadData));
                localStorage.setItem('cached_kajian_sabtu_enabled', enabled);
                localStorage.setItem('cached_kajian_sabtu_start_time', jamMulai);

                await Promise.all([
                    this.saveSettingItem('kajian_sabtu_data', payloadData),
                    this.saveSettingItem('kajian_sabtu_enabled', enabled),
                    this.saveSettingItem('kajian_sabtu_start_time', jamMulai)
                ]);

                if (typeof this.broadcastChange === 'function') {
                    this.broadcastChange('kajian', 'SYNC', { data: payloadData, enabled, jamMulai });
                } else if (typeof this.sendRemoteCommand === 'function') {
                    this.sendRemoteCommand('SYNC_KAJIAN', { data: payloadData, enabled, jamMulai });
                }
                return true;
            } catch (err) {
                console.warn('[SupabaseDB] Gagal saveKajianData:', err);
                return false;
            }
        },

        /**
         * Simpan / Perbarui agenda rutin masjid (Yasin, Kajian, Tahsin, Tafsir)
         */
        async saveAgendaRutin(payload) {
            try {
                localStorage.setItem('agenda_rutin_settings', JSON.stringify(payload));
                await this.saveSettingItem('kegiatan_rutin_settings', payload);
                if (typeof this.broadcastChange === 'function') {
                    this.broadcastChange('agenda_rutin', 'SYNC', payload);
                } else if (typeof this.sendRemoteCommand === 'function') {
                    this.sendRemoteCommand('SYNC_AGENDA_RUTIN', payload);
                }
                return true;
            } catch (err) {
                console.warn('[SupabaseDB] Gagal saveAgendaRutin:', err);
                return false;
            }
        },

        /**
         * Simpan / Perbarui lembar pengumuman sholat jumat & kas
         */
        async savePengumumanJumat(pjData) {
            try {
                localStorage.setItem('pengumuman_jumat_data', JSON.stringify(pjData));
                await this.saveSettingItem('pengumuman_jumat', pjData);
                if (typeof this.broadcastChange === 'function') {
                    this.broadcastChange('pengumuman_jumat', 'SYNC', pjData);
                } else if (typeof this.sendRemoteCommand === 'function') {
                    this.sendRemoteCommand('SYNC_PENGUMUMAN_JUMAT', pjData);
                }
                return true;
            } catch (err) {
                console.warn('[SupabaseDB] Gagal savePengumumanJumat:', err);
                return false;
            }
        },

        /**
         * Simpan / Perbarui app_settings ke Cloud Supabase (id=1 dan key-value dinamis)
         * Mendukung pembaruan sebagian (partial update) dan pembaruan menyeluruh
         * @param {Object} updatedFields
         */
        async saveSettings(updatedFields) {
            if (!updatedFields || typeof updatedFields !== 'object') {
                return { success: false, error: 'Payload tidak valid' };
            }

            // 1. Simpan langsung ke cache lokal browser
            try {
                const currentRaw = localStorage.getItem('cached_app_settings');
                const current = currentRaw ? JSON.parse(currentRaw) : Object.assign({}, this.defaultSettings);
                const merged = Object.assign({}, current, updatedFields);
                localStorage.setItem('cached_app_settings', JSON.stringify(merged));
                if (updatedFields.rotation_interval) {
                    localStorage.setItem('display_rotation_interval', updatedFields.rotation_interval);
                }
            } catch (e) {
                console.warn('[SupabaseDB] Gagal update cached_app_settings lokal:', e);
            }

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

            // 3. Kirim update ke Cloud Supabase
            try {
                if (SUPABASE_CONFIG && SUPABASE_CONFIG.url) {
                    const headers = {
                        'apikey': SUPABASE_CONFIG.anonKey,
                        'Authorization': `Bearer ${SUPABASE_CONFIG.anonKey}`,
                        'Content-Type': 'application/json',
                        'Prefer': 'return=representation'
                    };

                    // Update row 1 jika ada field row 1
                    if (Object.keys(row1Payload).length > 0) {
                        await fetch(`${SUPABASE_CONFIG.url}/rest/v1/app_settings?id=eq.1`, {
                            method: 'PATCH',
                            headers: headers,
                            body: JSON.stringify(row1Payload)
                        });
                    }

                    // Update / Upsert key-value items
                    for (const [key, val] of Object.entries(keyValuePayloads)) {
                        await this.saveSettingItem(key, val);
                    }

                    // Sinkronkan juga baris cadangan jika rotation_interval berubah
                    if (updatedFields.rotation_interval) {
                        fetch(`${SUPABASE_CONFIG.url}/rest/v1/app_settings?id=gt.1`, {
                            method: 'PATCH',
                            headers: headers,
                            body: JSON.stringify({ rotation_interval: updatedFields.rotation_interval })
                        }).catch(() => {});
                    }

                    // Kirim sinyal broadcast remote UPDATE_SETTINGS agar display TV langsung adopsi tanpa jeda
                    if (typeof this.broadcastChange === 'function') {
                        this.broadcastChange('app_settings', 'UPDATE', updatedFields);
                    } else if (typeof this.sendRemoteCommand === 'function') {
                        this.sendRemoteCommand('UPDATE_SETTINGS', updatedFields).catch(() => {});
                    }

                    console.log('✅ [SupabaseDB] Sukses sinkronisasi settings ke cloud:', updatedFields);
                    return { success: true };
                }
            } catch (err) {
                console.warn('⚠️ [SupabaseDB] Jaringan error saat saveSettings:', err);
            }

            return { success: false };
        },

        /**
         * Ambil jadwal sholat dari tabel jadwal_sholat
         */
        async getJadwalSholat() {
            try {
                const client = getClient();
                if (client) {
                    const { data, error } = await client.from('jadwal_sholat').select('*').order('id', { ascending: true });
                    if (!error && data && data.length > 0) {
                        localStorage.setItem('cached_jadwal_sholat', JSON.stringify(data));
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
                    return rows;
                }
            } catch (restErr) {
                console.warn('REST fallback jadwal_sholat:', restErr);
            }

            const cached = localStorage.getItem('cached_jadwal_sholat');
            if (cached) {
                try { return JSON.parse(cached); } catch (e) {}
            }
            return [];
        },

        /**
         * Ambil daftar pengumuman
         */
        async getPengumuman() {
            try {
                const client = getClient();
                if (client) {
                    const { data, error } = await client.from('pengumuman').select('*').order('id', { ascending: false });
                    if (!error && data) {
                        localStorage.setItem('cached_pengumuman', JSON.stringify(data));
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
                    return rows;
                }
            } catch (restErr) {
                console.warn('REST fallback pengumuman:', restErr);
            }
            const cached = localStorage.getItem('cached_pengumuman');
            return cached ? JSON.parse(cached) : [];
        },

        /**
         * Ambil petugas sholat Jumat terbaru
         */
        async getSholatJumat() {
            try {
                const client = getClient();
                if (client) {
                    const { data, error } = await client.from('sholat_jumat').select('*').order('tanggal', { ascending: false }).order('id', { ascending: false }).limit(1);
                    if (!error && data && data.length > 0) {
                        localStorage.setItem('cached_sholat_jumat', JSON.stringify(data[0]));
                        return data[0];
                    }
                }
            } catch (err) {
                console.warn('Gagal membaca sholat_jumat SDK:', err);
            }
            // Direct REST API Fallback (Bypass SDK jika CDN belum siap / offline)
            try {
                const rows = await this.restFetch('sholat_jumat?order=tanggal.desc,id.desc&limit=1');
                if (Array.isArray(rows) && rows.length > 0) {
                    localStorage.setItem('cached_sholat_jumat', JSON.stringify(rows[0]));
                    return rows[0];
                }
            } catch (restErr) {
                console.warn('Gagal membaca sholat_jumat via REST:', restErr);
            }
            const cached = localStorage.getItem('cached_sholat_jumat');
            return cached ? JSON.parse(cached) : null;
        },

        /**
         * Ambil data transaksi keuangan kas masjid
         */
        async getKeuangan() {
            try {
                const client = getClient();
                if (client) {
                    const { data, error } = await client.from('keuangan').select('*').order('tanggal', { ascending: false });
                    if (!error && data) {
                        localStorage.setItem('cached_keuangan', JSON.stringify(data));
                        return data;
                    }
                }
            } catch (err) {
                console.warn('Gagal membaca keuangan SDK:', err);
            }
            // Direct REST API Fallback
            try {
                const rows = await this.restFetch('keuangan?order=tanggal.desc');
                if (Array.isArray(rows)) {
                    localStorage.setItem('cached_keuangan', JSON.stringify(rows));
                    return rows;
                }
            } catch (restErr) {
                console.warn('REST fallback keuangan:', restErr);
            }
            const cached = localStorage.getItem('cached_keuangan');
            return cached ? JSON.parse(cached) : [];
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
        async getKeuanganKasUtama() {
            const all = await this.getKeuangan();
            return (all || []).filter(item => this.isKasUtamaMasjid(item));
        },

        /**
         * Ambil data transaksi kas mobil ambulance
         */
        async getKeuanganAmbulance() {
            try {
                const client = getClient();
                if (client) {
                    const { data, error } = await client.from('keuangan_ambulance').select('*').order('tanggal', { ascending: false });
                    if (!error && data) {
                        localStorage.setItem('cached_keuangan_ambulance', JSON.stringify(data));
                        return data;
                    }
                }
            } catch (err) {
                console.warn('Gagal membaca keuangan_ambulance:', err);
            }
            const cached = localStorage.getItem('cached_keuangan_ambulance');
            return cached ? JSON.parse(cached) : [];
        },

        /**
         * Ambil program infaq & target donasi
         */
        async getProgramInfaq() {
            try {
                const client = getClient();
                if (client) {
                    const { data, error } = await client.from('program_infaq').select('*').order('id', { ascending: false });
                    if (!error && data) {
                        localStorage.setItem('cached_program_infaq', JSON.stringify(data));
                        return data;
                    }
                }
            } catch (err) {
                console.warn('Gagal membaca program_infaq:', err);
            }
            const cached = localStorage.getItem('cached_program_infaq');
            return cached ? JSON.parse(cached) : [];
        },

        /**
         * Ambil daftar donasi infaq per program atau keseluruhan
         */
        async getDonasiInfaq(programId = null) {
            try {
                const client = getClient();
                if (client) {
                    let query = client.from('donasi_infaq').select('*').order('tanggal', { ascending: true }).order('id', { ascending: true });
                    if (programId) query = query.eq('program_infaq_id', programId);
                    const { data, error } = await query;
                    if (!error && data) {
                        return data;
                    }
                }
            } catch (err) {
                console.warn('Gagal membaca donasi_infaq:', err);
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
        async getIdulFitri() {
            try {
                const client = getClient();
                if (client) {
                    const { data, error } = await client.from('sholat_idul_fitri').select('*').order('id', { ascending: false }).limit(1);
                    if (!error && data && data.length > 0) {
                        localStorage.setItem('cached_idul_fitri', JSON.stringify(data[0]));
                        return data[0];
                    }
                }
            } catch (err) {
                console.warn('Gagal membaca sholat_idul_fitri:', err);
            }
            const cached = localStorage.getItem('cached_idul_fitri');
            return cached ? JSON.parse(cached) : null;
        },

        /**
         * Ambil jadwal Sholat Idul Adha terbaru
         */
        async getIdulAdha() {
            try {
                const client = getClient();
                if (client) {
                    const { data, error } = await client.from('sholat_idul_adha').select('*').order('id', { ascending: false }).limit(1);
                    if (!error && data && data.length > 0) {
                        localStorage.setItem('cached_idul_adha', JSON.stringify(data[0]));
                        return data[0];
                    }
                }
            } catch (err) {
                console.warn('Gagal membaca sholat_idul_adha:', err);
            }
            const cached = localStorage.getItem('cached_idul_adha');
            return cached ? JSON.parse(cached) : null;
        },

        /**
        /**
         * Ambil daftar galeri informasi masjid
         */
        async getSlides() {
            // 1. Coba baca dari cloud database Supabase
            try {
                const client = getClient();
                if (client) {
                    const { data, error } = await client.from('slides').select('*').order('urutan', { ascending: true });
                    if (!error && Array.isArray(data) && data.length > 0) {
                        try {
                            localStorage.setItem('aljihad_galeri_informasi', JSON.stringify(data));
                            localStorage.setItem('cached_slides', JSON.stringify(data));
                        } catch (e) {}
                        return data;
                    }
                }
            } catch (err) {
                console.warn('Gagal membaca slides dari Supabase:', err);
            }

            // 2. Coba baca dari localStorage lokal
            try {
                const local = localStorage.getItem('aljihad_galeri_informasi');
                if (local) {
                    const parsed = JSON.parse(local);
                    if (Array.isArray(parsed) && parsed.length > 0) {
                        return parsed;
                    }
                }
            } catch (e) {}

            // 3. Default fallback 3 arsip dokumentasi & sertifikasi resmi Masjid Al-Jihad
            const defaultSlides = [
                {
                    id: 1,
                    judul: 'Arah Qiblat',
                    kategori: 'Pengukuran & Validasi',
                    deskripsi: 'Hasil pengecekkan arah qiblat pada hari Kamis, 16 Juli 2026 Jam: 16:27 WIB di Masjid Jami\' Al-Jihad.',
                    gambar: 'image/slides/1gdpqFYCyv7Sv0qLDTpyxSjMnknbVEM9OLVOjPM3.png',
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

            try {
                localStorage.setItem('aljihad_galeri_informasi', JSON.stringify(defaultSlides));
            } catch (e) {}

            return defaultSlides;
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
                    const cleanSlides = slidesList.map(s => {
                        let imgVal = s.gambar || '';
                        let b64Val = s.gambar_base64 || null;

                        // Jika imgVal adalah string data URI base64 atau melebihi 250 karakter
                        if (imgVal.startsWith('data:image/') || imgVal.length > 250) {
                            b64Val = imgVal;
                            imgVal = 'image/slides/custom_uploaded.png';
                        }

                        return {
                            id: s.id,
                            judul: s.judul || '',
                            deskripsi: s.deskripsi || '',
                            gambar: imgVal.substring(0, 250),
                            gambar_base64: b64Val,
                            urutan: parseInt(s.urutan, 10) || 1,
                            durasi: parseInt(s.durasi, 10) || 10,
                            aktif: s.aktif !== false,
                            updated_at: new Date().toISOString()
                        };
                    });

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
         */
        subscribeRealtime(onUpdate) {
            const client = getClient();
            if (!client || !client.channel) return null;

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
                    }
                });

            return channel;
        },

        /**
         * =====================================================
         * MODUL LAYANAN REMOTE TV & DIAGNOSTIK JARAK JAUH
         * =====================================================
         */
        _remoteChannel: null,

        getRemoteChannel() {
            if (!this._remoteChannel) {
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
        subscribeRemoteCommands(onCommand) {
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
            const timestamp = Date.now();
            const payload = {
                table: table,
                action: action,
                data: data,
                sender: sender,
                timestamp: timestamp
            };

            console.log(`📡 [Universal Sync] Memancarkan pembaruan realtime [${table} - ${action}]:`, payload);

            // Track 1: WebSocket Broadcast ke seluruh TV via Remote Channel Supabase (<100ms ultra-cepat)
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

            // Track 5: PostMessage ke window induk (jika dijalankan dari iframe admin / slide)
            try {
                if (typeof window !== 'undefined' && window.parent && window.parent !== window) {
                    window.parent.postMessage({ type: 'DATA_UPDATED', ...payload }, '*');
                }
            } catch (e) {}
        },

        /**
         * Pasang pendengar pembaruan data universal (Universal Real-Time Listener)
         * Mengikat 5 saluran redundan sekaligus:
         * 1. Supabase Postgres WAL Changes
         * 2. Supabase Realtime WebSocket Remote Broadcast (<100ms)
         * 3. Local BroadcastChannel
         * 4. Window postMessage (Parent Display TV <-> Iframe)
         * 5. Window storage sync event
         * @param {Function} callback - function({ table, action, data, sender, source, timestamp })
         */
        onDataChange(callback) {
            if (typeof callback !== 'function') return null;

            // Debounce pelindung agar jika 5 saluran menyala bersamaan, callback hanya dipanggil 1x per batch
            let debounceTimer = null;
            let lastEventSignature = '';
            const safeTrigger = (evt) => {
                const sig = `${evt.table}_${evt.action}_${evt.timestamp || Date.now()}`;
                if (sig === lastEventSignature) return;
                lastEventSignature = sig;

                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    try {
                        callback(evt);
                    } catch (err) {
                        console.error('[SupabaseDB.onDataChange] Callback error:', err);
                    }
                }, 35); // 35ms debounce super responsif
            };

            // 1. Supabase Realtime Postgres Changes
            try {
                this.subscribeRealtime((payload) => {
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
                            'SYNC_SLIDES': 'slides',
                            'SYNC_QRIS': 'qris',
                            'SYNC_QURBAN': 'qurban',
                            'SYNC_UNDANGAN': 'undangan',
                            'SYNC_RUNNING_TEXT': 'running_text'
                        };
                        safeTrigger({
                            table: tableMap[cmd] || cmd.toLowerCase(),
                            action: 'SYNC',
                            data: d,
                            sender: cmdPayload?.sender || 'Remote Admin',
                            source: 'remote_sync_command',
                            timestamp: Date.now()
                        });
                    } else if (cmd === 'UPDATE_SETTINGS') {
                        safeTrigger({
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

            // 3. Local BroadcastChannel
            try {
                const bc = this.getLocalBroadcastChannel();
                if (bc) {
                    bc.addEventListener('message', (event) => {
                        if (event.data && (event.data.type === 'DATA_UPDATED' || event.data.table)) {
                            safeTrigger({
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

            // 4. Window postMessage (dari Parent TV atau iframe)
            try {
                if (typeof window !== 'undefined') {
                    window.addEventListener('message', (event) => {
                        if (!event.data) return;
                        if (event.data.type === 'DATA_UPDATED') {
                            safeTrigger({
                                table: event.data.table,
                                action: event.data.action || 'UPDATE',
                                data: event.data.data || event.data.payload || {},
                                source: 'window_message',
                                timestamp: Date.now()
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
        async trackDevicePresence(deviceInfo) {
            const channel = this.getRemoteChannel();
            if (!channel) return;

            const payload = Object.assign({
                type: 'tv-display',
                device_id: 'TV_MASJID_UTAMA',
                device_name: 'TV Layar Utama Masjid Jami\' Al-Jihad',
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
        async getUndanganEksternal() {
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
                console.warn('Gagal membaca undangan_eksternal dari Supabase:', err);
            }

            // 2. Coba baca dari localStorage lokal
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
            if (!Array.isArray(undanganList)) return false;

            // 1. Simpan ke local cache seketika (offline-ready)
            try {
                localStorage.setItem('aljihad_undangan_eksternal', JSON.stringify(undanganList));
                localStorage.setItem('cached_undangan_eksternal', JSON.stringify(undanganList));
            } catch (e) {}

            // 2. Coba simpan ke Supabase jika tabel tersedia
            try {
                const client = getClient();
                if (client) {
                    for (const item of undanganList) {
                        const payload = {
                            nama_pengundang: item.nama_pengundang,
                            nama_acara: item.nama_acara,
                            penceramah: item.penceramah || '',
                            tanggal_acara: item.tanggal_acara || '',
                            waktu_acara: item.waktu_acara || '',
                            tempat_acara: item.tempat_acara || '',
                            keterangan: item.keterangan || '',
                            is_active: item.is_active !== false,
                            urutan: parseInt(item.urutan) || 1
                        };

                        if (item.id && typeof item.id === 'number') {
                            await client.from('undangan_eksternal').upsert({ id: item.id, ...payload });
                        } else {
                            await client.from('undangan_eksternal').insert([payload]);
                        }
                    }
                }
            } catch (err) {
                console.warn('Gagal sinkronisasi undangan_eksternal ke Supabase:', err);
            }

            if (typeof this.broadcastChange === 'function') {
                this.broadcastChange('undangan', 'UPDATE', { list: undanganList }, 'Admin Undangan');
            }

            return true;
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
                    'nu_last_sync_date'
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
