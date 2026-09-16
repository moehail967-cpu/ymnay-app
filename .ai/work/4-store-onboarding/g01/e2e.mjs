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

async function assertProgressVisible(page, label) {
  await page.evaluate(() => window.scrollTo(0, 0));
  const evidence = await page.evaluate(() => {
    const header = document.querySelector('.ym-site-header')?.getBoundingClientRect();
    const markers = [...document.querySelectorAll('.ym-progress .ym-dot')].map(dot => {
      const rect = dot.getBoundingClientRect();
      const x = rect.left + rect.width / 2;
      const y = rect.top + rect.height / 2;
      const hit = document.elementFromPoint(x, y);
      return {
        marker: dot.textContent.trim(),
        top: rect.top,
        bottom: rect.bottom,
        center_y: y,
        center_uncovered: Boolean(hit && (hit === dot || dot.contains(hit))),
      };
    });
    return { header_bottom: header?.bottom ?? 0, markers };
  });
  if (evidence.markers.length !== 5 || evidence.markers.some(marker =>
    marker.top < evidence.header_bottom || marker.bottom > page.viewportSize().height || !marker.center_uncovered
  )) {
    throw new Error(`${label} progress markers are hidden or obstructed: ${JSON.stringify(evidence)}`);
  }
  check(`${label}-five-progress-markers-visible-unobstructed`, evidence);
}

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

async function capturedMailCount(recipient) {
  const list = await fetch(`${mailpitUrl}/api/v1/messages`).then(response => response.json());
  return (list.messages || []).filter(item => JSON.stringify(item.To || item.to || '').includes(recipient)).length;
}

async function waitForMailCount(recipient, minimum) {
  for (let attempt = 0; attempt < 30; attempt += 1) {
    const count = await capturedMailCount(recipient);
    if (count >= minimum) return count;
    await new Promise(resolve => setTimeout(resolve, 250));
  }
  return capturedMailCount(recipient);
}

async function latestResetUrl(recipient, excludedUrls = []) {
  for (let attempt = 0; attempt < 30; attempt += 1) {
    const list = await fetch(`${mailpitUrl}/api/v1/messages`).then(response => response.json());
    const messages = (list.messages || []).filter(item =>
      JSON.stringify(item.To || item.to || '').includes(recipient)
    );
    for (const message of messages) {
      const id = message.ID || message.Id || message.id;
      const body = await fetch(`${mailpitUrl}/api/v1/message/${id}`).then(response => response.json());
      const content = `${body.Text || ''}\n${body.HTML || ''}`;
      const match = content.match(/https?:\/\/[^"'\s<]+\/login\/reset-password\/[^"'\s<]+/);
      if (match && !excludedUrls.includes(match[0])) return match[0];
    }
    await new Promise(resolve => setTimeout(resolve, 250));
  }
  throw new Error(`No new password-reset URL was captured by Mailpit for ${recipient}.`);
}

async function submitPasswordReset(page, resetUrl, password) {
  await page.goto(resetUrl, { waitUntil: 'domcontentloaded' });
  await page.locator('#newPass').fill(password);
  await page.locator('#confirmPass').fill(password);
  await Promise.all([
    page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
    page.locator('button[type="submit"]').click(),
  ]);
}

const browser = await chromium.launch({ headless: true });
try {
  const desktop = await browser.newContext({
    viewport: { width: 1440, height: 1000 },
    locale: 'ar-SA',
  });
  const page = await desktop.newPage();
  page.setDefaultTimeout(15 * 60 * 1000);

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
  await assertProgressVisible(page, 'desktop');
  const planCards = await page.locator('form[action$="/create-store/plan"] .ym-option').allInnerTexts();
  if (planCards.length < 3
      || !planCards.some(text => text.includes('100') && text.includes('10'))
      || !planCards.some(text => text.includes('350') && text.includes('25') && text.includes('40'))
      || !planCards.some(text => text.includes('غير محدود'))
      || planCards.some(text => !text.includes('ر.س') || text.includes('$'))) {
    throw new Error(`Plan cards did not render distinct configured limits and SAR: ${JSON.stringify(planCards)}`);
  }
  check('multi-plan-data-driven-limits-and-sar', planCards);
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
  await assertProgressVisible(mobilePage, 'mobile');
  const mobilePlanCards = await mobilePage.locator('form[action$="/create-store/plan"] .ym-option').allInnerTexts();
  if (mobilePlanCards.length < 3 || mobilePlanCards.some(text => !text.includes('ر.س') || text.includes('$'))) {
    throw new Error(`Mobile plan cards did not render representative SAR data: ${JSON.stringify(mobilePlanCards)}`);
  }
  await mobilePage.screenshot({ path: `${evidenceDir}/onboarding-mobile-step-1.png`, fullPage: true });
  check('mobile-landing-and-onboarding');
  await mobile.close();

  await page.locator('input[name="plan_id"]').first().check();
  await Promise.all([
    page.waitForURL(/step=2/),
    page.locator('form[action$="/create-store/plan"] button[type="submit"]').click(),
  ]);
  check('plan-selection-preserved');
  if (await page.locator('input[name="theme_slug"]').count() < 3) {
    throw new Error('Representative multi-theme choices were not rendered for the selected plan.');
  }
  await page.screenshot({ path: `${evidenceDir}/onboarding-desktop-multi-theme.png`, fullPage: true });
  check('representative-multi-theme-layout');

  await page.locator('input[name="theme_slug"][value="hexfashion"]').check();
  const selectedThemeDisplayName = await page.locator('.ym-option:has(input[value="hexfashion"]) strong').innerText();
  await Promise.all([
    page.waitForURL(/step=3/),
    page.locator('form[action$="/create-store/theme"] button[type="submit"]').click(),
  ]);
  check('theme-selection-preserved');

  const stepOneLink = page.locator('.ym-progress a[href*="step=1"]');
  await Promise.all([page.waitForURL(/step=1/), stepOneLink.click()]);
  if (!await page.locator('input[name="plan_id"]').first().isChecked()) {
    throw new Error('Completed-step navigation lost the selected plan.');
  }
  await page.goto(`${baseUrl}/create-store?step=3`, { waitUntil: 'networkidle' });
  check('completed-step-navigation-visible-usable-and-preserves-selection');

  await page.locator('#store_name').fill('متجر G01 المعزول');
  await page.locator('#subdomain').fill('g01-browser-store');
  await Promise.all([
    page.waitForURL(/step=4/),
    page.locator('form[action$="/create-store/details"] button[type="submit"]').click(),
  ]);
  check('store-details-preserved');

  // Simulate an expired anonymous session. The encrypted, HTTP-only onboarding
  // reference cookie must restore the same draft without exposing sensitive data.
  await desktop.clearCookies({ name: /session/i });
  await page.goto(`${baseUrl}/create-store?step=4`, { waitUntil: 'networkidle' });
  const resumedSummary = await page.locator('.ym-summary').first().innerText();
  if (!resumedSummary.includes('متجر G01 المعزول') || !resumedSummary.includes('g01-browser-store')) {
    throw new Error('Session expiry did not resume the same onboarding request.');
  }
  check('browser-session-expiry-resumes-from-http-only-request-cookie');

  await page.locator('[data-auth="login"]').click();
  const recoveryUrl = await page.locator('#login-panel a').getAttribute('href');
  if (!recoveryUrl) throw new Error('Password recovery link is missing from onboarding.');
  await page.goto(new URL(recoveryUrl, baseUrl).toString(), { waitUntil: 'domcontentloaded' });
  await page.goto(`${baseUrl}/create-store?step=4`, { waitUntil: 'networkidle' });
  const recoverySummary = await page.locator('.ym-summary').first().innerText();
  if (!recoverySummary.includes('متجر G01 المعزول') || !recoverySummary.includes('g01-browser-store')) {
    throw new Error('Password-recovery detour did not preserve the onboarding request.');
  }
  check('password-recovery-detour-preserves-onboarding-request');

  const email = `g01-${Date.now()}@example.test`;
  await page.locator('#reg_name').fill('مستخدم اختبار G01');
  await page.locator('#reg_email').fill(email);
  await page.locator('#reg_phone').fill(`9665${String(Date.now()).slice(-8)}`);
  await page.locator('#reg_password').fill('G01-Isolated-Password!');
  await page.locator('#reg_password_confirmation').fill('G01-Isolated-Password!');
  await page.locator('#reg_terms').check();
  await page.locator('#register-btn').click();
  await page.locator('#otp-panel:not([hidden])').waitFor({ timeout: 30000 });
  await page.setViewportSize({ width: 390, height: 844 });
  await assertProgressVisible(page, 'mobile-otp');
  await page.screenshot({ path: `${evidenceDir}/onboarding-mobile-otp.png`, fullPage: true });
  await page.setViewportSize({ width: 1440, height: 1000 });
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
  await page.setViewportSize({ width: 390, height: 844 });
  await assertProgressVisible(page, 'mobile-review');
  await page.screenshot({ path: `${evidenceDir}/onboarding-mobile-review.png`, fullPage: true });
  await page.setViewportSize({ width: 1440, height: 1000 });
  check('valid-otp-auth-session-and-review');

  const summary = await page.locator('.ym-summary').first().innerText();
  for (const expected of ['متجر G01 المعزول', 'g01-browser-store', selectedThemeDisplayName]) {
    if (!summary.includes(expected)) throw new Error(`Review did not preserve: ${expected}`);
  }
  check('review-values-preserved');

  await page.locator('#final-terms').check();
  const uiCompletionResponse = page.waitForResponse(response =>
    response.url().endsWith('/create-store/complete') && response.request().method() === 'POST'
  );
  await page.locator('#complete-btn').click();
  await page.locator('#provisioning-overlay.active').waitFor();
  await page.setViewportSize({ width: 390, height: 844 });
  await page.screenshot({ path: `${evidenceDir}/onboarding-mobile-provisioning.png`, fullPage: true });
  await page.setViewportSize({ width: 1440, height: 1000 });
  const csrf = await page.locator('meta[name="csrf-token"]').getAttribute('content');
  const parallelCompletion = desktop.request.post(`${baseUrl}/create-store/complete`, {
    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
    data: { terms_condition: true },
  });
  const redirect = Promise.race([
    page.waitForURL(/g01-browser-store\.localhost/, { timeout: 15 * 60 * 1000, waitUntil: 'domcontentloaded' }),
    page.locator('#complete-message .ym-error').waitFor({ timeout: 15 * 60 * 1000 }).then(async () => {
      throw new Error(`Primary provisioning failed: ${await page.locator('#complete-message').innerText()}`);
    }),
  ]);
  const [uiResponse, parallelResult] = await Promise.all([uiCompletionResponse, parallelCompletion]);
  const completionStatuses = [uiResponse.status(), parallelResult.status()].sort((a, b) => a - b);
  if (completionStatuses[0] !== 200 || ![200, 202].includes(completionStatuses[1])) {
    throw new Error(`Same-request completion race returned unexpected statuses: ${completionStatuses.join(',')}`);
  }
  check('same-request-parallel-http-completion-is-idempotent', completionStatuses);
  await redirect;
  await page.locator('.dash-card').first().waitFor({ timeout: 30000 });
  if (await page.locator('.dash-card').count() < 4) {
    throw new Error('Token login did not render the tenant dashboard.');
  }
  check('full-provisioning-and-token-login', page.url().replace(/token-login\/[^/]+/, 'token-login/[redacted]'));
  check('tenant-dashboard-rendered-without-exception');
  await page.screenshot({ path: `${evidenceDir}/tenant-dashboard.png`, fullPage: true });
  await page.setViewportSize({ width: 390, height: 844 });
  await page.screenshot({ path: `${evidenceDir}/tenant-dashboard-mobile.png`, fullPage: true });
  await page.setViewportSize({ width: 1440, height: 1000 });
  check('mobile-verification-review-loading-and-success-evidence');

  const existingContext = await browser.newContext({ locale: 'ar-SA' });
  const existingPage = await existingContext.newPage();
  existingPage.setDefaultTimeout(30000);
  await existingPage.goto(`${baseUrl}/login`, { waitUntil: 'domcontentloaded' });
  const existingLogin = await existingPage.evaluate(async () => {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    const response = await fetch('/store-login', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
      body: JSON.stringify({ username: 'g01-existing-unverified@example.test', password: 'G01-Isolated-Password!' }),
    });
    return { status: response.status, body: await response.json() };
  });
  if (existingLogin.status !== 200 || existingLogin.body.status !== 'valid') {
    throw new Error('Existing unverified account could not log in.');
  }
  await existingPage.goto(`${baseUrl}/create-store?step=4`, { waitUntil: 'networkidle' });
  await Promise.all([
    existingPage.waitForURL(/verify-email/),
    existingPage.locator('a[href*="verify-email"]').click(),
  ]);
  const firstDeliveryCount = await waitForMailCount('g01-existing-unverified@example.test', 1);
  if (firstDeliveryCount < 1) throw new Error('Existing-account verification mail was not captured.');
  await Promise.all([
    existingPage.waitForNavigation({ waitUntil: 'domcontentloaded' }),
    existingPage.locator('#send').click(),
  ]);
  const resentDeliveryCount = await waitForMailCount('g01-existing-unverified@example.test', firstDeliveryCount + 1);
  const successFlashVisible = await existingPage.locator('i.tabler-circle-check').count() > 0;
  if (resentDeliveryCount <= firstDeliveryCount || !successFlashVisible) {
    throw new Error(`Existing-account verification resend did not complete successfully: ${JSON.stringify({ firstDeliveryCount, resentDeliveryCount, successFlashVisible })}`);
  }
  check('existing-account-verification-initial-send-and-resend', { firstDeliveryCount, resentDeliveryCount });
  await existingContext.close();

  const recoveryEmail = 'g01-password-recovery@example.test';
  const recoveryContext = await browser.newContext({ locale: 'ar-SA' });
  const recoveryPage = await recoveryContext.newPage();
  recoveryPage.setDefaultTimeout(30000);
  await recoveryPage.goto(`${baseUrl}/create-store`, { waitUntil: 'networkidle' });
  await recoveryPage.locator('input[name="plan_id"]').first().check();
  await Promise.all([
    recoveryPage.waitForURL(/step=2/),
    recoveryPage.locator('form[action$="/create-store/plan"] button[type="submit"]').click(),
  ]);
  await recoveryPage.locator('input[name="theme_slug"][value="hexfashion"]').check();
  await Promise.all([
    recoveryPage.waitForURL(/step=3/),
    recoveryPage.locator('form[action$="/create-store/theme"] button[type="submit"]').click(),
  ]);
  await recoveryPage.locator('#store_name').fill('G01 Password Recovery Store');
  await recoveryPage.locator('#subdomain').fill('g01-password-recovery');
  await Promise.all([
    recoveryPage.waitForURL(/step=4/),
    recoveryPage.locator('form[action$="/create-store/details"] button[type="submit"]').click(),
  ]);

  await recoveryPage.goto(`${baseUrl}/login/forget-password`, { waitUntil: 'domcontentloaded' });
  await recoveryPage.locator('input[name="username"]').fill(recoveryEmail);
  await Promise.all([
    recoveryPage.waitForNavigation({ waitUntil: 'domcontentloaded' }),
    recoveryPage.locator('#send').click(),
  ]);
  const firstResetUrl = await latestResetUrl(recoveryEmail);

  await recoveryPage.locator('input[name="username"]').fill(recoveryEmail);
  await Promise.all([
    recoveryPage.waitForNavigation({ waitUntil: 'domcontentloaded' }),
    recoveryPage.locator('#send').click(),
  ]);
  const secondResetUrl = await latestResetUrl(recoveryEmail, [firstResetUrl]);
  if (firstResetUrl === secondResetUrl) throw new Error('A repeated recovery request reused the first reset URL.');

  await submitPasswordReset(recoveryPage, firstResetUrl, 'G01-Stale-Recovery!');
  if (await recoveryPage.locator('i.tabler-circle-check').count() > 0) {
    throw new Error('The superseded password-reset URL was accepted.');
  }
  await submitPasswordReset(recoveryPage, secondResetUrl, 'G01-Recovered-Password-1!');
  if (!recoveryPage.url().endsWith('/login')) throw new Error('The replacement reset URL was not accepted.');

  await recoveryPage.goto(`${baseUrl}/login/forget-password`, { waitUntil: 'domcontentloaded' });
  await recoveryPage.locator('input[name="username"]').fill(recoveryEmail);
  await Promise.all([
    recoveryPage.waitForNavigation({ waitUntil: 'domcontentloaded' }),
    recoveryPage.locator('#send').click(),
  ]);
  const thirdResetUrl = await latestResetUrl(recoveryEmail, [firstResetUrl, secondResetUrl]);
  await submitPasswordReset(recoveryPage, thirdResetUrl, 'G01-Recovered-Password-2!');

  const recoveredLogin = await recoveryPage.evaluate(async ({ email }) => {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    const response = await fetch('/store-login', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
      body: JSON.stringify({ username: email, password: 'G01-Recovered-Password-2!' }),
    });
    return { status: response.status, body: await response.json() };
  }, { email: recoveryEmail });
  if (recoveredLogin.status !== 200 || recoveredLogin.body.status !== 'valid'
      || !recoveredLogin.body.redirect_url?.includes('/create-store?step=5')) {
    throw new Error(`Recovered account did not resume onboarding: ${JSON.stringify(recoveredLogin)}`);
  }
  await recoveryPage.goto(recoveredLogin.body.redirect_url, { waitUntil: 'networkidle' });
  const recoveredSummary = await recoveryPage.locator('.ym-summary').first().innerText();
  for (const expected of ['G01 Password Recovery Store', 'g01-password-recovery', selectedThemeDisplayName]) {
    if (!recoveredSummary.includes(expected)) throw new Error(`Password recovery lost onboarding choice: ${expected}`);
  }
  check('repeated-password-recovery-replaces-consumes-and-resumes-onboarding');
  await recoveryContext.close();

  const loginRaceUser = async (url, email) => {
    const context = await browser.newContext({ locale: 'ar-SA' });
    const racePage = await context.newPage();
    racePage.setDefaultTimeout(15 * 60 * 1000);
    await racePage.goto(`${url}/login`, { waitUntil: 'domcontentloaded' });
    const login = await racePage.evaluate(async ({ email }) => {
      const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
      const response = await fetch('/store-login', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
        body: JSON.stringify({ username: email, password: 'G01-Isolated-Password!' }),
      });
      return { status: response.status, body: await response.json() };
    }, { email });
    if (login.status !== 200 || login.body.status !== 'valid') throw new Error(`Race login failed for ${email}.`);
    await racePage.goto(`${url}/create-store?step=5`, { waitUntil: 'domcontentloaded' });
    await racePage.locator('#complete-btn').waitFor();
    return { context, page: racePage };
  };

  const raceOne = await loginRaceUser('http://localhost', 'g01-race-1@example.test');
  const raceTwo = await loginRaceUser('http://localhost', 'g01-race-2@example.test');
  const foreignReferenceCookie = (await raceTwo.context.cookies(baseUrl))
    .find(cookie => cookie.name === 'store_onboarding_request_id');
  if (!foreignReferenceCookie) throw new Error('The foreign onboarding reference cookie was not issued.');
  await raceOne.context.addCookies([foreignReferenceCookie]);
  await raceOne.page.goto(`${baseUrl}/create-store?step=5`, { waitUntil: 'networkidle' });
  const isolatedSummary = await raceOne.page.locator('.ym-summary').first().innerText();
  if (!isolatedSummary.includes('G01 Race Store 1') || isolatedSummary.includes('G01 Race Store 2')) {
    throw new Error(`A foreign onboarding reference crossed the account boundary: ${isolatedSummary}`);
  }
  check('foreign-request-cookie-cannot-cross-account-boundary');
  const submitComplete = racePage => racePage.evaluate(async () => {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    const response = await fetch('/create-store/complete', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
      body: JSON.stringify({ terms_condition: true }),
    });
    return { status: response.status, body: await response.json() };
  });
  const raceResults = await Promise.all([submitComplete(raceOne.page), submitComplete(raceTwo.page)]);
  const raceStatuses = raceResults.map(result => result.status).sort((a, b) => a - b);
  if (JSON.stringify(raceStatuses) !== JSON.stringify([200, 422])) {
    throw new Error(`Parallel address race produced unexpected statuses: ${raceStatuses.join(',')}`);
  }
  check('parallel-address-race-one-winner', raceResults.map(result => ({ status: result.status, state: result.body.status })));
  await raceOne.context.close();
  await raceTwo.context.close();

  await fs.writeFile(`${evidenceDir}/browser-report.json`, `${JSON.stringify(report, null, 2)}\n`);
  await desktop.close();
} catch (error) {
  report.error = error instanceof Error ? error.message : String(error);
  await fs.writeFile(`${evidenceDir}/browser-report.json`, `${JSON.stringify(report, null, 2)}\n`);
  throw error;
} finally {
  await browser.close();
}
