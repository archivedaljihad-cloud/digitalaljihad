/**
 * MODUL ADMIN: admin-undangan.js
 * Digital Signage Masjid Jami' Al-Jihad
 * Versi: aljihad-signage-v5.8.45
 */

        // MANAJEMEN SURAT UNDANGAN LUAR (WARTA UKHUWAH)
        // =======================================================
        let cachedUndanganList = [];

        async function loadUndanganEksternalAdmin() {
            const tbody = document.getElementById('tbodyUndanganEksternal');
            if (!tbody) return;

            try {
                if (window.SupabaseDB && typeof SupabaseDB.getUndanganEksternal === 'function') {
                    cachedUndanganList = await SupabaseDB.getUndanganEksternal();
                } else {
                    const raw = localStorage.getItem('aljihad_undangan_eksternal');
                    cachedUndanganList = raw ? JSON.parse(raw) : [];
                }
            } catch (err) {
                console.warn('Gagal load undangan eksternal:', err);
                cachedUndanganList = [];
            }

            renderUndanganEksternalTable();
        }

        function renderUndanganEksternalTable() {
            const tbody = document.getElementById('tbodyUndanganEksternal');
            if (!tbody) return;

            const list = cachedUndanganList || [];
            list.sort((a, b) => (parseInt(a.urutan, 10) || 0) - (parseInt(b.urutan, 10) || 0));

            if (list.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="fas fa-envelope-open-text fa-3x text-gray-300 mb-3 d-block"></i>
                            <span class="font-weight-bold">Belum ada Undangan Jama'ah yang diinput.</span><br>
                            <small class="text-muted">Klik tombol <strong>"Tambah Undangan Baru"</strong> di atas untuk memasukkan undangan kegiatan dari masjid/mushola mitra.</small>
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = list.map((item, idx) => {
                const isAktif = (item.is_active !== false && item.is_active !== '0' && item.is_active !== 0);
                const badgeStatus = isAktif
                    ? '<span class="badge badge-success px-2 py-1 font-weight-bold"><i class="fas fa-check-circle mr-1"></i> Aktif di TV</span>'
                    : '<span class="badge badge-secondary px-2 py-1 font-weight-bold"><i class="fas fa-eye-slash mr-1"></i> Nonaktif</span>';

                const toggleBtn = isAktif
                    ? `<button class="btn btn-outline-secondary btn-sm" onclick="toggleStatusUndangan(${item.id})" title="Nonaktifkan dari TV"><i class="fas fa-eye-slash"></i></button>`
                    : `<button class="btn btn-outline-success btn-sm" onclick="toggleStatusUndangan(${item.id})" title="Aktifkan di TV"><i class="fas fa-check"></i></button>`;

                const penceramahText = item.penceramah && item.penceramah.trim()
                    ? `<span class="text-primary font-weight-bold"><i class="fas fa-microphone-alt mr-1"></i>${escapeHtml(item.penceramah)}</span>`
                    : `<span class="text-muted font-italic small">- Tidak ada penceramah / belum diisi -</span>`;

                return `
                    <tr>
                        <td class="text-center font-weight-bold align-middle">${idx + 1}</td>
                        <td class="align-middle">
                            <strong class="text-gray-900 d-block" style="font-size: 0.95rem;">
                                <i class="fas fa-mosque text-info mr-1"></i> ${escapeHtml(item.nama_pengundang || '-')}
                            </strong>
                            <small class="text-muted d-block mt-1">
                                <span class="badge badge-light border">Urutan: ${item.urutan || (idx + 1)}</span>
                            </small>
                        </td>
                        <td class="align-middle">
                            <strong class="text-dark d-block mb-1" style="font-size: 0.93rem;">${escapeHtml(item.nama_acara || '-')}</strong>
                            <small class="d-block">${penceramahText}</small>
                        </td>
                        <td class="align-middle">
                            <strong class="text-dark d-block small mb-1"><i class="far fa-calendar-alt text-info mr-1"></i> ${escapeHtml(item.tanggal_acara || '-')}</strong>
                            <span class="badge badge-info px-2 py-1"><i class="far fa-clock mr-1"></i> ${escapeHtml(item.waktu_acara || '-')}</span>
                        </td>
                        <td class="align-middle">
                            <small class="text-dark font-weight-bold d-block"><i class="fas fa-map-marker-alt text-danger mr-1"></i> ${escapeHtml(item.tempat_acara || '-')}</small>
                            ${item.keterangan ? `<small class="text-muted d-block mt-1 text-truncate" style="max-width: 200px;" title="${escapeHtml(item.keterangan)}">${escapeHtml(item.keterangan)}</small>` : ''}
                        </td>
                        <td class="text-center align-middle">${badgeStatus}</td>
                        <td class="text-center align-middle">
                            <div class="btn-group" role="group">
                                ${toggleBtn}
                                <button class="btn btn-warning btn-sm font-weight-bold ml-1" onclick="editUndanganEksternal(${item.id})" title="Edit Undangan">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-danger btn-sm font-weight-bold ml-1" onclick="hapusUndanganEksternal(${item.id})" title="Hapus Undangan">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        function bukaModalTambahUndangan() {
            document.getElementById('modalUndanganTitle').innerHTML = '<i class="fas fa-plus-circle mr-2"></i> Tambah Undangan Jama\'ah';
            document.getElementById('undanganId').value = '';
            document.getElementById('undanganPengundang').value = '';
            document.getElementById('undanganAcara').value = '';
            document.getElementById('undanganPenceramah').value = '';
            document.getElementById('undanganTanggal').value = '';
            document.getElementById('undanganWaktu').value = '';
            document.getElementById('undanganTempat').value = '';
            document.getElementById('undanganKeterangan').value = '';
            document.getElementById('undanganUrutan').value = (cachedUndanganList.length + 1);
            document.getElementById('undanganActive').value = '1';

            $('#modalUndanganEksternal').modal('show');
        }

        function editUndanganEksternal(id) {
            const item = (cachedUndanganList || []).find(u => String(u.id) === String(id));
            if (!item) return;

            document.getElementById('modalUndanganTitle').innerHTML = '<i class="fas fa-edit mr-2"></i> Edit Undangan Jama\'ah';
            document.getElementById('undanganId').value = item.id;
            document.getElementById('undanganPengundang').value = item.nama_pengundang || '';
            document.getElementById('undanganAcara').value = item.nama_acara || '';
            document.getElementById('undanganPenceramah').value = item.penceramah || '';
            document.getElementById('undanganTanggal').value = item.tanggal_acara || '';
            document.getElementById('undanganWaktu').value = item.waktu_acara || '';
            document.getElementById('undanganTempat').value = item.tempat_acara || '';
            document.getElementById('undanganKeterangan').value = item.keterangan || '';
            document.getElementById('undanganUrutan').value = item.urutan || 1;
            document.getElementById('undanganActive').value = (item.is_active !== false && item.is_active !== '0' && item.is_active !== 0) ? '1' : '0';

            $('#modalUndanganEksternal').modal('show');
        }

        async function simpanUndanganEksternal(event) {
            if (event) event.preventDefault();

            const idVal = document.getElementById('undanganId').value;
            const pengundang = document.getElementById('undanganPengundang').value.trim();
            const acara = document.getElementById('undanganAcara').value.trim();
            const penceramah = document.getElementById('undanganPenceramah').value.trim();
            const tanggal = document.getElementById('undanganTanggal').value.trim();
            const waktu = document.getElementById('undanganWaktu').value.trim();
            const tempat = document.getElementById('undanganTempat').value.trim();
            const keterangan = document.getElementById('undanganKeterangan').value.trim();
            const urutan = parseInt(document.getElementById('undanganUrutan').value, 10) || 1;
            const isActive = (document.getElementById('undanganActive').value === '1');

            if (!pengundang || !acara || !tanggal || !waktu || !tempat) {
                alert('Mohon lengkapi data wajib (Nama Pengundang, Acara, Tanggal, Waktu, dan Tempat)!');
                return;
            }

            let list = [...cachedUndanganList];

            if (idVal) {
                const idx = list.findIndex(u => String(u.id) === String(idVal));
                if (idx !== -1) {
                    list[idx] = {
                        ...list[idx],
                        nama_pengundang: pengundang,
                        nama_acara: acara,
                        penceramah: penceramah,
                        tanggal_acara: tanggal,
                        waktu_acara: waktu,
                        tempat_acara: tempat,
                        keterangan: keterangan,
                        urutan: urutan,
                        is_active: isActive
                    };
                }
            } else {
                const newId = Date.now();
                list.push({
                    id: newId,
                    nama_pengundang: pengundang,
                    nama_acara: acara,
                    penceramah: penceramah,
                    tanggal_acara: tanggal,
                    waktu_acara: waktu,
                    tempat_acara: tempat,
                    keterangan: keterangan,
                    urutan: urutan,
                    is_active: isActive
                });
            }

            cachedUndanganList = list;

            if (window.SupabaseDB && typeof SupabaseDB.saveUndanganEksternal === 'function') {
                await SupabaseDB.saveUndanganEksternal(list);
            } else {
                localStorage.setItem('aljihad_undangan_eksternal', JSON.stringify(list));
            }

            $('#modalUndanganEksternal').modal('hide');
            renderUndanganEksternalTable();
            alert('Alhamdulillah! Data Undangan Jama\'ah berhasil disimpan dan langsung disinkronkan ke Layar TV!');
        }

        async function hapusUndanganEksternal(id) {
            const item = (cachedUndanganList || []).find(u => String(u.id) === String(id));
            if (!item) return;

            if (confirm(`Apakah Anda yakin ingin menghapus surat undangan "${item.nama_acara}" dari ${item.nama_pengundang}?`)) {
                cachedUndanganList = cachedUndanganList.filter(u => String(u.id) !== String(id));
                if (window.SupabaseDB && typeof SupabaseDB.saveUndanganEksternal === 'function') {
                    await SupabaseDB.saveUndanganEksternal(cachedUndanganList);
                } else {
                    localStorage.setItem('aljihad_undangan_eksternal', JSON.stringify(cachedUndanganList));
                }
                renderUndanganEksternalTable();
            }
        }

        async function toggleStatusUndangan(id) {
            const idx = (cachedUndanganList || []).findIndex(u => String(u.id) === String(id));
            if (idx === -1) return;

            cachedUndanganList[idx].is_active = !cachedUndanganList[idx].is_active;

            if (window.SupabaseDB && typeof SupabaseDB.saveUndanganEksternal === 'function') {
                await SupabaseDB.saveUndanganEksternal(cachedUndanganList);
            } else {
                localStorage.setItem('aljihad_undangan_eksternal', JSON.stringify(cachedUndanganList));
            }

            renderUndanganEksternalTable();
        }

        // =====================================================