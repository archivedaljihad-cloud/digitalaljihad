'use strict';
const assert = require('assert/strict');
const fs = require('fs');
const os = require('os');
const path = require('path');
const { auditProject } = require('./lib/binding-audit');
const temp = fs.mkdtempSync(path.join(os.tmpdir(), 'signage-bindings-'));
let n = 0;
try {
    fs.mkdirSync(path.join(temp, 'web-statis/slides'), { recursive: true });
    const admin = `<select id="style" onchange="saveStyle()"></select>
        <input class="days" id="mon"><input class="days" id="tue">
        <script>function saveStyle(){DB.saveSettings()}
        document.querySelectorAll('.days').forEach(cb => cb.addEventListener('change', () => saveStyle()));</script>`;
    const slide = `<div id="days">Setiap Senin</div><script>
        function render(){ const target=document.getElementById('days'); target.textContent='Selasa'; }</script>`;
    const contract = { fields: { 'agenda.days': {} },
        forms: [{file:'web-statis/admin.html',selector:'#style',path:'agenda.days',mode:'auto',event:'change',handler:'saveStyle',sink:'DB.saveSettings'},
            {file:'web-statis/admin.html',selector:'.days',path:'agenda.days',mode:'auto',event:'change',handler:'saveStyle',sink:'DB.saveSettings'}],
        bindings: [{file:'web-statis/slides/test.html',selector:'#days',path:'agenda.days',renderer:'render'}] };
    const write = (a = admin, s = slide) => { fs.writeFileSync(path.join(temp, 'web-statis/admin.html'), a); fs.writeFileSync(path.join(temp, 'web-statis/slides/test.html'), s); };
    function expect(mutator, code) { const c = JSON.parse(JSON.stringify(contract)); write(); mutator(c);
        const findings = auditProject(temp, c).findings;
        assert.ok(findings.some(f => f.code === code), code + ' must be detected'); n++; }
    write(); assert.deepEqual(auditProject(temp, contract).findings, []); n++;
    expect(() => write(admin.replace('onchange="saveStyle()"', '')), 'AUTOSAVE_CHAIN_MISSING');
    expect(() => write(admin.replace('DB.saveSettings()', 'console.log("saveSettings")')), 'AUTOSAVE_CHAIN_MISSING');
    expect(() => write(admin.replace("cb.addEventListener('change', () => saveStyle())", "cb.addEventListener('click', () => saveStyle())")), 'AUTOSAVE_CHAIN_MISSING');
    expect(() => write(admin, slide.replace('id="days"', 'id="renamed"')), 'BINDING_TARGET_COUNT');
    expect(() => write(admin, slide.replace("target.textContent='Selasa'", "console.log(target)")), 'BINDING_WRITE_MISSING');
    expect(() => write(admin, slide + '<div id="days"></div>'), 'DUPLICATE_ID');
    expect(() => write(admin + '<input id="newUnwiredField">'), 'UNCLASSIFIED_FORM');
    expect(() => write(admin, slide + '<p>Setiap Kamis 20:30 WIB</p>'), 'STATIC_TEXT_CANDIDATE');
    expect(c => { c.bindings[0].path = 'unknown'; }, 'UNKNOWN_SCHEMA_PATH');
    expect(() => write(admin + '<script src="missing.js"></script>'), 'MISSING_SCRIPT');
    // Comments and string literals must not count as event listeners or render assignments.
    expect(() => write(admin.replace('onchange="saveStyle()"', '') + '<script>// document.getElementById("style").addEventListener("change",saveStyle)</script>'), 'AUTOSAVE_CHAIN_MISSING');
    // Manual save is valid when declared; autosave is not universally required.
    write('<input id="manual"><button id="save" onclick="saveManual()">Save</button><script>function saveManual(){DB.saveSettings()}</script>', slide);
    const manual = { ...contract, forms: [{file:'web-statis/admin.html',selector:'#manual',path:'agenda.days',mode:'manual',saveSelector:'#save',event:'click',handler:'saveManual',sink:'DB.saveSettings'}] };
    assert.equal(auditProject(temp, manual).findings.length, 0); n++;
    write('<input id="manual">', slide);
    assert.ok(auditProject(temp, manual).findings.some(f => f.code === 'MANUAL_SAVE_CHAIN_MISSING')); n++;
    expect(c => { c.bindings[0].file = 'web-statis/typo.html'; }, 'CONTRACT_PAGE_MISSING');
    console.log(`Binding audit: ${n} fixture/mutation checks passed`);
} finally { fs.rmSync(temp, { recursive: true, force: true }); }
