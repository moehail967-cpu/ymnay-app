import { chromium } from 'playwright';
import fs from 'node:fs/promises';

const baseUrl = process.env.G01_BASE_URL || 'http://localhost';
const mailpitUrl = process.env.G01_MAILPIT_URL || 'http://127.0.0.1:8025';
const evidenceDir = process.env.G01_EVIDENCE_DIR;

if (!evidenceDir || process.env.YMNAY_G01 !== '1') {
  throw new Error('G01 browser checks require the isolated evidence directory and YMNAY_G01=1.');
}

await fs.mkdir(evidenceDir, { recursive: true });
const report = { base_url: baseUrl, checks: [], synthetic_only: true };
const check = (name, detail = true) => report.checks.push({ name, detail });

async function latestOtp(recipient) {
  for (let attempt = 0; attempt < 30; attempt += 1) {
    const list = await fetch(`${mailpitUrl}/api/v1/messages`).then(response => response.json());
    const message = (list.messages || []).find(item =>
      JSON.stringify(item.To || item.to || '').includes(recipient)
    );
    if (message) {
      const id = message.ID || message.Id || message.id;
      const body = await fetch(`${mailpitUrl}/api/v1/message/${id}`).then(response => response.json());
      const match = `${body.Text || ''}\n${body.HTML || ''}`.match(/\b\d{6}\b/);
      if (match) return match[0];
    }
    await new Promise(resolve => setTimeout(resolve, 1000));
  }
  throw new Error(`No OTP was captured by Mailpit for ${recipient}.`);
}

const browser = await chromium.launch({ headless: true });
try {
  const desktop = await browser.newContext({
    viewport: { width: 1440, height: 1000 },
    locale: 'ar-SA',
  });
  const page = await desktop.newPage();

  await page.goto(baseUrl, { waitUntil: 'networkidle' });
  await page.locator('#ym-hero-title').waitFor();
  for (const section of ['#features', '#themes', '#how-it-works', '#pricing', '#faq']) {
    if (await page.locator(section).count() !== 1) throw new Error(`Landing section missing: ${section}`);
  }
  if (await page.locator('html').getAttribute('dir') !== 'rtl') throw new Error('Landing page is not RTL.');
  await page.screenshot({ path: `${evidenceDir}/landing-desktop.png`, fullPage: true });
  check('landing-desktop-rtl-sections');

  await page.goto(`${baseUrl}/create-store`, { waitUntil: 'networkidle' });
  await page.locator('input[name="plan_id"]').first().waitFor();
  await page.screenshot({ path: `${evidenceDir}/onboarding-desktop-step-1.png`, fullPage: true });
  check('onboarding-desktop-step-1');

  const mobile = await browser.newContext({
    viewport: { width: 390, height: 844 },
    deviceScaleFactor: 1,
    isMobile: true,
    locale: 'ar-SA',
  });
  const mobilePage = await mobile.newPage();
  await mobilePage.goto(baseUrl, { waitUntil: 'networkidle' });
  await mobilePage.screenshot({ path: `${evidenceDir}/landing-mobile.png`, fullPage: true });
  await mobilePage.goto(`${baseUrl}/create-store`, { waitUntil: 'networkidle' });
  await mobilePage.screenshot({ path: `${evidenceDir}/onboarding-mobile-step-1.png`, fullPage: true });
  check('mobile-landing-and-onboarding');
  await mobile.close();

  await page.locator('input[name="plan_id"]').first().check();
  await Promise.all([
    page.waitForURL(/step=2/),
    page.locator('form[action$="/create-store/plan"] button[type="submit"]').click(),
  ]);
  check('plan-selection-preserved');

  await page.locator('input[name="theme_slug"][value="hexfashion"]').check();
  await Promise.all([
    page.waitForURL(/step=3/),
    page.locator('form[action$="/create-store/theme"] button[type="submit"]').click(),
  ]);
  check('theme-selection-preserved');

  await page.locator('#store_name').fill('متجر G01 المعزول');
  await page.locator('#subdomain').fill('g01-browser-store');
  await Promise.all([
    page.waitForURL(/step=4/),
    page.locator('form[action$="/create-store/details"] button[type="submit"]').click(),
  ]);
  check('store-details-preserved');

  const email = `g01-${Date.now()}@example.test`;
  await page.locator('#reg_name').fill('مستخدم اختبار G01');
  await page.locator('#reg_email').fill(email);
  await page.locator('#reg_phone').fill(`9665${String(Date.now()).slice(-8)}`);
  await page.locator('#reg_password').fill('G01-Isolated-Password!');
  await page.locator('#reg_password_confirmation').fill('G01-Isolated-Password!');
  await page.locator('#reg_terms').check();
  await page.locator('#register-btn').click();
  await page.locator('#otp-panel:not([hidden])').waitFor({ timeout: 30000 });
  check('registration-http-session-and-mail-send');

  const cooldown = await page.evaluate(async () => {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    const response = await fetch('/register-otp-resend', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
      body: '{}',
    });
    return { status: response.status, body: await response.json() };
  });
  if (cooldown.status !== 429 || !cooldown.body.retry_after) throw new Error('OTP resend cooldown was not enforced.');
  check('otp-resend-rate-limit', cooldown.status);

  await page.locator('#otp').fill('000000');
  await page.locator('#otp-panel button[type="submit"]').click();
  await page.locator('#auth-message .ym-error').waitFor();
  check('invalid-otp-rejected');

  const otp = await latestOtp(email);
  await page.locator('#otp').fill(otp);
  await Promise.all([
    page.waitForURL(/step=5/, { timeout: 30000 }),
    page.locator('#otp-panel button[type="submit"]').click(),
  ]);
  await page.screenshot({ path: `${evidenceDir}/onboarding-desktop-review.png`, fullPage: true });
  check('valid-otp-auth-session-and-review');

  const summary = await page.locator('.ym-summary').first().innerText();
  for (const expected of ['متجر G01 المعزول', 'g01-browser-store', 'hexfashion']) {
    if (!summary.includes(expected)) throw new Error(`Review did not preserve: ${expected}`);
  }
  check('review-values-preserved');

  await page.locator('#final-terms').check();
  await page.locator('#complete-btn').click();
  await page.waitForURL(/g01-browser-store\.localhost/, { timeout: 15 * 60 * 1000, waitUntil: 'domcontentloaded' });
  check('full-provisioning-and-token-login', page.url().replace(/token-login\/[^/]+/, 'token-login/[redacted]'));
  await page.screenshot({ path: `${evidenceDir}/tenant-dashboard.png`, fullPage: true });

  await fs.writeFile(`${evidenceDir}/browser-report.json`, `${JSON.stringify(report, null, 2)}\n`);
  await desktop.close();
} catch (error) {
  report.error = error instanceof Error ? error.message : String(error);
  await fs.writeFile(`${evidenceDir}/browser-report.json`, `${JSON.stringify(report, null, 2)}\n`);
  throw error;
} finally {
  await browser.close();
}
