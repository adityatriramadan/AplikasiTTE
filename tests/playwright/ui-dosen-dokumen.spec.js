const { test, expect } = require('@playwright/test');

test('Dosen can access dokumen page (skeleton)', async ({ page }) => {
  const base = process.env.EOFFICE_BASE_URL || 'http://127.0.0.1:8080';
  const consoleLogs = [];
  page.on('console', msg => { try { consoleLogs.push(`${new Date().toISOString()} [${msg.type()}] ${msg.text()}`); } catch(e){} });

  await page.goto(`${base}/?url=__test_login&nip=${process.env.EOFFICE_DOSEN_USER || 'dosen001'}`);
  await page.waitForURL('**/?url=dosen/*');

  await page.goto(`${base}/?url=dosen/dokumen`);
  await expect(page.locator('text=Dokumen Saya')).toBeVisible({ timeout: 5000 });
  try { require('fs').writeFileSync('test-results/dosen_console.log', consoleLogs.join('\n')); } catch(e) {}
});
