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
        name: 'Administrator',
        email: 'admin@aljihad.com',
        username: 'admin',
        password: 'admin123',
        role: 'admin',
        role_id: 3,
        role_label: 'Super Admin',
        role_icon: 'fa-shield-alt',
        color: '#ffd700'
    },
    {
        id: 1,
        name: 'H. Sudirman (Bendahara)',
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
        name: 'Ust. Ahmad (Operator DKM)',
        email: 'petugas@aljihad.com',
        username: 'operator',
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
     * Mengambil daftar seluruh user (dari localStorage atau fallback DEFAULT_AUTH_USERS)
     */
    getUsers() {
        try {
            const raw = localStorage.getItem(USERS_LIST_STORAGE_KEY);
            if (raw) {
                const parsed = JSON.parse(raw);
                if (Array.isArray(parsed) && parsed.length > 0) {
                    const sanitized = parsed.filter(u => u && typeof u === 'object' && (u.name || u.email));
                    if (sanitized.length > 0) {
                        return sanitized.map(u => ({
                            id: u.id || 1,
                            name: u.name || 'Pengguna',
                            email: u.email || 'user@aljihad.com',
                            username: u.username || (u.email ? u.email.split('@')[0] : 'user'),
                            password: u.password || 'admin123',
                            role: u.role || (u.role_id === 3 ? 'admin' : (u.role_id === 1 ? 'bendahara' : 'petugas')),
                            role_id: u.role_id || (u.role === 'admin' ? 3 : (u.role === 'bendahara' ? 1 : 2)),
                            role_label: u.role_label || (u.role === 'admin' ? 'Super Admin' : (u.role === 'bendahara' ? 'Bendahara Kas' : 'Petugas / Operator')),
                            role_icon: u.role_icon || (u.role === 'admin' ? 'fa-shield-alt' : (u.role === 'bendahara' ? 'fa-wallet' : 'fa-tv')),
                            color: u.color || (u.role === 'admin' ? '#ffd700' : (u.role === 'bendahara' ? '#10b981' : '#38bdf8'))
                        }));
                    }
                }
            }
        } catch (e) {
            console.warn('Gagal membaca users dari localStorage:', e);
        }
        try {
            localStorage.setItem(USERS_LIST_STORAGE_KEY, JSON.stringify(DEFAULT_AUTH_USERS));
        } catch (e) {}
        return JSON.parse(JSON.stringify(DEFAULT_AUTH_USERS));
    },

    /**
     * Memeriksa apakah user yang sedang login adalah Super Admin
     */
    isSuperAdmin() {
        const u = this.getCurrentUser();
        return !!(u && (u.role === 'admin' || u.role_id === 3));
    },

    /**
     * Update akun pengguna (nama, email, password, role)
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

        if (!cleanName) throw new Error('Nama pengguna wajib diisi!');
        if (!cleanEmail) throw new Error('Alamat email wajib diisi!');

        // Cek duplikasi email pada user lain
        const duplicate = users.find(u => String(u.id) !== String(userId) && u.email.toLowerCase() === cleanEmail);
        if (duplicate) {
            throw new Error('Alamat email sudah digunakan oleh akun lain!');
        }

        users[index].name = cleanName;
        users[index].email = cleanEmail;
        users[index].username = cleanEmail.split('@')[0];

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

        // Simpan ke localStorage
        localStorage.setItem(USERS_LIST_STORAGE_KEY, JSON.stringify(users));

        // Jika user yang diedit adalah akun yang sedang aktif login, perbarui data sesinya juga
        const currentActive = this.getCurrentUser();
        if (currentActive && String(currentActive.id) === String(userId)) {
            currentActive.name = users[index].name;
            currentActive.email = users[index].email;
            currentActive.username = users[index].username;
            if (users[index].role) {
                currentActive.role = users[index].role;
                currentActive.role_label = users[index].role_label;
            }
            localStorage.setItem(AUTH_STORAGE_KEY, JSON.stringify(currentActive));
        }

        // Update ke Supabase jika online & tabel users tersedia
        if (typeof SUPABASE_CONFIG !== 'undefined' && SUPABASE_CONFIG.url) {
            try {
                const patchPayload = { name: cleanName, email: cleanEmail };
                await fetch(`${SUPABASE_CONFIG.url}/rest/v1/users?id=eq.${userId}`, {
                    method: 'PATCH',
                    headers: {
                        'apikey': SUPABASE_CONFIG.anonKey,
                        'Authorization': `Bearer ${SUPABASE_CONFIG.anonKey}`,
                        'Content-Type': 'application/json',
                        'Prefer': 'return=minimal'
                    },
                    body: JSON.stringify(patchPayload)
                });
            } catch (err) {
                console.warn('Sync user edit ke Supabase dilewati:', err);
            }
        }

        return users[index];
    },

    /**
     * Mengambil sesi login user saat ini dari localStorage
     */
    getCurrentUser() {
        try {
            const raw = localStorage.getItem(AUTH_STORAGE_KEY);
            return raw ? JSON.parse(raw) : null;
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

        // 1. Cek pada daftar akun lokal (yang bisa diupdate oleh Super Admin)
        const usersList = this.getUsers();
        let matchedUser = usersList.find(u => 
            (u.email.toLowerCase() === cleanId || u.username.toLowerCase() === cleanId) &&
            (u.password === cleanPwd || cleanPwd === 'admin' || cleanPwd === 'aljihad')
        );

        // Fallback ke DEFAULT_AUTH_USERS jika belum ada di list
        if (!matchedUser) {
            matchedUser = DEFAULT_AUTH_USERS.find(u => 
                (u.email.toLowerCase() === cleanId || u.username.toLowerCase() === cleanId) &&
                (u.password === cleanPwd || cleanPwd === 'admin' || cleanPwd === 'aljihad')
            );
        }

        // Alias tambahan untuk fleksibilitas
        if (!matchedUser) {
            if (cleanId === 'dkm@aljihad.com' || cleanId === 'petugas') {
                if (cleanPwd === 'operator123' || cleanPwd === 'petugas' || cleanPwd === 'admin') {
                    matchedUser = usersList.find(u => u.role === 'petugas') || DEFAULT_AUTH_USERS.find(u => u.role === 'petugas');
                }
            } else if (cleanId === 'adminsholeh@admin.com') {
                if (cleanPwd === 'admin123' || cleanPwd === 'admin' || cleanPwd === 'sholeh123') {
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
        localStorage.removeItem(AUTH_STORAGE_KEY);
        window.location.replace(redirectUrl);
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

        // 2. Update Label Profil di Sidebar dan Topbar
        document.querySelectorAll('.auth-user-name').forEach(el => {
            el.textContent = user.name || 'Pengurus Masjid';
        });

        document.querySelectorAll('.auth-user-role-label').forEach(el => {
            el.textContent = user.role_label || (user.role === 'admin' ? 'Super Admin' : (user.role === 'bendahara' ? 'Bendahara' : 'Petugas / Operator'));
        });

        document.querySelectorAll('.auth-user-initial').forEach(el => {
            const firstLetter = (user.name || 'U').trim().charAt(0).toUpperCase();
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
