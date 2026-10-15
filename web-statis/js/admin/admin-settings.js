/**
 * MODUL ADMIN: admin-settings.js
 * Digital Signage Masjid Jami' Al-Jihad
 * Versi: aljihad-signage-v5.8.63
 */

        async function simpanPengaturanSistem() {
            const nama = document.getElementById('cfgNamaMasjid')?.value;
            const interval = Math.max(1, parseInt(document.getElementById('cfgIntervalTVSystem')?.value || document.getElementById('cfgIntervalTV')?.value) || 10);
            const footerText = document.getElementById('cfgFooterMasjid')?.value?.trim() || "© 2026 MASJID JAMI' AL JIHAD (GRAHA ASRI). All Rights Reserved";
            const liveMekah = document.getElementById('cfgLiveMekah')?.value?.trim() || '';
            const liveMadinah = document.getElementById('cfgLiveMadinah')?.value?.trim() || '';
            const cctv = document.getElementById('cfgCctvMimbar')?.value?.trim() || '';
            const geminiApiKey = document.getElementById('cfgGeminiApiKey')?.value?.trim();
            const geminiModel = document.getElementById('cfgGeminiModel')?.value || 'gemini-1.5-flash';

            // Ambil pengaturan Surat Yaasiin
            const yasinEnabled = document.getElementById('cfgYasinEnabled') ? document.getElementById('cfgYasinEnabled').checked : true;
            const yasinStartTime = document.getElementById('cfgYasinStartTime') ? document.getElementById('cfgYasinStartTime').value : '18:30';
            const isYasinMushaf = document.getElementById('radioYasinOption2') && document.getElementById('radioYasinOption2').checked;
            const yasinDisplayMode = isYasinMushaf ? 'mushaf' : 'step';
            const yasinStepDuration = document.getElementById('cfgYasinStepDuration') ? parseInt(document.getElementById('cfgYasinStepDuration').value) || 20 : 20;
            const yasinMushafDuration = document.getElementById('cfgYasinMushafDuration') ? parseInt(document.getElementById('cfgYasinMushafDuration').value) || 120 : 120;

            // Simpan langsung ke LocalStorage untuk responsivitas instan
            localStorage.setItem('display_rotation_interval', interval);
            localStorage.setItem('app_footer_text', footerText);
            localStorage.setItem('live_makkah_url', liveMekah);
            localStorage.setItem('live_madinah_url', liveMadinah);
            localStorage.setItem('yasin_display_mode', yasinDisplayMode);
            localStorage.setItem('yasin_step_duration', yasinStepDuration);
            localStorage.setItem('yasin_mushaf_duration', yasinMushafDuration);
            localStorage.setItem('yasin_mode_enabled', yasinEnabled);
            localStorage.setItem('yasin_start_time', yasinStartTime);

            // Perbarui cache lokal cached_app_settings agar display TV offline langsung akurat
            try {
                const cachedRaw = localStorage.getItem('cached_app_settings');
                const cached = cachedRaw ? JSON.parse(cachedRaw) : {};
                cached.rotation_interval = interval;
                if (nama) cached.nama_aplikasi = nama;
                if (footerText) cached.footer = footerText;
                cached.live_makkah_url = liveMekah;
                cached.live_madinah_url = liveMadinah;
                localStorage.setItem('cached_app_settings', JSON.stringify(cached));
            } catch (e) {}

            // Simpan lokal di browser untuk Gemini
            if (window.GeminiAI) {
                window.GeminiAI.setApiKey(geminiApiKey);
                window.GeminiAI.setModel(geminiModel);
                updateGeminiStatusBadge();
            }

            try {
                const patchPayload = {
                    nama_aplikasi: nama,
                    rotation_interval: interval,
                    footer: footerText,
                    live_makkah_url: liveMekah,
                    live_madinah_url: liveMadinah,
                    cctv_mimbar_url: cctv,
                    yasin_mode_enabled: yasinEnabled,
                    yasin_start_time: yasinStartTime,
                    yasin_scroll_speed: `${yasinDisplayMode}:${yasinStepDuration}:${yasinMushafDuration}`.substring(0, 20)
                };
                if (typeof geminiApiKey === 'string') {
                    patchPayload.gemini_api_key = geminiApiKey;
                    patchPayload.gemini_model = geminiModel;
                }

                const res = await fetch(`${SUPABASE_CONFIG.url}/rest/v1/app_settings?id=eq.1`, {
                    method: 'PATCH',
                    headers: getSupabaseHeaders(),
                    body: JSON.stringify(patchPayload)
                });

                // Sinkronkan juga baris cadangan di cloud
                fetch(`${SUPABASE_CONFIG.url}/rest/v1/app_settings?id=gt.1`, {
                    method: 'PATCH',
                    headers: getSupabaseHeaders(),
                    body: JSON.stringify({ rotation_interval: interval })
                }).catch(() => {});

                // Kirim sinyal Remote Broadcast UPDATE_SETTINGS agar display TV langsung mengadopsi interval & streaming baru tanpa jeda
                if (window.SupabaseDB && typeof SupabaseDB.broadcastChange === 'function') {
                    SupabaseDB.broadcastChange('app_settings', 'UPDATE', patchPayload, 'Admin Pengaturan Sistem');
                    SupabaseDB.broadcastChange('live_tv', 'UPDATE', {
                        live_makkah_url: liveMekah,
                        live_madinah_url: liveMadinah,
                        cctv_mimbar_url: cctv
                    }, 'Admin Pengaturan Sistem');
                }
                if (window.SupabaseDB && typeof SupabaseDB.sendRemoteCommand === 'function') {
                    SupabaseDB.sendRemoteCommand('UPDATE_SETTINGS', { 
                        rotation_interval: interval,
                        live_makkah_url: liveMekah,
                        live_madinah_url: liveMadinah
                    }).catch(() => {});
                }

                if (res.ok) {
                    alert('Alhamdulillah! Konfigurasi sistem, siaran live streaming Mekah & Madinah, serta Surat Yaasiin berhasil disimpan.');
                } else {
                    alert('Gagal menyimpan konfigurasi ke Supabase. Namun konfigurasi telah disimpan secara lokal di browser.');
                }
            } catch (err) {
                alert('Tersimpan secara lokal di browser ini.');
            }
        }

        /**
         * Pembersihan menyeluruh cache usang, purge CacheStorage lama,
         * refresh Service Worker v5.6.5, dan sinkronisasi realtime ke seluruh layar TV Display
         */
        async function bersihkanCacheSistem() {
            const btn = document.getElementById('btnBersihkanCacheSistem');
            const topBtn = document.getElementById('btnTopbarBersihkanCache');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Membersihkan Cache...';
            }
            if (topBtn) {
                topBtn.disabled = true;
                topBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> <span class="d-none d-lg-inline">Membersihkan...</span><span class="d-inline d-lg-none">Proses...</span>';
            }

            try {
                // 1. Jalankan pembersihan cache internal SupabaseDB
                let removedItems = [];
                if (window.SupabaseDB && typeof SupabaseDB.cleanupObsoleteCache === 'function') {
                    removedItems = SupabaseDB.cleanupObsoleteCache();
                } else {
                    const obsoleteKeys = [
                        'cached_prayer_mode_enabled',
                        'cached_prayer_mode_jumat_duration',
                        'nu_last_sync_date',
                        'cached_ramadhan_legacy',
                        'cached_tarhim_legacy'
                    ];
                    obsoleteKeys.forEach(k => {
                        if (localStorage.getItem(k) !== null) {
                            localStorage.removeItem(k);
                            removedItems.push(k);
                        }
                    });
                }

                // 2. Bersihkan CacheStorage Service Worker (sisakan v5.8.40)
                if ('caches' in window) {
                    try {
                        const cacheNames = await caches.keys();
                        await Promise.all(
                            cacheNames.map(name => {
                                if (name !== 'aljihad-signage-v5.8.91') {
                                    console.log('🗑️ Menghapus cache usang:', name);
                                    return caches.delete(name);
                                }
                            })
                        );
                    } catch (cErr) {
                        console.warn('Gagal membersihkan cache storage:', cErr);
                    }
                }

                // 3. Perbarui Service Worker
                if ('serviceWorker' in navigator) {
                    try {
                        const regs = await navigator.serviceWorker.getRegistrations();
                        for (let reg of regs) {
                            await reg.update().catch(() => {});
                        }
                    } catch (swErr) {}
                }

                // 4. Siarkan sinyal remote RELOAD & REFRESH ke seluruh layar TV Display
                if (window.SupabaseDB && typeof SupabaseDB.broadcastChange === 'function') {
                    SupabaseDB.broadcastChange('app_settings', 'RELOAD', { timestamp: Date.now() }, 'Admin Maintenance');
                }
                if (window.SupabaseDB && typeof SupabaseDB.sendRemoteCommand === 'function') {
                    SupabaseDB.sendRemoteCommand('RELOAD', { reason: 'Pembersihan cache sistem v5.8.40' }).catch(() => {});
                    SupabaseDB.sendRemoteCommand('REFRESH_DISPLAY', { reason: 'Pembersihan cache sistem v5.8.40' }).catch(() => {});
                }

                // 5. Muat ulang data terbaru dari Supabase
                if (typeof loadAllSupabaseData === 'function') {
                    await loadAllSupabaseData();
                }

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Cache Berhasil Dibersihkan!',
                        html: `
                            <p class="text-left mb-2">Seluruh cache usang dan memori usang telah dibersihkan:</p>
                            <ul class="text-left small text-muted mb-3 pl-3">
                                <li>Service Worker diperbarui ke <b>v5.8.86</b></li>
                                <li>Kunci memori lama yang dibersihkan: <b>${removedItems.length > 0 ? removedItems.join(', ') : 'Bersih (Sudah Optimal)'}</b></li>
                                <li>Sinyal refresh TV telah dipancarkan secara realtime (&lt; 100ms) ke seluruh layar</li>
                            </ul>
                            <div class="alert alert-success py-2 px-3 small font-weight-bold mb-0">
                                Responsivitas Web Statis & TV Display kini meningkat optimal!
                            </div>
                        `,
                        confirmButtonColor: '#10b981'
                    });
                } else {
                    alert('Alhamdulillah! Seluruh cache usang berhasil dibersihkan dan sinyal sinkronisasi telah dikirimkan ke Layar TV.');
                }
            } catch (err) {
                console.error('Error saat bersihkanCacheSistem:', err);
                alert('Pemberitahuan pembersihan cache: ' + err.message);
            } finally {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-trash-alt mr-1"></i> Bersihkan Cache Usang & Refresh TV';
                }
                if (topBtn) {
                    topBtn.disabled = false;
                    topBtn.innerHTML = '<i class="fas fa-broom mr-1"></i> <span class="d-none d-lg-inline">Bersihkan Cache & Refresh TV</span><span class="d-inline d-lg-none">Refresh TV</span>';
                }
            }
        }

        // ==========================================
        // FITUR PREVIEW LIVE STREAMING (YOUTUBE TESTER)
        // ==========================================
        function previewStreamUrl(inputId, title) {
            const input = document.getElementById(inputId);
            const rawVal = input ? input.value.trim() : '';
            if (!rawVal) {
                alert('Silakan masukkan link atau URL YouTube terlebih dahulu pada kolom sebelum melakukan uji putar.');
                return;
            }

            const embedUrl = (typeof SupabaseDB !== 'undefined' && SupabaseDB.formatYouTubeEmbed)
                ? SupabaseDB.formatYouTubeEmbed(rawVal)
                : rawVal;

            const titleEl = document.getElementById('modalPreviewLiveStreamTitle');
            if (titleEl) titleEl.innerHTML = `<i class="fab fa-youtube text-danger mr-2"></i> Uji Coba: ${title}`;

            const iframe = document.getElementById('previewStreamIframe');
            if (iframe) iframe.src = embedUrl;

            if (typeof $ !== 'undefined') {
                $('#modalPreviewLiveStream').modal('show');
            }
        }

        function closeStreamPreview() {
            const iframe = document.getElementById('previewStreamIframe');
            if (iframe) iframe.src = '';
        }

        // Pasang Siaran Resmi 24 Jam YouTube (1-Klik)
        function setOfficialStream(inputId, url) {
            const input = document.getElementById(inputId);
            if (input) {
                input.value = url;
                const key = (inputId === 'cfgLiveMekah') ? 'live_makkah_url' : 'live_madinah_url';
                localStorage.setItem(key, url);
                const title = (inputId === 'cfgLiveMekah') ? 'Live TV Makkah (Masjidil Haram)' : 'Live TV Madinah (Masjid Nabawi)';
                const slideUrl = (inputId === 'cfgLiveMekah') ? 'slides/live-mekah.html' : 'slides/live-madinah.html';
                
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Siaran Resmi 24 Jam Dipasang!',
                        html: `Link siaran resmi YouTube: <br><code>${url}</code><br><br>Siaran sudah tersinkron dan siap tayang di TV Display.`,
                        showCancelButton: true,
                        confirmButtonText: '<i class="fas fa-desktop mr-1"></i> Buka Preview Sekarang',
                        cancelButtonText: 'Tutup',
                        confirmButtonColor: '#059669',
                        cancelButtonColor: '#6b7280'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            bukaPreviewSlide(slideUrl, title);
                        }
                    });
                } else {
                    alert('Siaran resmi YouTube 24 jam berhasil dipasang!');
                    bukaPreviewSlide(slideUrl, title);
                }
            }
        }

        // Event listener saat modal preview ditutup via klik di luar atau ESC
        if (typeof $ !== 'undefined') {
            $(document).ready(function() {
                $('#modalPreviewLiveStream').on('hidden.bs.modal', function() {
                    closeStreamPreview();
                });
            });
        }

        // ==========================================
        // FITUR GOOGLE GEMINI AI ENGINE (ADMIN PANEL)
        // ==========================================
        let lastAiResult = null;
