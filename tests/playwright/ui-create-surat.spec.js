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

  // 5) Choose first template: read its href and navigate using query-route
  const tplHref = await page.getAttribute('div.grid.cols-3 a', 'href');
  if (!tplHref) throw new Error('No template links found on Buat Surat page');
  const tplMatch = tplHref.match(/isi-form\/(\d+)/);
  if (!tplMatch) throw new Error('Cannot extract template ID from href: ' + tplHref);
  const tplId = tplMatch[1];
  await page.goto(`${base}/?url=sekretaris/isi-form/${tplId}`);

  // 6) Fill minimal fields
  await page.fill('input[name="perihal"]', 'Automated test surat');
  await page.fill('input[name="tanggal_surat"]', new Date().toISOString().slice(0,10));

  // 7) Force form to submit via query-route (built-in PHP server) then submit
  await page.evaluate(() => {
    const f = document.getElementById('form-surat');
    if (f) {
      f.action = window.location.origin + '/?url=sekretaris/preview-surat';
    }
  });
  await page.click('button[type="submit"]');

  // 8) Expect preview page to show perihal
  await expect(page.locator('text=Automated test surat')).toBeVisible({ timeout: 5000 });
});
