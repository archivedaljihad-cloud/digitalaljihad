// Local integration only. Every remote request is intercepted; no production writes.
const { chromium } = require('playwright');
const fs = require('fs'), path = require('path'), http = require('http'), assert = require('assert/strict');
const root = path.resolve(__dirname, '../web-statis');
const server = http.createServer((req, res) => {
    let file = path.join(root, decodeURIComponent(new URL(req.url, 'http://localhost').pathname));
    if (file === root + '/') file += 'index.html';
    if (!path.extname(file)) file += '.html';
    if (!file.startsWith(root + path.sep)) return res.writeHead(403).end();
    fs.readFile(file, (error, body) => {
        if (error) return res.writeHead(404).end();
        res.setHeader('Content-Type', ({ '.html': 'text/html', '.js': 'text/javascript', '.json': 'application/json', '.css': 'text/css', '.png': 'image/png', '.jpg': 'image/jpeg', '.webp': 'image/webp' })[path.extname(file)] || 'application/octet-stream');
        res.end(body);
    });
});
(async () => {
    await new Promise(resolve => server.listen(0, '127.0.0.1', resolve));
    const origin = `http://127.0.0.1:${server.address().port}`;
    const browser = await chromium.launch({ executablePath: process.env.SIGNAGE_CHROMIUM_PATH || (fs.existsSync('/usr/bin/chromium') ? '/usr/bin/chromium' : undefined), headless: true, args: ['--no-sandbox'] });
    try {
        const cases = { jumat: 'sholat_jumat', 'kas-jumat': 'keuangan', kajian: 'kajian', slide: 'slides', 'live-madinah': 'live_madinah', yasin: 'yasin', undangan: 'undangan', hikmah: 'hikmah' };
        for (const [slide, table] of Object.entries(cases)) {
            if (process.env.TEST_SLIDE && process.env.TEST_SLIDE !== slide) continue;
            let delay = 0, offline = false, revision = 1;
            const context = await browser.newContext({ timezoneId: 'Asia/Jakarta', viewport: { width: 1920, height: 1080 }, serviceWorkers: 'block' });
            await context.addInitScript(() => { try { localStorage.setItem('kemenag_auto_sync', 'false'); } catch (error) {} });
            const now = new Date(); const date = '2026-10-16';
            const officer = () => ({ schema_version: 2, tanggal: date, khatib: 'Khatib ' + revision, imam: 'Imam ' + revision, muadzin: 'Satu', muadzin2: 'Dua', bilal: 'Bilal', waktu: '12:03', durasi: 45, foto_imam: 'image/display/default_imam.jpg' });
            await context.route('**/*', async route => {
                const req = route.request(), url = new URL(req.url());
                if (url.origin === origin) return route.continue();
                if (!url.hostname.endsWith('.supabase.co') || req.method() !== 'GET') return route.abort();
                if (delay) await new Promise(resolve => setTimeout(resolve, delay));
                if (offline) return route.fulfill({ status: 503, contentType: 'application/json', body: '{"message":"fixture offline"}' });
                let data = [];
                if (url.pathname.includes('/app_settings')) data = [{ id: 1, nama_aplikasi: 'MASJID FIXTURE ' + revision, prayer_mode_enabled: false, rotation_interval: 300,
                    live_madinah_url: revision === 1 ? 'https://www.youtube.com/watch?v=Rs7St51oDDc' : 'https://www.youtube.com/watch?v=dQw4w9WgXc', running_text_pages: { [slide]: 'Teks fixture ' + revision },
                    rotation_pages: [{ path: `slides/${slide}.html`, page: '/' + slide + '-embed', active: true, order: 1 }] },
                    { id: 2, key: 'sholat_jumat', value: JSON.stringify(officer()) },
                    { id: 3, key: 'pengumuman_jumat', value: JSON.stringify({ saldo_awal: 10000, pemasukan: 2500, pengeluaran: 500, saldo_akhir: 12000 }) },
                    { id: 4, key: 'undangan_eksternal_list', value: JSON.stringify([{ nama_acara: 'Undangan ' + revision, is_active: true }]) }];
                if (url.pathname.includes('/sholat_jumat')) data = [officer()];
                if (url.pathname.includes('/jadwal_sholat')) data = [{ id: 1, nama_sholat: 'Subuh', waktu: '04:30' }, { id: 2, nama_sholat: 'Isya', waktu: '23:30' }];
                if (url.pathname.includes('/keuangan')) data = [{ tanggal: '2020-01-01', pemasukan: 10000, pengeluaran: 0 }, { tanggal: date, pemasukan: revision * 1000, pengeluaran: 200 }];
                if (url.pathname.endsWith('/slides')) data = [{ id: 1, judul: 'Poster ' + revision, gambar: 'image/display/default_imam.jpg', aktif: true }];
                return route.fulfill({ contentType: 'application/json', headers: { 'access-control-allow-origin': '*' }, body: JSON.stringify(data) });
            });
            const page = await context.newPage(), errors = [];
            page.on('pageerror', error => errors.push(error.message));
            if (process.env.TEST_DEBUG) page.on('console', msg => { if (['warning','error'].includes(msg.type())) console.log(slide, msg.text()); });
            await page.clock.setFixedTime(new Date('2026-10-16T09:00:00+07:00'));
            await page.goto(origin, { waitUntil: 'domcontentloaded' });
            await page.waitForFunction(slide => SupabaseDB._slideReadiness?.has(slide), slide, { timeout: 15000 });
            revision = 2; delay = 900;
            await page.evaluate(({ table }) => {
                window.__acks = [];
                SupabaseDB.sendTvAcknowledgment = (device, event, table, action) => window.__acks.push({ event, table, action });
                window.postMessage({ type: 'DATA_UPDATED', event_id: 'edit-test', table, data: {} }, location.origin);
            }, { table });
            await page.waitForTimeout(600);
            assert.equal(await page.evaluate(() => __acks.some(ack => ack.action === 'APPLIED')), false, slide + ': no premature ACK');
            await page.waitForFunction(() => __acks.some(ack => ack.event === 'edit-test' && ack.action === 'APPLIED'), null, { timeout: 15000 });
            const frame = page.frameLocator('#frame1');
            if (slide === 'live-madinah') assert.match(await frame.locator('#videoIframe').getAttribute('src'), /dQw4w9WgXc/);
            else assert.match(await frame.locator('#nama-masjid').textContent(), /FIXTURE 2/);
            if (slide === 'jumat') {
                assert.match(await frame.locator('#valImamKhatib').textContent(), /Khatib 2/);
                assert.match(await frame.locator('#valMuadzin').textContent(), /Satu \/ Dua/);
                assert.match(await frame.locator('#valWaktuJumat').textContent(), /12:03/);
            }
            if (slide === 'kas-jumat') assert.match(await frame.locator('#tvKasSaldoAkhir').textContent(), /11\.800/);
            if (slide === 'slide') assert.match(await frame.locator('#slideTitle').textContent(), /Poster 2/);
            if (slide === 'undangan') assert.match(await frame.locator('#invAcara').textContent(), /Undangan 2/);
            console.log('PASS:', slide, 'startup readiness + correlated ACK follows fresh DOM');
            // Keep last display on failed refresh; don't claim the failed event was applied.
            delay = 0; offline = true;
            await page.evaluate(table => window.postMessage({ type: 'DATA_UPDATED', event_id: 'offline-test', table, data: {} }, location.origin), table);
            await page.waitForTimeout(600);
            assert.equal(await page.evaluate(() => __acks.some(ack => ack.event === 'offline-test' && ack.action === 'APPLIED')), false);
            if (slide === 'live-madinah') assert.match(await frame.locator('#videoIframe').getAttribute('src'), /dQw4w9WgXc/);
            else assert.match(await frame.locator('#nama-masjid').textContent(), /FIXTURE 2/);
            assert.deepEqual(errors, [], slide + ': no uncaught browser errors');
            await context.close();
        }
        // Exercise the actual Friday form and its single batch endpoint, not a mock save helper.
        {
            let failWrites = true;
            const writes = [];
            const values = new Map([
                ['sholat_jumat', { schema_version: 2, tanggal: '2026-10-16', khatib: 'Lama', imam: 'Lama', muadzin: 'Satu', muadzin2: 'Dua', foto_imam: 'image/display/default_imam.jpg', waktu: '11:45', durasi: 50 }],
                ['pengumuman_jumat', { saldo_awal: 1000, pemasukan: 200, pengeluaran: 100, saldo_akhir: 1100, petugas: { imamKhotib: 'Lama' } }]
            ]);
            const context = await browser.newContext({ timezoneId: 'Asia/Jakarta', serviceWorkers: 'block' });
            await context.route('**/*', async route => {
                const req = route.request(), url = new URL(req.url());
                if (url.origin === origin) {
                    if (url.pathname === '/js/admin-auth.js') return route.fulfill({ contentType: 'text/javascript', body: 'window.AdminAuth = undefined;' });
                    return route.continue();
                }
                if (!url.hostname.endsWith('.supabase.co')) return route.abort();
                let data = [];
                if (req.method() === 'POST' && url.pathname.endsWith('/app_settings')) {
                    const rows = req.postDataJSON(); writes.push(rows);
                    if (failWrites) return route.fulfill({ status: 403, contentType: 'application/json', body: '{"message":"fixture forbidden"}' });
                    assert.ok(Array.isArray(rows));
                    for (const row of rows) values.set(row.key, JSON.parse(row.value));
                    data = rows;
                } else if (req.method() !== 'GET') return route.abort();
                else if (url.pathname.endsWith('/app_settings')) data = [{ id: 1, nama_aplikasi: 'FIXTURE ADMIN', prayer_mode_enabled: false }, ...Array.from(values, ([key, value], index) => ({ id: index + 2, key, value: JSON.stringify(value) }))];
                else if (url.pathname.endsWith('/sholat_jumat')) data = [values.get('sholat_jumat')];
                return route.fulfill({ contentType: 'application/json', headers: { 'access-control-allow-origin': '*' }, body: JSON.stringify(data) });
            });
            const admin = await context.newPage(), errors = [];
            admin.on('pageerror', error => errors.push(error.message));
            await admin.goto(origin + '/admin.html', { waitUntil: 'domcontentloaded' });
            await admin.waitForFunction(() => document.getElementById('jumatMuadzin').value === 'Satu');
            const before = await admin.evaluate(() => localStorage.getItem('cached_sholat_jumat'));
            await admin.evaluate(() => {
                document.getElementById('jumatKhotib').value = 'Dr. H. Nama Khatib Baru yang Panjang, Lc., M.Ag.';
                document.getElementById('jumatImam').value = 'Imam Baru';
                document.getElementById('jumatMuadzin').value = 'Muadzin Baru';
                document.getElementById('jumatWaktu').value = '12:07';
                triggerAutoSavePetugasJumat();
            });
            assert.equal(await admin.evaluate(() => simpanPetugasJumat(true)), false);
            assert.equal(await admin.evaluate(() => localStorage.getItem('cached_sholat_jumat')), before);
            assert.ok(await admin.evaluate(() => !!localStorage.getItem('draft_petugas_jumat')));
            assert.match(await admin.locator('#jumatSaveStatus').textContent(), /Belum terkonfirmasi/);
            failWrites = false;
            const count = writes.length;
            assert.equal(await admin.evaluate(() => simpanPetugasJumat(true)), true);
            assert.equal(writes.length, count + 1, 'One HTTP mutation for the entire Friday snapshot');
            assert.ok(writes.at(-1).some(row => row.key === 'sholat_jumat') && writes.at(-1).some(row => row.key === 'prayer_mode_jumat_duration'));
            assert.equal(values.get('sholat_jumat').muadzin, 'Muadzin Baru');
            assert.equal(values.get('sholat_jumat').waktu, '12:07');
            assert.equal(values.get('pengumuman_jumat').saldo_awal, 1000);
            assert.equal(await admin.evaluate(() => localStorage.getItem('draft_petugas_jumat')), null);
            assert.deepEqual(errors, []);
            console.log('PASS: Friday admin HTTP403 preserves draft/cache; retry commits one complete batch');
            await context.close();
        }
    } finally { await browser.close(); server.close(); }
})().catch(error => { console.error(error); server.close(); process.exitCode = 1; });
