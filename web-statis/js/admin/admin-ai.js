/**
 * MODUL ADMIN: admin-ai.js
 * Digital Signage Masjid Jami' Al-Jihad
 * Versi: aljihad-signage-v5.8.45
 */

        function updateGeminiStatusBadge() {
            const badge = document.getElementById('geminiStatusBadge');
            if (!badge) return;
            const hasKey = window.GeminiAI ? window.GeminiAI.hasApiKey() : false;
            if (hasKey) {
                badge.className = 'badge badge-success px-3 py-1';
                badge.innerHTML = '<i class="fas fa-check-circle mr-1"></i> Gemini AI Siap';
            } else {
                badge.className = 'badge badge-warning text-dark px-3 py-1';
                badge.innerHTML = '<i class="fas fa-exclamation-triangle mr-1"></i> Belum Dikonfigurasi';
            }
        }

        function toggleApiKeyVisibility() {
            const input = document.getElementById('cfgGeminiApiKey');
            const icon = document.getElementById('toggleApiKeyIcon');
            const text = document.getElementById('toggleApiKeyText');
            if (!input) return;
            if (input.type === 'password') {
                input.type = 'text';
                if (icon) icon.className = 'fas fa-eye-slash';
                if (text) text.textContent = 'Sembunyikan';
            } else {
                input.type = 'password';
                if (icon) icon.className = 'fas fa-eye';
                if (text) text.textContent = 'Tampilkan';
            }
        }

        async function ujiKoneksiGeminiAdmin() {
            const key = document.getElementById('cfgGeminiApiKey')?.value?.trim();
            const model = document.getElementById('cfgGeminiModel')?.value || 'gemini-1.5-flash';
            const feedback = document.getElementById('geminiTestFeedback');
            const btn = document.getElementById('btnTestGemini');

            if (!key) {
                alert('Silakan masukkan Google Gemini API Key terlebih dahulu.');
                return;
            }

            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Menguji...';
            }
            if (feedback) {
                feedback.className = 'small text-primary font-weight-bold mb-2 mb-md-0';
                feedback.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Menghubungi Google Gemini API...';
            }

            try {
                const testRes = await window.GeminiAI.testConnection(key, model);
                if (testRes.success) {
                    if (feedback) {
                        feedback.className = 'small text-success font-weight-bold mb-2 mb-md-0';
                        feedback.innerHTML = `<i class="fas fa-check-circle mr-1"></i> ${testRes.message}`;
                    }
                    window.GeminiAI.setApiKey(key);
                    window.GeminiAI.setModel(model);
                    updateGeminiStatusBadge();
                    alert(`Alhamdulillah! Koneksi ke Google Gemini AI (${model}) Berhasil! (Latensi: ${testRes.latencyMs} ms)`);
                } else {
                    if (feedback) {
                        feedback.className = 'small text-danger font-weight-bold mb-2 mb-md-0';
                        feedback.innerHTML = `<i class="fas fa-times-circle mr-1"></i> ${testRes.message}`;
                    }
                    alert(`Uji Koneksi Gagal:\n${testRes.message}`);
                }
            } catch (err) {
                if (feedback) {
                    feedback.className = 'small text-danger font-weight-bold mb-2 mb-md-0';
                    feedback.innerHTML = `<i class="fas fa-times-circle mr-1"></i> Gagal: ${err.message}`;
                }
                alert('Terjadi kesalahan koneksi.');
            } finally {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-plug mr-1"></i> Uji Koneksi AI';
                }
            }
        }

        function bukaModalAiCopywriter(kategori, target) {
            if (kategori && document.getElementById('aiKategori')) {
                document.getElementById('aiKategori').value = kategori;
            }
            if (target && document.getElementById('aiTargetApply')) {
                document.getElementById('aiTargetApply').value = target;
            }
            $('#modalAiCopywriter').modal('show');
        }

        async function prosesGenerateAiAnnouncement() {
            const rawPoints = document.getElementById('aiRawPoints')?.value?.trim();
            const kategori = document.getElementById('aiKategori')?.value || 'Kajian Rutin';
            const masjid = document.getElementById('cfgNamaMasjid')?.value || 'Masjid Jami\' Al-Jihad';
            const loadingBox = document.getElementById('aiLoadingBox');
            const resultBox = document.getElementById('aiResultBox');
            const btnGen = document.getElementById('btnGenerateAi');

            if (!rawPoints) {
                alert('Silakan tuliskan poin-poin atau catatan mentah kegiatan terlebih dahulu.');
                return;
            }

            if (loadingBox) loadingBox.classList.remove('d-none');
            if (resultBox) resultBox.classList.add('d-none');
            if (btnGen) {
                btnGen.disabled = true;
                btnGen.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Menyusun...';
            }

            try {
                const res = await window.GeminiAI.generateAnnouncement(rawPoints, kategori, masjid);
                lastAiResult = res.data;

                if (document.getElementById('aiResJudul')) document.getElementById('aiResJudul').value = res.data.judul || '';
                if (document.getElementById('aiResPemateri')) document.getElementById('aiResPemateri').value = res.data.pemateri || '';
                if (document.getElementById('aiResWaktu')) document.getElementById('aiResWaktu').value = res.data.waktu || '';
                if (document.getElementById('aiResTempat')) document.getElementById('aiResTempat').value = res.data.tempat || '';
                if (document.getElementById('aiResIsi')) document.getElementById('aiResIsi').value = res.data.isi || '';
                if (document.getElementById('aiResRunningText')) document.getElementById('aiResRunningText').value = res.data.running_text || '';

                const badge = document.getElementById('aiResultBadge');
                if (badge) {
                    badge.textContent = res.source === 'gemini_ai' ? `Google Gemini AI (${res.model || 'Flash'})` : 'Fallback Offline Cerdas';
                    badge.className = res.source === 'gemini_ai' ? 'badge badge-success px-2 py-1' : 'badge badge-warning text-dark px-2 py-1';
                }

                if (resultBox) resultBox.classList.remove('d-none');
            } catch (err) {
                alert('Terjadi kesalahan saat menyusun draf: ' + err.message);
            } finally {
                if (loadingBox) loadingBox.classList.add('d-none');
                if (btnGen) {
                    btnGen.disabled = false;
                    btnGen.innerHTML = '<i class="fas fa-robot mr-1"></i> Susun Draf dengan AI';
                }
            }
        }

        function salinDrafAiLengkap() {
            const judul = document.getElementById('aiResJudul')?.value || '';
            const pemateri = document.getElementById('aiResPemateri')?.value || '';
            const waktu = document.getElementById('aiResWaktu')?.value || '';
            const tempat = document.getElementById('aiResTempat')?.value || '';
            const isi = document.getElementById('aiResIsi')?.value || '';
            const running = document.getElementById('aiResRunningText')?.value || '';

            let text = `*${judul}*\n`;
            if (pemateri) text += `Pemateri: ${pemateri}\n`;
            if (waktu) text += `Waktu: ${waktu}\n`;
            if (tempat) text += `Tempat: ${tempat}\n`;
            text += `\n${isi}\n\nTeks Berjalan: ${running}`;

            navigator.clipboard.writeText(text).then(() => {
                alert('Alhamdulillah! Draf pengumuman lengkap berhasil disalin ke clipboard.');
            }).catch(() => {
                alert('Gagal menyalin teks ke clipboard.');
            });
        }

        function terapkanHasilAiKeForm() {
            const target = document.getElementById('aiTargetApply')?.value || 'kajian';
            const judul = document.getElementById('aiResJudul')?.value || '';
            const pemateri = document.getElementById('aiResPemateri')?.value || '';
            const running = document.getElementById('aiResRunningText')?.value || '';

            if (target === 'kajian') {
                if (pemateri && document.getElementById('kajianUstadzNama')) {
                    document.getElementById('kajianUstadzNama').value = pemateri;
                }
                if (judul && document.getElementById('kajianKitabRujukan')) {
                    document.getElementById('kajianKitabRujukan').value = judul;
                }
                if (typeof onKajianFieldChanged === 'function') {
                    onKajianFieldChanged();
                } else if (typeof updateKajianPreview === 'function') {
                    updateKajianPreview();
                }
                switchAdminSection('kajian-sabtu');
                $('#modalAiCopywriter').modal('hide');
                alert('Alhamdulillah! Data pengumuman hasil AI berhasil diterapkan ke Form Pengajian Malam Ahad Pekan Ini.');
            } else if (target && target.startsWith('running_text_page:')) {
                const slug = target.replace('running_text_page:', '');
                const inputRT = document.getElementById(`inputRT_${slug}`);
                if (inputRT && running) {
                    const current = inputRT.value.trim();
                    inputRT.value = current ? `${current} • ${running}` : running;
                    onRunningTextInputChange(slug);
                }
                switchAdminSection('running-text');
                $('#modalAiCopywriter').modal('hide');
                alert(`Alhamdulillah! Teks berjalan hasil AI berhasil diterapkan untuk halaman ${slug}.`);
            } else if (target === 'running_text') {
                const inputUtama = document.getElementById('inputRT_utama');
                if (inputUtama && running) {
                    const current = inputUtama.value.trim();
                    inputUtama.value = current ? `${current} • ${running}` : running;
                    onRunningTextInputChange('utama');
                }
                switchAdminSection('running-text');
                $('#modalAiCopywriter').modal('hide');
                alert('Alhamdulillah! Teks berjalan hasil AI berhasil ditambahkan ke Jadwal Sholat Utama.');
            } else {
                salinDrafAiLengkap();
                $('#modalAiCopywriter').modal('hide');
            }
        }

        // Simpan Susunan Rotasi TV ke Supabase (Khusus Super Admin)