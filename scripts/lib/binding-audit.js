'use strict';
const fs = require('fs');
const path = require('path');
const crypto = require('crypto');
const { parse: parseHtml } = require('parse5');
const { parse: parseJs } = require('acorn');

function walk(node, visit) {
    if (!node || typeof node !== 'object') return;
    visit(node);
    for (const [key, value] of Object.entries(node)) {
        if (key === 'parentNode' || key === 'sourceCodeLocation') continue;
        if (Array.isArray(value)) value.forEach(child => walk(child, visit));
        else if (value && typeof value === 'object') walk(value, visit);
    }
}
function attrs(node) { return Object.fromEntries((node.attrs || []).map(a => [a.name, a.value])); }
function matches(node, selector) {
    const a = attrs(node);
    if (selector.startsWith('#')) return a.id === selector.slice(1);
    if (selector.startsWith('.')) return (a.class || '').split(/\s+/).includes(selector.slice(1));
    return node.tagName === selector;
}
function callName(node) {
    if (!node) return '';
    if (node.type === 'Identifier') return node.name;
    if (node.type === 'MemberExpression' && !node.computed) {
        const base = callName(node.object);
        return base ? base + '.' + node.property.name : node.property.name;
    }
    return '';
}
function calls(node) {
    const names = new Set();
    walk(node, n => { if (n.type === 'CallExpression') names.add(callName(n.callee)); });
    return [...names].filter(Boolean);
}
function reaches(names, goal, graph, seen = new Set()) {
    for (const name of names) {
        if (name === goal || name === 'window.' + goal) return true;
        if (seen.has(name)) continue;
        seen.add(name);
        if (reaches(graph.get(name) || [], goal, graph, seen)) return true;
    }
    return false;
}
function analyzeJs(code, source) {
    const ast = parseJs(code, { ecmaVersion: 'latest', sourceType: 'script', locations: true, allowReturnOutsideFunction: true });
    const graph = new Map(), events = [], writes = [];
    function values(expr, env) {
        if (!expr) return [];
        if (expr.type === 'Literal' && typeof expr.value === 'string') return [{ value: expr.value }];
        if (expr.type === 'Identifier') return env.get(expr.name) || [];
        if (expr.type === 'ArrayExpression') return expr.elements.flatMap(e => values(e, env));
        if (expr.type === 'CallExpression') {
            const name = callName(expr.callee);
            if (name.endsWith('.getElementById')) return values(expr.arguments[0], env).map(v => ({ selector: '#' + v.value }));
            if (name.endsWith('.querySelector') || name.endsWith('.querySelectorAll')) return values(expr.arguments[0], env).map(v => ({ selector: v.value }));
        }
        return [];
    }
    function visit(node, env = new Map(), owner = '') {
        if (!node || typeof node !== 'object') return;
        if (node.type === 'Program' || node.type === 'BlockStatement') {
            const scope = new Map(env);
            node.body.forEach(n => visit(n, scope, owner)); return;
        }
        if (node.type === 'VariableDeclaration') {
            node.declarations.forEach(d => {
                if (d.id.type === 'Identifier') {
                    env.set(d.id.name, values(d.init, env));
                    if (d.init && /FunctionExpression/.test(d.init.type)) {
                        graph.set(d.id.name, calls(d.init.body));
                        visit(d.init.body, new Map(env), d.id.name); return;
                    }
                }
                visit(d.init, env, owner);
            }); return;
        }
        if (node.type === 'FunctionDeclaration') {
            graph.set(node.id.name, calls(node.body));
            visit(node.body, new Map(env), node.id.name); return;
        }
        if (node.type === 'CallExpression') {
            const member = node.callee;
            if (member.type === 'MemberExpression' && member.property.name === 'forEach') {
                const callback = node.arguments[0];
                if (callback && /FunctionExpression/.test(callback.type)) {
                    const scope = new Map(env);
                    if (callback.params[0]?.type === 'Identifier') scope.set(callback.params[0].name, values(member.object, env));
                    visit(callback.body, scope, owner); return;
                }
            }
            if (member.type === 'MemberExpression' && member.property.name === 'addEventListener') {
                events.push({ selectors: values(member.object, env).map(v => v.selector).filter(Boolean),
                    event: node.arguments[0]?.value, calls: calls(node.arguments[1]), source, line: node.loc.start.line });
            }
        }
        if (node.type === 'AssignmentExpression' && node.left.type === 'MemberExpression') {
            const member = node.left;
            if (['textContent', 'innerHTML', 'value', 'checked', 'src'].includes(member.property.name)) {
                writes.push({ selectors: values(member.object, env).map(v => v.selector).filter(Boolean), owner, source, line: node.loc.start.line });
            }
        }
        for (const value of Object.values(node)) {
            if (Array.isArray(value)) value.forEach(child => visit(child, new Map(env), owner));
            else if (value && typeof value === 'object' && value.type) visit(value, new Map(env), owner);
        }
    }
    visit(ast);
    return { ast, graph, events, writes };
}

function auditProject(root, contract) {
    const findings = [], pages = [], forms = [], occurrences = new Map();
    function finding(code, file, selector, detail, node, strict = false) {
        const signature = JSON.stringify([code, file, selector, detail]);
        const occurrence = (occurrences.get(signature) || 0) + 1;
        occurrences.set(signature, occurrence);
        const id = crypto.createHash('sha256').update(signature + ':' + occurrence).digest('hex').slice(0, 20);
        findings.push({ id, code, file, selector, detail, line: node?.sourceCodeLocation?.startLine || 1, strict });
    }
    const slideRoot = path.join(root, 'web-statis/slides');
    const files = ['web-statis/admin.html', ...fs.readdirSync(slideRoot).filter(f => f.endsWith('.html')).sort().map(f => 'web-statis/slides/' + f)];
    const cache = new Map();
    for (const file of files) {
        const html = fs.readFileSync(path.join(root, file), 'utf8');
        const doc = parseHtml(html, { sourceCodeLocationInfo: true });
        const elements = [], sources = [];
        walk(doc, n => { if (n.tagName) elements.push(n); });
        for (const node of elements.filter(n => n.tagName === 'script')) {
            const a = attrs(node);
            if (a.type && !['text/javascript', 'application/javascript', 'module'].includes(a.type)) continue;
            if (a.src && /^(https?:)?\/\//.test(a.src)) continue;
            const source = a.src ? path.posix.normalize(path.posix.join(path.posix.dirname(file), a.src.split('?')[0])) : file + ':' + (node.sourceCodeLocation?.startLine || 1);
            if (a.src && !fs.existsSync(path.join(root, source))) {
                finding('MISSING_SCRIPT', file, a.src, 'Local script file does not exist', node); continue;
            }
            if (a.src && source.includes('/vendor/')) continue;
            const code = a.src ? fs.readFileSync(path.join(root, source), 'utf8') : (node.childNodes || []).map(n => n.value || '').join('');
            try {
                if (!cache.has(source)) cache.set(source, analyzeJs(code, source));
                sources.push(cache.get(source));
            } catch (err) { finding('JS_PARSE', file, a.src || 'inline', err.message, node, true); }
        }
        const graph = new Map(), events = sources.flatMap(s => s.events), writes = sources.flatMap(s => s.writes);
        for (const s of sources) for (const [name, edges] of s.graph) graph.set(name, [...(graph.get(name) || []), ...edges]);
        const seenIds = new Set();
        for (const node of elements) {
            const a = attrs(node);
            if (a.id) {
                if (seenIds.has(a.id)) finding('DUPLICATE_ID', file, '#' + a.id, 'ID repeated in this document', node);
                seenIds.add(a.id);
            }
        }
        const fieldContracts = (contract.forms || []).filter(c => c.file === file);
        for (const node of elements) {
            const a = attrs(node);
            const isControl = ['input', 'select', 'textarea'].includes(node.tagName) && !['hidden', 'submit', 'button', 'reset'].includes(a.type);
            const isPill = node.tagName === 'button' && ('data-day' in a || 'aria-pressed' in a || /(?:^|\s)ws-day-pill(?:\s|$)/.test(a.class || ''));
            if (!isControl && !isPill) continue;
            const selector = a.id ? '#' + a.id : a['data-day'] ? '.ws-day-pill[data-day=' + a['data-day'] + ']' : node.tagName + '@' + node.sourceCodeLocation?.startLine;
            const inline = Object.entries(a).filter(([name]) => /^on(?:change|input|click|blur)$/.test(name)).flatMap(([name, code]) => {
                try { return [{ event: name.slice(2), calls: calls(analyzeJs(code, file).ast) }]; }
                catch { return []; }
            });
            const bound = [...inline, ...events.filter(e => e.selectors.some(sel => matches(node, sel)))];
            const declared = fieldContracts.find(c => matches(node, c.selector));
            forms.push({ file, selector, line: node.sourceCodeLocation?.startLine, events: [...new Set(bound.map(e => e.event))], mode: declared?.mode || 'unclassified' });
            if (!declared) finding('UNCLASSIFIED_FORM', file, selector,
                bound.length ? 'Event wiring found; persistence mode/sink not declared' : 'No statically resolved input/change/click handler; manual/delegated save needs review', node);
        }
        for (const c of fieldContracts) {
            const nodes = elements.filter(n => matches(n, c.selector));
            if (!nodes.length) { finding('CONTRACT_TARGET_MISSING', file, c.selector, 'Declared form target missing', null, true); continue; }
            if (!(c.path in contract.fields)) finding('UNKNOWN_SCHEMA_PATH', file, c.selector, c.path, null, true);
            if (!['auto', 'manual', 'local', 'readonly'].includes(c.mode)) finding('INVALID_SAVE_MODE', file, c.selector, String(c.mode), null, true);
            if (c.mode === 'manual') {
                const saves = c.saveSelector ? elements.filter(n => matches(n, c.saveSelector)) : [];
                const bound = saves.flatMap(node => {
                    const a = attrs(node), result = events.filter(e => e.selectors.some(sel => matches(node, sel)));
                    if (a['on' + c.event]) {
                        try { result.push({ event: c.event, calls: calls(analyzeJs(a['on' + c.event], file).ast) }); } catch {}
                    }
                    return result;
                }).filter(e => e.event === c.event).flatMap(e => e.calls);
                if (!saves.length || !c.handler || !c.sink || !reaches(bound, c.handler, graph) || !reaches([c.handler], c.sink, graph)) {
                    finding('MANUAL_SAVE_CHAIN_MISSING', file, c.selector, 'Manual mode requires a real save control and handler/sink chain', null, true);
                }
            }
            if (c.mode === 'auto') for (const node of nodes) {
                const a = attrs(node), bound = events.filter(e => e.selectors.some(sel => matches(node, sel)));
                for (const [name, code] of Object.entries(a)) if (name === 'on' + c.event) {
                    try { bound.push({ event: c.event, calls: calls(analyzeJs(code, file).ast) }); } catch {}
                }
                const eventCalls = bound.filter(e => e.event === c.event).flatMap(e => e.calls);
                if (!reaches(eventCalls, c.handler, graph) || !reaches([c.handler], c.sink, graph)) {
                    finding('AUTOSAVE_CHAIN_MISSING', file, c.selector, 'Expected ' + c.event + ' -> ' + c.handler + ' -> ' + c.sink, node, true);
                }
            }
        }
        const bindings = (contract.bindings || []).filter(c => c.file === file);
        for (const c of bindings) {
            const targets = elements.filter(n => matches(n, c.selector));
            if (targets.length !== 1) finding('BINDING_TARGET_COUNT', file, c.selector, 'Expected one target, found ' + targets.length, null, true);
            if (!(c.path in contract.fields)) finding('UNKNOWN_SCHEMA_PATH', file, c.selector, c.path, null, true);
            if (!writes.some(w => w.owner === c.renderer && w.selectors.includes(c.selector))) {
                finding('BINDING_WRITE_MISSING', file, c.selector, 'No resolved DOM assignment in ' + c.renderer, null, true);
            }
        }
        if (file.includes('/slides/')) for (const node of elements) {
            if (['script', 'style'].includes(node.tagName)) continue;
            const text = (node.childNodes || []).filter(n => n.nodeName === '#text').map(n => n.value).join(' ').trim().replace(/\s+/g, ' ');
            if (!/(?:\bsetiap\b|\bba['’]?da\b|\bust(?:adz|\.)|\b\d{1,2}[:.]\d{2}\s*wib)/i.test(text)) continue;
            let ancestor = node, covered = false;
            while (ancestor) {
                const id = attrs(ancestor).id;
                if (id && writes.some(w => w.selectors.includes('#' + id))) { covered = true; break; }
                ancestor = ancestor.parentNode;
            }
            if (!covered) finding('STATIC_TEXT_CANDIDATE', file, attrs(node).id ? '#' + attrs(node).id : node.tagName,
                text.slice(0, 180), node);
        }
        pages.push({ file, elements: elements.length, parsedScripts: sources.length, resolvedListeners: events.filter(e => e.selectors.length).length });
    }
    const displayPaths = new Set((contract.bindings || []).map(c => c.path));
    const checkedPaths = new Set();
    for (const c of contract.forms || []) {
        if (['auto', 'manual'].includes(c.mode) && !displayPaths.has(c.path) && !checkedPaths.has(c.path)) {
            checkedPaths.add(c.path);
            finding('DISPLAY_CONTRACT_MISSING', c.file, c.selector, 'No declared display consumer for ' + c.path, null);
        }
    }
    for (const c of [...(contract.forms || []), ...(contract.bindings || [])]) {
        if (!files.includes(c.file)) finding('CONTRACT_PAGE_MISSING', c.file, c.selector, 'Declared page is outside the audited page set', null, true);
    }
    return { pages, forms, findings };
}
module.exports = { auditProject, analyzeJs, matches, attrs, walk };
