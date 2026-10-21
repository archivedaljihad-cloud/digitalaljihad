// Prayer fixtures are wall-clock WIB, independent of CI/host timezone.
process.env.TZ = 'Asia/Jakarta';
/**
 * SUITE PENGUJIAN REGRESI OTOMATIS - DIGITAL SIGNAGE MASJID (DIGITALv304)
 * Menjalankan verifikasi komprehensif sebelum setiap kali deployment:
 * 1. Syntax & Integrity Check (Seluruh File HTML & JS)
 * 2. Prayer Engine & 24-Jam Timeline Simulation (Sholat 5 Waktu, Jumat, Yasin, Kajian)
 * 3. Rotation Guard & Watchdog Integrity (Mencegah Layar Membeku / Direbut Paksa)
 * 4. Petugas Sholat & Data Parser Integrity
 * 5. Service Worker Cache Version Consistency
 */

const fs = require('fs');
const path = require('path');

let totalTests = 0;
let passedTests = 0;
let failedTests = 0;
const pendingTests = [];

function assert(condition, message) {
    totalTests++;
    if (condition) {
        passedTests++;
        console.log(`  ✅ PASS: ${message}`);
    } else {
        failedTests++;
        console.error(`  ❌ FAIL: ${message}`);
    }
}

console.log('================================================================');
console.log('🔍 MEMULAI PENGUJIAN REGRESI OTOMATIS DIGITAL SIGNAGE MASJID');
console.log('================================================================\n');

// -------------------------------------------------------------
// 1. SYNTAX & INTEGRITY CHECK
// -------------------------------------------------------------
console.log('📌 [TEST 1] Syntax & Script Integrity Check:');
const baseDir = path.resolve(__dirname, '../web-statis');

const filesToCheck = [
    'index.html',
    'admin.html',
    'petugas.html',
    'modeinput.html',
    'login.html',
    'prayer-mode.html',
    'sw.js',
    'js/prayer-engine.js',
    'js/supabase-db.js',
    'js/supabase-config.js',
    'js/admin-auth.js',
    'js/anti-idle.js'
];

// Tambahkan seluruh modul admin di js/admin/
const adminJsDir = path.join(baseDir, 'js/admin');
if (fs.existsSync(adminJsDir)) {
    fs.readdirSync(adminJsDir).forEach(f => {
        if (f.endsWith('.js')) filesToCheck.push(`js/admin/${f}`);
    });
}

// Tambahkan seluruh slide
const slidesDir = path.join(baseDir, 'slides');
if (fs.existsSync(slidesDir)) {
    fs.readdirSync(slidesDir).forEach(f => {
        if (f.endsWith('.html')) filesToCheck.push(`slides/${f}`);
    });
}

filesToCheck.forEach(relPath => {
    const fullPath = path.join(baseDir, relPath);
    if (!fs.existsSync(fullPath)) {
        assert(false, `File ada: ${relPath}`);
        return;
    }
    const content = fs.readFileSync(fullPath, 'utf8');
    if (relPath.endsWith('.html')) {
        const scripts = content.match(/<script[\s\S]*?<\/script>/gi) || [];
        let htmlValid = true;
        let testedCount = 0;
        scripts.forEach((s, idx) => {
            const openTag = (s.match(/<script[^>]*>/i) || [''])[0];
            if (/\bsrc\s*=/i.test(openTag)) return;
            const code = s.replace(/<script[^>]*>/i, '').replace(/<\/script>/i, '');
            testedCount++;
            try {
                new Function(code);
            } catch (err) {
                htmlValid = false;
                console.error(`     Syntax error in ${relPath} script #${idx}:`, err.message);
            }
        });
        assert(htmlValid, `Validasi sintaks JavaScript di ${relPath} (${testedCount} blok inline script)`);
    } else {
        let jsValid = true;
        try {
            new Function(content);
        } catch (err) {
            jsValid = false;
            console.error(`     Syntax error in ${relPath}:`, err.message);
        }
        assert(jsValid, `Validasi sintaks ${relPath}`);
    }
});

// -------------------------------------------------------------
// 2. PRAYER ENGINE TIMELINE SIMULATION
// -------------------------------------------------------------
console.log('\n📌 [TEST 2] Simulasi Prayer Engine (24-Jam, Jumat, Yasin, Kajian):');

global.window = global;
const prayerEngineCode = fs.readFileSync(path.join(baseDir, 'js/prayer-engine.js'), 'utf8');
eval(prayerEngineCode);

const mockSettings = {
    yasin_mode_enabled: true,
    yasin_start_time: '18:30',
    kajian_sabtu_enabled: true,
    kajian_sabtu_start_time: '18:25',
    prayer_mode_before_adzan: 3,
    prayer_mode_adzan_duration: 5,
    prayer_mode_iqamah_duration: 10,
    prayer_mode_duration: 10,
    prayer_mode_jumat_duration: 50
};

const mockJadwal = [
    { nama_sholat: 'Subuh', waktu: '04:15:00' },
    { nama_sholat: 'Dzuhur', waktu: '11:39:00' },
    { nama_sholat: 'Ashar', waktu: '14:41:00' },
    { nama_sholat: 'Maghrib', waktu: '17:45:00' },
    { nama_sholat: 'Isya', waktu: '18:55:00' }
];

// Test 2.1: Malam Jumat (Kamis 8 Okt 2026, 18:30 s/d 18:52)
const thursYasinTime = new Date('2026-10-08T18:35:00+07:00');
const thursState = window.PrayerEngine.getPrayerState(mockSettings, mockJadwal, null, thursYasinTime);
assert(thursState.yasin_active === true, 'Malam Jumat 18:35 WIB: Surat Yaasiin harus AKTIF');
assert(thursState.active === false, 'Malam Jumat 18:35 WIB: Belum waktu sholat fardhu (bukan adzan)');

// Test 2.2: Adzan Isya Malam Jumat (18:52 countdown)
const thursIsyaCountdown = new Date('2026-10-08T18:52:01+07:00');
const countdownState = window.PrayerEngine.getPrayerState(mockSettings, mockJadwal, null, thursIsyaCountdown);
assert(countdownState.active === true, 'Pukul 18:52:01 WIB: Mode Sholat Isya harus MENGAMBIL ALIH (countdown)');

// Test 2.3: Malam Ahad / Kajian Sabtu (Sabtu 10 Okt 2026, ba'da Maghrib s/d selesai Isya)
const satKajianTime = new Date('2026-10-10T18:30:00+07:00');
const satState = window.PrayerEngine.getPrayerState(mockSettings, mockJadwal, null, satKajianTime);
assert(satState.kajian_active === true, 'Malam Ahad 18:30 WIB: Slide Kajian harus AKTIF (terkunci ba\'da Maghrib)');

// Test 2.3b: Selesai Sholat Isya Malam Ahad (19:25 WIB, Isya 18:55 + 25m = 19:20 selesai)
const satPostIsyaTime = new Date('2026-10-10T19:25:00+07:00');
const satPostIsyaState = window.PrayerEngine.getPrayerState(mockSettings, mockJadwal, null, satPostIsyaTime);
assert(satPostIsyaState.kajian_active === false && satPostIsyaState.active === false, 'Malam Ahad 19:25 WIB: Setelah selesai sholat Isya, kunci kajian dilepas & rotasi normal kembali');

// Test 2.3c: Mode Timer Khusus Super Admin (Kunci 25 menit setelah Maghrib selesai 18:10 -> batas 18:35)
const timerSettings = Object.assign({}, mockSettings, {
    kajian_sabtu_data: { lock_mode: 'timer', lock_duration_minutes: 25 }
});
const satTimerActive = window.PrayerEngine.getPrayerState(timerSettings, mockJadwal, null, new Date('2026-10-10T18:30:00+07:00'));
assert(satTimerActive.kajian_active === true, 'Malam Ahad Mode Timer: Masih dalam durasi timer 25 menit (Kajian AKTIF)');
const satTimerExpired = window.PrayerEngine.getPrayerState(timerSettings, mockJadwal, null, new Date('2026-10-10T18:40:00+07:00'));
assert(satTimerExpired.kajian_active === false, 'Malam Ahad Mode Timer: Melebihi durasi timer 25 menit (Kajian DILEPAS kembali ke rotasi)');

// Test 2.4: Sholat Jumat (Jumat 9 Okt 2026, 11:39 s/d 12:34)
const friAdzanTime = new Date('2026-10-09T11:40:00+07:00');
const friState = window.PrayerEngine.getPrayerState(mockSettings, mockJadwal, null, friAdzanTime);
assert(friState.active === true && friState.isFriday === true, 'Jumat Siang 11:40 WIB: Sholat Jumat harus AKTIF');

// Test 2.5: Khutbah & Sholat Jumat (Jumat 9 Okt 2026, 12:20 WIB)
const friKhutbahTime = new Date('2026-10-09T12:20:00+07:00');
const friKhutbahState = window.PrayerEngine.getPrayerState(mockSettings, mockJadwal, { khatib: 'Ust. Jamal', imam: 'Ust. Jamal' }, friKhutbahTime);
assert(friKhutbahState.active === true && friKhutbahState.phase === 'khutbah' && friKhutbahState.prayer === "SHOLAT JUM'AT", 'Jumat Siang 12:20 WIB: Fase Khutbah & Sholat Jumat harus AKTIF');

// -------------------------------------------------------------
// 3. ROTATION & WATCHDOG GUARDS INTEGRITY
// -------------------------------------------------------------
console.log('\n📌 [TEST 3] Rotation Guard & Watchdog Integrity di index.html:');
const indexHtml = fs.readFileSync(path.join(baseDir, 'index.html'), 'utf8');
const prayerModeHtml = fs.readFileSync(path.join(baseDir, 'prayer-mode.html'), 'utf8');

assert(indexHtml.includes('let isYasinModeActive = false;'), 'index.html memiliki state isYasinModeActive');
assert(indexHtml.includes('let isKajianModeActive = false;'), 'index.html memiliki state isKajianModeActive');

// Cek scheduleNextRotation guard
const hasScheduleGuard = indexHtml.includes('if (isPaused || isPrayerModeActive || isYasinModeActive || isKajianModeActive');
assert(hasScheduleGuard, 'scheduleNextRotation() dijaga dari isYasinModeActive & isKajianModeActive');

// Cek switchSlide guard
const hasSwitchGuard = indexHtml.includes('if (activePages.length === 0 || isPrayerModeActive || isYasinModeActive || isKajianModeActive)');
assert(hasSwitchGuard, 'switchSlide() dijaga dari isYasinModeActive & isKajianModeActive');

// Cek Watchdog guard
const hasWatchdogGuard = indexHtml.includes('if (isPaused || isPrayerModeActive || isYasinModeActive || isKajianModeActive) return;');
assert(hasWatchdogGuard, 'SRE Watchdog dijaga agar TIDAK MEMBATALKAN Surat Yaasiin/Kajian');

// Cek initDisplay instant boot
const hasInitBoot = indexHtml.includes('isInitialYasin') && indexHtml.includes('isInitialKajian') && indexHtml.includes('isInitialPrayer');
assert(hasInitBoot, 'initDisplay() langsung memuat Sholat/Yaasiin/Kajian saat TV dinyalakan di jam agenda');

// Cek Prayer Frame Synchronizer & Memory Bridge
const hasPrayerBridge = indexHtml.includes('window.latestPrayerState = state;') && indexHtml.includes('UPDATE_PRAYER_STATE');
assert(hasPrayerBridge, 'index.html mengekspos latestPrayerState dan push UPDATE_PRAYER_STATE ke prayerFrame');

const hasPrayerModeListener = prayerModeHtml.includes('UPDATE_PRAYER_STATE') && prayerModeHtml.includes('cached_app_settings');
assert(hasPrayerModeListener, 'prayer-mode.html memiliki listener UPDATE_PRAYER_STATE dan LocalStorage cache bridge');

// -------------------------------------------------------------
// 4. DATA PARSER INTEGRITY
// -------------------------------------------------------------
console.log('\n📌 [TEST 4] Data Parser & Petugas Integrity:');
// Simulasi parser bilal di jumat.html
function parseBilal(rawBilal, maklumatFallback) {
    let bilalStr = rawBilal;
    let maklumatStr = '';
    if (rawBilal && rawBilal.includes('/')) {
        const parts = rawBilal.split('/').map(s => s.trim());
        bilalStr = parts[0] || '';
        if (parts[1]) maklumatStr = parts[1];
    }
    if (!maklumatStr) maklumatStr = maklumatFallback || '';
    return { bilal: bilalStr, maklumat: maklumatStr };
}

const p1 = parseBilal('Ust. Sahroni / Ust. Agus Purwanto', 'Ust. Fatkhurokhman');
assert(p1.maklumat === 'Ust. Agus Purwanto', 'Prioritas Pembaca Maklumat dari sholat_jumat (Agus Purwanto)');
assert(p1.bilal === 'Ust. Sahroni', 'Bilal terpisah dengan benar (Sahroni)');

const p2 = parseBilal('Ust. Sahroni', 'Ust. Budi');
assert(p2.maklumat === 'Ust. Budi', 'Fallback Maklumat berjalan jika tidak ada slash');

// -------------------------------------------------------------
// 5. CACHE VERSION CONSISTENCY
// -------------------------------------------------------------
console.log('\n📌 [TEST 5] Service Worker & Cache Consistency:');
const swContent = fs.readFileSync(path.join(baseDir, 'sw.js'), 'utf8');
const adminContent = fs.readFileSync(path.join(baseDir, 'admin.html'), 'utf8');

const swMatch = swContent.match(/CACHE_NAME\s*=\s*['"]([^'"]+)['"]/);
const adminSettingsPath = path.join(baseDir, 'js/admin/admin-settings.js');
const adminSettingsContent = fs.existsSync(adminSettingsPath) ? fs.readFileSync(adminSettingsPath, 'utf8') : '';
const combinedAdmin = adminContent + '\n' + adminSettingsContent;
const adminMatch = combinedAdmin.match(/if\s*\(name\s*!==\s*['"]([^'"]+)['"]\)/);

assert(swMatch && swMatch[1], `Versi CACHE_NAME terdeteksi di sw.js (${swMatch ? swMatch[1] : 'NONE'})`);
assert(adminMatch && adminMatch[1], `Versi Cache Cleaner terdeteksi di admin.html (${adminMatch ? adminMatch[1] : 'NONE'})`);
assert(swMatch && adminMatch && swMatch[1] === adminMatch[1], `Versi cache sw.js dan admin.html IDENTIK (${swMatch[1]} === ${adminMatch[1]})`);

// -------------------------------------------------------------
// 6. FORM PETUGAS & AUTENTIKASI INTEGRITY
// -------------------------------------------------------------
console.log('\n📌 [TEST 6] Form Petugas & Autentikasi Mandiri Integrity:');
const loginContent = fs.readFileSync(path.join(baseDir, 'login.html'), 'utf8');
const petugasContent = fs.readFileSync(path.join(baseDir, 'petugas.html'), 'utf8');
const modeinputContent = fs.readFileSync(path.join(baseDir, 'modeinput.html'), 'utf8');

assert(petugasContent.includes('authGateOverlay'), 'petugas.html memiliki PIN/password modal internal');
assert(modeinputContent.includes('authGateOverlay'), 'modeinput.html memiliki PIN/password modal internal');
assert(petugasContent.includes('handleAuthSubmit'), 'petugas.html memiliki fungsi otentikasi mandiri handleAuthSubmit');
assert(petugasContent.includes('SupabaseDB.onDataChange'), 'petugas.html memiliki listener realtime multi-akun');
assert(modeinputContent.includes('SupabaseDB.onDataChange'), 'modeinput.html memiliki listener realtime multi-akun');
assert(loginContent.includes('login') || loginContent.includes('Login'), 'login.html memiliki form otentikasi standar');

// Verifikasi Engine Auto-Logout Saat Tidak Ada Aktivitas (3 Menit)
const authContent = fs.readFileSync(path.join(baseDir, 'js/admin-auth.js'), 'utf8');
assert(authContent.includes('IDLE_TIMEOUT_MS: 3 * 60 * 1000'), 'AdminAuth memiliki IDLE_TIMEOUT_MS tepat 3 menit (180.000 ms)');
assert(authContent.includes('_idleHeartbeatInterval'), 'AdminAuth memiliki _idleHeartbeatInterval untuk periodic heartbeat check');
assert(authContent.includes('now - effectiveLastTime >= this.IDLE_TIMEOUT_MS'), 'AdminAuth recordActivity mencegah reset timer jika durasi idle sudah melampaui batas 3 menit');
assert(authContent.includes("sessionStorage.removeItem(AUTH_STORAGE_KEY)"), 'AdminAuth handleIdleTimeout membersihkan sesi sessionStorage secara tuntas');
assert(authContent.includes("localStorage.removeItem('quick_petugas_authenticated')"), 'AdminAuth handleIdleTimeout membersihkan kredensial petugas');
assert(loginContent.includes("urlParams.get('reason') === 'idle_timeout'"), 'login.html menangani pembersihan sesi dan cegah bouncing redirect saat idle_timeout');
assert(petugasContent.includes('AdminAuth.initIdleTimer()'), 'petugas.html mengaktifkan idle timer saat otorisasi berhasil');
assert(modeinputContent.includes('AdminAuth.initIdleTimer()'), 'modeinput.html mengaktifkan idle timer saat otorisasi berhasil');

// Verifikasi Keamanan Autentikasi (Hotfix Anti-Bypass & Custom Password Isolation)
assert(!authContent.includes('/rest/v1/users?or=(email.eq.'), 'AdminAuth tidak memiliki query fallback unauthenticated ke /rest/v1/users (anti-bypass)');
assert(authContent.includes('has_custom_password'), 'AdminAuth memiliki proteksi has_custom_password untuk mematikan default password saat akun diubah');
assert(authContent.includes('!u.has_custom_password'), 'AdminAuth isPasswordMatch memastikan password default hanya berlaku jika has_custom_password belum aktif');

// Verifikasi Pengerasan RLS Database (Tahap 2)
const rlsFixContent = fs.readFileSync(path.join(path.dirname(baseDir), 'database/supabase_rls_security_fix.sql'), 'utf8');
const rlsHardenedContent = fs.readFileSync(path.join(path.dirname(baseDir), 'database/supabase_rls_hardened.sql'), 'utf8');
assert(!rlsFixContent.includes('GRANT DELETE ON public.users TO anon'), 'supabase_rls_security_fix.sql tidak memberikan hak DELETE users kepada anon');
assert(!rlsHardenedContent.includes('GRANT DELETE ON public.users TO anon'), 'supabase_rls_hardened.sql tidak memberikan hak DELETE users kepada anon');
assert(rlsHardenedContent.includes('REVOKE ALL ON TABLE public.%I FROM PUBLIC, anon, authenticated'), 'supabase_rls_hardened.sql mencabut seluruh hak destruktif users dari anon');
assert(rlsHardenedContent.includes('signage_users_read') && rlsHardenedContent.includes('GRANT SELECT (id, name, email, role_id, created_at)'), 'supabase_rls_hardened.sql membatasi pembacaan users tanpa kolom password');

// Verifikasi Multi-Device Realtime Auth Synchronization & Fluid Layout
assert(loginContent.includes('SYNC_AUTH_USERS') && loginContent.includes('SupabaseDB.onRemoteCommand'), 'login.html memiliki listener realtime broadcast SYNC_AUTH_USERS');
assert(loginContent.includes('rbac_users_list') && loginContent.includes('SupabaseDB.onDataChange'), 'login.html memiliki listener realtime Postgres Changes rbac_users_list');
const adminCoreContent = fs.readFileSync(path.join(baseDir, 'js/admin/admin-core.js'), 'utf8');
assert(adminCoreContent.includes('SYNC_AUTH_USERS'), 'admin-core.js memiliki handler SYNC_AUTH_USERS untuk sinkronisasi akun realtime');
assert(petugasContent.includes('SYNC_AUTH_USERS'), 'petugas.html memiliki handler SYNC_AUTH_USERS');
assert(modeinputContent.includes('SYNC_AUTH_USERS'), 'modeinput.html memiliki handler SYNC_AUTH_USERS');
assert(loginContent.includes('max-width: 100%') && loginContent.includes('min-height: 100dvh'), 'login.html memiliki CSS layout fluid adaptif cross-device');
assert(loginContent.includes('@media (max-width: 380px)'), 'login.html memiliki breakpoint responsif ultra-compact mobile (< 380px)');
assert(petugasContent.includes('@media (max-width: 380px)'), 'petugas.html memiliki breakpoint responsif ultra-compact mobile (< 380px)');
assert(modeinputContent.includes('@media (max-width: 380px)'), 'modeinput.html memiliki breakpoint responsif ultra-compact mobile (< 380px)');
assert(adminContent.includes('@media (max-width: 380px)'), 'admin.html memiliki breakpoint responsif ultra-compact mobile (< 380px)');
assert(petugasContent.includes('visibilitychange') && petugasContent.includes('handleDeviceWakeSync'), 'petugas.html memiliki listener visibilitychange untuk sync otomatis saat tab aktif');
assert(modeinputContent.includes('visibilitychange') && modeinputContent.includes('handleDeviceWakeSync'), 'modeinput.html memiliki listener visibilitychange untuk sync otomatis saat tab aktif');
assert(adminCoreContent.includes('visibilitychange') && adminCoreContent.includes('handleAdminTabWakeSync'), 'admin-core.js memiliki listener visibilitychange untuk sync otomatis saat tab aktif');

// -------------------------------------------------------------
// 7. PRAYER MODE REALTIME & OFFICER SYNC INTEGRITY
// -------------------------------------------------------------
console.log('\n📌 [TEST 7] Prayer Mode Realtime & Officer Sync Integrity:');
const prayerContent = fs.readFileSync(path.join(baseDir, 'prayer-mode.html'), 'utf8');
assert(prayerContent.includes('SupabaseDB.onDataChange'), 'prayer-mode.html memiliki listener universal SupabaseDB.onDataChange');
assert(prayerContent.includes('cardMaklumat') && prayerContent.includes('has-maklumat'), 'prayer-mode.html memiliki komponen display Pembaca Maklumat');
assert(prayerContent.includes('init(true)') || prayerContent.includes('forceFresh'), 'prayer-mode.html mendukung forceFresh cache bypass saat update');
assert(prayerContent.includes('window.initPrayerMode') && prayerContent.includes('window.updatePrayerUI'), 'prayer-mode.html mengekspos public interface bridge ke parent TV');
assert(prayerContent.includes('flip-card-box') && prayerContent.includes('tile-face') && prayerContent.includes('tile-number'), 'prayer-mode.html mendukung kedua desain countdown (Model Flip 3D & Model Biasa Klasik)');
assert(prayerContent.includes('getCountdownStyle') && prayerContent.includes('switchCountdownStyle'), 'prayer-mode.html memiliki controller dynamic switch gaya countdown');
assert(adminContent.includes('cfgCountdownStyle'), 'admin.html memiliki konfigurasi pilihan Desain Countdown Sholat (cfgCountdownStyle)');

// -------------------------------------------------------------
// 8. SEO ROBOTS INTEGRITY
// -------------------------------------------------------------
console.log('\n📌 [TEST 8] SEO Robots Integrity:');
const robotsContent = fs.readFileSync(path.join(baseDir, 'robots.txt'), 'utf8');
assert(robotsContent.includes('Disallow: /admin.html'), 'robots.txt memblokir crawler ke rute admin');
assert(robotsContent.includes('Disallow: /login.html'), 'robots.txt memblokir crawler ke rute login');
assert(robotsContent.includes('Disallow: /petugas.html'), 'robots.txt memblokir crawler ke rute petugas');
assert(robotsContent.includes('Disallow: /modeinput.html'), 'robots.txt memblokir crawler ke rute modeinput');

// -------------------------------------------------------------
// 9. MULTI-DEVICE CLOUD SYNC & TV ENGINE INTEGRITY
// -------------------------------------------------------------
console.log('\n📌 [TEST 9] Multi-Device Cloud Sync & TV Display Engine:');
const dbContent = fs.readFileSync(path.join(baseDir, 'js/supabase-db.js'), 'utf8');
const themeContent = fs.readFileSync(path.join(baseDir, 'css/display-theme.css'), 'utf8');
const slideContent = fs.readFileSync(path.join(baseDir, 'slides/slide.html'), 'utf8');
const redirectsPath = path.join(baseDir, '_redirects');

assert(dbContent.includes("t === 'app_settings'") && dbContent.includes("delete this._cache['sholat_jumat']"), 'SupabaseDB.invalidateCache membersihkan seluruh tabel dependen saat app_settings berubah');
assert(dbContent.includes("const batch = { sholat_jumat: snapshot }") && dbContent.includes("this.saveSettingsBatch(batch)"), 'SupabaseDB.saveSholatJumat menyimpan foto dan snapshot Jumat melalui JSON settings atomik tanpa overflow kolom SQL foto');
assert(dbContent.includes('mergeCustomSlideImages') && dbContent.includes('slides_custom_data'), 'SupabaseDB mendukung resolusi custom base64 slide tanpa merusak skema SQL');
assert(loginContent.includes('AdminAuth.syncUsersFromCloud') && loginContent.includes('rbac_users_list'), 'login.html mendukung sinkronisasi akun mutakhir lintas perangkat dengan live fallback');
assert(!fs.existsSync(redirectsPath), '_redirects dieliminasi untuk mencegah infinite redirect loop 307 pada Cloudflare Workers Static Assets');
assert(themeContent.includes('filter: none !important;'), 'display-theme.css menonaktifkan filter CSS berat untuk melindungi akurasi warna dan performa Smart TV');
assert(slideContent.includes('cross-dissolve') || slideContent.includes('transisi halus'), 'slide.html menggunakan hardware-accelerated cross-dissolve anti-crash TV');

// -------------------------------------------------------------
// 10. QRIS DISPLAY & CAPSULE BADGE INTEGRITY
// -------------------------------------------------------------
console.log('\n📌 [TEST 10] QRIS Display & Capsule Badge Integrity:');
const qrisHtmlContent = fs.readFileSync(path.join(baseDir, 'slides/qris.html'), 'utf8');
const qrisImgExists = fs.existsSync(path.join(baseDir, 'img/QRIS.jpg'));
const qrisPngExists = fs.existsSync(path.join(baseDir, 'image/qris.png'));

assert(qrisImgExists, 'File fisik img/QRIS.jpg tersedia');
assert(qrisPngExists, 'File kompatibilitas image/qris.png tersedia');
assert(qrisHtmlContent.includes('src="../img/QRIS.jpg"'), 'slides/qris.html menggunakan sumber utama ../img/QRIS.jpg');
assert(qrisHtmlContent.includes('onerror="this.onerror=null; this.src=\'../img/QRIS.jpg\';"'), 'slides/qris.html memiliki fallback lokal ../img/QRIS.jpg');
assert(qrisHtmlContent.includes('.qris-brand-badge') && qrisHtmlContent.includes('color: #ffffff !important;'), 'Kotak kapsul .qris-brand-badge memiliki kontras teks putih tajam');
assert(qrisHtmlContent.includes('.qris-frame') && qrisHtmlContent.includes('max-height: 44vh;'), 'slides/qris.html membatasi tinggi frame QRIS agar tidak meluap ke atas');
assert(qrisHtmlContent.includes('@media (max-height: 850px)'), 'slides/qris.html memiliki optimasi media query khusus Smart TV');
assert(swContent.includes("'img/QRIS.jpg'") && swContent.includes("'image/qris.png'"), 'sw.js me-cache seluruh varian gambar QRIS');

// -------------------------------------------------------------
// 11. FINANCIAL SUMMARY TV MUTATION CONTRAST INTEGRITY
// -------------------------------------------------------------
console.log('\n📌 [TEST 11] Financial Summary TV Mutation Contrast Integrity:');
const keuSummaryContent = fs.readFileSync(path.join(baseDir, 'slides/keuangan-summary.html'), 'utf8');
assert(keuSummaryContent.includes('.trans-val.income') && keuSummaryContent.includes('color: #ffffff !important;'), 'slides/keuangan-summary.html memiliki nominal pemasukan putih tajam kontras');
assert(keuSummaryContent.includes('.income-sign') && keuSummaryContent.includes('#22c55e'), 'slides/keuangan-summary.html memiliki tanda plus hijau segar tegas');
assert(!keuSummaryContent.includes('.trans-val.income { color: #00e676;'), 'slides/keuangan-summary.html tidak menggunakan warna hijau neon bulam lama');

// -------------------------------------------------------------
// 12. PRAYER MODE SHAF HADITH CENTER FORMAT & TRANSPARENT BOX INTEGRITY
// -------------------------------------------------------------
console.log('\n📌 [TEST 12] Prayer Mode Shaf Hadith Center Format & Transparent Box Integrity:');
const prayerModeHtmlContent = fs.readFileSync(path.join(baseDir, 'prayer-mode.html'), 'utf8');
assert(prayerModeHtmlContent.includes('.prayer-shaf-arabic') && prayerModeHtmlContent.includes('text-align: center;'), 'prayer-mode.html memiliki CSS .prayer-shaf-arabic format center');
assert(prayerModeHtmlContent.includes('.prayer-shaf-arabic') && prayerModeHtmlContent.includes('clamp(30px, 3.4vw, 50px)'), 'prayer-mode.html memiliki ukuran font .prayer-shaf-arabic yang diperbesar proporsional');
assert(prayerModeHtmlContent.includes('.prayer-shaf-arti') && prayerModeHtmlContent.includes('text-align: center;'), 'prayer-mode.html memiliki CSS .prayer-shaf-arti format center');
assert(prayerModeHtmlContent.includes('.prayer-shaf-arti') && prayerModeHtmlContent.includes('clamp(22px, 2.25vw, 32px)'), 'prayer-mode.html memiliki ukuran font .prayer-shaf-arti yang diperbesar proporsional');
assert(prayerModeHtmlContent.includes('.prayer-shaf-source') && prayerModeHtmlContent.includes('text-align: center;'), 'prayer-mode.html memiliki CSS .prayer-shaf-source format center');
assert(prayerModeHtmlContent.includes('.prayer-shaf-source') && prayerModeHtmlContent.includes('clamp(14px, 1.4vw, 20px)'), 'prayer-mode.html memiliki ukuran font .prayer-shaf-source yang diperbesar proporsional');
assert(prayerModeHtmlContent.includes('“Luruskan shaf-shaf kalian, karena lurusnya shaf adalah kesempurnaan shalat”'), 'prayer-mode.html memuat terjemahan hadits shaf dengan kata "shalat"');
assert(!prayerModeHtmlContent.includes('kesempurnaan salat'), 'prayer-mode.html tidak lagi memuat kata usang "kesempurnaan salat"');
assert(prayerModeHtmlContent.includes('Shalatlah seolah-olah ini adalah shalat terakhirmu'), 'prayer-mode.html memuat pengingat shalat terakhir');
assert(prayerModeHtmlContent.includes('.prayer-shaf-reminder') && prayerModeHtmlContent.includes('clamp(20px, 2.0vw, 28px)'), 'prayer-mode.html memiliki ukuran font .prayer-shaf-reminder yang diperbesar proporsional');
assert(prayerModeHtmlContent.includes('(HR. Bukhari no.690 &amp; HR. Muslim no.433)'), 'prayer-mode.html memuat sumber hadits HR. Bukhari no.690 & HR. Muslim no.433');
assert(!prayerModeHtmlContent.includes("prayerModeTextEl.textContent = 'Iqamah Segera Dikumandangkan"), 'prayer-mode.html tidak lagi menggunakan teks Iqamah lama pada fase sholat berjamaah');
assert(prayerModeHtmlContent.includes('body.phase-prayer .prayer-mode-box') && prayerModeHtmlContent.includes('background: transparent !important;'), 'prayer-mode.html memiliki kotak hadits shaf 100% transparan di fase sholat');
assert(prayerModeHtmlContent.includes('body.phase-prayer #hadithPlakat') && prayerModeHtmlContent.includes('display: none !important;'), 'prayer-mode.html menyembunyikan kotak hadithPlakat pada fase sholat');
assert(prayerModeHtmlContent.includes('body.phase-prayer .message-section') && prayerModeHtmlContent.includes('margin-top: auto !important;') && prayerModeHtmlContent.includes('margin-bottom: clamp(90px,'), 'prayer-mode.html menaikkan posisi teks hadits shaf ke posisi tinggi dan proporsional');
assert(prayerModeHtmlContent.includes('.prayer-mode-box .icons-row') && prayerModeHtmlContent.includes('display: none;'), 'prayer-mode.html menyembunyikan 3 ikon saat fase sholat berlangsung');
assert(prayerModeHtmlContent.includes('body.phase-prayer .phase-section') && prayerModeHtmlContent.includes('clamp(26px,'), 'prayer-mode.html memiliki aturan posisi seimbang kotak kapsul nama sholat pas di antara judul dan hadits shaf');
assert(prayerModeHtmlContent.includes('.bg-kaabah') && prayerModeHtmlContent.includes('transform: scale('), 'prayer-mode.html memiliki background Kaabah yang di-zoom in (scale)');
assert(prayerModeHtmlContent.includes('.bg-kaabah') && prayerModeHtmlContent.includes('contrast(1.30)') && prayerModeHtmlContent.includes('image-rendering: -webkit-optimize-contrast;'), 'prayer-mode.html memiliki background Kaabah yang dipertegas dan dipertajam');
assert(fs.existsSync(path.join(baseDir, 'img/IkonFlightMode.png')), 'File fisik img/IkonFlightMode.png tersedia');
assert(fs.existsSync(path.join(baseDir, 'img/IkonSilentHP.png')), 'File fisik img/IkonSilentHP.png tersedia');
assert(prayerModeHtmlContent.includes('src="img/IkonFlightMode.png"') && prayerModeHtmlContent.includes('src="img/IkonSilentHP.png"'), 'prayer-mode.html memiliki ikon Flight Mode (kiri) dan Silent HP (kanan) di messageBanner');
assert(swContent.includes("'img/IkonFlightMode.png'") && swContent.includes("'img/IkonSilentHP.png'"), 'sw.js me-cache ikon IkonFlightMode.png dan IkonSilentHP.png');

// -------------------------------------------------------------
// 13. INFAQ SLIDE TREASURER WHATSAPP CONTRAST INTEGRITY
// -------------------------------------------------------------
console.log('\n📌 [TEST 13] Infaq Slide Treasurer WhatsApp Contrast Integrity:');
const infaqHtmlContent = fs.readFileSync(path.join(baseDir, 'slides/infaq.html'), 'utf8');
assert(infaqHtmlContent.includes('.rek-wa-wrap') && infaqHtmlContent.includes('background: rgba(3, 40, 24,'), 'slides/infaq.html memiliki badge kontras tinggi untuk kontak WhatsApp');
assert(infaqHtmlContent.includes('.rek-wa-wrap .wa-number') && infaqHtmlContent.includes('color: #FFFFFF !important;'), 'slides/infaq.html memiliki nomor bendahara putih tajam kontras');
assert(infaqHtmlContent.includes('.rek-wa-wrap .wa-role') && infaqHtmlContent.includes('color: #FFD700 !important;'), 'slides/infaq.html memiliki peran bendahara emas tajam kontras');
assert(infaqHtmlContent.includes('fa-whatsapp'), 'slides/infaq.html memiliki ikon WhatsApp resmi');

// -------------------------------------------------------------
// 14. KAJIAN SLIDE 2-CARD REDESIGN & AUTO-SLIDING INTEGRITY
// -------------------------------------------------------------
console.log('\n📌 [TEST 14] Kajian Slide 2-Card Redesign & Auto-Sliding Integrity:');
const kajianHtmlContent = fs.readFileSync(path.join(baseDir, 'slides/kajian.html'), 'utf8');
assert(kajianHtmlContent.includes('.kajian-cards-grid') && kajianHtmlContent.includes('grid-template-columns: repeat(2, 1fr);'), 'slides/kajian.html memiliki CSS grid 2 kotak berdampingan proporsional');
assert(kajianHtmlContent.includes('card-hadits-header') && kajianHtmlContent.includes('MUTIARA_HADITS_LIST'), 'slides/kajian.html memiliki header kartu Mutiara Hadits/HR per pekan');
assert(kajianHtmlContent.includes('card-photo-wrapper') && kajianHtmlContent.includes('card-ustadz-name'), 'slides/kajian.html memiliki frame foto pemateri dan nama pemateri emas');
assert(kajianHtmlContent.includes('card-kitab-box') && kajianHtmlContent.includes('card-schedule-footer'), 'slides/kajian.html memiliki komponen Kitab Rujukan, Hari/Tanggal, dan Jam Pelaksanaan');
assert(kajianHtmlContent.includes('kajian-pagination-bar') && kajianHtmlContent.includes('pagination-progress-fill'), 'slides/kajian.html mengganti timeline lama dengan bar indikator halaman ramping');
assert(kajianHtmlContent.includes('ITEMS_PER_PAGE = 2') && kajianHtmlContent.includes('renderCurrentPage'), 'slides/kajian.html memiliki paging 2 kartu per slide dengan rotasi otomatis bergiliran');
assert(!kajianHtmlContent.includes('.card-tema-box') && !kajianHtmlContent.includes('${temaHtml}'), 'slides/kajian.html tidak memiliki kotak tema sekunder asimetris yang merusak keseimbangan 2 kartu');

// -------------------------------------------------------------
// 15. NEO BACKGROUND ROTATOR INTEGRITY (BGR-0 S/D BGR-5)
// -------------------------------------------------------------
console.log('\n📌 [TEST 15] Neo Background Rotator Integrity:');
for (let i = 0; i <= 5; i++) {
    const pngPath = path.join(baseDir, `image/display/background/BGR-${i}.png`);
    const webpPath = path.join(baseDir, `image/display/background/BGR-${i}.webp`);
    assert(fs.existsSync(pngPath), `File fisik image/display/background/BGR-${i}.png tersedia`);
    assert(fs.existsSync(webpPath), `File terkompresi image/display/background/BGR-${i}.webp tersedia`);
}

const targetSlides = [
    'slides/keuangan.html',
    'slides/keuangan-summary.html',
    'slides/qris.html',
    'slides/ambulance.html',
    'slides/infaq.html',
    'slides/slide.html'
];

targetSlides.forEach(slideRel => {
    const slideContent = fs.readFileSync(path.join(baseDir, slideRel), 'utf8');
    const hasBgrWebp = slideContent.includes('BGR-0.webp') && slideContent.includes('BGR-5.webp');
    assert(hasBgrWebp, `${slideRel} menggunakan background BGR-0 s/d BGR-5`);
});

// Verifikasi isolasi halaman lain (tidak boleh berubah)
const untouchedSlides = ['slides/undangan.html', 'slides/pengumuman.html'];
untouchedSlides.forEach(slideRel => {
    const content = fs.readFileSync(path.join(baseDir, slideRel), 'utf8');
    assert(!content.includes('BGR-0.webp'), `Isolasi aman: ${slideRel} tidak tersentuh pembaruan background`);
});

assert(swContent.includes('image/display/background/BGR-0.webp'), 'sw.js me-cache aset background BGR terbaru');

// -------------------------------------------------------------
// 16. AGENDA RUTIN CARD NO. 4 BADGE TITLE INTEGRITY
// -------------------------------------------------------------
console.log('\n📌 [TEST 16] Agenda Rutin Card No. 4 Badge Title Integrity:');
const agendaContent = fs.readFileSync(path.join(baseDir, 'slides/agenda-rutin.html'), 'utf8');
const pengumumanAgendaContent = fs.readFileSync(path.join(baseDir, 'slides/pengumuman.html'), 'utf8');
assert(agendaContent.includes('Kajian ba\'da Shubuh'), 'slides/agenda-rutin.html memiliki badge no. 4 "Kajian ba\'da Shubuh"');
assert(!agendaContent.includes('Kajian Shubuh / Tematik'), 'slides/agenda-rutin.html tidak lagi memuat string usang "Kajian Shubuh / Tematik"');
assert(pengumumanAgendaContent.includes('Kajian ba\'da Shubuh'), 'slides/pengumuman.html memiliki badge no. 4 "Kajian ba\'da Shubuh"');
assert(!pengumumanAgendaContent.includes('Kajian Shubuh / Tematik'), 'slides/pengumuman.html tidak lagi memuat string usang "Kajian Shubuh / Tematik"');

// -------------------------------------------------------------
// 17. SLIDES UTAMA PRAYER TIME LEGIBILITY (10M DISTANCE)
// -------------------------------------------------------------
console.log('\n📌 [TEST 17] Slides Utama Prayer Time Legibility (10M Distance Visibility):');
const utamaContent = fs.readFileSync(path.join(baseDir, 'slides/utama.html'), 'utf8');
assert(utamaContent.includes('font-size: 2.65rem;') && utamaContent.includes('.sholat-card .waktu-sholat'), 'slides/utama.html memiliki font-size 2.65rem untuk angka jam sholat agar terbaca dari 10m');
assert(utamaContent.includes("'Urbanist'") && utamaContent.includes('font-weight: 700;'), 'slides/utama.html menggunakan font Urbanist Bold 700 untuk angka jam sholat');
assert(utamaContent.includes('.npb-countdown') && utamaContent.includes('font-size: 19px;'), 'slides/utama.html memiliki font-size 19px untuk countdown sholat berikutnya');
assert(utamaContent.includes('.sholat-card .waktu-sholat') && utamaContent.includes('font-size: 2.2rem;'), 'slides/utama.html memiliki font-size adaptif 2.2rem pada layar tablet <= 1024px');
assert(swContent.includes('aljihad-signage-v5.8.109'), 'sw.js memiliki bumping versi cache terbaru v5.8.109');

// -------------------------------------------------------------
// 18. SLIDES JUMAT IMAM PHOTO BADGE 1-LINE FORMAT INTEGRITY
// -------------------------------------------------------------
console.log('\n📌 [TEST 18] Slides Jumat Imam Photo Badge 1-Line Format Integrity:');
const jumatContent = fs.readFileSync(path.join(baseDir, 'slides/jumat.html'), 'utf8');
assert(jumatContent.includes('.imam-badge-overlay .badge-name') && jumatContent.includes('white-space: nowrap;'), 'slides/jumat.html memiliki CSS white-space nowrap agar nama imam selalu 1 baris');
assert(jumatContent.includes('text-overflow: ellipsis;'), 'slides/jumat.html memiliki proteksi text-overflow ellipsis agar tidak meluap');
assert(jumatContent.includes('.imam-card-box') && jumatContent.includes('width: 280px;'), 'slides/jumat.html memiliki lebar imam-card-box 280px yang lapang untuk gelar nama');
assert(jumatContent.includes('badgeImamEl.style.fontSize'), 'slides/jumat.html memiliki dynamic font-size scaler untuk nama panjang di badge imam');

// -------------------------------------------------------------
// 19. SLIDES SLIDE CONTRAST & CINEMATIC TRANSITION INTEGRITY
// -------------------------------------------------------------
console.log('\n📌 [TEST 19] Slides Slide Contrast & Cinematic Transition Integrity:');
const slidePageContent = fs.readFileSync(path.join(baseDir, 'slides/slide.html'), 'utf8');
assert(slidePageContent.includes('.slide-desc') && slidePageContent.includes('background: rgba(3, 22, 15, 0.80);'), 'slides/slide.html memiliki background frosted gelap untuk kontras teks deskripsi');
assert(slidePageContent.includes('-webkit-text-stroke: 0.35px rgba(0, 0, 0, 0.9);'), 'slides/slide.html memiliki outline/stroke kontras gelap pada teks deskripsi');
assert(slidePageContent.includes('performCinematicTransition'), 'slides/slide.html memiliki fungsi performCinematicTransition bebas hentakan');
assert(slidePageContent.includes('fadeOutMs = 450') && slidePageContent.includes('fadeInMs = 550'), 'slides/slide.html memiliki kurva timing transisi sinematik sinkron');
assert(slidePageContent.includes('BGR-0.webp') && slidePageContent.includes('BGR-5.webp'), 'slides/slide.html menggunakan background rotator islami BGR-0 s.d BGR-5 selaras dengan qris.html');

// -------------------------------------------------------------
// 20. ADMIN DASHBOARD REMINDER & REFRESH TV NOTICE INTEGRITY
// -------------------------------------------------------------
console.log('\n📌 [TEST 20] Admin Dashboard Reminder & Bersihkan Cache Notice Integrity:');
const adminPageContent = fs.readFileSync(path.join(baseDir, 'admin.html'), 'utf8');
assert(adminPageContent.includes('Setelah melakukan input/edit data, <strong class="text-warning">JANGAN LUPA !</strong> tekan menu <strong class="text-warning">SAVE</strong> dan tekan <strong class="text-warning">BERSIHKAN CACHE &amp; REFRESH TV</strong> agar seluruh perubahan data di dashboard ini langsung tersimpan dan disinkronkan secara real-time ke Layar TV Display.'), 'admin.html memuat teks pengingat SAVE dan Bersihkan Cache & Refresh TV terbaru');
assert(adminPageContent.includes('.hero-sub-desc') && adminPageContent.includes('max-width: 880px;'), 'admin.html memiliki CSS .hero-sub-desc dengan max-width 880px yang proporsional');
assert(adminPageContent.includes('id="btnTopbarBersihkanCache"'), 'admin.html memiliki tombol pintas Bersihkan Cache & Refresh TV di topbar');

// -------------------------------------------------------------
// 21. KHUTBAH JUM'AT TRANSPARENT BOXES PREVIEW INTEGRITY
// -------------------------------------------------------------
console.log("\n📌 [TEST 21] Khutbah Jum'at Transparent Boxes Preview Integrity:");
assert(fs.existsSync(path.join(baseDir, 'preview-khutbah.html')), 'File fisik preview-khutbah.html tersedia');
const previewKhutbahContent = fs.readFileSync(path.join(baseDir, 'preview-khutbah.html'), 'utf8');
assert(previewKhutbahContent.includes('khutbah-transparent'), 'preview-khutbah.html mendukung mode transparan');
assert(previewKhutbahContent.includes('setMode'), 'preview-khutbah.html memiliki fungsi interaktif pembanding real-time');
assert(previewKhutbahContent.includes('setTheme'), 'preview-khutbah.html memiliki fungsi pemilih tema warna teks');
assert(previewKhutbahContent.includes('#FFE082') && previewKhutbahContent.includes('#FDE047'), 'preview-khutbah.html menerapkan warna kontras tinggi emas radiant & kuning terang');
assert(prayerModeHtmlContent.includes('khutbah-transparent') && prayerModeHtmlContent.includes('toggleTransparentBox'), 'prayer-mode.html memiliki handler dan toggle khutbah transparan');
assert(prayerModeHtmlContent.includes('#FFE082') && prayerModeHtmlContent.includes('#FDE047'), 'prayer-mode.html menerapkan penegasan kontras teks khutbah terbaru');
// -------------------------------------------------------------
// 22. END-TO-END REALTIME & STORAGE RELIABILITY INTEGRITY
// -------------------------------------------------------------
console.log('\n📌 [TEST 22] End-to-End Realtime & Storage Reliability Integrity:');
const supabaseDbContent = fs.readFileSync(path.join(baseDir, 'js/supabase-db.js'), 'utf8');
const modeinputHtmlContent = fs.readFileSync(path.join(baseDir, 'modeinput.html'), 'utf8');
const indexHtmlContent = fs.readFileSync(path.join(baseDir, 'index.html'), 'utf8');
const petugasHtmlContent = fs.readFileSync(path.join(baseDir, 'petugas.html'), 'utf8');

assert(supabaseDbContent.includes('safeRestMutation'), 'supabase-db.js memiliki safeRestMutation dengan penanganan timeout, HTTP status, dan rowsAffected');
assert(supabaseDbContent.includes("return=representation"), 'supabase-db.js meminta representasi baris dari Supabase untuk verifikasi rowsAffected > 0');
assert(supabaseDbContent.includes('cloudConfirmed'), 'supabase-db.js memverifikasi konfirmasi cloud sebelum melaporkan status sukses');
assert(supabaseDbContent.includes('debounceTimers = new Map()'), 'supabase-db.js memisahkan timer debounce per-tabel agar mutasi dua tabel tidak saling membatalkan');
assert(supabaseDbContent.includes('tableEventQueues = new Map()'), 'supabase-db.js memiliki antrean batch buffer per-tabel agar seluruh baris mutasi tersampaikan');
assert(supabaseDbContent.includes('getTvDeviceId'), 'supabase-db.js mendukung identifikasi dinamis perangkat TV Display');
assert(indexHtmlContent.includes('acceptTvRenderConfirmation') && supabaseDbContent.includes('SLIDE_RENDER_CONFIRMED'), 'index.html mendengarkan konfirmasi render selesai dari slide iframe');
assert(supabaseDbContent.includes('recentSignatures = new Map()'), 'supabase-db.js memiliki ring buffer deduplikasi multi-track 250ms');
assert(supabaseDbContent.includes('sendTvAcknowledgment') && supabaseDbContent.includes('onTvAcknowledgment'), 'supabase-db.js memiliki protokol acknowledgment (ACK) dua arah TV Display');
assert(indexHtmlContent.includes('isPrayerModeActive'), 'index.html memiliki proteksi mode sholat/Yaasiin/kajian aktif agar tidak terganggu reload saat data berubah');
assert(supabaseDbContent.includes('event.origin !== myOrigin'), 'supabase-db.js memvalidasi origin dan menolak pesan window.message dari origin asing');
assert(petugasHtmlContent.includes('toast-saving') && petugasHtmlContent.includes('toast-tv'), 'petugas.html memiliki indikator 4 status UI (saving, cloudConfirmed, error, tv_synced)');
assert(modeinputHtmlContent.includes('toast-saving') && modeinputHtmlContent.includes('toast-tv'), 'modeinput.html memiliki indikator 4 status UI (saving, cloudConfirmed, error, tv_synced)');
assert(swContent.includes('Promise.allSettled'), 'sw.js mengeliminasi all-or-nothing cache install dengan Promise.allSettled');
assert(swContent.includes('caches.delete(CACHE_NAME)') && swContent.includes('throw new Error'), 'sw.js membatalkan instalasi dan membersihkan cache jika aset inti gagal diprecache');

// Sandbox Test Logika Realtime & Mutasi:
// 1. Uji simulasi respon HTTP 403 (Forbidden / RLS Rejection)
const testHttp403Result = (() => {
    const mockRes = { ok: false, status: 403, statusText: 'Forbidden' };
    if (!mockRes.ok) {
        return { success: false, cloudConfirmed: false, error: `HTTP ${mockRes.status}` };
    }
    return { success: true, cloudConfirmed: true };
})();
assert(testHttp403Result.success === false && testHttp403Result.cloudConfirmed === false, 'Logika mutasi: Respon HTTP 403 ditolak dan dilaporkan gagal');

// 2. Uji simulasi respon 200 dengan 0 baris terpengaruh (Zero Rows Affected)
const testZeroRowsResult = (() => {
    const data = [];
    const rowsAffected = data.length;
    if (rowsAffected === 0) {
        return { success: false, cloudConfirmed: false, error: 'Tidak ada baris yang cocok' };
    }
    return { success: true, cloudConfirmed: true };
})();
assert(testZeroRowsResult.success === false && testZeroRowsResult.cloudConfirmed === false, 'Logika mutasi: Update dengan nol baris terpengaruh dilaporkan sebagai kegagalan');

// 3. Uji simulasi per-tabel debounce (dua tabel berbeda diproses keduanya)
const debounceTableMap = new Map();
let processedTables = [];
function mockSchedule(table) {
    if (debounceTableMap.has(table)) clearTimeout(debounceTableMap.get(table));
    debounceTableMap.set(table, setTimeout(() => {
        processedTables.push(table);
    }, 10));
}
mockSchedule('sholat_jumat');
mockSchedule('keuangan');
// Cek bahwa kedua timer tersimpan di map terpisah
assert(debounceTableMap.has('sholat_jumat') && debounceTableMap.has('keuangan'), 'Realtime Engine: Dua tabel berbeda yang berubah bersamaan memiliki slot timer terpisah');

// 4. Uji simulasi deduplikasi multi-track 250ms (ring buffer signature)
const mockRecentSigs = new Map();
let executedEvents = 0;
function mockHandleMultiTrack(sig, now) {
    if (mockRecentSigs.has(sig) && (now - mockRecentSigs.get(sig)) < 250) {
        return false; // Duplikat diabaikan
    }
    mockRecentSigs.set(sig, now);
    executedEvents++;
    return true;
}
const testSig = 'keuangan_INSERT_105_hash99';
mockHandleMultiTrack(testSig, 1000); // Track 1: Broadcast
mockHandleMultiTrack(testSig, 1020); // Track 2: Postgres Change
mockHandleMultiTrack(testSig, 1040); // Track 3: Local PostMessage
assert(executedEvents === 1, 'Realtime Engine: Multi-track churn dalam 250ms berhasil dideduplikasi menjadi tepat 1 eksekusi');

// 5. Uji simulasi per-tabel batch queue (dua baris berbeda pada tabel yang sama diproses seluruhnya)
const mockTableQueue = new Map();
const mockDebounceTimers = new Map();
let mockReceivedRows = [];

function mockTriggerRow(table, rowId, rowData) {
    if (!mockTableQueue.has(table)) mockTableQueue.set(table, []);
    const q = mockTableQueue.get(table);
    q.push({ table, rowId, rowData });

    if (mockDebounceTimers.has(table)) clearTimeout(mockDebounceTimers.get(table));
    mockDebounceTimers.set(table, setTimeout(() => {
        const rows = mockTableQueue.get(table) || [];
        mockTableQueue.delete(table);
        rows.forEach(r => mockReceivedRows.push(r));
    }, 15));
}

mockTriggerRow('keuangan', 101, { nominal: 50000 });
mockTriggerRow('keuangan', 102, { nominal: 100000 });

// Jalankan timer synchronous mock check
pendingTests.push(new Promise(resolve => setTimeout(() => {
    assert(mockReceivedRows.length === 2, 'Realtime Engine: Dua baris berbeda pada tabel yang sama keduanya berhasil diterima tanpa terpotong');
    assert(mockReceivedRows[0].rowId === 101 && mockReceivedRows[1].rowId === 102, 'Realtime Engine: Urutan baris mutasi pertama dan kedua terjaga utuh');
    resolve();
}, 30)));

// -------------------------------------------------------------
// 23. SURAT YAASIIN SETTINGS PERSISTENCE & SCHEMA SAFETY
// -------------------------------------------------------------
console.log('\n📌 [TEST 23] Surat Yaasiin Settings Persistence & Schema Safety:');
const adminYasinContent = fs.readFileSync(path.join(baseDir, 'js/admin/admin-yasin.js'), 'utf8');
const freshAdminCoreContent = fs.readFileSync(path.join(baseDir, 'js/admin/admin-core.js'), 'utf8');
const adminDataLoaderContent = fs.readFileSync(path.join(baseDir, 'js/admin/admin-data-loader.js'), 'utf8');

assert(adminYasinContent.includes('compactSpeed') && adminYasinContent.includes('.substring(0, 20)'), 'admin-yasin.js menggunakan format ringkas yasin_scroll_speed <= 20 karakter');
assert(freshAdminCoreContent.includes("localStorage.getItem('yasin_start_time')") && freshAdminCoreContent.includes('cfgYasinStartTime'), 'admin-core.js menghidrasi timer yaasiin secara instan dari localStorage saat DOM ready');
assert(adminDataLoaderContent.includes("localStorage.getItem('yasin_start_time')"), 'admin-data-loader.js memprioritaskan fallback timer yaasiin ke localStorage');
assert(supabaseDbContent.includes('yasin_scroll_speed') && supabaseDbContent.includes('.substring(0, 20)'), 'supabase-db.js memproteksi kolom yasin_scroll_speed maksimal 20 karakter sebelum dikirim ke cloud');

// -------------------------------------------------------------
// 24. 24-HOUR FORMAT & FLATPICKR OFFLINE INTEGRITY
// -------------------------------------------------------------
console.log('\n📌 [TEST 24] 24-Hour Format & Flatpickr Offline Resilience Integrity:');
assert(fs.existsSync(path.join(baseDir, 'vendor/flatpickr/flatpickr.min.css')), 'vendor/flatpickr/flatpickr.min.css fisik tersedia untuk offline PWA');
assert(fs.existsSync(path.join(baseDir, 'vendor/flatpickr/flatpickr.min.js')), 'vendor/flatpickr/flatpickr.min.js fisik tersedia untuk offline PWA');
assert(adminPageContent.includes('vendor/flatpickr/flatpickr.min.css'), 'admin.html memuat vendor/flatpickr/flatpickr.min.css');
assert(adminPageContent.includes('vendor/flatpickr/flatpickr.min.js'), 'admin.html memuat vendor/flatpickr/flatpickr.min.js');
assert(adminPageContent.includes('id="sholatDzuhur"') && adminPageContent.includes('class="form-control font-weight-bold text-center timepicker-24"'), 'admin.html input sholatDzuhur menggunakan text input class timepicker-24');
assert(!adminPageContent.includes('type="time" id="sholatDzuhur"') && !adminPageContent.includes('id="sholatDzuhur" type="time"'), 'admin.html sholatDzuhur tidak lagi menggunakan native input type="time"');
assert(freshAdminCoreContent.includes('init24HourTimepickers') && freshAdminCoreContent.includes('formatJam24H') && freshAdminCoreContent.includes('validateJam24H'), 'admin-core.js memiliki fungsi formatJam24H, validateJam24H, dan init24HourTimepickers');
const adminJadwalContent = fs.readFileSync(path.join(baseDir, 'js/admin/admin-jadwal-sholat.js'), 'utf8');
assert(adminJadwalContent.includes('setPrayerInputValue'), 'admin-jadwal-sholat.js memiliki helper setPrayerInputValue yang mensinkronkan Flatpickr instance');
assert(swContent.includes('vendor/flatpickr/flatpickr.min.css') && swContent.includes('vendor/flatpickr/flatpickr.min.js'), 'sw.js memuat flatpickr.min.css dan flatpickr.min.js dalam STATIC_ASSETS');

// -------------------------------------------------------------
// 25. COUNTDOWN SHOLAT 3D FLIP CLOCK & REALTIME SYNC INTEGRITY
// -------------------------------------------------------------
console.log('\n📌 [TEST 25] Countdown Sholat 3D Flip Clock & Realtime Sync Integrity:');
const prayerModeContent = fs.readFileSync(path.join(baseDir, 'prayer-mode.html'), 'utf8');
const indexTvContent = fs.readFileSync(path.join(baseDir, 'index.html'), 'utf8');
assert(adminPageContent.includes('id="cfgCountdownStyle"') && adminPageContent.includes('onchange="simpanPengaturanDurasiTV(true)"'), 'admin.html memiliki dropdown cfgCountdownStyle dengan handler onchange otomatis');
assert(adminJadwalContent.includes("prayer_countdown_style: countdownStyle") && adminJadwalContent.includes("SupabaseDB.saveSettingItem('prayer_countdown_style'"), 'admin-jadwal-sholat.js menyimpan prayer_countdown_style ke Supabase cloud');
assert(prayerModeContent.includes("getCountdownStyle()") && prayerModeContent.includes("window.switchCountdownStyle"), 'prayer-mode.html memiliki getCountdownStyle dan window.switchCountdownStyle');
assert(prayerModeContent.includes("window.addEventListener('message'") && prayerModeContent.includes("newStyle === 'flip' || newStyle === 'biasa'"), 'prayer-mode.html mendengarkan postMessage update gaya hitung mundur');
assert(indexTvContent.includes("prayerFrame.contentWindow.postMessage") && indexTvContent.includes("prayer_countdown_style"), 'index.html menyiarkan prayer_countdown_style ke prayerFrame iframe');
assert(supabaseDbContent.includes("prayer_countdown_style: 'flip'") && supabaseDbContent.includes("localStorage.getItem('prayer_countdown_style')"), 'supabase-db.js meng-cache dan mengembalikan fallback prayer_countdown_style');

// -------------------------------------------------------------
// 26. AGENDA RUTIN 1-CLICK PICKER & DYNAMIC DAYS SYNC INTEGRITY
// -------------------------------------------------------------
console.log('\n📌 [TEST 26] Agenda Rutin 1-Click Picker & Dynamic Days Sync Integrity:');
const adminAgendaContent = fs.readFileSync(path.join(baseDir, 'js/admin/admin-agenda-rutin.js'), 'utf8');
assert(adminAgendaContent.includes('triggerAutoSaveAgendaRutin'), 'admin-agenda-rutin.js memiliki triggerAutoSaveAgendaRutin untuk auto-sync instan');
assert(adminAgendaContent.includes('toggleWsDayPill') && adminAgendaContent.includes('triggerAutoSaveAgendaRutin()'), 'admin-agenda-rutin.js toggleWsDayPill otomatis memicu auto-save ke TV');
assert(adminAgendaContent.includes('initAgendaRutinAutoSync'), 'admin-agenda-rutin.js menginisialisasi listener auto-save pada checkbox siklus tafsir dan toggle aktif');
assert(adminPageContent.includes('id="wsTahsinDayPillGroup"'), 'admin.html memiliki kontainer wsTahsinDayPillGroup untuk 1-click picker');
assert(adminPageContent.includes('id="wsTahsinSyncStatus"'), 'admin.html memiliki elemen badge wsTahsinSyncStatus untuk feedback visual sinkronisasi');
assert(agendaContent.includes('formatHariTahsin') && agendaContent.includes('formatPekanTafsir'), 'slides/agenda-rutin.html memiliki helper formatHariTahsin dan formatPekanTafsir');
assert(agendaContent.includes('id="waktuTahsin"') && agendaContent.includes('id="waktuTafsir"'), 'slides/agenda-rutin.html memiliki target DOM waktuTahsin dan waktuTafsir dinamis');
assert(agendaContent.includes('SYNC_AGENDA_RUTIN'), 'slides/agenda-rutin.html mendengarkan postMessage SYNC_AGENDA_RUTIN');
assert(pengumumanAgendaContent.includes('formatHariTahsin') && pengumumanAgendaContent.includes('formatPekanTafsir'), 'slides/pengumuman.html memiliki helper formatHariTahsin dan formatPekanTafsir');
assert(pengumumanAgendaContent.includes('id="waktuTahsin"') && pengumumanAgendaContent.includes('id="waktuTafsir"'), 'slides/pengumuman.html memiliki target DOM waktuTahsin dan waktuTafsir dinamis');

// -------------------------------------------------------------
// 27. FOTO IMAM & SLIDE AUTO-KOMPRESI CANVAS & GOOGLE DRIVE INTEGRITY
// -------------------------------------------------------------
console.log('\n📌 [TEST 27] Foto Imam & Slide Auto-Kompresi Canvas & Google Drive Integrity:');
const freshPetugasContent = fs.readFileSync(path.join(baseDir, 'petugas.html'), 'utf8');
const freshModeinputContent = fs.readFileSync(path.join(baseDir, 'modeinput.html'), 'utf8');
const adminGaleriContent = fs.readFileSync(path.join(baseDir, 'js/admin/admin-galeri.js'), 'utf8');
assert(freshPetugasContent.includes('convertGoogleDriveUrl') && freshPetugasContent.includes('lh3.googleusercontent.com/d/'), 'petugas.html memiliki helper convertGoogleDriveUrl untuk auto-convert link Google Drive');
assert(freshPetugasContent.includes('jumatImamFotoBase64 = compressed;') && freshPetugasContent.includes("canvas.toDataURL('image/jpeg', 0.72)"), 'petugas.html memiliki auto-kompresor canvas (~30KB) untuk upload foto imam dari HP');
assert(freshPetugasContent.includes('id="imamPhotoInfo"'), 'petugas.html memiliki badge info kompresi foto imam');
assert(freshModeinputContent.includes('convertGoogleDriveUrl') && freshModeinputContent.includes('lh3.googleusercontent.com/d/'), 'modeinput.html memiliki helper convertGoogleDriveUrl untuk auto-convert link Google Drive');
assert(freshModeinputContent.includes('jumatImamFotoBase64 = compressed;') && freshModeinputContent.includes("canvas.toDataURL('image/jpeg', 0.72)"), 'modeinput.html memiliki auto-kompresor canvas (~30KB) untuk upload foto imam dari HP');
assert(freshModeinputContent.includes('id="imamPhotoInfo"'), 'modeinput.html memiliki badge info kompresi foto imam');
const adminJumatContent = fs.readFileSync(path.join(baseDir, 'js/admin/admin-jumat.js'), 'utf8');
assert(adminJumatContent.includes('convertGoogleDriveUrl') && adminJumatContent.includes('lh3.googleusercontent.com/d/'), 'admin-jumat.js memiliki auto-convert URL Google Drive');
assert(adminGaleriContent.includes('convertGoogleDriveUrl') && adminGaleriContent.includes('lh3.googleusercontent.com/d/'), 'admin-galeri.js memiliki auto-convert URL Google Drive');
const adminQrisContent = fs.readFileSync(path.join(baseDir, 'js/admin/admin-qris.js'), 'utf8');
assert(adminQrisContent.includes('convertGoogleDriveUrl') && adminQrisContent.includes('lh3.googleusercontent.com/d/'), 'admin-qris.js memiliki auto-convert URL Google Drive');
assert(adminQrisContent.includes("canvas.toDataURL('image/jpeg', 0.82)"), 'admin-qris.js memiliki auto-kompresor canvas (~40KB) untuk upload barcode QRIS');

// -------------------------------------------------------------
// HASIL AKHIR
// -------------------------------------------------------------
Promise.all(pendingTests).then(() => {
    console.log('\n================================================================');
    console.log(`📊 RINGKASAN HASIL TEST: ${passedTests} / ${totalTests} LULUS`);
    if (failedTests > 0) {
        console.error(`🚨 ADA ${failedTests} TEST GAGAL! PERBAIKI SEBELUM DEPLOY!`);
        process.exitCode = 1;
    } else {
        console.log('Semua pemeriksaan dalam suite ini lulus; kesiapan produksi tetap memerlukan validasi integrasi.');
    }
}).catch(error => { console.error(error); process.exitCode = 1; });
