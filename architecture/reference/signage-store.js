'use strict';
// Reference only: not imported by web-statis and not a replacement for Supabase authorization.
function createSignageStore({ validators, persist = () => {}, onError = () => {} }) {
    const records = new Map(), listeners = new Map();
    const clone = value => JSON.parse(JSON.stringify(value));
    const stable = value => Array.isArray(value) ? '[' + value.map(stable).join(',') + ']'
        : value && typeof value === 'object' ? '{' + Object.keys(value).sort().map(k => JSON.stringify(k) + ':' + stable(value[k])).join(',') + '}'
        : JSON.stringify(value);
    const freeze = value => { if (value && typeof value === 'object') { Object.values(value).forEach(freeze); Object.freeze(value); } return value; };
    function report(error) { try { onError(error); } catch {} }
    function validate(input) {
        if (!input || typeof input !== 'object' || Array.isArray(input) || input.schemaVersion !== 1 ||
            !Object.hasOwn(validators, input.domain) || !Number.isSafeInteger(input.revision) || input.revision < 0 ||
            typeof input.mutationId !== 'string' || !input.mutationId || input.mutationId.length > 160) {
            throw new Error('Invalid snapshot envelope');
        }
        // Validators must REJECT bad payloads, not silently replace user values with defaults.
        const payload = validators[input.domain](clone(input.payload));
        if (!payload || typeof payload !== 'object') throw new Error('Validator must return a normalized object');
        return freeze({ schemaVersion: 1, domain: input.domain, revision: input.revision, mutationId: input.mutationId, payload: clone(payload) });
    }
    function commit(input, source) {
        const snapshot = validate(input), old = records.get(snapshot.domain);
        if (source === 'cache' && old) return { applied: false, reason: 'cache-not-authoritative' };
        if (old && old.source === 'cloud' && snapshot.revision < old.snapshot.revision) return { applied: false, reason: 'stale-revision' };
        if (old && old.source === 'cloud' && snapshot.revision === old.snapshot.revision) {
            if (stable(old.snapshot.payload) !== stable(snapshot.payload) || old.snapshot.mutationId !== snapshot.mutationId) {
                throw new Error('Same revision carries conflicting data; request a fresh server snapshot');
            }
            if (old.source === 'cloud' || source === 'cache') return { applied: false, reason: 'duplicate' };
        }
        const record = freeze({ snapshot, source });
        records.set(snapshot.domain, record);
        if (source === 'cloud') {
            // Storage quota/permissions cannot invalidate an already confirmed server commit.
            try { Promise.resolve(persist(snapshot.domain, clone(snapshot))).catch(report); } catch (error) { report(error); }
        }
        for (const listener of listeners.get(snapshot.domain) || []) {
            try { Promise.resolve(listener(record)).catch(report); } catch (error) { report(error); }
        }
        return { applied: true, record };
    }
    return Object.freeze({
        get(domain) { return records.get(domain) || null; },
        hydrate(snapshot) { return commit(snapshot, 'cache'); },
        applyConfirmed(snapshot) { return commit(snapshot, 'cloud'); },
        subscribe(domain, listener) {
            if (!Object.hasOwn(validators, domain) || typeof listener !== 'function') throw new Error('Invalid subscriber');
            if (!listeners.has(domain)) listeners.set(domain, new Set());
            listeners.get(domain).add(listener);
            return () => listeners.get(domain).delete(listener);
        }
    });
}
module.exports = { createSignageStore };
