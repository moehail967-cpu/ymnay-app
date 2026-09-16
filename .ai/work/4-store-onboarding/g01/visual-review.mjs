import { chromium } from 'playwright';
import fs from 'node:fs/promises';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import { execFileSync } from 'node:child_process';
import { createHash } from 'node:crypto';
import assert from 'node:assert/strict';

const here = path.dirname(fileURLToPath(import.meta.url));
const root = path.resolve(here, '../../../..');
const out = process.env.G01_VISUAL_DIR;
const base = process.env.G01_BASE_URL || 'http://localhost';
const mail = process.env.G01_MAILPIT_URL || 'http://127.0.0.1:8025';
if (process.env.YMNAY_G01 !== '1' || process.env.APP_ENV !== 'testing' || !out || new URL(base).hostname !== 'localhost') {
  throw new Error('Visual review only runs against the disposable G01 application.');
}
await fs.mkdir(out, { recursive: true });
const report = { candidate: process.env.G01_CANDIDATE_SHA, synthetic_only: true, production_mutations: false, checks: [], screenshots: [], limitations: [] };
const check = (name, details = true) => report.checks.push({ name, details });
const assetRoots = [root, path.join(root, 'core'), path.join(root, 'core/public')];
async function writeAsset(relative, bytes) {
  for (const baseDir of assetRoots) {
    const file = path.join(baseDir, 'assets', relative);
    await fs.mkdir(path.dirname(file), { recursive: true });
    await fs.writeFile(file, bytes);
  }
}
function fixture(mode = 'setup') {
  try {
    const output = execFileSync('php', [path.join(here, 'visual-fixture.php'), mode], { cwd: path.join(root, 'core'), env: process.env, timeout: 900000, maxBuffer: 16 * 1024 * 1024 });
    return fs.writeFile(path.join(out, `fixture-${mode}.txt`), output);
  } catch (error) {
    throw new Error(`Isolated visual fixture ${mode} failed: ${String(error.stderr || error.stdout || error.message).slice(-5000)}`);
  }
}
async function otpFor(recipient) {
  for (let i = 0; i < 40; i++) {
    const list = await fetch(`${mail}/api/v1/messages`).then(r => r.json());
    const message = (list.messages || []).find(m => JSON.stringify(m.To || m.to || '').includes(recipient));
    if (message) {
      const body = await fetch(`${mail}/api/v1/message/${message.ID || message.Id || message.id}`).then(r => r.json());
      const match = `${body.Text || ''}\n${body.HTML || ''}`.match(/\b\d{6}\b/);
      if (match) return match[0];
    }
    await new Promise(resolve => setTimeout(resolve, 250));
  }
  throw new Error('The isolated mail service did not deliver the expected visual-review OTP.');
}
async function shot(page, name, full = false) {
  await page.evaluate(() => document.fonts.ready);
  await page.evaluate(() => window.scrollTo(0, 0));
  await page.screenshot({ path: path.join(out, `${name}.png`), animations: 'disabled' });
  if (full) await page.screenshot({ path: path.join(out, `${name}-full.png`), fullPage: true, animations: 'disabled' });
  report.screenshots.push({ name, viewport: page.viewportSize(), full_page_companion: full });
  const overflow = await page.evaluate(() => document.documentElement.scrollWidth > innerWidth + 1);
  assert.equal(overflow, false, `${name}: horizontal overflow`);
}
async function selected(page, name) {
  await page.waitForTimeout(250);
  const state = await page.locator('.ym-option input:checked').evaluate(input => {
    const card = input.closest('.ym-option');
    const style = getComputedStyle(card);
    const indicator = card.querySelector('.ym-selected-indicator');
    return { border: style.borderTopWidth, color: style.borderTopColor, text: indicator.textContent.trim(), visible: getComputedStyle(indicator).visibility, enhanced: card.classList.contains('is-selected') };
  });
  assert.deepEqual(state, { border: '2px', color: 'rgb(67, 56, 202)', text: '✓ محددة', visible: 'visible', enhanced: true });
  assert.equal(await page.locator('.ym-option.is-selected').count(), 1);
  const unselected = await page.locator('.ym-option:not(.is-selected) .ym-selected-indicator').evaluateAll(nodes => nodes.every(n => getComputedStyle(n).visibility === 'hidden'));
  assert.ok(unselected);
  check(name, state);
}
async function legal(page, container, name) {
  for (const policy of ['terms', 'privacy']) {
    const link = page.locator(`${container} [data-policy="${policy}"]`);
    assert.equal(await link.getAttribute('target'), '_blank');
    assert.match(await link.getAttribute('rel'), /noopener/);
    const before = page.url();
    const popupPromise = page.waitForEvent('popup');
    await link.click();
    const popup = await popupPromise;
    await popup.waitForLoadState('domcontentloaded');
    assert.match(popup.url(), /\/visual-(terms_condition|privacy_policy)$/);
    const text = await popup.locator('body').innerText();
    assert.ok(text.includes('صفحة تجريبية'), `Policy ${popup.url()} did not render its public fixture: ${text.slice(0, 400)}`);
    assert.equal(page.url(), before);
    await popup.close();
  }
  check(name);
}
const browser = await chromium.launch({ headless: true });
try {
  const identity = { source: 'https://ymnay.com/', observed_at: new Date().toISOString(), verified: false };
  try {
    const publicContext = await browser.newContext({ viewport: { width: 1440, height: 900 } });
    // Public read-only identity inspection: no login, forms, cookies from the owner, or POSTs.
    await publicContext.route('**/*', route => {
      const req = route.request();
      if (!['GET', 'HEAD'].includes(req.method()) || /facebook|googletagmanager|google-analytics/.test(req.url())) return route.abort();
      return route.continue();
    });
    const publicPage = await publicContext.newPage();
    await publicPage.goto(identity.source, { waitUntil: 'domcontentloaded', timeout: 60000 });
    await publicPage.evaluate(() => document.fonts.ready);
    await publicPage.waitForTimeout(1000);
    const observed = await publicPage.evaluate(() => {
      const body_family = getComputedStyle(document.body).fontFamily.split(',')[0].replace(/["']/g, '').trim();
      const heading_family = getComputedStyle(document.querySelector('h1') || document.body).fontFamily.split(',')[0].replace(/["']/g, '').trim();
      const logos = [...document.querySelectorAll('nav img,header img,.logo-wrapper img,.navbar-brand img')].filter(img => img.getBoundingClientRect().width > 35 && img.getBoundingClientRect().height > 10 && img.naturalWidth > 0);
      const logo = logos[0];
      const faces = [];
      const walk = (rules, baseUrl) => {
        for (const rule of rules) {
          if (rule.type === 5) {
            const family = rule.style.getPropertyValue('font-family').replace(/["']/g, '').trim();
            if ([body_family, heading_family].includes(family)) {
              const source = rule.style.getPropertyValue('src').match(/url\(["']?([^"')]+)["']?\)/);
              if (source) faces.push({ family, source: new URL(source[1], baseUrl).href, weight: rule.style.getPropertyValue('font-weight'), style: rule.style.getPropertyValue('font-style') });
            }
          }
          if (rule.cssRules) walk(rule.cssRules, baseUrl);
        }
      };
      for (const sheet of document.styleSheets) { try { walk(sheet.cssRules, sheet.href || location.href); } catch {} }
      return { body_family, heading_family, logo_url: logo?.currentSrc || logo?.src, font_faces: faces, public_loaded_fonts: [...document.fonts].filter(f => f.status === 'loaded').map(f => f.family) };
    });
    assert.ok(observed.logo_url, 'No actual public brand logo was found.');
    // The public homepage intentionally uses a white mark over a dark hero.
    // Observe the real inner-page mark separately for our light header; never recolor an asset or copy white-on-white.
    const whiteLogoUrl = observed.logo_url;
    await publicPage.screenshot({ path: path.join(out, 'public-brand-source.png') });
    await publicPage.goto('https://ymnay.com/login', { waitUntil: 'domcontentloaded', timeout: 60000 });
    await publicPage.evaluate(() => document.fonts.ready);
    const innerLogo = await publicPage.evaluate(() => [...document.querySelectorAll('.auth-logo img,nav img,header img,.logo-wrapper img,.navbar-brand img')]
      .find(img => img.getBoundingClientRect().width > 35 && img.getBoundingClientRect().height > 10 && img.naturalWidth > 0)?.currentSrc);
    assert.ok(innerLogo, 'The public inner-page logo could not be observed.');
    observed.logo_url = innerLogo;
    observed.logo_source = 'https://ymnay.com/login';
    observed.white_logo_url = whiteLogoUrl;
    await publicPage.screenshot({ path: path.join(out, 'public-inner-brand-source.png') });
    const logoUrl = new URL(observed.logo_url);
    assert.equal(logoUrl.protocol, 'https:');
    assert.ok(/(^|\.)(ymnay\.com|nazmart\.net)$/.test(logoUrl.hostname), 'Unexpected public logo host.');
    const logoResponse = await publicContext.request.get(logoUrl.href);
    assert.ok(logoResponse.ok());
    const bytes = await logoResponse.body();
    assert.ok(bytes.length > 100 && bytes.length < 5 * 1024 * 1024);
    const extension = /\.(png|svg|webp|jpe?g)$/i.exec(logoUrl.pathname)?.[1] || 'png';
    identity.logo_file = `visual-ymnay-logo.${extension}`;
    identity.logo_sha256 = createHash('sha256').update(bytes).digest('hex');
    await writeAsset(`landlord/uploads/media-uploader/${identity.logo_file}`, bytes);
    const whiteUrl = new URL(whiteLogoUrl);
    assert.equal(whiteUrl.protocol, 'https:');
    assert.ok(/(^|\.)(ymnay\.com|nazmart\.net)$/.test(whiteUrl.hostname));
    const whiteResponse = await publicContext.request.get(whiteUrl.href);
    assert.ok(whiteResponse.ok());
    const whiteBytes = await whiteResponse.body();
    assert.ok(whiteBytes.length > 100 && whiteBytes.length < 5 * 1024 * 1024);
    const whiteExtension = /\.(png|svg|webp|jpe?g)$/i.exec(whiteUrl.pathname)?.[1] || 'png';
    identity.white_logo_file = `visual-ymnay-white-logo.${whiteExtension}`;
    identity.white_logo_sha256 = createHash('sha256').update(whiteBytes).digest('hex');
    await writeAsset(`landlord/uploads/media-uploader/${identity.white_logo_file}`, whiteBytes);
    const catalog = {};
    for (const [index, face] of observed.font_faces.entries()) {
      if (index > 15) break;
      const url = new URL(face.source);
      if (url.protocol !== 'https:' || !/(^|\.)(ymnay\.com|nazmart\.net|gstatic\.com)$/.test(url.hostname)) continue;
      const response = await publicContext.request.get(url.href);
      if (!response.ok()) continue;
      const font = await response.body();
      if (font.length > 8 * 1024 * 1024) continue;
      const ext = /\.(woff2?|ttf|otf)$/i.exec(url.pathname)?.[1] || 'woff2';
      const filename = `visual-font-${index}.${ext}`;
      await writeAsset(`landlord/frontend/webfonts/custom/${filename}`, font);
      catalog[face.family] ??= { files: {} };
      const weight = /^\d+$/.test(face.weight) ? face.weight : '400';
      const variant = `${face.style === 'italic' ? 1 : 0},${weight}`;
      catalog[face.family].files[variant] = filename;
      catalog[face.family].files['0,400'] ??= filename;
      catalog[face.family].files['0,700'] ??= filename;
    }
    if (Object.keys(catalog).length) {
      await writeAsset('landlord/frontend/webfonts/custom-fonts.json', JSON.stringify(catalog));
    }
    Object.assign(identity, observed, { verified: true });
    await publicContext.close();
  } catch (error) {
    identity.error = error.message;
    report.limitations.push(`Public identity could not be fully verified: ${error.message}`);
  }
  await fs.writeFile(path.join(out, 'identity.json'), JSON.stringify(identity, null, 2));
  await fixture();
  const fixtureData = JSON.parse(await fs.readFile(path.join(out, 'fixture.json'), 'utf8'));
  const previewContext = await browser.newContext({ viewport: { width: 1440, height: 900 }, locale: 'ar-YE' });
  const preview = await previewContext.newPage();
  await preview.goto(fixtureData.arabic_preview, { waitUntil: 'networkidle', timeout: 120000 });
  await preview.locator('.ar-hero-title').waitFor();
  assert.ok((await preview.locator('.ar-hero-title').innerText()).includes('اكتشف عطرك'));
  await preview.screenshot({ path: path.join(out, 'arabic-template-source.png') });
  await writeAsset('landlord/uploads/media-uploader/visual-aromatic.png', await preview.screenshot());
  await fixture('preview-image');
  await previewContext.close();
  check('actual-repository-template-rendered-with-declared-arabic-content');

  for (const [kind, viewport] of [['desktop', { width: 1440, height: 900 }], ['mobile', { width: 390, height: 844 }]]) {
    const context = await browser.newContext({ viewport, locale: 'ar-YE', isMobile: kind === 'mobile' });
    const page = await context.newPage();
    page.setDefaultTimeout(30000);
    const errors = [];
    page.on('pageerror', error => errors.push(error.message));
    await page.goto(base, { waitUntil: 'networkidle' });
    await shot(page, `${kind}-01-landing`, true);
    const brand = await page.locator('.ym-site-header img').first().evaluate(img => ({ complete: img.complete, width: img.naturalWidth })).catch(() => null);
    const fonts = await page.evaluate(() => ({ body: getComputedStyle(document.body).fontFamily, loaded: [...document.fonts].filter(f => f.status === 'loaded').map(f => f.family.replace(/["']/g, '')) }));
    check(`${kind}-brand-and-font-observation`, { brand, fonts });
    if (!identity.verified || !brand?.width || !fonts.loaded.includes(identity.body_family)) report.limitations.push(`${kind}: exact logo/font gate is not satisfied.`);
    await page.goto(`${base}/create-store`, { waitUntil: 'networkidle' });
    const radio = page.locator(`input[name="plan_id"][value="${fixtureData.plan_ids[0]}"]`);
    await radio.check();
    await selected(page, `${kind}-plan-selection`);
    await page.locator(`input[name="plan_id"][value="${fixtureData.plan_ids[1]}"]`).check();
    await selected(page, `${kind}-plan-selection-change`);
    await radio.focus(); await page.keyboard.press('Space');
    await selected(page, `${kind}-native-keyboard-selection`);
    await page.mouse.move(0, 0);
    await page.locator('.ym-title').click();
    await shot(page, `${kind}-02-package-selected`, true);
    await Promise.all([page.waitForURL('**/create-store?step=2'), page.locator('form[action$="/create-store/plan"] button[type="submit"]').click()]);
    await page.locator('input[value="aromatic"]').check();
    await selected(page, `${kind}-theme-selection`);
    await shot(page, `${kind}-03-theme-selected`, true);
    await page.locator('.ym-option:has(input[value="aromatic"]) [data-onboarding-theme-preview]').click();
    await page.locator('#onboarding-theme-preview[open]').waitFor();
    await shot(page, `${kind}-04-theme-preview`);
    await page.locator('[data-onboarding-preview-close]').click();
    assert.ok(await page.locator('input[value="aromatic"]').isChecked());
    check(`${kind}-preview-preserves-theme-selection`);
    await Promise.all([page.waitForURL('**/create-store?step=3'), page.locator('form[action$="/create-store/theme"] button[type="submit"]').click()]);
    const subdomain = `g01-visual-${kind}`;
    await page.locator('#store_name').fill('متجر عِطري');
    await page.locator('#subdomain').fill(subdomain);
    await Promise.all([page.waitForURL('**/create-store?step=4'), page.locator('form[action$="/create-store/details"] button[type="submit"]').click()]);
    const firstEmail = `visual-${kind}-old@example.test`;
    const newEmail = `visual-${kind}@example.test`;
    const phone = kind === 'desktop' ? '967700000041' : '967700000042';
    await page.locator('#reg_name').fill('عميل مراجعة تجريبي');
    await page.locator('#reg_email').fill(firstEmail);
    await page.locator('#reg_phone').fill(phone);
    await legal(page, '#register-panel', `${kind}-registration-policy-popups`);
    assert.equal(await page.locator('#reg_email').inputValue(), firstEmail);
    await shot(page, `${kind}-05-registration`, true);
    const password = 'Visual-Review-Only-2026!';
    const send = async email => {
      await page.locator('#reg_email').fill(email);
      await page.locator('#reg_password').fill(password);
      await page.locator('#reg_password_confirmation').fill(password);
      await page.locator('#reg_terms').check();
      await page.locator('#register-btn').click();
      await page.locator('#otp-panel:not([hidden])').waitFor();
      assert.equal(await page.locator('#auth-title').innerText(), 'تحقق من بريدك');
    };
    await send(firstEmail);
    const oldOtp = await otpFor(firstEmail);
    await page.reload({ waitUntil: 'networkidle' });
    assert.equal(await page.locator('#auth-title').innerText(), 'تحقق من بريدك');
    assert.equal(await page.locator('#otp-email').innerText(), firstEmail);
    assert.ok(await page.locator('#resend-btn').isDisabled());
    await shot(page, `${kind}-06-otp-restored`, true);
    await page.locator('#otp-panel a').click();
    await page.waitForURL('**/create-store?step=3');
    assert.equal(await page.locator('#store_name').inputValue(), 'متجر عِطري');
    assert.equal(await page.locator('#subdomain').inputValue(), subdomain);
    await page.goto(`${base}/create-store?step=4`, { waitUntil: 'networkidle' });
    await page.locator('#edit-email-btn').click();
    assert.equal(await page.locator('#reg_name').inputValue(), 'عميل مراجعة تجريبي');
    assert.equal(await page.locator('#reg_phone').inputValue(), phone);
    assert.equal(await page.locator('#reg_password').inputValue(), '');
    assert.equal(await page.locator('#reg_password_confirmation').inputValue(), '');
    assert.equal(await page.locator('#otp').inputValue(), '');
    await page.locator('#reg_email').fill(newEmail);
    await shot(page, `${kind}-07-edit-email`, true);
    await send(newEmail);
    const newOtp = await otpFor(newEmail);
    if (newOtp !== oldOtp) {
      await page.locator('#otp').fill(oldOtp);
      await page.locator('#otp-panel button[type="submit"]').click();
      await page.locator('#auth-message .ym-error').waitFor();
      assert.equal(await page.locator('#auth-title').innerText(), 'تحقق من بريدك');
      check(`${kind}-old-email-code-rejected-after-replacement`);
    }
    await page.locator('#otp').fill(newOtp);
    await Promise.all([page.waitForURL('**/create-store?step=5'), page.locator('#otp-panel button[type="submit"]').click()]);
    const summary = page.locator(kind === 'mobile' ? 'details.ym-summary-mobile' : 'aside.ym-summary');
    const text = await summary.innerText();
    assert.ok(text.includes('عِطري') && text.includes('شهريًا') && text.includes(newEmail) && text.includes(subdomain));
    assert.equal(await summary.locator('.ym-summary-edit').count(), 4);
    for (const label of ['تعديل الباقة', 'تعديل القالب', 'تعديل المتجر', 'تعديل الرابط']) {
      await summary.locator(`[aria-label="${label}"]`).click();
      assert.match(page.url(), /step=[123]$/);
      await page.goto(`${base}/create-store?step=5`, { waitUntil: 'networkidle' });
    }
    check(`${kind}-review-name-period-and-four-preserving-edit-links`);
    await legal(page, '.ym-consent', `${kind}-review-policy-popups`);
    await shot(page, `${kind}-08-review`, true);
    await page.locator('#final-terms').check();
    const completePromise = page.waitForResponse(response => response.url().endsWith('/create-store/complete') && response.request().method() === 'POST', { timeout: 900000 });
    await page.locator('#complete-btn').click();
    await page.locator('#provisioning-overlay.active').waitFor();
    await shot(page, `${kind}-09-provisioning`);
    const complete = await completePromise;
    assert.ok(complete.ok(), 'Actual provisioning failed.');
    await page.waitForURL(/\/admin-home/, { timeout: 900000 });
    await page.locator('.dash-card').first().waitFor({ timeout: 60000 });
    await shot(page, `${kind}-10-dashboard-success`, true);
    await page.goto(`${base}/create-store?step=5`, { waitUntil: 'networkidle' });
    assert.ok((await page.locator('.ym-alert.ym-success').innerText()).includes('متجرك جاهز'));
    assert.equal(await page.locator('.ym-summary-edit').count(), 0);
    await shot(page, `${kind}-11-ready`, true);
    check(`${kind}-real-provisioning-dashboard-and-immutable-ready-summary`);
    assert.deepEqual(errors, [], `${kind}: JavaScript errors`);
    check(`${kind}-no-javascript-errors-or-horizontal-overflow`);
    await context.close();
  }
  assert.equal(report.limitations.length, 0, 'V05 still has an explicit identity/font limitation; inspect report.');
  report.result = 'PASS';
} catch (error) {
  report.result = 'FAIL';
  report.error = error.message;
  process.exitCode = 1;
} finally {
  await fs.writeFile(path.join(out, 'visual-review-report.json'), JSON.stringify(report, null, 2));
  await fs.writeFile(path.join(out, 'README.txt'), 'Engineering evidence from the exact candidate, not an independent Nour/Salem approval.\nAll store/account/legal data is synthetic. fixture.json declares representative plan inputs.\nidentity.json records the public logo/font source. Font binaries are deliberately excluded.\nScreenshots are unmodified running-application captures: desktop 1440x900 and mobile 390x844; -full companions include below-fold content.\n');
  await browser.close();
}
