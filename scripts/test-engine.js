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
        scripts.forEach((s, idx) => {
            if (s.includes('src=')) return;
            const code = s.replace(/<script[^>]*>/i, '').replace(/<\/script>/i, '');
            try {
                new Function(code);
            } catch (err) {
                htmlValid = false;
                console.error(`     Syntax error in ${relPath} script #${idx}:`, err.message);
            }
        });
        assert(htmlValid, `Validasi sintaks JavaScript di ${relPath}`);
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

// Test 2.3: Malam Ahad / Kajian Sabtu (Sabtu 10 Okt 2026, 18:25 s/d 18:52)
const satKajianTime = new Date('2026-10-10T18:30:00+07:00');
const satState = window.PrayerEngine.getPrayerState(mockSettings, mockJadwal, null, satKajianTime);
assert(satState.kajian_active === true, 'Malam Ahad 18:30 WIB: Slide Kajian harus AKTIF');

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
const adminMatch = adminContent.match(/if\s*\(name\s*!==\s*['"]([^'"]+)['"]\)/);

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

// -------------------------------------------------------------
// 7. PRAYER MODE REALTIME & OFFICER SYNC INTEGRITY
// -------------------------------------------------------------
console.log('\n📌 [TEST 7] Prayer Mode Realtime & Officer Sync Integrity:');
const prayerContent = fs.readFileSync(path.join(baseDir, 'prayer-mode.html'), 'utf8');
assert(prayerContent.includes('SupabaseDB.onDataChange'), 'prayer-mode.html memiliki listener universal SupabaseDB.onDataChange');
assert(prayerContent.includes('cardMaklumat') && prayerContent.includes('has-maklumat'), 'prayer-mode.html memiliki komponen display Pembaca Maklumat');
assert(prayerContent.includes('init(true)') || prayerContent.includes('forceFresh'), 'prayer-mode.html mendukung forceFresh cache bypass saat update');
assert(prayerContent.includes('window.initPrayerMode') && prayerContent.includes('window.updatePrayerUI'), 'prayer-mode.html mengekspos public interface bridge ke parent TV');

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
// HASIL AKHIR
// -------------------------------------------------------------
console.log('\n================================================================');
console.log(`📊 RINGKASAN HASIL TEST: ${passedTests} / ${totalTests} LULUS`);
if (failedTests > 0) {
    console.error(`🚨 ADA ${failedTests} TEST GAGAL! PERBAIKI SEBELUM DEPLOY!`);
    process.exit(1);
} else {
    console.log('🎉 SEMUA PENGUJIAN LULUS DENGAN SEMPURNA! SISTEM SIAP & AMAN DEPLOY.');
    process.exit(0);
}
