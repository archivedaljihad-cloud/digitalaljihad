// web-statis/js/supabase-db.js
/**
 * Supabase DB Client & Realtime Data Manager untuk Display Masjid
 */

(function (window) {
    let sbClient = null;

    function getClient() {
        if (!sbClient) {
            if (typeof window.supabase === 'undefined' || !window.supabase.createClient) {
                console.warn('Supabase JS SDK belum dimuat. Menggunakan fallback lokal.');
                return null;
            }
            sbClient = window.supabase.createClient(SUPABASE_CONFIG.url, SUPABASE_CONFIG.anonKey);
        }
        return sbClient;
    }

    const SupabaseDB = {
        // Fallback default settings jika offline atau database belum terhubung
        defaultSettings: {
            nama_aplikasi: 'MASJID JAMI\' AL-JIHAD',
            sub_header: 'Graha Asri, Cikarang Utara, Bekasi',
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
            running_text: 'Selamat Datang di Masjid Jami\' Al-Jihad. Luruskan dan rapatkan shaf saat sholat berjamaah. Jagalah kebersihan dan kesucian masjid.',
            running_text_pages: {},
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
                { page: '/ramadhan-embed', path: 'slides/ramadhan.html', name: 'Semarak Ramadhan & Kas Tromol', active: true, order: 18 }
            ]
        },

        /**
         * Ambil pengaturan umum (app_settings)
         */
        async getSettings() {
            try {
                const client = getClient();
                if (client) {
                    const { data, error } = await client.from('app_settings').select('*').order('id', { ascending: true }).limit(5);
                    if (!error && data && data.length > 0) {
                        // Prioritaskan baris id 1 jika ada
                        const row = data.find(r => r.id === 1) || data[0];
                        const settings = Object.assign({}, this.defaultSettings, row);
                        
                        // Parse JSON fields jika bertipe string
                        if (typeof settings.rotation_pages === 'string') {
                            try { settings.rotation_pages = JSON.parse(settings.rotation_pages); } catch (e) {}
                        }
                        if (typeof settings.running_text_pages === 'string') {
                            try { settings.running_text_pages = JSON.parse(settings.running_text_pages); } catch (e) {}
                        }

                        // Sanitasi nama masjid & sub header agar tidak ada nilai dummy lama
                        if (!settings.nama_aplikasi || settings.nama_aplikasi.trim().toUpperCase() === 'DISPLAY MASJID' || settings.nama_aplikasi.trim().toUpperCase() === 'NAMA MASJID') {
                            settings.nama_aplikasi = 'MASJID JAMI\' AL-JIHAD';
                        }
                        if (!settings.sub_header || settings.sub_header.includes('Kebon Jeruk') || settings.sub_header.includes('Melati')) {
                            settings.sub_header = 'Graha Asri, Cikarang Utara, Bekasi';
                        }

                        // Simpan ke cache lokal browser
                        localStorage.setItem('cached_app_settings', JSON.stringify(settings));
                        return settings;
                    }
                }
            } catch (err) {
                console.warn('Gagal membaca app_settings dari Supabase:', err);
            }

            // Fallback ke localStorage atau default
            const cached = localStorage.getItem('cached_app_settings');
            if (cached) {
                try {
                    const parsed = JSON.parse(cached);
                    // Sanitasi cache browser lama
                    if (!parsed.nama_aplikasi || parsed.nama_aplikasi.trim().toUpperCase() === 'DISPLAY MASJID' || parsed.nama_aplikasi.trim().toUpperCase() === 'NAMA MASJID') {
                        parsed.nama_aplikasi = 'MASJID JAMI\' AL-JIHAD';
                    }
                    if (!parsed.sub_header || parsed.sub_header.includes('Kebon Jeruk') || parsed.sub_header.includes('Melati')) {
                        parsed.sub_header = 'Graha Asri, Cikarang Utara, Bekasi';
                    }
                    localStorage.setItem('cached_app_settings', JSON.stringify(parsed));
                    return parsed;
                } catch (e) {}
            }
            return this.defaultSettings;
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
                console.warn('Gagal membaca jadwal_sholat dari Supabase:', err);
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
                console.warn('Gagal membaca pengumuman:', err);
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
                    const { data, error } = await client.from('sholat_jumat').select('*').order('id', { ascending: false }).limit(1);
                    if (!error && data && data.length > 0) {
                        localStorage.setItem('cached_sholat_jumat', JSON.stringify(data[0]));
                        return data[0];
                    }
                }
            } catch (err) {
                console.warn('Gagal membaca sholat_jumat:', err);
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
                console.warn('Gagal membaca keuangan:', err);
            }
            const cached = localStorage.getItem('cached_keuangan');
            return cached ? JSON.parse(cached) : [];
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
         * Ambil poster slide informasi masjid
         */
        async getSlides() {
            try {
                const client = getClient();
                if (client) {
                    const { data, error } = await client.from('slides').select('*').order('urutan', { ascending: true });
                    if (!error && data) {
                        localStorage.setItem('cached_slides', JSON.stringify(data));
                        return data;
                    }
                }
            } catch (err) {
                console.warn('Gagal membaca slides:', err);
            }
            const cached = localStorage.getItem('cached_slides');
            return cached ? JSON.parse(cached) : [];
        },

        /**
         * Ambil teks berjalan untuk halaman tertentu (Opsi 3 Multi-Halaman)
         */
        getRunningTextForPage(pagePath, settings) {
            if (!settings) return '';
            const pathClean = pagePath.replace(/^\/+/, '').replace(/\.html$/, '');
            const pagesMapping = settings.running_text_pages || {};

            // Cek variasi kunci URL
            const candidates = [
                pagePath,
                '/' + pathClean,
                pathClean,
                pathClean + '-embed',
                '/' + pathClean + '-embed'
            ];

            for (const key of candidates) {
                if (pagesMapping[key] && pagesMapping[key].trim() !== '') {
                    return pagesMapping[key].trim();
                }
            }

            return (settings.running_text || '').trim();
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
        }
    };

    window.SupabaseDB = SupabaseDB;
})(window);
