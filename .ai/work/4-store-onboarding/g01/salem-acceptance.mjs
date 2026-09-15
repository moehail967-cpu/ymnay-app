import { chromium } from 'playwright';
import fs from 'node:fs/promises';
const base = process.env.G01_BASE_URL;
const mail = process.env.G01_MAILPIT_URL;
const dir = process.env.G01_EVIDENCE_DIR;
if (process.env.YMNAY_G01 !== '1' || process.env.APP_ENV !== 'testing' || !dir
    || new URL(base).hostname !== 'localhost' || new URL(mail).hostname !== '127.0.0.1') throw new Error('Isolated G01 only');
const report = { candidate: 'e2bdaee3a1f8644451b23dd9d41c1234a1b381e6', checks: [], errors: [] };
const record = (name, pass, detail = {}) => report.checks.push({ name, result: pass ? 'PASS' : 'FAIL', detail });
const browser = await chromium.launch({ headless: true });
const mailList = async () => (await fetch(`${mail}/api/v1/messages`).then(r => r.json())).messages || [];
const mailId = m => m.ID || m.Id || m.id;
async function resetMail(recipient, before) {
  for (let n = 0; n < 40; n++) {
    const messages = await mailList();
    for (const m of messages.filter(m => !before.has(mailId(m)) && JSON.stringify(m.To || m.to || '').includes(recipient))) {
      const body = await fetch(`${mail}/api/v1/message/${mailId(m)}`).then(r => r.json());
      const match = (body.HTML || '').match(/href=["']([^"']*\/login\/reset-password\/[^"']+)["']/i);
      if (match) {
        const target = new URL(match[1].replaceAll('&amp;', '&'), base);
        if (target.origin !== new URL(base).origin) throw new Error('Non-isolated reset URL');
        return target.href;
      }
    }
    await new Promise(r => setTimeout(r, 250));
  }
  throw new Error('Reset mail not captured');
}
async function login(page, email, password) {
  await page.goto(`${base}/login`, { waitUntil: 'domcontentloaded' });
  return page.evaluate(async ({ email, password }) => {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    const r = await fetch('/store-login', { method: 'POST', headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': csrf }, body: JSON.stringify({ username: email, password }) });
    const b = await r.json();
    return { http: r.status, status: b.status, redirect: b.redirect_url ? new URL(b.redirect_url).pathname + new URL(b.redirect_url).search : null };
  }, { email, password });
}
async function requestReset(page, email) {
  await page.goto(`${base}/login/forget-password`, { waitUntil: 'domcontentloaded' });
  const before = new Set((await mailList()).map(mailId));
  await page.locator('input[name="username"]').fill(email);
  await Promise.all([page.waitForNavigation({ waitUntil: 'domcontentloaded' }), page.locator('form button[type="submit"]').click()]);
  return resetMail(email, before);
}
async function useReset(page, url, password) {
  await page.goto(url, { waitUntil: 'domcontentloaded' });
  await page.locator('input[name="password"]').fill(password);
  await page.locator('input[name="password_confirmation"]').fill(password);
  await Promise.all([page.waitForNavigation({ waitUntil: 'domcontentloaded' }), page.locator('form button[type="submit"]').click()]);
  return new URL(page.url()).pathname === '/login';
}
try {
  for (const [name, viewport] of [['desktop', { width: 1440, height: 900 }], ['mobile', { width: 390, height: 844 }]]) {
    const c = await browser.newContext({ viewport, locale: 'ar-SA', isMobile: name === 'mobile' });
    const p = await c.newPage(); p.setDefaultTimeout(30000);
    try {
      await p.goto(`${base}/create-store`, { waitUntil: 'networkidle' });
      const cards = await p.locator('form[action$="/create-store/plan"] .ym-option').evaluateAll(nodes => nodes.map(node => ({
        text: node.innerText,
        limits: Object.fromEntries([...node.querySelectorAll('[data-plan-limit]')].map(li => [li.dataset.planLimit, li.querySelector('.ym-plan-limit-value')?.textContent.trim()]))
      })));
      const expected = [ ['100','10','10','512 MB'], ['350','25','40','2048 MB'], ['غير محدود','غير محدود','غير محدود','5120 MB'] ];
      record(`${name}: B07 exact limits per representative plan`, cards.length === 3 && cards.every((card, i) =>
        ['المنتجات','الصفحات','المدونة','التخزين'].every((k, j) => card.limits[k] === expected[i][j])), { cards });
      record(`${name}: SAR and dynamic trial on every plan`, cards.length === 3 && cards.every((card, i) => card.text.includes('ر.س') && !card.text.includes('$') && card.text.includes(String([37,45,60][i]))));
      const dimensions = await p.evaluate(() => ({ viewport: innerWidth, document: document.documentElement.scrollWidth }));
      record(`${name}: no horizontal page overflow`, dimensions.document <= dimensions.viewport + 1, dimensions);
      await p.screenshot({ path: `${dir}/salem-${name}-limits.png`, fullPage: true });
      await p.locator('input[name="plan_id"]').first().check();
      await Promise.all([p.waitForURL(/step=2/), p.locator('form[action$="/create-store/plan"] button[type="submit"]').click()]);
      // Count only selectable radios in the actual theme form, not unrelated hidden modal inputs.
      const choices = await p.locator('form[action$="/create-store/theme"] input[type="radio"][name="theme_slug"]').evaluateAll(nodes => nodes.map(n => n.value).sort());
      const allNamedInputs = await p.locator('input[name="theme_slug"]').evaluateAll(nodes => nodes.map(n => ({ type: n.type, form_action: n.form ? new URL(n.form.action).pathname : null })));
      record(`${name}: three actual theme choices`, JSON.stringify(choices) === JSON.stringify(['aromatic','bakerco','hexfashion']), { choices, all_named_inputs: allNamedInputs });
      await p.locator('input[name="theme_slug"][value="hexfashion"]').check();
      await Promise.all([p.waitForURL(/step=3/), p.locator('form[action$="/create-store/theme"] button[type="submit"]').click()]);
      await Promise.all([p.waitForURL(/step=1/), p.locator('.ym-progress a[href*="step=1"]').click()]);
      record(`${name}: B06 completed progress link preserves selected plan`, await p.locator('input[name="plan_id"]').first().isChecked());
    } catch (e) { report.errors.push({ stage: name, error: String(e.message).replace(/token-login\/[^\s]+/g, 'token-login/[redacted]') }); }
    finally { await c.close(); }
  }
  // Exercise the real email/reset/login routes. Only the synthetic existing G01 account is used.
  const c = await browser.newContext({ viewport: { width: 1440, height: 900 }, locale: 'ar-SA' });
  const p = await c.newPage(); p.setDefaultTimeout(30000);
  const email = 'g01-existing-unverified@example.test';
  const original = 'G01-Isolated-Password!';
  try {
    const initial = await login(p, email, original);
    if (initial.status !== 'valid') throw new Error('Synthetic existing account login failed');
    await p.goto(`${base}/create-store?step=4`, { waitUntil: 'networkidle' });
    const before = await p.locator('.ym-summary').first().innerText();
    const statusBefore = await c.request.get(`${base}/create-store/status`).then(r => r.json());
    await p.goto(`${base}/logout`, { waitUntil: 'domcontentloaded' });
    const firstLink = await requestReset(p, email);
    const firstPassword = 'Salem-G01-Recovered-A!';
    const firstRedirect = await useReset(p, firstLink, firstPassword);
    const firstLogin = await login(p, email, firstPassword);
    record('actual first password reset and new-password login', firstRedirect && firstLogin.status === 'valid', { redirect_to_login: firstRedirect, login: firstLogin });
    if (firstLogin.status === 'valid') {
      await p.goto(`${base}/create-store?step=4`, { waitUntil: 'networkidle' });
      const after = await p.locator('.ym-summary').first().innerText();
      const statusAfter = await c.request.get(`${base}/create-store/status`).then(r => r.json());
      record('actual recovery returns to the same owned draft and selections', before === after && statusBefore.reference === statusAfter.reference, { same_reference: statusBefore.reference === statusAfter.reference, same_summary: before === after, state: statusAfter.status });
      await p.screenshot({ path: `${dir}/salem-after-password-recovery.png`, fullPage: true });
      await p.goto(`${base}/logout`, { waitUntil: 'domcontentloaded' });
    }
    const secondLink = await requestReset(p, email);
    const secondPassword = 'Salem-G01-Recovered-B!';
    const secondRedirect = await useReset(p, secondLink, secondPassword);
    if (!secondRedirect) await p.screenshot({ path: `${dir}/salem-second-password-reset-rejected.png`, fullPage: true });
    const secondLogin = await login(p, email, secondPassword);
    record('a second requested reset link is usable and changes the password', secondRedirect && secondLogin.status === 'valid', { reset_mail_captured: true, redirect_to_login: secondRedirect, login: secondLogin });
    // Record whether a failed latest reset left the prior password intact, without exposing passwords or reset tokens.
    if (secondLogin.status !== 'valid') {
      const prior = await login(p, email, firstPassword);
      record('failed second reset did not corrupt the prior password', prior.status === 'valid', { login: prior });
      if (prior.status === 'valid') await p.goto(`${base}/create-store?step=4`, { waitUntil: 'networkidle' });
    }
  } catch (e) { report.errors.push({ stage: 'actual-password-recovery', error: String(e.message).replace(/\/login\/reset-password\/[^\s]+/g, '/login/reset-password/[redacted]') }); }
  finally { await c.close(); }
} finally {
  await browser.close();
  report.totals = { passed: report.checks.filter(c => c.result === 'PASS').length, failed: report.checks.filter(c => c.result === 'FAIL').length, errors: report.errors.length };
  await fs.writeFile(`${dir}/salem-acceptance-report.json`, JSON.stringify(report, null, 2) + '\n');
  console.log(JSON.stringify(report.totals));
}
if (report.totals.failed || report.totals.errors) process.exitCode = 1;
