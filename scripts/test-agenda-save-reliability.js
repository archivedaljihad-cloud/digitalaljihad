'use strict';
const assert = require('assert/strict');
const fs = require('fs');
const path = require('path');
const vm = require('vm');
const { extractFunctions } = require('./lib/source-functions');
const root = path.resolve(__dirname, '..');
let checks = 0;
const ok = (condition, label) => { assert.ok(condition, label); checks++; };
const flush = async () => { for (let i = 0; i < 12; i++) await Promise.resolve(); };
function harness() {
    const values = new Map(), broadcasts = [], alerts = [], nodes = new Map();
    const localStorage = { getItem: key => values.get(key) ?? null, setItem: (key, value) => values.set(key, String(value)), removeItem: key => values.delete(key), get length() { return values.size; }, key: i => [...values.keys()][i] };
    const window = { location: { origin: 'https://test.invalid', search: '' }, addEventListener() {} };
    window.parent = window;
    const sandbox = { window, localStorage, console: { log() {}, warn() {}, error() {} }, setTimeout, clearTimeout, URLSearchParams,
        alert: message => alerts.push(message), renderDashAgendaRutinWidget() {}, syncIntervalTV() {}, syncDurasiJumat() {},
        document: { getElementById: id => nodes.get(id) || null, querySelectorAll: () => [] } };
    vm.createContext(sandbox);
    vm.runInContext(fs.readFileSync(path.join(root, 'web-statis/js/supabase-db.js'), 'utf8'), sandbox);
    const db = window.SupabaseDB;
    sandbox.SupabaseDB = db;
    db.broadcastChange = (...args) => broadcasts.push(args);
    function load(file, names) { vm.runInContext(extractFunctions(path.join(root, file), names), sandbox); }
    function node(id, value = '') { const n = { value, checked: true, disabled: false, style: {}, textContent: '', innerHTML: '' }; nodes.set(id, n); return n; }
    return { db, sandbox, values, broadcasts, alerts, load, node, localStorage };
}
(async () => {
    for (const method of ['saveAgendaRutin', 'saveKajianData', 'savePengumumanJumat']) {
        const h = harness();
        h.values.set('cached_app_settings', JSON.stringify({ marker: 'old' }));
        h.db.saveSettingItem = async () => false;
        ok(await h.db[method]({ test: 1 }) === false, method + ' propagates false');
        ok(h.broadcasts.length === 0, method + ' does not broadcast failure');
        ok(JSON.parse(h.values.get('cached_app_settings')).marker === 'old', method + ' preserves confirmed cache');
        h.db.saveSettingItem = async () => { throw new Error('offline'); };
        ok(await h.db[method]({ test: 2 }) === false, method + ' handles rejection');
        h.db.saveSettingItem = async () => true;
        ok(await h.db[method]({ test: 3 }) === true, method + ' confirms success');
        ok(h.broadcasts.length === 1, method + ' broadcasts once after confirmation');
    }
    const partial = harness();
    partial.db.saveSettingItem = async key => key !== 'kajian_sabtu_enabled';
    ok(await partial.db.saveKajianData({}) === false, 'Partial multi-key save fails');
    ok(partial.broadcasts.length === 0, 'Partial save cannot emit successful aggregate');
    const quota = harness(); quota.db.saveSettingItem = async () => true;
    quota.localStorage.setItem = () => { throw new Error('quota'); };
    ok(await quota.db.saveAgendaRutin({}) === true, 'Storage quota does not undo confirmed cloud save');
    const transport = harness(); transport.db.saveSettingItem = async () => true; transport.db.broadcastChange = () => { throw new Error('disconnected'); };
    ok(await transport.db.saveAgendaRutin({}) === true, 'Broadcast failure is separate from cloud commit');
    for (const alias of ['agenda_rutin', 'kegiatan_rutin_settings', 'kajian', 'pengumuman_jumat']) {
        const h = harness(); h.db._cache.settings = {}; h.db._cache.keuangan = [1]; h.db._cacheTimestamps.settings = Date.now();
        h.db.invalidateCache(alias);
        ok(!h.db._cache.settings && !h.db._cacheTimestamps.settings, alias + ' invalidates settings');
        ok(h.db._cache.keuangan.length === 1, alias + ' leaves unrelated finance cache');
    }
    const serial = harness(), releases = [], sent = [];
    serial.db.saveSettingItem = (key, payload) => new Promise(resolve => { sent.push(payload); releases.push(resolve); });
    const firstPayload = { tahsin: { hari_aktif: ['senin'] } };
    const first = serial.db.saveAgendaRutin(firstPayload);
    firstPayload.tahsin.hari_aktif.push('INVALID');
    const second = serial.db.saveAgendaRutin({ tahsin: { hari_aktif: [] } });
    await flush();
    ok(sent.length === 1 && sent[0].tahsin.hari_aktif.length === 1, 'Queue captures immutable first payload');
    releases.shift()(true); await first; await flush();
    ok(sent.length === 2, 'Second save starts after first completes');
    releases.shift()(true); await second;
    ok(JSON.parse(serial.values.get('agenda_rutin_settings')).tahsin.hari_aktif.length === 0, 'Newest empty selection wins confirmed cache');
    const lookup = harness(); let mutations = 0;
    lookup.db.safeRestMutation = async () => { mutations++; return { success: false, cloudConfirmed: false }; };
    ok(await lookup.db.saveSettingItem('test', 'value') === false && mutations === 1, 'Failed lookup never falls through to INSERT');
    const emptyPost = harness(); const methods = [];
    emptyPost.sandbox.fetch = async (url, options) => { methods.push(options.method); return { ok: true, status: options.method === 'GET' ? 200 : 201, json: async () => [] }; };
    ok(await emptyPost.db.saveSettingItem('test', 'value') === false, 'Empty INSERT representation is not confirmation');
    ok(methods.join(',') === 'GET,POST', 'Empty lookup allows a real INSERT attempt');

    const admin = harness(); const status = admin.node('wsTahsinSyncStatus');
    admin.values.set('agenda_rutin_settings', 'OLD');
    admin.db.saveSettingItem = async () => false;
    admin.load('web-statis/js/admin/admin-agenda-rutin.js', ['simpanAgendaRutinAdmin']);
    ok(await admin.sandbox.simpanAgendaRutinAdmin(true) === false, 'Admin rejects failed cloud write');
    ok(admin.values.get('agenda_rutin_settings') === 'OLD' && admin.values.has('draft_agenda_rutin_settings'), 'Admin separates draft and confirmed');
    ok(status.textContent.includes('Belum tersimpan') && !status.textContent.includes('Tersinkron'), 'Admin failure is visible');
    admin.db.saveSettingItem = async () => true;
    ok(await admin.sandbox.simpanAgendaRutinAdmin(true) === true, 'Admin accepts cloud success');
    const agenda = JSON.parse(admin.values.get('agenda_rutin_settings'));
    ok(agenda.tahsin.hari_aktif.length === 0 && agenda.tafsir.pekan_aktif.length === 0, 'Empty day and week selections persist');
    ok(!admin.values.has('draft_agenda_rutin_settings'), 'Successful draft is cleared');
    ok(status.textContent.includes('belum dikonfirmasi'), 'Cloud success does not claim TV render');
    const noApi = harness(); noApi.node('wsTahsinSyncStatus'); noApi.sandbox.SupabaseDB = noApi.sandbox.window.SupabaseDB = undefined;
    noApi.load('web-statis/js/admin/admin-agenda-rutin.js', ['simpanAgendaRutinAdmin']);
    ok(await noApi.sandbox.simpanAgendaRutinAdmin(true) === false, 'Missing cloud SDK cannot report success');
    const fallback = harness(); fallback.node('wsTahsinSyncStatus');
    fallback.sandbox.SupabaseDB = { saveSettings: async () => ({ success: false, cloudConfirmed: false }) };
    fallback.load('web-statis/js/admin/admin-agenda-rutin.js', ['simpanAgendaRutinAdmin']);
    ok(await fallback.sandbox.simpanAgendaRutinAdmin(true) === false, 'Truthy error object is not success');

    for (const scenario of ['style-failed', 'zero-rows', 'success']) {
        const h = harness(); const badge = h.node('statusAutoSyncTV'); const btn = h.node('btnSimpanDurasiTV');
        h.node('cfgIntervalTV', '17'); h.node('cfgCountdownStyle', 'biasa');
        h.values.set('cached_app_settings', '{"marker":"OLD"}');
        h.db.saveSettingItem = async () => scenario !== 'style-failed';
        h.db.safeRestMutation = async () => ({ success: true, cloudConfirmed: true, rowsAffected: scenario === 'zero-rows' ? 0 : 1 });
        h.load('web-statis/js/admin/admin-jadwal-sholat.js', ['simpanPengaturanDurasiTV']);
        const saved = await h.sandbox.simpanPengaturanDurasiTV(true);
        ok(saved === (scenario === 'success'), 'Duration result: ' + scenario);
        ok(!btn.disabled, 'Save button restored: ' + scenario);
        if (!saved) {
            ok(h.broadcasts.length === 0 && h.values.has('draft_durasi_tv'), 'Duration failure stays draft: ' + scenario);
            ok(JSON.parse(h.values.get('cached_app_settings')).marker === 'OLD', 'Duration failure preserves cache: ' + scenario);
        } else {
            ok(h.broadcasts.length === 1 && !h.values.has('draft_durasi_tv'), 'Duration broadcasts only confirmed data');
            ok(badge.textContent.includes('belum dikonfirmasi'), 'Duration badge distinguishes cloud from TV');
        }
    }
    const friday = harness();
    friday.sandbox.pjData = { title: 'test' };
    friday.db.saveSettingItem = async () => false;
    friday.load('web-statis/js/admin/admin-pengumuman-jumat.js', ['simpanDataPengumumanJumat']);
    ok(await friday.sandbox.simpanDataPengumumanJumat(false) === false, 'Friday admin rejects cloud failure');
    ok(friday.values.has('draft_pengumuman_jumat_data') && !friday.values.has('pengumuman_jumat_data'), 'Friday preserves draft without overwriting confirmed');
    const editedFriday = harness(); editedFriday.sandbox.pjData = {title:'SAVED VERSION'};
    let finishFriday;
    editedFriday.db.saveSettingItem = () => new Promise(resolve => { finishFriday = resolve; });
    editedFriday.load('web-statis/js/admin/admin-pengumuman-jumat.js', ['simpanDataPengumumanJumat']);
    const savingFriday = editedFriday.sandbox.simpanDataPengumumanJumat(false); await flush();
    editedFriday.sandbox.pjData.title = 'NEW UNSAVED VERSION';
    finishFriday(true); ok(await savingFriday === true, 'Friday cloud snapshot completes');
    ok(JSON.parse(editedFriday.values.get('pengumuman_jumat_data')).title === 'SAVED VERSION', 'Friday confirmed cache uses exact submitted snapshot');
    ok(JSON.parse(editedFriday.values.get('draft_pengumuman_jumat_data')).title === 'NEW UNSAVED VERSION', 'Edits during Friday save remain a separate draft');
    const recovered = harness();
    recovered.node('wsTahsinSyncStatus'); const title = recovered.node('wsTahsinJudul');
    recovered.values.set('agenda_rutin_settings', JSON.stringify({ tahsin: {judul:'CONFIRMED',hari_aktif:['senin']} }));
    recovered.values.set('draft_agenda_rutin_settings', JSON.stringify({ tahsin: {judul:'RECOVERED DRAFT',hari_aktif:[]}, tafsir:{pekan_aktif:[]} }));
    let dashboard;
    recovered.sandbox.renderDashAgendaRutinWidget = value => { dashboard = value; };
    recovered.load('web-statis/js/admin/admin-agenda-rutin.js', ['getDefaultAgendaRutinWs', 'getAgendaRutinDataWs', 'loadAgendaRutinAdmin']);
    recovered.sandbox.loadAgendaRutinAdmin();
    ok(title.value === 'RECOVERED DRAFT', 'Failed agenda draft restores into form');
    ok(dashboard.tahsin.judul === 'CONFIRMED', 'Draft recovery never marks dashboard as confirmed');
    ok(recovered.sandbox.document.getElementById('wsTahsinSyncStatus').textContent.includes('belum tersimpan'), 'Recovered draft is labelled unconfirmed');
    const schedule = harness(); schedule.load('web-statis/js/admin/admin-jadwal-sholat.js', ['simpanJadwalSholat']);
    schedule.sandbox.simpanPengaturanDurasiTV = async () => true;
    schedule.node('sholatSubuh', '04:30');
    schedule.db.safeRestMutation = async () => ({success:true,cloudConfirmed:true,rowsAffected:0});
    ok(await schedule.sandbox.simpanJadwalSholat() === false, 'Prayer-time form rejects zero affected rows');
    ok(schedule.broadcasts.length === 0 && schedule.values.has('draft_jadwal_sholat'), 'Prayer-time failure preserves draft and sends no schedule broadcast');
    console.log(`Agenda/save reliability: ${checks} assertions passed`);
})().catch(error => { console.error(error); process.exitCode = 1; });
