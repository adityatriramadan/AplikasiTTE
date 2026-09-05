const { test, expect } = require('@playwright/test');

test('Kaprodi can sign a surat (skeleton)', async ({ page }) => {
  const base = process.env.EOFFICE_BASE_URL || 'http://127.0.0.1:8080';

  // Login as kaprodi
  await page.goto(`${base}/auth/login`);
  await page.fill('input[name="username"]', process.env.EOFFICE_KAPRODI_USER || 'kaprodi@example.com');
  await page.fill('input[name="password"]', process.env.EOFFICE_KAPRODI_PASS || 'secret');
  await page.click('button[type="submit"]');

  // Wait for kaprodi dashboard
  await page.waitForURL('**/kaprodi/*');

  // Go to antrian and open first review
  await page.goto(`${base}/kaprodi/antrian`);
  // Click first review link (selector may vary)
  await page.click('a[href*="/kaprodi/review/"]');

  // Click tombol tanda tangan / input pin
  await page.click('a[href*="/kaprodi/input-pin/"]');

  // Fill PIN (test account must have known PIN)
  await page.fill('input[name="pin"]', process.env.EOFFICE_KAPRODI_PIN || '1234');
  await page.click('button[type="submit"]');

  // Expect success page or notification
  await expect(page).toHaveURL(/.*kaprodi\/sukses\/.*/);
});
