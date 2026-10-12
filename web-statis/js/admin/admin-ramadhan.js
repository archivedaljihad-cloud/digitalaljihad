/**
 * MODUL ADMIN: admin-ramadhan.js
 * Digital Signage Masjid Jami' Al-Jihad
 * Versi: aljihad-signage-v5.8.45
 */

        // 9. MODUL RAMADHAN: AGENDA TARAWIH & KAS TROMOL INFAQ
        // =======================================================
        const DEFAULT_ADMIN_RAMADHAN_PETUGAS = {
            malam_ke: 'Malam Ke-15 Ramadhan 1448 H',
            imam: 'Ust. H. Ahmad Fauzi, Lc.',
            muadzin: 'Bpk. Dedi Supriyadi',
            kultum_penceramah: 'Drs. KH. Syarifuddin, M.Ag.',
            kultum_tema: '', // Opsional
            bilal: 'Ust. Muhammad Ridwan',
            hadits: '"Barangsiapa mendirikan sholat malam di bulan Ramadhan karena iman dan mengharap pahala dari Allah, niscaya diampuni dosa-dosanya yang telah lalu." (HR. Bukhari & Muslim)'
        };

        const DEFAULT_ADMIN_RAMADHAN_INFAQ = [
            { id: 1, tanggal: '2026-03-01', uraian: 'Kotak Tromol Tarawih Malam 1', jenis: 'masuk', nominal: 2450000 },
            { id: 2, tanggal: '2026-03-02', uraian: 'Konsumsi Takjil & Air Mineral Jamaah', jenis: 'keluar', nominal: 650000 },
            { id: 3, tanggal: '2026-03-02', uraian: 'Kotak Tromol Tarawih Malam 2', jenis: 'masuk', nominal: 2850000 }
        ];

        let cachedRamadhanPetugas = null;
        let cachedRamadhanInfaq = null;

        // Render Seluruh Tampilan Modul Ramadhan di Panel Admin
        function renderAdminRamadhanView() {
            // 1. Muat Cache Petugas jika belum ada
            if (!cachedRamadhanPetugas) {
                const localPet = localStorage.getItem('cached_ramadhan_petugas');
                if (localPet) {
                    try { cachedRamadhanPetugas = JSON.parse(localPet); } catch (e) {}
                }
                if (!cachedRamadhanPetugas) cachedRamadhanPetugas = { ...DEFAULT_ADMIN_RAMADHAN_PETUGAS };
            }

            // Isi nilai form Petugas
            if (document.getElementById('inputRamadhanMalamKe')) document.getElementById('inputRamadhanMalamKe').value = cachedRamadhanPetugas.malam_ke || '';
            if (document.getElementById('inputRamadhanImam')) document.getElementById('inputRamadhanImam').value = cachedRamadhanPetugas.imam || '';
            if (document.getElementById('inputRamadhanMuadzin')) document.getElementById('inputRamadhanMuadzin').value = cachedRamadhanPetugas.muadzin || '';
            if (document.getElementById('inputRamadhanKultum')) document.getElementById('inputRamadhanKultum').value = cachedRamadhanPetugas.kultum_penceramah || '';
            if (document.getElementById('inputRamadhanTema')) document.getElementById('inputRamadhanTema').value = cachedRamadhanPetugas.kultum_tema || ''; // Tema Kultum Bersifat Opsional
            if (document.getElementById('inputRamadhanBilal')) document.getElementById('inputRamadhanBilal').value = cachedRamadhanPetugas.bilal || '';
            if (document.getElementById('inputRamadhanHadits')) document.getElementById('inputRamadhanHadits').value = cachedRamadhanPetugas.hadits || '';

            // 2. Muat Cache Infaq jika belum ada
            if (!cachedRamadhanInfaq) {
                const localInf = localStorage.getItem('cached_ramadhan_infaq');
                if (localInf) {
                    try { cachedRamadhanInfaq = JSON.parse(localInf); } catch (e) {}
                }
                if (!cachedRamadhanInfaq || !Array.isArray(cachedRamadhanInfaq)) cachedRamadhanInfaq = [...DEFAULT_ADMIN_RAMADHAN_INFAQ];
            }

            // Hitung 3 Metrik: Total Pendapatan (+), Total Pengeluaran (-), Saldo Tromol (=)
            let totalMasuk = 0;
            let totalKeluar = 0;
            cachedRamadhanInfaq.forEach(item => {
                const nom = parseFloat(item.nominal) || 0;
                if (item.jenis === 'keluar') {
                    totalKeluar += nom;
                } else {
                    totalMasuk += nom;
                }
            });
            const saldo = totalMasuk - totalKeluar;

            if (document.getElementById('cardRamadhanTotalMasuk')) {
                document.getElementById('cardRamadhanTotalMasuk').textContent = `Rp ${totalMasuk.toLocaleString('id-ID')}`;
            }
            if (document.getElementById('cardRamadhanTotalKeluar')) {
                document.getElementById('cardRamadhanTotalKeluar').textContent = `Rp ${totalKeluar.toLocaleString('id-ID')}`;
            }
            if (document.getElementById('cardRamadhanSaldo')) {
                document.getElementById('cardRamadhanSaldo').textContent = `Rp ${saldo.toLocaleString('id-ID')}`;
            }

            // Render Tabel Mutasi Tromol Ramadhan
            const tbody = document.getElementById('adminTbodyTromolRamadhan');
            if (tbody) {
                if (cachedRamadhanInfaq.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="5" class="text-center text-muted py-3">Belum ada pencatatan infaq tromol Ramadhan.</td></tr>`;
                } else {
                    tbody.innerHTML = cachedRamadhanInfaq.map((it, idx) => {
                        const isKeluar = it.jenis === 'keluar';
                        const badge = isKeluar ? '<span class="badge badge-danger">Pengeluaran</span>' : '<span class="badge badge-success">Penerimaan</span>';
                        const sign = isKeluar ? '-' : '+';
                        const colorClass = isKeluar ? 'text-danger' : 'text-success';
                        const tgl = it.tanggal ? it.tanggal : '-';
                        const nom = (parseFloat(it.nominal) || 0).toLocaleString('id-ID');
                        return `
                            <tr>
                                <td><small class="text-muted">${tgl}</small></td>
                                <td><strong>${escapeHtml(it.uraian || 'Tromol Ramadhan')}</strong></td>
                                <td>${badge}</td>
                                <td class="text-right font-weight-bold ${colorClass}">${sign} Rp ${nom}</td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-danger py-0 px-1" title="Hapus catatan" onclick="hapusTransaksiTromol(${idx})">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                    }).join('');
                }
            }

            // Set default tanggal transaksi hari ini
            if (document.getElementById('inputTromolTanggal') && !document.getElementById('inputTromolTanggal').value) {
                document.getElementById('inputTromolTanggal').value = new Date().toISOString().split('T')[0];
            }

            // Update status switch mode TV
            if (cachedRotationPages && cachedRotationPages.length > 0) {
                const rPage = cachedRotationPages.find(p => (p.url || p.path || '').includes('ramadhan'));
                const isActive = rPage ? (rPage.active !== false) : true;
                if (document.getElementById('switchModeRamadhanTV')) {
                    document.getElementById('switchModeRamadhanTV').checked = isActive;
                }
                if (document.getElementById('lblSwitchRamadhan')) {
                    document.getElementById('lblSwitchRamadhan').textContent = isActive ? 'Mode Ramadhan TV Aktif' : 'Mode Ramadhan TV Nonaktif';
                    document.getElementById('lblSwitchRamadhan').className = isActive ? 'custom-control-label font-weight-bold text-success' : 'custom-control-label font-weight-bold text-secondary';
                }
            }
        }

        // Simpan Petugas Ramadhan Malam Ini (Tema Kultum Bersifat Opsional)
        async function simpanPetugasRamadhan(e) {
            if (e) e.preventDefault();
            const btn = document.getElementById('btnSimpanPetugasRamadhan');
            const originalText = btn ? btn.innerHTML : '';
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...';
            }

            const dataPetugas = {
                malam_ke: document.getElementById('inputRamadhanMalamKe')?.value.trim() || 'Malam Ramadhan',
                imam: document.getElementById('inputRamadhanImam')?.value.trim() || '-',
                muadzin: document.getElementById('inputRamadhanMuadzin')?.value.trim() || '-',
                kultum_penceramah: document.getElementById('inputRamadhanKultum')?.value.trim() || '-',
                kultum_tema: document.getElementById('inputRamadhanTema')?.value.trim() || '', // Tema Kultum Bersifat Opsional
                bilal: document.getElementById('inputRamadhanBilal')?.value.trim() || '-',
                hadits: document.getElementById('inputRamadhanHadits')?.value.trim() || ''
            };

            cachedRamadhanPetugas = dataPetugas;
            localStorage.setItem('cached_ramadhan_petugas', JSON.stringify(dataPetugas));

            try {
                let resOk = false;
                if (window.SupabaseDB && typeof SupabaseDB.saveRamadhanUnified === 'function') {
                    const rRes = await SupabaseDB.saveRamadhanUnified(dataPetugas);
                    resOk = rRes.success;
                } else {
                    const res = await fetch(`${SUPABASE_CONFIG.url}/rest/v1/app_settings?id=eq.1`, {
                        method: 'PATCH',
                        headers: getSupabaseHeaders(),
                        body: JSON.stringify({
                            ramadhan_petugas: dataPetugas
                        })
                    });
                    resOk = res.ok;
                    if (resOk) {
                        if (window.SupabaseDB && typeof SupabaseDB.broadcastChange === 'function') {
                            SupabaseDB.broadcastChange('ramadhan', 'UPDATE', { ramadhan_petugas: dataPetugas }, 'Panitia Ramadhan');
                        }
                        if (window.SupabaseDB && typeof SupabaseDB.sendRemoteCommand === 'function') {
                            SupabaseDB.sendRemoteCommand('SYNC_RAMADHAN', { ramadhan_petugas: dataPetugas });
                        }
                    }
                }

                if (resOk) {
                    alert('Alhamdulillah! Jadwal petugas tarawih & kultum Ramadhan berhasil disimpan dan langsung tayang di TV Display.');
                } else {
                    alert('Tersimpan di cache lokal browser (offline-ready).');
                }
            } catch (err) {
                alert('Tersimpan di cache lokal: ' + err.message);
            } finally {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
                renderAdminRamadhanView();
            }
        }

        // Tambah Catatan Mutasi Kas Tromol Ramadhan
        async function tambahTransaksiTromol(e) {
            if (e) e.preventDefault();
            const tgl = document.getElementById('inputTromolTanggal')?.value || new Date().toISOString().split('T')[0];
            const jenis = document.getElementById('inputTromolJenis')?.value || 'masuk';
            const uraian = document.getElementById('inputTromolUraian')?.value.trim();
            const nominal = parseFloat(document.getElementById('inputTromolNominal')?.value) || 0;

            if (!uraian || nominal <= 0) {
                alert('Harap isi uraian dan nominal transaksi dengan benar!');
                return;
            }

            if (!cachedRamadhanInfaq) cachedRamadhanInfaq = [];
            const newTrans = {
                id: Date.now(),
                tanggal: tgl,
                jenis: jenis,
                uraian: uraian,
                nominal: nominal
            };

            // Masukkan di urutan paling atas
            cachedRamadhanInfaq.unshift(newTrans);
            localStorage.setItem('cached_ramadhan_infaq', JSON.stringify(cachedRamadhanInfaq));

            try {
                await fetch(`${SUPABASE_CONFIG.url}/rest/v1/app_settings?id=eq.1`, {
                    method: 'PATCH',
                    headers: getSupabaseHeaders(),
                    body: JSON.stringify({
                        ramadhan_infaq: cachedRamadhanInfaq
                    })
                });
            } catch (err) {
                console.warn('Sync tromol Supabase dilewati:', err);
            }

            if (window.SupabaseDB && typeof SupabaseDB.broadcastChange === 'function') {
                SupabaseDB.broadcastChange('ramadhan', 'UPDATE', { ramadhan_infaq: cachedRamadhanInfaq }, 'Panitia Ramadhan');
            }
            if (window.SupabaseDB && typeof SupabaseDB.sendRemoteCommand === 'function') {
                SupabaseDB.sendRemoteCommand('SYNC_RAMADHAN', { ramadhan_infaq: cachedRamadhanInfaq });
            }

            // Reset field formulir
            if (document.getElementById('inputTromolUraian')) document.getElementById('inputTromolUraian').value = '';
            if (document.getElementById('inputTromolNominal')) document.getElementById('inputTromolNominal').value = '';
            renderAdminRamadhanView();
            alert(`Catatan kas tromol "${uraian}" sebesar Rp ${nominal.toLocaleString('id-ID')} berhasil disimpan!`);
        }

        // Hapus Transaksi Kas Tromol
        async function hapusTransaksiTromol(idx) {
            if (!cachedRamadhanInfaq || idx < 0 || idx >= cachedRamadhanInfaq.length) return;
            const target = cachedRamadhanInfaq[idx];
            if (confirm(`Apakah Anda yakin ingin menghapus catatan "${target.uraian}" (Rp ${(target.nominal || 0).toLocaleString('id-ID')})?`)) {
                cachedRamadhanInfaq.splice(idx, 1);
                localStorage.setItem('cached_ramadhan_infaq', JSON.stringify(cachedRamadhanInfaq));
                try {
                    await fetch(`${SUPABASE_CONFIG.url}/rest/v1/app_settings?id=eq.1`, {
                        method: 'PATCH',
                        headers: getSupabaseHeaders(),
                        body: JSON.stringify({
                            ramadhan_infaq: cachedRamadhanInfaq
                        })
                    });
                } catch (err) {
                    console.warn('Sync hapus tromol Supabase:', err);
                }
                if (window.SupabaseDB && typeof SupabaseDB.broadcastChange === 'function') {
                    SupabaseDB.broadcastChange('ramadhan', 'UPDATE', { ramadhan_infaq: cachedRamadhanInfaq }, 'Panitia Ramadhan');
                }
                if (window.SupabaseDB && typeof SupabaseDB.sendRemoteCommand === 'function') {
                    SupabaseDB.sendRemoteCommand('SYNC_RAMADHAN', { ramadhan_infaq: cachedRamadhanInfaq });
                }
                renderAdminRamadhanView();
            }
        }

        // Switch Aktif/Nonaktif Layar Slide Ramadhan di TV
        async function toggleModeRamadhanTV(isActive) {
            if (!cachedRotationPages || cachedRotationPages.length === 0) {
                cachedRotationPages = normalizeRotationPages(localStorage.getItem('cached_rotation_pages'));
            }

            let rPage = cachedRotationPages.find(p => (p.url || p.path || '').includes('ramadhan'));
            if (rPage) {
                rPage.active = isActive;
            } else {
                cachedRotationPages.push({
                    order: 18,
                    name: 'Semarak Ramadhan & Kas Tromol',
                    path: 'slides/ramadhan.html',
                    url: 'slides/ramadhan.html',
                    page: '/ramadhan-embed',
                    category: 'Ramadhan',
                    category_color: 'warning text-dark',
                    active: isActive
                });
            }

            cachedRotationPages = normalizeRotationPages(cachedRotationPages);
            localStorage.setItem('cached_rotation_pages', JSON.stringify(cachedRotationPages));
            if (document.getElementById('lblSwitchRamadhan')) {
                document.getElementById('lblSwitchRamadhan').textContent = isActive ? 'Mode Ramadhan TV Aktif' : 'Mode Ramadhan TV Nonaktif';
                document.getElementById('lblSwitchRamadhan').className = isActive ? 'custom-control-label font-weight-bold text-success' : 'custom-control-label font-weight-bold text-secondary';
            }

            try {
                await fetch(`${SUPABASE_CONFIG.url}/rest/v1/app_settings?id=eq.1`, {
                    method: 'PATCH',
                    headers: getSupabaseHeaders(),
                    body: JSON.stringify({
                        rotation_pages: cachedRotationPages
                    })
                });
            } catch (err) {
                console.warn('Sync toggle rotasi ramadhan Supabase:', err);
            }

            if (window.SupabaseDB && typeof SupabaseDB.broadcastChange === 'function') {
                SupabaseDB.broadcastChange('app_settings', 'UPDATE', { rotation_pages: cachedRotationPages }, 'Panitia Ramadhan');
            }

            renderRotationTable(cachedRotationPages);
            alert(isActive ? 'Layar Semarak Ramadhan & Kas Tromol kini AKTIF di rotasi TV display.' : 'Layar Semarak Ramadhan dinonaktifkan dari rotasi TV.');
        }

        // Switch Aktif/Nonaktif Slide Layar Penuh "Maklumat & Kas Jum'at" di TV (Smart Scheduling)