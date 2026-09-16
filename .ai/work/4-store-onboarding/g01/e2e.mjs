// QA-only B08 runner: preserve original suites, repin metadata only, then test sequential token lifecycle.
import fs from 'node:fs/promises';
import { createHash } from 'node:crypto';
import { execFileSync } from 'node:child_process';
const candidate = '525d2a3e2d5143179a53e220bddcb44fd8e016b7';
const dir = process.env.G01_EVIDENCE_DIR;
if (process.env.YMNAY_G01 !== '1' || process.env.APP_ENV !== 'testing' || !dir
    || new URL(process.env.G01_BASE_URL).origin !== 'http://localhost'
    || new URL(process.env.G01_MAILPIT_URL).origin !== 'http://127.0.0.1:8025') throw new Error('Isolated G01 only');
const root = execFileSync('git', ['rev-parse', '--show-toplevel'], { encoding: 'utf8' }).trim();
execFileSync('git', ['diff', '--exit-code', candidate, 'HEAD', '--', 'core/', '.github/workflows/'], { cwd: root });
const provenance = { application_candidate: candidate, qa_head: execFileSync('git', ['rev-parse', 'HEAD'], { encoding: 'utf8' }).trim(), application_and_workflows_unchanged: true, historical_scripts: [] };
const scripts = [
  ['engineer-e2e.mjs', '4a74ffd29786398821067eec33edea997cc6779b', null],
  ['salem-ui-review.mjs', 'b09f0fa87fe1b36b45706c23c9267f54be1444eb', 'ef2cee35d42d6834d3ca8acda019d79628faf68b'],
  ['salem-acceptance.mjs', '1650e236241b574a5d26d060da944a0e5a5795e3', 'e2bdaee3a1f8644451b23dd9d41c1234a1b381e6'],
];
for (const [name, expected, old] of scripts) {
  const path = new URL(name, import.meta.url);
  const bytes = await fs.readFile(path);
  const blob = createHash('sha1').update(`blob ${bytes.length}\0`).update(bytes).digest('hex');
  if (blob !== expected) throw new Error(`Historical script hash mismatch: ${name}`);
  let content = bytes.toString('utf8');
  if (old) {
    if (content.split(old).length !== 2) throw new Error(`Expected exactly one metadata ref: ${name}`);
    content = content.replace(old, candidate);
    await fs.writeFile(path, content);
  }
  provenance.historical_scripts.push({ name, original_blob: blob, only_candidate_metadata_repinned: Boolean(old), assertions_unchanged: true });
}
await fs.mkdir(dir, { recursive: true });
await fs.writeFile(`${dir}/salem-b08-provenance.json`, JSON.stringify(provenance, null, 2) + '\n');
await import('./engineer-e2e.mjs');
await import('./salem-ui-review.mjs');
await import('./salem-acceptance.mjs');
await import('./salem-b08-lifecycle.mjs');
