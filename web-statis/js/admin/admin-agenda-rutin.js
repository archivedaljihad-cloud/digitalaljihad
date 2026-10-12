/**
 * MODUL ADMIN: admin-agenda-rutin.js
 * Digital Signage Masjid Jami' Al-Jihad
 * Versi: aljihad-signage-v5.8.45
 */

        // PUSAT AGENDA RUTIN MASJID (ADMIN & PETUGAS)
        // =====================================================
        function getDefaultAgendaRutinWs() {
            return {
                yasin: {
                    enabled: true,
                    judul: 'Pembacaan Surat Yaasiin & Tahlil',
                    hari_teks: 'Setiap Malam Jum\'at (Kamis Malam)',
                    waktu: 'Ba\'da Maghrib s/d Isya',
                    pembimbing: 'Imam Rawatib / Asatidz Masjid',
                    lokasi: 'Ruang Utama Masjid Jami\' Al Jihad'
                },
                kajian_ahad: {
                    enabled: true,
                    judul: 'Kajian Malam Ahad',
                    hari_teks: 'Setiap Malam Ahad (Sabtu Malam)',
                    waktu: 'Ba\'da Maghrib s/d Isya',
                    pembimbing: '',
                    keterangan: ''
                },
                tahsin: {
                    enabled: true,
                    judul: 'Bimbingan Tahsin Al-Qur\'an',
                    hari_aktif: ['senin', 'rabu', 'sabtu'],
                    waktu: 'Ba\'da Sholat Isya (20:00 WIB)',
                    pembimbing: 'Ust. Pembina Tahsin Al-Qur\'an',
                    keterangan: 'Terbuka untuk Jamaah Ikhwan & Akhwat (Semua Usia)'
                },
                tafsir: {
                    enabled: true,
                    judul: 'Kajian Umum Tafsir Al-Qur\'an',
                    pekan_aktif: [1, 3],
                    waktu: 'Ahad Ba\'da Subuh (05:15 WIB)',
                    pembimbing: 'Asatidz Dewan Syari\'ah Masjid',
                    keterangan: 'Tafsir Ayat-ayat Pilihan & Sarapan Pagi Bersama'
                }
            };
        }

        function getAgendaRutinDataWs() {
            try {
                const raw = localStorage.getItem('agenda_rutin_settings');
                if (raw) {
                    const parsed = JSON.parse(raw);
                    const def = getDefaultAgendaRutinWs();
                    return {
                        yasin: Object.assign({}, def.yasin, parsed.yasin || {}),
                        kajian_ahad: Object.assign({}, def.kajian_ahad, parsed.kajian_ahad || {}),
                        tahsin: Object.assign({}, def.tahsin, parsed.tahsin || {}),
                        tafsir: Object.assign({}, def.tafsir, parsed.tafsir || {})
                    };
                }
            } catch(e) {
                console.warn('Gagal membaca agenda_rutin_settings:', e);
            }
            return getDefaultAgendaRutinWs();
        }

        function toggleWsDayPill(btn) {
            const isSelected = btn.classList.contains('btn-warning');
            const day = btn.getAttribute('data-day');
            const label = day ? (day.charAt(0).toUpperCase() + day.slice(1)) : 'Hari';
            if (isSelected) {
                btn.className = 'btn btn-sm ws-day-pill btn-outline-secondary';
                btn.innerHTML = `<i class="fas fa-circle mr-1"></i> ${label}`;
            } else {
                btn.className = 'btn btn-sm ws-day-pill btn-warning font-weight-bold text-dark';
                btn.innerHTML = `<i class="fas fa-check-circle mr-1"></i> ${label}`;
            }
        }

        // =====================================================
        // SINKRONISASI JADWAL PEKAN INI KE PUSAT AGENDA RUTIN
        // =====================================================
        function syncKajianPekanIniKeAgendaRutin(isUserClick = false, targetItem = null) {
            let activeItem = targetItem;

            if (!activeItem) {
                // 1. Coba ambil dari currentKajianBulanData di memori
                if (typeof currentKajianBulanData !== 'undefined' && currentKajianBulanData && Array.isArray(currentKajianBulanData.jadwal_list)) {
                    const todayStr = (typeof getTodayDateStringKajian === 'function') ? getTodayDateStringKajian() : (new Date()).toISOString().split('T')[0];
                    let nearestIndex = 0;
                    let minDiff = Infinity;
                    currentKajianBulanData.jadwal_list.forEach((item, idx) => {
                        if (item.tanggal) {
                            if (item.tanggal === todayStr) {
                                nearestIndex = idx;
                                minDiff = -1;
                            } else if (item.tanggal >= todayStr && minDiff !== -1) {
                                const diff = new Date(item.tanggal) - new Date(todayStr);
                                if (diff < minDiff) {
                                    minDiff = diff;
                                    nearestIndex = idx;
                                }
                            }
                        }
                    });
                    activeItem = currentKajianBulanData.jadwal_list[nearestIndex] || currentKajianBulanData.jadwal_list[0];
                }

                // 2. Jika belum ada, baca dari cached_kajian_sabtu
                if (!activeItem) {
                    try {
                        const raw = localStorage.getItem('cached_kajian_sabtu');
                        if (raw) {
                            const parsed = JSON.parse(raw);
                            if (Array.isArray(parsed.jadwal_list) && parsed.jadwal_list.length > 0) {
                                const todayStr = (new Date()).toISOString().split('T')[0];
                                let nearestIdx = 0;
                                let minDiff = Infinity;
                                parsed.jadwal_list.forEach((item, idx) => {
                                    if (item.tanggal) {
                                        if (item.tanggal === todayStr) {
                                            nearestIdx = idx;
                                            minDiff = -1;
                                        } else if (item.tanggal >= todayStr && minDiff !== -1) {
                                            const diff = new Date(item.tanggal) - new Date(todayStr);
                                            if (diff < minDiff) {
                                                minDiff = diff;
                                                nearestIdx = idx;
                                            }
                                        }
                                    }
                                });
                                activeItem = parsed.jadwal_list[nearestIdx] || parsed.jadwal_list[0];
                            } else if (parsed.ustadz_nama) {
                                activeItem = parsed;
                            }
                        }
                    } catch(e) {
                        console.warn('Gagal membaca cached_kajian_sabtu:', e);
                    }
                }
            }

            if (!activeItem || (!activeItem.ustadz_nama && !activeItem.tema_kajian && !activeItem.kitab_rujukan)) {
                if (isUserClick) {
                    alert('Belum ada data jadwal kajian 1 bulan yang tersimpan. Silakan isi terlebih dahulu di menu "Kajian Malam Ahad" lalu klik "Simpan Agenda 1 Bulan".');
                }
                return false;
            }

            // Ambil elemen form di Pusat Agenda Rutin
            const elJudul = document.getElementById('wsKajianAhadJudul');
            const elWaktu = document.getElementById('wsKajianAhadWaktu');
            const elUstadz = document.getElementById('wsKajianAhadPembimbing');
            const elKet = document.getElementById('wsKajianAhadKet');
            const elStatus = document.getElementById('wsKajianAhadSyncStatus');

            const ustadzNama = (activeItem.ustadz_nama || '').trim();
            const waktuPelaksanaan = (activeItem.waktu_pelaksanaan || "Ba'da Maghrib s/d Isya").trim();

            let ketTema = '';
            if (activeItem.kitab_rujukan && activeItem.tema_kajian && activeItem.kitab_rujukan !== activeItem.tema_kajian) {
                ketTema = `${activeItem.kitab_rujukan} (${activeItem.tema_kajian})`;
            } else {
                ketTema = activeItem.kitab_rujukan || activeItem.tema_kajian || "Kitab Bidayatul Hidayah & Tanya Jawab Fiqih";
            }

            const pekanLabel = activeItem.pekan_label || (activeItem.pekan ? `Pekan ${activeItem.pekan}` : 'Pekan Aktif');
            const tglShort = activeItem.tanggal ? (typeof formatTanggalShort === 'function' ? formatTanggalShort(activeItem.tanggal) : activeItem.tanggal) : '';

            // Update nilai form
            if (elJudul && (!elJudul.value || elJudul.value === 'Kajian Malam Ahad' || isUserClick)) {
                elJudul.value = 'Kajian Malam Ahad';
            }
            if (elWaktu && waktuPelaksanaan) elWaktu.value = waktuPelaksanaan;
            if (elUstadz && ustadzNama) elUstadz.value = ustadzNama;
            if (elKet && ketTema) elKet.value = ketTema;

            // Highlight animasi saat diklik manual
            if (isUserClick) {
                [elJudul, elWaktu, elUstadz, elKet].forEach(el => {
                    if (el) {
                        el.style.transition = 'all 0.4s ease';
                        el.style.borderColor = '#10b981';
                        el.style.backgroundColor = '#ecfdf5';
                        setTimeout(() => {
                            el.style.borderColor = '';
                            el.style.backgroundColor = '';
                        }, 1200);
                    }
                });
            }

            // Tampilkan status sinkronisasi
            if (elStatus) {
                elStatus.style.display = 'block';
                elStatus.innerHTML = `<i class="fas fa-check-circle text-success mr-1"></i> Tersinkronisasi dari <strong>${pekanLabel}</strong>${tglShort ? ' (' + tglShort + ')' : ''}: <em>${ustadzNama}</em>.`;
            }

            // Simpan otomatis ke agenda_rutin_settings di localStorage & Supabase
            try {
                const currentAgenda = getAgendaRutinDataWs();
                currentAgenda.kajian_ahad.judul = (elJudul ? elJudul.value : currentAgenda.kajian_ahad.judul).trim() || 'Kajian Malam Ahad';
                currentAgenda.kajian_ahad.waktu = waktuPelaksanaan;
                currentAgenda.kajian_ahad.pembimbing = ustadzNama;
                currentAgenda.kajian_ahad.keterangan = ketTema;

                localStorage.setItem('agenda_rutin_settings', JSON.stringify(currentAgenda));

                if (typeof SupabaseDB !== 'undefined' && typeof SupabaseDB.saveAgendaRutin === 'function') {
                    SupabaseDB.saveAgendaRutin(currentAgenda).catch(e => console.warn('Sync Supabase agenda rutin gagal:', e));
                } else if (typeof SUPABASE_CONFIG !== 'undefined' && typeof getSupabaseHeaders === 'function') {
                    fetch(`${SUPABASE_CONFIG.url}/rest/v1/app_settings?id=eq.1`, {
                        method: 'PATCH',
                        headers: getSupabaseHeaders(),
                        body: JSON.stringify({ kegiatan_rutin_settings: currentAgenda })
                    }).catch(e => console.warn('Sync Supabase agenda rutin gagal:', e));
                }

                if (typeof renderDashAgendaRutinWidget === 'function') {
                    renderDashAgendaRutinWidget(currentAgenda);
                }
            } catch(e) {
                console.warn('Gagal simpan sync agenda rutin:', e);
            }

            if (isUserClick) {
                alert(`Alhamdulillah! Jadwal Kajian Malam Ahad berhasil disinkronkan dari ${pekanLabel}:\n\n` +
                      `• Pemateri: ${ustadzNama}\n` +
                      `• Kitab / Tema: ${ketTema}\n` +
                      `• Waktu: ${waktuPelaksanaan}\n\n` +
                      `Data langsung otomatis tersimpan dan aktif di Layar Display TV.`);
            }

            return true;
        }

        function loadAgendaRutinAdmin() {
            const data = getAgendaRutinDataWs();

            // 1. Yaasiin
            const wsYasinEnabled = document.getElementById('wsYasinEnabled');
            const wsYasinJudul = document.getElementById('wsYasinJudul');
            const wsYasinWaktu = document.getElementById('wsYasinWaktu');
            const wsYasinPembimbing = document.getElementById('wsYasinPembimbing');
            const wsYasinLokasi = document.getElementById('wsYasinLokasi');

            if (wsYasinEnabled) wsYasinEnabled.checked = !!data.yasin.enabled;
            if (wsYasinJudul) wsYasinJudul.value = data.yasin.judul || '';
            if (wsYasinWaktu) wsYasinWaktu.value = data.yasin.waktu || '';
            if (wsYasinPembimbing) wsYasinPembimbing.value = data.yasin.pembimbing || '';
            if (wsYasinLokasi) wsYasinLokasi.value = data.yasin.lokasi || '';

            // 2. Kajian Ahad
            const wsKajianAhadEnabled = document.getElementById('wsKajianAhadEnabled');
            const wsKajianAhadJudul = document.getElementById('wsKajianAhadJudul');
            const wsKajianAhadWaktu = document.getElementById('wsKajianAhadWaktu');
            const wsKajianAhadPembimbing = document.getElementById('wsKajianAhadPembimbing');
            const wsKajianAhadKet = document.getElementById('wsKajianAhadKet');

            if (wsKajianAhadEnabled) wsKajianAhadEnabled.checked = !!data.kajian_ahad.enabled;
            if (wsKajianAhadJudul) wsKajianAhadJudul.value = data.kajian_ahad.judul || '';
            if (wsKajianAhadWaktu) wsKajianAhadWaktu.value = data.kajian_ahad.waktu || '';
            if (wsKajianAhadPembimbing) wsKajianAhadPembimbing.value = data.kajian_ahad.pembimbing || '';
            if (wsKajianAhadKet) wsKajianAhadKet.value = data.kajian_ahad.keterangan || '';

            // Tampilkan info sinkronisasi aktif jika tersedia
            const elSyncStatus = document.getElementById('wsKajianAhadSyncStatus');
            try {
                const rawKajian = localStorage.getItem('cached_kajian_sabtu');
                if (rawKajian && elSyncStatus) {
                    const parsedKajian = JSON.parse(rawKajian);
                    const todayStr = (new Date()).toISOString().split('T')[0];
                    let activeItem = null;
                    if (Array.isArray(parsedKajian.jadwal_list) && parsedKajian.jadwal_list.length > 0) {
                        let nearestIdx = 0;
                        let minDiff = Infinity;
                        parsedKajian.jadwal_list.forEach((item, idx) => {
                            if (item.tanggal) {
                                if (item.tanggal === todayStr) {
                                    nearestIdx = idx;
                                    minDiff = -1;
                                } else if (item.tanggal >= todayStr && minDiff !== -1) {
                                    const diff = new Date(item.tanggal) - new Date(todayStr);
                                    if (diff < minDiff) {
                                        minDiff = diff;
                                        nearestIdx = idx;
                                    }
                                }
                            }
                        });
                        activeItem = parsedKajian.jadwal_list[nearestIdx] || parsedKajian.jadwal_list[0];
                    } else if (parsedKajian.ustadz_nama) {
                        activeItem = parsedKajian;
                    }

                    if (activeItem && activeItem.ustadz_nama) {
                        const pLabel = activeItem.pekan_label || (activeItem.pekan ? `Pekan ${activeItem.pekan}` : 'Pekan Aktif');
                        const tShort = activeItem.tanggal ? (typeof formatTanggalShort === 'function' ? formatTanggalShort(activeItem.tanggal) : activeItem.tanggal) : '';
                        elSyncStatus.style.display = 'block';
                        elSyncStatus.innerHTML = `<i class="fas fa-check-circle text-success mr-1"></i> Terhubung ke Jadwal 1 Bulan: <strong>${pLabel}</strong>${tShort ? ' (' + tShort + ')' : ''} — <em>${activeItem.ustadz_nama}</em>.`;
                    }
                }
            } catch(e) {}

            // 3. Tahsin (1-Click Days)
            const wsTahsinEnabled = document.getElementById('wsTahsinEnabled');
            const wsTahsinJudul = document.getElementById('wsTahsinJudul');
            const wsTahsinWaktu = document.getElementById('wsTahsinWaktu');
            const wsTahsinPembimbing = document.getElementById('wsTahsinPembimbing');
            const wsTahsinKet = document.getElementById('wsTahsinKet');

            if (wsTahsinEnabled) wsTahsinEnabled.checked = !!data.tahsin.enabled;
            if (wsTahsinJudul) wsTahsinJudul.value = data.tahsin.judul || '';
            if (wsTahsinWaktu) wsTahsinWaktu.value = data.tahsin.waktu || '';
            if (wsTahsinPembimbing) wsTahsinPembimbing.value = data.tahsin.pembimbing || '';
            if (wsTahsinKet) wsTahsinKet.value = data.tahsin.keterangan || '';

            const hariAktif = Array.isArray(data.tahsin.hari_aktif) ? data.tahsin.hari_aktif.map(h => String(h).toLowerCase()) : ['senin', 'rabu', 'sabtu'];
            document.querySelectorAll('#wsTahsinDayPillGroup .ws-day-pill').forEach(btn => {
                const day = btn.getAttribute('data-day');
                const label = day.charAt(0).toUpperCase() + day.slice(1);
                if (hariAktif.includes(day)) {
                    btn.className = 'btn btn-sm ws-day-pill btn-warning font-weight-bold text-dark';
                    btn.innerHTML = `<i class="fas fa-check-circle mr-1"></i> ${label}`;
                } else {
                    btn.className = 'btn btn-sm ws-day-pill btn-outline-secondary';
                    btn.innerHTML = `<i class="fas fa-circle mr-1"></i> ${label}`;
                }
            });

            // 4. Tafsir (2 Pekan Sekali)
            const wsTafsirEnabled = document.getElementById('wsTafsirEnabled');
            const wsTafsirJudul = document.getElementById('wsTafsirJudul');
            const wsTafsirWaktu = document.getElementById('wsTafsirWaktu');
            const wsTafsirPembimbing = document.getElementById('wsTafsirPembimbing');
            const wsTafsirKet = document.getElementById('wsTafsirKet');

            if (wsTafsirEnabled) wsTafsirEnabled.checked = !!data.tafsir.enabled;
            if (wsTafsirJudul) wsTafsirJudul.value = data.tafsir.judul || '';
            if (wsTafsirWaktu) wsTafsirWaktu.value = data.tafsir.waktu || '';
            if (wsTafsirPembimbing) wsTafsirPembimbing.value = data.tafsir.pembimbing || '';
            if (wsTafsirKet) wsTafsirKet.value = data.tafsir.keterangan || '';

            const pekanAktif = Array.isArray(data.tafsir.pekan_aktif) ? data.tafsir.pekan_aktif.map(Number) : [1, 3];
            document.querySelectorAll('.ws-tafsir-pekan').forEach(cb => {
                cb.checked = pekanAktif.includes(Number(cb.value));
            });

            renderDashAgendaRutinWidget(data);
        }

        function simpanAgendaRutinAdmin() {
            // Ambil hari aktif tahsin
            const hariAktif = [];
            document.querySelectorAll('#wsTahsinDayPillGroup .ws-day-pill.btn-warning').forEach(btn => {
                const day = btn.getAttribute('data-day');
                if (day) hariAktif.push(day);
            });

            // Ambil pekan aktif tafsir
            const pekanAktif = [];
            document.querySelectorAll('.ws-tafsir-pekan:checked').forEach(cb => {
                pekanAktif.push(Number(cb.value));
            });

            const payload = {
                yasin: {
                    enabled: document.getElementById('wsYasinEnabled') ? document.getElementById('wsYasinEnabled').checked : true,
                    judul: (document.getElementById('wsYasinJudul') ? document.getElementById('wsYasinJudul').value : '').trim() || 'Pembacaan Surat Yaasiin & Tahlil',
                    hari_teks: 'Setiap Malam Jum\'at (Kamis Malam)',
                    waktu: (document.getElementById('wsYasinWaktu') ? document.getElementById('wsYasinWaktu').value : '').trim() || 'Ba\'da Maghrib s/d Isya',
                    pembimbing: (document.getElementById('wsYasinPembimbing') ? document.getElementById('wsYasinPembimbing').value : '').trim() || 'Imam Rawatib / Asatidz Masjid',
                    lokasi: (document.getElementById('wsYasinLokasi') ? document.getElementById('wsYasinLokasi').value : '').trim() || 'Ruang Utama Masjid Jami\' Al Jihad'
                },
                kajian_ahad: {
                    enabled: document.getElementById('wsKajianAhadEnabled') ? document.getElementById('wsKajianAhadEnabled').checked : true,
                    judul: (document.getElementById('wsKajianAhadJudul') ? document.getElementById('wsKajianAhadJudul').value : '').trim() || 'Kajian Malam Ahad',
                    hari_teks: 'Setiap Malam Ahad (Sabtu Malam)',
                    waktu: (document.getElementById('wsKajianAhadWaktu') ? document.getElementById('wsKajianAhadWaktu').value : '').trim() || 'Ba\'da Maghrib s/d Isya',
                    pembimbing: (document.getElementById('wsKajianAhadPembimbing') ? document.getElementById('wsKajianAhadPembimbing').value : '').trim(),
                    keterangan: (document.getElementById('wsKajianAhadKet') ? document.getElementById('wsKajianAhadKet').value : '').trim()
                },
                tahsin: {
                    enabled: document.getElementById('wsTahsinEnabled') ? document.getElementById('wsTahsinEnabled').checked : true,
                    judul: (document.getElementById('wsTahsinJudul') ? document.getElementById('wsTahsinJudul').value : '').trim() || 'Bimbingan Tahsin Al-Qur\'an',
                    hari_aktif: hariAktif.length > 0 ? hariAktif : ['senin', 'rabu', 'sabtu'],
                    waktu: (document.getElementById('wsTahsinWaktu') ? document.getElementById('wsTahsinWaktu').value : '').trim() || 'Ba\'da Sholat Isya (20:00 WIB)',
                    pembimbing: (document.getElementById('wsTahsinPembimbing') ? document.getElementById('wsTahsinPembimbing').value : '').trim() || 'Ust. Pembina Tahsin Al-Qur\'an',
                    keterangan: (document.getElementById('wsTahsinKet') ? document.getElementById('wsTahsinKet').value : '').trim() || 'Terbuka untuk Jamaah Ikhwan & Akhwat (Semua Usia)'
                },
                tafsir: {
                    enabled: document.getElementById('wsTafsirEnabled') ? document.getElementById('wsTafsirEnabled').checked : true,
                    judul: (document.getElementById('wsTafsirJudul') ? document.getElementById('wsTafsirJudul').value : '').trim() || 'Kajian Umum Tafsir Al-Qur\'an',
                    pekan_aktif: pekanAktif.length > 0 ? pekanAktif : [1, 3],
                    waktu: (document.getElementById('wsTafsirWaktu') ? document.getElementById('wsTafsirWaktu').value : '').trim() || 'Ahad Ba\'da Subuh (05:15 WIB)',
                    pembimbing: (document.getElementById('wsTafsirPembimbing') ? document.getElementById('wsTafsirPembimbing').value : '').trim() || 'Asatidz Dewan Syari\'ah Masjid',
                    keterangan: (document.getElementById('wsTafsirKet') ? document.getElementById('wsTafsirKet').value : '').trim() || 'Tafsir Ayat-ayat Pilihan & Sarapan Pagi Bersama'
                }
            };

            // Simpan ke LocalStorage
            localStorage.setItem('agenda_rutin_settings', JSON.stringify(payload));

            // Simpan ke Supabase jika tersedia (Multi-row key-value + Realtime Broadcast)
            if (typeof SupabaseDB !== 'undefined' && typeof SupabaseDB.saveAgendaRutin === 'function') {
                SupabaseDB.saveAgendaRutin(payload);
            } else if (typeof SupabaseDB !== 'undefined' && typeof SupabaseDB.saveSettings === 'function') {
                SupabaseDB.saveSettings({ kegiatan_rutin_settings: payload });
            }

            renderDashAgendaRutinWidget(payload);
            alert('Alhamdulillah! Jadwal Agenda Rutin Masjid berhasil disimpan dan langsung disinkronkan ke Layar TV!');
        }

        function renderDashAgendaRutinWidget(data) {
            if (!data) data = getAgendaRutinDataWs();

            // Yasin
            const elYasinJudul = document.getElementById('dashTextYasinJudul');
            const elYasinWaktu = document.getElementById('dashTextYasinWaktu');
            const elYasinBadge = document.getElementById('dashBadgeYasin');
            if (elYasinJudul) elYasinJudul.textContent = data.yasin.judul;
            if (elYasinWaktu) elYasinWaktu.textContent = data.yasin.waktu;
            if (elYasinBadge) {
                elYasinBadge.innerHTML = data.yasin.enabled ? '<i class="fas fa-check-circle text-success"></i> Aktif TV' : '<i class="fas fa-times-circle text-danger"></i> Nonaktif';
            }

            // Kajian Ahad
            const elKajianJudul = document.getElementById('dashTextKajianAhadJudul');
            const elKajianWaktu = document.getElementById('dashTextKajianAhadWaktu');
            const elKajianBadge = document.getElementById('dashBadgeKajianAhad');
            if (elKajianJudul) elKajianJudul.textContent = data.kajian_ahad.judul;
            if (elKajianWaktu) elKajianWaktu.textContent = data.kajian_ahad.waktu;
            if (elKajianBadge) {
                elKajianBadge.innerHTML = data.kajian_ahad.enabled ? '<i class="fas fa-check-circle text-success"></i> Aktif TV' : '<i class="fas fa-times-circle text-danger"></i> Nonaktif';
            }

            // Tahsin
            const elTahsinJudul = document.getElementById('dashTextTahsinJudul');
            const elTahsinWaktu = document.getElementById('dashTextTahsinWaktu');
            const elTahsinBadge = document.getElementById('dashBadgeTahsin');
            const elTahsinDays = document.getElementById('dashTahsinDaysList');
            if (elTahsinJudul) elTahsinJudul.textContent = data.tahsin.judul;
            if (elTahsinWaktu) elTahsinWaktu.textContent = data.tahsin.waktu;
            if (elTahsinBadge) {
                elTahsinBadge.innerHTML = data.tahsin.enabled ? '<i class="fas fa-check-circle text-success"></i> Aktif TV' : '<i class="fas fa-times-circle text-danger"></i> Nonaktif';
            }
            if (elTahsinDays) {
                const days = Array.isArray(data.tahsin.hari_aktif) ? data.tahsin.hari_aktif : ['senin', 'rabu', 'sabtu'];
                elTahsinDays.innerHTML = days.map(d => {
                    const cap = d.charAt(0).toUpperCase() + d.slice(1);
                    return `<span class="badge badge-warning text-dark mr-1 mb-1">${cap}</span>`;
                }).join('');
            }

            // Tafsir
            const elTafsirJudul = document.getElementById('dashTextTafsirJudul');
            const elTafsirWaktu = document.getElementById('dashTextTafsirWaktu');
            const elTafsirBadge = document.getElementById('dashBadgeTafsir');
            const elTafsirPekan = document.getElementById('dashTafsirPekanList');
            if (elTafsirJudul) elTafsirJudul.textContent = data.tafsir.judul;
            if (elTafsirWaktu) elTafsirWaktu.textContent = data.tafsir.waktu;
            if (elTafsirBadge) {
                elTafsirBadge.innerHTML = data.tafsir.enabled ? '<i class="fas fa-check-circle text-success"></i> Aktif TV' : '<i class="fas fa-times-circle text-danger"></i> Nonaktif';
            }
            if (elTafsirPekan) {
                const pekans = Array.isArray(data.tafsir.pekan_aktif) ? data.tafsir.pekan_aktif : [1, 3];
                elTafsirPekan.textContent = 'Pekan Ke: ' + pekans.join(' & ');
            }
        }

        
        // =========================================================================
        // =========================================================================