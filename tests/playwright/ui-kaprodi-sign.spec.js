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

  // Click tombol tanda tangan / input pin
  await page.click('a[href*="?url=kaprodi/input-pin/"]');

  // Fill PIN (test account must have known PIN)
  await page.fill('input[name="pin"]', process.env.EOFFICE_KAPRODI_PIN || '1234');
  await page.click('button[type="submit"]');

  // Expect success page or notification
  await expect(page).toHaveURL(/.*\?url=kaprodi\/sukses\/.*/);
});
