/**
 * MODUL ADMIN: admin-core.js
 * Digital Signage Masjid Jami' Al-Jihad
 * Versi: aljihad-signage-v5.8.46
 */

        // 1. Verifikasi Autentikasi Pengguna saat halaman dimuat
        var currentUser = null;
        var cachedKasData = [];
        var cachedAmbData = [];
        var cachedRotationPages = [];
        var cachedQurbanData = [];
        var cachedProgramInfaq = [];
        var cachedDonasiInfaq = [];
        var selectedProgramInfaqId = null;
        var cachedQrisList = [];
        var uploadedQrisBase64 = null;

        // Master List Resmi Seluruh 18 Halaman Layar Display TV Masjid Jami' Al-Jihad
        const MASTER_ROTATION_PAGES = [
            { order: 1, name: 'Jadwal Sholat 5 Waktu', path: 'slides/utama.html', url: 'slides/utama.html', page: '/utama-embed', category: 'Utama / Sholat', category_color: 'primary', active: true },
            { order: 2, name: 'Laporan Kas Masjid', path: 'slides/keuangan.html', url: 'slides/keuangan.html', page: '/keuangan-embed', category: 'Keuangan', category_color: 'success', active: true },
            { order: 3, name: 'Petugas Sholat Jum\'at', path: 'slides/jumat.html', url: 'slides/jumat.html', page: '/jumat-embed', category: 'Jum\'at', category_color: 'warning text-dark', active: true },
            { order: 4, name: 'Agenda Rutin', path: 'slides/pengumuman.html', url: 'slides/pengumuman.html', page: '/pengumuman-embed', category: 'Agenda Rutin', category_color: 'info', active: true },
            { order: 5, name: 'Grafik Arus Kas', path: 'slides/keuangan-summary.html', url: 'slides/keuangan-summary.html', page: '/keuangan-summary-embed', category: 'Keuangan', category_color: 'success', active: true },
            { order: 6, name: 'QRIS Donasi & Infaq', path: 'slides/qris.html', url: 'slides/qris.html', page: '/qris-embed', category: 'Donasi', category_color: 'danger', active: true },
            { order: 7, name: 'Galeri Informasi & Dokumentasi', path: 'slides/slide.html', url: 'slides/slide.html', page: '/slide-embed', category: 'Dokumentasi', category_color: 'info', active: true },
            { order: 8, name: 'Kas Layanan Ambulance', path: 'slides/ambulance.html', url: 'slides/ambulance.html', page: '/ambulance-embed', category: 'Ambulance', category_color: 'danger', active: true },
            { order: 9, name: 'Program Donasi Infaq', path: 'slides/infaq.html', url: 'slides/infaq.html', page: '/infaq-embed', category: 'Infaq', category_color: 'success', active: true },
            { order: 10, name: 'Mutiara Hadits & Hikmah', path: 'slides/hikmah.html', url: 'slides/hikmah.html', page: '/hikmah-embed', category: 'Dakwah AI', category_color: 'dark', active: true },
            { order: 11, name: 'Penerimaan Hewan Qurban', path: 'slides/qurban.html', url: 'slides/qurban.html', page: '/qurban-embed', category: 'Idul Adha', category_color: 'warning text-dark', active: true },
            { order: 12, name: 'Surat Yaasiin 83 Ayat', path: 'slides/yasin.html', url: 'slides/yasin.html', page: '/yasin-embed', category: 'Ibadah', category_color: 'primary', active: true },
            { order: 13, name: 'Live TV Makkah (Masjidil Haram)', path: 'slides/live-mekah.html', url: 'slides/live-mekah.html', page: '/live-mekah-embed', category: 'Live TV', category_color: 'danger', active: true },
            { order: 14, name: 'Live TV Madinah (Masjid Nabawi)', path: 'slides/live-madinah.html', url: 'slides/live-madinah.html', page: '/live-madinah-embed', category: 'Live TV', category_color: 'success', active: true },
            { order: 15, name: 'Live CCTV Mimbar Khutbah', path: 'slides/live-mimbar.html', url: 'slides/live-mimbar.html', page: '/live-mimbar-embed', category: 'CCTV Mimbar', category_color: 'warning text-dark', active: true },
            { order: 16, name: 'Petugas Sholat Idul Fitri', path: 'slides/idul-fitri.html', url: 'slides/idul-fitri.html', page: '/idul-fitri-embed', category: 'Hari Raya', category_color: 'info', active: false },
            { order: 17, name: 'Petugas Sholat Idul Adha', path: 'slides/idul-adha.html', url: 'slides/idul-adha.html', page: '/idul-adha-embed', category: 'Hari Raya', category_color: 'info', active: false },
            { order: 18, name: 'Semarak Ramadhan & Kas Tromol', path: 'slides/ramadhan.html', url: 'slides/ramadhan.html', page: '/ramadhan-embed', category: 'Ramadhan', category_color: 'warning text-dark', active: true },
            { order: 19, name: 'Pengajian Rutin Malam Ahad', path: 'slides/kajian.html', url: 'slides/kajian.html', page: '/kajian-embed', category: 'Kajian', category_color: 'success', active: true },
            { order: 20, name: "Maklumat & Kas Jum'at (Khusus Jum'at)", path: 'slides/kas-jumat.html', url: 'slides/kas-jumat.html', page: '/kas-jumat-embed', category: "Jum'at", category_color: 'warning text-dark', active: true }
        ];

        // Helper untuk ekstrak canonical key dari URL/Path/Page slide (contoh: 'slides/utama.html' -> 'utama')
        function getCanonicalSlideKey(str) {
            if (!str) return '';
            return str.toLowerCase()
                .replace(/^https?:\/\/[^\/]+/, '') // buang domain jika ada
                .replace(/^\//, '')                 // buang leading slash
                .replace(/^slides\//, '')           // buang folder slides/
                .replace(/-embed$/, '')             // buang akhiran -embed
                .replace(/\.html$/, '')             // buang akhiran .html
                .trim();
        }

        // Normalisasi array rotasi agar ke-19 halaman dijamin 100% selalu ada & TIDAK AKAN PERNAH DUPLIKAT
        function normalizeRotationPages(input) {
            let list = input;
            if (typeof list === 'string') {
                try { list = JSON.parse(list); } catch (e) { list = null; }
            }
            if (!Array.isArray(list) || list.length === 0) {
                return JSON.parse(JSON.stringify(MASTER_ROTATION_PAGES));
            }

            const result = [];
            const seenKeys = new Set();

            // 1. Masukkan item dari input yang cocok dengan master (Hanya 1x per slide unik)
            list.forEach(item => {
                if (!item) return;
                const rawKey = getCanonicalSlideKey(item.path || item.url || item.page || item.name || '');
                const master = MASTER_ROTATION_PAGES.find(m => {
                    return getCanonicalSlideKey(m.url) === rawKey || getCanonicalSlideKey(m.page) === rawKey;
                });

                if (master) {
                    const masterKey = getCanonicalSlideKey(master.url);
                    if (!seenKeys.has(masterKey)) {
                        seenKeys.add(masterKey);
                        result.push({
                            order: result.length + 1,
                            name: master.name,
                            title: master.name,
                            path: master.url,
                            url: master.url,
                            page: master.page,
                            category: master.category,
                            category_color: master.category_color,
                            active: (item.active !== false)
                        });
                    }
                }
            });

            // 2. Tambahkan sisa halaman master yang belum ada di input
            MASTER_ROTATION_PAGES.forEach(m => {
                const masterKey = getCanonicalSlideKey(m.url);
                if (!seenKeys.has(masterKey)) {
                    seenKeys.add(masterKey);
                    result.push({
                        order: result.length + 1,
                        name: m.name,
                        title: m.name,
                        path: m.url,
                        url: m.url,
                        page: m.page,
                        category: m.category,
                        category_color: m.category_color,
                        active: m.active
                    });
                }
            });

            // 3. Batasi dan beri nomor urut rapi tepat 1..18
            return result.slice(0, MASTER_ROTATION_PAGES.length).map((item, idx) => {
                item.order = idx + 1;
                return item;
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            if (typeof AdminAuth !== 'undefined') {
                currentUser = AdminAuth.requireAuth('login.html');
                if (currentUser) {
                    applyCurrentUserState(currentUser);
                }
            }

            // Inisialisasi Jam & Widget Topbar
            startTopbarClock();
            initDateInputs();
            initSidebarToggle();

            // Inisialisasi awal nilai interval rotasi TV dari cache / setting
            try {
                const savedInterval = localStorage.getItem('display_rotation_interval') || (localStorage.getItem('cached_app_settings') ? JSON.parse(localStorage.getItem('cached_app_settings')).rotation_interval : null);
                if (savedInterval) {
                    if (document.getElementById('cfgIntervalTV')) document.getElementById('cfgIntervalTV').value = savedInterval;
                    if (document.getElementById('cfgIntervalTVSystem')) document.getElementById('cfgIntervalTVSystem').value = savedInterval;
                }
            } catch (e) {}

            // Inisialisasi awal tabel rotasi (langsung dari cache lokal atau master 18 halaman)
            try {
                const localRot = localStorage.getItem('cached_rotation_pages');
                if (localRot) {
                    cachedRotationPages = normalizeRotationPages(localRot);
                } else {
                    cachedRotationPages = JSON.parse(JSON.stringify(MASTER_ROTATION_PAGES));
                }
            } catch (e) {
                cachedRotationPages = JSON.parse(JSON.stringify(MASTER_ROTATION_PAGES));
            }
            // Simpan cache bersih kembali ke localStorage agar data kotor/duplikat langsung lenyap
            localStorage.setItem('cached_rotation_pages', JSON.stringify(cachedRotationPages));
            renderRotationTable(cachedRotationPages);
            renderUsersTable();
            renderAdminRamadhanView();
            // Inisialisasi awal panel running text 19 halaman rotasi
            try {
                const localRT = localStorage.getItem('cached_running_text_pages');
                if (localRT) {
                    cachedRunningTextPages = JSON.parse(localRT);
                }
            } catch (e) {}
            renderRunningTextPagesPanel(cachedRunningTextPages);

            // Muat Data Live dari Supabase
            loadAllSupabaseData();

            // Realtime Listener Lintas Perangkat (Multi-Device Auto-Sync Anti-Freeze)
            if (window.SupabaseDB && typeof window.SupabaseDB.onDataChange === 'function') {
                window.SupabaseDB.onDataChange((evt) => {
                    console.log('🔄 [Admin Realtime] Perubahan data terdeteksi:', evt);
                    // Sinkronisasi realtime daftar akun pengurus jika rbac_users_list diperbarui di Supabase
                    if (evt && (evt.key === 'rbac_users_list' || (evt.table === 'app_settings' && evt.payload && (evt.payload.key === 'rbac_users_list' || evt.payload.rbac_users_list)))) {
                        console.log('🔄 [Admin Realtime] Data rbac_users_list diperbarui dari Cloud. Memperbarui tabel user...');
                        if (typeof AdminAuth !== 'undefined' && typeof AdminAuth.syncUsersFromCloud === 'function') {
                            AdminAuth.syncUsersFromCloud().then(() => {
                                if (typeof renderUsersTable === 'function') renderUsersTable();
                                const activeU = AdminAuth.getCurrentUser();
                                if (activeU) applyCurrentUserState(activeU);
                            }).catch(() => {});
                        }
                    }

                    if (evt && (evt.table === 'app_settings' || evt.key === 'app_settings') && evt.payload) {
                        const s = evt.payload;
                        const cd = s.prayer_mode_before_adzan || s.countdown_adzan_duration;
                        if (document.getElementById('cfgCountdownAdzan') && cd) document.getElementById('cfgCountdownAdzan').value = cd;
                        const ad = s.prayer_mode_adzan_duration || s.adzan_duration;
                        if (document.getElementById('cfgDurasiAdzan') && ad) document.getElementById('cfgDurasiAdzan').value = ad;
                        const iq = s.prayer_mode_iqamah_duration || s.iqamah_duration;
                        if (document.getElementById('cfgDurasiIqamah') && iq) document.getElementById('cfgDurasiIqamah').value = iq;
                        const sh = s.prayer_mode_after_prayer || s.prayer_mode_duration;
                        if (document.getElementById('cfgDurasiSholat') && sh) document.getElementById('cfgDurasiSholat').value = sh;
                        const jm = parseInt(s.prayer_mode_jumat_duration || s.jumat_durasi);
                        if (jm) syncDurasiJumat(jm);
                        if (s.prayer_countdown_style && document.getElementById('cfgCountdownStyle')) {
                            document.getElementById('cfgCountdownStyle').value = s.prayer_countdown_style;
                        }
                    }
                    debouncedLoadAllSupabaseData(600);
                });
            }
            if (window.SupabaseDB && typeof window.SupabaseDB.onRemoteCommand === 'function') {
                window.SupabaseDB.onRemoteCommand((cmd, payload) => {
                    if (cmd === 'SYNC_AUTH_USERS') {
                        console.log('🔄 [Admin Realtime] Perintah remote SYNC_AUTH_USERS diterima. Memperbarui tabel akun...');
                        if (typeof AdminAuth !== 'undefined' && typeof AdminAuth.syncUsersFromCloud === 'function') {
                            AdminAuth.syncUsersFromCloud().then(() => {
                                if (typeof renderUsersTable === 'function') renderUsersTable();
                                const activeU = AdminAuth.getCurrentUser();
                                if (activeU) applyCurrentUserState(activeU);
                            }).catch(() => {});
                        }
                    }
                    if (cmd === 'SYNC_JADWAL_SHOLAT' || cmd === 'UPDATE_SETTINGS') {
                        if (payload) {
                            const s = payload;
                            const cd = s.prayer_mode_before_adzan || s.countdown_adzan_duration;
                            if (document.getElementById('cfgCountdownAdzan') && cd) document.getElementById('cfgCountdownAdzan').value = cd;
                            const ad = s.prayer_mode_adzan_duration || s.adzan_duration;
                            if (document.getElementById('cfgDurasiAdzan') && ad) document.getElementById('cfgDurasiAdzan').value = ad;
                            const iq = s.prayer_mode_iqamah_duration || s.iqamah_duration;
                            if (document.getElementById('cfgDurasiIqamah') && iq) document.getElementById('cfgDurasiIqamah').value = iq;
                            const sh = s.prayer_mode_after_prayer || s.prayer_mode_duration;
                            if (document.getElementById('cfgDurasiSholat') && sh) document.getElementById('cfgDurasiSholat').value = sh;
                            const jm = parseInt(s.prayer_mode_jumat_duration || s.jumat_durasi);
                            if (jm) syncDurasiJumat(jm);
                            if (s.prayer_countdown_style && document.getElementById('cfgCountdownStyle')) {
                                document.getElementById('cfgCountdownStyle').value = s.prayer_countdown_style;
                            }
                        }
                        debouncedLoadAllSupabaseData(600);
                    }
                });
            }

            // PWA Service Worker Registration (Silent Background Update - Tanpa Reload Kasar yang Membekukan Browser)
            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.addEventListener('controllerchange', () => {
                    console.log('🔄 [Admin PWA] Service Worker versi baru telah aktif di latar belakang (tanpa reload paksa).');
                });

                navigator.serviceWorker.register('sw.js').then((reg) => {
                    console.log('✅ [Admin PWA] Service Worker aktif:', reg.scope);
                }).catch((err) => {
                    console.log('ℹ️ [Admin PWA] SW info:', err);
                });
            }
        });

        // =======================================================
        // HELPER SINKRONISASI PRAYER MODE & DURASI SHOLAT JUMAT
        // =======================================================
        function syncDurasiJumat(val) {
            const num = Math.max(10, parseInt(val) || 50);
            const cfgEl = document.getElementById('cfgDurasiJumat');
            const jumatEl = document.getElementById('jumatDurasiInput');
            const badgeEl = document.getElementById('prevJumatDurasiBadge');
            if (cfgEl && cfgEl.value != num) cfgEl.value = num;
            if (jumatEl && jumatEl.value != num) jumatEl.value = num;
            if (badgeEl) badgeEl.innerHTML = `<i class="fas fa-hourglass-half mr-1"></i> Durasi TV: ${num} Menit`;
            try { localStorage.setItem('cached_prayer_mode_jumat_duration', num); } catch (e) {}
        }

        function handlePrayerModeSwitch(input) {
            const isChecked = input.checked;
            try { localStorage.setItem('cached_prayer_mode_enabled', isChecked); } catch (e) {}
        }

        // =======================================================
        // TOGGLE SIDEBAR UNIVERSAL (DESKTOP COLLAPSIBLE & MOBILE DRAWER)
        // =======================================================
        function toggleSidebarUniversal(e) {
            if (e) {
                if (typeof e.preventDefault === 'function') e.preventDefault();
                if (typeof e.stopPropagation === 'function') e.stopPropagation();
            }

            const sidebar = document.getElementById('accordionSidebar');
            const icon = document.getElementById('iconSidebarToggle');
            const btnTop = document.getElementById('sidebarToggleTop');
            const isMobile = window.innerWidth <= 768;

            if (isMobile) {
                // Mobile off-canvas drawer mode
                let backdrop = document.querySelector('.sidebar-backdrop');
                if (!backdrop) {
                    backdrop = document.createElement('div');
                    backdrop.className = 'sidebar-backdrop';
                    document.body.appendChild(backdrop);
                    backdrop.addEventListener('click', function() {
                        sidebar?.classList.remove('show-mobile');
                        backdrop.classList.remove('show');
                        document.body.style.overflow = '';
                    });
                }

                const isOpen = sidebar?.classList.toggle('show-mobile');
                if (isOpen) {
                    backdrop.classList.add('show');
                    document.body.style.overflow = 'hidden';
                } else {
                    backdrop.classList.remove('show');
                    document.body.style.overflow = '';
                }
            } else {
                // Desktop collapsible mode (Full Show / Hide)
                const isCollapsed = document.body.classList.toggle('sidebar-toggled');
                if (sidebar) {
                    sidebar.classList.toggle('toggled', isCollapsed);
                }
                localStorage.setItem('aljihad_sidebar_collapsed', isCollapsed ? '1' : '0');

                if (btnTop) {
                    btnTop.setAttribute('title', isCollapsed ? 'Tampilkan Menu Sidebar (Ctrl+B)' : 'Sembunyikan Menu Sidebar (Ctrl+B)');
                }
            }
        }
        window.toggleSidebarUniversal = toggleSidebarUniversal;

        // Inisialisasi Sidebar Show / Hide saat Halaman Dimuat
        function initSidebarToggle() {
            const sidebar = document.getElementById('accordionSidebar');
            const toggleTop = document.getElementById('sidebarToggleTop');
            const toggleBtn = document.getElementById('sidebarToggle');
            
            // Siapkan backdrop mobile jika belum ada
            let backdrop = document.querySelector('.sidebar-backdrop');
            if (!backdrop) {
                backdrop = document.createElement('div');
                backdrop.className = 'sidebar-backdrop';
                document.body.appendChild(backdrop);
                backdrop.addEventListener('click', function() {
                    sidebar?.classList.remove('show-mobile');
                    backdrop.classList.remove('show');
                    document.body.style.overflow = '';
                });
            }

            // 1. Pulihkan status sidebar Desktop dari localStorage
            const savedState = localStorage.getItem('aljihad_sidebar_collapsed');
            if (savedState === '1' && window.innerWidth > 768) {
                document.body.classList.add('sidebar-toggled');
                sidebar?.classList.add('toggled');
            } else if (savedState === '0' && window.innerWidth > 768) {
                document.body.classList.remove('sidebar-toggled');
                sidebar?.classList.remove('toggled');
            }

            // 2. Pasang event listener cadangan jika atribut inline tidak terpicu
            if (toggleTop) {
                toggleTop.onclick = toggleSidebarUniversal;
            }
            if (toggleBtn) {
                toggleBtn.onclick = toggleSidebarUniversal;
            }

            // 3. Shortcut Keyboard Universal Ctrl+B untuk Tampilkan / Sembunyikan Sidebar
            window.addEventListener('keydown', function(e) {
                if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'b') {
                    e.preventDefault();
                    toggleSidebarUniversal(e);
                }
            });

            // 4. Auto-close drawer saat item navigasi sidebar diklik di mobile
            document.querySelectorAll('#accordionSidebar .nav-item .nav-link').forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth <= 768) {
                        sidebar?.classList.remove('show-mobile');
                        backdrop?.classList.remove('show');
                        document.body.style.overflow = '';
                    }
                });
            });
        }

        // 2. Terapkan Sesi Pengguna & RBAC
        function applyCurrentUserState(user) {
            currentUser = user;
            AdminAuth.applyRBAC(user);

            // Pasang data-role pada body untuk selektor styling
            document.body.setAttribute('data-role', user.role || 'petugas');
            document.body.classList.remove('role-admin', 'role-bendahara', 'role-petugas');
            document.body.classList.add('role-' + (user.role || 'petugas'));

            // Perbarui tombol role switch aktif di topbar
            document.querySelectorAll('.role-switch-btn').forEach(b => b.classList.remove('active'));
            const btnAdmin = document.getElementById('btnSwitchAdmin');
            const btnBendahara = document.getElementById('btnSwitchBendahara');
            const btnOperator = document.getElementById('btnSwitchOperator');

            // HILANGKAN TEKS / TOMBOL SUPER ADMIN PADA DASHBOARD BENDAHARA & PETUGAS
            if (btnAdmin) {
                if (user.role === 'admin') {
                    btnAdmin.style.display = 'inline-flex';
                    btnAdmin.classList.add('active');
                } else {
                    btnAdmin.style.display = 'none'; // Sembunyikan total di peran Bendahara & Petugas
                }
            }

            if (user.role === 'bendahara') {
                btnBendahara?.classList.add('active');
            } else if (user.role === 'petugas') {
                btnOperator?.classList.add('active');
            }

            // Atur Reorder Tombol di Tabel Rotasi TV (Super Admin vs Operator)
            const isSuperAdmin = (user.role === 'admin');
            document.querySelectorAll('.btn-reorder').forEach(btn => {
                btn.disabled = !isSuperAdmin;
                if (!isSuperAdmin) {
                    btn.classList.add('disabled');
                    btn.style.opacity = '0.4';
                    btn.style.cursor = 'not-allowed';
                } else {
                    btn.classList.remove('disabled');
                    btn.style.opacity = '1';
                    btn.style.cursor = 'pointer';
                }
            });

            const badgeRot = document.getElementById('badgeRotasiRole');
            if (badgeRot) {
                if (isSuperAdmin) {
                    badgeRot.style.display = 'inline-block';
                    badgeRot.className = 'badge badge-warning font-weight-bold px-3 py-2';
                    badgeRot.innerHTML = '<i class="fas fa-shield-alt mr-1"></i> Mode Super Admin (Reorder ▲/▼ Aktif)';
                } else {
                    badgeRot.style.display = 'none';
                    badgeRot.innerHTML = '';
                }
            }

            const subTitleRot = document.getElementById('descRotasiSubtitle');
            if (subTitleRot) {
                if (isSuperAdmin) {
                    subTitleRot.textContent = 'Kelola urutan dan status aktif seluruh 17 halaman tayang slide TV. Super Admin dapat memindah urutan (▲/▼).';
                } else {
                    subTitleRot.textContent = 'Untuk mengelola/mengatur urutan halaman rotasi, silakan menghubungi Admin.';
                }
            }

            // Tentukan View default jika peran bendahara
            if (user.role === 'bendahara') {
                const activeView = document.querySelector('.section-view.active');
                if (activeView && (activeView.id === 'view-jadwal-sholat' || activeView.id === 'view-sholat-jumat' || activeView.id === 'view-rotasi-tv' || activeView.id === 'view-running-text' || activeView.id === 'view-users' || activeView.id === 'view-settings')) {
                    switchAdminSection('keuangan');
                }
            }

            // Update Welcome Hero Banner Greeting Sesuai Peran Aktif Pengguna
            updateWelcomeBannerGreeting(user);

            // Khusus Super Admin: Panel Pengaturan Timer Rotasi & Penguncian Kajian
            const cardSuperTimer = document.getElementById('cardSuperAdminKajianTimer');
            if (cardSuperTimer) {
                cardSuperTimer.style.display = isSuperAdmin ? 'block' : 'none';
            }

            // Render ulang tabel pengguna sesuai hak akses terkini
            renderUsersTable();
        }

        // Helper: Toggle Tampilan Opsi Penguncian Kajian Sesuai Mode
        function toggleKajianLockModeUI() {
            const mode = document.getElementById('cfgKajianLockMode')?.value || 'full';
            const wrapDur = document.getElementById('wrapKajianLockDuration');
            const wrapInt = document.getElementById('wrapKajianLockInterleave');
            if (wrapDur) wrapDur.style.display = (mode === 'timer') ? 'block' : 'none';
            if (wrapInt) wrapInt.style.display = (mode === 'interleaved') ? 'block' : 'none';
        }
        window.toggleKajianLockModeUI = toggleKajianLockModeUI;

        // Helper: Pembaruan Banner Sapaan Waktu & Gelar Resmi Berdasarkan Peran (100% Dinamis dari Edit Akun)
        function updateWelcomeBannerGreeting(user) {
            if (!user) return;
            const greetingData = (typeof AdminAuth !== 'undefined' && typeof AdminAuth.getFormattedGreeting === 'function')
                ? AdminAuth.getFormattedGreeting(user)
                : null;

            const greetingTimeEl = document.getElementById('heroGreetingTime');
            const heroGreetingUserEl = document.getElementById('heroGreetingUser');
            const welcomeHeroTitleEl = document.getElementById('welcomeHeroTitle');

            if (greetingData) {
                if (greetingTimeEl) greetingTimeEl.textContent = greetingData.salamWaktu;
                if (heroGreetingUserEl) heroGreetingUserEl.textContent = `${greetingData.displayName} ${greetingData.roleSuffix}`;
                if (welcomeHeroTitleEl) {
                    welcomeHeroTitleEl.innerHTML = `Selamat Datang, <span class="auth-welcome-name text-warning font-weight-bold" id="heroGreetingUser">${greetingData.displayName} ${greetingData.roleSuffix}</span>!`;
                }
            } else {
                const hours = new Date().getHours();
                let salamWaktu = 'Selamat Malam,';
                if (hours >= 3 && hours < 11) salamWaktu = 'Selamat Pagi,';
                else if (hours >= 11 && hours < 15) salamWaktu = 'Selamat Siang,';
                else if (hours >= 15 && hours < 18) salamWaktu = 'Selamat Sore,';

                if (greetingTimeEl) greetingTimeEl.textContent = salamWaktu;
            }
        }

        // 3. Switch Role Instan (Demo Mode)
        function switchRoleDemo(roleKey) {
            AdminAuth.switchRole(roleKey);
        }

        // 4. Navigasi Seksi Antara Menu
        function switchAdminSection(sectionName) {
            // Guard: Batasi akses menu QRIS & Super Admin khusus peran admin
            const currentUser = (typeof AdminAuth !== 'undefined') ? AdminAuth.getCurrentUser() : null;
            const currentRole = currentUser ? (currentUser.role || 'petugas') : 'petugas';
            if (sectionName === 'qris' && currentRole !== 'admin') {
                sectionName = (currentRole === 'bendahara') ? 'keuangan' : 'dashboard';
            }

            document.querySelectorAll('.section-view').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('#accordionSidebar .nav-item').forEach(el => el.classList.remove('active'));

            const targetView = document.getElementById('view-' + sectionName);
            const targetNav = document.getElementById('nav-' + sectionName);

            if (targetView) targetView.classList.add('active');
            if (targetNav) targetNav.classList.add('active');

            if (sectionName === 'users') {
                renderUsersTable();
            } else if (sectionName === 'galeri-informasi') {
                renderGaleriTable();
            } else if (sectionName === 'rotasi-tv') {
                renderRotationTable(cachedRotationPages);
            } else if (sectionName === 'ramadhan') {
                renderAdminRamadhanView();
            } else if (sectionName === 'infaq') {
                renderProgramInfaqSection();
            } else if (sectionName === 'qris') {
                renderQrisTable();
            } else if (sectionName === 'remote-tv') {
                initRemoteControlView();
            } else if (sectionName === 'agenda-rutin') {
                loadAgendaRutinAdmin();
            } else if (sectionName === 'undangan-eksternal') {
                loadUndanganEksternalAdmin();
            } else if (sectionName === 'pengumuman-jumat') {
                initPengumumanJumatView();
            }

            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // =====================================================

        // 5. Inisialisasi Jam & Widget Topbar
        function startTopbarClock() {
            const timeEl = document.getElementById('topbarClockTime');
            const dateEl = document.getElementById('topbarClockDate');

            function updateTime() {
                const now = new Date();
                if (timeEl) {
                    timeEl.textContent = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }).replace(/\./g, ':');
                }
                if (dateEl) {
                    dateEl.textContent = now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'short', year: 'numeric' });
                }

                // Update ucapan selamat mengikuti jam secara real-time pada Banner Selamat Datang
                if (typeof AdminAuth !== 'undefined' && typeof AdminAuth.getGreetingWaktu === 'function') {
                    const greetingTimeEl = document.getElementById('heroGreetingTime');
                    if (greetingTimeEl) {
                        const currentSalam = AdminAuth.getGreetingWaktu();
                        if (greetingTimeEl.textContent !== currentSalam) {
                            greetingTimeEl.textContent = currentSalam;
                        }
                    }
                }
            }
            updateTime();
            setInterval(updateTime, 1000);
        }

        function initDateInputs() {
            const today = new Date().toISOString().split('T')[0];
            const kasTgl = document.getElementById('kasTanggal');
            if (kasTgl) kasTgl.value = today;
            const jumTgl = document.getElementById('jumatTanggal');
            if (jumTgl && !jumTgl.value) jumTgl.value = today;
            const donasiTgl = document.getElementById('donasiTanggal');
            if (donasiTgl && !donasiTgl.value) donasiTgl.value = today;
        }

        function formatDateIndo(dateStr) {
            if (!dateStr) return '-';
            try {
                const parts = dateStr.split('T')[0].split('-');
                if (parts.length === 3) {
                    return `${parts[2]}/${parts[1]}/${parts[0]}`;
                }
                const d = new Date(dateStr);
                if (isNaN(d.getTime())) return dateStr;
                return d.toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' });
            } catch (e) {
                return dateStr;
            }
        }

        function formatRupiah(num) {
            return 'Rp ' + Number(num || 0).toLocaleString('id-ID');
        }

        // Format input rupiah secara dinamis dengan pemisah ribuan titik (1.000.000)
        function formatRupiahInput(el) {
            if (!el) return;
            let value = (el.value || '').toString().replace(/[^0-9]/g, '');
            el.value = value ? parseInt(value, 10).toLocaleString('id-ID') : '';
        }

        // Parse nominal rupiah dari input bertitik menjadi float angka murni
        function parseRupiahInput(val) {
            if (!val) return 0;
            return parseFloat(val.toString().replace(/[^0-9]/g, '')) || 0;
        }

        // Buka Modal Catat Transaksi Kas dengan kategori akun terpilih otomatis
        function bukaModalCatatKas(kategoriDefault = 'Kas Utama Masjid') {
            const today = new Date().toISOString().split('T')[0];
            const kasTgl = document.getElementById('kasTanggal');
            if (kasTgl) kasTgl.value = today;

            const kasKat = document.getElementById('kasKategori');
            if (kasKat && kategoriDefault) {
                kasKat.value = kategoriDefault;
            }

            const kasUraian = document.getElementById('kasUraian');
            if (kasUraian) kasUraian.value = '';

            const kasNominal = document.getElementById('kasNominal');
            if (kasNominal) kasNominal.value = '';

            $('#modalTambahKas').modal('show');
        }

        function getSupabaseHeaders() {
            return {
                'apikey': SUPABASE_CONFIG.anonKey,
                'Authorization': `Bearer ${SUPABASE_CONFIG.anonKey}`,
                'Content-Type': 'application/json'
            };
        }

        // =======================================================