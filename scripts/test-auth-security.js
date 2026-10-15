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

    console.log('\n🎉 SEMUA PENGUJIAN KEAMANAN AUTENTIKASI (TAHAP 1) 100% SUKSES!');
})();
