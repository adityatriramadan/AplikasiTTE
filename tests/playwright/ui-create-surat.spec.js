const { test, expect } = require('@playwright/test');

test('Sekretaris can create surat (skeleton)', async ({ page }) => {
  // Configure BASE_URL in env when running locally, e.g. export EOFFICE_BASE_URL=http://localhost/eoffice
  const base = process.env.EOFFICE_BASE_URL || 'http://localhost/eoffice';

  // 1) Visit login page
  await page.goto(`${base}/auth/login`);

  // 2) Fill credentials (update selectors if different)
  await page.fill('input[name="username"]', process.env.EOFFICE_TEST_USER || 'sekretaris@example.com');
  await page.fill('input[name="password"]', process.env.EOFFICE_TEST_PASS || 'secret');
  await page.click('button[type="submit"]');

  // 3) Wait for dashboard
  await page.waitForURL('**/sekretaris/*');

  // 4) Navigate to buat surat
  await page.goto(`${base}/sekretaris/buat-surat`);

  // 5) This is a skeleton: choose first template and proceed (selector may need adjustment)
  await page.click('a.template-card, .template-item');
  await page.click('a[href*="/isi-form"]');

  // 6) Fill minimal fields
  await page.fill('input[name="perihal"]', 'Automated test surat');
  await page.fill('input[name="tanggal_surat"]', new Date().toISOString().slice(0,10));

  // 7) Preview (submit form)
  await page.click('button[type="submit"]');

  // 8) Expect preview page to show perihal
  await expect(page.locator('text=Automated test surat')).toBeVisible();
});
