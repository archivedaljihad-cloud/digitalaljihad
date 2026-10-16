const assert = require('assert/strict');
const fs = require('fs');
const vm = require('vm');
const path = require('path');
const source = fs.readFileSync(path.join(__dirname, '../web-statis/js/supabase-db.js'), 'utf8');
let assertions = 0;
function check(value, label) { assert.ok(value, label); assertions++; }
function harness(child = false) {
    const timers = new Map(), listeners = {}, frames = [], messages = [], acks = [];
    let nextId = 0;
    const localStorage = { getItem() { return null; }, setItem() {}, removeItem() {}, length: 0, key() {} };
    const window = {
        location: { origin: 'https://signage.test', search: '' }, frames: [],
        addEventListener(type, fn) { (listeners[type] ||= []).push(fn); },
        requestAnimationFrame(fn) { frames.push(fn); }
    };
    window.parent = child ? { postMessage(msg) { messages.push(msg); } } : window;
    const sandbox = { window, document: { visibilityState: 'visible' }, localStorage,
        console: { log() {}, warn() {}, error() {} }, URLSearchParams,
        setTimeout(fn, ms) { const id = ++nextId; timers.set(id, { fn, ms }); return id; },
        clearTimeout(id) { timers.delete(id); }, Map, Date, Promise };
    vm.runInNewContext(source, sandbox);
    const db = window.SupabaseDB;
    db.sendTvAcknowledgment = (device, event, table, action) => { acks.push({ device, event, table, action }); };
    db.getTvDeviceId = () => 'TV_TEST';
    db.invalidateCache = () => {};
    db.sendRemoteCommand = async () => {};
    return { db, window, sandbox, messages, acks, frames,
        fireTimers(ms) { for (const [id, timer] of [...timers]) if (timer.ms <= ms) { timers.delete(id); timer.fn(); } },
        message(event) { for (const fn of listeners.message || []) fn(event); } };
}
async function flush() { for (let i = 0; i < 8; i++) await Promise.resolve(); }
(async () => {
    const h = harness(), frame = { src: '/slides/utama', contentWindow: {} };
    let visible = true;
    const start = id => h.db.beginTvRenderConfirmation({ event_id: id, table: 'jadwal_sholat' }, frame, () => visible);
    const msg = id => ({ origin: h.window.location.origin, source: frame.contentWindow,
        data: { type: 'SLIDE_RENDER_CONFIRMED', event_id: id, table: 'jadwal_sholat' } });
    start('timeout');
    check(h.acks.at(-1).action === 'RECEIVED', 'Receipt is distinct from render');
    h.fireTimers(600);
    check(h.acks.length === 1, '600ms never fabricates success');
    h.fireTimers(10000);
    check(h.acks.at(-1).action === 'RENDER_UNCONFIRMED', 'Timeout is unconfirmed');
    check(!h.db.acceptTvRenderConfirmation(msg('timeout')), 'Late timed-out ACK rejected');
    start('render');
    check(!h.db.acceptTvRenderConfirmation({ ...msg('render'), origin: 'https://foreign.test' }), 'Foreign origin rejected');
    check(!h.db.acceptTvRenderConfirmation({ ...msg('render'), source: {} }), 'Standby/foreign frame rejected');
    check(!h.db.acceptTvRenderConfirmation({ ...msg('render'), data: { ...msg('render').data, table: 'keuangan' } }), 'Wrong table rejected');
    visible = false;
    check(!h.db.acceptTvRenderConfirmation(msg('render')), 'Hidden frame rejected');
    visible = true; frame.src = '/slides/keuangan';
    check(!h.db.acceptTvRenderConfirmation(msg('render')), 'Navigated frame rejected');
    frame.src = '/slides/utama';
    check(h.db.acceptTvRenderConfirmation(msg('render')), 'Expected visible frame confirms render');
    check(h.acks.at(-1).action === 'APPLIED', 'Rendered ACK emitted');
    check(!h.db.acceptTvRenderConfirmation(msg('render')), 'Duplicate render rejected');
    start('old'); start('new');
    check(!h.db.acceptTvRenderConfirmation(msg('old')), 'Superseded update cannot confirm newer data');
    h.fireTimers(10000);

    const own = h.db.broadcastChange('jadwal_sholat');
    check(typeof own === 'string', 'Caller receives event ID');
    const ack = { event_id: own, table: 'jadwal_sholat', device_id: 'TV_1', action: 'RECEIVED' };
    check(!h.db.isOwnTvAcknowledgment({ ...ack, event_id: 'other-user' }), 'Other user update ignored');
    check(!h.db.isOwnTvAcknowledgment({ ...ack, action: 'APPLIED_FALLBACK' }), 'Legacy fallback cannot turn indicator green');
    check(!h.db.isOwnTvAcknowledgment({ ...ack, table: 'other' }), 'ACK must match emitted table');
    check(h.db.isOwnTvAcknowledgment(ack), 'Own receipt accepted');
    check(h.db.isOwnTvAcknowledgment({ ...ack, action: 'APPLIED' }), 'Own render accepted');
    check(!h.db.isOwnTvAcknowledgment(ack), 'Late receipt cannot regress rendered status');
    check(h.db.isOwnTvAcknowledgment({ ...ack, device_id: 'TV_2', action: 'APPLIED' }), 'Each TV tracked independently');

    // Exercise the actual onDataChange listener, not a duplicate implementation.
    async function runChild(callback, id) {
        const c = harness(true);
        c.db.onDataChange(callback);
        c.message({ origin: c.window.location.origin, source: c.window.parent,
            data: { type: 'DATA_UPDATED', event_id: id, table: 'jadwal_sholat', data: { id: 1 } } });
        c.fireTimers(80); await flush(); return c;
    }
    let resolveRender;
    const slow = await runChild(() => new Promise(resolve => { resolveRender = resolve; }), 'slow');
    check(slow.messages.length === 0 && slow.frames.length === 0, 'Async renderer must finish before paint');
    resolveRender(true); await flush();
    check(slow.messages.length === 0 && slow.frames.length === 1, 'No ACK before first paint');
    slow.frames.shift()(); await flush();
    check(slow.messages.length === 0 && slow.frames.length === 1, 'No ACK before second paint');
    slow.frames.shift()(); await flush();
    check(slow.messages.length === 1 && slow.messages[0].event_id === 'slow', 'ACK follows awaited renderer and paint');
    for (const [label, cb] of [['legacy', () => undefined], ['unrelated', () => false], ['failed', () => Promise.reject(new Error('offline'))]]) {
        const c = await runChild(cb, label);
        check(c.messages.length === 0 && c.frames.length === 0, label + ' renderer never confirms');
    }
    // Strict cloud reads must not green-light an offline cached/default rendering.
    const offline = harness();
    offline.db.restFetch = async () => { throw new Error('offline'); };
    await assert.rejects(offline.db.getSettings(true, true)); assertions++;
    await assert.rejects(offline.db.getJadwalSholat(true, true)); assertions++;
    check(Array.isArray(await offline.db.getJadwalSholat()), 'Normal offline fallback preserved');
    console.log(`TV render sync: ${assertions} assertions passed`);
})().catch(err => { console.error(err); process.exitCode = 1; });
