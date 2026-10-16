'use strict';
const fs = require('fs');
const path = require('path');
const { auditProject } = require('./lib/binding-audit');
function run() {
    const args = process.argv.slice(2), root = path.resolve(__dirname, '..');
    const contract = JSON.parse(fs.readFileSync(path.join(root, 'architecture/binding-contract.json'), 'utf8'));
    const result = auditProject(root, contract);
    const baselineFile = path.join(root, 'architecture/binding-debt-baseline.json');
    if (args.includes('--write-baseline')) {
        if (result.findings.some(f => f.strict)) throw new Error('Cannot baseline a broken explicit contract');
        fs.writeFileSync(baselineFile, JSON.stringify({ purpose: 'Known unresolved legacy inventory, NOT proof of correctness', findings: result.findings.map(({ id, code, file, selector }) => ({ id, code, file, selector })) }, null, 2) + '\n');
    }
    const baseline = fs.existsSync(baselineFile) ? JSON.parse(fs.readFileSync(baselineFile, 'utf8')) : { findings: [] };
    const known = new Set(baseline.findings.map(f => f.id));
    const blocking = result.findings.filter(f => f.strict || !known.has(f.id));
    if (args.includes('--json')) console.log(JSON.stringify({ ...result, blocking }, null, 2));
    else {
        const counts = {};
        result.findings.forEach(f => { counts[f.code] = (counts[f.code] || 0) + 1; });
        console.log('Binding audit:', JSON.stringify({ pages: result.pages.length, controls: result.forms.length, knownDebt: result.findings.length - blocking.length, blocking: blocking.length, counts }));
        blocking.slice(0, 30).forEach(f => console.error(`${f.file}:${f.line} ${f.code} ${f.selector}: ${f.detail}`));
        console.log('Static wiring is evidence, not proof of save/render success. Legacy debt remains unresolved; review with --json.');
    }
    if (blocking.length) process.exitCode = 1;
}
if (require.main === module) { try { run(); } catch (err) { console.error(err.message); process.exitCode = 1; } }
module.exports = { run };
