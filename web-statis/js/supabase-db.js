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
         * Simpan / Perbarui app_settings ke Cloud Supabase (id=1)
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
            } catch (e) {
                console.warn('[SupabaseDB] Gagal update cached_app_settings lokal:', e);
            }

            // 2. Kirim update ke Cloud Supabase (id=1)
            try {
                if (SUPABASE_CONFIG && SUPABASE_CONFIG.url) {
                    const headers = {
                        'apikey': SUPABASE_CONFIG.anonKey,
                        'Authorization': `Bearer ${SUPABASE_CONFIG.anonKey}`,
                        'Content-Type': 'application/json',
                        'Prefer': 'return=representation'
                    };

                    const res = await fetch(`${SUPABASE_CONFIG.url}/rest/v1/app_settings?id=eq.1`, {
                        method: 'PATCH',
                        headers: headers,
                        body: JSON.stringify(updatedFields)
                    });

                    if (res.ok) {
                        const data = await res.json().catch(() => null);
                        console.log('✅ [SupabaseDB] Sukses sinkronisasi settings ke cloud:', updatedFields);
                        return { success: true, data };
                    } else {
                        const errText = await res.text().catch(() => '');
                        console.warn('⚠️ [SupabaseDB] Gagal update app_settings cloud:', res.status, errText);
                    }
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
                    console.log('🎮 [TV Receiver] Menerima sinyal remote:', payload.command, payload);
                    if (typeof onCommand === 'function') {
                        onCommand(payload);
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
        }
    };

    window.SupabaseDB = SupabaseDB;
})(window);
