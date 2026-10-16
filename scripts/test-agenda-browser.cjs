// Local integration test. All remote requests are fulfilled/aborted; no production writes.
const { chromium } = require('playwright');
const http = require('http'), fs = require('fs'), path = require('path'), assert = require('assert/strict');
const directory = path.resolve(__dirname, '../web-statis');
fs.mkdirSync(path.resolve('work'), { recursive: true });
let failWrites = false;
let agenda = { yasin: { enabled: true, judul: 'Yasin', waktu: '18:00' }, kajian_ahad: { enabled: true, judul: 'Kajian', waktu: '18:10' }, tahsin: { enabled: true, judul: 'Tahsin', hari_aktif: ['senin'], waktu: '20:00 WIB' }, tafsir: { enabled: true, judul: 'Tafsir', pekan_aktif: [1], waktu: '05:00 WIB' } };
const baseSettings = { id: 1, nama_aplikasi: 'MASJID TEST', prayer_mode_enabled: false, rotation_interval: 300,
    rotation_pages: [{ path: 'slides/agenda-rutin.html', page: '/agenda-rutin-embed', active: true, order: 1 }, { path: 'slides/pengumuman.html', page: '/pengumuman-embed', active: true, order: 2 }] };
const server = http.createServer((req, res) => {
    let file = path.join(directory, decodeURIComponent(new URL(req.url, 'http://localhost').pathname));
    if (file === directory + '/') file += 'index.html';
    if (!path.extname(file)) file += '.html';
    if (!file.startsWith(directory + path.sep)) return res.writeHead(403).end();
    fs.readFile(file, (error, body) => {
        if (error) return res.writeHead(404).end();
        res.setHeader('Content-Type', ({ '.html': 'text/html', '.js': 'text/javascript', '.css': 'text/css', '.png': 'image/png', '.jpg': 'image/jpeg', '.webp': 'image/webp' })[path.extname(file)] || 'application/octet-stream');
        res.end(body);
    });
});
(async () => {
    await new Promise(resolve => server.listen(0, '127.0.0.1', resolve));
    const origin = `http://127.0.0.1:${server.address().port}`;
    const browser = await chromium.launch({ executablePath: process.env.SIGNAGE_CHROMIUM_PATH || (fs.existsSync('/usr/bin/chromium') ? '/usr/bin/chromium' : undefined), headless: true, args: ['--no-sandbox'] });
    try {
        const context = await browser.newContext({ viewport: { width: 1920, height: 1080 }, timezoneId: 'Asia/Jakarta', serviceWorkers: 'block' });
        await context.addInitScript(() => localStorage.setItem('kemenag_auto_sync', 'false'));
        await context.route('**/*', async route => {
            const request = route.request(), url = new URL(request.url());
            if (url.origin === origin) {
                // Authentication fixture is LOCAL ONLY. Never navigate to production.
                if (url.pathname === '/js/admin-auth.js') return route.fulfill({ contentType: 'text/javascript', body: 'window.AdminAuth = undefined;' });
                return route.continue();
            }
            if (!url.hostname.endsWith('.supabase.co')) return route.abort();
            let data = [];
            if (request.method() !== 'GET' && failWrites) return route.fulfill({ status: 403, contentType: 'application/json', headers: { 'access-control-allow-origin': '*' }, body: '{"message":"Simulated forbidden"}' });
            if (url.pathname.includes('/app_settings')) {
                const key = (url.searchParams.get('key') || '').replace(/^eq\./, '');
                if (request.method() === 'GET') {
                    const rows = [{ ...baseSettings }, { id: 2, key: 'kegiatan_rutin_settings', value: JSON.stringify(agenda) }, { id: 3, key: 'prayer_countdown_style', value: 'flip' }];
                    data = key ? rows.filter(row => row.key === key) : rows;
                } else {
                    const body = request.postDataJSON() || {};
                    if (key === 'kegiatan_rutin_settings') agenda = JSON.parse(body.value);
                    data = [{ id: key ? 2 : 1, ...body }];
                }
            }
            if (url.pathname.includes('/jadwal_sholat')) data = [{ id: 1, nama_sholat: 'Subuh', waktu: '04:30:00' }, { id: 2, nama_sholat: 'Isya', waktu: '23:30:00' }];
            return route.fulfill({ status: 200, contentType: 'application/json', headers: { 'access-control-allow-origin': '*' }, body: JSON.stringify(data) });
        });
        const tv = await context.newPage(), admin = await context.newPage();
        const errors = [];
        for (const [name, page] of [['TV', tv], ['ADMIN', admin]]) page.on('pageerror', error => errors.push(name + ': ' + error.message));
        await tv.goto(origin, { waitUntil: 'domcontentloaded' });
        await tv.waitForFunction(() => document.getElementById('frame1').contentDocument?.getElementById('waktuTahsin'));
        await admin.goto(origin + '/admin.html', { waitUntil: 'domcontentloaded' });
        await admin.waitForTimeout(1800);
        assert.equal(await admin.evaluate(() => typeof simpanDataPengumumanJumat), 'function');
        console.log('PASS: corrected Friday module path loads and declares its functions');
        const before = await admin.evaluate(() => localStorage.getItem('agenda_rutin_settings'));
        await admin.evaluate(() => {
            document.querySelectorAll('.ws-day-pill').forEach(button => button.classList.remove('btn-warning'));
            document.querySelectorAll('.ws-tafsir-pekan').forEach(input => input.checked = false);
        });
        failWrites = true;
        assert.equal(await admin.evaluate(() => simpanAgendaRutinAdmin(true)), false);
        assert.equal(await admin.evaluate(() => localStorage.getItem('agenda_rutin_settings')), before);
        assert.match(await admin.locator('#wsTahsinSyncStatus').textContent(), /Belum tersimpan/);
        console.log('PASS: HTTP403 is visible; draft retained and confirmed agenda cache unchanged');
        failWrites = false;
        assert.equal(await admin.evaluate(() => simpanAgendaRutinAdmin(true)), true);
        assert.deepEqual(agenda.tahsin.hari_aktif, []);
        assert.deepEqual(agenda.tafsir.pekan_aktif, []);
        await tv.waitForFunction(() => document.getElementById('frame1').contentDocument?.getElementById('waktuTahsin').textContent.includes('Belum ada hari dipilih'));
        await tv.waitForFunction(() => document.getElementById('frame2').contentDocument?.getElementById('waktuTafsir').textContent.includes('Belum ada pekan dipilih'));
        assert.equal(await tv.evaluate(() => settings.kegiatan_rutin_settings.tahsin.hari_aktif.length), 0);
        console.log('PASS: empty selections persist to API, parent memory and both iframe DOMs without restoring defaults');
        await admin.evaluate(() => {
            document.querySelectorAll('.ws-day-pill').forEach(button => button.classList.toggle('btn-warning', ['selasa', 'kamis'].includes(button.dataset.day)));
            document.querySelectorAll('.ws-tafsir-pekan').forEach(input => input.checked = [2, 4].includes(Number(input.value)));
        });
        assert.equal(await admin.evaluate(() => simpanAgendaRutinAdmin(true)), true);
        await tv.waitForFunction(() => document.getElementById('frame1').contentDocument?.getElementById('waktuTahsin').textContent.includes('Selasa, Kamis'));
        await tv.waitForFunction(() => document.getElementById('frame2').contentDocument?.getElementById('waktuTahsin').textContent.includes('Selasa, Kamis'));
        console.log('PASS: a second non-default change reaches both slides without reload');
        agenda.tahsin.hari_aktif = ['ahad'];
        await tv.evaluate(() => {
            settings.kegiatan_rutin_settings.tahsin.hari_aktif = ['STALE'];
            window.postMessage({ type: 'DATA_UPDATED', table: 'agenda_rutin', event_id: 'external-agenda', data: {} }, location.origin);
        });
        await tv.waitForFunction(() => settings.kegiatan_rutin_settings.tahsin.hari_aktif[0] === 'ahad');
        await tv.waitForFunction(() => document.getElementById('frame1').contentDocument?.getElementById('waktuTahsin').textContent.includes('Ahad'));
        console.log('PASS: logical agenda event replaces deliberately stale parent settings from fresh cloud fixture');
        console.log('Browser uncaught errors:', JSON.stringify(errors));
        assert.deepEqual(errors, []);
        await tv.screenshot({ path: 'work/agenda-fixed-tv.png' });
    } finally { await browser.close(); server.close(); }
})().catch(error => { console.error(error); server.close(); process.exitCode = 1; });
