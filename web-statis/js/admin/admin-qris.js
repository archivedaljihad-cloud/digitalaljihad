/**
 * MODUL ADMIN: admin-qris.js
 * Digital Signage Masjid Jami' Al-Jihad
 * Versi: aljihad-signage-v5.8.46
 */

        // MODUL MANAJEMEN QRIS DONASI DIGITAL
        // =====================================================
        function renderQrisTable() {
            const tbody = document.getElementById('qrisTableBody');
            if (!tbody) return;

            const searchVal = (document.getElementById('qrisSearchInput')?.value || '').toLowerCase().trim();
            const entriesPerPage = parseInt(document.getElementById('qrisEntriesPerPage')?.value || 10);

            let filtered = (cachedQrisList || []).filter(item => {
                if (!searchVal) return true;
                const nama = (item.nama || '').toLowerCase();
                const bank = (item.bank || '').toLowerCase();
                const norek = (item.nomor_rekening || '').toLowerCase();
                const atasNama = (item.atas_nama || '').toLowerCase();
                const ket = (item.keterangan || '').toLowerCase();
                return nama.includes(searchVal) || bank.includes(searchVal) || norek.includes(searchVal) || atasNama.includes(searchVal) || ket.includes(searchVal);
            });

            const total = filtered.length;
            const displayed = filtered.slice(0, entriesPerPage);

            if (document.getElementById('badgeTotalQris')) {
                document.getElementById('badgeTotalQris').textContent = `${total} QRIS`;
            }

            if (displayed.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="fas fa-qrcode fa-2x mb-2 text-gray-300 d-block"></i>
                            Tidak ada data QRIS yang cocok dengan pencarian.
                        </td>
                    </tr>
                `;
                if (document.getElementById('qrisPaginationInfo')) {
                    document.getElementById('qrisPaginationInfo').textContent = 'Menampilkan 0 entri';
                }
                return;
            }

            tbody.innerHTML = displayed.map((item, idx) => {
                const isActive = item.status === 'aktif';
                const statusBadge = isActive
                    ? '<span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle mr-1"></i>Aktif</span>'
                    : '<span class="badge badge-secondary px-2 py-1">Nonaktif</span>';

                const activateBtn = !isActive
                    ? `<button type="button" class="btn btn-success btn-sm" onclick="setAktifQris(${item.id})" title="Aktifkan di Layar TV"><i class="fas fa-check-circle"></i></button>`
                    : '';

                const imgSrc = item.gambar || '../image/qris.png';

                return `
                    <tr>
                        <td class="text-center font-weight-bold text-gray-700">${idx + 1}</td>
                        <td class="font-weight-bold text-gray-900">${escapeHtml(item.nama || 'QRIS Masjid')}</td>
                        <td class="text-center">
                            <img src="${imgSrc}" alt="${escapeHtml(item.nama)}" 
                                 onclick="bukaModalDetailQris(${item.id})"
                                 style="width: 50px; height: 50px; object-fit: contain; border: 1px solid #e3e6f0; border-radius: 6px; padding: 2px; cursor: pointer; background: #fff;"
                                 title="Klik untuk melihat rincian penuh">
                        </td>
                        <td class="text-center">${statusBadge}</td>
                        <td class="font-weight-bold text-gray-800">${escapeHtml(item.bank || '-')}</td>
                        <td class="text-gray-700">${escapeHtml(item.atas_nama || '-')}</td>
                        <td class="text-center">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-info btn-sm" onclick="bukaModalDetailQris(${item.id})" title="Lihat Rincian">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button type="button" class="btn btn-warning btn-sm" onclick="bukaModalEditQris(${item.id})" title="Edit QRIS">
                                    <i class="fas fa-edit"></i>
                                </button>
                                ${activateBtn}
                                <button type="button" class="btn btn-danger btn-sm" onclick="hapusQris(${item.id})" title="Hapus QRIS">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            }).join('');

            if (document.getElementById('qrisPaginationInfo')) {
                document.getElementById('qrisPaginationInfo').textContent = `Menampilkan 1 sampai ${displayed.length} dari ${total} entri`;
            }
        }

        function bukaModalTambahQris() {
            document.getElementById('qrisEditId').value = '';
            document.getElementById('inputQrisNama').value = 'QRIS Masjid Al-Jihad';
            document.getElementById('previewQrisImg').src = '../image/qris.png';
            document.getElementById('inputQrisFile').value = '';
            document.getElementById('inputQrisGambarUrl').value = '';
            document.getElementById('inputQrisKeterangan').value = 'Bagi yang ingin berinfaq dan sedekah secara digital, silahkan scan QRIS ini';
            document.getElementById('inputQrisBank').value = 'Bank Jawa Barat (BJB)';
            document.getElementById('inputQrisRekening').value = '011 686 685 4100';
            document.getElementById('inputQrisAtasNama').value = 'DKM Jami Al Jihad';
            document.getElementById('inputQrisStatus').value = 'aktif';
            uploadedQrisBase64 = null;

            document.getElementById('modalQrisLabel').innerHTML = '<i class="fas fa-plus-circle mr-2"></i> Tambah QRIS Baru';
            $('#modalQris').modal('show');
        }

        function bukaModalEditQris(id) {
            const item = (cachedQrisList || []).find(q => q.id == id);
            if (!item) return;

            document.getElementById('qrisEditId').value = item.id;
            document.getElementById('inputQrisNama').value = item.nama || '';
            document.getElementById('previewQrisImg').src = item.gambar || '../image/qris.png';
            document.getElementById('inputQrisFile').value = '';
            document.getElementById('inputQrisGambarUrl').value = (item.gambar && item.gambar.startsWith('http')) ? item.gambar : '';
            document.getElementById('inputQrisKeterangan').value = item.keterangan || '';
            document.getElementById('inputQrisBank').value = item.bank || '';
            document.getElementById('inputQrisRekening').value = item.nomor_rekening || '';
            document.getElementById('inputQrisAtasNama').value = item.atas_nama || '';
            document.getElementById('inputQrisStatus').value = item.status || 'aktif';
            uploadedQrisBase64 = null;

            document.getElementById('modalQrisLabel').innerHTML = '<i class="fas fa-edit mr-2"></i> Form Edit QRIS';
            $('#modalQris').modal('show');
        }

        function bukaModalDetailQris(id) {
            const item = (cachedQrisList || []).find(q => q.id == id);
            if (!item) return;

            document.getElementById('detailQrisImg').src = item.gambar || '../image/qris.png';
            document.getElementById('detailQrisNama').textContent = item.nama || 'QRIS Masjid';
            const isActive = item.status === 'aktif';
            document.getElementById('detailQrisStatusBadge').innerHTML = isActive
                ? '<span class="badge badge-success px-3 py-1 font-weight-bold"><i class="fas fa-check-circle mr-1"></i> Aktif di TV Display</span>'
                : '<span class="badge badge-secondary px-3 py-1 font-weight-bold">Nonaktif (Arsip)</span>';
            document.getElementById('detailQrisBank').textContent = item.bank || '-';
            document.getElementById('detailQrisRekening').textContent = item.nomor_rekening || '-';
            document.getElementById('detailQrisAtasNama').textContent = item.atas_nama || '-';
            document.getElementById('detailQrisKeterangan').textContent = item.keterangan || '-';

            $('#modalDetailQris').modal('show');
        }

        function previewUploadQris(event) {
            const file = event.target.files[0];
            if (!file) return;

            if (file.size > 2 * 1024 * 1024) {
                alert('Ukuran file maksimal adalah 2MB.');
                event.target.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                uploadedQrisBase64 = e.target.result;
                document.getElementById('previewQrisImg').src = uploadedQrisBase64;
            };
            reader.readAsDataURL(file);
        }

        function previewUrlQris(url) {
            const cleanUrl = url.trim();
            if (cleanUrl) {
                document.getElementById('previewQrisImg').src = cleanUrl;
            } else if (!uploadedQrisBase64) {
                document.getElementById('previewQrisImg').src = '../image/qris.png';
            }
        }

        async function simpanDataQris() {
            const editId = document.getElementById('qrisEditId').value;
            const nama = document.getElementById('inputQrisNama').value.trim();
            const keterangan = document.getElementById('inputQrisKeterangan').value.trim();
            const bank = document.getElementById('inputQrisBank').value.trim();
            const norek = document.getElementById('inputQrisRekening').value.trim();
            const atasNama = document.getElementById('inputQrisAtasNama').value.trim();
            const status = document.getElementById('inputQrisStatus').value;
            const urlInput = document.getElementById('inputQrisGambarUrl').value.trim();

            if (!nama) {
                alert('Mohon isi nama QRIS.');
                return;
            }

            let finalImage = '../image/qris.png';
            if (uploadedQrisBase64) {
                finalImage = uploadedQrisBase64;
            } else if (urlInput) {
                finalImage = urlInput;
            } else if (editId) {
                const existing = (cachedQrisList || []).find(q => q.id == editId);
                if (existing && existing.gambar) finalImage = existing.gambar;
            }

            if (status === 'aktif') {
                (cachedQrisList || []).forEach(q => {
                    q.status = 'nonaktif';
                });
            }

            if (editId) {
                const idx = cachedQrisList.findIndex(q => q.id == editId);
                if (idx !== -1) {
                    cachedQrisList[idx] = {
                        ...cachedQrisList[idx],
                        nama: nama,
                        gambar: finalImage,
                        keterangan: keterangan,
                        bank: bank,
                        nomor_rekening: norek,
                        atas_nama: atasNama,
                        status: status,
                        updated_at: new Date().toISOString()
                    };
                }
            } else {
                const newId = Date.now();
                const newQris = {
                    id: newId,
                    nama: nama,
                    gambar: finalImage,
                    keterangan: keterangan,
                    bank: bank,
                    nomor_rekening: norek,
                    atas_nama: atasNama,
                    status: status,
                    created_at: new Date().toISOString(),
                    updated_at: new Date().toISOString()
                };
                cachedQrisList.unshift(newQris);
            }

            localStorage.setItem('cached_qris_list', JSON.stringify(cachedQrisList));

            // Sync ke Supabase (jika terhubung)
            try {
                const activeQris = (cachedQrisList || []).find(q => q.status === 'aktif') || (cachedQrisList || [])[0];
                if (window.SupabaseDB && typeof SupabaseDB.saveSettingItem === 'function') {
                    await SupabaseDB.saveSettingItem('cached_qris_list', cachedQrisList);
                }
                if (SUPABASE_CONFIG.url) {
                    if (activeQris && status === 'aktif') {
                        // Sinkronkan ke app_settings agar slide TV otomatis mengadopsi
                        await fetch(`${SUPABASE_CONFIG.url}/rest/v1/app_settings?id=eq.1`, {
                            method: 'PATCH',
                            headers: getSupabaseHeaders(),
                            body: JSON.stringify({
                                qris_image: activeQris.gambar.startsWith('data:') ? 'image/qris.png' : activeQris.gambar
                            })
                        });
                    }
                }
                if (window.SupabaseDB && typeof SupabaseDB.broadcastChange === 'function') {
                    SupabaseDB.broadcastChange('qris', 'UPDATE', { list: cachedQrisList }, 'Admin QRIS');
                    if (activeQris && status === 'aktif') {
                        SupabaseDB.broadcastChange('app_settings', 'UPDATE', { qris_image: activeQris.gambar }, 'Admin QRIS');
                    }
                }
            } catch (err) {
                console.warn('Gagal sync ke cloud Supabase:', err);
            }

            $('#modalQris').modal('hide');
            renderQrisTable();
            alert(`Alhamdulillah! Data "${nama}" berhasil disimpan ${status === 'aktif' ? 'dan aktif di Layar TV Display' : ''}.`);
        }

        async function setAktifQris(id) {
            const item = (cachedQrisList || []).find(q => q.id == id);
            if (!item) return;

            if (!confirm(`Aktifkan "${item.nama}" untuk ditayangkan di Layar TV Display?`)) return;

            (cachedQrisList || []).forEach(q => {
                q.status = (q.id == id) ? 'aktif' : 'nonaktif';
            });

            localStorage.setItem('cached_qris_list', JSON.stringify(cachedQrisList));

            try {
                if (window.SupabaseDB && typeof SupabaseDB.saveSettingItem === 'function') {
                    await SupabaseDB.saveSettingItem('cached_qris_list', cachedQrisList);
                }
                if (SUPABASE_CONFIG.url) {
                    await fetch(`${SUPABASE_CONFIG.url}/rest/v1/app_settings?id=eq.1`, {
                        method: 'PATCH',
                        headers: getSupabaseHeaders(),
                        body: JSON.stringify({
                            qris_image: item.gambar.startsWith('data:') ? 'image/qris.png' : item.gambar
                        })
                    });
                }
                if (window.SupabaseDB && typeof SupabaseDB.broadcastChange === 'function') {
                    SupabaseDB.broadcastChange('qris', 'UPDATE', { list: cachedQrisList }, 'Admin QRIS');
                    SupabaseDB.broadcastChange('app_settings', 'UPDATE', { qris_image: item.gambar }, 'Admin QRIS');
                }
            } catch (err) {}

            renderQrisTable();
            alert(`"${item.nama}" sekarang aktif ditayangkan di Layar TV Display.`);
        }

        async function hapusQris(id) {
            const item = (cachedQrisList || []).find(q => q.id == id);
            if (!item) return;

            if (cachedQrisList.length <= 1) {
                alert('Tidak dapat menghapus. Minimal harus ada 1 data QRIS donasi tersimpan.');
                return;
            }

            if (!confirm(`Yakin ingin menghapus "${item.nama}"?`)) return;

            const wasActive = item.status === 'aktif';
            cachedQrisList = cachedQrisList.filter(q => q.id != id);

            if (wasActive && cachedQrisList.length > 0) {
                cachedQrisList[0].status = 'aktif';
            }

            localStorage.setItem('cached_qris_list', JSON.stringify(cachedQrisList));

            try {
                if (window.SupabaseDB && typeof SupabaseDB.saveSettingItem === 'function') {
                    await SupabaseDB.saveSettingItem('cached_qris_list', cachedQrisList);
                }
                if (wasActive && cachedQrisList.length > 0 && SUPABASE_CONFIG.url) {
                    const newActive = cachedQrisList[0];
                    await fetch(`${SUPABASE_CONFIG.url}/rest/v1/app_settings?id=eq.1`, {
                        method: 'PATCH',
                        headers: getSupabaseHeaders(),
                        body: JSON.stringify({
                            qris_image: newActive.gambar.startsWith('data:') ? 'image/qris.png' : newActive.gambar
                        })
                    }).catch(() => {});
                }
                if (window.SupabaseDB && typeof SupabaseDB.broadcastChange === 'function') {
                    SupabaseDB.broadcastChange('qris', 'DELETE', { list: cachedQrisList }, 'Admin QRIS');
                    if (wasActive && cachedQrisList.length > 0) {
                        SupabaseDB.broadcastChange('app_settings', 'UPDATE', { qris_image: cachedQrisList[0].gambar }, 'Admin QRIS');
                    }
                }
            } catch (e) {}

            renderQrisTable();
            alert(`Data "${item.nama}" berhasil dihapus.`);
        }

        // Render Rotation Table (Menampilkan seluruh 17 halaman display TV)