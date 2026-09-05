import { defineConfig } from '@playwright/test';

export default defineConfig({
  testDir: '.',
  timeout: 120_000,
  expect: { timeout: 10_000 },
  retries: process.env.CI ? 2 : 0,
  workers: process.env.CI ? 1 : undefined,
  use: {
    headless: true,
    viewport: { width: 1280, height: 720 },
    baseURL: process.env.EOFFICE_BASE_URL || 'http://127.0.0.1:8080',
    ignoreHTTPSErrors: true,
    screenshot: 'only-on-failure',
    trace: 'retain-on-failure',
    video: 'retain-on-failure',
    launchOptions: { args: ['--disable-dev-shm-usage'] },
    // store artifacts in test-results for CI uploads
    outputDir: 'test-results',
  },
  reporter: process.env.CI ? [['dot'], ['github'], ['html', { open: 'never' }]] : 'list',
});
