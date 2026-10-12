/**
 * MODUL ADMIN: admin-kajian.js
 * Digital Signage Masjid Jami' Al-Jihad
 * Versi: aljihad-signage-v5.8.46
 */

        // PENGAJIAN RUTIN MALAM AHAD (SABTU MALAM)
        // =======================================================
        const NAMA_BULAN_INDONESIA = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        const KAJIAN_PRESETS = {
            1: {
                pekan: "Pekan 1",
                ustadz: "Ust. H. Ahmad Sholeh Al-Hafidz",
                gelar: "Dewan Asatidz DKM Masjid Jami' Al-Jihad",
                kitab: "Kitab Bidayatul Hidayah (Imam Al-Ghazali)",
                tema: "Kajian Fiqih Ibadah & Adab Keseharian Seorang Muslim"
            },
            2: {
                pekan: "Pekan 2",
                ustadz: "Ust. Dr. H. Faisal, M.Ag",
                gelar: "Pakar Ilmu Al-Qur'an & Tafsir",
                kitab: "Tafsir Al-Qur'an Al-'Azhim (Ibnu Katsir)",
                tema: "Tafsir Tematik: Membangun Keluarga Sakinah & Berkah Qur'ani"
            },
            3: {
                pekan: "Pekan 3",
                ustadz: "Ust. M. Syahrul Ramadhan, Lc",
                gelar: "Alumnus Al-Azhar Kairo / Dai Kamtibmas",
                kitab: "Kitab Al-Adab Al-Mufrad (Imam Bukhari)",
                tema: "Tazkiyatun Nafs: Menjaga Hati & Memperbaiki Akhlak Bermasyarakat"
            },
            4: {
                pekan: "Pekan 4",
                ustadz: "Ust. H. Ahmad Sholeh Al-Hafidz",
                gelar: "Dewan Asatidz DKM Masjid Jami' Al-Jihad",
                kitab: "Kitab Riyadhus Shalihin (Imam An-Nawawi)",
                tema: "Keutamaan Menjaga Hak Sesama Muslim & Adab Bertetangga"
            },
            5: {
                pekan: "Pekan 5",
                ustadz: "Dai Tamu / Asatidz Pilihan DKM",
                gelar: "Tabligh Akbar Tematik Keummatan",
                kitab: "Kajian Tematik Sirah Nabawiyah & Muamalah Kontemporer",
                tema: "Meneladani Kepemimpinan Rasulullah SAW di Era Modern"
            }
        };

        let currentKajianPekanActive = 1;
        let currentKajianBulanData = {
            bulan_num: (new Date()).getMonth() + 1,
            tahun: (new Date()).getFullYear(),
            jadwal_list: [
                { pekan: 1, pekan_label: "Pekan 1", tanggal: "", ustadz_nama: "", ustadz_gelar: "", kitab_rujukan: "", tema_kajian: "", waktu_pelaksanaan: "Ba'da Sholat Maghrib s/d Menjelang Isya" },
                { pekan: 2, pekan_label: "Pekan 2", tanggal: "", ustadz_nama: "", ustadz_gelar: "", kitab_rujukan: "", tema_kajian: "", waktu_pelaksanaan: "Ba'da Sholat Maghrib s/d Menjelang Isya" },
                { pekan: 3, pekan_label: "Pekan 3", tanggal: "", ustadz_nama: "", ustadz_gelar: "", kitab_rujukan: "", tema_kajian: "", waktu_pelaksanaan: "Ba'da Sholat Maghrib s/d Menjelang Isya" },
                { pekan: 4, pekan_label: "Pekan 4", tanggal: "", ustadz_nama: "", ustadz_gelar: "", kitab_rujukan: "", tema_kajian: "", waktu_pelaksanaan: "Ba'da Sholat Maghrib s/d Menjelang Isya" },
                { pekan: 5, pekan_label: "Pekan 5", tanggal: "", ustadz_nama: "", ustadz_gelar: "", kitab_rujukan: "", tema_kajian: "", waktu_pelaksanaan: "Ba'da Sholat Maghrib s/d Menjelang Isya" }
            ]
        };

        function getTodayDateStringKajian() {
            const now = new Date();
            const y = now.getFullYear();
            const m = String(now.getMonth() + 1).padStart(2, '0');
            const d = String(now.getDate()).padStart(2, '0');
            return `${y}-${m}-${d}`;
        }

        function formatTanggalIndoKajian(dateStr) {
            if (!dateStr) return '-';
            try {
                const parts = dateStr.split('-');
                if (parts.length === 3) {
                    const y = parseInt(parts[0]);
                    const m = parseInt(parts[1]) - 1;
                    const d = parseInt(parts[2]);
                    const dObj = new Date(y, m, d);
                    const days = ['Ahad', 'Senin', 'Selasa', 'Rabu', 'Kamis', "Jum'at", 'Sabtu'];
                    return `${days[dObj.getDay()]}, ${d} ${NAMA_BULAN_INDONESIA[m]} ${y}`;
                }
            } catch (e) {}
            return dateStr;
        }

        function formatTanggalShortKajian(dateStr) {
            if (!dateStr) return '-';
            try {
                const parts = dateStr.split('-');
                if (parts.length === 3) {
                    const m = parseInt(parts[1]) - 1;
                    const d = parseInt(parts[2]);
                    const shortMonths = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                    return `${d} ${shortMonths[m]}`;
                }
            } catch (e) {}
            return dateStr;
        }

        function simpanInputFormKeMemory(pekanNum) {
            const idx = pekanNum - 1;
            if (!currentKajianBulanData.jadwal_list[idx]) return;
            const target = currentKajianBulanData.jadwal_list[idx];

            const tglVal = document.getElementById('kajianTanggal')?.value || '';
            const ustadzVal = document.getElementById('kajianUstadzNama')?.value.trim() || '';
            const gelarVal = document.getElementById('kajianUstadzGelar')?.value.trim() || '';
            const kitabVal = document.getElementById('kajianKitabRujukan')?.value.trim() || '';
            const temaVal = document.getElementById('kajianTemaPembahasan')?.value.trim() || '';
            const waktuVal = document.getElementById('kajianWaktuPelaksanaan')?.value.trim() || "Ba'da Sholat Maghrib s/d Menjelang Isya";

            target.tanggal = tglVal;
            target.ustadz_nama = ustadzVal;
            target.ustadz_gelar = gelarVal;
            target.kitab_rujukan = kitabVal;
            target.tema_kajian = temaVal;
            target.waktu_pelaksanaan = waktuVal;
        }

        function muatDataPekanKeForm(pekanNum) {
            const idx = pekanNum - 1;
            const item = currentKajianBulanData.jadwal_list[idx];
            if (!item) return;

            if (document.getElementById('kajianActivePekanTitle')) {
                document.getElementById('kajianActivePekanTitle').textContent = `Pekan ke-${pekanNum}`;
            }
            if (document.getElementById('kajianTanggal')) {
                document.getElementById('kajianTanggal').value = item.tanggal || '';
            }
            if (document.getElementById('kajianTanggalLabelInfo')) {
                document.getElementById('kajianTanggalLabelInfo').textContent = item.tanggal ? formatTanggalIndoKajian(item.tanggal) : 'Belum diatur (Hari Sabtu)';
            }
            if (document.getElementById('kajianUstadzNama')) {
                document.getElementById('kajianUstadzNama').value = item.ustadz_nama || '';
            }
            if (document.getElementById('kajianUstadzGelar')) {
                document.getElementById('kajianUstadzGelar').value = item.ustadz_gelar || '';
            }
            if (document.getElementById('kajianKitabRujukan')) {
                document.getElementById('kajianKitabRujukan').value = item.kitab_rujukan || '';
            }
            if (document.getElementById('kajianTemaPembahasan')) {
                document.getElementById('kajianTemaPembahasan').value = item.tema_kajian || '';
            }
            if (document.getElementById('kajianWaktuPelaksanaan')) {
                document.getElementById('kajianWaktuPelaksanaan').value = item.waktu_pelaksanaan || "Ba'da Sholat Maghrib s/d Menjelang Isya";
            }
        }

        function switchKajianTab(pekanNum) {
            simpanInputFormKeMemory(currentKajianPekanActive);
            currentKajianPekanActive = pekanNum;
            muatDataPekanKeForm(pekanNum);
            renderKajianPekanTabsUI();
            renderKajianRekapTable();
            updateKajianPreview();
        }

        function onKajianFieldChanged() {
            simpanInputFormKeMemory(currentKajianPekanActive);
            const item = currentKajianBulanData.jadwal_list[currentKajianPekanActive - 1];
            if (document.getElementById('kajianTanggalLabelInfo') && item) {
                document.getElementById('kajianTanggalLabelInfo').textContent = item.tanggal ? formatTanggalIndoKajian(item.tanggal) : 'Belum diatur (Hari Sabtu)';
            }
            renderKajianPekanTabsUI();
            renderKajianRekapTable();
            updateKajianPreview();
        }

        function onKajianBulanTahunChanged() {
            const bulan = parseInt(document.getElementById('kajianBulanSelect')?.value || ((new Date()).getMonth() + 1));
            const tahun = parseInt(document.getElementById('kajianTahunSelect')?.value || (new Date()).getFullYear());
            currentKajianBulanData.bulan_num = bulan;
            currentKajianBulanData.tahun = tahun;
            generateTanggalSabtuBulan(false);
        }

        function generateTanggalSabtuBulan(showNotification = true) {
            simpanInputFormKeMemory(currentKajianPekanActive);
            const bulan = parseInt(document.getElementById('kajianBulanSelect')?.value || ((new Date()).getMonth() + 1));
            const tahun = parseInt(document.getElementById('kajianTahunSelect')?.value || (new Date()).getFullYear());
            const monthIndex0 = bulan - 1;

            const saturdays = [];
            const d = new Date(tahun, monthIndex0, 1);
            while (d.getMonth() === monthIndex0) {
                if (d.getDay() === 6) { // 6 = Sabtu
                    const yyyy = d.getFullYear();
                    const mm = String(d.getMonth() + 1).padStart(2, '0');
                    const dd = String(d.getDate()).padStart(2, '0');
                    saturdays.push(`${yyyy}-${mm}-${dd}`);
                }
                d.setDate(d.getDate() + 1);
            }

            currentKajianBulanData.bulan_num = bulan;
            currentKajianBulanData.tahun = tahun;

            // Terapkan ke 5 pekan
            for (let i = 0; i < 5; i++) {
                if (saturdays[i]) {
                    currentKajianBulanData.jadwal_list[i].tanggal = saturdays[i];
                } else {
                    currentKajianBulanData.jadwal_list[i].tanggal = "";
                }
            }

            muatDataPekanKeForm(currentKajianPekanActive);
            renderKajianPekanTabsUI();
            renderKajianRekapTable();
            updateKajianPreview();

            if (showNotification) {
                const namaBulan = NAMA_BULAN_INDONESIA[monthIndex0];
                alert(`Alhamdulillah! Ditemukan ${saturdays.length} hari Sabtu di bulan ${namaBulan} ${tahun}.\nTanggal berhasil diisi otomatis ke Pekan 1 s/d ${saturdays.length}!`);
            }
        }

        function isiPresetPekanAktif() {
            const p = KAJIAN_PRESETS[currentKajianPekanActive];
            if (!p) return;
            const target = currentKajianBulanData.jadwal_list[currentKajianPekanActive - 1];
            if (target) {
                target.ustadz_nama = p.ustadz;
                target.ustadz_gelar = p.gelar;
                target.kitab_rujukan = p.kitab;
                target.tema_kajian = p.tema;
            }
            muatDataPekanKeForm(currentKajianPekanActive);
            renderKajianPekanTabsUI();
            renderKajianRekapTable();
            updateKajianPreview();
        }

        function isiSemuaPresetSiklus() {
            if (!confirm('Terapkan preset tema & kitab DKM untuk seluruh 5 pekan kajian bulan ini?')) return;
            simpanInputFormKeMemory(currentKajianPekanActive);
            for (let i = 1; i <= 5; i++) {
                const p = KAJIAN_PRESETS[i];
                const target = currentKajianBulanData.jadwal_list[i - 1];
                if (p && target) {
                    target.ustadz_nama = p.ustadz;
                    target.ustadz_gelar = p.gelar;
                    target.kitab_rujukan = p.kitab;
                    target.tema_kajian = p.tema;
                }
            }
            muatDataPekanKeForm(currentKajianPekanActive);
            renderKajianPekanTabsUI();
            renderKajianRekapTable();
            updateKajianPreview();
            alert('Alhamdulillah! Preset 5 pekan DKM berhasil diterapkan ke seluruh jadwal bulan ini.');
        }

        function renderKajianPekanTabsUI() {
            const todayStr = getTodayDateStringKajian();
            for (let i = 1; i <= 5; i++) {
                const linkEl = document.getElementById(`tabLinkPekan${i}`);
                const badgeEl = document.getElementById(`tabBadgeDate${i}`);
                const subtextEl = document.getElementById(`tabSubtext${i}`);
                const item = currentKajianBulanData.jadwal_list[i - 1];

                if (linkEl) {
                    if (i === currentKajianPekanActive) {
                        linkEl.className = 'nav-link active font-weight-bold py-2 px-3 text-left bg-success text-white shadow-sm';
                    } else {
                        linkEl.className = 'nav-link font-weight-bold py-2 px-3 text-left text-dark';
                    }
                }

                if (badgeEl) {
                    if (item && item.tanggal) {
                        const shortTgl = formatTanggalShortKajian(item.tanggal);
                        if (item.tanggal === todayStr) {
                            badgeEl.className = 'badge badge-danger ml-1';
                            badgeEl.textContent = `${shortTgl} • HARI INI`;
                        } else {
                            badgeEl.className = i === currentKajianPekanActive ? 'badge badge-light text-dark ml-1' : 'badge badge-secondary ml-1';
                            badgeEl.textContent = shortTgl;
                        }
                    } else {
                        badgeEl.className = 'badge badge-light text-muted ml-1';
                        badgeEl.textContent = 'Kosong';
                    }
                }

                if (subtextEl && item) {
                    subtextEl.textContent = item.kitab_rujukan ? item.kitab_rujukan.split('(')[0].trim() : (item.tema_kajian ? item.tema_kajian.substring(0, 24) : 'Belum diatur');
                }
            }
        }

        function renderKajianRekapTable() {
            const container = document.getElementById('kajianRekapCardsContainer');
            const tbody = document.getElementById('kajianRekapTableBody');

            const todayStr = getTodayDateStringKajian();
            const bulan = currentKajianBulanData.bulan_num || ((new Date()).getMonth() + 1);
            const tahun = currentKajianBulanData.tahun || (new Date()).getFullYear();
            const namaBulan = NAMA_BULAN_INDONESIA[bulan - 1] || '';

            if (document.getElementById('rekapBulanTitle')) {
                document.getElementById('rekapBulanTitle').textContent = `${namaBulan} ${tahun}`;
            }

            if (container) {
                let cardsHtml = '';
                currentKajianBulanData.jadwal_list.forEach((item, idx) => {
                    const pekanNum = idx + 1;
                    const isSelected = (pekanNum === currentKajianPekanActive);
                    const isToday = (item.tanggal && item.tanggal === todayStr);

                    let statusBadge = '<span class="badge badge-secondary" style="font-size: 9.5px; font-weight: 600;">Belum Diatur</span>';
                    if (isToday) {
                        statusBadge = '<span class="badge badge-danger font-weight-bold" style="font-size: 9.5px;"><i class="fas fa-broadcast-tower mr-1"></i> Hari Ini</span>';
                    } else if (item.tanggal) {
                        if (item.tanggal > todayStr) {
                            statusBadge = '<span class="badge badge-success font-weight-bold" style="font-size: 9.5px;"><i class="fas fa-calendar-check mr-1"></i> Mendatang</span>';
                        } else {
                            statusBadge = '<span class="badge badge-light border text-muted" style="font-size: 9.5px;">Selesai</span>';
                        }
                    }

                    const cardBg = isSelected 
                        ? 'background: #f0fdf4; border: 2px solid #10b981; box-shadow: 0 4px 16px rgba(16, 185, 129, 0.18);' 
                        : 'background: #ffffff; border: 1.5px solid #e2e8f0; box-shadow: 0 2px 6px rgba(0,0,0,0.03);';

                    const actionBtn = isSelected
                        ? '<div class="mt-2 text-center py-1 px-2 rounded font-weight-bold bg-success text-white shadow-sm" style="font-size: 11px;"><i class="fas fa-check-circle mr-1"></i> Sedang Diedit</div>'
                        : `<button type="button" class="btn btn-outline-secondary btn-sm btn-block font-weight-bold mt-2 py-1" onclick="switchKajianTab(${pekanNum}); event.stopPropagation();" style="font-size: 11px; border-radius: 6px;"><i class="fas fa-edit mr-1"></i> Edit Pekan ${pekanNum}</button>`;

                    cardsHtml += `
                        <div class="kajian-pekan-col" style="flex: 1 1 0; min-width: 185px; max-width: 100%;">
                            <div class="card h-100 p-2 p-md-3 kajian-rekap-card position-relative" style="${cardBg} border-radius: 12px; cursor: pointer;" onclick="switchKajianTab(${pekanNum})" title="Klik untuk mengedit data Pekan ${pekanNum}">
                                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                    <span class="font-weight-bold" style="font-size: 13px; color: ${isSelected ? '#065f46' : '#1e293b'};">
                                        <i class="fas fa-calendar-week mr-1 ${isSelected ? 'text-success' : 'text-primary'}"></i> Pekan ${pekanNum}
                                    </span>
                                    <div>${statusBadge}</div>
                                </div>
                                <div class="mb-2">
                                    <div class="font-weight-bold text-dark d-flex align-items-center" style="font-size: 11.5px;">
                                        <i class="fas fa-calendar-day mr-1 text-primary" style="font-size: 11px;"></i>
                                        <span>${item.tanggal ? formatTanggalIndoKajian(item.tanggal) : '<em class="text-muted font-weight-normal">Belum ada tgl</em>'}</span>
                                    </div>
                                    <div class="text-muted d-flex align-items-center mt-1" style="font-size: 10.5px;">
                                        <i class="fas fa-clock mr-1 text-warning" style="font-size: 10px;"></i>
                                        <span class="text-truncate">${item.waktu_pelaksanaan || "Ba'da Maghrib"}</span>
                                    </div>
                                </div>
                                <div class="p-2 rounded mb-2" style="background: ${isSelected ? '#e8f5e9' : '#f8fafc'}; border-left: 3px solid ${isSelected ? '#10b981' : '#cbd5e1'}; font-size: 11px;">
                                    <div class="font-weight-bold text-success text-truncate mb-1" title="${item.ustadz_nama || '-'}">
                                        <i class="fas fa-user-tie mr-1"></i> ${item.ustadz_nama || '<span class="text-muted font-italic">Ustadz belum diisi</span>'}
                                    </div>
                                    <div class="text-warning font-weight-bold text-truncate mb-1" style="font-size: 10px;" title="${item.kitab_rujukan || '-'}">
                                        <i class="fas fa-book-open mr-1"></i> ${item.kitab_rujukan || '-'}
                                    </div>
                                    <div class="text-secondary text-truncate" style="font-size: 10px;" title="${item.tema_kajian || ''}">
                                        <i class="fas fa-quote-left mr-1 opacity-50" style="font-size: 9px;"></i> ${item.tema_kajian || '<span class="text-muted font-italic">Tema belum diatur</span>'}
                                    </div>
                                </div>
                                <div class="mt-auto">
                                    ${actionBtn}
                                </div>
                            </div>
                        </div>
                    `;
                });
                container.innerHTML = cardsHtml;
            }

            if (tbody) {
                tbody.innerHTML = '';
            }
        }

        function updateKajianPreview() {
            const item = currentKajianBulanData.jadwal_list[currentKajianPekanActive - 1] || currentKajianBulanData.jadwal_list[0];
            if (!item) return;

            if (document.getElementById('prevKajianPekanBadge')) {
                document.getElementById('prevKajianPekanBadge').innerHTML = `<i class="fas fa-calendar-week mr-1"></i> PEKAN KE-${currentKajianPekanActive} (SABTU MALAM)`;
            }
            if (document.getElementById('prevKajianDateBadge')) {
                document.getElementById('prevKajianDateBadge').textContent = item.tanggal ? formatTanggalIndoKajian(item.tanggal) : 'Sabtu, -';
            }
            if (document.getElementById('prevKajianUstadz')) {
                document.getElementById('prevKajianUstadz').textContent = item.ustadz_nama || '-';
            }
            if (document.getElementById('prevKajianKitab')) {
                document.getElementById('prevKajianKitab').textContent = item.kitab_rujukan || '-';
            }
            if (document.getElementById('prevKajianTema')) {
                document.getElementById('prevKajianTema').textContent = item.tema_kajian || 'Tema Kajian Belum Diatur';
            }
            if (document.getElementById('prevKajianWaktu')) {
                document.getElementById('prevKajianWaktu').textContent = item.waktu_pelaksanaan || "Ba'da Maghrib - Isya";
            }
        }

        // --- Helper Input Jam 24H Kajian Malam Ahad ---
        function formatTime24H(input) {
            if (!input) return;
            let v = input.value.replace(/[^0-9:]/g, '');
            if (v.length === 2 && !v.includes(':') && !input._deleting) {
                v = v + ':';
            }
            input.value = v.slice(0, 5);
        }

        function validateTime24H(input) {
            if (!input) return;
            let val = input.value.trim();
            if (!val) { input.value = '18:25'; return; }
            if (/^\d{4}$/.test(val)) val = val.substring(0, 2) + ':' + val.substring(2);
            if (/^\d{1,2}$/.test(val)) val = val.padStart(2, '0') + ':00';
            const parts = val.split(':');
            let h = parseInt(parts[0], 10);
            let m = parseInt(parts[1], 10);
            if (isNaN(h) || h < 0) h = 18;
            if (h > 23) h = 23;
            if (isNaN(m) || m < 0) m = 0;
            if (m > 59) m = 59;
            input.value = String(h).padStart(2, '0') + ':' + String(m).padStart(2, '0');
        }

        function setKajianJam24H(val) {
            const el = document.getElementById('kajianJamMulai');
            if (el) {
                el.value = val;
                validateTime24H(el);
            }
        }

        async function simpanKajianSabtu() {
            const btnSimpan = document.getElementById('btnSimpanKajian');
            let originalBtnHtml = '';
            if (btnSimpan) {
                originalBtnHtml = btnSimpan.innerHTML;
                btnSimpan.disabled = true;
                btnSimpan.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Menyimpan Agenda...';
            }

            try {
                simpanInputFormKeMemory(currentKajianPekanActive);
                const elJam = document.getElementById('kajianJamMulai');
                if (elJam && typeof validateTime24H === 'function') validateTime24H(elJam);
                const enabled = document.getElementById('cfgKajianSabtuEnabled') ? document.getElementById('cfgKajianSabtuEnabled').checked : true;
                const jamMulai = elJam?.value || '18:25';
                const bulan = parseInt(document.getElementById('kajianBulanSelect')?.value || ((new Date()).getMonth() + 1));
                const tahun = parseInt(document.getElementById('kajianTahunSelect')?.value || (new Date()).getFullYear());
                const namaBulan = (typeof NAMA_BULAN_INDONESIA !== 'undefined' && NAMA_BULAN_INDONESIA[bulan - 1]) ? NAMA_BULAN_INDONESIA[bulan - 1] : 'Bulan Ini';

                // Sinkronkan tema_kajian jika kosong tapi kitab_rujukan ada
                currentKajianBulanData.jadwal_list.forEach(j => {
                    if (!j.tema_kajian && j.kitab_rujukan) {
                        j.tema_kajian = j.kitab_rujukan;
                    }
                });

                // Validasi minimal ada 1 ustadz & tema / kitab terisi
                const hasValidEntry = currentKajianBulanData.jadwal_list.some(j => j.ustadz_nama && (j.tema_kajian || j.kitab_rujukan));
                if (!hasValidEntry) {
                    alert('Harap lengkapi setidaknya satu jadwal kajian (Nama Ustadz Pemateri dan Tema / Kitab Rujukan)!');
                    return;
                }

                // Cari pekan terdekat atau hari ini untuk kompatibilitas backward
                const todayStr = getTodayDateStringKajian();
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

                const nearestItem = currentKajianBulanData.jadwal_list[nearestIndex] || currentKajianBulanData.jadwal_list[0];

                const rotasiPekanDetik = Math.max(3, parseInt(document.getElementById('cfgKajianRotasiPekanDetik')?.value) || 9);
                const lockMode = document.getElementById('cfgKajianLockMode')?.value || 'full';
                const lockDurationMinutes = Math.max(15, parseInt(document.getElementById('cfgKajianLockDurationMinutes')?.value) || 60);
                const interleaveInterval = Math.max(20, parseInt(document.getElementById('cfgKajianInterleaveInterval')?.value) || 60);

                const payloadData = {
                    bulan_num: bulan,
                    tahun: tahun,
                    bulan_label: `${namaBulan} ${tahun}`,
                    jadwal_list: currentKajianBulanData.jadwal_list,
                    rotasi_pekan_detik: rotasiPekanDetik,
                    lock_mode: lockMode,
                    lock_duration_minutes: lockDurationMinutes,
                    interleave_interval: interleaveInterval,
                    // Kompatibilitas dengan pembaca lama:
                    pekan_label: nearestItem.pekan_label || `Pekan ${nearestIndex + 1}`,
                    tanggal: nearestItem.tanggal || '',
                    ustadz_nama: nearestItem.ustadz_nama || '',
                    ustadz_gelar: nearestItem.ustadz_gelar || '',
                    kitab_rujukan: nearestItem.kitab_rujukan || '',
                    tema_kajian: nearestItem.tema_kajian || '',
                    waktu_pelaksanaan: nearestItem.waktu_pelaksanaan || "Ba'da Sholat Maghrib s/d Menjelang Isya"
                };

                // Simpan ke local cache
                localStorage.setItem('cached_kajian_sabtu', JSON.stringify(payloadData));
                localStorage.setItem('cached_kajian_sabtu_enabled', enabled);
                localStorage.setItem('cached_kajian_sabtu_start_time', jamMulai);
                localStorage.setItem('cached_kajian_rotasi_pekan_detik', rotasiPekanDetik);
                localStorage.setItem('cached_kajian_lock_mode', lockMode);
                localStorage.setItem('cached_kajian_lock_duration_minutes', lockDurationMinutes);
                localStorage.setItem('cached_kajian_interleave_interval', interleaveInterval);

                // Simpan ke app_settings Supabase secara multi-row aman & broadcast realtime
                let sbSuccess = false;
                try {
                    if (typeof SupabaseDB !== 'undefined' && typeof SupabaseDB.saveKajianData === 'function') {
                        const sbRes = await SupabaseDB.saveKajianData(payloadData, enabled, jamMulai);
                        sbSuccess = !!sbRes;
                    }
                } catch (fetchErr) {
                    console.warn('Gagal sync ke Supabase (offline/network):', fetchErr);
                }

                if (typeof renderKajianPekanTabsUI === 'function') renderKajianPekanTabsUI();
                if (typeof renderKajianRekapTable === 'function') renderKajianRekapTable();
                if (typeof updateKajianPreview === 'function') updateKajianPreview();

                // Otomatis sinkronkan jadwal pekan ini ke Pusat Agenda Rutin (Kotak 2: Kajian Malam Ahad)
                try {
                    if (typeof syncKajianPekanIniKeAgendaRutin === 'function') {
                        syncKajianPekanIniKeAgendaRutin(false, nearestItem);
                    }
                } catch (syncErr) {
                    console.warn('Otomatis sync ke agenda rutin:', syncErr);
                }

                if (sbSuccess) {
                    alert(`Alhamdulillah! Seluruh Agenda Pengajian Rutin Malam Ahad 1 Bulan Penuh (${namaBulan} ${tahun}) berhasil disimpan dan otomatis tersinkronisasi ke Pusat Agenda Rutin serta Layar Display TV.`);
                } else {
                    alert(`Alhamdulillah! Agenda Pengajian Rutin Malam Ahad (${namaBulan} ${tahun}) berhasil disimpan di cache lokal peramban, tersinkronisasi ke Pusat Agenda Rutin & Layar TV (Mode Offline).`);
                }
            } catch (err) {
                console.error('Error saat simpanKajianSabtu:', err);
                alert('Terjadi kendala saat menyimpan agenda: ' + err.message);
            } finally {
                if (btnSimpan && originalBtnHtml) {
                    btnSimpan.disabled = false;
                    btnSimpan.innerHTML = originalBtnHtml;
                }
            }
        }

        // Update Tampilan Badge Sumber Jadwal Sholat (Kemenag & Falakiyah NU)