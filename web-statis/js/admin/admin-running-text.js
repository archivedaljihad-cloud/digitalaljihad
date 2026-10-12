/**
 * MODUL ADMIN: admin-running-text.js
 * Digital Signage Masjid Jami' Al-Jihad
 * Versi: aljihad-signage-v5.8.45
 */

        // MANAJEMEN TEKS BERJALAN MULTI-HALAMAN DISPLAY TV
        // =======================================================
        let cachedRunningTextPages = {};

        function renderRunningTextPagesPanel(pagesMapping) {
            const container = document.getElementById('accordionRunningPages');
            if (!container) return;

            let mapping = pagesMapping || {};
            if (typeof mapping === 'string') {
                try { mapping = JSON.parse(mapping); } catch (e) { mapping = {}; }
            }
            cachedRunningTextPages = mapping;

            const iconMap = {
                'utama': 'fas fa-mosque',
                'keuangan': 'fas fa-wallet',
                'jumat': 'fas fa-user-tie',
                'pengumuman': 'fas fa-bullhorn',
                'keuangan-summary': 'fas fa-chart-pie',
                'qris': 'fas fa-qrcode',
                'slide': 'fas fa-images',
                'ambulance': 'fas fa-ambulance',
                'infaq': 'fas fa-hand-holding-heart',
                'hikmah': 'fas fa-book-open',
                'qurban': 'fas fa-drum',
                'yasin': 'fas fa-moon',
                'live-mekah': 'fas fa-kaaba',
                'live-madinah': 'fas fa-star-and-crescent',
                'live-mimbar': 'fas fa-video',
                'idul-fitri': 'fas fa-bullhorn',
                'idul-adha': 'fas fa-bullhorn',
                'ramadhan': 'fas fa-star-and-crescent',
                'kajian': 'fas fa-graduation-cap',
                'kas-jumat': 'fas fa-hand-holding-usd'
            };

            container.innerHTML = MASTER_ROTATION_PAGES.map((page, idx) => {
                const slug = getCanonicalSlideKey(page.url);
                const collapseId = `collapseRT_${slug}`;
                const defaultText = (typeof SupabaseDB !== 'undefined' && SupabaseDB.DEFAULT_RUNNING_TEXTS && SupabaseDB.DEFAULT_RUNNING_TEXTS[slug]) 
                    ? SupabaseDB.DEFAULT_RUNNING_TEXTS[slug] 
                    : "🌙 \"Luruskan dan rapatkan shaf saat sholat berjamaah.\" (HR. Bukhari & Muslim) • Jagalah kebersihan dan kesucian masjid";

                const currentVal = mapping[slug] || mapping[slug + '-embed'] || mapping['/' + slug + '-embed'] || mapping[page.url] || defaultText;
                const isCustom = (mapping[slug] || mapping[slug + '-embed'] || mapping['/' + slug + '-embed']) && (currentVal.trim() !== defaultText.trim());
                const icon = iconMap[slug] || 'fas fa-tv';

                return `
                    <div class="card mb-3 shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1.5px solid ${isCustom ? '#10b981' : 'rgba(212,175,55,0.35)'} !important;">
                        <div class="card-header py-2 px-3 d-flex justify-content-between align-items-center"
                            style="cursor: pointer; user-select: none; background: linear-gradient(135deg, #041f14 0%, #083824 100%) !important; border-bottom: 1px solid rgba(255,255,255,0.08) !important;"
                            data-toggle="collapse"
                            data-target="#${collapseId}">
                            <div class="d-flex align-items-center">
                                <span style="width: 38px; height: 38px; min-width: 38px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; background: ${isCustom ? '#10b981' : 'rgba(255,215,0,0.18)'}; color: ${isCustom ? '#ffffff' : '#ffd700'}; border: 1.5px solid ${isCustom ? '#34d399' : 'rgba(255,215,0,0.45)'}; font-size: 1rem; box-shadow: 0 2px 6px rgba(0,0,0,0.4);" class="mr-3">
                                    <i class="${icon}"></i>
                                </span>
                                <div>
                                    <div class="d-flex align-items-center flex-wrap">
                                        <strong style="color: #ffffff !important; font-size: 1.01rem; font-weight: 700; letter-spacing: 0.3px;">${page.name}</strong>
                                        <span class="badge badge-${page.category_color || 'info'} ml-2 font-weight-bold" style="font-size: 0.74rem;">${page.category || 'Display'}</span>
                                        <span class="badge ml-2" style="background: rgba(255, 215, 0, 0.16); color: #ffd700; border: 1px solid rgba(255, 215, 0, 0.4); font-size: 0.72rem; font-family: monospace;">/${slug}-embed</span>
                                    </div>
                                    <small style="color: #cbd5e1 !important; font-size: 0.80rem; display: block; margin-top: 1px;">
                                        Teks berjalan pada layar rotasi: ${page.url}
                                    </small>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="badge px-2 py-1 mr-2 badge-rt-status-${slug}" style="background: ${isCustom ? '#10b981' : 'rgba(255, 255, 255, 0.16)'}; color: #ffffff; font-weight: 700; font-size: 0.78rem;">
                                    ${isCustom ? '<i class="fas fa-check-circle mr-1"></i> Teks Kustom' : '<i class="fas fa-book-open mr-1"></i> Rekomendasi Bawaan'}
                                </span>
                                <i class="fas fa-chevron-down text-warning" style="font-size: 0.95rem;"></i>
                            </div>
                        </div>

                        <div id="${collapseId}" class="collapse show">
                            <div class="card-body p-3" style="background: #ffffff !important; border-top: 1px solid #e2e8f0 !important;">
                                <div class="form-group mb-2">
                                    <label class="small font-weight-bold d-flex justify-content-between align-items-center text-gray-900 mb-1">
                                        <span><i class="fas fa-edit text-success mr-1"></i> Kalimat Teks Berjalan Layar <strong>${page.name}</strong>:</span>
                                        <span class="text-muted font-weight-normal small">Pisahkan tiap bait dengan tanda ( • )</span>
                                    </label>
                                    <textarea class="form-control font-weight-bold input-running-page"
                                        id="inputRT_${slug}"
                                        data-slug="${slug}"
                                        rows="3"
                                        style="font-size: 0.95rem; line-height: 1.6; border: 1.5px solid #cbd5e1; border-radius: 8px; color: #0f172a;"
                                        oninput="onRunningTextInputChange('${slug}')">${escapeHtml(currentVal)}</textarea>
                                </div>
                                <div class="d-flex flex-wrap align-items-center justify-content-between pt-1">
                                    <div class="btn-group btn-group-sm mb-1">
                                        <button type="button" class="btn btn-outline-warning font-weight-bold" onclick="bukaModalAiCopywriter('Teks Berjalan - ${page.name.replace(/'/g, "\\'")}', 'running_text_page:${slug}')">
                                            <i class="fas fa-magic mr-1"></i> ✨ Buat AI
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary font-weight-bold" onclick="resetSingleRunningText('${slug}')">
                                            <i class="fas fa-undo mr-1"></i> Rekomendasi Hadits
                                        </button>
                                        <button type="button" class="btn btn-outline-danger font-weight-bold" onclick="document.getElementById('inputRT_${slug}').value = ''; onRunningTextInputChange('${slug}');">
                                            <i class="fas fa-eraser mr-1"></i> Kosongkan
                                        </button>
                                    </div>
                                    <div class="mb-1">
                                        <button type="button" class="btn btn-sm btn-outline-info font-weight-bold" onclick="bukaPreviewSlide('${page.url}', '${page.name.replace(/'/g, "\\'")}')">
                                            <i class="fas fa-desktop mr-1"></i> Preview Layar TV
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');

            const badgeCount = document.getElementById('badgeCountCustomRT');
            if (badgeCount) {
                badgeCount.textContent = `${MASTER_ROTATION_PAGES.length} Halaman Terkonfigurasi`;
            }
        }

        function onRunningTextInputChange(slug) {
            const input = document.getElementById(`inputRT_${slug}`);
            const badge = document.querySelector(`.badge-rt-status-${slug}`);
            if (!input || !badge) return;

            const defaultText = (typeof SupabaseDB !== 'undefined' && SupabaseDB.DEFAULT_RUNNING_TEXTS && SupabaseDB.DEFAULT_RUNNING_TEXTS[slug]) 
                ? SupabaseDB.DEFAULT_RUNNING_TEXTS[slug].trim() 
                : '';
            const currentVal = input.value.trim();

            if (currentVal && currentVal !== defaultText) {
                badge.style.background = '#10b981';
                badge.innerHTML = '<i class="fas fa-check-circle mr-1"></i> Teks Kustom';
            } else {
                badge.style.background = 'rgba(255, 255, 255, 0.16)';
                badge.innerHTML = '<i class="fas fa-book-open mr-1"></i> Rekomendasi Bawaan';
            }
        }

        function resetSingleRunningText(slug) {
            const input = document.getElementById(`inputRT_${slug}`);
            if (!input) return;
            const defaultText = (typeof SupabaseDB !== 'undefined' && SupabaseDB.DEFAULT_RUNNING_TEXTS && SupabaseDB.DEFAULT_RUNNING_TEXTS[slug]) 
                ? SupabaseDB.DEFAULT_RUNNING_TEXTS[slug] 
                : '';
            input.value = defaultText;
            onRunningTextInputChange(slug);
        }

        function resetAllRunningTextsToDefault() {
            if (!confirm('Apakah Anda yakin ingin mengisi seluruh 19 halaman rotasi dengan teks hadits dan warta rekomendasi resmi?')) return;
            MASTER_ROTATION_PAGES.forEach(page => {
                const slug = getCanonicalSlideKey(page.url);
                resetSingleRunningText(slug);
            });
            alert('Alhamdulillah! Seluruh 19 halaman rotasi telah diisi dengan rekomendasi hadits & warta resmi. Jangan lupa klik "Simpan Seluruh Teks Berjalan" agar tersimpan ke Supabase.');
        }

        let isAllRunningPagesOpen = true;
        function toggleAllRunningPagesAccordion() {
            isAllRunningPagesOpen = !isAllRunningPagesOpen;
            $('#accordionRunningPages .collapse').collapse(isAllRunningPagesOpen ? 'show' : 'hide');
        }

        // Simpan Seluruh Teks Berjalan Multi-Halaman ke Supabase
        async function simpanRunningText() {
            const pagesObj = {};

            MASTER_ROTATION_PAGES.forEach(page => {
                const slug = getCanonicalSlideKey(page.url);
                const input = document.getElementById(`inputRT_${slug}`);
                const defaultText = (typeof SupabaseDB !== 'undefined' && SupabaseDB.DEFAULT_RUNNING_TEXTS && SupabaseDB.DEFAULT_RUNNING_TEXTS[slug]) 
                    ? SupabaseDB.DEFAULT_RUNNING_TEXTS[slug] 
                    : '';
                const val = input ? (input.value.trim() || defaultText) : defaultText;
                pagesObj[slug] = val;
                pagesObj[slug + '-embed'] = val;
                pagesObj['/' + slug + '-embed'] = val;
            });

            cachedRunningTextPages = pagesObj;
            localStorage.setItem('cached_running_text_pages', JSON.stringify(pagesObj));

            try {
                const res = await fetch(`${SUPABASE_CONFIG.url}/rest/v1/app_settings?id=eq.1`, {
                    method: 'PATCH',
                    headers: getSupabaseHeaders(),
                    body: JSON.stringify({
                        running_text_pages: pagesObj
                    })
                });

                if (res.ok) {
                    if (window.SupabaseDB && typeof SupabaseDB.broadcastChange === 'function') {
                        SupabaseDB.broadcastChange('running_text', 'UPDATE', { pages: pagesObj }, 'Admin DKM');
                        SupabaseDB.broadcastChange('app_settings', 'UPDATE', { running_text_pages: pagesObj }, 'Admin DKM');
                    }
                    alert('Alhamdulillah! Teks Berjalan untuk SELURUH 19 Halaman Rotasi berhasil disimpan ke Supabase dan seketika tayang di layar TV.');
                } else {
                    alert('Gagal memperbarui ke server Supabase, namun telah disimpan di sesi lokal browser.');
                }
            } catch (err) {
                alert('Tersimpan di sesi lokal browser (offline mode).');
            }
        }

        // Pemilihan Model Tampilan Surat Yaasiin (Autoswitch Mutually Exclusive - Sync Dashboard & Settings)