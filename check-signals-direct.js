const puppeteer = require('puppeteer');

(async () => {
  console.log('🔍 Direct signals page check...');

  const browser = await puppeteer.launch({
    headless: true,
    args: ['--no-sandbox', '--disable-setuid-sandbox']
  });

  const page = await browser.newPage();

  try {
    // Login first
    await page.goto('https://tickerai.app/signin', { waitUntil: 'networkidle2' });
    await page.type('input[name="email"]', 'coretechlead@gmail.com');
    await page.type('input[name="password"]', 'PassAman@2025');
    await Promise.all([
      page.waitForNavigation({ waitUntil: 'networkidle2' }),
      page.click('button[type="submit"]')
    ]);

    // Try signals without trailing slash
    console.log('Testing /signals...');
    try {
      await page.goto('https://tickerai.app/signals', { waitUntil: 'networkidle2', timeout: 10000 });
      console.log('✅ /signals works, URL:', page.url());
      console.log('Title:', await page.title());
    } catch (e) {
      console.log('❌ /signals failed:', e.message);
    }

    // Try signals with trailing slash
    console.log('Testing /signals/...');
    try {
      await page.goto('https://tickerai.app/signals/', { waitUntil: 'networkidle2', timeout: 10000 });
      console.log('✅ /signals/ works, URL:', page.url());
      console.log('Title:', await page.title());
    } catch (e) {
      console.log('❌ /signals/ failed:', e.message);
    }

    // Check if route exists
    console.log('Testing direct access...');
    const response = await page.goto('https://tickerai.app/signals', { waitUntil: 'load' });
    console.log('Response status:', response.status());
    console.log('Response headers:', Object.fromEntries(response.headers()));

    await page.screenshot({ path: 'signals-debug-screenshot.png', fullPage: true });

  } catch (error) {
    console.error('❌ Error:', error.message);
  } finally {
    await browser.close();
  }
})();