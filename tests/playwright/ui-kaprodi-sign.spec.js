const { test, expect } = require('@playwright/test');

test('Kaprodi can sign a surat (skeleton)', async ({ page }) => {
  const base = process.env.EOFFICE_BASE_URL || 'http://127.0.0.1:8080';

  // Quick-test login via test helper route
  await page.goto(`${base}/?url=__test_login&nip=${process.env.EOFFICE_KAPRODI_USER || 'kaprodi001'}`);
  await page.waitForURL('**/?url=kaprodi/*');

  // Go to antrian and open first review: extract href and navigate with query-route
  await page.goto(`${base}/?url=kaprodi/antrian`);
  const revHref = await page.getAttribute('table a.btn.secondary', 'href');
  if (!revHref) throw new Error('No review links found in antrian');
  const revMatch = revHref.match(/review\/(\d+)/);
  if (!revMatch) throw new Error('Cannot extract review ID from href: ' + revHref);
  const revId = revMatch[1];
  await page.goto(`${base}/?url=kaprodi/review/${revId}`);

  // Click tombol tanda tangan / input pin: extract href and navigate via query-route
  const pinHref = await page.getAttribute('a.btn.ok', 'href');
  if (!pinHref) throw new Error('Input PIN link not found on review page');
  const pinMatch = pinHref.match(/input-pin\/(\d+)/);
  if (!pinMatch) throw new Error('Cannot extract input-pin ID from href: ' + pinHref);
  const pinId = pinMatch[1];
  await page.goto(`${base}/?url=kaprodi/input-pin/${pinId}`);

  // Verify input-pin page displayed and perform signing (use test PIN)
  await expect(page.locator('input[name="pin"]')).toBeVisible({ timeout: 5000 });
  const pin = process.env.EOFFICE_KAPRODI_PIN || '1234';
  await page.fill('input[name="pin"]', pin);
  await page.click('button[type="submit"]');
  // Wait for success page after signing
  await page.waitForURL('**/?url=kaprodi/sukses/*', { timeout: 10000 });
});
