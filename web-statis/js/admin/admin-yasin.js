/**
 * MODUL ADMIN: admin-yasin.js
 * Digital Signage Masjid Jami' Al-Jihad
 * Versi: aljihad-signage-v5.8.46
 */

        function selectYasinOption(option) {
            const r1 = document.getElementById('radioYasinOption1');
            const r2 = document.getElementById('radioYasinOption2');
            const c1 = document.getElementById('cardYasinOption1');
            const c2 = document.getElementById('cardYasinOption2');

            const dr1 = document.getElementById('dashRadioYasinOption1');
            const dr2 = document.getElementById('dashRadioYasinOption2');
            const dc1 = document.getElementById('dashCardYasinOption1');
            const dc2 = document.getElementById('dashCardYasinOption2');

            if (option === 'step') {
                if (r1) r1.checked = true;
                if (r2) r2.checked = false;
                if (c1) { c1.style.border = '2px solid #10b981'; c1.style.background = '#f0fdf4'; }
                if (c2) { c2.style.border = '1.5px solid #cbd5e1'; c2.style.background = '#f8fafc'; }

                if (dr1) dr1.checked = true;
                if (dr2) dr2.checked = false;
                if (dc1) { dc1.style.border = '2px solid #10b981'; dc1.style.background = '#f0fdf4'; }
                if (dc2) { dc2.style.border = '1.5px solid #cbd5e1'; dc2.style.background = '#f8fafc'; }
            } else {
                if (r1) r1.checked = false;
                if (r2) r2.checked = true;
                if (c1) { c1.style.border = '1.5px solid #cbd5e1'; c1.style.background = '#f8fafc'; }
                if (c2) { c2.style.border = '2px solid #0284c7'; c2.style.background = '#f0f9ff'; }

                if (dr1) dr1.checked = false;
                if (dr2) dr2.checked = true;
                if (dc1) { dc1.style.border = '1.5px solid #cbd5e1'; dc1.style.background = '#f8fafc'; }
                if (dc2) { dc2.style.border = '2px solid #0284c7'; dc2.style.background = '#f0f9ff'; }
            }
        }

        function onYasinOptionRadioChange(radio) {
            selectYasinOption(radio.value);
        }

        // Sinkronisasi dua arah kontrol Yaasiin (Dashboard Petugas <-> Pengaturan Sistem Admin)
        function syncYasinControl(type, val) {
            if (type === 'enabled') {
                const c1 = document.getElementById('cfgYasinEnabled');
                const c2 = document.getElementById('dashYasinEnabled');
                if (c1 && c1.checked !== val) c1.checked = val;
                if (c2 && c2.checked !== val) c2.checked = val;
            } else if (type === 'time') {
                const t1 = document.getElementById('cfgYasinStartTime');
                const t2 = document.getElementById('dashYasinStartTime');
                if (t1 && t1.value !== val) t1.value = val;
                if (t2 && t2.value !== val) t2.value = val;
            } else if (type === 'step') {
                const s1 = document.getElementById('cfgYasinStepDuration');
                const s2 = document.getElementById('dashYasinStepDuration');
                if (s1 && s1.value !== val) s1.value = val;
                if (s2 && s2.value !== val) s2.value = val;
            } else if (type === 'mushaf') {
                const m1 = document.getElementById('cfgYasinMushafDuration');
                const m2 = document.getElementById('dashYasinMushafDuration');
                if (m1 && m1.value !== val) m1.value = val;
                if (m2 && m2.value !== val) m2.value = val;
            }
        }

        // ==========================================
        // FORMAT & PRESET JAM 24 JAM (SURAT YAASIIN)
        // ==========================================
        function formatJam24H(input) {
            if (!input) return;
            let v = input.value.replace(/[^0-9]/g, '');
            if (v.length > 4) v = v.substring(0, 4);
            if (v.length >= 3) {
                let jam = parseInt(v.substring(0, 2), 10);
                let menit = parseInt(v.substring(2, 4), 10);
                if (jam > 23) jam = 23;
                let jamStr = jam < 10 ? '0' + jam : '' + jam;
                if (v.length === 3) {
                    input.value = jamStr + ':' + v.substring(2, 3);
                } else {
                    if (menit > 59) menit = 59;
                    let menitStr = menit < 10 ? '0' + menit : '' + menit;
                    input.value = jamStr + ':' + menitStr;
                }
            } else if (v.length === 2) {
                let jam = parseInt(v, 10);
                if (jam > 23) jam = 23;
                input.value = (jam < 10 ? '0' + jam : '' + jam);
            } else {
                input.value = v;
            }
        }

        function validateJam24H(input) {
            if (!input) return;
            let val = input.value.trim();
            const regex = /^([01]?[0-9]|2[0-3]):[0-5][0-9]$/;
            if (!regex.test(val)) {
                let digits = val.replace(/[^0-9]/g, '');
                if (digits.length === 4) {
                    let h = parseInt(digits.substring(0, 2), 10);
                    let m = parseInt(digits.substring(2, 4), 10);
                    if (h > 23) h = 23;
                    if (m > 59) m = 59;
                    input.value = (h < 10 ? '0' + h : '' + h) + ':' + (m < 10 ? '0' + m : '' + m);
                } else if (digits.length === 2) {
                    let h = parseInt(digits, 10);
                    if (h > 23) h = 23;
                    input.value = (h < 10 ? '0' + h : '' + h) + ':00';
                } else {
                    input.value = '18:30';
                }
            }
        }

        function setPresetJamYasin(val) {
            const inputs = [
                document.getElementById('cfgYasinStartTime'),
                document.getElementById('dashYasinStartTime')
            ].filter(Boolean);

            inputs.forEach(input => {
                input.value = val;
                input.style.transition = 'all 0.3s ease';
                input.style.backgroundColor = '#ecfdf5';
                input.style.borderColor = '#059669';
                setTimeout(() => {
                    input.style.backgroundColor = '#ffffff';
                    input.style.borderColor = '#10b981';
                }, 500);
            });
        }

        // ==========================================
        // SIMPAN KHUSUS PENGATURAN SURAT YAASIIN & TAHLIL
        // ==========================================
        async function simpanPengaturanYasin(source) {
            const buttons = [
                document.getElementById('btnSaveYasinTopDash'),
                document.getElementById('btnSaveYasinBottomDash'),
                document.getElementById('btnSaveYasinTop'),
                document.getElementById('btnSaveYasinBottom')
            ].filter(Boolean);
            const origButtonStates = buttons.map(b => ({ btn: b, html: b.innerHTML }));

            try {
                buttons.forEach(b => {
                    b.disabled = true;
                    b.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...';
                });

                const chk = document.getElementById('cfgYasinEnabled') || document.getElementById('dashYasinEnabled');
                const yasinEnabled = chk ? chk.checked : true;

                const timeInput = document.getElementById('cfgYasinStartTime') || document.getElementById('dashYasinStartTime');
                let yasinStartTime = timeInput ? timeInput.value.trim() : '18:30';

                // Validasi format jam 24 jam (HH:MM)
                const timeMatch = yasinStartTime.match(/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/);
                if (!timeMatch) {
                    const cleanDigits = yasinStartTime.replace(/[^0-9]/g, '');
                    if (cleanDigits.length === 4) {
                        let h = parseInt(cleanDigits.substring(0, 2), 10);
                        let m = parseInt(cleanDigits.substring(2, 4), 10);
                        if (h > 23) h = 23;
                        if (m > 59) m = 59;
                        yasinStartTime = (h < 10 ? '0' + h : '' + h) + ':' + (m < 10 ? '0' + m : '' + m);
                    } else {
                        yasinStartTime = '18:30';
                    }
                }
                if (document.getElementById('cfgYasinStartTime')) document.getElementById('cfgYasinStartTime').value = yasinStartTime;
                if (document.getElementById('dashYasinStartTime')) document.getElementById('dashYasinStartTime').value = yasinStartTime;

                const isYasinMushaf = (document.getElementById('radioYasinOption2') && document.getElementById('radioYasinOption2').checked) ||
                                      (document.getElementById('dashRadioYasinOption2') && document.getElementById('dashRadioYasinOption2').checked);
                const yasinDisplayMode = isYasinMushaf ? 'mushaf' : 'step';
                const yasinStepDuration = Math.max(5, parseInt(document.getElementById('cfgYasinStepDuration')?.value || document.getElementById('dashYasinStepDuration')?.value) || 20);
                const yasinMushafDuration = Math.max(10, parseInt(document.getElementById('cfgYasinMushafDuration')?.value || document.getElementById('dashYasinMushafDuration')?.value) || 120);

                // 1. Simpan ke LocalStorage seketika untuk responsivitas instan di TV
                localStorage.setItem('yasin_mode_enabled', yasinEnabled);
                localStorage.setItem('yasin_start_time', yasinStartTime);
                localStorage.setItem('yasin_display_mode', yasinDisplayMode);
                localStorage.setItem('yasin_step_duration', yasinStepDuration);
                localStorage.setItem('yasin_mushaf_duration', yasinMushafDuration);

                // Perbarui cached_app_settings jika ada
                try {
                    const cachedRaw = localStorage.getItem('cached_app_settings');
                    const cached = cachedRaw ? JSON.parse(cachedRaw) : {};
                    cached.yasin_mode_enabled = yasinEnabled;
                    cached.yasin_start_time = yasinStartTime;
                    cached.yasin_scroll_speed = JSON.stringify({
                        mode: yasinDisplayMode,
                        step_duration: yasinStepDuration,
                        mushaf_duration: yasinMushafDuration,
                        updated_at: new Date().toISOString()
                    });
                    localStorage.setItem('cached_app_settings', JSON.stringify(cached));
                } catch (e) {}

                // 2. Simpan ke Cloud Supabase table app_settings
                let cloudSaved = false;
                if (typeof SUPABASE_CONFIG !== 'undefined' && SUPABASE_CONFIG.url) {
                    const patchPayload = {
                        yasin_mode_enabled: yasinEnabled,
                        yasin_start_time: yasinStartTime,
                        yasin_scroll_speed: JSON.stringify({
                            mode: yasinDisplayMode,
                            step_duration: yasinStepDuration,
                            mushaf_duration: yasinMushafDuration,
                            updated_at: new Date().toISOString()
                        })
                    };

                    const res = await fetch(`${SUPABASE_CONFIG.url}/rest/v1/app_settings?id=eq.1`, {
                        method: 'PATCH',
                        headers: getSupabaseHeaders(),
                        body: JSON.stringify(patchPayload)
                    });
                    cloudSaved = res.ok;

                    // Broadcast sinyal remote jika aktif
                    if (window.SupabaseDB && typeof SupabaseDB.broadcastChange === 'function') {
                        SupabaseDB.broadcastChange('yasin', 'UPDATE', {
                            yasin_mode_enabled: yasinEnabled,
                            yasin_start_time: yasinStartTime,
                            yasin_display_mode: yasinDisplayMode,
                            step_duration: yasinStepDuration,
                            mushaf_duration: yasinMushafDuration
                        }, 'Admin Yaasiin');
                        SupabaseDB.broadcastChange('app_settings', 'UPDATE', patchPayload, 'Admin Yaasiin');
                    }
                    if (window.SupabaseDB && typeof SupabaseDB.sendRemoteCommand === 'function') {
                        SupabaseDB.sendRemoteCommand('UPDATE_SETTINGS', {
                            yasin_mode_enabled: yasinEnabled,
                            yasin_start_time: yasinStartTime,
                            yasin_display_mode: yasinDisplayMode,
                            step_duration: yasinStepDuration,
                            mushaf_duration: yasinMushafDuration
                        }).catch(() => {});
                    }
                }

                // Tampilkan feedback visual
                const modeLabel = isYasinMushaf ? 'Opsi 2: Lembaran Mushaf Madinah (6 Halaman)' : 'Opsi 1: Lompat Halus per Blok Ayat (17 Blok)';
                const durasiLabel = isYasinMushaf ? `${yasinMushafDuration} Detik / Halaman` : `${yasinStepDuration} Detik / Blok`;

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Pengaturan Yaasiin & Tahlil Berhasil Disimpan!',
                        html: `
                            <div class="text-left p-3 mt-2" style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; font-size: 13.5px; line-height: 1.7;">
                                <div><i class="fas fa-toggle-on text-success mr-2"></i><strong>Status Tayang:</strong> ${yasinEnabled ? '<span class="badge badge-success px-2 py-1">Aktif Tiap Kamis Malam</span>' : '<span class="badge badge-secondary px-2 py-1">Nonaktif</span>'}</div>
                                <div><i class="far fa-clock text-primary mr-2"></i><strong>Jam Mulai (24 Jam):</strong> <span class="badge badge-dark px-2 py-1" style="font-size: 13px;">${yasinStartTime} WIB</span></div>
                                <div><i class="fas fa-sliders-h text-info mr-2"></i><strong>Model Tampilan:</strong> <span class="text-dark font-weight-bold">${modeLabel}</span></div>
                                <div><i class="fas fa-stopwatch text-warning mr-2"></i><strong>Durasi Pergantian:</strong> <span class="badge badge-info px-2 py-1" style="font-size: 13px;">${durasiLabel}</span></div>
                            </div>
                            <div class="mt-3 text-muted small">
                                ${cloudSaved ? '<i class="fas fa-cloud-upload-alt text-success mr-1"></i> Tersinkron ke Cloud Database & TV Masjid.' : '<i class="fas fa-hdd text-info mr-1"></i> Tersimpan di LocalStorage TV Browser.'}
                            </div>
                        `,
                        confirmButtonText: '<i class="fas fa-check mr-1"></i> Siap, Selesai',
                        confirmButtonColor: '#059669'
                    });
                } else {
                    alert(`Alhamdulillah! Pengaturan Yaasiin & Tahlil Berhasil Disimpan.\nStatus: ${yasinEnabled ? 'Aktif' : 'Nonaktif'}\nJam Mulai: ${yasinStartTime} WIB (Format 24 Jam)\nModel: ${modeLabel}\nDurasi: ${durasiLabel}`);
                }
            } catch (err) {
                console.error('Error simpan pengaturan Yaasiin:', err);
                alert('Pengaturan Yaasiin berhasil disimpan di memori lokal browser.');
            } finally {
                origButtonStates.forEach(item => {
                    if (item.btn) {
                        item.btn.disabled = false;
                        item.btn.innerHTML = item.html;
                    }
                });
            }
        }

        // Simpan Konfigurasi Sistem