/**
 * MODUL ADMIN: admin-qurban.js
 * Digital Signage Masjid Jami' Al-Jihad
 * Versi: aljihad-signage-v5.8.46
 */

        // 8. MANAJEMEN PENERIMAAN HEWAN QURBAN IDUL ADHA
        // =======================================================

        const DEFAULT_ADMIN_QURBAN_DATA = [
            { id: 1, nama: "H. Bambang Sugiarto", bin: "Sekeluarga (Kelompok Sapi A)", jenis: "SAPI", jumlah: 1, infaq: 500000, status: "Diterima" },
            { id: 2, nama: "Ir. H. Muhammad Arifin", bin: "Sekeluarga (Kelompok Sapi A)", jenis: "SAPI", jumlah: 1, infaq: 500000, status: "Diterima" },
            { id: 3, nama: "Drs. H. Ahmad Fauzi", bin: "Sekeluarga (Kelompok Sapi B)", jenis: "SAPI", jumlah: 1, infaq: 500000, status: "Diterima" },
            { id: 4, nama: "Hj. Siti Rahmah", bin: "Binti H. Dahlan (Kelompok Sapi B)", jenis: "SAPI", jumlah: 1, infaq: 500000, status: "Diterima" },
            { id: 5, nama: "H. Hendra Gunawan", bin: "Sekeluarga (Sapi Mandiri)", jenis: "SAPI", jumlah: 1, infaq: 1000000, status: "Diterima" },
            { id: 6, nama: "Dr. H. Sulaiman Hasan", bin: "Sekeluarga (Sapi Mandiri)", jenis: "SAPI", jumlah: 1, infaq: 1000000, status: "Diterima" },
            { id: 7, nama: "H. Fachruddin", bin: "Sekeluarga (Sapi Mandiri)", jenis: "SAPI", jumlah: 1, infaq: 1000000, status: "Diterima" },
            { id: 8, nama: "Ustadz H. Abdullah Syafi'i", bin: "Bin H. M. Nur", jenis: "KAMBING", jumlah: 1, infaq: 350000, status: "Diterima" },
            { id: 9, nama: "Bpk. Rahmat Hidayat", bin: "Bin Sastro Wardoyo", jenis: "KAMBING", jumlah: 1, infaq: 350000, status: "Diterima" },
            { id: 10, nama: "Bpk. Dedi Kurniawan", bin: "Sekeluarga", jenis: "KAMBING", jumlah: 1, infaq: 350000, status: "Diterima" },
            { id: 11, nama: "Ibu Fatimah Az-Zahra", bin: "Binti Syamsudin", jenis: "DOMBA", jumlah: 1, infaq: 350000, status: "Diterima" },
            { id: 12, nama: "Bpk. Agus Setiawan", bin: "Bin H. Ridwan", jenis: "KAMBING", jumlah: 1, infaq: 350000, status: "Diterima" },
            { id: 13, nama: "Bpk. Ilham Pratama", bin: "Sekeluarga", jenis: "KAMBING", jumlah: 1, infaq: 350000, status: "Diterima" },
            { id: 14, nama: "Hj. Nurhayati", bin: "Binti H. Marzuki", jenis: "DOMBA", jumlah: 1, infaq: 350000, status: "Diterima" },
            { id: 15, nama: "Bpk. Hendro Susilo", bin: "Bin Sudarsono", jenis: "KAMBING", jumlah: 1, infaq: 350000, status: "Diterima" },
            { id: 16, nama: "Bpk. Zainal Abidin", bin: "Sekeluarga", jenis: "KAMBING", jumlah: 1, infaq: 350000, status: "Diterima" },
            { id: 17, nama: "Bpk. Wahyu Nugroho", bin: "Bin Siswanto", jenis: "KAMBING", jumlah: 1, infaq: 350000, status: "Diterima" },
            { id: 18, nama: "Bpk. Tri Wibowo", bin: "Sekeluarga", jenis: "DOMBA", jumlah: 1, infaq: 350000, status: "Diterima" }
        ];

        // Render Tabel Shohibul Qurban di Admin
        function renderAdminQurbanTable(data) {
            cachedQurbanData = (data && Array.isArray(data) && data.length > 0) ? data : DEFAULT_ADMIN_QURBAN_DATA;

            let totalSapi = 0;
            let totalKambing = 0;
            let totalDomba = 0;
            let totalInfaq = 0;

            const tbody = document.getElementById('adminQurbanTableBody');
            if (tbody) {
                tbody.innerHTML = cachedQurbanData.map((item, idx) => {
                    const jns = (item.jenis || 'KAMBING').toUpperCase().trim();
                    const jml = parseInt(item.jumlah) || 1;
                    const inf = parseFloat(item.infaq) || 0;

                    if (jns === 'SAPI') totalSapi += jml;
                    else if (jns === 'DOMBA') { totalDomba += jml; totalKambing += jml; }
                    else { totalKambing += jml; }

                    totalInfaq += inf;

                    let badgeHtml = '';
                    if (jns === 'SAPI') {
                        badgeHtml = '<span class="badge badge-warning font-weight-bold"><i class="fas fa-cow mr-1"></i> SAPI</span>';
                    } else if (jns === 'DOMBA') {
                        badgeHtml = '<span class="badge badge-info font-weight-bold"><i class="fas fa-paw mr-1"></i> DOMBA</span>';
                    } else {
                        badgeHtml = '<span class="badge badge-success font-weight-bold"><i class="fas fa-paw mr-1"></i> KAMBING</span>';
                    }

                    return `
                        <tr>
                            <td class="text-center font-weight-bold">${idx + 1}</td>
                            <td><strong>${item.nama}</strong></td>
                            <td class="text-muted small">${item.bin || '-'}</td>
                            <td class="text-center">${badgeHtml}</td>
                            <td class="text-center font-weight-bold">${jml} Ekor</td>
                            <td class="text-right font-weight-bold text-success">${formatRupiah(inf)}</td>
                            <td class="text-center"><span class="badge badge-light border text-success"><i class="fas fa-check mr-1"></i> ${item.status || 'Diterima'}</span></td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-danger" onclick="hapusShohibulQurban(${idx})" title="Hapus Baris"><i class="fas fa-trash-alt"></i></button>
                            </td>
                        </tr>
                    `;
                }).join('');
            }

            // Update KPI Cards di Admin
            if (document.getElementById('adminTotalSapi')) document.getElementById('adminTotalSapi').textContent = `${totalSapi} Ekor`;
            if (document.getElementById('adminSubSapi')) document.getElementById('adminSubSapi').textContent = `Terkumpul dari shohibul qurban`;
            if (document.getElementById('adminTotalKambing')) document.getElementById('adminTotalKambing').textContent = `${totalKambing} Ekor`;
            if (document.getElementById('adminSubKambing')) document.getElementById('adminSubKambing').textContent = `${totalKambing - totalDomba} Kambing, ${totalDomba} Domba`;
            if (document.getElementById('adminTotalInfaq')) document.getElementById('adminTotalInfaq').textContent = formatRupiah(totalInfaq);
            if (document.getElementById('badgeTotalShohibul')) document.getElementById('badgeTotalShohibul').textContent = `${cachedQurbanData.length} Orang Terdaftar`;

            // Cek status rotasi slide qurban
            if (cachedRotationPages && cachedRotationPages.length > 0) {
                const qPage = cachedRotationPages.find(p => (p.url || p.path || '').includes('qurban'));
                if (qPage && document.getElementById('adminStatusRotasiQurban')) {
                    const isActive = qPage.active !== false;
                    document.getElementById('adminStatusRotasiQurban').textContent = isActive ? 'Aktif di TV' : 'Nonaktif di TV';
                    document.getElementById('adminStatusRotasiQurban').className = isActive ? 'h5 mb-0 font-weight-bold text-success' : 'h5 mb-0 font-weight-bold text-danger';
                }
            }
        }

        // Tambah Shohibul Qurban Baru
        function tambahShohibulQurban() {
            const nama = document.getElementById('newQurbanNama')?.value.trim();
            const bin = document.getElementById('newQurbanBin')?.value.trim();
            const jenis = document.getElementById('newQurbanJenis')?.value;
            const jumlah = parseInt(document.getElementById('newQurbanJumlah')?.value) || 1;
            const infaq = parseFloat(document.getElementById('newQurbanInfaq')?.value) || 0;
            const status = document.getElementById('newQurbanStatus')?.value || 'Diterima';

            if (!nama) {
                alert('Harap masukkan nama shohibul qurban!');
                return;
            }

            const newItem = {
                id: Date.now(),
                nama: nama,
                bin: bin || 'Atas Nama Shohibul Qurban',
                jenis: jenis,
                jumlah: jumlah,
                infaq: infaq,
                status: status
            };

            cachedQurbanData.push(newItem);
            renderAdminQurbanTable(cachedQurbanData);

            // Simpan sementara di local storage
            localStorage.setItem('cached_qurban_data', JSON.stringify(cachedQurbanData));

            $('#modalTambahQurban').modal('hide');
            document.getElementById('formTambahQurban').reset();
            alert(`Alhamdulillah! Shohibul qurban "${nama}" (${jenis}) berhasil ditambahkan ke daftar.`);
        }

        // Hapus Shohibul Qurban
        function hapusShohibulQurban(idx) {
            if (idx >= 0 && idx < cachedQurbanData.length) {
                const target = cachedQurbanData[idx];
                if (confirm(`Apakah Anda yakin ingin menghapus data shohibul qurban "${target.nama}"?`)) {
                    cachedQurbanData.splice(idx, 1);
                    renderAdminQurbanTable(cachedQurbanData);
                    localStorage.setItem('cached_qurban_data', JSON.stringify(cachedQurbanData));
                }
            }
        }

        // Simpan Data Qurban & Jadwal Penyembelihan ke Supabase
        async function simpanDataQurban() {
            const jam = document.getElementById('inputQurbanJam')?.value.trim() || '07:30 WIB s/d Selesai';
            const lokasi = document.getElementById('inputQurbanLokasi')?.value.trim() || 'Hari H 10 Dzulhijjah • Halaman Belakang Masjid Jami\' Al-Jihad';

            try {
                let resOk = false;
                if (window.SupabaseDB && typeof SupabaseDB.saveQurbanUnified === 'function') {
                    const qRes = await SupabaseDB.saveQurbanUnified({
                        qurban_data: cachedQurbanData,
                        qurban_jam_sembelih: jam,
                        qurban_lokasi_sembelih: lokasi
                    });
                    resOk = qRes.success;
                } else {
                    const res = await fetch(`${SUPABASE_CONFIG.url}/rest/v1/app_settings?id=eq.1`, {
                        method: 'PATCH',
                        headers: getSupabaseHeaders(),
                        body: JSON.stringify({
                            qurban_data: cachedQurbanData,
                            qurban_jam_sembelih: jam,
                            qurban_lokasi_sembelih: lokasi
                        })
                    });
                    resOk = res.ok;
                    localStorage.setItem('cached_qurban_data', JSON.stringify(cachedQurbanData));
                    if (resOk && window.SupabaseDB && typeof SupabaseDB.broadcastChange === 'function') {
                        SupabaseDB.broadcastChange('qurban', 'UPDATE', { qurban_data: cachedQurbanData }, 'Panitia Qurban');
                    }
                }

                if (resOk) {
                    alert('Alhamdulillah! Seluruh data penerimaan hewan qurban dan jadwal penyembelihan berhasil disimpan ke Supabase & seketika tayang di layar TV display.');
                } else {
                    alert('Data qurban berhasil disimpan di cache lokal browser (offline-ready).');
                }
            } catch (err) {
                alert('Tersimpan di cache lokal: ' + err.message);
            }
        }

        // =======================================================