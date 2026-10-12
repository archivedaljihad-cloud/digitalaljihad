/**
 * SKRIP OTOMASI MODULARISASI ADMIN.HTML
 * DIGITALv304 - Masjid Jami' Al-Jihad
 * 
 * Memecah skrip monolitik 594 KB di web-statis/admin.html menjadi 21 modul
 * independen di web-statis/js/admin/ secara presisi, aman, dan tanpa kehilangan fungsi.
 */

const fs = require('fs');
const path = require('path');

const rootDir = path.resolve(__dirname, '..');
const webStatisDir = path.join(rootDir, 'web-statis');
const adminHtmlPath = path.join(webStatisDir, 'admin.html');
const adminJsDir = path.join(webStatisDir, 'js', 'admin');
const swPath = path.join(webStatisDir, 'sw.js');
const testEnginePath = path.join(rootDir, 'scripts', 'test-engine.js');

const NEW_VERSION = 'aljihad-signage-v5.8.45';
const VERSION_QUERY = '?v=5.8.45';

console.log('🚀 Memulai proses modularisasi admin.html...');

// 1. Pastikan direktori web-statis/js/admin ada
if (!fs.existsSync(adminJsDir)) {
    fs.mkdirSync(adminJsDir, { recursive: true });
    console.log(`📁 Direktori dibuat: ${adminJsDir}`);
}

// 2. Baca admin.html
const adminContent = fs.readFileSync(adminHtmlPath, 'utf8');

// 3. Cari blok skrip utama (#9)
const regex = /<script([\s\S]*?)>([\s\S]*?)<\/script>/gi;
let m, count = 0, script9 = '', script9FullMatch = '';
while ((m = regex.exec(adminContent)) !== null) {
    count++;
    if (count === 9) {
        script9FullMatch = m[0];
        script9 = m[2];
        break;
    }
}

if (!script9 || script9.length < 500000) {
    console.error('❌ Gagal menemukan blok skrip monolitik admin.html (#9). Panjang terbaca:', script9.length);
    process.exit(1);
}

console.log(`📄 Skrip monolitik ditemukan: ${script9.length} bytes (${script9.split('\n').length} baris).`);

const lines = script9.split('\n');

// 4. Definisikan marker pemotongan presisi
const markers = [
    { file: 'admin-core.js', pattern: /^\s*\/\/\s*1\.\s*Verifikasi Autentikasi/ },
    { file: 'admin-users.js', pattern: /MANAJEMEN AKUN PENGGUNA \(KHUSUS SUPER ADMIN\)/ },
    { file: 'admin-ui.js', pattern: /^\s*\/\/\s*5\.\s*Inisialisasi Jam & Widget Topbar/ },
    { file: 'admin-data-loader.js', pattern: /^\s*\/\/\s*6\.\s*LIVE DATA LOADER DARI SUPABASE/ },
    { file: 'admin-kas.js', pattern: /^\s*function renderKasTables\(/ },
    { file: 'admin-infaq.js', pattern: /MANAJEMEN MULTI-PROGRAM PENGGALANGAN INFAQ/ },
    { file: 'admin-qris.js', pattern: /MODUL MANAJEMEN QRIS DONASI/ },
    { file: 'admin-rotasi.js', pattern: /^\s*function renderRotationTable\(/ },
    { file: 'admin-kas-ops.js', pattern: /^\s*function bukaModalEditKas\(/ },
    { file: 'admin-jumat.js', pattern: /PENGELOLAAN UPLOAD FOTO IMAM/ },
    { file: 'admin-kajian.js', pattern: /PENGAJIAN RUTIN MALAM AHAD/ },
    { file: 'admin-jadwal-sholat.js', pattern: /^\s*function updatePrayerSourceBadgeUI\(/ },
    { file: 'admin-running-text.js', pattern: /MANAJEMEN TEKS BERJALAN MULTI-HALAMAN/ },
    { file: 'admin-yasin.js', pattern: /^\s*function selectYasinOption\(/ },
    { file: 'admin-settings.js', pattern: /^\s*(?:async\s+)?function simpanPengaturanSistem\(/ },
    { file: 'admin-ai.js', pattern: /^\s*function updateGeminiStatusBadge\(/ },
    { file: 'admin-rotasi-save.js', pattern: /^\s*(?:async\s+)?function simpanRotasiTV\(/ },
    { file: 'admin-qurban.js', pattern: /MANAJEMEN PENERIMAAN HEWAN QURBAN/ },
    { file: 'admin-ramadhan.js', pattern: /MODUL RAMADHAN: AGENDA TARAWIH/ },
    { file: 'admin-rotasi-extra.js', pattern: /^\s*(?:async\s+)?function toggleSlideKasJumat\(/ },
    { file: 'admin-kas-export.js', pattern: /^\s*function exportKasExcel\(/ },
    { file: 'admin-remote.js', pattern: /LAYANAN REMOT TV & DIAGNOSTIK/ },
    { file: 'admin-galeri.js', pattern: /MANAJEMEN GALERI INFORMASI/ },
    { file: 'admin-undangan.js', pattern: /MANAJEMEN SURAT UNDANGAN LUAR/ },
    { file: 'admin-agenda-rutin.js', pattern: /PUSAT AGENDA RUTIN MASJID/ },
    { file: 'admin-pengumuman-jumat.js', pattern: /VIEW: PENGUMUMAN SHOLAT JUM'AT/ }
];

const found = [];
for (let i = 0; i < lines.length; i++) {
    const line = lines[i];
    for (const m of markers) {
        if (m.pattern.test(line)) {
            found.push({ file: m.file, line: i });
            break;
        }
    }
}

const slices = {};
found.forEach((f, idx) => {
    const nextLine = (idx + 1 < found.length) ? found[idx + 1].line : lines.length;
    slices[f.file] = lines.slice(f.line, nextLine).join('\n');
});

// 5. Gabungkan menjadi 21 modul domain yang bersih & kohesif
let coreCode = (slices['admin-core.js'] || '') + '\n\n' + (slices['admin-ui.js'] || '');
// Ganti let menjadi var untuk variabel state global di admin-core agar cross-module safe di browser
coreCode = coreCode.replace(/let currentUser = null;/, 'var currentUser = null;');
coreCode = coreCode.replace(/let cachedKasData = \[\];/, 'var cachedKasData = [];');
coreCode = coreCode.replace(/let cachedAmbData = \[\];/, 'var cachedAmbData = [];');
coreCode = coreCode.replace(/let cachedRotationPages = \[\];/, 'var cachedRotationPages = [];');
coreCode = coreCode.replace(/let cachedQurbanData = \[\];/, 'var cachedQurbanData = [];');
coreCode = coreCode.replace(/let cachedProgramInfaq = \[\];/, 'var cachedProgramInfaq = [];');
coreCode = coreCode.replace(/let cachedDonasiInfaq = \[\];/, 'var cachedDonasiInfaq = [];');
coreCode = coreCode.replace(/let selectedProgramInfaqId = null;/, 'var selectedProgramInfaqId = null;');
coreCode = coreCode.replace(/let cachedQrisList = \[\];/, 'var cachedQrisList = [];');
coreCode = coreCode.replace(/let uploadedQrisBase64 = null;/, 'var uploadedQrisBase64 = null;');

let settingsCode = slices['admin-settings.js'] || '';
// Bumping cache cleaner version ke NEW_VERSION
settingsCode = settingsCode.replace(/aljihad-signage-v5\.\d+\.\d+/g, NEW_VERSION);

const modules = {
    'admin-core.js': coreCode,
    'admin-data-loader.js': slices['admin-data-loader.js'] || '',
    'admin-users.js': slices['admin-users.js'] || '',
    'admin-kas.js': (slices['admin-kas.js'] || '') + '\n\n' + (slices['admin-kas-ops.js'] || '') + '\n\n' + (slices['admin-kas-export.js'] || ''),
    'admin-infaq.js': slices['admin-infaq.js'] || '',
    'admin-qris.js': slices['admin-qris.js'] || '',
    'admin-rotasi.js': (slices['admin-rotasi.js'] || '') + '\n\n' + (slices['admin-rotasi-save.js'] || '') + '\n\n' + (slices['admin-rotasi-extra.js'] || ''),
    'admin-jumat.js': slices['admin-jumat.js'] || '',
    'admin-kajian.js': slices['admin-kajian.js'] || '',
    'admin-jadwal-sholat.js': slices['admin-jadwal-sholat.js'] || '',
    'admin-running-text.js': slices['admin-running-text.js'] || '',
    'admin-yasin.js': slices['admin-yasin.js'] || '',
    'admin-settings.js': settingsCode,
    'admin-ai.js': slices['admin-ai.js'] || '',
    'admin-qurban.js': slices['admin-qurban.js'] || '',
    'admin-ramadhan.js': slices['admin-ramadhan.js'] || '',
    'admin-remote.js': slices['admin-remote.js'] || '',
    'admin-galeri.js': slices['admin-galeri.js'] || '',
    'admin-undangan.js': slices['admin-undangan.js'] || '',
    'admin-agenda-rutin.js': slices['admin-agenda-rutin.js'] || '',
    'admin-pengumuman-jumat.js': slices['admin-pengumuman-jumat.js'] || ''
};

// 6. Validasi sintaks setiap modul & simpan berkas
console.log('\n🔍 Memvalidasi sintaks dan menulis 21 berkas modul...');
for (const [filename, code] of Object.entries(modules)) {
    try {
        new Function(code);
    } catch(err) {
        console.error(`❌ FATAL: Sintaks error pada modul ${filename}:`, err.message);
        process.exit(1);
    }

    const banner = `/**\n * MODUL ADMIN: ${filename}\n * Digital Signage Masjid Jami' Al-Jihad\n * Versi: ${NEW_VERSION}\n */\n\n`;
    const targetFile = path.join(adminJsDir, filename);
    fs.writeFileSync(targetFile, banner + code, 'utf8');
    console.log(`  ✅ Wrote ${filename} (${code.length} bytes, ${code.split('\n').length} baris)`);
}

// 7. Siapkan tag script pengganti di admin.html
const moduleScriptTags = [
    '    <!-- ======================================================= -->',
    '    <!-- MODULARISASI PANEL ADMIN SCRIPTS (AL-JIHAD SIGNAGE)     -->',
    '    <!-- ======================================================= -->',
    `    <script src="js/admin/admin-core.js${VERSION_QUERY}"></script>`,
    `    <script src="js/admin/admin-data-loader.js${VERSION_QUERY}"></script>`,
    `    <script src="js/admin/admin-users.js${VERSION_QUERY}"></script>`,
    `    <script src="js/admin/admin-kas.js${VERSION_QUERY}"></script>`,
    `    <script src="js/admin/admin-infaq.js${VERSION_QUERY}"></script>`,
    `    <script src="js/admin/admin-qris.js${VERSION_QUERY}"></script>`,
    `    <script src="js/admin/admin-rotasi.js${VERSION_QUERY}"></script>`,
    `    <script src="js/admin/admin-jumat.js${VERSION_QUERY}"></script>`,
    `    <script src="js/admin/admin-kajian.js${VERSION_QUERY}"></script>`,
    `    <script src="js/admin/admin-jadwal-sholat.js${VERSION_QUERY}"></script>`,
    `    <script src="js/admin/admin-running-text.js${VERSION_QUERY}"></script>`,
    `    <script src="js/admin/admin-yasin.js${VERSION_QUERY}"></script>`,
    `    <script src="js/admin/admin-settings.js${VERSION_QUERY}"></script>`,
    `    <script src="js/admin/admin-ai.js${VERSION_QUERY}"></script>`,
    `    <script src="js/admin/admin-qurban.js${VERSION_QUERY}"></script>`,
    `    <script src="js/admin/admin-ramadhan.js${VERSION_QUERY}"></script>`,
    `    <script src="js/admin/admin-remote.js${VERSION_QUERY}"></script>`,
    `    <script src="js/admin/admin-galeri.js${VERSION_QUERY}"></script>`,
    `    <script src="js/admin/admin-undangan.js${VERSION_QUERY}"></script>`,
    `    <script src="js/admin/admin-agenda-rutin.js${VERSION_QUERY}"></script>`,
    `    <script src="js/admin/admin-pengumuman-jumat.js${VERSION_QUERY}"></script>`
].join('\n');

// 8. Gantikan blok script monolitik di admin.html
const updatedAdminHtml = adminContent.replace(script9FullMatch, moduleScriptTags);
fs.writeFileSync(adminHtmlPath, updatedAdminHtml, 'utf8');
console.log(`\n🎉 admin.html berhasil diperbarui! Ukuran berkas berkurang dari ${adminContent.length} bytes menjadi ${updatedAdminHtml.length} bytes.`);

// 9. Bumping cache di sw.js
let swContent = fs.readFileSync(swPath, 'utf8');
swContent = swContent.replace(/const CACHE_NAME = 'aljihad-signage-v5\.\d+\.\d+';/, `const CACHE_NAME = '${NEW_VERSION}';`);
fs.writeFileSync(swPath, swContent, 'utf8');
console.log(`✅ Service Worker di-bump ke ${NEW_VERSION}`);

// 10. Update test-engine.js untuk validasi otomatis semua berkas modul baru & cache cleaner
let testEngineContent = fs.readFileSync(testEnginePath, 'utf8');

// Pastikan test-engine mendeteksi js/admin/*.js
if (!testEngineContent.includes("path.join(baseDir, 'js/admin')")) {
    const adminScanCode = `
// Tambahkan seluruh modul admin di js/admin/
const adminJsDir = path.join(baseDir, 'js/admin');
if (fs.existsSync(adminJsDir)) {
    fs.readdirSync(adminJsDir).forEach(f => {
        if (f.endsWith('.js')) filesToCheck.push(\`js/admin/\${f}\`);
    });
}
`;
    testEngineContent = testEngineContent.replace(
        "// Tambahkan seluruh slide",
        adminScanCode.trim() + "\n\n// Tambahkan seluruh slide"
    );
}

// Pastikan test-engine mengecek cache cleaner di admin-settings.js jika ada
if (!testEngineContent.includes("adminSettingsContent")) {
    const oldCacheCheck = `const adminMatch = adminContent.match(/if\\s*\\(name\\s*!==\\s*['"]([^'"]+)['"]\\)/);`;
    const newCacheCheck = `const adminSettingsPath = path.join(baseDir, 'js/admin/admin-settings.js');
const adminSettingsContent = fs.existsSync(adminSettingsPath) ? fs.readFileSync(adminSettingsPath, 'utf8') : '';
const combinedAdmin = adminContent + '\\n' + adminSettingsContent;
const adminMatch = combinedAdmin.match(/if\\s*\\(name\\s*!==\\s*['"]([^'"]+)['"]\\)/);`;
    testEngineContent = testEngineContent.replace(oldCacheCheck, newCacheCheck);
}

fs.writeFileSync(testEnginePath, testEngineContent, 'utf8');
console.log(`✅ scripts/test-engine.js diperbarui untuk memverifikasi 21 modul baru.`);

console.log('\n✨ PROSES MODULARISASI SELESAI DENGAN SUKSES! Siap menjalankan npm test.');
