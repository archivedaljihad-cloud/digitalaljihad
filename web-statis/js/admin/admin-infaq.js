/**
 * MODUL ADMIN: admin-infaq.js
 * Digital Signage Masjid Jami' Al-Jihad
 * Versi: aljihad-signage-v5.8.45
 */

        // MANAJEMEN MULTI-PROGRAM PENGGALANGAN INFAQ & DONATUR (IDENTIK WEB DINAMIS)
        // =======================================================

        function renderProgramInfaqSection() {
            const selectEl = document.getElementById('selectProgramInfaq');
            const containerDetail = document.getElementById('containerDetailProgramInfaq');
            const emptyState = document.getElementById('emptyProgramInfaqState');

            if (!cachedProgramInfaq || cachedProgramInfaq.length === 0) {
                if (containerDetail) containerDetail.classList.add('d-none');
                if (emptyState) emptyState.classList.remove('d-none');
                if (selectEl) selectEl.innerHTML = '<option value="">(Belum Ada Program)</option>';
                const btnGroup = document.getElementById('btnGroupProgramActions');
                if (btnGroup) btnGroup.innerHTML = '';
                return;
            }

            if (emptyState) emptyState.classList.add('d-none');
            if (containerDetail) containerDetail.classList.remove('d-none');

            // Pastikan ada selectedProgramInfaqId yang valid
            let currentProg = cachedProgramInfaq.find(p => p.id == selectedProgramInfaqId);
            if (!currentProg) {
                currentProg = cachedProgramInfaq.find(p => p.is_active === true) || cachedProgramInfaq[0];
                selectedProgramInfaqId = currentProg.id;
            }

            // 1. Render Dropdown Selector Program
            if (selectEl) {
                selectEl.innerHTML = cachedProgramInfaq.map(p => {
                    const tagActive = p.is_active ? ' ★ (Tayang di TV)' : ' ✕ (Ditutup/Nonaktif)';
                    return `<option value="${p.id}" ${p.id == selectedProgramInfaqId ? 'selected' : ''}>${escapeHtml(p.nama_program)}${tagActive}</option>`;
                }).join('');
            }

            // 2. Render Tombol Aksi di samping Selector
            const btnGroup = document.getElementById('btnGroupProgramActions');
            if (btnGroup) {
                const isActive = currentProg.is_active === true;
                const statusBtn = isActive 
                    ? `<button class="btn btn-success btn-sm shadow-sm mr-2 mb-1 font-weight-bold" onclick="toggleStatusProgramInfaqTV(${currentProg.id})" title="Klik untuk menonaktifkan/menutup slide ini dari TV"><i class="fas fa-check-circle mr-1"></i> Tayang di TV (Aktif)</button>`
                    : `<button class="btn btn-outline-secondary btn-sm shadow-sm mr-2 mb-1 font-weight-bold" onclick="toggleStatusProgramInfaqTV(${currentProg.id})" title="Klik untuk mengaktifkan dan menampilkan slide ini di TV"><i class="fas fa-eye-slash mr-1"></i> Ditutup (Klik utk Tayangkan)</button>`;

                btnGroup.innerHTML = `
                    ${statusBtn}
                    <div class="btn-group mr-2 mb-1">
                        <button type="button" class="btn btn-info btn-sm shadow-sm dropdown-toggle font-weight-bold" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-file-export mr-1"></i> Export Laporan
                        </button>
                        <div class="dropdown-menu shadow">
                            <a class="dropdown-item font-weight-bold" href="javascript:void(0)" onclick="exportLaporanInfaqCSV(${currentProg.id})">
                                <i class="fas fa-file-excel text-success mr-2"></i> Download Excel (.CSV)
                            </a>
                            <a class="dropdown-item font-weight-bold" href="javascript:void(0)" onclick="cetakLaporanInfaqPDF(${currentProg.id})">
                                <i class="fas fa-print text-warning mr-2"></i> Cetak Laporan Resmi (PDF)
                            </a>
                        </div>
                    </div>
                    <button class="btn btn-warning btn-sm shadow-sm mr-2 mb-1 font-weight-bold" onclick="bukaModalEditProgramInfaq(${currentProg.id})">
                        <i class="fas fa-edit mr-1"></i> Edit Target
                    </button>
                    <button class="btn btn-danger btn-sm shadow-sm mb-1 font-weight-bold" onclick="hapusProgramInfaq(${currentProg.id})" ${cachedProgramInfaq.length <= 1 ? 'disabled title="Minimal harus ada 1 program"' : ''}>
                        <i class="fas fa-trash mr-1"></i> Hapus
                    </button>
                `;
            }

            // 3. Render Detail Banner & Periode
            if (document.getElementById('detailProgNama')) {
                document.getElementById('detailProgNama').textContent = currentProg.nama_program;
            }
            if (document.getElementById('detailProgKet')) {
                document.getElementById('detailProgKet').textContent = currentProg.keterangan || 'Program penggalangan infaq & donasi jamaah.';
            }
            if (document.getElementById('detailProgPeriode')) {
                const tglMulai = formatDateIndo(currentProg.tanggal_mulai);
                const tglSelesai = formatDateIndo(currentProg.tanggal_selesai);
                const statusTayang = currentProg.is_active ? '<span class="badge badge-success ml-2">Tayang di TV</span>' : '<span class="badge badge-secondary ml-2">Ditutup / Tidak Tayang</span>';
                document.getElementById('detailProgPeriode').innerHTML = `<i class="fas fa-calendar-alt text-primary mr-1"></i> Periode: ${tglMulai} s/d ${tglSelesai} ${statusTayang}`;
            }

            // 4. Hitung Donasi Masuk Khusus Program Terpilih (Urutan: Paling lama di atas, paling baru di bawah)
            const donasiList = (cachedDonasiInfaq || [])
                .filter(d => d.program_infaq_id == currentProg.id)
                .sort((a, b) => {
                    const dateA = new Date(a.tanggal || 0).getTime();
                    const dateB = new Date(b.tanggal || 0).getTime();
                    if (dateA !== dateB) return dateA - dateB;
                    return (parseInt(a.id) || 0) - (parseInt(b.id) || 0);
                });
            const totalTerkumpul = donasiList.reduce((acc, d) => acc + (parseFloat(d.nominal) || 0), 0);
            const targetDana = parseFloat(currentProg.target_dana) || 0;
            const sisaDana = Math.max(0, targetDana - totalTerkumpul);
            const jumlahDonatur = donasiList.length;
            const persentase = targetDana > 0 ? Math.min(100, (totalTerkumpul / targetDana) * 100).toFixed(1) : 0;

            // 5. Update Progress Bar
            if (document.getElementById('detailProgPersenText')) {
                const isTercapai = targetDana > 0 && totalTerkumpul >= targetDana;
                document.getElementById('detailProgPersenText').innerHTML = isTercapai 
                    ? `<span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle mr-1"></i> 100% Target Terpenuhi!</span>` 
                    : `${persentase}% Terkumpul`;
            }
            const pBar = document.getElementById('detailProgProgressBar');
            if (pBar) {
                pBar.style.width = persentase + '%';
                pBar.textContent = persentase + '%';
                pBar.setAttribute('aria-valuenow', persentase);
                if (targetDana > 0 && totalTerkumpul >= targetDana) {
                    pBar.className = 'progress-bar bg-success font-weight-bold';
                }
            }

            // 6. Update 4 Summary Cards
            if (document.getElementById('cardTargetDana')) document.getElementById('cardTargetDana').textContent = formatRupiah(targetDana);
            if (document.getElementById('cardDanaTerkumpul')) document.getElementById('cardDanaTerkumpul').textContent = formatRupiah(totalTerkumpul);
            if (document.getElementById('cardSisaDana')) {
                if (targetDana > 0 && totalTerkumpul >= targetDana) {
                    document.getElementById('cardSisaDana').innerHTML = '<span class="text-success" style="font-size: 1.1rem;">Rp 0 (Terpenuhi)</span>';
                } else {
                    document.getElementById('cardSisaDana').textContent = formatRupiah(sisaDana);
                }
            }
            if (document.getElementById('cardJumlahDonatur')) document.getElementById('cardJumlahDonatur').textContent = `${jumlahDonatur} Orang/Hamba Allah`;
            if (document.getElementById('badgeCountDonatur')) document.getElementById('badgeCountDonatur').textContent = `${jumlahDonatur} Data`;

            // 7. Render Tabel Daftar Penerimaan Infaq Donatur
            const tbody = document.getElementById('tableDonaturInfaqBody');
            if (tbody) {
                if (donasiList.length === 0) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="6" class="text-center py-5" style="background-color: #f8fafc;">
                                <i class="fas fa-info-circle fa-3x mb-2 text-info d-block"></i>
                                <h6 class="font-weight-bold text-gray-800 mb-1" style="font-size: 1.05rem;">
                                    Belum ada catatan infaq untuk program ini
                                </h6>
                                <p class="text-gray-600 small mb-0 font-weight-500">
                                    Klik tombol <span class="badge badge-success px-2 py-1"><i class="fas fa-plus-circle mr-1"></i> Catat Donasi Masuk</span> di atas untuk membuka formulir dan mencatat donasi masuk.
                                </p>
                            </td>
                        </tr>
                    `;
                } else {
                    tbody.innerHTML = donasiList.map((d, idx) => {
                        const tglStr = formatDateIndo(d.tanggal);
                        const isAnon = d.is_anonim === true || d.nama_donatur === 'Hamba Allah';
                        const namaBadge = isAnon 
                            ? `<span class="badge badge-success px-2 py-1" style="font-size: 0.82rem; font-weight: 600;"><i class="fas fa-user-secret mr-1"></i> Hamba Allah</span>`
                            : `<span style="font-size: 0.88rem; font-weight: 600; color: #1e293b;"><i class="fas fa-user-circle text-primary mr-1" style="font-size: 0.82rem;"></i> ${escapeHtml(d.nama_donatur || 'Hamba Allah')}</span>`;

                        return `
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td class="align-middle text-muted font-weight-bold" style="padding: 9px 6px; font-size: 0.84rem;">${idx + 1}</td>
                                <td class="align-middle" style="white-space: nowrap; padding: 9px 8px; font-size: 0.85rem; color: #334155; font-weight: 600;">
                                    <i class="far fa-calendar-alt text-success mr-1" style="font-size: 0.8rem;"></i>${tglStr}
                                </td>
                                <td class="text-left align-middle" style="padding: 9px 10px;">${namaBadge}</td>
                                <td class="text-right align-middle" style="padding: 9px 10px;">
                                    <span style="font-size: 0.94rem; font-weight: 800; color: #047857;">${formatRupiah(d.nominal)}</span>
                                </td>
                                <td class="text-left align-middle" style="padding: 9px 10px;">
                                    <span style="font-size: 0.84rem; color: #475569; font-weight: 500;">${escapeHtml(d.keterangan || '-')}</span>
                                </td>
                                <td class="align-middle" style="padding: 7px 6px; white-space: nowrap;">
                                    <button class="btn btn-warning btn-sm p-0 shadow-sm mr-1.5" style="width: 28px; height: 28px; line-height: 28px; border-radius: 6px;" onclick="bukaModalEditDonasiInfaq(${d.id})" title="Edit Donatur">
                                        <i class="fas fa-edit text-dark" style="font-size: 0.76rem;"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm p-0 shadow-sm" style="width: 28px; height: 28px; line-height: 28px; border-radius: 6px;" onclick="hapusDonasiInfaq(${d.id})" title="Hapus Donatur">
                                        <i class="fas fa-trash" style="font-size: 0.76rem;"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                    }).join('');
                }
            }
        }

        // Pindah Program Terpilih dari Dropdown Selector
        function gantiProgramInfaqTerpilih(progId) {
            selectedProgramInfaqId = progId;
            renderProgramInfaqSection();
        }

        // Toggle Checkbox Hamba Allah
        function toggleAnonimDonasi(checkbox) {
            const input = document.getElementById('donasiNama');
            if (!input) return;
            if (checkbox.checked) {
                input.value = 'Hamba Allah';
                input.setAttribute('disabled', 'disabled');
            } else {
                input.value = '';
                input.removeAttribute('disabled');
                input.focus();
            }
        }

        // Simpan Donasi Infaq Masuk (Spesifik Program Ini)
        async function simpanDonasiInfaq() {
            if (!selectedProgramInfaqId) {
                alert('Pilih program penggalangan infaq terlebih dahulu!');
                return;
            }

            const currentProg = cachedProgramInfaq.find(p => p.id == selectedProgramInfaqId);
            const tgl = document.getElementById('donasiTanggal')?.value || new Date().toISOString().split('T')[0];
            const isAnonim = document.getElementById('donasiIsAnonim')?.checked === true;
            let nama = document.getElementById('donasiNama')?.value.trim();
            if (isAnonim || !nama) nama = 'Hamba Allah';
            const nominal = parseRupiahInput(document.getElementById('donasiNominal')?.value);
            const ket = document.getElementById('donasiKeterangan')?.value.trim() || '';

            if (nominal <= 0) {
                alert('Harap isi nominal donasi infaq dengan benar!');
                return;
            }

            const payload = {
                program_infaq_id: selectedProgramInfaqId,
                nama_donatur: nama,
                is_anonim: isAnonim,
                nominal: nominal,
                tanggal: tgl,
                keterangan: ket
            };

            try {
                if (SUPABASE_CONFIG.url) {
                    const res = await fetch(`${SUPABASE_CONFIG.url}/rest/v1/donasi_infaq`, {
                        method: 'POST',
                        headers: {
                            ...getSupabaseHeaders(),
                            'Prefer': 'return=representation'
                        },
                        body: JSON.stringify(payload)
                    });

                    if (res.ok) {
                        const newItems = await res.json().catch(() => ([]));
                        if (newItems && newItems.length > 0) {
                            cachedDonasiInfaq.push(newItems[0]);
                        } else {
                            payload.id = Date.now();
                            cachedDonasiInfaq.push(payload);
                        }
                    } else {
                        payload.id = Date.now();
                        cachedDonasiInfaq.push(payload);
                    }
                } else {
                    payload.id = Date.now();
                    cachedDonasiInfaq.push(payload);
                }
            } catch (err) {
                payload.id = Date.now();
                cachedDonasiInfaq.push(payload);
            }

            localStorage.setItem('cached_donasi_infaq', JSON.stringify(cachedDonasiInfaq));

            if (window.SupabaseDB && typeof SupabaseDB.broadcastChange === 'function') {
                SupabaseDB.broadcastChange('donasi_infaq', 'INSERT', payload, 'Petugas Infaq');
                SupabaseDB.broadcastChange('program_infaq', 'UPDATE', { program_infaq_id: selectedProgramInfaqId }, 'Petugas Infaq');
            }

            // Reset Form Donasi
            if (document.getElementById('donasiNama')) {
                document.getElementById('donasiNama').value = '';
                document.getElementById('donasiNama').removeAttribute('disabled');
            }
            if (document.getElementById('donasiIsAnonim')) document.getElementById('donasiIsAnonim').checked = false;
            if (document.getElementById('donasiNominal')) document.getElementById('donasiNominal').value = '';
            if (document.getElementById('donasiKeterangan')) document.getElementById('donasiKeterangan').value = '';

            renderProgramInfaqSection();
            alert(`Alhamdulillah! Donasi sebesar ${formatRupiah(nominal)} berhasil dicatat untuk "${currentProg ? currentProg.nama_program : 'Program Infaq'}".`);
        }

        // Hapus Catatan Donasi Infaq
        async function hapusDonasiInfaq(id) {
            if (!confirm('Apakah Anda yakin ingin menghapus catatan donasi ini?')) return;

            try {
                if (SUPABASE_CONFIG.url) {
                    await fetch(`${SUPABASE_CONFIG.url}/rest/v1/donasi_infaq?id=eq.${id}`, {
                        method: 'DELETE',
                        headers: getSupabaseHeaders()
                    });
                }
            } catch (e) {}

            cachedDonasiInfaq = cachedDonasiInfaq.filter(d => d.id != id);
            localStorage.setItem('cached_donasi_infaq', JSON.stringify(cachedDonasiInfaq));
            if (window.SupabaseDB && typeof SupabaseDB.broadcastChange === 'function') {
                SupabaseDB.broadcastChange('donasi_infaq', 'DELETE', { id }, 'Petugas Infaq');
                SupabaseDB.broadcastChange('program_infaq', 'UPDATE', {}, 'Petugas Infaq');
            }
            renderProgramInfaqSection();
            if (typeof filterKasTable === 'function') {
                filterKasTable();
            }
            alert('Catatan donasi berhasil dihapus.');
        }

        // Buka Modal Edit Donasi Infaq
        function bukaModalEditDonasiInfaq(id) {
            const donasi = (cachedDonasiInfaq || []).find(d => d.id == id);
            if (!donasi) {
                alert('Data donatur tidak ditemukan.');
                return;
            }

            const inputId = document.getElementById('editDonasiId');
            const inputProgId = document.getElementById('editDonasiProgramId');
            const inputTgl = document.getElementById('editDonasiTanggal');
            const chkAnon = document.getElementById('editDonasiIsAnonim');
            const inputNama = document.getElementById('editDonasiNama');
            const inputNominal = document.getElementById('editDonasiNominal');
            const inputKet = document.getElementById('editDonasiKeterangan');

            if (inputId) inputId.value = donasi.id;
            if (inputProgId) inputProgId.value = donasi.program_infaq_id || selectedProgramInfaqId || '';
            if (inputTgl) inputTgl.value = donasi.tanggal ? donasi.tanggal.split('T')[0] : new Date().toISOString().split('T')[0];

            const isAnon = donasi.is_anonim === true || donasi.nama_donatur === 'Hamba Allah';
            if (chkAnon) chkAnon.checked = isAnon;
            if (inputNama) {
                inputNama.value = donasi.nama_donatur || '';
                if (isAnon) {
                    inputNama.value = 'Hamba Allah';
                    inputNama.setAttribute('disabled', 'disabled');
                } else {
                    inputNama.removeAttribute('disabled');
                }
            }

            if (inputNominal) {
                inputNominal.value = Number(donasi.nominal || 0).toLocaleString('id-ID');
            }

            if (inputKet) {
                inputKet.value = donasi.keterangan || '';
            }

            $('#modalEditDonasiInfaq').modal('show');
        }

        // Toggle Checkbox Hamba Allah pada Modal Edit Donasi
        function toggleAnonimEditDonasi(checkbox) {
            const input = document.getElementById('editDonasiNama');
            if (!input) return;
            if (checkbox.checked) {
                input.value = 'Hamba Allah';
                input.setAttribute('disabled', 'disabled');
            } else {
                if (input.value === 'Hamba Allah') input.value = '';
                input.removeAttribute('disabled');
                input.focus();
            }
        }

        // Simpan Perubahan Data Donatur Infaq (PATCH ke Supabase & Local Cache)
        async function simpanEditDonasiInfaq() {
            const id = document.getElementById('editDonasiId')?.value;
            if (!id) {
                alert('ID data donasi tidak valid.');
                return;
            }

            const tgl = document.getElementById('editDonasiTanggal')?.value || new Date().toISOString().split('T')[0];
            const isAnonim = document.getElementById('editDonasiIsAnonim')?.checked === true;
            let nama = document.getElementById('editDonasiNama')?.value.trim();
            if (isAnonim || !nama) nama = 'Hamba Allah';
            const nominal = parseRupiahInput(document.getElementById('editDonasiNominal')?.value);
            const ket = document.getElementById('editDonasiKeterangan')?.value.trim() || '';

            if (nominal <= 0) {
                alert('Harap isi nominal donasi infaq dengan benar!');
                return;
            }

            const payload = {
                nama_donatur: nama,
                is_anonim: isAnonim,
                nominal: nominal,
                tanggal: tgl,
                keterangan: ket
            };

            try {
                if (SUPABASE_CONFIG.url) {
                    await fetch(`${SUPABASE_CONFIG.url}/rest/v1/donasi_infaq?id=eq.${id}`, {
                        method: 'PATCH',
                        headers: {
                            ...getSupabaseHeaders(),
                            'Prefer': 'return=representation'
                        },
                        body: JSON.stringify(payload)
                    });
                }
            } catch (err) {
                console.warn('Gagal PATCH ke Supabase donasi_infaq:', err);
            }

            // Update in cachedDonasiInfaq
            const idx = (cachedDonasiInfaq || []).findIndex(d => d.id == id);
            if (idx !== -1) {
                cachedDonasiInfaq[idx] = {
                    ...cachedDonasiInfaq[idx],
                    ...payload
                };
            }

            localStorage.setItem('cached_donasi_infaq', JSON.stringify(cachedDonasiInfaq));

            if (window.SupabaseDB && typeof SupabaseDB.broadcastChange === 'function') {
                SupabaseDB.broadcastChange('donasi_infaq', 'UPDATE', payload, 'Petugas Infaq');
                SupabaseDB.broadcastChange('program_infaq', 'UPDATE', {}, 'Petugas Infaq');
            }

            $('#modalEditDonasiInfaq').modal('hide');
            renderProgramInfaqSection();
            if (typeof filterKasTable === 'function') {
                filterKasTable();
            }
            alert(`Alhamdulillah, data donatur "${nama}" berhasil diperbarui.`);
        }

        // =====================================================================
        // PENGATURAN REKENING & SALURAN DONASI PROGRAM INFAQ
        // =====================================================================

        // Toggle buka/tutup panel rekening infaq
        function toggleRekeningInfaqPanel() {
            const panel = document.getElementById('panelRekeningInfaq');
            const lbl   = document.getElementById('lblRekeningInfaqToggle');
            if (!panel) return;
            const isHidden = (panel.style.display === 'none' || panel.style.display === '');
            panel.style.display = isHidden ? 'block' : 'none';
            if (lbl) lbl.textContent = isHidden ? '▲ Klik untuk tutup' : '▼ Klik untuk buka';
        }

        // Update live preview saat user mengetik
        function updateRekeningPreview() {
            const bank   = document.getElementById('cfgInfaqBankName')?.value || '';
            const noRek  = document.getElementById('cfgInfaqNoRekening')?.value || '';
            const nama   = document.getElementById('cfgInfaqAtasNama')?.value || '';
            const wa     = document.getElementById('cfgInfaqWaKonfirmasi')?.value || '';
            if (document.getElementById('prevInfaqBankName')) document.getElementById('prevInfaqBankName').textContent = bank || '-';
            if (document.getElementById('prevInfaqNoRek'))   document.getElementById('prevInfaqNoRek').textContent   = noRek || '-';
            if (document.getElementById('prevInfaqAtasNama')) document.getElementById('prevInfaqAtasNama').textContent = nama || '-';
            if (document.getElementById('prevInfaqWa'))       document.getElementById('prevInfaqWa').textContent       = wa || '-';
        }

        // Simpan rekening infaq ke Supabase (via app_settings.running_text_pages.infaq_rekening) & LocalStorage
        async function simpanRekeningInfaq() {
            const btn   = document.getElementById('btnSimpanRekeningInfaq');
            const alert = document.getElementById('alertRekeningInfaq');
            const bank  = document.getElementById('cfgInfaqBankName')?.value.trim() || '';
            const noRek = document.getElementById('cfgInfaqNoRekening')?.value.trim() || '';
            const nama  = document.getElementById('cfgInfaqAtasNama')?.value.trim() || '';
            const wa    = document.getElementById('cfgInfaqWaKonfirmasi')?.value.trim() || '';

            if (!bank || !noRek || !nama) {
                if (alert) { alert.textContent = '⚠ Nama Bank, Nomor Rekening, dan Atas Nama wajib diisi.'; alert.style.display='inline'; alert.style.color='#dc2626'; }
                return;
            }

            if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...'; }

            const rekData = { bank, noRek, nama, wa };
            const payloadCompat = {
                bank,
                noRek,
                nama,
                wa,
                infaq_bank_name: bank,
                infaq_no_rekening: noRek,
                infaq_atas_nama: nama,
                infaq_wa_konfirmasi: wa
            };

            // Simpan ke LocalStorage sebagai cache cepat
            localStorage.setItem('infaq_rekening', JSON.stringify(payloadCompat));
            updateRekeningPreview();

            try {
                if (typeof SUPABASE_CONFIG !== 'undefined' && SUPABASE_CONFIG.url) {
                    const headers = {
                        'apikey':        SUPABASE_CONFIG.anonKey,
                        'Authorization': 'Bearer ' + SUPABASE_CONFIG.anonKey,
                        'Content-Type':  'application/json',
                        'Prefer':        'return=representation'
                    };

                    // Ambil running_text_pages yang ada saat ini
                    const getRes = await fetch(`${SUPABASE_CONFIG.url}/rest/v1/app_settings?id=eq.1`, { headers });
                    let currentRt = {};
                    if (getRes.ok) {
                        const rows = await getRes.json();
                        let rt = rows[0]?.running_text_pages;
                        if (typeof rt === 'string') {
                            try { rt = JSON.parse(rt); } catch(e) { rt = {}; }
                        }
                        currentRt = rt || {};
                    }
                    currentRt.infaq_rekening = rekData;

                    const patchRes = await fetch(`${SUPABASE_CONFIG.url}/rest/v1/app_settings?id=eq.1`, {
                        method:  'PATCH',
                        headers: headers,
                        body:    JSON.stringify({ running_text_pages: currentRt })
                    });

                    if (!patchRes.ok) {
                        throw new Error('Supabase response status ' + patchRes.status);
                    }
                }
                if (window.SupabaseDB && typeof SupabaseDB.broadcastChange === 'function') {
                    SupabaseDB.broadcastChange('app_settings', 'UPDATE', { running_text_pages: { infaq_rekening: rekData } }, 'Petugas Infaq');
                    SupabaseDB.broadcastChange('rekening_infaq', 'UPDATE', payloadCompat, 'Petugas Infaq');
                }
                if (alert) { alert.textContent = '✅ Berhasil disimpan! Layar TV diperbarui otomatis.'; alert.style.display='inline'; alert.style.color='#16a34a'; }
            } catch (err) {
                console.warn('Supabase patch rekening infaq gagal:', err);
                if (alert) { alert.textContent = '⚠ Tersimpan lokal. Sinkronisasi cloud gagal.'; alert.style.display='inline'; alert.style.color='#d97706'; }
            } finally {
                if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fas fa-save mr-1"></i> Simpan & Tayang ke TV'; }
                setTimeout(() => { if (alert) alert.style.display = 'none'; }, 4000);
            }
        }

        // Reset rekening infaq ke nilai default BJB
        function resetRekeningInfaqDefault() {
            const defaults = {
                bank:  'Bank Jawa Barat (BJB)',
                noRek: '011 686 685 4100',
                nama:  'DKM Jami Al Jihad',
                wa:    '0856 1235 167 (BENDAHARA)'
            };
            if (document.getElementById('cfgInfaqBankName'))      document.getElementById('cfgInfaqBankName').value      = defaults.bank;
            if (document.getElementById('cfgInfaqNoRekening'))    document.getElementById('cfgInfaqNoRekening').value    = defaults.noRek;
            if (document.getElementById('cfgInfaqAtasNama'))      document.getElementById('cfgInfaqAtasNama').value      = defaults.nama;
            if (document.getElementById('cfgInfaqWaKonfirmasi')) document.getElementById('cfgInfaqWaKonfirmasi').value  = defaults.wa;
            updateRekeningPreview();
        }

        // Muat nilai rekening dari Supabase settings atau LocalStorage ke form
        function loadRekeningInfaqToForm(settings) {
            const cached = (() => { try { return JSON.parse(localStorage.getItem('infaq_rekening') || '{}'); } catch(e){ return {}; } })();
            const rt = settings?.running_text_pages;
            const rek = (rt && typeof rt === 'object' && rt.infaq_rekening) ? rt.infaq_rekening : {};

            const bank  = rek.bank  || settings?.infaq_bank_name   || cached.bank  || cached.infaq_bank_name   || 'Bank Jawa Barat (BJB)';
            const noRek = rek.noRek || settings?.infaq_no_rekening || cached.noRek || cached.infaq_no_rekening || '011 686 685 4100';
            const nama  = rek.nama  || settings?.infaq_atas_nama   || cached.nama  || cached.infaq_atas_nama   || 'DKM Jami Al Jihad';
            const wa    = rek.wa    || settings?.infaq_wa_konfirmasi || cached.wa  || cached.infaq_wa_konfirmasi || '0856 1235 167 (BENDAHARA)';

            if (document.getElementById('cfgInfaqBankName'))      document.getElementById('cfgInfaqBankName').value      = bank;
            if (document.getElementById('cfgInfaqNoRekening'))    document.getElementById('cfgInfaqNoRekening').value    = noRek;
            if (document.getElementById('cfgInfaqAtasNama'))      document.getElementById('cfgInfaqAtasNama').value      = nama;
            if (document.getElementById('cfgInfaqWaKonfirmasi')) document.getElementById('cfgInfaqWaKonfirmasi').value  = wa;
            updateRekeningPreview();

            // Pasang event listener live preview (sekali saja)
            ['cfgInfaqBankName','cfgInfaqNoRekening','cfgInfaqAtasNama','cfgInfaqWaKonfirmasi'].forEach(id => {
                const el = document.getElementById(id);
                if (el && !el.dataset.previewBound) {
                    el.addEventListener('input', updateRekeningPreview);
                    el.dataset.previewBound = '1';
                }
            });
        }

        // Buka Modal Tambah Program Baru
        function bukaModalTambahProgramInfaq() {
            document.getElementById('inputProgInfaqNama').value = '';
            document.getElementById('inputProgInfaqTarget').value = '';
            if (document.getElementById('inputProgInfaqTglMulai')) {
                document.getElementById('inputProgInfaqTglMulai').value = new Date().toISOString().split('T')[0];
            }
            if (document.getElementById('inputProgInfaqTglSelesai')) {
                document.getElementById('inputProgInfaqTglSelesai').value = '';
            }
            document.getElementById('inputProgInfaqKeterangan').value = '';
            document.getElementById('checkProgInfaqAktifTV').checked = true;
            $('#modalTambahProgramInfaq').modal('show');
        }

        // Simpan Program Infaq Baru (Dukungan Multi-Program Aktif Bersamaan)
        async function simpanProgramInfaqBaru() {
            const nama = document.getElementById('inputProgInfaqNama')?.value.trim();
            const target = parseRupiahInput(document.getElementById('inputProgInfaqTarget')?.value);
            const tglMulai = document.getElementById('inputProgInfaqTglMulai')?.value || null;
            const tglSelesai = document.getElementById('inputProgInfaqTglSelesai')?.value || null;
            const ket = document.getElementById('inputProgInfaqKeterangan')?.value.trim() || '';
            const isActive = document.getElementById('checkProgInfaqAktifTV')?.checked === true;

            if (!nama || target <= 0) {
                alert('Harap isi judul program dan target nominal dana dengan benar!');
                return;
            }

            try {
                const payload = {
                    nama_program: nama,
                    target_dana: target,
                    tanggal_mulai: tglMulai,
                    tanggal_selesai: tglSelesai,
                    keterangan: ket,
                    is_active: isActive
                };

                let savedId = null;
                if (SUPABASE_CONFIG.url) {
                    const res = await fetch(`${SUPABASE_CONFIG.url}/rest/v1/program_infaq`, {
                        method: 'POST',
                        headers: {
                            ...getSupabaseHeaders(),
                            'Prefer': 'return=representation'
                        },
                        body: JSON.stringify(payload)
                    });

                    if (res.ok) {
                        const resData = await res.json().catch(() => ([]));
                        if (resData && resData.length > 0) {
                            savedId = resData[0].id;
                            cachedProgramInfaq.unshift(resData[0]);
                        }
                    }
                }

                if (!savedId) {
                    savedId = Date.now();
                    payload.id = savedId;
                    cachedProgramInfaq.unshift(payload);
                }

                selectedProgramInfaqId = savedId;
                localStorage.setItem('cached_program_infaq', JSON.stringify(cachedProgramInfaq));
                if (window.SupabaseDB && typeof SupabaseDB.broadcastChange === 'function') {
                    SupabaseDB.broadcastChange('program_infaq', 'INSERT', payload, 'Petugas Infaq');
                }
                $('#modalTambahProgramInfaq').modal('hide');
                renderProgramInfaqSection();
                alert(`Alhamdulillah! Program penggalangan infaq "${nama}" berhasil dibuat ${isActive ? 'dan langsung aktif di Layar TV' : ''}.`);
            } catch (e) {
                const newId = Date.now();
                const localItem = {
                    id: newId,
                    nama_program: nama,
                    target_dana: target,
                    tanggal_mulai: tglMulai,
                    tanggal_selesai: tglSelesai,
                    keterangan: ket,
                    is_active: isActive
                };
                cachedProgramInfaq.unshift(localItem);
                selectedProgramInfaqId = newId;
                localStorage.setItem('cached_program_infaq', JSON.stringify(cachedProgramInfaq));
                if (window.SupabaseDB && typeof SupabaseDB.broadcastChange === 'function') {
                    SupabaseDB.broadcastChange('program_infaq', 'INSERT', localItem, 'Petugas Infaq');
                }
                renderProgramInfaqSection();
                $('#modalTambahProgramInfaq').modal('hide');
                alert(`Program penggalangan infaq "${nama}" berhasil disimpan.`);
            }
        }

        // Buka Modal Edit Program Infaq
        function bukaModalEditProgramInfaq(id) {
            const prog = cachedProgramInfaq.find(p => p.id == id);
            if (!prog) return;

            document.getElementById('editProgInfaqId').value = prog.id;
            document.getElementById('editProgInfaqNama').value = prog.nama_program;
            document.getElementById('editProgInfaqTarget').value = parseInt(prog.target_dana || 0, 10).toLocaleString('id-ID');
            if (document.getElementById('editProgInfaqTglMulai')) {
                document.getElementById('editProgInfaqTglMulai').value = prog.tanggal_mulai ? prog.tanggal_mulai.split('T')[0] : '';
            }
            if (document.getElementById('editProgInfaqTglSelesai')) {
                document.getElementById('editProgInfaqTglSelesai').value = prog.tanggal_selesai ? prog.tanggal_selesai.split('T')[0] : '';
            }
            document.getElementById('editProgInfaqKeterangan').value = prog.keterangan || '';
            document.getElementById('editCheckProgInfaqAktifTV').checked = prog.is_active === true;

            $('#modalEditProgramInfaq').modal('show');
        }

        // Simpan Koreksi / Edit Program Infaq (Multi-Program)
        async function simpanEditProgramInfaq() {
            const id = document.getElementById('editProgInfaqId')?.value;
            const nama = document.getElementById('editProgInfaqNama')?.value.trim();
            const target = parseRupiahInput(document.getElementById('editProgInfaqTarget')?.value);
            const tglMulai = document.getElementById('editProgInfaqTglMulai')?.value || null;
            const tglSelesai = document.getElementById('editProgInfaqTglSelesai')?.value || null;
            const ket = document.getElementById('editProgInfaqKeterangan')?.value.trim() || '';
            const isActive = document.getElementById('editCheckProgInfaqAktifTV')?.checked === true;

            if (!id || !nama || target <= 0) {
                alert('Harap isi judul dan target dana program!');
                return;
            }

            try {
                if (SUPABASE_CONFIG.url) {
                    await fetch(`${SUPABASE_CONFIG.url}/rest/v1/program_infaq?id=eq.${id}`, {
                        method: 'PATCH',
                        headers: {
                            ...getSupabaseHeaders(),
                            'Prefer': 'return=representation'
                        },
                        body: JSON.stringify({
                            nama_program: nama,
                            target_dana: target,
                            tanggal_mulai: tglMulai,
                            tanggal_selesai: tglSelesai,
                            keterangan: ket,
                            is_active: isActive
                        })
                    });
                }
            } catch (e) {}

            const prog = cachedProgramInfaq.find(p => p.id == id);
            if (prog) {
                prog.nama_program = nama;
                prog.target_dana = target;
                prog.tanggal_mulai = tglMulai;
                prog.tanggal_selesai = tglSelesai;
                prog.keterangan = ket;
                prog.is_active = isActive;
                localStorage.setItem('cached_program_infaq', JSON.stringify(cachedProgramInfaq));
            }

            selectedProgramInfaqId = id;
            if (window.SupabaseDB && typeof SupabaseDB.broadcastChange === 'function') {
                SupabaseDB.broadcastChange('program_infaq', 'UPDATE', { id, nama_program: nama, is_active: isActive }, 'Petugas Infaq');
            }
            renderProgramInfaqSection();
            $('#modalEditProgramInfaq').modal('hide');
            alert('Alhamdulillah! Perubahan program penggalangan infaq berhasil disimpan.');
        }

        // Toggle Status Tayang TV (Bisa Menayangkan 1, 2, atau 3 Program Sekaligus atau Menutupnya)
        async function toggleStatusProgramInfaqTV(id) {
            const prog = cachedProgramInfaq.find(p => p.id == id);
            if (!prog) return;

            const newStatus = !(prog.is_active === true);

            try {
                if (SUPABASE_CONFIG.url) {
                    await fetch(`${SUPABASE_CONFIG.url}/rest/v1/program_infaq?id=eq.${id}`, {
                        method: 'PATCH',
                        headers: getSupabaseHeaders(),
                        body: JSON.stringify({ is_active: newStatus })
                    });
                }
            } catch (e) {
                console.warn('Gagal sinkron status program infaq ke Supabase:', e);
            }

            prog.is_active = newStatus;
            selectedProgramInfaqId = id;
            localStorage.setItem('cached_program_infaq', JSON.stringify(cachedProgramInfaq));
            if (window.SupabaseDB && typeof SupabaseDB.broadcastChange === 'function') {
                SupabaseDB.broadcastChange('program_infaq', 'UPDATE', { id, is_active: newStatus }, 'Petugas Infaq');
            }
            renderProgramInfaqSection();

            if (newStatus) {
                alert(`Alhamdulillah! Program "${prog.nama_program}" sekarang TAYANG di Layar TV.`);
            } else {
                alert(`Program "${prog.nama_program}" telah DITUTUP / TIDAK TAYANG di Layar TV.`);
            }
        }

        // Hapus Program Infaq
        async function hapusProgramInfaq(id) {
            if (cachedProgramInfaq.length <= 1) {
                alert('Minimal harus ada 1 program penggalangan infaq.');
                return;
            }
            if (!confirm('Yakin ingin menghapus program ini beserta seluruh riwayat donasinya?')) return;

            try {
                if (SUPABASE_CONFIG.url) {
                    await fetch(`${SUPABASE_CONFIG.url}/rest/v1/donasi_infaq?program_infaq_id=eq.${id}`, {
                        method: 'DELETE',
                        headers: getSupabaseHeaders()
                    });
                    await fetch(`${SUPABASE_CONFIG.url}/rest/v1/program_infaq?id=eq.${id}`, {
                        method: 'DELETE',
                        headers: getSupabaseHeaders()
                    });
                }
            } catch (e) {}

            cachedProgramInfaq = cachedProgramInfaq.filter(p => p.id != id);
            cachedDonasiInfaq = (cachedDonasiInfaq || []).filter(d => d.program_infaq_id != id);
            if (!cachedProgramInfaq.some(p => p.is_active)) {
                cachedProgramInfaq[0].is_active = true;
            }
            selectedProgramInfaqId = cachedProgramInfaq[0].id;
            localStorage.setItem('cached_program_infaq', JSON.stringify(cachedProgramInfaq));
            localStorage.setItem('cached_donasi_infaq', JSON.stringify(cachedDonasiInfaq));
            if (window.SupabaseDB && typeof SupabaseDB.broadcastChange === 'function') {
                SupabaseDB.broadcastChange('program_infaq', 'DELETE', { id }, 'Petugas Infaq');
                SupabaseDB.broadcastChange('donasi_infaq', 'DELETE', { program_infaq_id: id }, 'Petugas Infaq');
            }
            renderProgramInfaqSection();
            alert('Program penggalangan infaq berhasil dihapus.');
        }

        // =======================================================
        // FITUR EXPORT LAPORAN DONASI INFAQ RESMI (EXCEL & PDF)
        // =======================================================
        function exportLaporanInfaqCSV(progId) {
            const targetId = progId || selectedProgramInfaqId;
            const prog = (cachedProgramInfaq || []).find(p => p.id == targetId);
            if (!prog) {
                alert('Pilih program infaq yang ingin di-export terlebih dahulu.');
                return;
            }

            const donasiList = (cachedDonasiInfaq || [])
                .filter(d => d.program_infaq_id == prog.id)
                .sort((a, b) => {
                    const dateA = new Date(a.tanggal || 0).getTime();
                    const dateB = new Date(b.tanggal || 0).getTime();
                    if (dateA !== dateB) return dateA - dateB;
                    return (parseInt(a.id) || 0) - (parseInt(b.id) || 0);
                });
            const targetDana = parseFloat(prog.target_dana) || 0;
            const totalTerkumpul = donasiList.reduce((acc, d) => acc + (parseFloat(d.nominal) || 0), 0);
            const sisaDana = Math.max(0, targetDana - totalTerkumpul);
            const persentase = targetDana > 0 ? ((totalTerkumpul / targetDana) * 100).toFixed(1) : 0;
            const now = new Date();
            const tglCetak = `${now.getDate()}/${now.getMonth() + 1}/${now.getFullYear()}`;

            let csv = '\uFEFF'; // UTF-8 BOM agar rapi di Microsoft Excel
            csv += `LAPORAN PENERIMAAN INFAQ & DONASI - MASJID JAMI' AL-JIHAD\r\n`;
            csv += `Nama Program,"${prog.nama_program.replace(/"/g, '""')}"\r\n`;
            csv += `Target Penggalangan Dana,"Rp ${targetDana.toLocaleString('id-ID')}"\r\n`;
            csv += `Total Dana Terkumpul,"Rp ${totalTerkumpul.toLocaleString('id-ID')}"\r\n`;
            csv += `Sisa Kekurangan Dana,"Rp ${sisaDana.toLocaleString('id-ID')}"\r\n`;
            csv += `Persentase Pencapaian,"${persentase}%"\r\n`;
            csv += `Jumlah Donatur / Muhsinin,"${donasiList.length} Orang/Hamba Allah"\r\n`;
            csv += `Status Tayang di TV,"${prog.is_active ? 'Aktif Tayang di TV' : 'Ditutup / Nonaktif'}"\r\n`;
            csv += `Tanggal Export,"${tglCetak}"\r\n\r\n`;

            csv += `No,Tanggal,Nama Donatur,Nominal (Rp),Keterangan / Niat Infaq\r\n`;
            donasiList.forEach((d, idx) => {
                const tgl = d.tanggal || '-';
                const nama = (d.is_anonim ? 'Hamba Allah' : (d.nama_donatur || 'Hamba Allah')).replace(/"/g, '""');
                const nominal = parseFloat(d.nominal) || 0;
                const ket = (d.keterangan || '-').replace(/"/g, '""');
                csv += `${idx + 1},"${tgl}","${nama}",${nominal},"${ket}"\r\n`;
            });

            csv += `\r\n,,,TOTAL PENERIMAAN TERKUMPUL,${totalTerkumpul}\r\n`;

            const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            const safeName = (prog.nama_program || 'Program_Infaq').replace(/[^a-zA-Z0-9_-]/g, '_');
            a.href = url;
            a.download = `Laporan_Infaq_${safeName}_${now.toISOString().split('T')[0]}.csv`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        }

        function cetakLaporanInfaqPDF(progId) {
            const targetId = progId || selectedProgramInfaqId;
            const prog = (cachedProgramInfaq || []).find(p => p.id == targetId);
            if (!prog) {
                alert('Pilih program infaq yang ingin dicetak terlebih dahulu.');
                return;
            }

            const donasiList = (cachedDonasiInfaq || [])
                .filter(d => d.program_infaq_id == prog.id)
                .sort((a, b) => {
                    const dateA = new Date(a.tanggal || 0).getTime();
                    const dateB = new Date(b.tanggal || 0).getTime();
                    if (dateA !== dateB) return dateA - dateB;
                    return (parseInt(a.id) || 0) - (parseInt(b.id) || 0);
                });
            const targetDana = parseFloat(prog.target_dana) || 0;
            const totalTerkumpul = donasiList.reduce((acc, d) => acc + (parseFloat(d.nominal) || 0), 0);
            const sisaDana = Math.max(0, targetDana - totalTerkumpul);
            const persentase = targetDana > 0 ? ((totalTerkumpul / targetDana) * 100).toFixed(1) : 0;
            const now = new Date();
            const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            const tglCetak = `${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()}`;

            const printWin = window.open('', '_blank');
            if (!printWin) {
                alert('Pop-up cetak terblokir browser. Harap izinkan pop-up untuk mencetak dokumen.');
                return;
            }

            let rowsHtml = '';
            if (donasiList.length === 0) {
                rowsHtml = `<tr><td colspan="5" style="text-align:center; padding: 15px; color:#666;">Belum ada catatan penerimaan donasi untuk program ini.</td></tr>`;
            } else {
                rowsHtml = donasiList.map((d, idx) => {
                    const nama = d.is_anonim ? 'Hamba Allah' : (d.nama_donatur || 'Hamba Allah');
                    const nominal = (parseFloat(d.nominal) || 0).toLocaleString('id-ID');
                    return `
                        <tr>
                            <td style="text-align:center; padding: 6px 8px; border: 1px solid #333;">${idx + 1}</td>
                            <td style="text-align:center; padding: 6px 8px; border: 1px solid #333;">${d.tanggal || '-'}</td>
                            <td style="padding: 6px 8px; border: 1px solid #333;">${escapeHtml(nama)}</td>
                            <td style="text-align:right; padding: 6px 8px; border: 1px solid #333; font-weight: bold;">Rp ${nominal}</td>
                            <td style="padding: 6px 8px; border: 1px solid #333;">${escapeHtml(d.keterangan || '-')}</td>
                        </tr>
                    `;
                }).join('');
            }

            printWin.document.write(`
                <!DOCTYPE html>
                <html lang="id">
                <head>
                    <meta charset="UTF-8">
                    <title>Laporan Infaq - ${escapeHtml(prog.nama_program)}</title>
                    <style>
                        @page { size: A4 portrait; margin: 15mm 20mm; }
                        body { font-family: 'Times New Roman', serif; color: #111; line-height: 1.35; font-size: 11pt; margin: 0; }
                        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 8px; margin-bottom: 14px; }
                        .kop-masjid { font-size: 15pt; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; margin: 0; }
                        .kop-sub { font-size: 10pt; margin: 3px 0; color: #333; }
                        .doc-title { text-align: center; font-size: 12pt; font-weight: bold; text-transform: uppercase; text-decoration: underline; margin: 12px 0 4px 0; }
                        .doc-prog { text-align: center; font-size: 11pt; font-weight: bold; color: #1e5a3a; margin-bottom: 14px; }
                        .meta-table { width: 100%; border-collapse: collapse; margin-bottom: 14px; font-size: 10.5pt; }
                        .meta-table td { padding: 4px 6px; vertical-align: top; }
                        .table-donasi { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 10pt; }
                        .table-donasi th { background-color: #f2f2f2; border: 1px solid #333; padding: 7px 8px; text-align: center; }
                        .total-row td { background-color: #f9f9f9; font-weight: bold; border: 1px solid #333; padding: 7px 8px; }
                        .ttd-box { width: 100%; display: flex; justify-content: space-between; margin-top: 35px; page-break-inside: avoid; }
                        .ttd-col { width: 45%; text-align: center; }
                        .ttd-space { height: 65px; }
                        @media print {
                            .no-print { display: none !important; }
                        }
                    </style>
                </head>
                <body>
                    <div class="no-print" style="background: #f8fafc; padding: 10px; border-bottom: 1px solid #cbd5e1; text-align: right; margin-bottom: 15px;">
                        <button onclick="window.print()" style="background:#1e5a3a; color:#fff; border:none; padding:8px 18px; border-radius:5px; font-weight:bold; cursor:pointer;">
                            🖨️ Cetak / Simpan PDF
                        </button>
                    </div>

                    <div class="header">
                        <h1 class="kop-masjid">DEWAN KEMAKMURAN MASJID (DKM) JAMI' AL-JIHAD</h1>
                        <p class="kop-sub">Sekretariat: Jl. Abdullah Syafei No. 5 | WhatsApp / Konfirmasi: 0877 5876 7000</p>
                        <p class="kop-sub" style="font-size: 9pt;">Email: archived.aljihad@gmail.com | Situs Resmi: https://digitalaljihad.my.id</p>
                    </div>

                    <div class="doc-title">LAPORAN PENERIMAAN INFAQ & DONASI KHUSUS</div>
                    <div class="doc-prog">Program: ${escapeHtml(prog.nama_program)}</div>

                    <table class="meta-table">
                        <tr>
                            <td style="width: 25%; font-weight: bold;">Target Penggalangan</td>
                            <td style="width: 2%;">:</td>
                            <td style="width: 28%; font-weight: bold;">Rp ${targetDana.toLocaleString('id-ID')}</td>
                            <td style="width: 22%; font-weight: bold;">Jumlah Donatur</td>
                            <td style="width: 2%;">:</td>
                            <td style="width: 21%;">${donasiList.length} Orang / Muhsinin</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold; color: #047857;">Total Dana Terkumpul</td>
                            <td>:</td>
                            <td style="font-weight: bold; color: #047857;">Rp ${totalTerkumpul.toLocaleString('id-ID')} (${persentase}%)</td>
                            <td style="font-weight: bold;">Status Tayang TV</td>
                            <td>:</td>
                            <td>${prog.is_active ? 'Aktif Tayang di TV' : 'Ditutup / Tidak Tayang'}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold; color: ${sisaDana > 0 ? '#b91c1c' : '#047857'};">Sisa Kekurangan</td>
                            <td>:</td>
                            <td style="font-weight: bold; color: ${sisaDana > 0 ? '#b91c1c' : '#047857'};">${sisaDana > 0 ? 'Rp ' + sisaDana.toLocaleString('id-ID') : 'Rp 0 (Target Terpenuhi)'}</td>
                            <td style="font-weight: bold;">Tanggal Laporan</td>
                            <td>:</td>
                            <td>${tglCetak}</td>
                        </tr>
                    </table>

                    <table class="table-donasi">
                        <thead>
                            <tr>
                                <th style="width: 6%;">No</th>
                                <th style="width: 16%;">Tanggal</th>
                                <th style="width: 32%;">Nama Donatur / Muhsinin</th>
                                <th style="width: 22%;">Nominal Infaq</th>
                                <th style="width: 24%;">Keterangan / Niat</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${rowsHtml}
                            <tr class="total-row">
                                <td colspan="3" style="text-align: right; text-transform: uppercase;">Total Penerimaan Infaq :</td>
                                <td style="text-align: right; color: #047857;">Rp ${totalTerkumpul.toLocaleString('id-ID')}</td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="ttd-box">
                        <div class="ttd-col">
                            <p style="margin: 0;">Mengetahui,</p>
                            <p style="margin: 0; font-weight: bold;">Ketua DKM Masjid Jami' Al-Jihad</p>
                            <div class="ttd-space"></div>
                            <p style="margin: 0; font-weight: bold; text-decoration: underline;">( .................................................. )</p>
                        </div>
                        <div class="ttd-col">
                            <p style="margin: 0;">Dibuat Oleh,</p>
                            <p style="margin: 0; font-weight: bold;">Bendahara Masjid Jami' Al-Jihad</p>
                            <div class="ttd-space"></div>
                            <p style="margin: 0; font-weight: bold; text-decoration: underline;">( Bpk. H. Utut Priastya )</p>
                        </div>
                    </div>
                </body>
                </html>
            `);
            printWin.document.close();
            printWin.focus();
        }

        // =====================================================