'use strict';
const assert = require('assert/strict');
const fs = require('fs');
const vm = require('vm');
const { extractFunctions } = require('./lib/source-functions');
let checks = 0;
function check(value, label) { assert.ok(value, label); checks++; }
const flush = async () => { for (let i = 0; i < 12; i++) await Promise.resolve(); };

async function precache(fail = new Set()) {
    const listeners = {}, pending = [], attempts = [], deleted = [];
    let active = 0, max = 0, skipped = false;
    const cache = { add(url) {
        attempts.push(url); active++; max = Math.max(max, active);
        return new Promise((resolve, reject) => pending.push(() => {
            active--; fail.has(url) ? reject(new Error('fixture offline')) : resolve();
        }));
    } };
    vm.runInNewContext(fs.readFileSync('web-statis/sw.js', 'utf8'), {
        self: { addEventListener(type, callback) { listeners[type] = callback; }, skipWaiting() { skipped = true; } },
        caches: { async open() { return cache; }, async delete(key) { deleted.push(key); } },
        console: { log() {}, warn() {}, error() {} }, Promise, Response
    });
    let finished = false, failure = null;
    listeners.install({ waitUntil(promise) { promise.then(() => { finished = true; }, error => { finished = true; failure = error; }); } });
    await flush();
    check(attempts.length === 3, 'Only three requests start before any precache response');
    let rounds = 0;
    while (!finished && rounds++ < 100) {
        pending.splice(0).forEach(resolve => resolve());
        await flush();
    }
    check(finished && max <= 3, 'Installation settles with at most three concurrent requests');
    check(attempts.includes('slides/agenda-rutin.html') && attempts.includes('image/display/background/BG11.webp'), 'Full offline slide/media inventory is retained');
    return { failure, deleted, skipped };
}

async function startup() {
    const calls = [], resolvers = {}, timers = [], elements = {};
    for (const id of ['splashStatus', 'splashTitle', 'frame1', 'prayerFrame']) elements[id] = { dataset: {}, contentWindow: { postMessage() {} } };
    const context = {
        SupabaseDB: Object.fromEntries(['getSettings', 'getProgramInfaq', 'getJadwalSholat', 'getSholatJumat'].map(name => [name, () => {
            calls.push(name); return new Promise(resolve => { resolvers[name] = resolve; });
        }])),
        document: { getElementById(id) { return elements[id]; }, title: '' },
        window: { PrayerEngine: { getPrayerState() { return { active: false }; } } },
        localStorage: { getItem() { return null; }, setItem() {} },
        resolveActivePages() { return ['slides/utama.html']; }, updateMasterFooter() {},
        settings: null, jadwalList: [], jumatPetugas: null, activePages: [], currentIndex: 0,
        DEFAULT_PAGES: ['slides/utama.html'], console: { log() {}, error() {} },
        setTimeout(fn, ms) { timers.push({ fn, ms }); }, Promise
    };
    vm.createContext(context);
    vm.runInContext(extractFunctions('web-statis/index.html', ['initDisplay']), context);
    const done = context.initDisplay();
    check(calls.length === 4, 'All independent startup reads start before the first response');
    resolvers.getSettings({ nama_aplikasi: 'Fixture', rotation_interval: 10 });
    resolvers.getJadwalSholat([{ nama_sholat: 'Subuh', waktu: '04:30' }]);
    resolvers.getSholatJumat({ imam: 'Fixture' });
    await flush();
    check(!elements.frame1.src, 'Mode/slide selection still waits for the complete initial snapshot');
    resolvers.getProgramInfaq([]);
    await done;
    check(elements.frame1.src === 'slides/utama.html', 'First slide is selected from the completed startup snapshot');
    check(context.window.jadwalList[0].waktu === '04:30', 'Fresh prayer data remains available to display and prayer mode');
}
(async () => {
    const success = await precache();
    check(success.skipped && !success.failure && !success.deleted.length, 'Complete cache activates normally');
    const optional = await precache(new Set(['image/display/background/BG11.webp']));
    check(optional.skipped && !optional.failure, 'One optional asset failure still allows offline installation');
    const core = await precache(new Set(['slides/utama.html']));
    check(!core.skipped && core.failure && core.deleted.length === 1, 'Core failure rejects installation and removes incomplete new cache');
    await startup();
    console.log(`TV loading: ${checks} checks passed`);
})().catch(error => { console.error(error); process.exitCode = 1; });
