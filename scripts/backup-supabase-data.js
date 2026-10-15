/**
 * scripts/backup-supabase-data.js
 * Ekspor data tabel publik Supabase yang dapat diakses melalui REST API
 */

const fs = require('fs');
const path = require('path');
const https = require('https');

const SUPABASE_CONFIG = {
    url: 'https://xskusfacwsclbgdtgier.supabase.co',
    anonKey: 'sb_publishable_lsUgbFcTmwuwiiV70rzSWQ_V0JUR-mX'
};

const tables = [
    'app_settings',
    'jadwal_sholat',
    'sholat_jumat',
    'sholat_idul_fitri',
    'sholat_idul_adha',
    'pengumuman',
    'keuangan',
    'keuangan_ambulance',
    'program_infaq',
    'donasi_infaq',
    'qris',
    'slides',
    'slide_informasis',
    'agenda_kajian'
];

const backupDir = 'c:\\Users\\anthu\\Documents\\supabase_data_backup_20261015';
if (!fs.existsSync(backupDir)) {
    fs.mkdirSync(backupDir, { recursive: true });
}

function fetchTable(table) {
    return new Promise((resolve, reject) => {
        const url = `${SUPABASE_CONFIG.url}/rest/v1/${table}?select=*`;
        const req = https.get(url, {
            headers: {
                'apikey': SUPABASE_CONFIG.anonKey,
                'Authorization': `Bearer ${SUPABASE_CONFIG.anonKey}`
            }
        }, (res) => {
            let body = '';
            res.on('data', chunk => body += chunk);
            res.on('end', () => {
                if (res.statusCode >= 200 && res.statusCode < 300) {
                    try {
                        const parsed = JSON.parse(body);
                        resolve({ table, status: res.statusCode, count: parsed.length, data: parsed });
                    } catch (e) {
                        resolve({ table, status: res.statusCode, count: 0, error: 'JSON parse error' });
                    }
                } else {
                    resolve({ table, status: res.statusCode, count: 0, error: body });
                }
            });
        });
        req.on('error', err => reject(err));
        req.setTimeout(8000, () => {
            req.destroy();
            resolve({ table, status: 0, count: 0, error: 'Timeout' });
        });
    });
}

async function run() {
    console.log('🔄 Memulai ekspor data Supabase yang dapat diakses...');
    const summary = [];
    for (const t of tables) {
        try {
            const res = await fetchTable(t);
            if (res.data) {
                fs.writeFileSync(path.join(backupDir, `${t}.json`), JSON.stringify(res.data, null, 2), 'utf8');
                console.log(`  ✅ [${t}]: Berhasil ekspor ${res.count} baris (HTTP ${res.status})`);
                summary.push({ table: t, success: true, count: res.count });
            } else {
                console.warn(`  ⚠️ [${t}]: Gagal ekspor (HTTP ${res.status}) - ${res.error}`);
                summary.push({ table: t, success: false, error: res.error });
            }
        } catch (e) {
            console.error(`  ❌ [${t}]: Error koneksi - ${e.message}`);
            summary.push({ table: t, success: false, error: e.message });
        }
    }
    fs.writeFileSync(path.join(backupDir, 'backup_summary.json'), JSON.stringify({
        timestamp: new Date().toISOString(),
        supabase_url: SUPABASE_CONFIG.url,
        summary
    }, null, 2), 'utf8');
    console.log(`\n📁 Data backup tersimpan di: ${backupDir}`);
}

run();
