'use strict';
const fs = require('fs');
const { parse } = require('parse5');
const { analyzeJs, walk } = require('./binding-audit');
function extractFunctions(filename, names) {
    const raw = fs.readFileSync(filename, 'utf8');
    const snippets = filename.endsWith('.html') ? [] : [raw];
    if (filename.endsWith('.html')) walk(parse(raw), n => {
        if (n.tagName === 'script' && !n.attrs.some(a => a.name === 'src')) snippets.push((n.childNodes || []).map(c => c.value || '').join(''));
    });
    const found = new Map();
    for (const code of snippets) {
        const { ast } = analyzeJs(code, filename);
        walk(ast, node => {
            if (node.type === 'FunctionDeclaration' && names.includes(node.id.name)) {
                if (found.has(node.id.name)) throw new Error('Ambiguous function: ' + node.id.name);
                found.set(node.id.name, code.slice(node.start, node.end));
            }
        });
    }
    for (const name of names) if (!found.has(name)) throw new Error('Missing function: ' + name);
    return names.map(n => found.get(n)).join('\n');
}
module.exports = { extractFunctions };
