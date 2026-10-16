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
            if (id) nodes.set(id, { textContent: 'STALE', innerHTML: 'STALE', style: {} });
        });
        const document = { getElementById(id) { return nodes.get(id) || null; } };
        const settings = { kegiatan_rutin_settings: {} };
        const window = { parent: { settings } };
        const sandbox = { window, document, applySmartHighlights() {},
            localStorage: { getItem() { throw new Error('Unexpected stale fallback'); } },
            console: { warn(...args) { throw new Error('Renderer caught unexpected error: ' + args.join(' ')); } } };
        vm.createContext(sandbox);
        vm.runInContext(extractFunctions(path.join(__dirname, `../web-statis/slides/${page}.html`), ['escapeHtml', 'formatHariTahsin', 'formatPekanTafsir', 'loadAgendaData']), sandbox);
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
    }
    console.log(`Agenda DOM bindings: ${checks} assertions on actual renderer functions passed`);
})().catch(e => { console.error(e); process.exitCode = 1; });
