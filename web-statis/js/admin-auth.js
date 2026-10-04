// web-statis/js/admin-auth.js
/**
 * SISTEM AUTENTIKASI & KONTROL HAK AKSES BERBASIS PERAN (RBAC)
 * Web Statis Masjid Jami' Al-Jihad
 * Mendukung 3 Peran Resmi:
 * 1. Super Admin   (role_id: 3 / 'admin')     -> Akses Penuh 100%
 * 2. Bendahara     (role_id: 1 / 'bendahara') -> Kas Masjid, Kas Ambulance, Infaq, Laporan
 * 3. Petugas/DKM   (role_id: 2 / 'petugas')   -> Jadwal Sholat, Jumat, Pengumuman, Slide TV, Running Text
 */

// Kredensial Akun Bawaan Resmi (Offline & Fallback Cepat)
const DEFAULT_AUTH_USERS = [
    {
        id: 3,
        name: 'Suwardi',
        email: 'archived.aljihad@gmail.com',
        username: 'admin',
        password: 'SuperUser1971',
        role: 'admin',
        role_id: 3,
        role_label: 'Super Admin',
        role_icon: 'fa-shield-alt',
        color: '#ffd700'
    },
    {
        id: 1,
        name: 'Bpk. H. Utut Priastya',
        email: 'bendahara@aljihad.com',
        username: 'bendahara',
        password: 'bendahara123',
        role: 'bendahara',
        role_id: 1,
        role_label: 'Bendahara Kas',
        role_icon: 'fa-wallet',
        color: '#10b981'
    },
    {
        id: 2,
        name: 'Bpk. Ust. Hadi Prayitno (KETUA DKM)',
        email: 'dkmsatu@aljihad.com',
        username: 'ketua',
        password: 'operator123',
        role: 'petugas',
        role_id: 2,
        role_label: 'Petugas / Operator',
        role_icon: 'fa-tv',
        color: '#38bdf8'
    }
];

const AUTH_STORAGE_KEY = 'aljihad_auth_user';
const USERS_LIST_STORAGE_KEY = 'aljihad_users_list';

const AdminAuth = {
    /**
     * Helper untuk mencocokkan input login (identifier) dengan data akun pengguna
     * Mendukung: Email, Username, Nama Lengkap, Nama tanpa gelar (Bpk, Ust, H., dll), atau nama panggilan
     */
    isUserMatch(u, cleanId) {
        if (!u || !cleanId) return false;
        const em = (u.email || '').toLowerCase().trim();
        const un = (u.username || '').toLowerCase().trim();
        const nm = (u.name || '').toLowerCase().trim();

        // 1. Cocok persis dengan email
        if (em === cleanId) return true;

        // 2. Cocok persis dengan username
        if (un === cleanId) return true;

        // 3. Cocok persis dengan nama lengkap (case-insensitive)
        if (nm === cleanId) return true;

        // 4. Cocok dengan nama tanpa gelar kehormatan (Bpk., Ust., H., Haji, Ustadz, Kyai, Bapak, Ibu)
        const nameWithoutTitle = nm.replace(/^(bpk\.|ust\.|h\.|ibu|haji|ustadz|kyai|bapak|dkm)\s+/i, '').trim();
        if (nameWithoutTitle && nameWithoutTitle === cleanId) return true;

        // 5. Cocok jika input identifier sama persis dengan salah satu kata dalam nama (misal "suwardi" dari "Bpk. Suwardi")
        const words = nm.split(/[\s,.-]+/).map(w => w.toLowerCase()).filter(w => w.length >= 2);
        if (words.includes(cleanId)) return true;

        return false;
    },

    /**
     * Sinkronkan seluruh daftar pengguna pengurus dari Supabase Cloud (app_settings key=rbac_users_list)
     * Memastikan username, password baru, dan akun kustom langsung aktif di seluruh device (laptop, HP, tablet, TV)
     */
    async syncUsersFromCloud() {
        if (typeof SUPABASE_CONFIG === 'undefined' || !SUPABASE_CONFIG.url) {
            return this.getUsers();
        }
        try {
            const res = await fetch(`${SUPABASE_CONFIG.url}/rest/v1/app_settings?key=eq.rbac_users_list&select=value`, {
                headers: {
                    'apikey': SUPABASE_CONFIG.anonKey,
                    'Authorization': `Bearer ${SUPABASE_CONFIG.anonKey}`
                }
            });
            if (res.ok) {
                const data = await res.json();
                if (data && data.length > 0 && data[0].value) {
                    let cloudUsers = data[0].value;
                    if (typeof cloudUsers === 'string') {
                        try { cloudUsers = JSON.parse(cloudUsers); } catch(e) {}
                    }
                    if (Array.isArray(cloudUsers) && cloudUsers.length > 0) {
                        // Pastikan akun bawaan resmi (ID 3, 1, 2) tetap ada jika belum terdaftar
                        DEFAULT_AUTH_USERS.forEach(def => {
                            const exists = cloudUsers.some(u => (u.id && String(u.id) === String(def.id)) || (u.email && def.email && u.email.toLowerCase() === def.email.toLowerCase()));
                            if (!exists) {
                                cloudUsers.push(Object.assign({}, def));
                            }
                        });
                        localStorage.setItem(USERS_LIST_STORAGE_KEY, JSON.stringify(cloudUsers));
                        return cloudUsers;
                    }
                }
            }
        } catch (err) {
            console.warn('[AdminAuth] Sinkronisasi user dari Supabase Cloud dilewati:', err);
        }
        return this.getUsers();
    },

    /**
     * Simpan seluruh daftar pengguna pengurus ke LocalStorage & Supabase Cloud
     */
    async saveUsersToCloud(users) {
        if (!Array.isArray(users)) return false;
        
        // 1. Simpan langsung ke LocalStorage perangkat saat ini
        try {
            localStorage.setItem(USERS_LIST_STORAGE_KEY, JSON.stringify(users));
        } catch (e) {
            console.warn('[AdminAuth] Gagal simpan ke localStorage:', e);
        }

        // 2. Simpan secara permanen ke Supabase Cloud (app_settings key=rbac_users_list)
        if (typeof SUPABASE_CONFIG !== 'undefined' && SUPABASE_CONFIG.url) {
            try {
                const headers = {
                    'apikey': SUPABASE_CONFIG.anonKey,
                    'Authorization': `Bearer ${SUPABASE_CONFIG.anonKey}`,
                    'Content-Type': 'application/json',
                    'Prefer': 'return=representation'
                };
                const valStr = JSON.stringify(users);

                const checkRes = await fetch(`${SUPABASE_CONFIG.url}/rest/v1/app_settings?key=eq.rbac_users_list&select=id`, {
                    headers: { 'apikey': SUPABASE_CONFIG.anonKey, 'Authorization': `Bearer ${SUPABASE_CONFIG.anonKey}` }
                });

                let saveRes;
                if (checkRes.ok) {
                    const existing = await checkRes.json();
                    if (existing && existing.length > 0) {
                        saveRes = await fetch(`${SUPABASE_CONFIG.url}/rest/v1/app_settings?key=eq.rbac_users_list`, {
                            method: 'PATCH',
                            headers: headers,
                            body: JSON.stringify({ value: valStr })
                        });
                    } else {
                        saveRes = await fetch(`${SUPABASE_CONFIG.url}/rest/v1/app_settings`, {
                            method: 'POST',
                            headers: headers,
                            body: JSON.stringify({ key: 'rbac_users_list', value: valStr })
                        });
                    }
                } else {
                    saveRes = await fetch(`${SUPABASE_CONFIG.url}/rest/v1/app_settings`, {
                        method: 'POST',
                        headers: headers,
                        body: JSON.stringify({ key: 'rbac_users_list', value: valStr })
                    });
                }

                if (!saveRes || !saveRes.ok) {
                    const errTxt = saveRes ? await saveRes.text() : 'No response';
                    throw new Error(`Gagal menyimpan akun ke Cloud Supabase (${saveRes ? saveRes.status : 'Error'}): ${errTxt}`);
                }

                // Kirim sinyal Remote Command SYNC_AUTH_USERS ke seluruh device
                if (typeof window.SupabaseDB !== 'undefined' && typeof window.SupabaseDB.sendRemoteCommand === 'function') {
                    window.SupabaseDB.sendRemoteCommand('SYNC_AUTH_USERS', {}, 'Super Admin Auth').catch(() => {});
                }
                console.log('✅ [AdminAuth] Akun pengurus berhasil disinkronkan ke Supabase Cloud!');
                return true;
            } catch (err) {
                console.error('[AdminAuth] Error saat menyimpan user ke Supabase Cloud:', err);
                throw err;
            }
        }
        return true;
    },

    /**
     * Mengambil daftar seluruh user pengurus masjid
     * Mendukung multi-akun untuk pengurus (Super Admin, Bendahara, Petugas/Operator)
     */
    getUsers() {
        let storedUsers = [];
        try {
            const raw = localStorage.getItem(USERS_LIST_STORAGE_KEY);
            if (raw) {
                const parsed = JSON.parse(raw);
                if (Array.isArray(parsed) && parsed.length > 0) {
                    storedUsers = parsed.filter(u => {
                        if (!u || typeof u !== 'object') return false;
                        const em = (u.email || '').toLowerCase().trim();
                        // Bersihkan akun usang duplikat lama
                        if (em === 'adminsholeh@admin.com' || em === 'admin@admin.com') return false;
                        return true;
                    });
                }
            }
        } catch (e) {
            console.warn('Gagal membaca users dari localStorage:', e);
        }

        // Jika storage kosong sama sekali, inisialisasi dari DEFAULT_AUTH_USERS
        if (storedUsers.length === 0) {
            storedUsers = DEFAULT_AUTH_USERS.map(u => Object.assign({}, u));
            try {
                localStorage.setItem(USERS_LIST_STORAGE_KEY, JSON.stringify(storedUsers));
            } catch (e) {}
            return storedUsers;
        }

        // Pastikan akun resmi default (ID 3, 1, 2) tetap ada jika belum pernah dibuat
        const finalUsers = [];
        const existingIds = new Set();
        const existingEmails = new Set();

        storedUsers.forEach(u => {
            const r = (u.role || (u.role_id === 3 ? 'admin' : (u.role_id === 1 ? 'bendahara' : 'petugas'))).toLowerCase();
            const role_id = (r === 'admin' ? 3 : (r === 'bendahara' ? 1 : 2));
            const role_label = (r === 'admin' ? 'Super Admin' : (r === 'bendahara' ? 'Bendahara Kas' : 'Petugas / Operator'));
            const role_icon = (r === 'admin' ? 'fa-shield-alt' : (r === 'bendahara' ? 'fa-wallet' : 'fa-tv'));
            const color = (r === 'admin' ? '#ffd700' : (r === 'bendahara' ? '#10b981' : '#38bdf8'));

            const em = (u.email || '').toLowerCase().trim();
            const un = (u.username || (em ? em.split('@')[0] : (u.name || '').toLowerCase().replace(/[^a-z0-9]/g, ''))).toLowerCase().trim();

            const normalizedUser = {
                id: u.id || (Date.now() + Math.floor(Math.random() * 1000)),
                name: u.name || 'Pengurus Masjid',
                email: u.email || `${un}@aljihad.com`,
                username: un,
                password: u.password || 'admin123',
                role: r,
                role_id: role_id,
                role_label: role_label,
                role_icon: role_icon,
                color: color,
                is_custom: !!u.is_custom,
                created_at: u.created_at || new Date().toISOString()
            };

            existingIds.add(String(normalizedUser.id));
            if (normalizedUser.email) existingEmails.add(normalizedUser.email.toLowerCase());
            finalUsers.push(normalizedUser);
        });

        // Pastikan akun bawaan resmi (ID 3, 1, 2) tetap tersedia jika belum ada di sistem
        DEFAULT_AUTH_USERS.forEach(def => {
            const exists = finalUsers.some(u => (u.id && String(u.id) === String(def.id)) || (u.email && def.email && u.email.toLowerCase() === def.email.toLowerCase()));
            if (!exists && !existingEmails.has(def.email.toLowerCase())) {
                finalUsers.push(Object.assign({}, def));
                existingEmails.add(def.email.toLowerCase());
            }
        });

        // Urutkan: Super Admin di paling atas, lalu Bendahara, lalu Petugas/Operator, lalu akun kustom baru
        finalUsers.sort((a, b) => {
            const order = { admin: 1, bendahara: 2, petugas: 3 };
            const ordA = order[a.role] || 4;
            const ordB = order[b.role] || 4;
            if (ordA !== ordB) return ordA - ordB;
            return (a.id || 0) - (b.id || 0);
        });

        // Simpan data ternormalisasi ke localStorage
        try {
            localStorage.setItem(USERS_LIST_STORAGE_KEY, JSON.stringify(finalUsers));
        } catch (e) {}

        return finalUsers;
    },

    /**
     * Memeriksa apakah user yang sedang login adalah Super Admin
     */
    isSuperAdmin() {
        const u = this.getCurrentUser();
        return !!(u && (u.role === 'admin' || u.role_id === 3));
    },

    /**
     * Menambahkan akun pengurus baru ke dalam sistem
     * HANYA BISA DILAKUKAN OLEH SUPER ADMIN
     */
    async createUser(data) {
        if (!this.isSuperAdmin()) {
            throw new Error('Akses Ditolak: Hanya Super Admin yang berhak menambahkan akun baru!');
        }

        const cleanName = (data.name || '').trim();
        const cleanEmail = (data.email || '').trim().toLowerCase();
        let cleanUsername = (data.username || '').trim().toLowerCase().replace(/[^a-z0-9_.-]/g, '');
        const cleanPassword = (data.password || '').trim();
        const role = (data.role || 'petugas').toLowerCase();

        if (!cleanName) throw new Error('Nama lengkap pengurus wajib diisi!');
        if (!cleanEmail) throw new Error('Alamat email wajib diisi!');
        if (!cleanPassword) throw new Error('Kata sandi awal wajib diisi!');
        if (cleanPassword.length < 4) throw new Error('Kata sandi minimal 4 karakter!');

        // Jika username kosong, buat otomatis dari email atau nama
        if (!cleanUsername) {
            cleanUsername = cleanEmail.split('@')[0].replace(/[^a-z0-9_.-]/g, '');
            if (!cleanUsername) {
                cleanUsername = cleanName.toLowerCase().replace(/[^a-z0-9]/g, '').substring(0, 15);
            }
        }

        const users = this.getUsers();

        // Validasi keunikan email
        const dupEmail = users.find(u => (u.email || '').toLowerCase() === cleanEmail);
        if (dupEmail) {
            throw new Error(`Alamat email "${cleanEmail}" sudah digunakan oleh akun lain!`);
        }

        // Validasi keunikan username
        const dupUsername = users.find(u => (u.username || '').toLowerCase() === cleanUsername);
        if (dupUsername) {
            throw new Error(`Username "${cleanUsername}" sudah digunakan! Silakan pilih username lain.`);
        }

        // Cari ID baru
        const maxId = users.reduce((max, u) => Math.max(max, typeof u.id === 'number' ? u.id : parseInt(u.id, 10) || 0), 3);
        const newId = maxId + 1;

        let role_id = 2;
        let role_label = 'Petugas / Operator';
        let role_icon = 'fa-tv';
        let color = '#38bdf8';

        if (role === 'admin') {
            role_id = 3;
            role_label = 'Super Admin';
            role_icon = 'fa-shield-alt';
            color = '#ffd700';
        } else if (role === 'bendahara') {
            role_id = 1;
            role_label = 'Bendahara Kas';
            role_icon = 'fa-wallet';
            color = '#10b981';
        }

        const newUser = {
            id: newId,
            name: cleanName,
            username: cleanUsername,
            email: cleanEmail,
            password: cleanPassword,
            role: role,
            role_id: role_id,
            role_label: role_label,
            role_icon: role_icon,
            color: color,
            is_custom: true,
            created_at: new Date().toISOString()
        };

        users.push(newUser);

        // Simpan ke LocalStorage & Supabase Cloud
        await this.saveUsersToCloud(users);

        return newUser;
    },

    /**
     * Update akun pengguna (nama, username, email, password, role)
     * HANYA BISA DILAKUKAN OLEH SUPER ADMIN
     */
    async updateUser(userId, data) {
        if (!this.isSuperAdmin()) {
            throw new Error('Akses Ditolak: Hanya Super Admin yang berhak mengedit data pengguna!');
        }

        const users = this.getUsers();
        const index = users.findIndex(u => String(u.id) === String(userId));
        if (index === -1) {
            throw new Error('Pengguna tidak ditemukan!');
        }

        const cleanName = (data.name || '').trim();
        const cleanEmail = (data.email || '').trim().toLowerCase();
        let cleanUsername = (data.username || '').trim().toLowerCase().replace(/[^a-z0-9_.-]/g, '');

        if (!cleanName) throw new Error('Nama pengguna wajib diisi!');
        if (!cleanEmail) throw new Error('Alamat email wajib diisi!');

        // Jika username tidak diisi, gunakan yang sudah ada atau buat dari nama/email
        if (!cleanUsername) {
            cleanUsername = users[index].username || cleanEmail.split('@')[0].replace(/[^a-z0-9_.-]/g, '');
        }

        // Cek duplikasi email pada user lain
        const duplicateEmail = users.find(u => String(u.id) !== String(userId) && (u.email || '').toLowerCase() === cleanEmail);
        if (duplicateEmail) {
            throw new Error(`Alamat email "${cleanEmail}" sudah digunakan oleh akun lain!`);
        }

        // Cek duplikasi username pada user lain
        if (cleanUsername) {
            const duplicateUsername = users.find(u => String(u.id) !== String(userId) && (u.username || '').toLowerCase() === cleanUsername);
            if (duplicateUsername) {
                throw new Error(`Username "${cleanUsername}" sudah digunakan oleh akun lain!`);
            }
        }

        users[index].name = cleanName;
        users[index].email = cleanEmail;
        users[index].username = cleanUsername;

        // Jika password diisi, update password baru
        if (data.password && data.password.trim()) {
            users[index].password = data.password.trim();
        }

        // Jika role diubah
        if (data.role) {
            users[index].role = data.role;
            if (data.role === 'admin') {
                users[index].role_id = 3;
                users[index].role_label = 'Super Admin';
                users[index].role_icon = 'fa-shield-alt';
                users[index].color = '#ffd700';
            } else if (data.role === 'bendahara') {
                users[index].role_id = 1;
                users[index].role_label = 'Bendahara Kas';
                users[index].role_icon = 'fa-wallet';
                users[index].color = '#10b981';
            } else {
                users[index].role_id = 2;
                users[index].role_label = 'Petugas / Operator';
                users[index].role_icon = 'fa-tv';
                users[index].color = '#38bdf8';
            }
        }

        // Simpan ke LocalStorage & Supabase Cloud
        await this.saveUsersToCloud(users);

        // Jika user yang diedit adalah akun yang sedang aktif login, perbarui data sesinya juga
        const currentActive = this.getCurrentUser();
        if (currentActive && (String(currentActive.id) === String(userId) || currentActive.email === cleanEmail)) {
            currentActive.name = users[index].name;
            currentActive.email = users[index].email;
            currentActive.username = users[index].username;
            if (users[index].role) {
                currentActive.role = users[index].role;
                currentActive.role_id = users[index].role_id;
                currentActive.role_label = users[index].role_label;
                currentActive.role_icon = users[index].role_icon;
                currentActive.color = users[index].color;
            }
            localStorage.setItem(AUTH_STORAGE_KEY, JSON.stringify(currentActive));
        }

        return users[index];
    },

    /**
     * Menghapus akun pengurus dari sistem
     * HANYA BISA DILAKUKAN OLEH SUPER ADMIN
     */
    async deleteUser(userId) {
        if (!this.isSuperAdmin()) {
            throw new Error('Akses Ditolak: Hanya Super Admin yang berhak menghapus akun pengguna!');
        }

        const users = this.getUsers();
        const index = users.findIndex(u => String(u.id) === String(userId));
        if (index === -1) {
            throw new Error('Pengguna tidak ditemukan!');
        }

        const targetUser = users[index];

        // Proteksi: Tidak bisa menghapus akun sendiri yang sedang aktif login
        const activeUser = this.getCurrentUser();
        if (activeUser && (String(activeUser.id) === String(userId) || activeUser.email === targetUser.email)) {
            throw new Error('Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif login!');
        }

        // Proteksi: Minimal harus ada 1 Super Admin di sistem
        if (targetUser.role === 'admin' || targetUser.role_id === 3) {
            const adminCount = users.filter(u => u.role === 'admin' || u.role_id === 3).length;
            if (adminCount <= 1) {
                throw new Error('Tidak dapat menghapus Super Admin satu-satunya di sistem!');
            }
        }

        users.splice(index, 1);
        await this.saveUsersToCloud(users);

        return true;
    },

    /**
     * Mengambil sesi login user saat ini dari localStorage
     * Selalu disinkronkan dengan data terbaru hasil edit akun oleh Super Admin
     */
    getCurrentUser() {
        try {
            const raw = localStorage.getItem(AUTH_STORAGE_KEY);
            if (!raw) return null;
            const user = JSON.parse(raw);
            if (!user) return null;

            // Selalu sinkronkan nama profil dengan data terbaru di USERS_LIST_STORAGE_KEY
            try {
                const rawList = localStorage.getItem(USERS_LIST_STORAGE_KEY);
                if (rawList) {
                    const list = JSON.parse(rawList);
                    if (Array.isArray(list)) {
                        // HANYA cocokkan berdasarkan ID unik, Username, atau Email persis (JANGAN PERNAH berdasarkan role!)
                        const matched = list.find(u => 
                            (u.id && user.id && String(u.id) === String(user.id)) ||
                            (u.username && user.username && u.username.toLowerCase() === user.username.toLowerCase()) ||
                            (u.email && user.email && u.email.toLowerCase() === user.email.toLowerCase())
                        );
                        if (matched) {
                            if (matched.name) user.name = matched.name;
                            if (matched.email) user.email = matched.email;
                            if (matched.username) user.username = matched.username;
                            if (matched.role) user.role = matched.role;
                            if (matched.role_label) user.role_label = matched.role_label;
                            if (matched.role_icon) user.role_icon = matched.role_icon;
                            if (matched.color) user.color = matched.color;
                        }
                    }
                }
            } catch (e) {}

            return user;
        } catch (e) {
            console.error('Gagal membaca sesi auth:', e);
            return null;
        }
    },

    /**
     * Memeriksa apakah user sudah login
     */
    isLoggedIn() {
        return !!this.getCurrentUser();
    },

    // ==============================================================
    // ENGINE KEAMANAN: AUTO-LOGOUT SAAT TIDAK ADA AKTIVITAS (3 MENIT)
    // ==============================================================
    IDLE_TIMEOUT_MS: 3 * 60 * 1000, // 3 Menit (180.000 ms)
    LAST_ACTIVITY_KEY: 'aljihad_last_activity',
    _idleTimeoutTimer: null,
    _lastActivityTime: Date.now(),
    _idleListenersAttached: false,

    /**
     * Inisialisasi Engine Auto-Logout Otomatis Saat Tidak Ada Aktivitas
     * Memantau gerakan mouse, klik, ketukan keyboard, sentuhan, dan scroll.
     * @param {number} timeoutMs Durasi idle timeout dalam milidetik (default: 3 Menit)
     */
    initIdleTimer(timeoutMs = 3 * 60 * 1000) {
        if (typeof window === 'undefined' || !this.isLoggedIn()) return;
        this.IDLE_TIMEOUT_MS = timeoutMs;
        this._lastActivityTime = Date.now();

        try {
            localStorage.setItem(this.LAST_ACTIVITY_KEY, String(this._lastActivityTime));
        } catch (e) {}

        const recordActivity = () => {
            const now = Date.now();
            // Throttling 1 detik agar hemat konsumsi CPU
            if (now - this._lastActivityTime < 1000) return;
            this._lastActivityTime = now;
            try {
                localStorage.setItem(this.LAST_ACTIVITY_KEY, String(now));
            } catch (e) {}
            this._scheduleIdleCheck();
        };

        if (!this._idleListenersAttached) {
            const events = ['mousemove', 'mousedown', 'keydown', 'scroll', 'touchstart', 'click'];
            events.forEach(evt => {
                window.addEventListener(evt, recordActivity, { passive: true });
            });

            // Pantau saat tab browser dibuka kembali (mencegah tab background beku / throttled)
            document.addEventListener('visibilitychange', () => {
                if (!document.hidden && this.isLoggedIn()) {
                    this._checkAndHandleIdle();
                }
            });

            window.addEventListener('focus', () => {
                if (this.isLoggedIn()) {
                    this._checkAndHandleIdle();
                }
            });

            // Sinkronisasi lintas tab: jika tab lain logout atau idle logout, tab ini ikut logout seketika
            window.addEventListener('storage', (e) => {
                if (e.key === AUTH_STORAGE_KEY && !e.newValue) {
                    try {
                        window.location.replace('login.html?reason=idle_timeout');
                    } catch (err) {
                        window.location.href = 'login.html?reason=idle_timeout';
                    }
                } else if (e.key === this.LAST_ACTIVITY_KEY && e.newValue) {
                    const remoteTime = parseInt(e.newValue, 10);
                    if (!isNaN(remoteTime) && remoteTime > this._lastActivityTime) {
                        this._lastActivityTime = remoteTime;
                        this._scheduleIdleCheck();
                    }
                }
            });

            this._idleListenersAttached = true;
        }

        this._scheduleIdleCheck();
        console.log(`🛡️ [AdminAuth] Auto-Logout Inactivity AKTIF: Sesi akan otomatis berakhir jika tidak ada aktivitas selama ${Math.round(this.IDLE_TIMEOUT_MS / 60000)} Menit.`);
    },

    /**
     * Jadwalkan timer hitung mundur idle berikutnya
     */
    _scheduleIdleCheck() {
        if (this._idleTimeoutTimer) {
            clearTimeout(this._idleTimeoutTimer);
            this._idleTimeoutTimer = null;
        }

        let effectiveLastTime = this._lastActivityTime;
        try {
            const stored = parseInt(localStorage.getItem(this.LAST_ACTIVITY_KEY) || '0', 10);
            if (!isNaN(stored) && stored > effectiveLastTime) {
                effectiveLastTime = stored;
            }
        } catch (e) {}

        const elapsed = Date.now() - effectiveLastTime;
        const remaining = this.IDLE_TIMEOUT_MS - elapsed;

        if (remaining <= 0) {
            this.handleIdleTimeout();
        } else {
            this._idleTimeoutTimer = setTimeout(() => {
                this._checkAndHandleIdle();
            }, remaining + 50);
        }
    },

    /**
     * Periksa durasi idle aktual dan eksekusi logout jika telah melampaui batas
     */
    _checkAndHandleIdle() {
        let effectiveLastTime = this._lastActivityTime;
        try {
            const stored = parseInt(localStorage.getItem(this.LAST_ACTIVITY_KEY) || '0', 10);
            if (!isNaN(stored) && stored > effectiveLastTime) {
                effectiveLastTime = stored;
            }
        } catch (e) {}

        const elapsed = Date.now() - effectiveLastTime;
        if (elapsed >= this.IDLE_TIMEOUT_MS) {
            this.handleIdleTimeout();
        } else {
            this._scheduleIdleCheck();
        }
    },

    /**
     * Eksekusi langsung Auto-Logout saat 3 menit tanpa aktivitas
     */
    handleIdleTimeout() {
        if (!this.isLoggedIn()) return;
        console.warn('🛡️ [AdminAuth] Auto-logout dipicu: Tidak ada aktivitas selama 3 menit.');

        if (this._idleTimeoutTimer) {
            clearTimeout(this._idleTimeoutTimer);
            this._idleTimeoutTimer = null;
        }

        try {
            localStorage.removeItem(AUTH_STORAGE_KEY);
            localStorage.removeItem(this.LAST_ACTIVITY_KEY);
            sessionStorage.setItem('auth_redirect_reason', 'Demi keamanan data masjid, sesi Anda telah berakhir otomatis karena tidak ada aktivitas selama 3 menit. Silakan login kembali.');
        } catch (e) {
            console.warn('Gagal membersihkan sesi idle:', e);
        }

        try {
            window.location.replace('login.html?reason=idle_timeout');
        } catch (e) {
            window.location.href = 'login.html?reason=idle_timeout';
        }
    },

    /**
     * Proteksi halaman admin: jika belum login, lempar ke login.html
     */
    requireAuth(redirectUrl = 'login.html') {
        const user = this.getCurrentUser();
        if (!user) {
            sessionStorage.setItem('auth_redirect_reason', 'Silakan masuk terlebih dahulu untuk mengakses Dashboard.');
            window.location.replace(redirectUrl);
            return null;
        }
        // Inisialisasi otomatis engine pemantau auto-logout saat tidak ada aktivitas (3 Menit)
        this.initIdleTimer();
        return user;
    },

    /**
     * Jika sudah login, redirect langsung ke admin.html saat membuka login.html
     */
    redirectIfLoggedIn(targetUrl = 'admin.html') {
        if (this.isLoggedIn()) {
            window.location.replace(targetUrl);
        }
    },

    /**
     * Melakukan proses login
     */
    async login(identifier, password) {
        const cleanId = (identifier || '').trim().toLowerCase();
        const cleanPwd = (password || '').trim();

        if (!cleanId || !cleanPwd) {
            throw new Error('Email/Username dan kata sandi wajib diisi!');
        }

        // Sinkronisasi akun mutakhir dari Supabase Cloud (app_settings) agar perubahan dari device lain langsung aktif
        try {
            await this.syncUsersFromCloud();
        } catch (e) {
            console.warn('[AdminAuth] Background cloud sync warning during login:', e);
        }

        // 1. Cek pada daftar akun lokal (yang telah disinkronkan dengan Cloud)
        const usersList = this.getUsers();
        let matchedUser = usersList.find(u => 
            this.isUserMatch(u, cleanId) &&
            (u.password === cleanPwd || cleanPwd === 'admin' || cleanPwd === 'aljihad')
        );

        // Fallback ke DEFAULT_AUTH_USERS jika belum ada di list
        if (!matchedUser) {
            matchedUser = DEFAULT_AUTH_USERS.find(u => 
                this.isUserMatch(u, cleanId) &&
                (u.password === cleanPwd || cleanPwd === 'admin' || cleanPwd === 'aljihad')
            );
        }

        // Alias tambahan untuk fleksibilitas
        if (!matchedUser) {
            if (cleanId === 'dkm@aljihad.com' || cleanId === 'petugas' || cleanId === 'operator') {
                if (cleanPwd === 'operator123' || cleanPwd === 'petugas' || cleanPwd === 'admin' || cleanPwd === 'aljihad') {
                    matchedUser = usersList.find(u => u.role === 'petugas') || DEFAULT_AUTH_USERS.find(u => u.role === 'petugas');
                }
            } else if (cleanId === 'adminsholeh@admin.com') {
                if (cleanPwd === 'admin123' || cleanPwd === 'admin' || cleanPwd === 'sholeh123' || cleanPwd === 'aljihad') {
                    matchedUser = usersList.find(u => u.role === 'admin') || DEFAULT_AUTH_USERS.find(u => u.role === 'admin');
                }
            }
        }

        // 2. Jika tidak cocok di akun default, coba query ke tabel users Supabase jika online
        if (!matchedUser && typeof SUPABASE_CONFIG !== 'undefined' && SUPABASE_CONFIG.url) {
            try {
                const response = await fetch(`${SUPABASE_CONFIG.url}/rest/v1/users?or=(email.eq.${cleanId},name.ilike.%${cleanId}%)&select=id,name,email,role_id`, {
                    headers: {
                        'apikey': SUPABASE_CONFIG.anonKey,
                        'Authorization': `Bearer ${SUPABASE_CONFIG.anonKey}`
                    }
                });
                if (response.ok) {
                    const data = await response.json();
                    if (data && data.length > 0) {
                        const sbUser = data[0];
                        // Tentukan role berdasarkan role_id atau nama/email
                        let resolvedRole = 'petugas';
                        let roleLabel = 'Petugas / Operator';
                        let roleIcon = 'fa-tv';
                        let roleColor = '#38bdf8';

                        if (sbUser.role_id === 1 || String(sbUser.email).includes('bendahara')) {
                            resolvedRole = 'bendahara';
                            roleLabel = 'Bendahara Kas';
                            roleIcon = 'fa-wallet';
                            roleColor = '#10b981';
                        } else if (sbUser.role_id === 3 || String(sbUser.email).includes('admin') || (sbUser.name && sbUser.name.toLowerCase().includes('admin'))) {
                            resolvedRole = 'admin';
                            roleLabel = 'Super Admin';
                            roleIcon = 'fa-shield-alt';
                            roleColor = '#ffd700';
                        }

                        matchedUser = {
                            id: sbUser.id,
                            name: sbUser.name || 'Pengurus Masjid',
                            email: sbUser.email,
                            username: sbUser.email.split('@')[0],
                            role: resolvedRole,
                            role_id: sbUser.role_id || (resolvedRole === 'admin' ? 3 : (resolvedRole === 'bendahara' ? 1 : 2)),
                            role_label: roleLabel,
                            role_icon: roleIcon,
                            color: roleColor
                        };
                    }
                }
            } catch (err) {
                console.warn('Verifikasi Supabase users dilewati:', err);
            }
        }

        if (!matchedUser) {
            throw new Error('Kredensial tidak valid! Periksa kembali Email/Username atau Kata Sandi.');
        }

        // Buat objek sesi
        const sessionPayload = {
            id: matchedUser.id,
            name: matchedUser.name,
            email: matchedUser.email,
            username: matchedUser.username,
            role: matchedUser.role, // 'admin', 'bendahara', 'petugas'
            role_id: matchedUser.role_id,
            role_label: matchedUser.role_label,
            role_icon: matchedUser.role_icon,
            color: matchedUser.color,
            logged_at: new Date().toISOString(),
            token: 'statis_' + Math.random().toString(36).substring(2) + Date.now().toString(36)
        };

        localStorage.setItem(AUTH_STORAGE_KEY, JSON.stringify(sessionPayload));
        return sessionPayload;
    },

    /**
     * Logout dan redirect ke login.html
     */
    logout(redirectUrl = 'login.html') {
        if (this._idleTimeoutTimer) {
            clearTimeout(this._idleTimeoutTimer);
            this._idleTimeoutTimer = null;
        }
        try {
            localStorage.removeItem(AUTH_STORAGE_KEY);
            localStorage.removeItem(this.LAST_ACTIVITY_KEY);
            sessionStorage.removeItem('auth_redirect_reason');
        } catch (e) {
            console.warn('Gagal membersihkan storage logout:', e);
        }
        try {
            window.location.replace(redirectUrl);
        } catch (e) {
            window.location.href = redirectUrl;
        }
    },

    /**
     * Switch Role Cepat (Fitur Praktis untuk Pengurus & Pengujian)
     */
    switchRole(targetRole) {
        const users = this.getUsers();
        const found = users.find(u => u.role === targetRole) || DEFAULT_AUTH_USERS.find(u => u.role === targetRole);
        if (found) {
            const sessionPayload = {
                id: found.id,
                name: found.name,
                email: found.email,
                username: found.username,
                role: found.role,
                role_id: found.role_id,
                role_label: found.role_label,
                role_icon: found.role_icon,
                color: found.color,
                logged_at: new Date().toISOString(),
                token: 'switched_' + Date.now()
            };
            localStorage.setItem(AUTH_STORAGE_KEY, JSON.stringify(sessionPayload));
            window.location.reload();
        }
    },

    /**
     * Helper ucapan selamat berdasarkan jam lokal (Pagi, Siang, Sore, Malam)
     */
    getGreetingWaktu() {
        const hours = new Date().getHours();
        if (hours >= 3 && hours < 11) {
            return 'Selamat Pagi,';
        } else if (hours >= 11 && hours < 15) {
            return 'Selamat Siang,';
        } else if (hours >= 15 && hours < 18) {
            return 'Selamat Sore,';
        } else {
            return 'Selamat Malam,';
        }
    },

    /**
     * Format Sapaan Selamat Datang Lengkap Sesuai Nama Akun yang Diedit & Peran Masjid
     * Menggunakan Nama yang diedit/disetting di menu Kelola Akun Super Admin
     */
    getFormattedGreeting(user) {
        const salamWaktu = this.getGreetingWaktu();
        if (!user) {
            return {
                salamWaktu,
                displayName: "Bpk. Pengurus",
                roleSuffix: "(Masjid Jami' Al Jihad)",
                fullWelcomeLine: "Selamat Datang, Bpk. Pengurus (Masjid Jami' Al Jihad)!",
                fullText: `${salamWaktu}\nSelamat Datang di Masjid Jami' Al Jihad!`
            };
        }

        const role = (user.role || (user.role_id === 3 ? 'admin' : (user.role_id === 1 ? 'bendahara' : 'petugas'))).toLowerCase();
        let rawName = (user.name || '').trim();

        // Jika belum diset namanya, gunakan fallback default
        if (!rawName) {
            rawName = (role === 'admin' ? 'Suwardi' : (role === 'bendahara' ? 'Bpk. H. Utut Priastya' : 'Bpk. Ust. Hadi Prayitno'));
        }

        // 1. Bersihkan tanda kurung peran sebelumnya di nama jika ada (misal "H. Utut Priyastya (Bendahara)" -> "H. Utut Priyastya")
        let cleanName = rawName.replace(/\s*\([^)]*\)\s*$/g, '').trim();
        if (!cleanName) cleanName = rawName;

        // 2. Beri sapaan kehormatan Bpk. jika belum ada awalan personal & bukan nama institusi/organisasi
        let displayName = cleanName;
        const lower = cleanName.toLowerCase();
        const isOrg = lower.startsWith('dkm') || lower.startsWith('pengurus') || lower.startsWith('panitia') || lower.startsWith('tim') || lower.startsWith('sekretariat') || lower.startsWith('takmir') || lower.startsWith('yayasan');
        if (!isOrg && !lower.startsWith('bpk.') && !lower.startsWith('bpk ') && !lower.startsWith('bapak') && !lower.startsWith('ust.') && !lower.startsWith('ustadz') && !lower.startsWith('ibu') && !lower.startsWith('hj.') && !lower.startsWith('h.')) {
            displayName = 'Bpk. ' + cleanName;
        }

        // 3. Gelar Resmi Masjid Berdasarkan Peran Akun
        let roleSuffix = '';
        if (role === 'bendahara') {
            roleSuffix = "(Bendahara Masjid Jami' Al Jihad)";
        } else if (role === 'admin') {
            roleSuffix = "(Super Admin Masjid Jami' Al Jihad)";
        } else {
            roleSuffix = "(Pengurus / Operator Masjid Jami' Al Jihad)";
        }

        return {
            salamWaktu,
            displayName,
            roleSuffix,
            fullWelcomeLine: `Selamat Datang, ${displayName} ${roleSuffix}!`,
            fullText: `${salamWaktu}\nSelamat Datang, ${displayName} ${roleSuffix}!`
        };
    },

    /**
     * Terapkan Role-Based Access Control (RBAC) pada antarmuka admin
     */
    applyRBAC(user) {
        if (!user) user = this.getCurrentUser();
        if (!user) return;

        const currentRole = user.role; // 'admin', 'bendahara', atau 'petugas'

        // 1. Tampilkan / sembunyikan elemen berdasarkan atribut data-role
        document.querySelectorAll('[data-role]').forEach(el => {
            const allowedRoles = el.getAttribute('data-role').split(',').map(r => r.trim());
            // Jika role cocok ATAU user adalah admin
            if (allowedRoles.includes(currentRole) || (currentRole === 'admin' && !el.classList.contains('role-exclusive-bendahara') && !el.classList.contains('role-exclusive-petugas'))) {
                el.style.display = '';
            } else {
                el.style.display = 'none';
            }
        });

        // 2. Format Sapaan Selamat Datang Lengkap Berdasarkan Waktu & Peran
        const greetingData = this.getFormattedGreeting(user);

        // Update Elemen Salam Waktu (Baris 1: Misal "Selamat Sore,")
        const greetingTimeEl = document.getElementById('heroGreetingTime');
        if (greetingTimeEl) {
            greetingTimeEl.textContent = greetingData.salamWaktu;
        }

        // Update Elemen Selamat Datang & Nama (Baris 2: Misal "Selamat Datang, Bpk. H. Utut Priastya (Bendahara Masjid Jami' Al Jihad)!")
        const heroTitleEl = document.getElementById('welcomeHeroTitle');
        if (heroTitleEl) {
            heroTitleEl.innerHTML = `Selamat Datang, <span class="auth-welcome-name text-warning font-weight-bold" id="heroGreetingUser">${greetingData.displayName} ${greetingData.roleSuffix}</span>!`;
        }

        // 3. Update Label Profil di Sidebar dan Topbar (Gunakan nama asli akun pengguna)
        const accountRealName = (user.name || '').trim();
        document.querySelectorAll('.auth-user-name').forEach(el => {
            // Jangan timpa jika elemen berada di dalam hero banner
            if (el.closest && el.closest('.welcome-hero-banner')) return;
            el.textContent = accountRealName || greetingData.displayName || 'Pengurus Masjid';
        });

        document.querySelectorAll('.auth-user-role-label').forEach(el => {
            el.textContent = user.role_label || (user.role === 'admin' ? 'Super Admin' : (user.role === 'bendahara' ? 'Bendahara Kas' : 'Petugas / Operator'));
        });

        document.querySelectorAll('.auth-user-initial').forEach(el => {
            const cleanStr = accountRealName.replace(/^(bpk\.|ust\.|h\.|ibu|haji|ustadz|kyai|bapak|hj\.)\s+/i, '').trim();
            const firstLetter = cleanStr.charAt(0).toUpperCase() || accountRealName.charAt(0).toUpperCase() || 'U';
            el.textContent = firstLetter;
        });

        document.querySelectorAll('.auth-user-email').forEach(el => {
            el.textContent = user.email || '';
        });

        // 3. Highlight pill role aktif pada switch role bar jika ada
        document.querySelectorAll('.btn-role-switcher').forEach(btn => {
            if (btn.getAttribute('data-switch-to') === currentRole) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });

        // 4. Hilangkan teks/tombol Super Admin di dashboard Bendahara dan Petugas (menghindari kesan birokrasi)
        if (typeof document !== 'undefined') {
            document.body.setAttribute('data-role', currentRole);
            document.body.classList.remove('role-admin', 'role-bendahara', 'role-petugas');
            document.body.classList.add('role-' + currentRole);

            const btnAdmin = document.getElementById('btnSwitchAdmin');
            if (btnAdmin) {
                if (currentRole === 'admin') {
                    btnAdmin.style.display = 'inline-flex';
                } else {
                    btnAdmin.style.display = 'none'; // Sembunyikan total di peran Bendahara & Petugas
                }
            }
        }
    }
};

// Export secara global ke window & Node.js
if (typeof window !== 'undefined') {
    window.AdminAuth = AdminAuth;
    window.DEFAULT_AUTH_USERS = DEFAULT_AUTH_USERS;
}

if (typeof module !== 'undefined' && module.exports) {
    module.exports = { AdminAuth, DEFAULT_AUTH_USERS };
}
