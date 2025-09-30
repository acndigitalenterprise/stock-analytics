const puppeteer = require('puppeteer');

(async () => {
  console.log('🔄 Force refreshing production with cache bypass...');

  const browser = await puppeteer.launch({
    headless: true,
    args: ['--no-sandbox', '--disable-setuid-sandbox']
  });

  const page = await browser.newPage();

  try {
    // Disable cache and force reload
    await page.setCacheEnabled(false);

    // 1. Navigate to signin with cache bypass
    console.log('📝 Force loading signin page...');
    await page.goto('https://tickerai.app/signin', {
      waitUntil: 'networkidle2',
      timeout: 30000
    });

    // 2. Fill login form
    console.log('🔐 Logging in...');
    await page.type('input[name="email"]', 'coretechlead@gmail.com');
    await page.type('input[name="password"]', 'PassAman@2025');

    // 3. Submit form
    await Promise.all([
      page.waitForNavigation({ waitUntil: 'networkidle2' }),
      page.click('button[type="submit"]')
    ]);

    console.log('✅ Login successful');

    // 4. Force reload Signals page with cache bypass
    console.log('🔄 Force reloading Signals page...');
    await page.goto('https://tickerai.app/signals?v=' + Date.now(), {
      waitUntil: 'networkidle2',
      timeout: 30000
    });

    // 5. Check if styling is fixed
    const stylingCheck = await page.evaluate(() => {
      const body = document.body;
      const computed = window.getComputedStyle(body);
      return {
        backgroundColor: computed.backgroundColor,
        background: computed.background,
        hasGradient: computed.background.includes('gradient'),
        bodyClasses: body.className
      };
    });

    console.log('🎨 Styling check results:');
    console.log('- Background color:', stylingCheck.backgroundColor);
    console.log('- Has gradient:', stylingCheck.hasGradient);
    console.log('- Body classes:', stylingCheck.bodyClasses);

    // 6. Take new screenshot
    await page.screenshot({ path: 'production-refreshed-screenshot.png', fullPage: true });
    console.log('📸 New screenshot saved: production-refreshed-screenshot.png');

    if (!stylingCheck.hasGradient) {
      console.log('✅ Styling fix appears to be working!');
    } else {
      console.log('⚠️ Gradient still present - may need server-side cache clear');
    }

  } catch (error) {
    console.error('❌ Error during force refresh:', error.message);
  } finally {
    await browser.close();
    console.log('🏁 Force refresh completed');
  }
})();