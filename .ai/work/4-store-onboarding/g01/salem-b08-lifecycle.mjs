// Independent sequential central-reset checks. Only existing disposable G01 fixtures are used.
import { chromium } from 'playwright';
import fs from 'node:fs/promises';
const base = process.env.G01_BASE_URL;
const mail = process.env.G01_MAILPIT_URL;
const dir = process.env.G01_EVIDENCE_DIR;
if (process.env.APP_ENV !== 'testing' || process.env.YMNAY_G01 !== '1' || !dir
    || new URL(base).origin !== 'http://localhost' || new URL(mail).origin !== 'http://127.0.0.1:8025') throw new Error('Isolated G01 only');
const report = { candidate: '525d2a3e2d5143179a53e220bddcb44fd8e016b7', method: 'Real HTTP/Chromium/Mailpit, synthetic central account; sequential lifecycle only', checks: [], errors: [] };
const record = (name, pass, detail = {}) => report.checks.push({ name, result: pass ? 'PASS' : 'FAIL', detail });
const email = 'g01-password-recovery@example.test';
// Public, synthetic G01 fixture values. Never used against production or a real account.
const initialPassword = 'G01-Recovered-Password-2!';
const passwordA = 'Salem-B08-Fixture-A!';
const passwordB = 'Salem-B08-Fixture-B!';
const rejectedPassword = 'Salem-B08-Rejected-Fixture!';
const browser = await chromium.launch({ headless: true });
const context = await browser.newContext({ viewport: { width: 1440, height: 900 }, locale: 'ar-SA' });
const page = await context.newPage();
page.setDefaultTimeout(30000);
let stage = 'initial';
const messages = async () => (await fetch(`${mail}/api/v1/messages`).then(r => r.json())).messages || [];
const id = m => m.ID || m.Id || m.id;
async function login(password) {
  await page.goto(`${base}/login`, { waitUntil: 'domcontentloaded' });
  return page.evaluate(async ({ email, password }) => {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    const response = await fetch('/store-login', { method: 'POST', headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': csrf }, body: JSON.stringify({ username: email, password }) });
    const data = await response.json();
    return { http: response.status, status: data.status, redirect: data.redirect_url ? new URL(data.redirect_url).pathname + new URL(data.redirect_url).search : null };
  }, { email, password });
}
async function logout() { await page.goto(`${base}/logout`, { waitUntil: 'domcontentloaded' }); }
async function requestReset() {
  await page.goto(`${base}/login/forget-password`, { waitUntil: 'domcontentloaded' });
  const before = new Set((await messages()).map(id));
  await page.locator('input[name="username"]').fill(email);
  await Promise.all([page.waitForNavigation({ waitUntil: 'domcontentloaded' }), page.locator('form button[type="submit"]').click()]);
  for (let attempt = 0; attempt < 40; attempt++) {
    for (const msg of (await messages()).filter(m => !before.has(id(m)) && JSON.stringify(m.To || m.to || '').includes(email))) {
      const body = await fetch(`${mail}/api/v1/message/${id(msg)}`).then(r => r.json());
      const match = (body.HTML || '').match(/href=["']([^"']*\/login\/reset-password\/[^"']+)["']/i);
      if (match) {
        const url = new URL(match[1].replaceAll('&amp;', '&'), base);
        if (url.origin !== new URL(base).origin) throw new Error('Non-isolated reset URL');
        return url.href;
      }
    }
    await new Promise(r => setTimeout(r, 250));
  }
  throw new Error('Fixture reset mail not captured');
}
async function useReset(url, password) {
  await page.goto(url, { waitUntil: 'domcontentloaded' });
  await page.locator('input[name="password"]').fill(password);
  await page.locator('input[name="password_confirmation"]').fill(password);
  await Promise.all([page.waitForNavigation({ waitUntil: 'domcontentloaded' }), page.locator('form button[type="submit"]').click()]);
  return new URL(page.url()).pathname === '/login';
}
async function snapshot() {
  await page.goto(`${base}/create-store?step=5`, { waitUntil: 'networkidle' });
  const status = await context.request.get(`${base}/create-store/status`).then(r => r.json());
  return { reference: status.reference, status: status.status, summary: await page.locator('.ym-summary').first().innerText() };
}
try {
  if ((await login(initialPassword)).status !== 'valid') throw new Error('Prepared fixture login failed');
  const before = await snapshot();
  await logout();
  stage = 'replacement-before-use';
  const first = await requestReset();
  const second = await requestReset();
  record('repeated issuance produces a distinct latest link', first !== second);
  const staleRedirect = await useReset(first, rejectedPassword);
  const staleLogin = await login(rejectedPassword);
  const unchangedLogin = await login(initialPassword);
  record('superseded link rejected and existing password unchanged', !staleRedirect && staleLogin.status === 'invalid' && unchangedLogin.status === 'valid', { stale_redirect: staleRedirect, rejected_login: staleLogin.status, previous_login: unchangedLogin.status });
  await logout();
  stage = 'replacement-consumption';
  const replacementRedirect = await useReset(second, passwordA);
  const replacementLogin = await login(passwordA);
  record('latest replacement link changes password and authenticates', replacementRedirect && replacementLogin.status === 'valid', { redirect_to_login: replacementRedirect, login: replacementLogin.status });
  if (replacementLogin.status !== 'valid') throw new Error('Replacement login failed');
  await logout();
  stage = 'used-link-replay';
  const replayRedirect = await useReset(second, rejectedPassword);
  const replayLogin = await login(rejectedPassword);
  const intactLogin = await login(passwordA);
  record('consumed link cannot be reused or overwrite password', !replayRedirect && replayLogin.status === 'invalid' && intactLogin.status === 'valid', { replay_redirect: replayRedirect, rejected_login: replayLogin.status, current_login: intactLogin.status });
  await logout();
  stage = 'repeat-after-completed-reset';
  const third = await requestReset();
  const thirdRedirect = await useReset(third, passwordB);
  const thirdLogin = await login(passwordB);
  record('new request after completed recovery works', third !== first && third !== second && thirdRedirect && thirdLogin.status === 'valid', { redirected_to_login: thirdRedirect, login: thirdLogin.status });
  if (thirdLogin.status !== 'valid') throw new Error('Final fixture login failed');
  stage = 'owned-request-preservation';
  const after = await snapshot();
  record('final login resumes step five with same owned request and choices', thirdLogin.redirect === '/create-store?step=5' && before.reference === after.reference && before.summary === after.summary && before.status === after.status, { redirect: thirdLogin.redirect, same_reference: before.reference === after.reference, same_choices: before.summary === after.summary, request_status: after.status });
  await page.screenshot({ path: `${dir}/salem-b08-recovered-step-five.png`, fullPage: true });
} catch (error) {
  // No reset URLs, passwords, mail bodies or session tokens in retained reports.
  report.errors.push({ stage, error_type: error instanceof Error ? error.name : 'UnknownError' });
} finally {
  await context.close();
  await browser.close();
  report.totals = { passed: report.checks.filter(c => c.result === 'PASS').length, failed: report.checks.filter(c => c.result === 'FAIL').length, errors: report.errors.length };
  await fs.writeFile(`${dir}/salem-b08-lifecycle-report.json`, JSON.stringify(report, null, 2) + '\n');
  console.log('Salem B08 lifecycle:', JSON.stringify(report.totals));
}
if (report.totals.failed || report.totals.errors) process.exitCode = 1;
