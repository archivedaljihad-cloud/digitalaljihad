/**
 * MODUL ADMIN: admin-rotasi.js
 * Digital Signage Masjid Jami' Al-Jihad
 * Versi: aljihad-signage-v5.8.46
 */

        function renderRotationTable(pages) {
            const tbody = document.getElementById('rotationTableBody');
            if (!tbody) return;

            const normalized = normalizeRotationPages(pages);
            cachedRotationPages = normalized;

            tbody.innerHTML = normalized.map((p, idx) => {
                const isChecked = (p.active !== false) ? 'checked' : '';
                const title = p.name || p.title || `Slide ${idx + 1}`;
                const url = p.path || p.url || `slides/slide${idx + 1}.html`;
                const cat = p.category || 'Display';
                const catColor = p.category_color || 'secondary';

                return `
                    <tr data-index="${idx + 1}">
                        <td class="font-weight-bold text-center align-middle">${idx + 1}</td>
                        <td class="align-middle">
                            <div class="d-flex align-items-center">
                                <span class="badge badge-${catColor} font-weight-bold mr-2 badge-slide-category" style="font-size: 11px; min-width: 80px; text-align: center;">${cat}</span>
                                <strong class="text-gray-900">${title}</strong>
                            </div>
                        </td>
                        <td class="align-middle"><code class="text-muted font-weight-bold">${url}</code></td>
                        <td class="text-center align-middle">
                            <button class="btn btn-sm btn-outline-info font-weight-bold mr-1" onclick="bukaPreviewSlide('${url}', '${title.replace(/'/g, "\\'")}')" title="Pratinjau Layar TV di Modal">
                                <i class="fas fa-desktop mr-1"></i> Preview
                            </button>
                            <a href="${url}" target="_blank" class="btn btn-sm btn-outline-secondary" title="Buka Tab Baru">
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                        </td>
                        <td class="text-center align-middle">
                            <div class="custom-control custom-switch d-inline-block">
                                <input type="checkbox" class="custom-control-input" id="rotCheck${idx + 1}" ${isChecked} onchange="handleRotationSwitchChange(this)">
                                <label class="custom-control-label font-weight-bold ${p.active !== false ? 'text-success' : 'text-muted'}" for="rotCheck${idx + 1}">${p.active !== false ? 'Aktif' : 'Nonaktif'}</label>
                            </div>
                        </td>
                        <td class="text-center align-middle reorder-col">
                            <button class="btn btn-sm btn-outline-secondary btn-reorder btn-reorder-up mr-1" onclick="reorderSlide(this, 'up')" title="Geser Naik (▲)"><i class="fas fa-arrow-up"></i></button>
                            <button class="btn btn-sm btn-outline-secondary btn-reorder btn-reorder-down" onclick="reorderSlide(this, 'down')" title="Geser Turun (▼)"><i class="fas fa-arrow-down"></i></button>
                        </td>
                    </tr>
                `;
            }).join('');

            updateActiveRotationCountBadge();

            // Terapkan hak akses reorder Super Admin (sembunyikan kolom seluruhnya untuk Petugas)
            if (currentUser) {
                const isSuperAdmin = (currentUser.role === 'admin' || currentUser.role_id === 3);

                // Tampilkan atau sembunyikan seluruh kolom Aksi Reorder berdasarkan role
                document.querySelectorAll('.reorder-col').forEach(el => {
                    el.style.display = isSuperAdmin ? '' : 'none';
                });

                const roleBadge = document.getElementById('badgeRotasiRole');
                if (roleBadge) {
                    if (isSuperAdmin) {
                        roleBadge.style.display = 'inline-block';
                        roleBadge.className = 'badge badge-warning font-weight-bold px-3 py-2';
                        roleBadge.innerHTML = '<i class="fas fa-shield-alt mr-1"></i> Mode Super Admin (Reorder ▲/▼ Aktif)';
                    } else {
                        roleBadge.style.display = 'none';
                        roleBadge.innerHTML = '';
                    }
                }

                const subTitleRot = document.getElementById('descRotasiSubtitle');
                if (subTitleRot) {
                    if (isSuperAdmin) {
                        subTitleRot.textContent = 'Kelola urutan dan status aktif seluruh 18 halaman tayang slide TV. Super Admin dapat memindah urutan (▲/▼).';
                    } else {
                        subTitleRot.textContent = 'Untuk mengelola/mengatur urutan halaman rotasi, silakan menghubungi Admin.';
                    }
                }
            }
        }

        // Event handler switch aktif/nonaktif baris
        function handleRotationSwitchChange(input) {
            const label = input.nextElementSibling;
            if (label) {
                label.textContent = input.checked ? 'Aktif' : 'Nonaktif';
                label.className = input.checked ? 'custom-control-label font-weight-bold text-success' : 'custom-control-label font-weight-bold text-muted';
            }
            updateActiveRotationCountBadge();
        }

        // Perbarui badge jumlah total slide yang aktif
        function updateActiveRotationCountBadge() {
            const allChecks = document.querySelectorAll('#rotationTableBody input[type="checkbox"]');
            const activeChecks = document.querySelectorAll('#rotationTableBody input[type="checkbox"]:checked');
            const badge = document.getElementById('badgeRotasiActiveCount');
            if (badge) {
                badge.textContent = `${activeChecks.length} dari ${allChecks.length} Halaman Aktif di TV`;
                badge.className = activeChecks.length > 0 ? 'badge badge-success font-weight-bold px-3 py-2' : 'badge badge-danger font-weight-bold px-3 py-2';
            }
            const statTv = document.getElementById('statStatusTV');
            if (statTv) {
                statTv.textContent = `${activeChecks.length} Halaman Aktif`;
            }
        }

        // Aktifkan / Matikan semua halaman sekaligus (Khusus Super Admin)
        function setAllRotationPagesActive(isActive) {
            const isSuperAdmin = (currentUser && (currentUser.role === 'admin' || currentUser.role_id === 3));
            if (!isSuperAdmin) {
                alert('Akses Dibatasi: Hanya Super Admin yang berhak mengubah status rotasi massal.');
                return;
            }
            const allChecks = document.querySelectorAll('#rotationTableBody input[type="checkbox"]');
            allChecks.forEach(ch => {
                ch.checked = isActive;
                const label = ch.nextElementSibling;
                if (label) {
                    label.textContent = isActive ? 'Aktif' : 'Nonaktif';
                    label.className = isActive ? 'custom-control-label font-weight-bold text-success' : 'custom-control-label font-weight-bold text-muted';
                }
            });
            updateActiveRotationCountBadge();
        }

        // Reset Urutan dan Status Halaman ke Standar Master
        function resetRotationPagesToDefault() {
            const isSuperAdmin = (currentUser && (currentUser.role === 'admin' || currentUser.role_id === 3));
            if (!isSuperAdmin) {
                alert('Akses Dibatasi: Hanya Super Admin yang berhak mereset susunan rotasi.');
                return;
            }
            if (!confirm('Kembalikan urutan rotasi seluruh 18 slide ke susunan standar awal masjid?')) return;
            cachedRotationPages = JSON.parse(JSON.stringify(MASTER_ROTATION_PAGES));
            localStorage.setItem('cached_rotation_pages', JSON.stringify(cachedRotationPages));
            renderRotationTable(cachedRotationPages);
            alert('Susunan rotasi telah direset ke 18 halaman standar. Silakan klik "Simpan Rotasi" untuk menerapkan ke layar display TV.');
        }

        // Buka Modal Live Preview TV
        function bukaPreviewSlide(url, title) {
            const frame = document.getElementById('framePreviewTV');
            const labelTitle = document.getElementById('modalPreviewTVLabel');
            const labelUrl = document.getElementById('previewUrlLabel');
            const btnNewTab = document.getElementById('btnPreviewNewTab');

            if (!frame) return;

            // Sinkronkan nilai input streaming langsung ke localStorage agar preview selalu mutakhir
            const mekahVal = document.getElementById('cfgLiveMekah')?.value?.trim();
            if (mekahVal !== undefined && mekahVal !== null) {
                localStorage.setItem('live_makkah_url', mekahVal);
            }
            const madinahVal = document.getElementById('cfgLiveMadinah')?.value?.trim();
            if (madinahVal !== undefined && madinahVal !== null) {
                localStorage.setItem('live_madinah_url', madinahVal);
            }

            if (labelTitle) labelTitle.textContent = title || 'Pratinjau Layar TV Display';
            if (labelUrl) labelUrl.textContent = url || '';
            if (btnNewTab) btnNewTab.href = url || '#';

            frame.src = url || 'about:blank';
            $('#modalPreviewTV').modal('show');
        }

        // Muat Ulang Live Preview Iframe
        function refreshPreviewIframe() {
            const frame = document.getElementById('framePreviewTV');
            if (frame && frame.src) {
                frame.src = frame.src;
            }
        }

        // Fullscreen Live Preview
        function fullscreenPreviewIframe() {
            const frame = document.getElementById('framePreviewTV');
            if (!frame) return;
            if (frame.requestFullscreen) {
                frame.requestFullscreen();
            } else if (frame.webkitRequestFullscreen) {
                frame.webkitRequestFullscreen();
            } else if (frame.msRequestFullscreen) {
                frame.msRequestFullscreen();
            }
        }

        // Reset iframe src saat modal ditutup agar animasi / audio berhenti
        $(document).ready(function() {
            $('#modalPreviewTV').on('hidden.bs.modal', function () {
                const frame = document.getElementById('framePreviewTV');
                if (frame) frame.src = '';
            });
        });

        // Buka Modal Edit Kas

        async function simpanRotasiTV() {
            const isSuperAdmin = (currentUser && (currentUser.role === 'admin' || currentUser.role_id === 3));
            if (!isSuperAdmin) {
                alert('Akses Dibatasi: Hanya Super Admin yang berhak menyimpan perubahan urutan rotasi display TV.');
                return;
            }

            const rows = document.querySelectorAll('#rotationTableBody tr');
            const updatedPages = [];
            rows.forEach((r, idx) => {
                const title = r.querySelector('strong')?.textContent.trim();
                const url = r.querySelector('code')?.textContent.trim();
                const active = r.querySelector('input[type="checkbox"]')?.checked ?? true;
                const badge = r.querySelector('.badge-slide-category')?.textContent.trim() || 'Display';
                if (title && url) {
                    updatedPages.push({
                        order: idx + 1,
                        name: title,
                        title: title,
                        path: url,
                        url: url,
                        page: url.replace('slides/', '/').replace('.html', '-embed'),
                        category: badge,
                        active: active
                    });
                }
            });

            if (updatedPages.length === 0) {
                alert('Tidak ada halaman slide untuk disimpan.');
                return;
            }

            // Normalisasi ketat agar susunan dan integritas 18 slide terjamin bebas duplikat
            const cleanPages = normalizeRotationPages(updatedPages);

            try {
                cachedRotationPages = cleanPages;
                localStorage.setItem('cached_rotation_pages', JSON.stringify(cleanPages));
                renderRotationTable(cleanPages);

                const res = await fetch(`${SUPABASE_CONFIG.url}/rest/v1/app_settings?id=eq.1`, {
                    method: 'PATCH',
                    headers: getSupabaseHeaders(),
                    body: JSON.stringify({
                        rotation_pages: cleanPages
                    })
                });

                if (res.ok) {
                    if (window.SupabaseDB && typeof SupabaseDB.broadcastChange === 'function') {
                        SupabaseDB.broadcastChange('app_settings', 'UPDATE', { rotation_pages: cleanPages }, 'Super Admin');
                    }
                    alert('Alhamdulillah! Susunan rotasi seluruh ' + updatedPages.length + ' halaman slide TV berhasil disimpan ke Supabase Cloud dan seketika aktif pada layar display TV.');
                    
                    // Update status badge di panel Qurban jika ada
                    const qPage = updatedPages.find(p => (p.url || p.path || '').includes('qurban'));
                    if (qPage && document.getElementById('adminStatusRotasiQurban')) {
                        const isActive = qPage.active !== false;
                        document.getElementById('adminStatusRotasiQurban').textContent = isActive ? 'Aktif di TV' : 'Nonaktif di TV';
                        document.getElementById('adminStatusRotasiQurban').className = isActive ? 'h5 mb-0 font-weight-bold text-success' : 'h5 mb-0 font-weight-bold text-danger';
                    }
                    await loadAllSupabaseData();
                } else {
                    alert('Tersimpan di cache lokal browser. (Koneksi Supabase: ' + res.statusText + ')');
                }
            } catch (err) {
                alert('Tersimpan di cache lokal browser: ' + err.message);
            }
        }

        // =======================================================

        async function toggleSlideKasJumat(isActive) {
            if (!cachedRotationPages || cachedRotationPages.length === 0) {
                cachedRotationPages = normalizeRotationPages(localStorage.getItem('cached_rotation_pages'));
            }

            let rPage = cachedRotationPages.find(p => (p.url || p.path || '').includes('kas-jumat'));
            if (rPage) {
                rPage.active = isActive;
            } else {
                cachedRotationPages.push({
                    order: 20,
                    name: "Maklumat & Kas Jum'at (Khusus Jum'at)",
                    path: 'slides/kas-jumat.html',
                    url: 'slides/kas-jumat.html',
                    page: '/kas-jumat-embed',
                    category: "Jum'at",
                    category_color: 'warning text-dark',
                    active: isActive
                });
            }

            cachedRotationPages = normalizeRotationPages(cachedRotationPages);
            localStorage.setItem('cached_rotation_pages', JSON.stringify(cachedRotationPages));
            const lbl = document.getElementById('lblSwitchKasJumat');
            if (lbl) {
                lbl.textContent = isActive ? 'Tayang di TV Aktif' : 'Tayang di TV Nonaktif';
                lbl.className = isActive ? 'custom-control-label font-weight-bold text-success' : 'custom-control-label font-weight-bold text-secondary';
            }

            try {
                await fetch(`${SUPABASE_CONFIG.url}/rest/v1/app_settings?id=eq.1`, {
                    method: 'PATCH',
                    headers: getSupabaseHeaders(),
                    body: JSON.stringify({
                        rotation_pages: cachedRotationPages
                    })
                });
            } catch (err) {
                console.warn('Sync toggle rotasi kas-jumat Supabase:', err);
            }

            if (window.SupabaseDB && typeof SupabaseDB.broadcastChange === 'function') {
                SupabaseDB.broadcastChange('app_settings', 'UPDATE', { rotation_pages: cachedRotationPages }, 'Petugas Jumat');
            }

            renderRotationTable(cachedRotationPages);
            alert(isActive ? 'Alhamdulillah! Slide Khusus "Maklumat & Kas Jum\'at" kini AKTIF dan siap tayang otomatis setiap hari Jum\'at pada TV Display.' : 'Slide Maklumat & Kas Jum\'at dinonaktifkan dari rotasi TV.');
        }

        // Reorder Slide (Super Admin Only)
        function reorderSlide(elementOrIndex, direction) {
            const isSuperAdmin = (currentUser && (currentUser.role === 'admin' || currentUser.role_id === 3));
            if (!isSuperAdmin) {
                alert('Akses Dibatasi: Hanya Super Admin yang berhak memindah urutan siaran TV.');
                return;
            }

            let row = null;
            if (elementOrIndex && typeof elementOrIndex === 'object' && elementOrIndex.closest) {
                row = elementOrIndex.closest('tr');
            } else if (elementOrIndex) {
                row = document.querySelector(`tr[data-index="${elementOrIndex}"]`);
            }
            if (!row) return;

            if (direction === 'up' && row.previousElementSibling) {
                row.parentNode.insertBefore(row, row.previousElementSibling);
            } else if (direction === 'down' && row.nextElementSibling) {
                row.parentNode.insertBefore(row.nextElementSibling, row);
            }

            // Perbarui nomor urut tampilan & data-index
            const allRows = document.querySelectorAll('#rotationTableBody tr');
            allRows.forEach((r, idx) => {
                r.setAttribute('data-index', idx + 1);
                const tdNum = r.querySelector('td:first-child');
                if (tdNum) tdNum.textContent = (idx + 1);
            });
            updateActiveRotationCountBadge();
        }

        // Export Kas ke Format Excel / CSV (Mendukung data aktif terfilter)