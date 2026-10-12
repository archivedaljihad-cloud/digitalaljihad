/**
 * MODUL ADMIN: admin-kas.js
 * Digital Signage Masjid Jami' Al-Jihad
 * Versi: aljihad-signage-v5.8.45
 */

        function renderKasTables(data) {
            let totalSaldo = 0;
            let totalPemasukan = 0;
            let totalPengeluaran = 0;
            let totalMasukBulanIni = 0;
            const now = new Date();
            const currentMonth = now.getMonth();
            const currentYear = now.getFullYear();

            (data || []).forEach(item => {
                const masuk = parseFloat(item.pemasukan) || 0;
                const keluar = parseFloat(item.pengeluaran) || 0;
                totalSaldo += (masuk - keluar);
                totalPemasukan += masuk;
                totalPengeluaran += keluar;

                if (item.tanggal) {
                    const itemDate = new Date(item.tanggal);
                    if (itemDate.getMonth() === currentMonth && itemDate.getFullYear() === currentYear) {
                        totalMasukBulanIni += masuk;
                    }
                }
            });

            // Update Card Metrik Dashboard
            if (document.getElementById('statKasUtama')) {
                document.getElementById('statKasUtama').textContent = formatRupiah(totalSaldo);
            }

            // Update Card Metrik di View Kas Utama Masjid (Saldo Terkini, Pemasukan, Pengeluaran)
            if (document.getElementById('statSaldoKasKeuanganCard')) {
                document.getElementById('statSaldoKasKeuanganCard').textContent = formatRupiah(totalSaldo);
            }
            if (document.getElementById('statTotalMasukKasCard')) {
                document.getElementById('statTotalMasukKasCard').textContent = formatRupiah(totalPemasukan);
            }
            if (document.getElementById('statTotalKeluarKasCard')) {
                document.getElementById('statTotalKeluarKasCard').textContent = formatRupiah(totalPengeluaran);
            }

            // Quick table (Dashboard - 5 record terbaru)
            const quickTbody = document.getElementById('quickKasTableBody');
            if (quickTbody) {
                if (!data || data.length === 0) {
                    quickTbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-3">Belum ada transaksi kas tercatat.</td></tr>';
                } else {
                    const sortedQuick = [...data].sort((a, b) => {
                        const dateA = new Date(a.tanggal || 0).getTime();
                        const dateB = new Date(b.tanggal || 0).getTime();
                        if (dateA !== dateB) return dateB - dateA;
                        return (parseInt(b.id) || 0) - (parseInt(a.id) || 0);
                    });
                    quickTbody.innerHTML = sortedQuick.slice(0, 5).map(item => {
                        const isMasuk = (parseFloat(item.pemasukan) || 0) > 0;
                        const nominal = isMasuk ? item.pemasukan : item.pengeluaran;
                        const badgeHTML = isMasuk ? '<span class="badge-masuk">Masuk</span>' : '<span class="badge-keluar">Keluar</span>';
                        const textClass = isMasuk ? 'text-success' : 'text-danger';
                        const sign = isMasuk ? '+' : '-';
                        const tgl = item.tanggal ? new Date(item.tanggal).toLocaleDateString('id-ID') : '-';
                        return `
                            <tr>
                                <td>${tgl}</td>
                                <td><strong>${item.deskripsi || item.uraian || 'Infaq Kas'}</strong></td>
                                <td>${badgeHTML}</td>
                                <td class="text-right font-weight-bold ${textClass}">${sign} ${formatRupiah(nominal)}</td>
                            </tr>
                        `;
                    }).join('');
                }
            }

            // Jalankan filter & render baris tabel utama
            filterKasTable();
        }

        // Filter & Search Tabel Kas Utama Masjid (Murni Kas Utama Masjid Saja)
        // Urutan: Paling atas terbaru, paling bawah yang lama
        function filterKasTable() {
            const query = (document.getElementById('kasSearchInput')?.value || '').toLowerCase().trim();
            const typeFilter = document.getElementById('kasFilterType')?.value || 'all';
            const katFilter = document.getElementById('kasFilterKat')?.value || 'all';

            // 1. Siapkan dataset MURNI Kas Utama Masjid (tidak mencampur kas ambulance maupun program infaq)
            const kasUtamaItems = (cachedKasData || []).map(item => ({
                ...item,
                kategori: item.kategori || 'Kas Utama Masjid',
                _sourceTable: 'keuangan'
            }));

            let baseData = [...kasUtamaItems];

            // Filter Kategori Kas Utama jika ada
            if (katFilter !== 'all') {
                baseData = baseData.filter(item => (item.kategori || '').toLowerCase() === katFilter.toLowerCase());
            }

            let totalAvailable = kasUtamaItems.length;
            let filtered = baseData;

            // 2. Filter berdasarkan Tipe (Pemasukan / Pengeluaran)
            if (typeFilter === 'masuk') {
                filtered = filtered.filter(item => (parseFloat(item.pemasukan) || 0) > 0);
            } else if (typeFilter === 'keluar') {
                filtered = filtered.filter(item => (parseFloat(item.pengeluaran) || 0) > 0);
            }

            // 3. Filter berdasarkan Pencarian Teks
            if (query) {
                filtered = filtered.filter(item => {
                    const desc = (item.deskripsi || item.uraian || '').toLowerCase();
                    const masukStr = (item.pemasukan || '').toString();
                    const keluarStr = (item.pengeluaran || '').toString();
                    const tglStr = (item.tanggal || '').toLowerCase();
                    const katStr = (item.kategori || '').toLowerCase();
                    return desc.includes(query) || masukStr.includes(query) || keluarStr.includes(query) || tglStr.includes(query) || katStr.includes(query);
                });
            }

            // 4. Urutkan: Paling atas terbaru, paling bawah yang lama (Tanggal DESC, ID DESC)
            filtered = [...filtered].sort((a, b) => {
                const dateA = new Date(a.tanggal || 0).getTime();
                const dateB = new Date(b.tanggal || 0).getTime();
                if (dateA !== dateB) return dateB - dateA;
                return (parseInt(b.id) || 0) - (parseInt(a.id) || 0);
            });

            // Simpan cache hasil filter aktif untuk ekspor excel
            window._lastFilteredKasData = filtered;

            // 5. Update badge info status filter
            const infoEl = document.getElementById('kasFilterInfo');
            if (infoEl) {
                if (query || typeFilter !== 'all' || (katFilter !== 'all' && katFilter !== 'Kas Utama Masjid')) {
                    infoEl.className = 'badge badge-warning font-weight-bold';
                    infoEl.textContent = `Ditemukan: ${filtered.length} dari ${totalAvailable}`;
                } else {
                    infoEl.className = 'badge badge-light border text-muted font-weight-bold';
                    infoEl.textContent = `Menampilkan Semua (${totalAvailable})`;
                }
            }

            renderKasTableRows(filtered);
        }

        // Render Baris Tabel Kas Utama dengan Tombol Edit & Hapus (Urutan: Paling atas terbaru, paling bawah yang lama)
        function renderKasTableRows(items) {
            const mainTbody = document.getElementById('mainKasTableBody');
            if (!mainTbody) return;
            if (!items || items.length === 0) {
                mainTbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4"><i class="fas fa-search mr-1"></i> Tidak ada transaksi kas utama yang sesuai dengan filter pencarian.</td></tr>';
                return;
            }

            // Jamin urutan: paling atas terbaru dan paling bawah yang lama
            const sortedItems = [...items].sort((a, b) => {
                const dateA = new Date(a.tanggal || 0).getTime();
                const dateB = new Date(b.tanggal || 0).getTime();
                if (dateA !== dateB) return dateB - dateA;
                return (parseInt(b.id) || 0) - (parseInt(a.id) || 0);
            });

            mainTbody.innerHTML = sortedItems.map(item => {
                const isMasuk = (parseFloat(item.pemasukan) || 0) > 0;
                const nominal = isMasuk ? item.pemasukan : item.pengeluaran;
                const badgeHTML = isMasuk ? '<span class="badge-masuk">Pemasukan</span>' : '<span class="badge-keluar">Pengeluaran</span>';
                const textClass = isMasuk ? 'text-success' : 'text-danger';
                const sign = isMasuk ? '+' : '-';
                const tgl = item.tanggal ? new Date(item.tanggal).toLocaleDateString('id-ID') : '-';

                return `
                    <tr>
                        <td>${tgl}</td>
                        <td><strong>${escapeHtml(item.deskripsi || item.uraian || 'Infaq Kas')}</strong></td>
                        <td><span class="badge badge-success px-2 py-1">${escapeHtml(item.kategori || 'Kas Utama Masjid')}</span></td>
                        <td>${badgeHTML}</td>
                        <td class="text-right font-weight-bold ${textClass}">${sign} ${formatRupiah(nominal)}</td>
                        <td class="text-center text-nowrap">
                            <button class="btn btn-sm btn-outline-warning mr-1" onclick="bukaModalEditKas(${item.id}, 'keuangan')" title="Koreksi / Edit"><i class="fas fa-edit"></i></button>
                            <button class="btn btn-sm btn-outline-danger" onclick="hapusTransaksi(${item.id}, 'keuangan')" title="Hapus"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        // Render Ambulance Data (Urutan: Paling atas terbaru, paling bawah yang lama)
        function renderAmbulanceData(data) {
            let totalSaldoAmb = 0;
            (data || []).forEach(item => {
                totalSaldoAmb += (parseFloat(item.pemasukan) || 0) - (parseFloat(item.pengeluaran) || 0);
            });

            if (document.getElementById('statKasAmbulance')) {
                document.getElementById('statKasAmbulance').textContent = formatRupiah(totalSaldoAmb);
            }
            if (document.getElementById('statSaldoAmbulanceCard')) {
                document.getElementById('statSaldoAmbulanceCard').textContent = formatRupiah(totalSaldoAmb);
            }

            const ambTbody = document.getElementById('ambulanceTableBody');
            if (ambTbody) {
                if (!data || data.length === 0) {
                    ambTbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-4">Belum ada riwayat transaksi armada ambulance di database.</td></tr>';
                } else {
                    // Jamin urutan: paling atas terbaru dan paling bawah yang lama (Tanggal DESC, ID DESC)
                    const sortedAmb = [...data].sort((a, b) => {
                        const dateA = new Date(a.tanggal || 0).getTime();
                        const dateB = new Date(b.tanggal || 0).getTime();
                        if (dateA !== dateB) return dateB - dateA;
                        return (parseInt(b.id) || 0) - (parseInt(a.id) || 0);
                    });

                    ambTbody.innerHTML = sortedAmb.map(item => {
                        const isMasuk = (parseFloat(item.pemasukan) || 0) > 0;
                        const nominal = isMasuk ? item.pemasukan : item.pengeluaran;
                        const badgeHTML = isMasuk ? '<span class="badge-masuk">Pemasukan</span>' : '<span class="badge-keluar">Pengeluaran</span>';
                        const textClass = isMasuk ? 'text-success' : 'text-danger';
                        const sign = isMasuk ? '+' : '-';
                        const tgl = item.tanggal ? new Date(item.tanggal).toLocaleDateString('id-ID') : '-';
                        return `
                            <tr>
                                <td>${tgl}</td>
                                <td><strong>${item.deskripsi || item.uraian || 'Operasional Ambulance'}</strong></td>
                                <td>${badgeHTML}</td>
                                <td class="text-right font-weight-bold ${textClass}">${sign} ${formatRupiah(nominal)}</td>
                                <td class="text-center text-nowrap">
                                    <button class="btn btn-sm btn-outline-warning mr-1" onclick="bukaModalEditKas(${item.id}, 'keuangan_ambulance')" title="Koreksi / Edit"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="hapusTransaksi(${item.id}, 'keuangan_ambulance')" title="Hapus"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        `;
                    }).join('');
                }
            }
        }

        // =======================================================

        function bukaModalEditKas(id, table = 'keuangan') {
            const dataset = (table === 'keuangan_ambulance') ? cachedAmbData : cachedKasData;
            const item = dataset.find(d => d.id == id);
            if (!item) {
                alert('Data transaksi tidak ditemukan.');
                return;
            }

            const isMasuk = (parseFloat(item.pemasukan) || 0) > 0;
            const rawNominal = isMasuk ? item.pemasukan : item.pengeluaran;

            document.getElementById('editKasId').value = item.id;
            document.getElementById('editKasTable').value = table;
            document.getElementById('editKasTanggal').value = item.tanggal ? item.tanggal.split('T')[0] : '';
            document.getElementById('editKasTipe').value = isMasuk ? 'masuk' : 'keluar';
            let katVal = item.kategori || (table === 'keuangan_ambulance' ? 'Kas Ambulance' : 'Kas Utama Masjid');
            if (katVal === 'Infaq Renovasi' || katVal === 'Penggalangan Infaq' || katVal === 'Kas Utama') katVal = 'Kas Utama Masjid';
            document.getElementById('editKasKategori').value = katVal;
            document.getElementById('editKasUraian').value = item.deskripsi || item.uraian || '';
            document.getElementById('editKasNominal').value = parseInt(rawNominal || 0, 10).toLocaleString('id-ID');

            $('#modalEditKas').modal('show');
        }

        // Simpan Koreksi / Edit Kas ke Supabase
        async function simpanEditTransaksiKas() {
            const id = document.getElementById('editKasId')?.value;
            const table = document.getElementById('editKasTable')?.value || 'keuangan';
            const tgl = document.getElementById('editKasTanggal')?.value;
            const tipe = document.getElementById('editKasTipe')?.value;
            const kat = document.getElementById('editKasKategori')?.value;
            const uraian = document.getElementById('editKasUraian')?.value;
            const nominal = parseRupiahInput(document.getElementById('editKasNominal')?.value);

            if (!id || !uraian || nominal <= 0) {
                alert('Harap lengkapi uraian dan nominal transaksi!');
                return;
            }

            const payload = {
                tanggal: tgl,
                deskripsi: uraian,
                pemasukan: (tipe === 'masuk' ? nominal : 0),
                pengeluaran: (tipe === 'keluar' ? nominal : 0),
                saldo: nominal,
                kategori: kat
            };

            try {
                const res = await fetch(`${SUPABASE_CONFIG.url}/rest/v1/${table}?id=eq.${id}`, {
                    method: 'PATCH',
                    headers: {
                        ...getSupabaseHeaders(),
                        'Prefer': 'return=representation'
                    },
                    body: JSON.stringify(payload)
                });

                if (res.ok) {
                    $('#modalEditKas').modal('hide');
                    if (window.SupabaseDB && typeof SupabaseDB.broadcastChange === 'function') {
                        SupabaseDB.broadcastChange(table, 'UPDATE', payload, 'Bendahara Kas');
                    }
                    await loadAllSupabaseData();
                    alert('Alhamdulillah! Koreksi transaksi kas berhasil disimpan ke Supabase.');
                } else {
                    const errJson = await res.json().catch(() => ({}));
                    throw new Error(errJson.message || 'Gagal menyimpan perubahan ke server.');
                }
            } catch (err) {
                alert('Pemberitahuan: ' + err.message);
            }
        }

        // Redirect legacy simpanPengurusBaru ke sistem RBAC resmi
        function simpanPengurusBaru() {
            bukaModalTambahUser();
        }

        // =======================================================
        // 7. OPERASI TULIS (WRITE & DELETE) KE SUPABASE
        // =======================================================

        // Simpan Transaksi Kas
        async function simpanTransaksiKas() {
            const tgl = document.getElementById('kasTanggal')?.value;
            const tipe = document.getElementById('kasTipe')?.value;
            const kat = document.getElementById('kasKategori')?.value;
            const uraian = document.getElementById('kasUraian')?.value;
            const nominal = parseRupiahInput(document.getElementById('kasNominal')?.value);

            if (!uraian || nominal <= 0) {
                alert('Harap lengkapi uraian dan nominal transaksi!');
                return;
            }

            const targetTable = (kat === 'Kas Ambulance') ? 'keuangan_ambulance' : 'keuangan';
            const payload = {
                tanggal: tgl,
                deskripsi: uraian,
                pemasukan: (tipe === 'masuk' ? nominal : 0),
                pengeluaran: (tipe === 'keluar' ? nominal : 0),
                saldo: nominal,
                kategori: kat
            };

            try {
                let resOk = false;
                if (window.SupabaseDB && typeof SupabaseDB.saveTransaksiKas === 'function') {
                    const saveRes = await SupabaseDB.saveTransaksiKas(payload);
                    resOk = saveRes.success;
                    if (!resOk && saveRes.error) throw new Error(saveRes.error);
                } else {
                    const res = await fetch(`${SUPABASE_CONFIG.url}/rest/v1/${targetTable}`, {
                        method: 'POST',
                        headers: {
                            ...getSupabaseHeaders(),
                            'Prefer': 'return=representation'
                        },
                        body: JSON.stringify(payload)
                    });
                    resOk = res.ok;
                    if (!resOk) {
                        const errJson = await res.json().catch(() => ({}));
                        throw new Error(errJson.message || 'Gagal menyimpan ke server.');
                    }
                    if (window.SupabaseDB && typeof SupabaseDB.broadcastChange === 'function') {
                        SupabaseDB.broadcastChange(targetTable, 'INSERT', payload, 'Bendahara Kas');
                    }
                }

                if (resOk) {
                    $('#modalTambahKas').modal('hide');
                    document.getElementById('kasUraian').value = '';
                    document.getElementById('kasNominal').value = '';
                    await loadAllSupabaseData();
                    alert('Alhamdulillah! Transaksi kas berhasil dicatat dan disinkronkan ke Supabase.');
                }
            } catch (err) {
                alert('Pemberitahuan: ' + err.message);
            }
        }

        // Hapus Transaksi Kas
        async function hapusTransaksi(id, table = 'keuangan') {
            if (!confirm('Apakah Anda yakin ingin menghapus catatan transaksi ini dari database?')) {
                return;
            }

            try {
                let delOk = false;
                if (window.SupabaseDB && typeof SupabaseDB.deleteTransaksiKas === 'function') {
                    delOk = await SupabaseDB.deleteTransaksiKas(id, table);
                } else {
                    const res = await fetch(`${SUPABASE_CONFIG.url}/rest/v1/${table}?id=eq.${id}`, {
                        method: 'DELETE',
                        headers: getSupabaseHeaders()
                    });
                    delOk = res.ok;
                    if (delOk && window.SupabaseDB && typeof SupabaseDB.broadcastChange === 'function') {
                        SupabaseDB.broadcastChange(table, 'DELETE', { id }, 'Bendahara Kas');
                    }
                }

                if (delOk) {
                    await loadAllSupabaseData();
                    alert('Catatan transaksi berhasil dihapus.');
                } else {
                    alert('Gagal menghapus transaksi dari database.');
                }
            } catch (err) {
                alert('Terjadi kesalahan saat menghapus data: ' + err.message);
            }
        }

        // =======================================================

        function exportKasExcel() {
            const dataToExport = window._lastFilteredKasData || cachedKasData || [];
            if (!dataToExport || dataToExport.length === 0) {
                alert('Belum ada data transaksi kas untuk diexport!');
                return;
            }

            const katFilter = document.getElementById('kasFilterKat')?.value || 'Semua';
            let csvContent = "data:text/csv;charset=utf-8,\uFEFF";
            csvContent += "No,Tanggal,Uraian Transaksi,Kategori,Tipe,Pemasukan (Rp),Pengeluaran (Rp)\n";

            dataToExport.forEach((item, index) => {
                const isMasuk = (parseFloat(item.pemasukan) || 0) > 0;
                const tipe = isMasuk ? "Pemasukan" : "Pengeluaran";
                const cleanUraian = (item.deskripsi || item.uraian || 'Infaq').replace(/,/g, ' ');
                const cleanKat = (item.kategori || 'Kas Utama Masjid').replace(/,/g, ' ');
                const row = [
                    index + 1,
                    item.tanggal || '-',
                    cleanUraian,
                    cleanKat,
                    tipe,
                    parseFloat(item.pemasukan) || 0,
                    parseFloat(item.pengeluaran) || 0
                ].join(",");
                csvContent += row + "\n";
            });

            const encodedUri = encodeURI(csvContent);
            const link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", `Laporan_Kas_${katFilter.replace(/\s+/g, '_')}_${new Date().toISOString().split('T')[0]}.csv`);
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        // =====================================================