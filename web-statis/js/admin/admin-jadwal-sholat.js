/**
 * MODUL ADMIN: admin-jadwal-sholat.js
 * Digital Signage Masjid Jami' Al-Jihad
 * Versi: aljihad-signage-v5.8.46
 */

        function setPrayerInputValue(id, val) {
    const el = document.getElementById(id);
    if (!el) return;
    el.value = val;
    if (el._flatpickr) {
        try { el._flatpickr.setDate(val, false); } catch(e) {}
    }
}

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

                // Isi form input 24H
                setPrayerInputValue('sholatImsak', jadwal.imsak);
                setPrayerInputValue('sholatSubuh', jadwal.subuh);
                setPrayerInputValue('sholatTerbit', jadwal.terbit);
                setPrayerInputValue('sholatDzuhur', jadwal.dzuhur);
                setPrayerInputValue('sholatAshar', jadwal.ashar);
                setPrayerInputValue('sholatMaghrib', jadwal.maghrib);
                setPrayerInputValue('sholatIsya', jadwal.isya);

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
                if (typeof window.updateTopbarNextPrayer === 'function') {
                    window.updateTopbarNextPrayer(true);
                }
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

                alert(`Alhamdulillah! Jadwal sholat berhasil disinkronkan resmi dengan Bimas Islam Kemenag RI (${cityName}) untuk ${jadwal.tanggal}.\n\n📌 Rincian Waktu Sholat (24H WIB):\n• Imsak: ${jadwal.imsak}\n• Subuh: ${jadwal.subuh}\n• Terbit: ${jadwal.terbit}\n• Dzuhur: ${jadwal.dzuhur}\n• Ashar: ${jadwal.ashar}\n• Maghrib: ${jadwal.maghrib}\n• Isya: ${jadwal.isya}\n\nLayar TV Display otomatis ter-update.`);
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
                // Bekasi: Lat -6.2415, Lng 107.1587, Elevasi 18m
                let lat = -6.2415, lng = 107.1587;
                if (cityId === '1221') { lat = -6.2383; lng = 106.9756; }
                if (cityId === '1301') { lat = -6.2088; lng = 106.8456; }

                let jadwalTimes = null;

                // 1. Tarik hisab astronomis terkalibrasi Falakiyah NU via Aladhan Method 20 dengan tune LF PBNU
                try {
                    const aladhanUrl = `https://api.aladhan.com/v1/timings/${d}-${m}-${y}?latitude=${lat}&longitude=${lng}&method=20&tune=0,0,-1,1,-1,1,0,0,0`;
                    const resAladhan = await fetch(aladhanUrl);
                    if (resAladhan.ok) {
                        const jsonAladhan = await resAladhan.json();
                        const t = jsonAladhan.data?.timings;
                        if (t && t.Fajr && t.Dhuhr && t.Maghrib) {
                            jadwalTimes = {
                                imsak: t.Imsak || '04:02',
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
                    console.warn('Aladhan LF PBNU fetch error, mencoba fallback Kemenag:', e);
                }

                // 2. Fallback jika Aladhan tidak dapat dijangkau
                if (!jadwalTimes) {
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
                        console.warn('Fallback Kemenag error:', e);
                    }
                }

                if (!jadwalTimes) throw new Error('Data jadwal hisab Falakiyah NU tidak berhasil diambil.');

                // Isi form input di Admin Panel 24H
                setPrayerInputValue('sholatImsak', jadwalTimes.imsak);
                setPrayerInputValue('sholatSubuh', jadwalTimes.subuh);
                setPrayerInputValue('sholatTerbit', jadwalTimes.terbit);
                setPrayerInputValue('sholatDzuhur', jadwalTimes.dzuhur);
                setPrayerInputValue('sholatAshar', jadwalTimes.ashar);
                setPrayerInputValue('sholatMaghrib', jadwalTimes.maghrib);
                setPrayerInputValue('sholatIsya', jadwalTimes.isya);

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
                if (typeof window.updateTopbarNextPrayer === 'function') {
                    window.updateTopbarNextPrayer(true);
                }
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

                alert(`Alhamdulillah! Jadwal sholat berhasil disinkronkan resmi dengan metode hisab Lembaga Falakiyah Nahdlatul Ulama (LF PBNU - ${cityName}) untuk tanggal ${jadwalTimes.tanggal}.\n\n📌 Rincian Waktu Sholat (24H WIB):\n• Imsak: ${jadwalTimes.imsak}\n• Subuh: ${jadwalTimes.subuh}\n• Terbit: ${jadwalTimes.terbit}\n• Dzuhur: ${jadwalTimes.dzuhur}\n• Ashar: ${jadwalTimes.ashar}\n• Maghrib: ${jadwalTimes.maghrib}\n• Isya: ${jadwalTimes.isya}\n\nLayar TV Display otomatis ter-update.`);
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
        function syncIntervalTV(val, skipTriggerSave = false) {
            const el1 = document.getElementById('cfgIntervalTV');
            const el2 = document.getElementById('cfgIntervalTVSystem');
            if (el1 && el1.value != val) el1.value = val;
            if (el2 && el2.value != val) el2.value = val;

            if (skipTriggerSave) return;

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
            const sequence = (simpanPengaturanDurasiTV.sequence || 0) + 1;
            simpanPengaturanDurasiTV.sequence = sequence;
            const btn = document.getElementById('btnSimpanDurasiTV');
            const badge = document.getElementById('statusAutoSyncTV');
            if (btn) btn.disabled = true;
            if (badge) badge.textContent = 'Menyimpan pengaturan ke cloud...';
            const prayerEnabled = document.getElementById('cfgPrayerModeEnabled') ? document.getElementById('cfgPrayerModeEnabled').checked : true;
            const rotInterval = Math.max(1, parseInt(document.getElementById('cfgIntervalTV')?.value || document.getElementById('cfgIntervalTVSystem')?.value) || 10);
            const countdownDur = Math.max(1, parseInt(document.getElementById('cfgCountdownAdzan')?.value) || 5);
            const adzanDur = Math.max(1, parseInt(document.getElementById('cfgDurasiAdzan')?.value) || 3);
            const iqamahDur = Math.max(1, parseInt(document.getElementById('cfgDurasiIqamah')?.value) || 10);
            const sholatDur = Math.max(1, parseInt(document.getElementById('cfgDurasiSholat')?.value) || 15);
            const jumatDur = Math.max(10, parseInt(document.getElementById('cfgDurasiJumat')?.value) || 50);
            const audioTarhim = parseInt(document.getElementById('cfgAudioTarhim')?.value ?? 5);

            // Sinkronkan input form
            syncIntervalTV(rotInterval, true);
            syncDurasiJumat(jumatDur);
            const countdownStyle = document.getElementById('cfgCountdownStyle')?.value || 'flip';

            const cleanPayload = {
                rotation_interval: rotInterval,
                value: prayerEnabled ? '1' : '0',
                prayer_mode_before_adzan: countdownDur,
                prayer_mode_adzan_duration: adzanDur,
                prayer_mode_iqamah_duration: iqamahDur,
                prayer_mode_after_prayer: sholatDur,
                prayer_mode_jumat_duration: jumatDur,
                audio_tarhim: audioTarhim,
                tarhim_trigger_seconds: audioTarhim * 60
            };

            try { localStorage.setItem('draft_durasi_tv', JSON.stringify({ ...cleanPayload, prayer_countdown_style: countdownStyle })); } catch (err) {}
            const save = async () => {
                try {
                    if (!window.SupabaseDB || typeof SupabaseDB.safeRestMutation !== 'function' || typeof SupabaseDB.saveSettingItem !== 'function') return false;
                    const [styleOk, result] = await Promise.all([
                        SupabaseDB.saveSettingItem('prayer_countdown_style', countdownStyle),
                        SupabaseDB.safeRestMutation('app_settings?id=eq.1', 'PATCH', cleanPayload)
                    ]);
                    if (styleOk !== true || !result.success || !result.cloudConfirmed || !(result.rowsAffected > 0)) return false;
                    try {
                        const cached = JSON.parse(localStorage.getItem('cached_app_settings') || '{}');
                        Object.assign(cached, cleanPayload, { prayer_mode_enabled: prayerEnabled, prayer_mode_duration: sholatDur, prayer_countdown_style: countdownStyle });
                        localStorage.setItem('cached_app_settings', JSON.stringify(cached));
                        const entries = { display_rotation_interval: rotInterval, display_prayer_mode_enabled: prayerEnabled,
                            display_prayer_mode_jumat_duration: jumatDur, prayer_countdown_style: countdownStyle,
                            cfg_countdown_adzan: countdownDur, cfg_durasi_adzan: adzanDur, cfg_durasi_iqamah: iqamahDur,
                            cfg_durasi_sholat: sholatDur, cfg_durasi_jumat: jumatDur, cfg_audio_tarhim: audioTarhim };
                        Object.entries(entries).forEach(([key, val]) => localStorage.setItem(key, String(val)));
                    } catch (err) { console.warn('Cloud tersimpan; cache durasi tidak tersedia:', err); }
                    SupabaseDB.invalidateCache('app_settings');
                    SupabaseDB.notifyConfirmedChange('app_settings', { ...cleanPayload, prayer_countdown_style: countdownStyle }, 'UPDATE_SETTINGS');
                    return true;
                } catch (err) { console.warn('Pengaturan cloud belum terkonfirmasi:', err); return false; }
            };
            simpanPengaturanDurasiTV.queue = (simpanPengaturanDurasiTV.queue || Promise.resolve()).then(save, save);
            const confirmed = await simpanPengaturanDurasiTV.queue;
            if (sequence !== simpanPengaturanDurasiTV.sequence) return confirmed;
            if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fas fa-save mr-1"></i> Simpan Pengaturan Durasi TV'; }
            if (badge) badge.textContent = confirmed
                ? 'Pengaturan tersimpan di cloud. Tampilan TV belum dikonfirmasi.'
                : 'Belum semua pengaturan tersimpan di cloud. Draft tetap tersedia; coba Simpan lagi.';
            if (confirmed) { try { localStorage.removeItem('draft_durasi_tv'); } catch (err) {} }
            if (!isSilent) {
                if (typeof Swal !== 'undefined') Swal.fire({ icon: confirmed ? 'success' : 'error', title: confirmed ? 'Tersimpan di cloud' : 'Penyimpanan belum terkonfirmasi', text: confirmed ? 'Konfirmasi tampilan TV dilakukan terpisah.' : 'Periksa koneksi. Draft tetap tersedia untuk dicoba kembali.' });
                else alert(confirmed ? 'Pengaturan tersimpan di cloud; tampilan TV belum dikonfirmasi.' : 'Penyimpanan cloud belum terkonfirmasi. Draft tetap tersedia.');
            }
            return confirmed;
        }

        // Simpan Jadwal Sholat, Pengaturan Prayer Mode TV & Durasi Sholat Jum'at
        async function simpanJadwalSholat() {
            const sholatTimes = [
                { name: 'Imsak', time: document.getElementById('sholatImsak')?.value },
                { name: 'Subuh', time: document.getElementById('sholatSubuh')?.value },
                { name: 'Syuruk', time: document.getElementById('sholatTerbit')?.value },
                { name: 'Dzuhur', time: document.getElementById('sholatDzuhur')?.value },
                { name: 'Ashar', time: document.getElementById('sholatAshar')?.value },
                { name: 'Maghrib', time: document.getElementById('sholatMaghrib')?.value },
                { name: 'Isya', time: document.getElementById('sholatIsya')?.value }
            ];

            try { localStorage.setItem('draft_jadwal_sholat', JSON.stringify(sholatTimes)); } catch (err) {}
            try {
                if (!window.SupabaseDB || typeof SupabaseDB.safeRestMutation !== 'function') throw new Error('Layanan cloud belum tersedia');
                for (const item of sholatTimes) {
                    if (item.time && !/^([01]\d|2[0-3]):[0-5]\d(?::[0-5]\d)?$/.test(item.time)) throw new Error('Format jam tidak valid: ' + item.name);
                }
                if (await simpanPengaturanDurasiTV(true) !== true) throw new Error('Pengaturan durasi belum seluruhnya terkonfirmasi');
                for (const item of sholatTimes) {
                    if (!item.time) continue;
                    const waktu = item.time.length === 5 ? item.time + ':00' : item.time;
                    let result = await SupabaseDB.safeRestMutation(
                        `jadwal_sholat?nama_sholat=ilike.*${encodeURIComponent(item.name)}*`, 'PATCH', { waktu });
                    if ((!result.success || !result.cloudConfirmed || !(result.rowsAffected > 0)) && item.name === 'Syuruk' && result.rowsAffected === 0 && result.status >= 200 && result.status < 300) {
                        result = await SupabaseDB.safeRestMutation('jadwal_sholat?nama_sholat=ilike.*terbit*', 'PATCH', { waktu });
                    }
                    if (!result.success || !result.cloudConfirmed || !(result.rowsAffected > 0)) throw new Error('Jadwal ' + item.name + ' belum terkonfirmasi');
                }
                const list = await SupabaseDB.getJadwalSholat(true, true);
                SupabaseDB.notifyConfirmedChange('jadwal_sholat', { list }, 'SYNC_JADWAL_SHOLAT');
                if (typeof window.updateTopbarNextPrayer === 'function') {
                    window.updateTopbarNextPrayer(true);
                }
                try { localStorage.removeItem('draft_jadwal_sholat'); } catch (err) {}
                alert('Jadwal dan durasi tersimpan di cloud. Tampilan TV belum dikonfirmasi.');
                return true;
            } catch (err) {
                console.warn('Penyimpanan jadwal belum lengkap:', err);
                alert('Belum semua perubahan terkonfirmasi di cloud. Draft tetap tersedia. ' + err.message);
                return false;
            }
        }

        // =======================================================