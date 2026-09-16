import { chromium } from 'playwright';
import fs from 'node:fs/promises';

const baseUrl = process.env.G01_BASE_URL || 'http://localhost';
const dir = process.env.G01_EVIDENCE_DIR;
if (process.env.YMNAY_G01 !== '1' || process.env.APP_ENV !== 'testing'
    || !dir || !['localhost', '127.0.0.1'].includes(new URL(baseUrl).hostname)) {
  throw new Error('Salem UI checks require the isolated G01 application.');
}
await fs.mkdir(dir, { recursive: true });
const report = { application_candidate: 'ef2cee35d42d6834d3ca8acda019d79628faf68b',
  method: 'Real Chromium/HTTP; existing G01 synthetic plan and guest drafts; no application changes',
  checks: [], errors: [], screenshots: [] };
const record = (name, pass, evidence) => report.checks.push({ name, result: pass ? 'PASS' : 'FAIL', evidence });
const browser = await chromium.launch({ headless: true });
try {
  for (const [label, viewport, isMobile] of [
    ['desktop', { width: 1440, height: 900 }, false],
    ['mobile', { width: 390, height: 844 }, true],
  ]) {
    const context = await browser.newContext({ viewport, isMobile, locale: 'ar-SA' });
    const page = await context.newPage();
    page.setDefaultTimeout(30000);
    const shot = async (name) => {
      const filename = `salem-${label}-${name}.png`;
      await page.screenshot({ path: `${dir}/${filename}`, fullPage: true });
      report.screenshots.push(filename);
    };
    try {
      await page.goto(`${baseUrl}/create-store`, { waitUntil: 'networkidle' });
      await page.evaluate(() => window.scrollTo(0, 0));
      const progress = await page.locator('.ym-progress .ym-dot').evaluateAll(dots => dots.map(dot => {
        const r = dot.getBoundingClientRect();
        const x = r.left + r.width / 2, y = r.top + r.height / 2;
        const hit = document.elementFromPoint(x, y);
        const style = getComputedStyle(dot);
        return { marker: dot.textContent.trim(), top: r.top, bottom: r.bottom,
          center: { x, y }, css_visible: style.visibility !== 'hidden' && style.display !== 'none',
          center_uncovered: Boolean(hit && (hit === dot || dot.contains(hit))),
          topmost_tag: hit?.tagName, topmost_class: String(hit?.className || '') };
      }));
      record(`${label}: five progress markers visible and unobstructed`, progress.length === 5
        && progress.every(p => p.css_visible && p.top >= 0 && p.bottom <= viewport.height && p.center_uncovered), progress);
      const planCards = await page.locator('.ym-option').allInnerTexts();
      record(`${label}: configured non-60 trial visible`, planCards.some(text => text.includes('37')), { planCards });
      await shot('step-1');
      await page.locator('input[name="plan_id"]').first().check();
      await Promise.all([page.waitForURL(/step=2/), page.locator('form[action$="/create-store/plan"] button[type="submit"]').click()]);
      await shot('step-2');
      const previewButton = page.locator('[data-onboarding-theme-preview]').first();
      await previewButton.click();
      const preview = page.locator('dialog.ym-theme-preview-dialog');
      await preview.waitFor({ state: 'visible' });
      await shot('theme-preview');
      await page.keyboard.press('Escape');
      await preview.waitFor({ state: 'hidden' });
      record(`${label}: preview Escape restores focus`, await previewButton.evaluate(el => document.activeElement === el), { dialog_closed: true });
      await page.locator('input[name="theme_slug"][value="hexfashion"]').check();
      await Promise.all([page.waitForURL(/step=3/), page.locator('form[action$="/create-store/theme"] button[type="submit"]').click()]);
      await page.locator('#store_name').fill(`Salem ${label} fixture`);
      await page.locator('#subdomain').fill('admin');
      await Promise.all([page.waitForNavigation({ waitUntil: 'networkidle' }), page.locator('form[action$="/create-store/details"] button[type="submit"]').click()]);
      record(`${label}: reserved address rejected on details`, await page.locator('.ym-error,.ym-field-error').count() > 0
        && await page.locator('#subdomain').count() === 1, { remained_on_details: await page.locator('#subdomain').count() === 1 });
      await shot('reserved-address');
      await page.locator('#subdomain').fill(`g01-salem-${label}`);
      await Promise.all([page.waitForURL(/step=4/), page.locator('form[action$="/create-store/details"] button[type="submit"]').click()]);
      await shot('step-4');
      record(`${label}: approved account fields visible`, await page.locator('#reg_name,#reg_email,#reg_phone,#reg_password,#reg_password_confirmation').count() === 5,
        { extra_username_field: await page.locator('#register-panel input[name="username"]').count() });
      // Use the visible form Back action, not the potentially obscured progress navigation.
      await Promise.all([page.waitForURL(/step=3/), page.locator('#register-panel a[href*="step=3"]').click()]);
      record(`${label}: Back preserves saved store details`, await page.locator('#store_name').inputValue() === `Salem ${label} fixture`
        && await page.locator('#subdomain').inputValue() === `g01-salem-${label}`, { returned_to_step: 3 });
      await shot('step-3-restored');
    } catch (error) {
      report.errors.push({ viewport: label, error: error instanceof Error ? error.message : String(error) });
      await shot('instrumentation-error').catch(() => {});
    } finally { await context.close(); }
  }
} finally {
  await browser.close();
  report.totals = { passed: report.checks.filter(x => x.result === 'PASS').length,
    failed: report.checks.filter(x => x.result === 'FAIL').length, errors: report.errors.length };
  await fs.writeFile(`${dir}/salem-ui-report.json`, JSON.stringify(report, null, 2) + '\n');
  console.log(JSON.stringify(report.totals));
}
if (report.errors.length || report.checks.some(x => x.result === 'FAIL')) process.exitCode = 1;
