const { test, expect } = require('@playwright/test');

test('Kaprodi can sign a surat (skeleton)', async ({ page }) => {
  const base = process.env.EOFFICE_BASE_URL || 'http://127.0.0.1:8080';

  // Quick-test login via test helper route
  await page.goto(`${base}/?url=__test_login&nip=${process.env.EOFFICE_KAPRODI_USER || 'kaprodi001'}`);
  await page.waitForURL('**/?url=kaprodi/*');

  // Go to antrian and open first review (query-route)
  await page.goto(`${base}/?url=kaprodi/antrian`);
  // Click first review link (selector may vary)
  await page.click('a[href*="?url=kaprodi/review/"]');

  // Click tombol tanda tangan / input pin
  await page.click('a[href*="?url=kaprodi/input-pin/"]');

  // Fill PIN (test account must have known PIN)
  await page.fill('input[name="pin"]', process.env.EOFFICE_KAPRODI_PIN || '1234');
  await page.click('button[type="submit"]');

  // Expect success page or notification
  await expect(page).toHaveURL(/.*\?url=kaprodi\/sukses\/.*/);
});
