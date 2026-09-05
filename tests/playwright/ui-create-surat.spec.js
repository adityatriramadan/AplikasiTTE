const { test, expect } = require('@playwright/test');

test('Sekretaris can create surat (skeleton)', async ({ page }) => {
  // Configure BASE_URL in env when running locally, e.g. export EOFFICE_BASE_URL=http://localhost/eoffice
  const base = process.env.EOFFICE_BASE_URL || 'http://127.0.0.1:8080';

  // Quick-test login via test helper route (only enabled when EOFFICE_ENABLE_TEST_ROUTES=1)
  await page.goto(`${base}/?url=__test_login&nip=${process.env.EOFFICE_TEST_USER || 'sekretaris001'}`);
  // Wait for redirect to sekretaris dashboard (front controller uses query param)
  await page.waitForURL('**/?url=sekretaris/*');

  // 4) Navigate to buat surat (use query-route for PHP built-in server)
  await page.goto(`${base}/?url=sekretaris/buat-surat`);

  // 5) This is a skeleton: choose first template and proceed (selector may need adjustment)
  await page.click('a.template-card, .template-item');
  await page.click('a[href*="?url=sekretaris/isi-form"]');

  // 6) Fill minimal fields
  await page.fill('input[name="perihal"]', 'Automated test surat');
  await page.fill('input[name="tanggal_surat"]', new Date().toISOString().slice(0,10));

  // 7) Preview (submit form)
  await page.click('button[type="submit"]');

  // 8) Expect preview page to show perihal
  await expect(page.locator('text=Automated test surat')).toBeVisible({ timeout: 5000 });
});
