'use strict';
const assert = require('assert/strict');
const path = require('path');
const vm = require('vm');
const fs = require('fs');
const { parse } = require('parse5');
const { walk, attrs } = require('./lib/binding-audit');
const { extractFunctions } = require('./lib/source-functions');
let checks = 0;
(async () => {
    for (const page of ['agenda-rutin', 'pengumuman']) {
        const nodes = new Map();
        const filename = path.join(__dirname, `../web-statis/slides/${page}.html`);
        walk(parse(fs.readFileSync(filename, 'utf8')), node => {
            const id = attrs(node).id;
            if (id) {
                const classes = new Set((attrs(node).class || '').split(/\s+/));
                nodes.set(id, { textContent: 'STALE', innerHTML: 'STALE', style: {}, classList: { add: c => classes.add(c), remove: c => classes.delete(c), contains: c => classes.has(c) } });
            }
        });
        const document = { getElementById(id) { return nodes.get(id) || null; } };
        const settings = { kegiatan_rutin_settings: {} };
        const window = { parent: { settings } };
        const warnings = [];
        const sandbox = { window, document,
            localStorage: { getItem() { throw new Error('Unexpected stale fallback'); } },
            console: { warn(...args) { warnings.push(args); } } };
        vm.createContext(sandbox);
        vm.runInContext(extractFunctions(path.join(__dirname, `../web-statis/slides/${page}.html`), ['escapeHtml', 'formatHariTahsin', 'formatPekanTafsir', 'applySmartHighlights', 'loadAgendaData']), sandbox);
        for (const [days, weeks] of [[['selasa', 'kamis'], [2, 4]], [['ahad'], [5]]]) {
            settings.kegiatan_rutin_settings = {
                tahsin: { judul: 'TAHSIN BARU', pembimbing: 'PEMBIMBING BARU', keterangan: 'KETERANGAN BARU', lokasi: 'LOKASI BARU', hari_aktif: days, waktu: '21:17 WIB' },
                tafsir: { judul: 'TAFSIR BARU', pembimbing: 'MENTOR TAFSIR', keterangan: 'CATATAN TAFSIR', lokasi: 'TEMPAT TAFSIR', pekan_aktif: weeks, waktu: '06:23 WIB' }
            };
            await sandbox.loadAgendaData();
            for (const [id, expected] of Object.entries({ titleTahsin: 'TAHSIN BARU', mentorTahsin: 'PEMBIMBING BARU', descTahsin: 'KETERANGAN BARU', lokasiTahsin: 'LOKASI BARU', titleTafsir: 'TAFSIR BARU', mentorTafsir: 'MENTOR TAFSIR', descTafsir: 'CATATAN TAFSIR', lokasiTafsir: 'TEMPAT TAFSIR' })) {
                assert.equal(nodes.get(id)?.textContent, expected, page + ':' + id); checks++;
            }
            for (const day of days) { assert.match(nodes.get('waktuTahsin').innerHTML.toLowerCase(), new RegExp(day)); checks++; }
            for (const week of weeks) { assert.match(nodes.get('waktuTafsir').innerHTML, new RegExp(String(week))); checks++; }
            assert.match(nodes.get('waktuTahsin').innerHTML, /21:17 WIB/); checks++;
            assert.match(nodes.get('waktuTafsir').innerHTML, /06:23 WIB/); checks++;
        }
        settings.kegiatan_rutin_settings.tahsin.hari_aktif = [];
        settings.kegiatan_rutin_settings.tafsir.pekan_aktif = [];
        await sandbox.loadAgendaData();
        assert.match(nodes.get('waktuTahsin').innerHTML, /Belum ada hari dipilih/); checks++;
        assert.match(nodes.get('waktuTafsir').innerHTML, /Belum ada pekan dipilih/); checks++;
        for (const day of [1, 0]) {
            sandbox.Date = class extends Date { getDay() { return day; } getDate() { return 8; } };
            sandbox.applySmartHighlights();
            assert.equal(nodes.get('badgeStatusTahsin').style.display, 'none'); checks++;
            assert.equal(nodes.get('badgeStatusTafsir').style.display, 'none'); checks++;
            assert.equal(nodes.get('cardTahsin').classList.contains('today-active'), false); checks++;
            assert.equal(nodes.get('cardTafsir').classList.contains('today-active'), false); checks++;
        }
        assert.equal(warnings.length, 0, 'Renderer must not swallow unexpected errors'); checks++;
        const resolvers = [];
        const client = { getSettings: () => new Promise(resolve => resolvers.push(resolve)) };
        window.SupabaseDB = sandbox.SupabaseDB = client;
        const slow = sandbox.loadAgendaData(true), fast = sandbox.loadAgendaData(true);
        const fresh = { kegiatan_rutin_settings: { tahsin: { hari_aktif: ['ahad'] }, tafsir: { pekan_aktif: [5] } } };
        resolvers[1](fresh); assert.equal(await fast, true); checks++;
        resolvers[0]({ kegiatan_rutin_settings: { tahsin: { hari_aktif: ['senin'] }, tafsir: { pekan_aktif: [1] } } });
        assert.equal(await slow, false); checks++;
        assert.match(nodes.get('waktuTahsin').innerHTML, /Ahad/); checks++;
        client.getSettings = async () => ({});
        assert.equal(await sandbox.loadAgendaData(true), false); checks++;
        assert.match(nodes.get('waktuTahsin').innerHTML, /Ahad/); checks++;
        client.getSettings = async () => { throw new Error('offline'); };
        assert.equal(await sandbox.loadAgendaData(true), false); checks++;
        assert.match(nodes.get('waktuTahsin').innerHTML, /Ahad/); checks++;
    }
    console.log(`Agenda DOM bindings: ${checks} assertions on actual renderer functions passed`);
})().catch(e => { console.error(e); process.exitCode = 1; });
