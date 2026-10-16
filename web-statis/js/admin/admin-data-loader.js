/**
 * MODUL ADMIN: admin-data-loader.js
 * Digital Signage Masjid Jami' Al-Jihad
 * Versi: aljihad-signage-v5.8.46
 */

        // 6. LIVE DATA LOADER DARI SUPABASE (ANTI-FREEZE & PROTEKSI TITIK MACET)
        // =======================================================
        let _isLoadingAllData = false;
        let _pendingLoadAllData = false;
        let _debounceLoadTimer = null;

        function debouncedLoadAllSupabaseData(delayMs = 600) {
            if (_debounceLoadTimer) clearTimeout(_debounceLoadTimer);
            _debounceLoadTimer = setTimeout(() => {
                loadAllSupabaseData();
            }, delayMs);
        }

        async function loadAllSupabaseData() {
            if (typeof SUPABASE_CONFIG === 'undefined' || !SUPABASE_CONFIG.url) return;
            if (_isLoadingAllData) {
                _pendingLoadAllData = true;
                return;
            }
            _isLoadingAllData = true;
            try {
                const headers = getSupabaseHeaders();

                // Eksekusi paralel 10 query ke Supabase (memangkas latensi serial dari ~3500ms menjadi ~350ms)
                const [
                    resKasSettled,
                    resAmbSettled,
                    resJumatSettled,
                    resSholatSettled,
                    resSetSettled,
                    resUsersSettled,
                    resInfaqSettled,
                    resDonasiSettled,
                    resQrisSettled,
                    resSlidesSettled
                ] = await Promise.allSettled([
                    fetch(`${SUPABASE_CONFIG.url}/rest/v1/keuangan?order=tanggal.desc,id.desc`, { headers }),
                    fetch(`${SUPABASE_CONFIG.url}/rest/v1/keuangan_ambulance?order=tanggal.desc,id.desc`, { headers }),
                    fetch(`${SUPABASE_CONFIG.url}/rest/v1/sholat_jumat?order=tanggal.desc,id.desc&limit=1`, { headers }),
                    fetch(`${SUPABASE_CONFIG.url}/rest/v1/jadwal_sholat?order=id.asc`, { headers }),
                    fetch(`${SUPABASE_CONFIG.url}/rest/v1/app_settings?id=eq.1`, { headers }),
                    fetch(`${SUPABASE_CONFIG.url}/rest/v1/users?select=id,name,email,role_id`, { headers }),
                    fetch(`${SUPABASE_CONFIG.url}/rest/v1/program_infaq?order=id.desc`, { headers }),
                    fetch(`${SUPABASE_CONFIG.url}/rest/v1/donasi_infaq?order=tanggal.asc,id.asc`, { headers }),
                    fetch(`${SUPABASE_CONFIG.url}/rest/v1/qris?order=id.asc`, { headers }),
                    fetch(`${SUPABASE_CONFIG.url}/rest/v1/slides?order=urutan.asc`, { headers })
                ]);

                // A. Kas Utama Masjid
                try {
                    if (resKasSettled.status === 'fulfilled' && resKasSettled.value.ok) {
                        const rawKas = await resKasSettled.value.json();
                        // FILTER KETAT: Jaga independensi Kas Utama Masjid dari pos penggalangan infaq / donasi program lain
                        cachedKasData = (rawKas || []).filter(item => {
                            if (!item) return false;
                            const kat = (item.kategori || '').toLowerCase().trim();
                            const bukanKas = ['penggalangan', 'program infaq', 'infaq program', 'infaq renovasi', 'renovasi', 'ambulance', 'ambulans', 'qurban', 'ramadhan', 'donasi infaq', 'donatur'];
                            for (const b of bukanKas) {
                                if (kat.includes(b)) return false;
                            }
                            return true;
                        });
                        renderKasTables(cachedKasData);
                    }
                } catch (err) {
                    console.warn('Gagal memuat kas utama dari Supabase:', err);
                }

                // Beri napas ke message pump browser (mencegah Windows mendeteksi Not Responding)
                await new Promise(r => setTimeout(r, 0));

            // B. Kas Mobil Ambulance
            try {
                if (resAmbSettled.status === 'fulfilled' && resAmbSettled.value.ok) {
                    cachedAmbData = await resAmbSettled.value.json();
                    renderAmbulanceData(cachedAmbData);
                }
            } catch (err) {
                console.warn('Gagal memuat kas ambulance dari Supabase:', err);
            }

            // C. Petugas Sholat Jum'at Terkini
            try {
                if (window.SupabaseDB || (resJumatSettled.status === 'fulfilled' && resJumatSettled.value.ok)) {
                    const jumatList = window.SupabaseDB ? [await SupabaseDB.getSholatJumat()].filter(Boolean) : await resJumatSettled.value.json();
                    if (jumatList && jumatList.length > 0) {
                        const j = jumatList[0];
                        const namaKhotibImam = (j.khatib && j.imam && j.khatib !== j.imam) 
                            ? `${j.khatib} / ${j.imam}` 
                            : (j.khatib || j.imam || '');

                        if (document.getElementById('jumatTanggal')) document.getElementById('jumatTanggal').value = j.tanggal || '';
                        if (document.getElementById('jumatKhotib')) document.getElementById('jumatKhotib').value = j.khatib || j.imam || '';
                        if (document.getElementById('jumatImam')) document.getElementById('jumatImam').value = j.imam || j.khatib || '';
                        let bilalVal = '';
                        let maklumatVal = '';

                        // 1. Ekstrak dari j.bilal jika menggunakan format terpadu "Nama Bilal / Nama Maklumat"
                        const rawBilal = (j.bilal || '').trim();
                        if (rawBilal.includes('/')) {
                            const bParts = rawBilal.split('/').map(s => s.trim());
                            bilalVal = bParts[0] || '';
                            maklumatVal = bParts[1] || '';
                        } else {
                            bilalVal = rawBilal;
                        }

                        // 2. Baca dari app_settings (running_text_pages) jika ada nilai tersimpan
                        try {
                            if (resSetSettled.status === 'fulfilled' && resSetSettled.value.ok) {
                                const sList = await resSetSettled.value.clone().json();
                                if (sList && sList[0] && sList[0].running_text_pages) {
                                    const rt = sList[0].running_text_pages;
                                    if (rt.jumat_maklumat) maklumatVal = rt.jumat_maklumat;
                                    if (rt.jumat_bilal) bilalVal = rt.jumat_bilal;
                                }
                            }
                        } catch (e) {}

                        // 3. Fallback cache localStorage tersimpan
                        if (!maklumatVal) {
                            try { maklumatVal = localStorage.getItem('cached_jumat_maklumat') || ''; } catch (e) {}
                        }
                        if (!bilalVal) {
                            try { bilalVal = localStorage.getItem('cached_jumat_bilal') || ''; } catch (e) {}
                        }

                        // 4. Default nama resmi jika kosong
                        if (!bilalVal) bilalVal = 'Ust. Mansur';
                        if (!maklumatVal) maklumatVal = 'Ust. Fatkhurokhman';

                        // 5. PROTEKSI KETAT: BEDAKAN PETUGAS BILAL DAN PEMBACA MAKLUMAT
                        // Jangan biarkan bilal tertimpa menjadi sama dengan maklumat akibat riwayat data duplikat lama
                        if (bilalVal.toLowerCase() === maklumatVal.toLowerCase()) {
                            try {
                                const cachedB = localStorage.getItem('cached_jumat_bilal');
                                if (cachedB && cachedB.toLowerCase() !== maklumatVal.toLowerCase()) {
                                    bilalVal = cachedB;
                                } else {
                                    bilalVal = 'Ust. Mansur';
                                }
                            } catch (e) {
                                bilalVal = 'Ust. Mansur';
                            }
                        }

                        if (document.getElementById('jumatMuadzin2')) document.getElementById('jumatMuadzin2').value = j.muadzin2 || '';
                        if (document.getElementById('jumatWaktu')) document.getElementById('jumatWaktu').value = j.waktu || '11:45';
                        if (document.getElementById('jumatMuadzin')) document.getElementById('jumatMuadzin').value = j.muadzin || '';
                        if (document.getElementById('jumatBilal')) document.getElementById('jumatBilal').value = bilalVal;
                        if (document.getElementById('jumatMaklumat')) document.getElementById('jumatMaklumat').value = maklumatVal;

                        // 6. Muat Foto Imam & Khotib (Display TV)
                        let fotoImamSrc = (j.foto_imam || '').trim();
                        try {
                            if (resSetSettled.status === 'fulfilled' && resSetSettled.value.ok) {
                                const sList = await resSetSettled.value.clone().json();
                                if (sList && sList[0] && sList[0].running_text_pages && sList[0].running_text_pages.jumat_foto_imam_b64) {
                                    fotoImamSrc = sList[0].running_text_pages.jumat_foto_imam_b64;
                                }
                            }
                        } catch (e) {}

                        if (!fotoImamSrc || fotoImamSrc === 'custom_uploaded') {
                            try {
                                const cachedF = localStorage.getItem('cached_jumat_foto_imam');
                                if (cachedF) fotoImamSrc = cachedF;
                            } catch (e) {}
                        }

                        if (!fotoImamSrc || fotoImamSrc === 'custom_uploaded') {
                            fotoImamSrc = 'image/display/default_imam.jpg';
                        }

                        if (document.getElementById('jumatFotoPreviewImg')) document.getElementById('jumatFotoPreviewImg').src = fotoImamSrc;
                        if (document.getElementById('prevJumatImamPhoto')) document.getElementById('prevJumatImamPhoto').src = fotoImamSrc;
                        if (document.getElementById('jumatFotoUrlInput') && j.foto_imam && j.foto_imam !== 'custom_uploaded' && !j.foto_imam.startsWith('data:')) {
                            document.getElementById('jumatFotoUrlInput').value = j.foto_imam;
                        }

                        // Update Dashboard & Preview
                        if (document.getElementById('statKhotibJumat')) document.getElementById('statKhotibJumat').textContent = namaKhotibImam || '-';
                        if (document.getElementById('dashJumatKhotib')) document.getElementById('dashJumatKhotib').textContent = namaKhotibImam || '-';
                        if (document.getElementById('dashJumatImam')) document.getElementById('dashJumatImam').textContent = namaKhotibImam || '-';
                        if (document.getElementById('dashJumatMuadzin')) document.getElementById('dashJumatMuadzin').textContent = j.muadzin || '-';
                        if (document.getElementById('dashJumatBilal')) document.getElementById('dashJumatBilal').textContent = bilalVal || '-';
                        if (document.getElementById('dashJumatMaklumat')) document.getElementById('dashJumatMaklumat').textContent = maklumatVal || '-';
                        if (document.getElementById('prevJumatKhotib')) document.getElementById('prevJumatKhotib').textContent = namaKhotibImam || '-';
                        if (document.getElementById('prevJumatImam')) document.getElementById('prevJumatImam').textContent = namaKhotibImam || '-';
                        if (document.getElementById('prevJumatMuadzin')) document.getElementById('prevJumatMuadzin').textContent = j.muadzin || '-';
                        if (document.getElementById('prevJumatBilal')) document.getElementById('prevJumatBilal').textContent = bilalVal || '-';
                        if (document.getElementById('prevJumatMaklumat')) document.getElementById('prevJumatMaklumat').textContent = maklumatVal || '-';
                        if (document.getElementById('prevJumatTanggal') && j.tanggal) {
                            const d = new Date(j.tanggal);
                            document.getElementById('prevJumatTanggal').textContent = d.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
                        }
                    }
                }
            } catch (err) {
                console.warn('Gagal memuat petugas jumat:', err);
            }

            // D. Jadwal Sholat
            try {
                if (resSholatSettled.status === 'fulfilled' && resSholatSettled.value.ok) {
                    const sholatList = await resSholatSettled.value.json();
                    sholatList.forEach(s => {
                        const name = (s.nama_sholat || s.nama || '').toLowerCase();
                        const timeVal = s.waktu ? s.waktu.substring(0, 5) : (s.jam || '');
                        const setVal = typeof setPrayerInputValue === 'function' ? setPrayerInputValue : (id, v) => {
                            const el = document.getElementById(id);
                            if (el) el.value = v;
                        };
                        if (name.includes('imsak')) setVal('sholatImsak', timeVal);
                        if (name.includes('subuh')) setVal('sholatSubuh', timeVal);
                        if (name.includes('syuru') || name.includes('terbit')) setVal('sholatTerbit', timeVal);
                        if (name.includes('dzuhur')) setVal('sholatDzuhur', timeVal);
                        if (name.includes('ashar')) setVal('sholatAshar', timeVal);
                        if (name.includes('maghrib')) setVal('sholatMaghrib', timeVal);
                        if (name.includes('isya')) setVal('sholatIsya', timeVal);
                    });
                }

                // Status switch toggle Imsak & Syuruk
                const showImsak = localStorage.getItem('display_show_imsak') !== 'false';
                const showSyuruk = localStorage.getItem('display_show_syuruk') !== 'false';
                if (document.getElementById('cfgShowImsak')) document.getElementById('cfgShowImsak').checked = showImsak;
                if (document.getElementById('cfgShowSyuruk')) document.getElementById('cfgShowSyuruk').checked = showSyuruk;

                // Konfigurasi Sinkronisasi Jadwal Sholat (Kemenag & Falakiyah NU)
                const savedSource = localStorage.getItem('sholat_source') || 'kemenag';
                if (document.getElementById('selPrayerSource')) document.getElementById('selPrayerSource').value = savedSource;
                if (typeof updatePrayerSourceBadgeUI === 'function') updatePrayerSourceBadgeUI(savedSource);

                const savedCityId = localStorage.getItem('kemenag_city_id') || '1203';
                const savedCityName = localStorage.getItem('kemenag_city_name') || 'Kab. Bekasi';
                if (document.getElementById('selKemenagCity')) document.getElementById('selKemenagCity').value = savedCityId;
                if (document.getElementById('badgeKemenagCity')) document.getElementById('badgeKemenagCity').textContent = savedCityName;
                const autoSync = localStorage.getItem('kemenag_auto_sync') !== 'false';
                if (document.getElementById('cfgAutoSyncKemenag')) document.getElementById('cfgAutoSyncKemenag').checked = autoSync;
                
                const lastDate = localStorage.getItem(savedSource === 'nu' ? 'nu_last_sync_date' : 'kemenag_last_sync_date') || localStorage.getItem('kemenag_last_sync_date');
                if (lastDate && document.getElementById('infoKemenagTanggal')) {
                    const srcLabel = savedSource === 'nu' ? 'Falakiyah NU' : 'Kemenag';
                    document.getElementById('infoKemenagTanggal').textContent = `Sinkronisasi ${srcLabel}: ${lastDate} (Terkini)`;
                }
            } catch (err) {
                console.warn('Gagal memuat jadwal sholat:', err);
            }

            // E. App Settings (Durasi, Running Text, Rotasi TV)
            try {
                if (resSetSettled.status === 'fulfilled' && resSetSettled.value.ok) {
                    const settingsList = await resSetSettled.value.json();
                    if (settingsList && settingsList.length > 0) {
                        const s = settingsList[0];
                        if (document.getElementById('cfgNamaMasjid') && s.nama_aplikasi) document.getElementById('cfgNamaMasjid').value = s.nama_aplikasi;
                        if (s.rotation_interval) {
                            if (document.getElementById('cfgIntervalTV')) document.getElementById('cfgIntervalTV').value = s.rotation_interval;
                            if (document.getElementById('cfgIntervalTVSystem')) document.getElementById('cfgIntervalTVSystem').value = s.rotation_interval;
                            localStorage.setItem('display_rotation_interval', s.rotation_interval);
                        }
                        if (document.getElementById('cfgFooterMasjid')) {
                            document.getElementById('cfgFooterMasjid').value = s.footer || localStorage.getItem('app_footer_text') || "© 2026 MASJID JAMI' AL JIHAD (GRAHA ASRI). All Rights Reserved";
                        }
                        if (document.getElementById('cfgLiveMekah')) {
                            let valMekah = s.live_makkah_url || localStorage.getItem('live_makkah_url') || '';
                            if (valMekah.includes('live_stream?channel=')) valMekah = '';
                            document.getElementById('cfgLiveMekah').value = valMekah;
                        }
                        if (document.getElementById('cfgLiveMadinah')) {
                            let valMadinah = s.live_madinah_url || localStorage.getItem('live_madinah_url') || '';
                            if (valMadinah.includes('live_stream?channel=')) valMadinah = '';
                            document.getElementById('cfgLiveMadinah').value = valMadinah;
                        }
                        // Sinkronkan input streaming otomatis ke localStorage saat pengguna mengetik/menempelkan link
                        ['cfgLiveMekah', 'cfgLiveMadinah'].forEach(id => {
                            const inputEl = document.getElementById(id);
                            if (inputEl && !inputEl.dataset.autoSyncBound) {
                                inputEl.dataset.autoSyncBound = 'true';
                                inputEl.addEventListener('input', function() {
                                    let val = this.value.trim();
                                    // Auto-convert jika pengguna menempelkan link portal makkahlive.net
                                    if (val.includes('makkahlive.net')) {
                                        if (id === 'cfgLiveMadinah' || val.includes('madin') || val.includes('medin')) {
                                            val = 'https://www.youtube.com/watch?v=Rs7St51oDDc';
                                        } else {
                                            val = 'https://www.youtube.com/watch?v=eC4LfEVxvKg';
                                        }
                                        this.value = val;
                                    }
                                    const key = (id === 'cfgLiveMekah') ? 'live_makkah_url' : 'live_madinah_url';
                                    localStorage.setItem(key, val);
                                });
                            }
                        });
                        if (document.getElementById('cfgCctvMimbar') && s.cctv_mimbar_url) document.getElementById('cfgCctvMimbar').value = s.cctv_mimbar_url;
                        
                        // Google Gemini AI Settings
                        const activeGeminiKey = s.gemini_api_key || (window.GeminiAI ? window.GeminiAI.getApiKey() : '');
                        const activeGeminiModel = s.gemini_model || (window.GeminiAI ? window.GeminiAI.getModel() : 'gemini-1.5-flash');
                        if (document.getElementById('cfgGeminiApiKey')) document.getElementById('cfgGeminiApiKey').value = activeGeminiKey || '';
                        if (document.getElementById('cfgGeminiModel')) document.getElementById('cfgGeminiModel').value = activeGeminiModel;
                        if (window.GeminiAI) {
                            if (activeGeminiKey) window.GeminiAI.setApiKey(activeGeminiKey);
                            if (activeGeminiModel) window.GeminiAI.setModel(activeGeminiModel);
                        }
                        if (typeof updateGeminiStatusBadge === 'function') {
                            updateGeminiStatusBadge();
                        }

                        // Switch & Durasi Prayer Mode
                        const prayerEnabled = (s.prayer_mode_enabled !== false && s.value != '0');
                        if (document.getElementById('cfgPrayerModeEnabled')) document.getElementById('cfgPrayerModeEnabled').checked = prayerEnabled;
                        const cdVal = s.prayer_mode_before_adzan ?? s.countdown_adzan_duration ?? localStorage.getItem('cfg_countdown_adzan');
                        if (document.getElementById('cfgCountdownAdzan') && cdVal !== null && cdVal !== undefined) document.getElementById('cfgCountdownAdzan').value = cdVal;
                        const adzVal = s.prayer_mode_adzan_duration ?? s.adzan_duration ?? localStorage.getItem('cfg_durasi_adzan');
                        if (document.getElementById('cfgDurasiAdzan') && adzVal !== null && adzVal !== undefined) document.getElementById('cfgDurasiAdzan').value = adzVal;
                        const iqmVal = s.prayer_mode_iqamah_duration ?? s.iqamah_duration ?? localStorage.getItem('cfg_durasi_iqamah');
                        if (document.getElementById('cfgDurasiIqamah') && iqmVal !== null && iqmVal !== undefined) document.getElementById('cfgDurasiIqamah').value = iqmVal;
                        const sholatDurVal = s.prayer_mode_after_prayer ?? s.prayer_mode_duration ?? localStorage.getItem('cfg_durasi_sholat');
                        if (document.getElementById('cfgDurasiSholat') && sholatDurVal !== null && sholatDurVal !== undefined) document.getElementById('cfgDurasiSholat').value = sholatDurVal;
                        const audioTarhimVal = s.audio_tarhim !== undefined ? s.audio_tarhim : localStorage.getItem('cfg_audio_tarhim');
                        if (document.getElementById('cfgAudioTarhim') && audioTarhimVal !== null && audioTarhimVal !== undefined) document.getElementById('cfgAudioTarhim').value = audioTarhimVal;
                        const savedCountdownStyle = s.prayer_countdown_style || localStorage.getItem('prayer_countdown_style');
                        if (savedCountdownStyle && document.getElementById('cfgCountdownStyle')) {
                            document.getElementById('cfgCountdownStyle').value = savedCountdownStyle;
                        }
                        
                        // Durasi Sholat Jum'at (Khutbah & Sholat Berjamaah)
                        const jumatDur = parseInt(s.prayer_mode_jumat_duration || s.jumat_durasi || localStorage.getItem('cfg_durasi_jumat')) || 50;
                        syncDurasiJumat(jumatDur);

                        // Pengaturan Agenda Surat Yaasiin & Tahlil (Sync Dashboard & Settings)
                        const yasinEnabled = (s.yasin_mode_enabled !== undefined && s.yasin_mode_enabled !== null)
                            ? (s.yasin_mode_enabled !== false && s.yasin_mode_enabled !== 'false' && s.yasin_mode_enabled !== 0 && s.yasin_mode_enabled !== '0')
                            : (localStorage.getItem('yasin_mode_enabled') !== 'false');
                        if (document.getElementById('cfgYasinEnabled')) document.getElementById('cfgYasinEnabled').checked = yasinEnabled;
                        if (document.getElementById('dashYasinEnabled')) document.getElementById('dashYasinEnabled').checked = yasinEnabled;

                        // Baca jam mulai dari Supabase Cloud dengan fallback aman ke LocalStorage browser
                        const rawYTime = s.yasin_start_time || localStorage.getItem('yasin_start_time') || '18:30';
                        const yStartTimeFormatted = String(rawYTime).trim().substring(0, 5);
                        if (document.getElementById('cfgYasinStartTime')) document.getElementById('cfgYasinStartTime').value = yStartTimeFormatted;
                        if (document.getElementById('dashYasinStartTime')) document.getElementById('dashYasinStartTime').value = yStartTimeFormatted;
                        localStorage.setItem('yasin_start_time', yStartTimeFormatted);

                        let yMode = 'step';
                        let yStepDur = 20;
                        let yMushafDur = 120;

                        if (s.yasin_scroll_speed) {
                            try {
                                if (typeof s.yasin_scroll_speed === 'string' && s.yasin_scroll_speed.includes(':') && !s.yasin_scroll_speed.trim().startsWith('{')) {
                                    const parts = s.yasin_scroll_speed.split(':');
                                    if (parts[0]) yMode = parts[0];
                                    if (parts[1]) yStepDur = parseInt(parts[1]) || 20;
                                    if (parts[2]) yMushafDur = parseInt(parts[2]) || 120;
                                } else if (typeof s.yasin_scroll_speed === 'string' && s.yasin_scroll_speed.trim().startsWith('{')) {
                                    const parsed = JSON.parse(s.yasin_scroll_speed);
                                    if (parsed.mode) yMode = parsed.mode;
                                    if (parsed.step_duration) yStepDur = parseInt(parsed.step_duration) || 20;
                                    if (parsed.mushaf_duration) yMushafDur = parseInt(parsed.mushaf_duration) || 120;
                                } else if (s.yasin_scroll_speed === 'mushaf' || s.yasin_scroll_speed === 'step') {
                                    yMode = s.yasin_scroll_speed;
                                } else if (s.yasin_scroll_speed === 'slow') {
                                    yStepDur = 28;
                                    yMushafDur = 150;
                                } else if (s.yasin_scroll_speed === 'fast') {
                                    yStepDur = 14;
                                    yMushafDur = 90;
                                }
                            } catch (e) {}
                        }

                        // Cek localStorage browser lokal jika ada setting spesifik
                        const localYMode = localStorage.getItem('yasin_display_mode');
                        if (localYMode) yMode = localYMode;
                        const localYStepDur = localStorage.getItem('yasin_step_duration');
                        if (localYStepDur) yStepDur = parseInt(localYStepDur) || yStepDur;
                        const localYMushafDur = localStorage.getItem('yasin_mushaf_duration');
                        if (localYMushafDur) yMushafDur = parseInt(localYMushafDur) || yMushafDur;

                        if (document.getElementById('cfgYasinStepDuration')) document.getElementById('cfgYasinStepDuration').value = yStepDur;
                        if (document.getElementById('dashYasinStepDuration')) document.getElementById('dashYasinStepDuration').value = yStepDur;
                        if (document.getElementById('cfgYasinMushafDuration')) document.getElementById('cfgYasinMushafDuration').value = yMushafDur;
                        if (document.getElementById('dashYasinMushafDuration')) document.getElementById('dashYasinMushafDuration').value = yMushafDur;
                        if (typeof selectYasinOption === 'function') {
                            selectYasinOption(yMode);
                        }
                        
                        // Running text Multi-Halaman Rotasi
                        let rtPages = s.running_text_pages;
                        if (typeof rtPages === 'string') {
                            try { rtPages = JSON.parse(rtPages); } catch (e) {}
                        }
                        if (!rtPages) {
                            try { rtPages = JSON.parse(localStorage.getItem('cached_running_text_pages')); } catch (e) {}
                        }
                        cachedRunningTextPages = rtPages || {};
                        renderRunningTextPagesPanel(cachedRunningTextPages);

                        // Rotasi TV Count & Render Seluruh 17 Tabel Rotasi
                        let rotPages = s.rotation_pages;
                        if (typeof rotPages === 'string') {
                            try { rotPages = JSON.parse(rotPages); } catch (e) {}
                        }
                        if (rotPages) {
                            cachedRotationPages = normalizeRotationPages(rotPages);
                        } else {
                            cachedRotationPages = normalizeRotationPages(localStorage.getItem('cached_rotation_pages'));
                        }
                        localStorage.setItem('cached_rotation_pages', JSON.stringify(cachedRotationPages));
                        renderRotationTable(cachedRotationPages);
                        const activeCount = cachedRotationPages.filter(p => p.active !== false).length;
                        if (document.getElementById('statStatusTV')) document.getElementById('statStatusTV').textContent = `${activeCount} Halaman Aktif`;

                        // Data Qurban Idul Adha
                        if (s.qurban_jam_sembelih && document.getElementById('inputQurbanJam')) {
                            document.getElementById('inputQurbanJam').value = s.qurban_jam_sembelih;
                        }
                        if (s.qurban_lokasi_sembelih && document.getElementById('inputQurbanLokasi')) {
                            document.getElementById('inputQurbanLokasi').value = s.qurban_lokasi_sembelih;
                        }

                        let qData = s.qurban_data;
                        if (typeof qData === 'string') {
                            try { qData = JSON.parse(qData); } catch (e) {}
                        }
                        if (!qData || !Array.isArray(qData) || qData.length === 0) {
                            const localQ = localStorage.getItem('cached_qurban_data');
                            if (localQ) {
                                try { qData = JSON.parse(localQ); } catch (e) {}
                            }
                        }
                        if (qData && Array.isArray(qData) && qData.length > 0) {
                            cachedQurbanData = qData;
                        } else {
                            cachedQurbanData = DEFAULT_ADMIN_QURBAN_DATA;
                        }
                        // Data Agenda & Kas Tromol Ramadhan
                        let rPetugas = s.ramadhan_petugas;
                        if (typeof rPetugas === 'string') {
                            try { rPetugas = JSON.parse(rPetugas); } catch (e) {}
                        }
                        if (rPetugas && typeof rPetugas === 'object') {
                            cachedRamadhanPetugas = rPetugas;
                            localStorage.setItem('cached_ramadhan_petugas', JSON.stringify(rPetugas));
                        }

                        let rInfaq = s.ramadhan_infaq;
                        if (typeof rInfaq === 'string') {
                            try { rInfaq = JSON.parse(rInfaq); } catch (e) {}
                        }
                        if (rInfaq && Array.isArray(rInfaq)) {
                            cachedRamadhanInfaq = rInfaq;
                            localStorage.setItem('cached_ramadhan_infaq', JSON.stringify(rInfaq));
                        }
                        if (typeof renderAdminRamadhanView === 'function') {
                            renderAdminRamadhanView();
                        }

                        // Data Pengajian Rutin Malam Ahad (Sabtu Malam)
                        if (s.kajian_sabtu_enabled !== undefined) {
                            if (document.getElementById('cfgKajianSabtuEnabled')) document.getElementById('cfgKajianSabtuEnabled').checked = (s.kajian_sabtu_enabled !== false);
                        }
                        if (document.getElementById('kajianJamMulai')) {
                            const savedJam = s.kajian_sabtu_start_time || localStorage.getItem('cached_kajian_sabtu_start_time');
                            if (savedJam) {
                                document.getElementById('kajianJamMulai').value = savedJam;
                                if (typeof validateTime24H === 'function') validateTime24H(document.getElementById('kajianJamMulai'));
                            }
                        }
                        // Bersihkan cache lama jika masih memuat preset dummy Ahmad Sholeh
                        try {
                            const rawCacheKajian = localStorage.getItem('cached_kajian_sabtu');
                            if (rawCacheKajian && rawCacheKajian.includes('Ahmad Sholeh')) {
                                localStorage.removeItem('cached_kajian_sabtu');
                            }
                        } catch (e) {}

                        let kData = s.kajian_sabtu_data;
                        if (typeof kData === 'string') {
                            try { kData = JSON.parse(kData); } catch (e) {}
                        }
                        if (!kData || Object.keys(kData).length === 0) {
                            try { kData = JSON.parse(localStorage.getItem('cached_kajian_sabtu')); } catch (e) {}
                        }

                        // Muat Konfigurasi Timer Khusus Super Admin
                        const rotPekan = (kData && kData.rotasi_pekan_detik) || s.kajian_rotasi_pekan_detik || localStorage.getItem('cached_kajian_rotasi_pekan_detik');
                        if (document.getElementById('cfgKajianRotasiPekanDetik') && rotPekan) {
                            document.getElementById('cfgKajianRotasiPekanDetik').value = parseInt(rotPekan) || 9;
                        }
                        const lMode = (kData && kData.lock_mode) || s.kajian_lock_mode || localStorage.getItem('cached_kajian_lock_mode') || 'full';
                        if (document.getElementById('cfgKajianLockMode') && lMode) {
                            document.getElementById('cfgKajianLockMode').value = lMode;
                        }
                        const lDur = (kData && kData.lock_duration_minutes) || s.kajian_lock_duration_minutes || localStorage.getItem('cached_kajian_lock_duration_minutes');
                        if (document.getElementById('cfgKajianLockDurationMinutes') && lDur) {
                            document.getElementById('cfgKajianLockDurationMinutes').value = parseInt(lDur) || 60;
                        }
                        const lInt = (kData && kData.interleave_interval) || s.kajian_interleave_interval || localStorage.getItem('cached_kajian_interleave_interval');
                        if (document.getElementById('cfgKajianInterleaveInterval') && lInt) {
                            document.getElementById('cfgKajianInterleaveInterval').value = parseInt(lInt) || 60;
                        }
                        if (typeof toggleKajianLockModeUI === 'function') toggleKajianLockModeUI();

                        if (kData && kData.jadwal_list && Array.isArray(kData.jadwal_list) && kData.jadwal_list.length > 0) {
                            currentKajianBulanData.jadwal_list = kData.jadwal_list;
                            if (kData.bulan_num) currentKajianBulanData.bulan_num = parseInt(kData.bulan_num);
                            if (kData.tahun) currentKajianBulanData.tahun = parseInt(kData.tahun);
                        } else if (kData && (kData.ustadz_nama || kData.tema_kajian || kData.kitab_rujukan)) {
                            // Kompatibilitas data single item lama
                            const pNum = parseInt((kData.pekan_label || 'Pekan 1').replace(/\D/g, '')) || 1;
                            if (currentKajianBulanData.jadwal_list[pNum - 1]) {
                                if (kData.tanggal) currentKajianBulanData.jadwal_list[pNum - 1].tanggal = kData.tanggal;
                                if (kData.ustadz_nama) currentKajianBulanData.jadwal_list[pNum - 1].ustadz_nama = kData.ustadz_nama;
                                if (kData.ustadz_gelar) currentKajianBulanData.jadwal_list[pNum - 1].ustadz_gelar = kData.ustadz_gelar;
                                if (kData.kitab_rujukan) currentKajianBulanData.jadwal_list[pNum - 1].kitab_rujukan = kData.kitab_rujukan;
                                if (kData.tema_kajian) currentKajianBulanData.jadwal_list[pNum - 1].tema_kajian = kData.tema_kajian;
                                if (kData.waktu_pelaksanaan) currentKajianBulanData.jadwal_list[pNum - 1].waktu_pelaksanaan = kData.waktu_pelaksanaan;
                            }
                        }
                        if (document.getElementById('kajianBulanSelect') && currentKajianBulanData.bulan_num) {
                            document.getElementById('kajianBulanSelect').value = currentKajianBulanData.bulan_num;
                        }
                        if (document.getElementById('kajianTahunSelect') && currentKajianBulanData.tahun) {
                            document.getElementById('kajianTahunSelect').value = currentKajianBulanData.tahun;
                        }
                        // Auto-generate tanggal Sabtu jika belum ada satupun tanggal terisi
                        const hasAnyDate = currentKajianBulanData.jadwal_list.some(j => j.tanggal);
                        if (!hasAnyDate && typeof generateTanggalSabtuBulan === 'function') {
                            generateTanggalSabtuBulan(false);
                        } else {
                            if (typeof muatDataPekanKeForm === 'function') muatDataPekanKeForm(currentKajianPekanActive);
                            if (typeof renderKajianPekanTabsUI === 'function') renderKajianPekanTabsUI();
                            if (typeof renderKajianRekapTable === 'function') renderKajianRekapTable();
                            if (typeof updateKajianPreview === 'function') updateKajianPreview();
                        }
                    }

                        // Muat data rekening infaq ke form pengaturan
                        if (typeof loadRekeningInfaqToForm === 'function') {
                            loadRekeningInfaqToForm(s);
                        }
                }
            } catch (err) {
                console.warn('Gagal memuat app_settings:', err);
                renderAdminQurbanTable(DEFAULT_ADMIN_QURBAN_DATA);
            }

            // F. Muat Akun Pengguna & Sinkronkan dengan Supabase Cloud (app_settings: rbac_users_list)
            try {
                if (typeof AdminAuth !== 'undefined' && typeof AdminAuth.syncUsersFromCloud === 'function') {
                    await AdminAuth.syncUsersFromCloud();
                }
                renderUsersTable();
            } catch (err) {
                console.warn('Gagal sinkronisasi users dari Supabase Cloud:', err);
                renderUsersTable();
            }

            // F. Program Penggalangan Infaq & Donasi Khusus
            try {
                if (resInfaqSettled.status === 'fulfilled' && resInfaqSettled.value.ok) {
                    cachedProgramInfaq = await resInfaqSettled.value.json();
                }
            } catch (err) {
                console.warn('Gagal memuat program infaq dari Supabase:', err);
            }
            if (!cachedProgramInfaq || cachedProgramInfaq.length === 0) {
                const localInfaq = localStorage.getItem('cached_program_infaq');
                if (localInfaq) {
                    try { cachedProgramInfaq = JSON.parse(localInfaq); } catch (e) {}
                }
            }
            if (!cachedProgramInfaq || cachedProgramInfaq.length === 0) {
                cachedProgramInfaq = [
                    {
                        id: 1,
                        nama_program: "PENGADAAN AC BARU ( DAIKIN 4 unit)",
                        target_dana: 36000000,
                        keterangan: "Untuk penggantian AC sekarang yang sudah tidak OPTIMAL/sering error",
                        tanggal_mulai: "2026-09-01",
                        tanggal_selesai: "2026-12-31",
                        is_active: true
                    }
                ];
            }
            localStorage.setItem('cached_program_infaq', JSON.stringify(cachedProgramInfaq));

            // F2. Daftar Donatur Donasi Infaq
            try {
                if (resDonasiSettled.status === 'fulfilled' && resDonasiSettled.value.ok) {
                    cachedDonasiInfaq = await resDonasiSettled.value.json();
                }
            } catch (err) {
                console.warn('Gagal memuat donasi infaq dari Supabase:', err);
            }
            if (!cachedDonasiInfaq || cachedDonasiInfaq.length === 0) {
                const localDonasi = localStorage.getItem('cached_donasi_infaq');
                if (localDonasi) {
                    try { cachedDonasiInfaq = JSON.parse(localDonasi); } catch (e) {}
                }
            }
            if (!cachedDonasiInfaq) cachedDonasiInfaq = [];
            localStorage.setItem('cached_donasi_infaq', JSON.stringify(cachedDonasiInfaq));

            if (!selectedProgramInfaqId && cachedProgramInfaq.length > 0) {
                const active = cachedProgramInfaq.find(p => p.is_active === true);
                selectedProgramInfaqId = active ? active.id : cachedProgramInfaq[0].id;
            }
            renderProgramInfaqSection();

            // G. Data QRIS Donasi Digital
            try {
                if (resQrisSettled.status === 'fulfilled' && resQrisSettled.value.ok) {
                    cachedQrisList = await resQrisSettled.value.json();
                }
            } catch (err) {
                console.warn('Gagal memuat QRIS dari Supabase:', err);
            }
            if (!cachedQrisList || cachedQrisList.length === 0) {
                const localQris = localStorage.getItem('cached_qris_list');
                if (localQris) {
                    try { cachedQrisList = JSON.parse(localQris); } catch (e) {}
                }
            }
            if (!cachedQrisList || cachedQrisList.length === 0) {
                cachedQrisList = [
                    {
                        id: 1,
                        nama: 'QRIS Masjid Al-Jihad',
                        gambar: '../image/qris.png',
                        keterangan: 'Bagi yang ingin berinfaq dan sedekah secara digital, silahkan scan QRIS ini',
                        bank: 'Bank Jawa Barat (BJB)',
                        nomor_rekening: '011 686 685 4100',
                        atas_nama: 'DKM Jami Al Jihad',
                        status: 'aktif',
                        created_at: new Date().toISOString()
                    }
                ];
            }
            localStorage.setItem('cached_qris_list', JSON.stringify(cachedQrisList));
            renderQrisTable();

            // H. Data Galeri Informasi & Dokumentasi TV (Slides)
            try {
                if (resSlidesSettled.status === 'fulfilled' && resSlidesSettled.value.ok) {
                    const sbSlides = await resSlidesSettled.value.json();
                    if (Array.isArray(sbSlides) && sbSlides.length > 0) {
                        cachedGaleriData = sbSlides;
                        localStorage.setItem('aljihad_galeri_informasi', JSON.stringify(sbSlides));
                        localStorage.setItem('cached_slides', JSON.stringify(sbSlides));
                        if (typeof renderGaleriTable === 'function') {
                            renderGaleriTable();
                        }
                    }
                }
            } catch (err) {
                console.warn('Gagal memuat slides galeri informasi dari Supabase:', err);
            }

            // Refresh tabel filter Kas Utama Masjid murni
            if (typeof filterKasTable === 'function') {
                filterKasTable();
            }
        } finally {
            _isLoadingAllData = false;
            if (_pendingLoadAllData) {
                _pendingLoadAllData = false;
                debouncedLoadAllSupabaseData(300);
            }
        }
    }

        // Render Tabel Kas
// Restore unconfirmed edits after asynchronous form hydration.
const hydrateSupabaseData = loadAllSupabaseData;
loadAllSupabaseData = async function(...args) { try { return await hydrateSupabaseData.apply(this, args); } finally { if (typeof restorePetugasJumatDraft === 'function') restorePetugasJumatDraft(); } };
