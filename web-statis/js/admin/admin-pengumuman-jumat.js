/**
 * MODUL ADMIN: admin-pengumuman-jumat.js
 * Digital Signage Masjid Jami' Al-Jihad
 * Versi: aljihad-signage-v5.8.45
 */

        // VIEW: PENGUMUMAN SHOLAT JUM'AT & LAPORAN KAS (KHUSUS BENDAHARA & MAKLUMAT)
        // =========================================================================
        let pjData = {
            tanggalJudul: "02 Oktober 2026",
            tanggalKas: "02 Oktober 2026",
            titimangsa: "Simpangan, 02 Oktober 2026",
            saldoAwal: 19379023,
            pemasukan: [
                { ket: "Pendapatan Tromol Jumat ( 25 Sept 2026 )", nominal: 3500000 }
            ],
            pengeluaran: [
                { ket: "Biaya Majlis Ta'lim Ibu - Ibu Selama 4 Minggu", nominal: 800000 },
                { ket: "Biaya Alokasi Buat Operasional Ambulan", nominal: 200000 },
                { ket: "Biaya Operasional Masjid Periode Tanggal 02 sd 04 Okt 2026", nominal: 3137000 }
            ],
            petugas: {
                imamKhotib: "Ust. Faiq Rido",
                muadzin: "Ust. Rudy",
                bilal: "Ust. Mansur",
                maklumat: "Ust. Fatkhurokhman"
            },
            himbauan: [
                "Diharapkan Jamaah yang masih berada diluar dipersilakan masuk untuk mengisi shaf yang masih kosong",
                "Bagi Jamaah yang membawa kendaraan diharapkan diparkir dengan rapi dan jangan lupa mengunci ganda kendaraanya",
                "Bagi Jamaah yang membawa HP Mohon di Non Aktifkan / di Silent sementara.",
                "Kepada para Jamaah semuanya, apabila khatib sudah naik mimbar dimohon untuk tidak berbicara atau mengobrol sementara waktu"
            ],
            himbauanText: "Diharapkan Jamaah yang masih berada diluar dipersilakan masuk untuk mengisi shaf yang masih kosong\nBagi Jamaah yang membawa kendaraan diharapkan diparkir dengan rapi dan jangan lupa mengunci ganda kendaraanya\nBagi Jamaah yang membawa HP Mohon di Non Aktifkan / di Silent sementara.\nKepada para Jamaah semuanya, apabila khatib sudah naik mimbar dimohon untuk tidak berbicara atau mengobrol sementara waktu",
            ttd: {
                namaKetua: "Hadi Prayitno",
                tampilkanKetua: true,
                namaBendahara: "Utut Priyastya",
                tampilkanBendahara: true,
                tempat: "Simpangan"
            }
        };

        // Helper Format Angka Rupiah
        function pjFormatRupiah(angka) {
            return (angka || 0).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        // Helper Parse Tanggal Indonesia / ISO ke Date Object
        function pjParseIndoDate(str) {
            if (!str) return new Date();
            const isoMatch = str.match(/^(\d{4})-(\d{1,2})-(\d{1,2})/);
            if (isoMatch) {
                return new Date(parseInt(isoMatch[1]), parseInt(isoMatch[2]) - 1, parseInt(isoMatch[3]), 12, 0, 0);
            }
            const months = ['januari','februari','maret','april','mei','juni','juli','agustus','september','oktober','november','desember'];
            const parts = str.toLowerCase().replace(/[^a-z0-9\s]/g, ' ').trim().split(/\s+/);
            let day = 1, month = 0, year = 2026;
            for (let p of parts) {
                if (/^\d{4}$/.test(p)) year = parseInt(p);
                else if (/^\d{1,2}$/.test(p) && day === 1) day = parseInt(p);
                else {
                    const mIdx = months.findIndex(m => m.startsWith(p.substring(0, 3)));
                    if (mIdx !== -1) month = mIdx;
                }
            }
            return new Date(year, month, day, 12, 0, 0);
        }

        // Helper Format Date Object ke Tanggal Indonesia
        function pjFormatIndoDate(d, short = false) {
            const months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
            const shortMonths = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sept','Okt','Nov','Des'];
            const dd = String(d.getDate()).padStart(2, '0');
            const mm = short ? shortMonths[d.getMonth()] : months[d.getMonth()];
            const yyyy = d.getFullYear();
            return `${dd} ${mm} ${yyyy}`;
        }

        // Helper Hitung Tanggal Jum'at 1 Minggu yang Lalu (Poin 5)
        function pjHitungTanggalPekanLalu(tglStr, short = false) {
            const d = pjParseIndoDate(tglStr);
            d.setDate(d.getDate() - 7);
            return pjFormatIndoDate(d, short);
        }

        // Handler saat Tanggal Hari H Berubah (Poin 2, 3, 5, 11)
        function pjOnTanggalJudulChange(val) {
            if (!val) return;
            const elTglKas = document.getElementById('pjInputTanggalKas');
            const elTitimangsa = document.getElementById('pjInputTitimangsa');
            if (elTglKas) elTglKas.value = val;
            if (elTitimangsa) elTitimangsa.value = 'Simpangan, ' + val;

            // Otomatis sesuaikan tanggal 1 pekan lalu pada baris pemasukan tromol jika ada (Poin 5)
            const tglPekanLalu = pjHitungTanggalPekanLalu(val, true);
            if (pjData.pemasukan.length > 0 && pjData.pemasukan[0].ket.toLowerCase().includes('tromol')) {
                pjData.pemasukan[0].ket = `Pendapatan Tromol Jumat ( ${tglPekanLalu} )`;
                pjRenderFormPemasukan();
            }
            pjHitungKas();
        }

        // Buka Native Date Picker untuk Tanggal Kas
        function pjBukaDatePickerKas() {
            const picker = document.getElementById('pjPickerTanggalKas');
            const input = document.getElementById('pjInputTanggalKas');
            if (!picker) return;
            if (input && input.value) {
                try {
                    const d = pjParseIndoDate(input.value);
                    const yyyy = d.getFullYear();
                    const mm = String(d.getMonth() + 1).padStart(2, '0');
                    const dd = String(d.getDate()).padStart(2, '0');
                    picker.value = `${yyyy}-${mm}-${dd}`;
                } catch(e) {}
            }
            try {
                if (picker.showPicker) {
                    picker.showPicker();
                } else {
                    picker.click();
                }
            } catch(e) {
                picker.click();
            }
        }

        // Handler saat User Memilih Tanggal dari Date Picker Kas
        function pjOnDatePickerKasChange(isoVal) {
            if (!isoVal) return;
            const parts = isoVal.split('-');
            if (parts.length === 3) {
                const d = new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, parseInt(parts[2]), 12, 0, 0);
                const formatted = pjFormatIndoDate(d, false);
                const el = document.getElementById('pjInputTanggalKas');
                if (el) {
                    el.value = formatted;
                    pjUpdatePreview();
                }
            }
        }

        // Auto Bullets dihilangkan sesuai permintaan pengguna karena sudah menggunakan sistem Auto-Numerik (1, 2, 3...)
        function setupAutoBulletsHimbauan() {
            // No-op (dinonaktifkan agar tidak ada bullet ganda)
        }

        // Inisialisasi Modul Pengumuman Jum'at
        async function initPengumumanJumat() {
            try {
                if (window.SupabaseDB) {
                    const settings = await SupabaseDB.getSettings();
                    if (settings && settings.pengumuman_jumat) {
                        const parsedCloud = typeof settings.pengumuman_jumat === 'string' ? JSON.parse(settings.pengumuman_jumat) : settings.pengumuman_jumat;
                        pjData = Object.assign({}, pjData, parsedCloud);
                        localStorage.setItem('pengumuman_jumat_data', JSON.stringify(pjData));
                    }
                }
                const saved = localStorage.getItem('pengumuman_jumat_data');
                if (saved) {
                    const parsed = JSON.parse(saved);
                    pjData = Object.assign({}, pjData, parsed);
                }
            } catch(e) {
                console.warn('Gagal memuat saved pengumuman_jumat_data:', e);
            }

            // Isi nilai form input
            const elTglJudul = document.getElementById('pjInputTanggalJudul');
            const elTglKas = document.getElementById('pjInputTanggalKas');
            const elTitimangsa = document.getElementById('pjInputTitimangsa');
            const elSaldoAwal = document.getElementById('pjInputSaldoAwal');

            const elImam = document.getElementById('pjInputImamKhotib');
            const elMuadzin = document.getElementById('pjInputMuadzin');
            const elBilal = document.getElementById('pjInputBilal');
            const elMaklumat = document.getElementById('pjInputMaklumat');

            const elHim = document.getElementById('pjInputHimbauan');
            const elKetua = document.getElementById('pjInputNamaKetua');
            const elCheckKetua = document.getElementById('pjCheckStempelKetua');
            const elBendahara = document.getElementById('pjInputNamaBendahara');
            const elCheckBendahara = document.getElementById('pjCheckTtdBendahara');

            if (elTglJudul) elTglJudul.value = pjData.tanggalJudul || '02 Oktober 2026';
            if (elTglKas) elTglKas.value = pjData.tanggalKas || '02 Oktober 2026';
            if (elTitimangsa) elTitimangsa.value = pjData.titimangsa || 'Simpangan, 02 Oktober 2026';
            if (elSaldoAwal) {
                const sVal = (pjData.saldoAwal !== undefined && pjData.saldoAwal !== null) ? pjData.saldoAwal : 19379023;
                const cleanSVal = parseInt(sVal.toString().replace(/[^0-9]/g, ''), 10) || 0;
                elSaldoAwal.value = cleanSVal ? pjFormatRupiah(cleanSVal) : '0';
            }

            if (elImam) elImam.value = pjData.petugas.imamKhotib || 'Ust. Faiq Rido';
            if (elMuadzin) elMuadzin.value = pjData.petugas.muadzin || 'Ust. Rudy';
            if (elBilal) elBilal.value = pjData.petugas.bilal || 'Ust. Mansur';
            if (elMaklumat) elMaklumat.value = pjData.petugas.maklumat || 'Ust. Fatkhurokhman';

            if (elHim) {
                let rawText = pjData.himbauanText || (Array.isArray(pjData.himbauan) ? pjData.himbauan.join('\n') : '');
                // Bersihkan bullet sisa jika tersimpan sebelumnya
                rawText = rawText.split('\n').map(l => l.replace(/^([•\-\*]|\d+[\.\)]|[a-zA-Z][\.\)])\s*/g, '').trim()).filter(l => l.length > 0).join('\n');
                elHim.value = rawText;
            }

            if (elKetua) elKetua.value = pjData.ttd.namaKetua || 'Hadi Prayitno';
            if (elCheckKetua) elCheckKetua.checked = (pjData.ttd.tampilkanKetua !== false);
            if (elBendahara) elBendahara.value = pjData.ttd.namaBendahara || 'Utut Priyastya';
            if (elCheckBendahara) elCheckBendahara.checked = (pjData.ttd.tampilkanBendahara !== false);

            pjRenderFormPemasukan();
            pjRenderFormPengeluaran();
            pjHitungKas();

            // Sinkronkan status switch Slide Layar Penuh TV Maklumat & Kas Jumat
            try {
                if (!cachedRotationPages || cachedRotationPages.length === 0) {
                    cachedRotationPages = normalizeRotationPages(localStorage.getItem('cached_rotation_pages'));
                }
                const kjPage = cachedRotationPages.find(p => (p.url || p.path || '').includes('kas-jumat'));
                const isKjActive = kjPage ? (kjPage.active !== false) : true;
                const swEl = document.getElementById('switchSlideKasJumat');
                const lblEl = document.getElementById('lblSwitchKasJumat');
                if (swEl) swEl.checked = isKjActive;
                if (lblEl) {
                    lblEl.textContent = isKjActive ? 'Tayang di TV Aktif' : 'Tayang di TV Nonaktif';
                    lblEl.className = isKjActive ? 'custom-control-label font-weight-bold text-success' : 'custom-control-label font-weight-bold text-secondary';
                }
            } catch(e) {}
        }

        // Render List Input Pemasukan di Form
        function pjRenderFormPemasukan() {
            const contIn = document.getElementById('pjContainerFormPemasukan');
            if (!contIn) return;
            contIn.innerHTML = pjData.pemasukan.map((item, idx) => `
                <div class="d-flex align-items-center mb-2" style="gap: 8px;">
                    <span class="badge badge-light border font-weight-bold" style="min-width: 26px;">${idx + 1}</span>
                    <input type="text" class="form-control form-control-sm" value="${escapeHtml(item.ket)}" placeholder="Keterangan Pemasukan" oninput="pjUpdatePemasukanKet(${idx}, this.value)" style="flex: 2;">
                    <div class="input-group input-group-sm" style="flex: 1.4; min-width: 130px;">
                        <div class="input-group-prepend"><span class="input-group-text font-weight-bold">Rp</span></div>
                        <input type="text" class="form-control font-weight-bold" value="${item.nominal ? pjFormatRupiah(item.nominal) : '0'}" placeholder="0" oninput="pjOnPemasukanNominalInput(${idx}, this)" autocomplete="off">
                    </div>
                    <button type="button" class="btn btn-xs btn-outline-danger" onclick="pjHapusBarisPemasukan(${idx})" title="Hapus baris" ${pjData.pemasukan.length <= 1 ? 'disabled' : ''}>
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            `).join('');
        }

        // Render List Input Pengeluaran di Form (Poin 7 - Add Button)
        function pjRenderFormPengeluaran() {
            const contOut = document.getElementById('pjContainerFormPengeluaran');
            if (!contOut) return;
            contOut.innerHTML = pjData.pengeluaran.map((item, idx) => `
                <div class="d-flex align-items-center mb-2" style="gap: 8px;">
                    <span class="badge badge-light border font-weight-bold" style="min-width: 26px;">${idx + 1}</span>
                    <input type="text" class="form-control form-control-sm" value="${escapeHtml(item.ket)}" placeholder="Keterangan Pengeluaran" oninput="pjUpdatePengeluaranKet(${idx}, this.value)" style="flex: 2;">
                    <div class="input-group input-group-sm" style="flex: 1.4; min-width: 130px;">
                        <div class="input-group-prepend"><span class="input-group-text font-weight-bold">Rp</span></div>
                        <input type="text" class="form-control font-weight-bold" value="${item.nominal ? pjFormatRupiah(item.nominal) : '0'}" placeholder="0" oninput="pjOnPengeluaranNominalInput(${idx}, this)" autocomplete="off">
                    </div>
                    <button type="button" class="btn btn-xs btn-outline-danger" onclick="pjHapusBarisPengeluaran(${idx})" title="Hapus baris" ${pjData.pengeluaran.length <= 1 ? 'disabled' : ''}>
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            `).join('');
        }

        function pjUpdatePemasukanKet(idx, val) {
            if (pjData.pemasukan[idx]) {
                pjData.pemasukan[idx].ket = val;
                pjUpdatePreview();
                renderDashPengumumanJumatWidget();
            }
        }

        function pjOnPemasukanNominalInput(idx, el) {
            if (!el) return;
            formatRupiahInput(el);
            const valNum = parseInt((el.value || '').replace(/[^0-9]/g, ''), 10) || 0;
            if (pjData.pemasukan[idx]) {
                pjData.pemasukan[idx].nominal = valNum;
                pjHitungKas();
            }
        }

        function pjUpdatePemasukanNominal(idx, val) {
            if (pjData.pemasukan[idx]) {
                pjData.pemasukan[idx].nominal = parseInt((val || '').toString().replace(/[^0-9]/g, ''), 10) || 0;
                pjHitungKas();
            }
        }

        function pjTambahBarisPemasukan(ket = '', nominal = 0) {
            pjData.pemasukan.push({ ket: ket || 'Pemasukan Infaq / Donasi Lainnya', nominal: nominal || 0 });
            pjRenderFormPemasukan();
            pjHitungKas();
        }

        function pjHapusBarisPemasukan(idx) {
            if (pjData.pemasukan.length <= 1) {
                alert('Minimal harus ada 1 baris pemasukan!');
                return;
            }
            pjData.pemasukan.splice(idx, 1);
            pjRenderFormPemasukan();
            pjHitungKas();
        }

        function pjUpdatePengeluaranKet(idx, val) {
            if (pjData.pengeluaran[idx]) {
                pjData.pengeluaran[idx].ket = val;
                pjUpdatePreview();
            }
        }

        function pjOnPengeluaranNominalInput(idx, el) {
            if (!el) return;
            formatRupiahInput(el);
            const valNum = parseInt((el.value || '').replace(/[^0-9]/g, ''), 10) || 0;
            if (pjData.pengeluaran[idx]) {
                pjData.pengeluaran[idx].nominal = valNum;
                pjHitungKas();
            }
        }

        function pjUpdatePengeluaranNominal(idx, val) {
            if (pjData.pengeluaran[idx]) {
                pjData.pengeluaran[idx].nominal = parseInt((val || '').toString().replace(/[^0-9]/g, ''), 10) || 0;
                pjHitungKas();
            }
        }

        // Poin 7: Tambah Add Button untuk Pengeluaran
        function pjTambahBarisPengeluaran(ket = '', nominal = 0) {
            pjData.pengeluaran.push({ ket: ket || 'Biaya Operasional Lainnya', nominal: nominal || 0 });
            pjRenderFormPengeluaran();
            pjHitungKas();
        }

        function pjHapusBarisPengeluaran(idx) {
            if (pjData.pengeluaran.length <= 1) {
                alert('Minimal harus ada 1 baris pengeluaran!');
                return;
            }
            pjData.pengeluaran.splice(idx, 1);
            pjRenderFormPengeluaran();
            pjHitungKas();
        }

        // Handler saat Saldo Awal diinput pengguna (dengan titik ribuan otomatis)
        function pjOnSaldoAwalInput(el) {
            if (!el) return;
            formatRupiahInput(el);
            pjHitungKas();
        }

        // Hitung Otomatis Kas Masjid (Poin 4, 5, 6, 7, 8)
        function pjHitungKas() {
            const elSaldoAwal = document.getElementById('pjInputSaldoAwal');
            const rawSaldoVal = elSaldoAwal ? (elSaldoAwal.value || '').toString().replace(/[^0-9]/g, '') : '';
            const saldoAwal = rawSaldoVal ? (parseInt(rawSaldoVal, 10) || 0) : (parseInt((pjData.saldoAwal || 0).toString().replace(/[^0-9]/g, ''), 10) || 0);
            pjData.saldoAwal = saldoAwal;

            let totalIn = 0;
            pjData.pemasukan.forEach(p => { 
                totalIn += (parseInt((p.nominal || 0).toString().replace(/[^0-9]/g, ''), 10) || 0); 
            });

            let totalOut = 0;
            pjData.pengeluaran.forEach(p => { 
                totalOut += (parseInt((p.nominal || 0).toString().replace(/[^0-9]/g, ''), 10) || 0); 
            });

            // POIN 6: SALDO = Saldo Awal + Total Pemasukan
            const subtotalSaldo = saldoAwal + totalIn;

            // POIN 8: SALDO AKHIR = (Saldo Awal + Pemasukan) - Total Pengeluaran
            const saldoAkhir = subtotalSaldo - totalOut;

            // Update form badges
            const bAwal = document.getElementById('badgeFormSaldoAwal');
            const bIn = document.getElementById('badgeFormTotalPemasukan');
            const bSubtotal = document.getElementById('badgeFormSaldoSubtotal');
            const bOut = document.getElementById('badgeFormTotalPengeluaran');
            const bAkhir = document.getElementById('badgeFormSaldoAkhir');

            if (bAwal) bAwal.textContent = 'Rp ' + pjFormatRupiah(saldoAwal);
            if (bIn) bIn.textContent = 'Rp ' + pjFormatRupiah(totalIn);
            if (bSubtotal) bSubtotal.textContent = 'Rp ' + pjFormatRupiah(subtotalSaldo);
            if (bOut) bOut.textContent = 'Rp ' + pjFormatRupiah(totalOut);
            if (bAkhir) bAkhir.textContent = 'Rp ' + pjFormatRupiah(saldoAkhir);

            // Update preview badges & cards
            const prevAwal = document.getElementById('pjPrevSaldoAwal');
            const prevIn = document.getElementById('pjPrevTotalPemasukan');
            const prevSubtotal = document.getElementById('pjPrevSubtotalSaldo');
            const prevOut = document.getElementById('pjPrevTotalPengeluaran');
            const prevAkhir = document.getElementById('pjPrevSaldoAkhir');

            if (prevAwal) prevAwal.textContent = pjFormatRupiah(saldoAwal);
            if (prevIn) prevIn.textContent = pjFormatRupiah(totalIn);
            if (prevSubtotal) prevSubtotal.textContent = pjFormatRupiah(subtotalSaldo);
            if (prevOut) prevOut.textContent = pjFormatRupiah(totalOut);
            if (prevAkhir) prevAkhir.textContent = pjFormatRupiah(saldoAkhir);

            pjUpdatePreview();
        }

        // Update Tampilan Lembar Pengumuman Resmi (Standar Dokumen PDF DKM)
        function pjUpdatePreview() {
            // Read form inputs
            const tglJudul = document.getElementById('pjInputTanggalJudul')?.value || pjData.tanggalJudul;
            const tglKas = document.getElementById('pjInputTanggalKas')?.value || pjData.tanggalKas;
            const titimangsa = document.getElementById('pjInputTitimangsa')?.value || pjData.titimangsa;

            const imam = document.getElementById('pjInputImamKhotib')?.value || pjData.petugas.imamKhotib;
            const muadzin = document.getElementById('pjInputMuadzin')?.value || pjData.petugas.muadzin;
            const bilal = document.getElementById('pjInputBilal')?.value || pjData.petugas.bilal;
            const maklumat = document.getElementById('pjInputMaklumat')?.value || pjData.petugas.maklumat;

            // Himbauan Textarea
            const himbauanRaw = document.getElementById('pjInputHimbauan')?.value ?? pjData.himbauanText ?? '';
            pjData.himbauanText = himbauanRaw;
            const lines = himbauanRaw.split('\n').map(l => l.trim()).filter(l => l.length > 0);
            pjData.himbauan = lines;

            const ketua = document.getElementById('pjInputNamaKetua')?.value || pjData.ttd.namaKetua;
            const checkKetua = document.getElementById('pjCheckStempelKetua')?.checked ?? pjData.ttd.tampilkanKetua;
            const bendahara = document.getElementById('pjInputNamaBendahara')?.value || pjData.ttd.namaBendahara;
            const checkBendahara = document.getElementById('pjCheckTtdBendahara')?.checked ?? pjData.ttd.tampilkanBendahara;

            // Sync state
            pjData.tanggalJudul = tglJudul;
            pjData.tanggalKas = tglKas;
            pjData.titimangsa = titimangsa;
            pjData.petugas = { imamKhotib: imam, muadzin: muadzin, bilal: bilal, maklumat: maklumat };
            pjData.ttd = { namaKetua: ketua, tampilkanKetua: checkKetua, namaBendahara: bendahara, tampilkanBendahara: checkBendahara };

            // Update DOM preview
            const elPrevTglJudul = document.getElementById('pjPrevTanggalJudul');
            const elPrevTglKasText = document.getElementById('pjPrevTanggalKasText');
            const elPrevTitimangsa = document.getElementById('pjPrevTitimangsa');

            if (elPrevTglJudul) elPrevTglJudul.textContent = (tglJudul || '02 OKTOBER 2026').toUpperCase();
            if (elPrevTglKasText) elPrevTglKasText.textContent = tglKas || tglJudul || '02 Oktober 2026';
            if (elPrevTitimangsa) elPrevTitimangsa.textContent = titimangsa || 'Simpangan, 02 Oktober 2026';

            // Calculate subtotals
            let totalIn = 0;
            pjData.pemasukan.forEach(p => { totalIn += (parseInt(p.nominal, 10) || 0); });
            let totalOut = 0;
            pjData.pengeluaran.forEach(p => { totalOut += (parseInt(p.nominal, 10) || 0); });

            const saldoAwal = parseInt(pjData.saldoAwal, 10) || 0;
            const subtotalSaldo = saldoAwal + totalIn;

            // Subtotals in tables
            const elSubIn = document.getElementById('pjPrevSubtotalIn');
            const elSubSaldo = document.getElementById('pjPrevSubtotalSaldo');
            const elSubOut = document.getElementById('pjPrevSubtotalOut');
            if (elSubIn) elSubIn.textContent = pjFormatRupiah(totalIn);
            if (elSubSaldo) elSubSaldo.textContent = pjFormatRupiah(subtotalSaldo);
            if (elSubOut) elSubOut.textContent = pjFormatRupiah(totalOut);

            // Preview List Pemasukan (Tabel Bersih Memanjang Penuh & Font Tajam)
            const contPrevIn = document.getElementById('pjPrevListPemasukan');
            if (contPrevIn) {
                if (pjData.pemasukan.length === 0) {
                    contPrevIn.innerHTML = '<div class="text-muted small p-2 text-center">- Belum ada data pemasukan -</div>';
                } else {
                    contPrevIn.innerHTML = pjData.pemasukan.map((item, idx) => `
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 7px 6px; border-bottom: 1px solid #f1f5f9; font-size: 14.5px;">
                            <span style="color: #1e293b; font-weight: 600;">${idx + 1}. ${escapeHtml(item.ket)}</span>
                            <span style="font-weight: 800; color: #047857; font-family: monospace; font-size: 15px; white-space: nowrap; margin-left: 14px;">Rp ${pjFormatRupiah(item.nominal)}</span>
                        </div>
                    `).join('');
                }
            }

            // Preview List Pengeluaran (Tabel Bersih Memanjang Penuh di Bawah Pemasukan & Font Tajam)
            const contPrevOut = document.getElementById('pjPrevListPengeluaran');
            if (contPrevOut) {
                if (pjData.pengeluaran.length === 0) {
                    contPrevOut.innerHTML = '<div class="text-muted small p-2 text-center">- Belum ada data pengeluaran -</div>';
                } else {
                    contPrevOut.innerHTML = pjData.pengeluaran.map((item, idx) => `
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 7px 6px; border-bottom: 1px solid #f1f5f9; font-size: 14.5px;">
                            <span style="color: #1e293b; font-weight: 600;">${idx + 1}. ${escapeHtml(item.ket)}</span>
                            <span style="font-weight: 800; color: #dc2626; font-family: monospace; font-size: 15px; white-space: nowrap; margin-left: 14px;">Rp ${pjFormatRupiah(item.nominal)}</span>
                        </div>
                    `).join('');
                }
            }

            // Preview Petugas (Poin 9)
            const prevImam = document.getElementById('pjPrevImamKhotib');
            const prevMuadzin = document.getElementById('pjPrevMuadzin');
            const prevBilal = document.getElementById('pjPrevBilal');
            const prevMaklumat = document.getElementById('pjPrevMaklumat');

            if (prevImam) prevImam.textContent = imam || 'Ust. Faiq Rido';
            if (prevMuadzin) prevMuadzin.textContent = muadzin || 'Ust. Rudy';
            if (prevBilal) prevBilal.textContent = bilal || 'Ust. Mansur';
            if (prevMaklumat) prevMaklumat.textContent = maklumat || 'Ust. Fatkhurokhman';

            // Preview Himbauan (Nomor urut otomatis 1, 2, 3, 4 tanpa bullet)
            const prevContHim = document.getElementById('pjPrevContainerHimbauan');
            if (prevContHim) {
                if (lines.length === 0) {
                    prevContHim.innerHTML = '<div class="text-muted font-italic small">- Tidak ada himbauan khusus -</div>';
                } else {
                    prevContHim.innerHTML = lines.map((line, idx) => {
                        let cleanText = line.replace(/^([•\-\*]|\d+[\.\)]|[a-zA-Z][\.\)])\s*/g, '').trim();
                        cleanText = cleanText.replace(/^[•\-\*]\s*/g, '').trim();
                        return `
                            <div style="margin-bottom: 8px; display: flex; align-items: flex-start; gap: 8px; font-size: 14.5px;">
                                <span style="font-weight: 800; min-width: 22px; color: #047857;">${idx + 1}.</span>
                                <span style="color: #1e293b; font-weight: 500;">${escapeHtml(cleanText)}</span>
                            </div>
                        `;
                    }).join('');
                }
            }

            // Preview TTD & Stempel Ketua
            const prevNamaKetua = document.getElementById('pjPrevNamaKetua');
            const prevImgKetua = document.getElementById('pjPrevImgKetua');
            const prevImgStempel = document.getElementById('pjPrevImgStempel');
            if (prevNamaKetua) prevNamaKetua.textContent = ketua || 'Hadi Prayitno';
            if (prevImgKetua) {
                prevImgKetua.style.display = checkKetua ? 'inline-block' : 'none';
            }
            if (prevImgStempel) {
                prevImgStempel.style.display = checkKetua ? 'block' : 'none';
            }

            // Preview TTD Bendahara
            const prevNamaBendahara = document.getElementById('pjPrevNamaBendahara');
            const prevImgBendahara = document.getElementById('pjPrevImgBendahara');
            if (prevNamaBendahara) prevNamaBendahara.textContent = bendahara || 'Utut Priyastya';
            if (prevImgBendahara) {
                prevImgBendahara.style.display = checkBendahara ? 'inline-block' : 'none';
            }
        }

        // Poin 2 & 9: Tarik Data dari Form Petugas Sholat Jum'at
        function pjTarikPetugasTerkini() {
            try {
                // 1. Tarik Tanggal Sholat Jum'at (Poin 2)
                const elTglJumat = document.getElementById('jumatTanggal');
                if (elTglJumat && elTglJumat.value) {
                    const indoDate = pjFormatIndoDate(pjParseIndoDate(elTglJumat.value));
                    const elTglJudul = document.getElementById('pjInputTanggalJudul');
                    if (elTglJudul) {
                        elTglJudul.value = indoDate;
                        pjOnTanggalJudulChange(indoDate);
                    }
                }

                // 2. Tarik Petugas Sholat Jum'at (Poin 9)
                const elKhotib = document.getElementById('jumatKhotib');
                const elMuadzin = document.getElementById('jumatMuadzin');
                const elBilal = document.getElementById('jumatBilal');
                const elMaklumat = document.getElementById('jumatMaklumat');

                let k = elKhotib ? elKhotib.value.trim() : '';
                let m = elMuadzin ? elMuadzin.value.trim() : '';
                let b = elBilal ? elBilal.value.trim() : '';
                let mk = elMaklumat ? elMaklumat.value.trim() : '';

                if (!b) b = localStorage.getItem('cached_jumat_bilal') || '';
                if (!mk) mk = localStorage.getItem('cached_jumat_maklumat') || '';

                if (k) document.getElementById('pjInputImamKhotib').value = k;
                if (m) document.getElementById('pjInputMuadzin').value = m;
                if (b) document.getElementById('pjInputBilal').value = b;
                if (mk) document.getElementById('pjInputMaklumat').value = mk;

                pjUpdatePreview();
                if (typeof showCustomToast === 'function') {
                    showCustomToast("Data Petugas & Tanggal Sholat Jum'at berhasil disinkronkan!", 'success');
                } else {
                    alert("Data Petugas & Tanggal Sholat Jum'at berhasil diambil dari jadwal!");
                }
            } catch(e) {
                console.error(e);
            }
        }

        function tarikKasDanPetugasOtomatis() {
            pjTarikPetugasTerkini();
            try {
                const saldoEl = document.getElementById('dashSaldoUtama');
                if (saldoEl) {
                    const txt = saldoEl.textContent.replace(/[^0-9]/g, '');
                    if (txt) {
                        const parsedSaldo = parseInt(txt, 10);
                        if (!isNaN(parsedSaldo) && parsedSaldo > 0) {
                            document.getElementById('pjInputSaldoAwal').value = pjFormatRupiah(parsedSaldo);
                            pjHitungKas();
                        }
                    }
                }
            } catch(e) {
                console.warn(e);
            }
        }

        async function simpanDataPengumumanJumat(tampilkanNotif = true) {
            try {
                localStorage.setItem('pengumuman_jumat_data', JSON.stringify(pjData));
                if (window.SupabaseDB) {
                    if (typeof SupabaseDB.savePengumumanJumat === 'function') {
                        await SupabaseDB.savePengumumanJumat(pjData);
                    } else {
                        await SupabaseDB.saveSettings({ pengumuman_jumat: pjData });
                    }
                }
                if (tampilkanNotif) {
                    if (typeof showCustomToast === 'function') {
                        showCustomToast("Lembar Pengumuman Sholat Jum'at & Kas berhasil disimpan & disinkronkan ke Layar TV!", 'success');
                    } else {
                        alert("Lembar Pengumuman Sholat Jum'at & Kas berhasil disimpan & disinkronkan ke Layar TV!");
                    }
                }
            } catch(e) {
                console.error(e);
                alert('Gagal menyimpan data: ' + e.message);
            }
        }

        // =========================================================================
        // FITUR 1: CETAK LEMBAR PENGUMUMAN A4 RESMI (PERSIS TEMPLATE DKM)
        // =========================================================================
        function cetakPengumumanJumat() {
            pjUpdatePreview();
            const printable = document.getElementById('pjPrintableSheet');
            if (!printable) {
                alert('Lembar pengumuman belum siap dicetak.');
                return;
            }

            const printWin = window.open('', '_blank', 'width=950,height=1100');
            if (!printWin) {
                alert('Pop-up terblokir! Izinkan pop-up peramban untuk mencetak lembar pengumuman.');
                return;
            }

            printWin.document.open();
            printWin.document.write(`
                <!DOCTYPE html>
                <html lang="id">
                <head>
                    <meta charset="UTF-8">
                    <title>Pengumuman Sholat Jum'at - ${escapeHtml(pjData.tanggalJudul || 'Al-Jihad')}</title>
                    <style>
                        @page {
                            size: A4 portrait;
                            margin: 10mm 14mm;
                        }
                        * {
                            box-sizing: border-box;
                            -webkit-print-color-adjust: exact !important;
                            print-color-adjust: exact !important;
                        }
                        body {
                            margin: 0;
                            padding: 0;
                            background: #ffffff;
                            font-family: 'Segoe UI', Arial, Helvetica, sans-serif;
                            font-size: 13px;
                            color: #000000;
                            line-height: 1.45;
                        }
                        #pjPrintableSheet {
                            width: 100% !important;
                            max-width: 100% !important;
                            min-width: 100% !important;
                            margin: 0 !important;
                            padding: 0 !important;
                            box-shadow: none !important;
                            border: none !important;
                        }
                    </style>
                </head>
                <body>
                    ${printable.outerHTML}
                    <script>
                        window.onload = function() {
                            setTimeout(function() {
                                window.focus();
                                window.print();
                                window.close();
                            }, 500);
                        };
                    <\/script>
                </body>
                </html>
            `);
            printWin.document.close();
        }

        // =========================================================================
        // FITUR 2: DOWNLOAD FLYER DIGITAL PENGUMUMAN JUMAT (CANVAS 2D 1200 x 1720 px)
        // =========================================================================
        function downloadFlyerPengumumanJumat() {
            pjUpdatePreview();
            let canvas = document.getElementById('canvasFlyerPengumumanJumat');
            if (!canvas) {
                canvas = document.createElement('canvas');
                canvas.id = 'canvasFlyerPengumumanJumat';
                canvas.style.display = 'none';
                document.body.appendChild(canvas);
            }

            const W = 1200;
            const H = 1720;
            canvas.width = W;
            canvas.height = H;
            const ctx = canvas.getContext('2d');

            // 1. Background Kertas Putih Bersih
            ctx.fillStyle = "#ffffff";
            ctx.fillRect(0, 0, W, H);

            // Muat Kop Surat Resmi (KOP SURAT ALJIHAD.png)
            const imgKop = new Image();
            imgKop.crossOrigin = "anonymous";
            imgKop.onload = function() {
                // Gambar Kop Surat (Lebar 1120px)
                const kopW = 1120;
                const kopH = kopW * (imgKop.naturalHeight / imgKop.naturalWidth);
                ctx.drawImage(imgKop, 40, 36, kopW, kopH);

                // Garis Hijau Emerald Pemisah Kop
                let curY = 36 + kopH + 10;
                ctx.strokeStyle = "#047857";
                ctx.lineWidth = 3.5;
                ctx.beginPath();
                ctx.moveTo(40, curY);
                ctx.lineTo(1160, curY);
                ctx.stroke();

                // 2. JUDUL PENGUMUMAN
                curY += 38;
                ctx.textAlign = "center";
                ctx.fillStyle = "#000000";
                ctx.font = "bold 23px 'Segoe UI', Arial, sans-serif";
                ctx.fillText("PENGUMUMAN KEGIATAN SHOLAT JUM’AT MASJID JAMI’ AL JIHAD", W / 2, curY);

                curY += 28;
                ctx.font = "bold 21px 'Segoe UI', Arial, sans-serif";
                ctx.fillText("TANGGAL : " + (pjData.tanggalJudul || '02 OKTOBER 2026').toUpperCase(), W / 2, curY);

                // 3. BAGIAN I: LAPORAN KEUANGAN KAS MASJID
                curY += 46;
                ctx.textAlign = "left";
                ctx.fillStyle = "#047857";
                ctx.font = "bold 20px 'Segoe UI', Arial, sans-serif";
                ctx.fillText("I. LAPORAN KEUANGAN KAS MASJID", 40, curY);

                ctx.textAlign = "right";
                ctx.fillStyle = "#475569";
                ctx.font = "bold 17px 'Segoe UI', Arial, sans-serif";
                ctx.fillText("s/d Tanggal : " + (pjData.tanggalKas || pjData.tanggalJudul || '02 Oktober 2026'), 1160, curY);

                // Hitung Keuangan
                let totalIn = 0;
                pjData.pemasukan.forEach(p => { totalIn += (parseInt(p.nominal, 10) || 0); });
                let totalOut = 0;
                pjData.pengeluaran.forEach(p => { totalOut += (parseInt(p.nominal, 10) || 0); });
                const saldoAwal = parseInt(pjData.saldoAwal, 10) || 0;
                const subtotalSaldo = saldoAwal + totalIn;
                const saldoAkhir = subtotalSaldo - totalOut;

                // 4 Kartu Kas Sejajar
                curY += 16;
                const cardGap = 16;
                const cardW = (1120 - (cardGap * 3)) / 4;
                const cardH = 84;
                const cardY = curY;

                // Card 1: Saldo Awal
                ctx.fillStyle = "#ffffff";
                ctx.strokeStyle = "#e2e8f0";
                ctx.lineWidth = 1.5;
                ctx.fillRect(40, cardY, cardW, cardH);
                ctx.strokeRect(40, cardY, cardW, cardH);
                ctx.fillStyle = "#f59e0b";
                ctx.fillRect(40, cardY, 6, cardH);
                ctx.textAlign = "center";
                ctx.fillStyle = "#92400e";
                ctx.font = "bold 15px 'Segoe UI', Arial, sans-serif";
                ctx.fillText("SALDO AWAL", 40 + (cardW / 2), cardY + 30);
                ctx.font = "bold 23px 'Segoe UI', Arial, sans-serif";
                ctx.fillText("Rp " + pjFormatRupiah(saldoAwal), 40 + (cardW / 2), cardY + 62);

                // Card 2: Pemasukan (+)
                const c2X = 40 + cardW + cardGap;
                ctx.fillStyle = "#ffffff";
                ctx.fillRect(c2X, cardY, cardW, cardH);
                ctx.strokeRect(c2X, cardY, cardW, cardH);
                ctx.fillStyle = "#10b981";
                ctx.fillRect(c2X, cardY, 6, cardH);
                ctx.textAlign = "center";
                ctx.fillStyle = "#047857";
                ctx.font = "bold 15px 'Segoe UI', Arial, sans-serif";
                ctx.fillText("PEMASUKAN (+)", c2X + (cardW / 2), cardY + 30);
                ctx.font = "bold 23px 'Segoe UI', Arial, sans-serif";
                ctx.fillText("Rp " + pjFormatRupiah(totalIn), c2X + (cardW / 2), cardY + 62);

                // Card 3: Pengeluaran (-)
                const c3X = c2X + cardW + cardGap;
                ctx.fillStyle = "#ffffff";
                ctx.fillRect(c3X, cardY, cardW, cardH);
                ctx.strokeRect(c3X, cardY, cardW, cardH);
                ctx.fillStyle = "#ef4444";
                ctx.fillRect(c3X, cardY, 6, cardH);
                ctx.textAlign = "center";
                ctx.fillStyle = "#dc2626";
                ctx.font = "bold 15px 'Segoe UI', Arial, sans-serif";
                ctx.fillText("PENGELUARAN (-)", c3X + (cardW / 2), cardY + 30);
                ctx.font = "bold 23px 'Segoe UI', Arial, sans-serif";
                ctx.fillText("Rp " + pjFormatRupiah(totalOut), c3X + (cardW / 2), cardY + 62);

                // Card 4: Saldo Akhir Kas
                const c4X = c3X + cardW + cardGap;
                ctx.fillStyle = "#047857";
                ctx.fillRect(c4X, cardY, cardW, cardH);
                ctx.textAlign = "center";
                ctx.fillStyle = "#ffffff";
                ctx.font = "bold 15px 'Segoe UI', Arial, sans-serif";
                ctx.fillText("SALDO AKHIR KAS", c4X + (cardW / 2), cardY + 30);
                ctx.fillStyle = "#facc15";
                ctx.font = "bold 24px 'Segoe UI', Arial, sans-serif";
                ctx.fillText("Rp " + pjFormatRupiah(saldoAkhir), c4X + (cardW / 2), cardY + 62);

                // =====================================================================
                // 4. KOTAK PEMASUKAN KAS (MEMANJANG PENUH DARI KIRI KE KANAN 1120px)
                // =====================================================================
                curY = cardY + cardH + 20;
                const fullColW = 1120;
                const rowItemH = 34;
                const headerH = 38;
                const subRowH = 36;
                const saldoRowH = 38;
                
                // Tinggi total kotak Pemasukan
                const inTableH = headerH + (pjData.pemasukan.length * rowItemH) + subRowH + saldoRowH;

                ctx.strokeStyle = "#cbd5e1";
                ctx.lineWidth = 1.5;
                ctx.strokeRect(40, curY, fullColW, inTableH);

                // Header Pemasukan Kas
                ctx.fillStyle = "#0d9488";
                ctx.fillRect(40, curY, fullColW, headerH);
                ctx.fillStyle = "#ffffff";
                ctx.font = "bold 19.5px 'Segoe UI', Arial, sans-serif";
                ctx.textAlign = "left";
                ctx.fillText("Pemasukan Kas", 56, curY + 26);
                ctx.textAlign = "right";
                ctx.fillText("Nominal (Rp)", 40 + fullColW - 16, curY + 26);

                let inRowY = curY + headerH + 24;
                pjData.pemasukan.forEach((p, idx) => {
                    ctx.textAlign = "left";
                    ctx.fillStyle = "#1e293b";
                    ctx.font = "600 17.5px 'Segoe UI', Arial, sans-serif";
                    ctx.fillText((idx + 1) + ". " + p.ket, 56, inRowY);

                    ctx.textAlign = "right";
                    ctx.fillStyle = "#047857";
                    ctx.font = "bold 18.5px monospace";
                    ctx.fillText("Rp " + pjFormatRupiah(p.nominal), 40 + fullColW - 16, inRowY);
                    
                    if (idx < pjData.pemasukan.length - 1) {
                        ctx.strokeStyle = "#f1f5f9";
                        ctx.lineWidth = 1;
                        ctx.beginPath();
                        ctx.moveTo(40, inRowY + 10);
                        ctx.lineTo(40 + fullColW, inRowY + 10);
                        ctx.stroke();
                    }
                    inRowY += rowItemH;
                });

                // Baris Total Pemasukan
                const subInY = curY + headerH + (pjData.pemasukan.length * rowItemH);
                ctx.fillStyle = "#ecfdf5";
                ctx.fillRect(40, subInY, fullColW, subRowH);
                ctx.fillStyle = "#047857";
                ctx.font = "bold 18.5px 'Segoe UI', Arial, sans-serif";
                ctx.textAlign = "left";
                ctx.fillText("Total Pemasukan", 56, subInY + 24);
                ctx.textAlign = "right";
                ctx.font = "bold 19.5px monospace";
                ctx.fillText("Rp " + pjFormatRupiah(totalIn), 40 + fullColW - 16, subInY + 24);

                // Baris SALDO (Awal + Masuk)
                const saldoInY = subInY + subRowH;
                ctx.fillStyle = "#d1fae5";
                ctx.fillRect(40, saldoInY, fullColW, saldoRowH);
                ctx.fillStyle = "#047857";
                ctx.font = "bold 18.5px 'Segoe UI', Arial, sans-serif";
                ctx.textAlign = "left";
                ctx.fillText("SALDO (Awal + Masuk)", 56, saldoInY + 25);
                ctx.textAlign = "right";
                ctx.font = "bold 20px monospace";
                ctx.fillText("Rp " + pjFormatRupiah(subtotalSaldo), 40 + fullColW - 16, saldoInY + 25);

                // =====================================================================
                // 5. KOTAK PENGELUARAN / BIAYA (TEPAT DI BAWAHNYA, MEMANJANG PENUH 1120px)
                // =====================================================================
                curY += inTableH + 16;

                const outTableH = headerH + (pjData.pengeluaran.length * rowItemH) + saldoRowH;

                ctx.strokeStyle = "#cbd5e1";
                ctx.lineWidth = 1.5;
                ctx.strokeRect(40, curY, fullColW, outTableH);

                // Header Pengeluaran / Biaya
                ctx.fillStyle = "#334155";
                ctx.fillRect(40, curY, fullColW, headerH);
                ctx.fillStyle = "#ffffff";
                ctx.font = "bold 19.5px 'Segoe UI', Arial, sans-serif";
                ctx.textAlign = "left";
                ctx.fillText("Pengeluaran / Biaya", 56, curY + 26);
                ctx.textAlign = "right";
                ctx.fillText("Nominal (Rp)", 40 + fullColW - 16, curY + 26);

                let outRowY = curY + headerH + 24;
                pjData.pengeluaran.forEach((p, idx) => {
                    ctx.textAlign = "left";
                    ctx.fillStyle = "#1e293b";
                    ctx.font = "600 17.5px 'Segoe UI', Arial, sans-serif";
                    // Teks utuh tanpa pemotongan / ellipsis karena lebar 1120px sangat luas
                    ctx.fillText((idx + 1) + ". " + p.ket, 56, outRowY);

                    ctx.textAlign = "right";
                    ctx.fillStyle = "#dc2626";
                    ctx.font = "bold 18.5px monospace";
                    ctx.fillText("Rp " + pjFormatRupiah(p.nominal), 40 + fullColW - 16, outRowY);

                    if (idx < pjData.pengeluaran.length - 1) {
                        ctx.strokeStyle = "#f1f5f9";
                        ctx.lineWidth = 1;
                        ctx.beginPath();
                        ctx.moveTo(40, outRowY + 10);
                        ctx.lineTo(40 + fullColW, outRowY + 10);
                        ctx.stroke();
                    }
                    outRowY += rowItemH;
                });

                // Baris Total Pengeluaran
                const subOutY = curY + headerH + (pjData.pengeluaran.length * rowItemH);
                ctx.fillStyle = "#fef2f2";
                ctx.fillRect(40, subOutY, fullColW, saldoRowH);
                ctx.fillStyle = "#dc2626";
                ctx.font = "bold 18.5px 'Segoe UI', Arial, sans-serif";
                ctx.textAlign = "left";
                ctx.fillText("Total Pengeluaran", 56, subOutY + 25);
                ctx.textAlign = "right";
                ctx.font = "bold 20px monospace";
                ctx.fillText("Rp " + pjFormatRupiah(totalOut), 40 + fullColW - 16, subOutY + 25);

                // =====================================================================
                // 6. BAGIAN II: PETUGAS SHOLAT JUM'AT HARI INI
                // =====================================================================
                curY += outTableH + 26;
                ctx.textAlign = "left";
                ctx.fillStyle = "#047857";
                ctx.font = "bold 21px 'Segoe UI', Arial, sans-serif";
                ctx.fillText("II. PETUGAS SHOLAT JUM'AT HARI INI", 40, curY);

                curY += 14;
                const petCards = [
                    { label: "IMAM & KHOTIB", val: pjData.petugas.imamKhotib || 'Ust. Faiq Rido', bar: "#10b981", textCol: "#059669" },
                    { label: "MUADZIN", val: pjData.petugas.muadzin || 'Ust. Rudy', bar: "#0284c7", textCol: "#0284c7" },
                    { label: "BILAL", val: pjData.petugas.bilal || 'Ust. Mansur', bar: "#f59e0b", textCol: "#d97706" },
                    { label: "MC MAKLUMAT", val: pjData.petugas.maklumat || 'Ust. Fatkhurokhman', bar: "#8b5cf6", textCol: "#7c3aed" }
                ];

                const petCardH = 76;
                petCards.forEach((c, idx) => {
                    const px = 40 + idx * (cardW + cardGap);
                    ctx.fillStyle = "#f8fafc";
                    ctx.strokeStyle = "#e2e8f0";
                    ctx.fillRect(px, curY, cardW, petCardH);
                    ctx.strokeRect(px, curY, cardW, petCardH);
                    ctx.fillStyle = c.bar;
                    ctx.fillRect(px, curY, cardW, 4);

                    ctx.textAlign = "left";
                    ctx.fillStyle = c.textCol;
                    ctx.font = "bold 15px 'Segoe UI', Arial, sans-serif";
                    ctx.fillText(c.label, px + 14, curY + 28);

                    ctx.fillStyle = "#0f172a";
                    ctx.font = "bold 19px 'Segoe UI', Arial, sans-serif";
                    ctx.fillText(c.val, px + 14, curY + 58);
                });

                // =====================================================================
                // 7. BAGIAN III: HIMBAUAN (FONT DIPERBESAR & HANYA NOMOR URUT)
                // =====================================================================
                curY += petCardH + 26;
                ctx.textAlign = "left";
                ctx.fillStyle = "#047857";
                ctx.font = "bold 21px 'Segoe UI', Arial, sans-serif";
                ctx.fillText("III. HIMBAUAN", 40, curY);

                curY += 14;
                const hLines = (pjData.himbauan && pjData.himbauan.length > 0) ? pjData.himbauan : [
                    "Diharapkan Jamaah yang masih berada diluar dipersilakan masuk untuk mengisi shaf yang masih kosong",
                    "Bagi Jamaah yang membawa kendaraan diharapkan diparkir dengan rapi dan jangan lupa mengunci ganda kendaraanya",
                    "Bagi Jamaah yang membawa HP Mohon di Non Aktifkan / di Silent sementara.",
                    "Kepada para Jamaah semuanya, apabila khatib sudah naik mimbar dimohon untuk tidak berbicara atau mengobrol sementara waktu"
                ];

                const himLineH = 36;
                const himBoxH = 22 + (hLines.length * himLineH);
                ctx.fillStyle = "#fafafa";
                ctx.strokeStyle = "#e2e8f0";
                ctx.fillRect(40, curY, 1120, himBoxH);
                ctx.strokeRect(40, curY, 1120, himBoxH);

                let himY = curY + 30;
                hLines.forEach((line, idx) => {
                    let clean = line.replace(/^([•\-\*]|\d+[\.\)]|[a-zA-Z][\.\)])\s*/g, '').trim();
                    clean = clean.replace(/^[•\-\*]\s*/g, '').trim();

                    ctx.fillStyle = "#047857";
                    ctx.font = "bold 19px 'Segoe UI', Arial, sans-serif";
                    ctx.fillText((idx + 1) + ".", 58, himY);

                    ctx.fillStyle = "#1e293b";
                    ctx.font = "500 18.5px 'Segoe UI', Arial, sans-serif";
                    ctx.fillText(clean, 88, himY);
                    himY += himLineH;
                });

                // =====================================================================
                // 8. PENGESAHAN DOKUMEN & TITIMANGSA
                // =====================================================================
                curY += himBoxH + 34;
                ctx.textAlign = "right";
                ctx.fillStyle = "#0f172a";
                ctx.font = "bold 17px 'Segoe UI', Arial, sans-serif";
                ctx.fillText("Dibuat di : " + (pjData.titimangsa || 'Simpangan, 02 Oktober 2026'), 1160, curY);

                curY += 26;
                // Kolom Kiri: Ketua DKM MASJID AL JIHAD
                ctx.textAlign = "center";
                ctx.font = "500 16px 'Segoe UI', Arial, sans-serif";
                ctx.fillText("Mengetahui,", 240, curY);
                ctx.font = "bold 17px 'Segoe UI', Arial, sans-serif";
                ctx.fillText("Ketua DKM MASJID AL JIHAD", 240, curY + 24);

                // Kolom Kanan: Bendahara MASJID AL JIHAD
                ctx.font = "500 16px 'Segoe UI', Arial, sans-serif";
                ctx.fillText("Dibuat Oleh,", 960, curY);
                ctx.font = "bold 17px 'Segoe UI', Arial, sans-serif";
                ctx.fillText("Bendahara MASJID AL JIHAD", 960, curY + 24);

                // Muat dan Gambar Cap Stempel & Tanda Tangan
                let loadedAssets = 0;
                const totalAssets = 3;
                function checkFinishRender() {
                    loadedAssets++;
                    if (loadedAssets >= totalAssets) {
                        // Footer Dokumen
                        ctx.strokeStyle = "#cbd5e1";
                        ctx.lineWidth = 1;
                        ctx.beginPath();
                        ctx.moveTo(40, H - 46);
                        ctx.lineTo(1160, H - 46);
                        ctx.stroke();

                        ctx.fillStyle = "#64748b";
                        ctx.font = "500 13px 'Segoe UI', Arial, sans-serif";
                        ctx.textAlign = "left";
                        ctx.fillText("Dokumen Resmi Pengumuman Ibadah & Kas Jum'at DKM Masjid Jami' Al-Jihad Simpangan", 40, H - 24);
                        ctx.textAlign = "right";
                        ctx.fillText("Dibuat dari Sistem Informasi Digital Al Jihad", 1160, H - 24);

                        // Trigger Download
                        const cleanTgl = (pjData.tanggalJudul || 'Jumat').replace(/[^a-zA-Z0-9]/g, '_');
                        const link = document.createElement('a');
                        link.download = `Flyer_Pengumuman_Jumat_${cleanTgl}.png`;
                        link.href = canvas.toDataURL('image/png');
                        link.click();

                        if (typeof showCustomToast === 'function') {
                            showCustomToast("Flyer Pengumuman Sholat Jum'at berhasil diunduh!", 'success');
                        }
                    }
                }

                // 1. Gambar Stempel DKM
                const imgStempel = new Image();
                imgStempel.onload = function() {
                    if (pjData.ttd.tampilkanKetua !== false) {
                        ctx.save();
                        ctx.translate(195, curY + 95);
                        ctx.rotate(-5 * Math.PI / 180);
                        ctx.globalAlpha = 0.92;
                        ctx.drawImage(imgStempel, -75, -55, 150, 110);
                        ctx.restore();
                    }
                    checkFinishRender();
                };
                imgStempel.onerror = function() { checkFinishRender(); };
                imgStempel.src = 'img/stempelDKM_trans.png';

                // 2. Gambar TTD Ketua
                const imgKetua = new Image();
                imgKetua.onload = function() {
                    if (pjData.ttd.tampilkanKetua !== false) {
                        ctx.drawImage(imgKetua, 195, curY + 45, 140, 100);
                    }
                    // Nama Ketua DKM (Garis Bawah)
                    ctx.fillStyle = "#0f172a";
                    ctx.font = "bold 17px 'Segoe UI', Arial, sans-serif";
                    ctx.textAlign = "center";
                    ctx.fillText(pjData.ttd.namaKetua || 'Hadi Prayitno', 240, curY + 165);
                    const ketuaW = ctx.measureText(pjData.ttd.namaKetua || 'Hadi Prayitno').width;
                    ctx.beginPath();
                    ctx.moveTo(240 - (ketuaW / 2), curY + 169);
                    ctx.lineTo(240 + (ketuaW / 2), curY + 169);
                    ctx.stroke();
                    checkFinishRender();
                };
                imgKetua.onerror = function() {
                    ctx.fillStyle = "#0f172a";
                    ctx.font = "bold 17px 'Segoe UI', Arial, sans-serif";
                    ctx.textAlign = "center";
                    ctx.fillText(pjData.ttd.namaKetua || 'Hadi Prayitno', 240, curY + 165);
                    const ketuaW = ctx.measureText(pjData.ttd.namaKetua || 'Hadi Prayitno').width;
                    ctx.beginPath();
                    ctx.moveTo(240 - (ketuaW / 2), curY + 169);
                    ctx.lineTo(240 + (ketuaW / 2), curY + 169);
                    ctx.stroke();
                    checkFinishRender();
                };
                imgKetua.src = 'img/tandatanganketuaDKM_trans.png';

                // 3. Gambar TTD Bendahara
                const imgBen = new Image();
                imgBen.onload = function() {
                    if (pjData.ttd.tampilkanBendahara !== false) {
                        ctx.drawImage(imgBen, 912, curY + 58, 96, 78);
                    }
                    // Nama Bendahara (Garis Bawah)
                    ctx.fillStyle = "#0f172a";
                    ctx.font = "bold 17px 'Segoe UI', Arial, sans-serif";
                    ctx.textAlign = "center";
                    ctx.fillText(pjData.ttd.namaBendahara || 'Utut Priyastya', 960, curY + 165);
                    const benW = ctx.measureText(pjData.ttd.namaBendahara || 'Utut Priyastya').width;
                    ctx.beginPath();
                    ctx.moveTo(960 - (benW / 2), curY + 169);
                    ctx.lineTo(960 + (benW / 2), curY + 169);
                    ctx.stroke();
                    checkFinishRender();
                };
                imgBen.onerror = function() {
                    ctx.fillStyle = "#0f172a";
                    ctx.font = "bold 17px 'Segoe UI', Arial, sans-serif";
                    ctx.textAlign = "center";
                    ctx.fillText(pjData.ttd.namaBendahara || 'Utut Priyastya', 960, curY + 165);
                    const benW = ctx.measureText(pjData.ttd.namaBendahara || 'Utut Priyastya').width;
                    ctx.beginPath();
                    ctx.moveTo(960 - (benW / 2), curY + 169);
                    ctx.lineTo(960 + (benW / 2), curY + 169);
                    ctx.stroke();
                    checkFinishRender();
                };
                imgBen.src = 'img/tantatanganbendahara_trans.png';
            };

            imgKop.onerror = function() {
                alert('Gagal memuat gambar Kop Surat Pengumuman!');
            };
            imgKop.src = 'img/kop_surat_aljihad.png';
        }

        // =========================================================================
        // FITUR 3: TEKS BACAAN MAKLUMAT UNTUK PEMBACA MAKLUMAT (POIN 1 S/D 13)
        // =========================================================================
        function generateNaskahMaklumatText() {
            pjUpdatePreview();
            let totalIn = 0;
            pjData.pemasukan.forEach(p => { totalIn += (parseInt(p.nominal, 10) || 0); });
            let totalOut = 0;
            pjData.pengeluaran.forEach(p => { totalOut += (parseInt(p.nominal, 10) || 0); });
            const saldoAwal = parseInt(pjData.saldoAwal, 10) || 0;
            const subtotalSaldo = saldoAwal + totalIn;
            const saldoAkhir = subtotalSaldo - totalOut;

            const tglHariH = pjData.tanggalJudul || '02 Oktober 2026';
            const tglPekanLalu = pjHitungTanggalPekanLalu(tglHariH, false);

            let naskah = `Bismillahirrohmanirrohim...\n`;
            naskah += `Assalamu'alaikum Warahmatullahi Wabarakatuh.\n\n`;
            naskah += `PENGUMUMAN KEGIATAN SHOLAT JUM’AT MASJID JAMI’ AL JIHAD\n`;
            naskah += `Hari ini, Jum'at, ${tglHariH}.\n\n`;

            naskah += `I. LAPORAN KEUANGAN KAS MASJID:\n`;
            naskah += `Laporan keuangan kas masjid sampai dengan hari ini Jum'at, ${tglHariH}:\n`;
            naskah += `Saldo Awal kas masjid sebesar: Rp ${pjFormatRupiah(saldoAwal)}\n`;
            naskah += `2. Pemasukan Kas:\n`;
            pjData.pemasukan.forEach((p, idx) => {
                naskah += `   - ${p.ket}: Rp ${pjFormatRupiah(p.nominal)}\n`;
            });
            naskah += `   Total Pemasukan sebesar: Rp ${pjFormatRupiah(totalIn)}\n`;
            naskah += `3. SALDO (Saldo Awal ditambah Pemasukan) berjumlah sebesar: Rp ${pjFormatRupiah(subtotalSaldo)}\n`;
            naskah += `4. Pengeluaran Kas Masjid:\n`;
            pjData.pengeluaran.forEach((p, idx) => {
                naskah += `   ${idx + 1}. ${p.ket}: Rp ${pjFormatRupiah(p.nominal)}\n`;
            });
            naskah += `   Total Pengeluaran sebesar: Rp ${pjFormatRupiah(totalOut)}\n`;
            naskah += `5. Sehingga SALDO AKHIR KAS MASJID saat ini adalah sebesar: Rp ${pjFormatRupiah(saldoAkhir)}\n\n`;

            naskah += `II. PETUGAS SHOLAT JUM'AT HARI INI:\n`;
            naskah += `- Imam & Khotib : ${pjData.petugas.imamKhotib || 'Ust. Faiq Rido'}\n`;
            naskah += `- Muadzin       : ${pjData.petugas.muadzin || 'Ust. Rudy'}\n`;
            naskah += `- Bilal         : ${pjData.petugas.bilal || 'Ust. Mansur'}\n`;
            naskah += `- MC Maklumat   : ${pjData.petugas.maklumat || 'Ust. Fatkhurokhman'}\n\n`;

            naskah += `III. HIMBAUAN:\n`;
            const hLines = (pjData.himbauan && pjData.himbauan.length > 0) ? pjData.himbauan : [
                "Diharapkan Jamaah yang masih berada diluar dipersilakan masuk untuk mengisi shaf yang masih kosong",
                "Bagi Jamaah yang membawa kendaraan diharapkan diparkir dengan rapi dan jangan lupa mengunci ganda kendaraanya",
                "Bagi Jamaah yang membawa HP Mohon di Non Aktifkan / di Silent sementara.",
                "Kepada para Jamaah semuanya, apabila khatib sudah naik mimbar dimohon untuk tidak berbicara atau mengobrol sementara waktu"
            ];
            hLines.forEach((line, idx) => {
                let clean = line.replace(/^([•\-\*]|\d+[\.\)]|[a-zA-Z][\.\)])\s*/g, '').trim();
                clean = clean.replace(/^[•\-\*]\s*/g, '').trim();
                naskah += `${idx + 1}. ${clean}\n`;
            });

            naskah += `\nDemikian maklumat pengumuman ini kami sampaikan, terima kasih atas perhatian seluruh jamaah.\n`;
            naskah += `Wassalamu'alaikum Warahmatullahi Wabarakatuh.`;

            return naskah;
        }

        // =========================================================================
        // FITUR 3B: CETAK / SIMPAN PDF NASKAH BACAAN MAKLUMAT JUM'AT (A4 DOKUMEN RESMI)
        // =========================================================================
        function cetakNaskahMaklumatPDF() {
            if (typeof pjUpdatePreview === 'function') pjUpdatePreview();
            const tglHariH = pjData.tanggalJudul || '02 Oktober 2026';
            let totalIn = 0;
            pjData.pemasukan.forEach(p => { totalIn += (parseInt(p.nominal, 10) || 0); });
            let totalOut = 0;
            pjData.pengeluaran.forEach(p => { totalOut += (parseInt(p.nominal, 10) || 0); });
            const saldoAwal = parseInt(pjData.saldoAwal, 10) || 0;
            const subtotalSaldo = saldoAwal + totalIn;
            const saldoAkhir = subtotalSaldo - totalOut;

            const w = window.open('', '_blank');
            if (!w) {
                alert('Pop-up terblokir. Silakan izinkan pop-up peramban untuk mencetak/menyimpan PDF.');
                return;
            }

            const html = `
            <!DOCTYPE html>
            <html lang="id">
            <head>
                <meta charset="utf-8">
                <title>Naskah Maklumat Sholat Jum'at - ${escapeHtml(tglHariH)}</title>
                <style>
                    @page {
                        size: A4 portrait;
                        margin: 12mm 15mm;
                    }
                    * { box-sizing: border-box; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
                    body {
                        font-family: 'Segoe UI', Arial, Helvetica, sans-serif;
                        font-size: 13.5px;
                        line-height: 1.6;
                        color: #0f172a;
                        margin: 0;
                        padding: 10px;
                        background: #ffffff;
                    }
                    .kop-img { width: 100%; max-height: 110px; object-fit: contain; display: block; margin-bottom: 6px; }
                    .garis-kop { height: 3px; background: #047857; margin-bottom: 14px; }
                    .judul-maklumat { text-align: center; margin-bottom: 16px; }
                    .judul-maklumat h2 { margin: 0; font-size: 16px; font-weight: 800; text-transform: uppercase; color: #047857; letter-spacing: 0.5px; }
                    .judul-maklumat .tgl { font-size: 14px; font-weight: 700; color: #1e293b; margin-top: 2px; }
                    .pembuka { margin-bottom: 12px; font-weight: 600; font-size: 14px; }
                    .section-title { font-size: 14px; font-weight: 800; color: #047857; margin-top: 14px; margin-bottom: 6px; border-bottom: 1.5px solid #a7f3d0; padding-bottom: 2px; }
                    .kas-box { background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 6px; padding: 10px 14px; margin: 8px 0; }
                    .kas-row { display: flex; justify-content: space-between; margin-bottom: 4px; }
                    .kas-highlight { font-weight: 800; color: #065f46; background: #ecfdf5; padding: 4px 8px; border-radius: 4px; margin: 4px 0; border-left: 3px solid #10b981; }
                    .petugas-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 8px; margin: 8px 0; }
                    .petugas-item { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 6px 12px; }
                    .petugas-label { font-size: 11px; color: #64748b; font-weight: 600; text-transform: uppercase; }
                    .petugas-nama { font-size: 13.5px; font-weight: 700; color: #0f172a; }
                    .himbauan-list { margin: 6px 0; padding-left: 20px; }
                    .himbauan-list li { margin-bottom: 4px; }
                    .penutup { margin-top: 14px; font-weight: 600; }
                    .ttd-container { display: flex; justify-content: space-between; margin-top: 24px; page-break-inside: avoid; }
                    .ttd-col { text-align: center; width: 220px; }
                    .ttd-jabatan { font-size: 12px; font-weight: 600; color: #475569; }
                    .ttd-img-box { height: 55px; display: flex; align-items: center; justify-content: center; margin: 4px 0; }
                    .ttd-img-box img { max-height: 50px; }
                    .ttd-nama { font-weight: 800; font-size: 13.5px; border-bottom: 1.5px solid #0f172a; display: inline-block; padding-bottom: 2px; }
                </style>
            </head>
            <body>
                <img src="img/kop_surat_aljihad.png" onerror="this.onerror=null; this.src='img/kop_pengumuman_jumat.png';" class="kop-img" alt="Kop Surat DKM Al-Jihad">
                <div class="garis-kop"></div>

                <div class="judul-maklumat">
                    <h2>Naskah Bacaan Maklumat Sholat Jum'at</h2>
                    <div class="tgl">Hari ini, Jum'at, ${escapeHtml(tglHariH)}</div>
                </div>

                <div class="pembuka">
                    <div>Bismillahirrohmanirrohim...</div>
                    <div>Assalamu'alaikum Warahmatullahi Wabarakatuh.</div>
                </div>

                <div class="section-title">I. LAPORAN KEUANGAN KAS MASJID</div>
                <div style="margin-bottom: 6px;">Laporan keuangan kas masjid sampai dengan hari ini Jum'at, ${escapeHtml(tglHariH)}:</div>
                <div class="kas-box">
                    <div class="kas-row"><span>1. Saldo Awal Kas Masjid:</span> <strong>Rp ${pjFormatRupiah(saldoAwal)}</strong></div>
                    <div style="margin: 4px 0 2px; font-weight: 600;">2. Pemasukan Kas:</div>
                    ${pjData.pemasukan.map(p => `<div class="kas-row" style="padding-left: 14px; font-size: 12.5px;"><span>• ${escapeHtml(p.ket)}</span> <span>Rp ${pjFormatRupiah(p.nominal)}</span></div>`).join('')}
                    <div class="kas-row" style="font-weight: 700; border-top: 1px dashed #cbd5e1; padding-top: 3px; margin-top: 3px;">
                        <span>Total Pemasukan:</span> <span>Rp ${pjFormatRupiah(totalIn)}</span>
                    </div>
                    <div class="kas-highlight">
                        3. SALDO (Saldo Awal + Pemasukan): Rp ${pjFormatRupiah(subtotalSaldo)}
                    </div>
                    <div style="margin: 6px 0 2px; font-weight: 600;">4. Pengeluaran Kas Masjid:</div>
                    ${pjData.pengeluaran.map((p, idx) => `<div class="kas-row" style="padding-left: 14px; font-size: 12.5px;"><span>${idx + 1}. ${escapeHtml(p.ket)}</span> <span>Rp ${pjFormatRupiah(p.nominal)}</span></div>`).join('')}
                    <div class="kas-row" style="font-weight: 700; border-top: 1px dashed #cbd5e1; padding-top: 3px; margin-top: 3px;">
                        <span>Total Pengeluaran:</span> <span>Rp ${pjFormatRupiah(totalOut)}</span>
                    </div>
                    <div class="kas-highlight" style="background: #fef3c7; color: #92400e; border-left-color: #f59e0b; margin-top: 6px;">
                        5. Sehingga SALDO AKHIR KAS MASJID saat ini adalah: Rp ${pjFormatRupiah(saldoAkhir)}
                    </div>
                </div>

                <div class="section-title">II. PETUGAS SHOLAT JUM'AT HARI INI</div>
                <div class="petugas-grid">
                    <div class="petugas-item">
                        <div class="petugas-label">Imam & Khotib</div>
                        <div class="petugas-nama">${escapeHtml(pjData.petugas.imamKhotib || 'Ust. Faiq Rido')}</div>
                    </div>
                    <div class="petugas-item">
                        <div class="petugas-label">Muadzin</div>
                        <div class="petugas-nama">${escapeHtml(pjData.petugas.muadzin || 'Ust. Rudy')}</div>
                    </div>
                    <div class="petugas-item">
                        <div class="petugas-label">Bilal Jum'at</div>
                        <div class="petugas-nama">${escapeHtml(pjData.petugas.bilal || 'Ust. Mansur')}</div>
                    </div>
                    <div class="petugas-item">
                        <div class="petugas-label">Pembaca Maklumat (MC)</div>
                        <div class="petugas-nama">${escapeHtml(pjData.petugas.maklumat || 'Ust. Fatkhurokhman')}</div>
                    </div>
                </div>

                <div class="section-title">III. HIMBAUAN KEPADA SELURUH JAMAAH</div>
                <ol class="himbauan-list">
                    ${(pjData.himbauan && pjData.himbauan.length > 0 ? pjData.himbauan : [
                        "Diharapkan Jamaah yang masih berada diluar dipersilakan masuk untuk mengisi shaf yang masih kosong",
                        "Bagi Jamaah yang membawa kendaraan diharapkan diparkir dengan rapi dan jangan lupa mengunci ganda kendaraanya",
                        "Bagi Jamaah yang membawa HP Mohon di Non Aktifkan / di Silent sementara.",
                        "Kepada para Jamaah semuanya, apabila khatib sudah naik mimbar dimohon untuk tidak berbicara atau mengobrol sementara waktu"
                    ]).map(h => `<li>${escapeHtml(h.replace(/^([•\-\*]|\d+[\.\)]|[a-zA-Z][\.\)])\s*/g, '').trim())}</li>`).join('')}
                </ol>

                <div class="penutup">
                    <div>Demikian maklumat pengumuman ini kami sampaikan, terima kasih atas perhatian seluruh jamaah.</div>
                    <div style="margin-top: 4px;">Wassalamu'alaikum Warahmatullahi Wabarakatuh.</div>
                </div>

                <div class="ttd-container">
                    <div class="ttd-col">
                        <div class="ttd-jabatan">Ketua DKM Masjid Jami' Al Jihad</div>
                        <div class="ttd-img-box">
                            <img src="img/tandatanganketuaDKM_trans.png" alt="TTD Ketua" onerror="this.style.display='none'">
                        </div>
                        <div class="ttd-nama">${escapeHtml(pjData.ttd.namaKetua || 'Agus Purwanto, S.Kom.')}</div>
                    </div>
                    <div style="display: flex; align-items: center; justify-content: center;">
                        <img src="img/stempelDKM_trans.png" alt="Stempel DKM" style="height: 60px; opacity: 0.85;" onerror="this.style.display='none'">
                    </div>
                    <div class="ttd-col">
                        <div class="ttd-jabatan">Bendahara DKM Al-Jihad</div>
                        <div class="ttd-img-box">
                            <img src="img/tantatanganbendahara_trans.png" alt="TTD Bendahara" onerror="this.style.display='none'">
                        </div>
                        <div class="ttd-nama">${escapeHtml(pjData.ttd.namaBendahara || 'Utut Priyastya')}</div>
                    </div>
                </div>

                <script>
                    setTimeout(() => { window.print(); }, 400);
                <\/script>
            </body>
            </html>
            `;

            w.document.open();
            w.document.write(html);
            w.document.close();
        }

        // =========================================================================
        // FITUR 3C: DOWNLOAD GAMBAR NASKAH MAKLUMAT (PNG RESOLUSI TINGGI 1200 x 2100 PX)
        // =========================================================================
        function downloadNaskahMaklumatPNG() {
            if (typeof pjUpdatePreview === 'function') pjUpdatePreview();
            let canvas = document.getElementById('canvasNaskahMaklumatJumat');
            if (!canvas) {
                canvas = document.createElement('canvas');
                canvas.id = 'canvasNaskahMaklumatJumat';
                canvas.style.display = 'none';
                document.body.appendChild(canvas);
            }

            const W = 1200;
            const H = 2100;
            canvas.width = W;
            canvas.height = H;
            const ctx = canvas.getContext('2d');

            ctx.fillStyle = "#ffffff";
            ctx.fillRect(0, 0, W, H);

            ctx.strokeStyle = "#e2e8f0";
            ctx.lineWidth = 4;
            ctx.strokeRect(10, 10, W - 20, H - 20);

            let totalIn = 0;
            pjData.pemasukan.forEach(p => { totalIn += (parseInt(p.nominal, 10) || 0); });
            let totalOut = 0;
            pjData.pengeluaran.forEach(p => { totalOut += (parseInt(p.nominal, 10) || 0); });
            const saldoAwal = parseInt(pjData.saldoAwal, 10) || 0;
            const subtotalSaldo = saldoAwal + totalIn;
            const saldoAkhir = subtotalSaldo - totalOut;
            const tglHariH = pjData.tanggalJudul || '02 Oktober 2026';

            const imgKop = new Image();
            imgKop.crossOrigin = "anonymous";
            imgKop.onload = function() {
                const kopW = 1120;
                const kopH = kopW * (imgKop.naturalHeight / imgKop.naturalWidth);
                ctx.drawImage(imgKop, 40, 36, kopW, kopH);

                let curY = 36 + kopH + 12;
                ctx.strokeStyle = "#047857";
                ctx.lineWidth = 3.5;
                ctx.beginPath();
                ctx.moveTo(40, curY);
                ctx.lineTo(1160, curY);
                ctx.stroke();

                curY += 36;
                ctx.fillStyle = "#047857";
                ctx.font = "bold 26px 'Segoe UI', Arial, sans-serif";
                ctx.textAlign = "center";
                ctx.fillText("NASKAH BACAAN MAKLUMAT SHOLAT JUM'AT", W / 2, curY);

                curY += 28;
                ctx.fillStyle = "#0f172a";
                ctx.font = "bold 20px 'Segoe UI', Arial, sans-serif";
                ctx.fillText("MASJID JAMI' AL JIHAD - GRAHA ASRI", W / 2, curY);

                curY += 26;
                ctx.fillStyle = "#475569";
                ctx.font = "600 17px 'Segoe UI', Arial, sans-serif";
                ctx.fillText(`Hari ini, Jum'at, ${tglHariH}`, W / 2, curY);

                curY += 40;
                ctx.fillStyle = "#0f172a";
                ctx.font = "bold 18px 'Segoe UI', Arial, sans-serif";
                ctx.textAlign = "left";
                ctx.fillText("Bismillahirrohmanirrohim...", 50, curY);
                curY += 26;
                ctx.fillText("Assalamu'alaikum Warahmatullahi Wabarakatuh.", 50, curY);

                curY += 38;
                ctx.fillStyle = "#047857";
                ctx.font = "bold 20px 'Segoe UI', Arial, sans-serif";
                ctx.fillText("I. LAPORAN KEUANGAN KAS MASJID:", 50, curY);

                curY += 26;
                ctx.fillStyle = "#334155";
                ctx.font = "500 16.5px 'Segoe UI', Arial, sans-serif";
                ctx.fillText(`Laporan keuangan kas masjid sampai dengan hari ini Jum'at, ${tglHariH}:`, 50, curY);

                curY += 14;
                const boxStartY = curY;
                const boxH = 260 + (pjData.pemasukan.length * 22) + (pjData.pengeluaran.length * 22);
                ctx.fillStyle = "#f8fafc";
                ctx.fillRect(50, curY, 1100, boxH);
                ctx.strokeStyle = "#cbd5e1";
                ctx.lineWidth = 1.5;
                ctx.strokeRect(50, curY, 1100, boxH);

                let kasY = curY + 28;
                ctx.fillStyle = "#0f172a";
                ctx.font = "600 16px 'Segoe UI', Arial, sans-serif";
                ctx.fillText("1. Saldo Awal Kas Masjid:", 70, kasY);
                ctx.textAlign = "right";
                ctx.fillText(`Rp ${pjFormatRupiah(saldoAwal)}`, 1130, kasY);
                ctx.textAlign = "left";

                kasY += 24;
                ctx.font = "bold 16px 'Segoe UI', Arial, sans-serif";
                ctx.fillText("2. Pemasukan Kas:", 70, kasY);
                ctx.font = "500 15px 'Segoe UI', Arial, sans-serif";
                pjData.pemasukan.forEach(p => {
                    kasY += 22;
                    ctx.fillText(`   - ${p.ket}`, 70, kasY);
                    ctx.textAlign = "right";
                    ctx.fillText(`Rp ${pjFormatRupiah(p.nominal)}`, 1130, kasY);
                    ctx.textAlign = "left";
                });

                kasY += 24;
                ctx.font = "bold 15px 'Segoe UI', Arial, sans-serif";
                ctx.fillText("   Total Pemasukan:", 70, kasY);
                ctx.textAlign = "right";
                ctx.fillText(`Rp ${pjFormatRupiah(totalIn)}`, 1130, kasY);
                ctx.textAlign = "left";

                kasY += 26;
                ctx.fillStyle = "#ecfdf5";
                ctx.fillRect(65, kasY - 18, 1070, 26);
                ctx.fillStyle = "#065f46";
                ctx.font = "bold 16px 'Segoe UI', Arial, sans-serif";
                ctx.fillText("3. SALDO (Saldo Awal ditambah Pemasukan):", 75, kasY);
                ctx.textAlign = "right";
                ctx.fillText(`Rp ${pjFormatRupiah(subtotalSaldo)}`, 1125, kasY);
                ctx.textAlign = "left";

                kasY += 28;
                ctx.fillStyle = "#0f172a";
                ctx.font = "bold 16px 'Segoe UI', Arial, sans-serif";
                ctx.fillText("4. Pengeluaran Kas Masjid:", 70, kasY);
                ctx.font = "500 15px 'Segoe UI', Arial, sans-serif";
                pjData.pengeluaran.forEach((p, idx) => {
                    kasY += 22;
                    ctx.fillText(`   ${idx + 1}. ${p.ket}`, 70, kasY);
                    ctx.textAlign = "right";
                    ctx.fillText(`Rp ${pjFormatRupiah(p.nominal)}`, 1130, kasY);
                    ctx.textAlign = "left";
                });

                kasY += 24;
                ctx.font = "bold 15px 'Segoe UI', Arial, sans-serif";
                ctx.fillText("   Total Pengeluaran:", 70, kasY);
                ctx.textAlign = "right";
                ctx.fillText(`Rp ${pjFormatRupiah(totalOut)}`, 1130, kasY);
                ctx.textAlign = "left";

                kasY += 26;
                ctx.fillStyle = "#fef3c7";
                ctx.fillRect(65, kasY - 18, 1070, 26);
                ctx.fillStyle = "#92400e";
                ctx.font = "bold 16.5px 'Segoe UI', Arial, sans-serif";
                ctx.fillText("5. Sehingga SALDO AKHIR KAS MASJID saat ini adalah:", 75, kasY);
                ctx.textAlign = "right";
                ctx.fillText(`Rp ${pjFormatRupiah(saldoAkhir)}`, 1125, kasY);
                ctx.textAlign = "left";

                curY = boxStartY + boxH + 28;

                ctx.fillStyle = "#047857";
                ctx.font = "bold 20px 'Segoe UI', Arial, sans-serif";
                ctx.fillText("II. PETUGAS SHOLAT JUM'AT HARI INI:", 50, curY);

                curY += 24;
                ctx.fillStyle = "#0f172a";
                ctx.font = "600 16px 'Segoe UI', Arial, sans-serif";
                ctx.fillText(`• Imam & Khotib : ${pjData.petugas.imamKhotib || 'Ust. Faiq Rido'}`, 70, curY);
                curY += 22;
                ctx.fillText(`• Muadzin       : ${pjData.petugas.muadzin || 'Ust. Rudy'}`, 70, curY);
                curY += 22;
                ctx.fillText(`• Bilal         : ${pjData.petugas.bilal || 'Ust. Mansur'}`, 70, curY);
                curY += 22;
                ctx.fillText(`• MC Maklumat   : ${pjData.petugas.maklumat || 'Ust. Fatkhurokhman'}`, 70, curY);

                curY += 34;
                ctx.fillStyle = "#047857";
                ctx.font = "bold 20px 'Segoe UI', Arial, sans-serif";
                ctx.fillText("III. HIMBAUAN KEPADA SELURUH JAMAAH:", 50, curY);

                const hLines = (pjData.himbauan && pjData.himbauan.length > 0) ? pjData.himbauan : [
                    "Diharapkan Jamaah yang masih berada diluar dipersilakan masuk untuk mengisi shaf yang masih kosong",
                    "Bagi Jamaah yang membawa kendaraan diharapkan diparkir dengan rapi dan jangan lupa mengunci ganda kendaraanya",
                    "Bagi Jamaah yang membawa HP Mohon di Non Aktifkan / di Silent sementara.",
                    "Kepada para Jamaah semuanya, apabila khatib sudah naik mimbar dimohon untuk tidak berbicara atau mengobrol sementara waktu"
                ];

                hLines.forEach((line, idx) => {
                    curY += 22;
                    let clean = line.replace(/^([•\-\*]|\d+[\.\)]|[a-zA-Z][\.\)])\s*/g, '').trim();
                    ctx.fillStyle = "#0f172a";
                    ctx.font = "500 15px 'Segoe UI', Arial, sans-serif";
                    ctx.fillText(`${idx + 1}. ${clean}`, 70, curY);
                });

                curY += 30;
                ctx.fillStyle = "#334155";
                ctx.font = "italic 15px 'Segoe UI', Arial, sans-serif";
                ctx.fillText("Demikian maklumat pengumuman ini kami sampaikan, terima kasih atas perhatian seluruh jamaah.", 50, curY);
                curY += 24;
                ctx.fillStyle = "#0f172a";
                ctx.font = "bold 16px 'Segoe UI', Arial, sans-serif";
                ctx.fillText("Wassalamu'alaikum Warahmatullahi Wabarakatuh.", 50, curY);

                let ttdLoaded = 0;
                function finishExport() {
                    ttdLoaded++;
                    if (ttdLoaded >= 3) {
                        const cleanTgl = (pjData.tanggalJudul || 'Jumat').replace(/[^a-zA-Z0-9]/g, '_');
                        const link = document.createElement('a');
                        link.download = `Naskah_Maklumat_Jumat_${cleanTgl}.png`;
                        link.href = canvas.toDataURL('image/png');
                        link.click();
                        if (typeof showCustomToast === 'function') {
                            showCustomToast("Gambar Naskah Maklumat berhasil diunduh!", 'success');
                        }
                    }
                }

                const ttdBaseY = H - 220;

                const imgStempel = new Image();
                imgStempel.onload = function() {
                    ctx.save();
                    ctx.translate(210, ttdBaseY + 60);
                    ctx.rotate(-5 * Math.PI / 180);
                    ctx.globalAlpha = 0.88;
                    ctx.drawImage(imgStempel, -70, -50, 140, 100);
                    ctx.restore();
                    finishExport();
                };
                imgStempel.onerror = finishExport;
                imgStempel.src = 'img/stempelDKM_trans.png';

                const imgKetua = new Image();
                imgKetua.onload = function() {
                    ctx.drawImage(imgKetua, 200, ttdBaseY + 15, 120, 80);
                    ctx.fillStyle = "#0f172a";
                    ctx.font = "bold 16px 'Segoe UI', Arial, sans-serif";
                    ctx.textAlign = "center";
                    ctx.fillText(pjData.ttd.namaKetua || 'Agus Purwanto, S.Kom.', 260, ttdBaseY + 115);
                    ctx.textAlign = "left";
                    finishExport();
                };
                imgKetua.onerror = function() {
                    ctx.fillStyle = "#0f172a";
                    ctx.font = "bold 16px 'Segoe UI', Arial, sans-serif";
                    ctx.textAlign = "center";
                    ctx.fillText(pjData.ttd.namaKetua || 'Agus Purwanto, S.Kom.', 260, ttdBaseY + 115);
                    ctx.textAlign = "left";
                    finishExport();
                };
                imgKetua.src = 'img/tandatanganketuaDKM_trans.png';

                const imgBen = new Image();
                imgBen.onload = function() {
                    ctx.drawImage(imgBen, 880, ttdBaseY + 15, 120, 80);
                    ctx.fillStyle = "#0f172a";
                    ctx.font = "bold 16px 'Segoe UI', Arial, sans-serif";
                    ctx.textAlign = "center";
                    ctx.fillText(pjData.ttd.namaBendahara || 'Utut Priyastya', 940, ttdBaseY + 115);
                    ctx.textAlign = "left";
                    finishExport();
                };
                imgBen.onerror = function() {
                    ctx.fillStyle = "#0f172a";
                    ctx.font = "bold 16px 'Segoe UI', Arial, sans-serif";
                    ctx.textAlign = "center";
                    ctx.fillText(pjData.ttd.namaBendahara || 'Utut Priyastya', 940, ttdBaseY + 115);
                    ctx.textAlign = "left";
                    finishExport();
                };
                imgBen.src = 'img/tantatanganbendahara_trans.png';

                ctx.fillStyle = "#475569";
                ctx.font = "600 15px 'Segoe UI', Arial, sans-serif";
                ctx.textAlign = "center";
                ctx.fillText("Ketua DKM Masjid Jami' Al Jihad", 260, ttdBaseY);
                ctx.fillText("Bendahara DKM Al-Jihad", 940, ttdBaseY);
                ctx.textAlign = "left";
            };

            imgKop.onerror = function() {
                alert("Gagal memuat kop surat untuk ekspor gambar naskah.");
            };
            imgKop.src = 'img/kop_surat_aljihad.png';
        }

        // =========================================================================
        // FITUR 3D: DOWNLOAD DOKUMEN WORD NASKAH MAKLUMAT (.DOC)
        // =========================================================================
        function downloadNaskahMaklumatDoc() {
            if (typeof pjUpdatePreview === 'function') pjUpdatePreview();
            const tglHariH = pjData.tanggalJudul || '02 Oktober 2026';
            let totalIn = 0;
            pjData.pemasukan.forEach(p => { totalIn += (parseInt(p.nominal, 10) || 0); });
            let totalOut = 0;
            pjData.pengeluaran.forEach(p => { totalOut += (parseInt(p.nominal, 10) || 0); });
            const saldoAwal = parseInt(pjData.saldoAwal, 10) || 0;
            const subtotalSaldo = saldoAwal + totalIn;
            const saldoAkhir = subtotalSaldo - totalOut;

            const docContent = `
            <html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'>
            <head><meta charset='utf-8'><title>Naskah Maklumat Sholat Jum'at</title>
            <style>
                body { font-family: 'Calibri', 'Segoe UI', Arial, sans-serif; font-size: 11pt; line-height: 1.5; color: #000000; }
                h1 { font-size: 14pt; color: #047857; text-align: center; margin-bottom: 2pt; }
                h2 { font-size: 12pt; color: #047857; margin-top: 12pt; margin-bottom: 4pt; border-bottom: 1pt solid #047857; padding-bottom: 2pt; }
                .sub { text-align: center; font-size: 11pt; font-weight: bold; margin-bottom: 14pt; }
                .kas-table { width: 100%; border-collapse: collapse; margin: 8pt 0; }
                .kas-table td { padding: 4pt 6pt; font-size: 10.5pt; }
                .highlight { background-color: #ecfdf5; font-weight: bold; }
                .highlight-gold { background-color: #fef3c7; font-weight: bold; }
                .ttd-table { width: 100%; margin-top: 24pt; border: none; }
                .ttd-table td { text-align: center; font-size: 10.5pt; }
            </style>
            </head>
            <body>
                <h1>PENGUMUMAN KEGIATAN SHOLAT JUM'AT MASJID JAMI' AL JIHAD</h1>
                <div class='sub'>Hari ini, Jum'at, ${escapeHtml(tglHariH)}</div>
                <p><b>Bismillahirrohmanirrohim...</b><br><b>Assalamu'alaikum Warahmatullahi Wabarakatuh.</b></p>
                
                <h2>I. LAPORAN KEUANGAN KAS MASJID</h2>
                <p>Laporan keuangan kas masjid sampai dengan hari ini Jum'at, ${escapeHtml(tglHariH)}:</p>
                <table class='kas-table'>
                    <tr><td><b>1. Saldo Awal Kas Masjid</b></td><td align='right'><b>Rp ${pjFormatRupiah(saldoAwal)}</b></td></tr>
                    <tr><td colspan='2'><b>2. Pemasukan Kas:</b></td></tr>
                    ${pjData.pemasukan.map(p => `<tr><td style='padding-left: 20pt;'>- ${escapeHtml(p.ket)}</td><td align='right'>Rp ${pjFormatRupiah(p.nominal)}</td></tr>`).join('')}
                    <tr><td style='padding-left: 20pt;'><b>Total Pemasukan</b></td><td align='right'><b>Rp ${pjFormatRupiah(totalIn)}</b></td></tr>
                    <tr class='highlight'><td><b>3. SALDO (Saldo Awal + Pemasukan)</b></td><td align='right'><b>Rp ${pjFormatRupiah(subtotalSaldo)}</b></td></tr>
                    <tr><td colspan='2'><b>4. Pengeluaran Kas Masjid:</b></td></tr>
                    ${pjData.pengeluaran.map((p, idx) => `<tr><td style='padding-left: 20pt;'>${idx + 1}. ${escapeHtml(p.ket)}</td><td align='right'>Rp ${pjFormatRupiah(p.nominal)}</td></tr>`).join('')}
                    <tr><td style='padding-left: 20pt;'><b>Total Pengeluaran</b></td><td align='right'><b>Rp ${pjFormatRupiah(totalOut)}</b></td></tr>
                    <tr class='highlight-gold'><td><b>5. Sehingga SALDO AKHIR KAS MASJID</b></td><td align='right'><b>Rp ${pjFormatRupiah(saldoAkhir)}</b></td></tr>
                </table>

                <h2>II. PETUGAS SHOLAT JUM'AT HARI INI</h2>
                <p>
                    - <b>Imam & Khotib :</b> ${escapeHtml(pjData.petugas.imamKhotib || 'Ust. Faiq Rido')}<br>
                    - <b>Muadzin       :</b> ${escapeHtml(pjData.petugas.muadzin || 'Ust. Rudy')}<br>
                    - <b>Bilal Jum'at  :</b> ${escapeHtml(pjData.petugas.bilal || 'Ust. Mansur')}<br>
                    - <b>MC Maklumat   :</b> ${escapeHtml(pjData.petugas.maklumat || 'Ust. Fatkhurokhman')}
                </p>

                <h2>III. HIMBAUAN KEPADA SELURUH JAMAAH</h2>
                <ol>
                    ${(pjData.himbauan && pjData.himbauan.length > 0 ? pjData.himbauan : [
                        "Diharapkan Jamaah yang masih berada diluar dipersilakan masuk untuk mengisi shaf yang masih kosong",
                        "Bagi Jamaah yang membawa kendaraan diharapkan diparkir dengan rapi dan jangan lupa mengunci ganda kendaraanya",
                        "Bagi Jamaah yang membawa HP Mohon di Non Aktifkan / di Silent sementara.",
                        "Kepada para Jamaah semuanya, apabila khatib sudah naik mimbar dimohon untuk tidak berbicara atau mengobrol sementara waktu"
                    ]).map(h => `<li>${escapeHtml(h.replace(/^([•\-\*]|\d+[\.\)]|[a-zA-Z][\.\)])\s*/g, '').trim())}</li>`).join('')}
                </ol>

                <p>Demikian maklumat pengumuman ini kami sampaikan, terima kasih atas perhatian seluruh jamaah.<br><b>Wassalamu'alaikum Warahmatullahi Wabarakatuh.</b></p>

                <table class='ttd-table'>
                    <tr>
                        <td width='50%'>Ketua DKM Masjid Jami' Al Jihad<br><br><br><br><b><u>${escapeHtml(pjData.ttd.namaKetua || 'Agus Purwanto, S.Kom.')}</u></b></td>
                        <td width='50%'>Bendahara DKM Al-Jihad<br><br><br><br><b><u>${escapeHtml(pjData.ttd.namaBendahara || 'Utut Priyastya')}</u></b></td>
                    </tr>
                </table>
            </body></html>
            `;

            const blob = new Blob(['\ufeff', docContent], { type: 'application/msword;charset=utf-8' });
            const cleanTgl = (pjData.tanggalJudul || 'Jumat').replace(/[^a-zA-Z0-9]/g, '_');
            const filename = `Naskah_Maklumat_Jumat_${cleanTgl}.doc`;
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            setTimeout(() => { document.body.removeChild(a); URL.revokeObjectURL(url); }, 200);

            if (typeof showCustomToast === 'function') {
                showCustomToast(`Dokumen Naskah Maklumat berhasil diunduh (${filename})!`, 'success');
            }
        }

        function bukaModalTeksMaklumat() {
            if (typeof pjUpdatePreview === 'function') {
                pjUpdatePreview();
            }
            const naskah = generateNaskahMaklumatText();
            const elKonten = document.getElementById('kontenTeksMaklumat');
            if (elKonten) {
                elKonten.innerHTML = naskah
                    .split('\n')
                    .map(line => {
                        if (line.startsWith('I.') || line.startsWith('II.') || line.startsWith('III.') || line.startsWith('PENGUMUMAN')) {
                            return `<div style="font-weight: 800; color: #047857; margin-top: 14px; margin-bottom: 4px; font-size: 15.5px;">${escapeHtml(line)}</div>`;
                        } else if (line.startsWith('Bismillah') || line.startsWith('Assalamu') || line.startsWith('Wassalamu')) {
                            return `<div style="font-weight: 700; color: #0f172a; margin: 6px 0; font-size: 15px;">${escapeHtml(line)}</div>`;
                        } else if (line.startsWith('3. SALDO') || line.startsWith('5. Sehingga SALDO AKHIR')) {
                            return `<div style="font-weight: 800; color: #b45309; background: #fffbeb; padding: 4px 8px; border-radius: 4px; margin: 4px 0;">${escapeHtml(line)}</div>`;
                        } else if (line.trim().length === 0) {
                            return '<div style="height: 6px;"></div>';
                        }
                        return `<div>${escapeHtml(line)}</div>`;
                    })
                    .join('');
            }

            if (window.$ && $('#modalTeksMaklumat').length) {
                $('#modalTeksMaklumat').modal('show');
            }
        }

        function salinTeksMaklumat() {
            const naskah = generateNaskahMaklumatText();
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(naskah).then(() => {
                    if (typeof showCustomToast === 'function') {
                        showCustomToast("Naskah Pembaca Maklumat berhasil disalin ke clipboard!", 'success');
                    } else {
                        alert("Naskah Pembaca Maklumat berhasil disalin!");
                    }
                }).catch(e => {
                    fallbackSalinTeks(naskah, "Naskah Pembaca Maklumat berhasil disalin!");
                });
            } else {
                fallbackSalinTeks(naskah, "Naskah Pembaca Maklumat berhasil disalin!");
            }
        }

        function downloadTeksMaklumat() {
            try {
                if (typeof pjUpdatePreview === 'function') {
                    pjUpdatePreview();
                }
                const naskah = generateNaskahMaklumatText();
                const cleanTgl = (pjData && pjData.tanggalJudul ? pjData.tanggalJudul : 'Jumat').replace(/[^a-zA-Z0-9]/g, '_');
                const filename = `Naskah_Maklumat_Jumat_${cleanTgl}.txt`;
                
                const blob = new Blob([naskah], { type: 'text/plain;charset=utf-8' });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = filename;
                document.body.appendChild(a);
                a.click();
                setTimeout(() => {
                    document.body.removeChild(a);
                    URL.revokeObjectURL(url);
                }, 150);
                
                if (typeof showCustomToast === 'function') {
                    showCustomToast(`Naskah Maklumat Sholat Jum'at berhasil diunduh (${filename})!`, 'success');
                } else {
                    alert(`Naskah Maklumat Sholat Jum'at berhasil diunduh (${filename})!`);
                }
            } catch (err) {
                console.error("Gagal mendownload Teks Maklumat:", err);
                alert("Gagal mengunduh teks maklumat: " + (err.message || err));
            }
        }

        // =========================================================================
        // FITUR 4: BROADCAST WHATSAPP PENGUMUMAN SHOLAT JUMAT
        // =========================================================================
        function bukaModalWAPengumumanJumat() {
            try {
                if (typeof pjUpdatePreview === 'function') {
                    pjUpdatePreview();
                }
                let totalIn = 0;
                if (pjData && Array.isArray(pjData.pemasukan)) {
                    pjData.pemasukan.forEach(p => {
                        totalIn += (parseInt(p.nominal, 10) || 0);
                    });
                }

                let totalOut = 0;
                if (pjData && Array.isArray(pjData.pengeluaran)) {
                    pjData.pengeluaran.forEach(p => {
                        totalOut += (parseInt(p.nominal, 10) || 0);
                    });
                }

                const saldoAwal = parseInt(pjData?.saldoAwal, 10) || 0;
                const subtotalSaldo = saldoAwal + totalIn;
                const saldoAkhir = subtotalSaldo - totalOut;

                let waText = `*PENGUMUMAN KEGIATAN SHOLAT JUM’AT MASJID JAMI’ AL JIHAD*\n`;
                waText += `*TANGGAL : ${(pjData?.tanggalJudul || '02 OKTOBER 2026').toUpperCase()}*\n\n`;

                waText += `*I. LAPORAN KEUANGAN KAS MASJID*\n`;
                waText += `• Saldo Awal: *Rp ${typeof pjFormatRupiah === 'function' ? pjFormatRupiah(saldoAwal) : saldoAwal.toLocaleString('id-ID')}*\n`;
                waText += `• Pemasukan Tromol & Kas: *Rp ${typeof pjFormatRupiah === 'function' ? pjFormatRupiah(totalIn) : totalIn.toLocaleString('id-ID')}*\n`;
                waText += `• *SALDO (Awal + Pemasukan): Rp ${typeof pjFormatRupiah === 'function' ? pjFormatRupiah(subtotalSaldo) : subtotalSaldo.toLocaleString('id-ID')}*\n`;
                waText += `• Pengeluaran / Biaya: *Rp ${typeof pjFormatRupiah === 'function' ? pjFormatRupiah(totalOut) : totalOut.toLocaleString('id-ID')}*\n`;
                waText += `• *SALDO AKHIR KAS: Rp ${typeof pjFormatRupiah === 'function' ? pjFormatRupiah(saldoAkhir) : saldoAkhir.toLocaleString('id-ID')}*\n\n`;

                waText += `*II. PETUGAS SHOLAT JUM'AT HARI INI*\n`;
                waText += `• Imam & Khotib: *${pjData?.petugas?.imamKhotib || 'Ust. Faiq Rido'}*\n`;
                waText += `• Muadzin: *${pjData?.petugas?.muadzin || 'Ust. Rudy'}*\n`;
                waText += `• Bilal: *${pjData?.petugas?.bilal || 'Ust. Mansur'}*\n`;
                waText += `• MC Maklumat: *${pjData?.petugas?.maklumat || 'Ust. Fatkhurokhman'}*\n\n`;

                waText += `*III. HIMBAUAN*\n`;
                if (pjData && Array.isArray(pjData.himbauan) && pjData.himbauan.length > 0) {
                    pjData.himbauan.forEach((line, idx) => {
                        let clean = line.replace(/^([•\-\*]|\d+[\.\)]|[a-zA-Z][\.\)])\s*/, '');
                        waText += `${idx + 1}. ${clean}\n`;
                    });
                } else {
                    waText += `1. Diharapkan Jamaah yang masih berada diluar dipersilakan masuk untuk mengisi shaf yang masih kosong\n`;
                    waText += `2. Bagi Jamaah yang membawa kendaraan diharapkan diparkir dengan rapi dan jangan lupa mengunci ganda kendaraanya\n`;
                    waText += `3. Bagi Jamaah yang membawa HP Mohon di Non Aktifkan / di Silent sementara.\n`;
                    waText += `4. Kepada para Jamaah semuanya, apabila khatib sudah naik mimbar dimohon untuk tidak berbicara atau mengobrol sementara waktu\n`;
                }

                waText += `\n_Dibuat di : ${pjData?.titimangsa || 'Simpangan, 02 Oktober 2026'}_\n`;
                waText += `_Mengetahui: Ketua DKM (${pjData?.ttd?.namaKetua || 'Hadi Prayitno'})_\n`;
                waText += `_Dibuat Oleh: Bendahara (${pjData?.ttd?.namaBendahara || 'Utut Priyastya'})_\n\n`;
                waText += `*Sistem Informasi Digital Masjid Jami' Al-Jihad Simpangan*`;

                const elText = document.getElementById('pjTextBroadcastWA');
                if (elText) elText.value = waText;

                if (window.$ && $('#modalWAPengumumanJumat').length) {
                    $('#modalWAPengumumanJumat').modal('show');
                }
            } catch(err) {
                console.error('Error saat membuka modal WA pengumuman jumat:', err);
                alert('Terjadi kesalahan saat memproses data WA: ' + err.message);
            }
        }

        function salinTeksPengumumanWA() {
            const elText = document.getElementById('pjTextBroadcastWA');
            if (!elText) return;
            const text = elText.value;
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text).then(() => {
                    if (typeof showCustomToast === 'function') {
                        showCustomToast("Teks Siaran WhatsApp berhasil disalin ke clipboard!", 'success');
                    } else {
                        alert("Teks Siaran WhatsApp berhasil disalin!");
                    }
                }).catch(e => {
                    fallbackSalinTeks(text, "Teks Siaran WhatsApp berhasil disalin!");
                });
            } else {
                fallbackSalinTeks(text, "Teks Siaran WhatsApp berhasil disalin!");
            }
        }

        function fallbackSalinTeks(text, successMsg) {
            const textArea = document.createElement("textarea");
            textArea.value = text;
            textArea.style.position = "fixed";
            textArea.style.left = "-999999px";
            textArea.style.top = "-999999px";
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            try {
                document.execCommand('copy');
                if (typeof showCustomToast === 'function') {
                    showCustomToast(successMsg, 'success');
                } else {
                    alert(successMsg);
                }
            } catch (err) {
                console.error('Fallback salin gagal: ', err);
                alert('Gagal menyalin teks secara otomatis. Silakan salin manual.');
            }
            document.body.removeChild(textArea);
        }
        
        
        // Render Sinkronisasi Data Pengumuman Jum'at ke Dashboard Bendahara
        function renderDashPengumumanJumatWidget() {
            try {
                if (typeof pjData === 'undefined') return;

                let totalIn = 0;
                pjData.pemasukan.forEach(p => { totalIn += (parseInt(p.nominal, 10) || 0); });
                let totalOut = 0;
                pjData.pengeluaran.forEach(p => { totalOut += (parseInt(p.nominal, 10) || 0); });
                const saldoAwal = parseInt(pjData.saldoAwal, 10) || 0;
                const subtotalSaldo = saldoAwal + totalIn;
                const saldoAkhir = subtotalSaldo - totalOut;

                const tglHariH = pjData.tanggalJudul || '02 Oktober 2026';
                const tglPekanLalu = typeof pjHitungTanggalPekanLalu === 'function' ? pjHitungTanggalPekanLalu(tglHariH, true) : '25 Sept 2026';

                const elBadgeTgl = document.getElementById('dashPjBadgeTgl');
                const elSaldoAwal = document.getElementById('dashPjSaldoAwal');
                const elLabelTromol = document.getElementById('dashPjLabelTromol');
                const elPemasukan = document.getElementById('dashPjPemasukan');
                const elSubtotal = document.getElementById('dashPjSaldoSubtotal');
                const elPengeluaran = document.getElementById('dashPjPengeluaran');
                const elSaldoAkhir = document.getElementById('dashPjSaldoAkhir');

                const elImam = document.getElementById('dashPjImam');
                const elMuadzin = document.getElementById('dashPjMuadzin');
                const elBilal = document.getElementById('dashPjBilal');
                const elMaklumat = document.getElementById('dashPjMaklumat');
                const elTitimangsa = document.getElementById('dashPjTitimangsa');

                if (elBadgeTgl) elBadgeTgl.innerHTML = `<i class="fas fa-calendar-day mr-1 text-danger"></i> Jum'at, ${tglHariH}`;
                if (elSaldoAwal) elSaldoAwal.textContent = 'Rp ' + pjFormatRupiah(saldoAwal);
                if (elLabelTromol) elLabelTromol.textContent = `2. Pemasukan Tromol (${tglPekanLalu})`;
                if (elPemasukan) elPemasukan.textContent = '+ Rp ' + pjFormatRupiah(totalIn);
                if (elSubtotal) elSubtotal.textContent = 'Rp ' + pjFormatRupiah(subtotalSaldo);
                if (elPengeluaran) elPengeluaran.textContent = '- Rp ' + pjFormatRupiah(totalOut);
                if (elSaldoAkhir) elSaldoAkhir.textContent = 'Rp ' + pjFormatRupiah(saldoAkhir);

                if (elImam) elImam.textContent = pjData.petugas?.imamKhotib || 'Ust. Faiq Rido';
                if (elMuadzin) elMuadzin.textContent = pjData.petugas?.muadzin || 'Ust. Rudy';
                if (elBilal) elBilal.textContent = pjData.petugas?.bilal || 'Ust. Mansur';
                if (elMaklumat) elMaklumat.textContent = pjData.petugas?.maklumat || 'Ust. Fatkhurokhman';
                if (elTitimangsa) elTitimangsa.textContent = pjData.titimangsa || `Simpangan, ${tglHariH}`;
            } catch(e) {
                console.warn('Gagal merender widget pengumuman jumat dashboard:', e);
            }
        }

        function bukaWhatsAppPengumuman() {
            const elText = document.getElementById('pjTextBroadcastWA');
            if (!elText) return;
            const text = elText.value;
            const url = 'https://api.whatsapp.com/send?text=' + encodeURIComponent(text);
            window.open(url, '_blank');
        }

        // Auto render dashboard widget saat dimuat
        document.addEventListener('DOMContentLoaded', function() {
            try {
                renderDashAgendaRutinWidget();
                renderDashPengumumanJumatWidget();
            } catch(e) {}
        });
    