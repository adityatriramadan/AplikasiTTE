const { test, expect } = require('@playwright/test');

test.setTimeout(120000);

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
  // Wait for either success or review redirect (allow longer timeout)
  await page.waitForTimeout(2000);
  // Give server up to 45s for processing then check resulting URL
  await page.waitForTimeout(43000);
  const finalUrl = page.url();
  if (finalUrl.includes('?url=kaprodi/sukses/')) {
    // success
    return;
  }
  if (finalUrl.includes('?url=kaprodi/review/')) {
    // Attempt to surface error message from page
    const err = await page.locator('.alert.error').innerText().catch(() => 'Unknown error - no .alert.error');
    throw new Error('Signing failed, redirected to review: ' + err + ' | URL=' + finalUrl);
  }
  throw new Error('Signing did not complete; final URL=' + finalUrl);
});
