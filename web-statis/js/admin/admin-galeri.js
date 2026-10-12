/**
 * MODUL ADMIN: admin-galeri.js
 * Digital Signage Masjid Jami' Al-Jihad
 * Versi: aljihad-signage-v5.8.45
 */

        // MANAJEMEN GALERI INFORMASI & DOKUMENTASI MASJID
        // =====================================================
        const DEFAULT_GALERI_DATA = [
            {
                id: 1,
                judul: 'Arah Qiblat',
                deskripsi: 'Hasil pengecekkan arah qiblat pada hari Kamis, 16 Juli 2026 Jam: 16:27 WIB',
                gambar: 'image/slides/custom_uploaded.png',
                urutan: 1,
                durasi: 10,
                aktif: true
            },
            {
                id: 2,
                judul: 'Qiblat Sertifikat',
                deskripsi: 'Sertifikasi Gerakan Nasional 1.148K Rasdhul Qiblat',
                gambar: 'image/slides/GkxyYVJO2IdZoU1X6mgSNUcghs0gu1HqVtYlxgYA.png',
                urutan: 2,
                durasi: 10,
                aktif: true
            },
            {
                id: 3,
                judul: 'Sistem Informasi Masjid KEMENAG (SIMAS)',
                deskripsi: 'Surat Keterangan Masjid Al Jihad terdaftar di KEMENAG',
                gambar: 'image/slides/E6Vbbx4rwXUpvmdD8LodQkbK9x82dYzX723Fb386.webp',
                urutan: 3,
                durasi: 10,
                aktif: true
            }
        ];

        let cachedGaleriData = [];
        let galeriUploadedBase64 = null;

        function getGaleriData() {
            try {
                const raw = localStorage.getItem('aljihad_galeri_informasi');
                if (raw) {
                    const parsed = JSON.parse(raw);
                    if (Array.isArray(parsed) && parsed.length > 0) {
                        cachedGaleriData = parsed;
                        return cachedGaleriData;
                    }
                }
            } catch(e) {}
            cachedGaleriData = JSON.parse(JSON.stringify(DEFAULT_GALERI_DATA));
            try {
                localStorage.setItem('aljihad_galeri_informasi', JSON.stringify(cachedGaleriData));
            } catch(e) {}
            return cachedGaleriData;
        }

        function renderGaleriTable() {
            const tbody = document.getElementById('tbodyGaleriInformasi');
            if (!tbody) return;

            const list = getGaleriData();
            list.sort((a, b) => (parseInt(a.urutan, 10) || 0) - (parseInt(b.urutan, 10) || 0));

            if (list.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-4"><i class="fas fa-images mr-1"></i> Belum ada data Galeri Informasi. Silakan klik tombol Tambah.</td></tr>';
                return;
            }

            tbody.innerHTML = list.map((item, idx) => {
                const isAktif = (item.aktif !== false && item.status !== 'nonaktif');
                const badgeStatus = isAktif
                    ? '<span class="badge badge-success px-2 py-1 font-weight-bold"><i class="fas fa-check-circle mr-1"></i> Aktif</span>'
                    : '<span class="badge badge-secondary px-2 py-1 font-weight-bold"><i class="fas fa-eye-slash mr-1"></i> Nonaktif</span>';

                const displaySrc = item.gambar_base64 || item.gambar || '';
                let imgThumb = '';
                if (displaySrc) {
                    imgThumb = `<img src="${escapeHtml(displaySrc)}" class="img-thumbnail" style="max-width: 120px; max-height: 80px; object-fit: contain; background: #fdfdfd; cursor: pointer;" alt="${escapeHtml(item.judul)}" onclick="zoomGambarGaleri('${escapeHtml(displaySrc)}', '${escapeHtml(item.judul)}')">`;
                } else {
                    imgThumb = '<span class="text-muted small"><i class="fas fa-image mr-1"></i> Tanpa gambar</span>';
                }

                return `
                    <tr>
                        <td class="text-center font-weight-bold align-middle">${idx + 1}</td>
                        <td class="text-center align-middle">${imgThumb}</td>
                        <td class="align-middle">
                            <strong class="text-gray-900 d-block mb-1" style="font-size: 1rem;">${escapeHtml(item.judul)}</strong>
                            <small class="text-muted d-block" style="line-height: 1.4;">${escapeHtml(item.deskripsi || '-')}</small>
                        </td>
                        <td class="text-center align-middle font-weight-bold"><span class="badge badge-light border px-2 py-1">${item.urutan || (idx + 1)}</span></td>
                        <td class="text-center align-middle"><span class="badge badge-info px-2 py-1">${item.durasi || 10} detik</span></td>
                        <td class="text-center align-middle">${badgeStatus}</td>
                        <td class="text-center align-middle">
                            <button class="btn btn-warning btn-sm font-weight-bold mr-1" onclick="bukaModalEditGaleri(${item.id})" title="Edit Galeri">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="btn btn-danger btn-sm font-weight-bold" onclick="hapusGaleriInformasi(${item.id})" title="Hapus Galeri">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        function bukaModalTambahGaleri() {
            document.getElementById('modalGaleriTitle').innerHTML = '<i class="fas fa-plus-circle mr-2"></i> Tambah Galeri Informasi';
            document.getElementById('galeriId').value = '';
            document.getElementById('galeriJudul').value = '';
            document.getElementById('galeriDeskripsi').value = '';
            document.getElementById('galeriUrutan').value = (getGaleriData().length + 1);
            document.getElementById('galeriDurasi').value = '10';
            document.getElementById('galeriAktif').value = '1';
            document.getElementById('galeriGambarUrl').value = '';
            document.getElementById('galeriFileInput').value = '';
            galeriUploadedBase64 = null;

            const previewImg = document.getElementById('galeriPreviewImg');
            const placeholder = document.getElementById('galeriPlaceholder');
            previewImg.style.display = 'none';
            placeholder.style.display = 'block';

            $('#modalGaleriInformasi').modal('show');
        }

        function bukaModalEditGaleri(id) {
            const list = getGaleriData();
            const item = list.find(g => String(g.id) === String(id));
            if (!item) return;

            document.getElementById('modalGaleriTitle').innerHTML = '<i class="fas fa-edit mr-2"></i> Edit Galeri Informasi';
            document.getElementById('galeriId').value = item.id;
            document.getElementById('galeriJudul').value = item.judul || '';
            document.getElementById('galeriDeskripsi').value = item.deskripsi || '';
            document.getElementById('galeriUrutan').value = item.urutan || 1;
            document.getElementById('galeriDurasi').value = item.durasi || 10;
            document.getElementById('galeriAktif').value = (item.aktif !== false && item.status !== 'nonaktif') ? '1' : '0';
            const displayImg = item.gambar_base64 || item.gambar || '';
            document.getElementById('galeriGambarUrl').value = (item.gambar && !item.gambar.startsWith('data:image/') && !item.gambar.includes('custom_uploaded')) ? item.gambar : '';
            document.getElementById('galeriFileInput').value = '';
            galeriUploadedBase64 = item.gambar_base64 || null;

            const previewImg = document.getElementById('galeriPreviewImg');
            const placeholder = document.getElementById('galeriPlaceholder');
            if (displayImg) {
                previewImg.src = displayImg;
                previewImg.style.display = 'block';
                placeholder.style.display = 'none';
            } else {
                previewImg.style.display = 'none';
                placeholder.style.display = 'block';
            }

            $('#modalGaleriInformasi').modal('show');
        }

        function handleGaleriImageUpload(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                if (file.size > 10 * 1024 * 1024) {
                    alert('Ukuran berkas terlalu besar! Maksimal 10MB.');
                    input.value = '';
                    return;
                }
                const reader = new FileReader();
                reader.onload = function(e) {
                    const rawBase64 = e.target.result;
                    // Optimasi otomatis gambar ke resolusi ramah TV (maks 1280px) agar memori TV & localStorage tetap aman
                    const img = new Image();
                    img.onload = function() {
                        const MAX_W = 1280;
                        const MAX_H = 1280;
                        let w = img.width;
                        let h = img.height;
                        if (w > MAX_W || h > MAX_H) {
                            if (w > h) {
                                h = Math.round((h * MAX_W) / w);
                                w = MAX_W;
                            } else {
                                w = Math.round((w * MAX_H) / h);
                                h = MAX_H;
                            }
                        }
                        const canvas = document.createElement('canvas');
                        canvas.width = w;
                        canvas.height = h;
                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(img, 0, 0, w, h);
                        galeriUploadedBase64 = canvas.toDataURL('image/jpeg', 0.85);

                        const previewImg = document.getElementById('galeriPreviewImg');
                        const placeholder = document.getElementById('galeriPlaceholder');
                        previewImg.src = galeriUploadedBase64;
                        previewImg.style.display = 'block';
                        placeholder.style.display = 'none';
                    };
                    img.onerror = function() {
                        galeriUploadedBase64 = rawBase64;
                        const previewImg = document.getElementById('galeriPreviewImg');
                        const placeholder = document.getElementById('galeriPlaceholder');
                        previewImg.src = galeriUploadedBase64;
                        previewImg.style.display = 'block';
                        placeholder.style.display = 'none';
                    };
                    img.src = rawBase64;
                };
                reader.readAsDataURL(file);
            }
        }

        function simpanGaleriInformasi(event) {
            if (event) event.preventDefault();

            const idVal = document.getElementById('galeriId').value;
            const judul = document.getElementById('galeriJudul').value.trim();
            const deskripsi = document.getElementById('galeriDeskripsi').value.trim();
            const urutan = parseInt(document.getElementById('galeriUrutan').value, 10) || 1;
            const durasi = parseInt(document.getElementById('galeriDurasi').value, 10) || 10;
            const aktif = (document.getElementById('galeriAktif').value === '1');
            const urlInput = document.getElementById('galeriGambarUrl').value.trim();

            if (!judul) {
                alert('Judul Galeri Informasi wajib diisi!');
                return;
            }

            let list = getGaleriData();

            if (idVal) {
                const index = list.findIndex(g => String(g.id) === String(idVal));
                if (index !== -1) {
                    list[index].judul = judul;
                    list[index].deskripsi = deskripsi;
                    list[index].urutan = urutan;
                    list[index].durasi = durasi;
                    list[index].aktif = aktif;
                    if (galeriUploadedBase64) {
                        list[index].gambar_base64 = galeriUploadedBase64;
                        list[index].gambar = 'image/slides/custom_uploaded.png';
                    } else if (urlInput) {
                        list[index].gambar = urlInput;
                        list[index].gambar_base64 = null;
                    }
                }
            } else {
                const newId = Date.now();
                let b64Val = galeriUploadedBase64 || null;
                let pathVal = urlInput || (b64Val ? 'image/slides/custom_uploaded.png' : 'image/slides/1gdpqFYCyv7Sv0qLDTpyxSjMnknbVEM9OLVOjPM3.png');
                list.push({
                    id: newId,
                    judul: judul,
                    deskripsi: deskripsi,
                    urutan: urutan,
                    durasi: durasi,
                    aktif: aktif,
                    gambar: pathVal,
                    gambar_base64: b64Val
                });
            }

            if (typeof SupabaseDB !== 'undefined' && typeof SupabaseDB.saveSlides === 'function') {
                SupabaseDB.saveSlides(list);
            } else {
                localStorage.setItem('aljihad_galeri_informasi', JSON.stringify(list));
            }

            $('#modalGaleriInformasi').modal('hide');
            renderGaleriTable();
            alert('Data Galeri Informasi berhasil disimpan dan disinkronkan ke Layar TV!');
        }

        function hapusGaleriInformasi(id) {
            const list = getGaleriData();
            const item = list.find(g => String(g.id) === String(id));
            if (!item) return;

            if (confirm(`Apakah Anda yakin ingin menghapus "${item.judul}" dari Galeri Informasi?`)) {
                const filtered = list.filter(g => String(g.id) !== String(id));
                if (typeof SupabaseDB !== 'undefined' && typeof SupabaseDB.saveSlides === 'function') {
                    SupabaseDB.saveSlides(filtered);
                } else {
                    localStorage.setItem('aljihad_galeri_informasi', JSON.stringify(filtered));
                }
                renderGaleriTable();
            }
        }

        function zoomGambarGaleri(src, judul) {
            const modalImg = document.getElementById('zoomGaleriImg');
            const modalTitle = document.getElementById('zoomGaleriTitle');
            if (modalImg && modalTitle) {
                modalImg.src = src;
                modalTitle.textContent = judul || 'Pratinjau Dokumen / Gambar';
                $('#modalZoomGambarGaleri').modal('show');
            }
        }

        // =======================================================