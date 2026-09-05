import { defineConfig } from '@playwright/test';

export default defineConfig({
  testDir: '.',
  timeout: 30_000,
  expect: { timeout: 5000 },
  use: {
    headless: true,
    viewport: { width: 1280, height: 720 },
    baseURL: process.env.EOFFICE_BASE_URL || 'http://127.0.0.1:8080',
    ignoreHTTPSErrors: true,
  },
  reporter: process.env.CI ? [['dot'], ['github']] : 'list',
});
