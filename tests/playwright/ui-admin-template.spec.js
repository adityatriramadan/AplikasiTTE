const { test, expect } = require('@playwright/test');

test.setTimeout(120000);

test('Admin can view template list (skeleton)', async ({ page }) => {
  const base = process.env.EOFFICE_BASE_URL || 'http://127.0.0.1:8080';
  const consoleLogs = [];
  page.on('console', msg => { try { consoleLogs.push(`${new Date().toISOString()} [${msg.type()}] ${msg.text()}`); } catch(e){} });

  await page.goto(`${base}/?url=__test_login&nip=${process.env.EOFFICE_ADMIN_USER || 'admin001'}`);
  await page.waitForURL('**/?url=admin/*');

  await page.goto(`${base}/?url=admin/template`);
  await page.waitForSelector('h2:has-text("Template Surat")', { timeout: 10000 });
  await expect(page.locator('h2:has-text("Template Surat")')).toBeVisible({ timeout: 5000 });
  try { require('fs').writeFileSync('test-results/admin_console.log', consoleLogs.join('\n')); } catch(e) {}
});
