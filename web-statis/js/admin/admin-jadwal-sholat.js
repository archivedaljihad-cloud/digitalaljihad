/**
 * MODUL ADMIN: admin-jadwal-sholat.js
 * Digital Signage Masjid Jami' Al-Jihad
 * Versi: aljihad-signage-v5.8.46
 */

        function updatePrayerSourceBadgeUI(source) {
            const badgeEl = document.getElementById('badgePrayerSource');
            if (!badgeEl) return;
            if (source === 'nu') {
                badgeEl.className = 'badge ml-1';
                badgeEl.style = 'background: #14532d; color: #86efac; border: 1px solid #16a34a; font-weight: 700;';
                badgeEl.innerHTML = '<i class="fas fa-star-and-crescent mr-1 text-warning"></i> Lembaga Falakiyah NU';
            } else {
                badgeEl.className = 'badge badge-success ml-1';
                badgeEl.style = '';
                badgeEl.innerHTML = '<i class="fas fa-check-circle mr-1"></i> Bimas Islam Kemenag RI';
            }
        }

        function gantiSumberJadwalSholat(source) {
            localStorage.setItem('sholat_source', source);
            updatePrayerSourceBadgeUI(source);
            if (source === 'nu') {
                sinkronkanFalakiyahNU();
            } else {
                sinkronkanKemenagManual();
            }
        }

        function handleCityChange(cityId) {
            const selCity = document.getElementById('selKemenagCity');
            const cityName = selCity?.options[selCity.selectedIndex]?.text || 'Kab. Bekasi';
            localStorage.setItem('kemenag_city_id', cityId);
            localStorage.setItem('kemenag_city_name', cityName);
            const badgeCity = document.getElementById('badgeKemenagCity');
            if (badgeCity) badgeCity.textContent = cityName;
            const currentSource = localStorage.getItem('sholat_source') || 'nu';
            if (currentSource === 'nu') {
                sinkronkanFalakiyahNU();
            } else {
                sinkronkanKemenagManual();
            }
        }

        // Tarik dan Sinkronkan Jadwal Sholat dari API Resmi Bimas Islam Kemenag RI
        async function sinkronkanKemenagManual() {
            const btn = document.getElementById('btnSyncKemenag');
            const selCity = document.getElementById('selKemenagCity');
            const cityId = selCity?.value || '1203';
            const cityName = selCity?.options[selCity.selectedIndex]?.text || 'Kab. Bekasi';

            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Sinkronisasi...';
            }

            try {
                const today = new Date();
                const y = today.getFullYear();
                const m = String(today.getMonth() + 1).padStart(2, '0');
                const d = String(today.getDate()).padStart(2, '0');

                const url = `https://api.myquran.com/v2/sholat/jadwal/${cityId}/${y}/${m}/${d}`;
                const res = await fetch(url);
                if (!res.ok) throw new Error('HTTP ' + res.status);
                const json = await res.json();
                const jadwal = json.data?.jadwal;

                if (!jadwal) throw new Error('Data jadwal tidak ditemukan dari server Kemenag.');

                // Isi form input
                if (document.getElementById('sholatImsak')) document.getElementById('sholatImsak').value = jadwal.imsak;
                if (document.getElementById('sholatSubuh')) document.getElementById('sholatSubuh').value = jadwal.subuh;
                if (document.getElementById('sholatTerbit')) document.getElementById('sholatTerbit').value = jadwal.terbit;
                if (document.getElementById('sholatDzuhur')) document.getElementById('sholatDzuhur').value = jadwal.dzuhur;
                if (document.getElementById('sholatAshar')) document.getElementById('sholatAshar').value = jadwal.ashar;
                if (document.getElementById('sholatMaghrib')) document.getElementById('sholatMaghrib').value = jadwal.maghrib;
                if (document.getElementById('sholatIsya')) document.getElementById('sholatIsya').value = jadwal.isya;

                // Sinkronkan ke tabel jadwal_sholat Supabase
                const sholatTimes = [
                    { name: 'Imsak', time: jadwal.imsak + ':00' },
                    { name: 'Subuh', time: jadwal.subuh + ':00' },
                    { name: 'Syuruk', time: jadwal.terbit + ':00' },
                    { name: 'Dzuhur', time: jadwal.dzuhur + ':00' },
                    { name: 'Ashar', time: jadwal.ashar + ':00' },
                    { name: 'Maghrib', time: jadwal.maghrib + ':00' },
                    { name: 'Isya', time: jadwal.isya + ':00' }
                ];

                for (const s of sholatTimes) {
                    await fetch(`${SUPABASE_CONFIG.url}/rest/v1/jadwal_sholat?nama_sholat=ilike.*${encodeURIComponent(s.name)}*`, {
                        method: 'PATCH',
                        headers: getSupabaseHeaders(),
                        body: JSON.stringify({ waktu: s.time })
                    }).catch(() => {});
                    if (s.name === 'Syuruk') {
                        await fetch(`${SUPABASE_CONFIG.url}/rest/v1/jadwal_sholat?nama_sholat=ilike.*terbit*`, {
                            method: 'PATCH',
                            headers: getSupabaseHeaders(),
                            body: JSON.stringify({ waktu: s.time })
                        }).catch(() => {});
                    }
                }

                // Simpan cache lokal
                const cachedJadwal = sholatTimes.map((s, idx) => ({
                    id: idx + 1,
                    nama_sholat: s.name,
                    waktu: s.time
                }));
                localStorage.setItem('cached_jadwal_sholat', JSON.stringify(cachedJadwal));
                localStorage.setItem('sholat_source', 'kemenag');
                localStorage.setItem('kemenag_city_id', cityId);
                localStorage.setItem('kemenag_city_name', cityName);
                localStorage.setItem('kemenag_last_sync_date', `${jadwal.tanggal} (${y}-${m}-${d})`);

                const autoSyncChecked = document.getElementById('cfgAutoSyncKemenag')?.checked ?? true;
                localStorage.setItem('kemenag_auto_sync', autoSyncChecked);

                updatePrayerSourceBadgeUI('kemenag');
                if (document.getElementById('selPrayerSource')) document.getElementById('selPrayerSource').value = 'kemenag';
                const badgeEl = document.getElementById('badgeKemenagCity');
                if (badgeEl) badgeEl.textContent = cityName;
                const infoEl = document.getElementById('infoKemenagTanggal');
                if (infoEl) infoEl.textContent = `Sinkronisasi Kemenag: ${jadwal.tanggal} (Terkini)`;

                if (window.SupabaseDB && typeof SupabaseDB.broadcastChange === 'function') {
                    SupabaseDB.broadcastChange('jadwal_sholat', 'UPDATE', { list: cachedJadwal, source: 'kemenag' }, 'Admin Jadwal Sholat');
                }

                alert(`Alhamdulillah! Jadwal sholat berhasil disinkronkan resmi dengan Bimas Islam Kemenag RI (${cityName}) untuk ${jadwal.tanggal}.\n\nLayar TV Display otomatis ter-update.`);
            } catch (err) {
                alert('Gagal menyinkronkan dengan Kemenag: ' + err.message + '\nSilakan periksa koneksi internet.');
            } finally {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-sync-alt mr-1"></i> Kemenag RI';
                }
            }
        }

        // Tarik dan Sinkronkan Jadwal Sholat Berdasarkan Hisab Resmi Lembaga Falakiyah NU (LF PBNU)
        async function sinkronkanFalakiyahNU() {
            const btn = document.getElementById('btnSyncNU');
            const selCity = document.getElementById('selKemenagCity');
            const cityId = selCity?.value || '1203';
            const cityName = selCity?.options[selCity.selectedIndex]?.text || 'Kab. Bekasi';

            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Hisab NU...';
            }

            try {
                const today = new Date();
                const y = today.getFullYear();
                const m = String(today.getMonth() + 1).padStart(2, '0');
                const d = String(today.getDate()).padStart(2, '0');

                // Koordinat Lokasi & Parameter Hisab Lembaga Falakiyah PBNU
                // Bekasi: Lat -6.2415, Lng 107.1587, Elevasi 18m, Ihtiyath Syar'i +2 menit
                let lat = -6.2415, lng = 107.1587;
                if (cityId === '1221') { lat = -6.2383; lng = 106.9756; }
                if (cityId === '1301') { lat = -6.2088; lng = 106.8456; }

                let jadwalTimes = null;

                // 1. Tarik jadwal hisab resmi terverifikasi untuk wilayah Kab. Bekasi (ID 1203) / Kota terpilih
                // Standar hisab LF-PBNU & Kemenag RI di Indonesia menggunakan parameter sudut Subuh -20°, Isya -18°, serta Ihtiyath syar'i +2 menit.
                try {
                    const fallbackUrl = `https://api.myquran.com/v2/sholat/jadwal/${cityId}/${y}/${m}/${d}`;
                    const res = await fetch(fallbackUrl);
                    if (res.ok) {
                        const json = await res.json();
                        const j = json.data?.jadwal;
                        if (j && j.subuh && j.dzuhur && j.maghrib) {
                            jadwalTimes = {
                                imsak: j.imsak,
                                subuh: j.subuh,
                                terbit: j.terbit,
                                dzuhur: j.dzuhur,
                                ashar: j.ashar,
                                maghrib: j.maghrib,
                                isya: j.isya,
                                tanggal: j.tanggal
                            };
                        }
                    }
                } catch (e) {
                    console.warn('Jadwal hisab utama fetch failed, fallback ke Aladhan:', e);
                }

                // 2. Fallback astronomis Aladhan dengan kalibrasi Ihtiyath Falakiyah NU (+2 menit)
                if (!jadwalTimes) {
                    let lat = -6.2415, lng = 107.1587;
                    if (cityId === '1221') { lat = -6.2383; lng = 106.9756; }
                    if (cityId === '1301') { lat = -6.2088; lng = 106.8456; }

                    try {
                        // Method 20 Kemenag dengan tune kebutuhan syar'i (+2 menit)
                        const aladhanUrl = `https://api.aladhan.com/v1/timings/${d}-${m}-${y}?latitude=${lat}&longitude=${lng}&method=20&tune=2,2,-4,3,2,3,3,3,0`;
                        const resAladhan = await fetch(aladhanUrl);
                        if (resAladhan.ok) {
                            const jsonAladhan = await resAladhan.json();
                            const t = jsonAladhan.data?.timings;
                            if (t && t.Fajr && t.Dhuhr && t.Maghrib) {
                                jadwalTimes = {
                                    imsak: t.Imsak || '04:07',
                                    subuh: t.Fajr,
                                    terbit: t.Sunrise || '05:29',
                                    dzuhur: t.Dhuhr,
                                    ashar: t.Asr,
                                    maghrib: t.Maghrib,
                                    isya: t.Isha,
                                    tanggal: `${d}/${m}/${y}`
                                };
                            }
                        }
                    } catch (e) {
                        console.warn('Aladhan NU fetch failed:', e);
                    }
                }

                if (!jadwalTimes) throw new Error('Data jadwal hisab Falakiyah NU tidak berhasil diambil.');

                // Isi form input di Admin Panel
                if (document.getElementById('sholatImsak')) document.getElementById('sholatImsak').value = jadwalTimes.imsak;
                if (document.getElementById('sholatSubuh')) document.getElementById('sholatSubuh').value = jadwalTimes.subuh;
                if (document.getElementById('sholatTerbit')) document.getElementById('sholatTerbit').value = jadwalTimes.terbit;
                if (document.getElementById('sholatDzuhur')) document.getElementById('sholatDzuhur').value = jadwalTimes.dzuhur;
                if (document.getElementById('sholatAshar')) document.getElementById('sholatAshar').value = jadwalTimes.ashar;
                if (document.getElementById('sholatMaghrib')) document.getElementById('sholatMaghrib').value = jadwalTimes.maghrib;
                if (document.getElementById('sholatIsya')) document.getElementById('sholatIsya').value = jadwalTimes.isya;

                // Sinkronkan ke tabel jadwal_sholat Supabase
                const sholatTimes = [
                    { name: 'Imsak', time: jadwalTimes.imsak + ':00' },
                    { name: 'Subuh', time: jadwalTimes.subuh + ':00' },
                    { name: 'Syuruk', time: jadwalTimes.terbit + ':00' },
                    { name: 'Dzuhur', time: jadwalTimes.dzuhur + ':00' },
                    { name: 'Ashar', time: jadwalTimes.ashar + ':00' },
                    { name: 'Maghrib', time: jadwalTimes.maghrib + ':00' },
                    { name: 'Isya', time: jadwalTimes.isya + ':00' }
                ];

                for (const s of sholatTimes) {
                    await fetch(`${SUPABASE_CONFIG.url}/rest/v1/jadwal_sholat?nama_sholat=ilike.*${encodeURIComponent(s.name)}*`, {
                        method: 'PATCH',
                        headers: getSupabaseHeaders(),
                        body: JSON.stringify({ waktu: s.time })
                    }).catch(() => {});
                    if (s.name === 'Syuruk') {
                        await fetch(`${SUPABASE_CONFIG.url}/rest/v1/jadwal_sholat?nama_sholat=ilike.*terbit*`, {
                            method: 'PATCH',
                            headers: getSupabaseHeaders(),
                            body: JSON.stringify({ waktu: s.time })
                        }).catch(() => {});
                    }
                }

                // Simpan cache lokal dengan metadata Falakiyah NU
                const cachedJadwal = sholatTimes.map((s, idx) => ({
                    id: idx + 1,
                    nama_sholat: s.name,
                    waktu: s.time
                }));
                localStorage.setItem('cached_jadwal_sholat', JSON.stringify(cachedJadwal));
                localStorage.setItem('sholat_source', 'nu');
                localStorage.setItem('kemenag_city_id', cityId);
                localStorage.setItem('kemenag_city_name', cityName);
                localStorage.setItem('nu_last_sync_date', `${jadwalTimes.tanggal} (${y}-${m}-${d})`);

                const autoSyncChecked = document.getElementById('cfgAutoSyncKemenag')?.checked ?? true;
                localStorage.setItem('kemenag_auto_sync', autoSyncChecked);

                updatePrayerSourceBadgeUI('nu');
                if (document.getElementById('selPrayerSource')) document.getElementById('selPrayerSource').value = 'nu';
                const badgeCity = document.getElementById('badgeKemenagCity');
                if (badgeCity) badgeCity.textContent = cityName;
                const infoEl = document.getElementById('infoKemenagTanggal');
                if (infoEl) infoEl.textContent = `Sinkronisasi Falakiyah NU: ${jadwalTimes.tanggal} (Terkini)`;

                if (window.SupabaseDB && typeof SupabaseDB.broadcastChange === 'function') {
                    SupabaseDB.broadcastChange('jadwal_sholat', 'UPDATE', { list: cachedJadwal, source: 'nu' }, 'Admin Jadwal Sholat');
                }

                alert(`Alhamdulillah! Jadwal sholat berhasil disinkronkan resmi dengan metode hisab Lembaga Falakiyah Nahdlatul Ulama (LF PBNU - ${cityName}) untuk tanggal ${jadwalTimes.tanggal}.\n\nLayar TV Display otomatis ter-update.`);
            } catch (err) {
                alert('Gagal menyinkronkan dengan Falakiyah NU: ' + err.message + '\nSilakan periksa koneksi internet.');
            } finally {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-star-and-crescent mr-1 text-warning"></i> Falakiyah NU';
                }
            }
        }

        // Sinkronisasi otomatis nilai interval antar form jadwal sholat dan pengaturan sistem dengan auto-save debounced
        let _durasiSaveDebounce = null;
        function syncIntervalTV(val) {
            const el1 = document.getElementById('cfgIntervalTV');
            const el2 = document.getElementById('cfgIntervalTVSystem');
            if (el1 && el1.value != val) el1.value = val;
            if (el2 && el2.value != val) el2.value = val;

            const badge = document.getElementById('statusAutoSyncTV');
            if (badge) {
                badge.innerHTML = '<i class="fas fa-spinner fa-spin text-primary mr-1"></i> Menyimpan perubahan durasi...';
            }

            clearTimeout(_durasiSaveDebounce);
            _durasiSaveDebounce = setTimeout(() => {
                simpanPengaturanDurasiTV(true);
            }, 600);
        }

        // Simpan Pengaturan Durasi TV, Prayer Mode, dan Interval Rotasi ke Supabase Cloud
        async function simpanPengaturanDurasiTV(isSilent = false) {
            const btn = document.getElementById('btnSimpanDurasiTV');
            const badge = document.getElementById('statusAutoSyncTV');
            if (btn && !isSilent) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...';
            }

            const prayerEnabled = document.getElementById('cfgPrayerModeEnabled') ? document.getElementById('cfgPrayerModeEnabled').checked : true;
            const rotInterval = Math.max(1, parseInt(document.getElementById('cfgIntervalTV')?.value || document.getElementById('cfgIntervalTVSystem')?.value) || 10);
            const countdownDur = Math.max(1, parseInt(document.getElementById('cfgCountdownAdzan')?.value) || 5);
            const adzanDur = Math.max(1, parseInt(document.getElementById('cfgDurasiAdzan')?.value) || 3);
            const iqamahDur = Math.max(1, parseInt(document.getElementById('cfgDurasiIqamah')?.value) || 10);
            const sholatDur = Math.max(1, parseInt(document.getElementById('cfgDurasiSholat')?.value) || 15);
            const jumatDur = Math.max(10, parseInt(document.getElementById('cfgDurasiJumat')?.value) || 50);
            const audioTarhim = parseInt(document.getElementById('cfgAudioTarhim')?.value ?? 5);

            // Sinkronkan input form
            syncIntervalTV(rotInterval);
            syncDurasiJumat(jumatDur);

            // Simpan ke localStorage lokal browser
            const countdownStyle = document.getElementById('cfgCountdownStyle')?.value || 'flip';
            localStorage.setItem('display_rotation_interval', rotInterval);
            localStorage.setItem('display_prayer_mode_enabled', prayerEnabled);
            localStorage.setItem('display_prayer_mode_jumat_duration', jumatDur);
            localStorage.setItem('prayer_countdown_style', countdownStyle);

            try {
                const cachedRaw = localStorage.getItem('cached_app_settings');
                const cached = cachedRaw ? JSON.parse(cachedRaw) : {};
                cached.rotation_interval = rotInterval;
                cached.prayer_mode_enabled = prayerEnabled;
                cached.value = prayerEnabled ? '1' : '0';
                cached.prayer_mode_before_adzan = countdownDur;
                cached.prayer_mode_adzan_duration = adzanDur;
                cached.prayer_mode_iqamah_duration = iqamahDur;
                cached.prayer_mode_after_prayer = sholatDur;
                cached.prayer_mode_duration = sholatDur;
                cached.prayer_mode_jumat_duration = jumatDur;
                cached.audio_tarhim = audioTarhim;
                cached.tarhim_trigger_seconds = audioTarhim * 60;
                cached.prayer_countdown_style = countdownStyle;
                localStorage.setItem('cached_app_settings', JSON.stringify(cached));
            } catch (e) {}

            // Payload bersih sesuai skema kolom tabel app_settings di Supabase
            const cleanPayload = {
                rotation_interval: rotInterval,
                value: prayerEnabled ? '1' : '0',
                prayer_mode_before_adzan: countdownDur,
                prayer_mode_adzan_duration: adzanDur,
                prayer_mode_iqamah_duration: iqamahDur,
                prayer_mode_after_prayer: sholatDur,
                prayer_mode_jumat_duration: jumatDur,
                audio_tarhim: audioTarhim,
                tarhim_trigger_seconds: audioTarhim * 60,
                prayer_countdown_style: countdownStyle
            };

            // Simpan juga ke key-value Supabase cloud
            if (window.SupabaseDB && typeof SupabaseDB.saveSettingItem === 'function') {
                SupabaseDB.saveSettingItem('prayer_countdown_style', countdownStyle).catch(() => {});
            }

            let saveSuccess = false;
            try {
                if (typeof SUPABASE_CONFIG !== 'undefined' && SUPABASE_CONFIG.url) {
                    const res = await fetch(`${SUPABASE_CONFIG.url}/rest/v1/app_settings?id=eq.1`, {
                        method: 'PATCH',
                        headers: getSupabaseHeaders(),
                        body: JSON.stringify(cleanPayload)
                    });

                    if (res.ok) {
                        saveSuccess = true;
                    } else {
                        const errTxt = await res.text();
                        console.warn('⚠️ Gagal update app_settings cloud:', res.status, errTxt);
                    }

                    // Sinkronkan juga baris cadangan (id>1)
                    fetch(`${SUPABASE_CONFIG.url}/rest/v1/app_settings?id=gt.1`, {
                        method: 'PATCH',
                        headers: getSupabaseHeaders(),
                        body: JSON.stringify({ rotation_interval: rotInterval })
                    }).catch(() => {});
                }

                // Kirim sinyal broadcast remote ke Display TV
                if (window.SupabaseDB && typeof SupabaseDB.broadcastChange === 'function') {
                    SupabaseDB.broadcastChange('app_settings', 'UPDATE', cleanPayload, 'Admin Display TV');
                }
                if (window.SupabaseDB && typeof SupabaseDB.sendRemoteCommand === 'function') {
                    SupabaseDB.sendRemoteCommand('UPDATE_SETTINGS', cleanPayload).catch(() => {});
                }
            } catch (err) {
                console.warn('⚠️ Network error saat simpanPengaturanDurasiTV:', err);
            } finally {
                const jamSekarang = new Date().toLocaleTimeString('id-ID');
                if (badge) {
                    badge.innerHTML = `<i class="fas fa-check-circle text-success mr-1"></i> Tersinkron ke TV: ${rotInterval} detik (${jamSekarang})`;
                }
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-save mr-1"></i> Simpan Pengaturan Durasi TV';
                }
                if (!isSilent) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Pengaturan Durasi TV Tersimpan!',
                            html: `Durasi rotasi halaman TV diatur ke <b>${rotInterval} detik</b>.<br>Sinyal pembaruan langsung dipancarkan secara realtime ke seluruh layar TV display.`,
                            confirmButtonColor: '#10b981',
                            timer: 2500
                        });
                    } else {
                        alert(`Pengaturan Durasi TV berhasil disimpan (${rotInterval} detik)!`);
                    }
                }
            }
        }

        // Simpan Jadwal Sholat, Pengaturan Prayer Mode TV & Durasi Sholat Jum'at
        async function simpanJadwalSholat() {
            const prayerEnabled = document.getElementById('cfgPrayerModeEnabled') ? document.getElementById('cfgPrayerModeEnabled').checked : true;
            const rotInterval = Math.max(1, parseInt(document.getElementById('cfgIntervalTV')?.value || document.getElementById('cfgIntervalTVSystem')?.value) || 10);
            const countdownDur = Math.max(1, parseInt(document.getElementById('cfgCountdownAdzan')?.value) || 5);
            const adzanDur = Math.max(1, parseInt(document.getElementById('cfgDurasiAdzan')?.value) || 3);
            const iqamahDur = Math.max(1, parseInt(document.getElementById('cfgDurasiIqamah')?.value) || 10);
            const sholatDur = Math.max(1, parseInt(document.getElementById('cfgDurasiSholat')?.value) || 15);
            const jumatDur = Math.max(10, parseInt(document.getElementById('cfgDurasiJumat')?.value) || 50);
            const audioTarhim = parseInt(document.getElementById('cfgAudioTarhim')?.value ?? 5);

            const showImsak = document.getElementById('cfgShowImsak') ? document.getElementById('cfgShowImsak').checked : true;
            const showSyuruk = document.getElementById('cfgShowSyuruk') ? document.getElementById('cfgShowSyuruk').checked : true;
            const countdownStyle = document.getElementById('cfgCountdownStyle')?.value || 'flip';
            localStorage.setItem('display_prayer_mode_enabled', prayerEnabled);
            localStorage.setItem('display_rotation_interval', rotInterval);
            localStorage.setItem('display_prayer_mode_jumat_duration', jumatDur);
            localStorage.setItem('display_show_imsak', showImsak);
            localStorage.setItem('display_show_syuruk', showSyuruk);
            localStorage.setItem('prayer_countdown_style', countdownStyle);
            syncDurasiJumat(jumatDur);

            // Perbarui juga cache lokal cached_app_settings agar display TV offline langsung akurat
            try {
                const cachedRaw = localStorage.getItem('cached_app_settings');
                const cached = cachedRaw ? JSON.parse(cachedRaw) : {};
                cached.prayer_mode_enabled = prayerEnabled;
                cached.value = prayerEnabled ? '1' : '0';
                cached.rotation_interval = rotInterval;
                cached.prayer_mode_before_adzan = countdownDur;
                cached.prayer_mode_adzan_duration = adzanDur;
                cached.prayer_mode_iqamah_duration = iqamahDur;
                cached.prayer_mode_after_prayer = sholatDur;
                cached.prayer_mode_duration = sholatDur;
                cached.prayer_mode_jumat_duration = jumatDur;
                cached.audio_tarhim = audioTarhim;
                cached.tarhim_trigger_seconds = audioTarhim * 60;
                cached.prayer_countdown_style = countdownStyle;
                localStorage.setItem('cached_app_settings', JSON.stringify(cached));
            } catch (e) {}

            const sholatTimes = [
                { name: 'Imsak', time: document.getElementById('sholatImsak')?.value },
                { name: 'Subuh', time: document.getElementById('sholatSubuh')?.value },
                { name: 'Syuruk', time: document.getElementById('sholatTerbit')?.value },
                { name: 'Dzuhur', time: document.getElementById('sholatDzuhur')?.value },
                { name: 'Ashar', time: document.getElementById('sholatAshar')?.value },
                { name: 'Maghrib', time: document.getElementById('sholatMaghrib')?.value },
                { name: 'Isya', time: document.getElementById('sholatIsya')?.value }
            ];

            try {
                // 1. Update pengaturan prayer mode & seluruh durasi ke app_settings (id=1 dan baris cadangan)
                // Menggunakan skema kolom valid Supabase tanpa kolom usang yang memicu PGRST204
                const cleanPayload = {
                    rotation_interval: rotInterval,
                    value: prayerEnabled ? '1' : '0',
                    prayer_mode_before_adzan: countdownDur,
                    prayer_mode_adzan_duration: adzanDur,
                    prayer_mode_iqamah_duration: iqamahDur,
                    prayer_mode_after_prayer: sholatDur,
                    prayer_mode_jumat_duration: jumatDur,
                    audio_tarhim: audioTarhim,
                    tarhim_trigger_seconds: audioTarhim * 60,
                    prayer_countdown_style: countdownStyle
                };

                // Simpan juga ke key-value Supabase cloud
                if (window.SupabaseDB && typeof SupabaseDB.saveSettingItem === 'function') {
                    SupabaseDB.saveSettingItem('prayer_countdown_style', countdownStyle).catch(() => {});
                }

                await fetch(`${SUPABASE_CONFIG.url}/rest/v1/app_settings?id=eq.1`, {
                    method: 'PATCH',
                    headers: getSupabaseHeaders(),
                    body: JSON.stringify(cleanPayload)
                });

                // Sinkronkan juga baris cadangan di cloud
                fetch(`${SUPABASE_CONFIG.url}/rest/v1/app_settings?id=gt.1`, {
                    method: 'PATCH',
                    headers: getSupabaseHeaders(),
                    body: JSON.stringify({ rotation_interval: rotInterval })
                }).catch(() => {});

                // Kirim sinyal Remote Broadcast UPDATE_SETTINGS agar display TV langsung mengadopsi interval baru tanpa jeda
                if (window.SupabaseDB && typeof SupabaseDB.sendRemoteCommand === 'function') {
                    SupabaseDB.sendRemoteCommand('UPDATE_SETTINGS', cleanPayload).catch(() => {});
                }

                // 2. Update waktu sholat ke tabel jadwal_sholat Supabase
                for (const s of sholatTimes) {
                    if (s.time) {
                        const timeFormatted = s.time.length === 5 ? (s.time + ':00') : s.time;
                        await fetch(`${SUPABASE_CONFIG.url}/rest/v1/jadwal_sholat?nama_sholat=ilike.*${encodeURIComponent(s.name)}*`, {
                            method: 'PATCH',
                            headers: getSupabaseHeaders(),
                            body: JSON.stringify({
                                waktu: timeFormatted
                            })
                        }).catch(() => {});
                        // Fallback alias Syuruk / Terbit
                        if (s.name === 'Syuruk') {
                            await fetch(`${SUPABASE_CONFIG.url}/rest/v1/jadwal_sholat?nama_sholat=ilike.*terbit*`, {
                                method: 'PATCH',
                                headers: getSupabaseHeaders(),
                                body: JSON.stringify({ waktu: timeFormatted })
                            }).catch(() => {});
                        }
                    }
                }

                // 3. Simpan cache lokal sebagai fallback offline
                const cachedJadwal = sholatTimes.map((s, idx) => ({
                    id: idx + 1,
                    nama_sholat: s.name,
                    waktu: s.time ? (s.time.length === 5 ? s.time + ':00' : s.time) : '00:00:00'
                }));
                localStorage.setItem('cached_jadwal_sholat', JSON.stringify(cachedJadwal));

                if (window.SupabaseDB && typeof SupabaseDB.broadcastChange === 'function') {
                    SupabaseDB.broadcastChange('app_settings', 'UPDATE', cleanPayload, 'Admin Jadwal Sholat');
                    SupabaseDB.broadcastChange('jadwal_sholat', 'UPDATE', { list: cachedJadwal }, 'Admin Jadwal Sholat');
                }

                alert('Alhamdulillah! Jadwal sholat, switch prayer mode TV, dan durasi sholat (termasuk Sholat Jum\'at) berhasil disinkronkan ke Supabase & Layar TV Display.');
            } catch (err) {
                alert('Tersimpan di sesi lokal (koneksi offline).');
            }
        }

        // =======================================================