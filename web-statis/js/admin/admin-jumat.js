/**
 * MODUL ADMIN: admin-jumat.js
 * Digital Signage Masjid Jami' Al-Jihad
 * Versi: aljihad-signage-v5.8.46
 */

        // PENGELOLAAN UPLOAD FOTO IMAM & KHOTIB JUMAT
        // =======================================================
        let jumatFotoImamBase64 = null;

        function handleJumatFotoUpload(event) {
            const file = event.target.files && event.target.files[0];
            if (!file) return;

            if (file.size > 8 * 1024 * 1024) {
                alert('Ukuran berkas foto terlalu besar! Maksimal 8MB.');
                event.target.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                const img = new Image();
                img.onload = function() {
                    // Kompresi otomatis hemat kuota (maks 360x480 px, kualitas 0.75, ukuran <30KB)
                    const canvas = document.createElement('canvas');
                    let w = img.width;
                    let h = img.height;
                    const maxW = 360;
                    const maxH = 480;
                    if (w > maxW || h > maxH) {
                        if (w / h > maxW / maxH) {
                            h = Math.round((h * maxW) / w);
                            w = maxW;
                        } else {
                            w = Math.round((w * maxH) / h);
                            h = maxH;
                        }
                    }
                    canvas.width = w;
                    canvas.height = h;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, w, h);

                    const compressed = canvas.toDataURL('image/jpeg', 0.75);
                    jumatFotoImamBase64 = compressed;

                    const prevForm = document.getElementById('jumatFotoPreviewImg');
                    const prevTv = document.getElementById('prevJumatImamPhoto');
                    if (prevForm) prevForm.src = compressed;
                    if (prevTv) prevTv.src = compressed;

                    const badge = document.getElementById('jumatFotoBadge');
                    if (badge) {
                        badge.className = 'badge badge-primary position-absolute';
                        badge.textContent = 'Foto Baru';
                    }

                    const urlInput = document.getElementById('jumatFotoUrlInput');
                    if (urlInput) urlInput.value = '';
                    triggerAutoSavePetugasJumat();
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }

        function handleJumatFotoUrlChange(url) {
            const clean = (url || '').trim();
            if (!clean) return;
            jumatFotoImamBase64 = null;

            const prevForm = document.getElementById('jumatFotoPreviewImg');
            const prevTv = document.getElementById('prevJumatImamPhoto');
            if (prevForm) prevForm.src = clean;
            if (prevTv) prevTv.src = clean;

            const badge = document.getElementById('jumatFotoBadge');
            if (badge) {
                badge.className = 'badge badge-info position-absolute';
                badge.textContent = 'URL Web';
            }
            triggerAutoSavePetugasJumat();
        }

        function resetJumatFoto() {
            jumatFotoImamBase64 = '';
            const fileInput = document.getElementById('jumatFotoFile');
            if (fileInput) fileInput.value = '';
            const urlInput = document.getElementById('jumatFotoUrlInput');
            if (urlInput) urlInput.value = '';

            const defaultSrc = 'image/display/default_imam.jpg';
            const prevForm = document.getElementById('jumatFotoPreviewImg');
            const prevTv = document.getElementById('prevJumatImamPhoto');
            if (prevForm) prevForm.src = defaultSrc;
            if (prevTv) prevTv.src = defaultSrc;

            const badge = document.getElementById('jumatFotoBadge');
            if (badge) {
                badge.className = 'badge badge-secondary position-absolute';
                badge.textContent = 'Default';
            }
            triggerAutoSavePetugasJumat();
        }

        // Simpan Petugas Sholat Jum'at & Durasi Sholat Jum'at Khusus Pekan Ini
                function collectPetugasJumatDraft() {
            const read = id => document.getElementById(id)?.value?.trim() || '';
            const combined = read('jumatKhotib').split('/').map(value => value.trim());
            const existingPhoto = localStorage.getItem('cached_jumat_foto_imam') || 'image/display/default_imam.jpg';
            return { tanggal: read('jumatTanggal'), khatib: combined[0], imam: combined[1] || read('jumatImam') || combined[0],
                muadzin: read('jumatMuadzin'), muadzin2: read('jumatMuadzin2'), bilal: read('jumatBilal'),
                maklumat: read('jumatMaklumat'), waktu: read('jumatWaktu') || '11:45', durasi: Number(read('jumatDurasiInput') || 50),
                foto_imam: jumatFotoImamBase64 !== null ? (jumatFotoImamBase64 || 'image/display/default_imam.jpg') : (read('jumatFotoUrlInput') || existingPhoto) };
        }

        function setPetugasJumatStatus(text) {
            const status = document.getElementById('jumatSaveStatus');
            if (status) status.textContent = text;
        }

        function triggerAutoSavePetugasJumat() {
            try { localStorage.setItem('draft_petugas_jumat', JSON.stringify(collectPetugasJumatDraft())); } catch (error) {}
            setPetugasJumatStatus('Draft — menunggu penyimpanan…');
            clearTimeout(triggerAutoSavePetugasJumat.timer);
            triggerAutoSavePetugasJumat.timer = setTimeout(() => simpanPetugasJumat(true), 700);
        }

        function restorePetugasJumatDraft() {
            try {
                const raw = localStorage.getItem('draft_petugas_jumat');
                if (!raw) return;
                const draft = JSON.parse(raw);
                const fields = { jumatTanggal: 'tanggal', jumatKhotib: 'khatib', jumatImam: 'imam', jumatMuadzin: 'muadzin',
                    jumatMuadzin2: 'muadzin2', jumatBilal: 'bilal', jumatMaklumat: 'maklumat', jumatWaktu: 'waktu', jumatDurasiInput: 'durasi' };
                Object.entries(fields).forEach(([id, key]) => { const el = document.getElementById(id); if (el && draft[key] !== undefined) el.value = draft[key]; });
                if (draft.foto_imam) {
                    jumatFotoImamBase64 = draft.foto_imam.startsWith('data:') ? draft.foto_imam : null;
                    const url = document.getElementById('jumatFotoUrlInput'); if (url) url.value = jumatFotoImamBase64 ? '' : draft.foto_imam;
                    const preview = document.getElementById('jumatFotoPreviewImg'); if (preview) preview.src = draft.foto_imam;
                }
                setPetugasJumatStatus('Draft dipulihkan — belum tersimpan di cloud.');
            } catch (error) { console.warn('Draft Jumat tidak dapat dipulihkan:', error); }
        }

        async function simpanPetugasJumat(silent = false) {
            clearTimeout(triggerAutoSavePetugasJumat.timer);
            const snapshot = collectPetugasJumatDraft();
            const serialized = JSON.stringify(snapshot);
            try { localStorage.setItem('draft_petugas_jumat', serialized); } catch (error) {}
            const sequence = (simpanPetugasJumat.sequence || 0) + 1;
            simpanPetugasJumat.sequence = sequence;
            setPetugasJumatStatus('Menyimpan ke cloud…');
            const save = async () => {
                if (!window.SupabaseDB || typeof SupabaseDB.saveSholatJumat !== 'function') return { success: false, error: 'Layanan cloud belum siap' };
                return SupabaseDB.saveSholatJumat(snapshot);
            };
            simpanPetugasJumat.queue = (simpanPetugasJumat.queue || Promise.resolve()).then(save, save);
            let result;
            try { result = await simpanPetugasJumat.queue; } catch (error) { result = { success: false, error: error.message }; }
            const confirmed = result?.success === true && result?.cloudConfirmed === true;
            const unchanged = JSON.stringify(collectPetugasJumatDraft()) === serialized;
            if (sequence === simpanPetugasJumat.sequence && unchanged) {
                setPetugasJumatStatus(confirmed ? 'Tersimpan di cloud — tampilan TV menunggu konfirmasi.' : 'Belum terkonfirmasi di cloud. Draft tetap tersedia. ' + (result?.error || ''));
                if (confirmed) {
                    try {
                        if (localStorage.getItem('draft_petugas_jumat') === serialized) localStorage.removeItem('draft_petugas_jumat');
                        localStorage.setItem('cached_jumat_foto_imam', snapshot.foto_imam);
                    } catch (error) {}
                }
            }
            if (!silent && !confirmed) alert(result?.error || 'Penyimpanan belum terkonfirmasi. Draft tetap tersedia.');
            return confirmed;
        }



        // Live input listeners untuk form Petugas Jum'at
        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('jumatKhotib')?.addEventListener('input', (e) => {
                if (document.getElementById('prevJumatKhotib')) document.getElementById('prevJumatKhotib').textContent = e.target.value || '-';
            });
            document.getElementById('jumatMuadzin')?.addEventListener('input', (e) => {
                if (document.getElementById('prevJumatMuadzin')) document.getElementById('prevJumatMuadzin').textContent = e.target.value || '-';
            });
            document.getElementById('jumatBilal')?.addEventListener('input', (e) => {
                if (document.getElementById('prevJumatBilal')) document.getElementById('prevJumatBilal').textContent = e.target.value || '-';
            });
            document.getElementById('jumatMaklumat')?.addEventListener('input', (e) => {
                if (document.getElementById('prevJumatMaklumat')) document.getElementById('prevJumatMaklumat').textContent = e.target.value || '-';
            });
            document.getElementById('jumatTanggal')?.addEventListener('change', (e) => {
                if (document.getElementById('prevJumatTanggal') && e.target.value) {
                    const d = new Date(e.target.value);
                    document.getElementById('prevJumatTanggal').textContent = d.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
                }
            });
        });

        // =======================================================
        // FITUR AUTO GENERATE FLYER WHATSAPP PETUGAS JUM'AT
        // =======================================================
        function drawCanvasRoundRect(ctx, x, y, width, height, radius, fill, stroke) {
            ctx.save();
            ctx.beginPath();
            ctx.moveTo(x + radius, y);
            ctx.lineTo(x + width - radius, y);
            ctx.quadraticCurveTo(x + width, y, x + width, y + radius);
            ctx.lineTo(x + width, y + height - radius);
            ctx.quadraticCurveTo(x + width, y + height, x + width - radius, y + height);
            ctx.lineTo(x + radius, y + height);
            ctx.quadraticCurveTo(x, y + height, x, y + height - radius);
            ctx.lineTo(x, y + radius);
            ctx.quadraticCurveTo(x, y, x + radius, y);
            ctx.closePath();
            if (fill) ctx.fill();
            if (stroke) ctx.stroke();
            ctx.restore();
        }

        // =======================================================
        // FITUR AUTO GENERATE FLYER WHATSAPP PETUGAS JUM'AT
        // =======================================================
        let cachedFlyerBgImg = null;
        let cachedFlyerLogoImg = null;
        let flyerUserEditedText = false;

        function onFlyerWaTextEdited() {
            flyerUserEditedText = true;
            const badge = document.getElementById('badgeWaTextStatus');
            if (badge) {
                badge.className = 'badge badge-warning text-dark font-weight-bold ml-1';
                badge.innerHTML = '<i class="fas fa-pen mr-1"></i> Teks Kustom';
            }
        }

        function resetTeksFlyerWA() {
            flyerUserEditedText = false;
            const badge = document.getElementById('badgeWaTextStatus');
            if (badge) {
                badge.className = 'badge badge-success font-weight-bold ml-1';
                badge.innerHTML = '<i class="fas fa-magic mr-1"></i> Otomatis';
            }
            const tglVal = document.getElementById('jumatTanggal')?.value || new Date().toISOString().split('T')[0];
            const khotibImam = document.getElementById('jumatKhotib')?.value.trim() || 'Ust. Faiq Rido';
            const muadzin = document.getElementById('jumatMuadzin')?.value.trim() || 'Ust. Rudy';
            const bilal = document.getElementById('jumatBilal')?.value.trim() || 'Ust. Mansur';
            const maklumat = document.getElementById('jumatMaklumat')?.value.trim() || 'Ust. Fatkhurokhman';
            const jumatDur = parseInt(document.getElementById('jumatDurasiInput')?.value) || 50;
            let tglFormatted = 'Jum\'at, ' + tglVal;
            try {
                const parts = tglVal.split('-');
                if (parts.length === 3) {
                    const d = new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, parseInt(parts[2]));
                    tglFormatted = d.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
                }
            } catch (e) {}
            updateFlyerWaBroadcastText(tglFormatted, khotibImam, muadzin, bilal, maklumat, jumatDur, true);
        }

        async function renderFlyerJumatCanvas() {
            const canvas = document.getElementById('flyerCanvas');
            const previewImg = document.getElementById('flyerPreviewImg');
            const spinner = document.getElementById('flyerLoadingSpinner');
            if (!canvas) return;

            // Tampilkan spinner loading secara presisi (inline style)
            if (spinner) spinner.style.display = 'flex';

            // Resolusi default tajam & tidak pecah: 1080 x 1620 px (aspek rasio 2:3 sesuai latar belakang)
            const resSelect = document.getElementById('flyerResolutionSelect');
            const targetW = resSelect ? (parseInt(resSelect.value) || 1080) : 1080;
            const targetH = Math.round(targetW * 1.5); // 1080x1620, 1440x2160, atau 720x1080

            canvas.width = targetW;
            canvas.height = targetH;
            const ctx = canvas.getContext('2d');

            // Skalakan canvas agar rendering gambar tajam, cepat, dan presisi di segala resolusi
            const scale = targetW / 1080;
            ctx.save();
            ctx.scale(scale, scale);

            // Ambil data terbaru dari form input
            const tglVal = document.getElementById('jumatTanggal')?.value || new Date().toISOString().split('T')[0];
            const khotibImam = document.getElementById('jumatKhotib')?.value.trim() || 'Ust. Faiq Rido';
            const muadzin = document.getElementById('jumatMuadzin')?.value.trim() || 'Ust. Rudy';
            const bilal = document.getElementById('jumatBilal')?.value.trim() || 'Ust. Mansur';
            const maklumat = document.getElementById('jumatMaklumat')?.value.trim() || 'Ust. Fatkhurokhman';
            const jumatDur = parseInt(document.getElementById('jumatDurasiInput')?.value) || 50;

            // Format tanggal Indonesia
            let tglFormatted = 'Jum\'at, ' + tglVal;
            try {
                const parts = tglVal.split('-');
                if (parts.length === 3) {
                    const d = new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, parseInt(parts[2]));
                    tglFormatted = d.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
                }
            } catch (e) {}

            // 1. LATAR BELAKANG GAMBAR 1 (ISLAMIC ARCH EMERALD GOLD FRAME)
            if (!cachedFlyerBgImg) {
                await new Promise((resolve) => {
                    let resolved = false;
                    const done = () => {
                        if (!resolved) {
                            resolved = true;
                            resolve();
                        }
                    };
                    const timer = setTimeout(done, 1200);

                    const bgImg = new Image();
                    bgImg.crossOrigin = 'anonymous';
                    bgImg.onload = () => {
                        clearTimeout(timer);
                        cachedFlyerBgImg = bgImg;
                        done();
                    };
                    bgImg.onerror = () => {
                        clearTimeout(timer);
                        done();
                    };
                    bgImg.src = 'img/bg-flyer-jumat.jpg';
                });
            }

            if (cachedFlyerBgImg && cachedFlyerBgImg.complete && cachedFlyerBgImg.naturalWidth > 0) {
                ctx.drawImage(cachedFlyerBgImg, 0, 0, 1080, 1620);
            } else {
                // Fallback jika gambar latar belum termuat: Radial Emerald Green Mewah
                const bgGrad = ctx.createRadialGradient(540, 810, 80, 540, 810, 850);
                bgGrad.addColorStop(0, '#0a4226');
                bgGrad.addColorStop(0.55, '#052917');
                bgGrad.addColorStop(1, '#01150b');
                ctx.fillStyle = bgGrad;
                ctx.fillRect(0, 0, 1080, 1620);
            }

            // 2. LOGO MASJID (Dengan image cache & safety timeout)
            if (!cachedFlyerLogoImg) {
                await new Promise((resolve) => {
                    let resolved = false;
                    const done = () => {
                        if (!resolved) {
                            resolved = true;
                            resolve();
                        }
                    };
                    const timer = setTimeout(done, 800);

                    const logoImg = new Image();
                    logoImg.crossOrigin = 'anonymous';
                    logoImg.onload = () => {
                        clearTimeout(timer);
                        cachedFlyerLogoImg = logoImg;
                        done();
                    };
                    logoImg.onerror = () => {
                        clearTimeout(timer);
                        done();
                    };
                    logoImg.src = 'img/logo-aljihad-circle.png';
                });
            }

            const logoCenterY = 236;
            const logoRadius = 50;

            if (cachedFlyerLogoImg && cachedFlyerLogoImg.complete && cachedFlyerLogoImg.naturalWidth > 0) {
                ctx.save();
                ctx.beginPath();
                ctx.arc(540, logoCenterY, logoRadius, 0, Math.PI * 2);
                ctx.closePath();
                ctx.clip();
                ctx.drawImage(cachedFlyerLogoImg, 540 - logoRadius, logoCenterY - logoRadius, logoRadius * 2, logoRadius * 2);
                ctx.restore();

                // Cincin emas logo (Proporsional & Lebih Mewah)
                ctx.save();
                ctx.beginPath();
                ctx.arc(540, logoCenterY, logoRadius + 1, 0, Math.PI * 2);
                ctx.strokeStyle = '#FFD700';
                ctx.lineWidth = 3;
                ctx.shadowColor = 'rgba(255, 215, 0, 0.65)';
                ctx.shadowBlur = 12;
                ctx.stroke();
                ctx.restore();
            } else {
                ctx.save();
                ctx.fillStyle = '#064e3b';
                ctx.beginPath();
                ctx.arc(540, logoCenterY, logoRadius, 0, Math.PI * 2);
                ctx.fill();
                ctx.strokeStyle = '#FFD700';
                ctx.lineWidth = 3;
                ctx.stroke();
                ctx.fillStyle = '#FFD700';
                ctx.font = '46px "Segoe UI Symbol", sans-serif';
                ctx.fillText('🕌', 540, logoCenterY + 16);
                ctx.restore();
            }

            // 3. KALIGRAFI BISMILLAH & ASSALAMU'ALAIKUM (DI DALAM FRAME AREA HIJAU, DIPERBESAR & DITURUNKAN SEDIKIT)
            ctx.textAlign = 'center';
            ctx.fillStyle = '#FFD700';
            ctx.font = 'bold 42px "Traditional Arabic", "Amiri", "Scheherazade", "Segoe UI", serif';
            ctx.shadowColor = 'rgba(0, 0, 0, 0.95)';
            ctx.shadowBlur = 8;
            ctx.fillText('بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ', 540, 338);

            // Teks Arab Assalamu'alaikum Lengkap di Bawah Bismillah (Font Diperbesar & Diturunkan Sedikit)
            ctx.fillStyle = '#FFF2A8';
            ctx.font = 'bold 33px "Traditional Arabic", "Amiri", "Scheherazade", "Segoe UI", serif';
            ctx.shadowColor = 'rgba(0, 0, 0, 0.95)';
            ctx.shadowBlur = 8;
            ctx.fillText('السَّلَامُ عَلَيْكُمْ وَرَحْمَةُ اللَّهِ وَبَرَكَاتُهُ', 540, 382);
            ctx.shadowBlur = 0;

            // 4. NAMA MASJID & ALAMAT (DITURUNKAN AGAR TEPAT CENTER DI ANTARA TEKS ARAB & ADAB KHUTBAH)
            ctx.textAlign = 'center';
            ctx.fillStyle = '#FFFFFF';
            ctx.font = '800 32px "Poppins", "Segoe UI", sans-serif';
            ctx.shadowColor = 'rgba(0, 0, 0, 0.9)';
            ctx.shadowBlur = 8;
            ctx.fillText("MASJID JAMI' AL-JIHAD", 540, 457);

            ctx.fillStyle = '#A7F3D0';
            ctx.font = '600 16px "Poppins", "Segoe UI", sans-serif';
            ctx.fillText("Perumahan Graha Asri, Simpangan, Cikarang Utara - Bekasi", 540, 484);
            ctx.shadowBlur = 0;

            // 5. PITA / KAPSUL JUDUL JADWAL
            const ribbonGrad = ctx.createLinearGradient(250, 511, 830, 511);
            ribbonGrad.addColorStop(0, '#D4AF37');
            ribbonGrad.addColorStop(0.5, '#FFD700');
            ribbonGrad.addColorStop(1, '#B4821E');
            ctx.fillStyle = ribbonGrad;
            drawCanvasRoundRect(ctx, 250, 511, 580, 44, 22, true, false);

            ctx.fillStyle = '#022413';
            ctx.font = '900 19px "Poppins", "Segoe UI", sans-serif';
            ctx.fillText('★ JADWAL PETUGAS SHOLAT JUM\'AT ★', 540, 541);

            // 6. TANGGAL & WAKTU SHOLAT
            ctx.fillStyle = '#FFD700';
            ctx.font = '800 32px "Poppins", "Segoe UI", sans-serif';
            ctx.shadowColor = 'rgba(0, 0, 0, 0.9)';
            ctx.shadowBlur = 8;
            ctx.fillText(tglFormatted, 540, 593);
            ctx.shadowBlur = 0;

            ctx.fillStyle = '#E2E8F0';
            ctx.font = '600 19px "Poppins", "Segoe UI", sans-serif';
            ctx.fillText('⏰ Waktu Khutbah & Sholat: 11:45 WIB', 540, 625);

            // 7. EMPAT PETUGAS RESMI (RATA TENGAH, TANPA KOTAK, TEKS BESAR & TAJAM)
            const officers = [
                { role: '🎙️  KHATIB & IMAM SHOLAT', name: khotibImam },
                { role: '📢  MUADZIN', name: muadzin },
                { role: '🤲  BILAL', name: bilal || '-' },
                { role: '📜  PEMBACA MAKLUMAT', name: maklumat || '-' }
            ];

            const officerStartY = 682;
            const officerStep = 108;

            officers.forEach((off, idx) => {
                const yBase = officerStartY + idx * officerStep;

                // Peran Petugas (Rata Tengah, Emas Cerah Mewah, Font Diperbesar)
                ctx.textAlign = 'center';
                ctx.fillStyle = '#FFD700';
                ctx.font = '800 20px "Poppins", "Segoe UI", sans-serif';
                ctx.shadowColor = 'rgba(0, 0, 0, 0.7)';
                ctx.shadowBlur = 5;
                ctx.fillText(off.role, 540, yBase);
                ctx.shadowBlur = 0;

                // Nama Petugas (Rata Tengah, Putih Bersih, Font Diperbesar & Jelas)
                ctx.fillStyle = '#FFFFFF';
                ctx.font = '800 34px "Poppins", "Segoe UI", sans-serif';
                ctx.shadowColor = 'rgba(0, 0, 0, 0.9)';
                ctx.shadowBlur = 8;
                let displayName = off.name;
                if (ctx.measureText(displayName).width > 840) {
                    ctx.font = '800 28px "Poppins", "Segoe UI", sans-serif';
                }
                ctx.fillText(displayName, 540, yBase + 40);
                ctx.shadowBlur = 0;

                // Garis aksen emas halus antar petugas (tanpa kotak)
                if (idx < officers.length - 1) {
                    const divY = yBase + 64;
                    const divGrad = ctx.createLinearGradient(300, divY, 780, divY);
                    divGrad.addColorStop(0, 'rgba(212, 175, 55, 0)');
                    divGrad.addColorStop(0.5, 'rgba(212, 175, 55, 0.35)');
                    divGrad.addColorStop(1, 'rgba(212, 175, 55, 0)');
                    ctx.strokeStyle = divGrad;
                    ctx.lineWidth = 1.2;
                    ctx.beginPath();
                    ctx.moveTo(300, divY);
                    ctx.lineTo(780, divY);
                    ctx.stroke();
                }
            });

            // 8. PLAKAT ADAB KHUTBAH JUM'AT (POSISI KOTAK SUDAH BENAR, DIBUAT FULL TRANSPARAN TANPA KOTAK)
            const hadithY = 1100;

            // Judul Adab Khutbah (Teks Emas Mewah dengan Shadow Tajam)
            ctx.textAlign = 'center';
            ctx.fillStyle = '#FFD700';
            ctx.font = '700 17px "Poppins", "Segoe UI", sans-serif';
            ctx.shadowColor = 'rgba(0, 0, 0, 0.95)';
            ctx.shadowBlur = 8;
            ctx.fillText('✨ Adab Khutbah: Diam, Dengarkan & Simak Khutbah', 540, hadithY + 34);

            // Sabda Rasulullah
            ctx.fillStyle = '#F0DEAA';
            ctx.font = '600 19px "Amiri", "Traditional Arabic", "Poppins", serif';
            ctx.fillText('Rasulullah ﷺ bersabda:', 540, hadithY + 76);

            // Teks Hadits (Putih Bersih Kontras Tinggi)
            ctx.fillStyle = '#FFFFFF';
            ctx.font = 'italic 500 21px "Poppins", "Segoe UI", sans-serif';
            ctx.fillText('"Jika engkau berkata kepada sahabatmu pada hari Jum\'at: \'Diamlah!\'', 540, hadithY + 118);
            ctx.fillText('ketika imam sedang berkhutbah, maka sungguh engkau telah berbuat sia-sia."', 540, hadithY + 154);

            // Sumber Riwayat
            ctx.fillStyle = '#FFD700';
            ctx.font = '700 17px "Poppins", "Segoe UI", sans-serif';
            ctx.fillText('(HR. Bukhari no. 934 & Muslim no. 851)', 540, hadithY + 195);
            ctx.shadowBlur = 0;

            // 9. FOOTER RESMI (2 BARIS SESUAI PERMINTAAN USER)
            ctx.strokeStyle = 'rgba(212, 175, 55, 0.45)';
            ctx.lineWidth = 1.2;
            ctx.beginPath();
            ctx.moveTo(180, 1400);
            ctx.lineTo(900, 1400);
            ctx.stroke();

            ctx.textAlign = 'center';
            ctx.shadowColor = 'rgba(0, 0, 0, 0.95)';
            ctx.shadowBlur = 6;
            // Baris 1: Diterbitkan oleh DKM Masjid Jami' Al Jihad
            ctx.fillStyle = '#FFFFFF';
            ctx.font = '700 17px "Poppins", "Segoe UI", sans-serif';
            ctx.fillText("Diterbitkan oleh DKM Masjid Jami' Al Jihad", 540, 1436);

            // Baris 2: Sistem Informasi Digital (http://digitalaljihad.my.id)
            ctx.fillStyle = '#A7F3D0';
            ctx.font = '600 15px "Poppins", "Segoe UI", sans-serif';
            ctx.fillText('Sistem Informasi Digital (http://digitalaljihad.my.id)', 540, 1466);
            ctx.shadowBlur = 0;

            // Restore canvas transformation
            ctx.restore();

            // Update preview image di modal
            try {
                const dataUrl = canvas.toDataURL('image/jpeg', 0.95);
                if (previewImg) previewImg.src = dataUrl;
            } catch (e) {
                console.warn('Canvas toDataURL fallback:', e);
            }

            // Sembunyikan spinner loading secara instan & tuntas
            if (spinner) spinner.style.display = 'none';

            // Update teks WhatsApp Broadcast (hanya jika belum diedit manual oleh user)
            updateFlyerWaBroadcastText(tglFormatted, khotibImam, muadzin, bilal, maklumat, jumatDur);
        }

        function updateFlyerWaBroadcastText(tglFormatted, khotibImam, muadzin, bilal, maklumat, jumatDur, force = false) {
            const ta = document.getElementById('flyerWaBroadcastText');
            if (!ta) return;
            if (flyerUserEditedText && !force) {
                return; // Jangan ditimpa jika sudah diedit manual oleh petugas
            }
            const waText = 
`🕌 *JADWAL PETUGAS SHOLAT JUM'AT* 🕌
*MASJID JAMI' AL-JIHAD (GRAHA ASRI)*
📍 Simpangan, Cikarang Utara, Bekasi

_Assalamu'alaikum Warahmatullahi Wabarakatuh_

📅 *Hari / Tanggal:* ${tglFormatted}
⏰ *Waktu Khutbah & Sholat:* 11:45 WIB

━━━━━━━━━━━━━━━━━━━━━━
📋 *SUSUNAN PETUGAS PEKAN INI:*
🎙️ *Khatib & Imam:* ${khotibImam}
📢 *Muadzin:* ${muadzin}
🤲 *Bilal:* ${bilal || '-'}
📜 *Pembaca Maklumat:* ${maklumat || '-'}
━━━━━━━━━━━━━━━━━━━━━━

✨ *Adab Khutbah Jum'at:*
_Rasulullah ﷺ bersabda:_
_"Jika engkau berkata kepada sahabatmu pada hari Jum'at: 'Diamlah!' ketika imam sedang berkhutbah, maka sungguh engkau telah berbuat sia-sia."_ (HR. Bukhari no. 934 & Muslim no. 851)

Mari hadir lebih awal, luruskan & rapatkan shaf, serta nonaktifkan nada dering handphone demi kekhusyukan ibadah.

🌐 *Website Resmi Masjid:* https://digitalaljihad.my.id`;

            ta.value = waText;
        }

        async function bukaModalFlyerJumat(isAuto = false) {
            $('#modalFlyerJumat').modal('show');
            await renderFlyerJumatCanvas();
        }

        function downloadFlyerJumat(format = 'jpg', btnEl = null) {
            const canvas = document.getElementById('flyerCanvas');
            if (!canvas) return;

            let origBtnHtml = '';
            if (btnEl) {
                origBtnHtml = btnEl.innerHTML;
                btnEl.disabled = true;
                btnEl.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Mengunduh...';
            }

            const tglVal = document.getElementById('jumatTanggal')?.value || 'terkini';
            const isJpeg = (format === 'jpg' || format === 'jpeg');
            const mime = isJpeg ? 'image/jpeg' : 'image/png';
            const ext = isJpeg ? 'jpg' : 'png';
            const curW = canvas.width || 1080;
            const resTag = (curW >= 1440) ? '2K' : ((curW >= 1080) ? 'UltraHD' : '720p');
            const filename = `Flyer-Petugas-Jumat-AlJihad-${tglVal}-${resTag}.${ext}`;

            const finishDownload = () => {
                if (btnEl) {
                    btnEl.disabled = false;
                    btnEl.innerHTML = origBtnHtml;
                }
            };

            if (canvas.toBlob) {
                canvas.toBlob((blob) => {
                    if (!blob) {
                        const link = document.createElement('a');
                        link.download = filename;
                        link.href = canvas.toDataURL(mime, isJpeg ? 0.95 : undefined);
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                        finishDownload();
                        return;
                    }
                    const url = URL.createObjectURL(blob);
                    const link = document.createElement('a');
                    link.download = filename;
                    link.href = url;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    setTimeout(() => {
                        URL.revokeObjectURL(url);
                        finishDownload();
                    }, 800);
                }, mime, isJpeg ? 0.95 : undefined);
            } else {
                const link = document.createElement('a');
                link.download = filename;
                link.href = canvas.toDataURL(mime, isJpeg ? 0.95 : undefined);
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                finishDownload();
            }
        }

        async function downloadFlyerJumatLangsung(btnEl = null) {
            let origHtml = '';
            if (btnEl) {
                origHtml = btnEl.innerHTML;
                btnEl.disabled = true;
                btnEl.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Mengunduh...';
            }
            await renderFlyerJumatCanvas();
            downloadFlyerJumat('jpg', btnEl);
            if (btnEl) {
                setTimeout(() => {
                    btnEl.disabled = false;
                    btnEl.innerHTML = origHtml;
                }, 1000);
            }
        }

        function salinTeksFlyerWA() {
            const ta = document.getElementById('flyerWaBroadcastText');
            if (!ta) return;
            navigator.clipboard.writeText(ta.value).then(() => {
                alert('Teks pengumuman WhatsApp berhasil disalin ke clipboard! Silakan paste (tempel) di grup WhatsApp.');
            }).catch(() => {
                ta.select();
                document.execCommand('copy');
                alert('Teks pengumuman WhatsApp berhasil disalin!');
            });
        }

        async function shareFlyerWhatsApp() {
            const canvas = document.getElementById('flyerCanvas');
            const ta = document.getElementById('flyerWaBroadcastText');
            const textToShare = ta ? ta.value : '';

            // Coba Web Share API (cocok untuk HP / browser modern yang mendukung share file gambar langsung)
            if (navigator.canShare && canvas && canvas.toBlob) {
                try {
                    const blob = await new Promise(r => canvas.toBlob(r, 'image/jpeg', 0.92));
                    if (blob) {
                        const file = new File([blob], 'Flyer-Petugas-Jumat-AlJihad.jpg', { type: 'image/jpeg' });
                        if (navigator.canShare({ files: [file] })) {
                            await navigator.share({
                                title: "Jadwal Petugas Sholat Jum'at Masjid Jami' Al-Jihad",
                                text: textToShare,
                                files: [file]
                            });
                            return;
                        }
                    }
                } catch (e) {
                    console.log('Web share ditutup/dibatalkan:', e);
                }
            }

            // Fallback: Otomatis salin teks pengumuman dan buka WhatsApp
            salinTeksFlyerWA();
            const waUrl = 'https://api.whatsapp.com/send?text=' + encodeURIComponent(textToShare);
            window.open(waUrl, '_blank');
        }

        // =======================================================