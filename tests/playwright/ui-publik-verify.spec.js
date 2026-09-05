const { test, expect } = require('@playwright/test');

test.setTimeout(120000);

test('Publik can open verification page (skeleton)', async ({ page }) => {
  const base = process.env.EOFFICE_BASE_URL || 'http://127.0.0.1:8080';
  const consoleLogs = [];
  page.on('console', msg => { try { consoleLogs.push(`${new Date().toISOString()} [${msg.type()}] ${msg.text()}`); } catch(e){} });

  await page.goto(`${base}/?url=verifikasi`);
  // Check for the heading text to avoid strict-mode matching multiple elements
  // Wait for the page heading (the public view uses an <h1>)
  await page.waitForSelector('text=Verifikasi Dokumen', { timeout: 15000 });
  await expect(page.locator('text=Verifikasi Dokumen')).toBeVisible({ timeout: 5000 });
  try { require('fs').writeFileSync('test-results/publik_console.log', consoleLogs.join('\n')); } catch(e) {}
});
