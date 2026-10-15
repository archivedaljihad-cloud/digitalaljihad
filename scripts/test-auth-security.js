/**
 * scripts/test-auth-security.js
 * Test Verifikasi Keamanan Autentikasi AdminAuth (Zero-Bypass & Custom Password Protection)
 */

const fs = require('fs');
const path = require('path');
const vm = require('vm');
const assert = require('assert');

console.log('🧪 MENJALANKAN TEST KEAMANAN AUTENTIKASI ADMINAUTH...');

// Buat mock browser environment
const mockLocalStorage = {
    _data: {},
    getItem(k) { return this._data[k] || null; },
    setItem(k, v) { this._data[k] = String(v); },
    removeItem(k) { delete this._data[k]; },
    clear() { this._data = {}; }
};

const mockSessionStorage = {
    _data: {},
    getItem(k) { return this._data[k] || null; },
    setItem(k, v) { this._data[k] = String(v); },
    removeItem(k) { delete this._data[k]; },
    clear() { this._data = {}; }
};

const sandbox = {
    window: {
        location: { replace() {}, href: '' },
        addEventListener() {},
        removeEventListener() {}
    },
    document: {
        addEventListener() {}
    },
    localStorage: mockLocalStorage,
    sessionStorage: mockSessionStorage,
    console: {
        log() {},
        warn() {},
        error() {}
    },
    setTimeout,
    clearTimeout,
    setInterval,
    clearInterval,
    Date
};

// Baca dan jalankan admin-auth.js di dalam sandbox
const authCode = fs.readFileSync(path.join(__dirname, '../web-statis/js/admin-auth.js'), 'utf8');
vm.createContext(sandbox);
vm.runInContext(authCode, sandbox);

const AdminAuth = sandbox.AdminAuth;
const DEFAULT_AUTH_USERS = sandbox.DEFAULT_AUTH_USERS;

assert(AdminAuth, 'AdminAuth harus terdefinisi');
assert(Array.isArray(DEFAULT_AUTH_USERS), 'DEFAULT_AUTH_USERS harus berupa array');

console.log('  ✅ [1] Inisialisasi AdminAuth di sandbox sukses');

// 1. Uji Login Sukses Akun Bawaan
const users = AdminAuth.getUsers();
const bendahara = users.find(u => u.username === 'bendahara');
assert(bendahara, 'Akun bendahara harus ada di daftar');

assert(AdminAuth.isPasswordMatch(bendahara, '#1.Bendahara') === true, 'Password bendahara resmi harus cocok');
assert(AdminAuth.isPasswordMatch(bendahara, '#1.bendahara') === true, 'Password bendahara lowercase harus cocok');
assert(AdminAuth.isPasswordMatch(bendahara, 'salah123') === false, 'Password salah harus ditolak');

console.log('  ✅ [2] Password resmi bendahara cocok & password salah ditolak');

// 2. Uji Proteksi Custom Password Isolation
// Jika bendahara mengubah password ke 'PasswordBaru999'
bendahara.password = 'PasswordBaru999';
bendahara.has_custom_password = true;

assert(AdminAuth.isPasswordMatch(bendahara, 'PasswordBaru999') === true, 'Password kustom baru harus cocok');
assert(AdminAuth.isPasswordMatch(bendahara, '#1.Bendahara') === false, 'Password default lama WAJIB DITOLAK saat has_custom_password aktif');
assert(AdminAuth.isPasswordMatch(bendahara, '#1.bendahara') === false, 'Password default lama lowercase WAJIB DITOLAK saat has_custom_password aktif');

console.log('  ✅ [3] Proteksi Custom Password Isolation terbukti aktif: password default lama dinonaktifkan permanen saat password diubah');

// 3. Uji Anti-Bypass Login
(async () => {
    // Reset storage
    mockLocalStorage.clear();
    mockSessionStorage.clear();

    // Test a: Login dengan password salah harus throw error
    let thrownWrongPwd = false;
    try {
        await AdminAuth.login('bendahara', 'PasswordSalahTotal');
    } catch (e) {
        thrownWrongPwd = true;
        assert(e.message.includes('Kredensial tidak valid'), 'Pesan error harus kredensial tidak valid');
    }
    assert(thrownWrongPwd, 'Login dengan password salah WAJIB throw error');
    assert(!mockLocalStorage.getItem('aljihad_auth_user'), 'Tidak boleh ada sesi disimpan di localStorage saat password salah');

    console.log('  ✅ [4] Percobaan login password salah berhasil digagalkan (tidak ada sesi bocor)');

    // Test b: Login dengan user tidak dikenal harus throw error
    let thrownUnknownUser = false;
    try {
        await AdminAuth.login('user_palsu_yang_tidak_ada', 'bebas123');
    } catch (e) {
        thrownUnknownUser = true;
    }
    assert(thrownUnknownUser, 'Login user tidak dikenal WAJIB throw error');

    console.log('  ✅ [5] Percobaan login user tidak dikenal berhasil digagalkan');

    // Test c: Login dengan kredensial benar harus berhasil dan membuat sesi
    const session = await AdminAuth.login('admin', 'SuperUser1971');
    assert(session, 'Login admin harus mengembalikan sesi');
    assert.strictEqual(session.role, 'admin', 'Role sesi harus admin');
    assert(mockLocalStorage.getItem('aljihad_auth_user'), 'Sesi harus tersimpan di localStorage');
    assert(mockSessionStorage.getItem('aljihad_auth_user'), 'Sesi harus tersimpan di sessionStorage');

    console.log('  ✅ [6] Login dengan kredensial valid berhasil membuat sesi aman');

    // Test d (Reviewer Audit Issue A): Uji Alur Login Lengkap dengan Akun yang Diubah Passwordnya
    // Simpan akun dengan password baru ke localStorage (users_list)
    const storedList = AdminAuth.getUsers();
    const bendaharaInList = storedList.find(u => u.username === 'bendahara');
    bendaharaInList.password = 'BendaharaBaru2026!';
    bendaharaInList.has_custom_password = true;
    mockLocalStorage.setItem('aljihad_users_list', JSON.stringify(storedList));

    // Coba login alur lengkap menggunakan password default sebelumnya (#1.Bendahara)
    let oldDefaultRejected = false;
    try {
        await AdminAuth.login('bendahara', '#1.Bendahara');
    } catch (e) {
        oldDefaultRejected = true;
        assert(e.message.includes('Kredensial tidak valid'), 'Login dengan password default lama harus ditolak');
    }
    assert(oldDefaultRejected, 'Reviewer Issue A Fix: Login dengan password lama WAJIB DITOLAK pada seluruh alur login!');

    // Coba login alur lengkap menggunakan password kustom baru
    const newSession = await AdminAuth.login('bendahara', 'BendaharaBaru2026!');
    assert(newSession && newSession.role === 'bendahara', 'Login dengan password kustom baru harus berhasil');
    console.log('  ✅ [7] Reviewer Issue A Terbukti Lulus: Password default lama ditolak total pada alur login lengkap');

    // Test e (Reviewer Audit Issue B): Proteksi switchRole() Tanpa Login
    mockLocalStorage.clear();
    mockSessionStorage.clear();

    let switchWithoutLoginBlocked = false;
    try {
        AdminAuth.switchRole('admin');
    } catch (e) {
        switchWithoutLoginBlocked = true;
        assert(e.message.includes('Akses Ditolak'), 'switchRole tanpa login harus melempar error Akses Ditolak');
    }
    assert(switchWithoutLoginBlocked, 'Reviewer Issue B Fix: switchRole tanpa login WAJIB DIBLOKIR');

    // Test f: Proteksi switchRole() eskalasi hak akses dari non-admin ke admin tanpa verifikasi kata sandi
    await AdminAuth.login('petugas', '135dkmlJihad'); // Login sebagai petugas
    let escalationBlocked = false;
    try {
        AdminAuth.switchRole('admin'); // Coba eskalasi ke admin tanpa password
    } catch (e) {
        escalationBlocked = true;
        assert(e.message.includes('Akses Ditolak'), 'Eskalasi ke Super Admin tanpa kata sandi harus ditolak');
    }
    assert(escalationBlocked, 'Reviewer Issue B Fix: Eskalasi peran ke Super Admin tanpa verifikasi kata sandi WAJIB DITOLAK');

    // Test g: Eskalasi dengan verifikasi kata sandi Super Admin yang benar berhasil
    const elevatedSession = AdminAuth.switchRole('admin', 'SuperUser1971');
    assert(elevatedSession && elevatedSession.role === 'admin', 'switchRole ke Super Admin dengan password valid harus sukses');

    // Test h: Super Admin dapat beralih ke Bendahara (impersonasi/preview) lalu kembali ke Admin
    const switchedToBendahara = AdminAuth.switchRole('bendahara');
    assert(switchedToBendahara && switchedToBendahara.role === 'bendahara', 'Admin dapat beralih ke peran bendahara untuk preview');
    assert.strictEqual(switchedToBendahara.original_role, 'admin', 'Sesi mencatat original_role admin');

    const switchedBackToAdmin = AdminAuth.switchRole('admin');
    assert(switchedBackToAdmin && switchedBackToAdmin.role === 'admin', 'Admin yang sedang preview dapat kembali ke Admin');
    console.log('  ✅ [8] Reviewer Issue B Terbukti Lulus: switchRole() terproteksi autentikasi & otorisasi RBAC');

    console.log('\n🎉 SEMUA PENGUJIAN KEAMANAN AUTENTIKASI (TAHAP 1 & 2) 100% SUKSES!');
})();

